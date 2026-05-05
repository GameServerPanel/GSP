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
$module_version = "3.0";
$db_version     = 3;
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
);

// ── Install queries ──────────────────────────────────────────────────────
//
// $install_queries[0]  – runs on fresh install (module manager iterates all keys).
//                        Drops any legacy tables and creates the new schema.
// $install_queries[3]  – runs when upgrading from db_version 2 → 3.
//                        Same content; idempotent because of IF [NOT] EXISTS.
//
// Note: the module manager loops $install_queries[$i+1] for each step from
// current db_version up to target.  Keys 1 and 2 are intentionally absent;
// the manager safely skips undefined keys (PHP returns NULL → empty array).

$install_queries = array();

$install_queries[0] = array_merge($_sw_drop_old, $_sw_create_new);
$install_queries[3] = array_merge($_sw_drop_old, $_sw_create_new);

unset($_sw_drop_old, $_sw_create_new);

// ── Uninstall queries ─────────────────────────────────────────────────────
$uninstall_queries = array(
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXsteam_workshop_server_mods`",
    "DROP TABLE IF EXISTS `OGP_DB_PREFIXsteam_workshop_game_profiles`",
);
