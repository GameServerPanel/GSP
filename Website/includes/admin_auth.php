<?php
// Admin authorization include — include early (before output) on admin pages
require_once(__DIR__ . '/session_bridge.php');

// If not logged in, redirect to login
if (empty($_SESSION['website_user_id'])) {
    $loginUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/login.php';
    $returnTo = $_SERVER['SCRIPT_NAME'] ?? '/';
    header('Location: ' . $loginUrl . '?return_to=' . urlencode($returnTo));
    exit();
}

// Require DB config and check role live from panel DB
require_once(__DIR__ . '/config_loader.php');

// Variables from config.inc.php (helps IDEs understand scope)
/** @var string $db_host Database host */
/** @var string $db_user Database user */
/** @var string $db_pass Database password */
/** @var string $db_name Database name */
/** @var string $table_prefix Table prefix for database tables */

$auth_db_port = isset($db_port) ? (int)$db_port : null;
// Use a local connection variable so we don't clash with pages that also use $db
$auth_db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name, $auth_db_port);
if (!$auth_db) {
    // If DB unavailable, deny access gracefully
    $loginUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/login.php';
    header('Location: ' . $loginUrl);
    exit();
}

$uid = intval($_SESSION['website_user_id']);
$role = '';
$res = mysqli_query($auth_db, "SELECT users_role FROM {$table_prefix}users WHERE user_id = $uid LIMIT 1");
if ($res && mysqli_num_rows($res) === 1) {
    $row = mysqli_fetch_assoc($res);
    $role = (string)($row['users_role'] ?? '');
}
mysqli_close($auth_db);

if (strtolower($role) !== 'admin') {
    // Not an admin — redirect to login or home
    $loginUrl = rtrim(dirname($_SERVER['SCRIPT_NAME'] ?? '/'), '/\\') . '/login.php';
    header('Location: ' . $loginUrl);
    exit();
}

// If we reach here, user is an admin
?>
