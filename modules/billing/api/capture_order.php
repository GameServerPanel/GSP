<?php
/**
 * PayPal Order Capture Endpoint
 * Processes PayPal payment, marks invoices paid, creates order records
 * Standalone billing module - uses only standard PHP mysqli
 */

require_once(__DIR__ . '/../includes/config_loader.php');

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

// Start session to get user_id (use billing website session name)
if (session_status() === PHP_SESSION_NONE) {
    session_name("opengamepanel_web");
    session_start();
}
$user_id = isset($_SESSION['website_user_id']) ? intval($_SESSION['website_user_id']) : 
           (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);

if ($user_id <= 0) {
    log_payment('NO_USER_SESSION', $_SESSION);
    ob_clean();
    echo json_encode(['error' => 'no_user_session', 'request_id' => $requestId]);
    exit;
}

// Connect to database
$mysqli = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$mysqli) {
    log_payment('DB_CONNECTION_FAILED', mysqli_connect_error());
    ob_clean();
    echo json_encode(['error' => 'db_connection_failed', 'request_id' => $requestId]);
    exit;
}

$now = date('Y-m-d H:i:s');
$esc_txid = mysqli_real_escape_string($mysqli, $txid);
$esc_paypal_json = mysqli_real_escape_string($mysqli, $paypal_json);

// Apply coupon from session to invoices before marking paid
session_start();
$coupon_id = isset($_SESSION['cart_coupon_id']) ? intval($_SESSION['cart_coupon_id']) : 0;
if ($coupon_id > 0) {
    // Get unpaid invoices for this user to apply coupon
    $invoices_query = "SELECT invoice_id, amount FROM {$table_prefix}billing_invoices 
                      WHERE user_id=$user_id AND status='due'";
    $invoices_result = mysqli_query($mysqli, $invoices_query);
    
    // Get coupon details
    $coupon_query = "SELECT discount_percent FROM {$table_prefix}billing_coupons 
                    WHERE coupon_id=$coupon_id AND is_active=1 LIMIT 1";
    $coupon_result = mysqli_query($mysqli, $coupon_query);
    
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
            mysqli_query($mysqli, $update_coupon_sql);
            log_payment('COUPON_APPLIED', ['invoice_id' => $inv_id, 'discount' => $discount_amt]);
        }
        
        // Increment coupon usage
        $update_usage_sql = "UPDATE {$table_prefix}billing_coupons 
                            SET current_uses = current_uses + 1 
                            WHERE coupon_id=$coupon_id";
        mysqli_query($mysqli, $update_usage_sql);
        
        // Clear coupon from session
        unset($_SESSION['cart_coupon_code']);
        unset($_SESSION['cart_coupon_id']);
    }
}

// Mark all due invoices for this user as paid.
// Note: billing_invoices is the pre-purchase cart table and uses its own
// status vocabulary (due -> paid). This is separate from gsp_invoices
// (renewal invoices) and server_homes.billing_status (Active/Invoiced/Expired).
$updateInvoicesSql = "UPDATE {$table_prefix}billing_invoices 
                      SET status='paid', paid_date='$now', payment_txid='$esc_txid', payment_method='paypal'
                      WHERE user_id=$user_id AND status='due'";
$updateResult = mysqli_query($mysqli, $updateInvoicesSql);

if (!$updateResult) {
    log_payment('UPDATE_INVOICES_FAILED', mysqli_error($mysqli));
    mysqli_close($mysqli);
    ob_clean();
    echo json_encode(['error' => 'update_invoices_failed', 'request_id' => $requestId]);
    exit;
}

$affectedInvoices = mysqli_affected_rows($mysqli);
log_payment('INVOICES_MARKED_PAID', ['count' => $affectedInvoices]);

// Get all invoices we just marked paid
$getInvoicesSql = "SELECT * FROM {$table_prefix}billing_invoices 
                   WHERE user_id=$user_id AND payment_txid='$esc_txid'";
$invoicesResult = mysqli_query($mysqli, $getInvoicesSql);

$ordersCreated = 0;
$renewedOrders = 0;
$newOrderIds = [];
while ($inv = mysqli_fetch_assoc($invoicesResult)) {
    $invoice_id = intval($inv['invoice_id']);
    $existing_order_id = intval($inv['order_id'] ?? 0);
    
    // Handle renewals by extending the existing order
    if ($existing_order_id > 0) {
        $durationUnit = strtolower(trim($inv['invoice_duration'] ?? 'month'));
        $allowedDurations = ['day','month','year'];
        if (!in_array($durationUnit, $allowedDurations, true)) {
            $durationUnit = 'month';
        }
        $qty = max(1, intval($inv['qty'] ?? 1));
        $orderInfoSql = "SELECT end_date FROM {$table_prefix}billing_orders WHERE order_id=$existing_order_id LIMIT 1";
        $orderInfoRes = mysqli_query($mysqli, $orderInfoSql);
        $currentEnd = null;
        if ($orderInfoRes && mysqli_num_rows($orderInfoRes) === 1) {
            $infoRow = mysqli_fetch_assoc($orderInfoRes);
            $currentEnd = $infoRow['end_date'] ?? null;
        }
        $baseTs = time();
        if (!empty($currentEnd)) {
            $parsed = strtotime($currentEnd);
            if ($parsed !== false && $parsed > time()) {
                $baseTs = $parsed;
            }
        }
        $newEndDate = date('Y-m-d H:i:s', strtotime("+$qty $durationUnit", $baseTs));
        $renewSql = "UPDATE {$table_prefix}billing_orders 
                     SET status='Active', end_date='$newEndDate', paid_ts='$now', payment_txid='$esc_txid'
                     WHERE order_id=$existing_order_id LIMIT 1";
        if (mysqli_query($mysqli, $renewSql)) {
            $renewedOrders++;
            log_payment('ORDER_RENEWED', [
                'order_id' => $existing_order_id,
                'invoice_id' => $invoice_id,
                'new_end_date' => $newEndDate
            ]);

            // Also update server_homes.billing_status and next_invoice_date
            $homeIdRow = mysqli_query($mysqli, "SELECT home_id FROM {$table_prefix}billing_orders WHERE order_id=$existing_order_id LIMIT 1");
            if ($homeIdRow && mysqli_num_rows($homeIdRow) === 1) {
                $homeData = mysqli_fetch_assoc($homeIdRow);
                $home_id_upd = intval($homeData['home_id'] ?? 0);
                if ($home_id_upd > 0) {
                    $next_inv_date = mysqli_real_escape_string($mysqli, $newEndDate);
                    mysqli_query($mysqli, "UPDATE {$table_prefix}server_homes
                        SET billing_status        = 'Active',
                            next_invoice_date     = '$next_inv_date',
                            server_expiration_date = NULL
                        WHERE home_id = $home_id_upd");
                    // Mark the matching gsp_invoices renewal invoice as Active
                    mysqli_query($mysqli, "UPDATE {$table_prefix}invoices
                        SET billing_status = 'Active',
                            paid_at        = '$now',
                            payment_id     = '$esc_txid'
                        WHERE home_id = $home_id_upd AND billing_status = 'Invoiced'");
                    log_payment('SERVER_HOME_ACTIVATED', ['home_id' => $home_id_upd]);
                }
            }
        } else {
            log_payment('ORDER_RENEWAL_FAILED', [
                'order_id' => $existing_order_id,
                'invoice_id' => $invoice_id,
                'error' => mysqli_error($mysqli)
            ]);
        }
        continue;
    }
    
    // Create new order
    $service_id = intval($inv['service_id']);
    $home_name = mysqli_real_escape_string($mysqli, $inv['home_name']);
    $ip = intval($inv['ip']);
    $max_players = intval($inv['max_players']);
    $qty = intval($inv['qty']);
    $duration = mysqli_real_escape_string($mysqli, $inv['invoice_duration']);
    $amount = floatval($inv['amount']);
    $rcon_pw = mysqli_real_escape_string($mysqli, $inv['remote_control_password']);
    $ftp_pw = mysqli_real_escape_string($mysqli, $inv['ftp_password']);
    
    // Calculate end_date
    $end_date = date('Y-m-d H:i:s', strtotime("+$qty $duration"));
    
    // Insert order with status='Active' (server will be provisioned automatically)
    $insertOrderSql = "INSERT INTO {$table_prefix}billing_orders (
        user_id, service_id, home_name, ip, max_players, qty, invoice_duration,
        price, remote_control_password, ftp_password, status, order_date, end_date,
        payment_txid, paid_ts, paypal_data
    ) VALUES (
        $user_id, $service_id, '$home_name', $ip, $max_players, $qty, '$duration',
        $amount, '$rcon_pw', '$ftp_pw', 'Active', '$now', '$end_date',
        '$esc_txid', '$now', '$esc_paypal_json'
    )";
    
    log_payment('INSERT_ORDER_SQL', substr($insertOrderSql, 0, 300));
    
    if (mysqli_query($mysqli, $insertOrderSql)) {
        $new_order_id = mysqli_insert_id($mysqli);
        log_payment('ORDER_CREATED', ['order_id' => $new_order_id, 'invoice_id' => $invoice_id]);
        $newOrderIds[] = $new_order_id;
        
        // Link invoice to order
        $linkSql = "UPDATE {$table_prefix}billing_invoices SET order_id=$new_order_id WHERE invoice_id=$invoice_id";
        mysqli_query($mysqli, $linkSql);
        
        $ordersCreated++;
    } else {
        log_payment('INSERT_ORDER_FAILED', mysqli_error($mysqli));
    }
}

mysqli_close($mysqli);

$autoProvisionResult = ['provisioned_count' => 0, 'failed_count' => 0, 'orders' => []];
if (!empty($newOrderIds)) {
    require_once __DIR__ . '/../includes/panel_bridge.php';
    $panelCtx = billing_panel_bootstrap();
    if ($panelCtx && isset($panelCtx['db']) && $panelCtx['db'] instanceof OGPDatabase) {
        $GLOBALS['db'] = $panelCtx['db'];
        $GLOBALS['settings'] = $panelCtx['settings'];
        require_once __DIR__ . '/../create_servers.php';
        $autoProvisionResult = billing_invoke_provision([
            'order_ids' => $newOrderIds,
            'user_id' => $user_id,
            'is_admin' => true
        ]);
        log_payment('AUTO_PROVISION_COMPLETE', $autoProvisionResult);
    } else {
        log_payment('AUTO_PROVISION_SKIPPED', 'panel bootstrap failed');
    }
}

log_payment('PROCESSING_COMPLETE', [
    'invoices_paid' => $affectedInvoices,
    'orders_created' => $ordersCreated,
    'orders_renewed' => $renewedOrders,
    'txid' => $txid
]);

// Return success response
ob_clean();
echo json_encode([
    'status' => 'COMPLETED',
    'order_id' => $paypal_order_id,
    'txid' => $txid,
    'invoices_paid' => $affectedInvoices,
    'orders_created' => $ordersCreated,
    'orders_renewed' => $renewedOrders,
    'provisioned' => $autoProvisionResult['provisioned_count'] ?? 0
]);


