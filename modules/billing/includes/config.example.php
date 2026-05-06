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
# Leave empty to use relative paths (works for any install path).
# Set to your full base URL (without trailing slash) if you need absolute URLs:
#   e.g. "https://gameservers.world" or "http://173.208.136.11/testing/modules/billing"
$SITE_BASE_URL = '';

# --- Background image ---
# Relative to the billing site root.
$SITE_BACKGROUND = 'images/dark.jpg';

# --- Data directory ---
# Absolute path where payment webhook JSON files are stored.
# Default: modules/billing/data/
$SITE_DATA_DIR = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';

# --- PayPal settings ---
$paypal_sandbox       = true;   // Set to false for live payments
$paypal_client_id     = '';     // Your PayPal Client ID
$paypal_client_secret = '';     // Your PayPal Client Secret
$paypal_webhook_id    = '';     // Your PayPal Webhook ID (for webhook signature verification)

# --- Admin config backup retention ---
# Number of config backups to keep (1–10). Oldest backups beyond this limit are deleted.
$SITE_CONFIG_BACKUP_RETENTION = 5;
