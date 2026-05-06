<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Server - GameServers.World</title>
</head>
<body>
<?php

/*
This is the "order gameserver" page. It displays the options for a single specific game server and
has the "Add to Cart" button. The gameserver selected is passed from the serverlist page by a GET
of the service_id. When the user clicks "Add to Cart", the next page is add_to_cart.php.

OS-aware selection: if both a Linux and a Windows variant of the same game exist as separate
billing_services entries, the system automatically detects the selected location's OS (from
remote_servers.server_os) and routes the cart add to the correct service variant.
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

/**
 * Derive OS ('linux'|'windows'|'any') from a game_key string.
 * Checks for _win / _windows substrings; then _linux; else 'any'.
 */
function order_game_key_os(string $gameKey): string
{
    $lk = strtolower($gameKey);
    if (str_contains($lk, '_win')) {
        return 'windows';
    }
    if (str_contains($lk, '_linux')) {
        return 'linux';
    }
    return 'any';
}

// --- Fetch the requested service with config_homes join for canonical game info ---
$req_service_id = intval($_REQUEST['service_id'] ?? 0);
if ($req_service_id !== 0) {
    $where_service_id = " WHERE bs.enabled = 1 AND bs.service_id=" . $req_service_id;
} else {
    $where_service_id = " WHERE bs.enabled = 1";
}

$qry_services = "SELECT bs.*, ch.game_name AS cfg_game_name, ch.game_key AS cfg_game_key
                 FROM {$table_prefix}billing_services bs
                 LEFT JOIN {$table_prefix}config_homes ch ON ch.home_cfg_id = bs.home_cfg_id
                 {$where_service_id}
                 ORDER BY bs.service_name";
$services_result = $db->query($qry_services);

if ($services_result === false) {
    // Fallback: query without join if config_homes doesn't exist in this context
    $where_service_id_simple = str_replace('bs.', '', $where_service_id);
    $qry_services = "SELECT *, NULL AS cfg_game_name, NULL AS cfg_game_key
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

?>
<div class="clearfix">
<?php
foreach ($serviceRows as $row)
{
if (!isset($_REQUEST['service_id']))
{
?>
<div class="float-left p-30-20">
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
if (floatval($row['price_monthly']) == 0.0) {
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
// Determine canonical game name and OS for this service
$svcGameKey = (string)($row['cfg_game_key'] ?? '');
$svcGameOs  = order_game_key_os($svcGameKey);
$canonicalGameName = (string)($row['cfg_game_name'] ?? $row['service_name']);

// Build map of OS variant service IDs for JS-based automatic selection.
// Look for sibling services that share the same cfg_game_name (canonical) but differ in OS.
// e.g. if current service is arma3_linux64, find the arma3_win64 service too.
$osServiceMap = []; // ['linux' => service_id, 'windows' => service_id]
if ($svcGameOs !== 'any' && !empty($canonicalGameName)) {
$escapedName = $db->real_escape_string($canonicalGameName);
$siblingQuery = "SELECT bs.service_id, ch.game_key AS cfg_game_key
                 FROM {$table_prefix}billing_services bs
                 LEFT JOIN {$table_prefix}config_homes ch ON ch.home_cfg_id = bs.home_cfg_id
                 WHERE bs.enabled = 1 AND ch.game_name = '{$escapedName}'";
$siblingResult = $db->query($siblingQuery);
if ($siblingResult) {
while ($sib = $siblingResult->fetch_assoc()) {
$sibOs = order_game_key_os((string)($sib['cfg_game_key'] ?? ''));
$osServiceMap[$sibOs] = (int)$sib['service_id'];
}
$siblingResult->free();
}
}
// Always include the current service as a fallback
if (!isset($osServiceMap[$svcGameOs]) || $svcGameOs === 'any') {
$osServiceMap[$svcGameOs] = (int)$row['service_id'];
}
$osServiceMapJson = json_encode($osServiceMap, JSON_THROW_ON_ERROR);

?>
<div class="float-left decorative-bottom">
<?php
$imgSrc = billing_image_url((string)($row['img_url'] ?? ''));
if ($imgSrc === '') { $imgSrc = '/images/games/default_server.png'; }
?>
<img src="<?php echo htmlspecialchars($imgSrc, ENT_QUOTES, 'UTF-8'); ?>" width="230" height="112"
     onerror="this.src='/images/games/default_server.png'; this.onerror=null;">
<center><b><?php echo htmlspecialchars($canonicalGameName, ENT_QUOTES, 'UTF-8'); ?></b></center>
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
echo "<p style='color:gray;width:280px;'>" . htmlspecialchars((string)($row['description'] ?? ''), ENT_QUOTES, 'UTF-8') . "</p>";
}
?>
</div>
<table class="float-left">
<form method="post" action="add_to_cart.php">
    <!-- service_id is updated by JS when the location OS changes -->
    <input type="hidden" id="order_service_id" name="service_id" value="<?php echo intval($row['service_id']); ?>">
    <input type="hidden" name="remote_control_password" value="">
<input type="hidden" name="ftp_password" value="">
<tr>
<td align="right"><b>Game Server Name</b> </td>
<td align="left">
<input type="text" name="home_name" size="40" value="<?php echo htmlspecialchars((string)($row['service_name'] ?? ''), ENT_QUOTES, 'UTF-8'); ?>">
</td>
<tr>
  <td align="right"><b>Location</b></td>
  <td align="left">
<?php
// Fetch servers available for this game from billing_services.remote_server_id
// (a comma-separated list of numeric remote server IDs, e.g. "1,3,7").
// When OS-aware: also collect sibling service's allowed IDs to show all compatible locations.
$available_server = false;
$remoteIdsCsv = (string)($row['remote_server_id'] ?? '');

// Also gather allowed IDs from sibling OS-variant services
$allAllowedIds = [];
foreach (explode(',', $remoteIdsCsv) as $part) {
$part = trim($part);
if ($part !== '' && ctype_digit($part)) {
$allAllowedIds[] = (int)$part;
}
}
// Add IDs from sibling service variants so locations appear regardless of which
// service variant is the "primary" one shown to the user.
if (count($osServiceMap) > 1) {
foreach ($osServiceMap as $_os => $sibSvcId) {
if ($sibSvcId === (int)$row['service_id']) continue;
$sibRow = $db->query("SELECT remote_server_id FROM {$table_prefix}billing_services WHERE service_id = " . intval($sibSvcId) . " LIMIT 1");
if ($sibRow && ($sibData = $sibRow->fetch_assoc())) {
foreach (explode(',', (string)($sibData['remote_server_id'] ?? '')) as $part) {
$part = trim($part);
if ($part !== '' && ctype_digit($part)) {
$allAllowedIds[] = (int)$part;
}
}
$sibRow->free();
}
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
$rsOs    = (string)($rs['server_os'] ?? 'any');
$checked = $firstServer ? ' checked' : '';
// Skip this location if we know the service is OS-specific and the
// node OS is incompatible AND no sibling service covers this OS.
if ($svcGameOs !== 'any' && $rsOs !== 'any' && $rsOs !== $svcGameOs && !isset($osServiceMap[$rsOs])) {
continue; // Incompatible OS variant with no fallback service
}
$available_server = true;
$firstServer = false;
$safeOs = htmlspecialchars($rsOs, ENT_QUOTES, 'UTF-8');
echo "<div>\n"
   . "  <input type='radio' name='ip_id' id='rs_{$rsID}' value='{$rsID}' data-os='{$safeOs}' required{$checked} onchange='gspUpdateServiceId(this)'>\n"
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

<p>Player Slots: <span id="playerSlots"></span><br>
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

// OS-aware service variant map: {os: service_id}
var osServiceMap = <?php echo $osServiceMapJson; ?>;

function recalc() {
var slots = parseInt(slider.value, 10);
var months = parseInt(invoiceslider.value, 10);
output.innerHTML = slots;
invoiceDuration.innerHTML = "Duration: " + months + " month" + (months !== 1 ? "s" : "");
price.innerHTML = "Total Price: $" + (slots * months * pricePerSlot).toFixed(2);
}
recalc();
slider.oninput = recalc;
invoiceslider.oninput = recalc;

// Update the hidden service_id based on the selected location's OS.
window.gspUpdateServiceId = function(radio) {
var os = radio.getAttribute('data-os') || 'any';
var svcInput = document.getElementById('order_service_id');
if (!svcInput) return;
// Pick the service for this OS, fall back to 'any', then first available
if (osServiceMap[os] !== undefined) {
svcInput.value = osServiceMap[os];
} else if (osServiceMap['any'] !== undefined) {
svcInput.value = osServiceMap['any'];
}
// else keep the current value
};

// Trigger on page load for the pre-checked radio
var checked = document.querySelector('input[name="ip_id"]:checked');
if (checked) { window.gspUpdateServiceId(checked); }
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
<button type="submit" name="add_to_cart" class="gsw-btn">Add to Cart</button>
<?php elseif (!$is_logged_in): ?>
<div class="login-placeholder">Please <a href="login.php">login</a> to order</div>
<?php else: ?>
<p class="error">No available server locations for this game.</p>
<?php endif; ?>
</form>
</td>
</tr>
<tr>
<td align="left" colspan="2">
<form action="serverlist.php" method="GET">
<button class="gsw-btn-secondary">Back to List</button>
</form>
</td>
</tr>
</table>
<?php
}
}
?>
</div>
<?php
// Close database connection
billing_maybe_close_db($db);
?>
</body>
<?php include(__DIR__ . '/includes/footer.php'); ?>
</html>
