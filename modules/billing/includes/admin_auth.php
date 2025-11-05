<?php
// Admin authorization include — include early (before output) on admin pages
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}

// If not logged in, redirect to login
if (empty($_SESSION['website_user_id'])) {
    // Build absolute login URL to avoid browser-relative resolution issues
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $siteRoot = '/';
    $pos = strpos($script, '/_website');
    if ($pos !== false) {
        $siteRoot = substr($script, 0, $pos + strlen('/_website'));
    } else {
        $siteRoot = rtrim(dirname($script), '/\\');
    }
    $loginUrl = $siteRoot . '/login.php';
    $returnTo = $siteRoot . '/' . basename($_SERVER['PHP_SELF']);
    header('Location: ' . $loginUrl . '?return_to=' . urlencode($returnTo));
    exit();
}

// Require DB config and check role live from panel DB

require_once(__DIR__ . '/config.inc.php');
// Use a local connection variable so we don't clash with pages that also use $db
$auth_db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$auth_db) {
    // If DB unavailable, deny access gracefully
    // Redirect to absolute login URL
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/_website');
    $siteRoot = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');
    header('Location: ' . $siteRoot . '/login.php');
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
    // Redirect to absolute login URL
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/_website');
    $siteRoot = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');
    header('Location: ' . $siteRoot . '/login.php');
    exit();
}

// If we reach here, user is an admin
?>
