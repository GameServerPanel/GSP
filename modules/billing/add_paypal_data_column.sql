-- Add paypal_data column to billing_orders table
-- This stores the full PayPal response JSON for admin/refund tracking

ALTER TABLE `ogp_billing_orders` 
ADD COLUMN `paypal_data` TEXT NULL AFTER `payment_txid`;

-- Update comment
ALTER TABLE `ogp_billing_orders` 
MODIFY COLUMN `paypal_data` TEXT NULL COMMENT 'Full PayPal API response JSON for tracking/refunds';
