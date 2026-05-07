<?php
/**
 * Shopping Cart - Rebuilt from scratch for reliability
 * Displays unpaid invoices and provides PayPal checkout
 * Standalone billing module - uses only standard PHP mysqli
 */

// Start session with website session name
if (session_status() === PHP_SESSION_NONE) {
    session_name("opengamepanel_web");
    session_start();
}

// Load configuration
require_once(__DIR__ . '/bootstrap.php');

function billing_cart_money_to_cents(float $amount): int
{
    return (int) round($amount * 100);
}

function billing_cart_cents_to_money(int $cents): float
{
    return $cents / 100;
}

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */
/** @var string $SITE_BASE_URL Site base URL */
/** @var string $SITE_DATA_DIR Data directory path */

// Check if user is logged in
$user_id = 0;
if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
    $user_id = intval($_SESSION['website_user_id']);
} elseif (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
}

// Redirect to login if not authenticated
if ($user_id <= 0) {
    $return_to = urlencode($_SERVER['REQUEST_URI'] ?? '/cart.php');
    header('Location: /login.php?return_to=' . $return_to);
    exit;
}

// Connect to database (non-fatal)
$db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
$db_error = '';

// Initialize variables
$invoices = [];
$total_amount = 0.00;
$total_amount_cents = 0;
$discount_amount = 0.00;
$discount_amount_cents = 0;
$coupon_discount_percent = 0;
$applied_coupon = null;
$error_message = '';
$success_message = '';

if (!$db) {
    // record error for UI/debugging but do not die here
    $db_error = 'Database connection failed: ' . mysqli_connect_error();
    $cart_empty = true;
} else {
    // Fetch unpaid invoices for this user. Select only invoice fields to avoid referencing
    // columns that may not exist in all deployments (some schemas differ).
    $query = "SELECT i.* 
              FROM {$table_prefix}billing_invoices i
              WHERE i.user_id = " . intval($user_id) . "
                AND (i.status = 'due' OR i.status = '')
                AND (i.payment_status IS NULL OR i.payment_status NOT IN ('paid','cancelled','refunded'))
              ORDER BY i.invoice_date ASC";

    $result = mysqli_query($db, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $invoices[] = $row;
            $lineAmount = (float)($row['total_due'] ?? $row['amount'] ?? 0);
            $total_amount_cents += billing_cart_money_to_cents($lineAmount);
        }
        mysqli_free_result($result);
    }

    $cart_empty = (count((array)$invoices) === 0);
    $total_amount = billing_cart_cents_to_money($total_amount_cents);
}

// Handle coupon application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $coupon_code = trim($_POST['coupon_code'] ?? '');
    
    if (empty($coupon_code)) {
        $error_message = 'Please enter a coupon code.';
    } else {
        // Validate coupon
        if (!$db) {
            $error_message = 'Coupon system unavailable: database connection failed.';
        } else {
            $safe_code = mysqli_real_escape_string($db, $coupon_code);
            $coupon_query = "SELECT * FROM {$table_prefix}billing_coupons 
                            WHERE code = '$safe_code' AND is_active = 1";
            $coupon_result = mysqli_query($db, $coupon_query);
        
        if ($coupon_result && mysqli_num_rows($coupon_result) === 1) {
            $coupon = mysqli_fetch_assoc($coupon_result);
            
            // Check if expired
            $expired = false;
            if (!empty($coupon['expires'])) {
                $expires_time = strtotime($coupon['expires']);
                if ($expires_time && $expires_time < time()) {
                    $expired = true;
                }
            }
            
            // Check usage limit
            $max_uses_reached = false;
            if (!empty($coupon['max_uses'])) {
                if (intval($coupon['current_uses']) >= intval($coupon['max_uses'])) {
                    $max_uses_reached = true;
                }
            }
            
            if ($expired) {
                $error_message = 'This coupon has expired.';
            } elseif ($max_uses_reached) {
                $error_message = 'This coupon has reached its maximum usage limit.';
            } else {
                // Check game filter
                $game_valid = true;
                if ($coupon['game_filter_type'] === 'specific_games' && !empty($coupon['game_filter_list'])) {
                    $allowed_games = json_decode($coupon['game_filter_list'], true);
                    if (is_array($allowed_games) && count((array)$allowed_games) > 0) {
                        $has_valid_game = false;
                        foreach ((array)$invoices as $inv) {
                            $inv_game_key = isset($inv['game_key']) ? $inv['game_key'] : null;
                            if ($inv_game_key !== null && in_array($inv_game_key, $allowed_games)) {
                                $has_valid_game = true;
                                break;
                            }
                        }
                        if (!$has_valid_game) {
                            $game_valid = false;
                        }
                    }
                }
                
                if (!$game_valid) {
                    $error_message = 'This coupon is not valid for the items in your cart.';
                } else {
                    // Apply coupon
                    $applied_coupon = $coupon;
                    $coupon_discount_percent = floatval($coupon['discount_percent']);
                    $_SESSION['cart_coupon_code'] = $coupon_code;
                    $_SESSION['cart_coupon_id'] = $coupon['coupon_id'];
                    $success_message = 'Coupon "' . htmlspecialchars($coupon['name']) . '" applied! You save ' . $coupon_discount_percent . '%';
                }
            }
            mysqli_free_result($coupon_result);
        } else {
            $error_message = 'Invalid coupon code.';
        }
        }
    }
}

// Handle coupon removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_coupon'])) {
    unset($_SESSION['cart_coupon_code']);
    unset($_SESSION['cart_coupon_id']);
    $applied_coupon = null;
    $coupon_discount_percent = 0;
}

// Re-validate coupon from session if present
if ($db && empty($applied_coupon) && isset($_SESSION['cart_coupon_code'])) {
    $coupon_code = $_SESSION['cart_coupon_code'];
    $safe_code = mysqli_real_escape_string($db, $coupon_code);
    $coupon_query = "SELECT * FROM {$table_prefix}billing_coupons 
                    WHERE code = '$safe_code' AND is_active = 1";
    $coupon_result = mysqli_query($db, $coupon_query);
    
    if ($coupon_result && mysqli_num_rows($coupon_result) === 1) {
        $applied_coupon = mysqli_fetch_assoc($coupon_result);
        $coupon_discount_percent = floatval($applied_coupon['discount_percent']);
        mysqli_free_result($coupon_result);
    } else {
        // Coupon no longer valid, clear from session
        unset($_SESSION['cart_coupon_code']);
        unset($_SESSION['cart_coupon_id']);
    }
}

// AJAX remove invoice action (hard delete) - returns JSON when remove_invoice_ajax is set
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_invoice_ajax']) && isset($_POST['invoice_id'])) {
    header('Content-Type: application/json');
    $remove_id = intval($_POST['invoice_id']);
    if ($remove_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid invoice id.']);
        exit;
    }

    if (!$db) {
        echo json_encode(['success' => false, 'error' => 'Database unavailable.']);
        exit;
    }

    // Verify ownership and that invoice is still unpaid/due
    $check_q = "SELECT invoice_id FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND (status = 'due' OR status = '') AND (payment_status IS NULL OR payment_status NOT IN ('paid','cancelled','refunded')) LIMIT 1";
    $check_r = mysqli_query($db, $check_q);
    if (!($check_r && mysqli_num_rows($check_r) === 1)) {
        echo json_encode(['success' => false, 'error' => 'Invoice not found or cannot be removed.']);
        exit;
    }

    // Hard-delete the invoice row
    $del_q = "DELETE FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND (status = 'due' OR status = '') AND (payment_status IS NULL OR payment_status NOT IN ('paid','cancelled','refunded')) LIMIT 1";
    $ok = mysqli_query($db, $del_q);
    if ($ok && mysqli_affected_rows($db) > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete invoice.']);
    }
    exit;
}

// Handle non-AJAX remove invoice action (hard delete + redirect)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_invoice']) && isset($_POST['invoice_id'])) {
    $remove_id = intval($_POST['invoice_id']);
    if ($remove_id <= 0) {
        $error_message = 'Invalid invoice id.';
    } else {
        if (!$db) {
            $error_message = 'Unable to remove item: database unavailable.';
        } else {
            // Verify ownership and that invoice is still unpaid/due
            $check_q = "SELECT invoice_id FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND (status = 'due' OR status = '') AND (payment_status IS NULL OR payment_status NOT IN ('paid','cancelled','refunded')) LIMIT 1";
            $check_r = mysqli_query($db, $check_q);
            if ($check_r && mysqli_num_rows($check_r) === 1) {
                // Hard-delete to remove from cart
                $del_q = "DELETE FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND (status = 'due' OR status = '') AND (payment_status IS NULL OR payment_status NOT IN ('paid','cancelled','refunded')) LIMIT 1";
                if (mysqli_query($db, $del_q)) {
                    // Reload to avoid form re-submission and refresh invoice list
                    header('Location: /cart.php');
                    exit;
                } else {
                    $error_message = 'Failed to remove item from cart.';
                }
            } else {
                $error_message = 'Invoice not found or cannot be removed.';
            }
        }
    }
}

// Calculate discount
if ($applied_coupon && $coupon_discount_percent > 0) {
    $discount_amount_cents = (int) round($total_amount_cents * ($coupon_discount_percent / 100));
    $discount_amount_cents = min($discount_amount_cents, $total_amount_cents);
}

$discount_amount = billing_cart_cents_to_money($discount_amount_cents);
$final_amount_cents = max(0, $total_amount_cents - $discount_amount_cents);
$final_amount = billing_cart_cents_to_money($final_amount_cents);

// PayPal configuration (from config)
$client_id = function_exists('gsp_paypal_get_client_id') ? gsp_paypal_get_client_id() : ($paypal_client_id ?? '');
$sandbox   = function_exists('gsp_paypal_is_sandbox')    ? gsp_paypal_is_sandbox()    : ($paypal_sandbox ?? true);

// Prepare PayPal items
$paypal_items = [];
$paypal_invoice_ids = [];
foreach ((array)$invoices as $inv) {
    $game_display = !empty($inv['game_name']) ? $inv['game_name'] : 'Game Server';
    $qty = max(1, intval($inv['qty']));
    $paypal_invoice_ids[] = intval($inv['invoice_id']);
    $lineAmountCents = billing_cart_money_to_cents((float)($inv['total_due'] ?? $inv['amount'] ?? 0));
    $lineAmount = billing_cart_cents_to_money($lineAmountCents);
    $paypal_items[] = [
        'name' => $inv['home_name'] . ' (' . $game_display . ')',
        'description' => $inv['description'] ?? '',
        'quantity' => $qty,
        'unit_amount' => [
            'currency_code' => 'USD',
            'value' => number_format($lineAmount / $qty, 2, '.', '')
        ]
    ];
}
$paypal_custom_id = 'cart:' . implode(',', $paypal_invoice_ids);

// Get site base URL
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$siteBase = $protocol . $host;

// (Do not close the shared DB connection here; menu and other includes may use it.)
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Game Server Panel</title>
    <link rel="stylesheet" href="css/header.css">
    <style>
        /* Do not override site-wide font or header/menu styles here.
           Keep body reset minimal so includes/menu.php can control header styling. */
        body {
            margin: 0;
            padding: 0;
        }
        .cart-container {
            max-width: 900px;
            margin: 24px auto;
            background: white;
            padding: 24px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            width: min(100%, calc(100% - 24px));
            box-sizing: border-box;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            font-size: 2em;
        }
        .alert {
            padding: 12px 20px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .cart-empty {
            text-align: center;
            padding: 60px 20px;
        }
        .cart-empty h2 {
            color: #666;
            margin-bottom: 15px;
        }
        .cart-empty p {
            color: #999;
            margin-bottom: 30px;
        }
        .cart-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            table-layout: fixed;
        }
        .cart-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
            color: #495057;
        }
        .cart-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .cart-table tbody tr:hover {
            background: #f8f9fa;
        }
        .game-name {
            font-weight: 600;
            color: #007bff;
            font-size: 1.05em;
        }
        .server-name {
            color: #666;
            font-size: 0.9em;
            margin-top: 4px;
        }
        .description {
            color: #999;
            font-size: 0.85em;
            margin-top: 4px;
        }
        .price {
            font-weight: 600;
            color: #28a745;
            font-size: 1.1em;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
            background: #fff3cd;
            color: #856404;
        }
        .coupon-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .coupon-section h3 {
            margin-top: 0;
            color: #333;
        }
        .coupon-form {
            display: flex;
            gap: 10px;
            align-items: flex-end;
            flex-wrap: wrap;
        }
        .coupon-form > div {
            flex: 1;
        }
        .coupon-form label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #495057;
        }
        .coupon-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1em;
        }
        .coupon-applied {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #d4edda;
            padding: 15px;
            border-radius: 4px;
            border: 1px solid #c3e6cb;
        }
        .coupon-applied-text {
            color: #155724;
        }
        .cart-total {
            text-align: right;
            padding: 20px 0;
            border-top: 2px solid #dee2e6;
            margin-bottom: 30px;
        }
        .cart-total-row {
            margin-bottom: 10px;
        }
        .cart-total-label {
            font-size: 1.2em;
            font-weight: 600;
            margin-right: 20px;
            color: #495057;
        }
        .cart-total-amount {
            font-size: 1.5em;
            font-weight: 700;
            color: #28a745;
        }
        .subtotal-amount {
            font-size: 1.2em;
            color: #666;
        }
        .discount-amount {
            font-size: 1.2em;
            font-weight: 600;
            color: #28a745;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border: none;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            font-size: 1em;
        }
        .btn:hover {
            background: #0056b3;
        }
        .btn-secondary {
            background: #6c757d;
        }
        .btn-secondary:hover {
            background: #545b62;
        }
        .btn-small {
            padding: 8px 16px;
            font-size: 0.9em;
        }
        .checkout-section {
            padding: 20px 0;
        }
        .checkout-section h3 {
            color: #333;
            margin-bottom: 10px;
        }
        .checkout-section p {
            color: #666;
            margin-bottom: 20px;
        }
        #paypal-button-container {
            max-width: 400px;
            margin: 20px 0;
        }
        .status-message {
            text-align: center;
            padding: 20px;
            color: #666;
            display: none;
        }
        .action-buttons {
            margin-top: 30px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .cart-table-wrap {
            width: 100%;
            max-width: 100%;
            overflow-x: auto;
        }
        @media (max-width: 768px) {
            .cart-container {
                width: min(100%, calc(100% - 12px));
                padding: 14px;
                margin: 12px auto;
            }
            h1 {
                font-size: 1.5rem;
                margin-bottom: 18px;
            }
            .cart-table thead {
                display: none;
            }
            .cart-table,
            .cart-table tbody,
            .cart-table tr,
            .cart-table td {
                display: block;
                width: 100%;
            }
            .cart-table tr {
                border: 1px solid #dee2e6;
                border-radius: 8px;
                margin-bottom: 12px;
                padding: 6px 8px;
                background: #fff;
            }
            .cart-table td {
                border: 0;
                padding: 6px 4px;
                text-align: left !important;
            }
            .cart-table td[data-label]::before {
                content: attr(data-label) ": ";
                font-weight: 600;
                color: #495057;
            }
            .coupon-form {
                flex-direction: column;
                align-items: stretch;
            }
            .coupon-form button {
                width: 100%;
            }
            .coupon-applied {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            .cart-total {
                text-align: left;
            }
            .cart-total-row {
                display: flex;
                justify-content: space-between;
                gap: 10px;
            }
            .cart-total-label,
            .cart-total-amount,
            .subtotal-amount,
            .discount-amount {
                font-size: 1rem;
                margin-right: 0;
            }
            .btn {
                width: 100%;
                text-align: center;
            }
            .action-buttons {
                margin-top: 16px;
            }
        }
    </style>
    <?php // Font Awesome for small icon buttons ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" href="images/logo-sm.png" type="image/png">
    <link rel="apple-touch-icon" href="images/logo-sm.png">
    <?php if (!$cart_empty && !empty($client_id)): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($client_id, ENT_QUOTES, 'UTF-8'); ?>&currency=USD&intent=capture<?php echo $sandbox ? '&debug=false' : ''; ?>"></script>
    <?php endif; ?>
</head>
<body>
    <?php include(__DIR__ . '/includes/top.php'); ?>
    <?php include(__DIR__ . '/includes/menu.php'); ?>
    
    <div class="cart-container">
        <?php if (!empty($db_error)): ?>
            <div class="alert-error" style="margin-bottom:15px;">
                <strong>Database error:</strong> <?php echo htmlspecialchars($db_error); ?>
            </div>
        <?php endif; ?>
        <h1>🛒 Shopping Cart</h1>
        
        <?php if ($error_message): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <?php if ($cart_empty): ?>
            <div class="cart-empty">
                <h2>Your cart is empty</h2>
                <p>Browse our game servers and add them to your cart to get started!</p>
                <a href="/serverlist.php" class="btn">Browse Servers</a>
            </div>
        <?php else: ?>
            <div class="cart-table-wrap">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th>Game Server</th>
                        <th>Duration</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th style="text-align: right;">Price</th>
                        <th style="text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ((array)$invoices as $inv): ?>
                    <tr>
                        <td data-label="Game Server">
                            <div class="game-name"><?php echo htmlspecialchars($inv['game_name'] ?? 'Game Server'); ?></div>
                            <div class="server-name"><?php echo htmlspecialchars($inv['home_name']); ?></div>
                            <?php if (!empty($inv['description'])): ?>
                            <div class="description"><?php echo htmlspecialchars($inv['description']); ?></div>
                            <?php endif; ?>
                        </td>
                                <td data-label="Duration"><?php echo htmlspecialchars((string)($inv['invoice_duration'] ?? 'month')); ?></td>
                                <td data-label="Quantity"><?php echo intval($inv['qty'] ?? 1); ?>x</td>
                                <td data-label="Status"><span class="status-badge"><?php echo htmlspecialchars(strtoupper((string)($inv['status'] ?? 'due'))); ?></span></td>
                                <td data-label="Price" style="text-align: right;">
                                    <span class="price">$<?php echo number_format(floatval($inv['total_due'] ?? $inv['amount'] ?? 0), 2); ?></span>
                                </td>
                                <td data-label="Action" style="text-align: right;">
                                    <button type="button" class="btn btn-secondary btn-small" title="Remove" onclick="removeInvoice(<?php echo intval($inv['invoice_id']); ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>

            <!-- Coupon Section -->
            <div class="coupon-section">
                <h3>Coupon Code</h3>
                
                <?php if (!$applied_coupon): ?>
                    <form method="POST" class="coupon-form">
                        <div>
                            <label>Enter Code:</label>
                            <input type="text" name="coupon_code" placeholder="Enter coupon code" required>
                        </div>
                        <button type="submit" name="apply_coupon" class="btn">Apply Coupon</button>
                    </form>
                <?php else: ?>
                    <div class="coupon-applied">
                        <div class="coupon-applied-text">
                            <strong>Coupon Applied:</strong> 
                            <?php echo htmlspecialchars($applied_coupon['name']); ?> 
                            (<?php echo htmlspecialchars($applied_coupon['discount_percent']); ?>% off)
                        </div>
                        <form method="POST" style="margin: 0;">
                            <button type="submit" name="remove_coupon" class="btn btn-secondary btn-small">Remove</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Cart Total -->
            <div class="cart-total">
                <?php if ($discount_amount > 0): ?>
                    <div class="cart-total-row">
                        <span class="cart-total-label">Subtotal:</span>
                        <span class="subtotal-amount">$<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div class="cart-total-row">
                        <span class="cart-total-label">Discount (<?php echo $coupon_discount_percent; ?>%):</span>
                        <span class="discount-amount">-$<?php echo number_format($discount_amount, 2); ?></span>
                    </div>
                <?php endif; ?>
                <div class="cart-total-row">
                    <span class="cart-total-label">Total:</span>
                    <span class="cart-total-amount">$<?php echo number_format($final_amount, 2); ?></span>
                </div>
            </div>

            <!-- Checkout Section -->
            <?php if ($final_amount_cents === 0): ?>
            <!-- Zero-dollar checkout: coupon covers the full amount, no PayPal needed -->
            <div class="checkout-section">
                <h3>🎉 Complete Your Free Order</h3>
                <p>Your coupon covers the full amount. Click below to confirm and automatically provision your server(s).</p>
                <div id="status-message" class="status-message"></div>
                <form method="POST" action="/checkout_free.php" onsubmit="document.getElementById('free-submit-btn').disabled=true; document.getElementById('status-message').style.display='block'; document.getElementById('status-message').textContent='Processing…';">
                    <input type="hidden" name="coupon_id"   value="<?php echo intval($_SESSION['cart_coupon_id'] ?? 0); ?>">
                    <input type="hidden" name="coupon_code" value="<?php echo htmlspecialchars($_SESSION['cart_coupon_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <button id="free-submit-btn" type="submit" class="btn" style="background:#28a745;">
                        ✓ Complete Free Order
                    </button>
                </form>
                <div class="action-buttons" style="margin-top:15px;">
                    <a href="/serverlist.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="/my_account.php" class="btn btn-secondary">My Account</a>
                </div>
            </div>
            <?php else: ?>
            <div class="checkout-section">
                <h3>Checkout with PayPal</h3>
                <?php if (empty($client_id)): ?>
                <div class="alert alert-error">
                    <strong>Checkout Unavailable:</strong> PayPal has not been configured for this site.
                    Please contact the site administrator or try again later.
                    <?php
                    // Admin hint: only show config link if the current user is an admin
                    $cart_user_id_check = intval($_SESSION['website_user_id'] ?? 0);
                    $cart_is_admin = false;
                    if ($cart_user_id_check > 0 && $db) {
                        $ar = mysqli_query($db, "SELECT users_role FROM {$table_prefix}users WHERE user_id = " . $cart_user_id_check . " LIMIT 1");
                        if ($ar && ($arow = mysqli_fetch_assoc($ar))) {
                            $cart_is_admin = strtolower($arow['users_role'] ?? '') === 'admin';
                        }
                    }
                    if ($cart_is_admin):
                    ?>
                    <br><small><em>Admin: configure PayPal credentials in <a href="/admin_config.php" style="color:inherit;text-decoration:underline;">Site Config</a>.</em></small>
                    <?php endif; ?>
                </div>
                <?php else: ?>
                <p>Click the button below to complete your purchase securely through PayPal.</p>
                <div id="paypal-button-container"></div>
                <div id="status-message" class="status-message"></div>
                <?php endif; ?>
                <div class="action-buttons">
                    <a href="/serverlist.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="/my_account.php" class="btn btn-secondary">My Account</a>
                </div>
            </div>
            <?php endif; ?>

            <script>
                function setStatus(msg) {
                    const statusDiv = document.getElementById('status-message');
                    if (statusDiv) {
                        statusDiv.textContent = msg;
                        statusDiv.style.display = 'block';
                    }
                }
            </script>

            <?php if ($final_amount_cents > 0 && !empty($client_id)): ?>
            <script>
                function showPaymentError(msg) {
                    var statusDiv = document.getElementById('status-message');
                    if (statusDiv) {
                        statusDiv.textContent = msg;
                        statusDiv.style.display = 'block';
                        statusDiv.style.color = '#721c24';
                        statusDiv.style.background = '#f8d7da';
                        statusDiv.style.border = '1px solid #f5c6cb';
                        statusDiv.style.padding = '12px 16px';
                        statusDiv.style.borderRadius = '4px';
                    }
                }

                function logErrorToServer(context, errorCode, message, debugId, orderId) {
                    try {
                        fetch('/api/log_error.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                context: context,
                                error_code: errorCode,
                                message: message,
                                paypal_debug_id: debugId || null,
                                order_id: orderId || null,
                                timestamp: new Date().toISOString()
                            })
                        }).catch(function() {}); // silently ignore logging failures
                    } catch (e) {}
                }

                paypal.Buttons({
                    createOrder: function(data, actions) {
                        setStatus('Creating order...');
                        return actions.order.create({
                            purchase_units: [{
                                custom_id: '<?php echo htmlspecialchars($paypal_custom_id, ENT_QUOTES, 'UTF-8'); ?>',
                                amount: {
                                    currency_code: 'USD',
                                    value: '<?php echo number_format($final_amount, 2, '.', ''); ?>',
                                    breakdown: {
                                        item_total: {
                                            currency_code: 'USD',
                                            value: '<?php echo number_format($total_amount, 2, '.', ''); ?>'
                                        }
                                        <?php if ($discount_amount > 0): ?>
                                        ,
                                        discount: {
                                            currency_code: 'USD',
                                            value: '<?php echo number_format($discount_amount, 2, '.', ''); ?>'
                                        }
                                        <?php endif; ?>
                                    }
                                },
                                items: <?php echo json_encode($paypal_items); ?>
                            }]
                        });
                    },

                    onApprove: function(data, actions) {
                        setStatus('Processing payment...');

                        return fetch('/api/capture_order.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ order_id: data.orderID })
                        })
                        .then(function(res) {
                            return res.json().then(function(body) {
                                return { ok: res.ok, body: body };
                            }).catch(function() {
                                return { ok: false, body: { error_code: 'invalid_response', message: 'Server returned non-JSON response (HTTP ' + res.status + ').' } };
                            });
                        })
                        .then(function(result) {
                            if (!result.ok || result.body.success === false) {
                                var errCode = result.body.error_code || result.body.error || 'capture_failed';
                                var errMsg  = result.body.message  || 'Payment capture failed. Please try again or contact support.';
                                var debugId = result.body.debug_id || null;
                                logErrorToServer('cart_capture', errCode, errMsg, debugId, data.orderID);
                                showPaymentError('Payment failed: ' + errMsg);
                                return;
                            }
                            // status=COMPLETED is the success indicator
                            if (result.body.status === 'COMPLETED') {
                                setStatus('Payment successful! Redirecting...');
                                window.location.href = '/payment_success.php?order_id=' + encodeURIComponent(data.orderID);
                            } else {
                                var unexpectedMsg = 'Unexpected payment status: ' + (result.body.status || 'unknown');
                                logErrorToServer('cart_capture', 'unexpected_status', unexpectedMsg, null, data.orderID);
                                showPaymentError(unexpectedMsg + '. Please contact support.');
                            }
                        })
                        .catch(function(err) {
                            var errMsg = err && err.message ? err.message : 'Network error during payment capture.';
                            logErrorToServer('cart_capture', 'fetch_error', errMsg, null, data.orderID);
                            showPaymentError('Payment error: ' + errMsg);
                        });
                    },

                    onError: function(err) {
                        var errMsg = err && err.message ? err.message : String(err);
                        logErrorToServer('cart_paypal_sdk', 'sdk_error', errMsg, null, null);
                        showPaymentError('A PayPal error occurred. Please try again or contact support.');
                    },

                    onCancel: function(data) {
                        setStatus('Payment cancelled.');
                        window.location.href = '/payment_cancel.php';
                    }
                }).render('#paypal-button-container');
            </script>
            <?php endif; ?>
                <script>
                    // Remove invoice via AJAX and perform a partial reload of the cart container
                    function removeInvoice(invoiceId) {
                        if (!confirm('Remove this item from your cart?')) return;
                        setStatus('Removing item...');

                        var body = 'remove_invoice_ajax=1&invoice_id=' + encodeURIComponent(invoiceId);

                        fetch(window.location.href, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                            body: body
                        })
                        .then(function(res) { return res.json(); })
                        .then(function(data) {
                            if (data && data.success) {
                                // Partial reload: fetch the current page and replace the cart container
                                fetch(window.location.href, { method: 'GET', credentials: 'same-origin' })
                                    .then(function(r) { return r.text(); })
                                    .then(function(html) {
                                        var parser = new DOMParser();
                                        var doc = parser.parseFromString(html, 'text/html');
                                        var newContainer = doc.querySelector('.cart-container');
                                        var oldContainer = document.querySelector('.cart-container');
                                        if (newContainer && oldContainer) {
                                            oldContainer.innerHTML = newContainer.innerHTML;
                                        } else {
                                            // Fallback to full reload
                                            window.location.reload();
                                        }
                                    });
                            } else {
                                alert(data && data.error ? data.error : 'Failed to remove item.');
                                setStatus('');
                            }
                        })
                        .catch(function(err) {
                            console.error('Remove error', err);
                            alert('Error removing item. See console for details.');
                            setStatus('');
                        });
                    }
                </script>
        <?php endif; ?>
    </div>
    <?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
