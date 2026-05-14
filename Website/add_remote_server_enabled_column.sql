-- DEPRECATED: This file is no longer needed.
--
-- The billing module no longer references an `enabled` column on gsp_remote_servers.
-- gsp_remote_servers is the server inventory table only.
-- Server availability per game/service is stored in gsp_billing_services.remote_server_id
-- as a comma-separated list of numeric server IDs (e.g. "1,3,7").
--
-- The original content of this file is kept below for historical reference only.
-- Do NOT run this script on new installations.
--
-- Migration: add `enabled` column to gsp_remote_servers
--
-- The original panel schema (panel.sql / ogp_remote_servers) includes an `enabled`
-- INT(11) column. Installations that were created from an older schema, or whose
-- table was renamed without carrying the column forward, may be missing it.
--
-- Run this once against your panel database (replace `gsp_` with your prefix if
-- different). Safe to skip if the column already exists — just check with:
--   SHOW COLUMNS FROM `gsp_remote_servers` LIKE 'enabled';
--
-- Usage:
--   mysql -u <user> -p <db_name> < modules/billing/add_remote_server_enabled_column.sql

SET @table_name = 'gsp_remote_servers';
SET @col_name   = 'enabled';

SET @sql = IF(
    (
        SELECT COUNT(*)
        FROM   INFORMATION_SCHEMA.COLUMNS
        WHERE  TABLE_SCHEMA = DATABASE()
          AND  TABLE_NAME   = @table_name
          AND  COLUMN_NAME  = @col_name
    ) = 0,
    CONCAT('ALTER TABLE `', @table_name, '` ADD COLUMN `enabled` INT(11) NOT NULL DEFAULT 1'),
    'SELECT "Column already exists — nothing to do" AS note'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;
