-- Enhanced coupon system for billing module
-- This creates a flexible coupon system with game filters and usage tracking
-- Table prefix is hardcoded to gsp_ for standalone billing module

-- Drop existing table if upgrading from old coupon module
DROP TABLE IF EXISTS `gsp_billing_coupons`;

-- Create enhanced coupons table
CREATE TABLE `gsp_billing_coupons` (
    `coupon_id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT,
    `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `usage_type` ENUM('one_time', 'permanent') NOT NULL DEFAULT 'one_time',
    `game_filter_type` ENUM('all_games', 'specific_games') NOT NULL DEFAULT 'all_games',
    `game_filter_list` TEXT COMMENT 'JSON array of game keys when game_filter_type=specific_games',
    `max_uses` INT(11) DEFAULT NULL COMMENT 'NULL for unlimited uses',
    `current_uses` INT(11) NOT NULL DEFAULT 0,
    `expires` DATETIME DEFAULT NULL,
    `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT(11) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`coupon_id`),
    UNIQUE KEY `idx_code` (`code`),
    KEY `idx_active_expires` (`is_active`, `expires`),
    KEY `idx_created_by` (`created_by`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Add coupon_id field to billing_orders if it doesn't exist
SET @tablename = 'gsp_billing_orders';
SET @checkIfColumnExists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'coupon_id'
);

SET @addColumn = IF(@checkIfColumnExists = 0,
    'ALTER TABLE `gsp_billing_orders` ADD COLUMN `coupon_id` INT(11) DEFAULT NULL AFTER `user_id`, ADD KEY `idx_coupon` (`coupon_id`)',
    'SELECT "Column coupon_id already exists in gsp_billing_orders"'
);

PREPARE stmt FROM @addColumn;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add coupon_id field to billing_invoices if it doesn't exist
SET @tablename = 'gsp_billing_invoices';
SET @checkIfColumnExists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = @tablename 
    AND COLUMN_NAME = 'coupon_id'
);

SET @addColumn = IF(@checkIfColumnExists = 0,
    'ALTER TABLE `gsp_billing_invoices` ADD COLUMN `coupon_id` INT(11) DEFAULT NULL AFTER `user_id`, ADD KEY `idx_coupon` (`coupon_id`)',
    'SELECT "Column coupon_id already exists in gsp_billing_invoices"'
);

PREPARE stmt FROM @addColumn;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add discount_amount field to billing_invoices to track actual discount applied
SET @checkIfColumnExists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'gsp_billing_invoices' 
    AND COLUMN_NAME = 'discount_amount'
);

SET @addColumn = IF(@checkIfColumnExists = 0,
    'ALTER TABLE `gsp_billing_invoices` ADD COLUMN `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount`',
    'SELECT "Column discount_amount already exists in gsp_billing_invoices"'
);

PREPARE stmt FROM @addColumn;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add discount_amount field to billing_orders to track permanent discounts
SET @checkIfColumnExists = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'gsp_billing_orders' 
    AND COLUMN_NAME = 'discount_amount'
);

SET @addColumn = IF(@checkIfColumnExists = 0,
    'ALTER TABLE `gsp_billing_orders` ADD COLUMN `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `price`',
    'SELECT "Column discount_amount already exists in gsp_billing_orders"'
);

PREPARE stmt FROM @addColumn;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Sample coupons for testing
INSERT INTO `gsp_billing_coupons` (`code`, `name`, `description`, `discount_percent`, `usage_type`, `game_filter_type`, `game_filter_list`, `expires`) VALUES
('WELCOME10', 'Welcome 10% Off', 'New customer welcome discount - 10% off any game', 10.00, 'one_time', 'all_games', NULL, DATE_ADD(NOW(), INTERVAL 1 YEAR)),
('ARMA25', 'Arma Series 25% Off', 'Save 25% on any Arma game server', 25.00, 'permanent', 'specific_games', '["arma2_win32", "arma2oa_win32", "arma3_linux32", "arma3_linux64", "arma3_win64", "arma-reforger_linux64", "arma-reforger_win64"]', NULL);
