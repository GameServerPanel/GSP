<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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
$module_title = "billing";
$module_version = "3.1";
$db_version = 3;
$module_required = FALSE;
// Module description
$module_description = "Billing storefront / provisioning integration. Public ordering runs as a standalone site; panel pages provide provisioning and admin order management.";

// Register module menus so panel can show links (user and admin views)
$module_menus = array(
    array('subpage' => 'my_orders', 'name' => 'My Orders', 'group' => 'user'),
    array('subpage' => 'provision_servers', 'name' => 'Provision Servers', 'group' => 'user'),
    array('subpage' => 'admin_orders', 'name' => 'Manage Orders', 'group' => 'admin')
);

$install_queries = array();

// Version 1: Current schema - clean install with all tables and required columns
$install_queries[0] = array(
    // Billing Services - Available game server packages
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_services` (
        `service_id` INT(11) NOT NULL AUTO_INCREMENT,
        `home_cfg_id` INT(11) NOT NULL,
        `mod_cfg_id` INT(11) NOT NULL,
        `service_name` VARCHAR(255) NOT NULL,
        `remote_server_id` VARCHAR(255) NOT NULL,
        `out_of_stock` VARCHAR(255) NOT NULL DEFAULT '',
        `slot_max_qty` INT(11) NOT NULL,
        `slot_min_qty` INT(11) NOT NULL,
        `price_daily` FLOAT(15,4) NOT NULL DEFAULT 0,
        `price_monthly` FLOAT(15,4) NOT NULL DEFAULT 0,
        `price_year` FLOAT(15,4) NOT NULL DEFAULT 0,
        `description` VARCHAR(1000) NOT NULL DEFAULT '',
        `img_url` VARCHAR(255) NOT NULL DEFAULT '',
        `ftp` VARCHAR(255) NOT NULL DEFAULT '',
        `install_method` VARCHAR(255) NOT NULL DEFAULT '',
        `manual_url` VARCHAR(255) NOT NULL DEFAULT '', 
        `access_rights` VARCHAR(255) NOT NULL DEFAULT '',
        `enabled` INT(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (`service_id`),
        KEY `enabled` (`enabled`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",
    
    // Billing Orders - Actual game server instances (ongoing services)
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_orders` (
        `order_id` INT(11) NOT NULL AUTO_INCREMENT,
        `user_id` INT(11) NOT NULL,
        `service_id` INT(11) NOT NULL,
        `home_name` VARCHAR(255) NOT NULL,
        `ip` VARCHAR(255) NOT NULL DEFAULT '',
        `qty` INT(11) NOT NULL DEFAULT 1,
        `invoice_duration` VARCHAR(16) NOT NULL DEFAULT 'month',
        `max_players` INT(11) NOT NULL DEFAULT 0,
        `price` FLOAT(15,2) NOT NULL DEFAULT 0,
        `remote_control_password` VARCHAR(255) NULL,
        `ftp_password` VARCHAR(255) NULL,
        `home_id` VARCHAR(255) NOT NULL DEFAULT '0',
        `status` VARCHAR(16) NOT NULL DEFAULT 'in-cart',
        `order_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `end_date` DATETIME NULL,
        `payment_txid` VARCHAR(255) NULL,
        `paid_ts` DATETIME NULL,
        `coupon_id` INT(11) NOT NULL DEFAULT 0,
        PRIMARY KEY (`order_id`),
        KEY `user_id` (`user_id`),
        KEY `status` (`status`),
        KEY `home_id` (`home_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",
    
    // Billing Invoices - Created when user adds to cart, becomes order after payment
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_invoices` (
        `invoice_id` INT(11) NOT NULL AUTO_INCREMENT,
        `order_id` INT(11) NOT NULL DEFAULT 0,
        `user_id` INT(11) NOT NULL,
        `service_id` INT(11) NOT NULL,
        `home_name` VARCHAR(255) NOT NULL DEFAULT '',
        `ip` INT(11) NOT NULL DEFAULT 0,
        `max_players` INT(11) NOT NULL DEFAULT 0,
        `remote_control_password` VARCHAR(255) NULL,
        `ftp_password` VARCHAR(255) NULL,
        `customer_name` VARCHAR(255) NOT NULL DEFAULT '',
        `customer_email` VARCHAR(255) NOT NULL DEFAULT '',
        `amount` FLOAT(15,2) NOT NULL DEFAULT 0,
        `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
        `status` VARCHAR(16) NOT NULL DEFAULT 'due',
        `invoice_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `due_date` DATETIME NULL,
        `paid_date` DATETIME NULL,
        `payment_txid` VARCHAR(255) NULL,
        `payment_method` VARCHAR(50) NULL,
        `description` VARCHAR(500) NOT NULL DEFAULT '',
        `invoice_duration` VARCHAR(16) NOT NULL DEFAULT 'month',
        `qty` INT(11) NOT NULL DEFAULT 1,
        PRIMARY KEY (`invoice_id`),
        KEY `order_id` (`order_id`),
        KEY `user_id` (`user_id`),
        KEY `status` (`status`),
        KEY `due_date` (`due_date`),
        KEY `service_id` (`service_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"
);

// Version 2: New columns on billing_invoices, transaction log table, service-to-node mapping
// Each ALTER TABLE is a separate statement because ADD COLUMN IF NOT EXISTS requires MySQL 8.0+.
// The module manager only runs these once (on db_version bump 1->2), so they do not need IF NOT EXISTS.
$install_queries[1] = array(
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `home_id` INT(11) NOT NULL DEFAULT 0 AFTER `service_id`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `rate_type` ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'monthly' AFTER `invoice_duration`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `rate_per_player` DECIMAL(15,4) NOT NULL DEFAULT 0 AFTER `rate_type`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `players` INT(11) NOT NULL DEFAULT 0 AFTER `rate_per_player`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `period_start` DATETIME NULL AFTER `players`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `period_end` DATETIME NULL AFTER `period_start`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `period_end`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `total_due` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `subtotal`",
    "ALTER TABLE `".OGP_DB_PREFIX."billing_invoices` ADD COLUMN `payment_status` ENUM('unpaid','paid','cancelled','refunded') NOT NULL DEFAULT 'unpaid' AFTER `total_due`",

    // Payment transaction log — immutable audit trail
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_transactions` (
        `transaction_id`          INT(11) NOT NULL AUTO_INCREMENT,
        `invoice_id`              INT(11) NOT NULL DEFAULT 0,
        `user_id`                 INT(11) NOT NULL DEFAULT 0,
        `home_id`                 INT(11) NOT NULL DEFAULT 0,
        `payment_method`          VARCHAR(50) NOT NULL DEFAULT 'paypal',
        `transaction_external_id` VARCHAR(255) NOT NULL DEFAULT '',
        `amount`                  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
        `currency`                VARCHAR(3) NOT NULL DEFAULT 'USD',
        `status`                  ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
        `raw_response`            MEDIUMTEXT NULL,
        `created_at`              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`              DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`transaction_id`),
        KEY `invoice_id` (`invoice_id`),
        KEY `user_id` (`user_id`),
        KEY `home_id` (`home_id`),
        KEY `status` (`status`),
        KEY `payment_method` (`payment_method`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // Service-to-remote-server mapping (admin can enable/disable per service)
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_service_remote_servers` (
        `id`               INT(11) NOT NULL AUTO_INCREMENT,
        `service_id`       INT(11) NOT NULL,
        `remote_server_id` INT(11) NOT NULL,
        `enabled`          TINYINT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (`id`),
        UNIQUE KEY `svc_rs` (`service_id`, `remote_server_id`),
        KEY `service_id` (`service_id`),
        KEY `remote_server_id` (`remote_server_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
);

// Version 3: Add override_price to service-to-server mapping table
$install_queries[2] = array(
    "ALTER TABLE `".OGP_DB_PREFIX."billing_service_remote_servers` ADD COLUMN `override_price` DECIMAL(10,2) NULL AFTER `enabled`"
);

?>
