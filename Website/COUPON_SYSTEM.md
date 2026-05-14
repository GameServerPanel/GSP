# Coupon System Documentation

## Overview

The billing module now includes a comprehensive coupon system that allows administrators to create discount codes that customers can apply to their orders. The system supports:

- **Percentage-based discounts** (e.g., 10%, 25%, 50% off)
- **One-time or permanent discounts** (one-time applies to first invoice only, permanent applies to all renewals)
- **Game-specific filtering** (apply coupons to all games or specific games only)
- **Usage limits** (optional maximum number of uses per coupon)
- **Expiration dates** (optional expiry date for time-limited promotions)
- **Automatic usage tracking** (system tracks how many times each coupon has been used)

## Database Schema

### Table: `ogp_billing_coupons`

The main coupon table stores all coupon definitions:

```sql
CREATE TABLE `ogp_billing_coupons` (
    `coupon_id` INT(11) NOT NULL AUTO_INCREMENT,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `name` VARCHAR(255) NOT NULL DEFAULT '',
    `description` TEXT,
    `discount_percent` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    `usage_type` ENUM('one_time', 'permanent') NOT NULL DEFAULT 'one_time',
    `game_filter_type` ENUM('all_games', 'specific_games') NOT NULL DEFAULT 'all_games',
    `game_filter_list` TEXT COMMENT 'JSON array of game keys',
    `max_uses` INT(11) DEFAULT NULL COMMENT 'NULL for unlimited',
    `current_uses` INT(11) NOT NULL DEFAULT 0,
    `expires` DATETIME DEFAULT NULL,
    `created_date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT(11) DEFAULT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`coupon_id`),
    UNIQUE KEY `idx_code` (`code`)
);
```

### Updated Tables

#### `ogp_billing_invoices`
Added columns:
- `coupon_id` INT(11) - Links to the coupon used
- `discount_amount` DECIMAL(10,2) - Actual discount amount applied

#### `ogp_billing_orders`
Added columns:
- `coupon_id` INT(11) - Links to the coupon used (for permanent discounts)
- `discount_amount` DECIMAL(10,2) - Discount amount for renewals

## Installation

1. **Run the SQL migration:**
   ```bash
   mysql -u [username] -p [database_name] < modules/billing/create_coupons_table.sql
   ```

2. **Verify installation:**
   - Check that the `ogp_billing_coupons` table exists
   - Verify that `coupon_id` and `discount_amount` columns were added to both `ogp_billing_invoices` and `ogp_billing_orders`

## Admin Interface

### Accessing Coupon Management

1. Log in as an administrator
2. Navigate to `/modules/billing/admin.php`
3. Click on "Manage Coupons" button
4. Or go directly to `/modules/billing/admin_coupons.php`

### Creating a New Coupon

1. On the Manage Coupons page, scroll to "Add New Coupon" section
2. Fill in the required fields:
   - **Coupon Code**: Unique alphanumeric code (e.g., "SUMMER2025", "WELCOME10")
   - **Display Name**: User-friendly name shown in admin interface
   - **Description**: Internal notes about the coupon
   - **Discount Percentage**: Number between 0-100 (e.g., 25 for 25% off)
   - **Usage Type**:
     - **One Time**: Discount applies only to the first invoice
     - **Permanent**: Discount applies to initial order AND all future renewals
   - **Apply To**:
     - **All Games**: Works for any game server
     - **Specific Games**: Works only for selected games
   - **Maximum Uses**: Optional limit on total uses (blank = unlimited)
   - **Expiration Date**: Optional expiry date (blank = never expires)

3. Click "Add Coupon" to save

### Example Coupons

#### Welcome Discount (One-Time, All Games)
```
Code: WELCOME10
Name: Welcome 10% Off
Discount: 10%
Usage Type: One Time
Apply To: All Games
Max Uses: (unlimited)
Expires: (none)
```

#### Arma Series Promotion (Permanent, Specific Games)
```
Code: ARMA25
Name: Arma Series 25% Off
Discount: 25%
Usage Type: Permanent
Apply To: Specific Games
  - arma2_win32
  - arma2oa_win32
  - arma3_linux32
  - arma3_linux64
  - arma3_win64
  - arma-reforger_linux64
  - arma-reforger_win64
Max Uses: 100
Expires: 2025-12-31
```

### Editing Coupons

1. On the Manage Coupons page, find the coupon in the list
2. Click the "Edit" button
3. Modify any fields (except code uniqueness is enforced)
4. Click "Save Changes"

### Deactivating Coupons

1. Click "Edit" on the coupon
2. Uncheck the "Active" checkbox
3. Click "Save Changes"

Note: Deactivating prevents new uses but doesn't affect existing orders.

### Deleting Coupons

1. Find the coupon in the list
2. Click "Delete" button
3. Confirm the deletion

Warning: This permanently removes the coupon. Orders that used it will retain the discount but lose the coupon reference.

## Customer Usage

### Applying a Coupon

1. Customer adds items to cart at `/modules/billing/cart.php`
2. In the coupon section, enter coupon code in the input field
3. Click "Apply Coupon"
4. If valid, a success message appears showing:
   - Coupon code
   - Discount percentage
   - Whether it's one-time or permanent
5. Cart totals update automatically with discounted prices
6. Proceed to checkout with PayPal as normal

### Coupon Validation

The system validates:
- ✅ Code exists and is active
- ✅ Coupon hasn't expired
- ✅ Usage limit hasn't been reached
- ✅ Game matches filter (if game-specific)

Error messages shown if:
- ❌ Code is invalid or expired
- ❌ Usage limit reached
- ❌ Coupon doesn't apply to games in cart

### Removing a Coupon

1. On cart page, click "Remove" button next to active coupon
2. Cart prices revert to original amounts

## Coupon Behavior

### One-Time Coupons

- Applied to the initial invoice only
- When order is renewed, renewal invoice uses original price
- Coupon is cleared from session after first payment
- Example: "WELCOME10" gives 10% off first month only

### Permanent Coupons

- Applied to initial invoice AND stored in order record
- When order is renewed, the discount is automatically applied to renewal invoices
- Coupon stays associated with the order forever
- Example: "VIP50" gives 50% off forever for that specific server

### Game Filtering

#### All Games
- Coupon applies to any game server in the cart
- All cart items receive the discount

#### Specific Games
- Coupon checks each cart item's `home_name` field
- Only matching games receive the discount
- Uses partial string matching (e.g., "arma3" matches "arma3_linux64")
- Non-matching games show original price

Example:
```
Cart contains:
1. Arma 3 Server → ARMA25 coupon applies (25% off)
2. Minecraft Server → ARMA25 doesn't apply (full price)
3. Arma Reforger → ARMA25 applies (25% off)

Total discount = 25% off Arma servers only
```

## Technical Implementation

### Session Storage

Coupons are stored in `$_SESSION['applied_coupon']` when applied:
```php
$_SESSION['applied_coupon'] = [
    'coupon_id' => 1,
    'code' => 'ARMA25',
    'discount_percent' => 25.00,
    'usage_type' => 'permanent',
    'game_filter_type' => 'specific_games',
    'game_filter_list' => '["arma3_linux64","arma2_win32"]',
    // ... other fields
];
```

### Cart Calculation

In `cart.php`, the `couponAppliesTo()` function checks if a coupon applies to a specific game:

```php
function couponAppliesTo($coupon, $game_name) {
    if (!$coupon || $coupon['game_filter_type'] === 'all_games') {
        return true;
    }
    
    if ($coupon['game_filter_type'] === 'specific_games') {
        $allowed_games = json_decode($coupon['game_filter_list'], true);
        foreach ($allowed_games as $allowed_game) {
            if (stripos($game_name, $allowed_game) !== false) {
                return true;
            }
        }
    }
    
    return false;
}
```

Discount calculation:
```php
$rowtotal = $row['amount'] * $row['qty'] * $row['max_players'];

if ($applied_coupon && couponAppliesTo($applied_coupon, $row['home_name'])) {
    $discountPercent = floatval($applied_coupon['discount_percent']);
    $itemDiscount = ($rowtotal * $discountPercent) / 100;
    $rowtotal = $rowtotal - $itemDiscount;
}
```

### Payment Processing

In `api/capture_order.php`, when PayPal payment completes:

1. Coupon info is retrieved from session
2. Invoices are updated with `coupon_id`
3. Coupon usage count is incremented
4. For one-time coupons, cleared from session
5. For permanent coupons, stored in order record

```php
// Update invoice with coupon
UPDATE ogp_billing_invoices 
SET status='paid', coupon_id=?, discount_amount=?
WHERE user_id=? AND status='due'

// Increment usage count
UPDATE ogp_billing_coupons 
SET current_uses = current_uses + 1 
WHERE coupon_id = ?

// For permanent coupons, store in order
INSERT INTO ogp_billing_orders (
    ..., coupon_id, discount_amount
) VALUES (
    ..., ?, ?
)
```

## Display

### Cart Page
- Shows applied coupon with code and percentage
- Displays success/error messages
- Updates prices in real-time

### My Servers Page
- Shows original price (strikethrough)
- Shows discounted price (bold)
- Shows coupon code and percentage (green text)

### Admin Invoices Page
- Same display as My Servers
- Visible to administrators for all orders

## Troubleshooting

### Coupon not applying
- Check if code is typed correctly (case-sensitive)
- Verify coupon is active in admin panel
- Check expiration date hasn't passed
- Verify usage limit hasn't been reached
- For game-specific coupons, ensure game matches filter

### Discount not showing after payment
- Check `discount_amount` column exists in both tables
- Verify coupon_id was saved to invoice/order
- Clear browser cache and refresh page

### Permanent coupon not applying to renewals
- Verify `usage_type` is set to "permanent"
- Check order record has `coupon_id` populated
- Ensure renewal invoice creation copies coupon from order

## Security Considerations

1. **Code uniqueness**: System enforces unique coupon codes
2. **Usage tracking**: Prevents abuse by tracking total uses
3. **Expiration**: Automatic validation prevents expired coupon use
4. **Admin-only creation**: Only admins can create/edit coupons
5. **SQL injection protection**: All inputs are sanitized with `mysqli_real_escape_string()`
6. **CSRF protection**: Admin forms include CSRF tokens

## Future Enhancements

Potential features for future development:
- Minimum purchase amount requirements
- First-time customer restrictions
- User-specific coupons (assign to individual users)
- Combination rules (allow/prevent stacking)
- Auto-generated unique codes for campaigns
- Email notification when coupon is used
- Analytics dashboard for coupon performance
- Referral system integration

## Support

For issues or questions:
1. Check the troubleshooting section above
2. Review error logs in `/modules/billing/logs/`
3. Verify database schema matches documentation
4. Contact system administrator

---

**Last Updated**: 2025-10-29
**Version**: 1.0
**Module**: Billing/Coupons
