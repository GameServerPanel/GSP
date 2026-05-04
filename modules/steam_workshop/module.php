<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */
// Module general information
$module_title = "Steam Workshop";
$module_version = "2.3";
$db_version = 2;
$module_required = TRUE;
$module_menus = array();

// -----------------------------------------------------------------------
// $install_queries[0]  – executed for FRESH installs (all keys run).
//                        Contains the full v2 schema with every column.
// $install_queries[2]  – executed when upgrading an existing v1 install
//                        to v2 (ALTER TABLE + new settings table).
// $db_version = 2  (v1 = original release; v2 = this rewrite).
// -----------------------------------------------------------------------
$install_queries = array();

// Full schema for fresh installs (includes every column from all versions).
$install_queries[0] = array(
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."workshop_game_profiles` (
      `id`                    INT           NOT NULL AUTO_INCREMENT,
      `game_key`              VARCHAR(100)  NOT NULL,
      `game_name`             VARCHAR(255)  NOT NULL,
      `steam_app_id`          VARCHAR(32)   NOT NULL DEFAULT '',
      `workshop_app_id`       VARCHAR(32)   NOT NULL,
      `steam_login_required`  TINYINT(1)    NOT NULL DEFAULT 0,
      `steamcmd_login_mode`   ENUM('anonymous','account') NOT NULL DEFAULT 'anonymous',
      `steamcmd_path`         VARCHAR(512)  NOT NULL DEFAULT '',
      `supported_os`          SET('linux','windows') NOT NULL DEFAULT 'linux',
      `cache_path_template`   TEXT          NOT NULL,
      `install_path_template` TEXT          NOT NULL,
      `folder_naming_format`  ENUM('@%mod_name%','@%workshop_id%','custom') NOT NULL DEFAULT '@%workshop_id%',
      `folder_name_template`  VARCHAR(255)  NOT NULL DEFAULT '@%workshop_id%',
      `mod_launch_param`      VARCHAR(512)  NOT NULL DEFAULT '',
      `mod_separator`         ENUM('semicolon','comma','space') NOT NULL DEFAULT 'semicolon',
      `copy_method`           ENUM('copy','rsync','symlink') NOT NULL DEFAULT 'rsync',
      `copy_keys`             TINYINT(1)    NOT NULL DEFAULT 0,
      `key_source_path`       TEXT          NULL,
      `key_dest_path`         TEXT          NULL,
      `pre_update_script`     TEXT          NULL,
      `install_script`        TEXT          NULL,
      `post_update_script`    TEXT          NULL,
      `config_file_template`  TEXT          NULL,
      `launch_param_template` TEXT          NULL,
      `requires_restart`      TINYINT(1)    NOT NULL DEFAULT 1,
      `validation_notes`      TEXT          NULL,
      `enabled`               TINYINT(1)    NOT NULL DEFAULT 1,
      `created_at`            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`            DATETIME      NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_game_key` (`game_key`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."workshop_cache` (
      `id`               INT          NOT NULL AUTO_INCREMENT,
      `agent_id`         INT          NOT NULL,
      `os_type`          ENUM('linux','windows') NOT NULL DEFAULT 'linux',
      `workshop_app_id`  VARCHAR(32)  NOT NULL,
      `workshop_id`      VARCHAR(64)  NOT NULL,
      `title`            VARCHAR(255) NULL,
      `cache_path`       TEXT         NOT NULL,
      `status`           ENUM('missing','cached','failed') NOT NULL DEFAULT 'missing',
      `last_checked`     DATETIME     NULL,
      `last_updated`     DATETIME     NULL,
      `last_error`       TEXT         NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_agent_workshop` (`agent_id`, `workshop_app_id`, `workshop_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."server_workshop_mods` (
      `id`               INT          NOT NULL AUTO_INCREMENT,
      `home_id`          INT          NOT NULL,
      `agent_id`         INT          NOT NULL,
      `profile_id`       INT          NOT NULL,
      `workshop_app_id`  VARCHAR(32)  NOT NULL,
      `workshop_id`      VARCHAR(64)  NOT NULL,
      `title`            VARCHAR(255) NULL,
      `custom_folder`    VARCHAR(255) NOT NULL DEFAULT '',
      `enabled`          TINYINT(1)   NOT NULL DEFAULT 1,
      `install_path`     TEXT         NOT NULL,
      `load_order`       INT          NOT NULL DEFAULT 0,
      `installed_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at`       DATETIME     NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `uniq_home_workshop` (`home_id`, `workshop_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."server_workshop_settings` (
      `home_id`              INT          NOT NULL,
      `workshop_enabled`     TINYINT(1)   NOT NULL DEFAULT 0,
      `profile_id`           INT          NULL,
      `update_mode`          ENUM('manual','scheduled','on_restart') NOT NULL DEFAULT 'manual',
      `restart_behavior`     ENUM('none','queue','stop_update_start') NOT NULL DEFAULT 'none',
      `update_queued`        TINYINT(1)   NOT NULL DEFAULT 0,
      `last_update_status`   VARCHAR(20)  NOT NULL DEFAULT '',
      `last_update_error`    TEXT         NULL,
      `last_update_time`     DATETIME     NULL,
      `last_success_time`    DATETIME     NULL,
      `updated_at`           DATETIME     NULL,
      PRIMARY KEY (`home_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);

// Migration: upgrade existing v1 installs to v2 schema.
$install_queries[2] = array(
    // New columns on workshop_game_profiles
    "ALTER TABLE `".OGP_DB_PREFIX."workshop_game_profiles`
        ADD COLUMN IF NOT EXISTS `steam_app_id`         VARCHAR(32)  NOT NULL DEFAULT '' AFTER `game_name`,
        ADD COLUMN IF NOT EXISTS `steam_login_required` TINYINT(1)   NOT NULL DEFAULT 0  AFTER `workshop_app_id`,
        ADD COLUMN IF NOT EXISTS `steamcmd_login_mode`  ENUM('anonymous','account') NOT NULL DEFAULT 'anonymous' AFTER `steam_login_required`,
        ADD COLUMN IF NOT EXISTS `steamcmd_path`        VARCHAR(512) NOT NULL DEFAULT '' AFTER `steamcmd_login_mode`,
        ADD COLUMN IF NOT EXISTS `folder_naming_format` ENUM('@%mod_name%','@%workshop_id%','custom') NOT NULL DEFAULT '@%workshop_id%' AFTER `install_path_template`,
        ADD COLUMN IF NOT EXISTS `mod_launch_param`     VARCHAR(512) NOT NULL DEFAULT '' AFTER `folder_name_template`,
        ADD COLUMN IF NOT EXISTS `mod_separator`        ENUM('semicolon','comma','space') NOT NULL DEFAULT 'semicolon' AFTER `mod_launch_param`,
        ADD COLUMN IF NOT EXISTS `copy_keys`            TINYINT(1)   NOT NULL DEFAULT 0  AFTER `copy_method`,
        ADD COLUMN IF NOT EXISTS `key_source_path`      TEXT         NULL AFTER `copy_keys`,
        ADD COLUMN IF NOT EXISTS `key_dest_path`        TEXT         NULL AFTER `key_source_path`,
        ADD COLUMN IF NOT EXISTS `pre_update_script`    TEXT         NULL AFTER `key_dest_path`,
        ADD COLUMN IF NOT EXISTS `post_update_script`   TEXT         NULL AFTER `install_script`,
        ADD COLUMN IF NOT EXISTS `validation_notes`     TEXT         NULL AFTER `requires_restart`",

    // Rename copy_method enum values to match new options (copy/rsync/symlink)
    // (existing 'rsync' stays valid; 'robocopy'/'custom_script' are legacy)

    // New column on server_workshop_mods
    "ALTER TABLE `".OGP_DB_PREFIX."server_workshop_mods`
        ADD COLUMN IF NOT EXISTS `custom_folder` VARCHAR(255) NOT NULL DEFAULT '' AFTER `title`",

    // New server-level settings table
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."server_workshop_settings` (
      `home_id`              INT          NOT NULL,
      `workshop_enabled`     TINYINT(1)   NOT NULL DEFAULT 0,
      `profile_id`           INT          NULL,
      `update_mode`          ENUM('manual','scheduled','on_restart') NOT NULL DEFAULT 'manual',
      `restart_behavior`     ENUM('none','queue','stop_update_start') NOT NULL DEFAULT 'none',
      `update_queued`        TINYINT(1)   NOT NULL DEFAULT 0,
      `last_update_status`   VARCHAR(20)  NOT NULL DEFAULT '',
      `last_update_error`    TEXT         NULL,
      `last_update_time`     DATETIME     NULL,
      `last_success_time`    DATETIME     NULL,
      `updated_at`           DATETIME     NULL,
      PRIMARY KEY (`home_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
);
