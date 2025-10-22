<?php
// Start the website session
session_name("gameservers_website");
session_start();

// Logger function
function logger($logtext){
    file_put_contents(__DIR__ . "/logfile.txt", $logtext . PHP_EOL, FILE_APPEND);
}

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
