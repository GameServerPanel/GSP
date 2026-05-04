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
$module_version = "2.1";
$db_version = 1;
$module_required = TRUE;
$module_menus = array(
	array(
		'subpage' => 'workshop_admin',
		'name'    => 'Steam Workshop',
		'group'   => 'admin'
	)
);

// Database schema migration: create the three Workshop tables when not present.
// Called by the panel module installer when db_version increments.
$module_db_create = <<<'SQL'
CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXworkshop_game_profiles` (
  `id`                    INT          NOT NULL AUTO_INCREMENT,
  `game_key`              VARCHAR(100) NOT NULL,
  `game_name`             VARCHAR(255) NOT NULL,
  `workshop_app_id`       VARCHAR(32)  NOT NULL,
  `supported_os`          SET('linux','windows') NOT NULL DEFAULT 'linux',
  `cache_path_template`   TEXT         NOT NULL,
  `install_path_template` TEXT         NOT NULL,
  `folder_name_template`  VARCHAR(255) NOT NULL DEFAULT '@{mod_id}',
  `copy_method`           ENUM('rsync','robocopy','custom_script') NOT NULL DEFAULT 'rsync',
  `install_script`        TEXT         NULL,
  `config_file_template`  TEXT         NULL,
  `launch_param_template` TEXT         NULL,
  `requires_restart`      TINYINT(1)   NOT NULL DEFAULT 1,
  `enabled`               TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`            DATETIME     NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_game_key` (`game_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXworkshop_cache` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXserver_workshop_mods` (
  `id`               INT          NOT NULL AUTO_INCREMENT,
  `home_id`          INT          NOT NULL,
  `agent_id`         INT          NOT NULL,
  `profile_id`       INT          NOT NULL,
  `workshop_app_id`  VARCHAR(32)  NOT NULL,
  `workshop_id`      VARCHAR(64)  NOT NULL,
  `title`            VARCHAR(255) NULL,
  `enabled`          TINYINT(1)   NOT NULL DEFAULT 1,
  `install_path`     TEXT         NOT NULL,
  `load_order`       INT          NOT NULL DEFAULT 0,
  `installed_at`     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME     NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_home_workshop` (`home_id`, `workshop_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;
