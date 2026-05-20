# Billing System Migration Summary

## Files Modified

### 1. `module.php` - Database Schema
**Changes:**
- Removed all legacy `ALTER TABLE` migration queries (db_version reset to 1)
- Updated to single clean install with current schema
- Added `ogp_billing_invoices` table definition
- Added missing columns to `billing_orders`: `order_date`, `payment_txid`, `paid_ts`
- Changed `end_date` from VARCHAR to DATETIME
- Removed obsolete columns: `cart_id`, `extended`
- Removed `billing_carts` table (replaced by invoices)
- Added proper indexes for performance

### 2. `cron-shop.php` - Server Lifecycle Automation
**Fixed Logic Errors:**
- OLD BUG: Was deleting servers with `status='paid'` or `status='installed'` if end_date was close
- NEW: Only processes servers based on **invoice payment status**, not just order status
- Now uses `billing_invoices` table to determine if payment is due

**New 3-Step Process:**
1. **Create Renewal Invoices** (7 days before expiration)
   - Find `installed` servers expiring soon
   - Check if unpaid invoice exists
   - If not, create renewal invoice
   - Send email reminder

2. **Suspend Servers** (on expiration with unpaid invoice)
   - Find `installed` servers past end_date
   - Check if they have unpaid invoices
   - Stop server, disable FTP, unassign from user
   - Status → `suspended`

3. **Delete Servers** (7 days after suspension)
   - Find `suspended` servers 7+ days past end_date
   - Still have unpaid invoices
   - Permanently delete files and database
   - Status → `deleted`

## New Files Created

### 1. `migration_to_invoices.sql`
**Purpose:** Upgrade existing installations  
**What it does:**
- Adds new columns to `billing_orders`
- Creates `billing_invoices` table
- Migrates existing paid orders to have invoice records
- Removes obsolete `billing_carts` table
- Adds performance indexes

### 2. `INVOICE_SYSTEM.md`
**Purpose:** Documentation  
**Contents:**
- Table schemas explained
- Workflow diagrams
- Status field definitions
- Cron automation logic
- Migration instructions

## SQL for Fresh Install

The `module.php` now contains clean CREATE TABLE statements for:

### ogp_billing_services
```sql
CREATE TABLE `ogp_billing_services` (
    service_id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(255),
    remote_server_id VARCHAR(255),
    price_monthly FLOAT(15,4),
    enabled INT DEFAULT 1,
    ... [other fields]
);
```

### ogp_billing_orders
```sql
CREATE TABLE `ogp_billing_orders` (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT NOT NULL,
    home_name VARCHAR(255),
    home_id VARCHAR(255),
    status VARCHAR(16) DEFAULT 'in-cart',
    order_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    end_date DATETIME NULL,
    payment_txid VARCHAR(255) NULL,
    paid_ts DATETIME NULL,
    ... [other fields]
    KEY (user_id),
    KEY (status),
    KEY (home_id)
);
```

### ogp_billing_invoices (NEW)
```sql
CREATE TABLE `ogp_billing_invoices` (
    invoice_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    user_id INT NOT NULL,
    customer_name VARCHAR(255),
    customer_email VARCHAR(255),
    amount FLOAT(15,2),
    currency VARCHAR(3) DEFAULT 'USD',
    status VARCHAR(16) DEFAULT 'unpaid',
    invoice_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    due_date DATETIME NULL,
    paid_date DATETIME NULL,
    payment_txid VARCHAR(255),
    payment_method VARCHAR(50),
    description VARCHAR(500),
    invoice_duration VARCHAR(16),
    qty INT DEFAULT 1,
    KEY (order_id),
    KEY (user_id),
    KEY (status),
    KEY (due_date)
);
```

## Migration Steps for Existing Installations

1. **Backup Database**
   ```bash
   mysqldump -u root -p ogp_panel > backup_before_invoice_migration.sql
   ```

2. **Run Migration Script**
   ```bash
   mysql -u root -p ogp_panel < modules/billing/migration_to_invoices.sql
   ```

3. **Verify Tables**
   ```sql
   SHOW TABLES LIKE 'ogp_billing%';
   -- Should show: billing_services, billing_orders, billing_invoices
   
   DESCRIBE ogp_billing_orders;
   -- Should have: order_date, payment_txid, paid_ts, end_date (DATETIME)
   
   DESCRIBE ogp_billing_invoices;
   -- Should exist with all invoice fields
   ```

4. **Test Cron Job**
   ```bash
   cd /path/to/ogp/web
   php modules/billing/cron-shop.php
   ```

5. **Check Logs**
   ```sql
   SELECT * FROM ogp_logger WHERE type LIKE '%BILLING-CRON%' ORDER BY date DESC LIMIT 20;
   ```

## Key Improvements

1. **Accurate Server Management**
   - Servers only suspended if they have **unpaid invoices**
   - Active paid servers are never touched
   - Clear separation between order state and payment state

2. **Audit Trail**
   - Every payment creates an invoice record
   - Can track payment history per server
   - Know exactly when/why server was suspended

3. **Flexible Pricing**
   - Each renewal can have different price
   - Support for discounts and promotions
   - Currency per invoice (multi-currency support ready)

4. **Better Customer Experience**
   - Clear invoice emails with due dates
   - 7-day warning before expiration
   - 7-day grace period before deletion

## Status Field Values Reference

### billing_orders.status
- `in-cart` - Initial state, unpaid
- `paid` - Payment received, awaiting provisioning
- `installed` - Server active and running ✅
- `suspended` - Stopped due to non-payment
- `deleted` - Permanently removed
- `expired` - Service ended
- `renew` - Renewal in cart (legacy, now uses invoices)

### billing_invoices.status
- `unpaid` - Invoice created, awaiting payment
- `paid` - Invoice paid successfully

## Next Steps for Implementation

1. Update cart.php to show invoices instead of orders
2. Update my_account.php "Renew" button to create invoices
3. Update payment success flow to mark invoices paid
4. Add invoice viewing page
5. Test full workflow: order → pay → renew → pay renewal
