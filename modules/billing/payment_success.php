<?php
/**
 * Payment Success Page
 * User lands here after successful PayPal payment
 */

session_start();
require_once(__DIR__ . '/includes/header.php');
require_once(__DIR__ . '/includes/config.inc.php');
require_once(__DIR__ . '/../../includes/database_mysqli.php');
require_once(__DIR__ . '/includes/log.php');
require_once(__DIR__ . '/includes/payment_processor.php');

$invoice_ref = isset($_GET['invoice']) ? $_GET['invoice'] : '';
$user_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Game Server Panel</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<div class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <div class="success-box" style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h1 style="margin-top: 0;">✓ Payment Successful!</h1>
        <p>Thank you for your purchase. Your payment has been received and is being processed.</p>
        <?php if ($invoice_ref): ?>
        <p><strong>Invoice Reference:</strong> <?php echo htmlspecialchars($invoice_ref); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-box" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>What happens next?</h2>
        <ol>
            <li><strong>Payment Confirmation:</strong> Your payment has been captured by PayPal</li>
            <li><strong>Order Creation:</strong> Your game server order has been created</li>
            <li><strong>Server Provisioning:</strong> Your server will be provisioned automatically (this may take a few minutes)</li>
            <li><strong>Email Notification:</strong> You'll receive an email with your server details and login credentials</li>
        </ol>
    </div>

    <?php
    // Show user's recent orders
    if ($user_id > 0) {
        $db = createDatabaseConnection($db_host, $db_user, $db_pass, $db_name, $db_port);
        if ($db) {
            $result = mysqli_query($db, "SELECT * FROM ogp_billing_orders WHERE user_id=$user_id ORDER BY order_date DESC LIMIT 5");
            if ($result && mysqli_num_rows($result) > 0) {
                echo '<div class="orders-box" style="background: #fff; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px;">';
                echo '<h2>Your Recent Orders</h2>';
                echo '<table style="width: 100%; border-collapse: collapse;">';
                echo '<thead><tr style="background: #f8f9fa;">';
                echo '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Order ID</th>';
                echo '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Server</th>';
                echo '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Status</th>';
                echo '<th style="padding: 10px; text-align: left; border-bottom: 2px solid #dee2e6;">Date</th>';
                echo '<th style="padding: 10px; text-align: right; border-bottom: 2px solid #dee2e6;">Price</th>';
                echo '</tr></thead><tbody>';
                
                while ($order = mysqli_fetch_assoc($result)) {
                    $statusColor = $order['status'] === 'paid' ? '#28a745' : '#6c757d';
                    echo '<tr style="border-bottom: 1px solid #dee2e6;">';
                    echo '<td style="padding: 10px;">#' . htmlspecialchars($order['order_id']) . '</td>';
                    echo '<td style="padding: 10px;">' . htmlspecialchars($order['home_name']) . '</td>';
                    echo '<td style="padding: 10px;"><span style="color: ' . $statusColor . '; font-weight: bold;">' . htmlspecialchars(ucfirst($order['status'])) . '</span></td>';
                    echo '<td style="padding: 10px;">' . htmlspecialchars($order['order_date']) . '</td>';
                    echo '<td style="padding: 10px; text-align: right;">$' . htmlspecialchars(number_format($order['price'], 2)) . '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody></table>';
                echo '</div>';
            }
            mysqli_close($db);
        }
    }
    ?>

    <div class="actions" style="margin-top: 30px; text-align: center;">
        <a href="my_account.php" style="display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">View My Servers</a>
        <a href="order.php" style="display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px;">Order Another Server</a>
    </div>
</div>

<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
