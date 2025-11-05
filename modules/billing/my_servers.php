<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Servers - GameServers.World</title>
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

// Get user ID from session
$user_id = intval($_SESSION['website_user_id']);

// Fetch user's active servers
// We'll look for homes assigned to this user
// The relationship is: {table_prefix}billing_orders -> user_id and contains home_id references
// We need to join with {table_prefix}home to get server details

$query = "SELECT 
            h.home_id,
            h.home_name,
            h.enabled,
            rs.remote_server_name,
            gc.game_name,
            o.order_id,
            o.status,
            o.invoice_duration,
            -- use end_date as the expiration marker (set when order is paid/created)
            o.end_date AS expiration_date,
            bs.service_name,
            bs.price_monthly,
            o.price,
            o.discount_amount,
            o.coupon_id,
            bc.code AS coupon_code,
            bc.discount_percent AS coupon_discount_percent
          FROM {$table_prefix}home h
          LEFT JOIN {$table_prefix}remote_servers rs ON h.remote_server_id = rs.remote_server_id
          LEFT JOIN {$table_prefix}game_configs gc ON h.home_cfg_id = gc.home_cfg_id
          LEFT JOIN {$table_prefix}billing_orders o ON h.user_id = o.user_id
          LEFT JOIN {$table_prefix}billing_services bs ON o.service_id = bs.service_id
          LEFT JOIN {$table_prefix}billing_coupons bc ON o.coupon_id = bc.coupon_id
          WHERE h.user_id = $user_id
          ORDER BY h.home_id DESC";

$result = mysqli_query($db, $query);

?>

<div class="site-panel">
    <div class="site-panel-title">My Game Servers</div>
    
    <?php if ($result && mysqli_num_rows($result) > 0): ?>
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Server Name</th>
                    <th>Game</th>
                    <th>Location</th>
                    <th>Status</th>
                    <th>Expiration Date</th>
                    <th>Monthly Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($server = mysqli_fetch_assoc($result)): ?>
                    <?php
                    $is_active = $server['enabled'] == 1;
                    $is_expired = strtotime($server['expiration_date']) < time();
                    $status_class = $is_active ? 'text-success' : 'text-danger';
                    $status_text = $is_active ? 'Active' : 'Inactive';
                    
                    if ($is_expired) {
                        $status_text = 'Expired';
                        $status_class = 'text-danger';
                    }
                    ?>
                    <tr>
                        <td><?php echo htmlspecialchars($server['home_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($server['game_name'] ?? $server['service_name'] ?? 'Unknown'); ?></td>
                        <td><?php echo htmlspecialchars($server['remote_server_name'] ?? 'Unknown'); ?></td>
                        <td class="<?php echo $status_class; ?>"><?php echo $status_text; ?></td>
                        <td><?php echo $server['expiration_date'] ? date('M d, Y', strtotime($server['expiration_date'])) : 'N/A'; ?></td>
                        <td>
                            <?php 
                            $price = $server['price'] ?? $server['price_monthly'];
                            $discount = floatval($server['discount_amount'] ?? 0);
                            
                            if ($price) {
                                if ($discount > 0 && $server['coupon_code']) {
                                    echo '<span style="text-decoration: line-through; color: #999;">$' . number_format($price + $discount, 2) . '</span><br>';
                                    echo '<strong>$' . number_format($price, 2) . '</strong>';
                                    echo '<br><small style="color: #28a745;">(' . htmlspecialchars($server['coupon_code']) . ' -' . number_format($server['coupon_discount_percent'], 0) . '%)</small>';
                                } else {
                                    echo '$' . number_format($price, 2);
                                }
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </td>
                        <td>
                            <?php if ($server['order_id']): ?>
                                <a href="renew_server.php?order_id=<?php echo urlencode($server['order_id']); ?>" class="gsw-btn">Renew</a>
                            <?php else: ?>
                                <span class="muted">N/A</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div class="panel no-data">
            <p style="font-size:1.2rem;margin-bottom:20px;">You don't have any game servers yet.</p>
            <a href="serverlist.php" class="gsw-btn">Browse Game Servers</a>
        </div>
    <?php endif; ?>
</div>

<?php
// Close database connection
mysqli_close($db);
?>

</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
