<?php
/**
 * Panel bridge helpers for the storefront.
 * Provides access to the native OGPDatabase layer, settings, and XML parsers
 * without duplicating the panel bootstrap logic in each script.
 */
require_once dirname(__DIR__) . '/billing_bootstrap.php';

if (!function_exists('billing_panel_bootstrap')) {
    /**
     * Initialize the panel runtime and return shared context.
     *
     * @return array{db:OGPDatabase|null, settings:array, table_prefix:string}|null
     */
    function billing_panel_bootstrap()
    {
        static $context = null;
        if ($context !== null) {
            return $context;
        }

        $root = realpath(__DIR__ . '/../../');
        if ($root === false) {
            error_log('billing_panel_bootstrap: unable to resolve project root');
            return null;
        }
        $overrideRoot = getenv('GSP_PANEL_PATH');
        if (($overrideRoot === false || $overrideRoot === '') && function_exists('billing_runtime_panel_root')) {
            $overrideRoot = billing_runtime_panel_root();
        }
        if (is_string($overrideRoot) && $overrideRoot !== '') {
            $resolvedOverride = realpath($overrideRoot);
            if ($resolvedOverride && is_dir($resolvedOverride . '/includes')) {
                $root = $resolvedOverride;
            }
        }

        // When storefront runs from modules/billing/_website, $root points to modules/.
        // Adjust path so panel includes resolve from the repository root, not modules/.
        if (is_dir($root . '/modules') && is_dir($root . '/includes')) {
            // already at repo root
        } elseif (is_dir(dirname($root) . '/includes')) {
            $root = dirname($root);
        }

        static $includeInjected = false;
        if (!$includeInjected) {
            set_include_path($root . PATH_SEPARATOR . get_include_path());
            // Change CWD to the panel root so that SERVER_CONFIG_LOCATION and other
            // relative paths (e.g. XML_SCHEMA) resolve correctly when billing endpoints
            // run outside of home.php (e.g. api/capture_order.php, PayPal webhooks).
            chdir($root);
            $includeInjected = true;
        }

        // Define panel constants if they are not already defined (panel runtime does this for us).
        if (!defined('INCLUDES')) {
            define('INCLUDES', 'includes/');
        }
        if (!defined('MODULES')) {
            define('MODULES', 'modules/');
        }

        // Load panel helpers that provisioning logic depends on.
        require_once $root . '/includes/functions.php';
        require_once $root . '/includes/helpers.php';
        require_once $root . '/includes/lib_remote.php';
        require_once $root . '/modules/config_games/server_config_parser.php';

        // Load panel configuration (db credentials, prefix, etc.)
        $configFile = $root . '/includes/config.inc.php';
        if (!file_exists($configFile)) {
            error_log('billing_panel_bootstrap: missing config file ' . $configFile);
            return null;
        }
        require $configFile;

        // Ensure required variables exist before attempting to connect.
        if (!isset($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix)) {
            error_log('billing_panel_bootstrap: config variables not initialized');
            return null;
        }

        $panelDb = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix, isset($db_port) ? $db_port : NULL);
        if (!($panelDb instanceof OGPDatabase)) {
            error_log('billing_panel_bootstrap: failed to connect to panel database');
            return null;
        }

        $settings = $panelDb->getSettings();

        $context = [
            'db' => $panelDb,
            'settings' => is_array($settings) ? $settings : [],
            'table_prefix' => $table_prefix,
        ];

        return $context;
    }
}

if (!function_exists('billing_get_panel_db')) {
    /**
     * Convenience wrapper to fetch the shared OGPDatabase handle.
     *
     * @return OGPDatabase|null
     */
    function billing_get_panel_db()
    {
        $ctx = billing_panel_bootstrap();
        return $ctx['db'] ?? null;
    }
}

if (!function_exists('billing_get_panel_settings')) {
    /**
     * Convenience wrapper to fetch panel settings (time zone, steam creds, etc.).
     *
     * @return array
     */
    function billing_get_panel_settings()
    {
        $ctx = billing_panel_bootstrap();
        return $ctx['settings'] ?? [];
    }
}
