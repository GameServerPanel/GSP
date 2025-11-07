<?php
/**
 * PayPal Order Capture Endpoint
 * Processes PayPal payment, marks invoices paid, creates order records
 * Standalone billing module - uses only standard PHP mysqli
 */

require_once(__DIR__ . '/../includes/config.inc.php');

// Prevent any output before JSON
ob_start();
ini_set('display_errors', '0');
error_reporting(E_ALL);

// Setup logging
$logDir = __DIR__ . '/../logs';
@mkdir($logDir, 0755, true);
$logFile = $logDir . '/payment_capture.log';
$requestId = uniqid('req_', true);

function log_payment($label, $data) {
    global $logFile, $requestId;
    $entry = "[" . date('Y-m-d H:i:s') . "] [$requestId] $label\n";
    $entry .= is_array($data) || is_object($data) ? print_r($data, true) : (string)$data;
    $entry .= "\n" . str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

header('Content-Type: application/json');

// Parse input
$rawInput = file_get_contents('php://input');
$input = json_decode($rawInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    log_payment('JSON_ERROR', json_last_error_msg());
    ob_clean();
    echo json_encode(['error' => 'invalid_json', 'request_id' => $requestId]);
    exit;
}

$paypal_order_id = $input['order_id'] ?? null;
if (!$paypal_order_id) {
    log_payment('MISSING_ORDER_ID', $input);
    ob_clean();
    echo json_encode(['error' => 'missing_order_id', 'request_id' => $requestId]);
    exit;
}

log_payment('REQUEST_START', ['order_id' => $paypal_order_id]);

// PayPal API configuration
$sandbox = true;
$client_id = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';
$api = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

// Get OAuth token
$ch = curl_init("$api/v1/oauth2/token");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
    CURLOPT_HTTPHEADER => ['Accept: application/json'],
    CURLOPT_USERPWD => "$client_id:$client_secret",
]);
$tokenResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200) {
    log_payment('OAUTH_FAILED', ['http_code' => $httpCode, 'response' => $tokenResponse]);
    ob_clean();
    echo json_encode(['error' => 'oauth_failed', 'request_id' => $requestId]);
    exit;
}

$tokenData = json_decode($tokenResponse, true);
$accessToken = $tokenData['access_token'] ?? null;

if (!$accessToken) {
    log_payment('NO_ACCESS_TOKEN', $tokenData);
    ob_clean();
    echo json_encode(['error' => 'no_access_token', 'request_id' => $requestId]);
    exit;
}

// Capture the PayPal order
$ch = curl_init("$api/v2/checkout/orders/$paypal_order_id/capture");
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        "Authorization: Bearer $accessToken"
    ],
]);
$captureResponse = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 201 && $httpCode !== 200) {
    log_payment('CAPTURE_FAILED', ['http_code' => $httpCode, 'response' => substr($captureResponse, 0, 500)]);
    ob_clean();
    echo json_encode(['error' => 'capture_failed', 'http_code' => $httpCode, 'request_id' => $requestId]);
    exit;
}

$captureData = json_decode($captureResponse, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    log_payment('CAPTURE_JSON_ERROR', json_last_error_msg());
    ob_clean();
    echo json_encode(['error' => 'capture_json_error', 'request_id' => $requestId]);
    exit;
}

$status = $captureData['status'] ?? null;
if ($status !== 'COMPLETED') {
    log_payment('NOT_COMPLETED', ['status' => $status]);
    ob_clean();
    echo json_encode(['error' => 'payment_not_completed', 'status' => $status, 'request_id' => $requestId]);
    exit;
}

// Extract transaction ID
$txid = $captureData['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
$payer_email = $captureData['payer']['email_address'] ?? '';
$payer_name = ($captureData['payer']['name']['given_name'] ?? '') . ' ' . ($captureData['payer']['name']['surname'] ?? '');

// Store full PayPal response as JSON for admin/refund tracking
$paypal_json = json_encode($captureData);

log_payment('PAYMENT_CAPTURED', [
    'txid' => $txid,
    'payer_email' => $payer_email,
    'payer_name' => trim($payer_name)
]);

// Start session to get user_id
session_start();
$user_id = isset($_SESSION['website_user_id']) ? intval($_SESSION['website_user_id']) : 
           (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);

if ($user_id <= 0) {
    log_payment('NO_USER_SESSION', $_SESSION);
    ob_clean();
    echo json_encode(['error' => 'no_user_session', 'request_id' => $requestId]);
    exit;
}

// Connect to database
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    log_payment('DB_CONNECTION_FAILED', mysqli_connect_error());
    ob_clean();
    echo json_encode(['error' => 'db_connection_failed', 'request_id' => $requestId]);
    exit;
}

$now = date('Y-m-d H:i:s');
$esc_txid = mysqli_real_escape_string($db, $txid);
$esc_paypal_json = mysqli_real_escape_string($db, $paypal_json);

// Apply coupon from session to invoices before marking paid
session_start();
$coupon_id = isset($_SESSION['cart_coupon_id']) ? intval($_SESSION['cart_coupon_id']) : 0;
if ($coupon_id > 0) {
    // Get unpaid invoices for this user to apply coupon
    $invoices_query = "SELECT invoice_id, amount FROM {$table_prefix}billing_invoices 
                      WHERE user_id=$user_id AND status='due'";
    $invoices_result = mysqli_query($db, $invoices_query);
    
    // Get coupon details
    $coupon_query = "SELECT discount_percent FROM {$table_prefix}billing_coupons 
                    WHERE coupon_id=$coupon_id AND is_active=1 LIMIT 1";
    $coupon_result = mysqli_query($db, $coupon_query);
    
    if ($coupon_result && mysqli_num_rows($coupon_result) === 1) {
        $coupon_row = mysqli_fetch_assoc($coupon_result);
        $discount_percent = floatval($coupon_row['discount_percent']);
        
        // Update each invoice with coupon
        while ($inv_row = mysqli_fetch_assoc($invoices_result)) {
            $inv_id = intval($inv_row['invoice_id']);
            $inv_amount = floatval($inv_row['amount']);
            $discount_amt = $inv_amount * ($discount_percent / 100);
            $new_amount = $inv_amount - $discount_amt;
            
            $update_coupon_sql = "UPDATE {$table_prefix}billing_invoices 
                                 SET coupon_id=$coupon_id, 
                                     discount_amount=" . number_format($discount_amt, 2, '.', '') . ",
                                     amount=" . number_format($new_amount, 2, '.', '') . "
                                 WHERE invoice_id=$inv_id";
            mysqli_query($db, $update_coupon_sql);
            log_payment('COUPON_APPLIED', ['invoice_id' => $inv_id, 'discount' => $discount_amt]);
        }
        
        // Increment coupon usage
        $update_usage_sql = "UPDATE {$table_prefix}billing_coupons 
                            SET current_uses = current_uses + 1 
                            WHERE coupon_id=$coupon_id";
        mysqli_query($db, $update_usage_sql);
        
        // Clear coupon from session
        unset($_SESSION['cart_coupon_code']);
        unset($_SESSION['cart_coupon_id']);
    }
}

// Mark all due invoices for this user as paid
$updateInvoicesSql = "UPDATE {$table_prefix}billing_invoices 
                      SET status='paid', paid_date='$now', payment_txid='$esc_txid', payment_method='paypal'
                      WHERE user_id=$user_id AND status='due'";

log_payment('UPDATE_INVOICES_SQL', $updateInvoicesSql);
$updateResult = mysqli_query($db, $updateInvoicesSql);

if (!$updateResult) {
    log_payment('UPDATE_INVOICES_FAILED', mysqli_error($db));
    mysqli_close($db);
    ob_clean();
    echo json_encode(['error' => 'update_invoices_failed', 'request_id' => $requestId]);
    exit;
}

$affectedInvoices = mysqli_affected_rows($db);
log_payment('INVOICES_MARKED_PAID', ['count' => $affectedInvoices]);

// Get all invoices we just marked paid
$getInvoicesSql = "SELECT * FROM {$table_prefix}billing_invoices 
                   WHERE user_id=$user_id AND payment_txid='$esc_txid'";
$invoicesResult = mysqli_query($db, $getInvoicesSql);

$ordersCreated = 0;
while ($inv = mysqli_fetch_assoc($invoicesResult)) {
    $invoice_id = intval($inv['invoice_id']);
    $existing_order_id = intval($inv['order_id'] ?? 0);
    
    // Skip if invoice already linked to an order (renewal)
    if ($existing_order_id > 0) {
        log_payment('RENEWAL_INVOICE', ['invoice_id' => $invoice_id, 'order_id' => $existing_order_id]);
        continue;
    }
    
    // Create new order
    $service_id = intval($inv['service_id']);
    $home_name = mysqli_real_escape_string($db, $inv['home_name']);
    $ip = intval($inv['ip']);
    $max_players = intval($inv['max_players']);
    $qty = intval($inv['qty']);
    $duration = mysqli_real_escape_string($db, $inv['invoice_duration']);
    $amount = floatval($inv['amount']);
    $rcon_pw = mysqli_real_escape_string($db, $inv['remote_control_password']);
    $ftp_pw = mysqli_real_escape_string($db, $inv['ftp_password']);
    
    // Calculate end_date
    $end_date = date('Y-m-d H:i:s', strtotime("+$qty $duration"));
    
    // Insert order with status='paid' (panel will provision and change to 'active')
    $insertOrderSql = "INSERT INTO {$table_prefix}billing_orders (
        user_id, service_id, home_name, ip, max_players, qty, invoice_duration,
        price, remote_control_password, ftp_password, status, order_date, end_date,
        payment_txid, paid_ts, paypal_data
    ) VALUES (
        $user_id, $service_id, '$home_name', $ip, $max_players, $qty, '$duration',
        $amount, '$rcon_pw', '$ftp_pw', 'paid', '$now', '$end_date',
        '$esc_txid', '$now', '$esc_paypal_json'
    )";
    
    log_payment('INSERT_ORDER_SQL', substr($insertOrderSql, 0, 300));
    
    if (mysqli_query($db, $insertOrderSql)) {
        $new_order_id = mysqli_insert_id($db);
        log_payment('ORDER_CREATED', ['order_id' => $new_order_id, 'invoice_id' => $invoice_id]);
        
        // Link invoice to order
        $linkSql = "UPDATE {$table_prefix}billing_invoices SET order_id=$new_order_id WHERE invoice_id=$invoice_id";
        mysqli_query($db, $linkSql);
        
        $ordersCreated++;
    } else {
        log_payment('INSERT_ORDER_FAILED', mysqli_error($db));
    }
}

mysqli_close($db);

log_payment('PROCESSING_COMPLETE', [
    'invoices_paid' => $affectedInvoices,
    'orders_created' => $ordersCreated,
    'txid' => $txid
]);

// Return success response
ob_clean();
echo json_encode([
    'status' => 'COMPLETED',
    'order_id' => $paypal_order_id,
    'txid' => $txid,
    'invoices_paid' => $affectedInvoices,
    'orders_created' => $ordersCreated
]);
