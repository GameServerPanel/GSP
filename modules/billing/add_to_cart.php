<?php
// _website/add_to_cart.php
// Handle Add to Cart posts from order.php
require_once(__DIR__ . '/bootstrap.php');
require_once(__DIR__ . '/includes/login_required.php');
require_once(__DIR__ . '/includes/log.php');

// Start session if not already
if (session_status() === PHP_SESSION_NONE) session_start();

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
$remote_control_password = isset($_POST['remote_control_password']) ? $_POST['remote_control_password'] : '';
$ftp_password = isset($_POST['ftp_password']) ? $_POST['ftp_password'] : '';

// Price lookup: try to find service price_monthly
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    // Log connection error and exit
    @mkdir(__DIR__ . '/logs', 0775, true);
    $trace = __DIR__ . '/logs/add_to_cart.log';
    file_put_contents($trace, date('c') . " - mysqli_connect failed: " . mysqli_connect_error() . "\n", FILE_APPEND);
    die('DB connection failed');
} else {
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

$price = 0.0;
if ($service_id > 0) {
    $stmt = $db->prepare("SELECT price_monthly, slot_min_qty, slot_max_qty FROM {$table_prefix}billing_services WHERE service_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param('i', $service_id);
        $stmt->execute();
        $stmt->bind_result($price_monthly, $slot_min_qty, $slot_max_qty);
        if ($stmt->fetch()) {
            $price = floatval($price_monthly);
            // constrain slots
            if ($max_players < $slot_min_qty) $max_players = $slot_min_qty;
            if ($max_players > $slot_max_qty) $max_players = $slot_max_qty;
        }
        $stmt->close();
    }
}

// Insert into {table_prefix}billing_invoices (NOT orders - invoice created first)
$now = date('Y-m-d H:i:s');
$status = 'due'; // Invoice status: due (unpaid), paid

// Normal flow: process POST immediately. If debug=1 is passed, we'll still log SQL and show results in logs.
$debug = (isset($_GET['debug']) && $_GET['debug'] == '1') || (isset($_POST['debug']) && $_POST['debug'] == '1');

// Build and execute a simple INSERT using mysqli_query for debugging clarity
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
$esc_home_name = mysqli_real_escape_string($db, $home_name);
$esc_ip_id = intval($ip_id);
$esc_max_players = intval($max_players);
$esc_qty = intval($qty);
$esc_invoice_duration = mysqli_real_escape_string($db, $invoice_duration);
$esc_price = number_format((float)$price, 2, '.', '');
$esc_remote_control_password = mysqli_real_escape_string($db, $remote_control_password);
$esc_ftp_password = mysqli_real_escape_string($db, $ftp_password);
$esc_status = mysqli_real_escape_string($db, $status);
$esc_customer_name = mysqli_real_escape_string($db, $customer_name);
$esc_customer_email = mysqli_real_escape_string($db, $customer_email);
$esc_due_date = mysqli_real_escape_string($db, $due_date);
$esc_description = mysqli_real_escape_string($db, "New server: {$home_name}");

$sql = "INSERT INTO {$table_prefix}billing_invoices (
    user_id, service_id, home_name, ip, max_players, qty, invoice_duration, 
    amount, remote_control_password, ftp_password, status, customer_name, 
    customer_email, due_date, description, currency, order_id
) VALUES (
    {$esc_user_id}, {$esc_service_id}, '{$esc_home_name}', {$esc_ip_id}, 
    {$esc_max_players}, {$esc_qty}, '{$esc_invoice_duration}', {$esc_price}, 
    '{$esc_remote_control_password}', '{$esc_ftp_password}', '{$esc_status}', 
    '{$esc_customer_name}', '{$esc_customer_email}', '{$esc_due_date}', 
    '{$esc_description}', 'USD', 0
)";

site_log_info('add_to_cart_sql', ['sql'=>$sql]);
file_put_contents($logfile, date('c') . " - Creating invoice (not order): status=due\n", FILE_APPEND);
file_put_contents($logfile, date('c') . " - SQL: " . $sql . "\n", FILE_APPEND);

$res = @mysqli_query($db, $sql);
$err_no = mysqli_errno($db);
$err = mysqli_error($db);

if (!$res || $err_no > 0) {
    site_log_error('mysqli_query_failed', ['errno'=>$err_no, 'error'=>$err, 'sql'=>$sql]);
    file_put_contents($logfile, date('c') . " - ERROR: " . $err . " (errno: {$err_no})\n", FILE_APPEND);
    // Log table existence check
    $tbl_check = mysqli_query($db, "SHOW TABLES LIKE '{$table_prefix}billing_invoices'");
    $tbl_exists = ($tbl_check && mysqli_num_rows($tbl_check) > 0) ? 'yes' : 'no';
    site_log_warn('billing_invoices_exists', ['exists'=>$tbl_exists]);
    file_put_contents($logfile, date('c') . " - Table exists check: {$tbl_exists}\n", FILE_APPEND);
    
    // Show user-friendly error
    die("Error adding to cart: " . htmlspecialchars($err) . ". Please contact support.");
} else {
    $insert_id = mysqli_insert_id($db);
    $affected = mysqli_affected_rows($db);
    site_log_info('add_to_cart_insert', ['invoice_id'=>$insert_id, 'affected_rows'=>$affected]);
    file_put_contents($logfile, date('c') . " - Invoice created: invoice_id={$insert_id}\n", FILE_APPEND);
}

// Redirect to cart page
header('Location: cart.php');
exit;

?>
