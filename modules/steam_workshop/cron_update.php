#!/usr/bin/env php
<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop cron update script
 *
 * Usage:
 *   php modules/steam_workshop/cron_update.php --all
 *   php modules/steam_workshop/cron_update.php --agent-id=<ID>
 *   php modules/steam_workshop/cron_update.php --home-id=<ID>
 *   php modules/steam_workshop/cron_update.php --profile-id=<ID>
 *   php modules/steam_workshop/cron_update.php --workshop-id=<WID> --agent-id=<AID> --app-id=<APPID>
 *
 * This script:
 *   1. Finds enabled installed mods from gsp_server_workshop_mods.
 *   2. Groups them by agent_id and workshop_app_id.
 *   3. For each unique (agent, appid, workshop_id), runs SteamCMD
 *      workshop_download_item validate on the agent.
 *   4. Updates gsp_workshop_cache (status, last_checked, last_updated, last_error).
 *   5. Does NOT copy into running servers.
 *   6. Does NOT restart servers.
 *   7. Logs all update attempts.
 *
 * Run from the panel root directory:
 *   cd /var/www/html && php modules/steam_workshop/cron_update.php --all
 */

// -----------------------------------------------------------------------
// Bootstrap: load panel includes
// -----------------------------------------------------------------------

// Determine panel root
$panelRoot = defined('PANEL_ROOT') ? PANEL_ROOT : realpath(__DIR__ . '/../../..');
if ($panelRoot === false) {
    $panelRoot = __DIR__ . '/../../..';
}
chdir($panelRoot);

// Load configuration
if (!is_file('includes/config.inc.php')) {
    fwrite(STDERR, "[ERROR] Cannot locate includes/config.inc.php. Run this script from the panel root.\n");
    exit(1);
}

require_once 'includes/config.inc.php';
require_once 'includes/database.php';
require_once 'includes/database_mysqli.php';
require_once 'includes/lib_remote.php';

// Connect to database
if (!isset($db_host, $db_user, $db_pass, $db_name)) {
    fwrite(STDERR, "[ERROR] Database configuration variables not set.\n");
    exit(1);
}

$db = new OGPDatabaseMySQL();
/** @var int|true $connResult */
$connResult = $db->connect(
    $db_host,
    $db_user,
    $db_pass,
    $db_name,
    $table_prefix ?? 'gsp_',
    $db_port ?? null
);

if ($connResult !== true) {
    fwrite(STDERR, "[ERROR] Database connection failed (code: {$connResult}).\n");
    exit(1);
}

require_once __DIR__ . '/lib/WorkshopRepository.php';
require_once __DIR__ . '/lib/WorkshopInstaller.php';
require_once __DIR__ . '/lib/WorkshopUpdater.php';

$repo      = new WorkshopRepository($db);
$installer = new WorkshopInstaller($repo);
$updater   = new WorkshopUpdater($repo, $installer);

// -----------------------------------------------------------------------
// Parse CLI arguments
// -----------------------------------------------------------------------

$opts = getopt('', [
    'all',
    'agent-id:',
    'home-id:',
    'profile-id:',
    'workshop-id:',
    'app-id:',
    'help',
]);

if (isset($opts['help']) || $opts === false || empty($opts)) {
    echo <<<HELP
GSP Steam Workshop – cron cache updater

Usage:
  php cron_update.php --all
  php cron_update.php --agent-id=<ID>
  php cron_update.php --home-id=<ID>
  php cron_update.php --profile-id=<ID>
  php cron_update.php --workshop-id=<WID> --agent-id=<AID> --app-id=<APPID>

HELP;
    exit(0);
}

// -----------------------------------------------------------------------
// Execute the requested update
// -----------------------------------------------------------------------

function printResults(array $results): void
{
    $ok   = 0;
    $fail = 0;
    foreach ($results as $r) {
        $status = $r['success'] ? 'OK   ' : 'FAIL ';
        if ($r['success']) {
            $ok++;
        } else {
            $fail++;
        }
        $msg = $r['message'] ?? '';
        echo "[{$status}] agent={$r['agent_id']} app={$r['workshop_app_id']} mod={$r['workshop_id']} – {$msg}\n";
    }
    echo "Done: {$ok} succeeded, {$fail} failed.\n";
}

if (isset($opts['all'])) {
    echo "[INFO] Updating all enabled Workshop mods…\n";
    $results = $updater->updateAll();
    printResults($results);
    exit(0);
}

if (isset($opts['agent-id']) && !isset($opts['workshop-id'])) {
    $agentId = (int)$opts['agent-id'];
    echo "[INFO] Updating Workshop mods for agent {$agentId}…\n";
    $results = $updater->updateWorkshopCacheForAgent($agentId);
    printResults($results);
    exit(0);
}

if (isset($opts['home-id'])) {
    $homeId = (int)$opts['home-id'];
    echo "[INFO] Updating Workshop mods for home {$homeId}…\n";
    $results = $updater->updateWorkshopCacheForHome($homeId);
    printResults($results);
    exit(0);
}

if (isset($opts['profile-id'])) {
    $profileId = (int)$opts['profile-id'];
    echo "[INFO] Updating Workshop mods for profile {$profileId}…\n";
    $results = $updater->updateWorkshopCacheForProfile($profileId);
    printResults($results);
    exit(0);
}

if (isset($opts['workshop-id'], $opts['agent-id'], $opts['app-id'])) {
    $workshopId = preg_replace('/[^0-9]/', '', (string)$opts['workshop-id']) ?? '';
    $agentId    = (int)$opts['agent-id'];
    $appId      = preg_replace('/[^0-9]/', '', (string)$opts['app-id']) ?? '';

    if ($workshopId === '' || $appId === '') {
        fwrite(STDERR, "[ERROR] --workshop-id and --app-id must be numeric.\n");
        exit(1);
    }

    echo "[INFO] Updating single mod: agent={$agentId} app={$appId} mod={$workshopId}…\n";
    $result = $updater->updateSingleWorkshopMod($agentId, $appId, $workshopId);
    printResults([$result]);
    exit(0);
}

fwrite(STDERR, "[ERROR] No valid option provided. Use --help for usage.\n");
exit(1);
