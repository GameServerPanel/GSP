<?php
/*
 *
 * GSP - Game Server Panel (a heavily customized fork of OGP maintained by WDS)
 *
 * Module: addonsmanager → Server Content Manager
 * ─────────────────────────────────────────────────────────────────────────────
 * The module folder and DB table names are intentionally unchanged for
 * backward compatibility.  Only UI labels have been updated to the new
 * "Server Content" terminology.
 *
 * db_version history:
 *   1 – initial schema (addons table, addon_type VARCHAR(7))
 *   2 – expand addon_type to VARCHAR(32) to support extended content types
 *       (workshop=8 chars, and any future type up to 32 chars)
 *   3 – add server_content_workshop table for per-server Workshop item selections
 *   4 – Phase 2: add install_method / content_version / requires_stop /
 *       backup_before_install / restart_after_install / is_cacheable /
 *       description columns to addons table; add server_content_manifest
 *       and server_content_install_history tables
 *   5 – add workshop_item_id / workshop_app_id / target_path_template /
 *       optional_folder_name / config_edit_rule / launch_param_additions
 *       columns to addons table
 *   6 – add admin template policy columns to addons table
 *       (allow_user_workshop_ids, max_workshop_ids, required_workshop_ids,
 *       blocked_workshop_ids); add content_id column to
 *       server_content_workshop so user installs link to their template
 *
 */

// Module general information
$module_title   = "Server Content Manager";
$module_version = "2.4";
$db_version     = 6;
$module_required = TRUE;
$module_menus   = array(
    array( 'subpage' => 'addons_manager', 'name' => 'Server Content Manager', 'group' => 'admin' )
);

// ── db_version 1 : initial install ───────────────────────────────────────────
$install_queries    = array();
$install_queries[0] = array(
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."addons` (
        `addon_id`     INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `name`         VARCHAR(80)  NOT NULL,
        `url`          VARCHAR(200) NOT NULL,
        `path`         VARCHAR(80)  NOT NULL,
        `addon_type`   VARCHAR(7)   NOT NULL,
        `home_cfg_id`  VARCHAR(7)   NOT NULL,
        `post_script`  longtext     NOT NULL,
        `group_id`     int(11)      NULL
    ) ENGINE=MyISAM;"
);

// ── db_version 2 : expand addon_type to VARCHAR(32) ──────────────────────────
// Required so extended content types such as 'workshop' (8 chars) can be stored.
// MODIFY is safe on existing installs; existing 'plugin'/'mappack'/'config'
// values are preserved without alteration.
$install_queries[1] = array(
    "ALTER TABLE `".OGP_DB_PREFIX."addons`
        MODIFY `addon_type` VARCHAR(32) NOT NULL;"
);

// ── db_version 3 : workshop item selections per server home ───────────────────
$install_queries[2] = array(
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."server_content_workshop` (
        `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        `home_id` INT NOT NULL,
        `home_cfg_id` INT NOT NULL,
        `remote_server_id` INT NULL,
        `workshop_app_id` VARCHAR(32) NULL,
        `workshop_item_id` VARCHAR(64) NOT NULL,
        `title` VARCHAR(255) NULL,
        `install_state` VARCHAR(32) NOT NULL DEFAULT 'selected',
        `last_installed_at` DATETIME NULL,
        `last_updated_at` DATETIME NULL,
        `last_error` TEXT NULL,
        `created_by` INT NULL,
        `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at` DATETIME NULL,
        UNIQUE KEY `uniq_home_workshop_item` (`home_id`, `workshop_item_id`),
        KEY `idx_home_id` (`home_id`),
        KEY `idx_home_cfg_id` (`home_cfg_id`),
        KEY `idx_install_state` (`install_state`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;"
);

// ── db_version 4 : Phase 2 – install_method, per-server manifest, install history ──
//
// Uses a PHP callable so each ALTER is applied only when the column does not
// already exist (safe for repeated runs, compatible with all MySQL versions).
//
$install_queries[3] = array(
    function ($db) {
        $prefix = OGP_DB_PREFIX;

        // ── Extend the addons table with Phase 2 columns ──────────────────────
        $new_columns = array(
            'install_method'        => "VARCHAR(32) NOT NULL DEFAULT 'download_zip' AFTER `group_id`",
            'content_version'       => "VARCHAR(64) NULL AFTER `install_method`",
            'requires_stop'         => "TINYINT(1) NOT NULL DEFAULT 1 AFTER `content_version`",
            'backup_before_install' => "TINYINT(1) NOT NULL DEFAULT 1 AFTER `requires_stop`",
            'restart_after_install' => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `backup_before_install`",
            'is_cacheable'          => "TINYINT(1) NOT NULL DEFAULT 0 AFTER `restart_after_install`",
            'description'           => "TEXT NULL AFTER `is_cacheable`",
        );

        foreach ($new_columns as $col => $definition) {
            $escaped_col   = $db->realEscapeSingle($col);
            $escaped_table = $db->realEscapeSingle($prefix . 'addons');
            $check = $db->resultQuery(
                "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME   = '{$escaped_table}'
                    AND COLUMN_NAME  = '{$escaped_col}'"
            );
            if (empty($check)) {
                if (!$db->query("ALTER TABLE `{$prefix}addons` ADD COLUMN `{$col}` {$definition}")) {
                    return false;
                }
            }
        }

        // ── Per-server installed-content manifest ─────────────────────────────
        if (!$db->query(
            "CREATE TABLE IF NOT EXISTS `{$prefix}server_content_manifest` (
                `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `home_id`         INT NOT NULL,
                `addon_id`        INT NOT NULL,
                `install_method`  VARCHAR(32)  NOT NULL DEFAULT 'download_zip',
                `content_version` VARCHAR(64)  NULL,
                `install_state`   VARCHAR(32)  NOT NULL DEFAULT 'installed',
                `checksum_sha256` VARCHAR(64)  NULL,
                `source_url`      VARCHAR(255) NULL,
                `installed_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `installed_by`    INT          NULL,
                `updated_at`      DATETIME     NULL,
                `notes`           TEXT         NULL,
                UNIQUE KEY `uniq_home_addon`  (`home_id`, `addon_id`),
                KEY `idx_home_id`             (`home_id`),
                KEY `idx_addon_id`            (`addon_id`),
                KEY `idx_install_state`       (`install_state`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        )) {
            return false;
        }

        // ── Install history (one row per install attempt) ─────────────────────
        if (!$db->query(
            "CREATE TABLE IF NOT EXISTS `{$prefix}server_content_install_history` (
                `id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `home_id`         INT          NOT NULL,
                `addon_id`        INT          NOT NULL,
                `installed_by`    INT          NULL,
                `started_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
                `completed_at`    DATETIME     NULL,
                `install_state`   VARCHAR(32)  NOT NULL DEFAULT 'started',
                `install_method`  VARCHAR(32)  NULL,
                `content_version` VARCHAR(64)  NULL,
                `source_url`      VARCHAR(255) NULL,
                `cache_mode_used` VARCHAR(32)  NULL,
                `result_code`     INT          NULL,
                `log_output`      MEDIUMTEXT   NULL,
                KEY `idx_home_id`    (`home_id`),
                KEY `idx_addon_id`   (`addon_id`),
                KEY `idx_started_at` (`started_at`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
        )) {
            return false;
        }

        return true;
    },
);

// ── db_version 5 : content-type specific metadata for Workshop/config/folder actions ──
$install_queries[4] = array(
    function ($db) {
        $prefix = OGP_DB_PREFIX;
        $new_columns = array(
            'workshop_item_id'       => "VARCHAR(64) NULL AFTER `description`",
            'workshop_app_id'        => "VARCHAR(32) NULL AFTER `workshop_item_id`",
            'target_path_template'   => "VARCHAR(255) NULL AFTER `workshop_app_id`",
            'optional_folder_name'   => "VARCHAR(255) NULL AFTER `target_path_template`",
            'config_edit_rule'       => "TEXT NULL AFTER `optional_folder_name`",
            'launch_param_additions' => "VARCHAR(255) NULL AFTER `config_edit_rule`",
        );
        foreach ($new_columns as $col => $definition) {
            $escaped_col   = $db->realEscapeSingle($col);
            $escaped_table = $db->realEscapeSingle($prefix . 'addons');
            $check = $db->resultQuery(
                "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME   = '{$escaped_table}'
                    AND COLUMN_NAME  = '{$escaped_col}'"
            );
            if (empty($check)) {
                if (!$db->query("ALTER TABLE `{$prefix}addons` ADD COLUMN `{$col}` {$definition}")) {
                    return false;
                }
            }
        }
        return true;
    },
);
// ── db_version 6 : admin template policy columns + content_id on workshop rows ──
//
// allow_user_workshop_ids – whether users may enter their own IDs (default 1)
// max_workshop_ids        – optional cap on how many IDs a user may install
// required_workshop_ids   – JSON list of IDs that must always be installed
// blocked_workshop_ids    – JSON list of IDs that must not be installed
// content_id on server_content_workshop – links a user install row back to
//   the admin content template so the correct workshop_app_id is used.
//
$install_queries[5] = array(
    function ($db) {
        $prefix = OGP_DB_PREFIX;

        // ── New policy columns on the addons (content template) table ─────────
        $addon_columns = array(
            'allow_user_workshop_ids' => "TINYINT(1) NOT NULL DEFAULT 1 AFTER `blocked_workshop_ids`",
            'max_workshop_ids'        => "INT NULL AFTER `allow_user_workshop_ids`",
            'required_workshop_ids'   => "TEXT NULL AFTER `max_workshop_ids`",
            'blocked_workshop_ids'    => "TEXT NULL AFTER `launch_param_additions`",
        );
        foreach ($addon_columns as $col => $definition) {
            $escaped_col   = $db->realEscapeSingle($col);
            $escaped_table = $db->realEscapeSingle($prefix . 'addons');
            $check = $db->resultQuery(
                "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME   = '{$escaped_table}'
                    AND COLUMN_NAME  = '{$escaped_col}'"
            );
            if (empty($check)) {
                if (!$db->query("ALTER TABLE `{$prefix}addons` ADD COLUMN `{$col}` {$definition}")) {
                    return false;
                }
            }
        }

        // ── content_id on server_content_workshop ─────────────────────────────
        $wk_table = $db->realEscapeSingle($prefix . 'server_content_workshop');
        $col_check = $db->resultQuery(
            "SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME   = '{$wk_table}'
                AND COLUMN_NAME  = 'content_id'"
        );
        if (empty($col_check)) {
            if (!$db->query(
                "ALTER TABLE `{$prefix}server_content_workshop`
                 ADD COLUMN `content_id` INT NULL AFTER `id`,
                 ADD KEY `idx_content_id` (`content_id`)"
            )) {
                return false;
            }
        }

        return true;
    },
);
?>
