<?php
/*
 * GSP – Steam Workshop: shared helper functions
 * Copyright (C) 2025 WDS / GameServerPanel
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

function sw_db_prefix()
{
    if (defined('DB_PREFIX') && DB_PREFIX !== '') {
        return DB_PREFIX;
    }
    if (isset($GLOBALS['db_prefix']) && $GLOBALS['db_prefix'] !== '') {
        return $GLOBALS['db_prefix'];
    }
    if (isset($GLOBALS['table_prefix']) && $GLOBALS['table_prefix'] !== '') {
        return $GLOBALS['table_prefix'];
    }
    return 'gsp_';
}

function sw_table($tableName)
{
    return '`' . sw_db_prefix() . $tableName . '`';
}

// ── Profile helpers ───────────────────────────────────────────────────────

/**
 * Return all rows from steam_workshop_game_profiles ordered by game_name.
 *
 * @param  OGPDatabase $db
 * @return array|false
 */
function sw_get_profiles($db)
{
    return $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_game_profiles') . "
          ORDER BY `game_name` ASC, `config_name` ASC"
    );
}

/**
 * Return a single profile row by primary key.
 *
 * @param  OGPDatabase $db
 * @param  int         $id
 * @return array|false
 */
function sw_get_profile_by_id($db, $id)
{
    $id = (int)$id;
    $rows = $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_game_profiles') . "
          WHERE `id` = $id LIMIT 1"
    );
    return ($rows && isset($rows[0])) ? $rows[0] : false;
}

/**
 * Return a single profile row by config_name (= game_key from XML).
 *
 * @param  OGPDatabase $db
 * @param  string      $config_name
 * @return array|false
 */
function sw_get_profile_by_config_name($db, $config_name)
{
    $safe = $db->realEscapeSingle($config_name);
    $rows = $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_game_profiles') . "
          WHERE `config_name` = '$safe' LIMIT 1"
    );
    return ($rows && isset($rows[0])) ? $rows[0] : false;
}

/**
 * Return the Workshop profile that applies to a server home.
 * Resolves: home_id → config_homes.game_key → workshop profile.
 *
 * @param  OGPDatabase $db
 * @param  int         $home_id
 * @return array|false  profile row or false when none found / not enabled
 */
function sw_get_profile_for_home($db, $home_id)
{
    $home_id = (int)$home_id;
    $rows = $db->resultQuery(
        "SELECT p.*
           FROM " . sw_table('steam_workshop_game_profiles') . " p
           JOIN " . sw_table('config_homes') . " c
              ON c.`game_key` = p.`config_name`
           JOIN " . sw_table('server_homes') . " s
              ON s.`home_cfg_id` = c.`home_cfg_id`
          WHERE s.`home_id` = $home_id
            AND p.`enabled` = 1
          LIMIT 1"
    );
    return ($rows && isset($rows[0])) ? $rows[0] : false;
}

// ── Mod helpers ───────────────────────────────────────────────────────────

/**
 * Return all mods for a server, sorted by sort_order ASC.
 *
 * @param  OGPDatabase $db
 * @param  int         $home_id
 * @return array|false
 */
function sw_get_server_mods($db, $home_id)
{
    $home_id = (int)$home_id;
    return $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_server_mods') . "
          WHERE `home_id` = $home_id
          ORDER BY `sort_order` ASC, `id` ASC"
    );
}

/**
 * Return a single mod row by primary key.
 *
 * @param  OGPDatabase $db
 * @param  int         $id
 * @return array|false
 */
function sw_get_mod_by_id($db, $id)
{
    $id = (int)$id;
    $rows = $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_server_mods') . "
          WHERE `id` = $id LIMIT 1"
    );
    return ($rows && isset($rows[0])) ? $rows[0] : false;
}

// ── Server / ownership helpers ────────────────────────────────────────────

/**
 * Return server_homes row joined with config_homes and remote_servers
 * for the given home_id, or false when not found.
 *
 * @param  OGPDatabase $db
 * @param  int         $home_id
 * @return array|false
 */
function sw_get_home_info($db, $home_id)
{
    $home_id = (int)$home_id;
    $rows = $db->resultQuery(
        "SELECT s.*, c.`game_key`, c.`game_name`, r.`agent_ip`, r.`agent_port`
           FROM " . sw_table('server_homes') . " s
           JOIN " . sw_table('config_homes') . " c  ON c.`home_cfg_id` = s.`home_cfg_id`
           JOIN " . sw_table('remote_servers') . " r ON r.`remote_server_id` = s.`remote_server_id`
          WHERE s.`home_id` = $home_id LIMIT 1"
    );
    return ($rows && isset($rows[0])) ? $rows[0] : false;
}

/**
 * Verify that the current session user is allowed to manage this home.
 * Admins always pass.  Regular users/subusers must have an entry in
 * user_homes (or be the user_id_main).
 *
 * @param  OGPDatabase $db
 * @param  int         $user_id
 * @param  int         $home_id
 * @return bool
 */
function sw_user_owns_home($db, $user_id, $home_id)
{
    if (!isset($_SESSION['users_group'])) {
        return false;
    }
    if ($_SESSION['users_group'] === 'admin') {
        return true;
    }

    $user_id = (int)$user_id;
    $home_id = (int)$home_id;

    // Direct owner
    $rows = $db->resultQuery(
        "SELECT 1 FROM " . sw_table('server_homes') . "
          WHERE `home_id` = $home_id AND `user_id_main` = $user_id LIMIT 1"
    );
    if ($rows) {
        return true;
    }

    // Assigned via user_homes
    $rows = $db->resultQuery(
        "SELECT 1 FROM " . sw_table('user_homes') . "
          WHERE `home_id` = $home_id AND `user_id` = $user_id LIMIT 1"
    );
    if ($rows) {
        return true;
    }

    // Assigned via group
    $rows = $db->resultQuery(
        "SELECT 1 FROM " . sw_table('user_group_homes') . " ugh
           JOIN " . sw_table('user_groups') . " ug ON ug.`group_id` = ugh.`group_id`
          WHERE ugh.`home_id` = $home_id AND ug.`user_id` = $user_id LIMIT 1"
    );
    return (bool)$rows;
}

// ── Game-config helpers ───────────────────────────────────────────────────

/**
 * Return an array of all game configs from the XML files.
 * Each element is a SimpleXMLElement (game_config root node).
 *
 * @return SimpleXMLElement[]
 */
function sw_get_all_game_configs()
{
    if (!defined('SERVER_CONFIG_LOCATION')) {
        // server_config_parser.php defines this; load it if not already done.
        if (file_exists(__DIR__ . '/../../config_games/server_config_parser.php')) {
            require_once __DIR__ . '/../../config_games/server_config_parser.php';
        } else {
            return array();
        }
    }

    $configs = array();
    foreach (glob(SERVER_CONFIG_LOCATION . '*.xml') as $file) {
        $xml = read_server_config($file);
        if ($xml !== false) {
            $configs[] = $xml;
        }
    }
    return $configs;
}

/**
 * Ensure every game config has a matching row in steam_workshop_game_profiles.
 * Only creates rows that are missing; never overwrites existing data.
 *
 * @param  OGPDatabase $db
 * @return int  number of new rows inserted
 */
function sw_sync_profiles($db)
{
    $configs = sw_get_all_game_configs();
    $created = 0;

    foreach ($configs as $xml) {
        $config_name = (string)$xml->game_key;
        $game_name   = (string)$xml->game_name;

        if (empty($config_name)) {
            continue;
        }

        $existing = sw_get_profile_by_config_name($db, $config_name);
        if ($existing) {
            continue; // already have a profile for this game config
        }

        $safe_config = $db->realEscapeSingle($config_name);
        $safe_name   = $db->realEscapeSingle($game_name);
        $ok = $db->query("INSERT IGNORE INTO " . sw_table('steam_workshop_game_profiles') . " (`config_name`, `game_name`, `enabled`) VALUES ('$safe_config', '$safe_name', 0)");
        if ($ok) {
            $created++;
        }
    }

    return $created;
}

// ── Template / launch-param helpers ─────────────────────────────────────

/**
 * Replace {PLACEHOLDER} tokens in $template with values from $vars.
 * Unknown tokens are left intact so admins can spot missing values.
 *
 * @param  string $template
 * @param  array  $vars  associative: 'PLACEHOLDER' => 'value'
 * @return string
 */
function sw_apply_template($template, array $vars)
{
    $search  = array();
    $replace = array();
    foreach ($vars as $key => $value) {
        $search[]  = '{' . $key . '}';
        $replace[] = (string)$value;
    }
    return str_replace($search, $replace, $template);
}

/**
 * Build the -mod= and -serverMod= launch parameter strings from an ordered
 * list of enabled mods and the game profile.
 *
 * Returns an associative array:
 *   'mod'       => '-mod=@Mod1;@Mod2'        (client mods)
 *   'servermod' => '-serverMod=@ServerOnly'   (server-side mods)
 *   'combined'  => '-mod=... -serverMod=...'  (ready-to-paste)
 *
 * @param  array $mods     rows from steam_workshop_server_mods (must be pre-filtered
 *                         for enabled = 1 and sorted by sort_order)
 * @param  array $profile  row from steam_workshop_game_profiles
 * @return array
 */
function sw_generate_launch_params(array $mods, array $profile)
{
    $mod_param       = trim($profile['mod_launch_param_template']       ?? '-mod=');
    $servermod_param = trim($profile['servermod_launch_param_template'] ?? '-serverMod=');

    $client_folders = array();
    $server_folders = array();

    foreach ($mods as $mod) {
        if (empty($mod['enabled'])) {
            continue;
        }
        $folder = !empty($mod['folder_name']) ? $mod['folder_name'] : ('@' . $mod['workshop_id']);
        if ($mod['mod_type'] === 'server') {
            $server_folders[] = $folder;
        } else {
            $client_folders[] = $folder;
        }
    }

    $mod_str       = $client_folders ? ($mod_param       . implode(';', $client_folders)) : '';
    $servermod_str = $server_folders ? ($servermod_param . implode(';', $server_folders)) : '';
    $combined      = trim($mod_str . ' ' . $servermod_str);

    return array(
        'mod'       => $mod_str,
        'servermod' => $servermod_str,
        'combined'  => $combined,
    );
}

// ── Server behavior settings helpers ─────────────────────────────────────

/**
 * Return the workshop behavior settings row for a home, or an array of
 * safe defaults when no row exists yet.
 *
 * @param  OGPDatabase $db
 * @param  int         $home_id
 * @return array
 */
function sw_get_server_settings($db, $home_id)
{
    $home_id = (int)$home_id;
    $rows = $db->resultQuery(
        "SELECT * FROM " . sw_table('steam_workshop_server_settings') . "
          WHERE `home_id` = $home_id LIMIT 1"
    );
    if ($rows && isset($rows[0]) && is_array($rows[0])) {
        $settings = $rows[0];

        // Runtime normalization is kept as a fallback for legacy/manual rows that
        // were not updated via module migrations.
        $legacyUpdateMap = array(
            'scheduled' => 'manual',
        );
        $legacyRestartMap = array(
            'if_empty'     => 'if_stopped',
            'next_restart' => 'if_stopped',
            'immediate'    => 'none',
        );
        $legacyScheduleMap = array(
            'hourly' => 'daily',
        );

        if (isset($legacyUpdateMap[$settings['update_mode'] ?? ''])) {
            $settings['update_mode'] = $legacyUpdateMap[$settings['update_mode']];
        }
        if (isset($legacyRestartMap[$settings['restart_behavior'] ?? ''])) {
            $settings['restart_behavior'] = $legacyRestartMap[$settings['restart_behavior']];
        }
        if (isset($legacyScheduleMap[$settings['schedule_interval'] ?? ''])) {
            $settings['schedule_interval'] = $legacyScheduleMap[$settings['schedule_interval']];
        }

        $validUpdateModes      = array('manual', 'on_restart', 'before_start');
        $validRestartBehaviors = array('none', 'if_stopped');
        $validIntervals        = array('disabled', 'daily', 'weekly');

        if (!in_array($settings['update_mode'] ?? '', $validUpdateModes, true)) {
            $settings['update_mode'] = 'manual';
        }
        if (!in_array($settings['restart_behavior'] ?? '', $validRestartBehaviors, true)) {
            $settings['restart_behavior'] = 'none';
        }
        if (!in_array($settings['schedule_interval'] ?? '', $validIntervals, true)) {
            $settings['schedule_interval'] = 'disabled';
        }

        return $settings;
    }

    // Safe defaults – manual only, no automatic restarts, schedule disabled
    return array(
        'home_id'           => $home_id,
        'update_mode'       => 'manual',
        'restart_behavior'  => 'none',
        'schedule_interval' => 'disabled',
    );
}

/**
 * Upsert the workshop behavior settings for a server home.
 *
 * @param  OGPDatabase $db
 * @param  int         $home_id
 * @param  array       $data  keys: update_mode, restart_behavior, schedule_interval
 * @return bool
 */
function sw_save_server_settings($db, $home_id, array $data)
{
    $home_id = (int)$home_id;

    $valid_update_modes      = array('manual', 'on_restart', 'before_start');
    $valid_restart_behaviors = array('none', 'if_stopped');
    $valid_intervals         = array('disabled', 'daily', 'weekly');

    $update_mode      = in_array($data['update_mode']      ?? '', $valid_update_modes,      true) ? $data['update_mode']      : 'manual';
    $restart_behavior = in_array($data['restart_behavior'] ?? '', $valid_restart_behaviors, true) ? $data['restart_behavior'] : 'none';
    $schedule_interval = in_array($data['schedule_interval'] ?? '', $valid_intervals, true) ? $data['schedule_interval'] : 'disabled';

    $safe_um  = $db->realEscapeSingle($update_mode);
    $safe_rb  = $db->realEscapeSingle($restart_behavior);
    $safe_si  = $db->realEscapeSingle($schedule_interval);

    return (bool)$db->query(
        "INSERT INTO " . sw_table('steam_workshop_server_settings') . "
           (`home_id`, `update_mode`, `restart_behavior`, `hot_load`,
             `warning_minutes`, `schedule_interval`, `created_at`, `updated_at`)
         VALUES ($home_id, '$safe_um', '$safe_rb', 'disabled',
                 0, '$safe_si', NOW(), NOW())
         ON DUPLICATE KEY UPDATE
            `update_mode`       = '$safe_um',
            `restart_behavior`  = '$safe_rb',
            `hot_load`          = 'disabled',
            `warning_minutes`   = 0,
            `schedule_interval` = '$safe_si',
            `updated_at`        = NOW()"
    );
}

// ── Output helpers ────────────────────────────────────────────────────────

/**
 * Render a short inline success banner.
 *
 * @param string $msg
 * @return void
 */
function sw_success($msg)
{
    echo '<div style="background:#d4edda;border:1px solid #c3e6cb;color:#155724;padding:8px 12px;margin:8px 0;border-radius:4px;">'
       . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
}

/**
 * Render a short inline error banner.
 *
 * @param string $msg
 * @return void
 */
function sw_error($msg)
{
    echo '<div style="background:#f8d7da;border:1px solid #f5c6cb;color:#721c24;padding:8px 12px;margin:8px 0;border-radius:4px;">'
       . htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') . '</div>';
}

/**
 * Escape a value for HTML output.
 *
 * @param  mixed $v
 * @return string
 */
function sw_h($v)
{
    return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8');
}

function sw_detect_profile_defaults_from_xml($configName)
{
    $configName = trim((string)$configName);
    if ($configName === '') {
        return array();
    }

    $matched = null;
    foreach (sw_get_all_game_configs() as $xml) {
        if ((string)$xml->game_key === $configName) {
            $matched = $xml;
            break;
        }
    }
    if (!$matched) {
        return array();
    }

    $steamAppId = '';
    if (isset($matched->mods->mod)) {
        foreach ($matched->mods->mod as $mod) {
            $candidate = trim((string)$mod->installer_name);
            if ($candidate !== '' && preg_match('/^\d+$/', $candidate)) {
                $steamAppId = $candidate;
                break;
            }
        }
    }

    $xmlBlob = $matched->asXML();
    $workshopAppId = '';
    if ($xmlBlob !== false && preg_match('/steamapps\/workshop\/content\/(\d+)/i', $xmlBlob, $m)) {
        $workshopAppId = $m[1];
    }
    if ($workshopAppId === '') {
        $workshopAppId = $steamAppId;
    }

    return array(
        'steam_app_id' => $steamAppId,
        'workshop_app_id' => $workshopAppId,
        'steamcmd_path' => '/home/gameserver/steamcmd/steamcmd.sh',
        'server_root_template' => '{SERVER_ROOT}',
        'workshop_download_dir_template' => '{SERVER_ROOT}/steamapps/workshop/content/{WORKSHOP_APP_ID}',
        'install_path_template' => '{SERVER_ROOT}/{MOD_FOLDER}',
    );
}

function sw_apply_detected_profile_defaults($db, array $profile, array $detected, $overwriteExisting = false)
{
    $columns = array(
        'steam_app_id',
        'workshop_app_id',
        'steamcmd_path',
        'workshop_download_dir_template',
        'server_root_template',
        'install_path_template',
    );
    $setParts = array();
    $updated = 0;

    foreach ($columns as $column) {
        if (!array_key_exists($column, $detected) || $detected[$column] === '') {
            continue;
        }
        $current = trim((string)($profile[$column] ?? ''));
        if (!$overwriteExisting && $current !== '') {
            continue;
        }
        if ($current === $detected[$column]) {
            continue;
        }
        $setParts[] = "`$column` = '" . $db->realEscapeSingle($detected[$column]) . "'";
        $updated++;
    }

    if (empty($setParts)) {
        return 0;
    }

    $setParts[] = "`updated_at` = NOW()";
    $db->query(
        "UPDATE " . sw_table('steam_workshop_game_profiles') . "
            SET " . implode(', ', $setParts) . "
          WHERE `id` = " . (int)$profile['id'] . " LIMIT 1"
    );
    return $updated;
}
