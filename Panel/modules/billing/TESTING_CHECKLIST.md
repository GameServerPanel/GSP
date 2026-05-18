# Testing Checklist for Billing Invoice/Order Flow Fixes

## Prerequisites

1. **Database Setup**
   - [ ] Verify `ogp_billing_invoices` table exists
   - [ ] Verify `ogp_billing_orders` table exists
   - [ ] Verify tables have all required columns (see create_invoices_table.sql)

2. **Configuration**
   - [ ] Copy `modules/billing/includes/config.inc.php.orig` to `modules/billing/includes/config.inc.php`
   - [ ] Update database credentials in config.inc.php
   - [ ] Verify `$table_prefix` is set correctly (default: "ogp_")
   - [ ] Verify `$SITE_DATA_DIR` path is writable

3. **PayPal Configuration**
   - [ ] Verify sandbox client_id and client_secret in api/create_order.php
   - [ ] Verify sandbox client_id and client_secret in api/capture_order.php
   - [ ] Verify webhook_id in webhook.php

## Test 1: Add to Cart (Invoice Creation)

**Test NEW Order Flow**

1. Navigate to order.php
2. Select a game server configuration
3. Set price to $0.00 for testing (or use regular price)
4. Fill in all required fields
5. Click "Add to Cart"

**Expected Results:**
- [ ] Redirects to cart.php
- [ ] Item appears in cart
- [ ] Database check: Invoice created in `ogp_billing_invoices`
  - [ ] status = 'due'
  - [ ] order_id = 0 (no order yet)
  - [ ] user_id matches logged-in user
  - [ ] amount, qty, service_id populated correctly

**Verification SQL:**
```sql
SELECT * FROM ogp_billing_invoices WHERE status='due' ORDER BY invoice_id DESC LIMIT 5;
```

## Test 2: Free Button (Manual Order Creation)

**Test Free/Claim Flow**

1. Ensure you have item in cart with amount = 0.00
2. Click "Claim (Free)" button

**Expected Results:**
- [ ] Redirects to return.php
- [ ] Shows payment confirmation
- [ ] Invoice marked as paid
- [ ] Order created
- [ ] Cart is empty

**Verification SQL:**
```sql
-- Check invoice was marked paid
SELECT invoice_id, status, paid_date, order_id FROM ogp_billing_invoices 
WHERE status='paid' ORDER BY invoice_id DESC LIMIT 1;

-- Check order was created
SELECT order_id, user_id, status, end_date, payment_txid FROM ogp_billing_orders 
ORDER BY order_id DESC LIMIT 1;

-- Verify link
SELECT i.invoice_id, i.order_id, o.order_id 
FROM ogp_billing_invoices i 
LEFT JOIN ogp_billing_orders o ON i.order_id = o.order_id 
WHERE i.status='paid' ORDER BY i.invoice_id DESC LIMIT 5;
```

**Check Logs:**
```bash
tail -50 modules/billing/logs/site.log | grep -E "(payment|free_create)"
```

## Test 3: PayPal Payment Flow

**Test PayPal Checkout**

1. Add paid item to cart (e.g., $5.00)
2. Click PayPal button in cart
3. Should redirect to PayPal sandbox
4. Login with sandbox buyer account
5. Approve payment
6. Should return to payment_success.php

**Expected Results:**
- [ ] PayPal button renders correctly
- [ ] Creates PayPal order (check browser console for order ID)
- [ ] Redirects to PayPal sandbox
- [ ] After approval, returns to payment_success.php
- [ ] No JavaScript errors in console
- [ ] No "Unexpected end of JSON input" error
- [ ] Invoice marked as paid
- [ ] Order created
- [ ] Cart is empty

**Browser Console Checks:**
```
Look for:
✓ "PayPal cart debug: ..." - Shows cart data
✓ "Creating order..." - Order creation started
✓ "Order created." - Order creation succeeded
✓ "Capturing payment..." - Capture started
✗ Any errors - Should be none
```

**Verification SQL:**
```sql
-- Check invoice
SELECT invoice_id, status, paid_date, payment_txid, payment_method, order_id 
FROM ogp_billing_invoices 
WHERE payment_method='paypal' 
ORDER BY invoice_id DESC LIMIT 1;

-- Check order
SELECT order_id, user_id, status, price, end_date, payment_txid 
FROM ogp_billing_orders 
WHERE payment_txid LIKE '%' 
ORDER BY order_id DESC LIMIT 1;
```

**Check API Logs:**
```bash
# Check create_order.php payload
cat modules/billing/data/create_order_payload.log

# Check corrected URLs
cat modules/billing/data/corrected_urls.log

# Check for errors
cat modules/billing/data/create_order_errors.log
```

## Test 4: Webhook Processing

**Test Webhook Handler**

1. Trigger a PayPal payment (from Test 3)
2. PayPal will send webhook to webhook.php

**Expected Results:**
- [ ] Webhook receives POST from PayPal
- [ ] Signature verification succeeds
- [ ] Payment record processed
- [ ] Invoice marked paid (if not already)
- [ ] Order created/updated (if not already)

**Verification:**
```bash
# Check webhook log
tail -50 modules/billing/data/webhook.log

# Check for payment processing
grep "process_payment" modules/billing/data/webhook.log
```

**Check Data Files:**
```bash
ls -lah modules/billing/data/*.json
cat modules/billing/data/INV-*.json  # Check payment record format
```

## Test 5: Renewal Flow

**Setup Renewal Invoice**

1. Create a test order manually:
```sql
INSERT INTO ogp_billing_orders (
    user_id, service_id, home_name, ip, max_players, qty, invoice_duration,
    price, remote_control_password, ftp_password, status, order_date, end_date,
    payment_txid, paid_ts
) VALUES (
    1, 1, 'Test Server', 1, 10, 1, 'month',
    5.00, 'rconpass', 'ftppass', 'paid', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH),
    'TEST-INITIAL', NOW()
);
```

2. Get the order_id from the insert:
```sql
SELECT LAST_INSERT_ID();
```

3. Create renewal invoice:
```sql
INSERT INTO ogp_billing_invoices (
    order_id, user_id, service_id, home_name, ip, max_players, qty, invoice_duration,
    amount, status, customer_name, customer_email, due_date, description
) VALUES (
    LAST_INSERT_ID(), -- Use order_id from step 2
    1, 1, 'Test Server', 1, 10, 1, 'month',
    5.00, 'due', 'Test User', 'test@test.com', DATE_ADD(NOW(), INTERVAL 3 DAY), 
    'Renewal invoice'
);
```

**Test Renewal Payment**

1. Log in as user who owns the order
2. View cart - should show renewal invoice
3. Pay using free button or PayPal

**Expected Results:**
- [ ] Invoice marked as paid
- [ ] Original order's end_date extended by 1 month
- [ ] No duplicate order created
- [ ] Invoice.order_id still points to original order

**Verification SQL:**
```sql
-- Check order end_date was extended
SELECT order_id, end_date, status, payment_txid 
FROM ogp_billing_orders 
WHERE order_id = <order_id_from_step_2>;

-- Should show end_date = original end_date + 1 month

-- Check invoice
SELECT invoice_id, order_id, status, paid_date 
FROM ogp_billing_invoices 
WHERE order_id = <order_id_from_step_2>;

-- Should show paid invoice linked to same order_id
```

## Test 6: Error Handling

**Test Invalid Scenarios**

1. **Missing session**: Try to pay without being logged in
   - [ ] Should redirect to login or show error

2. **Database connection failure**: Temporarily break DB config
   - [ ] capture_order.php should return JSON error, not crash
   - [ ] Error should be logged

3. **PayPal API failure**: Use invalid credentials
   - [ ] Should show error in console
   - [ ] Should log error
   - [ ] Should not corrupt database

## Common Issues and Solutions

### Issue: "Config file not found"
**Solution**: Copy config.inc.php.orig to config.inc.php

### Issue: "Table doesn't exist"
**Solution**: Run create_invoices_table.sql

### Issue: "Permission denied writing to data/"
**Solution**: 
```bash
chmod 775 modules/billing/data
chown www-data:www-data modules/billing/data  # Or your web server user
```

### Issue: "PayPal button doesn't render"
**Solution**: Check browser console for errors, verify client_id

### Issue: "Unexpected end of JSON input"
**Solution**: 
- Check PHP error log: `tail -f /var/log/php/error.log`
- Verify display_errors=0 in capture_order.php
- Check for syntax errors: `php -l api/capture_order.php`

### Issue: "Cart still shows items after payment"
**Solution**: 
- Check if invoice status changed to 'paid'
- Check if process_payment_record was called
- Check logs for errors

## Performance Testing

**Test with Multiple Items**

1. Add 5 items to cart
2. Pay with PayPal
3. Verify all 5 invoices marked paid
4. Verify all 5 orders created
5. Verify all linked correctly

**Test Concurrent Payments**

1. Add item to cart in two different browsers (same user)
2. Attempt to pay both simultaneously
3. Verify both process correctly
4. Check for race conditions

## Security Testing

**Test SQL Injection**

1. Try adding special characters to form fields
2. Try manipulating invoice_id in POST requests
3. Verify all inputs are sanitized/escaped

**Test Session Hijacking**

1. Try accessing cart with invalid session
2. Try paying for someone else's invoice
3. Verify proper authorization checks

**Test Webhook Signature**

1. Send fake webhook without valid signature
2. Verify it's rejected
3. Check logs for security events

## Cleanup

After testing, clean up test data:

```sql
-- Remove test invoices
DELETE FROM ogp_billing_invoices WHERE customer_email = 'test@test.com';

-- Remove test orders
DELETE FROM ogp_billing_orders WHERE remote_control_password = 'rconpass';
```

## Sign-off

- [ ] All tests passed
- [ ] No errors in logs
- [ ] Documentation reviewed
- [ ] Security checks completed
- [ ] Ready for production deployment

**Tested by**: _______________  
**Date**: _______________  
**Environment**: _______________ (Dev/Staging/Production)  
**Notes**: _______________
