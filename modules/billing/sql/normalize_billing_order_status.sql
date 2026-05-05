-- normalize_billing_order_status.sql
-- ============================================================
-- Migrate legacy billing_orders.status values to the canonical
-- three-value lifecycle used by GSP billing:
--
--   Active   – server is provisioned and current
--   Invoiced – renewal invoice generated; payment due
--   Expired  – invoice unpaid past due date; server pending deletion
--
-- Old values and their mappings:
--   installed  -> Active   (was written by old provisioner)
--   paid       -> Active   (was written after PayPal capture, before provisioning)
--   suspended  -> Expired  (was written by old cron when overdue)
--
-- Run this ONCE against the panel database after deploying the updated
-- cron-shop.php and application code.  It is safe to re-run (idempotent).
-- ============================================================

-- Map old 'installed' to 'Active'
UPDATE `<PREFIX>billing_orders`
SET    `status` = 'Active'
WHERE  `status` = 'installed';

-- Map old 'paid' to 'Active'
-- (Orders that were paid but not yet provisioned should be provisioned
--  via the admin orders panel after this migration.)
UPDATE `<PREFIX>billing_orders`
SET    `status` = 'Active'
WHERE  `status` = 'paid';

-- Map old 'suspended' to 'Expired'
UPDATE `<PREFIX>billing_orders`
SET    `status` = 'Expired'
WHERE  `status` = 'suspended';

-- Optional: verify counts after migration
-- SELECT status, COUNT(*) AS count FROM gsp_billing_orders GROUP BY status ORDER BY count DESC;
