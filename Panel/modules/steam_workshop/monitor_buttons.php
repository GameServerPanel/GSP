<?php
/*
 * GSP – Steam Workshop: monitor page button
 * Copyright (C) 2025 WDS / GameServerPanel
 *
 * Adds a "Steam Workshop" button to the game/server monitor page when:
 *  - the game's Workshop profile is enabled in steam_workshop_game_profiles, AND
 *  - the current user owns the server or is an admin.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

$module_buttons = array();

if (!function_exists('sw_monitor_db_prefix')) {
    function sw_monitor_db_prefix()
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
}

if (!function_exists('sw_monitor_table')) {
    function sw_monitor_table($name)
    {
        return '`' . sw_monitor_db_prefix() . $name . '`';
    }
}

// Only show the button when a Workshop profile is enabled for this game config.
$_sw_profile = $db->resultQuery(
    "SELECT p.`id`
       FROM " . sw_monitor_table('steam_workshop_game_profiles') . " p
       JOIN " . sw_monitor_table('config_homes') . " c ON c.`game_key` = p.`config_name`
       JOIN " . sw_monitor_table('server_homes') . " s ON s.`home_cfg_id` = c.`home_cfg_id`
      WHERE s.home_id = " . (int)$server_home['home_id'] . "
         AND p.enabled = 1
       LIMIT 1"
);

if (!empty($_sw_profile)) {
    $module_buttons[] = "<a class='monitorbutton' href='home.php?m=steam_workshop&amp;p=user&amp;home_id=" . (int)$server_home['home_id'] . "'>
		<img src='" . htmlspecialchars(check_theme_image("images/steam_workshop.png"), ENT_QUOTES, 'UTF-8') . "' title='Steam Workshop'>
		<span>Steam Workshop</span>
	</a>";
}

unset($_sw_profile);
?>
