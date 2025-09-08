-<?php
// ==== YOUR CART DATA (server authoritative) ====
// TODO: set these from your cart/session/DB:
$amount      = number_format(19.99, 2, '.', '');
$currency    = 'USD';
$invoiceId   = 'INV-' . date('Ymd-His') . '-' . bin2hex(random_bytes(3));
$customId    = 'user_1234_order_5678';
$description = 'Game server monthly plan';

// Site base (adjust if different)
$siteBase   = 'https://panel.iaregamer.com';
$returnUrl  = $siteBase . '/paypal/return.php?invoice=' . urlencode($invoiceId);
$cancelUrl  = $siteBase . '/paypal/return.php?invoice=' . urlencode($invoiceId) . '&cancel=1';

// Where your API endpoints live:
$apiBase = '/paypal/api';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Checkout</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- PayPal JS SDK (Sandbox). Use LIVE client-id when you go live. -->
  <script src="https://www.paypal.com/sdk/js?client-id=AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c&currency=USD&intent=capture"></script>
  <style>body{font-family:system-ui,Arial,sans-serif;max-width:700px;margin:40px auto;padding:0 16px}</style>
</head>
<body>
  <h1>Complete your purchase</h1>
  <p><strong>Amount:</strong> <?= htmlspecialchars($currency) ?> <?= htmlspecialchars($amount) ?></p>
  <p><strong>Invoice:</strong> <?= htmlspecialchars($invoiceId) ?></p>
  <div id="paypal-button-container"></div>
  <div id="status" style="margin-top:16px"></div>

<script>
const statusEl    = document.getElementById('status');
const amount      = "<?= $amount ?>";
const currency    = "<?= $currency ?>";
const invoice_id  = "<?= $invoiceId ?>";
const custom_id   = "<?= htmlspecialchars($customId, ENT_QUOTES) ?>";
const description = "<?= htmlspecialchars($description, ENT_QUOTES) ?>";
const return_url  = "<?= $returnUrl ?>";
const cancel_url  = "<?= $cancelUrl ?>";

function setStatus(msg){ statusEl.textContent = msg; }


paypal.Buttons({
  // Show a single, small PayPal button
  style: {
    layout:  'vertical',   // or 'horizontal'
    color:   'gold',       // gold | blue | silver | black | white
    shape:   'pill',       // pill | rect
    label:   'paypal',     // paypal | pay | checkout | buynow
    height:  35,           // 25–55 (smaller button = lower height)
    tagline: false
  },
  fundingSource: paypal.FUNDING.PAYPAL, // only the PayPal button

  createOrder: function() {
    // (unchanged) — your fetch to create_order.php
    return fetch("<?= $apiBase ?>/create_order.php", {
      method: "POST",
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({
        amount, currency, invoice_id, custom_id, description,
        return_url, cancel_url,
        items, line_invoices
      })
    })
    .then(r => r.json())
    .then(d => {
      if (!d.id) throw new Error(d.error || 'No order id');
      return d.id;
    });
  },

  onApprove: function(data) {
    // (unchanged) — capture then redirect
    return fetch("<?= $apiBase ?>/capture_order.php", {
      method: "POST",
      headers: {"Content-Type":"application/json"},
      body: JSON.stringify({ order_id: data.orderID })
    })
    .then(r => r.json())
    .then(c => {
      if (c.status === 'COMPLETED') {
        window.location.href = return_url;
      } else {
        document.getElementById('pp-status').textContent = 'Capture status: ' + c.status;
      }
    });
  },

  onCancel: function(){ window.location.href = cancel_url; },
  onError:  function(err){ document.getElementById('pp-status').textContent = 'PayPal error: ' + err; }
}).render('#paypal-button-container');
</script>

</body>
</html>

