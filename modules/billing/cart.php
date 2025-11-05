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
    <link rel="stylesheet" href="/includes/style.css">
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

            <div class="cart-total">
                <span class="total-label">Total:</span>
                <span class="total-amount">$<?php echo number_format($total_amount, 2); ?></span>
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
                                    value: '<?php echo number_format($total_amount, 2, '.', ''); ?>',
                                    breakdown: {
                                        item_total: {
                                            currency_code: 'USD',
                                            value: '<?php echo number_format($total_amount, 2, '.', ''); ?>'
                                        }
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