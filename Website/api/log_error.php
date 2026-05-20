<?php
/**
 * Client-side error logging endpoint
 * Logs JavaScript errors from the cart page for debugging
 */

// Ensure all errors are logged, not displayed
ini_set('display_errors', '0');
error_reporting(E_ALL);

header('Content-Type: application/json');

// Setup logging
$logDir = __DIR__ . '/../logs';
@mkdir($logDir, 0755, true);
$logFile = $logDir . '/client_errors.log';

function log_client_error($data) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $entry = "[$timestamp] CLIENT ERROR\n";
    $entry .= "IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN') . "\n";
    $entry .= "User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN') . "\n";
    if (is_array($data) || is_object($data)) {
        $entry .= print_r($data, true);
    } else {
        $entry .= (string)$data;
    }
    $entry .= "\n" . str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);
}

// Read and parse input
$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);

if ($data) {
    log_client_error($data);
    echo json_encode(['status' => 'logged']);
} else {
    log_client_error(['raw_input' => $rawInput, 'error' => 'Invalid JSON']);
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
}
?>
