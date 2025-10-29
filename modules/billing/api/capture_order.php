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

// Read and parse input
$rawInput = file_get_contents('php://input');
capture_log('RAW_INPUT', substr($rawInput, 0, 1000));

$in = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    capture_log('JSON_DECODE_ERROR', [
        'error' => json_last_error_msg(),
        'raw_input_length' => strlen($rawInput),
        'raw_input_preview' => substr($rawInput, 0, 500)
    ]);
    http_response_code(400);
    echo json_encode(['error' => 'invalid_json', 'message' => json_last_error_msg(), 'request_id' => $requestId]);
    exit;
}

if (!$in) {
    $in = [];
}

$order_id = $in['order_id'] ?? null;
capture_log('PARSED_INPUT', ['order_id' => $order_id]);

if (!$order_id) {
    capture_log('MISSING_ORDER_ID', ['input' => $in]);
    http_response_code(400);
    echo json_encode(['error' => 'missing_order_id', 'request_id' => $requestId]);
    exit;
}

$api = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';

capture_log('PAYPAL_API_CONFIG', [
    'sandbox_mode' => $sandbox,
    'api_base' => $api,
    'has_client_id' => !empty($client_id),
    'has_client_secret' => !empty($client_secret)
]);

// Step 1: Get OAuth token
capture_log('OAUTH_REQUEST_START', ['endpoint' => "$api/v1/oauth2/token"]);

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
$oauth_curl_errno = curl_errno($ch);
$oauth_curl_error = curl_error($ch);
curl_close($ch);

capture_log('OAUTH_RESPONSE', [
    'http_code' => $http,
    'curl_errno' => $oauth_curl_errno,
    'curl_error' => $oauth_curl_error,
    'response_length' => strlen($tok),
    'response_preview' => substr($tok, 0, 200)
]);

if ($oauth_curl_errno !== 0) {
    capture_log('OAUTH_CURL_ERROR', ['errno' => $oauth_curl_errno, 'error' => $oauth_curl_error]);
    http_response_code(502);
    echo json_encode(['error' => 'oauth_curl_fail', 'details' => $oauth_curl_error, 'request_id' => $requestId]);
    exit;
}

if ($http !== 200) {
    capture_log('OAUTH_HTTP_ERROR', ['http_code' => $http, 'response' => $tok]);
    http_response_code(500);
    echo json_encode(['error' => 'oauth_fail', 'http_code' => $http, 'request_id' => $requestId]);
    exit;
}

$access = json_decode($tok, true)['access_token'] ?? null;
if (!$access) {
    capture_log('OAUTH_NO_TOKEN', ['response' => $tok]);
    http_response_code(500);
    echo json_encode(['error' => 'oauth_no_token', 'request_id' => $requestId]);
    exit;
}

capture_log('OAUTH_SUCCESS', ['token_length' => strlen($access)]);

// Step 2: Capture the PayPal order
capture_log('CAPTURE_REQUEST_START', [
    'endpoint' => "$api/v2/checkout/orders/$order_id/capture",
    'order_id' => $order_id
]);

$ch = curl_init("$api/v2/checkout/orders/$order_id/capture");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_HTTPHEADER => [ 'Content-Type: application/json', 'Authorization: Bearer ' . $access ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_err = curl_error($ch);
$curl_errno = curl_errno($ch);
curl_close($ch);

// Log the raw curl response for debugging
capture_log('CAPTURE_RESPONSE', [
    'http_code' => $http,
    'curl_errno' => $curl_errno,
    'curl_error' => $curl_err,
    'response_length' => strlen($res),
    'response_preview' => substr($res, 0, 1000)
]);

// Check for curl-level errors
if ($curl_errno !== 0) {
    capture_log('CAPTURE_CURL_ERROR', ['errno' => $curl_errno, 'error' => $curl_err]);
    http_response_code(502);
    $out = ['error' => 'capture_curl_fail', 'details' => $curl_err, 'request_id' => $requestId];
    echo json_encode($out);
    exit;
}

// Normalize response: ensure we always return valid JSON to the caller
if ($res === false || $res === '') {
    // Curl-level failure or empty body
    capture_log('CAPTURE_EMPTY_RESPONSE', ['http' => $http]);
    http_response_code(502);
    $out = ['error' => 'paypal_empty_response', 'http' => $http, 'request_id' => $requestId];
    echo json_encode($out);
    exit;
}

// Attempt to decode PayPal response
$capture = json_decode($res, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    // PayPal returned non-JSON / malformed response — return it as raw string inside JSON
    capture_log('CAPTURE_INVALID_JSON', [
        'json_error' => json_last_error_msg(),
        'http' => $http,
        'raw_preview' => substr($res, 0, 500)
    ]);
    http_response_code(502);
    $out = ['error' => 'paypal_invalid_json', 'http' => $http, 'request_id' => $requestId];
    echo json_encode($out);
    exit;
}

if ($http !== 201 && $http !== 200) {
    capture_log('CAPTURE_HTTP_ERROR', [
        'http_code' => $http,
        'response' => $capture
    ]);
    http_response_code($http);
    // Return structured JSON with PayPal's decoded response
    $out = ['error' => 'paypal_capture_failed', 'http' => $http, 'paypal_error' => $capture, 'request_id' => $requestId];
    echo json_encode($out);
    exit;
}

// Extract payment details
$txid = null;
capture_log('CAPTURE_SUCCESS', [
    'status' => $capture['status'] ?? 'UNKNOWN',
    'id' => $capture['id'] ?? 'UNKNOWN'
]);

if (isset($capture['purchase_units'][0]['payments']['captures'][0])) {
    $txid = $capture['purchase_units'][0]['payments']['captures'][0]['id'] ?? null;
}

// Get custom_id (should be invoice_id from cart.php)
$custom_id = $capture['purchase_units'][0]['custom_id'] ?? null;
$captureStatus = $capture['status'] ?? null;

capture_log('PAYMENT_DETAILS', [
    'txid' => $txid,
    'custom_id' => $custom_id,
    'status' => $captureStatus
]);

if ($captureStatus === 'COMPLETED' && $custom_id) {
    capture_log('STARTING_DB_PROCESSING', ['custom_id' => $custom_id, 'status' => $captureStatus]);
    
    // Connect to database using mysqli (standalone - no panel dependencies)
    $db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if (!$db) {
        $dbError = mysqli_connect_error();
        capture_log('DB_CONNECTION_FAILED', [
            'error' => $dbError,
            'host' => $db_host,
            'db_name' => $db_name
        ]);
        error_log('capture_order.php: DB connection failed - ' . $dbError);
        echo json_encode(['error' => 'db_connection_failed', 'status' => $captureStatus, 'request_id' => $requestId]);
        exit;
    }
    
    capture_log('DB_CONNECTED', ['database' => $db_name]);

    // Get coupon information from session if available
    session_start();
    $applied_coupon = isset($_SESSION['applied_coupon']) ? $_SESSION['applied_coupon'] : null;
    $coupon_id = $applied_coupon ? intval($applied_coupon['coupon_id']) : null;
    
    // Check both website_user_id and user_id for compatibility
    $user_id = isset($_SESSION['website_user_id']) ? intval($_SESSION['website_user_id']) : 
               (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);
    
    capture_log('SESSION_INFO', [
        'user_id' => $user_id,
        'coupon_id' => $coupon_id,
        'session_keys' => array_keys($_SESSION)
    ]);
    
    if ($user_id > 0) {
        capture_log('PROCESSING_INVOICES', ['user_id' => $user_id, 'custom_id' => $custom_id]);
        // Mark all due invoices for this user as paid, including coupon_id if applicable
        $now = date('Y-m-d H:i:s');
        $esc_txid = mysqli_real_escape_string($db, $txid);
        
        $updateInvoices = "UPDATE {$table_prefix}billing_invoices 
                          SET status='paid', paid_date='$now', payment_txid='$esc_txid', payment_method='paypal'";
        if ($coupon_id) {
            $updateInvoices .= ", coupon_id=$coupon_id";
        }
        $updateInvoices .= " WHERE user_id=$user_id AND status='due'";
        
        capture_log('UPDATE_INVOICES_QUERY', ['sql' => $updateInvoices]);
        
        $updateResult = mysqli_query($db, $updateInvoices);
        if (!$updateResult) {
            capture_log('UPDATE_INVOICES_FAILED', ['error' => mysqli_error($db)]);
        } else {
            $affectedRows = mysqli_affected_rows($db);
            capture_log('UPDATE_INVOICES_SUCCESS', ['affected_rows' => $affectedRows]);
        }
        
        // Update coupon usage count if a coupon was applied
        if ($coupon_id) {
            $updateCoupon = "UPDATE {$table_prefix}billing_coupons 
                            SET current_uses = current_uses + 1 
                            WHERE coupon_id = $coupon_id";
            capture_log('UPDATE_COUPON_QUERY', ['sql' => $updateCoupon]);
            
            $couponResult = mysqli_query($db, $updateCoupon);
            if (!$couponResult) {
                capture_log('UPDATE_COUPON_FAILED', ['error' => mysqli_error($db)]);
            } else {
                capture_log('UPDATE_COUPON_SUCCESS', ['affected_rows' => mysqli_affected_rows($db)]);
            }
            
            // Clear coupon from session after use (for one-time coupons)
            if ($applied_coupon && $applied_coupon['usage_type'] === 'one_time') {
                unset($_SESSION['applied_coupon']);
                capture_log('COUPON_CLEARED', ['type' => 'one_time']);
            }
        }
        
        // Get all invoices we just marked paid
        $getInvoices = "SELECT * FROM {$table_prefix}billing_invoices WHERE user_id=$user_id AND payment_txid='$esc_txid'";
        capture_log('GET_INVOICES_QUERY', ['sql' => $getInvoices]);
        
        $invoicesResult = mysqli_query($db, $getInvoices);
        if (!$invoicesResult) {
            capture_log('GET_INVOICES_FAILED', ['error' => mysqli_error($db)]);
        } else {
            $invoiceCount = mysqli_num_rows($invoicesResult);
            capture_log('GET_INVOICES_SUCCESS', ['count' => $invoiceCount]);
        }
        
        // For each invoice, either create a new order or extend existing one (renewal)
        $processedInvoices = 0;
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
            
            capture_log('PROCESSING_INVOICE', [
                'invoice_id' => $invoice_id,
                'existing_order_id' => $existing_order_id,
                'service_id' => $service_id,
                'home_name' => $home_name,
                'amount' => $amount
            ]);
            
            // Check if this is a renewal (existing order_id > 0) or new order (order_id = 0)
            if ($existing_order_id > 0) {
                capture_log('RENEWAL_DETECTED', ['order_id' => $existing_order_id, 'invoice_id' => $invoice_id]);
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
                    capture_log('UPDATE_ORDER_QUERY', ['sql' => $updateOrder]);
                    
                    if (mysqli_query($db, $updateOrder)) {
                        capture_log('ORDER_EXTENDED_SUCCESS', [
                            'order_id' => $existing_order_id,
                            'new_end_date' => $new_end_date,
                            'invoice_id' => $invoice_id
                        ]);
                        error_log("capture_order.php: Extended order $existing_order_id end_date to $new_end_date for invoice $invoice_id");
                        $processedInvoices++;
                    } else {
                        $dbError = mysqli_error($db);
                        capture_log('ORDER_EXTENDED_FAILED', [
                            'order_id' => $existing_order_id,
                            'error' => $dbError
                        ]);
                        error_log("capture_order.php: Failed to extend order $existing_order_id: " . $dbError);
                    }
                }
            } else {
                capture_log('NEW_ORDER_DETECTED', ['invoice_id' => $invoice_id]);
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
                
                capture_log('INSERT_ORDER_QUERY', ['sql' => substr($insertOrder, 0, 500)]);
                
                if (mysqli_query($db, $insertOrder)) {
                    $new_order_id = mysqli_insert_id($db);
                    
                    capture_log('ORDER_CREATED_SUCCESS', [
                        'new_order_id' => $new_order_id,
                        'invoice_id' => $invoice_id
                    ]);
                    
                    // Link invoice to order
                    $linkInvoice = "UPDATE {$table_prefix}billing_invoices SET order_id=$new_order_id WHERE invoice_id=$invoice_id";
                    capture_log('LINK_INVOICE_QUERY', ['sql' => $linkInvoice]);
                    
                    if (mysqli_query($db, $linkInvoice)) {
                        capture_log('INVOICE_LINKED_SUCCESS', ['invoice_id' => $invoice_id, 'order_id' => $new_order_id]);
                    } else {
                        capture_log('INVOICE_LINK_FAILED', ['error' => mysqli_error($db)]);
                    }
                    
                    error_log("capture_order.php: Created order $new_order_id for invoice $invoice_id");
                    $processedInvoices++;
                } else {
                    $dbError = mysqli_error($db);
                    capture_log('ORDER_CREATE_FAILED', [
                        'invoice_id' => $invoice_id,
                        'error' => $dbError
                    ]);
                    error_log("capture_order.php: Failed to create order for invoice $invoice_id: " . $dbError);
                }
            }
        }
        
        capture_log('PROCESSING_COMPLETE', [
            'processed_invoices' => $processedInvoices,
            'user_id' => $user_id
        ]);
        
        mysqli_close($db);
    } else {
        capture_log('NO_USER_ID', ['session_data' => $_SESSION]);
    }
} else {
    capture_log('SKIP_PROCESSING', [
        'captureStatus' => $captureStatus,
        'custom_id' => $custom_id,
        'reason' => !$captureStatus ? 'no_status' : (!$custom_id ? 'no_custom_id' : 'status_not_completed')
    ]);
}

// Return the full PayPal response (normalized JSON) for proper processing
capture_log('REQUEST_COMPLETE', ['returning_status' => $captureStatus]);
echo json_encode($capture);

?>
