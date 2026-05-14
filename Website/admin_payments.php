<?php
// Admin payment transaction log viewer
if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}
require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/includes/admin_auth.php';
require_once __DIR__ . '/classes/BillingRepository.php';

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
$transactions = [];
$errorMsg = '';
if (!$db) {
    $errorMsg = 'Database connection failed.';
} else {
    mysqli_set_charset($db, 'utf8mb4');
    $prefix = $table_prefix ?? 'gsp_';
    $repo   = new BillingRepository($db, $prefix);

    // Build filter from GET params
    $filter = [];
    if (!empty($_GET['user_id']))         $filter['user_id']        = intval($_GET['user_id']);
    if (!empty($_GET['home_id']))         $filter['home_id']        = intval($_GET['home_id']);
    if (!empty($_GET['payment_method']))  $filter['payment_method'] = trim($_GET['payment_method']);

    try {
        $transactions = $repo->getTransactions($filter, 200, 0);
    } catch (Throwable $e) {
        $errorMsg = 'Could not load transactions: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
    }
    mysqli_close($db);
    $db = null;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin — Payment Transactions</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
<?php include __DIR__ . '/includes/top.php'; include __DIR__ . '/includes/menu.php'; ?>
<div class="container-wide panel">
  <h1>Payment Transaction Log</h1>
  <?php if ($errorMsg): ?><div class="alert alert-error"><?= h($errorMsg) ?></div><?php endif; ?>

  <form method="get" style="margin-bottom:15px;">
    <label>User ID: <input name="user_id" value="<?= h($_GET['user_id'] ?? '') ?>" style="width:80px"></label>
    <label>Server ID: <input name="home_id" value="<?= h($_GET['home_id'] ?? '') ?>" style="width:80px"></label>
    <label>Method:
      <select name="payment_method">
        <option value="">All</option>
        <option value="paypal"  <?= ($_GET['payment_method'] ?? '') === 'paypal'  ? 'selected' : '' ?>>PayPal</option>
        <option value="stripe"  <?= ($_GET['payment_method'] ?? '') === 'stripe'  ? 'selected' : '' ?>>Stripe</option>
        <option value="manual"  <?= ($_GET['payment_method'] ?? '') === 'manual'  ? 'selected' : '' ?>>Manual</option>
      </select>
    </label>
    <button type="submit" class="gsw-btn">Filter</button>
    <a href="admin_payments.php" class="gsw-btn-secondary">Clear</a>
  </form>

  <?php if (empty($transactions)): ?>
    <p>No transactions found<?= (!empty($filter) ? ' matching filters' : '') ?>.</p>
  <?php else: ?>
  <table class="cart-table">
    <thead>
      <tr>
        <th>#</th><th>Invoice</th><th>User</th><th>Server</th>
        <th>Method</th><th>Txn ID</th><th>Amount</th><th>Status</th><th>Date</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach ($transactions as $t): ?>
      <tr>
        <td><?= h($t['transaction_id']) ?></td>
        <td><?= h($t['invoice_id']) ?></td>
        <td><?= h($t['users_login'] ?? $t['user_id']) ?></td>
        <td><?= $t['home_id'] ? h($t['home_id']) : '—' ?></td>
        <td><?= h($t['payment_method']) ?></td>
        <td style="font-size:11px;max-width:160px;overflow:hidden;text-overflow:ellipsis"><?= h($t['transaction_external_id']) ?></td>
        <td><?= h($t['currency'] . ' ' . number_format((float)$t['amount'], 2)) ?></td>
        <td><span class="status-badge status-<?= h(ucfirst($t['status'])) ?>"><?= h($t['status']) ?></span></td>
        <td><?= h($t['created_at']) ?></td>
      </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  <?php endif; ?>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
