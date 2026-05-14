# Billing Invoice/Order Flow - Fix Summary

## Problem Statement

The billing system had several critical issues:

1. **JSON Error**: "Failed to execute 'json' on 'Response': Unexpected end of JSON input" when returning from PayPal payment
2. **Cart not clearing**: Items remained in cart after payment (invoices stayed as status='due')
3. **No order creation**: Orders were not being created after successful payment
4. **Missing renewal flow**: Renewal invoices (linked to existing orders) were not handled
5. **Free button errors**: The free/claim button was also experiencing errors

## Invoice-First Flow (Intended Design)

The system uses an invoice-first architecture:

1. **Add to Cart**: Creates INVOICE with status='due', order_id=0 (no order yet)
2. **View Cart**: Shows all invoices WHERE status='due'
3. **Payment**: 
   - For NEW orders (order_id=0): Mark invoice paid + CREATE new order
   - For RENEWALS (order_id>0): Mark invoice paid + EXTEND existing order's end_date
4. **Provisioning**: Separate step that provisions servers for paid orders

## Root Causes Identified

### 1. Missing Function
- `process_payment_record()` was called but never defined
- Referenced in webhook.php, cart.php (free button), but didn't exist
- This prevented any payment processing from completing

### 2. JSON Response Corruption
- `capture_order.php` had PHP errors/warnings during DB operations
- These were being output to the response, corrupting the JSON
- JavaScript couldn't parse the malformed JSON → "Unexpected end of JSON input"

### 3. Incomplete Payment Processing
- `capture_order.php` was supposed to:
  - Mark invoices as paid (status: 'due' → 'paid')
  - Create new orders OR extend existing orders
  - Link invoices to orders
- But the logic was incomplete and had issues

### 4. Session Compatibility
- capture_order.php used `$_SESSION['user_id']`
- cart.php used `$_SESSION['website_user_id']`
- This mismatch meant user couldn't be identified for payment processing

### 5. Hardcoded Table Names
- capture_order.php used hardcoded "ogp_billing_invoices" and "ogp_billing_orders"
- Should use `$table_prefix . "billing_invoices"` for flexibility
- Could cause failures if table prefix is different

## Solutions Implemented

### 1. Created payment_processor.php Helper
**File**: `modules/billing/includes/payment_processor.php`

**Function**: `process_payment_record($record)`
- Accepts payment record from webhook or direct capture
- Finds invoices to process by custom_id (invoice_id) or invoice reference
- For each invoice:
  - Marks invoice as paid (status='due' → 'paid')
  - If NEW order (order_id=0): Creates new order with calculated end_date
  - If RENEWAL (order_id>0): Extends existing order's end_date by invoice duration
  - Links invoice to order
- Returns true/false and logs all operations
- No HTML output (safe to require from webhook/API endpoints)

### 2. Fixed capture_order.php
**File**: `modules/billing/api/capture_order.php`

**Changes**:
- **Disabled error display**: `ini_set('display_errors', '0')` to prevent JSON corruption
- **Session compatibility**: Checks both `website_user_id` and `user_id`
- **Proper JSON errors**: Returns structured JSON on DB connection failure
- **Table prefix usage**: Uses `$table_prefix` instead of hardcoded names
- **Complete invoice processing**:
  - Marks all due invoices as paid
  - Handles both NEW orders and RENEWALS
  - Proper end_date calculation (months from qty + invoice_duration)
  - Links invoices to orders

### 3. Fixed payment_success.php
**File**: `modules/billing/payment_success.php`

**Changes**:
- Requires `payment_processor.php` helper
- Displays payment confirmation page
- Shows user's recent orders
- No longer contains duplicate/incomplete function definitions

### 4. Fixed webhook.php
**File**: `modules/billing/webhook.php`

**Changes**:
- Uses `payment_processor.php` instead of requiring full payment_success.php
- Prevents HTML output that would interfere with webhook response
- Processes payment record after verification

### 5. Fixed cart.php Free Button
**File**: `modules/billing/cart.php`

**Changes**:
- Uses `payment_processor.php` for consistent processing
- Free button now properly:
  - Marks invoice as paid
  - Creates order record
  - Calculates end_date
  - Processes payment record through shared function

## Payment Flow (After Fixes)

### PayPal Payment Flow
```
1. User clicks "Pay with PayPal" in cart.php
   ↓
2. JavaScript calls api/create_order.php
   → Creates PayPal order with custom_id = invoice_id
   ↓
3. User approves payment on PayPal
   ↓
4. JavaScript calls api/capture_order.php
   → PayPal captures payment
   → capture_order.php:
     a) Marks invoices as paid (status='due' → 'paid')
     b) For NEW: Creates order in billing_orders
     c) For RENEW: Extends existing order's end_date
     d) Links invoice to order (sets invoice.order_id)
   → Returns JSON: { status: "COMPLETED", ... }
   ↓
5. JavaScript redirects to payment_success.php
   → Shows confirmation page
   → Displays order details
   ↓
6. PayPal sends webhook to webhook.php (parallel)
   → Verifies signature
   → Calls process_payment_record()
   → Same processing as step 4 (idempotent)
   ↓
7. Cart is empty (invoices now have status='paid', not shown)
```

### Free/Claim Flow
```
1. User clicks "Claim (Free)" button in cart.php
   ↓
2. Cart.php POST handler:
   → Marks invoice as paid
   → Creates order record with calculated end_date
   → Links invoice to order
   → Creates simulated webhook file
   → Calls process_payment_record() for consistency
   ↓
3. Redirects to return.php
   → Shows payment confirmation
   ↓
4. Cart is empty (invoice marked paid)
```

### Renewal Flow
```
1. User has existing order (order_id > 0)
   ↓
2. System creates renewal invoice:
   → status = 'due'
   → order_id = <existing_order_id>
   → qty = renewal months
   ↓
3. Invoice appears in cart
   ↓
4. User pays (PayPal or Free)
   ↓
5. process_payment_record():
   → Detects order_id > 0 (renewal)
   → Fetches current end_date from existing order
   → Calculates new end_date:
     - If current end_date > now: extend from current end_date
     - Otherwise: extend from now
   → Updates order with new end_date
   → Marks invoice as paid
   ↓
6. Order subscription extended by renewal period
```

## Testing Checklist

Before deployment, verify:

- [ ] Config setup: Copy `config.inc.php.orig` to `config.inc.php` and configure
- [ ] Database: Ensure `ogp_billing_invoices` and `ogp_billing_orders` tables exist
- [ ] Test NEW order flow:
  - [ ] Add item to cart (creates invoice with status='due')
  - [ ] View cart (item appears)
  - [ ] Click "Claim (Free)" for $0 item (creates order, clears cart)
  - [ ] Verify order created in billing_orders
  - [ ] Verify invoice marked paid, linked to order
- [ ] Test PayPal flow:
  - [ ] Add paid item to cart
  - [ ] Click PayPal button
  - [ ] Complete payment on PayPal sandbox
  - [ ] Verify returns to payment_success.php without errors
  - [ ] Verify order created
  - [ ] Verify invoice marked paid
  - [ ] Verify cart is empty
- [ ] Test RENEWAL flow:
  - [ ] Create renewal invoice for existing order
  - [ ] Pay renewal invoice
  - [ ] Verify order end_date extended correctly
  - [ ] Verify invoice marked paid

## Security Considerations

All code changes maintain or improve security:

1. **SQL Injection Protection**: Uses prepared statements where possible
2. **Input Validation**: Validates all user inputs (invoice_id, user_id, etc.)
3. **Session Security**: Maintains separate website/panel sessions
4. **Webhook Verification**: PayPal signature verification still in place
5. **Error Logging**: Errors logged, not displayed to users (prevents information leakage)
6. **Database Credentials**: Configuration file outside web root (best practice)

## Files Changed

1. `modules/billing/includes/payment_processor.php` - NEW
2. `modules/billing/api/capture_order.php` - MODIFIED
3. `modules/billing/payment_success.php` - MODIFIED
4. `modules/billing/webhook.php` - MODIFIED
5. `modules/billing/cart.php` - MODIFIED

## Known Limitations

1. **Config file required**: System requires `includes/config.inc.php` to be created from .orig template
2. **Multi-item cart matching**: If cart has multiple items, all are processed together (could improve to match specific invoice_id)
3. **No transaction rollback**: If order creation fails, invoice may still be marked paid (could improve with DB transactions)

## Future Enhancements

1. Add database transactions for atomic invoice→order operations
2. Improve invoice matching in process_payment_record (more specific matching)
3. Add unit tests for payment processing logic
4. Add admin UI for viewing/managing invoice-order relationships
5. Add email notifications for payment confirmations
