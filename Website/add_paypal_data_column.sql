-- Add paypal_data column to billing_orders table
-- This stores the full PayPal response JSON for admin/refund tracking
-- Table prefix is hardcoded to gsp_ for standalone billing module

ALTER TABLE `gsp_billing_orders` 
ADD COLUMN `paypal_data` TEXT NULL AFTER `payment_txid`;

-- Update comment
ALTER TABLE `gsp_billing_orders` 
MODIFY COLUMN `paypal_data` TEXT NULL COMMENT 'Full PayPal API response JSON for tracking/refunds';
