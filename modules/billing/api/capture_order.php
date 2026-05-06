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

function cap_invoice_ids_from_custom_id(?string $customId): array {
    if (!is_string($customId) || $customId === '') {
        return [];
    }
    if (ctype_digit($customId)) {
        return [intval($customId)];
    }
    if (stripos($customId, 'cart:') !== 0) {
        return [];
    }
    $parts = explode(',', substr($customId, 5));
    $invoiceIds = [];
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part !== '' && ctype_digit($part)) {
            $invoiceIds[] = intval($part);
        }
    }
    return array_values(array_unique($invoiceIds));
}

function cap_get_duration_metadata(array $invoice): array {
    $duration = strtolower((string)($invoice['invoice_duration'] ?? $invoice['rate_type'] ?? 'month'));
    switch ($duration) {
        case 'day':
        case 'daily':
            return ['invoice_duration' => 'day', 'rate_type' => 'daily', 'days' => 1];
        case 'year':
        case 'yearly':
            return ['invoice_duration' => 'year', 'rate_type' => 'yearly', 'days' => 365];
        case 'month':
        case 'monthly':
        default:
            return ['invoice_duration' => 'month', 'rate_type' => 'monthly', 'days' => 31];
    }
}

function cap_get_end_date(array $invoice, ?string $fromDate = null): string {
    $meta = cap_get_duration_metadata($invoice);
    $qty = max(1, intval($invoice['qty'] ?? 1));
    $baseTs = time();
    if (!empty($fromDate)) {
        $fromTs = strtotime($fromDate);
        if ($fromTs !== false && $fromTs > time()) {
            $baseTs = $fromTs;
        }
    }
    return date('Y-m-d H:i:s', $baseTs + ($meta['days'] * $qty * 86400));
}

function cap_discount_map(array $invoices, float $paidAmount): array {
    $baseTotals = [];
    $baseAmount = 0.0;
    foreach ($invoices as $invoice) {
        $invoiceId = intval($invoice['invoice_id'] ?? 0);
        $lineBase = round((float)($invoice['subtotal'] ?? $invoice['total_due'] ?? $invoice['amount'] ?? 0), 2);
        $baseTotals[$invoiceId] = $lineBase;
        $baseAmount += $lineBase;
    }

    $discountTotal = round(max(0, $baseAmount - $paidAmount), 2);
    if ($discountTotal <= 0 || $baseAmount <= 0) {
        return array_fill_keys(array_keys($baseTotals), 0.0);
    }

    $discounts = [];
    $remaining = $discountTotal;
    $lastInvoiceId = array_key_last($baseTotals);
    foreach ($baseTotals as $invoiceId => $lineBase) {
        if ($invoiceId === $lastInvoiceId) {
            $lineDiscount = $remaining;
        } else {
            $lineDiscount = round($discountTotal * ($lineBase / $baseAmount), 2);
            $remaining = round($remaining - $lineDiscount, 2);
        }
        $discounts[$invoiceId] = min($lineBase, max(0, $lineDiscount));
    }

    return $discounts;
}

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
    // Sanitize raw capture data before logging — never store secrets
    $captureForLog = $capture;
    foreach (['client_secret', 'access_token', 'refresh_token'] as $_sk) {
        unset($captureForLog[$_sk]);
    }
    $repo->logPaypalError([
        'context'         => 'capture_order',
        'error_code'      => $capture['error'] ?? 'capture_failed',
        'message'         => $capture['message'] ?? 'PayPal order capture failed.',
        'paypal_debug_id' => $capture['debug_id'] ?? null,
        'order_id'        => $paypalOrderId,
        'user_id'         => $userId,
        'raw_json'        => $captureForLog,
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
$paidAmount                = round((float)($capture['amount'] ?? 0), 2);
$capture['payment_method'] = 'paypal';
$invoiceIds                = cap_invoice_ids_from_custom_id($capture['custom_id'] ?? null);
$invoices                  = !empty($invoiceIds)
    ? $repo->getInvoicesForUserByIds($userId, $invoiceIds, true)
    : $repo->getUnpaidInvoicesForUser($userId);
$invoicesPaid              = 0;
$ordersCreated             = 0;
$newOrderIds               = [];
$now                       = date('Y-m-d H:i:s');
$couponId                  = intval($_SESSION['cart_coupon_id'] ?? 0);
$discountMap               = cap_discount_map($invoices, $paidAmount);
$couponCode                = trim((string)($_SESSION['cart_coupon_code'] ?? ''));

if ($couponId <= 0 && $couponCode !== '') {
    $coupon = $repo->getCouponByCode($couponCode);
    $couponId = intval($coupon['coupon_id'] ?? 0);
}

if (empty($invoices)) {
    cap_log('NO_INVOICES', ['user_id' => $userId, 'custom_id' => $capture['custom_id'] ?? null]);
    ob_clean();
    echo json_encode([
        'success'    => false,
        'error_code' => 'no_matching_invoices',
        'message'    => 'No matching unpaid invoices were found for this payment.',
        'timestamp'  => date('c'),
        'request_id' => $requestId,
    ]);
    mysqli_close($mysqli);
    exit;
}

foreach ($invoices as $inv) {
    $invoiceId = intval($inv['invoice_id']);
    $homeId = intval($inv['home_id'] ?? 0);
    $invoiceBase = round((float)($inv['subtotal'] ?? $inv['total_due'] ?? $inv['amount'] ?? 0), 2);
    $lineDiscount = round((float)($discountMap[$invoiceId] ?? 0), 2);
    $lineTotal = round(max(0, $invoiceBase - $lineDiscount), 2);
    $durationMeta = cap_get_duration_metadata($inv);

    $invoiceUpdate = [
        'coupon_id' => $couponId,
        'discount_amount' => $lineDiscount,
        'subtotal' => $invoiceBase,
        'amount' => $lineTotal,
        'total_due' => $lineTotal,
        'status' => 'paid',
        'billing_status' => 'Active',
        'payment_status' => 'paid',
        'payment_txid' => $txid,
        'payment_method' => 'paypal',
        'paid_date' => $now,
        'invoice_duration' => $durationMeta['invoice_duration'],
        'rate_type' => $durationMeta['rate_type'],
    ];

    if (!$repo->updateInvoiceFields($invoiceId, $invoiceUpdate)) {
        cap_log('INVOICE_PAY_FAILED', ['invoice_id' => $invoiceId, 'db_error' => $mysqli->error]);
        continue;
    }

    $invoicesPaid++;
    cap_log('INVOICE_PAID', ['invoice_id' => $invoiceId, 'txid' => $txid, 'amount' => $lineTotal]);

    $rawCapture = $capture['raw_response'] ?? [];
    if (is_array($rawCapture)) {
        unset($rawCapture['client_secret'], $rawCapture['access_token']); // never log secrets
    }

    // Resolve (or create) the billing_orders row for this invoice so the provisioner can run.
    // billing_orders.status='Active' is what create_servers.php queries.
    $orderId = intval($inv['order_id'] ?? 0);
    $currentHomeId = $homeId;

    if ($orderId > 0) {
        // Existing order linked to this invoice — extend it and mark Active.
        $order = $repo->getOrder($orderId);
        if ($order) {
            $newEnd = cap_get_end_date($inv, $order['end_date'] ?? null);
            $currentHomeId = intval($order['home_id'] ?? 0);
            $repo->updateOrderFields($orderId, [
                'status' => 'Active',
                'end_date' => $newEnd,
                'payment_txid' => $txid,
                'paid_ts' => $now,
                'price' => $lineTotal,
                'discount_amount' => $lineDiscount,
                'coupon_id' => $couponId,
            ]);
            if ($currentHomeId > 0) {
                $repo->updateInvoiceFields($invoiceId, ['home_id' => $currentHomeId]);
            }
            $ordersCreated++;
            // Queue for provisioning only if not yet provisioned (home_id still '0' / empty).
            if ($currentHomeId <= 0) {
                $newOrderIds[] = $orderId;
                cap_log('ORDER_QUEUED_PROVISION', ['order_id' => $orderId]);
            }
        }
    } else {
        // No billing_orders row yet — create one now so the provisioner can run.
        $newEnd = cap_get_end_date($inv, null);
        $newOrderId = $repo->createOrder([
            'user_id'                 => intval($inv['user_id']),
            'service_id'              => intval($inv['service_id']),
            'home_name'               => $inv['home_name'] ?? '',
            'ip'                      => (string)($inv['ip'] ?? '0'),
            'qty'                     => intval($inv['qty'] ?? 1),
            'invoice_duration'        => $durationMeta['invoice_duration'],
            'max_players'             => intval($inv['max_players'] ?? 0),
            'price'                   => $lineTotal,
            'discount_amount'         => $lineDiscount,
            'remote_control_password' => $inv['remote_control_password'] ?? '',
            'ftp_password'            => $inv['ftp_password'] ?? '',
            'status'                  => 'Active',
            'end_date'                => $newEnd,
            'payment_txid'            => $txid,
            'paid_ts'                 => $now,
            'coupon_id'               => $couponId,
        ]);
        if ($newOrderId > 0) {
            // Link invoice → order so retried captures are idempotent.
            $repo->updateInvoiceOrderId($invoiceId, $newOrderId);
            $repo->updateInvoiceFields($invoiceId, ['order_id' => $newOrderId]);
            $newOrderIds[] = $newOrderId;
            $ordersCreated++;
            cap_log('ORDER_CREATED', ['invoice_id' => $invoiceId, 'order_id' => $newOrderId]);
        } else {
            cap_log('ORDER_CREATE_FAILED', ['invoice_id' => $invoiceId, 'db_error' => $mysqli->error]);
            continue;
        }
    }

    $repo->logTransaction([
        'invoice_id'              => $invoiceId,
        'user_id'                 => $userId,
        'home_id'                 => $currentHomeId,
        'payment_method'          => 'paypal',
        'transaction_external_id' => $txid,
        'amount'                  => $lineTotal,
        'currency'                => (string)($inv['currency'] ?? 'USD'),
        'status'                  => 'completed',
        'raw_response'            => $rawCapture,
    ]);
}

if ($couponId > 0 && $invoicesPaid > 0) {
    $mysqli->query("UPDATE `{$prefix}billing_coupons`
                    SET current_uses = current_uses + 1
                    WHERE coupon_id = " . intval($couponId));
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

unset($_SESSION['cart_coupon_code'], $_SESSION['cart_coupon_id']);

mysqli_close($mysqli);

cap_log('COMPLETE', ['invoices_paid' => $invoicesPaid, 'txid' => $txid, 'orders' => $newOrderIds]);

ob_clean();
echo json_encode([
    'success'        => true,
    'status'         => 'COMPLETED',
    'txid'           => $txid,
    'invoices_paid'  => $invoicesPaid,
    'orders_created' => $ordersCreated,
    'provisioned'    => $autoProvision['provisioned_count'] ?? 0,
    'request_id'     => $requestId,
]);
