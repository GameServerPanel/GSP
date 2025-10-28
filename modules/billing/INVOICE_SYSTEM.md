# Billing System - Invoice-Based Architecture

## Overview
The billing system now uses a **dual-table architecture** separating orders (ongoing services) from invoices (payment records).

## Database Tables

### 1. `ogp_billing_services`
**Purpose:** Available game server packages/products  
**Key Fields:**
- `service_id` - Unique identifier
- `service_name` - Display name
- `remote_server_id` - Target server(s)
- `price_monthly`, `price_year` - Pricing tiers
- `enabled` - Availability flag

### 2. `ogp_billing_orders` (formerly just cart items)
**Purpose:** Active game server instances (ongoing services)  
**Key Fields:**
- `order_id` - Unique identifier
- `user_id` - Owner
- `service_id` - Product reference
- `home_id` - Panel game home ID (after provisioning)
- `home_name` - Server name
- `status` - Current state (see Status Flow below)
- `order_date` - When created
- `end_date` - Expiration date
- `payment_txid` - Last payment transaction
- `paid_ts` - Last payment timestamp

**Status Values:**
- `in-cart` - User added to cart, not yet paid
- `paid` - Payment received, awaiting provisioning
- `installed` - ✅ Server provisioned and running
- `suspended` - Server stopped due to non-payment
- `expired` - Service ended
- `renew` - Renewal pending in cart

### 3. `ogp_billing_invoices` (NEW)
**Purpose:** Payment records (one invoice per payment)  
**Key Fields:**
- `invoice_id` - Unique identifier
- `order_id` - Links to the server order
- `user_id` - Customer
- `customer_name` - Full name
- `customer_email` - Email address
- `amount` - Total due
- `currency` - USD, EUR, etc.
- `status` - `unpaid` or `paid`
- `invoice_date` - When created
- `due_date` - Payment deadline
- `paid_date` - When paid
- `payment_txid` - PayPal/Stripe transaction ID
- `payment_method` - PayPal, Stripe, etc.
- `description` - Invoice line items
- `invoice_duration` - Billing period (month/year)
- `qty` - Quantity/duration multiplier

## Workflow

### Initial Purchase
1. User selects game server package → Creates row in `billing_orders` (status: `in-cart`)
2. System creates `billing_invoices` entry (status: `unpaid`, linked to order_id)
3. Cart page shows unpaid invoices
4. User pays → Invoice status becomes `paid`, order status becomes `paid`
5. Provisioning happens → Order status becomes `installed`
6. Server is active until `end_date`

### Renewal Process
1. User clicks "Renew" on active server (My Account page)
2. System creates NEW invoice in `billing_invoices` (status: `unpaid`, same order_id)
3. Cart shows the unpaid renewal invoice
4. User pays → Invoice status becomes `paid`
5. Order `end_date` is extended by the renewal period

### Cron Automation (`cron-shop.php`)
The cron job checks invoice status to manage servers:

**7 days before expiration:**
- Check if order has unpaid invoice for upcoming period
- If NO unpaid invoice exists → Create one (status: `unpaid`)
- Email customer about upcoming renewal

**On expiration (end_date reached):**
- Check if order has unpaid invoice
- If YES → Suspend server (stop, disable FTP, unassign from user)
- Order status → `suspended`

**7 days after suspension:**
- If still unpaid → Delete server permanently
- Order status → `expired`

## Key Advantages

1. **Clear Payment History:** Each invoice represents one payment
2. **Audit Trail:** Can track when/how much each renewal cost
3. **Flexible Pricing:** Can adjust price per renewal (discounts, promotions)
4. **Multi-Payment Support:** One order can have many invoices
5. **Accurate Status:** Order status reflects server state, invoice status reflects payment
6. **No Race Conditions:** Webhook updates invoice, provisioning updates order

## Cart Logic

**Cart page displays:**
- All invoices with `status = 'unpaid'` for the current user
- Groups by order_id to show which server each invoice is for
- Total amount = SUM of all unpaid invoice amounts

**After payment:**
- Invoice `status` → `paid`
- Invoice `paid_date` → NOW()
- Invoice `payment_txid` → transaction ID from PayPal/Stripe
- Order `status` → `paid` (if new order) or `end_date` extended (if renewal)

## My Account Logic

**Show Invoices Section:**
- Group invoices by status (unpaid, paid, overdue)
- Display invoice_date, amount, status
- Link to view invoice details

**Show Current Servers Section:**
- Display orders with `status = 'installed'`
- Show end_date (expiration)
- "Renew" button creates new invoice

## Migration Notes

- Run `migration_to_invoices.sql` on existing installations
- Creates `billing_invoices` table
- Adds missing columns to `billing_orders`
- Migrates existing paid orders to have invoices
- Removes obsolete `billing_carts` table
