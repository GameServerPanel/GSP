-- Migration: add override_price to billing_service_remote_servers
-- Run once on existing installs that already have the mapping table (db_version 2)
-- but are missing the override_price column (added in db_version 3 / module v3.1).
--
-- Replace 'gsp_' with your actual table prefix if it differs.
--
-- This statement is safe to run multiple times only if your MySQL version supports
-- ADD COLUMN IF NOT EXISTS (MySQL 8.0.3+).  On older versions, check first:
--   SHOW COLUMNS FROM gsp_billing_service_remote_servers LIKE 'override_price';

ALTER TABLE `gsp_billing_service_remote_servers`
    ADD COLUMN IF NOT EXISTS `override_price` DECIMAL(10,2) NULL AFTER `enabled`;

-- If your MySQL is older than 8.0.3, use the conditional form instead:
-- ALTER TABLE `gsp_billing_service_remote_servers` ADD COLUMN `override_price` DECIMAL(10,2) NULL AFTER `enabled`;
