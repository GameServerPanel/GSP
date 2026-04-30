#!/usr/bin/env php
<?php
declare(strict_types=1);
/*
 * OGP / GSP – Steam Workshop pre-start sync helper
 *
 * Called from the game XML <pre_start> tag or a server pre-start hook:
 *   php modules/steam_workshop/prestart_sync.php --home-id=<ID>
 *
 * This script:
 *  1. Finds all enabled Workshop mods for the given home.
 *  2. Checks each mod's local cache on the agent.
 *  3. If the cache differs from the server install path, syncs it.
 *  4. Continues normal server start (exits 0 on success).
 *  5. Exits non-zero ONLY if a critical error prevents completion.
 *
 * Design note: sync failures are logged but do NOT abort the server start,
 * because a stale mod is better than no start.
 */

$panelRoot = defined('PANEL_ROOT') ? PANEL_ROOT : realpath(__DIR__ . '/../../..');
if ($panelRoot === false) {
    $panelRoot = __DIR__ . '/../../..';
}
chdir($panelRoot);

if (!is_file('includes/config.inc.php')) {
    fwrite(STDERR, "[ERROR] Cannot locate includes/config.inc.php.\n");
    exit(0); // don't block server start
}

require_once 'includes/config.inc.php';
require_once 'includes/database.php';
require_once 'includes/database_mysqli.php';
require_once 'includes/lib_remote.php';

if (!isset($db_host, $db_user, $db_pass, $db_name)) {
    fwrite(STDERR, "[ERROR] Database configuration not set.\n");
    exit(0);
}

$db = new OGPDatabaseMySQL();
$connResult = $db->connect($db_host, $db_user, $db_pass, $db_name, $table_prefix ?? 'gsp_', $db_port ?? null);
if ($connResult !== true) {
    fwrite(STDERR, "[ERROR] DB connect failed: {$connResult}\n");
    exit(0);
}

require_once __DIR__ . '/lib/WorkshopRepository.php';
require_once __DIR__ . '/lib/WorkshopInstaller.php';
require_once __DIR__ . '/lib/WorkshopPreStart.php';

$opts = getopt('', ['home-id:', 'help']);

if (isset($opts['help']) || !isset($opts['home-id'])) {
    echo "Usage: php prestart_sync.php --home-id=<ID>\n";
    exit(0);
}

$homeId = (int)$opts['home-id'];
if ($homeId <= 0) {
    fwrite(STDERR, "[ERROR] --home-id must be a positive integer.\n");
    exit(0);
}

$home = $db->getGameHome($homeId);
if (!is_array($home)) {
    fwrite(STDERR, "[WARN] Home {$homeId} not found – skipping pre-start sync.\n");
    exit(0);
}

$repo      = new WorkshopRepository($db);
$installer = new WorkshopInstaller($repo);
$preStart  = new WorkshopPreStart($repo, $installer);

$result = $preStart->syncModsForHome($home);

echo sprintf(
    "[PRE-START] home=%d synced=%d skipped=%d failed=%d\n",
    $homeId,
    $result['synced'],
    $result['skipped'],
    $result['failed']
);

foreach ((array)($result['log'] ?? []) as $line) {
    echo "  {$line}\n";
}

// Always exit 0 – don't block server start due to Workshop sync issues
exit(0);
