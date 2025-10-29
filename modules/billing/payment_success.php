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

/**
 * Process payment record from webhook or capture
 * Marks invoices as paid and creates/extends orders
 * 
 * @param array $record Payment record with invoice, custom, amount, txid, etc.
 * @return bool True if successful, false otherwise
 */
function process_payment_record($record) {
    global $db_host, $db_user, $db_pass, $db_name, $db_port, $table_prefix;
    
    // Extract payment details
    $invoice = $record['invoice'] ?? null;
    $custom = $record['custom'] ?? null;
    $txid = $record['resource_id'] ?? null;
    $amount = $record['amount'] ?? 0;
    
    // Require database connection
    $db = createDatabaseConnection($db_host, $db_user, $db_pass, $db_name, $db_port);
    if (!$db) {
        if (function_exists('site_log_error')) site_log_error('process_payment_db_fail', ['invoice'=>$invoice]);
        else error_log('[payment_success] DB connection failed for invoice=' . $invoice);
        return false;
    }
    
    $now = date('Y-m-d H:i:s');
    $esc_txid = mysqli_real_escape_string($db, (string)$txid);
    
    // Find invoices to mark as paid
    $invoices_to_process = [];
    
    // Try to match by custom_id (which should be invoice_id for single-item carts)
    if ($custom && ctype_digit((string)$custom)) {
        $invoice_id = intval($custom);
        $stmt = $db->prepare("SELECT * FROM " . $table_prefix . "billing_invoices WHERE invoice_id = ? AND status = 'due' LIMIT 1");
        if ($stmt) {
            $stmt->bind_param('i', $invoice_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result && $row = $result->fetch_assoc()) {
                $invoices_to_process[] = $row;
            }
            $stmt->close();
        }
    }
    
    // If no match by custom_id, try matching all unpaid invoices for this payment amount
    // (This handles multi-item carts where custom_id isn't a single invoice_id)
    if (empty($invoices_to_process) && $invoice) {
        // Match by invoice reference from PayPal
        $esc_invoice = mysqli_real_escape_string($db, $invoice);
        $query = "SELECT * FROM " . $table_prefix . "billing_invoices WHERE status = 'due' AND description LIKE '%$esc_invoice%'";
        $result = mysqli_query($db, $query);
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $invoices_to_process[] = $row;
            }
        }
    }
    
    // Process each invoice
    $processed_count = 0;
    foreach ($invoices_to_process as $inv) {
        $invoice_id = intval($inv['invoice_id']);
        $existing_order_id = intval($inv['order_id'] ?? 0);
        $user_id = intval($inv['user_id']);
        $service_id = intval($inv['service_id']);
        $home_name = mysqli_real_escape_string($db, $inv['home_name']);
        $ip = intval($inv['ip']);
        $max_players = intval($inv['max_players']);
        $qty = intval($inv['qty']);
        $duration = mysqli_real_escape_string($db, $inv['invoice_duration']);
        $invoice_amount = floatval($inv['amount']);
        $rcon_pw = mysqli_real_escape_string($db, $inv['remote_control_password'] ?? '');
        $ftp_pw = mysqli_real_escape_string($db, $inv['ftp_password'] ?? '');
        
        // Mark invoice as paid
        $upd_inv = $db->prepare("UPDATE " . $table_prefix . "billing_invoices SET status = 'paid', paid_date = ?, payment_txid = ?, payment_method = 'paypal' WHERE invoice_id = ? LIMIT 1");
        if ($upd_inv) {
            $upd_inv->bind_param('ssi', $now, $esc_txid, $invoice_id);
            $upd_inv->execute();
            $upd_inv->close();
        }
        
        // Check if this is a renewal (existing order_id > 0) or new order (order_id = 0)
        if ($existing_order_id > 0) {
            // RENEWAL: Extend the existing order's end_date
            // Calculate months to add
            $months = 0;
            $q = intval($qty);
            $invdur = strtolower(trim($duration));
            if (strpos($invdur, 'year') !== false) {
                $months = $q * 12;
            } else {
                $months = $q;
            }
            
            // Get current end_date and extend it
            $getEndDate = "SELECT end_date FROM " . $table_prefix . "billing_orders WHERE order_id = $existing_order_id LIMIT 1";
            $endDateResult = mysqli_query($db, $getEndDate);
            if ($endDateResult && mysqli_num_rows($endDateResult) === 1) {
                $endRow = mysqli_fetch_assoc($endDateResult);
                $current_end = $endRow['end_date'] ?? date('Y-m-d H:i:s');
                
                // Extend from current end_date or now (whichever is later)
                $extend_from = (strtotime($current_end) > time()) ? $current_end : date('Y-m-d H:i:s');
                $dt = new DateTime($extend_from);
                if ($months > 0) {
                    $dt->modify('+' . intval($months) . ' months');
                }
                $new_end_date = $dt->format('Y-m-d H:i:s');
                
                // Update order with new end_date and payment info
                $updateOrder = "UPDATE " . $table_prefix . "billing_orders 
                               SET end_date = '$new_end_date', status = 'paid', payment_txid = '$esc_txid', paid_ts = '$now'
                               WHERE order_id = $existing_order_id";
                if (mysqli_query($db, $updateOrder)) {
                    if (function_exists('site_log_info')) site_log_info('payment_renewal_processed', ['order_id'=>$existing_order_id, 'invoice_id'=>$invoice_id, 'new_end_date'=>$new_end_date]);
                    else error_log("[payment_success] Extended order $existing_order_id to $new_end_date for invoice $invoice_id");
                    $processed_count++;
                }
            }
        } else {
            // NEW ORDER: Create a new order record
            // Calculate months for end_date
            $months = 0;
            $q = intval($qty);
            $invdur = strtolower(trim($duration));
            if (strpos($invdur, 'year') !== false) {
                $months = $q * 12;
            } else {
                $months = $q;
            }
            
            $dt = new DateTime('now');
            if ($months > 0) {
                $dt->modify('+' . intval($months) . ' months');
            }
            $end_date = $dt->format('Y-m-d H:i:s');
            
            // Insert order
            $insertOrder = "INSERT INTO " . $table_prefix . "billing_orders (
                user_id, service_id, home_name, ip, max_players, qty, invoice_duration,
                price, remote_control_password, ftp_password, status, order_date, end_date,
                payment_txid, paid_ts
            ) VALUES (
                $user_id, $service_id, '$home_name', $ip, $max_players, $qty, '$duration',
                $invoice_amount, '$rcon_pw', '$ftp_pw', 'paid', '$now', '$end_date',
                '$esc_txid', '$now'
            )";
            
            if (mysqli_query($db, $insertOrder)) {
                $new_order_id = mysqli_insert_id($db);
                
                // Link invoice to order
                $linkInvoice = "UPDATE " . $table_prefix . "billing_invoices SET order_id = $new_order_id WHERE invoice_id = $invoice_id";
                mysqli_query($db, $linkInvoice);
                
                if (function_exists('site_log_info')) site_log_info('payment_new_order_created', ['order_id'=>$new_order_id, 'invoice_id'=>$invoice_id, 'end_date'=>$end_date]);
                else error_log("[payment_success] Created order $new_order_id for invoice $invoice_id");
                $processed_count++;
            }
        }
    }
    
    mysqli_close($db);
    
    if ($processed_count > 0) {
        if (function_exists('site_log_info')) site_log_info('payment_success_processed', ['count'=>$processed_count,'invoice'=>$invoice,'custom'=>$custom]);
        else error_log('[payment_success] Processed ' . $processed_count . ' invoice(s) - invoice=' . $invoice . ' custom=' . $custom);
        return true;
    } else {
        if (function_exists('site_log_warn')) site_log_warn('payment_success_no_match', ['invoice'=>$invoice,'custom'=>$custom]);
        else error_log('[payment_success] No matching invoices found for invoice=' . $invoice . ' custom=' . $custom);
        return false;
    }
}

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
