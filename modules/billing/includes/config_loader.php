<?php
/**
 * Billing config loader
 *
 * Priority order (standalone-first):
 *   1. modules/billing/includes/config.inc.php  (local billing config — always wins when present)
 *   2. <panel_root>/includes/config.inc.php      (panel config — fallback when no local config)
 *
 * This ensures that copying modules/billing/ to any web root works correctly
 * after editing its own config.inc.php, without being overridden by a parent
 * panel installation that may have a different database name.
 */
if (defined('BILLING_CONFIG_LOADED')) {
    return;
}

$localConfig = __DIR__ . '/config.inc.php';
$attempted = [];

// Always prefer the local billing config so the module is self-contained.
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

// Fallback: try to load the panel's config (useful when running embedded inside the panel
// and no local copy has been made yet).
$panelConfig = null;
$projectRoot = realpath(__DIR__ . '/../../..');
if ($projectRoot !== false) {
    $panelConfig = $projectRoot . '/includes/config.inc.php';
} else {
    $panelConfig = __DIR__ . '/../../..' . '/includes/config.inc.php';
}

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
