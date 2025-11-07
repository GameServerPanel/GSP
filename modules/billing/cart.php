<?php
/**
 * Shopping Cart - Display unpaid invoices and PayPal checkout
 * Standalone billing module - uses only standard PHP mysqli
 */
session_start();
require_once(__DIR__ . '/includes/config.inc.php');
require_once(__DIR__ . '/includes/login_required.php');

// Get user ID from session (website_user_id preferred)
$user_id = isset($_SESSION['website_user_id']) ? intval($_SESSION['website_user_id']) : 
           (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);

if ($user_id <= 0) {
    header('Location: /login.php?return_to=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

// Connect to database using mysqli
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die('Database connection failed: ' . mysqli_connect_error());
}

// Fetch all unpaid invoices for this user
$invoices = [];
$total_amount = 0.00;
$query = "SELECT i.*, s.game_key, s.game_name 
          FROM {$table_prefix}billing_invoices i
          LEFT JOIN {$table_prefix}billing_services s ON i.service_id = s.service_id
          WHERE i.user_id = " . intval($user_id) . " AND i.status = 'due'
          ORDER BY i.invoice_date ASC";

$result = mysqli_query($db, $query);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $invoices[] = $row;
        $total_amount += floatval($row['amount']);
    }
}

// If cart is empty, show message
$cart_empty = count($invoices) === 0;

// Coupon handling
$coupon_code = '';
$coupon_discount_percent = 0;
$coupon_error = '';
$coupon_success = '';
$applied_coupon = null;

// Check for coupon in session
if (isset($_SESSION['cart_coupon_code'])) {
    $coupon_code = $_SESSION['cart_coupon_code'];
}

// Handle coupon application
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $submitted_code = trim($_POST['coupon_code'] ?? '');
    
    if (empty($submitted_code)) {
        $coupon_error = 'Please enter a coupon code.';
    } else {
        // Validate coupon
        $safe_code = mysqli_real_escape_string($db, $submitted_code);
        $coupon_query = "SELECT * FROM {$table_prefix}billing_coupons 
                        WHERE code = '$safe_code' AND is_active = 1";
        $coupon_result = mysqli_query($db, $coupon_query);
        
        if ($coupon_result && mysqli_num_rows($coupon_result) === 1) {
            $coupon = mysqli_fetch_assoc($coupon_result);
            
            // Check expiration
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
                $coupon_error = 'This coupon has expired.';
            } elseif ($max_uses_reached) {
                $coupon_error = 'This coupon has reached its maximum usage limit.';
            } else {
                // Check game filter
                $game_valid = true;
                if ($coupon['game_filter_type'] === 'specific_games' && !empty($coupon['game_filter_list'])) {
                    $allowed_games = json_decode($coupon['game_filter_list'], true);
                    if (is_array($allowed_games) && count($allowed_games) > 0) {
                        // Check if any invoice game is in allowed list
                        $has_valid_game = false;
                        foreach ($invoices as $inv) {
                            if (in_array($inv['game_key'], $allowed_games)) {
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
                    $coupon_error = 'This coupon is not valid for the items in your cart.';
                } else {
                    // Apply coupon (stored in session, applied at checkout)
                    $applied_coupon = $coupon;
                    $coupon_code = $submitted_code;
                    $coupon_discount_percent = floatval($coupon['discount_percent']);
                    $_SESSION['cart_coupon_code'] = $coupon_code;
                    $_SESSION['cart_coupon_id'] = $coupon['coupon_id'];
                    $coupon_success = 'Coupon "' . htmlspecialchars($coupon['name']) . '" applied! You save ' . $coupon_discount_percent . '%';
                }
            }
        } else {
            $coupon_error = 'Invalid coupon code.';
        }
    }
}

// Handle coupon removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_coupon'])) {
    unset($_SESSION['cart_coupon_code']);
    unset($_SESSION['cart_coupon_id']);
    $coupon_code = '';
    $coupon_discount_percent = 0;
    $applied_coupon = null;
}

// Calculate discount if coupon is applied
$discount_amount = 0;
if (!empty($coupon_code) && $coupon_discount_percent > 0) {
    // Re-validate the coupon from session
    $safe_code = mysqli_real_escape_string($db, $coupon_code);
    $coupon_query = "SELECT * FROM {$table_prefix}billing_coupons 
                    WHERE code = '$safe_code' AND is_active = 1";
    $coupon_result = mysqli_query($db, $coupon_query);
    
    if ($coupon_result && mysqli_num_rows($coupon_result) === 1) {
        $applied_coupon = mysqli_fetch_assoc($coupon_result);
        $coupon_discount_percent = floatval($applied_coupon['discount_percent']);
        $discount_amount = $total_amount * ($coupon_discount_percent / 100);
    }
}

$final_amount = $total_amount - $discount_amount;

// PayPal configuration
$sandbox = true; // Set to false for live PayPal
$client_id = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';

// Prepare PayPal items array
$paypal_items = [];
foreach ($invoices as $inv) {
    $game_display = !empty($inv['game_name']) ? $inv['game_name'] : 'Game Server';
    $paypal_items[] = [
        'name' => $inv['home_name'] . ' (' . $game_display . ')',
        'description' => $inv['description'],
        'quantity' => intval($inv['qty']),
        'unit_amount' => [
            'currency_code' => 'USD',
            'value' => number_format(floatval($inv['amount']) / intval($inv['qty']), 2, '.', '')
        ]
    ];
}

// Get site base URL dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$siteBase = $protocol . $host;

mysqli_close($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Game Server Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f5f5;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        .cart-empty {
            text-align: center;
            padding: 40px;
            color: #666;
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
        }
        .cart-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .cart-table tr:hover {
            background: #f8f9fa;
        }
        .game-name {
            font-weight: 600;
            color: #007bff;
        }
        .server-name {
            color: #666;
            font-size: 0.9em;
        }
        .price {
            font-weight: 600;
            color: #28a745;
        }
        .cart-total {
            text-align: right;
            padding: 20px 0;
            border-top: 2px solid #dee2e6;
            margin-bottom: 30px;
        }
        .cart-total .total-label {
            font-size: 1.2em;
            font-weight: 600;
            margin-right: 20px;
        }
        .cart-total .total-amount {
            font-size: 1.5em;
            font-weight: 700;
            color: #28a745;
        }
        .checkout-section {
            padding: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85em;
            font-weight: 600;
        }
        .status-due {
            background: #fff3cd;
            color: #856404;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-right: 10px;
            font-weight: 600;
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
        #paypal-button-container {
            max-width: 400px;
            margin: 20px 0;
        }
        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .coupon-section {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .coupon-input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 4px;
            font-size: 1em;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
    <?php if (!$cart_empty): ?>
    <script src="https://www.paypal.com/sdk/js?client-id=<?php echo htmlspecialchars($client_id); ?>&currency=USD&intent=capture"></script>
    <?php endif; ?>
</head>
<body>
    <div class="container">
        <h1>🛒 Shopping Cart</h1>
        
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td>
                            <div class="game-name"><?php echo htmlspecialchars($inv['game_name'] ?? 'Game Server'); ?></div>
                            <div class="server-name"><?php echo htmlspecialchars($inv['home_name']); ?></div>
                            <?php if (!empty($inv['description'])): ?>
                            <div style="font-size: 0.85em; color: #999; margin-top: 4px;">
                                <?php echo htmlspecialchars($inv['description']); ?>
                            </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($inv['invoice_duration']); ?></td>
                        <td><?php echo htmlspecialchars($inv['qty']); ?>x</td>
                        <td>
                            <span class="status-badge status-due">
                                <?php echo htmlspecialchars(strtoupper($inv['status'])); ?>
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <span class="price">$<?php echo number_format(floatval($inv['amount']), 2); ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Coupon Section -->
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                <h3 style="margin-top: 0;">Apply Coupon Code</h3>
                
                <?php if (!empty($coupon_error)): ?>
                    <div style="background: #f8d7da; color: #721c24; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                        <?php echo htmlspecialchars($coupon_error); ?>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($coupon_success)): ?>
                    <div style="background: #d4edda; color: #155724; padding: 10px; border-radius: 4px; margin-bottom: 15px;">
                        <?php echo $coupon_success; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($applied_coupon)): ?>
                    <form method="POST" style="display: flex; gap: 10px; align-items: flex-end;">
                        <div style="flex: 1;">
                            <label style="display: block; margin-bottom: 5px; font-weight: 600;">Coupon Code:</label>
                            <input type="text" name="coupon_code" placeholder="Enter code" 
                                   style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 4px;" 
                                   value="<?php echo htmlspecialchars($coupon_code); ?>">
                        </div>
                        <button type="submit" name="apply_coupon" class="btn">Apply</button>
                    </form>
                <?php else: ?>
                    <div style="display: flex; justify-content: space-between; align-items: center; background: #d4edda; padding: 15px; border-radius: 4px;">
                        <div>
                            <strong style="color: #155724;">Coupon Applied:</strong> 
                            <span style="color: #155724;"><?php echo htmlspecialchars($applied_coupon['name']); ?> 
                            (<?php echo htmlspecialchars($applied_coupon['discount_percent']); ?>% off)</span>
                        </div>
                        <form method="POST" style="margin: 0;">
                            <button type="submit" name="remove_coupon" class="btn btn-secondary" 
                                    style="padding: 8px 16px;">Remove</button>
                        </form>
                    </div>
                <?php endif; ?>
            </div>

            <div class="cart-total">
                <?php if ($discount_amount > 0): ?>
                    <div style="margin-bottom: 10px;">
                        <span class="total-label">Subtotal:</span>
                        <span style="font-size: 1.2em; color: #666;">$<?php echo number_format($total_amount, 2); ?></span>
                    </div>
                    <div style="margin-bottom: 10px; color: #28a745;">
                        <span class="total-label">Discount (<?php echo $coupon_discount_percent; ?>%):</span>
                        <span style="font-size: 1.2em; font-weight: 600;">-$<?php echo number_format($discount_amount, 2); ?></span>
                    </div>
                <?php endif; ?>
                <span class="total-label">Total:</span>
                <span class="total-amount">$<?php echo number_format($final_amount, 2); ?></span>
            </div>

            <div class="checkout-section">
                <h3>Checkout with PayPal</h3>
                <p>Click the button below to complete your purchase securely through PayPal.</p>
                
                <div id="paypal-button-container"></div>
                <div id="status-message" class="loading" style="display:none;"></div>
                
                <div style="margin-top: 30px;">
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
                                // Redirect to success page
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
        <?php endif; ?>
    </div>
</body>
</html>