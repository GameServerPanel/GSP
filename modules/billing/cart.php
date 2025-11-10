<?php
// Debugging helper: enable when visiting with ?debug_cart=1
$debug_cart = isset($_GET['debug_cart']) && $_GET['debug_cart'] === '1';
if ($debug_cart) {
    // Show all errors for immediate feedback
    @ini_set('display_errors', '1');
    @ini_set('display_startup_errors', '1');
    @error_reporting(E_ALL);
}

// Register a shutdown function to capture fatal errors and write diagnostics to a debug log
register_shutdown_function(function() use ($debug_cart) {
    $err = error_get_last();
    $logPath = __DIR__ . '/data';
    if (!is_dir($logPath)) @mkdir($logPath, 0755, true);
    $logFile = $logPath . '/debug_cart.log';
    $out = "[" . date('Y-m-d H:i:s') . "] SHUTDOWN: ";
    if ($err) {
        $out .= json_encode($err) . "\n";
    } else {
        $out .= "no error\n";
    }
    @file_put_contents($logFile, $out, FILE_APPEND | LOCK_EX);
    if ($debug_cart && $err) {
        echo '<pre style="background:#fff7e6;border:1px solid #e6b800;padding:10px;">';
        echo "Shutdown error:\n" . htmlspecialchars(print_r($err, true));
        echo '</pre>';
    }
});
/**
 * Shopping Cart - Rebuilt from scratch for reliability
 * Displays unpaid invoices and provides PayPal checkout
 * Standalone billing module - uses only standard PHP mysqli
 */

// Start session with website session name
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}

// Load configuration
require_once(__DIR__ . '/bootstrap.php');

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
$discount_amount = 0.00;
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
              WHERE i.user_id = " . intval($user_id) . " AND i.status = 'due'
              ORDER BY i.invoice_date ASC";

    $result = mysqli_query($db, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $invoices[] = $row;
            $total_amount += floatval($row['amount']);
        }
        mysqli_free_result($result);
    }

    $cart_empty = (count($invoices) === 0);
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
                    if (is_array($allowed_games) && count($allowed_games) > 0) {
                        $has_valid_game = false;
                        foreach ($invoices as $inv) {
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
if (empty($applied_coupon) && isset($_SESSION['cart_coupon_code'])) {
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

    // Verify ownership and that invoice is still due
    $check_q = "SELECT invoice_id FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND status = 'due' LIMIT 1";
    $check_r = mysqli_query($db, $check_q);
    if (!($check_r && mysqli_num_rows($check_r) === 1)) {
        echo json_encode(['success' => false, 'error' => 'Invoice not found or cannot be removed.']);
        exit;
    }

    // Hard-delete the invoice row
    $del_q = "DELETE FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND status = 'due' LIMIT 1";
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
            // Verify ownership and that invoice is still due
            $check_q = "SELECT invoice_id FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND status = 'due' LIMIT 1";
            $check_r = mysqli_query($db, $check_q);
            if ($check_r && mysqli_num_rows($check_r) === 1) {
                // Hard-delete to remove from cart
                $del_q = "DELETE FROM {$table_prefix}billing_invoices WHERE invoice_id = " . intval($remove_id) . " AND user_id = " . intval($user_id) . " AND status = 'due' LIMIT 1";
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
    $discount_amount = $total_amount * ($coupon_discount_percent / 100);
}

$final_amount = $total_amount - $discount_amount;

// PayPal configuration
$sandbox = true;
$client_id = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';

// Prepare PayPal items
$paypal_items = [];
foreach ($invoices as $inv) {
    $game_display = !empty($inv['game_name']) ? $inv['game_name'] : 'Game Server';
    $qty = max(1, intval($inv['qty']));
    $paypal_items[] = [
        'name' => $inv['home_name'] . ' (' . $game_display . ')',
        'description' => $inv['description'] ?? '',
        'quantity' => $qty,
        'unit_amount' => [
            'currency_code' => 'USD',
            'value' => number_format(floatval($inv['amount']) / $qty, 2, '.', '')
        ]
    ];
}

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
            margin: 40px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
        }
    </style>
    <?php // Font Awesome for small icon buttons ?>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Favicon -->
    <link rel="icon" href="images/logo-sm.png" type="image/png">
    <link rel="apple-touch-icon" href="images/logo-sm.png">
    <?php if (!$cart_empty): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($client_id); ?>&currency=USD&intent=capture"></script>
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
                <a href="/order.php" class="btn">Browse Servers</a>
            </div>
        <?php else: ?>
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
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td>
                            <div class="game-name"><?php echo htmlspecialchars($inv['game_name'] ?? 'Game Server'); ?></div>
                            <div class="server-name"><?php echo htmlspecialchars($inv['home_name']); ?></div>
                            <?php if (!empty($inv['description'])): ?>
                            <div class="description"><?php echo htmlspecialchars($inv['description']); ?></div>
                            <?php endif; ?>
                        </td>
                                <td><?php echo htmlspecialchars($inv['invoice_duration']); ?></td>
                                <td><?php echo intval($inv['qty']); ?>x</td>
                                <td><span class="status-badge"><?php echo htmlspecialchars(strtoupper($inv['status'])); ?></span></td>
                                <td style="text-align: right;">
                                    <span class="price">$<?php echo number_format(floatval($inv['amount']), 2); ?></span>
                                </td>
                                <td style="text-align: right;">
                                    <button type="button" class="btn btn-secondary btn-small" title="Remove" onclick="removeInvoice(<?php echo intval($inv['invoice_id']); ?>)">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

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
            <div class="checkout-section">
                <h3>Checkout with PayPal</h3>
                <p>Click the button below to complete your purchase securely through PayPal.</p>
                
                <div id="paypal-button-container"></div>
                <div id="status-message" class="status-message"></div>
                
                <div class="action-buttons">
                    <a href="/order.php" class="btn btn-secondary">Continue Shopping</a>
                    <a href="/my_account.php" class="btn btn-secondary">My Account</a>
                </div>
            </div>

            <script>
                function setStatus(msg) {
                    const statusDiv = document.getElementById('status-message');
                    statusDiv.textContent = msg;
                    statusDiv.style.display = 'block';
                }

                paypal.Buttons({
                    createOrder: function(data, actions) {
                        setStatus('Creating order...');
                        return actions.order.create({
                            purchase_units: [{
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
                        
                        // Capture the order via our backend
                        return fetch('/api/capture_order.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ order_id: data.orderID })
                        })
                        .then(function(res) {
                            if (!res.ok) {
                                return res.text().then(function(text) {
                                    throw new Error('Payment capture failed: ' + text);
                                });
                            }
                            return res.json();
                        })
                        .then(function(orderData) {
                            console.log('Capture result:', orderData);
                            if (orderData.status === 'COMPLETED') {
                                setStatus('Payment successful! Redirecting...');
                                window.location.href = '/payment_success.php?order_id=' + data.orderID;
                            } else {
                                throw new Error('Unexpected payment status: ' + orderData.status);
                            }
                        })
                        .catch(function(err) {
                            console.error('Payment error:', err);
                            setStatus('Error: ' + err.message);
                            alert('Payment processing failed. Please try again or contact support.');
                        });
                    },
                    
                    onError: function(err) {
                        console.error('PayPal error:', err);
                        setStatus('Payment error occurred');
                        alert('An error occurred during payment. Please try again.');
                    },
                    
                    onCancel: function(data) {
                        setStatus('Payment cancelled');
                        window.location.href = '/payment_cancel.php';
                    }
                }).render('#paypal-button-container');
            </script>
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
