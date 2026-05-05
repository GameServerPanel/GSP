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
        "SELECT * FROM `OGP_DB_PREFIXsteam_workshop_game_profiles`
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
        "SELECT * FROM `OGP_DB_PREFIXsteam_workshop_game_profiles`
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
        "SELECT * FROM `OGP_DB_PREFIXsteam_workshop_game_profiles`
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
           FROM `OGP_DB_PREFIXsteam_workshop_game_profiles` p
           JOIN `OGP_DB_PREFIXconfig_homes` c
             ON c.`game_key` = p.`config_name`
           JOIN `OGP_DB_PREFIXserver_homes` s
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
        "SELECT * FROM `OGP_DB_PREFIXsteam_workshop_server_mods`
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
        "SELECT * FROM `OGP_DB_PREFIXsteam_workshop_server_mods`
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
           FROM `OGP_DB_PREFIXserver_homes` s
           JOIN `OGP_DB_PREFIXconfig_homes` c  ON c.`home_cfg_id` = s.`home_cfg_id`
           JOIN `OGP_DB_PREFIXremote_servers` r ON r.`remote_server_id` = s.`remote_server_id`
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
        "SELECT 1 FROM `OGP_DB_PREFIXserver_homes`
          WHERE `home_id` = $home_id AND `user_id_main` = $user_id LIMIT 1"
    );
    if ($rows) {
        return true;
    }

    // Assigned via user_homes
    $rows = $db->resultQuery(
        "SELECT 1 FROM `OGP_DB_PREFIXuser_homes`
          WHERE `home_id` = $home_id AND `user_id` = $user_id LIMIT 1"
    );
    if ($rows) {
        return true;
    }

    // Assigned via group
    $rows = $db->resultQuery(
        "SELECT 1 FROM `OGP_DB_PREFIXuser_group_homes` ugh
           JOIN `OGP_DB_PREFIXuser_groups` ug ON ug.`group_id` = ugh.`group_id`
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

        $ok = $db->query(
            "INSERT IGNORE INTO `OGP_DB_PREFIXsteam_workshop_game_profiles`
               (`config_name`, `game_name`, `enabled`)
             VALUES ('$safe_config', '$safe_name', 0)"
        );
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
