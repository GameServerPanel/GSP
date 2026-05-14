<?php
###############################################
# Billing Website Configuration Example
#
# Copy this file to config.inc.php and fill in
# your actual settings.
# config.inc.php is excluded from version control.
#
# This file is used by modules/billing/ both as a
# standalone website and as a panel-integrated module.
# The billing module reads ONLY this file — it does NOT
# depend on the parent panel's includes/config.inc.php.
###############################################

# --- Database connection ---
$db_host = "localhost";
$db_port = "3306";          // MySQL port (default 3306)
$db_user = "your_db_user";
$db_pass = "your_db_password";
$db_name = "your_db_name";  // Panel database name (e.g. "gsp" or "panel")
$table_prefix = "gsp_";     // Table prefix used in the panel database
$db_type = "mysql";

# --- Site base URL ---
# Full base URL WITHOUT trailing slash. Leave empty to use relative paths.
# Example: "https://gameservers.world" or "https://your-domain.com"
$SITE_BASE_URL = '';

# --- Background image ---
# Relative to the billing site root.
$SITE_BACKGROUND = 'images/dark.jpg';

# --- Data directory ---
# Absolute path where payment webhook JSON files are stored.
# Default: modules/billing/data/
$SITE_DATA_DIR = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';

# --- PayPal settings ---
# Mode: 'sandbox' for testing, 'live' for real payments.
$paypal_mode = 'sandbox';

# Sandbox credentials (PayPal Developer Dashboard → sandbox app)
$paypal_sandbox_client_id     = '';  // e.g. AfvY_...
$paypal_sandbox_client_secret = '';  // Keep server-side only
$paypal_sandbox_webhook_id    = '';  // Set after registering webhook in PayPal

# Live credentials (leave blank until ready for production)
$paypal_live_client_id     = '';
$paypal_live_client_secret = '';
$paypal_live_webhook_id    = '';

# Webhook path (relative to billing site root, must start with /)
# Full public URL = $SITE_BASE_URL + $paypal_webhook_path
# Example full URL: https://gameservers.world/paypal/webhook.php
$paypal_webhook_path = '/paypal/webhook.php';

# --- Admin config backup retention ---
# Number of config backups to keep (1–10). Oldest backups beyond this limit are deleted.
$SITE_CONFIG_BACKUP_RETENTION = 5;
