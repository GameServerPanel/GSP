<?php
/**
 * Billing config loader
 *
 * Priority order (panel-first):
 *   1. <panel_root>/includes/config.inc.php      (active panel config — always wins when present)
 *   2. modules/billing/includes/config.inc.php  (local billing config — standalone fallback only)
 *
 * The panel config is preferred so that every billing page, migration, and schema
 * check automatically uses the database from the active installation.  This prevents
 * a testing install from accidentally writing to a production database when the local
 * billing config.inc.php still contains hard-coded production credentials.
 *
 * Standalone deployments (billing module deployed without the panel) should place
 * their own config.inc.php in modules/billing/includes/ as a fallback.
 */
if (defined('BILLING_CONFIG_LOADED')) {
    return;
}

$attempted = [];

// Prefer the panel config so the billing module always uses the active installation's DB.
$panelConfig = null;
$projectRoot = realpath(__DIR__ . '/../../..');
if ($projectRoot !== false) {
    $panelConfig = $projectRoot . '/includes/config.inc.php';
} else {
    $panelConfig = __DIR__ . '/../../../includes/config.inc.php';
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

// Fallback: local billing config (useful for standalone deployments where no panel is present).
$localConfig = __DIR__ . '/config.inc.php';

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
