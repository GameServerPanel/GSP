# PayPal Payment Flow - Quick Debug Reference

## Quick Commands

### View recent errors:
```bash
cd /home/runner/work/GSP/GSP/modules/billing/logs

# Last 50 lines of create order log
tail -50 paypal_create_order.log

# Last 50 lines of capture log  
tail -50 paypal_capture.log

# Last 50 lines of client errors
tail -50 client_errors.log
```

### Watch logs live:
```bash
# In terminal, run:
tail -f /home/runner/work/GSP/GSP/modules/billing/logs/paypal_*.log
```

### Search for specific error:
```bash
# Find all OAuth errors
grep "OAUTH.*ERROR" paypal_create_order.log paypal_capture.log

# Find database errors
grep "DB.*FAILED" paypal_capture.log

# Find a specific request by ID
grep "req_12345" paypal_create_order.log
```

## Common Error Patterns

### ❌ "JSON error" or "unable to handle this request"

**What to check:**
1. Browser console (F12 → Console tab) for JavaScript errors
2. `client_errors.log` for client-side issues
3. `paypal_create_order.log` for `JSON_DECODE_ERROR`

**Quick fix:**
- Check if cart items are valid
- Verify amount calculations are correct
- Look for PHP errors that might corrupt JSON output

---

### ❌ HTTP ERROR 500

**What to check:**
1. `paypal_create_order.log` for `CREATE_ORDER_HTTP_ERROR`
2. `paypal_capture.log` for `CAPTURE_HTTP_ERROR`
3. Look for `OAUTH.*ERROR` entries

**Quick fix:**
- Verify PayPal credentials are correct
- Check PayPal API status: https://www.paypal-status.com/
- Verify sandbox vs live mode settings match credentials

---

### ❌ Payment seems successful but no order created

**What to check:**
1. `paypal_capture.log` for `DB_CONNECTION_FAILED`
2. Look for `UPDATE_INVOICES_FAILED`
3. Check `ORDER_CREATE_FAILED`

**Quick fix:**
- Verify database connection settings
- Check if `ogp_billing_invoices` table exists
- Verify `ogp_billing_orders` table exists
- Check table permissions

---

### ❌ Intermittent failures (works sometimes, fails sometimes)

**What to check:**
1. Compare successful vs failed requests in logs
2. Look for timeout errors (`CURL.*ERROR`)
3. Check for database connection pool exhaustion

**Quick fix:**
- Check server load/resources
- Verify network connectivity to PayPal API
- Check for rate limiting

## Log File Locations

```
/home/runner/work/GSP/GSP/modules/billing/logs/
├── paypal_create_order.log    # Order creation (when clicking "Pay")
├── paypal_capture.log          # Payment capture (after PayPal approval)
└── client_errors.log           # JavaScript/browser errors
```

## Request ID Format

- Create order: `req_XXXXXXXXXXXXX`
- Capture order: `cap_XXXXXXXXXXXXX`

When user sees an error with `(Ref: req_abc123)`, search logs for that ID.

## Important Log Labels

### Create Order Flow:
- `REQUEST_START` → `RAW_INPUT` → `PARSED_INPUT`
- `OAUTH_REQUEST_START` → `OAUTH_SUCCESS`
- `CREATE_ORDER_REQUEST_START` → `CREATE_ORDER_SUCCESS`

### Capture Order Flow:
- `REQUEST_START` → `PARSED_INPUT`
- `OAUTH_SUCCESS` → `CAPTURE_SUCCESS`
- `DB_CONNECTED` → `PROCESSING_INVOICES`
- `ORDER_CREATED_SUCCESS` or `ORDER_EXTENDED_SUCCESS`

### Error Labels:
- `*_ERROR` - Something went wrong
- `*_FAILED` - Operation failed
- `INVALID_*` - Invalid input/data

## Browser Console Debugging

1. Open cart page
2. Press F12 to open DevTools
3. Go to Console tab
4. Click "Pay with PayPal"
5. Watch for:
   - Red error messages
   - `PayPal Error:` logs
   - Network errors (check Network tab)

## Testing Checklist

When testing payments:

- [ ] Check browser console for errors
- [ ] Note the Ref ID if error occurs
- [ ] Check `paypal_create_order.log` for the request
- [ ] Check `paypal_capture.log` if got past order creation
- [ ] Verify database tables exist and have data
- [ ] Check PayPal sandbox account activity

## Need More Help?

See full guide: `PAYPAL_DEBUGGING_GUIDE.md`

## Key Configuration Files

- PayPal credentials: `api/create_order.php` and `api/capture_order.php`
  - Lines 5-6: `$client_id` and `$client_secret`
  - Line 4: `$sandbox` (true/false)
  
- Database config: `includes/config.inc.php`
  - `$db_host`, `$db_user`, `$db_pass`, `$db_name`
  - `$table_prefix`

## Status Checklist for Issues

When user reports error:

1. **Get details:**
   - [ ] What error message did they see?
   - [ ] What was the Ref ID (if shown)?
   - [ ] Can they reproduce it?

2. **Check logs:**
   - [ ] Find the request by Ref ID
   - [ ] Look for ERROR or FAILED labels
   - [ ] Check surrounding context (before/after)

3. **Verify config:**
   - [ ] PayPal credentials valid?
   - [ ] Database connection working?
   - [ ] Correct sandbox/live mode?

4. **Test:**
   - [ ] Try creating test order
   - [ ] Watch logs in real-time
   - [ ] Check database for created records
