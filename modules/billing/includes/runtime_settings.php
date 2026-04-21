<?php
require_once(__DIR__ . '/panel_bridge.php');

if (!function_exists('billing_bool_setting')) {
    function billing_bool_setting($value, $default = false)
    {
        if ($value === null || $value === '') {
            return $default;
        }
        if (is_bool($value)) {
            return $value;
        }
        $value = strtolower(trim((string)$value));
        return in_array($value, array('1', 'true', 'yes', 'on'), true);
    }
}

if (!function_exists('billing_detect_site_base')) {
    function billing_detect_site_base()
    {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = isset($_SERVER['HTTP_HOST']) ? trim((string)$_SERVER['HTTP_HOST']) : 'localhost';
        return $scheme . '://' . $host;
    }
}

if (!function_exists('billing_absolute_url')) {
    function billing_absolute_url($url, $siteBase)
    {
        $url = trim((string)$url);
        if ($url === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $url)) {
            return $url;
        }
        return rtrim($siteBase, '/') . '/' . ltrim($url, '/');
    }
}

if (!function_exists('billing_get_paypal_settings')) {
    function billing_get_paypal_settings()
    {
        $panelSettings = billing_get_panel_settings();
        $mode = strtolower(trim((string)($panelSettings['paypal_mode'] ?? 'sandbox')));
        if (!in_array($mode, array('sandbox', 'live'), true)) {
            $mode = 'sandbox';
        }

        $sandboxClientId = trim((string)($panelSettings['paypal_sandbox_client_id'] ?? ''));
        $sandboxClientSecret = trim((string)($panelSettings['paypal_sandbox_client_secret'] ?? ''));
        $liveClientId = trim((string)($panelSettings['paypal_live_client_id'] ?? ''));
        $liveClientSecret = trim((string)($panelSettings['paypal_live_client_secret'] ?? ''));
        $fallbackClientId = trim((string)($panelSettings['paypal_client_id'] ?? ''));
        $fallbackClientSecret = trim((string)($panelSettings['paypal_client_secret'] ?? ''));

        $clientId = $mode === 'live' ? $liveClientId : $sandboxClientId;
        $clientSecret = $mode === 'live' ? $liveClientSecret : $sandboxClientSecret;
        if ($clientId === '') {
            $clientId = $fallbackClientId;
        }
        if ($clientSecret === '') {
            $clientSecret = $fallbackClientSecret;
        }

        $currency = strtoupper(trim((string)($panelSettings['paypal_currency'] ?? 'USD')));
        if ($currency === '') {
            $currency = 'USD';
        }

        $enabled = billing_bool_setting($panelSettings['paypal_enabled'] ?? null, ($clientId !== '' && $clientSecret !== ''));
        $siteBase = billing_detect_site_base();
        $returnUrl = billing_absolute_url($panelSettings['paypal_return_url'] ?? '/payment_success.php', $siteBase);
        $cancelUrl = billing_absolute_url($panelSettings['paypal_cancel_url'] ?? '/payment_cancel.php', $siteBase);

        return array(
            'enabled' => $enabled,
            'mode' => $mode,
            'sandbox' => $mode !== 'live',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'currency' => $currency,
            'webhook_id' => trim((string)($panelSettings['paypal_webhook_id'] ?? '')),
            'email' => trim((string)($panelSettings['paypal_email'] ?? '')),
            'return_url' => $returnUrl,
            'cancel_url' => $cancelUrl,
            'site_base' => $siteBase,
            'api_base' => $mode === 'live' ? 'https://api-m.paypal.com' : 'https://api-m.sandbox.paypal.com',
        );
    }
}

if (!function_exists('billing_paypal_is_ready')) {
    function billing_paypal_is_ready($settings)
    {
        return !empty($settings['enabled']) && !empty($settings['client_id']) && !empty($settings['client_secret']);
    }
}

