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
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    die('Database connection failed: ' . htmlspecialchars(mysqli_connect_error()));
}
mysqli_set_charset($db, 'utf8mb4');

// Fetch unpaid invoices for this user
$invoices = [];
$q = mysqli_query($db, "SELECT * FROM {$table_prefix}billing_invoices
                        WHERE user_id = " . intval($userId) . "
                          AND (status = 'due' OR status = '')
                          AND (payment_status IS NULL OR payment_status NOT IN ('paid','cancelled','refunded'))
                        ORDER BY invoice_id ASC");
if ($q) {
    while ($row = mysqli_fetch_assoc($q)) {
        $invoices[] = $row;
    }
    mysqli_free_result($q);
}

if (empty($invoices)) {
    mysqli_close($db);
    header('Location: /cart.php?msg=empty');
    exit;
}

// Validate coupon from POST / session
$couponId   = intval($_POST['coupon_id'] ?? $_SESSION['cart_coupon_id'] ?? 0);
$couponCode = trim($_POST['coupon_code'] ?? $_SESSION['cart_coupon_code'] ?? '');
$discountPct = 0.0;

if ($couponCode !== '') {
    $safe = mysqli_real_escape_string($db, $couponCode);
    $cr   = mysqli_query($db, "SELECT * FROM {$table_prefix}billing_coupons
                               WHERE code = '$safe' AND is_active = 1 LIMIT 1");
    if ($cr && mysqli_num_rows($cr) === 1) {
        $coupon      = mysqli_fetch_assoc($cr);
        $discountPct = (float)($coupon['discount_percent'] ?? 0);
        mysqli_free_result($cr);
    }
}

// Calculate total and verify it is $0 after discount
$totalAmount = 0.0;
foreach ($invoices as $inv) {
    $totalAmount += (float)($inv['amount'] ?? 0);
}
$discountAmount = $totalAmount * ($discountPct / 100.0);
$finalAmount    = round($totalAmount - $discountAmount, 2);

if ($finalAmount > 0.00) {
    // Coupon no longer covers the full amount — redirect to cart
    mysqli_close($db);
    header('Location: /cart.php?msg=coupon_insufficient');
    exit;
}

// Process the free checkout
$now   = date('Y-m-d H:i:s');
$txid  = 'free-' . time() . '-' . $userId;

require_once __DIR__ . '/classes/BillingRepository.php';
require_once __DIR__ . '/classes/BillingService.php';

$repo     = new BillingRepository($db, $table_prefix);
$svc      = new BillingService($repo);
$newOrderIds = [];

foreach ($invoices as $inv) {
    $invoiceId = intval($inv['invoice_id']);

    // Mark invoice paid (zero-dollar, method=coupon)
    $repo->markInvoicePaid($invoiceId, $txid, 'coupon', $now);

    // Log a $0 transaction for the audit trail
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

    // Increment coupon use counter
    if ($couponId > 0) {
        mysqli_query($db, "UPDATE {$table_prefix}billing_coupons
                           SET current_uses = current_uses + 1
                           WHERE coupon_id = " . intval($couponId));
    }

    // Create billing_orders row so the provisioner can run
    $durMap  = ['daily'=>'+1 day','monthly'=>'+1 month','yearly'=>'+1 year','day'=>'+1 day','month'=>'+1 month','year'=>'+1 year'];
    $dur     = strtolower($inv['invoice_duration'] ?? 'month');
    $endDate = date('Y-m-d H:i:s', strtotime($durMap[$dur] ?? '+1 month'));

    $newOrderId = $repo->createOrder([
        'user_id'                 => intval($inv['user_id']),
        'service_id'              => intval($inv['service_id']),
        'home_name'               => $inv['home_name'] ?? '',
        'ip'                      => (string)($inv['ip'] ?? '0'),
        'qty'                     => intval($inv['qty'] ?? 1),
        'invoice_duration'        => $inv['invoice_duration'] ?? 'month',
        'max_players'             => intval($inv['max_players'] ?? 0),
        'price'                   => 0.00,
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
        $newOrderIds[] = $newOrderId;
    }
}

// Clear coupon from session
unset($_SESSION['cart_coupon_code'], $_SESSION['cart_coupon_id']);

// Attempt automatic provisioning via panel bridge
if (!empty($newOrderIds)) {
    require_once __DIR__ . '/includes/panel_bridge.php';
    $panelCtx = billing_panel_bootstrap();
    if ($panelCtx && isset($panelCtx['db'])) {
        $GLOBALS['db']       = $panelCtx['db'];
        $GLOBALS['settings'] = $panelCtx['settings'];
        require_once __DIR__ . '/create_servers.php';
        billing_invoke_provision(['order_ids' => $newOrderIds, 'user_id' => $userId, 'is_admin' => true]);
    }
    // If panel bootstrap fails the order is Active and admins can provision via the orders panel.
}

mysqli_close($db);

header('Location: /payment_success.php?order_id=' . urlencode($txid));
exit;
