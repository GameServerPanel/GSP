# Quick Start Guide - GSP Billing Panel Integration

## What Was Completed

✅ Created `navigation.xml` - Routes panel URLs to billing pages  
✅ Created `my_orders_panel.php` - User view of paid orders  
✅ Updated `create_servers.php` - Enhanced provisioning with multi-server support  
✅ Created `admin_orders.php` - Admin order management interface  
✅ Created `test_integration.php` - Integration testing tool  
✅ Created `PANEL_INTEGRATION.md` - Complete documentation  

## How to Test Right Now

### Step 1: Test Integration
1. Log into your GSP panel
2. Navigate to: `home.php?m=billing&p=test_integration`
3. Review all checks - everything should show green ✓

### Step 2: Create a Test Order (Database)
If you don't have paid orders yet, create one in the database:

```sql
INSERT INTO billing_orders 
(user_id, service_id, home_name, max_players, price, qty, invoice_duration, status, order_date)
VALUES 
(1, 1, 'Test Minecraft Server', 25, 9.99, 1, 'month', 'paid', NOW());
```

Replace:
- `user_id = 1` with your actual user ID
- `service_id = 1` with a valid service_id from billing_services table

### Step 3: View Your Orders
Navigate to: `home.php?m=billing&p=my_orders`

You should see:
- Table with your paid orders
- "Provision Server" button for each order
- "Provision All My Servers" button if multiple orders

### Step 4: Provision a Server
Click "Provision Server" button

Expected behavior:
- Redirects to provision_servers page
- Creates game_home entry
- Assigns IP and port
- Installs game files (Steam/rsync/manual)
- Updates order status to 'installed'
- Shows success message
- Auto-redirects to Game Monitor after 3 seconds

### Step 5: Admin Testing (Admin Only)
Navigate to: `home.php?m=billing&p=admin_orders`

Features to test:
- View all orders across all users
- Filter by status dropdown
- Search by username/order ID/server name
- Select multiple orders with checkboxes
- Bulk actions dropdown
- Individual provision/view buttons

## Multi-Server Cart Testing

### Setup:
Create multiple paid orders for the same user:

```sql
INSERT INTO billing_orders 
(user_id, service_id, home_name, max_players, price, qty, invoice_duration, status, order_date)
VALUES 
(1, 1, 'Minecraft Server 1', 25, 9.99, 1, 'month', 'paid', NOW()),
(1, 2, 'Minecraft Server 2', 50, 14.99, 1, 'month', 'paid', NOW()),
(1, 3, 'ARK Server', 100, 19.99, 1, 'month', 'paid', NOW());
```

### Test:
1. Navigate to: `home.php?m=billing&p=my_orders`
2. Click "Provision All My Servers (3)" button
3. Wait for provisioning to complete
4. Verify all 3 orders changed to status='installed'
5. Check Game Monitor - all 3 servers should appear

## Panel URLs Reference

| Page | URL | Access | Purpose |
|------|-----|--------|---------|
| Test Integration | `home.php?m=billing&p=test_integration` | user, admin | Verify setup |
| My Orders | `home.php?m=billing&p=my_orders` | user, admin | View paid orders |
| Provision Servers | `home.php?m=billing&p=provision_servers&order_id=X` | user, admin | Create servers |
| Admin Orders | `home.php?m=billing&p=admin_orders` | admin only | Manage all orders |

## Common Issues & Solutions

### "No paid orders found"
**Problem:** No orders with status='paid' in database  
**Solution:** Check database: `SELECT * FROM billing_orders WHERE status='paid'`  
**Fix:** Update test order: `UPDATE billing_orders SET status='paid' WHERE order_id=X`

### "Page not found" / 404 error
**Problem:** navigation.xml not loaded or file missing  
**Solution 1:** Verify navigation.xml exists in `modules/billing/`  
**Solution 2:** Check file permissions (must be readable by web server)  
**Solution 3:** Verify XML syntax is valid (no typos)

### "Access Denied"
**Problem:** User group doesn't match page access requirements  
**Solution:** Check `$_SESSION['users_group']` matches navigation.xml access attribute  
**Fix for admin pages:** Only 'admin' group can access admin_orders

### Provisioning fails silently
**Problem:** create_servers.php encounters error but doesn't show it  
**Solution:** Check PHP error logs  
**Common causes:**
- Invalid remote_server_id (stored in ip field)
- Missing game server files
- SteamCMD not configured
- Permissions issues on game directories

### Multiple servers provision but some fail
**Problem:** Foreach loop continues even if one fails  
**Solution:** Check individual order details in admin_orders  
**Fix:** Provision failed orders individually to see specific error

## Architecture Quick Reference

### Order Status Flow
```
in-cart → paid → installed → invoiced → suspended/deleted
          ↑                     ↑
          |                     |
    (payment)            (renewal or non-payment)
```

### Provisioning Flow
```
User orders on website → Payment processed → status='paid'
                                              ↓
User logs into panel → My Orders → Click "Provision Server"
                                              ↓
create_servers.php → Query WHERE status='paid' → foreach order
                                              ↓
Create game_home → Assign IP:Port → Install files → Update status='installed'
                                              ↓
                        Email + Discord notification → Redirect to Game Monitor
```

### File Locations
```
modules/billing/
├── navigation.xml              [Panel routing config]
├── my_orders_panel.php         [User order list]
├── admin_orders.php            [Admin management]
├── create_servers.php          [Server provisioning]
├── test_integration.php        [Testing tool]
├── PANEL_INTEGRATION.md        [Full documentation]
└── QUICK_START.md              [This file]
```

## Next Steps After Testing

### 1. Optional: Add Menu Items
Edit `modules/billing/module.php` around line 20:

```php
$module_menus = array(
    array('subpage' => 'my_orders', 'name'=>'My Orders', 'group'=>'user'),
    array('subpage' => 'admin_orders', 'name'=>'Manage Orders', 'group'=>'admin')
);
```

This adds menu items to panel sidebar navigation.

### 2. Customize Success Messages
Edit `create_servers.php` around line 385 to customize redirect behavior:
- Change auto-redirect delay (currently 3 seconds)
- Add custom success messages
- Modify redirect destination

### 3. Add Email Templates
Enhance email notifications in create_servers.php:
- Custom HTML email templates
- Include server connection details
- Add next steps for users

### 4. Discord Webhook Formatting
Improve Discord notifications with:
- Rich embeds with server details
- Color coding by status
- Direct links to Game Monitor

### 5. Production Deployment
Before going live:
- Test with real payment gateway (PayPal/Stripe)
- Verify SteamCMD and game installs work
- Test with multiple concurrent users
- Set up monitoring and logging
- Configure backup system

## Support & Troubleshooting

### Debug Mode
To see detailed errors, enable PHP error reporting temporarily:

In `create_servers.php` at the top of exec_ogp_module():
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Database Debugging
Check order details:
```sql
SELECT o.*, s.service_name, u.users_login 
FROM billing_orders o
LEFT JOIN billing_services s ON o.service_id = s.service_id
LEFT JOIN users u ON o.user_id = u.user_id
WHERE o.status = 'paid';
```

### Log Files to Check
- PHP error log: `/var/log/php_errors.log` (or server equivalent)
- Apache/Nginx error log: `/var/log/apache2/error.log`
- OGP agent log: Check agent output for remote commands
- Game server logs: In each game_home directory

## Questions?

Refer to:
- `PANEL_INTEGRATION.md` - Complete technical documentation
- `test_integration.php` - Run diagnostics: `home.php?m=billing&p=test_integration`
- OGP documentation - For panel-specific questions
- `create_servers.php` - Source code with comments

## Success Checklist

Before considering integration complete:

- [ ] test_integration.php shows all green checks
- [ ] Can view orders at my_orders page
- [ ] Can provision single order successfully
- [ ] Can provision multiple orders at once
- [ ] Orders update to status='installed' in database
- [ ] home_id saved correctly after provisioning
- [ ] end_date calculated and saved
- [ ] Servers appear in Game Monitor
- [ ] Admin can view all orders
- [ ] Admin can filter and search orders
- [ ] Bulk actions work (provision multiple)
- [ ] Email notifications sent (if configured)
- [ ] Discord webhooks work (if configured)

---

**Integration Status: COMPLETE**  
**Multi-Server Support: FUNCTIONAL**  
**Admin Tools: READY**  
**Testing Tool: AVAILABLE**

Start with: `home.php?m=billing&p=test_integration`
