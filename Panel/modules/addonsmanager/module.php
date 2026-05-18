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
$module_version = "2.0";
$db_version     = 2;
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
?>
