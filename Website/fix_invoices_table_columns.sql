-- Fix missing columns / indexes for gsp_billing_invoices
-- Safe script: checks information_schema and adds each missing column/index using prepared statements.
-- IMPORTANT: Run on the target database (use the panel DB). Make a backup before running.
-- Table prefix is hardcoded to gsp_ for standalone billing module

-- Use the current database
SET @db = DATABASE();
SET @tbl = 'gsp_billing_invoices';

-- Helper: add a column if missing
-- Usage pattern below; repeated for every column we expect from module.php

-- 1) service_id
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'service_id';

IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `service_id` INT(11) NOT NULL AFTER `user_id`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 2) home_name
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'home_name';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `home_name` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `service_id`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 3) ip
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'ip';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `ip` INT(11) NOT NULL DEFAULT 0 AFTER `home_name`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 4) max_players
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'max_players';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `max_players` INT(11) NOT NULL DEFAULT 0 AFTER `ip`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 5) remote_control_password
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'remote_control_password';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `remote_control_password` VARCHAR(255) NULL AFTER `max_players`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 6) ftp_password
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'ftp_password';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `ftp_password` VARCHAR(255) NULL AFTER `remote_control_password`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 7) customer_name
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'customer_name';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `customer_name` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `ftp_password`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 8) customer_email
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'customer_email';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `customer_email` VARCHAR(255) NOT NULL DEFAULT '''' AFTER `customer_name`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 9) amount
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'amount';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `amount` FLOAT(15,2) NOT NULL DEFAULT 0 AFTER `customer_email`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 10) currency
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'currency';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `currency` VARCHAR(3) NOT NULL DEFAULT ''USD'' AFTER `amount`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 11) status
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'status';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `status` VARCHAR(16) NOT NULL DEFAULT ''due'' AFTER `currency`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 12) invoice_date
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'invoice_date';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `invoice_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER `status`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 13) due_date
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'due_date';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `due_date` DATETIME NULL AFTER `invoice_date`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 14) paid_date
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'paid_date';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `paid_date` DATETIME NULL AFTER `due_date`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 15) payment_txid
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'payment_txid';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `payment_txid` VARCHAR(255) NULL AFTER `paid_date`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 16) payment_method
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'payment_method';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `payment_method` VARCHAR(50) NULL AFTER `payment_txid`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 17) description
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'description';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `description` VARCHAR(500) NOT NULL DEFAULT '''' AFTER `payment_method`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 18) invoice_duration
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'invoice_duration';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `invoice_duration` VARCHAR(16) NOT NULL DEFAULT ''month'' AFTER `description`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 19) qty
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'qty';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD COLUMN `qty` INT(11) NOT NULL DEFAULT 1 AFTER `invoice_duration`');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- 20) indexes: service_id, order_id, user_id, status, due_date
-- Add index helper

SELECT COUNT(*) INTO @cnt FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'service_id';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD KEY `service_id` (`service_id`)');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

SELECT COUNT(*) INTO @cnt FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'order_id';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD KEY `order_id` (`order_id`)');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

SELECT COUNT(*) INTO @cnt FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'user_id';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD KEY `user_id` (`user_id`)');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

SELECT COUNT(*) INTO @cnt FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'status';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD KEY `status` (`status`)');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

SELECT COUNT(*) INTO @cnt FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'due_date';
IF @cnt = 0 THEN
  SET @s = CONCAT('ALTER TABLE `', @tbl, '` ADD KEY `due_date` (`due_date`)');
  PREPARE stmt FROM @s; EXECUTE stmt; DEALLOCATE PREPARE stmt;
END IF;

-- Done
SELECT 'done' as status;
