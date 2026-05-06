<?php
/**
 * PayPal Order Capture Endpoint
 * Uses PayPalGateway, BillingService, and BillingRepository.
 * Credentials come from config — NOT hardcoded here.
 */

ini_set('display_errors', '0');
error_reporting(E_ALL);
ob_start();

require_once __DIR__ . '/../includes/config_loader.php';
require_once __DIR__ . '/../classes/PaymentGatewayInterface.php';
require_once __DIR__ . '/../classes/PayPalGateway.php';
require_once __DIR__ . '/../classes/GatewayFactory.php';
require_once __DIR__ . '/../classes/BillingRepository.php';
require_once __DIR__ . '/../classes/BillingService.php';

// Logging setup
$logDir  = __DIR__ . '/../logs';
@mkdir($logDir, 0755, true);
$logFile   = $logDir . '/payment_capture.log';
$requestId = uniqid('req_', true);

function cap_log(string $label, $data): void {
    global $logFile, $requestId;
    $entry  = '[' . date('Y-m-d H:i:s') . "] [$requestId] $label\n";
    $entry .= is_array($data) || is_object($data) ? print_r($data, true) : (string)$data;
    $entry .= "\n" . str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

header('Content-Type: application/json');

// Session (single call)
if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}

$userId = intval($_SESSION['website_user_id'] ?? $_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    cap_log('NO_USER_SESSION', ['session_keys' => array_keys($_SESSION)]);
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => 'no_user_session',
        'message'    => 'You must be logged in to complete payment.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}

// Parse input
$rawInput = file_get_contents('php://input');
$input    = json_decode($rawInput, true);
if (json_last_error() !== JSON_ERROR_NONE) {
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => 'invalid_json',
        'message'    => 'Invalid JSON in request body.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}

$paypalOrderId = $input['order_id'] ?? null;
if (!$paypalOrderId) {
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => 'missing_order_id',
        'message'    => 'Missing PayPal order ID.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}

cap_log('REQUEST', ['order_id' => $paypalOrderId, 'user_id' => $userId]);

// DB connection
$port   = intval($db_port ?? 3306) ?: 3306;
$mysqli = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $port);
if (!$mysqli) {
    cap_log('DB_FAILED', mysqli_connect_error());
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => 'db_connection_failed',
        'message'    => 'Database connection failed.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    exit;
}
mysqli_set_charset($mysqli, 'utf8mb4');

$prefix = $table_prefix ?? 'gsp_';
$repo   = new BillingRepository($mysqli, $prefix);
$svc    = new BillingService($repo);

// Capture payment via PayPal gateway
try {
    $gateway = GatewayFactory::make('paypal');
} catch (Exception $e) {
    cap_log('GATEWAY_ERROR', $e->getMessage());
    $repo->logPaypalError([
        'context'    => 'gateway_init',
        'error_code' => 'gateway_init_failed',
        'message'    => $e->getMessage(),
        'order_id'   => $paypalOrderId,
        'user_id'    => $userId,
    ]);
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => 'gateway_init_failed',
        'message'    => 'Payment gateway initialisation failed.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    mysqli_close($mysqli);
    exit;
}

$capture = $gateway->handleCallback(['order_id' => $paypalOrderId]);
cap_log('CAPTURE_RESULT', ['success' => $capture['success'], 'txid' => $capture['transaction_id'] ?? null]);

if (!$capture['success']) {
    cap_log('CAPTURE_FAILED', $capture);
    $repo->logPaypalError([
        'context'         => 'capture_order',
        'error_code'      => $capture['error'] ?? 'capture_failed',
        'message'         => $capture['message'] ?? 'PayPal order capture failed.',
        'paypal_debug_id' => $capture['debug_id'] ?? null,
        'order_id'        => $paypalOrderId,
        'user_id'         => $userId,
        'raw_json'        => $capture,
    ]);
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => $capture['error'] ?? 'capture_failed',
        'message'    => $capture['message'] ?? 'PayPal order capture failed. Please try again.',
        'debug_id'   => $capture['debug_id'] ?? null,
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    mysqli_close($mysqli);
    exit;
}

$txid                      = $capture['transaction_id'] ?? '';
$capture['payment_method'] = 'paypal';

// Process each unpaid invoice for this user
$invoices      = $repo->getUnpaidInvoicesForUser($userId);
$invoicesPaid  = 0;
$ordersCreated = 0;
$newOrderIds   = [];
$now           = date('Y-m-d H:i:s');

if (empty($invoices)) {
    cap_log('NO_INVOICES', ['user_id' => $userId]);
}

foreach ($invoices as $inv) {
    $invoiceId = intval($inv['invoice_id']);
    $homeId    = intval($inv['home_id'] ?? 0);

    $result = $svc->processPaymentSuccess($capture, $invoiceId, $userId, $homeId, $inv);
    if (!$result['success']) {
        cap_log('INVOICE_PAY_FAILED', ['invoice_id' => $invoiceId, 'error' => $result['error'] ?? '']);
        continue;
    }

    $invoicesPaid++;
    cap_log('INVOICE_PAID', ['invoice_id' => $invoiceId, 'txid' => $txid]);

    // Record transaction in billing_transactions (idempotent — skip on duplicate external ID)
    $rawCapture = $capture['raw_response'] ?? [];
    if (is_array($rawCapture)) {
        unset($rawCapture['client_secret'], $rawCapture['access_token']); // never log secrets
    }
    $repo->logTransaction([
        'invoice_id'              => $invoiceId,
        'user_id'                 => $userId,
        'home_id'                 => $homeId,
        'payment_method'          => 'paypal',
        'transaction_external_id' => $txid,
        'amount'                  => (float)($inv['amount'] ?? $inv['total_due'] ?? 0),
        'currency'                => (string)($inv['currency'] ?? 'USD'),
        'status'                  => 'completed',
        'raw_response'            => $rawCapture,
    ]);

    // Resolve (or create) the billing_orders row for this invoice so the provisioner can run.
    // billing_orders.status='Active' is what create_servers.php queries.
    $orderId = intval($inv['order_id'] ?? 0);

    $durMap = [
        'daily'   => '+1 day',  'monthly' => '+1 month', 'yearly' => '+1 year',
        'day'     => '+1 day',  'month'   => '+1 month', 'year'   => '+1 year',
    ];
    $dur    = strtolower($inv['rate_type'] ?? $inv['invoice_duration'] ?? 'month');
    $newEnd = date('Y-m-d H:i:s', strtotime($durMap[$dur] ?? '+1 month'));

    if ($orderId > 0) {
        // Existing order linked to this invoice — extend it and mark Active.
        $order = $repo->getOrder($orderId);
        if ($order) {
            $fromTs = (strtotime($order['end_date'] ?? '') > time()) ? strtotime($order['end_date']) : time();
            $newEnd = date('Y-m-d H:i:s', strtotime($durMap[$dur] ?? '+1 month', $fromTs));
            $repo->extendOrder($orderId, $newEnd, $txid, $now);
            $ordersCreated++;
            // Queue for provisioning only if not yet provisioned (home_id still '0' / empty).
            $currentHomeId = (string)($order['home_id'] ?? '0');
            if ($currentHomeId === '' || $currentHomeId === '0') {
                $newOrderIds[] = $orderId;
                cap_log('ORDER_QUEUED_PROVISION', ['order_id' => $orderId]);
            }
        }
    } else {
        // No billing_orders row yet — create one now so the provisioner can run.
        $newOrderId = $repo->createOrder([
            'user_id'                 => intval($inv['user_id']),
            'service_id'              => intval($inv['service_id']),
            'home_name'               => $inv['home_name'] ?? '',
            'ip'                      => (string)($inv['ip'] ?? '0'),
            'qty'                     => intval($inv['qty'] ?? 1),
            'invoice_duration'        => $inv['invoice_duration'] ?? 'month',
            'max_players'             => intval($inv['max_players'] ?? 0),
            'price'                   => (float)($inv['amount'] ?? $inv['total_due'] ?? 0),
            'remote_control_password' => $inv['remote_control_password'] ?? '',
            'ftp_password'            => $inv['ftp_password'] ?? '',
            'status'                  => 'Active',
            'end_date'                => $newEnd,
            'payment_txid'            => $txid,
            'paid_ts'                 => $now,
            'coupon_id'               => intval($inv['coupon_id'] ?? 0),
        ]);
        if ($newOrderId > 0) {
            // Link invoice → order so retried captures are idempotent.
            $repo->updateInvoiceOrderId($invoiceId, $newOrderId);
            $newOrderIds[] = $newOrderId;
            $ordersCreated++;
            cap_log('ORDER_CREATED', ['invoice_id' => $invoiceId, 'order_id' => $newOrderId]);
        } else {
            cap_log('ORDER_CREATE_FAILED', ['invoice_id' => $invoiceId, 'db_error' => $mysqli->error]);
        }
    }
}

// Auto-provision new servers (orders without a home_id)
$autoProvision = ['provisioned_count' => 0, 'failed_count' => 0];
if (!empty($newOrderIds)) {
    require_once __DIR__ . '/../includes/panel_bridge.php';
    $panelCtx = billing_panel_bootstrap();
    if ($panelCtx && isset($panelCtx['db'])) {
        $GLOBALS['db']       = $panelCtx['db'];
        $GLOBALS['settings'] = $panelCtx['settings'];
        require_once __DIR__ . '/../create_servers.php';
        $autoProvision = billing_invoke_provision(['order_ids' => $newOrderIds, 'user_id' => $userId, 'is_admin' => true]);
        if (($autoProvision['failed_count'] ?? 0) > 0) {
            cap_log('AUTO_PROVISION_PARTIAL_FAILURE', $autoProvision);
        }
    } else {
        cap_log('AUTO_PROVISION_SKIPPED', 'panel bootstrap failed — orders require manual provisioning: ' . implode(',', $newOrderIds));
    }
}

mysqli_close($mysqli);

cap_log('COMPLETE', ['invoices_paid' => $invoicesPaid, 'txid' => $txid]);

ob_clean();
echo json_encode([
    'status'         => 'COMPLETED',
    'txid'           => $txid,
    'invoices_paid'  => $invoicesPaid,
    'orders_created' => $ordersCreated,
    'provisioned'    => $autoProvision['provisioned_count'] ?? 0,
    'request_id'     => $requestId,
]);
