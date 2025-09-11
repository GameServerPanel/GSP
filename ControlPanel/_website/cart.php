<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
global $db, $view, $settings;

include "panel/_db.php";


$user_id=$_SESSION['user_id'] ?? 0;
$user_id = 186; // For testing purposes, set a default user ID

if ($user_id <= 0) {
    echo "<center><h4>Please login to view your cart</h4></center>";
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

<div style="width:100%; max-width:1000px; margin:auto; padding:1rem; background-color:#ffffff; border-radius:0.75rem; box-shadow:0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);">
   <h2 style="font-size:1.5rem; font-weight:bold; color:#1f2937; margin-bottom:1.5rem; text-align:center;">Your Cart</h2>

   <!-- 
   This is our cart form just for display and deletion.  There is a different form below that has the paypal button and fills in all the hidden fields
   -->

     <table style="border-collapse:separate; border-spacing:0; width:100%; color:#000000;">
        <thead style="background-color:#f9fafb;">
            <tr>
                <th style="padding:1rem 1.5rem; text-align:center; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;"></th>
                <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Server ID</th>

                <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Game Name</th>
                <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Location</th>
                <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Max Players</th>
                <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Price per Player</th>
                <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Months</th>
                 <th style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb; font-weight:600; text-transform:uppercase; font-size:0.75rem; letter-spacing:0.05em;">Total</th>
            </tr>
        </thead>
        <tbody style="background-color:#ffffff;">
            <?php
            $grandTotal = 0; // Initialize grand total variable
            
            if (isset($carts) && $carts instanceof mysqli_result && $carts->num_rows > 0) {
                while ($row = $carts->fetch_assoc()) {
                    ?>
                    <tr data-cart-id="<?php echo htmlspecialchars($row['order_id']); ?>" style="color:#000000;">
                        <td style="padding:1rem 1.5rem; text-align:center; border-bottom:1px solid #e5e7eb;">
                            <form method="post" action="" style="margin:0; display:inline;">
                                <button type="submit" name="delete_single" value="<?php echo htmlspecialchars($row['order_id']); ?>" style="background-color:#ef4444; color:#fff; border:none; border-radius:0.25rem; width:2rem; height:2rem; font-weight:bold; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                                    ✕ 
                                </button>
                            </form>
                        </td>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;"><?php echo htmlspecialchars($row['home_id']); ?></td>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;"><?php echo htmlspecialchars($row['home_name']); ?></td>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;"><?php echo htmlspecialchars($row['ip']); ?></td>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;"><?php echo htmlspecialchars($row['max_players']); ?></td>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;">$<?php echo number_format($row['price'], 2); ?></td>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;"><?php echo htmlspecialchars($row['qty']); ?></td>
                        <?php $rowtotal = $row['price'] * $row['qty'] * $row['max_players'];?>
                        <?php $grandTotal += $rowtotal; // Add to grand total ?>
                        <td style="padding:1rem 1.5rem; text-align:left; border-bottom:1px solid #e5e7eb;">$<?php echo number_format($rowtotal, 2); ?></td>
                        
                        
                    </tr>
                    <?php
                }
                
                // Add total row
                ?>
                <tr style="background-color:#f9fafb; font-weight:bold;">
                    <td colspan="7" style="padding:1rem 1.5rem; text-align:right; border-top:2px solid #374151; font-weight:600; color:#374151;">
                        Cart Total:
                    </td>
                    <td style="padding:1rem 1.5rem; text-align:left; border-top:2px solid #374151; font-weight:600; color:#374151; font-size:1.1rem;">
                        $<?php echo number_format($grandTotal, 2); ?>
                    </td>
                </tr>
                <?php
            } else {
                // Display a message if no cart items are found
                ?>
                <tr>
                    <td colspan="7" style="text-align:center; padding:1rem; color:#6b7280;">No items in your cart.</td>
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
$returnUrl  = $siteBase . '/paypal/return.php?invoice=' . urlencode($invoiceId);
$cancelUrl  = $siteBase . '/paypal/return.php?invoice=' . urlencode($invoiceId) . '&cancel=1';

// API base (relative)
$apiBase = '/paypal/api';
?>
<!-- PayPal JS SDK (Sandbox). Use LIVE client-id when going live. -->
<script src="https://www.paypal.com/sdk/js?client-id=AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c&currency=USD&intent=capture"></script>

<div id="paypal-button-container"></div>
<div id="pp-status" style="margin-top:12px;font:14px system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif;"></div>

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

