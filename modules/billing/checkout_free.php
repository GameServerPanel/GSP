<?php
/**
 * Free Checkout Handler
 *
 * Processes a zero-dollar cart when a coupon reduces the total to $0.
 * Marks invoices paid (method=coupon, txid=free-<timestamp>),
 * creates billing_orders rows, and triggers automatic server provisioning.
 *
 * POST params: coupon_id, coupon_code
 */

if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/includes/login_required.php';

function billing_free_money_to_cents(float $amount): int
{
    return (int) round($amount * 100);
}

$userId = intval($_SESSION['website_user_id'] ?? $_SESSION['user_id'] ?? 0);
if ($userId <= 0) {
    header('Location: /login.php');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /cart.php');
    exit;
}

// DB connection
$mysqli = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$mysqli) {
    die('<p>Database connection failed. Please <a href="/serverlist.php">return to the shop</a> or contact support.</p>');
}
mysqli_set_charset($mysqli, 'utf8mb4');

// Fetch unpaid invoices for this user (prepared statement)
$invoices = [];
$stmt = mysqli_prepare($mysqli, "SELECT * FROM {$table_prefix}billing_invoices
                             WHERE user_id = ?
                               AND (status = 'due' OR status = '')
                               AND (payment_status IS NULL OR payment_status NOT IN ('paid','cancelled','refunded'))
                             ORDER BY invoice_id ASC");
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    while ($row = mysqli_fetch_assoc($result)) {
        $invoices[] = $row;
    }
    mysqli_stmt_close($stmt);
}

if (empty($invoices)) {
    if ($mysqli instanceof mysqli) {
        mysqli_close($mysqli);
    }
    header('Location: /cart.php?msg=empty');
    exit;
}

// Validate coupon from POST / session
$couponId   = intval($_POST['coupon_id'] ?? $_SESSION['cart_coupon_id'] ?? 0);
$couponCode = trim($_POST['coupon_code'] ?? $_SESSION['cart_coupon_code'] ?? '');
$discountPct = 0.0;

if ($couponCode !== '') {
    $safe = mysqli_real_escape_string($mysqli, $couponCode);
    $cr   = mysqli_query($mysqli, "SELECT * FROM {$table_prefix}billing_coupons
                               WHERE code = '$safe' AND is_active = 1 LIMIT 1");
    if ($cr && mysqli_num_rows($cr) === 1) {
        $coupon      = mysqli_fetch_assoc($cr);
        $discountPct = (float)($coupon['discount_percent'] ?? 0);
        mysqli_free_result($cr);
    }
}

// Calculate total and verify it is $0 after discount
$totalAmountCents = 0;
foreach ($invoices as $inv) {
    $lineAmount = (float)($inv['total_due'] ?? $inv['amount'] ?? 0);
    $totalAmountCents += billing_free_money_to_cents($lineAmount);
}
$discountAmountCents = (int) round($totalAmountCents * ($discountPct / 100.0));
$discountAmountCents = min($discountAmountCents, $totalAmountCents);
$finalAmountCents = max(0, $totalAmountCents - $discountAmountCents);

if ($finalAmountCents !== 0) {
    // Coupon no longer covers the full amount — redirect to cart
    if ($mysqli instanceof mysqli) {
        mysqli_close($mysqli);
    }
    header('Location: /cart.php?msg=coupon_insufficient');
    exit;
}

// Process the free checkout
$now   = date('Y-m-d H:i:s');
$txid  = 'free-' . time() . '-' . $userId;

require_once __DIR__ . '/classes/BillingRepository.php';
require_once __DIR__ . '/classes/BillingService.php';

$repo = new BillingRepository($mysqli, $table_prefix);
$newOrderIds = [];
$duration_meta = static function (array $invoice): array {
    return ['invoice_duration' => 'month', 'rate_type' => 'monthly', 'days' => 31];
};

foreach ($invoices as $inv) {
    $invoiceId = intval($inv['invoice_id']);
    $invoiceBase = round((float)($inv['subtotal'] ?? $inv['total_due'] ?? $inv['amount'] ?? 0), 2);
    $orderId = intval($inv['order_id'] ?? 0);
    $meta = $duration_meta($inv);

    $repo->updateInvoiceFields($invoiceId, [
        'order_id' => $orderId,
        'coupon_id' => $couponId,
        'discount_amount' => $invoiceBase,
        'subtotal' => $invoiceBase,
        'amount' => 0.00,
        'total_due' => 0.00,
        'status' => 'paid',
        'billing_status' => 'Active',
        'payment_status' => 'paid',
        'payment_txid' => $txid,
        'payment_method' => 'coupon',
        'paid_date' => $now,
        'invoice_duration' => $meta['invoice_duration'],
        'rate_type' => $meta['rate_type'],
    ]);

    $repo->logTransaction([
        'invoice_id'              => $invoiceId,
        'user_id'                 => $userId,
        'home_id'                 => 0,
        'payment_method'          => 'coupon',
        'transaction_external_id' => $txid,
        'amount'                  => 0.00,
        'currency'                => 'USD',
        'status'                  => 'completed',
        'raw_response'            => ['coupon_id' => $couponId, 'discount_pct' => $discountPct, 'original_amount' => (float)($inv['amount'] ?? 0)],
    ]);

    $currentHomeId = 0;
    $extendFrom = null;
    if ($orderId > 0) {
        $order = $repo->getOrder($orderId);
        if ($order) {
            $currentHomeId = intval($order['home_id'] ?? 0);
            $extendFrom = $order['end_date'] ?? null;
        }
    }

    $baseTs = time();
    if (!empty($extendFrom)) {
        $extendTs = strtotime($extendFrom);
        if ($extendTs !== false && $extendTs > time()) {
            $baseTs = $extendTs;
        }
    }
    $endDate = date('Y-m-d H:i:s', $baseTs + ($meta['days'] * max(1, intval($inv['qty'] ?? 1)) * 86400));

    if ($orderId > 0) {
        $repo->updateOrderFields($orderId, [
            'status' => 'Active',
            'end_date' => $endDate,
            'payment_txid' => $txid,
            'paid_ts' => $now,
            'price' => 0.00,
            'discount_amount' => $invoiceBase,
            'coupon_id' => $couponId,
        ]);
        if ($currentHomeId > 0) {
            $repo->updateInvoiceFields($invoiceId, ['home_id' => $currentHomeId]);
        }
        if (!in_array($orderId, $newOrderIds, true)) {
            $newOrderIds[] = $orderId;
        }
    } else {
        $newOrderId = $repo->createOrder([
            'user_id'                 => intval($inv['user_id']),
            'service_id'              => intval($inv['service_id']),
            'home_name'               => $inv['home_name'] ?? '',
            'ip'                      => (string)($inv['ip'] ?? '0'),
            'qty'                     => intval($inv['qty'] ?? 1),
            'invoice_duration'        => $meta['invoice_duration'],
            'max_players'             => intval($inv['max_players'] ?? 0),
            'price'                   => 0.00,
            'discount_amount'         => $invoiceBase,
            'remote_control_password' => $inv['remote_control_password'] ?? '',
            'ftp_password'            => $inv['ftp_password'] ?? '',
            'status'                  => 'Active',
            'end_date'                => $endDate,
            'payment_txid'            => $txid,
            'paid_ts'                 => $now,
            'coupon_id'               => $couponId,
        ]);

        if ($newOrderId > 0) {
            $repo->updateInvoiceOrderId($invoiceId, $newOrderId);
            $repo->updateInvoiceFields($invoiceId, ['order_id' => $newOrderId]);
            if (!in_array($newOrderId, $newOrderIds, true)) {
                $newOrderIds[] = $newOrderId;
            }
        }
    }
}

if ($couponId > 0 && !empty($invoices)) {
    mysqli_query($mysqli, "UPDATE {$table_prefix}billing_coupons
                       SET current_uses = current_uses + 1
                       WHERE coupon_id = " . intval($couponId));
}

// Clear coupon from session
unset($_SESSION['cart_coupon_code'], $_SESSION['cart_coupon_id']);

// Attempt automatic provisioning via panel bridge
$autoProvision = ['provisioned_count' => 0, 'failed_count' => 0, 'details' => [], 'trace_log_path' => 'modules/billing/logs/provisioning_trace.log'];
if (!empty($newOrderIds)) {
    require_once __DIR__ . '/includes/panel_bridge.php';
    $panelCtx = billing_panel_bootstrap();
    if ($panelCtx && isset($panelCtx['db'])) {
        $GLOBALS['db']       = $panelCtx['db'];
        $GLOBALS['settings'] = $panelCtx['settings'];
        require_once __DIR__ . '/create_servers.php';
        $autoProvision = billing_invoke_provision(['order_ids' => $newOrderIds, 'user_id' => $userId, 'is_admin' => true]);
    } else {
        $autoProvision = [
            'provisioned_count' => 0,
            'failed_count' => count($newOrderIds),
            'details' => [],
            'trace_log_path' => 'modules/billing/logs/provisioning_trace.log',
            'trace_error' => 'Panel bootstrap failed before billing provisioning could start.',
        ];
    }
    // If panel bootstrap fails the order is Active and admins can provision via the orders panel.
}
if (function_exists('billing_store_provision_session_result')) {
    billing_store_provision_session_result($txid, [
        'source' => 'checkout_free.php',
        'txid' => $txid,
        'order_ids' => $newOrderIds,
        'result' => $autoProvision,
    ]);
}

if ($mysqli instanceof mysqli) {
    mysqli_close($mysqli);
}

header('Location: /payment_success.php?order_id=' . urlencode($txid) . '&source=free');
exit;
