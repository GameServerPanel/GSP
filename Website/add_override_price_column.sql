-- DEPRECATED: This file is no longer needed.
--
-- The gsp_billing_service_remote_servers mapping table has been removed.
-- Server availability per game/service is now stored in gsp_billing_services.remote_server_id
-- as a comma-separated list of numeric server IDs (e.g. "1,3,7").
-- The module migration (db_version 4) drops the mapping table automatically.
--
-- The original content of this file is kept below for historical reference only.
-- Do NOT run this script on new installations.
--
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
