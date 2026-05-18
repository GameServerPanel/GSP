-- Create billing_invoices table for invoice-first flow
-- Run this SQL to enable the new billing system
-- Table prefix is hardcoded to gsp_ for standalone billing module

CREATE TABLE IF NOT EXISTS `gsp_billing_invoices` (
    `invoice_id` INT(11) NOT NULL AUTO_INCREMENT,
    `order_id` INT(11) NOT NULL DEFAULT 0,
    `user_id` INT(11) NOT NULL,
    `service_id` INT(11) NOT NULL,
    `home_name` VARCHAR(255) NOT NULL DEFAULT '',
    `ip` INT(11) NOT NULL DEFAULT 0,
    `max_players` INT(11) NOT NULL DEFAULT 0,
    `remote_control_password` VARCHAR(255) NULL,
    `ftp_password` VARCHAR(255) NULL,
    `customer_name` VARCHAR(255) NOT NULL DEFAULT '',
    `customer_email` VARCHAR(255) NOT NULL DEFAULT '',
    `amount` FLOAT(15,2) NOT NULL DEFAULT 0,
    `currency` VARCHAR(3) NOT NULL DEFAULT 'USD',
    `status` VARCHAR(16) NOT NULL DEFAULT 'due',
    `invoice_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `due_date` DATETIME NULL,
    `paid_date` DATETIME NULL,
    `payment_txid` VARCHAR(255) NULL,
    `payment_method` VARCHAR(50) NULL,
    `description` VARCHAR(500) NOT NULL DEFAULT '',
    `invoice_duration` VARCHAR(16) NOT NULL DEFAULT 'month',
    `qty` INT(11) NOT NULL DEFAULT 1,
    PRIMARY KEY (`invoice_id`),
    KEY `order_id` (`order_id`),
    KEY `user_id` (`user_id`),
    KEY `status` (`status`),
    KEY `due_date` (`due_date`),
    KEY `service_id` (`service_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
