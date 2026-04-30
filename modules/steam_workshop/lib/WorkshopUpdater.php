<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopUpdater: scheduled / background cache update functions.
 *
 * Design rules:
 *  - Do NOT copy into running servers during a scheduled update.
 *  - Do NOT restart servers automatically.
 *  - Log every attempt.
 *  - Group SteamCMD calls by (agent_id, workshop_app_id, workshop_id) to
 *    avoid redundant downloads when multiple servers share a mod.
 */

require_once __DIR__ . '/WorkshopRepository.php';
require_once __DIR__ . '/WorkshopInstaller.php';

class WorkshopUpdater
{
    private WorkshopRepository $repo;
    private WorkshopInstaller  $installer;
    private string $logDir;
    private string $logFile;

    public function __construct(WorkshopRepository $repo, WorkshopInstaller $installer)
    {
        $this->repo      = $repo;
        $this->installer = $installer;
        $this->logDir    = __DIR__ . '/../logs';
        $this->logFile   = $this->logDir . '/workshop_update.log';

        if (!is_dir($this->logDir)) {
            mkdir($this->logDir, 0775, true);
        }
    }

    // ------------------------------------------------------------------
    // Public API – entry points called by cron_update.php
    // ------------------------------------------------------------------

    /**
     * Update Workshop cache for all enabled installed mods across all agents.
     *
     * @return array<string,mixed>
     */
    public function updateAll(): array
    {
        $this->log('=== updateAll start ===');
        $rows    = $this->repo->listDistinctEnabledWorkshopIds();
        $results = $this->processBatch($rows);
        $this->log('=== updateAll end: ' . count($results) . ' items processed ===');
        return $results;
    }

    /**
     * Update Workshop cache for all mods installed on a specific agent.
     *
     * @return array<string,mixed>
     */
    public function updateWorkshopCacheForAgent(int $agentId): array
    {
        $this->log("=== updateWorkshopCacheForAgent agent={$agentId} start ===");
        $rows    = $this->repo->listDistinctEnabledWorkshopIdsForAgent($agentId);
        $results = $this->processBatch($rows);
        $this->log("=== updateWorkshopCacheForAgent agent={$agentId} end ===");
        return $results;
    }

    /**
     * Update Workshop cache for all mods installed on a specific home.
     *
     * @return array<string,mixed>
     */
    public function updateWorkshopCacheForHome(int $homeId): array
    {
        $this->log("=== updateWorkshopCacheForHome home={$homeId} start ===");
        $rows    = $this->repo->listDistinctEnabledWorkshopIdsForHome($homeId);
        $results = $this->processBatch($rows);
        $this->log("=== updateWorkshopCacheForHome home={$homeId} end ===");
        return $results;
    }

    /**
     * Update Workshop cache for all mods associated with a specific profile.
     *
     * @return array<string,mixed>
     */
    public function updateWorkshopCacheForProfile(int $profileId): array
    {
        $this->log("=== updateWorkshopCacheForProfile profile={$profileId} start ===");
        $rows    = $this->repo->listDistinctEnabledWorkshopIdsForProfile($profileId);
        $results = $this->processBatch($rows);
        $this->log("=== updateWorkshopCacheForProfile profile={$profileId} end ===");
        return $results;
    }

    /**
     * Update a single Workshop mod on a specific agent.
     *
     * @return array<string,mixed>
     */
    public function updateSingleWorkshopMod(int $agentId, string $appId, string $workshopId): array
    {
        $workshopId = preg_replace('/[^0-9]/', '', $workshopId) ?? '';
        if ($workshopId === '') {
            return ['success' => false, 'error' => 'Workshop ID must be numeric.'];
        }

        $this->log("=== updateSingleWorkshopMod agent={$agentId} app={$appId} mod={$workshopId} ===");

        $row = [
            'agent_id'         => $agentId,
            'workshop_app_id'  => $appId,
            'workshop_id'      => $workshopId,
            'title'            => '',
        ];
        $results = $this->processBatch([$row]);
        return $results[0] ?? ['success' => false, 'error' => 'No result.'];
    }

    // ------------------------------------------------------------------
    // Internal – batch processor
    // ------------------------------------------------------------------

    /**
     * For each (agent_id, workshop_app_id, workshop_id) triplet, run a
     * SteamCMD validate download and update the cache table.
     *
     * @param array<int,array<string,mixed>> $rows
     * @return array<int,array<string,mixed>>
     */
    private function processBatch(array $rows): array
    {
        $results = [];

        // Group by agent_id so we can build one connection per agent
        $grouped = [];
        foreach ($rows as $row) {
            $aid = (int)($row['agent_id'] ?? 0);
            if ($aid <= 0) {
                continue;
            }
            $grouped[$aid][] = $row;
        }

        foreach ((array)$grouped as $agentId => $agentRows) {
            $home = $this->getAgentHome((int)$agentId);
            if ($home === null) {
                $this->log("Agent {$agentId}: cannot build remote – skipping.");
                foreach ((array)$agentRows as $row) {
                    $results[] = $this->buildResult($row, false, 'Agent home not found.');
                }
                continue;
            }

            $remote = $this->buildRemote($home);
            if ($remote === null || $remote->status_chk() !== 1) {
                $this->log("Agent {$agentId}: offline or unreachable – skipping.");
                foreach ((array)$agentRows as $row) {
                    $this->repo->upsertCacheEntry(
                        (int)$agentId,
                        $this->detectOsType($home),
                        (string)($row['workshop_app_id'] ?? ''),
                        (string)($row['workshop_id'] ?? ''),
                        '',
                        'failed',
                        null,
                        'Agent offline during scheduled update.'
                    );
                    $results[] = $this->buildResult($row, false, 'Agent offline.');
                }
                continue;
            }

            $osType = $this->detectOsType($home);

            foreach ((array)$agentRows as $row) {
                $appId      = (string)($row['workshop_app_id'] ?? '');
                $workshopId = (string)($row['workshop_id'] ?? '');
                $result     = $this->runSingleUpdate($remote, (int)$agentId, $osType, $appId, $workshopId, $home);
                $results[]  = $result;
            }
        }

        return $results;
    }

    /**
     * Run SteamCMD workshop_download_item validate for a single mod and
     * update the cache table accordingly.
     *
     * @return array<string,mixed>
     */
    private function runSingleUpdate(
        object $remote,
        int $agentId,
        string $osType,
        string $appId,
        string $workshopId,
        array $home
    ): array {
        $this->log("Update: agent={$agentId} app={$appId} mod={$workshopId}");

        // Build cache path from the profile (if available) or a sensible default
        $profile    = $this->repo->getProfileByAppId($appId);
        $steamCmdPath = '/home/gameserver/steamcmd/steamcmd.sh';
        $cachePath  = '';

        if ($profile !== null) {
            $vars      = $this->installer->buildTemplateVars($home, $profile, $workshopId, '', $steamCmdPath);
            $cachePath = $this->installer->resolveTemplate((string)($profile['cache_path_template'] ?? ''), $vars);
            $steamCmdPath = $vars['{steamcmd_path}'];
        }

        if ($cachePath === '') {
            $cachePath = "/home/gameserver/steamcmd/steamapps/workshop/content/{$appId}/{$workshopId}";
        }

        // Run SteamCMD with validate flag
        $cmd = implode(' ', [
            escapeshellarg($steamCmdPath),
            '+login', 'anonymous',
            '+workshop_download_item', escapeshellarg($appId), escapeshellarg($workshopId),
            'validate',
            '+quit',
        ]);

        $this->log("STEAMCMD CMD: {$cmd}");
        $output = (string)$remote->exec($cmd);
        $this->log('STEAMCMD OUTPUT: ' . substr($output, 0, 300));

        // Verify by checking path existence
        $exists = $remote->rfile_exists($cachePath);
        $success = ($exists === 1);

        if ($success) {
            $this->log("STEAMCMD SUCCESS app={$appId} mod={$workshopId}");
            $this->repo->upsertCacheEntry($agentId, $osType, $appId, $workshopId, $cachePath, 'cached');
        } else {
            $errorMsg = 'SteamCMD validate completed but cache path not found: ' . $cachePath;
            $this->log("STEAMCMD FAILURE app={$appId} mod={$workshopId}: {$errorMsg}");
            $this->repo->upsertCacheEntry($agentId, $osType, $appId, $workshopId, $cachePath, 'failed', null, $errorMsg);
        }

        return $this->buildResult(
            ['agent_id' => $agentId, 'workshop_app_id' => $appId, 'workshop_id' => $workshopId],
            $success,
            $success ? 'OK' : 'SteamCMD failed or cache path missing.'
        );
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /** Return a minimal home-like array for a given agent so we can build a remote. */
    private function getAgentHome(int $agentId): ?array
    {
        // We just need ip/port/key/timeout for the remote library connection.
        // Query the remote_servers table directly via the repository's db.
        // Use the OGPDatabase instance stored inside WorkshopRepository.
        $prefix = $this->repo->getPrefix();
        $row    = $this->repo->getAgentRow($agentId);
        return $row;
    }

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

    private function detectOsType(array $home): string
    {
        $gameKey = strtolower((string)($home['game_key'] ?? ''));
        if (preg_match('/win/', $gameKey)) {
            return 'windows';
        }
        return 'linux';
    }

    /** @return array<string,mixed> */
    private function buildResult(array $row, bool $success, string $message): array
    {
        return [
            'agent_id'        => $row['agent_id'] ?? 0,
            'workshop_app_id' => $row['workshop_app_id'] ?? '',
            'workshop_id'     => $row['workshop_id'] ?? '',
            'success'         => $success,
            'message'         => $message,
        ];
    }

    private function log(string $message): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
