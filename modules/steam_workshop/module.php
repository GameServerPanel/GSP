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

// ── Module metadata ──────────────────────────────────────────────────────
$module_title   = "Steam Workshop";
$module_version = "3.1";
$db_version     = 4;
$module_required = FALSE;
$module_menus   = array(
    array('subpage' => 'admin', 'name' => 'Steam Workshop', 'group' => 'admin'),
);

// ── SQL helpers ──────────────────────────────────────────────────────────
// All OGP_DB_PREFIX tokens are replaced at runtime by $db->query() /
// $db->resultQuery() before the SQL reaches MySQL.  Do not replace them
// here with literal strings.

$_sw_drop_old = array(
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXworkshop_game_profiles`",
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXworkshop_cache`",
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXserver_workshop_mods`",
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXserver_workshop_settings`",
);

$_sw_create_new = array(
    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXsteam_workshop_game_profiles` (
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
      `default_update_mode`             ENUM('manual','on_restart','before_start','scheduled') NOT NULL DEFAULT 'manual',
      `default_restart_behavior`        ENUM('none','if_empty','immediate','next_restart') NOT NULL DEFAULT 'none',
      `default_hot_load`                ENUM('disabled','attempt') NOT NULL DEFAULT 'disabled',
      `created_at`                      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`                      DATETIME      NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_config_name` (`config_name`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXsteam_workshop_server_mods` (
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

    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXsteam_workshop_server_settings` (
      `home_id`            INT          NOT NULL,
      `update_mode`        ENUM('manual','on_restart','before_start','scheduled')
                                        NOT NULL DEFAULT 'manual',
      `restart_behavior`   ENUM('none','if_empty','immediate','next_restart')
                                        NOT NULL DEFAULT 'none',
      `hot_load`           ENUM('disabled','attempt')
                                        NOT NULL DEFAULT 'disabled',
      `warning_minutes`    INT          NOT NULL DEFAULT 10,
      `schedule_interval`  VARCHAR(32)  NOT NULL DEFAULT 'daily',
      `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`         DATETIME     NULL,
      PRIMARY KEY (`home_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",
);

// ── Install queries ──────────────────────────────────────────────────────
//
// $install_queries[0]  – runs on fresh install (module manager iterates all keys).
//                        Drops any legacy tables and creates the new schema.
// $install_queries[3]  – runs when upgrading from db_version 2 → 3.
//                        Same content; idempotent because of IF [NOT] EXISTS.
// $install_queries[4]  – runs when upgrading from db_version 3 → 4.
//                        Adds steam_workshop_server_settings table and default
//                        behavior columns on steam_workshop_game_profiles.
//
// Note: the module manager loops $install_queries[$i+1] for each step from
// current db_version up to target.  Keys 1 and 2 are intentionally absent;
// the manager safely skips undefined keys (PHP returns NULL → empty array).

$install_queries = array();

$install_queries[0] = array_merge($_sw_drop_old, $_sw_create_new);
$install_queries[3] = array_merge($_sw_drop_old, $_sw_create_new);

unset($_sw_drop_old, $_sw_create_new);

// ── db_version 4: per-server behavior settings + per-profile defaults ────
//
// New table: steam_workshop_server_settings
//   Stores update/restart/hot-load preferences per server home.
//   Defaults are safe (manual-only, no auto-restart, hot-load disabled).
//
// Altered table: steam_workshop_game_profiles
//   Adds default_update_mode, default_restart_behavior, default_hot_load so
//   admins can configure defaults per game profile.
//
// All callables check INFORMATION_SCHEMA before ALTER so this is re-runnable.

$install_queries[4] = array(
    // Create per-server settings table
    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXsteam_workshop_server_settings` (
      `home_id`            INT          NOT NULL,
      `update_mode`        ENUM('manual','on_restart','before_start','scheduled')
                                        NOT NULL DEFAULT 'manual',
      `restart_behavior`   ENUM('none','if_empty','immediate','next_restart')
                                        NOT NULL DEFAULT 'none',
      `hot_load`           ENUM('disabled','attempt')
                                        NOT NULL DEFAULT 'disabled',
      `warning_minutes`    INT          NOT NULL DEFAULT 10,
      `schedule_interval`  VARCHAR(32)  NOT NULL DEFAULT 'daily',
      `created_at`         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`         DATETIME     NULL,
      PRIMARY KEY (`home_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    // Add default_update_mode to game_profiles if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXsteam_workshop_game_profiles' AND COLUMN_NAME = 'default_update_mode'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXsteam_workshop_game_profiles` ADD `default_update_mode` ENUM('manual','on_restart','before_start','scheduled') NOT NULL DEFAULT 'manual' AFTER `notes`");
    },
    // Add default_restart_behavior to game_profiles if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXsteam_workshop_game_profiles' AND COLUMN_NAME = 'default_restart_behavior'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXsteam_workshop_game_profiles` ADD `default_restart_behavior` ENUM('none','if_empty','immediate','next_restart') NOT NULL DEFAULT 'none' AFTER `default_update_mode`");
    },
    // Add default_hot_load to game_profiles if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXsteam_workshop_game_profiles' AND COLUMN_NAME = 'default_hot_load'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXsteam_workshop_game_profiles` ADD `default_hot_load` ENUM('disabled','attempt') NOT NULL DEFAULT 'disabled' AFTER `default_restart_behavior`");
    },
);

// ── Uninstall queries ─────────────────────────────────────────────────────
$uninstall_queries = array(
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXsteam_workshop_server_settings`",
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXsteam_workshop_server_mods`",
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXsteam_workshop_game_profiles`",
);
