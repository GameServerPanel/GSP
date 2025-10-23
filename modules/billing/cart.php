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
require_once(__DIR__ . '/includes/log.php');

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handler: allow admin quick-create OR user claim for free items
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['create_free_for'])) {
  if (session_status() === PHP_SESSION_NONE) session_start();
  $actor_id = intval($_SESSION['website_user_id'] ?? $_SESSION['user_id'] ?? 0);
  $actor_role = strtolower($_SESSION['website_user_role'] ?? '');
  $is_admin = ($actor_role === 'admin');

  // Fallback: if session role not present, try to resolve from DB using actor_id or website_username
  if (!$is_admin) {
    if ($actor_id > 0) {
      $ar = mysqli_query($db, "SELECT users_role FROM ogp_users WHERE user_id = " . intval($actor_id) . " LIMIT 1");
      if ($ar && mysqli_num_rows($ar) === 1) {
        $arr = mysqli_fetch_assoc($ar);
        if (strtolower((string)($arr['users_role'] ?? '')) === 'admin') {
          $is_admin = true;
          $_SESSION['website_user_role'] = 'admin';
        }
      }
    } elseif (isset($_SESSION['website_username']) && !empty($_SESSION['website_username'])) {
      $safe_un = mysqli_real_escape_string($db, $_SESSION['website_username']);
      $ar = mysqli_query($db, "SELECT user_id, users_role FROM ogp_users WHERE users_login = '$safe_un' LIMIT 1");
      if ($ar && mysqli_num_rows($ar) === 1) {
        $arr = mysqli_fetch_assoc($ar);
        if (strtolower((string)($arr['users_role'] ?? '')) === 'admin') {
          $is_admin = true;
          $_SESSION['website_user_role'] = 'admin';
          $_SESSION['website_user_id'] = intval($arr['user_id'] ?? 0);
        }
      }
    }
  }
  $orderId = (int)$_POST['create_free_for'];
  if ($orderId > 0) {
    // load order to verify ownership/price
  $stmt = $db->prepare("SELECT user_id, price, status, qty, invoice_duration FROM ogp_billing_orders WHERE order_id = ? LIMIT 1");
    if ($stmt) {
      $stmt->bind_param('i', $orderId);
      $stmt->execute();
  $stmt->bind_result($owner_id, $order_price, $prev_status, $order_qty, $order_invoice_duration);
      $found = $stmt->fetch();
      $stmt->close();
    } else {
      $found = false;
    }

    $audit_file = __DIR__ . '/logs/free_create_audit.log';

    if ($found) {
      $allowed = false;
      $reason = '';
      // Admin may force-create paid records for testing
      if ($is_admin) {
        $allowed = true;
        $reason = 'admin_create';
      }
      // Owner may claim a free order if the price is zero
      elseif ($actor_id > 0 && $actor_id === intval($owner_id) && floatval($order_price) == 0.0) {
        $allowed = true;
        $reason = 'user_claim_free';
      }

      if ($allowed) {
        // Compute finish_date: months based on invoice_duration and qty
        $months = 0;
        $q = intval($order_qty ?? 0);
        $invdur = strtolower(trim($order_invoice_duration ?? ''));
        if (strpos($invdur, 'year') !== false) {
          $months = $q * 12;
        } else {
          // default to months for anything else (month, monthly, etc.)
          $months = $q;
        }
        $finish_date = null;
        if ($months > 0) {
          $dt = new DateTime('now');
          $dt->modify('+' . intval($months) . ' months');
          $finish_date = $dt->format('Y-m-d H:i:s');
        } else {
          // if no months specified, set to now
          $finish_date = date('Y-m-d H:i:s');
        }

        // Check if finish_date column exists
        $finish_col_exists = false;
        $col_check = mysqli_query($db, "SHOW COLUMNS FROM ogp_billing_orders LIKE 'finish_date'");
        if ($col_check && mysqli_num_rows($col_check) > 0) $finish_col_exists = true;

        // Perform update and log results. Use prepared statements when available and fallback to direct query on error.
        $updated_rows = 0;
        if ($finish_col_exists) {
          $upd = $db->prepare("UPDATE ogp_billing_orders SET status = 'paid', finish_date = ? WHERE order_id = ? LIMIT 1");
          if ($upd) {
            $upd->bind_param('si', $finish_date, $orderId);
            $ok = $upd->execute();
            if (!$ok) site_log_warn('free_create_update_failed_prepare', ['error'=>$db->error, 'sql'=>'UPDATE with finish_date', 'order'=>$orderId]);
            $updated_rows = $upd->affected_rows;
            $upd->close();
          } else {
            // fallback
            $safe_fd = mysqli_real_escape_string($db, $finish_date);
            $q = "UPDATE ogp_billing_orders SET status = 'paid', finish_date = '$safe_fd' WHERE order_id = " . intval($orderId) . " LIMIT 1";
            $resq = mysqli_query($db, $q);
            if (!$resq) site_log_warn('free_create_update_failed_query', ['error'=>mysqli_error($db), 'sql'=>$q]);
            else $updated_rows = mysqli_affected_rows($db);
          }
        } else {
          $upd = $db->prepare("UPDATE ogp_billing_orders SET status = 'paid' WHERE order_id = ? LIMIT 1");
          if ($upd) {
            $upd->bind_param('i', $orderId);
            $ok = $upd->execute();
            if (!$ok) site_log_warn('free_create_update_failed_prepare', ['error'=>$db->error, 'sql'=>'UPDATE status only', 'order'=>$orderId]);
            $updated_rows = $upd->affected_rows;
            $upd->close();
          } else {
            $q = "UPDATE ogp_billing_orders SET status = 'paid' WHERE order_id = " . intval($orderId) . " LIMIT 1";
            $resq = mysqli_query($db, $q);
            if (!$resq) site_log_warn('free_create_update_failed_query', ['error'=>mysqli_error($db), 'sql'=>$q]);
            else $updated_rows = mysqli_affected_rows($db);
          }
        }

  // write audit log (include finish_date if set)
  site_log_info('free_create', ['actor'=>$actor_id, 'role'=>$actor_role, 'action'=>$reason, 'order'=>$orderId, 'owner'=>$owner_id, 'price'=>$order_price, 'prev_status'=>$prev_status, 'finish_date'=>$finish_date ?? '', 'updated_rows'=>$updated_rows]);

        // write a simulated webhook file (same behavior as previous admin flow)
        $dataDir = (isset($SITE_DATA_DIR) && $SITE_DATA_DIR) ? $SITE_DATA_DIR : realpath(__DIR__ . '/') . DIRECTORY_SEPARATOR . 'data';
        @mkdir($dataDir, 0775, true);
        $rec = [
          'event_type' => 'PAYMENT.CAPTURE.COMPLETED',
          'status' => 'PAID',
          'amount' => floatval($order_price),
          'currency' => 'USD',
          'payer' => $_SESSION['website_user_email'] ?? ($_SESSION['website_username'] ?? ''),
          'invoice' => 'FREE-' . $orderId . '-' . time(),
          // process_payment_record matches numeric custom values to order_id; use numeric order id here to ensure matching
          'custom' => (string)$orderId,
          'resource_id' => 'FREE-' . bin2hex(random_bytes(6)),
          'items' => [],
          'ts' => date('c'),
        ];
        $fname = $dataDir . DIRECTORY_SEPARATOR . $rec['invoice'] . '.json';
        file_put_contents($fname, json_encode($rec, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));

        // If available, process the payment record immediately so webhooks logic runs during creation
        $ps = __DIR__ . '/payment_success.php';
        if (is_file($ps)) {
          try {
            require_once($ps);
            if (function_exists('process_payment_record')) {
              process_payment_record($rec);
            }
          } catch (Exception $e) {
            error_log('[cart create_free] process_payment_record failed: ' . $e->getMessage());
          }
        }

        header('Location: return.php?invoice=' . urlencode($rec['invoice']));
        exit;
      } else {
  // unauthorized attempt - log and continue
  site_log_warn('unauthorized_free_create', ['actor'=>$actor_id, 'role'=>$actor_role, 'order'=>$orderId, 'owner'=>$owner_id, 'price'=>$order_price]);
      }
    }
  }
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

// Use session user_id where available
// Use session user_id where available; if not present but website_username exists, try to resolve it from DB
$user_id = intval($_SESSION['website_user_id'] ?? $_SESSION['user_id'] ?? 0);
if ($user_id <= 0 && isset($_SESSION['website_username']) && !empty($_SESSION['website_username'])) {
  // try to resolve username to user_id in DB and persist into session
  $safe_uname = mysqli_real_escape_string($db, $_SESSION['website_username']);
  $qr = mysqli_query($db, "SELECT user_id FROM ogp_users WHERE users_login = '$safe_uname' LIMIT 1");
  if ($qr && mysqli_num_rows($qr) === 1) {
    $rr = mysqli_fetch_assoc($qr);
    $user_id = intval($rr['user_id'] ?? 0);
    if ($user_id > 0) {
      $_SESSION['website_user_id'] = $user_id;
      site_log_info('cart_resolved_user_id', ['username'=>$_SESSION['website_username'],'user_id'=>$user_id]);
      // Resolve and persist the user's role to avoid extra DB lookups later
      $role_q = mysqli_query($db, "SELECT users_role FROM ogp_users WHERE user_id = " . intval($user_id) . " LIMIT 1");
      if ($role_q && mysqli_num_rows($role_q) === 1) {
        $role_r = mysqli_fetch_assoc($role_q);
        $_SESSION['website_user_role'] = $role_r['users_role'] ?? '';
      }
    }
  } else {
    site_log_warn('cart_resolve_user_failed', ['username'=>$_SESSION['website_username']]);
  }
}

if ($user_id <= 0) {
    echo "<center><h4>Please login to view your cart</h4></center>";
    mysqli_close($db);
    echo "</body></html>";
    return;
}

// Determine admin status for UI: prefer session role, otherwise check DB
$is_admin = false;
if (isset($_SESSION['website_user_role']) && !empty($_SESSION['website_user_role'])) {
  $is_admin = (strtolower($_SESSION['website_user_role']) === 'admin');
} elseif ($user_id > 0) {
  $rr = mysqli_query($db, "SELECT users_role FROM ogp_users WHERE user_id = " . intval($user_id) . " LIMIT 1");
  if ($rr && mysqli_num_rows($rr) === 1) {
    $rrow = mysqli_fetch_assoc($rr);
    $is_admin = (strtolower((string)($rrow['users_role'] ?? '')) === 'admin');
  }
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
            <?php
              // Use the previously resolved $is_admin (computed once above)
              $is_free = ((float)$row['price'] == 0.0);
            ?>
            <?php if ($is_admin || $is_free): ?>
              <td>
                <form method="post" action="" class="inline-form">
                  <input type="hidden" name="create_free_for" value="<?php echo (int)$row['order_id']; ?>">
                  <button type="submit" class="btn-primary"><?php echo $is_admin ? 'Create (Free)' : 'Claim (Free)'; ?></button>
                </form>
                <?php if ($is_admin): ?>
                  <div style="font-size:11px;color:#666;margin-top:4px;">Admin: force-create a paid record for testing.</div>
                <?php endif; ?>
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
