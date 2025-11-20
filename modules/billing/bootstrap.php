<?php
// modules/billing/bootstrap.php
// Central bootstrap for billing website pages. Loads config, provides safe DB helper
// and ensures $table_prefix is available.

// Ensure session sync with panel happens first
require_once __DIR__ . '/includes/session_bridge.php';

// Load configuration (includes/config.inc.php) if present
$config_path = __DIR__ . '/includes/config.inc.php';
if (file_exists($config_path)) {
    require_once $config_path;
} else {
    trigger_error('Billing config not found: ' . $config_path, E_USER_WARNING);
}

// Ensure $table_prefix exists (fallback to empty string)
if (!isset($table_prefix)) {
    $table_prefix = '';
}

// Billing DB connection cached in $billing_db
if (!isset($billing_db)) {
    $billing_db = null;
}

// Track whether bootstrap opened the connection (so callers can safely close it)
$billing_db_opened_by_bootstrap = false;

/**
 * Get a mysqli connection for billing pages.
 * - Reuses global $db if already created by other code.
 * - Tries to open a new connection using config variables if needed.
 * - Returns null on failure.
 */
function billing_get_db()
{
    global $billing_db, $db, $db_host, $db_user, $db_pass, $db_name, $billing_db_opened_by_bootstrap;
    if (!empty($billing_db) && ($billing_db instanceof mysqli)) {
        return $billing_db;
    }
    if (!empty($db) && ($db instanceof mysqli)) {
        $billing_db = $db;
        return $billing_db;
    }
    // Try to connect (suppress warnings; caller may check return value)
    $conn = @mysqli_connect($db_host ?? null, $db_user ?? null, $db_pass ?? null, $db_name ?? null);
    if ($conn) {
        // Set charset when available
        if (function_exists('mysqli_set_charset')) {
            @mysqli_set_charset($conn, 'utf8mb4');
        }
        $billing_db = $conn;
        $billing_db_opened_by_bootstrap = true;
        return $billing_db;
    }
    // Leave $billing_db as null
    $billing_db = null;
    return null;
}

/**
 * Close DB connection only if it was opened by bootstrap. If the connection
 * is shared (created by other code) this function will not close it.
 */
function billing_maybe_close_db($conn)
{
    global $billing_db, $billing_db_opened_by_bootstrap;
    if (!($conn instanceof mysqli)) return;
    if (!empty($billing_db_opened_by_bootstrap) && $billing_db === $conn) {
        @mysqli_close($conn);
        $billing_db = null;
        $billing_db_opened_by_bootstrap = false;
    }
}

// Small helper wrappers commonly used across billing pages
if (!function_exists('esc_mysqli')) {
    function esc_mysqli($db, $v)
    {
        if ($db instanceof mysqli) {
            return $db->real_escape_string((string)$v);
        }
        return addslashes((string)$v);
    }
}

if (!function_exists('fetch_all_assoc')) {
    function fetch_all_assoc($db, $sql)
    {
        if (!($db instanceof mysqli)) return [];
        $res = $db->query($sql);
        return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
    }
}

if (!function_exists('col_exists')) {
    function col_exists($db, $table, $col)
    {
        if (!($db instanceof mysqli)) return false;
        $t = $db->real_escape_string($table);
        $c = $db->real_escape_string($col);
        $res = $db->query("SHOW COLUMNS FROM `{$t}` LIKE '{$c}'");
        return ($res && $res->num_rows > 0);
    }
}

// expose a convenience variable for scripts that expect $db
// Do not overwrite an existing $db if present
if (!isset($db) || !($db instanceof mysqli)) {
    $maybe = billing_get_db();
    if ($maybe instanceof mysqli) {
        $db = $maybe;
    }
}

// End bootstrap
