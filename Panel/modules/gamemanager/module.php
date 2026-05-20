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
$module_title = "Game manager";
$module_version = "1.33";
$db_version = 2;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => 'game_monitor', 'name'=>'Game Monitor', 'group'=>'user' ) );
$module_access_rights = array('u' => 'allow_updates', 'p' => 'allow_parameter_usage', 'e' => 'allow_extra_params', 'c' => 'allow_custom_fields');

$install_queries[0] = array(
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."home_ip_ports` (
        `ip_id` int(11) NOT NULL,
        `port` int(11) NOT NULL,
        `home_id` int(11) NOT NULL,
        `force_mod_id` int(11) NOT NULL DEFAULT 0,
        PRIMARY KEY  (`ip_id`,`port`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."server_homes` (
        `home_id` int(50) NOT NULL auto_increment,
        `remote_server_id` int(11) NOT NULL,
        `user_id_main` int(11) NOT NULL,
        `home_path` varchar(500) NOT NULL,
        `home_cfg_id` int(50) NOT NULL,
        `home_name` varchar(500) NOT NULL,
        `control_password` VARCHAR(128) NULL,
        `ftp_password` VARCHAR(128) NULL,
        `ftp_login` varchar(32) NULL,
        `ftp_status` int(11) NOT NULL DEFAULT '0',
        `last_param` LONGTEXT NULL,
        `custom_fields` LONGTEXT NULL,
        `server_expiration_date` VARCHAR(21) NOT NULL DEFAULT 'X',
        `home_user_order` INT NOT NULL DEFAULT 99999,
        PRIMARY KEY  (`home_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."rcon_presets` (
        `preset_id` int(50) NOT NULL auto_increment,
        `name` varchar(20) NOT NULL,
        `command` varchar(100) NOT NULL,
        `home_cfg_id` int(50) NOT NULL,
        `mod_cfg_id` int(50) NOT NULL,
        PRIMARY KEY  (`preset_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."game_mods` (
        `mod_id` int(50) NOT NULL auto_increment,
        `home_id` int(255) NOT NULL,
        `mod_cfg_id` int(11) NOT NULL,
        `max_players` smallint(3) DEFAULT NULL,
        `extra_params` varchar(255) DEFAULT NULL,
        `cpu_affinity` varchar(2) DEFAULT NULL,
        `nice` smallint(3) DEFAULT '0',
        `precmd` TEXT,
        `postcmd` TEXT,
        PRIMARY KEY (mod_id),
        UNIQUE KEY home_id (home_id,mod_cfg_id)
    ) ENGINE=MyISAM;",

    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."status_cache` (
        `date_timestamp` char(16) NOT NULL,
        `ip_id` char(3) NOT NULL,
        `port` char(6) NOT NULL,
        `server_status_cache` longtext NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;"
);

// -----------------------------------------------------------------------
// db_version 2 — Add billing lifecycle columns to server_homes.
// Each callable is idempotent: it checks whether the column already exists
// and treats a "Duplicate column name" error as success (not a real failure).
// -----------------------------------------------------------------------
$install_queries[2] = array(
    // billing_status: current lifecycle state (Active / Invoiced / Expired)
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_status` VARCHAR(16) NOT NULL DEFAULT 'Active'")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // billing_enabled: whether this server participates in billing automation
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_enabled` TINYINT(1) NOT NULL DEFAULT 0")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // next_invoice_date: when cron-shop should generate the next renewal invoice
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `next_invoice_date` DATETIME NULL DEFAULT NULL")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // last_invoice_id: FK to billing_invoices.invoice_id (most recent renewal)
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `last_invoice_id` INT(11) NOT NULL DEFAULT 0")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // billing_expires_at: canonical billing expiration date (DATETIME)
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_expires_at` DATETIME NULL DEFAULT NULL")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // billing_price: price stored at provisioning time for renewals
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // billing_rate_type: 'daily' / 'monthly' / 'yearly'
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_rate_type` ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'monthly'")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // billing_players: slot count used to calculate per-player pricing
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_players` INT(11) NOT NULL DEFAULT 0")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
    // billing_invoice_sent_at: timestamp of last renewal invoice email
    function($db) {
        if (!$db->query("ALTER TABLE `OGP_DB_PREFIXserver_homes` ADD `billing_invoice_sent_at` DATETIME NULL DEFAULT NULL")) {
            return (stripos((string)$db->getError(), 'Duplicate column') !== false);
        }
        return true;
    },
);
?>