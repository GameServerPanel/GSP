# Invoice-First Billing Flow

## Overview
The billing system now follows an **invoice-first** workflow where invoices are created BEFORE orders. Orders are only created after successful payment.

## Workflow

### 1. Add to Cart (order.php → add_to_cart.php)
**What happens:**
- User clicks "Add to Cart" button on order page
- System creates a **billing_invoices** record with:
  - `status` = 'due'
  - `order_id` = 0 (no order exists yet)
  - All server details (service_id, home_name, ip, max_players, passwords, etc.)
  - Customer details (name, email from ogp_users)
  - Pricing (amount, qty, invoice_duration)
  - `due_date` = now + 3 days

**Database changes:**
- INSERT into `ogp_billing_invoices`
- NO changes to `ogp_billing_orders` (order doesn't exist yet)

### 2. Cart Display (cart.php)
**What shows:**
- Query: `SELECT * FROM ogp_billing_invoices WHERE status = 'due' AND user_id = ?`
- Displays all **unpaid invoices** (status='due')
- Shows invoice_id, home_name, ip, max_players, amount, qty
- Free items show "Claim (Free)" button
- Paid items show PayPal button

**Actions available:**
- Delete invoice (removes from cart, no order cleanup needed)
- Pay invoice (via PayPal or Free button)

### 3. Payment (PayPal or Free)

#### 3a. Free/Claim Flow (cart.php POST handler)
**When:** User clicks "Claim (Free)" or admin clicks "Create (Free)"

**What happens:**
1. Mark invoice as paid:
   - UPDATE `ogp_billing_invoices` SET status='paid', paid_date=NOW()
2. Create order record:
   - Calculate end_date (qty * invoice_duration)
   - INSERT into `ogp_billing_orders` with status='paid'
   - Get new order_id from INSERT
3. Link invoice to order:
   - UPDATE `ogp_billing_invoices` SET order_id=? WHERE invoice_id=?

**Database changes:**
- UPDATE `ogp_billing_invoices`: status='due' → 'paid', paid_date=NOW(), order_id=(new)
- INSERT `ogp_billing_orders`: New record with status='paid', end_date calculated

#### 3b. PayPal Flow (api/capture_order.php)
**When:** User pays via PayPal

**What should happen:**
1. PayPal sends capture webhook
2. System marks invoice as paid (same as Free flow)
3. System creates order record (same as Free flow)
4. System links invoice to order (same as Free flow)

**Database changes:** (Same as Free flow above)

### 4. Server Provisioning (create_servers.php)
**What happens:**
- Cron job or manual trigger finds orders with status='paid'
- Creates actual game server (home_id)
- Updates order: status='paid' → 'installed', home_id=(assigned)

**Database changes:**
- UPDATE `ogp_billing_orders`: status='paid' → 'installed', home_id=(assigned)

## Status Values

### Invoice Status
- **'due'** - Unpaid invoice (shows in cart)
- **'paid'** - Paid invoice (payment confirmed)
- **'cancelled'** - Deleted/cancelled invoice

### Order Status
- **'paid'** - Payment confirmed, awaiting provisioning
- **'installed'** - Server provisioned and running
- **'suspended'** - Server stopped for non-payment
- **'expired'** - Service ended

## Database Schema

### ogp_billing_invoices (INVOICE-FIRST)
```sql
invoice_id          INT AUTO_INCREMENT PRIMARY KEY
order_id            INT DEFAULT 0               -- Links to order AFTER payment (0 = not yet paid)
user_id             INT NOT NULL
service_id          INT NOT NULL                -- Server package being purchased
home_name           VARCHAR(255)                -- Server name
ip                  INT                         -- IP assignment
max_players         INT                         -- Player count
remote_control_password VARCHAR(255)            -- Server RCON password
ftp_password        VARCHAR(255)                -- FTP password
customer_name       VARCHAR(255)                -- Billing name
customer_email      VARCHAR(255)                -- Billing email
amount              FLOAT(15,2)                 -- Total price
currency            VARCHAR(3) DEFAULT 'USD'
status              VARCHAR(16) DEFAULT 'due'   -- 'due', 'paid', 'cancelled'
invoice_date        DATETIME DEFAULT NOW()
due_date            DATETIME                    -- Payment deadline
paid_date           DATETIME                    -- When paid
payment_txid        VARCHAR(255)                -- PayPal transaction ID
payment_method      VARCHAR(50)                 -- 'paypal', 'free', etc.
description         VARCHAR(500)                -- Invoice description
invoice_duration    VARCHAR(16) DEFAULT 'month' -- 'month', 'year', 'day'
qty                 INT DEFAULT 1               -- Quantity/duration multiplier
```

### ogp_billing_orders (ORDER-AFTER-PAYMENT)
```sql
order_id            INT AUTO_INCREMENT PRIMARY KEY
user_id             INT NOT NULL
service_id          INT NOT NULL
home_name           VARCHAR(255)
home_id             VARCHAR(255)                -- Panel game server ID (after provisioning)
ip                  INT
max_players         INT
qty                 INT
invoice_duration    VARCHAR(16)
price               FLOAT(15,2)
remote_control_password VARCHAR(255)
ftp_password        VARCHAR(255)
status              VARCHAR(16) DEFAULT 'paid'  -- 'paid', 'installed', 'suspended', 'expired'
order_date          DATETIME DEFAULT NOW()
end_date            DATETIME                    -- Subscription expiration
payment_txid        VARCHAR(255)
paid_ts             DATETIME
```

## Key Differences from Old Flow

### OLD (Order-First)
1. Add to cart → Create ORDER (status='in-cart')
2. View cart → Show orders WHERE status='in-cart'
3. Pay → UPDATE order status='in-cart' → 'paid'
4. Provision → UPDATE order status='paid' → 'installed'

### NEW (Invoice-First)
1. Add to cart → Create INVOICE (status='due', order_id=0)
2. View cart → Show invoices WHERE status='due'
3. Pay → Mark invoice paid + CREATE ORDER (status='paid') + Link invoice to order
4. Provision → UPDATE order status='paid' → 'installed'

## Benefits

1. **Clean Separation:** Invoices = payment requests, Orders = actual services
2. **Better Audit Trail:** Invoice IDs never change, order IDs created only after payment
3. **Renewal Support:** Can create multiple invoices for same order (renewals)
4. **Cart Simplicity:** Cart only shows unpaid invoices (single source of truth)
5. **Payment History:** All payments have invoice records, even free ones

## Migration Notes

**Existing orders with status='in-cart' need to be migrated:**
```sql
-- Convert existing cart items to invoices
INSERT INTO ogp_billing_invoices (
  order_id, user_id, service_id, home_name, ip, max_players, 
  remote_control_password, ftp_password, customer_name, customer_email,
  amount, status, invoice_duration, qty, description
)
SELECT 
  0, -- No order exists yet
  o.user_id,
  o.service_id,
  o.home_name,
  o.ip,
  o.max_players,
  o.remote_control_password,
  o.ftp_password,
  CONCAT(u.users_fname, ' ', u.users_lname),
  u.users_email,
  o.price,
  'due', -- Convert 'in-cart' to 'due'
  o.invoice_duration,
  o.qty,
  CONCAT('Migrated cart item: ', o.home_name)
FROM ogp_billing_orders o
LEFT JOIN ogp_users u ON o.user_id = u.user_id
WHERE o.status = 'in-cart';

-- Delete old cart items (now converted to invoices)
DELETE FROM ogp_billing_orders WHERE status = 'in-cart';
```
