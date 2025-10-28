<?php
/**
 * Payment Cancelled Page
 * User lands here if they cancel the PayPal payment
 */

session_start();
require_once(__DIR__ . '/includes/header.php');
require_once(__DIR__ . '/includes/config.inc.php');

$invoice_ref = isset($_GET['invoice']) ? $_GET['invoice'] : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Cancelled - Game Server Panel</title>
    <link rel="stylesheet" href="includes/style.css">
</head>
<body>

<div class="container" style="max-width: 800px; margin: 40px auto; padding: 20px;">
    <div class="warning-box" style="background: #fff3cd; border: 1px solid #ffeaa7; color: #856404; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h1 style="margin-top: 0;">Payment Cancelled</h1>
        <p>Your payment was cancelled. No charges have been made to your account.</p>
        <?php if ($invoice_ref): ?>
        <p><strong>Invoice Reference:</strong> <?php echo htmlspecialchars($invoice_ref); ?></p>
        <?php endif; ?>
    </div>

    <div class="info-box" style="background: #f8f9fa; border: 1px solid #dee2e6; padding: 20px; border-radius: 5px; margin-bottom: 20px;">
        <h2>What would you like to do?</h2>
        <ul>
            <li><strong>Return to Cart:</strong> Your items are still in your cart. You can complete the payment anytime.</li>
            <li><strong>Continue Shopping:</strong> Browse our game server options and add more to your cart.</li>
            <li><strong>Need Help?:</strong> Contact our support team if you encountered any issues during checkout.</li>
        </ul>
    </div>

    <div class="actions" style="margin-top: 30px; text-align: center;">
        <a href="cart.php" style="display: inline-block; padding: 12px 24px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Return to Cart</a>
        <a href="order.php" style="display: inline-block; padding: 12px 24px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Continue Shopping</a>
        <a href="support.php" style="display: inline-block; padding: 12px 24px; background: #6c757d; color: white; text-decoration: none; border-radius: 5px;">Contact Support</a>
    </div>
</div>

<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
