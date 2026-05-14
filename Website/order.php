<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Server - GameServers.World</title>
    <link rel="stylesheet" href="css/header.css">
    <style>
        body { margin: 0; padding: 0; }
        .order-shell { width: min(1100px, 100% - 24px); margin: 20px auto 28px; }
        .order-catalog-item { margin: 0 0 20px; }
        .order-layout { display: flex; gap: 18px; align-items: flex-start; flex-wrap: wrap; }
        .order-media { flex: 0 1 310px; max-width: 100%; }
        .order-media img { width: 100%; max-width: 280px; height: auto; border-radius: 8px; display: block; }
        .order-media-title { margin: 10px 0 6px; text-align: center; }
        .order-media-desc { color: #c6c6c6; max-width: 100%; word-break: break-word; }
        .order-form-card { flex: 1 1 500px; max-width: 100%; background: rgba(0,0,0,0.25); border-radius: 10px; padding: 14px; }
        .order-form-table { width: 100%; border-collapse: collapse; table-layout: fixed; }
        .order-form-table td { padding: 8px 6px; vertical-align: top; }
        .order-form-table td:first-child { width: 34%; }
        .order-form-table input[type="text"],
        .order-form-table input[type="number"],
        .order-form-table select,
        .order-form-table textarea {
            width: 100%;
            min-height: 40px;
            border-radius: 8px;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 8px 10px;
        }
        .order-form-table input[type="radio"] { margin-right: 8px; }
        .location-option { margin-bottom: 8px; }
        .slidecontainer { max-width: 100%; }
        .slidecontainer .slider { width: 100%; }
        .order-pricing { line-height: 1.5; word-break: break-word; }
        .order-actions { display: flex; gap: 10px; flex-wrap: wrap; }
        .order-actions .gsw-btn,
        .order-actions .gsw-btn-secondary { width: auto; }
        .order-back-form { margin: 0; }

        @media (max-width: 768px) {
            .order-shell { width: min(100%, calc(100% - 16px)); margin: 12px auto 20px; }
            .order-layout { gap: 12px; }
            .order-media { flex: 1 1 100%; display: flex; flex-direction: column; align-items: center; }
            .order-media img { max-width: min(100%, 260px); }
            .order-form-card { flex: 1 1 100%; padding: 10px; }
            .order-form-table,
            .order-form-table tbody,
            .order-form-table tr,
            .order-form-table td { display: block; width: 100%; }
            .order-form-table td:first-child { width: 100%; padding-bottom: 2px; }
            .order-form-table td { padding: 6px 4px; }
            .order-actions { flex-direction: column; }
            .order-actions .gsw-btn,
            .order-actions .gsw-btn-secondary { width: 100%; text-align: center; }
        }
    </style>
</head>
<body>
<?php

/*
This is the "order gameserver" page. It displays the options for a single specific game server and
has the "Add to Cart" button. The gameserver selected is passed from the serverlist page by a GET
of the service_id. When the user clicks "Add to Cart", the next page is add_to_cart.php.

Each enabled billing service row is listed and purchased as its own exact variant.
The selected service_id remains the source of truth for checkout and provisioning.
*/

// Require login for ordering
require_once(__DIR__ . '/includes/login_required.php');

// Include billing bootstrap (loads config and DB helper)
require_once(__DIR__ . '/bootstrap.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

// Include top bar and menu
include(__DIR__ . '/includes/top.php');
include(__DIR__ . '/includes/menu.php');

if (isset($_POST['save']) && !empty($_POST['description'])) {
    $new_description = str_replace("\\r\\n", "<br>", $_POST['description']);
    $service = intval($_POST['service_id']);
    $stmt = $db->prepare("UPDATE {$table_prefix}billing_services SET description = ? WHERE service_id = ?");
    if ($stmt) {
        $stmt->bind_param("si", $new_description, $service);
        $stmt->execute();
        $stmt->close();
    }
}

function order_price_is_free($value): bool
{
    return ((int) round(((float)$value) * 100)) === 0;
}

function order_detect_service_os(string $cfgFile, string $gameKey): string
{
    $haystack = strtolower(trim($cfgFile !== '' ? $cfgFile : $gameKey));
    if ($haystack === '') {
        return 'any';
    }
    if (preg_match('/(?:^|[_\\-])(win|windows)(?:[_\\-]|$)/i', $haystack)) {
        return 'windows';
    }
    if (preg_match('/(?:^|[_\\-])linux(?:[_\\-]|$)/i', $haystack)) {
        return 'linux';
    }
    return 'any';
}

function order_variant_label(string $serviceOs): string
{
    if ($serviceOs === 'windows') {
        return 'Windows';
    }
    if ($serviceOs === 'linux') {
        return 'Linux';
    }
    return '';
}

// --- Fetch the requested service with config_homes join for canonical game info ---
$req_service_id = intval($_REQUEST['service_id'] ?? 0);
if ($req_service_id !== 0) {
    $where_service_id = " WHERE bs.enabled = 1 AND bs.service_id=" . $req_service_id;
} else {
    $where_service_id = " WHERE bs.enabled = 1";
}

$qry_services = "SELECT bs.*, ch.game_name AS cfg_game_name, ch.game_key AS cfg_game_key, ch.home_cfg_file AS cfg_file
                 FROM {$table_prefix}billing_services bs
                 LEFT JOIN {$table_prefix}config_homes ch ON ch.home_cfg_id = bs.home_cfg_id
                 {$where_service_id}
                 ORDER BY bs.service_name";
$services_result = $db->query($qry_services);

if ($services_result === false) {
    // Fallback: query without join if config_homes doesn't exist in this context
    $where_service_id_simple = str_replace('bs.', '', $where_service_id);
    $qry_services = "SELECT *, NULL AS cfg_game_name, NULL AS cfg_game_key, NULL AS cfg_file
                      FROM {$table_prefix}billing_services
                      {$where_service_id_simple}
                      ORDER BY service_name";
    $services_result = $db->query($qry_services);
}

if ($services_result === false) {
    echo "<p class='error'>Unable to load service information. Please try again or contact support.</p>";
    error_log("billing order.php: query failed - " . $db->error);
    billing_maybe_close_db($db);
    include(__DIR__ . '/includes/footer.php');
    echo '</body></html>';
    exit;
}

$serviceRows = [];
while ($row = $services_result->fetch_assoc()) {
    $serviceRows[] = $row;
}
$services_result->free();

if ($req_service_id !== 0 && empty($serviceRows)) {
    error_log("billing order.php: service_id={$req_service_id} not found or not enabled");
    echo "<p class='error'>The requested service could not be found or is no longer available.</p>";
    echo "<p><a href='serverlist.php'>Back to server list</a></p>";
    billing_maybe_close_db($db);
    include(__DIR__ . '/includes/footer.php');
    echo '</body></html>';
    exit;
}

// Check whether remote_servers has a server_os column (added by db_version 6 migration).
// We gracefully degrade: if the column is absent, all servers are treated as compatible.
$hasServerOsColumn = false;
$osColCheck = $db->query("SHOW COLUMNS FROM {$table_prefix}remote_servers LIKE 'server_os'");
if ($osColCheck && $osColCheck->num_rows > 0) {
    $hasServerOsColumn = true;
    $osColCheck->free();
}

$order_error_message = isset($_GET['error_message']) ? trim((string)$_GET['error_message']) : '';

?>
<div class="order-shell">
<div class="clearfix">
<?php
foreach ($serviceRows as $row)
{
if (!isset($_REQUEST['service_id']))
{
?>
<div class="float-left p-30-20 order-catalog-item">
<?php
$imgSrc = billing_image_url((string)($row['img_url'] ?? ''));
if ($imgSrc === '') { $imgSrc = '/images/games/default_server.png'; }
?>
<img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" width="460" height="225"
     onerror="this.src='/images/games/default_server.png'; this.onerror=null;">
<br>
<?php echo htmlspecialchars((string)($row['cfg_game_name'] ?? $row['service_name']), ENT_QUOTES, 'UTF-8'); ?>
<br>
<?php
if (order_price_is_free($row['price_monthly'] ?? 0)) {
echo "FREE";
} else {
echo "$" . number_format(floatval($row['price_monthly']), 2) . " Monthly";
}
?>
<br>
<a href="order.php?service_id=<?php echo intval($row['service_id']); ?>" class="gsw-btn">Order Now</a>
</div>

<?php
}else
// THIS IS THE SERVER WE WANT TO ORDER
{
// Determine exact selected service display and OS label from config metadata.
$svcGameKey = (string)($row['cfg_game_key'] ?? '');
$cfgFile = (string)($row['cfg_file'] ?? '');
$svcGameOs  = order_detect_service_os($cfgFile, $svcGameKey);
$canonicalGameName = (string)($row['cfg_game_name'] ?? $row['service_name']);
$variantLabel = order_variant_label($svcGameOs);
$displayName = $canonicalGameName;
if ($variantLabel !== '' && stripos($displayName, $variantLabel) === false) {
    $displayName .= ' - ' . $variantLabel;
}

?>
<div class="order-layout">
<div class="order-media decorative-bottom">
<?php
$imgSrc = billing_image_url((string)($row['img_url'] ?? ''));
if ($imgSrc === '') { $imgSrc = '/images/games/default_server.png'; }
?>
<img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?>"
     onerror="this.src='/images/games/default_server.png'; this.onerror=null;">
<center class="order-media-title"><b><?php echo htmlspecialchars($displayName, ENT_QUOTES, 'UTF-8'); ?></b></center>
<?php
$isAdmin = false;
if ($isAdmin) {
if (!isset($_POST['edit'])) {
echo "<p style='color:gray;width:230px;'>" . htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') . "</p>";
echo "<form action='' method='post'>"
   . "<input type='hidden' name='service_id' value='" . intval($row['service_id']) . "' />"
   . "<input type='submit' name='edit' value='Edit' />"
   . "</form>";
} else {
$descEditable = htmlspecialchars(str_replace("<br>", "\r\n", (string)($row['description'] ?? '')), ENT_QUOTES, 'UTF-8');
echo "<form action='' method='post'>"
   . "<textarea style='resize:none;width:230px;height:132px;' name='description'>{$descEditable}</textarea><br>"
   . "<input type='hidden' name='service_id' value='" . intval($row['service_id']) . "' />"
   . "<input type='submit' name='save' value='Save' />"
   . "</form>";
}
} else {
echo "<p class='order-media-desc'>" . htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>
</div>
<div class="order-form-card">
<?php if ($order_error_message !== ''): ?>
<p class="error"><?php echo htmlspecialchars($order_error_message, ENT_QUOTES, 'UTF-8'); ?></p>
<?php endif; ?>
<table class="order-form-table">
<form method="post" action="add_to_cart.php">
    <input type="hidden" id="order_service_id" name="service_id" value="<?php echo intval($row['service_id']); ?>">
    <input type="hidden" name="display_service_id" value="<?php echo intval($row['service_id']); ?>">
    <input type="hidden" name="remote_control_password" value="">
    <input type="hidden" name="ftp_password" value="">
    <input type="hidden" name="display_rate" id="displayRateInput" value="<?php echo number_format(floatval($row['price_monthly']), 2, '.', ''); ?>">
    <input type="hidden" name="calculated_total" id="calculatedTotalInput" value="">
<tr>
<td align="right"><b>Game Server Name</b> </td>
<td align="left">
<input type="text" name="home_name" size="40" value="<?php echo htmlspecialchars((string)($row['service_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
</td>
<tr>
  <td align="right"><b>Location</b></td>
  <td align="left">
<?php
// Fetch servers available for this exact selected service from billing_services.remote_server_id.
$available_server = false;
$remoteIdsCsv = (string)($row['remote_server_id'] ?? '');
$allAllowedIds = [];
foreach (explode(',', $remoteIdsCsv) as $part) {
$part = trim($part);
if ($part !== '' && ctype_digit($part)) {
$allAllowedIds[] = (int)$part;
}
}
$allAllowedIds = array_unique($allAllowedIds);

if (!empty($allAllowedIds)) {
$inList = implode(',', $allAllowedIds);
// Select server_os if the column exists (added by db_version 6 migration)
$osSel = $hasServerOsColumn ? ', server_os' : ", 'any' AS server_os";
$rsQuery = "SELECT remote_server_id, remote_server_name{$osSel}
            FROM {$table_prefix}remote_servers
            WHERE remote_server_id IN ({$inList})
            ORDER BY remote_server_name";
$rsResult = $db->query($rsQuery);
if ($rsResult) {
$firstServer = true;
while ($rs = $rsResult->fetch_assoc()) {
$rsID    = (int)$rs['remote_server_id'];
$rsNAME  = htmlspecialchars((string)$rs['remote_server_name'], ENT_QUOTES, 'UTF-8');
$rsOsRaw = strtolower((string)($rs['server_os'] ?? 'any'));
$rsOs = str_starts_with($rsOsRaw, 'win') ? 'windows' : (str_starts_with($rsOsRaw, 'lin') ? 'linux' : ($rsOsRaw === '' ? 'any' : $rsOsRaw));
$checked = $firstServer ? ' checked' : '';
if ($svcGameOs !== 'any' && $rsOs !== 'any' && $rsOs !== $svcGameOs) {
continue;
}
$available_server = true;
$firstServer = false;
$safeOs = htmlspecialchars($rsOs, ENT_QUOTES, 'UTF-8');
echo "<div class='location-option'>\n"
   . "  <input type='radio' name='ip_id' id='rs_{$rsID}' value='{$rsID}' data-os='{$safeOs}' required{$checked}>\n"
   . "  <label for='rs_{$rsID}'>{$rsNAME}</label>\n"
   . "</div>\n";
}
$rsResult->free();
}
}
?>
  </td>
</tr>
<tr> 
  <td align="right"><b>Configure</b></td>
  <td  align="left">
  <div class="slidecontainer">
     <center><b>Player Slots</b> </center>
<input type="range" name="max_players" min="<?php echo intval($row['slot_min_qty']); ?>" max="<?php echo intval($row['slot_max_qty']); ?>" value="<?php echo intval($row['slot_min_qty']); ?>" class="slider" id="playerRange">
 <center><b>Months</b></center>
 <input type="range" name="qty" min="1" max="24" value="1" class="slider" id="invoiceRange">

<p class="order-pricing">Player Slots: <span id="playerSlots"></span><br>
<span>Price: $<?php echo number_format(floatval($row['price_monthly']), 2); ?> USD</span><br>
<span id="invoiceDuration"></span><br>
<span id="totalPrice"></span></p>
</div>

<script>
(function() {
var slider = document.getElementById("playerRange");
var invoiceslider = document.getElementById("invoiceRange");
var output = document.getElementById("playerSlots");
var price = document.getElementById("totalPrice");
var invoiceDuration = document.getElementById("invoiceDuration");
var pricePerSlot = <?php echo number_format(floatval($row['price_monthly']), 2, '.', ''); ?>;

function recalc() {
var slots = parseInt(slider.value, 10);
var months = parseInt(invoiceslider.value, 10);
output.innerHTML = slots;
invoiceDuration.innerHTML = "Duration: " + months + " month" + (months !== 1 ? "s" : "");
var total = (slots * months * pricePerSlot).toFixed(2);
price.innerHTML = "Total Price: $" + total;
var totalInput = document.getElementById("calculatedTotalInput");
if (totalInput) {
totalInput.value = total;
}
}
recalc();
slider.oninput = recalc;
invoiceslider.oninput = recalc;
})();
</script>
  
 <input type="hidden" name="invoice_duration" value="month" />
  </td>
</tr>

<tr>
<td align="left" colspan="2">
<?php
// Only show Add to Cart when logged in
$is_logged_in = (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) || (isset($_SESSION['website_username']) && !empty($_SESSION['website_username']));
?>
<?php if ($available_server && $is_logged_in): ?>
<div class="order-actions">
<button type="submit" name="add_to_cart" class="gsw-btn">Add to Cart</button>
</div>
<?php elseif (!$is_logged_in): ?>
<div class="login-placeholder">Please <a href="login.php">login</a> to order</div>
<?php else: ?>
<p class="error">
<?php
if ($svcGameOs === 'windows') {
echo 'This service requires a Windows server location.';
} elseif ($svcGameOs === 'linux') {
echo 'This service requires a Linux server location.';
} else {
echo 'No available server locations for this service.';
}
?>
</p>
<?php endif; ?>
</form>
</td>
</tr>
<tr>
<td align="left" colspan="2">
<form action="serverlist.php" method="GET" class="order-back-form">
<div class="order-actions">
<button class="gsw-btn-secondary">Back to List</button>
</div>
</form>
</td>
</tr>
</table>
</div>
</div>
<?php
}
}
?>
</div>
</div>
<?php
// Close database connection
billing_maybe_close_db($db);
?>
</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
