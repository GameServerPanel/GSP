<?php
// Admin invoices management
if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/classes/BillingRepository.php';
require_once __DIR__ . '/classes/BillingService.php';
require_once __DIR__ . '/classes/GatewayFactory.php';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) die('DB connection failed');
mysqli_set_charset($db, 'utf8mb4');
$prefix = $table_prefix ?? 'gsp_';
$repo   = new BillingRepository($db, $prefix);
$svc    = new BillingService($repo);

$message = '';
$msgType = 'success';

// Handle POST: mark as paid (manual), cancel, or refund
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['invoice_id'])) {
    $invId  = intval($_POST['invoice_id']);
    $action = $_POST['action'];
    $now    = date('Y-m-d H:i:s');

    // Fetch invoice to verify it exists
    $invRow = null;
    $stmt = $db->prepare("SELECT * FROM `{$prefix}billing_invoices` WHERE invoice_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $invId);
        $stmt->execute();
        $invRow = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }

    if (!$invRow) {
        $message = "Invoice #{$invId} not found.";
        $msgType = 'error';
    } elseif ($action === 'mark_paid') {
        $gateway       = GatewayFactory::make('manual');
        $captureResult = $gateway->handleCallback(['amount' => $invRow['total_due'] ?? $invRow['amount'] ?? 0, 'currency' => $invRow['currency'] ?? 'USD']);
        $captureResult['payment_method'] = 'manual';
        $homeId  = intval($invRow['home_id'] ?? 0);
        $result  = $svc->processPaymentSuccess($captureResult, $invId, intval($invRow['user_id']), $homeId, $invRow);
        $message = $result['success'] ? "Invoice #{$invId} marked as paid (manual)." : "Failed to mark invoice #{$invId} as paid.";
        if (!$result['success']) $msgType = 'error';
    } elseif ($action === 'cancel') {
        $stmt = $db->prepare("UPDATE `{$prefix}billing_invoices` SET payment_status='cancelled' WHERE invoice_id=? LIMIT 1");
        if ($stmt) { $stmt->bind_param('i', $invId); $stmt->execute(); $stmt->close(); }
        $message = "Invoice #{$invId} cancelled.";
    } elseif ($action === 'refund') {
        $stmt = $db->prepare("UPDATE `{$prefix}billing_invoices` SET payment_status='refunded' WHERE invoice_id=? LIMIT 1");
        if ($stmt) { $stmt->bind_param('i', $invId); $stmt->execute(); $stmt->close(); }
        $message = "Invoice #{$invId} marked as refunded.";
    }

    if (!headers_sent()) {
        header('Location: admin_invoices.php?msg=' . urlencode($message) . '&type=' . $msgType);
        mysqli_close($db);
        exit;
    }
}

// Fetch invoices
$invoices = [];
$res = $db->query(
    "SELECT i.*, u.users_login, u.users_email
     FROM `{$prefix}billing_invoices` i
     LEFT JOIN `{$prefix}users` u ON u.user_id = i.user_id
     ORDER BY i.invoice_id DESC
     LIMIT 500"
);
if ($res) $invoices = $res->fetch_all(MYSQLI_ASSOC);
mysqli_close($db);

if (isset($_GET['msg']))  $message = $_GET['msg'];
if (isset($_GET['type'])) $msgType = $_GET['type'];
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin — Invoices</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
  <style>
    .status-badge     { display:inline-block; padding:2px 8px; border-radius:3px; font-size:12px; font-weight:600; }
    .status-paid      { background:#d4edda; color:#155724; }
    .status-unpaid    { background:#fff3cd; color:#856404; }
    .status-cancelled { background:#e2e3e5; color:#383d41; }
    .status-refunded  { background:#f8d7da; color:#721c24; }
    .action-btn { padding:3px 8px; font-size:12px; border:none; border-radius:3px; cursor:pointer; }
    .btn-pay    { background:#28a745; color:#fff; }
    .btn-cancel { background:#6c757d; color:#fff; }
    .btn-refund { background:#dc3545; color:#fff; }
  </style>
</head>
<body>
<?php include __DIR__ . '/includes/top.php'; include __DIR__ . '/includes/menu.php'; ?>
<div class="container-wide panel">
  <h1>Admin — All Invoices</h1>
  <?php if ($message): ?>
    <div style="background:<?= $msgType==='error' ? '#f8d7da' : '#d4edda' ?>;padding:10px;margin-bottom:15px;border-radius:3px;color:<?= $msgType==='error' ? '#721c24' : '#155724' ?>;">
      <?= h($message) ?>
    </div>
  <?php endif; ?>

  <table class="cart-table">
    <thead>
      <tr>
        <th>#</th><th>User</th><th>Server</th><th>Service</th>
        <th>Rate</th><th>Players</th><th>Period</th>
        <th>Total</th><th>Status</th><th>Method</th><th>Txn ID</th><th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php if (empty($invoices)): ?>
      <tr><td colspan="12" style="text-align:center">No invoices found.</td></tr>
    <?php else: foreach ($invoices as $inv): ?>
      <tr>
        <td><?= h($inv['invoice_id']) ?></td>
        <td><?= h($inv['users_login'] ?? $inv['user_id']) ?></td>
        <td><?= h($inv['home_id'] ?: '—') ?></td>
        <td><?= h($inv['service_id']) ?></td>
        <td><?= h($inv['rate_type'] ?? '—') ?></td>
        <td><?= h($inv['players'] ?? '—') ?></td>
        <td style="font-size:11px"><?= h(substr($inv['period_start'] ?? '', 0, 10)) ?> – <?= h(substr($inv['period_end'] ?? '', 0, 10)) ?></td>
        <td><?= h(number_format((float)($inv['total_due'] ?? $inv['amount'] ?? 0), 2)) ?></td>
        <td><span class="status-badge status-<?= h($inv['payment_status'] ?? 'unpaid') ?>"><?= h($inv['payment_status'] ?? 'unpaid') ?></span></td>
        <td><?= h($inv['payment_method'] ?? '—') ?></td>
        <td style="font-size:11px;max-width:120px;overflow:hidden"><?= h($inv['payment_txid'] ?? '—') ?></td>
        <td>
          <?php if (($inv['payment_status'] ?? '') !== 'paid'): ?>
            <form method="post" style="display:inline">
              <input type="hidden" name="invoice_id" value="<?= intval($inv['invoice_id']) ?>">
              <input type="hidden" name="action" value="mark_paid">
              <button type="submit" class="action-btn btn-pay">Mark Paid</button>
            </form>
          <?php endif; ?>
          <?php if (!in_array($inv['payment_status'] ?? '', ['cancelled','refunded'])): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Cancel this invoice?')">
              <input type="hidden" name="invoice_id" value="<?= intval($inv['invoice_id']) ?>">
              <input type="hidden" name="action" value="cancel">
              <button type="submit" class="action-btn btn-cancel">Cancel</button>
            </form>
          <?php endif; ?>
          <?php if (($inv['payment_status'] ?? '') === 'paid'): ?>
            <form method="post" style="display:inline" onsubmit="return confirm('Mark as refunded?')">
              <input type="hidden" name="invoice_id" value="<?= intval($inv['invoice_id']) ?>">
              <input type="hidden" name="action" value="refund">
              <button type="submit" class="action-btn btn-refund">Refund</button>
            </form>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; endif; ?>
    </tbody>
  </table>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
