# PayPal Payment Flow Debugging Guide

## Overview

This guide explains how to diagnose and troubleshoot PayPal payment errors using the comprehensive logging system that has been added to the payment flow.

## Problem Being Addressed

Users were experiencing intermittent errors when clicking "Pay from PayPal" button:
- JSON parsing errors
- HTTP ERROR 500
- "Currently unable to handle this request" errors

These errors would "flip-flop" between different error types, making it difficult to diagnose the root cause.

## Log Files Location

All logs are stored in: `/modules/billing/logs/`

### Available Log Files

1. **`paypal_create_order.log`** - Logs all PayPal order creation requests
   - When: Created when user clicks "Pay with PayPal" button
   - Contains: Request data, OAuth tokens, PayPal API responses
   
2. **`paypal_capture.log`** - Logs all payment capture attempts
   - When: Created when PayPal redirects user back after approving payment
   - Contains: Capture requests, database operations, order creation
   
3. **`client_errors.log`** - Logs JavaScript errors from browser
   - When: Created when browser encounters errors during checkout
   - Contains: Client-side errors, PayPal SDK issues, network failures

## How to Debug Payment Issues

### Step 1: Identify the Request

Each request has a unique ID for tracking:
- Create order requests: `req_XXXXX`
- Capture order requests: `cap_XXXXX`

Look for these IDs in error messages shown to users.

### Step 2: Check the Logs

#### For "Failed to create order" errors:

```bash
tail -100 /modules/billing/logs/paypal_create_order.log
```

Look for:
- `JSON_DECODE_ERROR` - Invalid input from cart.php
- `OAUTH_CURL_ERROR` or `OAUTH_HTTP_ERROR` - Can't connect to PayPal
- `CREATE_ORDER_HTTP_ERROR` - PayPal rejected the order

#### For "Payment capture failed" errors:

```bash
tail -100 /modules/billing/logs/paypal_capture.log
```

Look for:
- `OAUTH_*_ERROR` - Authentication issues
- `CAPTURE_HTTP_ERROR` - PayPal rejected capture
- `DB_CONNECTION_FAILED` - Database issues
- `UPDATE_INVOICES_FAILED` - Can't mark invoices as paid
- `ORDER_CREATE_FAILED` - Can't create order record

#### For client-side errors:

```bash
tail -100 /modules/billing/logs/client_errors.log
```

Look for:
- Network errors (fetch failed)
- PayPal SDK errors
- JSON parsing errors

### Step 3: Common Issues and Solutions

#### Issue: OAuth fails (OAUTH_HTTP_ERROR)

**Log entry example:**
```
[2025-10-29 21:30:00] [req_12345] OAUTH_HTTP_ERROR
http_code => 401
```

**Cause:** Invalid PayPal credentials

**Solution:** Check that `$client_id` and `$client_secret` in `api/create_order.php` and `api/capture_order.php` are correct.

---

#### Issue: JSON decode error

**Log entry example:**
```
[2025-10-29 21:30:00] [req_12345] JSON_DECODE_ERROR
error => Syntax error
```

**Cause:** Malformed JSON from cart.php or corrupted request

**Solution:** 
1. Check the `RAW_INPUT` entry before the error
2. Verify cart.php is sending valid JSON
3. Check for PHP errors that might corrupt the output

---

#### Issue: PayPal returns error creating order

**Log entry example:**
```
[2025-10-29 21:30:00] [req_12345] CREATE_ORDER_HTTP_ERROR
http_code => 400
response => {"name":"INVALID_REQUEST","details":[{"issue":"..."}]}
```

**Cause:** Invalid order data sent to PayPal

**Solution:**
1. Look at `PAYPAL_ORDER_PAYLOAD` entry to see what was sent
2. Common issues:
   - Invalid amount format (must be 2 decimals)
   - Invalid currency code
   - Malformed items array
   - Invalid URLs (return_url, cancel_url must be absolute URLs)

---

#### Issue: Database connection failed

**Log entry example:**
```
[2025-10-29 21:30:00] [cap_12345] DB_CONNECTION_FAILED
error => Access denied for user
```

**Cause:** Can't connect to database

**Solution:**
1. Check database credentials in `includes/config.inc.php`
2. Verify database server is running
3. Check database permissions

---

#### Issue: Invoice update failed

**Log entry example:**
```
[2025-10-29 21:30:00] [cap_12345] UPDATE_INVOICES_FAILED
error => Table 'ogp_billing_invoices' doesn't exist
```

**Cause:** Database schema issue

**Solution:**
1. Verify table exists and has correct name
2. Check `$table_prefix` variable in config
3. Run database migrations if needed

## Log Entry Structure

Each log entry includes:

```
[TIMESTAMP] [REQUEST_ID] LOG_LABEL
key => value
key => value
--------------------------------------------------------------------------------
```

- **TIMESTAMP**: When the event occurred (Y-m-d H:i:s format)
- **REQUEST_ID**: Unique identifier for tracking the request
- **LOG_LABEL**: What happened (e.g., OAUTH_SUCCESS, CREATE_ORDER_FAILED)
- **Data**: Relevant data for the event (arrays/objects pretty-printed)

## Request Flow with Logging

### Creating an Order

1. User clicks "Pay with PayPal" in cart.php
2. JavaScript calls `api/create_order.php`
3. Logs generated:
   - `REQUEST_START` - Initial request info
   - `RAW_INPUT` - What was received
   - `PARSED_INPUT` - Decoded data
   - `OAUTH_REQUEST_START` - Starting OAuth
   - `OAUTH_RESPONSE` - OAuth result
   - `OAUTH_SUCCESS` or `OAUTH_*_ERROR`
   - `CREATE_ORDER_REQUEST_START` - Sending to PayPal
   - `CREATE_ORDER_RESPONSE` - PayPal's response
   - `CREATE_ORDER_SUCCESS` or `CREATE_ORDER_*_ERROR`

### Capturing Payment

1. User approves payment on PayPal
2. PayPal redirects back to site
3. JavaScript calls `api/capture_order.php`
4. Logs generated:
   - `REQUEST_START` - Initial request
   - `RAW_INPUT` - Order ID received
   - `PARSED_INPUT` - Decoded data
   - `OAUTH_*` - Authentication steps
   - `CAPTURE_REQUEST_START` - Starting capture
   - `CAPTURE_RESPONSE` - PayPal's response
   - `CAPTURE_SUCCESS` or `CAPTURE_*_ERROR`
   - `PAYMENT_DETAILS` - Extracted transaction info
   - `STARTING_DB_PROCESSING` - Beginning database work
   - `DB_CONNECTED` - Database ready
   - `SESSION_INFO` - User session details
   - `PROCESSING_INVOICES` - Starting invoice processing
   - `UPDATE_INVOICES_*` - Invoice update results
   - `PROCESSING_INVOICE` - For each invoice
   - `NEW_ORDER_DETECTED` or `RENEWAL_DETECTED`
   - `ORDER_CREATE_*` or `ORDER_EXTENDED_*`
   - `PROCESSING_COMPLETE` - Done

## Monitoring Tips

### Watch logs in real-time

```bash
# Watch create order logs
tail -f /modules/billing/logs/paypal_create_order.log

# Watch capture logs
tail -f /modules/billing/logs/paypal_capture.log

# Watch all logs
tail -f /modules/billing/logs/*.log
```

### Filter for errors only

```bash
grep -i error /modules/billing/logs/paypal_create_order.log
grep -i failed /modules/billing/logs/paypal_capture.log
```

### Find specific request by ID

```bash
grep "req_abc123" /modules/billing/logs/paypal_create_order.log
grep "cap_xyz789" /modules/billing/logs/paypal_capture.log
```

### Count successful vs failed requests

```bash
grep -c "CREATE_ORDER_SUCCESS" /modules/billing/logs/paypal_create_order.log
grep -c "CREATE_ORDER.*ERROR" /modules/billing/logs/paypal_create_order.log
```

## Log Rotation

Logs will grow over time. Consider implementing log rotation:

```bash
# Archive old logs
cd /modules/billing/logs
gzip paypal_create_order.log
mv paypal_create_order.log.gz paypal_create_order.$(date +%Y%m%d).log.gz
touch paypal_create_order.log
```

Or use logrotate:

```
/path/to/modules/billing/logs/*.log {
    daily
    rotate 7
    compress
    delaycompress
    notifempty
    create 0644 www-data www-data
}
```

## Error Messages to Users

When errors occur, users now see messages with request IDs:

- "Failed to create order: API error 500 (Ref: req_abc123)"
- "Payment capture failed: oauth_fail (Ref: cap_xyz789)"

Use these reference IDs to search the logs for the full details.

## Getting Help

When reporting issues, include:

1. The exact error message shown to user (including Ref ID)
2. Relevant log entries (search by Ref ID)
3. What the user was trying to do
4. Whether it's consistent or intermittent
5. Browser console output (F12 → Console tab)

## Additional Resources

- PayPal API Documentation: https://developer.paypal.com/api/rest/
- PayPal Sandbox Testing: https://developer.paypal.com/developer/accounts/
- PayPal Error Codes: https://developer.paypal.com/api/rest/reference/orders/v2/errors/

## Changelog

### 2025-10-29
- Added comprehensive logging to create_order.php
- Enhanced logging in capture_order.php  
- Added client-side error logging
- Created debugging guide
