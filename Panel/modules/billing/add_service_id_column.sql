-- Add missing service_id column to gsp_billing_invoices table
-- This column is required to track which service/game plan was purchased
-- Table prefix is hardcoded to gsp_ for standalone billing module

ALTER TABLE `gsp_billing_invoices` 
ADD COLUMN `service_id` INT(11) NOT NULL AFTER `user_id`;

-- Add index for better query performance
ALTER TABLE `gsp_billing_invoices`
ADD KEY `service_id` (`service_id`);
