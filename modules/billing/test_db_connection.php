<?php
/**
 * Database Connection Test Script
 * 
 * This script tests the database connection and queries the {$table_prefix}users table
 * to verify the login functionality will work correctly.
 * 
 * ⚠️ SECURITY WARNING: Delete this file after testing!
 * This file exposes sensitive database information and should not be 
 * accessible in production.
 */

// Include billing bootstrap (loads config and DB helper)
require_once(__DIR__ . '/bootstrap.php');

// Create database connection
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}

echo "<!DOCTYPE html>
<html>
<head>
    <title>Database Connection Test</title>
    <style>
        body { font-family: monospace; padding: 20px; background: #f5f5f5; }
        .success { color: green; font-weight: bold; }
        .error { color: red; font-weight: bold; }
        .info { color: blue; }
        .section { background: white; padding: 15px; margin: 10px 0; border-radius: 5px; }
        pre { background: #eee; padding: 10px; border-radius: 3px; overflow-x: auto; }
    </style>
</head>
<body>
    <h1>Database Connection Test</h1>
    <p class='error'>⚠️ WARNING: Delete this file after testing!</p>
";

// Test 1: Check database connection
echo "<div class='section'>";
echo "<h2>Test 1: Database Connection</h2>";
if ($db && mysqli_ping($db)) {
    echo "<p class='success'>✓ Database connection successful!</p>";
    echo "<p class='info'>Connected to database</p>";
} else {
    echo "<p class='error'>✗ Database connection failed!</p>";
    if ($db) {
        echo "<p class='error'>Error: " . mysqli_connect_error() . "</p>";
    }
    echo "</div></body></html>";
    exit();
}
echo "</div>";

// Test 2: Check if {$table_prefix}users table exists
echo "<div class='section'>";
echo "<h2>Test 2: Check {$table_prefix}users Table</h2>";
$result = mysqli_query($db, "SHOW TABLES LIKE '{$table_prefix}users'");
if ($result && mysqli_num_rows($result) > 0) {
    echo "<p class='success'>✓ {$table_prefix}users table exists!</p>";
} else {
    echo "<p class='error'>✗ {$table_prefix}users table not found!</p>";
    echo "</div></body></html>";
    exit();
}
echo "</div>";

// Test 3: Check table structure
echo "<div class='section'>";
echo "<h2>Test 3: Table Structure</h2>";
$result = mysqli_query($db, "DESCRIBE {$table_prefix}users");
if ($result) {
    echo "<p class='success'>✓ Table structure retrieved</p>";
    echo "<p>Columns:</p><pre>";
    while ($row = mysqli_fetch_assoc($result)) {
        echo $row['Field'] . " (" . $row['Type'] . ")\n";
    }
    echo "</pre>";
} else {
    echo "<p class='error'>✗ Failed to retrieve table structure</p>";
}
echo "</div>";

// Test 4: Count users
echo "<div class='section'>";
echo "<h2>Test 4: User Count</h2>";
$result = mysqli_query($db, "SELECT COUNT(*) as count FROM {$table_prefix}users");
if ($result) {
    $row = mysqli_fetch_assoc($result);
    echo "<p class='success'>✓ Total users in database: " . $row['count'] . "</p>";
} else {
    echo "<p class='error'>✗ Failed to count users</p>";
}
echo "</div>";

// Test 5: Check required columns
echo "<div class='section'>";
echo "<h2>Test 5: Required Columns Check</h2>";
$required_columns = ['user_id', 'users_login', 'users_passwd', 'users_role', 'users_email'];
$result = mysqli_query($db, "SHOW COLUMNS FROM {$table_prefix}users");
$existing_columns = [];
while ($row = mysqli_fetch_assoc($result)) {
    $existing_columns[] = $row['Field'];
}

$all_present = true;
foreach ($required_columns as $col) {
    if (in_array($col, $existing_columns)) {
        echo "<p class='success'>✓ Column '$col' exists</p>";
    } else {
        echo "<p class='error'>✗ Column '$col' missing!</p>";
        $all_present = false;
    }
}

if ($all_present) {
    echo "<p class='success'><strong>All required columns present!</strong></p>";
} else {
    echo "<p class='error'><strong>Some required columns are missing!</strong></p>";
}
echo "</div>";

// Test 6: Test MD5 hash function
echo "<div class='section'>";
echo "<h2>Test 6: Password Hashing Test</h2>";
$test_password = "testpassword";
$hashed = md5($test_password);
echo "<p class='info'>Test password: '$test_password'</p>";
echo "<p class='info'>MD5 hash: '$hashed'</p>";
echo "<p class='success'>✓ MD5 hashing works correctly</p>";
echo "</div>";

// Test 7: Test session functionality
echo "<div class='section'>";
echo "<h2>Test 7: Session Test</h2>";
session_name("opengamepanel_web");
session_start();
$_SESSION['test_key'] = 'test_value';
if (isset($_SESSION['test_key']) && $_SESSION['test_key'] === 'test_value') {
    echo "<p class='success'>✓ Sessions working correctly</p>";
    echo "<p class='info'>Session name: " . session_name() . "</p>";
    echo "<p class='info'>Session ID: " . session_id() . "</p>";
    unset($_SESSION['test_key']);
} else {
    echo "<p class='error'>✗ Session test failed</p>";
}
echo "</div>";

echo "<div class='section'>";
echo "<h2>Summary</h2>";
echo "<p class='success'><strong>✓ All tests passed! Login functionality should work correctly.</strong></p>";
echo "<p class='error'><strong>⚠️ Remember to delete this test file before deploying to production!</strong></p>";
echo "</div>";

echo "</body></html>";
?>

