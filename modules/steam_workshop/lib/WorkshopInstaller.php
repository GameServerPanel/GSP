<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopInstaller: handles mod download (via agent SteamCMD) and
 * copy/sync from agent cache to server install path.
 *
 * Template variables supported in all paths/scripts (%var% style):
 *   %home_id%           numeric home id
 *   %server_path%       game server home_path
 *   %steam_app_id%      Steam game App ID (e.g. 221100 for DayZ)
 *   %workshop_app_id%   Workshop App ID used for +workshop_download_item
 *   %workshop_id%       Workshop mod item id (numeric)
 *   %mod_name%          mod title sanitised for use as a folder name
 *   %install_name%      resolved mod folder name (from folder_naming_format)
 *   %download_path%     alias for %source_path% (SteamCMD cache dir for this mod)
 *   %source_path%       SteamCMD cache directory for this mod
 *   %target_path%       resolved install directory for this mod
 *   %keys_source_path%  key source path (resolved from profile key_source_path)
 *   %keys_target_path%  key destination path (resolved from profile key_dest_path)
 *   %steamcmd_path%     path to steamcmd.sh on the agent
 *
 * Legacy {var} style placeholders are also resolved for backward compat.
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
     * @return array{success:bool, message:string, restart_required:bool, log:list<string>}
     */
    public function install(
        array $home,
        array $profile,
        string $workshopId
    ): array {
        $log = [];

        $workshopId = preg_replace('/[^0-9]/', '', $workshopId) ?? '';
        if ($workshopId === '') {
            return $this->fail('Workshop ID must be numeric.', $log);
        }

        $homeId  = (int)($home['home_id'] ?? 0);
        $agentId = (int)($home['remote_server_id'] ?? 0);
        $appId   = (string)($profile['workshop_app_id'] ?? '');
        $osType  = $this->detectOsType($home);

        if ($homeId <= 0 || $agentId <= 0 || $appId === '') {
            return $this->fail('Invalid home, agent, or app ID.', $log);
        }

        $remote = $this->buildRemote($home);
        if ($remote === null) {
            return $this->fail('Unable to connect to agent.', $log);
        }
        if ($remote->status_chk() !== 1) {
            return $this->fail('Agent is offline.', $log);
        }

        // Build template vars (source/target paths filled after resolution below)
        $vars = $this->buildTemplateVars($home, $profile, $workshopId);

        // Run pre-update script once (before mods)
        $preScript = trim((string)($profile['pre_update_script'] ?? ''));
        if ($preScript !== '') {
            $log[] = 'Running pre-update script.';
            $this->runScript($remote, $preScript, $vars, $log);
        }

        // Download
        $cacheResult = $this->ensureCached($remote, $agentId, $osType, $appId, $workshopId, $profile, $vars, $log);
        if (!$cacheResult) {
            return $this->fail('SteamCMD download failed.', $log);
        }

        // Copy/sync to server
        $syncResult = $this->syncToServer($remote, $profile, $vars, $log);
        if (!$syncResult) {
            return $this->fail('Sync from cache to server failed. Check agent logs.', $log);
        }

        // Per-mod install script
        $installScript = trim((string)($profile['install_script'] ?? ''));
        if ($installScript !== '') {
            $log[] = 'Running per-mod install script.';
            $this->runScript($remote, $installScript, $vars, $log);
        }

        // Copy keys if configured
        if (!empty($profile['copy_keys'])) {
            $this->copyKeys($remote, $profile, $vars, $log);
        }

        // Post-update script
        $postScript = trim((string)($profile['post_update_script'] ?? ''));
        if ($postScript !== '') {
            $log[] = 'Running post-update script.';
            $this->runScript($remote, $postScript, $vars, $log);
        }

        // Record in database
        $this->repo->insertOrUpdateMod(
            $homeId, $agentId, (int)$profile['id'], $appId, $workshopId,
            $vars['%target_path%'] ?? '', '', 0
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
        $log        = [];
        $workshopId = (string)($modRow['workshop_id'] ?? '');
        $agentId    = (int)($modRow['agent_id'] ?? 0);
        $appId      = (string)($modRow['workshop_app_id'] ?? '');

        $cacheEntry = $this->repo->getCacheEntry($agentId, $appId, $workshopId);
        if ($cacheEntry === null || ($cacheEntry['status'] ?? '') !== 'cached') {
            return ['success' => false, 'changed' => false, 'message' => 'Mod not cached yet.', 'log' => $log];
        }

        $remote = $this->buildRemote($home);
        if ($remote === null || $remote->status_chk() !== 1) {
            return ['success' => false, 'changed' => false, 'message' => 'Agent offline.', 'log' => $log];
        }

        $vars = $this->buildTemplateVars($home, $profile, $workshopId, $modRow['title'] ?? '');

        $changed = $this->checkNeedsSync($remote, $vars['%source_path%'], $vars['%target_path%'], $profile, $log);
        if (!$changed) {
            $log[] = 'No changes detected – skipping sync.';
            return ['success' => true, 'changed' => false, 'message' => 'Already up to date.', 'log' => $log];
        }

        $log[] = 'Changes detected – syncing.';
        $ok = $this->syncToServer($remote, $profile, $vars, $log);

        if ($ok) {
            $installScript = trim((string)($profile['install_script'] ?? ''));
            if ($installScript !== '') {
                $this->runScript($remote, $installScript, $vars, $log);
            }
            if (!empty($profile['copy_keys'])) {
                $this->copyKeys($remote, $profile, $vars, $log);
            }
        }

        return [
            'success' => $ok,
            'changed' => true,
            'message' => $ok ? 'Sync complete.' : 'Sync failed.',
            'log'     => $log,
        ];
    }

    // ------------------------------------------------------------------
    // Template resolution (public – used by WorkshopUpdater)
    // ------------------------------------------------------------------

    /**
     * Replace template placeholders in a string.
     * Supports both %var% (canonical) and {var} (legacy) style.
     *
     * @param array<string,string> $vars
     */
    public function resolveTemplate(string $template, array $vars): string
    {
        // %var% style (canonical)
        $result = str_replace(array_keys($vars), array_values($vars), $template);

        // Legacy {var} style aliases – map old keys to same values
        $legacy = [];
        foreach ($vars as $k => $v) {
            $legacyKey = '{' . trim($k, '%') . '}';
            $legacy[$legacyKey] = $v;
        }
        // Extra legacy aliases
        $legacy['{mod_id}']    = $vars['%workshop_id%'] ?? '';
        $legacy['{mod_title}'] = $vars['%mod_name%'] ?? '';
        $legacy['{mod_folder}'] = $vars['%install_name%'] ?? '';
        $legacy['{install_path}'] = $vars['%target_path%'] ?? '';
        $legacy['{cache_path}']   = $vars['%source_path%'] ?? '';

        return str_replace(array_keys($legacy), array_values($legacy), $result);
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
        string $modTitle = ''
    ): array {
        $serverPath   = rtrim((string)($home['home_path'] ?? ''), '/');
        $steamcmdPath = trim((string)($profile['steamcmd_path'] ?? ''));
        if ($steamcmdPath === '') {
            $steamcmdPath = '/home/gameserver/steamcmd/steamcmd.sh';
        }

        $safeName = preg_replace('/[^a-zA-Z0-9_\-]/', '_', $modTitle) ?? '';

        // Resolve folder name from format
        $folderFormat = (string)($profile['folder_naming_format'] ?? '@%workshop_id%');
        if ($folderFormat === '@%mod_name%') {
            $installName = '@' . $safeName;
        } elseif ($folderFormat === '@%workshop_id%') {
            $installName = '@' . $workshopId;
        } else {
            // custom – use folder_name_template as-is, resolve %workshop_id%/%mod_name% inline
            $tpl = (string)($profile['folder_name_template'] ?? '@%workshop_id%');
            $installName = str_replace(['%workshop_id%', '%mod_name%'], [$workshopId, $safeName], $tpl);
        }

        $steamAppId     = (string)($profile['steam_app_id'] ?? '');
        $workshopAppId  = (string)($profile['workshop_app_id'] ?? '');

        // Resolve cache/source path template
        $cachePathTpl = (string)($profile['cache_path_template'] ?? '');
        $sourcePath   = str_replace(
            ['%workshop_app_id%', '%workshop_id%', '%mod_name%', '%install_name%', '%steam_app_id%', '%steamcmd_path%'],
            [$workshopAppId, $workshopId, $safeName, $installName, $steamAppId, dirname($steamcmdPath)],
            $cachePathTpl
        );

        // Resolve target/install path template
        $installPathTpl = (string)($profile['install_path_template'] ?? '');
        $targetPath     = str_replace(
            ['%server_path%', '%workshop_app_id%', '%workshop_id%', '%mod_name%', '%install_name%', '%steam_app_id%'],
            [$serverPath, $workshopAppId, $workshopId, $safeName, $installName, $steamAppId],
            $installPathTpl
        );

        // Resolve key paths
        $keySourceRaw = (string)($profile['key_source_path'] ?? '');
        $keyDestRaw   = (string)($profile['key_dest_path'] ?? '');
        $keySource    = str_replace(['%source_path%', '%server_path%'], [$sourcePath, $serverPath], $keySourceRaw);
        $keyDest      = str_replace(['%target_path%', '%server_path%'], [$targetPath, $serverPath], $keyDestRaw);

        return [
            '%home_id%'          => (string)($home['home_id'] ?? ''),
            '%server_path%'      => $serverPath,
            '%steam_app_id%'     => $steamAppId,
            '%workshop_app_id%'  => $workshopAppId,
            '%workshop_id%'      => $workshopId,
            '%mod_name%'         => $safeName,
            '%install_name%'     => $installName,
            '%download_path%'    => $sourcePath,
            '%source_path%'      => $sourcePath,
            '%target_path%'      => $targetPath,
            '%keys_source_path%' => $keySource,
            '%keys_target_path%' => $keyDest,
            '%steamcmd_path%'    => $steamcmdPath,
        ];
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    /**
     * Ensure a mod is downloaded/cached on the agent.
     * Returns true if cached and available.
     *
     * @param list<string> $log
     */
    private function ensureCached(
        object $remote,
        int $agentId,
        string $osType,
        string $appId,
        string $workshopId,
        array $profile,
        array &$vars,
        array &$log
    ): bool {
        $sourcePath = $vars['%source_path%'];

        $cacheEntry = $this->repo->getCacheEntry($agentId, $appId, $workshopId);
        $log[] = "Cache check: agent={$agentId} app={$appId} mod={$workshopId}";

        if ($cacheEntry !== null && ($cacheEntry['status'] ?? '') === 'cached') {
            $log[] = 'Cache HIT – using existing cached copy.';
            return true;
        }

        $log[] = 'Cache MISS – triggering SteamCMD download on agent.';
        $ok = $this->triggerSteamCmdDownload($remote, $agentId, $appId, $workshopId, $profile, $sourcePath, $log);

        $status = $ok ? 'cached' : 'missing';
        $this->repo->upsertCacheEntry($agentId, $osType, $appId, $workshopId, $sourcePath, $status);

        if ($ok) {
            $log[] = 'SteamCMD download success.';
        }
        return $ok;
    }

    /** Build an OGPRemoteLibrary instance from a home row. */
    private function buildRemote(array $home): ?object
    {
        if (!class_exists('OGPRemoteLibrary')) {
            @require_once __DIR__ . '/../../../includes/lib_remote.php';
        }
        if (!class_exists('OGPRemoteLibrary')) {
            return null;
        }

        $ip      = (string)($home['agent_ip'] ?? '');
        $port    = (string)($home['agent_port'] ?? '');
        $key     = (string)($home['encryption_key'] ?? '');
        $timeout = isset($home['timeout']) ? (int)$home['timeout'] : 30;

        if ($ip === '' || $port === '') {
            return null;
        }

        return new OGPRemoteLibrary($ip, $port, $key, $timeout);
    }

    /**
     * Trigger a SteamCMD workshop_download_item on the agent.
     * Returns true on success.
     *
     * @param list<string> $log
     */
    private function triggerSteamCmdDownload(
        object $remote,
        int $agentId,
        string $appId,
        string $workshopId,
        array $profile,
        string $cachePath,
        array &$log
    ): bool {
        $steamcmdPath = trim((string)($profile['steamcmd_path'] ?? ''));
        if ($steamcmdPath === '') {
            $steamcmdPath = '/home/gameserver/steamcmd/steamcmd.sh';
        }

        $loginMode    = (string)($profile['steamcmd_login_mode'] ?? 'anonymous');
        $loginArg     = $loginMode === 'account' ? 'account_placeholder' : 'anonymous';

        $cmd = implode(' ', [
            escapeshellarg($steamcmdPath),
            '+login', escapeshellarg($loginArg),
            '+workshop_download_item', escapeshellarg($appId), escapeshellarg($workshopId),
            'validate',
            '+quit',
        ]);

        $log[] = "SteamCMD start: agent={$agentId} app={$appId} mod={$workshopId}";
        $this->writeLog("STEAMCMD START agent={$agentId} app={$appId} mod={$workshopId}");

        $output = $remote->exec($cmd);

        if ($output === null) {
            $log[] = 'SteamCMD: no response from agent (command may still be running).';
        } else {
            $log[] = 'SteamCMD output: ' . substr((string)$output, 0, 500);
        }

        // Verify by checking whether the cache path now exists
        $exists = $remote->rfile_exists($cachePath);
        if ($exists === 1) {
            $this->writeLog("STEAMCMD SUCCESS agent={$agentId} app={$appId} mod={$workshopId} path={$cachePath}");
            return true;
        }

        $this->writeLog("STEAMCMD FAILURE agent={$agentId} app={$appId} mod={$workshopId} path={$cachePath}");
        return false;
    }

    /**
     * Check if cache path differs from install path (dry-run compare).
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
            $cmd  = sprintf(
                'rsync -rcn --delete %s %s 2>/dev/null; echo "RSYNC_EXIT:$?"',
                escapeshellarg(rtrim($cachePath, '/') . '/'),
                escapeshellarg(rtrim($installPath, '/') . '/')
            );
            $out  = (string)$remote->exec($cmd);
            $body = preg_replace('/RSYNC_EXIT:\d+\s*$/', '', $out) ?? '';
            return preg_match('/\S/', $body) === 1;
        }

        // copy / symlink: always sync
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
        array &$vars,
        array &$log
    ): bool {
        $copyMethod  = (string)($profile['copy_method'] ?? 'rsync');
        $sourcePath  = $vars['%source_path%'];
        $targetPath  = $vars['%target_path%'];

        if ($sourcePath === '' || $targetPath === '') {
            $log[] = 'Sync skipped: empty source or target path.';
            return false;
        }

        $log[] = "Sync start: method={$copyMethod} source={$sourcePath} target={$targetPath}";
        $this->writeLog("COPY START method={$copyMethod} source={$sourcePath} target={$targetPath}");

        if ($copyMethod === 'rsync') {
            $cmd = sprintf(
                'mkdir -p %s && rsync -a --delete %s %s 2>&1; echo "EXIT:$?"',
                escapeshellarg($targetPath),
                escapeshellarg(rtrim($sourcePath, '/') . '/'),
                escapeshellarg(rtrim($targetPath, '/') . '/')
            );
        } elseif ($copyMethod === 'symlink') {
            $cmd = sprintf(
                'mkdir -p %s && ln -sfn %s %s 2>&1; echo "EXIT:$?"',
                escapeshellarg(dirname($targetPath)),
                escapeshellarg($sourcePath),
                escapeshellarg($targetPath)
            );
        } else {
            // 'copy' – basic cp
            $cmd = sprintf(
                'mkdir -p %s && cp -r %s %s 2>&1; echo "EXIT:$?"',
                escapeshellarg($targetPath),
                escapeshellarg(rtrim($sourcePath, '/') . '/.'),
                escapeshellarg($targetPath)
            );
        }

        $out = (string)$remote->exec($cmd);
        $log[] = 'Sync output: ' . substr($out, 0, 500);

        if (preg_match('/EXIT:(\d+)/', $out, $m)) {
            $ok = (int)$m[1] === 0;
        } else {
            $ok = true;
        }

        if ($ok) {
            $log[] = 'Sync success.';
            $this->writeLog("COPY SUCCESS source={$sourcePath} target={$targetPath}");
        } else {
            $log[] = 'Sync failed (non-zero exit).';
            $this->writeLog("COPY FAILURE source={$sourcePath} target={$targetPath}");
        }

        return $ok;
    }

    /**
     * Copy key files from the mod's keys directory to the server keys directory.
     *
     * @param array<string,string> $vars
     * @param list<string>         $log
     */
    private function copyKeys(
        object $remote,
        array $profile,
        array $vars,
        array &$log
    ): void {
        $keySrc  = $vars['%keys_source_path%'];
        $keyDest = $vars['%keys_target_path%'];

        if ($keySrc === '' || $keyDest === '') {
            $log[] = 'Key copy skipped: key paths not configured.';
            return;
        }

        $log[] = "Copying keys: {$keySrc} → {$keyDest}";
        $cmd = sprintf(
            'if [ -d %s ]; then mkdir -p %s && cp -f %s/*.bikey %s/ 2>/dev/null; fi; echo "EXIT:$?"',
            escapeshellarg($keySrc),
            escapeshellarg($keyDest),
            escapeshellarg($keySrc),
            escapeshellarg($keyDest)
        );
        $out  = (string)$remote->exec($cmd);
        $log[] = 'Key copy output: ' . substr($out, 0, 200);
    }

    /**
     * Run an admin-defined bash script on the agent after resolving template vars.
     *
     * @param array<string,string> $vars
     * @param list<string>         $log
     */
    private function runScript(
        object $remote,
        string $script,
        array $vars,
        array &$log
    ): void {
        $resolved = $this->resolveTemplate($script, $vars);
        $out      = (string)$remote->exec($resolved . ' 2>&1');
        $log[]    = 'Script output: ' . substr($out, 0, 500);
        $this->writeLog('SCRIPT OUTPUT: ' . substr($out, 0, 1000));
    }

    private function detectOsType(array $home): string
    {
        $gameKey = strtolower((string)($home['game_key'] ?? ''));
        return preg_match('/win/', $gameKey) ? 'windows' : 'linux';
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
