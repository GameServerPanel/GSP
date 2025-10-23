<?php
// Admin payments viewer — lists persisted PayPal webhook JSON files
$session_name = session_name(); session_start();
require_once(__DIR__ . '/includes/config.inc.php');
require_once(__DIR__ . '/includes/admin_auth.php');

$dataDir = (isset($SITE_DATA_DIR) && $SITE_DATA_DIR) ? $SITE_DATA_DIR : realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
$files = [];
if (is_dir($dataDir)) {
    foreach (glob($dataDir . '/*.json') as $file) {
        $files[] = $file;
    }
}
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Payments</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
<?php include(__DIR__ . '/includes/top.php'); include(__DIR__ . '/includes/menu.php'); ?>
<div class="container-wide panel">
  <h1>Payments (webhook)</h1>
  <?php if (!$files): ?>
    <p>No payment records found in <?php echo h($dataDir); ?></p>
  <?php else: ?>
    <table class="cart-table">
      <thead>
        <tr>
          <th>Filename</th>
          <th>Invoice</th>
          <th>Amount</th>
          <th>Payer</th>
          <th>Date</th>
          <th>View</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ($files as $f): $j = json_decode(file_get_contents($f), true) ?: []; ?>
        <tr>
          <td><?php echo h(basename($f)); ?></td>
          <td><?php echo h($j['invoice'] ?? ($j['custom'] ?? '')); ?></td>
          <td><?php echo h(($j['currency'] ?? '') . ' ' . number_format((float)($j['amount'] ?? 0),2)); ?></td>
          <td><?php echo h($j['payer'] ?? ''); ?></td>
          <td><?php echo h($j['ts'] ?? ''); ?></td>
          <td><a href="return.php?invoice=<?php echo urlencode($j['invoice'] ?? ($j['custom'] ?? '')); ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
