<?php
/**
 * Billing config loader
 *
 * Load order:
 *   1. modules/billing/includes/config.inc.php  — always loaded first; contains billing-specific
 *      settings (PayPal credentials, SITE_BASE_URL, SITE_BACKGROUND, SITE_DATA_DIR, backup
 *      retention, and default DB settings for standalone installs).
 *   2. <panel_root>/includes/config.inc.php     — read via regex (no side-effects) when the
 *      billing module is installed inside a GSP panel tree.  DB variables extracted from the
 *      panel config override the billing config DB variables in memory so that the module
 *      always connects to the active panel database.
 *
 * Panel-child detection:
 *   Walk up from modules/billing/includes/ looking for the pattern
 *   <ancestor>/includes/config.inc.php that contains the GSP panel DB variables
 *   ($db_host, $db_user, $db_name, $table_prefix).  Stop after six levels.
 *
 * Config sync:
 *   If the panel config DB variables differ from the billing config file on disk, the loader
 *   updates only the DB variable lines in billing/includes/config.inc.php so that subsequent
 *   page loads (and standalone tools) always see current credentials.  The sync only runs when
 *   the file is writable.  If it cannot write, a non-fatal admin-visible warning is set in
 *   $billing_config_warning.
 *
 * Standalone installs:
 *   When no panel config is found the billing config is used as-is; standalone mode is fully
 *   supported.
 */
if (defined('BILLING_CONFIG_LOADED')) {
    return;
}

$billing_runtime_site_config = [];
$billing_site_cfg = dirname(__DIR__) . '/site_config.php';
$billing_site_cfg_local = dirname(__DIR__) . '/site_config.local.php';
if (is_readable($billing_site_cfg)) {
    $tmp = require $billing_site_cfg;
    if (is_array($tmp)) {
        $billing_runtime_site_config = $tmp;
    }
}
if (is_readable($billing_site_cfg_local)) {
    $tmp = require $billing_site_cfg_local;
    if (is_array($tmp)) {
        $billing_runtime_site_config = array_merge($billing_runtime_site_config, $tmp);
    }
}

// ---------------------------------------------------------------------------
// Helper: extract DB variable values from a PHP config file without including it.
// Uses regex on the raw file text so there are no side-effects.
// ---------------------------------------------------------------------------
if (!function_exists('_billing_extract_db_vars_from_file')) {
    function _billing_extract_db_vars_from_file(string $path): array
    {
        $content = @file_get_contents($path);
        if ($content === false) {
            return [];
        }
        $result = [];
        foreach (['db_host', 'db_port', 'db_user', 'db_pass', 'db_name', 'table_prefix', 'db_type'] as $var) {
            // Match: $varname = "value"; or $varname="value"; (single or double quotes, no escaped quotes)
            // Note: credentials containing escaped quotes or special chars are not supported by this
            // regex-based extractor — use var_export() to write values and keep creds simple.
            if (preg_match('/^\s*\$' . preg_quote($var, '/') . '\s*=\s*"([^"]*)"/m', $content, $m) ||
                preg_match('/^\s*\$' . preg_quote($var, '/') . "\s*=\s*'([^']*)'/m", $content, $m)) {
                $result[$var] = $m[1];
            }
        }
        return $result;
    }
}

// ---------------------------------------------------------------------------
// Helper: update DB variable lines in the billing config file without touching
// any other settings.  Returns true when the file was updated, false otherwise.
// ---------------------------------------------------------------------------
if (!function_exists('_billing_sync_db_vars_to_file')) {
    function _billing_sync_db_vars_to_file(string $filePath, array $panelVars): bool
    {
        if (!is_writable($filePath)) {
            return false;
        }
        $content = file_get_contents($filePath);
        if ($content === false) {
            return false;
        }
        $changed = false;
        foreach (['db_host', 'db_port', 'db_user', 'db_pass', 'db_name', 'table_prefix', 'db_type'] as $var) {
            if (!array_key_exists($var, $panelVars)) {
                continue;
            }
            $newVal = $panelVars[$var];
            // Match any existing assignment for this var (double or single quotes, no escaped quotes)
            // Use var_export() to produce the replacement value so special characters are handled
            // correctly (var_export produces a valid PHP string literal).
            $pattern = '/^(\s*\$' . preg_quote($var, '/') . '\s*=\s*)["\'][^"\']*["\'](.*)$/m';
            $exportedVal = var_export($newVal, true); // produces 'value' with proper escaping
            $newLine  = '${1}' . str_replace('\\', '\\\\', $exportedVal) . '${2}';
            $updated  = preg_replace($pattern, $newLine, $content, 1, $count);
            if ($count > 0 && $updated !== $content) {
                $content = (string)$updated;
                $changed  = true;
            }
        }
        if (!$changed) {
            return false;
        }
        return file_put_contents($filePath, $content, LOCK_EX) !== false;
    }
}

// ---------------------------------------------------------------------------
// Helper: locate the panel config by walking up ancestor directories.
// Returns an absolute path when found, or null.
// ---------------------------------------------------------------------------
if (!function_exists('_billing_find_panel_config')) {
    function _billing_find_panel_config(string $startDir): ?string
    {
        $dir = realpath($startDir);
        if ($dir === false) {
            return null;
        }
        // Walk up at most 6 levels (covers: includes/ → billing/ → modules/ → panel_root/)
        for ($i = 0; $i < 6; $i++) {
            $candidate = $dir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.inc.php';
            if (is_readable($candidate)) {
                // Confirm it is a GSP/panel config by looking for $db_host and $table_prefix
                $content = @file_get_contents($candidate);
                if ($content !== false &&
                    strpos($content, '$db_host') !== false &&
                    strpos($content, '$table_prefix') !== false) {
                    return $candidate;
                }
            }
            $parent = dirname($dir);
            if ($parent === $dir) {
                break; // reached filesystem root
            }
            $dir = $parent;
        }
        return null;
    }
}

// ---------------------------------------------------------------------------
// Step 1: Load the billing config (always — it holds billing-specific settings).
// ---------------------------------------------------------------------------
$billing_config_warning = null; // surfaced to admin pages when non-null

$localConfig = __DIR__ . '/config.inc.php';

if (!is_readable($localConfig)) {
    // No billing config found — render an informative error for the admin.
    $message  = "GSP Billing module cannot find modules/billing/includes/config.inc.php.\n";
    $message .= "Expected: " . $localConfig . "\n";
    $message .= "\nCreate the file from the example (config.example.php) and fill in your settings.\n";
    if (!headers_sent()) {
        header('Content-Type: text/plain; charset=UTF-8', true, 500);
    }
    echo $message;
    exit(1);
}

require_once $localConfig;

if (!defined('BILLING_CONFIG_PATH')) {
    define('BILLING_CONFIG_PATH', $localConfig);
}

// ---------------------------------------------------------------------------
// Step 2: Child-of-panel detection.
// ---------------------------------------------------------------------------
$_billing_panel_config = null;
$explicit_panel = getenv('GSP_PANEL_PATH');
if ($explicit_panel === false || $explicit_panel === '') {
    $explicit_panel = getenv('BILLING_PANEL_PATH');
}
if (($explicit_panel === false || $explicit_panel === '') && !empty($billing_runtime_site_config['panel_path'])) {
    $explicit_panel = (string)$billing_runtime_site_config['panel_path'];
}
if ($explicit_panel !== false && $explicit_panel !== '') {
    $explicit_panel_dir = realpath((string)$explicit_panel);
    if ($explicit_panel_dir && is_readable($explicit_panel_dir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.inc.php')) {
        $_billing_panel_config = $explicit_panel_dir . DIRECTORY_SEPARATOR . 'includes' . DIRECTORY_SEPARATOR . 'config.inc.php';
    }
}
if ($_billing_panel_config === null) {
    $_billing_panel_config = _billing_find_panel_config(dirname(__DIR__, 2));
}

if ($_billing_panel_config !== null) {
    // Found a panel config — extract its DB variables (no side-effects).
    $panelDbVars = _billing_extract_db_vars_from_file($_billing_panel_config);

    if (!empty($panelDbVars)) {
        // Override DB settings in the current scope with panel values.
        foreach ($panelDbVars as $_bk => $_bv) {
            $$_bk = $_bv;
        }
        unset($_bk, $_bv);

        // Record which panel config was found (admin pages may display this).
        if (!defined('BILLING_PANEL_CONFIG_PATH')) {
            define('BILLING_PANEL_CONFIG_PATH', $_billing_panel_config);
        }

        // -------------------------------------------------------------------
        // Step 3: Config sync — keep billing config.inc.php DB vars in sync.
        // Only rewrite the file when the on-disk values actually differ so that
        // we never touch the file on normal page loads where nothing changed.
        // -------------------------------------------------------------------
        $diskVars = _billing_extract_db_vars_from_file($localConfig);
        $needsSync = false;
        foreach ($panelDbVars as $k => $v) {
            if (!array_key_exists($k, $diskVars) || $diskVars[$k] !== $v) {
                $needsSync = true;
                break;
            }
        }

        if ($needsSync) {
            if (!_billing_sync_db_vars_to_file($localConfig, $panelDbVars)) {
                // Non-fatal: show admin warning; runtime DB vars are already overridden above.
                $billing_config_warning =
                    'Panel DB settings differ from billing/includes/config.inc.php but the file '
                    . 'is not writable. The billing module will use the panel DB settings for this '
                    . 'request, but consider updating file permissions or manually editing config.inc.php '
                    . 'to match the panel config at: ' . $_billing_panel_config;
            }
        }
    }
}

unset($_billing_panel_config, $panelDbVars, $diskVars, $needsSync, $k, $v);

// ---------------------------------------------------------------------------
// Step 4: Apply safe defaults for billing-specific variables that may be absent
// in older config files (never overwrite values already set by the config).
// ---------------------------------------------------------------------------

// --- PayPal mode (new-style) ---
// Backward compat: if $paypal_sandbox was set (old config) and $paypal_mode is absent,
// derive $paypal_mode from $paypal_sandbox.
if (!isset($paypal_mode)) {
    if (isset($paypal_sandbox)) {
        $paypal_mode = $paypal_sandbox ? 'sandbox' : 'live';
    } else {
        $paypal_mode = 'sandbox';
    }
}
$paypal_mode = (strtolower((string)$paypal_mode) === 'live') ? 'live' : 'sandbox';

// --- Sandbox credentials ---
if (!isset($paypal_sandbox_client_id)) {
    // Backward compat: if old $paypal_client_id was set while in sandbox mode, use it
    $paypal_sandbox_client_id = (isset($paypal_client_id) && $paypal_mode === 'sandbox') ? $paypal_client_id : '';
}
if (!isset($paypal_sandbox_client_secret)) {
    $paypal_sandbox_client_secret = (isset($paypal_client_secret) && $paypal_mode === 'sandbox') ? $paypal_client_secret : '';
}
if (!isset($paypal_sandbox_webhook_id)) {
    $paypal_sandbox_webhook_id = (isset($paypal_webhook_id) && $paypal_mode === 'sandbox') ? $paypal_webhook_id : '';
}

// --- Live credentials ---
if (!isset($paypal_live_client_id)) {
    $paypal_live_client_id = (isset($paypal_client_id) && $paypal_mode === 'live') ? $paypal_client_id : '';
}
if (!isset($paypal_live_client_secret)) {
    $paypal_live_client_secret = (isset($paypal_client_secret) && $paypal_mode === 'live') ? $paypal_client_secret : '';
}
if (!isset($paypal_live_webhook_id)) {
    $paypal_live_webhook_id = (isset($paypal_webhook_id) && $paypal_mode === 'live') ? $paypal_webhook_id : '';
}

// --- Legacy compatibility shims (read-only derived values) ---
// Keep old variable names populated so any code that still reads $paypal_sandbox,
// $paypal_client_id, $paypal_client_secret, $paypal_webhook_id keeps working.
if (!isset($paypal_sandbox)) {
    $paypal_sandbox = ($paypal_mode !== 'live');
}
if (!isset($paypal_client_id)) {
    $paypal_client_id = ($paypal_mode === 'live') ? $paypal_live_client_id : $paypal_sandbox_client_id;
}
if (!isset($paypal_client_secret)) {
    $paypal_client_secret = ($paypal_mode === 'live') ? $paypal_live_client_secret : $paypal_sandbox_client_secret;
}
if (!isset($paypal_webhook_id)) {
    $paypal_webhook_id = ($paypal_mode === 'live') ? $paypal_live_webhook_id : $paypal_sandbox_webhook_id;
}

// --- Webhook path ---
if (!isset($paypal_webhook_path) || (string)$paypal_webhook_path === '') {
    $paypal_webhook_path = '/paypal/webhook.php';
}
// Ensure webhook path starts with /
$paypal_webhook_path = '/' . ltrim((string)$paypal_webhook_path, '/');

// --- Site settings ---
if (!isset($SITE_BASE_URL)) {
    $SITE_BASE_URL = '';
}
$SITE_BASE_URL = rtrim(trim((string)$SITE_BASE_URL), '/');

if (!isset($SITE_BACKGROUND)) {
    $SITE_BACKGROUND = 'images/dark.jpg';
}
if (!isset($SITE_DATA_DIR)) {
    $SITE_DATA_DIR = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';
}
if (!isset($SITE_CONFIG_BACKUP_RETENTION) || !is_int($SITE_CONFIG_BACKUP_RETENTION)) {
    $SITE_CONFIG_BACKUP_RETENTION = 5;
}
$SITE_CONFIG_BACKUP_RETENTION = max(1, min(10, (int)$SITE_CONFIG_BACKUP_RETENTION));

define('BILLING_CONFIG_LOADED', true);

// ---------------------------------------------------------------------------
// PayPal helper functions — use these everywhere instead of reading globals.
// ---------------------------------------------------------------------------

if (!function_exists('gsp_paypal_get_mode')) {
    function gsp_paypal_get_mode(): string
    {
        return $GLOBALS['paypal_mode'] === 'live' ? 'live' : 'sandbox';
    }
}

if (!function_exists('gsp_paypal_is_sandbox')) {
    function gsp_paypal_is_sandbox(): bool
    {
        return gsp_paypal_get_mode() !== 'live';
    }
}

if (!function_exists('gsp_paypal_get_client_id')) {
    function gsp_paypal_get_client_id(): string
    {
        if (gsp_paypal_is_sandbox()) {
            return (string)($GLOBALS['paypal_sandbox_client_id'] ?? '');
        }
        return (string)($GLOBALS['paypal_live_client_id'] ?? '');
    }
}

if (!function_exists('gsp_paypal_get_client_secret')) {
    function gsp_paypal_get_client_secret(): string
    {
        if (gsp_paypal_is_sandbox()) {
            return (string)($GLOBALS['paypal_sandbox_client_secret'] ?? '');
        }
        return (string)($GLOBALS['paypal_live_client_secret'] ?? '');
    }
}

if (!function_exists('gsp_paypal_get_webhook_id')) {
    function gsp_paypal_get_webhook_id(): string
    {
        if (gsp_paypal_is_sandbox()) {
            return (string)($GLOBALS['paypal_sandbox_webhook_id'] ?? '');
        }
        return (string)($GLOBALS['paypal_live_webhook_id'] ?? '');
    }
}

if (!function_exists('gsp_paypal_get_api_base')) {
    function gsp_paypal_get_api_base(): string
    {
        return gsp_paypal_is_sandbox()
            ? 'https://api-m.sandbox.paypal.com'
            : 'https://api-m.paypal.com';
    }
}

if (!function_exists('gsp_paypal_get_webhook_path')) {
    function gsp_paypal_get_webhook_path(): string
    {
        $path = (string)($GLOBALS['paypal_webhook_path'] ?? '/paypal/webhook.php');
        return '/' . ltrim($path, '/');
    }
}

if (!function_exists('gsp_paypal_get_full_webhook_url')) {
    function gsp_paypal_get_full_webhook_url(): string
    {
        $base = rtrim((string)($GLOBALS['SITE_BASE_URL'] ?? ''), '/');
        $path = gsp_paypal_get_webhook_path();
        return $base . $path;
    }
}
