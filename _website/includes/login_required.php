<?php
if (session_status() === PHP_SESSION_NONE) {
    session_name("gameservers_website");
    session_start();
}

if (empty($_SESSION['website_user_id'])) {
    // Build return_to pointing to current script + query and force absolute login URL
    // Use raw REQUEST_URI (already absolute) and urlencode once when passing to login
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/index.php';
    // Determine site root (prefer up to /_website)
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $pos = strpos($script, '/_website');
    $siteRoot = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');
    $loginUrl = $siteRoot . '/login.php';
    header('Location: ' . $loginUrl . '?return_to=' . urlencode($requestUri));
    exit();
}
?>
