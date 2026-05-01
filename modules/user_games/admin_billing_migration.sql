-- Admin Billing Integration Migration
-- Run this once to add required columns to billing_orders.
-- All statements use IF NOT EXISTS so the file is safe to re-run.

-- Mark orders that were created by an admin (not paid via checkout)
ALTER TABLE `gsp_billing_orders`
    ADD COLUMN IF NOT EXISTS `created_by_admin` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'Set to 1 when an admin manually created this server via the panel';

-- Track whether an order is a renewal/extension (already referenced by create_servers.php)
ALTER TABLE `gsp_billing_orders`
    ADD COLUMN IF NOT EXISTS `extended` TINYINT(1) NOT NULL DEFAULT 0
    COMMENT 'Set to 1 when this order is a renewal of an existing server';
