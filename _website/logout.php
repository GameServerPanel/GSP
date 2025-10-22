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
// Optional safe return_to handling
$return_raw = $_GET['return_to'] ?? '';
// Determine site root (prefer up to /_website)
$script = $_SERVER['SCRIPT_NAME'] ?? '';
$pos = strpos($script, '/_website');
$siteRoot = $pos !== false ? substr($script, 0, $pos + strlen('/_website')) : rtrim(dirname($script), '/\\');

// sanitize: disallow absolute URLs (with protocol), CR/LF; allow safe path characters.
$sanitize_return = function($p) use ($siteRoot) {
    $p = trim((string)$p);
    if ($p === '') return '';
    // disallow absolute URLs or protocol-relative paths
    if (preg_match('#^(https?:)?//#i', $p)) return '';
    if (strpos($p, "\n") !== false || strpos($p, "\r") !== false) return '';
    // allow only safe characters (slash, query, percent-encodings, alnum and a few safe symbols)
    if (!preg_match('#^[A-Za-z0-9_./?&=%:\-]+$#', $p)) return '';
    // If it already starts with '/', treat it as an absolute path and return as-is
    if (strpos($p, '/') === 0) {
        return $p;
    }
    // Otherwise, build an absolute path under the site root
    return $siteRoot . '/' . ltrim($p, '/');
};

$sanitized = $sanitize_return($return_raw);
if ($sanitized !== '') {
    header('Location: ' . $sanitized);
} else {
    header('Location: ' . $siteRoot . '/index.php');
}
exit();
?>
