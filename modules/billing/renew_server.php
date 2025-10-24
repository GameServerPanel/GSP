<?php
// Silent renew endpoint: mark an existing order as 'renew' and redirect back.
// This endpoint produces no visible output — it always redirects back to the referrer.

// Require login and configuration
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}
require_once(__DIR__ . '/includes/config.inc.php');
require_once(__DIR__ . '/includes/login_required.php');
require_once(__DIR__ . '/includes/log.php');

// Connect to DB (use mysqli like other website modules)
$db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    // fail silently and redirect back
    $back = $_SERVER['HTTP_REFERER'] ?? 'my_account.php';
    header('Location: ' . $back);
    exit;
}

// Resolve user id from session (same logic as other pages)
$user_id = intval($_SESSION['website_user_id'] ?? $_SESSION['user_id'] ?? 0);
if ($user_id <= 0 && isset($_SESSION['website_username']) && !empty($_SESSION['website_username'])) {
    $safe_uname = mysqli_real_escape_string($db, $_SESSION['website_username']);
    $qr = mysqli_query($db, "SELECT user_id FROM ogp_users WHERE users_login = '$safe_uname' LIMIT 1");
    if ($qr && mysqli_num_rows($qr) === 1) {
        $rr = mysqli_fetch_assoc($qr);
        $user_id = intval($rr['user_id'] ?? 0);
        if ($user_id > 0) {
            $_SESSION['website_user_id'] = $user_id;
        }
    }
}

// Always accept order_id from GET (link-based) or POST (form)
$order_id = intval($_REQUEST['order_id'] ?? 0);
// Allow optional duration override via ?duration=year
$duration = (isset($_REQUEST['duration']) && $_REQUEST['duration'] === 'year') ? 'year' : 'month';

$redirect_to = 'cart.php';

if ($order_id <= 0 || $user_id <= 0) {
    header('Location: ' . $redirect_to);
    exit;
}

// Fetch order and verify ownership
$stmt = $db->prepare('SELECT order_id, user_id, service_id, qty, invoice_duration, price, home_id FROM ogp_billing_orders WHERE order_id = ? LIMIT 1');
if (!$stmt) {
    header('Location: ' . $redirect_to);
    exit;
}
$stmt->bind_param('i', $order_id);
$stmt->execute();
$res = $stmt->get_result();
if (!$res || $res->num_rows === 0) {
    $stmt->close();
    header('Location: ' . $redirect_to);
    exit;
}
$order = $res->fetch_assoc();
$stmt->close();

if (intval($order['user_id']) !== intval($user_id)) {
    // Not the owner — silently redirect
    header('Location: ' . $redirect_to);
    exit;
}

// Determine price for selected duration by looking up service prices
$service_id = intval($order['service_id'] ?? 0);
$price_val = floatval($order['price'] ?? 0.0);
if ($service_id > 0) {
    $sstmt = $db->prepare('SELECT price_monthly, price_year FROM ogp_billing_services WHERE service_id = ? LIMIT 1');
    if ($sstmt) {
        $sstmt->bind_param('i', $service_id);
        $sstmt->execute();
        $sres = $sstmt->get_result();
        if ($sres && $sres->num_rows > 0) {
            $srow = $sres->fetch_assoc();
            if ($duration === 'year' && !empty($srow['price_year']) && floatval($srow['price_year']) > 0) {
                $price_val = floatval($srow['price_year']);
            } else {
                $price_val = floatval($srow['price_monthly']);
            }
        }
        $sstmt->close();
    }
}

// Update order: set status='renew', invoice_duration, qty, price
$new_status = 'renew';
$qty = 1;
$price_formatted = number_format($price_val, 2, '.', '');
 $upd = $db->prepare('UPDATE ogp_billing_orders SET status = ?, invoice_duration = ?, qty = ?, price = ? WHERE order_id = ? AND user_id = ? LIMIT 1');
if ($upd) {
    // types: status (s), invoice_duration (s), qty (i), price (d), order_id (i), user_id (i)
    $upd->bind_param('ssiddi', $new_status, $duration, $qty, $price_formatted, $order_id, $user_id);
    $ok = $upd->execute();
    $affected = $upd->affected_rows;
    $upd->close();
    if ($ok && $affected > 0) {
        // Insert a row into ogp_logger if table exists (best-effort)
        $client_ip = $_SERVER['REMOTE_ADDR'] ?? '';
        $msg = "USER-RENEW: User {$user_id} marked order {$order_id} as renew";
        // Try insert into ogp_logger table
        $escaped_msg = mysqli_real_escape_string($db, $msg);
        $escaped_ip = mysqli_real_escape_string($db, $client_ip);
        // Determine logger table name (best-effort). Try standard name then any table that ends with 'logger'.
        $logger_table = null;
        $check = mysqli_query($db, "SHOW TABLES LIKE 'ogp_logger'");
        if ($check && mysqli_num_rows($check) > 0) {
            $logger_table = 'ogp_logger';
        } else {
            $reslt = mysqli_query($db, "SHOW TABLES LIKE '%logger'");
            if ($reslt && mysqli_num_rows($reslt) > 0) {
                $row = mysqli_fetch_row($reslt);
                $logger_table = $row[0];
            }
        }
        if ($logger_table) {
            $dt = date('Y-m-d H:i:s');
            $ins = "INSERT INTO `" . $logger_table . "` (`date`, `user_id`, `ip`, `message`) VALUES ('{$dt}', " . intval($user_id) . ", '{$escaped_ip}', '{$escaped_msg}')";
            @mysqli_query($db, $ins);
        }
    }
}

// Done — redirect back silently
header('Location: ' . $redirect_to);
exit;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Renew Server - GameServers.World</title>
</head>
<body>
// Process renewal: mark the existing order as a 'renew' so it appears in the cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_renewal'])) {
    $duration = isset($_POST['duration']) ? $_POST['duration'] : 'month';
    $invoice_duration = ($duration === 'year') ? 'year' : 'month';
    $qty = 1;

    // Determine price based on duration (fall back to monthly if missing)
    $price = ($duration === 'year' && !empty($order['price_year']) && floatval($order['price_year']) > 0) ? floatval($order['price_year']) : floatval($order['price_monthly']);

    // Prepare update to set this order into renew state
    if ($upd = $db->prepare("UPDATE ogp_billing_orders SET status = ?, invoice_duration = ?, qty = ?, price = ? WHERE order_id = ? AND user_id = ? LIMIT 1")) {
        $new_status = 'renew';
        $orderIdInt = intval($order_id);
        $userIdInt = intval($user_id);
        $price_val = number_format($price, 2, '.', '');
        $upd->bind_param('ssiids', $new_status, $invoice_duration, $qty, $price_val, $orderIdInt, $userIdInt);
        $ok = $upd->execute();
        $affected = $upd->affected_rows;
        $upd->close();
        if ($ok && $affected > 0) {
            // Log to panel logger that the order was marked for renewal
            if (isset($db) && method_exists($db, 'logger')) {
                $db->logger("USER-RENEW: User " . intval($userIdInt) . " marked order " . intval($orderIdInt) . " as renew");
            }
            // Successfully transitioned to renew — go to cart
            header('Location: cart.php');
            exit;
        } else {
            $err = mysqli_error($db);
            echo '<div class="site-panel"><div class="alert alert-danger">Failed to mark order for renewal: ' . htmlspecialchars($err) . '</div></div>';
        }
    } else {
        $err = mysqli_error($db);
        echo '<div class="site-panel"><div class="alert alert-danger">Failed to prepare renewal update: ' . htmlspecialchars($err) . '</div></div>';
    }
}

?>

<div class="site-panel">
    <div class="site-panel-title">Renew Server</div>
    
    <div class="panel" style="max-width:600px;margin:20px auto;">
        <h3><?php echo htmlspecialchars($order['service_name']); ?></h3>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>
                    <input type="radio" name="duration" value="month" checked>
                    1 Month - $<?php echo number_format($order['price_monthly'], 2); ?>
                </label>
                <?php if ($order['price_year'] > 0): ?>
                <label>
                    <input type="radio" name="duration" value="year">
                    1 Year - $<?php echo number_format($order['price_year'], 2); ?>
                </label>
                <?php endif; ?>
            </div>
            
            <button type="submit" name="confirm_renewal" class="gsw-btn">
                Proceed to Payment
            </button>
            
            <a href="my_servers.php" class="account-edit-summary" style="margin-left:20px;">Cancel</a>
        </form>
    </div>
</div>

<?php
// Close database connection
mysqli_close($db);
?>

</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
