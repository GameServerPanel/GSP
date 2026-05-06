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
$_billing_panel_config = _billing_find_panel_config(dirname(__DIR__, 2));

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
if (!isset($paypal_sandbox)) {
    $paypal_sandbox = true;
}
if (!isset($paypal_client_id)) {
    $paypal_client_id = '';
}
if (!isset($paypal_client_secret)) {
    $paypal_client_secret = '';
}
if (!isset($paypal_webhook_id)) {
    $paypal_webhook_id = '';
}
if (!isset($SITE_BASE_URL)) {
    $SITE_BASE_URL = '';
}
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
