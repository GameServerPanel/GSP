<?php
// Remote diagnostic helper for GameServers.World (_website)
// Upload this file to the remote server and open it in the browser to collect environment info.
header('Content-Type: text/plain; charset=utf-8');
echo "GSP _website remote diagnostic\n";
echo "Date: " . date('c') . "\n\n";

// PHP info summary
echo "PHP Version: " . PHP_VERSION . "\n";
echo "Loaded extensions: " . implode(', ', get_loaded_extensions()) . "\n\n";

// Session settings
echo "Session save path: " . (ini_get('session.save_path') ?: '(not set)') . "\n";
echo "Session cookie params: " . json_encode(session_get_cookie_params()) . "\n";
echo "Session status (before start): " . session_status() . "\n";

// Try to start a named session used by _website
session_name('opengamepanel_web');
@session_start();
echo "Session status (after start): " . session_status() . "\n";
echo "Session id: " . session_id() . "\n";
echo "Session variables: \n" . print_r($_SESSION, true) . "\n";

// Check config file readability (panel root first, module local second)
$panelCfgRoot = realpath(__DIR__ . '/../../..');
$panelCfg = $panelCfgRoot ? $panelCfgRoot . '/includes/config.inc.php' : __DIR__ . '/../../..' . '/includes/config.inc.php';
$localCfg = __DIR__ . '/includes/config.inc.php';
echo "Panel config: " . $panelCfg . " exists=" . (file_exists($panelCfg) ? 'yes' : 'no') . " readable=" . (is_readable($panelCfg) ? 'yes' : 'no') . "\n";
echo "Local config: " . $localCfg . " exists=" . (file_exists($localCfg) ? 'yes' : 'no') . " readable=" . (is_readable($localCfg) ? 'yes' : 'no') . "\n";

require_once(__DIR__ . '/includes/config_loader.php');
echo "Active config source: " . (defined('BILLING_CONFIG_PATH') ? BILLING_CONFIG_PATH : '(unknown)') . "\n";
if (defined('BILLING_CONFIG_PATH') && is_readable(BILLING_CONFIG_PATH)) {
    echo "Active config preview (first 200 chars):\n" . substr(file_get_contents(BILLING_CONFIG_PATH), 0, 200) . "\n";
}

echo "Trying DB connection...\n";
$ok = false;
if (isset($db_host)) {
    $db = @mysqli_connect($db_host, $db_user, $db_pass, $db_name);
    if ($db) {
        echo "DB connect: OK (host=$db_host db=$db_name)\n";
        $ok = true;
        // run a small query
        $q = @mysqli_query($db, "SELECT COUNT(*) AS cnt FROM information_schema.tables WHERE table_schema = '".mysqli_real_escape_string($db,$db_name)."'");
        if ($q) {
            $r = mysqli_fetch_assoc($q);
            echo "Tables in DB: " . ($r['cnt'] ?? 'unknown') . "\n";
        }
        mysqli_close($db);
    } else {
        echo "DB connect: FAILED (mysqli_connect_error: " . mysqli_connect_error() . ")\n";
    }
} else {
    echo "DB config not available to attempt connection.\n";
}

// Check data and logs directories
$data = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';
$logs = __DIR__ . DIRECTORY_SEPARATOR . 'logs';
echo "Site data dir: $data exists=" . (is_dir($data)?'yes':'no') . " writable=" . (is_writable($data)?'yes':'no') . "\n";
echo "Site logs dir: $logs exists=" . (is_dir($logs)?'yes':'no') . " writable=" . (is_writable($logs)?'yes':'no') . "\n";

// Try creating test files
if (is_dir($logs) && is_writable($logs)) {
    $fn = $logs . DIRECTORY_SEPARATOR . date('Y-m-d') . '.diag.txt';
    $w = @file_put_contents($fn, "diag " . date('c') . "\n", FILE_APPEND);
    echo "Wrote diag file to $fn result=" . ($w ? 'ok' : 'fail') . "\n";
}

echo "\nSuggested next checks:\n";
echo " - Confirm PHP can write session files to session.save_path and that cookies are sent to browser (use browser devtools).\n";
echo " - Ensure the site path is served under the expected /_website/ path and that session cookie domain/path match the served path.\n";
echo " - If sessions aren't persistent across requests, check webserver user permissions and session.save_path.\n";

?>

