<?php
/*
 * GSP – Steam Workshop: Agent CLI update script
 * Copyright (C) 2025 WDS / GameServerPanel
 *
 * This file must only be run from the command line (CLI).
 * Do NOT expose it through a web server.
 *
 * Usage:
 *   php agent_update_workshop.php --home-id=123
 *   php agent_update_workshop.php --all
 *
 * The script connects to the panel database, reads the list of enabled mods
 * for the specified server(s), downloads/updates each mod via SteamCMD,
 * copies mod folders into the server root, copies .bikey files into the
 * server keys/ directory, and updates the install status in the database.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

// ── Safety: CLI only ──────────────────────────────────────────────────────
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit("This script may only be run from the command line.\n");
}

// ── Bootstrap ─────────────────────────────────────────────────────────────
$panel_root = realpath(__DIR__ . '/../../..');
if (!$panel_root || !is_dir($panel_root)) {
    fwrite(STDERR, "ERROR: Cannot locate panel root from " . __DIR__ . "\n");
    exit(1);
}

$config_file = $panel_root . '/includes/config.inc.php';
if (!is_file($config_file)) {
    fwrite(STDERR, "ERROR: Panel config not found: $config_file\n");
    fwrite(STDERR, "       Copy includes/config.inc.php.example to includes/config.inc.php and set credentials.\n");
    exit(1);
}

require_once $config_file;
require_once $panel_root . '/includes/helpers.php';
require_once $panel_root . '/includes/database_mysqli.php';
require_once __DIR__ . '/includes/functions.php';

// ── Database connection ───────────────────────────────────────────────────
// Variables $db_host, $db_user, $db_pass, $db_name, $table_prefix, $db_type,
// $db_port come from config.inc.php (loaded above).
$db = createDatabaseConnection(
    $db_type,
    $db_host,
    $db_user,
    $db_pass,
    $db_name,
    $table_prefix,
    isset($db_port) ? $db_port : null
);

if (!is_object($db)) {
    $error_text = '';
    get_db_error_text($db, $error_text);
    fwrite(STDERR, "ERROR: Database connection failed: $error_text\n");
    exit(1);
}

// ── Argument parsing ──────────────────────────────────────────────────────
$opts = getopt('', array('home-id:', 'all', 'dry-run'));

$do_all    = array_key_exists('all',      $opts);
$dry_run   = array_key_exists('dry-run',  $opts);
$target_id = isset($opts['home-id']) ? (int)$opts['home-id'] : 0;

if (!$do_all && !$target_id) {
    fwrite(STDERR, "Usage:\n");
    fwrite(STDERR, "  php agent_update_workshop.php --home-id=123\n");
    fwrite(STDERR, "  php agent_update_workshop.php --all\n");
    fwrite(STDERR, "  Add --dry-run to simulate without running SteamCMD or copying files.\n");
    exit(1);
}

if ($dry_run) {
    echo "[DRY RUN] No files will be modified and SteamCMD will not be called.\n";
}

// ── Collect home IDs to process ───────────────────────────────────────────
if ($do_all) {
    // Find all home_ids that have at least one enabled queued mod with an enabled profile.
    $rows = $db->resultQuery(
        "SELECT DISTINCT m.home_id
           FROM " . sw_table('steam_workshop_server_mods') . " m
           JOIN " . sw_table('steam_workshop_game_profiles') . " p ON p.id = m.profile_id
          WHERE m.enabled = 1 AND p.enabled = 1 AND m.install_status = 'queued'"
    );
    $home_ids = $rows ? array_column($rows, 'home_id') : array();
} else {
    $home_ids = array($target_id);
}

if (empty($home_ids)) {
    echo "No servers with enabled Workshop mods found.\n";
    exit(0);
}

$overall_success = true;

foreach ($home_ids as $home_id) {
    $home_id = (int)$home_id;
    echo "\n=== Processing home_id=$home_id ===\n";

    $ok = sw_agent_process_home($db, $home_id, $dry_run);
    if (!$ok) {
        $overall_success = false;
        echo "  [WARN] One or more errors occurred for home_id=$home_id.\n";
    }
}

exit($overall_success ? 0 : 1);

// ─────────────────────────────────────────────────────────────────────────
// Core logic
// ─────────────────────────────────────────────────────────────────────────

/**
 * Process all enabled mods for one server home.
 *
 * @param  OGPDatabase $db
 * @param  int         $home_id
 * @param  bool        $dry_run
 * @return bool  true if all mods processed without errors
 */
function sw_agent_process_home($db, $home_id, $dry_run)
{
    // Load server info
    $home = sw_get_home_info($db, $home_id);
    if (!$home) {
        echo "  [ERROR] Server home $home_id not found in database.\n";
        return false;
    }

    echo "  Server: " . $home['home_name'] . " (game: " . $home['game_name'] . ")\n";
    echo "  Path:   " . $home['home_path'] . "\n";

    // Resolve Workshop profile via game config key
    $profile = sw_get_profile_for_home($db, $home_id);
    if (!$profile) {
        echo "  [SKIP] No enabled Workshop profile for this game type.\n";
        return true;
    }

    echo "  Profile: " . $profile['config_name'] . " (workshop_app_id=" . $profile['workshop_app_id'] . ")\n";

    // Build common template variables
    $server_root = sw_apply_template(
        $profile['server_root_template'] ?: $home['home_path'],
        sw_agent_tpl_vars($home, $profile)
    );
    $server_root = rtrim($server_root, '/');

    // Load queued+enabled mods, sorted by sort_order
    $mods = sw_agent_get_queued_mods($db, $home_id);

    if (empty($mods)) {
        echo "  No queued Workshop updates for this server.\n";
        return true;
    }

    $keys_dir = $server_root . '/keys';
    if (!$dry_run && !is_dir($keys_dir)) {
        @mkdir($keys_dir, 0755, true);
    }

    $all_ok = true;

    foreach ($mods as $mod) {
        $mod_id      = (int)$mod['id'];
        $workshop_id = $mod['workshop_id'];
        echo "\n  Mod: " . ($mod['mod_name'] ?: $workshop_id) . " [ID=$workshop_id]\n";

        // Mark as updating
        if (!$dry_run) {
            $db->query(
                "UPDATE " . sw_table('steam_workshop_server_mods') . "
                    SET `install_status` = 'updating', `last_error` = NULL, `updated_at` = NOW()
                  WHERE `id` = $mod_id LIMIT 1"
            );
        }

        // Build template vars for this mod
        $folder_name = $mod['folder_name'] ?: ('@' . $workshop_id);
        $tpl_vars    = array_merge(
            sw_agent_tpl_vars($home, $profile),
            array(
                'WORKSHOP_ID'         => $workshop_id,
                'MOD_NAME'            => $mod['mod_name'] ?: $workshop_id,
                'FOLDER_NAME'         => $folder_name,
                'MOD_FOLDER'          => $folder_name,
                'SERVER_ROOT'         => $server_root,
                'WORKSHOP_DOWNLOAD_DIR' => sw_apply_template(
                    $profile['workshop_download_dir_template']
                        ?: ($server_root . '/steamapps/workshop/content/' . $profile['workshop_app_id']),
                    array(
                        'SERVER_ROOT'     => $server_root,
                        'WORKSHOP_APP_ID' => $profile['workshop_app_id'],
                        'HOME_ID'         => $home['home_id'],
                    )
                ),
            )
        );

        $download_dir = $tpl_vars['WORKSHOP_DOWNLOAD_DIR'];
        $mod_cache    = rtrim($download_dir, '/') . '/' . $workshop_id;

        // 1. Download / update via SteamCMD
        $cmd_result = sw_agent_steamcmd_download($mod, $profile, $tpl_vars, $dry_run);
        if (!$cmd_result['ok']) {
            $err = $cmd_result['error'];
            echo "    [ERROR] SteamCMD failed: $err\n";
            if (!$dry_run) {
                $safe_err = $db->realEscapeSingle($err);
                $db->query(
                    "UPDATE " . sw_table('steam_workshop_server_mods') . "
                        SET `install_status` = 'failed', `last_error` = '$safe_err', `updated_at` = NOW()
                      WHERE `id` = $mod_id LIMIT 1"
                );
            }
            $all_ok = false;
            continue;
        }

        // 2. Copy / sync mod folder to server root
        $install_path = sw_apply_template(
            $profile['install_path_template'] ?: ($server_root . '/{MOD_FOLDER}'),
            $tpl_vars
        );

        $copy_ok = sw_agent_copy_mod($mod_cache, $install_path, $dry_run);
        if (!$copy_ok) {
            $err = "Failed to copy mod from $mod_cache to $install_path";
            echo "    [ERROR] $err\n";
            if (!$dry_run) {
                $safe_err = $db->realEscapeSingle($err);
                $db->query(
                    "UPDATE " . sw_table('steam_workshop_server_mods') . "
                        SET `install_status` = 'failed', `last_error` = '$safe_err', `updated_at` = NOW()
                      WHERE `id` = $mod_id LIMIT 1"
                );
            }
            $all_ok = false;
            continue;
        }

        // 3. Copy .bikey files to server keys/ directory
        if (!empty($profile['copy_bikeys_enabled'])) {
            sw_agent_copy_bikeys($install_path, $keys_dir, $dry_run);
        }

        // 4. Mark as installed
        if (!$dry_run) {
            $db->query(
                "UPDATE " . sw_table('steam_workshop_server_mods') . "
                    SET `install_status`    = 'installed',
                        `last_installed_at` = NOW(),
                        `last_updated_at`   = NOW(),
                        `last_error`        = NULL,
                        `updated_at`        = NOW()
                  WHERE `id` = $mod_id LIMIT 1"
            );
        }

        echo "    [OK] Installed → $install_path\n";
    }

    return $all_ok;
}

function sw_agent_get_queued_mods($db, $home_id)
{
    $home_id = (int)$home_id;
    $rows = $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_server_mods') . "
          WHERE `home_id` = $home_id
            AND `enabled` = 1
            AND `install_status` = 'queued'
          ORDER BY `sort_order` ASC, `id` ASC"
    );
    return $rows ? $rows : array();
}

/**
 * Build the standard template variable map for a given home + profile.
 *
 * @param  array $home
 * @param  array $profile
 * @return array
 */
function sw_agent_tpl_vars(array $home, array $profile)
{
    return array(
        'HOME_ID'          => $home['home_id'],
        'SERVER_ID'        => $home['home_id'],
        'REMOTE_SERVER_ID' => $home['remote_server_id'],
        'GAME_NAME'        => $home['game_name'],
        'CONFIG_NAME'      => $home['game_key'],
        'STEAM_APP_ID'     => $profile['steam_app_id'],
        'WORKSHOP_APP_ID'  => $profile['workshop_app_id'],
        'STEAMCMD_PATH'    => $profile['steamcmd_path'],
        'SERVER_ROOT'      => $home['home_path'],
        'INSTALL_PATH'     => $home['home_path'],
    );
}

/**
 * Run SteamCMD to download / update a single Workshop item.
 *
 * Uses the profile's update_script_template if set; otherwise falls back to
 * a standard anonymous or authenticated +workshop_download_item invocation.
 *
 * @param  array  $mod
 * @param  array  $profile
 * @param  array  $tpl_vars
 * @param  bool   $dry_run
 * @return array  ['ok' => bool, 'error' => string]
 */
function sw_agent_steamcmd_download(array $mod, array $profile, array $tpl_vars, $dry_run)
{
    $steamcmd     = $profile['steamcmd_path'] ?: '/home/gameserver/steamcmd/steamcmd.sh';
    $workshop_id  = $mod['workshop_id'];
    $app_id       = $profile['workshop_app_id'];
    $dl_dir       = $tpl_vars['WORKSHOP_DOWNLOAD_DIR'];

    if (!empty($profile['update_script_template'])) {
        // Admin has provided a custom update script.
        $script_body = sw_apply_template($profile['update_script_template'], $tpl_vars);
        return sw_agent_run_script($script_body, $dry_run);
    }

    // Build default SteamCMD command.
    // +force_install_dir is set to the parent of the workshop content so that
    // SteamCMD places files in <dl_dir>/<workshop_id>/.
    $parent_dir = dirname($dl_dir); // .../steamapps/workshop/content

    if ($profile['steamcmd_login_mode'] === 'account') {
        // When account login is required the operator must supply credentials
        // in the update_script_template.  We cannot safely store a password here.
        return array(
            'ok'    => false,
            'error' => "Account login is required for this profile but no update_script_template is set. "
                     . "Add a custom update_script_template in the admin profile that includes SteamCMD login credentials.",
        );
    }

    // Validate that steamcmd exists
    if (!$dry_run) {
        if (!is_file($steamcmd)) {
            return array('ok' => false, 'error' => "SteamCMD not found: $steamcmd");
        } elseif (!is_executable($steamcmd)) {
            return array('ok' => false, 'error' => "SteamCMD is not executable: $steamcmd");
        }
    }

    // Build argument list; escape each argument individually.
    $args = array(
        escapeshellarg($steamcmd),
        '+force_install_dir', escapeshellarg($parent_dir),
        '+login', 'anonymous',
        '+workshop_download_item', escapeshellarg($app_id), escapeshellarg($workshop_id),
        '+quit',
    );
    $cmd = implode(' ', $args);

    echo "    SteamCMD: $cmd\n";

    if ($dry_run) {
        echo "    [DRY RUN] Skipping SteamCMD execution.\n";
        return array('ok' => true, 'error' => '');
    }

    $output     = array();
    $return_var = 0;
    exec($cmd . ' 2>&1', $output, $return_var);

    foreach ($output as $line) {
        echo "      $line\n";
    }

    if ($return_var !== 0) {
        return array(
            'ok'    => false,
            'error' => "SteamCMD exited with code $return_var. " . implode(' ', array_slice($output, -3)),
        );
    }

    return array('ok' => true, 'error' => '');
}

/**
 * Execute a shell script body.
 * The script is written to a temporary file and executed with /bin/sh.
 *
 * @param  string $script_body
 * @param  bool   $dry_run
 * @return array  ['ok' => bool, 'error' => string]
 */
function sw_agent_run_script($script_body, $dry_run)
{
    if ($dry_run) {
        echo "    [DRY RUN] Would execute script:\n";
        foreach (explode("\n", $script_body) as $line) {
            echo "      $line\n";
        }
        return array('ok' => true, 'error' => '');
    }

    $tmp = tempnam(sys_get_temp_dir(), 'sw_agent_');
    if (!$tmp) {
        return array('ok' => false, 'error' => 'Could not create temporary file for script.');
    }

    file_put_contents($tmp, "#!/bin/sh\nset -e\n" . $script_body);
    chmod($tmp, 0700);

    $output     = array();
    $return_var = 0;
    exec('/bin/sh ' . escapeshellarg($tmp) . ' 2>&1', $output, $return_var);
    @unlink($tmp);

    foreach ($output as $line) {
        echo "      $line\n";
    }

    if ($return_var !== 0) {
        return array(
            'ok'    => false,
            'error' => "Script exited with code $return_var. " . implode(' ', array_slice($output, -3)),
        );
    }

    return array('ok' => true, 'error' => '');
}

/**
 * Copy (rsync-style) the downloaded mod folder into the server root.
 * Uses rsync when available, falls back to recursive PHP copy.
 *
 * @param  string $src    Downloaded mod folder (e.g. .../content/221100/2863534533)
 * @param  string $dst    Target path in server root (e.g. /servers/123/@CF)
 * @param  bool   $dry_run
 * @return bool
 */
function sw_agent_copy_mod($src, $dst, $dry_run)
{
    echo "    Copy: $src → $dst\n";

    if (!$dry_run && !is_dir($src)) {
        echo "    [WARN] Source directory not found: $src\n";
        return false;
    }

    if ($dry_run) {
        return true;
    }

    if (!is_dir($dst)) {
        if (!@mkdir($dst, 0755, true)) {
            return false;
        }
    }

    // Try rsync first (preserves permissions, handles deletes cleanly).
    if (sw_agent_cmd_exists('rsync')) {
        $cmd = 'rsync -a --delete '
             . escapeshellarg(rtrim($src, '/') . '/') . ' '
             . escapeshellarg(rtrim($dst, '/') . '/') . ' 2>&1';
        exec($cmd, $out, $ret);
        if ($ret === 0) {
            return true;
        }
        echo "    [WARN] rsync failed (exit $ret); falling back to PHP copy.\n";
    }

    // PHP recursive copy fallback.
    return sw_agent_recursive_copy($src, $dst);
}

/**
 * Copy all .bikey files found recursively under $mod_dir/keys/ into $keys_dir.
 *
 * @param  string $mod_dir   Installed mod directory
 * @param  string $keys_dir  Server keys directory
 * @param  bool   $dry_run
 * @return void
 */
function sw_agent_copy_bikeys($mod_dir, $keys_dir, $dry_run)
{
    // Search in common key locations within the mod folder.
    $search_dirs = array(
        $mod_dir . '/keys',
        $mod_dir . '/Keys',
        $mod_dir . '/key',
        $mod_dir . '/Key',
    );

    $found = 0;
    foreach ($search_dirs as $kdir) {
        if (!is_dir($kdir)) {
            continue;
        }
        foreach (glob($kdir . '/*.bikey') as $bikey) {
            $target = $keys_dir . '/' . basename($bikey);
            echo "    .bikey: " . basename($bikey) . " → $keys_dir/\n";
            if (!$dry_run) {
                @copy($bikey, $target);
            }
            $found++;
        }
    }

    if ($found === 0) {
        echo "    (no .bikey files found in mod keys/ folder)\n";
    }
}

/**
 * Return true if a command is available in PATH.
 *
 * @param  string $cmd
 * @return bool
 */
function sw_agent_cmd_exists($cmd)
{
    $which = trim((string)shell_exec('which ' . escapeshellarg($cmd) . ' 2>/dev/null'));
    return !empty($which);
}

/**
 * Recursively copy directory $src into $dst (creating $dst if needed).
 *
 * @param  string $src
 * @param  string $dst
 * @return bool
 */
function sw_agent_recursive_copy($src, $dst)
{
    $dir = @opendir($src);
    if (!$dir) {
        return false;
    }
    if (!is_dir($dst)) {
        @mkdir($dst, 0755, true);
    }
    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        $s = $src . '/' . $file;
        $d = $dst . '/' . $file;
        if (is_dir($s)) {
            sw_agent_recursive_copy($s, $d);
        } else {
            copy($s, $d);
        }
    }
    closedir($dir);
    return true;
}
