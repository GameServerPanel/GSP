-- Migration Script: Billing System with Invoice Table
-- This script upgrades existing billing installations to the new invoice-based system
-- Run this ONCE on existing installations (not needed for fresh installs)
-- Compatible with MySQL 5.7+ and MariaDB 10.2+
-- Table prefix is hardcoded to gsp_ for standalone billing module

-- Step 1: Add new columns to billing_orders (only if they don't exist)
SET @dbname = DATABASE();
SET @tablename = 'gsp_billing_orders';

-- Add order_date column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'order_date';
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `gsp_billing_orders` ADD COLUMN `order_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`',
    'SELECT "Column order_date already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add payment_txid column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'payment_txid';
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `gsp_billing_orders` ADD COLUMN `payment_txid` VARCHAR(255) NULL AFTER `end_date`',
    'SELECT "Column payment_txid already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add paid_ts column
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'paid_ts';
SET @sql = IF(@col_exists = 0, 
    'ALTER TABLE `gsp_billing_orders` ADD COLUMN `paid_ts` DATETIME NULL AFTER `payment_txid`',
    'SELECT "Column paid_ts already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Modify existing columns to use proper data types
ALTER TABLE `gsp_billing_orders`
    MODIFY COLUMN `status` VARCHAR(16) NOT NULL DEFAULT 'in-cart',
    MODIFY COLUMN `remote_control_password` VARCHAR(255) NULL,
    MODIFY COLUMN `ftp_password` VARCHAR(255) NULL;

-- Convert end_date from VARCHAR to DATETIME (handle existing data)
-- First, update any '0' values to NULL
UPDATE `gsp_billing_orders` SET `end_date` = NULL WHERE `end_date` = '0' OR `end_date` = '';

-- Check current end_date type and convert if needed
SET @col_type = '';
SELECT DATA_TYPE INTO @col_type FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'end_date';

SET @sql = IF(@col_type = 'varchar', 
    'ALTER TABLE `gsp_billing_orders` MODIFY COLUMN `end_date` DATETIME NULL',
    'SELECT "Column end_date already DATETIME" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Remove obsolete columns from billing_orders
SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'cart_id';
SET @sql = IF(@col_exists > 0, 
    'ALTER TABLE `gsp_billing_orders` DROP COLUMN `cart_id`',
    'SELECT "Column cart_id already removed" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @col_exists = 0;
SELECT COUNT(*) INTO @col_exists FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = 'extended';
SET @sql = IF(@col_exists > 0, 
    'ALTER TABLE `gsp_billing_orders` DROP COLUMN `extended`',
    'SELECT "Column extended already removed" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 4: Add indexes to billing_orders for better performance
SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = 'idx_user_id';
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `gsp_billing_orders` ADD INDEX `idx_user_id` (`user_id`)',
    'SELECT "Index idx_user_id already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = 'idx_status';
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `gsp_billing_orders` ADD INDEX `idx_status` (`status`)',
    'SELECT "Index idx_status already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @index_exists = 0;
SELECT COUNT(*) INTO @index_exists FROM information_schema.STATISTICS 
WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND INDEX_NAME = 'idx_home_id';
SET @sql = IF(@index_exists = 0, 
    'ALTER TABLE `gsp_billing_orders` ADD INDEX `idx_home_id` (`home_id`)',
    'SELECT "Index idx_home_id already exists" AS message');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;


-- Step 5: Create the new billing_invoices table
CREATE TABLE IF NOT EXISTS `gsp_billing_invoices` (
    `invoice_id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL,
    `user_id` INT(11) NOT NULL,
    `customer_name` VARCHAR(255) NOT NULL DEFAULT '',
    `customer_email` VARCHAR(255) NOT NULL DEFAULT '',
    `amount` FLOAT(15,2) NOT NULL DEFAULT 0,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `status` VARCHAR(16) NOT NULL DEFAULT 'unpaid',
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
    KEY `due_date` (`due_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Step 6: Migrate existing paid orders to create initial invoices
-- This creates a historical invoice for each paid/installed order
INSERT INTO `gsp_billing_invoices` 
    (`order_id`, `user_id`, `customer_name`, `customer_email`, `amount`, `currency`, `status`, `invoice_date`, `paid_date`, `payment_txid`, `description`, `invoice_duration`, `qty`)
SELECT 
    o.order_id,
    o.user_id,
    CONCAT(COALESCE(u.users_fname, ''), ' ', COALESCE(u.users_lname, '')) AS customer_name,
    u.users_email AS customer_email,
    o.price AS amount,
    'USD' AS currency,
    'paid' AS status,
    COALESCE(o.order_date, NOW()) AS invoice_date,
    COALESCE(o.paid_ts, o.order_date, NOW()) AS paid_date,
    o.payment_txid,
    CONCAT('Initial invoice for ', o.home_name) AS description,
    o.invoice_duration,
    o.qty
FROM `gsp_billing_orders` o
LEFT JOIN `ogp_users` u ON o.user_id = u.user_id
WHERE o.status IN ('paid', 'installed')
    AND NOT EXISTS (
        SELECT 1 FROM `gsp_billing_invoices` i 
        WHERE i.order_id = o.order_id AND i.status = 'paid'
    );

-- Step 7: Drop the obsolete billing_carts table (replaced by invoice system)
DROP TABLE IF EXISTS `gsp_billing_carts`;

-- Step 8: Update billing_services charset for consistency
ALTER TABLE `gsp_billing_services` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

SELECT 'Migration completed successfully! Invoice-based billing system is now active.' AS Status;
