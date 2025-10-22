<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - GameServers.World</title>
</head>
<body>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Require login
require_once(__DIR__ . '/includes/login_required.php');

// Include database configuration
require_once(__DIR__ . '/includes/config.inc.php');

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Admin quick-create handler: create a free "paid" record for an in-cart order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['create_free_for'])) {
  session_start();
  if (!empty($_SESSION['website_user_role']) && strtolower($_SESSION['website_user_role']) === 'admin') {
    $orderId = (int)$_POST['create_free_for'];
    if ($orderId > 0) {
      $stmt = $db->prepare("UPDATE ogp_billing_orders SET status = 'paid' WHERE order_id = ? LIMIT 1");
      if ($stmt) { $stmt->bind_param('i', $orderId); $stmt->execute(); $stmt->close(); }

      // write a simulated webhook file
  require_once(__DIR__ . '/includes/config.inc.php');
  $dataDir = (isset($SITE_DATA_DIR) && $SITE_DATA_DIR) ? $SITE_DATA_DIR : realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
      @mkdir($dataDir, 0775, true);
      $rec = [
        'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
        'status' => 'PAID',
        'amount' => 0.00,
        'currency' => 'USD',
        'payer' => $_SESSION['website_user_email'] ?? ($_SESSION['website_username'] ?? ''),
        'invoice' => 'FREE-' . $orderId . '-' . time(),
        'custom' => 'admin_free_create_order_' . $orderId,
        'resource_id' => 'FREE-' . bin2hex(random_bytes(6)),
        'items' => [],
        'ts' => date('c'),
      ];
  $fname = $dataDir . DIRECTORY_SEPARATOR . $rec['invoice'] . '.json';
  file_put_contents($fname, json_encode($rec, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    header('Location: return.php?invoice=' . urlencode($rec['invoice']));
      exit;
    }
  }
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

$user_id=$_SESSION['user_id'] ?? 0;
$user_id = 186; // For testing purposes, set a default user ID

if ($user_id <= 0) {
    echo "<center><h4>Please login to view your cart</h4></center>";
    mysqli_close($db);
    echo "</body></html>";
    return;
}



if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_single'])) {
    $order_id = intval($_POST['delete_single']);
    if ($order_id > 0) {
        // First, check if the status is 'renew'
        $stmt = $db->prepare("SELECT status FROM ogp_billing_orders WHERE order_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $order_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($status);
        if ($stmt->fetch() && strtolower($status) === 'renew') {
            $stmt->close();
            // Set status to 'expired' if currently 'renew'
            $update = $db->prepare("UPDATE ogp_billing_orders SET status = 'expired' WHERE order_id = ? AND user_id = ?");
            $update->bind_param("ii", $order_id, $user_id);
            $update->execute();
            $update->close();
        } else {
            $stmt->close();
            // Otherwise, delete the order
            $delete = $db->prepare("DELETE FROM ogp_billing_orders WHERE order_id = ? AND user_id = ?");
            $delete->bind_param("ii", $order_id, $user_id);
            $delete->execute();
            $delete->close();
        }
    }
}

if ($db){
        $carts = $db->query("SELECT * FROM ogp_billing_orders AS cart
            WHERE (status = 'in-cart' OR status = 'renew') AND user_id = " . $user_id . " ORDER BY order_id ASC");
	


}

?> 

<div class="site-panel">
  <h2 class="site-panel-title">Your Cart</h2>

   <!-- 
   This is our cart form just for display and deletion.  There is a different form below that has the paypal button and fills in all the hidden fields
   -->

  <table class="cart-table">
    <thead>
      <tr>
        <th class="table-compact text-center"></th>
        <th>Server ID</th>
        <th>Game Name</th>
        <th>Location</th>
        <th>Max Players</th>
        <th>Price per Player</th>
        <th>Months</th>
        <th>Total</th>
      </tr>
    </thead>
  <tbody>
            <?php
            $grandTotal = 0; // Initialize grand total variable
            
            if (isset($carts) && $carts instanceof mysqli_result && $carts->num_rows > 0) {
                while ($row = $carts->fetch_assoc()) {
                    ?>
          <tr data-cart-id="<?php echo htmlspecialchars($row['order_id']); ?>">
            <td>
              <form method="post" action="" class="inline-form">
                <button type="submit" name="delete_single" value="<?php echo htmlspecialchars($row['order_id']); ?>" class="btn-square text-danger">
                  
                </button>
              </form>
            </td>
            <td><?php echo htmlspecialchars($row['home_id']); ?></td>
            <td><?php echo htmlspecialchars($row['home_name']); ?></td>
            <td><?php echo htmlspecialchars($row['ip']); ?></td>
            <td><?php echo htmlspecialchars($row['max_players']); ?></td>
            <td>$<?php echo number_format($row['price'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['qty']); ?></td>
            <?php $rowtotal = $row['price'] * $row['qty'] * $row['max_players'];?>
            <?php if ((float)$row['price'] == 0.0 && isset($_SESSION['website_user_role']) && strtolower($_SESSION['website_user_role']) === 'admin'): ?>
              <td>
                <form method="post" action="" class="inline-form">
                  <input type="hidden" name="create_free_for" value="<?php echo (int)$row['order_id']; ?>">
                  <button type="submit" class="btn-primary">Create (Free)</button>
                </form>
              </td>
            <?php else: ?>
              <td>&nbsp;</td>
            <?php endif; ?>
                        <?php $grandTotal += $rowtotal; // Add to grand total ?>
                        <td>$<?php echo number_format($rowtotal, 2); ?></td>
                        
                        
                    </tr>
                    <?php
                }
                
                // Add total row
                ?>
        <tr class="cart-total-row">
          <td colspan="7" class="cart-total-label">
                        Cart Total:
                    </td>
          <td class="cart-total-value">
            $<?php echo number_format($grandTotal, 2); ?>
          </td>
                </tr>
                <?php
            } else {
                // Display a message if no cart items are found
                ?>
                <tr>
                    <td colspan="7" class="text-center muted">No items in your cart.</td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>


<?php
// These must already exist earlier in your cart page:
// $grandTotal  (number)  e.g., 24.49
// $invoice     (array)   e.g., [['serverID'=>'srv123','amount'=>9.99], ['serverID'=>'srv999','amount'=>14.50]]

// --- Sanity + normalization ---
if (!isset($grandTotal) || !is_numeric($grandTotal)) {
  $grandTotal = 0.00;
}
if (!isset($invoice) || !is_array($invoice)) {
  $invoice = [];
}
$currency    = 'USD';
$amount      = number_format((float)$grandTotal, 2, '.', '');
$lineItems   = [];

// Build PayPal-friendly items array (name, unit_amount, quantity, sku)
foreach ($invoice as $i) {
  $sid = isset($i['serverID']) ? (string)$i['serverID'] : 'unknown';
  $amt = isset($i['amount']) && is_numeric($i['amount']) ? number_format((float)$i['amount'], 2, '.', '') : '0.00';
  $lineItems[] = [
    'name'        => "Server $sid",
    'quantity'    => '1',
    'unit_amount' => ['currency_code' => $currency, 'value' => $amt],
    'sku'         => $sid
  ];
}

// Single overall invoice id for the order
$invoiceId   = 'INV-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3));

// A short custom reference derived from your line items (<= 127 chars for PayPal)
$customHash  = substr(strtoupper(sha1(json_encode($invoice))), 0, 16);
$customId    = "INVREF-$customHash";

// Text on the PayPal side
$description = 'Game server order (' . count($lineItems) . ' item' . (count($lineItems)===1?'': 's') . ')';

// URLs
$siteBase   = 'https://panel.iaregamer.com';
$returnUrl  = $siteBase . '/_website/return.php?invoice=' . urlencode($invoiceId);
$cancelUrl  = $siteBase . '/_website/return.php?invoice=' . urlencode($invoiceId) . '&cancel=1';

// API base (relative)
$apiBase = '/_website/api';
?>
<!-- PayPal JS SDK (Sandbox). Use LIVE client-id when going live. -->
<script src="https://www.paypal.com/sdk/js?client-id=AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c&currency=USD&intent=capture"></script>

<div id="paypal-button-container"></div>
<div id="pp-status" class="mt-12" style="font:14px system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;"></div>

<script>
(function(){
  const statusEl    = document.getElementById('pp-status');

  // Values from PHP
  const amount      = "<?= $amount ?>";
  const currency    = "<?= $currency ?>";
  const invoice_id  = "<?= $invoiceId ?>";
  const custom_id   = "<?= $customId ?>";
  const description = "<?= htmlspecialchars($description, ENT_QUOTES) ?>";
  const return_url  = "<?= $returnUrl ?>";
  const cancel_url  = "<?= $cancelUrl ?>";

  // Line items (serverID + per-item amount) for your records and webhook correlation
  const line_invoices = <?php echo json_encode($invoice, JSON_UNESCAPED_SLASHES); ?>;

  // PayPal "items" for purchase_units (shows on PayPal + returns in webhook under purchase_units)
  const items = <?php echo json_encode($lineItems, JSON_UNESCAPED_SLASHES); ?>;

  function setStatus(msg){ if(statusEl) statusEl.textContent = msg; }

  paypal.Buttons({
    createOrder: function() {
      setStatus('Creating order…');
      return fetch("<?= $apiBase ?>/create_order.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({
          amount, currency, invoice_id, custom_id, description,
          return_url, cancel_url,
          // The next two are for your server to include:
          items,             // PayPal purchase_units[0].items
          line_invoices      // your raw cart detail, persisted in your DB if you choose
        })
      })
      .then(res => res.json())
      .then(data => {
        if (!data.id) { throw new Error(data.error || 'No order id'); }
        setStatus('Order created.');
        return data.id;
      });
    },

    onApprove: function(data) {
      setStatus('Capturing payment…');
      return fetch("<?= $apiBase ?>/capture_order.php", {
        method: "POST",
        headers: {"Content-Type":"application/json"},
        body: JSON.stringify({ order_id: data.orderID })
      })
      .then(res => res.json())
      .then(capture => {
        if (capture.status === 'COMPLETED') {
          // go to your return page; webhook will fill data/<invoice_id>.json
          window.location.href = return_url;
        } else {
          setStatus('Capture status: ' + capture.status);
        }
      })
      .catch(err => setStatus('Error: ' + err.message));
    },

    onCancel: function() {
      window.location.href = cancel_url;
    },

    onError: function(err){
      setStatus('PayPal error: ' + (err && err.message ? err.message : err));
    }
  }).render('#paypal-button-container');
})();
</script>
  

</div>

<?php
// Close database connection
mysqli_close($db);
?>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
