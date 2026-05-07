<?php
// _website/add_to_cart.php
// Handle Add to Cart posts from order.php
require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/includes/login_required.php');
require_once(__DIR__ . '/includes/log.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

// Start session if not already
if (session_status() === PHP_SESSION_NONE) {
    session_name('opengamepanel_web');
    session_start();
}

function billing_generate_password(int $bytes = 12): string
{
    try {
        return substr(bin2hex(random_bytes($bytes)), 0, $bytes * 2);
    } catch (Throwable $e) {
        return substr(hash('sha256', uniqid('gsp', true) . microtime(true)), 0, $bytes * 2);
    }
}

function billing_normalize_duration(string $duration): array
{
    $duration = strtolower(trim($duration));
    switch ($duration) {
        case 'day':
        case 'daily':
            return ['invoice_duration' => 'day', 'rate_type' => 'daily', 'days' => 1];
        case 'year':
        case 'yearly':
            return ['invoice_duration' => 'year', 'rate_type' => 'yearly', 'days' => 365];
        case 'month':
        case 'monthly':
        default:
            return ['invoice_duration' => 'month', 'rate_type' => 'monthly', 'days' => 31];
    }
}

function billing_money_to_cents(float $amount): int
{
    return (int) round($amount * 100);
}

function billing_cents_to_money(int $cents): float
{
    return $cents / 100;
}

function billing_fail_add_to_cart(string $message, array $context = []): void
{
    site_log_error('add_to_cart_failed', array_merge(['message' => $message], $context));
    header('Location: /cart.php?error=add_to_cart');
    exit;
}

// Immediate request tracing log (helps confirm the script is hit)
@mkdir(__DIR__ . '/logs', 0775, true);
$trace_file = __DIR__ . '/logs/add_to_cart_requests.log';
file_put_contents($trace_file, date('c') . " - REQUEST_METHOD=" . ($_SERVER['REQUEST_METHOD'] ?? '') . " URI=" . ($_SERVER['REQUEST_URI'] ?? '') . "\n", FILE_APPEND);

// Prefer website session id if set (login.php sets website_user_id in debug mode)
$user_id = 0;
if (isset($_SESSION['website_user_id']) && !empty($_SESSION['website_user_id'])) {
    $user_id = intval($_SESSION['website_user_id']);
} elseif (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
}
// If we don't have a numeric user_id but have a username, try to resolve it from the panel DB
if ($user_id <= 0 && isset($_SESSION['website_username']) && !empty($_SESSION['website_username'])) {
    $uname = trim((string)$_SESSION['website_username']);
    // attempt to lookup in DB (if connection available later we will set session after connecting)
    // We'll set a temporary flag to resolve after DB connection is established below
    $resolve_username_for_user_id = $uname;
} else {
    $resolve_username_for_user_id = null;
}
/*
if ($user_id <= 0) {
    // Not logged in - redirect to login with return
    $return = urlencode('/' . trim(str_replace('\\', '/', $_SERVER['REQUEST_URI']), '/'));
    header('Location: ' . (isset($SITE_BASE_URL) ? $SITE_BASE_URL : '') . '/_website/login.php?return_to=' . $return);
    exit;
}*/

// Basic validation and normalization
$service_id = isset($_POST['service_id']) ? intval($_POST['service_id']) : 0;
$home_name = isset($_POST['home_name']) ? trim($_POST['home_name']) : '';
$ip_id = isset($_POST['ip_id']) ? intval($_POST['ip_id']) : 0;
$max_players = isset($_POST['max_players']) ? intval($_POST['max_players']) : 0;
$qty = isset($_POST['qty']) ? intval($_POST['qty']) : 1;
$invoice_duration = isset($_POST['invoice_duration']) ? $_POST['invoice_duration'] : 'month';
$remote_control_password = isset($_POST['remote_control_password']) ? trim((string)$_POST['remote_control_password']) : '';
$ftp_password = isset($_POST['ftp_password']) ? trim((string)$_POST['ftp_password']) : '';

// Price lookup: try to find service price_monthly
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    // Log connection error and return user to cart with a friendly error flag
    @mkdir(__DIR__ . '/logs', 0775, true);
    $trace = __DIR__ . '/logs/add_to_cart.log';
    file_put_contents($trace, date('c') . " - mysqli_connect failed: " . mysqli_connect_error() . "\n", FILE_APPEND);
    billing_fail_add_to_cart('DB connection failed');
} else {
    mysqli_set_charset($db, 'utf8mb4');
    // Log that config was loaded (mask password)
    @mkdir(__DIR__ . '/logs', 0775, true);
    $trace = __DIR__ . '/logs/add_to_cart.log';
    $masked_pass = strlen($db_pass) ? '***' : '';
    file_put_contents($trace, date('c') . " - DB connected host={$db_host} user={$db_user} pass={$masked_pass} db={$db_name}\n", FILE_APPEND);
}

// If we deferred resolving username to user_id, do it now with the DB connection
if (!empty($resolve_username_for_user_id) && $db) {
    $safe_uname = mysqli_real_escape_string($db, $resolve_username_for_user_id);
    // users_login is the correct column name in this schema
    $q = mysqli_query($db, "SELECT user_id FROM {$table_prefix}users WHERE users_login = '$safe_uname' LIMIT 1");
    if ($q && mysqli_num_rows($q) === 1) {
        $r = mysqli_fetch_assoc($q);
        $user_id = intval($r['user_id'] ?? 0);
        // persist into session for subsequent requests
        if ($user_id > 0) {
            $_SESSION['website_user_id'] = $user_id;
            site_log_info('resolved_user_id_from_username', ['username'=>$resolve_username_for_user_id,'user_id'=>$user_id]);
            // Also resolve and persist the user's role so menus and admin checks are consistent
            $role_q = mysqli_query($db, "SELECT users_role FROM {$table_prefix}users WHERE user_id = " . intval($user_id) . " LIMIT 1");
            if ($role_q && mysqli_num_rows($role_q) === 1) {
                $role_row = mysqli_fetch_assoc($role_q);
                $_SESSION['website_user_role'] = $role_row['users_role'] ?? '';
            }
        }
    } else {
        site_log_warn('resolve_user_failed', ['username'=>$resolve_username_for_user_id]);
    }
}

$service_name = '';
$base_rate = 0.0;
$slot_min_qty = 1;
$slot_max_qty = 1;
$durationInfo = billing_normalize_duration($invoice_duration);
if ($service_id > 0) {
    $stmt = $db->prepare("SELECT service_name, price_daily, price_monthly, price_year, slot_min_qty, slot_max_qty FROM {$table_prefix}billing_services WHERE service_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $service_id);
        $stmt->execute();
        $stmt->bind_result($service_name, $price_daily, $price_monthly, $price_year, $slot_min_qty, $slot_max_qty);
        if ($stmt->fetch()) {
            if ($durationInfo['rate_type'] === 'daily') {
                $base_rate = floatval($price_daily);
            } elseif ($durationInfo['rate_type'] === 'yearly') {
                $base_rate = floatval($price_year);
            } else {
                $base_rate = floatval($price_monthly);
            }
            // constrain slots
            if ($max_players < $slot_min_qty) $max_players = $slot_min_qty;
            if ($max_players > $slot_max_qty) $max_players = $slot_max_qty;
        }
        $stmt->close();
    }
}

if ($remote_control_password === '' || strcasecmp($remote_control_password, 'ChangeMe') === 0) {
    $remote_control_password = billing_generate_password();
}
if ($ftp_password === '' || strcasecmp($ftp_password, 'ChangeMe') === 0) {
    $ftp_password = billing_generate_password();
}

// Insert into {table_prefix}billing_invoices (NOT orders - invoice created first)
$now = date('Y-m-d H:i:s');
$status = 'due'; // Invoice status: due (unpaid), paid
$payment_status = 'unpaid';
$qty = max(1, $qty);
$max_players = max(1, $max_players);
$subtotal_cents = billing_money_to_cents((float)$base_rate * $max_players * $qty);
$subtotal = billing_cents_to_money($subtotal_cents);
$amount = $subtotal;
$period_end = date('Y-m-d H:i:s', strtotime('+' . ($durationInfo['days'] * $qty) . ' days'));

// Normal flow: process POST immediately. If debug=1 is passed, we'll still log SQL and show results in logs.
$debug = (isset($_GET['debug']) && $_GET['debug'] == '1') || (isset($_POST['debug']) && $_POST['debug'] == '1');

// Build and execute the INSERT with prepared statements
@mkdir(__DIR__ . '/logs', 0775, true);
$logfile = __DIR__ . '/logs/add_to_cart.log';
site_log_info('add_to_cart_invoked', ['user_id'=>$user_id, 'service_id'=>$service_id]);

// Get customer name and email from {table_prefix}users
$customer_name = '';
$customer_email = '';
$user_q = mysqli_query($db, "SELECT users_fname, users_lname, users_email FROM {$table_prefix}users WHERE user_id = " . intval($user_id) . " LIMIT 1");
if ($user_q && mysqli_num_rows($user_q) === 1) {
    $user_row = mysqli_fetch_assoc($user_q);
    $customer_name = trim(($user_row['users_fname'] ?? '') . ' ' . ($user_row['users_lname'] ?? ''));
    $customer_email = $user_row['users_email'] ?? '';
}

// Compute due_date = now + 3 days
$due_dt = new DateTime('now');
$due_dt->modify('+3 days');
$due_date = $due_dt->format('Y-m-d H:i:s');

// Escape values
$esc_user_id = intval($user_id);
$esc_service_id = intval($service_id);
$esc_ip_id = intval($ip_id);
$esc_max_players = intval($max_players);
$esc_qty = intval($qty);
$description = trim(($service_name !== '' ? $service_name : 'Game Server') . ': ' . $home_name);
$invoiceTable = $table_prefix . 'billing_invoices';
$invoiceColumns = [];
$columnsResult = mysqli_query($db, "SHOW COLUMNS FROM `{$invoiceTable}`");
if (!$columnsResult) {
    billing_fail_add_to_cart('Could not inspect billing invoice schema', ['table' => $invoiceTable, 'error' => mysqli_error($db)]);
}
while ($col = mysqli_fetch_assoc($columnsResult)) {
    $invoiceColumns[$col['Field']] = true;
}
mysqli_free_result($columnsResult);

$invoice_duration = $durationInfo['invoice_duration'];
$rate_type = $durationInfo['rate_type'];
$rowData = [
    'order_id' => 0,
    'user_id' => $esc_user_id,
    'service_id' => $esc_service_id,
    'home_id' => 0,
    'home_name' => $home_name,
    'ip' => $esc_ip_id,
    'max_players' => $esc_max_players,
    'remote_control_password' => $remote_control_password,
    'ftp_password' => $ftp_password,
    'customer_name' => $customer_name,
    'customer_email' => $customer_email,
    'amount' => $amount,
    'discount_amount' => 0.00,
    'currency' => 'USD',
    'status' => $status,
    'billing_status' => $status,
    'invoice_date' => $now,
    'due_date' => $due_date,
    'description' => $description,
    'invoice_duration' => $invoice_duration,
    'rate_type' => $rate_type,
    'rate_per_player' => (float)$base_rate,
    'players' => $max_players,
    'period_start' => $now,
    'period_end' => $period_end,
    'subtotal' => $subtotal,
    'total_due' => $amount,
    'payment_status' => $payment_status,
    'qty' => $esc_qty,
    'coupon_id' => 0,
];

$insertColumns = [];
$placeholders = [];
$bindTypes = '';
$bindValues = [];
foreach ($rowData as $column => $value) {
    if (!isset($invoiceColumns[$column])) {
        continue;
    }
    $insertColumns[] = "`{$column}`";
    $placeholders[] = '?';
    if (is_int($value)) {
        $bindTypes .= 'i';
    } elseif (is_float($value)) {
        $bindTypes .= 'd';
    } else {
        $bindTypes .= 's';
    }
    $bindValues[] = $value;
}

if (empty($insertColumns)) {
    billing_fail_add_to_cart('No compatible invoice columns were found for insert', ['table' => $invoiceTable]);
}

$sql = "INSERT INTO `{$invoiceTable}` (" . implode(', ', $insertColumns) . ")
        VALUES (" . implode(', ', $placeholders) . ")";

$stmt = $db->prepare($sql);
$res = false;
$err_no = 0;
$err = '';
if ($stmt) {
    $stmt->bind_param($bindTypes, ...$bindValues);
    $res = @$stmt->execute();
    $err_no = mysqli_errno($db);
    $err = mysqli_error($db);
} else {
    $err_no = mysqli_errno($db);
    $err = mysqli_error($db);
}

site_log_info('add_to_cart_invoice', [
    'user_id' => $user_id,
    'service_id' => $service_id,
    'home_name' => $home_name,
    'remote_server_id' => $ip_id,
    'players' => $max_players,
    'qty' => $qty,
    'invoice_duration' => $invoice_duration,
    'subtotal' => $subtotal,
    'total_due' => $amount,
]);
file_put_contents($logfile, date('c') . " - Creating invoice (not order): status=due total_due={$amount}\n", FILE_APPEND);

if (!$res || $err_no > 0) {
    site_log_error('mysqli_query_failed', ['errno'=>$err_no, 'error'=>$err, 'sql'=>$sql]);
    file_put_contents($logfile, date('c') . " - ERROR: " . $err . " (errno: {$err_no})\n", FILE_APPEND);
    // Log table existence check
    $tbl_check = mysqli_query($db, "SHOW TABLES LIKE '{$table_prefix}billing_invoices'");
    $tbl_exists = ($tbl_check && mysqli_num_rows($tbl_check) > 0) ? 'yes' : 'no';
    site_log_warn('billing_invoices_exists', ['exists'=>$tbl_exists]);
    file_put_contents($logfile, date('c') . " - Table exists check: {$tbl_exists}\n", FILE_APPEND);
    
    billing_fail_add_to_cart('Invoice insert failed', ['errno' => $err_no, 'error' => $err]);
} else {
    $insert_id = mysqli_insert_id($db);
    $affected = mysqli_affected_rows($db);
    site_log_info('add_to_cart_insert', ['invoice_id'=>$insert_id, 'affected_rows'=>$affected]);
    file_put_contents($logfile, date('c') . " - Invoice created: invoice_id={$insert_id}\n", FILE_APPEND);
}

if ($stmt instanceof mysqli_stmt) {
    $stmt->close();
}

// Redirect to cart page
header('Location: cart.php');
exit;

?>
