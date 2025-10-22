<?php
// Start the website session
session_name("gameservers_website");
session_start();

// Include database connection for logging
require_once('db.php');

// Log the logout
if (isset($_SESSION['website_username'])) {
    logger("Website logout: " . $_SESSION['website_username']);
}

// Destroy all session data
$_SESSION = array();

// Destroy the session cookie
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 42000, '/');
}

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: /');
exit();
?>
