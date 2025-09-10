#!/usr/bin/php
<?php
/*
 * Resource Collection Script for OGP Monitoring
 * This script should be run every 5 minutes via cron job
 * 
 * Cron example: 
 * 0,5,10,15,20,25,30,35,40,45,50,55 * * * * /usr/bin/php /path/to/GSP/scripts/resource_collector.php
 */

// Set up basic environment
chdir(dirname(__DIR__));
require_once('includes/config.inc.php');
require_once('includes/functions.php');
require_once('includes/helpers.php');
require_once('includes/database.php');

// Connect to database
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);

if (!$db instanceof OGPDatabase) {
    error_log("Resource collector: Failed to connect to database");
    exit(1);
}

// Include resource monitoring functions
require_once('modules/resource_monitor/resource_functions.php');

echo date('Y-m-d H:i:s') . " - Starting resource collection\n";

try {
    // Collect resources from all agents
    collect_all_resources();
    
    // Clean up old data
    cleanup_old_data();
    
    echo date('Y-m-d H:i:s') . " - Resource collection completed successfully\n";
} catch (Exception $e) {
    error_log("Resource collector error: " . $e->getMessage());
    echo date('Y-m-d H:i:s') . " - Resource collection failed: " . $e->getMessage() . "\n";
    exit(1);
}

exit(0);
?>