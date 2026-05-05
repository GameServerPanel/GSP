-- normalize_billing_order_status.sql
--
-- One-time migration: standardize gsp_billing_orders.status to the canonical
-- three-value set used by cron-shop.php, create_servers.php, and the game
-- monitor expiration lookup:
--
--   Active   – server provisioned and billing current
--   Invoiced – renewal invoice open; service still running
--   Expired  – invoice unpaid past grace period; server suspended/awaiting deletion
--
-- Legacy → canonical mapping applied by this script:
--   'installed'  → 'Active'   (provisioned via old invoice-first flow)
--   'paid'       → 'Active'   (payment captured but before explicit provisioning step)
--   'suspended'  → 'Invoiced' (overdue; renewal invoice was open — maps to Invoiced
--                               so cron Step B will expire them on the next run if
--                               still unpaid, rather than silently treating them as Active)
--
-- All other statuses ('in-cart', 'cancelled', 'refunded', 'Active', 'Invoiced',
-- 'Expired') are left unchanged.
--
-- Compatible with MySQL 5.7+ and MariaDB 10.2+.
-- Table prefix is hardcoded to gsp_ (standalone billing module context).
-- Run ONCE on an existing installation; safe to run again (no-op on clean data).

-- 'installed' → 'Active'
UPDATE `gsp_billing_orders`
   SET `status` = 'Active'
 WHERE `status` = 'installed';

-- 'paid' → 'Active'
UPDATE `gsp_billing_orders`
   SET `status` = 'Active'
 WHERE `status` = 'paid';

-- 'suspended' → 'Invoiced'
-- These rows had an open renewal invoice; cron-shop Step B will move them to
-- 'Expired' on the next run if the invoice remains unpaid.
UPDATE `gsp_billing_orders`
   SET `status` = 'Invoiced'
 WHERE `status` = 'suspended';

-- Diagnostic: show any remaining non-canonical status values after migration.
-- Expected result: only rows with status IN ('Active','Invoiced','Expired',
-- 'in-cart','cancelled','refunded') should appear.
SELECT `status`, COUNT(*) AS `count`
  FROM `gsp_billing_orders`
 GROUP BY `status`
 ORDER BY `status`;

-- Diagnostic: billing_orders whose home_id references a non-existent server home.
-- These orders will show "No expiration date found" on the game monitor until
-- home_id is corrected (set to the real home_id or to 0 if the server is gone).
SELECT o.`order_id`,
       o.`user_id`,
       o.`home_name`,
       o.`home_id`         AS missing_home_id,
       o.`status`,
       o.`end_date`
  FROM `gsp_billing_orders`  o
  LEFT JOIN `gsp_server_homes` sh ON sh.`home_id` = o.`home_id`
 WHERE o.`home_id` != '0'
   AND o.`home_id` != ''
   AND sh.`home_id` IS NULL
 ORDER BY o.`order_id`;
