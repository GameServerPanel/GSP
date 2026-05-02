<?php
require_once(__DIR__ . '/../includes/config_loader.php');
$db = mysqli_connect($db_host, $db_user, $db_pass, $db_name, isset($db_port) ? (int)$db_port : null);
if (!$db) {
    echo "DB connect failed: " . mysqli_connect_error() . PHP_EOL;
    exit(1);
}
$prefix    = isset($table_prefix) ? $table_prefix : '';
$user      = $argv[1] ?? 'iaregamer';
$user_safe = mysqli_real_escape_string($db, $user);
$has_shadow = false;
$res_cols = mysqli_query($db, "SHOW COLUMNS FROM `{$prefix}users` LIKE 'users_pass_hash'");
if ($res_cols && mysqli_num_rows($res_cols) > 0) $has_shadow = true;
$select_fields = 'user_id, users_login, users_passwd';
if ($has_shadow) $select_fields .= ", users_pass_hash";
$q = "SELECT $select_fields FROM `{$prefix}users` WHERE users_login = '$user_safe' LIMIT 1";
$res = mysqli_query($db, $q);
if (!$res) {
    echo "Query error: " . mysqli_error($db) . PHP_EOL;
    exit(1);
}
if (mysqli_num_rows($res) === 0) {
    echo "No user found for '$user'\n";
} else {
    $row = mysqli_fetch_assoc($res);
    echo "Found user: id={$row['user_id']}, login={$row['users_login']}\n";
    echo "passwd(db)=" . ($row['users_passwd'] ?? '') . "\n";
    echo "pass_hash(db)=" . ($row['users_pass_hash'] ?? '') . "\n";
}
mysqli_close($db);
?>
