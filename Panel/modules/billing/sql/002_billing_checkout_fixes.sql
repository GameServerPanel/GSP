-- =============================================================================
-- 002_billing_checkout_fixes.sql
-- Idempotent migration: adds columns required by the billing checkout fixes.
-- Safe to run multiple times (uses IF-based prepared statements).
-- Run against the panel database after deploying the updated PHP files.
-- IMPORTANT: Replace <PREFIX> with your actual table prefix (e.g. gsp_ or ogp_).
-- =============================================================================

SET @db  = DATABASE();
SET @tbl = '<PREFIX>billing_invoices';

-- 1) coupon_id — tracks which coupon was applied to an invoice
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'coupon_id';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_invoices` ADD COLUMN `coupon_id` INT(11) NOT NULL DEFAULT 0 AFTER `qty`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 2) discount_amount — records the monetary discount applied at checkout
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'discount_amount';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_invoices` ADD COLUMN `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `amount`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 3) payment_status — ENUM used by BillingRepository for idempotency
--    (present in module.php schema; older installs may be missing it)
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'payment_status';
SET @sql = IF(@cnt = 0,
    "ALTER TABLE `<PREFIX>billing_invoices` ADD COLUMN `payment_status` ENUM('unpaid','paid','cancelled','refunded') NOT NULL DEFAULT 'unpaid' AFTER `currency`",
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Backfill payment_status for existing rows:
--   'paid'   → payment_status = 'paid'
--   anything else → payment_status = 'unpaid'
UPDATE `<PREFIX>billing_invoices`
   SET `payment_status` = 'paid'
 WHERE `status` = 'paid' AND `payment_status` <> 'paid';

-- 4) subtotal — needed by BillingRepository::createInvoice (extended schema)
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'subtotal';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_invoices` ADD COLUMN `subtotal` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `discount_amount`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 5) total_due — needed by BillingRepository::createInvoice (extended schema)
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'total_due';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_invoices` ADD COLUMN `total_due` DECIMAL(15,2) NOT NULL DEFAULT 0 AFTER `subtotal`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 6) coupon_id index on billing_invoices
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.STATISTICS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND INDEX_NAME = 'coupon_id';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_invoices` ADD KEY `coupon_id` (`coupon_id`)',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- -------------------------
-- billing_orders additions
-- -------------------------
SET @tbl = '<PREFIX>billing_orders';

-- 7) coupon_id on billing_orders (already in baseline schema but guard for older installs)
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'coupon_id';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_orders` ADD COLUMN `coupon_id` INT(11) NOT NULL DEFAULT 0 AFTER `paid_ts`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 8) discount_amount on billing_orders (for permanent coupon records)
SET @cnt = 0;
SELECT COUNT(*) INTO @cnt FROM information_schema.COLUMNS
 WHERE TABLE_SCHEMA = @db AND TABLE_NAME = @tbl AND COLUMN_NAME = 'discount_amount';
SET @sql = IF(@cnt = 0,
    'ALTER TABLE `<PREFIX>billing_orders` ADD COLUMN `discount_amount` DECIMAL(10,2) NOT NULL DEFAULT 0.00 AFTER `price`',
    'SELECT 1');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- Done
SELECT 'billing_checkout_fixes migration complete' AS status;
