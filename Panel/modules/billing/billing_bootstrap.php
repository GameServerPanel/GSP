<?php
/**
 * Billing runtime bootstrap helpers.
 * Supports embedded panel mode and standalone copied deployments.
 */

if (defined('BILLING_RUNTIME_BOOTSTRAPPED')) {
    return;
}

define('BILLING_RUNTIME_BOOTSTRAPPED', true);
define('BILLING_ROOT_DIR', __DIR__);

if (!function_exists('billing_runtime_load_site_config')) {
    function billing_runtime_load_site_config(): array
    {
        $defaults = [];
        $defaultFile = BILLING_ROOT_DIR . '/site_config.php';
        $localFile = BILLING_ROOT_DIR . '/site_config.local.php';

        if (is_readable($defaultFile)) {
            $data = require $defaultFile;
            if (is_array($data)) {
                $defaults = $data;
            }
        }
        if (is_readable($localFile)) {
            $local = require $localFile;
            if (is_array($local)) {
                $defaults = array_merge($defaults, $local);
            }
        }
        return $defaults;
    }
}

$billing_runtime_site_config = billing_runtime_load_site_config();

if (!function_exists('billing_runtime_panel_root')) {
    function billing_runtime_panel_root(): ?string
    {
        static $panelRoot = null;
        if ($panelRoot !== null) {
            return $panelRoot ?: null;
        }

        global $billing_runtime_site_config;
        $candidate = getenv('GSP_PANEL_PATH') ?: getenv('BILLING_PANEL_PATH');
        if (!$candidate && !empty($billing_runtime_site_config['panel_path'])) {
            $candidate = (string)$billing_runtime_site_config['panel_path'];
        }
        if (!$candidate) {
            $candidate = dirname(BILLING_ROOT_DIR, 2);
        }
        $resolved = realpath($candidate);
        if ($resolved && is_dir($resolved . '/includes') && is_dir($resolved . '/modules')) {
            $panelRoot = $resolved;
            return $panelRoot;
        }
        $panelRoot = '';
        return null;
    }
}

if (!function_exists('billing_runtime_mode')) {
    function billing_runtime_mode(): string
    {
        return billing_runtime_panel_root() ? 'embedded' : 'standalone';
    }
}

if (!function_exists('billing_base_path')) {
    function billing_base_path(): string
    {
        static $basePath = null;
        if ($basePath !== null) {
            return $basePath;
        }

        global $billing_runtime_site_config;
        $forced = getenv('BILLING_BASE_PATH');
        if (!$forced && !empty($billing_runtime_site_config['base_path'])) {
            $forced = (string)$billing_runtime_site_config['base_path'];
        }
        if (is_string($forced) && $forced !== '') {
            $forced = '/' . trim($forced, '/');
            $basePath = ($forced === '/') ? '' : $forced;
            return $basePath;
        }

        $scriptName = (string)($_SERVER['SCRIPT_NAME'] ?? '');
        if ($scriptName !== '') {
            $norm = str_replace('\\', '/', $scriptName);
            $marker = '/modules/billing/';
            $pos = strpos($norm, $marker);
            if ($pos !== false) {
                $basePath = '/modules/billing';
                return $basePath;
            }
            $dir = trim(str_replace('\\', '/', dirname($norm)), '/');
            $basePath = $dir === '' ? '' : '/' . $dir;
            return $basePath;
        }

        $basePath = '';
        return $basePath;
    }
}

if (!function_exists('billing_url')) {
    function billing_url(string $path = ''): string
    {
        $base = rtrim(billing_base_path(), '/');
        $path = ltrim($path, '/');
        if ($path === '') {
            return $base === '' ? '/' : $base . '/';
        }
        return ($base === '' ? '' : $base) . '/' . $path;
    }
}

if (!function_exists('billing_abs_url')) {
    function billing_abs_url(string $path = ''): string
    {
        $siteBase = rtrim((string)($GLOBALS['SITE_BASE_URL'] ?? ''), '/');
        $rel = billing_url($path);
        if ($siteBase === '') {
            return $rel;
        }
        return $siteBase . $rel;
    }
}

if (!function_exists('billing_sync_timestamp_from_legacy')) {
    function billing_sync_timestamp_from_legacy(): void
    {
        global $billing_runtime_site_config;

        $billingTs = BILLING_ROOT_DIR . '/timestamp.txt';
        $legacy = getenv('BILLING_LEGACY_TIMESTAMP_PATH');
        if (!$legacy && !empty($billing_runtime_site_config['legacy_timestamp_path'])) {
            $legacy = (string)$billing_runtime_site_config['legacy_timestamp_path'];
        }
        if (!$legacy) {
            $panelRoot = billing_runtime_panel_root();
            if ($panelRoot) {
                $legacy = dirname($panelRoot) . '/Website/timestamp.txt';
            }
        }

        if (!$legacy || !is_readable($legacy)) {
            return;
        }

        $legacyText = trim((string)@file_get_contents($legacy));
        if ($legacyText === '') {
            return;
        }

        $currentText = is_readable($billingTs) ? trim((string)@file_get_contents($billingTs)) : '';
        if ($legacyText === $currentText) {
            return;
        }

        $targetDir = dirname($billingTs);
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0775, true);
        }
        if (is_writable($targetDir) || is_writable($billingTs)) {
            @file_put_contents($billingTs, $legacyText . PHP_EOL, LOCK_EX);
        }
    }
}

billing_sync_timestamp_from_legacy();
