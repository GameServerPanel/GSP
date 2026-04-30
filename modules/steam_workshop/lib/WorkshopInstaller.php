<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopInstaller: handles mod download (via agent SteamCMD) and
 * copy/sync from agent cache to server install path.
 *
 * Template variables supported in all paths/scripts:
 *   {home_id}          numeric home id
 *   {agent_id}         numeric remote_server_id
 *   {workshop_app_id}  Steam app id (e.g. 221100)
 *   {mod_id}           Workshop mod id (numeric string)
 *   {mod_title}        mod title (sanitised)
 *   {steamcmd_path}    path to steamcmd.sh / steamcmd.exe on the agent
 *   {server_path}      game server home_path
 *   {install_path}     resolved install path for this mod
 *   {cache_path}       resolved cache path for this mod
 */

require_once __DIR__ . '/WorkshopRepository.php';

class WorkshopInstaller
{
    private WorkshopRepository $repo;
    private string $logDir;

    public function __construct(WorkshopRepository $repo)
    {
        $this->repo   = $repo;
        $this->logDir = __DIR__ . '/../logs';
        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0775, true);
        }
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

    /**
     * Install a workshop mod for a game server.
     *
     * @param array  $home         Row from getGameHome/getUserGameHome
     * @param array  $profile      Row from gsp_workshop_game_profiles
     * @param string $workshopId   Numeric workshop item id
     * @param string $steamCmdPath Path to steamcmd binary on the agent
     * @return array{success:bool, message:string, restart_required:bool, log:list<string>}
     */
    public function install(
        array $home,
        array $profile,
        string $workshopId,
        string $steamCmdPath = ''
    ): array {
        $log = [];

        // Validate workshop id
        $workshopId = preg_replace('/[^0-9]/', '', $workshopId) ?? '';
        if ($workshopId === '') {
            return $this->fail('Workshop ID must be numeric.', $log);
        }

        $homeId   = (int)($home['home_id'] ?? 0);
        $agentId  = (int)($home['remote_server_id'] ?? 0);
        $appId    = (string)($profile['workshop_app_id'] ?? '');
        $osType   = $this->detectOsType($home);

        if ($homeId <= 0 || $agentId <= 0 || $appId === '') {
            return $this->fail('Invalid home, agent, or app ID.', $log);
        }

        // Build template vars
        $vars = $this->buildTemplateVars($home, $profile, $workshopId, '', $steamCmdPath);
        $cachePath   = $this->resolveTemplate((string)($profile['cache_path_template'] ?? ''), $vars);
        $installPath = $this->resolveTemplate((string)($profile['install_path_template'] ?? ''), $vars);
        $vars['{cache_path}']   = $cachePath;
        $vars['{install_path}'] = $installPath;

        // Build remote library
        $remote = $this->buildRemote($home);
        if ($remote === null) {
            return $this->fail('Unable to connect to agent.', $log);
        }

        // Check agent connectivity
        if ($remote->status_chk() !== 1) {
            return $this->fail('Agent is offline.', $log);
        }

        // Check cache
        $cacheEntry = $this->repo->getCacheEntry($agentId, $appId, $workshopId);
        $log[] = "Cache check: agent={$agentId} app={$appId} mod={$workshopId}";

        if ($cacheEntry === null || ($cacheEntry['status'] ?? '') !== 'cached') {
            $log[] = 'Cache MISS – triggering SteamCMD download on agent.';
            $downloadResult = $this->triggerSteamCmdDownload(
                $remote, $appId, $workshopId, $steamCmdPath, $cachePath, $log
            );

            if (!$downloadResult) {
                // Update cache status to 'missing' so the cron can retry
                $this->repo->upsertCacheEntry($agentId, $osType, $appId, $workshopId, $cachePath, 'missing');
                return $this->fail(
                    'SteamCMD download failed. The mod will be retried on the next scheduled update.',
                    $log
                );
            }

            $log[] = 'SteamCMD download success.';
            $this->repo->upsertCacheEntry($agentId, $osType, $appId, $workshopId, $cachePath, 'cached');
        } else {
            $log[] = 'Cache HIT – using existing cached copy.';
        }

        // Copy / sync from cache to server install path
        $syncResult = $this->syncToServer($remote, $profile, $vars, $log);
        if (!$syncResult) {
            return $this->fail('Sync from cache to server failed. Check agent logs.', $log);
        }

        // Optional install script (admin-defined only)
        $installScript = trim((string)($profile['install_script'] ?? ''));
        if ($installScript !== '') {
            $this->runInstallScript($remote, $installScript, $vars, $log);
        }

        // Record in database
        $this->repo->insertOrUpdateMod(
            $homeId, $agentId, (int)$profile['id'], $appId, $workshopId,
            $installPath, '', 0
        );

        $restartRequired = !empty($profile['requires_restart']);
        $log[] = $restartRequired ? 'Restart required after mod install.' : 'Hot-reload capable (no restart required).';

        return [
            'success'          => true,
            'message'          => 'Mod installed successfully.',
            'restart_required' => $restartRequired,
            'log'              => $log,
        ];
    }

    /**
     * Sync a single installed mod's cache into the server path.
     * Called from pre-start and from the user "Sync now" button.
     *
     * @param array $home     Game home row
     * @param array $modRow   Row from gsp_server_workshop_mods
     * @param array $profile  Row from gsp_workshop_game_profiles
     * @return array{success:bool, changed:bool, message:string, log:list<string>}
     */
    public function syncMod(array $home, array $modRow, array $profile): array
    {
        $log = [];
        $workshopId = (string)($modRow['workshop_id'] ?? '');
        $agentId    = (int)($modRow['agent_id'] ?? 0);
        $appId      = (string)($modRow['workshop_app_id'] ?? '');

        $cacheEntry = $this->repo->getCacheEntry($agentId, $appId, $workshopId);
        if ($cacheEntry === null || ($cacheEntry['status'] ?? '') !== 'cached') {
            $log[] = "Cache entry not available for mod {$workshopId} – skipping sync.";
            return ['success' => false, 'changed' => false, 'message' => 'Mod not cached yet.', 'log' => $log];
        }

        $remote = $this->buildRemote($home);
        if ($remote === null || $remote->status_chk() !== 1) {
            return ['success' => false, 'changed' => false, 'message' => 'Agent offline.', 'log' => $log];
        }

        $vars = $this->buildTemplateVars($home, $profile, $workshopId, $modRow['title'] ?? '');
        $vars['{cache_path}']   = $this->resolveTemplate((string)($profile['cache_path_template'] ?? ''), $vars);
        $vars['{install_path}'] = (string)($modRow['install_path'] ?? $this->resolveTemplate((string)($profile['install_path_template'] ?? ''), $vars));

        $changed = $this->checkNeedsSync($remote, $vars['{cache_path}'], $vars['{install_path}'], $profile, $log);
        if (!$changed) {
            $log[] = 'No changes detected – skipping sync.';
            return ['success' => true, 'changed' => false, 'message' => 'Already up to date.', 'log' => $log];
        }

        $log[] = 'Changes detected – syncing.';
        $ok = $this->syncToServer($remote, $profile, $vars, $log);

        return [
            'success' => $ok,
            'changed' => true,
            'message' => $ok ? 'Sync complete.' : 'Sync failed.',
            'log'     => $log,
        ];
    }

    // ------------------------------------------------------------------
    // Template resolution
    // ------------------------------------------------------------------

    /**
     * Replace template placeholders in a string.
     *
     * @param array<string,string> $vars
     */
    public function resolveTemplate(string $template, array $vars): string
    {
        return str_replace(array_keys($vars), array_values($vars), $template);
    }

    /**
     * Build the standard template variable map for a home + profile + mod.
     *
     * @return array<string,string>
     */
    public function buildTemplateVars(
        array $home,
        array $profile,
        string $workshopId,
        string $modTitle = '',
        string $steamCmdPath = ''
    ): array {
        $serverPath = rtrim((string)($home['home_path'] ?? ''), '/');
        $safeName   = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $modTitle) ?? '';

        $folderNameTpl  = (string)($profile['folder_name_template'] ?? '@{mod_id}');
        $folderNameVars = [
            '{mod_id}'    => $workshopId,
            '{mod_title}' => $safeName,
        ];
        $folderName = str_replace(array_keys($folderNameVars), array_values($folderNameVars), $folderNameTpl);

        return [
            '{home_id}'         => (string)($home['home_id'] ?? ''),
            '{agent_id}'        => (string)($home['remote_server_id'] ?? ''),
            '{workshop_app_id}' => (string)($profile['workshop_app_id'] ?? ''),
            '{mod_id}'          => $workshopId,
            '{mod_title}'       => $safeName,
            '{mod_folder}'      => $folderName,
            '{steamcmd_path}'   => $steamCmdPath !== '' ? $steamCmdPath : '/home/gameserver/steamcmd',
            '{server_path}'     => $serverPath,
            '{install_path}'    => '',   // filled by caller after resolution
            '{cache_path}'      => '',   // filled by caller after resolution
        ];
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    /** Build an OGPRemoteLibrary instance from a home row. */
    private function buildRemote(array $home): ?object
    {
        if (!class_exists('OGPRemoteLibrary')) {
            @require_once __DIR__ . '/../../../includes/lib_remote.php';
        }
        if (!class_exists('OGPRemoteLibrary')) {
            return null;
        }

        $ip  = (string)($home['agent_ip'] ?? '');
        $port = (string)($home['agent_port'] ?? '');
        $key  = (string)($home['encryption_key'] ?? '');
        $timeout = isset($home['timeout']) ? (int)$home['timeout'] : 30;

        if ($ip === '' || $port === '') {
            return null;
        }

        return new OGPRemoteLibrary($ip, $port, $key, $timeout);
    }

    /**
     * Trigger a SteamCMD workshop_download_item on the agent via exec().
     * Returns true on success.
     *
     * @param list<string> $log
     */
    private function triggerSteamCmdDownload(
        object $remote,
        string $appId,
        string $workshopId,
        string $steamCmdPath,
        string $cachePath,
        array &$log
    ): bool {
        if ($steamCmdPath === '') {
            $steamCmdPath = '/home/gameserver/steamcmd/steamcmd.sh';
        }

        $cmd = implode(' ', [
            escapeshellarg($steamCmdPath),
            '+login', 'anonymous',
            '+workshop_download_item', escapeshellarg($appId), escapeshellarg($workshopId),
            'validate',
            '+quit',
        ]);

        $log[] = "SteamCMD start: {$cmd}";
        $this->writeLog("STEAMCMD START agent={$this->agentIdFromRemote($remote)} app={$appId} mod={$workshopId}");

        $output = $remote->exec($cmd);

        if ($output === null) {
            $log[] = 'SteamCMD: no response from agent (command may still be running).';
            $this->writeLog("STEAMCMD NO_RESPONSE app={$appId} mod={$workshopId}");
            // Treat as unknown – check file existence
        } else {
            $log[] = 'SteamCMD output: ' . substr((string)$output, 0, 500);
        }

        // Verify the download succeeded by checking for the cache path on the agent
        $exists = $remote->rfile_exists($cachePath);
        if ($exists === 1) {
            $this->writeLog("STEAMCMD SUCCESS app={$appId} mod={$workshopId} path={$cachePath}");
            return true;
        }

        $this->writeLog("STEAMCMD FAILURE app={$appId} mod={$workshopId} path={$cachePath}");
        return false;
    }

    /**
     * Check if cache path differs from install path using a dry-run compare.
     * Returns true if sync is needed.
     *
     * @param list<string> $log
     */
    private function checkNeedsSync(
        object $remote,
        string $cachePath,
        string $installPath,
        array $profile,
        array &$log
    ): bool {
        $copyMethod = (string)($profile['copy_method'] ?? 'rsync');
        $log[]      = "Pre-start compare: cache={$cachePath} dest={$installPath} method={$copyMethod}";

        if ($copyMethod === 'rsync') {
            $cmd = sprintf(
                'rsync -rcn --delete %s %s 2>/dev/null; echo "EXIT:$?"',
                escapeshellarg(rtrim($cachePath, '/') . '/'),
                escapeshellarg(rtrim($installPath, '/') . '/')
            );
            $out = (string)$remote->exec($cmd);
            // If rsync dry-run produces file list output, changes exist
            $hasChanges = preg_match('/\S/', preg_replace('/EXIT:\d+\s*$/', '', $out) ?? '') === 1;
            return $hasChanges;
        }

        if ($copyMethod === 'robocopy') {
            // Robocopy /L = list only, /MIR = mirror, /NJH /NJS = no headers
            $cmd = sprintf(
                'robocopy /L /MIR /NJH /NJS %s %s',
                escapeshellarg($cachePath),
                escapeshellarg($installPath)
            );
            $out = (string)$remote->exec($cmd);
            // Exit code 0 = no changes, 1+ = changes
            return trim($out) !== '' && !preg_match('/\bNo new\b/i', $out);
        }

        // custom_script: always sync
        return true;
    }

    /**
     * Perform the actual copy/sync from cache to install path on the agent.
     *
     * @param array<string,string> $vars
     * @param list<string>         $log
     */
    private function syncToServer(
        object $remote,
        array $profile,
        array $vars,
        array &$log
    ): bool {
        $copyMethod  = (string)($profile['copy_method'] ?? 'rsync');
        $cachePath   = $vars['{cache_path}'] ?? '';
        $installPath = $vars['{install_path}'] ?? '';

        if ($cachePath === '' || $installPath === '') {
            $log[] = 'Sync skipped: empty cache or install path.';
            return false;
        }

        $log[] = "Sync start: method={$copyMethod} cache={$cachePath} dest={$installPath}";
        $this->writeLog("COPY START method={$copyMethod} cache={$cachePath} dest={$installPath}");

        if ($copyMethod === 'rsync') {
            $cmd = sprintf(
                'mkdir -p %s && rsync -a --delete %s %s 2>&1; echo "EXIT:$?"',
                escapeshellarg($installPath),
                escapeshellarg(rtrim($cachePath, '/') . '/'),
                escapeshellarg(rtrim($installPath, '/') . '/')
            );
        } elseif ($copyMethod === 'robocopy') {
            $cmd = sprintf(
                'robocopy /MIR /NJH /NJS %s %s; echo "ROBOCOPY EXIT:$LASTEXITCODE"',
                escapeshellarg($cachePath),
                escapeshellarg($installPath)
            );
        } elseif ($copyMethod === 'custom_script') {
            $script = trim((string)($profile['install_script'] ?? ''));
            if ($script === '') {
                $log[] = 'custom_script requested but install_script is empty – falling back to rsync.';
                $cmd = sprintf(
                    'mkdir -p %s && rsync -a --delete %s %s 2>&1; echo "EXIT:$?"',
                    escapeshellarg($installPath),
                    escapeshellarg(rtrim($cachePath, '/') . '/'),
                    escapeshellarg(rtrim($installPath, '/') . '/')
                );
            } else {
                // The admin-defined script is templated; execute it via the agent exec()
                $resolvedScript = $this->resolveTemplate($script, $vars);
                $cmd = $resolvedScript . ' 2>&1; echo "EXIT:$?"';
            }
        } else {
            $log[] = "Unknown copy method '{$copyMethod}'.";
            return false;
        }

        $out = (string)$remote->exec($cmd);
        $log[] = 'Sync output: ' . substr($out, 0, 500);

        // Check exit code hint embedded in output
        if (preg_match('/EXIT:(\d+)/', $out, $m)) {
            $code = (int)$m[1];
            // robocopy exit codes 0..7 are success/info, 8+ are errors
            if ($copyMethod === 'robocopy') {
                $ok = $code < 8;
            } else {
                $ok = $code === 0;
            }
        } else {
            $ok = true; // assume success if no code
        }

        if ($ok) {
            $log[] = 'Sync success.';
            $this->writeLog("COPY SUCCESS cache={$cachePath} dest={$installPath}");
        } else {
            $log[] = 'Sync failed (non-zero exit).';
            $this->writeLog("COPY FAILURE cache={$cachePath} dest={$installPath}");
        }

        return $ok;
    }

    /**
     * Run the admin-defined install script on the agent.
     *
     * @param array<string,string> $vars
     * @param list<string>         $log
     */
    private function runInstallScript(
        object $remote,
        string $script,
        array $vars,
        array &$log
    ): void {
        $resolved = $this->resolveTemplate($script, $vars);
        $log[]    = 'Running install script.';
        $out      = (string)$remote->exec($resolved . ' 2>&1');
        $log[]    = 'Script output: ' . substr($out, 0, 500);
        $this->writeLog('SCRIPT OUTPUT: ' . substr($out, 0, 1000));
    }

    private function detectOsType(array $home): string
    {
        $gameKey = strtolower((string)($home['game_key'] ?? ''));
        if (preg_match('/win/', $gameKey)) {
            return 'windows';
        }
        return 'linux';
    }

    private function agentIdFromRemote(object $remote): string
    {
        // OGPRemoteLibrary stores host/port; use reflection-free fallback
        return 'unknown';
    }

    private function writeLog(string $message): void
    {
        $file = $this->logDir . '/workshop_install.log';
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents($file, $line, FILE_APPEND | LOCK_EX);
    }

    private function fail(string $message, array $log): array
    {
        $this->writeLog('FAIL: ' . $message);
        return [
            'success'          => false,
            'message'          => $message,
            'restart_required' => false,
            'log'              => $log,
        ];
    }
}
