<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renew Server - GameServers.World</title>
</head>
<body>
<?php
// Require login for this page
require_once(__DIR__ . '/includes/login_required.php');

// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

// Get order ID from URL
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;
$user_id = intval($_SESSION['website_user_id']);

if ($order_id === 0) {
    echo '<div class="site-panel"><p class="text-danger">Invalid order ID.</p></div>';
    include(__DIR__ . '/includes/footer.php');
    exit;
}

// Fetch order details
$query = "SELECT o.*, bs.service_name, bs.price_monthly, bs.price_year 
          FROM ogp_billing_orders o
          LEFT JOIN ogp_billing_services bs ON o.service_id = bs.service_id
          WHERE o.order_id = $order_id AND o.user_id = $user_id
          LIMIT 1";
$result = mysqli_query($db, $query);

if (!$result || mysqli_num_rows($result) === 0) {
    echo '<div class="site-panel"><p class="text-danger">Order not found or you do not have permission to renew this server.</p></div>';
    mysqli_close($db);
    include(__DIR__ . '/includes/footer.php');
    exit;
}

$order = mysqli_fetch_assoc($result);

// Process renewal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_renewal'])) {
    $duration = isset($_POST['duration']) ? $_POST['duration'] : 'month';
    $price = ($duration === 'year') ? $order['price_year'] : $order['price_monthly'];
    
    // Create a new order for renewal
    // In a real system, this would redirect to payment gateway
    // For now, we'll just show a message
    
    $message = "Renewal initiated for " . htmlspecialchars($order['service_name']) . ". ";
    $message .= "Duration: " . ($duration === 'year' ? '1 year' : '1 month') . ". ";
    $message .= "Total: $" . number_format($price, 2) . ". ";
    $message .= "In a production system, you would be redirected to payment processing.";
    
    echo '<div class="site-panel"><div class="alert alert-success">' . $message . '</div></div>';
}

?>

<div class="site-panel">
    <div class="site-panel-title">Renew Server</div>
    
    <div class="panel" style="max-width:600px;margin:20px auto;">
        <h3 style="margin-bottom:20px;"><?php echo htmlspecialchars($order['service_name']); ?></h3>
        
        <form method="POST" action="">
            <div class="form-group" style="margin-bottom:20px;">
                <label style="display:block;margin-bottom:10px;">
                    <input type="radio" name="duration" value="month" checked>
                    1 Month - $<?php echo number_format($order['price_monthly'], 2); ?>
                </label>
                <?php if ($order['price_year'] > 0): ?>
                <label style="display:block;">
                    <input type="radio" name="duration" value="year">
                    1 Year - $<?php echo number_format($order['price_year'], 2); ?>
                </label>
                <?php endif; ?>
            </div>
            
            <button type="submit" name="confirm_renewal" class="btn-primary" style="padding:12px 24px;border-radius:8px;">
                Proceed to Payment
            </button>
            
            <a href="my_servers.php" style="margin-left:20px;color:#667eea;">Cancel</a>
        </form>
    </div>
</div>

<?php
// Close database connection
mysqli_close($db);
?>

<style>
.alert-success {
    background-color: #d1fae5;
    border: 1px solid #6ee7b7;
    color: #065f46;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 20px;
}
.form-group label {
    cursor: pointer;
    padding: 12px;
    border: 2px solid #e1e8ed;
    border-radius: 8px;
    background: rgba(255,255,255,0.05);
}
.form-group label:hover {
    background: rgba(255,255,255,0.1);
}
</style>

</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
