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
 *
 */

// Module general information
$module_title   = "Server Content Manager";
$module_version = "2.1";
$db_version     = 3;
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
?>
