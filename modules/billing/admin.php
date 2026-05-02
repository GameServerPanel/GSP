<?php
// Admin landing page
require_once(__DIR__ . '/includes/admin_auth.php');
require_once(__DIR__ . '/includes/config_loader.php');
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Admin — Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="css/header.css">
</head>
<body>
<div class="container-wide panel">
  <h1>Admin Dashboard</h1>
  <p>Welcome to the admin area. From here you can manage servers, payments, and site settings.</p>

  <div class="admin-flex-wrap">
  <a class="gsw-btn" href="adminserverlist.php">Manage Servers &amp; Services</a>
  <a class="gsw-btn" href="admin_invoices.php">Manage Invoices</a>
  <a class="gsw-btn" href="admin_payments.php">Transaction Log</a>
  <a class="gsw-btn" href="admin_coupons.php">Manage Coupons</a>
  <a class="gsw-btn" href="admin_config.php">Edit Site Config</a>
  <a class="gsw-btn" href="admin_xml_editor.php">XML Config Editor</a>
  <a class="gsw-btn" href="docs/xml_notes.php">XML Config Guide</a>
  </div>

  <hr>
  <h3>Quick usage notes</h3>
  <ul>
    <li>The <strong>Manage Servers & Services</strong> page allows enabling/disabling nodes and editing service rows.</li>
    <li>The <strong>Invoice History</strong> page reads JSON payment records from <code><?php echo h($SITE_DATA_DIR); ?></code>.</li>
    <li>The <strong>Edit Site Config</strong> page edits <code>_website/includes/config.inc.php</code>. Edits create a timestamped backup before saving.</li>
  </ul>

  <h3>Sandbox account (testing)</h3>
  <p>Use PayPal sandbox credentials when testing payments. Set your sandbox <code>client_id</code> and <code>client_secret</code> in <code>modules/billing/includes/config.inc.php</code> (the <code>$paypal_client_id</code> and <code>$paypal_client_secret</code> variables). Set <code>$paypal_sandbox = false</code> for live payments.</p>
  <ul>
    <li>Create a sandbox business account at <a href="https://developer.paypal.com">PayPal Developer</a> and obtain a sandbox client ID/secret.</li>
    <li>Update the payment handler config and restart the webserver if required.</li>
    <li>Run a checkout using the PayPal JS button on the checkout page — after payment completes, the webhook will record a JSON file into <code><?php echo h($SITE_DATA_DIR); ?></code>.</li>
    <li>If you need to simulate a webhook locally, drop a JSON file with the same schema into the <code>data/</code> folder (we added a sample: <code>SIMULATED-WEBHOOK-*.json</code>).</li>
  </ul>

  <h3>Payments: high-level program flow</h3>
  <ol>
    <li>User adds an item and proceeds to checkout (<code>_website/cart.php</code>).</li>
    <li>The checkout page renders the PayPal JS SDK and calls server-side endpoints (create_order/capture_order).</li>
  <li>After a successful capture, PayPal sends a webhook event to <code>_website/webhook.php</code> (or the equivalent handler under <code>_website/api/</code>).</li>
    <li>The webhook verifies the signature, fetches any missing order details, and writes a JSON record to the <code>data/</code> directory (this powers <code>invoices.php</code> and <code>return.php</code>).</li>
    <li>On successful payment we mark the order as PAID in the JSON and the site UI (invoices/returns) reads those JSONs to render receipts.</li>
    <li>Admin pages can view invoices at <code>./invoices.php</code> and reconcile or trigger further provisioning via internal panel APIs.</li>
  </ol>

  <h3>Environment</h3>
  <table class="cart-table">
    <tr><th>Site Base URL</th><td><?php echo h($SITE_BASE_URL ?: '(empty — using relative paths)'); ?></td></tr>
    <tr><th>Data directory</th><td><?php echo h($SITE_DATA_DIR); ?></td></tr>
    <tr><th>PHP SAPI</th><td><?php echo h(PHP_SAPI); ?></td></tr>
    <tr><th>Writable?</th><td><?php echo is_writable(__DIR__ . '/data') ? 'yes' : 'no'; ?></td></tr>
  </table>

</div>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</body>
</html>
