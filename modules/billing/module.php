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
$module_version = "3.5";
$db_version = 7;
$module_required = FALSE;
// Module description
$module_description = "Billing storefront / provisioning integration. Public ordering runs as a standalone site; panel pages provide provisioning and admin order management.";

// No panel menu entries – billing runs as a standalone website, not panel pages.
// Install/uninstall/update DB logic below is still active.
$module_menus = array();

$install_queries = array();

// -----------------------------------------------------------------------
// db_version 1 — Baseline schema for fresh installs.
// All CREATE TABLE statements use IF NOT EXISTS so they are safe to re-run.
// NOTE: The panel updater runs $install_queries[$i+1] when upgrading a
//       module from db_version $i.  A module installed fresh at db_version 0
//       therefore runs $install_queries[1], not $install_queries[0].
// -----------------------------------------------------------------------
$install_queries[1] = array(
    // Billing Services — available game server packages
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_services` (
        `service_id`       INT(11)          NOT NULL AUTO_INCREMENT,
        `home_cfg_id`      INT(11)          NOT NULL DEFAULT 0,
        `mod_cfg_id`       INT(11)          NOT NULL DEFAULT 0,
        `service_name`     VARCHAR(255)     NOT NULL,
        `description`      VARCHAR(1000)    NOT NULL DEFAULT '',
        `img_url`          VARCHAR(255)     NOT NULL DEFAULT '',
        `remote_server_id` VARCHAR(255)     NOT NULL DEFAULT '',
        `out_of_stock`     VARCHAR(255)     NOT NULL DEFAULT '',
        `slot_max_qty`     INT(11)          NOT NULL DEFAULT 0,
        `slot_min_qty`     INT(11)          NOT NULL DEFAULT 0,
        `price_daily`      FLOAT(15,4)      NOT NULL DEFAULT 0,
        `price_monthly`    FLOAT(15,4)      NOT NULL DEFAULT 0,
        `price_year`       FLOAT(15,4)      NOT NULL DEFAULT 0,
        `ftp`              VARCHAR(255)     NOT NULL DEFAULT '',
        `install_method`   VARCHAR(255)     NOT NULL DEFAULT 'steamcmd',
        `manual_url`       VARCHAR(255)     NOT NULL DEFAULT '',
        `access_rights`    VARCHAR(255)     NOT NULL DEFAULT '',
        `enabled`          INT(11)          NOT NULL DEFAULT 0,
        PRIMARY KEY (`service_id`),
        KEY `enabled`     (`enabled`),
        KEY `mod_cfg_id`  (`mod_cfg_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",

    // Billing Orders — active game server instances
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_orders` (
        `order_id`               INT(11)      NOT NULL AUTO_INCREMENT,
        `user_id`                INT(11)      NOT NULL,
        `service_id`             INT(11)      NOT NULL,
        `home_name`              VARCHAR(255) NOT NULL,
        `ip`                     VARCHAR(255) NOT NULL DEFAULT '',
        `qty`                    INT(11)      NOT NULL DEFAULT 1,
        `invoice_duration`       VARCHAR(16)  NOT NULL DEFAULT 'month',
        `max_players`            INT(11)      NOT NULL DEFAULT 0,
        `price`                  FLOAT(15,2)  NOT NULL DEFAULT 0,
        `discount_amount`        DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        `remote_control_password` VARCHAR(255) NULL,
        `ftp_password`           VARCHAR(255) NULL,
        `home_id`                VARCHAR(255) NOT NULL DEFAULT '0',
        `status`                 VARCHAR(16)  NOT NULL DEFAULT 'in-cart',
        `order_date`             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `end_date`               DATETIME     NULL,
        `payment_txid`           VARCHAR(255) NULL,
        `paid_ts`                DATETIME     NULL,
        `coupon_id`              INT(11)      NOT NULL DEFAULT 0,
        PRIMARY KEY (`order_id`),
        KEY `user_id` (`user_id`),
        KEY `status`  (`status`),
        KEY `home_id` (`home_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",

    // Billing Invoices — created on cart add, paid after payment capture.
    // home_id is 0 until the service is provisioned after payment.
    // billing_status tracks the renewal lifecycle (Active / Invoiced / Expired)
    // independently of payment_status which tracks the payment state.
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_invoices` (
        `invoice_id`             INT(11)          NOT NULL AUTO_INCREMENT,
        `order_id`               INT(11)          NOT NULL DEFAULT 0,
        `user_id`                INT(11)          NOT NULL,
        `service_id`             INT(11)          NOT NULL DEFAULT 0, -- 0 = ad-hoc or admin-created invoice not linked to a catalogue service
        `home_id`                INT(11)          NOT NULL DEFAULT 0,
        `home_name`              VARCHAR(255)     NOT NULL DEFAULT '',
        `ip`                     INT(11)          NOT NULL DEFAULT 0,
        `max_players`            INT(11)          NOT NULL DEFAULT 0,
        `remote_control_password` VARCHAR(255)    NULL,
        `ftp_password`           VARCHAR(255)     NULL,
        `customer_name`          VARCHAR(255)     NOT NULL DEFAULT '',
        `customer_email`         VARCHAR(255)     NOT NULL DEFAULT '',
        `amount`                 FLOAT(15,2)      NOT NULL DEFAULT 0,
        `discount_amount`        DECIMAL(10,2)    NOT NULL DEFAULT 0.00,
        `currency`               VARCHAR(3)       NOT NULL DEFAULT 'USD',
        `status`                 VARCHAR(16)      NOT NULL DEFAULT 'due',
        `billing_status`         VARCHAR(16)      NOT NULL DEFAULT 'due',
        `invoice_date`           DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `due_date`               DATETIME         NULL,
        `paid_date`              DATETIME         NULL,
        `payment_txid`           VARCHAR(255)     NULL,
        `payment_method`         VARCHAR(50)      NULL,
        `description`            VARCHAR(500)     NOT NULL DEFAULT '',
        `invoice_duration`       VARCHAR(16)      NOT NULL DEFAULT 'month',
        `rate_type`              ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'monthly',
        `rate_per_player`        DECIMAL(15,4)    NOT NULL DEFAULT 0,
        `players`                INT(11)          NOT NULL DEFAULT 0,
        `period_start`           DATETIME         NULL,
        `period_end`             DATETIME         NULL,
        `subtotal`               DECIMAL(15,2)    NOT NULL DEFAULT 0,
        `total_due`              DECIMAL(15,2)    NOT NULL DEFAULT 0,
        `payment_status`         ENUM('unpaid','paid','cancelled','refunded') NOT NULL DEFAULT 'unpaid',
        `qty`                    INT(11)          NOT NULL DEFAULT 1,
        `coupon_id`              INT(11)          NOT NULL DEFAULT 0,
        PRIMARY KEY (`invoice_id`),
        KEY `order_id`      (`order_id`),
        KEY `user_id`       (`user_id`),
        KEY `home_id`       (`home_id`),
        KEY `status`        (`status`),
        KEY `due_date`      (`due_date`),
        KEY `service_id`    (`service_id`),
        KEY `coupon_id`     (`coupon_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",

    // Billing Transactions — immutable payment audit trail
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_transactions` (
        `transaction_id`          INT(11)      NOT NULL AUTO_INCREMENT,
        `invoice_id`              INT(11)      NOT NULL DEFAULT 0,
        `user_id`                 INT(11)      NOT NULL DEFAULT 0,
        `home_id`                 INT(11)      NOT NULL DEFAULT 0,
        `payment_method`          VARCHAR(50)  NOT NULL DEFAULT 'paypal',
        `transaction_external_id` VARCHAR(255) NOT NULL DEFAULT '',
        `amount`                  DECIMAL(15,2) NOT NULL DEFAULT 0.00,
        `currency`                VARCHAR(3)   NOT NULL DEFAULT 'USD',
        `status`                  ENUM('pending','completed','failed','refunded') NOT NULL DEFAULT 'pending',
        `raw_response`            MEDIUMTEXT   NULL,
        `created_at`              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `updated_at`              DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`transaction_id`),
        KEY `invoice_id`     (`invoice_id`),
        KEY `user_id`        (`user_id`),
        KEY `home_id`        (`home_id`),
        KEY `status`         (`status`),
        KEY `payment_method` (`payment_method`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;",

    // Billing Coupons — discount codes
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_coupons` (
        `coupon_id`         INT(11)                              NOT NULL AUTO_INCREMENT,
        `code`              VARCHAR(50)                          NOT NULL,
        `name`              VARCHAR(255)                         NOT NULL DEFAULT '',
        `description`       TEXT                                 NULL,
        `discount_percent`  DECIMAL(5,2)                         NOT NULL DEFAULT 0.00,
        `usage_type`        ENUM('one_time','permanent')         NOT NULL DEFAULT 'one_time',
        `game_filter_type`  ENUM('all_games','specific_games')   NOT NULL DEFAULT 'all_games',
        `game_filter_list`  TEXT                                 NULL,
        `max_uses`          INT(11)                              NULL,
        `current_uses`      INT(11)                              NOT NULL DEFAULT 0,
        `expires`           DATETIME                             NULL,
        `created_date`      DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_by`        INT(11)                              NULL,
        `is_active`         TINYINT(1)                           NOT NULL DEFAULT 1,
        PRIMARY KEY (`coupon_id`),
        UNIQUE KEY `idx_code`            (`code`),
        KEY `idx_active_expires`         (`is_active`,`expires`),
        KEY `idx_created_by`             (`created_by`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",

    // Billing Config — global and per-game-key billing settings used by cron
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."billing_config` (
        `config_id`                 INT(11)                          NOT NULL AUTO_INCREMENT,
        `game_key`                  VARCHAR(100)                     NULL DEFAULT NULL,
        `enabled`                   TINYINT(1)                       NOT NULL DEFAULT 1,
        `grace_days`                INT(11)                          NOT NULL DEFAULT 0,
        `delete_after_expired_days` INT(11)                          NOT NULL DEFAULT 7,
        `rate_type`                 ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'monthly',
        `price_per_player`          DECIMAL(10,4)                    NOT NULL DEFAULT 0.0000,
        PRIMARY KEY (`config_id`),
        KEY `game_key` (`game_key`),
        KEY `enabled`  (`enabled`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",

    // Legacy mapping table is handled by a later idempotent migration.
    "SELECT 1"
);

// -----------------------------------------------------------------------
// db_version 2 — Safe idempotent column migrations for existing installs.
// Each callable queries INFORMATION_SCHEMA to confirm the column is absent
// before issuing ALTER TABLE, so this migration is safe to re-run.
//
// NOTE: The 'OGP_DB_PREFIXtable_name' placeholder inside SQL string
// literals is intentional.  Both $db->query() and $db->resultQuery()
// call str_replace("OGP_DB_PREFIX", $this->table_prefix, $query) at
// the PHP level before passing the SQL to MySQL, so the placeholder is
// resolved to the real prefix (e.g. 'ogp_billing_orders') even when it
// appears inside a single-quoted string in the SQL text.
// -----------------------------------------------------------------------
$install_queries[2] = array(
    // billing_orders: add discount_amount if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_orders' AND COLUMN_NAME = 'discount_amount'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_orders` ADD `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `price`");
    },
    // billing_invoices: add home_id if missing (needed by cron-shop.php join)
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'home_id'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `home_id` INT(11) NOT NULL DEFAULT 0 AFTER `service_id`");
    },
    // billing_invoices: add discount_amount if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'discount_amount'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount`");
    },
    // billing_invoices: add billing_status (lifecycle: Active/Invoiced/Expired) if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'billing_status'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `billing_status` VARCHAR(16) NOT NULL DEFAULT 'due' AFTER `status`");
    },
    // billing_invoices: add rate_type enum if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'rate_type'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `rate_type` ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'monthly' AFTER `invoice_duration`");
    },
    // billing_invoices: add rate_per_player if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'rate_per_player'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `rate_per_player` DECIMAL(15,4) NOT NULL DEFAULT 0 AFTER `rate_type`");
    },
    // billing_invoices: add players if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'players'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `players` INT(11) NOT NULL DEFAULT 0 AFTER `rate_per_player`");
    },
    // billing_invoices: add subtotal if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'subtotal'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `players`");
    },
    // billing_invoices: add total_due if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'total_due'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `total_due` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `subtotal`");
    },
    // billing_invoices: add payment_status enum if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'payment_status'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `payment_status` ENUM('unpaid','paid','cancelled','refunded') NOT NULL DEFAULT 'unpaid' AFTER `currency`");
    },
    // billing_invoices: add coupon_id if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'coupon_id'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `coupon_id` INT(11) NOT NULL DEFAULT 0 AFTER `qty`");
    },
    // Create billing_config table for cron-shop settings if missing
    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXbilling_config` (
        `config_id`                 INT(11)                          NOT NULL AUTO_INCREMENT,
        `game_key`                  VARCHAR(100)                     NULL DEFAULT NULL,
        `enabled`                   TINYINT(1)                       NOT NULL DEFAULT 1,
        `grace_days`                INT(11)                          NOT NULL DEFAULT 0,
        `delete_after_expired_days` INT(11)                          NOT NULL DEFAULT 7,
        `rate_type`                 ENUM('daily','monthly','yearly') NOT NULL DEFAULT 'monthly',
        `price_per_player`          DECIMAL(10,4)                    NOT NULL DEFAULT 0.0000,
        PRIMARY KEY (`config_id`),
        KEY `game_key` (`game_key`),
        KEY `enabled`  (`enabled`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;",
    // Create billing_coupons table if missing (older installs using panel.sql may not have it)
    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXbilling_coupons` (
        `coupon_id`         INT(11)                              NOT NULL AUTO_INCREMENT,
        `code`              VARCHAR(50)                          NOT NULL,
        `name`              VARCHAR(255)                         NOT NULL DEFAULT '',
        `description`       TEXT                                 NULL,
        `discount_percent`  DECIMAL(5,2)                         NOT NULL DEFAULT 0.00,
        `usage_type`        ENUM('one_time','permanent')         NOT NULL DEFAULT 'one_time',
        `game_filter_type`  ENUM('all_games','specific_games')   NOT NULL DEFAULT 'all_games',
        `game_filter_list`  TEXT                                 NULL,
        `max_uses`          INT(11)                              NULL,
        `current_uses`      INT(11)                              NOT NULL DEFAULT 0,
        `expires`           DATETIME                             NULL,
        `created_date`      DATETIME                             NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `created_by`        INT(11)                              NULL,
        `is_active`         TINYINT(1)                           NOT NULL DEFAULT 1,
        PRIMARY KEY (`coupon_id`),
        UNIQUE KEY `idx_code`       (`code`),
        KEY `idx_active_expires`    (`is_active`,`expires`),
        KEY `idx_created_by`        (`created_by`)
    ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;"
);

// -----------------------------------------------------------------------
// db_version 3 — Add billing_paypal_webhook_events table for idempotent
// webhook event processing.
// -----------------------------------------------------------------------
$install_queries[3] = array(
    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXbilling_paypal_webhook_events` (
        `id`                INT(11)       NOT NULL AUTO_INCREMENT,
        `paypal_event_id`   VARCHAR(100)  NOT NULL DEFAULT '',
        `event_type`        VARCHAR(100)  NOT NULL DEFAULT '',
        `resource_id`       VARCHAR(100)  NOT NULL DEFAULT '',
        `order_id`          VARCHAR(100)  NOT NULL DEFAULT '',
        `capture_id`        VARCHAR(100)  NOT NULL DEFAULT '',
        `billing_order_id`  INT(11)       NOT NULL DEFAULT 0,
        `processing_status` VARCHAR(50)   NOT NULL DEFAULT 'received',
        `raw_json`          MEDIUMTEXT    NULL,
        `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        `processed_at`      DATETIME      NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `uidx_paypal_event_id` (`paypal_event_id`),
        KEY `idx_event_type`          (`event_type`),
        KEY `idx_billing_order_id`    (`billing_order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
);

// -----------------------------------------------------------------------
// db_version 4 — Add billing_paypal_errors table for checkout error logging.
// -----------------------------------------------------------------------
$install_queries[4] = array(
    "CREATE TABLE IF NOT EXISTS `OGP_DB_PREFIXbilling_paypal_errors` (
        `id`                INT           NOT NULL AUTO_INCREMENT,
        `context`           VARCHAR(64)   NOT NULL DEFAULT '',
        `error_code`        VARCHAR(128)  NOT NULL DEFAULT '',
        `message`           TEXT          NULL,
        `paypal_debug_id`   VARCHAR(128)  NULL,
        `order_id`          VARCHAR(128)  NULL,
        `capture_id`        VARCHAR(128)  NULL,
        `billing_order_id`  INT           NULL,
        `user_id`           INT           NULL,
        `raw_json`          LONGTEXT      NULL,
        `created_at`        DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `idx_context`    (`context`),
        KEY `idx_created_at` (`created_at`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;"
);

// -----------------------------------------------------------------------
// db_version 5 — Preserve the unused legacy service/node mapping table by
// renaming it to a *_deprecated_backup table instead of dropping it.
// -----------------------------------------------------------------------
$install_queries[5] = array(
    function($db) {
        $legacy = 'OGP_DB_PREFIXbilling_service_remote_servers';
        $backup = 'OGP_DB_PREFIXbilling_service_remote_servers_deprecated_backup';
        $legacyCheck = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$legacy}'");
        if (!$legacyCheck || empty($legacyCheck[0]['cnt']) || (int)$legacyCheck[0]['cnt'] === 0) return true;

        $backupCheck = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = '{$backup}'");
        if ($backupCheck && !empty($backupCheck[0]['cnt']) && (int)$backupCheck[0]['cnt'] > 0) return true;

        return (bool)$db->query("RENAME TABLE `{$legacy}` TO `{$backup}`");
    }
);

// -----------------------------------------------------------------------
// db_version 6 — Add server_os column to remote_servers for OS-aware
// game/service selection in the billing storefront.
// Default 'linux' preserves existing behaviour for all current installs.
// -----------------------------------------------------------------------
$install_queries[6] = array(
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXremote_servers' AND COLUMN_NAME = 'server_os'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXremote_servers` ADD `server_os` ENUM('linux','windows','any') NOT NULL DEFAULT 'linux' AFTER `display_public_ip`");
    }
);

// -----------------------------------------------------------------------
// db_version 7 — Add period_start and period_end to billing_invoices.
// These columns define the service period covered by an invoice and are
// referenced in add_to_cart.php, capture_order.php, and cron-shop.php.
// They exist in the baseline CREATE TABLE (db_version 1) but were never
// added as idempotent ALTER migrations, so existing installs may be missing
// them.  Both columns are DATETIME NULL so they are safe to add without a
// default value.
// -----------------------------------------------------------------------
$install_queries[7] = array(
    // billing_invoices: add period_start if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'period_start'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `period_start` DATETIME NULL AFTER `players`");
    },
    // billing_invoices: add period_end if missing
    function($db) {
        $r = $db->resultQuery("SELECT COUNT(*) AS cnt FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'OGP_DB_PREFIXbilling_invoices' AND COLUMN_NAME = 'period_end'");
        if ($r && isset($r[0]['cnt']) && (int)$r[0]['cnt'] > 0) return true;
        return (bool)$db->query("ALTER TABLE `OGP_DB_PREFIXbilling_invoices` ADD `period_end` DATETIME NULL AFTER `period_start`");
    }
);

?>