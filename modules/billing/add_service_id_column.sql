-- Add missing service_id column to ogp_billing_invoices table
-- This column is required to track which service/game plan was purchased

ALTER TABLE `ogp_billing_invoices` 
ADD COLUMN `service_id` INT(11) NOT NULL AFTER `user_id`;

-- Add index for better query performance
ALTER TABLE `ogp_billing_invoices`
ADD KEY `service_id` (`service_id`);
