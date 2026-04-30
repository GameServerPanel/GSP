<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop
 * WorkshopPreStart: syncs updated cached mods into the game server folder
 * before the server is launched.
 *
 * Intended to be called from the game XML <pre_start> tag or from a
 * pre-start hook in the panel.
 *
 * Design rules:
 *  - Does NOT restart running servers.
 *  - Only syncs if the cache differs from the installed path.
 *  - Logs every check and sync attempt.
 */

require_once __DIR__ . '/WorkshopRepository.php';
require_once __DIR__ . '/WorkshopInstaller.php';

class WorkshopPreStart
{
    private WorkshopRepository $repo;
    private WorkshopInstaller  $installer;
    private string $logFile;

    public function __construct(WorkshopRepository $repo, WorkshopInstaller $installer)
    {
        $this->repo      = $repo;
        $this->installer = $installer;
        $logDir          = __DIR__ . '/../logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0775, true);
        }
        $this->logFile = $logDir . '/workshop_prestart.log';
    }

    // ------------------------------------------------------------------
    // Public API
    // ------------------------------------------------------------------

    /**
     * Sync all enabled mods for the given home_id before server start.
     *
     * @param array $home  Full game home row (from getGameHome / getUserGameHome)
     * @return array{synced:int, skipped:int, failed:int, log:list<string>}
     */
    public function syncModsForHome(array $home): array
    {
        $homeId  = (int)($home['home_id'] ?? 0);
        $log     = [];
        $synced  = 0;
        $skipped = 0;
        $failed  = 0;

        $this->log("PRE-START home={$homeId}");

        $mods = $this->repo->listEnabledModsForHome($homeId);

        if (empty($mods)) {
            $log[] = 'No enabled Workshop mods for this server.';
            $this->log("PRE-START home={$homeId}: no mods");
            return ['synced' => 0, 'skipped' => 0, 'failed' => 0, 'log' => $log];
        }

        foreach ((array)$mods as $modRow) {
            $workshopId = (string)($modRow['workshop_id'] ?? '');
            $profileId  = (int)($modRow['profile_id'] ?? 0);
            $log[]      = "Checking mod {$workshopId} …";

            $profile = $profileId > 0 ? $this->repo->getProfileById($profileId) : null;
            if ($profile === null) {
                $log[]  = "  Profile not found (profile_id={$profileId}) – skipped.";
                $this->log("PRE-START home={$homeId} mod={$workshopId}: profile missing");
                $skipped++;
                continue;
            }

            $result = $this->installer->syncMod($home, $modRow, $profile);

            if ($result['success'] && $result['changed']) {
                $log[]  = "  Synced: " . ($result['message'] ?? '');
                $this->log("PRE-START home={$homeId} mod={$workshopId}: synced");
                $synced++;
            } elseif ($result['success'] && !$result['changed']) {
                $log[]  = '  Already up to date – no sync needed.';
                $skipped++;
            } else {
                $log[]  = "  Sync failed: " . ($result['message'] ?? 'unknown error');
                $this->log("PRE-START home={$homeId} mod={$workshopId}: FAILED");
                $failed++;
            }

            // Append sub-log
            foreach ((array)($result['log'] ?? []) as $line) {
                $log[] = '    ' . $line;
            }
        }

        $this->log("PRE-START home={$homeId} done: synced={$synced} skipped={$skipped} failed={$failed}");

        return [
            'synced'  => $synced,
            'skipped' => $skipped,
            'failed'  => $failed,
            'log'     => $log,
        ];
    }

    // ------------------------------------------------------------------
    // Private helpers
    // ------------------------------------------------------------------

    private function log(string $message): void
    {
        $line = '[' . date('Y-m-d H:i:s') . '] ' . $message . "\n";
        @file_put_contents($this->logFile, $line, FILE_APPEND | LOCK_EX);
    }
}
