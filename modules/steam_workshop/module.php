<?php
/*
 * GSP – Steam Workshop module
 * Copyright (C) 2025 WDS / GameServerPanel
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 */

$module_title   = "Steam Workshop";
$module_version = "3.2";
$db_version     = 5;
$module_required = FALSE;
$module_menus   = array(
    array('subpage' => 'admin', 'name' => 'Steam Workshop', 'group' => 'admin'),
);

if (!function_exists('sw_module_db_prefix')) {
    function sw_module_db_prefix()
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

if (!function_exists('sw_module_table')) {
    function sw_module_table($table)
    {
        return '`' . sw_module_db_prefix() . $table . '`';
    }
}

if (!function_exists('sw_module_table_name')) {
    function sw_module_table_name($table)
    {
        return sw_module_db_prefix() . $table;
    }
}

$install_queries = array();

$legacyDrops = array(
    "DROP TABLE IF EXISTS " . sw_module_table('workshop_game_profiles'),
    "DROP TABLE IF EXISTS " . sw_module_table('workshop_cache'),
    "DROP TABLE IF EXISTS " . sw_module_table('server_workshop_mods'),
    "DROP TABLE IF EXISTS " . sw_module_table('server_workshop_settings'),
);

$schemaCreate = array(
    "CREATE TABLE IF NOT EXISTS " . sw_module_table('steam_workshop_game_profiles') . " (
      `id`                              INT           NOT NULL AUTO_INCREMENT,
      `config_name`                     VARCHAR(100)  NOT NULL,
      `game_name`                       VARCHAR(255)  NOT NULL DEFAULT '',
      `enabled`                         TINYINT(1)    NOT NULL DEFAULT 0,
      `steam_app_id`                    VARCHAR(32)   NOT NULL DEFAULT '',
      `workshop_app_id`                 VARCHAR(32)   NOT NULL DEFAULT '',
      `steam_login_required`            TINYINT(1)    NOT NULL DEFAULT 0,
      `steamcmd_login_mode`             ENUM('anonymous','account') NOT NULL DEFAULT 'anonymous',
      `steamcmd_path`                   VARCHAR(512)  NOT NULL DEFAULT '/home/gameserver/steamcmd/steamcmd.sh',
      `workshop_download_dir_template`  TEXT          NULL,
      `server_root_template`            TEXT          NULL,
      `install_path_template`           TEXT          NULL,
      `folder_naming_format`            VARCHAR(64)   NOT NULL DEFAULT '@{MOD_NAME}',
      `mod_launch_param_template`       VARCHAR(255)  NOT NULL DEFAULT '-mod=',
      `servermod_launch_param_template` VARCHAR(255)  NOT NULL DEFAULT '-serverMod=',
      `install_script_template`         TEXT          NULL,
      `update_script_template`          TEXT          NULL,
      `copy_bikeys_enabled`             TINYINT(1)    NOT NULL DEFAULT 1,
      `notes`                           TEXT          NULL,
      `default_update_mode`             ENUM('manual','on_restart','before_start') NOT NULL DEFAULT 'manual',
      `default_restart_behavior`        ENUM('none','if_stopped') NOT NULL DEFAULT 'none',
      `default_hot_load`                ENUM('disabled','attempt') NOT NULL DEFAULT 'disabled',
      `created_at`                      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`                      DATETIME      NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_config_name` (`config_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS " . sw_module_table('steam_workshop_server_mods') . " (
      `id`               INT          NOT NULL AUTO_INCREMENT,
      `home_id`          INT          NOT NULL,
      `profile_id`       INT          NOT NULL,
      `workshop_id`      VARCHAR(64)  NOT NULL,
      `mod_name`         VARCHAR(255) NOT NULL DEFAULT '',
      `folder_name`      VARCHAR(255) NOT NULL DEFAULT '',
      `mod_type`         ENUM('client','server') NOT NULL DEFAULT 'client',
      `sort_order`       INT          NOT NULL DEFAULT 0,
      `enabled`          TINYINT(1)   NOT NULL DEFAULT 1,
      `install_status`   VARCHAR(32)  NOT NULL DEFAULT '',
      `last_installed_at` DATETIME    NULL,
      `last_updated_at`  DATETIME     NULL,
      `last_error`       TEXT         NULL,
      `created_at`       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`       DATETIME     NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_home_workshop` (`home_id`, `workshop_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS " . sw_module_table('steam_workshop_server_settings') . " (
      `home_id`            INT          NOT NULL,
      `update_mode`        ENUM('manual','on_restart','before_start')
                                        NOT NULL DEFAULT 'manual',
      `restart_behavior`   ENUM('none','if_stopped')
                                        NOT NULL DEFAULT 'none',
      `hot_load`           ENUM('disabled','attempt')
                                        NOT NULL DEFAULT 'disabled',
      `warning_minutes`    INT          NOT NULL DEFAULT 0,
      `schedule_interval`  ENUM('disabled','daily','weekly') NOT NULL DEFAULT 'disabled',
      `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`         DATETIME     NULL,
      PRIMARY KEY (`home_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
);

$install_queries[0] = array_merge($legacyDrops, $schemaCreate);
$install_queries[3] = array_merge($legacyDrops, $schemaCreate);
$install_queries[4] = array();

$install_queries[5] = array(
    function ($db) {
        $table = sw_module_table_name('steam_workshop_server_settings');
        $exists = $db->resultQuery(
            "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $db->realEscapeSingle($table) . "'"
        );
        if (!$exists || (int)($exists[0]['cnt'] ?? 0) === 0) {
            return (bool)$db->query(
                "CREATE TABLE IF NOT EXISTS " . sw_module_table('steam_workshop_server_settings') . " (
                  `home_id`            INT          NOT NULL,
                  `update_mode`        ENUM('manual','on_restart','before_start') NOT NULL DEFAULT 'manual',
                  `restart_behavior`   ENUM('none','if_stopped') NOT NULL DEFAULT 'none',
                  `hot_load`           ENUM('disabled','attempt') NOT NULL DEFAULT 'disabled',
                  `warning_minutes`    INT          NOT NULL DEFAULT 0,
                  `schedule_interval`  ENUM('disabled','daily','weekly') NOT NULL DEFAULT 'disabled',
                  `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                  `updated_at`         DATETIME     NULL,
                  PRIMARY KEY (`home_id`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
            );
        }

        $ok = true;
        $ok = $ok && (bool)$db->query(
            "UPDATE " . sw_module_table('steam_workshop_server_settings') . "
                SET `update_mode` = CASE
                      WHEN `update_mode` = 'scheduled' THEN 'manual'
                      ELSE `update_mode`
                    END"
        );
        $ok = $ok && (bool)$db->query(
            "UPDATE " . sw_module_table('steam_workshop_server_settings') . "
                SET `restart_behavior` = CASE
                      WHEN `restart_behavior` IN ('if_empty','next_restart') THEN 'if_stopped'
                      WHEN `restart_behavior` = 'immediate' THEN 'none'
                      ELSE `restart_behavior`
                    END"
        );
        $ok = $ok && (bool)$db->query(
            "UPDATE " . sw_module_table('steam_workshop_server_settings') . "
                SET `schedule_interval` = CASE
                      WHEN `schedule_interval` = 'hourly' THEN 'daily'
                      WHEN `schedule_interval` IS NULL OR `schedule_interval` = '' THEN 'disabled'
                      ELSE `schedule_interval`
                    END"
        );
        // The simplified workflow intentionally hard-disables these legacy fields
        // for every row so old unsupported behaviors cannot be re-enabled.
        $ok = $ok && (bool)$db->query(
            "UPDATE " . sw_module_table('steam_workshop_server_settings') . "
                SET `hot_load` = 'disabled', `warning_minutes` = 0"
        );
        $ok = $ok && (bool)$db->query(
            "ALTER TABLE " . sw_module_table('steam_workshop_server_settings') . "
             MODIFY `update_mode` ENUM('manual','on_restart','before_start') NOT NULL DEFAULT 'manual'"
        );
        $ok = $ok && (bool)$db->query(
            "ALTER TABLE " . sw_module_table('steam_workshop_server_settings') . "
             MODIFY `restart_behavior` ENUM('none','if_stopped') NOT NULL DEFAULT 'none'"
        );
        $ok = $ok && (bool)$db->query(
            "ALTER TABLE " . sw_module_table('steam_workshop_server_settings') . "
             MODIFY `schedule_interval` ENUM('disabled','daily','weekly') NOT NULL DEFAULT 'disabled'"
        );

        return $ok;
    },
    function ($db) {
        $profileTable = sw_module_table_name('steam_workshop_game_profiles');
        $exists = $db->resultQuery(
            "SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES
             WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '" . $db->realEscapeSingle($profileTable) . "'"
        );
        if (!$exists || (int)($exists[0]['cnt'] ?? 0) === 0) {
            return true;
        }

        $ok = true;
        $ok = $ok && (bool)$db->query(
            "UPDATE " . sw_module_table('steam_workshop_game_profiles') . "
                SET `default_update_mode` = CASE
                      WHEN `default_update_mode` = 'scheduled' THEN 'manual'
                      ELSE `default_update_mode`
                    END"
        );
        $ok = $ok && (bool)$db->query(
            "UPDATE " . sw_module_table('steam_workshop_game_profiles') . "
                SET `default_restart_behavior` = CASE
                      WHEN `default_restart_behavior` IN ('if_empty','next_restart') THEN 'if_stopped'
                      WHEN `default_restart_behavior` = 'immediate' THEN 'none'
                      ELSE `default_restart_behavior`
                    END"
        );
        $ok = $ok && (bool)$db->query(
            "ALTER TABLE " . sw_module_table('steam_workshop_game_profiles') . "
             MODIFY `default_update_mode` ENUM('manual','on_restart','before_start') NOT NULL DEFAULT 'manual'"
        );
        $ok = $ok && (bool)$db->query(
            "ALTER TABLE " . sw_module_table('steam_workshop_game_profiles') . "
             MODIFY `default_restart_behavior` ENUM('none','if_stopped') NOT NULL DEFAULT 'none'"
        );

        return $ok;
    },
);

$uninstall_queries = array(
    "DROP TABLE IF EXISTS " . sw_module_table('steam_workshop_server_settings'),
    "DROP TABLE IF EXISTS " . sw_module_table('steam_workshop_server_mods'),
    "DROP TABLE IF EXISTS " . sw_module_table('steam_workshop_game_profiles'),
);
