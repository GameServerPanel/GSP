<?php
/**
 * PayPal Create Order API Endpoint
 * Enhanced with comprehensive logging for debugging
 */

// Ensure all errors are logged, not displayed (to prevent JSON corruption)
ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once(__DIR__ . '/../includes/config_loader.php');
// create_order for PayPal — adapted to run from _website/api
$sandbox       = true; // flip to false for Live
$client_id     = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';

// Setup comprehensive logging
$logDir = __DIR__ . '/../logs';
@mkdir($logDir, 0755, true);
$logFile = $logDir . '/paypal_create_order.log';
$requestId = uniqid('req_', true); // Unique request identifier for tracking

function create_order_log($label, $data) {
    global $logFile, $requestId;
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] [$requestId] $label\n";
    if (is_array($data) || is_object($data)) {
        $entry .= print_r($data, true);
    } else {
        $entry .= (string)$data;
    }
    $entry .= "\n" . str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

create_order_log('REQUEST_START', [
    'method' => $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
    'remote_addr' => $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN',
]);

header('Content-Type: application/json');

// Read and parse input
$rawInput = file_get_contents('php://input');
create_order_log('RAW_INPUT', substr($rawInput, 0, 2000)); // Log first 2000 chars

$in = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    create_order_log('JSON_DECODE_ERROR', [
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

$amount_in    = $in['amount'] ?? '0.00';
$currency     = $in['currency'] ?? 'USD';
$invoice_id   = $in['invoice_id'] ?? null;
$custom_id    = $in['custom_id'] ?? null;
$description  = $in['description'] ?? 'Order';
$return_url   = $in['return_url'] ?? null;
$cancel_url   = $in['cancel_url'] ?? null;
$items        = (isset($in['items']) && is_array($in['items'])) ? $in['items'] : null;
$line_invoices= (isset($in['line_invoices']) && is_array($in['line_invoices'])) ? $in['line_invoices'] : null;

create_order_log('PARSED_INPUT', [
    'amount' => $amount_in,
    'currency' => $currency,
    'invoice_id' => $invoice_id,
    'custom_id' => $custom_id,
    'items_count' => $items ? count($items) : 0,
    'line_invoices_count' => $line_invoices ? count($line_invoices) : 0
]);

$amount_value = number_format((float)$amount_in, 2, '.', '');
if ($items) {
  $sum = 0.00;
  foreach ($items as $it) {
    $qty  = isset($it['quantity']) ? (int)$it['quantity'] : 1;
    $val  = isset($it['unit_amount']['value']) ? (float)$it['unit_amount']['value'] : 0.00;
    $sum += $qty * $val;
  }
  $amount_value = number_format($sum, 2, '.', '');
  create_order_log('AMOUNT_CALCULATED', [
    'original_amount' => $amount_in,
    'calculated_from_items' => $amount_value,
    'items_sum' => $sum
  ]);
}

$api = $sandbox ? 'https://api-m.sandbox.paypal.com' : 'https://api-m.paypal.com';
create_order_log('PAYPAL_API_CONFIG', [
    'sandbox_mode' => $sandbox,
    'api_base' => $api,
    'has_client_id' => !empty($client_id),
    'has_client_secret' => !empty($client_secret)
]);

// Step 1: Get OAuth token
create_order_log('OAUTH_REQUEST_START', ['endpoint' => "$api/v1/oauth2/token"]);

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
$curl_errno = curl_errno($ch);
$curl_error = curl_error($ch);
curl_close($ch);

create_order_log('OAUTH_RESPONSE', [
    'http_code' => $http,
    'curl_errno' => $curl_errno,
    'curl_error' => $curl_error,
    'response_length' => strlen($tok),
    'response_preview' => substr($tok, 0, 200)
]);

if ($curl_errno !== 0) {
    create_order_log('OAUTH_CURL_ERROR', ['errno' => $curl_errno, 'error' => $curl_error]);
    http_response_code(502);
    echo json_encode(['error' => 'oauth_curl_fail', 'details' => $curl_error, 'request_id' => $requestId]);
    exit;
}

if ($http !== 200) {
    create_order_log('OAUTH_HTTP_ERROR', ['http_code' => $http, 'response' => $tok]);
    http_response_code(500);
    echo json_encode(['error' => 'oauth_fail', 'http_code' => $http, 'request_id' => $requestId]);
    exit;
}

$access = json_decode($tok, true)['access_token'] ?? null;
if (!$access) {
    create_order_log('OAUTH_NO_TOKEN', ['response' => $tok]);
    http_response_code(500);
    echo json_encode(['error' => 'oauth_no_token', 'request_id' => $requestId]);
    exit;
}

create_order_log('OAUTH_SUCCESS', ['token_length' => strlen($access)]);

// Update site base URL to exclude 'modules/billing'
$siteBaseUrl = 'http://gameservers.world';

create_order_log('URL_PROCESSING_BEFORE', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url,
    'site_base' => $siteBaseUrl
]);

// Ensure return_url and cancel_url are absolute URLs (relative to site root)
if (strpos($return_url, 'http') !== 0) {
    $return_url = $siteBaseUrl . '/' . ltrim($return_url, '/');
}
if (strpos($cancel_url, 'http') !== 0) {
    $cancel_url = $siteBaseUrl . '/' . ltrim($cancel_url, '/');
}

create_order_log('URL_PROCESSING_AFTER', [
    'return_url' => $return_url,
    'cancel_url' => $cancel_url
]);

$purchaseUnit = [
  'amount' => [ 'currency_code' => $currency, 'value' => $amount_value ],
  'description' => $description,
  'invoice_id' => $invoice_id,
  'custom_id'  => $custom_id
];
if ($items) {
  $purchaseUnit['items'] = $items;
  $purchaseUnit['amount']['breakdown'] = [ 'item_total' => ['currency_code'=>$currency,'value'=>$amount_value] ];
}

$body = [
  'intent' => 'CAPTURE',
  'purchase_units' => [ $purchaseUnit ],
  'application_context' => [ 'return_url'=>$return_url, 'cancel_url'=>$cancel_url, 'user_action'=>'PAY_NOW' ]
];

create_order_log('PAYPAL_ORDER_PAYLOAD', $body);

// Step 2: Create PayPal order
create_order_log('CREATE_ORDER_REQUEST_START', ['endpoint' => "$api/v2/checkout/orders"]);

$ch = curl_init("$api/v2/checkout/orders");
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_POST => true,
  CURLOPT_POSTFIELDS => json_encode($body),
  CURLOPT_HTTPHEADER => [ 'Content-Type: application/json', 'Authorization: Bearer ' . $access ],
]);
$res  = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curl_errno = curl_errno($ch);
$curl_error = curl_error($ch);
curl_close($ch);

create_order_log('CREATE_ORDER_RESPONSE', [
    'http_code' => $http,
    'curl_errno' => $curl_errno,
    'curl_error' => $curl_error,
    'response_length' => strlen($res),
    'response' => substr($res, 0, 1000) // First 1000 chars of response
]);

if ($curl_errno !== 0) {
    create_order_log('CREATE_ORDER_CURL_ERROR', ['errno' => $curl_errno, 'error' => $curl_error]);
    http_response_code(502);
    echo json_encode(['error' => 'create_order_curl_fail', 'details' => $curl_error, 'request_id' => $requestId]);
    exit;
}

if ($http !== 201) {
    create_order_log('CREATE_ORDER_HTTP_ERROR', [
        'http_code' => $http,
        'response' => $res,
        'payload_sent' => $body
    ]);
    
    // Try to parse PayPal error response
    $errorData = json_decode($res, true);
    http_response_code($http);
    echo json_encode([
        'error' => 'create_order_failed',
        'http_code' => $http,
        'paypal_error' => $errorData,
        'request_id' => $requestId
    ]);
    exit;
}

// Success - parse and validate response
$orderData = json_decode($res, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    create_order_log('CREATE_ORDER_INVALID_JSON', [
        'json_error' => json_last_error_msg(),
        'response' => $res
    ]);
    http_response_code(502);
    echo json_encode(['error' => 'invalid_paypal_response', 'request_id' => $requestId]);
    exit;
}

create_order_log('CREATE_ORDER_SUCCESS', [
    'order_id' => $orderData['id'] ?? 'UNKNOWN',
    'status' => $orderData['status'] ?? 'UNKNOWN'
]);

echo $res;

?>
