<?php
###############################################
# Website Database Configuration
# This file contains the database connection
# settings for the _website standalone site.
#
# These settings should match the panel's
# database configuration in includes/config.inc.php
###############################################
$db_host="localhost";
$db_port="3306";
$db_user="localuser";
$db_pass="Pkloyn7yvpht!";
$db_name="panel";
$table_prefix="gsp_";
$db_type="mysql";

// Optional: base URL used by admin pages to build absolute image previews.
// Leave empty to prefer relative paths (local folder).
// To enable production base URL, uncomment and set it to your site, e.g.:
// $SITE_BASE_URL = 'https://gameservers.world';
$SITE_BASE_URL = '';

// Normalize: ensure either empty or ends without trailing slash
$SITE_BASE_URL = rtrim(trim((string)$SITE_BASE_URL), '/');

// Site-wide background image (relative to site root). Change to your preferred background.
$SITE_BACKGROUND = 'images/dark.jpg';
// Normalize
$SITE_BACKGROUND = trim((string)$SITE_BACKGROUND);

// Data directory for persisted payment webhook JSON files (relative to repo root)
$SITE_DATA_DIR = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';

// ---------------------------------------------------------------------------
// PayPal configuration
// Set credentials here — never in API files or public pages.
//
// Mode: 'sandbox' for testing, 'live' for real payments.
// ---------------------------------------------------------------------------
$paypal_mode = 'sandbox';  // 'sandbox' or 'live'

// Sandbox credentials (use for testing — safe to share with the dev team)
$paypal_sandbox_client_id     = 'AfvY_C2zA_hTHxHq7TIhtOeub4xBdySYrt_Hjj3d_WYQwjWI9NfOAVOTeResx2rgZ_nP5tOoxQSAHw8c';
$paypal_sandbox_client_secret = 'EJ216np9cAj9n7KSddez3fLVxGe-zi4oKKKl1YGqPp88XIikr4Qzbxh0XW2as-V6LgdX-upjtQAg9dC0';
$paypal_sandbox_webhook_id    = '';  // Set after registering the webhook in PayPal sandbox dashboard

// Live credentials (leave blank until ready for production)
$paypal_live_client_id     = '';
$paypal_live_client_secret = '';
$paypal_live_webhook_id    = '';

// Webhook path (relative to billing site root, must start with /)
// Full public URL = $SITE_BASE_URL + $paypal_webhook_path
// e.g. https://gameservers.world/paypal/webhook.php
$paypal_webhook_path = '/paypal/webhook.php';

// Admin config backup retention: how many backups to keep (1–10). Default 5.
$SITE_CONFIG_BACKUP_RETENTION = 5;
?>
