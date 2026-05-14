<?php
/**
 * PayPal Create Order API Endpoint
 * Uses PayPalGateway class. Credentials come from config — NOT hardcoded here.
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/config_loader.php';
require_once __DIR__ . '/../classes/PaymentGatewayInterface.php';
require_once __DIR__ . '/../classes/PayPalGateway.php';
require_once __DIR__ . '/../classes/GatewayFactory.php';

// Logging
$logDir  = __DIR__ . '/../logs';
@mkdir($logDir, 0755, true);
$logFile   = $logDir . '/paypal_create_order.log';
$requestId = uniqid('req_', true);

function co_log(string $label, $data): void {
    global $logFile, $requestId;
    $entry  = '[' . date('Y-m-d H:i:s') . "] [$requestId] $label\n";
    $entry .= is_array($data) || is_object($data) ? print_r($data, true) : (string)$data;
    $entry .= "\n" . str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

header('Content-Type: application/json');

$rawInput = file_get_contents('php://input');
$in       = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE || !$in) {
    http_response_code(400);
    echo json_encode([
        'success'    => false,
        'error_code' => 'invalid_json',
        'message'    => 'Invalid JSON in request body.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}

co_log('REQUEST', ['amount' => $in['amount'] ?? null, 'invoice_id' => $in['invoice_id'] ?? null]);

// Resolve site base for return/cancel URLs
$siteBase  = rtrim($GLOBALS['SITE_BASE_URL'] ?? '', '/');
$returnUrl = $in['return_url'] ?? '/payment_success.php';
$cancelUrl = $in['cancel_url'] ?? '/payment_cancel.php';

// Ensure absolute URLs
if (strpos($returnUrl, 'http') !== 0) {
    $returnUrl = $siteBase . '/' . ltrim($returnUrl, '/');
}
if (strpos($cancelUrl, 'http') !== 0) {
    $cancelUrl = $siteBase . '/' . ltrim($cancelUrl, '/');
}

// Build gateway params
$params = [
    'amount'      => $in['amount'] ?? '0.00',
    'currency'    => $in['currency'] ?? 'USD',
    'invoice_id'  => $in['invoice_id'] ?? null,
    'custom_id'   => $in['custom_id'] ?? $in['invoice_id'] ?? null,
    'description' => $in['description'] ?? 'Game Server Order',
    'return_url'  => $returnUrl,
    'cancel_url'  => $cancelUrl,
    'items'       => $in['items'] ?? null,
];

try {
    $gateway = GatewayFactory::make('paypal');
    $result  = $gateway->createPayment($params);
} catch (Exception $e) {
    co_log('EXCEPTION', $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'success'    => false,
        'error_code' => 'gateway_error',
        'message'    => $e->getMessage(),
        'debug_id'   => null,
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}

if (!$result['success']) {
    co_log('CREATE_FAILED', $result);
    http_response_code(500);
    echo json_encode([
        'success'    => false,
        'error_code' => $result['error'] ?? 'create_failed',
        'message'    => $result['message'] ?? 'Failed to create PayPal order.',
        'debug_id'   => $result['debug_id'] ?? null,
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}

co_log('CREATE_SUCCESS', ['provider_order_id' => $result['provider_order_id']]);
echo json_encode($result['raw_response']);
