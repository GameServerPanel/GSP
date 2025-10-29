<?php
require_once(__DIR__ . '/../includes/config.inc.php');
// Standalone billing module - do NOT include panel files
// Connect directly to MySQL using mysqli
$sandbox       = true; // flip to false for Live
$client_id     = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';

// Ensure all errors are logged, not output (to prevent JSON corruption)
ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');
$in = json_decode(file_get_contents('php://input'), true) ?: [];
$order_id = $in['order_id'] ?? null;
if (!$order_id) { http_response_code(400); echo json_encode(['error'=>'missing order_id']); exit; }

$api = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

$ch = curl_init("$api/v1/oauth2/token");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
  CURLOPT_HTTPHEADER => ['Accept: application/json'],
  CURLOPT_USERPWD => $client_id . ':' . $client_secret,
]);
$tok  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($http !== 200) { http_response_code(500); echo json_encode(['error'=>'oauth_fail']); exit; }
$access = json_decode($tok, true)['access_token'] ?? null;

$ch = curl_init("$api/v2/checkout/orders/$order_id/capture");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [ 'Content-Type: application/json', 'Authorization: Bearer ' . $access ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
curl_close($ch);

// Ensure logs folder exists and provide a helper to write debug info
$logDir = __DIR__ . '/../logs';
if (!is_dir($logDir)) @mkdir($logDir, 0755, true);
$logFile = $logDir . '/paypal_capture.log';
function capture_log($label, $data) {
    global $logFile;
    $entry = '[' . date('Y-m-d H:i:s') . '] ' . $label . "\n";
    if (is_array($data) || is_object($data)) $entry .= print_r($data, true);
    else $entry .= (string)$data;
    $entry .= "\n---\n";
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

// Log the raw curl response for debugging
capture_log('paypal_curl_response_http_' . $http, $res === false ? "(curl failed) " . $curl_err : $res);

// Normalize response: ensure we always return valid JSON to the caller
if ($res === false || $res === '') {
    // Curl-level failure or empty body
    http_response_code(502);
    $out = ['error' => 'paypal_empty_response', 'http' => $http, 'curl_error' => $curl_err];
    capture_log('paypal_empty_response', $out);
    echo json_encode($out);
    exit;
}

// Attempt to decode PayPal response
$capture = json_decode($res, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // PayPal returned non-JSON / malformed response — return it as raw string inside JSON
    http_response_code(502);
    $out = ['error' => 'paypal_invalid_json', 'http' => $http, 'raw' => $res];
    capture_log('paypal_invalid_json', $out);
    echo json_encode($out);
    exit;
}

if ($http !== 201 && $http !== 200) {
    http_response_code($http);
    // Return structured JSON with PayPal's decoded response
    $out = ['error' => 'paypal_capture_failed', 'http' => $http, 'response' => $capture];
    capture_log('paypal_capture_failed', $out);
    echo json_encode($out);
    exit;
}

// Extract payment details
$txid = null;
capture_log('paypal_capture_success', $capture);
if (isset($capture['purchase_units'][0]['payments']['captures'][0])) {
    $txid = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
}

// Get custom_id (should be invoice_id from cart.php)
$custom_id = $capture['purchase_units'][0]['custom_id'] ?? null;
$captureStatus = $capture['status'] ?? null;

if ($captureStatus === 'COMPLETED' && $custom_id) {
    // Connect to database using mysqli (standalone - no panel dependencies)
    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$db) {
        error_log('capture_order.php: DB connection failed - ' . mysqli_connect_error());
        echo json_encode(['error' => 'db_connection_failed', 'status' => $captureStatus]);
        exit;
    }

    // Get coupon information from session if available
    $applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
    $coupon_id = $applied_coupon ? intval($applied_coupon['coupon_id']) : null;
    
    // Find all invoices with status='due' for this user (cart session)
    // For now, we'll mark ALL due invoices for the logged-in user as paid
    // TODO: Improve to match specific invoice_id from custom_id if cart sends it
    session_start();
    // Check both website_user_id and user_id for compatibility
    $user_id = isset($_SESSION['website_user_id']) ? intval($_SESSION['website_user_id']) : 
               (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);
    
    if ($user_id > 0) {
        // Mark all due invoices for this user as paid, including coupon_id if applicable
        $now = date('Y-m-d H:i:s');
        $esc_txid = mysqli_real_escape_string($db, $txid);
        
        $updateInvoices = "UPDATE {$table_prefix}billing_invoices 
                          SET status='paid', paid_date='$now', payment_txid='$esc_txid', payment_method='paypal'";
        if ($coupon_id) {
            $updateInvoices .= ", coupon_id=$coupon_id";
        }
        $updateInvoices .= " WHERE user_id=$user_id AND status='due'";
        mysqli_query($db, $updateInvoices);
        
        // Update coupon usage count if a coupon was applied
        if ($coupon_id) {
            $updateCoupon = "UPDATE {$table_prefix}billing_coupons 
                            SET current_uses = current_uses + 1 
                            WHERE coupon_id = $coupon_id";
            mysqli_query($db, $updateCoupon);
            
            // Clear coupon from session after use (for one-time coupons)
            if ($applied_coupon && $applied_coupon['usage_type'] === 'one_time') {
                unset($_SESSION['applied_coupon']);
            }
        }
        
        // Get all invoices we just marked paid
        $getInvoices = "SELECT * FROM {$table_prefix}billing_invoices WHERE user_id=$user_id AND payment_txid='$esc_txid'";
        $invoicesResult = mysqli_query($db, $getInvoices);
        
        // For each invoice, either create a new order or extend existing one (renewal)
        while ($inv = mysqli_fetch_assoc($invoicesResult)) {
            $invoice_id = intval($inv['invoice_id']);
            $existing_order_id = intval($inv['order_id'] ?? 0);
            $service_id = intval($inv['service_id']);
            $home_name = mysqli_real_escape_string($db, $inv['home_name']);
            $ip = intval($inv['ip']);
            $max_players = intval($inv['max_players']);
            $qty = intval($inv['qty']);
            $duration = mysqli_real_escape_string($db, $inv['invoice_duration']);
            $amount = floatval($inv['amount']);
            $discount_amount = floatval($inv['discount_amount'] ?? 0);
            $rcon_pw = mysqli_real_escape_string($db, $inv['remote_control_password']);
            $ftp_pw = mysqli_real_escape_string($db, $inv['ftp_password']);
            $inv_coupon_id = intval($inv['coupon_id'] ?? 0);
            
            // Check if this is a renewal (existing order_id > 0) or new order (order_id = 0)
            if ($existing_order_id > 0) {
                // RENEWAL: Extend the existing order's end_date
                // Calculate months to add based on qty and duration
                $months = 0;
                $q = intval($qty);
                $invdur = strtolower(trim($duration));
                if (strpos($invdur, 'year') !== false) {
                    $months = $q * 12;
                } else {
                    // default to months for anything else (month, monthly, etc.)
                    $months = $q;
                }
                
                // Get current end_date and extend it
                $getEndDate = "SELECT end_date FROM {$table_prefix}billing_orders WHERE order_id=$existing_order_id LIMIT 1";
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
                    
                    // Update order with new end_date and mark as paid/active
                    $updateOrder = "UPDATE {$table_prefix}billing_orders 
                                   SET end_date='$new_end_date', status='paid', payment_txid='$esc_txid', paid_ts='$now'
                                   WHERE order_id=$existing_order_id";
                    if (mysqli_query($db, $updateOrder)) {
                        error_log("capture_order.php: Extended order $existing_order_id end_date to $new_end_date for invoice $invoice_id");
                    } else {
                        error_log("capture_order.php: Failed to extend order $existing_order_id: " . mysqli_error($db));
                    }
                }
            } else {
                // NEW ORDER: Create a new order record
                // Calculate end_date based on qty * duration
                $end_date = date('Y-m-d H:i:s', strtotime("+$qty $duration"));
                
                // Insert order with coupon_id and discount_amount
                $insertOrder = "INSERT INTO {$table_prefix}billing_orders (
                    user_id, service_id, home_name, ip, max_players, qty, invoice_duration,
                    price, discount_amount, remote_control_password, ftp_password, status, order_date, end_date,
                    payment_txid, paid_ts" . ($inv_coupon_id ? ", coupon_id" : "") . "
                ) VALUES (
                    $user_id, $service_id, '$home_name', $ip, $max_players, $qty, '$duration',
                    $amount, $discount_amount, '$rcon_pw', '$ftp_pw', 'paid', '$now', '$end_date',
                    '$esc_txid', '$now'" . ($inv_coupon_id ? ", $inv_coupon_id" : "") . "
                )";
                
                if (mysqli_query($db, $insertOrder)) {
                    $new_order_id = mysqli_insert_id($db);
                    
                    // Link invoice to order
                    $linkInvoice = "UPDATE {$table_prefix}billing_invoices SET order_id=$new_order_id WHERE invoice_id=$invoice_id";
                    mysqli_query($db, $linkInvoice);
                    
                    error_log("capture_order.php: Created order $new_order_id for invoice $invoice_id");
                } else {
                    error_log("capture_order.php: Failed to create order for invoice $invoice_id: " . mysqli_error($db));
                }
            }
        }
        
        mysqli_close($db);
    }
}

// Return the full PayPal response (normalized JSON) for proper processing
echo json_encode($capture);

?>
