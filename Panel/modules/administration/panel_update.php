<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * GSP Panel Update System
 * Repository-layout aware updater with backup, rollback, patching, and apache path helpers.
 *
 */

defined('GSP_PANEL_DIR') || define('GSP_PANEL_DIR', realpath(dirname(__FILE__) . '/../../'));
defined('GSP_ROOT_DIR') || define('GSP_ROOT_DIR', realpath(dirname(GSP_PANEL_DIR)) ?: dirname(GSP_PANEL_DIR));
defined('GSP_WEBSITE_DIR') || define('GSP_WEBSITE_DIR', GSP_ROOT_DIR . '/Website');
defined('GSP_BACKUP_BASE') || define('GSP_BACKUP_BASE', GSP_ROOT_DIR . '/backups');
defined('GSP_UPDATE_LOG') || define('GSP_UPDATE_LOG', GSP_ROOT_DIR . '/logs/update_trace.log');
defined('GSP_VERSION_FILE') || define('GSP_VERSION_FILE', GSP_PANEL_DIR . '/includes/panel_version.php');
defined('GSP_VERSION_JSON') || define('GSP_VERSION_JSON', GSP_ROOT_DIR . '/version.json');
defined('GSP_PATCH_DIR') || define('GSP_PATCH_DIR', GSP_PANEL_DIR . '/modules/update/patches');
defined('GSP_EXPECTED_ROOT') || define('GSP_EXPECTED_ROOT', '/var/www/html/GSP');
defined('GSP_EXPECTED_PANEL') || define('GSP_EXPECTED_PANEL', GSP_EXPECTED_ROOT . '/Panel');
defined('GSP_EXPECTED_WEBSITE') || define('GSP_EXPECTED_WEBSITE', GSP_EXPECTED_ROOT . '/Website');
defined('GSP_CANONICAL_TIMESTAMP_FILE') || define('GSP_CANONICAL_TIMESTAMP_FILE', GSP_WEBSITE_DIR . '/timestamp.txt');
defined('GSP_BILLING_TIMESTAMP_FILE') || define('GSP_BILLING_TIMESTAMP_FILE', GSP_PANEL_DIR . '/modules/billing/timestamp.txt');

$gspPatchManager = GSP_PANEL_DIR . '/modules/update/patch_manager.php';
if (file_exists($gspPatchManager)) {
require_once($gspPatchManager);
}

function gsp_update_log($message)
{
$log_dir = dirname(GSP_UPDATE_LOG);
if (!is_dir($log_dir)) {
@mkdir($log_dir, 0755, true);
}
$line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
@file_put_contents(GSP_UPDATE_LOG, $line, FILE_APPEND | LOCK_EX);
}

function gsp_log_update_to_db($channel, $branch, $status, $message, $backup_path = null, $db_backup_path = null, $file_backup_path = null, $started_at = null, $finished_at = null)
{
global $db;
if (!isset($db) || !is_object($db)) {
return;
}
if ($started_at === null) {
$started_at = date('Y-m-d H:i:s');
}
$channel = $db->real_escape_string((string)$channel);
$branch = $branch !== null ? "'" . $db->real_escape_string((string)$branch) . "'" : 'NULL';
$status = $db->real_escape_string((string)$status);
$message_esc = $message !== null ? "'" . $db->real_escape_string((string)$message) . "'" : 'NULL';
$backup_path_esc = $backup_path !== null ? "'" . $db->real_escape_string((string)$backup_path) . "'" : 'NULL';
$db_backup_esc = $db_backup_path !== null ? "'" . $db->real_escape_string((string)$db_backup_path) . "'" : 'NULL';
$file_backup_esc = $file_backup_path !== null ? "'" . $db->real_escape_string((string)$file_backup_path) . "'" : 'NULL';
$started_esc = "'" . $db->real_escape_string((string)$started_at) . "'";
$finished_esc = $finished_at !== null ? "'" . $db->real_escape_string((string)$finished_at) . "'" : 'NULL';
$db->query(
"INSERT INTO OGP_DB_PREFIXpanel_update_log"
. " (channel, branch, status, message, backup_path, db_backup_path, file_backup_path, started_at, finished_at)"
. " VALUES ('{$channel}', {$branch}, '{$status}', {$message_esc}, {$backup_path_esc}, {$db_backup_esc}, {$file_backup_esc}, {$started_esc}, {$finished_esc})"
);
}

function gsp_random_token($bytes = 16)
{
if (function_exists('random_bytes')) {
try {
return bin2hex(random_bytes($bytes));
} catch (Exception $e) {
}
}
if (function_exists('openssl_random_pseudo_bytes')) {
return bin2hex(openssl_random_pseudo_bytes($bytes));
}
return sha1(uniqid('gsp', true) . mt_rand());
}

function gsp_detect_repo_root()
{
$candidates = [
GSP_ROOT_DIR,
GSP_PANEL_DIR,
dirname(GSP_ROOT_DIR),
];
foreach ($candidates as $candidate) {
$gitPath = $candidate . '/.git';
if (is_dir($gitPath) || is_file($gitPath)) {
return realpath($candidate) ?: $candidate;
}
}
return null;
}

function gsp_get_current_version()
{
if (file_exists(GSP_VERSION_FILE)) {
$code = @file_get_contents(GSP_VERSION_FILE);
if (preg_match("/define\('GSP_VERSION',\s*'([^']+)'\)/", (string)$code, $m)) {
return $m[1];
}
}
return 'unknown';
}

function gsp_get_current_branch()
{
$repo_root = gsp_detect_repo_root();
if ($repo_root && function_exists('exec')) {
$out = [];
$ret = 0;
exec('git -C ' . escapeshellarg($repo_root) . ' rev-parse --abbrev-ref HEAD 2>/dev/null', $out, $ret);
if ($ret === 0 && !empty($out[0])) {
return trim($out[0]);
}
}
return 'unknown';
}

function gsp_get_git_commit()
{
$repo_root = gsp_detect_repo_root();
if (!$repo_root || !function_exists('exec')) {
return null;
}
$out = [];
$ret = 0;
exec('git -C ' . escapeshellarg($repo_root) . ' rev-parse HEAD 2>/dev/null', $out, $ret);
if ($ret === 0 && !empty($out[0])) {
$sha = trim($out[0]);
if (preg_match('/^[0-9a-f]{40,64}$/i', $sha)) {
return $sha;
}
}
return null;
}

function gsp_read_version_json()
{
if (!file_exists(GSP_VERSION_JSON)) {
return null;
}
$data = json_decode(@file_get_contents(GSP_VERSION_JSON), true);
return is_array($data) ? $data : null;
}

function gsp_write_version_file($version, $branch_or_type)
{
$content = "<?php\n";
$content .= "// GSP Panel Version - written by the panel update system. Do not edit manually.\n";
$content .= "define('GSP_VERSION', " . var_export($version, true) . ");\n";
$content .= "define('GSP_BRANCH', " . var_export($branch_or_type, true) . ");\n";
$content .= "define('GSP_UPDATE_TIME', " . var_export(date('Y-m-d H:i:s'), true) . ");\n";
@file_put_contents(GSP_VERSION_FILE, $content);
}

function gsp_write_version_json($installed_type, $installed_source, $installed_version, $installed_commit = null)
{
$data = [
'installed_type' => (string)$installed_type,
'installed_source' => (string)$installed_source,
'installed_version' => (string)$installed_version,
'installed_commit' => $installed_commit,
'installed_at' => date('Y-m-d H:i:s'),
];
@file_put_contents(GSP_VERSION_JSON, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

function gsp_fetch_github_releases($repo_owner, $repo_name)
{
$url = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/releases?per_page=30";
if (function_exists('curl_init')) {
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'GSP-Panel-Updater');
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code === 200 && $data) {
return json_decode($data, true);
}
}
$ctx = stream_context_create([
'http' => [
'method' => 'GET',
'header' => "User-Agent: GSP-Panel-Updater\r\n",
'timeout' => 15,
],
'ssl' => [
'verify_peer' => true,
'verify_peer_name' => true,
],
]);
$data = @file_get_contents($url, false, $ctx);
if ($data) {
return json_decode($data, true);
}
return false;
}

function gsp_preflight_check()
{
$errors = [];
$warnings = [];
$cwd = getcwd();
$cwd_real = $cwd ? (realpath($cwd) ?: $cwd) : '';
$root_real = realpath(GSP_ROOT_DIR) ?: GSP_ROOT_DIR;
$panel_real = realpath(GSP_PANEL_DIR) ?: GSP_PANEL_DIR;
$website_real = realpath(GSP_WEBSITE_DIR) ?: GSP_WEBSITE_DIR;
$expected_root_real = realpath(GSP_EXPECTED_ROOT) ?: GSP_EXPECTED_ROOT;
$expected_panel_real = realpath(GSP_EXPECTED_PANEL) ?: GSP_EXPECTED_PANEL;
$expected_website_real = realpath(GSP_EXPECTED_WEBSITE) ?: GSP_EXPECTED_WEBSITE;
$layout = [
'cwd' => $cwd,
'cwd_real' => $cwd_real,
'expected_root' => GSP_EXPECTED_ROOT,
'expected_panel' => GSP_EXPECTED_PANEL,
'expected_website' => GSP_EXPECTED_WEBSITE,
'gsp_root' => GSP_ROOT_DIR,
'gsp_root_real' => $root_real,
'panel_dir' => GSP_PANEL_DIR,
'panel_dir_real' => $panel_real,
'website_dir' => GSP_WEBSITE_DIR,
'website_dir_real' => $website_real,
'backup_dir' => GSP_BACKUP_BASE,
'config_file' => GSP_PANEL_DIR . '/includes/config.inc.php',
'destination_panel' => GSP_PANEL_DIR,
'destination_website' => GSP_WEBSITE_DIR,
];

if (!$layout['cwd']) {
$errors[] = 'Unable to read current working directory.';
} elseif (strpos($cwd_real, $panel_real) !== 0) {
$errors[] = 'Current working directory must be under live Panel path: ' . $panel_real;
}
if (!is_dir(GSP_ROOT_DIR)) {
$errors[] = 'Detected GSP root path is missing.';
}
if ($root_real !== $expected_root_real) {
$errors[] = 'Detected GSP root does not match expected live root: ' . GSP_EXPECTED_ROOT;
}
if (!is_dir(GSP_PANEL_DIR)) {
$errors[] = 'Panel directory is missing.';
}
if ($panel_real !== $expected_panel_real) {
$errors[] = 'Detected Panel path does not match expected live Panel path: ' . GSP_EXPECTED_PANEL;
}
if (!is_dir(GSP_WEBSITE_DIR)) {
$errors[] = 'Website directory is missing.';
}
if ($website_real !== $expected_website_real) {
$errors[] = 'Detected Website path does not match expected live Website path: ' . GSP_EXPECTED_WEBSITE;
}
if (!file_exists($layout['config_file'])) {
$errors[] = 'Panel includes/config.inc.php was not found and cannot be preserved.';
}
if (!is_dir(GSP_BACKUP_BASE)) {
if (!@mkdir(GSP_BACKUP_BASE, 0755, true) && !is_dir(GSP_BACKUP_BASE)) {
$errors[] = 'Backups directory is missing and cannot be created.';
}
}

foreach ([GSP_ROOT_DIR, GSP_PANEL_DIR, GSP_WEBSITE_DIR, GSP_BACKUP_BASE] as $path) {
if (!is_writable($path)) {
$errors[] = 'Path is not writable: ' . $path;
}
}

gsp_update_log('Preflight layout: ' . json_encode($layout));
foreach ($warnings as $warning) {
gsp_update_log('Preflight warning: ' . $warning);
}
foreach ($errors as $error) {
gsp_update_log('Preflight error: ' . $error);
}

return [
'success' => empty($errors),
'errors' => $errors,
'warnings' => $warnings,
'layout' => $layout,
];
}

function gsp_load_panel_db_config()
{
$config_file = GSP_PANEL_DIR . '/includes/config.inc.php';
if (!is_readable($config_file)) {
return null;
}
$capture = static function ($__file) {
$db_host = 'localhost';
$db_port = '3306';
$db_user = '';
$db_pass = '';
$db_name = '';
@include $__file;
return [
'host' => (string)$db_host,
'port' => (string)$db_port,
'user' => (string)$db_user,
'pass' => (string)$db_pass,
'name' => (string)$db_name,
];
};
$cfg = $capture($config_file);
if (empty($cfg['user']) || empty($cfg['name'])) {
return null;
}
return $cfg;
}

function gsp_create_database_backup($backup_dir, $db_config)
{
$sql_file = $backup_dir . '/database.sql';
$check = [];
$ret = 0;
exec('command -v mysqldump 2>/dev/null', $check, $ret);
if ($ret !== 0 || empty($check[0])) {
return ['success' => false, 'error' => 'mysqldump is not available.'];
}
$creds_file = tempnam(sys_get_temp_dir(), 'gsp_db_');
if ($creds_file === false) {
return ['success' => false, 'error' => 'Cannot create temporary credential file.'];
}
$creds = "[client]\n"
. "user=" . addcslashes($db_config['user'], "\\\n") . "\n"
. "password=" . addcslashes($db_config['pass'], "\\\n") . "\n";
if (!empty($db_config['host'])) {
$creds .= "host=" . addcslashes($db_config['host'], "\\\n") . "\n";
}
if (!empty($db_config['port']) && $db_config['port'] !== '3306') {
$creds .= "port=" . addcslashes($db_config['port'], "\\\n") . "\n";
}
@file_put_contents($creds_file, $creds);
@chmod($creds_file, 0600);
$err_tmp = tempnam(sys_get_temp_dir(), 'gsp_db_err_');
if ($err_tmp === false) {
@unlink($creds_file);
return ['success' => false, 'error' => 'Cannot create temporary error capture file.'];
}
$cmd = 'mysqldump --defaults-extra-file=' . escapeshellarg($creds_file)
. ' --skip-opt --single-transaction --add-drop-table --create-options --extended-insert --quick --set-charset'
. ' ' . escapeshellarg($db_config['name'])
. ' > ' . escapeshellarg($sql_file)
. ' 2> ' . escapeshellarg($err_tmp);
$unused = [];
$ret = 0;
exec($cmd, $unused, $ret);
@unlink($creds_file);
$err = trim((string)@file_get_contents($err_tmp));
@unlink($err_tmp);
if ($ret !== 0) {
@unlink($sql_file);
return ['success' => false, 'error' => 'mysqldump failed: ' . $err];
}
if (!file_exists($sql_file) || filesize($sql_file) < 100) {
@unlink($sql_file);
return ['success' => false, 'error' => 'Database dump file missing or empty.'];
}
return ['success' => true, 'file' => $sql_file];
}

function gsp_create_archive($source_dir, $archive_file, array $excludes = [])
{
$check = [];
$ret = 0;
exec('command -v tar 2>/dev/null', $check, $ret);
if ($ret !== 0 || empty($check[0])) {
return ['success' => false, 'error' => 'tar is not available.'];
}
if (!is_dir($source_dir)) {
return ['success' => false, 'error' => 'Source directory missing: ' . $source_dir];
}
$exclude_args = '';
foreach ($excludes as $exclude) {
$exclude_args .= ' --exclude=' . escapeshellarg($exclude);
}
$cmd = 'tar -czf ' . escapeshellarg($archive_file)
. $exclude_args
. ' -C ' . escapeshellarg($source_dir)
. ' . 2>&1';
$out = [];
$ret = 0;
exec($cmd, $out, $ret);
if ($ret !== 0) {
@unlink($archive_file);
return ['success' => false, 'error' => 'tar failed: ' . implode(' | ', $out)];
}
if (!file_exists($archive_file) || filesize($archive_file) < 100) {
@unlink($archive_file);
return ['success' => false, 'error' => 'Archive missing or empty: ' . $archive_file];
}
return ['success' => true, 'file' => $archive_file];
}

function gsp_backup_apache_configs($backup_dir)
{
$available_source = '/etc/apache2/sites-available';
$enabled_source = '/etc/apache2/sites-enabled';
if (!is_dir($available_source)) {
return ['success' => false, 'error' => 'Apache sites-available path not found: ' . $available_source];
}
$dest = $backup_dir . '/apache-configs';
$dest_available = $dest . '/sites-available';
$dest_enabled = $dest . '/sites-enabled';
if (!@mkdir($dest_available, 0755, true) && !is_dir($dest_available)) {
return ['success' => false, 'error' => 'Cannot create apache backup directory.'];
}
if (!@mkdir($dest_enabled, 0755, true) && !is_dir($dest_enabled)) {
return ['success' => false, 'error' => 'Cannot create apache enabled backup directory.'];
}
$count = 0;
foreach ((glob($available_source . '/*.conf') ?: []) as $file) {
if (@copy($file, $dest_available . '/' . basename($file))) {
$count++;
}
}
if (is_dir($enabled_source)) {
foreach ((glob($enabled_source . '/*') ?: []) as $file) {
$dst = $dest_enabled . '/' . basename($file);
if (is_link($file)) {
$target = @readlink($file);
if ($target !== false) {
@symlink($target, $dst);
$count++;
}
} elseif (is_file($file) && @copy($file, $dst)) {
$count++;
}
}
}
return ['success' => true, 'path' => $dest, 'count' => $count];
}

function gsp_prune_old_backups($max_backups = 5)
{
$entries = gsp_get_available_backups();
if (count($entries) <= $max_backups) {
return;
}
$to_delete = array_slice($entries, $max_backups);
foreach ($to_delete as $entry) {
gsp_rmdir_recursive(GSP_BACKUP_BASE . '/' . $entry['ts']);
gsp_update_log('Pruned old backup: ' . $entry['ts']);
}
}

function gsp_create_full_backup($update_target_type, $update_target_version, $include_apache = false)
{
$ts = date('Y-m-d_H-i-s');
$backup_dir = GSP_BACKUP_BASE . '/' . $ts;
if (!is_dir(GSP_BACKUP_BASE) && !@mkdir(GSP_BACKUP_BASE, 0755, true)) {
return ['success' => false, 'error' => 'Cannot create backup base directory.'];
}
if (!@mkdir($backup_dir, 0755, true)) {
return ['success' => false, 'error' => 'Cannot create backup directory: ' . $backup_dir];
}

$meta = [
'backup_timestamp' => $ts,
'gsp_root' => GSP_ROOT_DIR,
'panel_root' => GSP_PANEL_DIR,
'website_root' => GSP_WEBSITE_DIR,
'update_target_type' => $update_target_type,
'update_target_version' => $update_target_version,
'created_at' => date('Y-m-d H:i:s'),
];

$db_config = gsp_load_panel_db_config();
if ($db_config !== null) {
$db_backup = gsp_create_database_backup($backup_dir, $db_config);
if (!$db_backup['success']) {
return ['success' => false, 'error' => $db_backup['error']];
}
$meta['database_backup'] = basename($db_backup['file']);
}

$panel_backup = gsp_create_archive(
GSP_PANEL_DIR,
$backup_dir . '/panel-files.tar.gz',
['./backups', './logs', './cache', './tmp', './node_modules', './vendor']
);
if (!$panel_backup['success']) {
return ['success' => false, 'error' => $panel_backup['error']];
}
$meta['panel_archive'] = basename($panel_backup['file']);

$website_backup = gsp_create_archive(
GSP_WEBSITE_DIR,
$backup_dir . '/website-files.tar.gz',
['./logs', './cache', './tmp']
);
if (!$website_backup['success']) {
return ['success' => false, 'error' => $website_backup['error']];
}
$meta['website_archive'] = basename($website_backup['file']);

if (file_exists(GSP_VERSION_JSON)) {
@copy(GSP_VERSION_JSON, $backup_dir . '/version.json.bak');
$meta['version_json_backup'] = 'version.json.bak';
}

if ($include_apache) {
$apache_backup = gsp_backup_apache_configs($backup_dir);
if ($apache_backup['success']) {
$meta['apache_backup'] = $apache_backup['path'];
} else {
$meta['apache_backup_error'] = $apache_backup['error'];
}
}

@file_put_contents($backup_dir . '/backup.json', json_encode($meta, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
gsp_prune_old_backups(5);
gsp_update_log('Backup created: ' . $backup_dir);

return [
'success' => true,
'backup_dir' => $backup_dir,
'backup_ts' => $ts,
];
}

function gsp_download_zip($repo_owner, $repo_name, $ref, $temp_dir)
{
$url = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/zipball/{$ref}";
$zip_file = $temp_dir . '/gsp_update.zip';
if (function_exists('curl_init')) {
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_USERAGENT, 'GSP-Panel-Updater');
curl_setopt($ch, CURLOPT_TIMEOUT, 180);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$data = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);
if ($code !== 200 || !$data) {
return false;
}
@file_put_contents($zip_file, $data);
} else {
$ctx = stream_context_create([
'http' => [
'method' => 'GET',
'header' => "User-Agent: GSP-Panel-Updater\r\n",
'timeout' => 180,
],
'ssl' => [
'verify_peer' => true,
'verify_peer_name' => true,
],
]);
$data = @file_get_contents($url, false, $ctx);
if (!$data) {
return false;
}
@file_put_contents($zip_file, $data);
}
if (!file_exists($zip_file) || filesize($zip_file) < 1000) {
return false;
}
return $zip_file;
}

function gsp_extract_update_source($zip_file)
{
$temp_dir = sys_get_temp_dir() . '/gsp_upd_' . time() . '_' . mt_rand(1000, 9999);
if (!@mkdir($temp_dir, 0750, true)) {
return ['success' => false, 'error' => 'Cannot create temp extraction directory.'];
}
require_once(GSP_PANEL_DIR . '/modules/update/unzip.php');
$result = extractZip($zip_file, $temp_dir);
if (!is_array($result)) {
gsp_rmdir_recursive($temp_dir);
return ['success' => false, 'error' => 'ZIP extraction failed: ' . $result];
}
$source_root = $temp_dir;
$subdirs = glob($temp_dir . '/*', GLOB_ONLYDIR);
if ($subdirs && count($subdirs) === 1) {
$source_root = $subdirs[0];
}
return ['success' => true, 'temp_dir' => $temp_dir, 'source_root' => $source_root];
}

function gsp_resolve_source_layout($temp_checkout_path, $source_root)
{
$source_root_real = realpath($source_root) ?: $source_root;
$candidates = [$source_root_real];
if (basename($source_root_real) === 'Panel' || basename($source_root_real) === 'Website') {
$candidates[] = dirname($source_root_real);
}
$candidates = array_values(array_unique(array_filter($candidates)));
$repo_root = null;
foreach ($candidates as $candidate) {
if (is_dir($candidate . '/Panel') && is_dir($candidate . '/Website')) {
$repo_root = realpath($candidate) ?: $candidate;
break;
}
}

$layout = [
'cwd' => getcwd() ?: '',
'live_gsp_root' => GSP_ROOT_DIR,
'live_panel_path' => GSP_PANEL_DIR,
'live_website_path' => GSP_WEBSITE_DIR,
'temporary_git_checkout_path' => $temp_checkout_path,
'source_root' => $source_root_real,
'source_repo_root' => $repo_root,
'source_panel_path' => $repo_root ? ($repo_root . '/Panel') : '',
'source_website_path' => $repo_root ? ($repo_root . '/Website') : '',
'destination_panel_path' => GSP_PANEL_DIR,
'destination_website_path' => GSP_WEBSITE_DIR,
];

$errors = [];
if (!$repo_root) {
$errors[] = 'Unable to resolve source repository root containing both Panel/ and Website/.';
} else {
if (!is_dir($layout['source_panel_path'])) {
$errors[] = 'Source Panel path is missing: ' . $layout['source_panel_path'];
}
if (!is_dir($layout['source_website_path'])) {
$errors[] = 'Source Website path is missing: ' . $layout['source_website_path'];
}
}
if (strpos((string)$layout['destination_panel_path'], '/Panel/Panel') !== false) {
$errors[] = 'Destination Panel path is nested incorrectly: ' . $layout['destination_panel_path'];
}
if (strpos((string)$layout['destination_website_path'], '/Website/Website') !== false) {
$errors[] = 'Destination Website path is nested incorrectly: ' . $layout['destination_website_path'];
}
if ((realpath(GSP_ROOT_DIR) ?: GSP_ROOT_DIR) !== (realpath(GSP_EXPECTED_ROOT) ?: GSP_EXPECTED_ROOT)) {
$errors[] = 'Live root mismatch. Expected ' . GSP_EXPECTED_ROOT . ' but detected ' . GSP_ROOT_DIR;
}
if ((realpath(GSP_PANEL_DIR) ?: GSP_PANEL_DIR) !== (realpath(GSP_EXPECTED_PANEL) ?: GSP_EXPECTED_PANEL)) {
$errors[] = 'Live Panel mismatch. Expected ' . GSP_EXPECTED_PANEL . ' but detected ' . GSP_PANEL_DIR;
}
if ((realpath(GSP_WEBSITE_DIR) ?: GSP_WEBSITE_DIR) !== (realpath(GSP_EXPECTED_WEBSITE) ?: GSP_EXPECTED_WEBSITE)) {
$errors[] = 'Live Website mismatch. Expected ' . GSP_EXPECTED_WEBSITE . ' but detected ' . GSP_WEBSITE_DIR;
}
if (strpos((realpath($layout['cwd']) ?: $layout['cwd']), (realpath(GSP_PANEL_DIR) ?: GSP_PANEL_DIR)) !== 0) {
$errors[] = 'Updater must run from a working directory under the live Panel path.';
}

gsp_update_log('Deployment layout detection: ' . json_encode($layout));
foreach ($errors as $error) {
gsp_update_log('Deployment layout error: ' . $error);
}

return [
'success' => empty($errors),
'errors' => $errors,
'layout' => $layout,
];
}

function gsp_normalize_rel($path)
{
$path = str_replace('\\', '/', $path);
$path = ltrim($path, '/');
return $path;
}

function gsp_is_preserved_path($relative_path)
{
$relative_path = gsp_normalize_rel($relative_path);
$exact = [
'Panel/includes/config.inc.php',
];
$prefixes = [
'logs/',
'backups/',
'Panel/logs/',
'Panel/backups/',
'Website/logs/',
'Website/uploads/',
'Website/upload/',
'Panel/uploads/',
'Panel/upload/',
];
if (in_array($relative_path, $exact, true)) {
return true;
}
foreach ($prefixes as $prefix) {
if (strpos($relative_path, $prefix) === 0) {
return true;
}
}
return false;
}

function gsp_copy_file($src, $dst)
{
$parent = dirname($dst);
if (!is_dir($parent)) {
@mkdir($parent, 0755, true);
}
return @copy($src, $dst);
}

function gsp_copy_tree($src_root, $dst_root, $base_rel = '')
{
$copied = 0;
$skipped = [];
$copied_files = [];
$source = rtrim($src_root, '/');
$iter = new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
RecursiveIteratorIterator::SELF_FIRST
);
foreach ($iter as $item) {
$rel = gsp_normalize_rel(($base_rel !== '' ? $base_rel . '/' : '') . substr($item->getPathname(), strlen($source) + 1));
if (gsp_is_preserved_path($rel)) {
$skipped[] = $rel;
continue;
}
$dst = rtrim($dst_root, '/') . '/' . $rel;
if ($item->isDir()) {
if (!is_dir($dst)) {
@mkdir($dst, 0755, true);
}
continue;
}
if (gsp_copy_file($item->getPathname(), $dst)) {
$copied++;
if (count($copied_files) < 200) {
$copied_files[] = $rel;
}
}
}
return ['copied' => $copied, 'skipped' => $skipped, 'copied_files' => $copied_files];
}

function gsp_updater_watch_list()
{
return [
'Panel/modules/administration/panel_update.php',
'Panel/modules/update/update.php',
'Panel/modules/update/updating.php',
'Panel/modules/update/post_update.php',
'Panel/modules/update/module.php',
'Panel/modules/update/unzip.php',
'Panel/modules/update/blacklist.php',
'Panel/modules/update/patch_manager.php',
'Panel/modules/update/patches',
];
}

function gsp_collect_files_under($path, $base_rel)
{
$list = [];
if (is_file($path)) {
$list[] = gsp_normalize_rel($base_rel);
return $list;
}
if (!is_dir($path)) {
return $list;
}
$iter = new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
RecursiveIteratorIterator::LEAVES_ONLY
);
foreach ($iter as $item) {
if ($item->isFile()) {
$list[] = gsp_normalize_rel($base_rel . '/' . substr($item->getPathname(), strlen($path) + 1));
}
}
return $list;
}

function gsp_detect_updater_drift_files($source_root, $target_root)
{
$drift = [];
foreach (gsp_updater_watch_list() as $rel) {
$src = rtrim($source_root, '/') . '/' . $rel;
$files = gsp_collect_files_under($src, $rel);
foreach ($files as $fileRel) {
$s = rtrim($source_root, '/') . '/' . $fileRel;
$d = rtrim($target_root, '/') . '/' . $fileRel;
if (!file_exists($d)) {
$drift[] = $fileRel;
continue;
}
if (@hash_file('sha256', $s) !== @hash_file('sha256', $d)) {
$drift[] = $fileRel;
}
}
}
return array_values(array_unique($drift));
}

function gsp_apply_updater_files_only($source_root, $target_root, array $drift_files)
{
$copied = 0;
foreach ($drift_files as $rel) {
$src = rtrim($source_root, '/') . '/' . $rel;
$dst = rtrim($target_root, '/') . '/' . $rel;
if (is_file($src) && gsp_copy_file($src, $dst)) {
$copied++;
}
}
return $copied;
}

function gsp_apply_layout_sync(array $layout)
{
$source_root = $layout['source_repo_root'];
$source_panel = $layout['source_panel_path'];
$source_website = $layout['source_website_path'];
$destination_panel = $layout['destination_panel_path'];
$destination_website = $layout['destination_website_path'];
$top_level = scandir($source_root);
$skip = ['.', '..', '.git', '.github', '.gitignore', '.vscode'];
$copied = 0;
$panel_copied = 0;
$website_copied = 0;
$skipped = [];
$copied_files = [];
gsp_update_log('Layout sync source mapping: ' . $source_panel . ' => ' . $destination_panel . ' ; ' . $source_website . ' => ' . $destination_website);
foreach ((array)$top_level as $entry) {
if (in_array($entry, $skip, true)) {
continue;
}
if ($entry === 'Panel' || $entry === 'Website' || $entry === 'backups' || $entry === 'logs') {
continue;
}
$src = rtrim($source_root, '/') . '/' . $entry;
$dst = GSP_ROOT_DIR . '/' . $entry;
if (is_file($src)) {
$rel = gsp_normalize_rel($entry);
if (gsp_is_preserved_path($rel)) {
$skipped[] = $rel;
continue;
}
if (gsp_copy_file($src, $dst)) {
$copied++;
if (count($copied_files) < 200) {
$copied_files[] = $rel;
}
}
continue;
}
if (is_dir($src)) {
$part = gsp_copy_tree($src, GSP_ROOT_DIR, $entry);
$copied += $part['copied'];
$copied_files = array_merge($copied_files, array_slice((array)$part['copied_files'], 0, max(0, 200 - count($copied_files))));
$skipped = array_merge($skipped, $part['skipped']);
}
}
$panel_part = gsp_copy_tree($source_panel, dirname($destination_panel), basename($destination_panel));
$copied += $panel_part['copied'];
$panel_copied += $panel_part['copied'];
$copied_files = array_merge($copied_files, array_slice((array)$panel_part['copied_files'], 0, max(0, 200 - count($copied_files))));
$skipped = array_merge($skipped, $panel_part['skipped']);

$website_part = gsp_copy_tree($source_website, dirname($destination_website), basename($destination_website));
$copied += $website_part['copied'];
$website_copied += $website_part['copied'];
$copied_files = array_merge($copied_files, array_slice((array)$website_part['copied_files'], 0, max(0, 200 - count($copied_files))));
$skipped = array_merge($skipped, $website_part['skipped']);

return [
'success' => true,
'files_copied' => $copied,
'panel_files_copied' => $panel_copied,
'website_files_copied' => $website_copied,
'skipped' => array_values(array_unique($skipped)),
'copied_files' => array_slice(array_values(array_unique($copied_files)), 0, 200),
];
}

function gsp_validate_layout_sync_result(array $layout, array $sync)
{
$errors = [];
$checks = [
'Panel/modules/administration/panel_update.php',
'Panel/modules/addonsmanager/addons_manager.php',
'Website/index.php',
];
foreach ($checks as $rel) {
$src = rtrim($layout['source_repo_root'], '/') . '/' . $rel;
$dst = rtrim(GSP_ROOT_DIR, '/') . '/' . $rel;
if (!is_file($src)) {
continue;
}
if (!is_file($dst)) {
$errors[] = 'Missing deployed file: ' . $rel;
continue;
}
$src_hash = @hash_file('sha256', $src);
$dst_hash = @hash_file('sha256', $dst);
if ($src_hash === false || $dst_hash === false || $src_hash !== $dst_hash) {
$errors[] = 'Copied file verification failed: ' . $rel;
}
}
if (!empty($sync['copied_files']) && intval($sync['panel_files_copied']) === 0) {
$errors[] = 'No Panel files were copied during layout sync.';
}
if (!empty($sync['copied_files']) && intval($sync['website_files_copied']) === 0) {
$errors[] = 'No Website files were copied during layout sync.';
}
foreach ($errors as $error) {
gsp_update_log('Layout sync validation error: ' . $error);
}
return [
'success' => empty($errors),
'errors' => $errors,
];
}

function gsp_write_last_update_markers()
{
$line = 'Last Updated at ' . date('g:ia') . ' on ' . date('Y-m-d');
$targets = [GSP_CANONICAL_TIMESTAMP_FILE, GSP_BILLING_TIMESTAMP_FILE];
foreach ($targets as $target) {
$dir = dirname($target);
if (!is_dir($dir)) {
@mkdir($dir, 0775, true);
}
if (is_writable($dir) || is_writable($target)) {
@file_put_contents($target, $line . PHP_EOL, LOCK_EX);
}
}
gsp_update_log('Last-update marker files written: ' . implode(', ', $targets));
return $line;
}

function gsp_run_required_patches($updater_version)
{
global $db;
if (!function_exists('gsp_patch_run_all')) {
return ['success' => false, 'error' => 'Patch manager helper is unavailable.'];
}
$run = gsp_patch_run_all($db, GSP_PATCH_DIR, 'gsp_update_log', $updater_version);
if (!$run['success']) {
return [
'success' => false,
'error' => 'Patch failure at ' . $run['failed_patch'] . ': ' . $run['error'],
'run' => $run,
];
}
return ['success' => true, 'run' => $run];
}

function gsp_apply_update_from_zip($zip_file, $restart_nonce = '')
{
$extract = gsp_extract_update_source($zip_file);
if (!$extract['success']) {
return $extract;
}
$temp_dir = $extract['temp_dir'];
$source_root = $extract['source_root'];
$resolved_layout = gsp_resolve_source_layout($temp_dir, $source_root);
if (!$resolved_layout['success']) {
gsp_rmdir_recursive($temp_dir);
return ['success' => false, 'error' => 'Deployment layout validation failed: ' . implode(' | ', $resolved_layout['errors'])];
}
$layout = $resolved_layout['layout'];
$_SESSION['gsp_last_update_layout'] = $layout;
$updater_version = substr((string)@hash_file('sha256', $layout['source_panel_path'] . '/modules/administration/panel_update.php'), 0, 12);

$drift_files = gsp_detect_updater_drift_files($layout['source_repo_root'], GSP_ROOT_DIR);
if (!empty($drift_files) && empty($restart_nonce)) {
$copied = gsp_apply_updater_files_only($layout['source_repo_root'], GSP_ROOT_DIR, $drift_files);
$nonce = gsp_random_token(12);
$_SESSION['gsp_update_restart_nonce'] = $nonce;
gsp_update_log('Updater self-update applied (' . $copied . ' files); restart nonce=' . $nonce);
gsp_rmdir_recursive($temp_dir);
return [
'success' => false,
'restart_required' => true,
'restart_nonce' => $nonce,
'updater_files_updated' => $copied,
'drift_files' => $drift_files,
];
}
if (!empty($restart_nonce)) {
$expected = isset($_SESSION['gsp_update_restart_nonce']) ? $_SESSION['gsp_update_restart_nonce'] : null;
if ($expected === null || !hash_equals($expected, $restart_nonce)) {
gsp_rmdir_recursive($temp_dir);
return ['success' => false, 'error' => 'Invalid updater restart marker.'];
}
unset($_SESSION['gsp_update_restart_nonce']);
}

$patches = gsp_run_required_patches($updater_version);
if (!$patches['success']) {
gsp_rmdir_recursive($temp_dir);
return ['success' => false, 'error' => $patches['error']];
}

$sync = gsp_apply_layout_sync($layout);
gsp_rmdir_recursive($temp_dir);
if (!$sync['success']) {
return $sync;
}
$sync_validation = gsp_validate_layout_sync_result($layout, $sync);
if (!$sync_validation['success']) {
return ['success' => false, 'error' => 'Deployed file validation failed: ' . implode(' | ', $sync_validation['errors'])];
}
gsp_update_log('Layout sync complete: copied=' . $sync['files_copied'] . ', skipped=' . count($sync['skipped']));
gsp_update_log('Layout sync totals: Panel=' . intval($sync['panel_files_copied']) . ', Website=' . intval($sync['website_files_copied']));
if (!empty($sync['skipped'])) {
gsp_update_log('Preserved paths: ' . implode(', ', array_slice($sync['skipped'], 0, 50)));
}
if (!empty($sync['copied_files'])) {
$copied_sample = array_slice((array)$sync['copied_files'], 0, 50);
gsp_update_log('Copied file sample: ' . implode(', ', $copied_sample));
$addons_updates = array_values(array_filter((array)$sync['copied_files'], function ($rel) {
return strpos($rel, 'Panel/modules/addonsmanager/') === 0;
}));
if (!empty($addons_updates)) {
gsp_update_log('Addonsmanager files copied: ' . implode(', ', array_slice($addons_updates, 0, 50)));
}
}
return [
'success' => true,
'files_copied' => $sync['files_copied'],
'panel_files_copied' => $sync['panel_files_copied'],
'website_files_copied' => $sync['website_files_copied'],
'preserved' => $sync['skipped'],
'copied_files' => $sync['copied_files'],
'patches' => $patches['run'],
];
}

function gsp_fix_permissions($root_dir)
{
@system(
'find ' . escapeshellarg($root_dir)
. ' -maxdepth 12 \( -name "*.php" -exec chmod 644 {} + \)'
. ' -o \( -type d -exec chmod 755 {} + \) 2>/dev/null'
);
}

function gsp_clear_panel_cache($panel_dir)
{
foreach (['cache', 'temp'] as $dir) {
$cache = $panel_dir . '/' . $dir;
if (!is_dir($cache)) {
continue;
}
foreach (glob($cache . '/*.php') ?: [] as $f) {
@unlink($f);
}
foreach (glob($cache . '/*.cache') ?: [] as $f) {
@unlink($f);
}
}
}

function gsp_do_update($repo_owner, $repo_name, $ref, $update_type, $restart_nonce = '')
{
global $db;
$preflight = gsp_preflight_check();
if (!$preflight['success']) {
return ['success' => false, 'error' => 'Preflight failed: ' . implode(' | ', $preflight['errors'])];
}

$backup = gsp_create_full_backup($update_type, $ref, false);
if (!$backup['success']) {
return $backup;
}
gsp_update_log("Backup created at {$backup['backup_ts']} before {$update_type} update to {$ref}");

$temp_dir = sys_get_temp_dir() . '/gsp_dl_' . time() . '_' . mt_rand(1000, 9999);
@mkdir($temp_dir, 0750, true);
$zip_file = gsp_download_zip($repo_owner, $repo_name, $ref, $temp_dir);
if (!$zip_file) {
gsp_rmdir_recursive($temp_dir);
return ['success' => false, 'error' => 'Failed to download update ZIP from GitHub.'];
}

$apply = gsp_apply_update_from_zip($zip_file, $restart_nonce);
@unlink($zip_file);
gsp_rmdir_recursive($temp_dir);
if (!empty($apply['restart_required'])) {
$apply['backup_dir'] = $backup['backup_dir'];
$apply['success'] = false;
return $apply;
}
if (!$apply['success']) {
return $apply;
}

$commit_after = gsp_get_git_commit();
gsp_fix_permissions(GSP_ROOT_DIR);
gsp_clear_panel_cache(GSP_PANEL_DIR);
gsp_write_version_file($ref, $update_type);
$vsource = ($update_type === 'release') ? 'GitHub Releases' : $ref;
$vversion = ($update_type === 'release') ? $ref : ($commit_after ?: $ref);
gsp_write_version_json($update_type, $vsource, $vversion, $commit_after);
gsp_write_last_update_markers();
$db->setSettings(['ogp_version' => $ref, 'version_type' => $update_type]);

if (file_exists(GSP_PANEL_DIR . '/modules/modulemanager/module_handling.php')) {
require_once(GSP_PANEL_DIR . '/modules/modulemanager/module_handling.php');
}
if (function_exists('updateAllPanelModules')) {
updateAllPanelModules();
}
if (function_exists('runPostUpdateOperations')) {
runPostUpdateOperations();
}

gsp_update_log("Update to {$ref} (type={$update_type}) complete");
return [
'success' => true,
'files_copied' => $apply['files_copied'],
'panel_files_copied' => isset($apply['panel_files_copied']) ? $apply['panel_files_copied'] : 0,
'website_files_copied' => isset($apply['website_files_copied']) ? $apply['website_files_copied'] : 0,
'copied_files' => isset($apply['copied_files']) ? $apply['copied_files'] : [],
'backup_dir' => $backup['backup_dir'],
'preserved' => $apply['preserved'],
'patches' => $apply['patches'],
];
}

function gsp_get_available_backups()
{
$backups = [];
if (!is_dir(GSP_BACKUP_BASE)) {
return $backups;
}
foreach ((array)scandir(GSP_BACKUP_BASE) as $entry) {
if ($entry === '.' || $entry === '..') {
continue;
}
$dir = GSP_BACKUP_BASE . '/' . $entry;
if (!is_dir($dir)) {
continue;
}
if (!preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', $entry)) {
continue;
}
$meta_file = $dir . '/backup.json';
$meta = [];
if (file_exists($meta_file)) {
$meta = json_decode(@file_get_contents($meta_file), true) ?: [];
}
$backups[] = ['ts' => $entry, 'meta' => $meta];
}
usort($backups, function ($a, $b) {
return strcmp($b['ts'], $a['ts']);
});
return $backups;
}

function gsp_restore_archive($archive, $target)
{
if (!file_exists($archive)) {
return ['success' => false, 'error' => 'Archive not found: ' . $archive];
}
$out = [];
$ret = 0;
exec('tar -xzf ' . escapeshellarg($archive) . ' -C ' . escapeshellarg($target) . ' 2>&1', $out, $ret);
if ($ret !== 0) {
return ['success' => false, 'error' => 'tar extract failed: ' . implode(' | ', $out)];
}
return ['success' => true];
}

function gsp_restore_database_from_backup($backup_dir)
{
$sql_file = $backup_dir . '/database.sql';
if (!file_exists($sql_file)) {
return ['success' => true, 'skipped' => true];
}
$db_config = gsp_load_panel_db_config();
if ($db_config === null) {
return ['success' => false, 'error' => 'Cannot load DB config for restore.'];
}
$creds_file = tempnam(sys_get_temp_dir(), 'gsp_db_');
if ($creds_file === false) {
return ['success' => false, 'error' => 'Cannot create temporary DB credential file.'];
}
$creds = "[client]\n"
. "user=" . addcslashes($db_config['user'], "\\\n") . "\n"
. "password=" . addcslashes($db_config['pass'], "\\\n") . "\n";
if (!empty($db_config['host'])) {
$creds .= "host=" . addcslashes($db_config['host'], "\\\n") . "\n";
}
if (!empty($db_config['port']) && $db_config['port'] !== '3306') {
$creds .= "port=" . addcslashes($db_config['port'], "\\\n") . "\n";
}
@file_put_contents($creds_file, $creds);
@chmod($creds_file, 0600);
$cmd = 'mysql --defaults-extra-file=' . escapeshellarg($creds_file)
. ' ' . escapeshellarg($db_config['name'])
. ' < ' . escapeshellarg($sql_file)
. ' 2>&1';
$out = [];
$ret = 0;
exec($cmd, $out, $ret);
@unlink($creds_file);
if ($ret !== 0) {
return ['success' => false, 'error' => 'MySQL restore failed: ' . implode(' | ', $out)];
}
return ['success' => true];
}

function gsp_do_revert($backup_ts, $restore_apache = false)
{
global $db;
$backup_dir = GSP_BACKUP_BASE . '/' . $backup_ts;
if (!is_dir($backup_dir)) {
return ['success' => false, 'error' => 'Backup not found: ' . $backup_ts];
}

$had_maintenance = isset($db->getSettings()['maintenance_mode']) && $db->getSettings()['maintenance_mode'] == '1';
if (!$had_maintenance) {
$db->setSettings([
'maintenance_mode' => '1',
'maintenance_title' => 'Reverting...',
'maintenance_message' => 'The panel is being reverted to a previous backup. Please wait.',
]);
}

$panel_restore = gsp_restore_archive($backup_dir . '/panel-files.tar.gz', GSP_PANEL_DIR);
if (!$panel_restore['success']) {
if (!$had_maintenance) {
$db->setSettings(['maintenance_mode' => '0']);
}
return $panel_restore;
}
$website_archive = $backup_dir . '/website-files.tar.gz';
if (file_exists($website_archive)) {
$website_restore = gsp_restore_archive($website_archive, GSP_WEBSITE_DIR);
if (!$website_restore['success']) {
if (!$had_maintenance) {
$db->setSettings(['maintenance_mode' => '0']);
}
return $website_restore;
}
}
if (file_exists($backup_dir . '/version.json.bak')) {
@copy($backup_dir . '/version.json.bak', GSP_VERSION_JSON);
}
$db_restore = gsp_restore_database_from_backup($backup_dir);
if (!$db_restore['success']) {
gsp_update_log('Revert warning: ' . $db_restore['error']);
}

if ($restore_apache && (is_dir($backup_dir . '/apache-configs') || is_dir($backup_dir . '/apache-sites-available'))) {
$apache_backup_dir = is_dir($backup_dir . '/apache-configs') ? ($backup_dir . '/apache-configs') : ($backup_dir . '/apache-sites-available');
$apache_restore = gsp_restore_apache_backup($apache_backup_dir, true);
if (!$apache_restore['success']) {
gsp_update_log('Revert apache restore warning: ' . $apache_restore['error']);
}
}

gsp_fix_permissions(GSP_ROOT_DIR);
gsp_clear_panel_cache(GSP_PANEL_DIR);
if (!$had_maintenance) {
$db->setSettings(['maintenance_mode' => '0']);
}

gsp_update_log('Revert complete: ' . $backup_ts);
return ['success' => true, 'files_restored' => 0];
}

function gsp_get_apache_vhost_target_path($filename)
{
$name = strtolower((string)$filename);
if (strpos($name, 'panel.') === 0) {
return GSP_PANEL_DIR;
}
if (strpos($name, 'gameservers.world') !== false) {
return GSP_WEBSITE_DIR;
}
return null;
}

function gsp_is_apache_stale_path($path)
{
$path = trim((string)$path);
$stale = [
'/var/www/html/panel',
'/var/www/html/GSP/Panel/GSP/Panel',
'/var/www/html/GSP/Panel/modules/billing',
];
if (in_array($path, $stale, true)) {
return true;
}
return (strpos($path, '/var/www/html/panel/') === 0 || strpos($path, '/var/www/html/GSP/Panel/modules/billing/') === 0);
}

function gsp_parse_apache_cert_error_line($line)
{
$line = trim((string)$line);
if (preg_match("/(SSLCertificate(?:File|KeyFile)):\\s*file '([^']+)' (does not exist(?: or is empty)?|is empty)/i", $line, $m)) {
return [
'directive' => $m[1],
'path' => $m[2],
'reason' => $m[3],
'line' => $line,
];
}
return null;
}

function gsp_scan_apache_configs()
{
$base = '/etc/apache2/sites-available';
$result = [
'success' => true,
'available' => is_dir($base),
'base' => $base,
'files' => [],
'issues' => [],
'stale_issues' => [],
'ssl_issues' => [],
'planned_replacements' => [],
'recommendations' => [],
];
if (!$result['available']) {
$result['success'] = false;
$result['issues'][] = 'Apache sites-available directory not found.';
return $result;
}
$files = glob($base . '/*.conf') ?: [];
foreach ($files as $file) {
$lines = @file($file, FILE_IGNORE_NEW_LINES);
if (!is_array($lines)) {
continue;
}
$vhost = basename($file);
$target = gsp_get_apache_vhost_target_path($vhost);
$file_info = [
'file' => $file,
'vhost' => $vhost,
'target' => $target,
'document_roots' => [],
'directories' => [],
'stale_hits' => [],
'ssl_hits' => [],
];
foreach ($lines as $line_number => $line) {
if (preg_match('/^\s*DocumentRoot\s+(.+)$/i', $line, $m)) {
$path = trim($m[1], "\"' ");
$file_info['document_roots'][] = $path;
if ($target !== null && $path !== $target && gsp_is_apache_stale_path($path)) {
$msg = $vhost . ' stale DocumentRoot: ' . $path . ' -> ' . $target;
$result['stale_issues'][] = $msg;
$result['issues'][] = $msg;
$result['planned_replacements'][] = ['vhost' => $vhost, 'directive' => 'DocumentRoot', 'from' => $path, 'to' => $target];
$file_info['stale_hits'][] = $path;
}
}
if (preg_match('/^\s*<Directory\s+(.+)>/i', $line, $m)) {
$path = trim($m[1], "\"' ");
$file_info['directories'][] = $path;
if ($target !== null && $path !== $target && gsp_is_apache_stale_path($path)) {
$msg = $vhost . ' stale <Directory>: ' . $path . ' -> ' . $target;
$result['stale_issues'][] = $msg;
$result['issues'][] = $msg;
$result['planned_replacements'][] = ['vhost' => $vhost, 'directive' => '<Directory>', 'from' => $path, 'to' => $target];
$file_info['stale_hits'][] = $path;
}
}
if (preg_match('/^\s*(SSLCertificate(?:File|KeyFile))\s+(.+)$/i', $line, $m)) {
$directive = $m[1];
$path = trim($m[2], "\"' ");
if ($path !== '' && (!file_exists($path) || @filesize($path) === 0)) {
$reason = !file_exists($path) ? 'missing' : 'empty';
$msg = $vhost . ' ' . $directive . ' ' . $path . ' is ' . $reason;
$issue = ['vhost' => $vhost, 'directive' => $directive, 'path' => $path, 'reason' => $reason, 'line' => ($line_number + 1), 'message' => $msg];
$result['ssl_issues'][] = $issue;
$result['issues'][] = $msg;
$file_info['ssl_hits'][] = $issue;
}
}
}
if ($target !== null) {
$result['recommendations'][] = $vhost . ' should target ' . $target;
}
$result['files'][] = $file_info;
}
$result['stale_issues'] = array_values(array_unique($result['stale_issues']));
$result['issues'] = array_values(array_unique($result['issues']));
return $result;
}

function gsp_apache_configtest_only_cert_failures($configtest_output)
{
$lines = preg_split('/\r\n|\r|\n/', (string)$configtest_output);
$seen = 0;
foreach ((array)$lines as $line) {
$line = trim((string)$line);
if ($line === '') {
continue;
}
if (stripos($line, 'Syntax OK') !== false) {
continue;
}
if (preg_match('/^AH[0-9]+:\s+/i', $line) && stripos($line, 'Could not reliably determine') !== false) {
continue;
}
if (gsp_parse_apache_cert_error_line($line) !== null) {
$seen++;
continue;
}
return false;
}
return $seen > 0;
}

function gsp_extract_apache_configtest_cert_issues($configtest_output)
{
$issues = [];
$lines = preg_split('/\r\n|\r|\n/', (string)$configtest_output);
foreach ((array)$lines as $line) {
$parsed = gsp_parse_apache_cert_error_line($line);
if ($parsed !== null) {
$issues[] = $parsed;
}
}
return $issues;
}

function gsp_restore_apache_backup($backup_dir, $reload_apache)
{
$available_target = '/etc/apache2/sites-available';
$enabled_target = '/etc/apache2/sites-enabled';
if (!is_dir($backup_dir)) {
return ['success' => false, 'error' => 'Apache backup folder not found.'];
}
$available_backup = is_dir($backup_dir . '/sites-available') ? $backup_dir . '/sites-available' : $backup_dir;
$enabled_backup = $backup_dir . '/sites-enabled';
$restored = 0;
foreach ((glob($available_backup . '/*.conf') ?: []) as $file) {
if (@copy($file, $available_target . '/' . basename($file))) {
$restored++;
}
}
if (is_dir($enabled_backup)) {
foreach ((glob($enabled_backup . '/*') ?: []) as $file) {
$dst = $enabled_target . '/' . basename($file);
if (is_link($dst) || is_file($dst)) {
@unlink($dst);
}
if (is_link($file)) {
$link_target = @readlink($file);
if ($link_target !== false) {
@symlink($link_target, $dst);
$restored++;
}
} elseif (is_file($file) && @copy($file, $dst)) {
$restored++;
}
}
}
$test = gsp_apache_configtest();
if (!$test['success']) {
if (gsp_apache_configtest_only_cert_failures($test['output'])) {
return [
'success' => true,
'restored' => $restored,
'configtest' => $test,
'warnings' => ['Apache config restore completed, but SSL certificate file(s) are missing.'],
'ssl_issues' => gsp_extract_apache_configtest_cert_issues($test['output']),
];
}
return ['success' => false, 'error' => 'apache2ctl configtest failed after restore: ' . $test['output']];
}
$reload = ['success' => true, 'output' => 'Apache reload skipped'];
if ($reload_apache) {
$reload = gsp_apache_reload();
}
return ['success' => true, 'restored' => $restored, 'configtest' => $test, 'reload' => $reload];
}

function gsp_apache_configtest()
{
$out = [];
$ret = 0;
exec('apache2ctl configtest 2>&1', $out, $ret);
return [
'success' => ($ret === 0),
'output' => trim(implode("\n", $out)),
];
}

function gsp_apache_reload()
{
$out = [];
$ret = 0;
exec('apache2ctl graceful 2>&1', $out, $ret);
return [
'success' => ($ret === 0),
'output' => trim(implode("\n", $out)),
];
}

function gsp_fix_apache_paths($confirmed, $reload_apache)
{
if (!$confirmed) {
return ['success' => false, 'error' => 'Apache path fix requires explicit confirmation.'];
}

function gsp_disable_ssl_vhost($vhost_file, $confirmed)
{
if (!$confirmed) {
return ['success' => false, 'error' => 'SSL vhost disable requires confirmation.'];
}
$vhost = basename((string)$vhost_file);
if (!preg_match('/\.conf$/', $vhost) || strpos($vhost, '-ssl') === false) {
return ['success' => false, 'error' => 'Only SSL vhost .conf files can be disabled from this action.'];
}
$enabled_path = '/etc/apache2/sites-enabled/' . $vhost;
if (!file_exists($enabled_path) && !is_link($enabled_path)) {
return ['success' => true, 'message' => $vhost . ' is already disabled.'];
}
if (!@unlink($enabled_path)) {
return ['success' => false, 'error' => 'Failed to disable SSL site: ' . $vhost];
}
$test = gsp_apache_configtest();
if (!$test['success']) {
return ['success' => false, 'error' => 'Disabled site but apache2ctl configtest still failed: ' . $test['output']];
}
$reload = gsp_apache_reload();
gsp_update_log('Disabled SSL vhost in sites-enabled: ' . $vhost);
return ['success' => true, 'configtest' => $test, 'reload' => $reload, 'message' => 'Disabled SSL vhost: ' . $vhost];
}
$scan = gsp_scan_apache_configs();
if (!$scan['available']) {
return ['success' => false, 'error' => 'Apache config folder not available.'];
}

$backup = gsp_create_full_backup('apache-fix', 'apache-path-repair', true);
if (!$backup['success']) {
return ['success' => false, 'error' => 'Could not create backup before apache fix: ' . $backup['error']];
}

$base = '/etc/apache2/sites-available';
$files = glob($base . '/*.conf') ?: [];
$changed = [];
$planned = [];
foreach ($files as $file) {
$orig = @file_get_contents($file);
if ($orig === false) {
continue;
}
$vhost = basename($file);
$target = gsp_get_apache_vhost_target_path($vhost);
if ($target === null) {
continue;
}
$new = preg_replace_callback('/(^\s*DocumentRoot\s+)(["\']?)([^"\']+)(\2\s*$)/mi', function ($m) use ($target, $vhost, &$planned) {
$current = trim((string)$m[3]);
if ($current === $target || !gsp_is_apache_stale_path($current)) {
return $m[0];
}
$planned[] = ['vhost' => $vhost, 'directive' => 'DocumentRoot', 'from' => $current, 'to' => $target];
return $m[1] . $m[2] . $target . $m[2];
}, $orig);
$new = preg_replace_callback('/(^\s*<Directory\s+)(["\']?)([^"\'>]+)(\2\s*>)/mi', function ($m) use ($target, $vhost, &$planned) {
$current = trim((string)$m[3]);
if ($current === $target || !gsp_is_apache_stale_path($current)) {
return $m[0];
}
$planned[] = ['vhost' => $vhost, 'directive' => '<Directory>', 'from' => $current, 'to' => $target];
return $m[1] . $m[2] . $target . $m[2] . '>';
}, $new);
if ($new !== $orig) {
@file_put_contents($file, $new);
$changed[] = $file;
}
}

$test = gsp_apache_configtest();
if (!$test['success']) {
$cert_only = gsp_apache_configtest_only_cert_failures($test['output']);
if (!$cert_only) {
$restore = gsp_restore_apache_backup($backup['backup_dir'] . '/apache-configs', false);
return [
'success' => false,
'error' => 'apache2ctl configtest failed; restored backup. Output: ' . $test['output']
. ($restore['success'] ? '' : (' | restore failed: ' . $restore['error'])),
];
}
gsp_update_log('Apache path fix completed but SSL certificates are missing: ' . $test['output']);
return [
'success' => true,
'warning' => 'Apache paths fixed, but SSL certificate is missing.',
'changed_files' => $changed,
'backup_dir' => $backup['backup_dir'],
'configtest' => $test,
'planned_replacements' => $planned,
'ssl_issues' => gsp_extract_apache_configtest_cert_issues($test['output']),
'reload' => ['success' => false, 'output' => 'Apache reload skipped because SSL certificate files are missing.'],
];
}

$reload = ['success' => true, 'output' => 'Apache reload skipped'];
if ($reload_apache) {
$reload = gsp_apache_reload();
}

gsp_update_log('Apache path fix changed ' . count($changed) . ' file(s).');
if (!empty($planned)) {
$samples = array_slice($planned, 0, 20);
foreach ($samples as $entry) {
gsp_update_log('Apache replacement planned/applied: ' . $entry['vhost'] . ' ' . $entry['directive'] . ' ' . $entry['from'] . ' => ' . $entry['to']);
}
}
return [
'success' => true,
'changed_files' => $changed,
'backup_dir' => $backup['backup_dir'],
'configtest' => $test,
'reload' => $reload,
'planned_replacements' => $planned,
];
}

function gsp_get_patch_overview()
{
global $db;
if (!function_exists('gsp_patch_load_definitions') || !function_exists('gsp_patch_get_applied_map') || !function_exists('gsp_patch_state_fallback_file')) {
return ['available' => [], 'applied' => [], 'pending' => []];
}
$defs = gsp_patch_load_definitions(GSP_PATCH_DIR);
$applied = gsp_patch_get_applied_map($db, gsp_patch_state_fallback_file());
$pending = [];
foreach ($defs as $def) {
$id = (string)$def['id'];
if (!isset($applied[$id]) || $applied[$id]['status'] !== 'applied') {
$pending[] = $id;
}
}
return [
'available' => $defs,
'applied' => $applied,
'pending' => $pending,
];
}

function gsp_rmdir_recursive($dir)
{
if (!is_dir($dir)) {
return;
}
$iter = new RecursiveIteratorIterator(
new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
RecursiveIteratorIterator::CHILD_FIRST
);
foreach ($iter as $item) {
if ($item->isDir()) {
@rmdir($item->getPathname());
} else {
@unlink($item->getPathname());
}
}
@rmdir($dir);
}

function gsp_render_restart_form($action, $csrf_token, $nonce, $release_version = '')
{
echo "<form id='gsp_update_restart_form' method='POST'>";
echo "<input type='hidden' name='gsp_update_action' value='" . htmlspecialchars($action) . "'>";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>";
echo "<input type='hidden' name='gsp_restart_nonce' value='" . htmlspecialchars($nonce) . "'>";
if ($release_version !== '') {
echo "<input type='hidden' name='gsp_release_version' value='" . htmlspecialchars($release_version) . "'>";
}
echo "</form>";
echo "<script>setTimeout(function(){document.getElementById('gsp_update_restart_form').submit();}, 800);</script>";
}

function gsp_panel_update_section()
{
global $db, $settings;
if ($_SESSION['users_group'] !== 'admin') {
return;
}

$repo_owner = !empty($settings['gsp_repo_owner']) ? $settings['gsp_repo_owner'] : 'GameServerPanel';
$repo_name = !empty($settings['gsp_repo_name']) ? $settings['gsp_repo_name'] : 'GSP';
$stable_branch = !empty($settings['gsp_stable_branch']) ? $settings['gsp_stable_branch'] : 'Panel-stable';
$unstable_branch = !empty($settings['gsp_unstable_branch']) ? $settings['gsp_unstable_branch'] : 'Panel-unstable';

if (empty($_SESSION['gsp_update_csrf'])) {
$_SESSION['gsp_update_csrf'] = gsp_random_token();
}
$csrf_token = $_SESSION['gsp_update_csrf'];

$apache_scan_result = null;
$preflight_result = null;
$auto_restart_payload = null;

if (isset($_POST['gsp_update_action'])) {
$submitted_csrf = isset($_POST['gsp_update_csrf']) ? $_POST['gsp_update_csrf'] : '';
if (!hash_equals($csrf_token, $submitted_csrf)) {
print_failure('Invalid security token. Please reload and try again.');
} else {
$action = $_POST['gsp_update_action'];
$started_at = date('Y-m-d H:i:s');
$restart_nonce = isset($_POST['gsp_restart_nonce']) ? trim($_POST['gsp_restart_nonce']) : '';
set_time_limit(0);

if ($action === 'preflight') {
$preflight_result = gsp_preflight_check();
if ($preflight_result['success']) {
print_success('Preflight check passed.');
} else {
print_failure('Preflight failed: ' . htmlspecialchars(implode(' | ', $preflight_result['errors'])));
}
} elseif ($action === 'apply_patches') {
$run = gsp_run_required_patches((string)gsp_get_current_version());
if ($run['success']) {
print_success('Required patches applied successfully.');
} else {
print_failure('Patch application failed: ' . htmlspecialchars($run['error']));
}
} elseif ($action === 'fix_apache') {
$apache_fix = gsp_fix_apache_paths(true, true);
if ($apache_fix['success']) {
if (!empty($apache_fix['warning'])) {
print_success(htmlspecialchars($apache_fix['warning']) . ' Updated files: ' . intval(count($apache_fix['changed_files'])) . '.');
echo "<p style='color:#8a6d3b;'><strong>Renew SSL certificate:</strong> <code>certbot --apache -d gameservers.world -d www.gameservers.world</code></p>\n";
} else {
print_success('Apache paths fixed successfully. Updated files: ' . intval(count($apache_fix['changed_files'])) . '.');
}
if (!empty($apache_fix['configtest']['output'])) {
echo "<p><strong>apache2ctl configtest output:</strong><br><pre style='white-space:pre-wrap;max-height:220px;overflow:auto;'>" . htmlspecialchars($apache_fix['configtest']['output']) . "</pre></p>\n";
}
} else {
print_failure('Apache path fix failed: ' . htmlspecialchars($apache_fix['error']));
}
} elseif ($action === 'disable_ssl_vhost') {
$vhost = isset($_POST['gsp_ssl_vhost']) ? trim($_POST['gsp_ssl_vhost']) : '';
$disable = gsp_disable_ssl_vhost($vhost, true);
if ($disable['success']) {
print_success(htmlspecialchars(isset($disable['message']) ? $disable['message'] : 'SSL vhost disabled.'));
} else {
print_failure('Disable SSL vhost failed: ' . htmlspecialchars($disable['error']));
}
} elseif ($action === 'backup_only') {
$result = gsp_create_full_backup('backup-only', 'manual', false);
if ($result['success']) {
print_success('Backup created: <code>' . htmlspecialchars($result['backup_dir']) . '</code>');
} else {
print_failure('Backup failed: ' . htmlspecialchars($result['error']));
}
} elseif ($action === 'update_release') {
$version = isset($_POST['gsp_release_version']) ? trim($_POST['gsp_release_version']) : '';
if (!preg_match('/^[a-zA-Z0-9._\-]+$/', $version) || strlen($version) > 80) {
print_failure('Invalid release tag selected.');
} else {
$result = gsp_do_update($repo_owner, $repo_name, $version, 'release', $restart_nonce);
if (!empty($result['restart_required'])) {
print_success('Updater files changed and were updated first. Restarting update with refreshed updater...');
$auto_restart_payload = ['action' => 'update_release', 'nonce' => $result['restart_nonce'], 'version' => $version];
} elseif ($result['success']) {
print_success('Panel updated to release <strong>' . htmlspecialchars($version) . '</strong>. '
. intval($result['files_copied']) . ' file(s) copied (Panel: ' . intval($result['panel_files_copied']) . ', Website: ' . intval($result['website_files_copied']) . ').');
} else {
print_failure('Update failed: ' . htmlspecialchars($result['error']));
}
}
} elseif ($action === 'update_stable') {
$result = gsp_do_update($repo_owner, $repo_name, $stable_branch, 'development', $restart_nonce);
if (!empty($result['restart_required'])) {
print_success('Updater files changed and were updated first. Restarting stable update...');
$auto_restart_payload = ['action' => 'update_stable', 'nonce' => $result['restart_nonce']];
} elseif ($result['success']) {
print_success('Panel updated to GitHub Stable (<strong>' . htmlspecialchars($stable_branch) . '</strong>). '
. intval($result['files_copied']) . ' file(s) copied (Panel: ' . intval($result['panel_files_copied']) . ', Website: ' . intval($result['website_files_copied']) . ').');
} else {
print_failure('Update failed: ' . htmlspecialchars($result['error']));
}
} elseif ($action === 'update_unstable') {
$result = gsp_do_update($repo_owner, $repo_name, $unstable_branch, 'cutting-edge', $restart_nonce);
if (!empty($result['restart_required'])) {
print_success('Updater files changed and were updated first. Restarting unstable update...');
$auto_restart_payload = ['action' => 'update_unstable', 'nonce' => $result['restart_nonce']];
} elseif ($result['success']) {
print_success('Panel updated to GitHub Unstable (<strong>' . htmlspecialchars($unstable_branch) . '</strong>). '
. intval($result['files_copied']) . ' file(s) copied (Panel: ' . intval($result['panel_files_copied']) . ', Website: ' . intval($result['website_files_copied']) . ').');
} else {
print_failure('Update failed: ' . htmlspecialchars($result['error']));
}
} elseif ($action === 'revert') {
$backup_ts = isset($_POST['gsp_revert_backup']) ? trim($_POST['gsp_revert_backup']) : '';
$restore_apache = !empty($_POST['gsp_restore_apache']);
if (!preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', $backup_ts)) {
print_failure('Invalid backup timestamp selected.');
} else {
$result = gsp_do_revert($backup_ts, $restore_apache);
if ($result['success']) {
print_success('Reverted to backup <strong>' . htmlspecialchars($backup_ts) . '</strong>.');
} else {
print_failure('Revert failed: ' . htmlspecialchars($result['error']));
}
}
}

$finished_at = date('Y-m-d H:i:s');
gsp_log_update_to_db('panel-update', null, 'info', 'Admin action: ' . $action, null, null, null, $started_at, $finished_at);
}
$_SESSION['gsp_update_csrf'] = gsp_random_token();
$csrf_token = $_SESSION['gsp_update_csrf'];
}

$current_version = gsp_get_current_version();
$current_branch = gsp_get_current_branch();
$git_commit = gsp_get_git_commit();
$last_layout = isset($_SESSION['gsp_last_update_layout']) && is_array($_SESSION['gsp_last_update_layout'])
? $_SESSION['gsp_last_update_layout']
: null;
$vinfo = gsp_read_version_json();
$releases = gsp_fetch_github_releases($repo_owner, $repo_name);
$latest_release = (is_array($releases) && !empty($releases)) ? htmlspecialchars($releases[0]['tag_name']) : 'N/A';
$backups = gsp_get_available_backups();
$patch_overview = gsp_get_patch_overview();
if ($apache_scan_result === null) {
$apache_scan_result = gsp_scan_apache_configs();
}
if ($preflight_result === null) {
$preflight_result = gsp_preflight_check();
}

echo "<h2>Panel Updates</h2>\n";
echo "<table class='administration-table'><tr><td>\n";
echo "<h3>Detected Layout</h3>\n";
echo "<table class='center'>\n";
echo "<tr><td><strong>Expected Root:</strong></td><td><code>" . htmlspecialchars(GSP_EXPECTED_ROOT) . "</code></td></tr>\n";
echo "<tr><td><strong>Detected GSP Root:</strong></td><td><code>" . htmlspecialchars(GSP_ROOT_DIR) . "</code></td></tr>\n";
echo "<tr><td><strong>Panel Path:</strong></td><td><code>" . htmlspecialchars(GSP_PANEL_DIR) . "</code></td></tr>\n";
echo "<tr><td><strong>Website Path:</strong></td><td><code>" . htmlspecialchars(GSP_WEBSITE_DIR) . "</code></td></tr>\n";
echo "<tr><td><strong>Configured Stable Branch:</strong></td><td><code>" . htmlspecialchars($stable_branch) . "</code></td></tr>\n";
echo "<tr><td><strong>Configured Unstable Branch:</strong></td><td><code>" . htmlspecialchars($unstable_branch) . "</code></td></tr>\n";
echo "<tr><td><strong>Update Trace Log:</strong></td><td><code>" . htmlspecialchars(GSP_UPDATE_LOG) . "</code></td></tr>\n";
if ($last_layout) {
echo "<tr><td><strong>Last Temp Checkout Path:</strong></td><td><code>" . htmlspecialchars(isset($last_layout['temporary_git_checkout_path']) ? $last_layout['temporary_git_checkout_path'] : '') . "</code></td></tr>\n";
echo "<tr><td><strong>Last Source Repo Root:</strong></td><td><code>" . htmlspecialchars(isset($last_layout['source_repo_root']) ? $last_layout['source_repo_root'] : '') . "</code></td></tr>\n";
echo "<tr><td><strong>Last Source Panel Path:</strong></td><td><code>" . htmlspecialchars(isset($last_layout['source_panel_path']) ? $last_layout['source_panel_path'] : '') . "</code></td></tr>\n";
}
echo "</table><br>\n";

echo "<h3>Current Installation</h3>\n";
echo "<table class='center'>\n";
if ($vinfo) {
echo "<tr><td><strong>Installed Type:</strong></td><td>" . htmlspecialchars($vinfo['installed_type']) . "</td></tr>\n";
echo "<tr><td><strong>Installed Source:</strong></td><td>" . htmlspecialchars($vinfo['installed_source']) . "</td></tr>\n";
echo "<tr><td><strong>Installed Version:</strong></td><td>" . htmlspecialchars($vinfo['installed_version']) . "</td></tr>\n";
echo "<tr><td><strong>Installed At:</strong></td><td>" . htmlspecialchars($vinfo['installed_at']) . "</td></tr>\n";
} else {
echo "<tr><td><strong>Installed Version:</strong></td><td>" . htmlspecialchars($current_version) . "</td></tr>\n";
echo "<tr><td><strong>Current Branch:</strong></td><td>" . htmlspecialchars($current_branch) . "</td></tr>\n";
}
if ($git_commit) {
echo "<tr><td><strong>Git Commit:</strong></td><td>" . htmlspecialchars(substr($git_commit, 0, 12)) . "</td></tr>\n";
}
echo "<tr><td><strong>Latest Release:</strong></td><td>" . $latest_release . "</td></tr>\n";
echo "<tr><td><strong>Repository:</strong></td><td>" . htmlspecialchars($repo_owner . '/' . $repo_name) . "</td></tr>\n";
echo "<tr><td><strong>Backup Directory:</strong></td><td><code>" . htmlspecialchars(GSP_BACKUP_BASE) . "</code></td></tr>\n";
echo "<tr><td><strong>Backups Stored:</strong></td><td>" . intval(count($backups)) . " (retention: 5)</td></tr>\n";
echo "</table><br>\n";

echo "<h3>Updater Preflight & Patches</h3>\n";
echo "<table class='center'>\n";
echo "<tr><td><strong>Preflight Status:</strong></td><td>" . ($preflight_result['success'] ? 'PASS' : 'FAIL') . "</td></tr>\n";
echo "<tr><td><strong>Pending Patches:</strong></td><td>" . intval(count($patch_overview['pending'])) . "</td></tr>\n";
echo "<tr><td><strong>Patch Directory:</strong></td><td><code>" . htmlspecialchars(GSP_PATCH_DIR) . "</code></td></tr>\n";
echo "</table>\n";
if (!empty($preflight_result['warnings'])) {
echo "<p style='color:#8a6d3b;'><strong>Preflight warnings:</strong><br>" . implode('<br>', array_map('htmlspecialchars', $preflight_result['warnings'])) . "</p>\n";
}
if (!empty($preflight_result['errors'])) {
echo "<p style='color:#a94442;'><strong>Preflight errors:</strong><br>" . implode('<br>', array_map('htmlspecialchars', $preflight_result['errors'])) . "</p>\n";
}

echo "<form method='POST' style='display:inline-block;margin-right:8px;'>\n";
echo "<input type='hidden' name='gsp_update_action' value='preflight'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<button type='submit'>Run Preflight Check</button>\n";
echo "</form>\n";
echo "<form method='POST' style='display:inline-block;'>\n";
echo "<input type='hidden' name='gsp_update_action' value='apply_patches'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<button type='submit'>Apply Required Patches</button>\n";
echo "</form>\n";

echo "<br><br><h3>Apache Configuration Status</h3>\n";
echo "<table class='center'>\n";
echo "<tr><td><strong>Config Directory:</strong></td><td><code>" . htmlspecialchars($apache_scan_result['base']) . "</code></td></tr>\n";
echo "<tr><td><strong>Configs Found:</strong></td><td>" . intval(count($apache_scan_result['files'])) . "</td></tr>\n";
echo "<tr><td><strong>Stale Path Hits:</strong></td><td>" . intval(count($apache_scan_result['stale_issues'])) . "</td></tr>\n";
echo "<tr><td><strong>SSL Certificate Issues:</strong></td><td>" . intval(count($apache_scan_result['ssl_issues'])) . "</td></tr>\n";
echo "<tr><td><strong>Recommended Panel Path:</strong></td><td><code>" . htmlspecialchars(GSP_PANEL_DIR) . "</code></td></tr>\n";
echo "<tr><td><strong>Recommended Website Path:</strong></td><td><code>" . htmlspecialchars(GSP_WEBSITE_DIR) . "</code></td></tr>\n";
echo "</table>\n";
if (!empty($apache_scan_result['stale_issues'])) {
echo "<p style='color:#a94442;'><strong>Apache stale path issues:</strong><br>" . implode('<br>', array_map('htmlspecialchars', array_unique($apache_scan_result['stale_issues']))) . "</p>\n";
}
if (!empty($apache_scan_result['planned_replacements'])) {
echo "<p><strong>Planned replacements:</strong></p><table class='center'><tr><th>Vhost</th><th>Directive</th><th>Current</th><th>Replacement</th></tr>";
foreach ((array)$apache_scan_result['planned_replacements'] as $plan_row) {
echo "<tr><td>" . htmlspecialchars($plan_row['vhost']) . "</td><td>" . htmlspecialchars($plan_row['directive']) . "</td><td><code>" . htmlspecialchars($plan_row['from']) . "</code></td><td><code>" . htmlspecialchars($plan_row['to']) . "</code></td></tr>";
}
echo "</table>";
}
if (!empty($apache_scan_result['ssl_issues'])) {
echo "<p style='color:#8a6d3b;'><strong>SSL certificate issues:</strong><br>";
foreach ((array)$apache_scan_result['ssl_issues'] as $ssl_issue) {
echo htmlspecialchars($ssl_issue['vhost'] . ' ' . $ssl_issue['directive'] . ': ' . $ssl_issue['path'] . ' (' . $ssl_issue['reason'] . ')') . "<br>";
}
echo "</p>\n";
echo "<p style='color:#8a6d3b;'><strong>Renew certificate command:</strong> <code>certbot --apache -d gameservers.world -d www.gameservers.world</code></p>";
foreach ((array)$apache_scan_result['ssl_issues'] as $ssl_issue) {
$vhost = basename((string)$ssl_issue['vhost']);
if (strpos($vhost, '-ssl.conf') === false) {
continue;
}
echo "<form method='POST' style='display:inline-block;margin-right:8px;'>";
echo "<input type='hidden' name='gsp_update_action' value='disable_ssl_vhost'>";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>";
echo "<input type='hidden' name='gsp_ssl_vhost' value='" . htmlspecialchars($vhost) . "'>";
echo "<button type='submit' onclick='return confirm(\"Disable broken SSL site " . htmlspecialchars($vhost) . " in sites-enabled?\\n\\nThis keeps path fixes while SSL certs are missing.\");'>Disable Broken SSL Vhost: " . htmlspecialchars($vhost) . "</button>";
echo "</form>";
}
}
echo "<form method='POST'>\n";
echo "<input type='hidden' name='gsp_update_action' value='fix_apache'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<button type='submit' onclick='return confirm(\"Backup Apache configs, run configtest, and apply path fixes?\\n\\nApache will only reload if configtest passes.\");'>Fix Apache Paths</button>\n";
echo "</form>\n";

echo "<br><h3>Backup</h3>\n";
echo "<form method='POST'>\n";
echo "<input type='hidden' name='gsp_update_action' value='backup_only'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<button type='submit'>Create Backup</button>\n";
echo "</form>\n";

echo "<br><h3>Update Panel</h3>\n";
if (is_array($releases) && !empty($releases)) {
echo "<form method='POST' style='margin-bottom:12px;'>\n";
echo "<input type='hidden' name='gsp_update_action' value='update_release'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<select name='gsp_release_version'>\n";
foreach ($releases as $rel) {
$tag = htmlspecialchars($rel['tag_name']);
$name = htmlspecialchars(!empty($rel['name']) ? $rel['name'] : $rel['tag_name']);
echo "<option value='{$tag}'>{$name}</option>\n";
}
echo "</select>\n";
echo " <button type='submit'>Update Panel (Release)</button>\n";
echo "</form>\n";
}
echo "<form method='POST' style='display:inline-block;margin-right:8px;'>\n";
echo "<input type='hidden' name='gsp_update_action' value='update_stable'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<button type='submit'>Update Panel (GitHub Stable)</button>\n";
echo "</form>\n";
echo "<form method='POST' style='display:inline-block;'>\n";
echo "<input type='hidden' name='gsp_update_action' value='update_unstable'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<button type='submit'>Update Panel (GitHub Unstable)</button>\n";
echo "</form>\n";

if (!empty($backups)) {
echo "<br><br><h3>Rollback</h3>\n";
echo "<form method='POST'>\n";
echo "<input type='hidden' name='gsp_update_action' value='revert'>\n";
echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
echo "<select name='gsp_revert_backup'>\n";
foreach ($backups as $bk) {
$ts = htmlspecialchars($bk['ts']);
$label = $ts;
if (!empty($bk['meta']['update_target_type']) && !empty($bk['meta']['update_target_version'])) {
$label .= ' (before ' . htmlspecialchars($bk['meta']['update_target_type']) . ': ' . htmlspecialchars($bk['meta']['update_target_version']) . ')';
}
echo "<option value='" . htmlspecialchars($bk['ts']) . "'>{$label}</option>\n";
}
echo "</select>\n";
echo " <label><input type='checkbox' name='gsp_restore_apache' value='1'> restore Apache configs if backup contains them</label>\n";
echo " <button type='submit' onclick='return confirm(\"Restore Panel, Website, version.json, and database from selected backup?\");'>Rollback</button>\n";
echo "</form>\n";
}

echo "</td></tr></table>\n";

if ($auto_restart_payload) {
$version = isset($auto_restart_payload['version']) ? $auto_restart_payload['version'] : '';
gsp_render_restart_form($auto_restart_payload['action'], $csrf_token, $auto_restart_payload['nonce'], $version);
}
}
?>
