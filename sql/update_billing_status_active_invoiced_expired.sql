-- ============================================================
-- GSP Billing Status Simplification Migration
-- Simplifies server billing lifecycle to: Active | Invoiced | Expired
--
-- Run manually ONCE on an existing installation.
-- Safe to re-run: every ALTER uses IF NOT EXISTS / PREPARE guards.
-- Table prefix: gsp_   (matches modules/billing/includes/config.inc.php)
--
-- BACK UP YOUR DATABASE BEFORE RUNNING THIS SCRIPT.
-- ============================================================

SET @dbname = DATABASE();

-- ============================================================
-- SECTION 1: Fix gsp_server_homes.server_expiration_date
--            Convert from VARCHAR(21) with 'X' default to DATETIME NULL
-- ============================================================

-- Clear placeholder 'X' and empty-string values so the column
-- can be safely converted to DATETIME.
UPDATE `gsp_server_homes`
SET `server_expiration_date` = NULL
WHERE `server_expiration_date` IN ('X', '', '0', '0000-00-00 00:00:00')
   OR `server_expiration_date` IS NULL;

-- Convert to DATETIME only when it is still stored as VARCHAR.
SET @col_type = '';
SELECT DATA_TYPE INTO @col_type
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname
  AND TABLE_NAME   = 'gsp_server_homes'
  AND COLUMN_NAME  = 'server_expiration_date';

SET @sql = IF(
    @col_type = 'varchar',
    'ALTER TABLE `gsp_server_homes` MODIFY COLUMN `server_expiration_date` DATETIME NULL DEFAULT NULL',
    'SELECT "server_expiration_date already DATETIME – skipping" AS _msg'
);
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

-- ============================================================
-- SECTION 2: Add new billing lifecycle columns to gsp_server_homes
-- ============================================================

-- billing_status
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND COLUMN_NAME = 'billing_status';
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `gsp_server_homes` ADD COLUMN `billing_status` ENUM(\'Active\',\'Invoiced\',\'Expired\') NOT NULL DEFAULT \'Active\' AFTER `server_expiration_date`',
    'SELECT "billing_status already exists" AS _msg'
);
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

-- next_invoice_date
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND COLUMN_NAME = 'next_invoice_date';
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `gsp_server_homes` ADD COLUMN `next_invoice_date` DATETIME NULL DEFAULT NULL AFTER `billing_status`',
    'SELECT "next_invoice_date already exists" AS _msg'
);
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

-- last_invoice_id
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND COLUMN_NAME = 'last_invoice_id';
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `gsp_server_homes` ADD COLUMN `last_invoice_id` INT NULL DEFAULT NULL AFTER `next_invoice_date`',
    'SELECT "last_invoice_id already exists" AS _msg'
);
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

-- billing_enabled
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND COLUMN_NAME = 'billing_enabled';
SET @sql = IF(
    @col_exists = 0,
    'ALTER TABLE `gsp_server_homes` ADD COLUMN `billing_enabled` TINYINT(1) NOT NULL DEFAULT 1 AFTER `last_invoice_id`',
    'SELECT "billing_enabled already exists" AS _msg'
);
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

-- ============================================================
-- SECTION 3: Create gsp_invoices (post-purchase renewal invoices)
--            Distinct from gsp_billing_invoices (pre-purchase cart)
-- ============================================================

CREATE TABLE IF NOT EXISTS `gsp_invoices` (
    `invoice_id`       INT            NOT NULL AUTO_INCREMENT,
    `home_id`          INT            NOT NULL,
    `user_id`          INT            NOT NULL,
    `created_at`       DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `due_date`         DATETIME       NOT NULL,
    `paid_at`          DATETIME       NULL     DEFAULT NULL,
    `billing_status`   ENUM('Invoiced','Active','Expired')
                                      NOT NULL DEFAULT 'Invoiced',
    `rate_type`        ENUM('daily','monthly','yearly')
                                      NOT NULL DEFAULT 'monthly',
    `price_per_player` DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `player_slots`     INT            NOT NULL DEFAULT 0,
    `quantity`         INT            NOT NULL DEFAULT 1,
    `subtotal`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `total_due`        DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `payment_method`   VARCHAR(64)    NOT NULL DEFAULT 'PayPal',
    `payment_id`       VARCHAR(255)   NULL     DEFAULT NULL,
    `notes`            TEXT           NULL,
    PRIMARY KEY (`invoice_id`),
    KEY `idx_home_id`       (`home_id`),
    KEY `idx_user_id`       (`user_id`),
    KEY `idx_billing_status`(`billing_status`),
    KEY `idx_due_date`      (`due_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
-- SECTION 4: Create gsp_billing_config (per-game or global rates)
-- ============================================================

CREATE TABLE IF NOT EXISTS `gsp_billing_config` (
    `config_id`                INT            NOT NULL AUTO_INCREMENT,
    `game_key`                 VARCHAR(128)   NULL     DEFAULT NULL COMMENT 'NULL = global default',
    `rate_type`                ENUM('daily','monthly','yearly')
                                              NOT NULL DEFAULT 'monthly',
    `price_per_player`         DECIMAL(10,2)  NOT NULL DEFAULT 0.00,
    `grace_days`               INT            NOT NULL DEFAULT 0,
    `delete_after_expired_days`INT            NOT NULL DEFAULT 7,
    `enabled`                  TINYINT(1)     NOT NULL DEFAULT 1,
    `created_at`               DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`               DATETIME       NULL     ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`config_id`),
    KEY `idx_game_key` (`game_key`),
    KEY `idx_enabled`  (`enabled`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert global default config if none exists
INSERT INTO `gsp_billing_config`
    (`game_key`, `rate_type`, `price_per_player`, `grace_days`, `delete_after_expired_days`, `enabled`)
SELECT NULL, 'monthly', 0.00, 0, 7, 1
WHERE NOT EXISTS (
    SELECT 1 FROM `gsp_billing_config` WHERE `game_key` IS NULL LIMIT 1
);

-- ============================================================
-- SECTION 5: Populate server_homes.billing_status from existing
--            gsp_billing_orders data
--            Priority: Expired > Invoiced > Active
-- ============================================================

-- Active: paid, installed, active, running, enabled, online
UPDATE `gsp_server_homes` sh
INNER JOIN `gsp_billing_orders` bo
        ON bo.home_id = sh.home_id
       AND CAST(bo.home_id AS UNSIGNED) > 0
SET sh.`billing_status`       = 'Active',
    sh.`server_expiration_date` = bo.`end_date`,
    sh.`next_invoice_date`     = bo.`end_date`
WHERE bo.`status` IN ('paid', 'installed', 'active', 'running', 'enabled', 'online');

-- Invoiced: renew, unpaid, pending, overdue, invoice, invoiced, in-cart
UPDATE `gsp_server_homes` sh
INNER JOIN `gsp_billing_orders` bo
        ON bo.home_id = sh.home_id
       AND CAST(bo.home_id AS UNSIGNED) > 0
SET sh.`billing_status`       = 'Invoiced',
    sh.`server_expiration_date` = bo.`end_date`,
    sh.`next_invoice_date`     = bo.`end_date`
WHERE bo.`status` IN ('renew', 'unpaid', 'pending', 'overdue', 'invoice', 'invoiced', 'in-cart');

-- Expired: expired, cancelled, terminated, suspended, deleted
UPDATE `gsp_server_homes` sh
INNER JOIN `gsp_billing_orders` bo
        ON bo.home_id = sh.home_id
       AND CAST(bo.home_id AS UNSIGNED) > 0
SET sh.`billing_status`       = 'Expired',
    sh.`server_expiration_date` = bo.`end_date`
WHERE bo.`status` IN ('expired', 'cancelled', 'terminated', 'suspended', 'deleted');

-- Backfill server_expiration_date from billing_orders where still NULL
UPDATE `gsp_server_homes` sh
INNER JOIN `gsp_billing_orders` bo
        ON bo.home_id = sh.home_id
       AND CAST(bo.home_id AS UNSIGNED) > 0
SET sh.`server_expiration_date` = bo.`end_date`
WHERE sh.`server_expiration_date` IS NULL
  AND bo.`end_date` IS NOT NULL;

-- ============================================================
-- SECTION 6: Normalise gsp_billing_orders.status to new values
-- ============================================================

-- Active (was: paid, installed, active, running, enabled, online)
UPDATE `gsp_billing_orders`
SET `status` = 'Active'
WHERE `status` IN ('paid', 'installed', 'active', 'running', 'enabled', 'online');

-- Invoiced (was: renew, unpaid, pending, overdue, invoice, invoiced, in-cart)
UPDATE `gsp_billing_orders`
SET `status` = 'Invoiced'
WHERE `status` IN ('renew', 'unpaid', 'pending', 'overdue', 'invoice', 'invoiced', 'in-cart');

-- Expired (was: expired, cancelled, terminated, suspended, deleted)
UPDATE `gsp_billing_orders`
SET `status` = 'Expired'
WHERE `status` IN ('expired', 'cancelled', 'terminated', 'suspended', 'deleted');

-- ============================================================
-- SECTION 7: Add indexes to gsp_server_homes for billing queries
-- ============================================================

SET @idx = 0;
SELECT COUNT(*) INTO @idx FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND INDEX_NAME = 'idx_billing_status';
SET @sql = IF(@idx = 0,
    'ALTER TABLE `gsp_server_homes` ADD INDEX `idx_billing_status` (`billing_status`)',
    'SELECT "idx_billing_status exists" AS _msg');
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

SET @idx = 0;
SELECT COUNT(*) INTO @idx FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND INDEX_NAME = 'idx_server_expiration_date';
SET @sql = IF(@idx = 0,
    'ALTER TABLE `gsp_server_homes` ADD INDEX `idx_server_expiration_date` (`server_expiration_date`)',
    'SELECT "idx_server_expiration_date exists" AS _msg');
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

SET @idx = 0;
SELECT COUNT(*) INTO @idx FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND INDEX_NAME = 'idx_next_invoice_date';
SET @sql = IF(@idx = 0,
    'ALTER TABLE `gsp_server_homes` ADD INDEX `idx_next_invoice_date` (`next_invoice_date`)',
    'SELECT "idx_next_invoice_date exists" AS _msg');
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

SET @idx = 0;
SELECT COUNT(*) INTO @idx FROM information_schema.STATISTICS
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = 'gsp_server_homes' AND INDEX_NAME = 'idx_billing_enabled';
SET @sql = IF(@idx = 0,
    'ALTER TABLE `gsp_server_homes` ADD INDEX `idx_billing_enabled` (`billing_enabled`)',
    'SELECT "idx_billing_enabled exists" AS _msg');
PREPARE _stmt FROM @sql; EXECUTE _stmt; DEALLOCATE PREPARE _stmt;

-- ============================================================
-- DONE
-- ============================================================

SELECT CONCAT(
    'Migration complete. ',
    'gsp_server_homes now has billing_status/next_invoice_date/last_invoice_id/billing_enabled. ',
    'gsp_invoices and gsp_billing_config tables created. ',
    'gsp_billing_orders.status normalised to Active/Invoiced/Expired.'
) AS Migration_Result;
