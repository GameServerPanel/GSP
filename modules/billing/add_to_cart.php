<?php
// _website/add_to_cart.php
// Handle Add to Cart posts from order.php
require_once(__DIR__ . '/includes/config.inc.php');
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
    $q = mysqli_query($db, "SELECT user_id FROM ogp_users WHERE users_login = '$safe_uname' LIMIT 1");
    if ($q && mysqli_num_rows($q) === 1) {
        $r = mysqli_fetch_assoc($q);
        $user_id = intval($r['user_id'] ?? 0);
        // persist into session for subsequent requests
        if ($user_id > 0) {
            $_SESSION['website_user_id'] = $user_id;
            site_log_info('resolved_user_id_from_username', ['username'=>$resolve_username_for_user_id,'user_id'=>$user_id]);
            // Also resolve and persist the user's role so menus and admin checks are consistent
            $role_q = mysqli_query($db, "SELECT users_role FROM ogp_users WHERE user_id = " . intval($user_id) . " LIMIT 1");
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
    $stmt = $db->prepare('SELECT price_monthly, slot_min_qty, slot_max_qty FROM ogp_billing_services WHERE service_id = ? LIMIT 1');
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

// Insert into ogp_billing_orders
$now = date('Y-m-d H:i:s');
$status = 'in-cart';

// Normal flow: process POST immediately. If debug=1 is passed, we'll still log SQL and show results in logs.
$debug = (isset($_GET['debug']) && $_GET['debug'] == '1') || (isset($_POST['debug']) && $_POST['debug'] == '1');

// Build and execute a simple INSERT using mysqli_query for debugging clarity
@mkdir(__DIR__ . '/logs', 0775, true);
$logfile = __DIR__ . '/logs/add_to_cart.log';
site_log_info('add_to_cart_invoked', ['user_id'=>$user_id, 'service_id'=>$service_id]);

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

$sql = "INSERT INTO ogp_billing_orders (user_id, service_id, home_name, ip, max_players, qty, invoice_duration, price, remote_control_password, ftp_password, status) VALUES ({$esc_user_id}, {$esc_service_id}, '{$esc_home_name}', {$esc_ip_id}, {$esc_max_players}, {$esc_qty}, '{$esc_invoice_duration}', {$esc_price}, '{$esc_remote_control_password}', '{$esc_ftp_password}', '{$esc_status}')";

// Compute finish_date = now + 3 days
$finish_dt = new DateTime('now');
$finish_dt->modify('+3 days');
$finish_date = $finish_dt->format('Y-m-d H:i:s');

// Check if the ogp_billing_orders table has a finish_date column; if so include it in the INSERT
$has_finish = false;
$col_check_q = mysqli_query($db, "SHOW COLUMNS FROM ogp_billing_orders LIKE 'finish_date'");
if ($col_check_q && mysqli_num_rows($col_check_q) > 0) {
    $has_finish = true;
}

if ($has_finish) {
    $esc_finish_date = mysqli_real_escape_string($db, $finish_date);
    $sql = "INSERT INTO ogp_billing_orders (user_id, service_id, home_name, ip, max_players, qty, invoice_duration, price, remote_control_password, ftp_password, status, finish_date) VALUES ({$esc_user_id}, {$esc_service_id}, '{$esc_home_name}', {$esc_ip_id}, {$esc_max_players}, {$esc_qty}, '{$esc_invoice_duration}', {$esc_price}, '{$esc_remote_control_password}', '{$esc_ftp_password}', '{$esc_status}', '{$esc_finish_date}')";
    file_put_contents($logfile, date('c') . " - finish_date included: {$esc_finish_date}\n", FILE_APPEND);
} else {
    file_put_contents($logfile, date('c') . " - finish_date column not present, skipping finish_date. computed_finish_date={$finish_date}\n", FILE_APPEND);
}

site_log_info('add_to_cart_sql', ['sql'=>$sql]);

$res = mysqli_query($db, $sql);
if (!$res) {
    $err_no = mysqli_errno($db);
    $err = mysqli_error($db);
    site_log_error('mysqli_query_failed', ['errno'=>$err_no, 'error'=>$err, 'sql'=>$sql]);
    // Log table existence check
    $tbl_check = mysqli_query($db, "SHOW TABLES LIKE 'ogp_billing_orders'");
    $tbl_exists = ($tbl_check && mysqli_num_rows($tbl_check) > 0) ? 'yes' : 'no';
    site_log_warn('ogp_billing_orders_exists', ['exists'=>$tbl_exists]);
} else {
    $insert_id = mysqli_insert_id($db);
    $affected = mysqli_affected_rows($db);
    site_log_info('add_to_cart_insert', ['insert_id'=>$insert_id, 'affected_rows'=>$affected]);
}

// Redirect to cart page
header('Location: cart.php');
exit;

?>
