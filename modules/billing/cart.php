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
    // load invoice to verify ownership/price (invoice-first flow)
  $stmt = $db->prepare("SELECT user_id, amount, status, qty, invoice_duration, service_id, home_name, ip, max_players, remote_control_password, ftp_password FROM " . $table_prefix . "billing_invoices WHERE invoice_id = ? LIMIT 1");
    if ($stmt) {
      $stmt->bind_param('i', $orderId);
      $stmt->execute();
  $stmt->bind_result($owner_id, $order_price, $prev_status, $order_qty, $order_invoice_duration, $service_id, $home_name, $ip, $max_players, $remote_control_password, $ftp_password);
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
        // Mark invoice as paid
        $upd_inv = $db->prepare("UPDATE " . $table_prefix . "billing_invoices SET status = 'paid', paid_date = NOW() WHERE invoice_id = ? LIMIT 1");
        if ($upd_inv) {
          $upd_inv->bind_param('i', $orderId);
          $upd_inv->execute();
          $upd_inv->close();
        }
        
        // Now create the order record (invoice -> order after payment)
        // Compute end_date: months based on invoice_duration and qty
        $months = 0;
        $q = intval($order_qty ?? 0);
        $invdur = strtolower(trim($order_invoice_duration ?? ''));
        if (strpos($invdur, 'year') !== false) {
          $months = $q * 12;
        } else {
          // default to months for anything else (month, monthly, etc.)
          $months = $q;
        }
        $end_date = null;
        if ($months > 0) {
          $dt = new DateTime('now');
          $dt->modify('+' . intval($months) . ' months');
          $end_date = $dt->format('Y-m-d H:i:s');
        } else {
          // if no months specified, set to now
          $end_date = date('Y-m-d H:i:s');
        }

        // INSERT new order record (invoice->order after payment)
        $esc_service_id = intval($service_id);
        $esc_home_name = mysqli_real_escape_string($db, $home_name);
        $esc_ip = intval($ip);
        $esc_max_players = intval($max_players);
        $esc_qty = intval($order_qty);
        $esc_inv_dur = mysqli_real_escape_string($db, $order_invoice_duration);
        $esc_price = floatval($order_price);
        $esc_rc_pass = mysqli_real_escape_string($db, $remote_control_password);
        $esc_ftp_pass = mysqli_real_escape_string($db, $ftp_password);
        $esc_user_id = intval($owner_id);
        $esc_end_date = mysqli_real_escape_string($db, $end_date);
        
        $insert_sql = "INSERT INTO " . $table_prefix . "billing_orders 
          (user_id, service_id, home_name, ip, max_players, qty, invoice_duration, price, remote_control_password, ftp_password, status, end_date, payment_txid, paid_ts) 
          VALUES 
          ({$esc_user_id}, {$esc_service_id}, '{$esc_home_name}', {$esc_ip}, {$esc_max_players}, {$esc_qty}, '{$esc_inv_dur}', {$esc_price}, '{$esc_rc_pass}', '{$esc_ftp_pass}', 'paid', '{$esc_end_date}', 'FREE-{$orderId}', NOW())";
        
        $insert_res = mysqli_query($db, $insert_sql);
        $new_order_id = 0;
        if ($insert_res) {
          $new_order_id = mysqli_insert_id($db);
          // Update invoice with the new order_id
          $upd_inv_order = $db->prepare("UPDATE " . $table_prefix . "billing_invoices SET order_id = ? WHERE invoice_id = ? LIMIT 1");
          if ($upd_inv_order) {
            $upd_inv_order->bind_param('ii', $new_order_id, $orderId);
            $upd_inv_order->execute();
            $upd_inv_order->close();
          }
        }

  // write audit log (include end_date if set)
  site_log_info('free_create', ['actor'=>$actor_id, 'role'=>$actor_role, 'action'=>$reason, 'invoice'=>$orderId, 'new_order'=>$new_order_id, 'owner'=>$owner_id, 'price'=>$order_price, 'prev_status'=>$prev_status, 'end_date'=>$end_date ?? '']);

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
    $invoice_id = intval($_POST['delete_single']);
    if ($invoice_id > 0) {
        // Check if this invoice is linked to an order (renewal case)
        $stmt = $db->prepare("SELECT order_id FROM ogp_billing_invoices WHERE invoice_id = ? AND user_id = ?");
        $stmt->bind_param("ii", $invoice_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($linked_order_id);
        $found = $stmt->fetch();
        $stmt->close();
        
    if ($found && $linked_order_id > 0) {
      // This is a renewal invoice - just delete the invoice, keep the order
      $delete = $db->prepare("DELETE FROM ogp_billing_invoices WHERE invoice_id = ? AND user_id = ?");
      $delete->bind_param("ii", $invoice_id, $user_id);
      $delete->execute();
      if (isset($db) && method_exists($db, 'logger')) {
        $db->logger("USER-CART: User " . intval($user_id) . " deleted renewal invoice " . intval($invoice_id));
      }
      $delete->close();
        } else {
            // New order invoice - delete it
            $delete = $db->prepare("DELETE FROM ogp_billing_invoices WHERE invoice_id = ? AND user_id = ?");
            $delete->bind_param("ii", $invoice_id, $user_id);
      $delete->execute();
      if (isset($db) && method_exists($db, 'logger')) {
        $db->logger("USER-CART: User " . intval($user_id) . " deleted invoice " . intval($invoice_id));
      }
      $delete->close();
        }
    }
}

if ($db){
        $carts = $db->query("SELECT * FROM ogp_billing_invoices AS cart
            WHERE status = 'due' AND user_id = " . $user_id . " ORDER BY invoice_id ASC");
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
          <tr data-cart-id="<?php echo htmlspecialchars($row['invoice_id']); ?>">
            <td>
              <form method="post" action="" class="inline-form">
                <button type="submit" name="delete_single" value="<?php echo htmlspecialchars($row['invoice_id']); ?>" class="btn-square text-danger">
                  
                </button>
              </form>
            </td>
            <td><?php echo htmlspecialchars($row['invoice_id']); ?></td>
            <td><?php echo htmlspecialchars($row['home_name']); ?></td>
            <td><?php echo htmlspecialchars($row['ip']); ?></td>
            <td><?php echo htmlspecialchars($row['max_players']); ?></td>
            <td>$<?php echo number_format($row['amount'], 2); ?></td>
            <td><?php echo htmlspecialchars($row['qty']); ?></td>
            <?php $rowtotal = $row['amount'] * $row['qty'] * $row['max_players'];?>
            <?php
              // Build invoice and line item structures used later when creating PayPal order
              if (!isset($invoice) || !is_array($invoice)) $invoice = [];
              $invoice[] = [
                'serverID' => 'invoice-' . $row['invoice_id'],
                'amount'   => number_format($rowtotal, 2, '.', ''),
                'invoice_id' => intval($row['invoice_id'])
              ];
            ?>
            <?php
              // Use the previously resolved $is_admin (computed once above)
              $is_free = ((float)$row['amount'] == 0.0);
            ?>
                <?php if ($is_admin || $is_free): ?>
              <td>
                <form method="post" action="" class="inline-form">
                  <input type="hidden" name="create_free_for" value="<?php echo (int)$row['invoice_id']; ?>">
                      <button type="submit" class="gsw-btn"><?php echo $is_admin ? 'Create (Free)' : 'Claim (Free)'; ?></button>
                </form>
                <?php if ($is_admin): ?>
                  <div class="admin-note">Admin: force-create a paid record for testing.</div>
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
// If the cart contains a single order, set custom_id to the numeric order id so webhooks
// can match the order directly (payment_success matches numeric custom -> order_id).
if (is_array($invoice) && count($invoice) === 1 && !empty($invoice[0]['order_id'])) {
  $customId = (string) intval($invoice[0]['order_id']);
}

// Text on the PayPal side
$description = 'Game server order (' . count($lineItems) . ' item' . (count($lineItems)===1?'': 's') . ')';

// URLs
// Define the site base URL - detect protocol and host dynamically
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'] ?? 'localhost';
$siteBase = $protocol . $host;

// Return URLs are root-relative (website will be deployed at root, not modules/billing)
$returnUrl  = $siteBase . '/payment_success.php?invoice=' . urlencode($invoiceId);
$cancelUrl  = $siteBase . '/payment_cancel.php?invoice=' . urlencode($invoiceId);

// API base (relative) - point to billing module API endpoints
$apiBase = 'api';
?>
<!-- PayPal JS SDK (Sandbox). Use LIVE client-id when going live. -->
<script src="https://www.paypal.com/sdk/js?client-id=AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c&currency=USD&intent=capture"></script>

<!-- Debug: Cart values -->
<?php if (isset($_GET['debug'])): ?>
<div style="background:#f0f0f0; padding:10px; margin:10px 0; font:12px monospace;">
  <strong>Debug Info:</strong><br>
  Grand Total: $<?php echo htmlspecialchars($grandTotal); ?><br>
  Invoice Items: <?php echo count($invoice); ?><br>
  Line Items: <?php echo count($lineItems); ?><br>
  Amount: <?php echo htmlspecialchars($amount); ?><br>
  Invoice ID: <?php echo htmlspecialchars($invoiceId); ?><br>
  Custom ID: <?php echo htmlspecialchars($customId); ?><br>
</div>
<?php endif; ?>

<div id="paypal-button-container"></div>
<div id="pp-status" class="mt-12" style="font:14px system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;"></div>

<script>
(function(){
  const statusEl    = document.getElementById('pp-status');

  // Values from PHP - use json_encode for proper JavaScript escaping
  const amount      = <?php echo json_encode($amount); ?>;
  const currency    = <?php echo json_encode($currency); ?>;
  const invoice_id  = <?php echo json_encode($invoiceId); ?>;
  const custom_id   = <?php echo json_encode($customId); ?>;
  const description = <?php echo json_encode($description); ?>;
  const return_url  = <?php echo json_encode($returnUrl); ?>;
  const cancel_url  = <?php echo json_encode($cancelUrl); ?>;

  // Line items (serverID + per-item amount) for your records and webhook correlation
  const line_invoices = <?php echo json_encode($invoice, JSON_UNESCAPED_SLASHES); ?>;

  // PayPal "items" for purchase_units (shows on PayPal + returns in webhook under purchase_units)
  const items = <?php echo json_encode($lineItems, JSON_UNESCAPED_SLASHES); ?>;

  // Debug logging
  console.log('PayPal cart debug:', {
    amount, currency, invoice_id, custom_id, description,
    line_invoices_count: line_invoices.length,
    items_count: items.length,
    return_url, cancel_url
  });

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
      .then(res => {
        if (!res.ok) {
          return res.text().then(errText => {
            throw new Error('API error ' + res.status + ': ' + errText.substring(0, 200));
          });
        }
        return res.json();
      })
      .then(data => {
        if (!data.id) { 
          throw new Error(JSON.stringify(data).substring(0, 200) || 'No order id'); 
        }
        setStatus('Order created.');
        return data.id;
      })
      .catch(err => {
        setStatus('PayPal error: ' + err.message);
        throw err;
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
