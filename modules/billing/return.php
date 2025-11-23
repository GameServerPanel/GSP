<?php
require_once(__DIR__ . '/includes/config_loader.php');

$dataDir = (isset($SITE_DATA_DIR) && $SITE_DATA_DIR) ? $SITE_DATA_DIR : realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
$invoice = $_GET['invoice'] ?? '';
$cancel  = isset($_GET['cancel']);

$status   = 'PENDING';
$details  = null;
$items    = [];

if ($invoice && is_file($dataDir . DIRECTORY_SEPARATOR . $invoice . '.json')) {
  $details = json_decode(file_get_contents($dataDir . DIRECTORY_SEPARATOR . $invoice . '.json'), true);
  if (!empty($details['status'])) {
    $status = $details['status'];
  }
  if (!empty($details['items']) && is_array($details['items'])) {
    $items = $details['items'];
  }
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function money_fmt($value, $currency) {
  if ($value === null || $value === '') return '';
  return h($currency) . ' ' . h(number_format((float)$value, 2, '.', ''));
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Payment Status</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
<?php include(__DIR__ . '/includes/top.php'); include(__DIR__ . '/includes/menu.php'); ?>
<div class="site-panel">
<?php if ($cancel): ?>
  <h1>Payment canceled</h1>
  <p>Invoice: <?= h($invoice) ?></p>
  <p class="muted">You can return to your cart and try again.</p>
<?php else: ?>
  <h1>Thank you!</h1>

  <p><strong>Invoice:</strong> <?= h($invoice) ?></p>
  <p><strong>Status:</strong> <span><?= h($status) ?></span></p>

  <?php if ($details): ?>
    <h3>Summary</h3>
    <ul>
      <li>Amount: <?= money_fmt($details['amount'] ?? null, $details['currency'] ?? '') ?></li>
      <li>Payer: <?= h($details['payer'] ?? '') ?></li>
      <li>Transaction ID: <code><?= h($details['resource_id'] ?? '') ?></code></li>
      <li>Event: <?= h($details['event_type'] ?? '') ?></li>
      <li>Timestamp: <?= h($details['ts'] ?? '') ?></li>
      <?php if (!empty($details['custom'])): ?>
        <li>Custom: <code><?= h($details['custom']) ?></code></li>
      <?php endif; ?>
    </ul>

    <h3>Items</h3>
    <?php if ($items): ?>
      <table class="cart-table">
        <thead>
          <tr>
            <th>Server ID</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Unit Price</th>
            <th>Line Total</th>
          </tr>
        </thead>
        <tbody>
        <?php
          $currency = $details['currency'] ?? ($items[0]['unit_amount']['currency_code'] ?? '');
          $grand  = 0.00;
          foreach ($items as $it) {
            $name = $it['name'] ?? '';
            $sku  = $it['sku'] ?? '';
            $qty  = isset($it['quantity']) ? (int)$it['quantity'] : 1;
            $unit = isset($it['unit_amount']['value']) ? (float)$it['unit_amount']['value'] : 0.00;
            $line = $qty * $unit;
            $grand += $line;
            echo '<tr>';
            echo '<td>'.h($sku).'</td>';
            echo '<td>'.h($name).'</td>';
            echo '<td>'.h($qty).'</td>';
            echo '<td>'.money_fmt($unit, $currency).'</td>';
            echo '<td>'.money_fmt($line, $currency).'</td>';
            echo '</tr>';
          }
        ?>
          <tr>
            <td colspan="4" class="text-right"><strong>Total</strong></td>
            <td><strong><?= money_fmt($grand, $currency) ?></strong></td>
          </tr>
        </tbody>
      </table>
    <?php else: ?>
      <p class="muted">No line items were included in this webhook. If you just paid, refresh in a few seconds.</p>
    <?php endif; ?>

    <?php if (strtoupper($status) !== 'PAID'): ?>
      <p class="muted">Waiting for confirmation from PayPal… this can take a few seconds. Refresh to update.</p>
    <?php endif; ?>
  <?php else: ?>
    <p class="muted">We’re waiting for PayPal to confirm your payment. This page will show the receipt once we receive the webhook. Try refreshing in a few seconds.</p>
  <?php endif; ?>
<?php endif; ?>
</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
