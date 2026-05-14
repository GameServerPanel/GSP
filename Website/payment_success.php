<?php
/**
 * Payment Success Page
 * Shows order confirmation after successful PayPal payment
 * Standalone billing module - uses only standard PHP mysqli
 */
if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}
require_once(__DIR__ . '/includes/config_loader.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Get PayPal order ID from URL
$paypal_order_id = isset($_GET['order_id']) ? trim($_GET['order_id']) : '';
$success_source = isset($_GET['source']) ? trim($_GET['source']) : '';

// Get user ID from session
$user_id = isset($_SESSION['website_user_id']) ? intval($_SESSION['website_user_id']) : 
           (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);
$is_admin_viewer = strtolower((string)($_SESSION['users_group'] ?? '')) === 'admin';
$provision_summary = $_SESSION['billing_provision_results'][$paypal_order_id] ?? null;

// Connect to database
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
$orders = [];
$total_paid = 0;

function billing_payment_success_provision_state(array $order): array
{
    $homeId = intval($order['home_id'] ?? 0);
    $hasHome = intval($order['has_home'] ?? 0) > 0;
    $ipPortCount = intval($order['ip_port_count'] ?? 0);
    $modCount = intval($order['mod_count'] ?? 0);

    if ($homeId <= 0) {
        return ['label' => 'PENDING', 'message' => 'Server record is queued for provisioning.', 'class' => 'status-badge status-pending'];
    }
    // home_id exists but server_homes row does not: orphaned consistency failure.
    if (!$hasHome) {
        return ['label' => 'FAILED', 'message' => 'Server setup failed. Please contact support with your order ID.', 'class' => 'status-badge status-failed'];
    }
    if ($ipPortCount <= 0 || $modCount <= 0) {
        return ['label' => 'PENDING', 'message' => 'Server created; install is pending final IP/mod setup.', 'class' => 'status-badge status-pending'];
    }
    return ['label' => 'INSTALL STARTED', 'message' => 'Server created and install/update trigger has been started or queued.', 'class' => 'status-badge'];
}

function billing_payment_success_banner(array $summary, array $orders): array
{
    if (!empty($summary['result']['failed_count'])) {
        return ['class' => 'status-failed', 'message' => 'Provisioning failed; support has been notified.'];
    }
    foreach ($orders as $order) {
        if (($order['provision_state']['label'] ?? '') === 'FAILED') {
            return ['class' => 'status-failed', 'message' => 'Provisioning failed; support has been notified.'];
        }
    }
    return ['class' => 'status-pending', 'message' => 'Your server is being installed.'];
}

if ($db && $user_id > 0) {
    // Get recent orders for this user (just paid)
    $query = "SELECT o.*, s.service_name,
                     CASE WHEN sh.home_id IS NULL THEN 0 ELSE 1 END AS has_home,
                     (SELECT COUNT(*) FROM {$table_prefix}home_ip_ports hip WHERE hip.home_id = o.home_id) AS ip_port_count,
                     (SELECT COUNT(*) FROM {$table_prefix}game_mods gm WHERE gm.home_id = o.home_id) AS mod_count
              FROM {$table_prefix}billing_orders o
              LEFT JOIN {$table_prefix}billing_services s ON o.service_id = s.service_id
              LEFT JOIN {$table_prefix}server_homes sh ON sh.home_id = o.home_id
              WHERE o.user_id = $user_id 
              AND o.status = 'Active'
              ORDER BY o.order_date DESC 
              LIMIT 10";
    
    $result = mysqli_query($db, $query);
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $row['provision_state'] = billing_payment_success_provision_state($row);
            $orders[] = $row;
            $total_paid += floatval($row['price']);
        }
    }
    
    mysqli_close($db);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Game Server Panel</title>
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
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success-header {
            text-align: center;
            padding: 30px 0;
            border-bottom: 2px solid #28a745;
            margin-bottom: 30px;
        }
        .success-icon {
            font-size: 64px;
            color: #28a745;
            margin-bottom: 20px;
        }
        h1 {
            color: #28a745;
            margin: 0 0 10px 0;
        }
        .subtitle {
            color: #666;
            font-size: 1.1em;
        }
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 20px;
            margin: 30px 0;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            color: #007bff;
        }
        .info-box ul {
            margin: 10px 0;
            padding-left: 20px;
        }
        .info-box li {
            margin: 8px 0;
            line-height: 1.6;
        }
        .orders-table {
            width: 100%;
            border-collapse: collapse;
            margin: 30px 0;
        }
        .orders-table th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }
        .orders-table td {
            padding: 15px 12px;
            border-bottom: 1px solid #dee2e6;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 0.85em;
            font-weight: 600;
            background: #28a745;
            color: white;
        }
        .status-pending {
            background: #f0ad4e;
        }
        .status-failed {
            background: #d9534f;
        }
        .provision-banner {
            margin: 20px 0 0;
            padding: 14px 18px;
            border-radius: 6px;
            color: #fff;
            font-weight: 600;
        }
        .provision-debug {
            margin-top: 20px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 14px 16px;
            background: #f8f9fa;
        }
        .provision-debug summary {
            cursor: pointer;
            font-weight: 600;
        }
        .provision-debug-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 12px;
        }
        .provision-debug-table th,
        .provision-debug-table td {
            border-bottom: 1px solid #dee2e6;
            padding: 8px 6px;
            text-align: left;
            font-size: 0.92em;
            vertical-align: top;
        }
        .btn {
            display: inline-block;
            padding: 12px 24px;
            margin: 10px 10px 10px 0;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
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
        .actions {
            text-align: center;
            margin-top: 40px;
            padding-top: 30px;
            border-top: 2px solid #dee2e6;
        }
    </style>
</head>
<body>
    <?php $banner = billing_payment_success_banner((array)$provision_summary, $orders); ?>
    <div class="container">
        <div class="success-header">
            <div class="success-icon">✓</div>
            <h1>Payment Successful!</h1>
            <p class="subtitle">Your payment has been processed successfully</p>
            <?php if ($paypal_order_id): ?>
            <p style="color: #999; font-size: 0.9em; margin-top: 10px;">
                Transaction ID: <?php echo htmlspecialchars($paypal_order_id); ?>
            </p>
            <?php endif; ?>
        </div>

        <div class="info-box">
            <h3>What Happens Next?</h3>
            <ul>
                <li><strong>✓ Payment Confirmed:</strong> Your payment has been captured by PayPal</li>
                <li><strong>⚙️ Server Provisioning:</strong> Your game server(s) are queued for automatic install now; if a node is unavailable they remain clearly marked as pending install</li>
                <li><strong>📧 Email Notification:</strong> You'll receive a confirmation email with your order details</li>
                <li><strong>🎮 Access Your Servers:</strong> Log into the Game Server Panel to manage your new servers</li>
            </ul>
            <div class="provision-banner <?php echo htmlspecialchars($banner['class']); ?>">
                <?php echo htmlspecialchars($banner['message']); ?>
            </div>
            <?php if ($is_admin_viewer && !empty($provision_summary['result'])): ?>
            <details class="provision-debug" <?php echo !empty($provision_summary['result']['failed_count']) ? 'open' : ''; ?>>
                <summary>Provisioning Debug Summary</summary>
                <p><strong>Provisioning started:</strong> <?php echo !empty($provision_summary['order_ids']) ? 'yes' : 'no'; ?></p>
                <p><strong>Provisioning succeeded:</strong> <?php echo intval($provision_summary['result']['failed_count'] ?? 0) === 0 ? 'yes' : 'no'; ?></p>
                <p><strong>Provisioning failed:</strong> <?php echo intval($provision_summary['result']['failed_count'] ?? 0) > 0 ? 'yes' : 'no'; ?></p>
                <p><strong>Log file path:</strong> <?php echo htmlspecialchars($provision_summary['result']['trace_log_path'] ?? 'modules/billing/logs/provisioning_trace.log'); ?></p>
                <?php if (!empty($provision_summary['result']['trace_error'])): ?>
                <p><strong>Trace error:</strong> <?php echo htmlspecialchars($provision_summary['result']['trace_error']); ?></p>
                <?php endif; ?>
                <?php if (!empty($provision_summary['result']['details']) && is_array($provision_summary['result']['details'])): ?>
                <table class="provision-debug-table">
                    <thead>
                        <tr>
                            <th>Order</th>
                            <th>Status</th>
                            <th>Home ID</th>
                            <th>Mod ID</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($provision_summary['result']['details'] as $detail): ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars((string)($detail['order_id'] ?? '0')); ?></td>
                            <td><?php echo htmlspecialchars((string)($detail['install_result'] ?? 'pending')); ?></td>
                            <td><?php echo htmlspecialchars((string)($detail['home_id'] ?? '0')); ?></td>
                            <td><?php echo htmlspecialchars((string)($detail['mod_id'] ?? '0')); ?></td>
                            <td><?php echo htmlspecialchars((string)($detail['error'] ?? $detail['install_message'] ?? '')); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php endif; ?>
            </details>
            <?php elseif ($success_source === 'free' && !empty($provision_summary['result'])): ?>
            <div style="margin-top:14px;color:#555;font-size:0.95em;">
                Free checkout completed. <?php echo htmlspecialchars($banner['message']); ?>
            </div>
            <?php endif; ?>
        </div>

        <?php if (count((array)$orders) > 0): ?>
        <h2>Your Orders</h2>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Server Name</th>
                    <th>Game</th>
                    <th>Duration</th>
                    <th>Status</th>
                    <th title="Server Setup Status" aria-label="Server Setup Status">Provisioning</th>
                    <th style="text-align: right;">Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ((array)$orders as $order): ?>
                <tr>
                    <td>#<?php echo htmlspecialchars($order['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($order['home_name']); ?></td>
                    <td><?php echo htmlspecialchars($order['service_name'] ?? 'Game Server'); ?></td>
                    <td><?php echo htmlspecialchars($order['qty']); ?>x <?php echo htmlspecialchars($order['invoice_duration']); ?></td>
                    <td><span class="status-badge">PAID</span></td>
                    <td>
                        <span class="<?php echo htmlspecialchars($order['provision_state']['class'] ?? 'status-badge'); ?>">
                            <?php echo htmlspecialchars($order['provision_state']['label'] ?? 'PENDING'); ?>
                        </span>
                        <div style="margin-top:6px;color:#555;font-size:0.9em;">
                            <?php echo htmlspecialchars($order['provision_state']['message'] ?? 'Provisioning state unavailable.'); ?>
                        </div>
                    </td>
                    <td style="text-align: right; font-weight: 600; color: #28a745;">
                        $<?php echo number_format(floatval($order['price']), 2); ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="info-box" style="background: #fff3cd; border-left-color: #856404;">
            <p><strong>Note:</strong> Your orders are being processed. If you don't see them listed above, please log into your account or contact support.</p>
        </div>
        <?php endif; ?>

        <div class="actions">
            <a href="/my_account.php" class="btn">View My Account</a>
            <a href="/order.php" class="btn btn-secondary">Order Another Server</a>
            <a href="/index.php" class="btn btn-secondary">Back to Home</a>
        </div>
    </div>
</body>
</html>
