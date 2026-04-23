<?php
/**
 * Billing config loader
 *
 * Attempts to load the main panel config file first (../includes/config.inc.php).
 * If that file is not readable, falls back to a module-local config.inc.php copy.
 * When neither file exists, output a plain-text error and stop execution so that
 * the admin knows to copy the config locally.
 */
if (defined('BILLING_CONFIG_LOADED')) {
    return;
}

$panelConfig = null;
$projectRoot = realpath(__DIR__ . '/../../..');
if ($projectRoot !== false) {
    $panelConfig = $projectRoot . '/includes/config.inc.php';
} else {
    // Fallback relative path without resolving symlinks
    $panelConfig = __DIR__ . '/../../..' . '/includes/config.inc.php';
}

$localConfig = __DIR__ . '/config.inc.php';
$attempted = [];

if ($panelConfig && is_readable($panelConfig)) {
    $attempted[] = $panelConfig;
    require_once $panelConfig;
    if (!defined('BILLING_CONFIG_PATH')) {
        define('BILLING_CONFIG_PATH', $panelConfig);
    }
    define('BILLING_CONFIG_LOADED', true);
    return;
}

$attempted[] = $panelConfig;
if (is_readable($localConfig)) {
    $attempted[] = $localConfig;
    require_once $localConfig;
    if (!defined('BILLING_CONFIG_PATH')) {
        define('BILLING_CONFIG_PATH', $localConfig);
    }
    define('BILLING_CONFIG_LOADED', true);
    return;
}

$attempted[] = $localConfig;

$message = "GSP Billing module cannot find config.inc.php.\n";
$message .= "Looked in:\n";
foreach ((array)$attempted as $path) {
    if (!$path) {
        continue;
    }
    $message .= " - " . $path . "\n";
}
$message .= "\nCopy your panel's includes/config.inc.php into modules/billing/includes/config.inc.php ";
$message .= "or ensure the panel config is readable so the billing pages can load database settings.\n";

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=UTF-8', true, 500);
}
echo $message;
exit(1);
