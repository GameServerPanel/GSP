<?php
###############################################
# Website Database Configuration
# This file contains the database connection
# settings for the modules/billing website.
# 
# These settings should match the panel's
# database configuration in /includes/config.inc.php
# to ensure both the website and panel access
# the same database.
###############################################
$db_host="localhost";
$db_user="localuser";
$db_pass="Pkloyn7yvpht!";
$db_name="panel";
$table_prefix="ogp_";
$db_type="mysql";

// Optional: base URL used by admin pages to build absolute image previews.
// Leave empty to prefer relative paths (local folder).
// To enable production base URL, uncomment and set it to your site, e.g.:
// $SITE_BASE_URL = 'https://gameservers.world/';
$SITE_BASE_URL = '';

// Normalize: ensure either empty or ends without trailing slash
$SITE_BASE_URL = trim((string)$SITE_BASE_URL);

// Site-wide background image (relative to site root). Change to your preferred background.
$SITE_BACKGROUND = 'images/dark.jpg';
// Normalize
$SITE_BACKGROUND = trim((string)$SITE_BACKGROUND);

// Data directory for persisted payment webhook JSON files (relative to module root)
$SITE_DATA_DIR = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'data';
?>
