# GSP Billing Module - Panel Integration Complete

## Overview
The GSP billing module has been successfully integrated with the panel-side provisioning system. The standalone website handles public orders and payments, while the panel manages server provisioning.

## Changes Made

### 1. Navigation Configuration (`navigation.xml`)
**File:** `modules/billing/navigation.xml`

Created XML configuration to expose billing pages in the panel:
- `provision_servers` → `create_servers.php` (admin,user)
- `my_orders` → `my_orders_panel.php` (admin,user)  
- `admin_orders` → `admin_orders.php` (admin only)

**Access URLs:**
- `home.php?m=billing&p=provision_servers` - Provision paid servers
- `home.php?m=billing&p=my_orders` - View user's orders
- `home.php?m=billing&p=admin_orders` - Admin order management

### 2. User Order Management (`my_orders_panel.php`)
**File:** `modules/billing/my_orders_panel.php`

User-facing page displaying paid but unprovisioned orders:
- Shows order details (service name, players, price, duration)
- "Provision Server" button for individual orders
- "Provision All My Servers" button for bulk provisioning
- Admin view includes username column
- Filters to show only `status='paid'` orders

### 3. Server Provisioning Updates (`create_servers.php`)
**File:** `modules/billing/create_servers.php`

Enhanced provisioning script to handle multiple workflows:

**NEW: provision_all Support**
```php
if (isset($_POST['provision_all'])) {
    // Query all paid orders for user
    // Process each in foreach loop
}
```

**NEW: provision_single Support**
```php
if (isset($_POST['provision_single']) && $_POST['order_id']) {
    // Query specific order_id
    // Process single order
}
```

**Improvements:**
- Added provisioning counters (`$provisioned_count`, `$failed_count`)
- Success message shows count of provisioned servers
- Auto-redirect to game monitor after 3 seconds
- Better error handling for missing order_id
- Clear feedback messages for all scenarios

### 4. Admin Order Management (`admin_orders.php`)
**File:** `modules/billing/admin_orders.php`

Comprehensive admin interface for managing all orders:

**Features:**
- View all orders across all users
- Filter by status (in-cart, paid, installed, invoiced, suspended, deleted)
- Search by order ID, username, email, server name
- Bulk actions:
  - Provision multiple servers at once
  - Activate (set to paid)
  - Suspend orders
  - Delete orders
- Quick links to provision or view active servers
- Order statistics summary (count and total value by status)

**Display Columns:**
- Order ID, Username, Email
- Server Name, Game Service, Players
- Price, Duration, Status
- Order Date, End Date, Home ID
- Action buttons

## Multi-Server Cart Support

The system already supports multiple servers in a single cart:

**How it works:**
1. Customer adds multiple services to cart on standalone website
2. Payment processed, all items marked `status='paid'`
3. User logs into panel → navigates to "My Orders"
4. Clicks "Provision All My Servers" button
5. `create_servers.php` queries all `WHERE status='paid' AND user_id=X`
6. `foreach ($orders as $order)` loop processes each:
   - Creates game_home
   - Assigns IP:Port
   - Installs files via Steam/rsync/manual
   - Calculates end_date based on duration
   - Updates `status='installed'`, saves `home_id`, sets `end_date`
   - Sends email and Discord notifications
7. All servers appear in Game Monitor as active

**Database Flow:**
```
billing_orders table:
status='in-cart' → (payment) → status='paid' → (provision) → status='installed'
                                               ↓
                                     (renewal invoice) → status='invoiced'
                                               ↓
                                    (non-payment) → status='suspended'
```

## Testing Workflow

### User Perspective:
1. Order servers on standalone website at `example.com/modules/billing/`
2. Complete payment (PayPal, Stripe, etc.)
3. Orders marked `status='paid'` in database
4. Log into panel at `panel.example.com/`
5. Navigate to `home.php?m=billing&p=my_orders`
6. Click "Provision Server" for individual order OR "Provision All" for bulk
7. Wait for provisioning (creates server, installs files)
8. Redirected to Game Monitor showing active servers

### Admin Perspective:
1. Log into panel with admin account
2. Navigate to `home.php?m=billing&p=admin_orders`
3. View all orders across all users
4. Filter by status or search for specific orders
5. Select multiple orders with checkboxes
6. Choose bulk action (provision, suspend, activate, delete)
7. Click individual "Provision" buttons for specific orders
8. Monitor order statistics at bottom of page

## Order Status Lifecycle

```
in-cart     → User shopping, not paid yet
paid        → Payment received, awaiting provisioning
installed   → Server created and active
invoiced    → Renewal invoice generated
suspended   → Server suspended (non-payment, violation)
deleted     → Order soft-deleted
```

## Database Schema Reference

### billing_orders Table (Key Fields)
- `order_id` - Primary key
- `user_id` - Links to ogp_users.user_id
- `service_id` - Links to billing_services.service_id
- `home_id` - Links to game_homes.home_id (after provisioning)
- `status` - Current order status (in-cart, paid, installed, etc.)
- `home_name` - Display name for server
- `max_players` - Player slot limit
- `price` - Order amount paid
- `qty` - Duration quantity (e.g., 3 for "3 months")
- `invoice_duration` - Duration unit (day, month, year)
- `order_date` - When order was created
- `end_date` - When service expires (calculated after provisioning)
- `extended` - Boolean flag for renewals vs new orders
- `ip` - Contains remote_server_id (target node)

### billing_services Table
- `service_id` - Primary key
- `service_name` - Display name (e.g., "Minecraft 25 Players")
- `home_cfg_id` - Links to game configs
- `mod_cfg_id` - Specific mod/version
- `install_method` - steam, rsync, manual
- `manual_url` - Direct download URL for manual installs

## Key Files Overview

```
modules/billing/
├── navigation.xml               [NEW] - Panel page routing
├── my_orders_panel.php          [NEW] - User order list
├── admin_orders.php             [NEW] - Admin order management
├── create_servers.php           [UPDATED] - Server provisioning
├── module.php                   [EXISTING] - Module metadata & schema
├── index.php                    [STANDALONE SITE] - Public storefront
├── cart.php                     [STANDALONE SITE] - Shopping cart
├── order.php                    [STANDALONE SITE] - Order checkout
├── payment_success.php          [STANDALONE SITE] - Payment return
└── ...                          [STANDALONE SITE] - Other public pages
```

## Access Control

**User (admin,user):**
- Can provision their own paid orders
- View only their own orders
- Cannot manage other users' orders

**Admin (admin):**
- Full access to all pages
- Can provision any user's orders
- View and manage all orders across all users
- Bulk actions on multiple orders
- Access to order statistics

## Next Steps

1. **Test provisioning workflow:**
   - Create test order with `status='paid'` in database
   - Log in as that user
   - Navigate to My Orders page
   - Click "Provision Server" and verify server creation

2. **Test multi-server scenario:**
   - Create multiple orders for same user with `status='paid'`
   - Use "Provision All" button
   - Verify all servers created and statuses updated

3. **Admin testing:**
   - Log in as admin
   - Access admin_orders page
   - Test filters and search
   - Test bulk actions
   - Verify order statistics display

4. **Optional: Add menu items**
   Edit `modules/billing/module.php` to add navigation menu:
   ```php
   $module_menus = array(
       array('subpage' => 'my_orders', 'name'=>'My Orders', 'group'=>'user'),
       array('subpage' => 'admin_orders', 'name'=>'Manage Orders', 'group'=>'admin')
   );
   ```

## Troubleshooting

**Issue: "No paid orders found"**
- Check database: `SELECT * FROM billing_orders WHERE status='paid'`
- Verify user_id matches logged-in user
- Ensure order_id is correct if using provision_servers directly

**Issue: Provisioning fails**
- Check create_servers.php for errors
- Verify remote_server_id (stored in ip field) is valid
- Ensure install_method is configured correctly (steam, rsync, manual)
- Check manual_url is accessible if using manual install

**Issue: Page not accessible**
- Verify navigation.xml is in `modules/billing/` directory
- Check XML syntax is valid
- Ensure file permissions allow reading
- Verify includes/navig.php is loading the module correctly

**Issue: "Access Denied"**
- Check user session: `$_SESSION['users_group']` must match page access
- admin_orders requires `$_SESSION['users_group'] = 'admin'`
- Regular pages need 'admin' or 'user' group

## Architecture Notes

**Separation of Concerns:**
- Standalone website: Public ordering, payment processing, cart management
- Panel: Server provisioning, lifecycle management, admin controls
- Database: Shared MySQL tables for order/service data

**Module Loading Pattern:**
1. User requests `home.php?m=billing&p=my_orders`
2. `includes/navig.php` validates module exists
3. Loads `modules/billing/navigation.xml`
4. Finds page with `key="my_orders"`
5. Checks user access against `access="admin,user"`
6. Includes `modules/billing/my_orders_panel.php`
7. Calls `exec_ogp_module()` function
8. Renders output in panel layout

**Multi-Server Processing:**
The `foreach ($orders as $order)` loop in create_servers.php handles multiple servers naturally:
- Query returns all paid orders for user
- Loop processes each order sequentially
- Each iteration creates one game_home
- Each iteration updates one order to 'installed'
- No special cart logic needed - works automatically

## Success Criteria Checklist

✅ navigation.xml created with 3 page definitions  
✅ my_orders_panel.php displays user's paid orders  
✅ Provision buttons link to create_servers.php with order_id  
✅ create_servers.php handles provision_all and provision_single  
✅ Multi-server support via foreach loop (already existed)  
✅ admin_orders.php provides comprehensive order management  
✅ Bulk actions for admin (provision, suspend, activate, delete)  
✅ Status updates: paid → installed with end_date calculation  
✅ home_id saved back to billing_orders after provisioning  
✅ Success messages and auto-redirect after provisioning  
✅ Access control enforced via navigation.xml attributes  

## Conclusion

The GSP billing module is now fully integrated with the panel provisioning system. Users can order servers on the standalone website, then log into the panel to provision them. Admins have comprehensive tools to manage all orders. The multi-server cart functionality works automatically via the existing foreach loop.

**Panel URLs:**
- User Orders: `home.php?m=billing&p=my_orders`
- Admin Orders: `home.php?m=billing&p=admin_orders`
- Provision: `home.php?m=billing&p=provision_servers&order_id=X`
