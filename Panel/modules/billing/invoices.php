<?php
// User invoice history (reads payments/data/*.json)
$session_name = session_name(); session_start();
require_once(__DIR__ . '/includes/config_loader.php');
// Intentionally do not require login here; invoices should be viewable (or filtered) without forcing a login.

// try to get logged-in user's email for matching
$user_email = $_SESSION['website_user_email'] ?? '';

$dataDir = (isset($SITE_DATA_DIR) && $SITE_DATA_DIR) ? $SITE_DATA_DIR : realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
$records = [];
if (is_dir($dataDir)) {
    foreach (glob($dataDir . '/*.json') as $file) {
        $j = json_decode(file_get_contents($file), true);
        if (!$j || !is_array($j)) continue;
        // Best-effort match: payer email or custom contains user identifier
        $match = false;
        if ($user_email && !empty($j['payer']) && stripos($j['payer'], $user_email) !== false) $match = true;
        if (!$match && !empty($j['custom']) && stripos($j['custom'], $user_email) !== false) $match = true;
        if (!$match && empty($user_email)) $match = true; // fallback: show everything when no email in session
        if ($match) {
            $records[] = $j;
        }
    }
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your Invoices</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
<?php include(__DIR__ . '/includes/top.php'); include(__DIR__ . '/includes/menu.php'); ?>
<div class="container-wide panel">
  <h1>Your Invoices</h1>
  <?php if (!$records): ?>
    <p>No invoices found for your account.</p>
  <?php else: ?>
  <table class="cart-table">
      <thead>
        <tr>
          <th>Invoice</th><th>Amount</th><th>Payer</th><th>Date</th><th>Details</th>
        </tr>
      </thead>
      <tbody>
      <?php foreach ((array)$records as $r): ?>
        <tr>
          <td><?php echo h($r['invoice'] ?? ($r['custom'] ?? 'NO-ID')); ?></td>
          <td><?php echo h(($r['currency'] ?? '') . ' ' . number_format((float)($r['amount'] ?? 0),2)); ?></td>
          <td><?php echo h($r['payer'] ?? ''); ?></td>
          <td><?php echo h($r['ts'] ?? ''); ?></td>
          <td><a href="return.php?invoice=<?php echo urlencode($r['invoice'] ?? ($r['custom'] ?? '')); ?>">View</a></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
