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
 * Provides safe, admin-only panel updates from GitHub with pre-update backup
 * and a revert capability.
 *
 */

// Panel root is two directories up from this file (modules/administration/panel_update.php)
defined('GSP_PANEL_DIR')    || define('GSP_PANEL_DIR',   realpath(dirname(__FILE__) . '/../../'));
defined('GSP_BACKUP_BASE')  || define('GSP_BACKUP_BASE', GSP_PANEL_DIR . '/backups');
defined('GSP_UPDATE_LOG')   || define('GSP_UPDATE_LOG',  GSP_PANEL_DIR . '/logs/panel_updates.log');
defined('GSP_VERSION_FILE') || define('GSP_VERSION_FILE', GSP_PANEL_DIR . '/includes/panel_version.php');
defined('GSP_VERSION_JSON') || define('GSP_VERSION_JSON', GSP_PANEL_DIR . '/version.json');

// ---------------------------------------------------------------------------
// Helper: write a line to the panel update log
// ---------------------------------------------------------------------------
function gsp_update_log($message)
{
	$log_dir = dirname(GSP_UPDATE_LOG);
	if (!is_dir($log_dir)) {
		@mkdir($log_dir, 0750, true);
	}
	$line = '[' . date('Y-m-d H:i:s') . '] ' . $message . PHP_EOL;
	@file_put_contents(GSP_UPDATE_LOG, $line, FILE_APPEND | LOCK_EX);
}

// ---------------------------------------------------------------------------
// Helper: insert a row into gsp_panel_update_log (silently skips on failure)
// ---------------------------------------------------------------------------
function gsp_log_update_to_db($channel, $branch, $status, $message, $backup_path = null, $db_backup_path = null, $file_backup_path = null, $started_at = null, $finished_at = null)
{
	global $db;
	if (!isset($db) || !is_object($db)) {
		return;
	}
	if ($started_at === null) {
		$started_at = date('Y-m-d H:i:s');
	}
	$channel          = $db->real_escape_string((string) $channel);
	$branch           = $branch !== null  ? "'" . $db->real_escape_string((string) $branch)           . "'" : 'NULL';
	$status           = $db->real_escape_string((string) $status);
	$message_esc      = $message !== null ? "'" . $db->real_escape_string((string) $message)           . "'" : 'NULL';
	$backup_path_esc  = $backup_path !== null      ? "'" . $db->real_escape_string((string) $backup_path)      . "'" : 'NULL';
	$db_backup_esc    = $db_backup_path !== null   ? "'" . $db->real_escape_string((string) $db_backup_path)   . "'" : 'NULL';
	$file_backup_esc  = $file_backup_path !== null ? "'" . $db->real_escape_string((string) $file_backup_path) . "'" : 'NULL';
	$started_esc      = "'" . $db->real_escape_string($started_at) . "'";
	$finished_esc     = $finished_at !== null ? "'" . $db->real_escape_string((string) $finished_at) . "'" : 'NULL';
	$db->query(
		"INSERT INTO OGP_DB_PREFIXpanel_update_log"
		. " (channel, branch, status, message, backup_path, db_backup_path, file_backup_path, started_at, finished_at)"
		. " VALUES ('{$channel}', {$branch}, '{$status}', {$message_esc},"
		. "  {$backup_path_esc}, {$db_backup_esc}, {$file_backup_esc}, {$started_esc}, {$finished_esc})"
	);
}

// ---------------------------------------------------------------------------
// Helper: read the installed version / branch from panel_version.php
// ---------------------------------------------------------------------------
function gsp_get_current_version()
{
	if (file_exists(GSP_VERSION_FILE)) {
		$code = file_get_contents(GSP_VERSION_FILE);
		if (preg_match("/define\('GSP_VERSION',\s*'([^']+)'\)/", $code, $m)) {
			return $m[1];
		}
	}
	return 'unknown';
}

function gsp_get_current_branch()
{
	if (file_exists(GSP_VERSION_FILE)) {
		$code = file_get_contents(GSP_VERSION_FILE);
		if (preg_match("/define\('GSP_BRANCH',\s*'([^']+)'\)/", $code, $m)) {
			return $m[1];
		}
	}
	// Fall back to reading .git/HEAD
	$git_head = GSP_PANEL_DIR . '/.git/HEAD';
	if (file_exists($git_head)) {
		$content = trim(file_get_contents($git_head));
		if (preg_match('/^ref: refs\/heads\/(.+)$/', $content, $m)) {
			return $m[1];
		}
	}
	return 'unknown';
}

function gsp_get_git_commit()
{
	$git_head = GSP_PANEL_DIR . '/.git/HEAD';
	if (!file_exists($git_head)) {
		return null;
	}
	$content = trim(file_get_contents($git_head));
	if (preg_match('/^ref: refs\/heads\/(.+)$/', $content, $m)) {
		$branch_file = GSP_PANEL_DIR . '/.git/refs/heads/' . $m[1];
		if (file_exists($branch_file)) {
			return trim(file_get_contents($branch_file));
		}
	} elseif (preg_match('/^[0-9a-f]{40}$/i', $content)) {
		return $content;
	}
	return null;
}

// ---------------------------------------------------------------------------
// Helper: write/update includes/panel_version.php
// ---------------------------------------------------------------------------
function gsp_write_version_file($version, $branch_or_type)
{
	$content  = "<?php\n";
	$content .= "// GSP Panel Version - written by the panel update system. Do not edit manually.\n";
	$content .= "define('GSP_VERSION', " . var_export($version, true) . ");\n";
	$content .= "define('GSP_BRANCH',  " . var_export($branch_or_type, true) . ");\n";
	$content .= "define('GSP_UPDATE_TIME', " . var_export(date('Y-m-d H:i:s'), true) . ");\n";
	@file_put_contents(GSP_VERSION_FILE, $content);
}

// ---------------------------------------------------------------------------
// Helper: read version.json and return its data as an array (or null)
// ---------------------------------------------------------------------------
function gsp_read_version_json()
{
	if (!file_exists(GSP_VERSION_JSON)) {
		return null;
	}
	$data = json_decode(file_get_contents(GSP_VERSION_JSON), true);
	return is_array($data) ? $data : null;
}

// ---------------------------------------------------------------------------
// Helper: write version.json with canonical installed-version metadata
// ---------------------------------------------------------------------------
function gsp_write_version_json($installed_type, $installed_source, $installed_version, $installed_commit = null)
{
	$allowed_types = ['release', 'development', 'cutting-edge'];
	if (!in_array($installed_type, $allowed_types, true)) {
		$installed_type = 'unknown';
	}
	$data = [
		'installed_type'    => $installed_type,
		'installed_source'  => $installed_source,
		'installed_version' => $installed_version,
		'installed_commit'  => $installed_commit,
		'installed_at'      => date('Y-m-d H:i:s'),
	];
	@file_put_contents(GSP_VERSION_JSON, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

// ---------------------------------------------------------------------------
// Helper: generate a cryptographically strong random hex token
// ---------------------------------------------------------------------------
function gsp_random_token($bytes = 16)
{
	try {
		return bin2hex(random_bytes($bytes));
	} catch (\Throwable $e) {
		// Fallback for environments where random_bytes() is unavailable
		return bin2hex(openssl_random_pseudo_bytes($bytes));
	}
}

// ---------------------------------------------------------------------------
// GitHub API: fetch list of releases (newest first)
// ---------------------------------------------------------------------------
function gsp_fetch_github_releases($repo_owner, $repo_name)
{
	$url = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/releases?per_page=30";

	if (function_exists('curl_init')) {
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, 'GSP-Panel-Updater');
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		$data = curl_exec($ch);
		$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		if ($code === 200 && $data) {
			return json_decode($data, true);
		}
	} else {
		$ctx = stream_context_create([
			'http' => [
				'method'  => 'GET',
				'header'  => "User-Agent: GSP-Panel-Updater\r\n",
				'timeout' => 10,
			],
			'ssl' => [
				'verify_peer'      => true,
				'verify_peer_name' => true,
			],
		]);
		$data = @file_get_contents($url, false, $ctx);
		if ($data) {
			return json_decode($data, true);
		}
	}
	return false;
}

// ---------------------------------------------------------------------------
// Helper: read DB credentials from includes/config.inc.php using an
// isolated scope so the caller's variables are not polluted and side effects
// from the config file (e.g. debug.php inclusion) stay contained.
// Returns an array with keys host/port/user/pass/name, or null on failure.
// ---------------------------------------------------------------------------
function load_panel_db_config()
{
	$config_file = GSP_PANEL_DIR . '/includes/config.inc.php';
	if (!is_readable($config_file)) {
		return null;
	}

	// Static closure gives an isolated variable scope; the @ suppresses any
	// non-fatal errors from transitive includes (e.g. debug.php).
	$capture = static function ($__file) {
		$db_host = 'localhost';
		$db_port = '3306';
		$db_user = '';
		$db_pass = '';
		$db_name = '';
		@include $__file;
		return [
			'host' => (string) $db_host,
			'port' => (string) $db_port,
			'user' => (string) $db_user,
			'pass' => (string) $db_pass,
			'name' => (string) $db_name,
		];
	};

	$cfg = $capture($config_file);

	if (empty($cfg['user']) || empty($cfg['name'])) {
		return null;
	}

	return $cfg;
}

// ---------------------------------------------------------------------------
// Backup: dump the MySQL database into $backup_dir/database.sql
// Returns ['success'=>bool, 'error'=>string, 'file'=>string]
// ---------------------------------------------------------------------------
function create_database_backup($backup_dir, $db_config)
{
	// Verify mysqldump is available before attempting anything
	$check_out = [];
	$check_ret = 0;
	exec('command -v mysqldump 2>/dev/null', $check_out, $check_ret);
	if ($check_ret !== 0 || empty($check_out[0])) {
		return [
			'success' => false,
			'error'   => 'mysqldump is not installed or not in PATH. '
			           . 'Install the mysql-client package and ensure it is on the PATH.',
		];
	}

	$sql_file   = $backup_dir . '/database.sql';
	$creds_file = tempnam(sys_get_temp_dir(), 'gsp_db_');
	if ($creds_file === false) {
		return ['success' => false, 'error' => 'Cannot create temporary credentials file.'];
	}

	// Build MySQL option-file content; addcslashes protects backslashes and
	// newlines so values are safe inside the ini-style [client] section.
	// The password never appears in the process list this way.
	$creds = "[client]\n"
	       . "user="     . addcslashes($db_config['user'], "\\\n") . "\n"
	       . "password=" . addcslashes($db_config['pass'], "\\\n") . "\n";
	if (!empty($db_config['host'])) {
		$creds .= "host=" . addcslashes($db_config['host'], "\\\n") . "\n";
	}
	if (!empty($db_config['port']) && $db_config['port'] !== '3306') {
		$creds .= "port=" . addcslashes($db_config['port'], "\\\n") . "\n";
	}
	file_put_contents($creds_file, $creds);
	chmod($creds_file, 0600);

	// Redirect stderr to a separate temp file so it never pollutes the SQL dump
	$err_tmp = tempnam(sys_get_temp_dir(), 'gsp_db_err_');
	if ($err_tmp === false) {
		@unlink($creds_file);
		return ['success' => false, 'error' => 'Cannot create temporary file for mysqldump error capture.'];
	}
	$command = 'mysqldump --defaults-extra-file=' . escapeshellarg($creds_file)
	         . ' --skip-opt --single-transaction --add-drop-table'
	         . ' --create-options --extended-insert --quick --set-charset'
	         . ' '    . escapeshellarg($db_config['name'])
	         . ' > '  . escapeshellarg($sql_file)
	         . ' 2> ' . escapeshellarg($err_tmp);

	$unused = [];
	$ret    = 0;
	exec($command, $unused, $ret);
	@unlink($creds_file);

	// Collect stderr; strip lines mentioning "password" or "passwd" as a
	// defensive measure (mysqldump error output does not normally include the
	// password, but we filter anyway in case of unusual configurations).
	$err_output = '';
	if (file_exists($err_tmp)) {
		$raw = trim(file_get_contents($err_tmp));
		@unlink($err_tmp);
		if ($raw !== '') {
			$err_output = implode("\n", array_filter(
				explode("\n", $raw),
				static function ($line) {
					return stripos($line, 'password') === false
					    && stripos($line, 'passwd') === false;
				}
			));
		}
	}

	if ($ret !== 0) {
		@unlink($sql_file);
		$msg = 'mysqldump failed (exit code ' . $ret . ').';
		if ($err_output !== '') {
			$msg .= ' Error: ' . $err_output;
		}
		return ['success' => false, 'error' => $msg];
	}

	if (!file_exists($sql_file) || filesize($sql_file) < 100) {
		@unlink($sql_file);
		return ['success' => false, 'error' => 'Database dump file is missing or empty after mysqldump.'];
	}

	return ['success' => true, 'file' => $sql_file];
}

// ---------------------------------------------------------------------------
// Backup: tar-gzip the panel root into $backup_dir/panel-files.tar.gz
// Returns ['success'=>bool, 'error'=>string, 'file'=>string]
// ---------------------------------------------------------------------------
function create_panel_files_archive($backup_dir, $panel_root)
{
	// Verify tar is available
	$check_out = [];
	$check_ret = 0;
	exec('command -v tar 2>/dev/null', $check_out, $check_ret);
	if ($check_ret !== 0 || empty($check_out[0])) {
		return ['success' => false, 'error' => 'tar is not installed or not in PATH.'];
	}

	$tar_file = $backup_dir . '/panel-files.tar.gz';

	// Top-level directories are anchored with ./ so they only match at the
	// archive root; wildcard patterns match at any depth.
	$exclude_dirs  = ['./backups', './.git', './logs', './cache', './tmp', './node_modules', './vendor'];
	$exclude_globs = ['*.log', '*.sql'];

	$exclude_args = '';
	foreach ($exclude_dirs as $dir) {
		$exclude_args .= ' --exclude=' . escapeshellarg($dir);
	}
	foreach ($exclude_globs as $glob) {
		$exclude_args .= ' --exclude=' . escapeshellarg($glob);
	}

	// -C panel_root . preserves relative paths (./home.php, ./modules/…)
	$command = 'tar -czf ' . escapeshellarg($tar_file)
	         . $exclude_args
	         . ' -C ' . escapeshellarg($panel_root)
	         . ' . 2>&1';

	$out = [];
	$ret = 0;
	exec($command, $out, $ret);

	if ($ret !== 0) {
		@unlink($tar_file);
		return [
			'success' => false,
			'error'   => 'tar failed (exit code ' . $ret . '). ' . implode(' | ', $out),
		];
	}

	if (!file_exists($tar_file) || filesize($tar_file) < 100) {
		@unlink($tar_file);
		return ['success' => false, 'error' => 'Panel archive file is missing or empty after tar.'];
	}

	return ['success' => true, 'file' => $tar_file];
}

// ---------------------------------------------------------------------------
// Backup: write backup.json metadata into $backup_dir
// Returns true on success, false on failure.
// ---------------------------------------------------------------------------
function write_backup_metadata($backup_dir, $metadata)
{
	return file_put_contents(
		$backup_dir . '/backup.json',
		json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
	) !== false;
}

// ---------------------------------------------------------------------------
// Backup: create a full timestamped backup (DB + files + metadata + log)
// ---------------------------------------------------------------------------
function gsp_create_full_backup($update_target_type, $update_target_version)
{
	$ts         = date('Y-m-d_H-i-s');
	$backup_dir = GSP_BACKUP_BASE . '/' . $ts;

	// Helper that writes a timestamped line to backup.log inside $backup_dir
	$append_log = static function ($msg) use ($backup_dir) {
		$line = '[' . date('Y-m-d H:i:s') . '] ' . $msg . PHP_EOL;
		@file_put_contents($backup_dir . '/backup.log', $line, FILE_APPEND | LOCK_EX);
	};

	// 1. Ensure backup base directory exists (0755 so the web server can write)
	if (!is_dir(GSP_BACKUP_BASE)) {
		$mkdir_ok = @mkdir(GSP_BACKUP_BASE, 0755, true);
		if (!$mkdir_ok) {
			$last_err = error_get_last();
			$detail   = ($last_err && !empty($last_err['message'])) ? ' (' . $last_err['message'] . ')' : '';
			return [
				'success' => false,
				'error'   => 'Cannot create backup base directory: ' . GSP_BACKUP_BASE . $detail
				           . '. Ensure the web server user has write access to: '
				           . dirname(GSP_BACKUP_BASE),
			];
		}
	}

	// 2. Create timestamped backup directory
	if (!@mkdir($backup_dir, 0755, true)) {
		$last_err = error_get_last();
		$detail   = ($last_err && !empty($last_err['message'])) ? ' (' . $last_err['message'] . ')' : '';
		return [
			'success' => false,
			'error'   => 'Cannot create backup directory: ' . $backup_dir . $detail
			           . '. Ensure the web server user has write access to: ' . GSP_BACKUP_BASE,
		];
	}

	$append_log("Backup started. Target: {$update_target_type} / {$update_target_version}");
	$append_log("Panel root: " . GSP_PANEL_DIR);
	$append_log("Backup directory: {$backup_dir}");

	// 3. Load DB configuration from includes/config.inc.php
	$db_config = load_panel_db_config();
	if ($db_config === null) {
		$append_log("ERROR: Cannot load database configuration from includes/config.inc.php");
		return [
			'success' => false,
			'error'   => 'Cannot load database configuration. '
			           . 'Ensure includes/config.inc.php exists and contains valid DB credentials.',
		];
	}
	$append_log("DB config loaded. Host: {$db_config['host']}, Database: {$db_config['name']}");

	// 4. Database backup — stops the update if it fails
	$append_log("Starting database backup (mysqldump)...");
	$db_result = create_database_backup($backup_dir, $db_config);
	if (!$db_result['success']) {
		$append_log("ERROR: Database backup failed: " . $db_result['error']);
		return ['success' => false, 'error' => $db_result['error']];
	}
	$append_log("Database backup complete: database.sql (" . filesize($db_result['file']) . " bytes)");

	// 5. Panel files archive — stops the update if it fails
	$append_log("Starting panel files archive (tar gzip)...");
	$tar_result = create_panel_files_archive($backup_dir, GSP_PANEL_DIR);
	if (!$tar_result['success']) {
		$append_log("ERROR: Panel files archive failed: " . $tar_result['error']);
		return ['success' => false, 'error' => $tar_result['error']];
	}
	$append_log("Panel files archive complete: panel-files.tar.gz (" . filesize($tar_result['file']) . " bytes)");

	// 6. Write backup.json metadata
	$vinfo    = gsp_read_version_json();
	$metadata = [
		'backup_timestamp'      => $ts,
		'panel_root'            => GSP_PANEL_DIR,
		'database_host'         => $db_config['host'],
		'database_name'         => $db_config['name'],
		'installed_version'     => $vinfo
			? ($vinfo['installed_version'] ?? gsp_get_current_version())
			: gsp_get_current_version(),
		'git_branch'            => gsp_get_current_branch(),
		'git_commit'            => gsp_get_git_commit(),
		'update_target_type'    => $update_target_type,
		'update_target_version' => $update_target_version,
		'backup_status'         => 'complete',
	];
	write_backup_metadata($backup_dir, $metadata);
	$append_log("Backup metadata written (backup.json).");

	// 7. Final validation — both required files must be present and non-empty
	$sql_file = $backup_dir . '/database.sql';
	$tar_file = $backup_dir . '/panel-files.tar.gz';

	if (!file_exists($sql_file) || filesize($sql_file) < 100) {
		$append_log("ERROR: Validation failed — database.sql is missing or empty.");
		return ['success' => false, 'error' => 'Backup validation failed: database.sql is missing or empty.'];
	}
	if (!file_exists($tar_file) || filesize($tar_file) < 100) {
		$append_log("ERROR: Validation failed — panel-files.tar.gz is missing or empty.");
		return ['success' => false, 'error' => 'Backup validation failed: panel-files.tar.gz is missing or empty.'];
	}

	$append_log("Backup validated and complete.");

	return [
		'success'    => true,
		'backup_dir' => $backup_dir,
		'backup_ts'  => $ts,
	];
}

// ---------------------------------------------------------------------------
// Update: download the GitHub archive ZIP for a given ref (tag or branch)
// ---------------------------------------------------------------------------
function gsp_download_zip($repo_owner, $repo_name, $ref, $temp_dir)
{
	// GitHub returns a redirect from /zipball/ to the actual download URL
	$url      = "https://api.github.com/repos/{$repo_owner}/{$repo_name}/zipball/{$ref}";
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
		file_put_contents($zip_file, $data);
	} else {
		$ctx  = stream_context_create([
			'http' => [
				'method'          => 'GET',
				'header'          => "User-Agent: GSP-Panel-Updater\r\n",
				'timeout'         => 180,
				'follow_location' => 1,
			],
			'ssl' => [
				'verify_peer'      => true,
				'verify_peer_name' => true,
			],
		]);
		$data = @file_get_contents($url, false, $ctx);
		if (!$data) {
			return false;
		}
		file_put_contents($zip_file, $data);
	}

	if (!file_exists($zip_file) || filesize($zip_file) < 1000) {
		return false;
	}
	return $zip_file;
}

// ---------------------------------------------------------------------------
// Update: apply the downloaded zip to the panel directory
// ---------------------------------------------------------------------------
function gsp_apply_update($zip_file)
{
	$panel_dir = GSP_PANEL_DIR;

	// Files to never overwrite when applying an update
	$preserve = [
		'includes/config.inc.php',
		'modules/gamemanager/rsync_sites_local.list',
		'install.php',
	];

	// Merge with the DB update-blacklist (strip leading slash from stored paths)
	global $db;
	$blacklisted = $db->resultQuery('SELECT file_path FROM `OGP_DB_PREFIXupdate_blacklist`;');
	if ($blacklisted !== false) {
		foreach ((array)$blacklisted as $bf) {
			$preserve[] = ltrim($bf['file_path'], '/');
		}
	}

	// Extract ZIP to a temporary directory
	$temp_dir = sys_get_temp_dir() . '/gsp_upd_' . time();
	if (!@mkdir($temp_dir, 0750)) {
		return ['success' => false, 'error' => 'Cannot create temporary extraction directory.'];
	}

	require_once($panel_dir . '/modules/update/unzip.php');
	$result = extractZip($zip_file, $temp_dir);
	if (!is_array($result)) {
		gsp_rmdir_recursive($temp_dir);
		return ['success' => false, 'error' => 'ZIP extraction failed: ' . $result];
	}

	// GitHub archives place all files under a single subdirectory (e.g. "Owner-Repo-sha/")
	// Detect that prefix directory
	$src_dir  = $temp_dir;
	$subdirs  = glob($temp_dir . '/*', GLOB_ONLYDIR);
	if ($subdirs && count($subdirs) === 1) {
		$src_dir = $subdirs[0];
	}

	// Copy files from the extracted source into the panel directory
	$copied = 0;
	$iter   = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($src_dir, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ($iter as $item) {
		$rel = str_replace('\\', '/', substr($item->getPathname(), strlen($src_dir) + 1));

		// Skip preserved/blacklisted files
		if (in_array($rel, $preserve)) {
			continue;
		}

		$dst = $panel_dir . '/' . $rel;

		if ($item->isDir()) {
			if (!is_dir($dst)) {
				@mkdir($dst, 0755, true);
			}
		} else {
			$dst_parent = dirname($dst);
			if (!is_dir($dst_parent)) {
				@mkdir($dst_parent, 0755, true);
			}
			if (@copy($item->getPathname(), $dst)) {
				$copied++;
			}
		}
	}

	gsp_rmdir_recursive($temp_dir);
	return ['success' => true, 'files_copied' => $copied];
}

// ---------------------------------------------------------------------------
// Helper: recursively remove a directory
// ---------------------------------------------------------------------------
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

// ---------------------------------------------------------------------------
// Post-update helpers
// ---------------------------------------------------------------------------
function gsp_fix_permissions($panel_dir)
{
	// Restore sane defaults in a single find traversal: files 644, directories 755
	@system(
		'find ' . escapeshellarg($panel_dir)
		. ' -maxdepth 10 \( -name "*.php" -exec chmod 644 {} + \)'
		. ' -o \( -type d -exec chmod 755 {} + \)'
		. ' 2>/dev/null'
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

// ---------------------------------------------------------------------------
// List available backup timestamps under GSP_BACKUP_BASE
// ---------------------------------------------------------------------------
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
		if (!is_dir(GSP_BACKUP_BASE . '/' . $entry)) {
			continue;
		}
		if (!preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', $entry)) {
			continue;
		}
		$meta_file = GSP_BACKUP_BASE . '/' . $entry . '/backup.json';
		$meta      = [];
		if (file_exists($meta_file)) {
			$meta = json_decode(file_get_contents($meta_file), true) ?: [];
		}
		$backups[] = ['ts' => $entry, 'meta' => $meta];
	}
	// Newest first
	usort($backups, function ($a, $b) {
		return strcmp($b['ts'], $a['ts']);
	});
	return $backups;
}

// ---------------------------------------------------------------------------
// Update: attempt a git-based update (fetch + checkout + reset --hard)
// Returns an array on success or null if git is unavailable / fails.
// ---------------------------------------------------------------------------
function gsp_try_git_update($branch)
{
	if (!function_exists('exec') || !is_dir(GSP_PANEL_DIR . '/.git')) {
		return null;
	}

	$panel_arg  = escapeshellarg(GSP_PANEL_DIR);
	$branch_arg = escapeshellarg($branch);
	$origin_ref = escapeshellarg('origin/' . $branch);

	$out = [];
	$ret = 0;

	exec("git -C {$panel_arg} fetch origin {$branch_arg} --tags 2>&1", $out, $ret);
	if ($ret !== 0) {
		gsp_update_log("Git fetch for branch {$branch} failed (exit {$ret}): " . implode(' | ', $out));
		return null;
	}

	exec("git -C {$panel_arg} checkout {$branch_arg} 2>&1", $out, $ret);
	exec("git -C {$panel_arg} reset --hard {$origin_ref} 2>&1", $out, $ret);
	if ($ret !== 0) {
		gsp_update_log("Git checkout/reset for branch {$branch} failed (exit {$ret}): " . implode(' | ', $out));
		return null;
	}

	$commit_out = [];
	$ret2       = 0;
	exec("git -C {$panel_arg} rev-parse HEAD 2>&1", $commit_out, $ret2);
	$commit = ($ret2 === 0 && !empty($commit_out[0]) && preg_match('/^[0-9a-f]{40,64}$/i', trim($commit_out[0])))
		? trim($commit_out[0])
		: null;

	return [
		'success' => true,
		'method'  => 'git',
		'commit'  => $commit,
		'output'  => implode("\n", $out),
	];
}

// ---------------------------------------------------------------------------
// Orchestrate a full update
// ---------------------------------------------------------------------------
function gsp_do_update($repo_owner, $repo_name, $ref, $update_type)
{
	global $db;
	$panel_dir = GSP_PANEL_DIR;

	// Step 1 — backup
	$backup = gsp_create_full_backup($update_type, $ref);
	if (!$backup['success']) {
		return $backup; // contains 'error'
	}
	gsp_update_log("Backup created at {$backup['backup_ts']} before {$update_type} update to {$ref}");

	// Step 2 — try git for branch updates; fall back to ZIP download
	$commit_after = null;
	$files_copied = 0;
	$used_git     = false;

	if ($update_type !== 'release') {
		$git_result = gsp_try_git_update($ref);
		if ($git_result && $git_result['success']) {
			$commit_after = $git_result['commit'];
			$used_git     = true;
			gsp_update_log("Updated via git to {$ref}: " . $git_result['output']);
		} else {
			gsp_update_log("Git update not available or failed for {$ref}; falling back to ZIP download");
		}
	}

	if (!$used_git) {
		$temp_dir = sys_get_temp_dir() . '/gsp_dl_' . time();
		@mkdir($temp_dir, 0750);
		$zip_file = gsp_download_zip($repo_owner, $repo_name, $ref, $temp_dir);
		if (!$zip_file) {
			@rmdir($temp_dir);
			return [
				'success' => false,
				'error'   => 'Failed to download update ZIP from GitHub. Check network connectivity.',
			];
		}
		gsp_update_log("Downloaded update ZIP for ref={$ref}");

		$apply = gsp_apply_update($zip_file);
		@unlink($zip_file);
		@rmdir($temp_dir);
		if (!$apply['success']) {
			return $apply;
		}
		$files_copied = $apply['files_copied'];
		$commit_after = gsp_get_git_commit();
		gsp_update_log("Applied update via ZIP: {$apply['files_copied']} files written");
	}

	// Step 3 — housekeeping
	gsp_fix_permissions($panel_dir);
	gsp_clear_panel_cache($panel_dir);
	gsp_write_version_file($ref, $update_type);

	// Write version.json with canonical type/source/version data
	$vsource  = ($update_type === 'release') ? 'GitHub Releases' : $ref;
	$vversion = ($update_type === 'release') ? $ref : ($commit_after ?? $ref);
	gsp_write_version_json($update_type, $vsource, $vversion, $commit_after);

	$db->setSettings(['ogp_version' => $ref, 'version_type' => $update_type]);

	// Step 4 — post-update module handling (mirrors updating.php behaviour)
	if (file_exists($panel_dir . '/modules/modulemanager/module_handling.php')) {
		require_once($panel_dir . '/modules/modulemanager/module_handling.php');
	}
	if (function_exists('updateAllPanelModules')) {
		updateAllPanelModules();
	}
	if (function_exists('runPostUpdateOperations')) {
		runPostUpdateOperations();
	}

	gsp_update_log("Update to {$ref} (type={$update_type}) complete");
	return ['success' => true, 'files_copied' => $files_copied, 'backup_dir' => $backup['backup_dir']];
}

// ---------------------------------------------------------------------------
// Orchestrate a revert to a previous backup
// ---------------------------------------------------------------------------
function gsp_do_revert($backup_ts)
{
	global $db;
	$panel_dir  = GSP_PANEL_DIR;
	$backup_dir = GSP_BACKUP_BASE . '/' . $backup_ts;

	if (!is_dir($backup_dir)) {
		return ['success' => false, 'error' => 'Backup directory not found: ' . htmlspecialchars($backup_ts)];
	}

	// Detect backup format: new (panel-files.tar.gz) or legacy (files/ directory)
	$tar_archive      = $backup_dir . '/panel-files.tar.gz';
	$legacy_files_dir = $backup_dir . '/files';
	$use_tar          = file_exists($tar_archive);

	if (!$use_tar && !is_dir($legacy_files_dir)) {
		return ['success' => false, 'error' => 'Backup files not found (expected panel-files.tar.gz or files/ directory).'];
	}

	// Detect SQL file: new (database.sql) or legacy glob *.sql
	$sql_file = $backup_dir . '/database.sql';
	if (!file_exists($sql_file)) {
		$sql_candidates = glob($backup_dir . '/*.sql') ?: [];
		$sql_file       = !empty($sql_candidates) ? $sql_candidates[0] : null;
	}
	if (!$sql_file || !file_exists($sql_file)) {
		return ['success' => false, 'error' => 'No SQL dump found in backup.'];
	}

	// Enable maintenance mode for the duration of the revert
	$had_maintenance = isset($db->getSettings()['maintenance_mode'])
		&& $db->getSettings()['maintenance_mode'] == '1';
	if (!$had_maintenance) {
		$db->setSettings([
			'maintenance_mode'    => '1',
			'maintenance_title'   => 'Reverting...',
			'maintenance_message' => 'The panel is being reverted to a previous version. Please wait.',
		]);
	}

	// Restore files
	$files_restored = 0;
	if ($use_tar) {
		// New format: extract tar archive back to the panel root
		$out = [];
		$ret = 0;
		exec('tar -xzf ' . escapeshellarg($tar_archive) . ' -C ' . escapeshellarg($panel_dir) . ' 2>&1', $out, $ret);
		if ($ret === 0) {
			// tar restores files in place; it does not provide a count,
			// so use 0 here — callers should not rely on an exact file count for the tar path.
			$files_restored = 0;
			gsp_update_log("Revert: extracted panel-files.tar.gz to {$panel_dir}");
		} else {
			gsp_update_log("Revert warning: tar extraction exited with code {$ret}: " . implode(' | ', $out));
		}
	} else {
		// Legacy format: recursive copy from files/ directory
		$iter = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator($legacy_files_dir, RecursiveDirectoryIterator::SKIP_DOTS),
			RecursiveIteratorIterator::SELF_FIRST
		);
		foreach ($iter as $item) {
			$rel = substr($item->getPathname(), strlen($legacy_files_dir));
			$dst = $panel_dir . $rel;
			if ($item->isDir()) {
				if (!is_dir($dst)) {
					@mkdir($dst, 0755, true);
				}
			} else {
				$dst_parent = dirname($dst);
				if (!is_dir($dst_parent)) {
					@mkdir($dst_parent, 0755, true);
				}
				if (@copy($item->getPathname(), $dst)) {
					$files_restored++;
				}
			}
		}
		gsp_update_log("Revert: restored {$files_restored} files from legacy backup {$backup_ts}");
	}

	// Restore database using credentials from config
	$db_config = load_panel_db_config();
	if ($db_config !== null) {
		$creds_file = tempnam(sys_get_temp_dir(), 'gsp_db_');
		if ($creds_file !== false) {
			$creds = "[client]\n"
			       . "user="     . addcslashes($db_config['user'], "\\\n") . "\n"
			       . "password=" . addcslashes($db_config['pass'], "\\\n") . "\n";
			if (!empty($db_config['host'])) {
				$creds .= "host=" . addcslashes($db_config['host'], "\\\n") . "\n";
			}
			if (!empty($db_config['port']) && $db_config['port'] !== '3306') {
				$creds .= "port=" . addcslashes($db_config['port'], "\\\n") . "\n";
			}
			file_put_contents($creds_file, $creds);
			chmod($creds_file, 0600);
			$cmd = 'mysql --defaults-extra-file=' . escapeshellarg($creds_file)
			     . ' '  . escapeshellarg($db_config['name'])
			     . ' < ' . escapeshellarg($sql_file)
			     . ' 2>&1';
			$unused_out = [];
			$ret        = 0;
			exec($cmd, $unused_out, $ret);
			@unlink($creds_file);
			if ($ret !== 0) {
				gsp_update_log("Revert warning: database restore exited with code {$ret}");
			}
		}
	} else {
		gsp_update_log("Revert warning: could not load DB config; database was not restored.");
	}

	// Housekeeping
	gsp_fix_permissions($panel_dir);
	gsp_clear_panel_cache($panel_dir);

	// Turn off maintenance mode (unless it was already on before we started)
	if (!$had_maintenance) {
		$db->setSettings(['maintenance_mode' => '0']);
	}

	gsp_update_log("Revert to backup {$backup_ts} complete");
	return ['success' => true, 'files_restored' => $files_restored];
}

// ---------------------------------------------------------------------------
// Main entry point — render the "Panel Updates" section
// ---------------------------------------------------------------------------
function gsp_panel_update_section()
{
	global $db, $settings;

	// Guard: admins only
	if ($_SESSION['users_group'] !== 'admin') {
		return;
	}

	// GitHub repository settings (with GSP defaults)
	$repo_owner     = !empty($settings['gsp_repo_owner'])     ? $settings['gsp_repo_owner']     : 'GameServerPanel';
	$repo_name      = !empty($settings['gsp_repo_name'])      ? $settings['gsp_repo_name']      : 'GSP';
	$stable_branch  = !empty($settings['gsp_stable_branch'])  ? $settings['gsp_stable_branch']  : 'Panel-stable';
	$unstable_branch= !empty($settings['gsp_unstable_branch'])? $settings['gsp_unstable_branch']: 'Panel-unstable';

	// Per-session CSRF token
	if (empty($_SESSION['gsp_update_csrf'])) {
		$_SESSION['gsp_update_csrf'] = gsp_random_token();
	}
	$csrf_token = $_SESSION['gsp_update_csrf'];

	// ---- Handle POST actions ------------------------------------------------
	if (isset($_POST['gsp_update_action'])) {
		$submitted_csrf = isset($_POST['gsp_update_csrf']) ? $_POST['gsp_update_csrf'] : '';
		if (!hash_equals($csrf_token, $submitted_csrf)) {
			print_failure('Invalid security token. Please reload the page and try again.');
		} else {
			$action = $_POST['gsp_update_action'];
			set_time_limit(0);

			$user_label = htmlspecialchars($_SESSION['users_login'])
				. ' (IP: ' . htmlspecialchars($_SERVER['REMOTE_ADDR']) . ')';

			if ($action === 'backup_only') {
				$started_at = date('Y-m-d H:i:s');
				$result = gsp_create_full_backup('backup-only', 'manual');
				$finished_at = date('Y-m-d H:i:s');
				if ($result['success']) {
					$bk_dir = htmlspecialchars($result['backup_dir']);
					print_success('Backup created successfully at <code>' . $bk_dir . '</code>.');
					gsp_update_log("Admin {$user_label} created manual backup at {$result['backup_dir']}");
					gsp_log_update_to_db(
						'backup-only', null, 'success',
						'Manual backup by ' . $_SESSION['users_login'],
						$result['backup_dir'],
						$result['backup_dir'] . '/database.sql',
						$result['backup_dir'] . '/panel-files.tar.gz',
						$started_at, $finished_at
					);
				} else {
					print_failure('Backup failed: ' . htmlspecialchars($result['error']));
					gsp_update_log("Admin {$user_label} manual backup FAILED: {$result['error']}");
					gsp_log_update_to_db(
						'backup-only', null, 'failed',
						'Manual backup failed: ' . $result['error'],
						null, null, null, $started_at, $finished_at
					);
				}

			} elseif ($action === 'update_release') {
				$version = isset($_POST['gsp_release_version']) ? trim($_POST['gsp_release_version']) : '';
				if (!preg_match('/^[a-zA-Z0-9._\-]+$/', $version) || strlen($version) > 80) {
					print_failure('Invalid release tag selected.');
				} else {
					$started_at = date('Y-m-d H:i:s');
					$result = gsp_do_update($repo_owner, $repo_name, $version, 'release');
					$finished_at = date('Y-m-d H:i:s');
					if ($result['success']) {
						print_success(
							'Panel updated to release <strong>' . htmlspecialchars($version) . '</strong>. '
							. intval($result['files_copied']) . ' file(s) updated. Source: <strong>GitHub Releases</strong>'
						);
						gsp_update_log("Admin {$user_label} updated panel to release {$version}");
						gsp_log_update_to_db(
							'release', $version, 'success',
							'Updated to release ' . $version . ' by ' . $_SESSION['users_login'],
							$result['backup_dir'] ?? null,
							isset($result['backup_dir']) ? $result['backup_dir'] . '/database.sql'      : null,
							isset($result['backup_dir']) ? $result['backup_dir'] . '/panel-files.tar.gz': null,
							$started_at, $finished_at
						);
					} else {
						print_failure('Update failed: ' . htmlspecialchars($result['error']));
						gsp_update_log("Admin {$user_label} update to release {$version} FAILED: {$result['error']}");
						gsp_log_update_to_db(
							'release', $version, 'failed',
							'Update to release ' . $version . ' failed: ' . $result['error'],
							null, null, null, $started_at, $finished_at
						);
					}
				}

			} elseif ($action === 'update_stable') {
				$started_at = date('Y-m-d H:i:s');
				$result = gsp_do_update($repo_owner, $repo_name, $stable_branch, 'development');
				$finished_at = date('Y-m-d H:i:s');
				if ($result['success']) {
					print_success(
						'Panel updated to GitHub Stable (<strong>' . htmlspecialchars($stable_branch) . '</strong>). '
						. intval($result['files_copied']) . ' file(s) updated. Source: <strong>'
						. htmlspecialchars($stable_branch) . '</strong>'
					);
					gsp_update_log("Admin {$user_label} updated panel to GitHub Stable branch {$stable_branch}");
					gsp_log_update_to_db(
						'development', $stable_branch, 'success',
						'Updated to GitHub Stable branch ' . $stable_branch . ' by ' . $_SESSION['users_login'],
						$result['backup_dir'] ?? null,
						isset($result['backup_dir']) ? $result['backup_dir'] . '/database.sql'      : null,
						isset($result['backup_dir']) ? $result['backup_dir'] . '/panel-files.tar.gz': null,
						$started_at, $finished_at
					);
				} else {
					print_failure('Update failed: ' . htmlspecialchars($result['error']));
					gsp_update_log("Admin {$user_label} update to GitHub Stable branch {$stable_branch} FAILED: {$result['error']}");
					gsp_log_update_to_db(
						'development', $stable_branch, 'failed',
						'Update to GitHub Stable branch ' . $stable_branch . ' failed: ' . $result['error'],
						null, null, null, $started_at, $finished_at
					);
				}

			} elseif ($action === 'update_unstable') {
				$started_at = date('Y-m-d H:i:s');
				$result = gsp_do_update($repo_owner, $repo_name, $unstable_branch, 'cutting-edge');
				$finished_at = date('Y-m-d H:i:s');
				if ($result['success']) {
					print_success(
						'Panel updated to GitHub Unstable (<strong>' . htmlspecialchars($unstable_branch) . '</strong>). '
						. intval($result['files_copied']) . ' file(s) updated. Source: <strong>'
						. htmlspecialchars($unstable_branch) . '</strong>'
					);
					gsp_update_log("Admin {$user_label} updated panel to GitHub Unstable branch {$unstable_branch}");
					gsp_log_update_to_db(
						'cutting-edge', $unstable_branch, 'success',
						'Updated to GitHub Unstable branch ' . $unstable_branch . ' by ' . $_SESSION['users_login'],
						$result['backup_dir'] ?? null,
						isset($result['backup_dir']) ? $result['backup_dir'] . '/database.sql'      : null,
						isset($result['backup_dir']) ? $result['backup_dir'] . '/panel-files.tar.gz': null,
						$started_at, $finished_at
					);
				} else {
					print_failure('Update failed: ' . htmlspecialchars($result['error']));
					gsp_update_log("Admin {$user_label} update to GitHub Unstable branch {$unstable_branch} FAILED: {$result['error']}");
					gsp_log_update_to_db(
						'cutting-edge', $unstable_branch, 'failed',
						'Update to GitHub Unstable branch ' . $unstable_branch . ' failed: ' . $result['error'],
						null, null, null, $started_at, $finished_at
					);
				}

			} elseif ($action === 'revert') {
				$backup_ts = isset($_POST['gsp_revert_backup']) ? trim($_POST['gsp_revert_backup']) : '';
				if (!preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', $backup_ts)) {
					print_failure('Invalid backup timestamp selected.');
				} else {
					$started_at = date('Y-m-d H:i:s');
					$result = gsp_do_revert($backup_ts);
					$finished_at = date('Y-m-d H:i:s');
					if ($result['success']) {
						print_success(
							'Panel reverted to backup from <strong>' . htmlspecialchars($backup_ts) . '</strong>. '
							. intval($result['files_restored']) . ' file(s) restored.'
						);
						gsp_update_log("Admin {$user_label} reverted panel to backup {$backup_ts}");
						gsp_log_update_to_db(
							'revert', $backup_ts, 'success',
							'Reverted to backup ' . $backup_ts . ' by ' . $_SESSION['users_login'],
							GSP_BACKUP_BASE . '/' . $backup_ts,
							GSP_BACKUP_BASE . '/' . $backup_ts . '/database.sql',
							GSP_BACKUP_BASE . '/' . $backup_ts . '/panel-files.tar.gz',
							$started_at, $finished_at
						);
					} else {
						print_failure('Revert failed: ' . htmlspecialchars($result['error']));
						gsp_update_log("Admin {$user_label} revert to backup {$backup_ts} FAILED: {$result['error']}");
						gsp_log_update_to_db(
							'revert', $backup_ts, 'failed',
							'Revert to backup ' . $backup_ts . ' failed: ' . $result['error'],
							null, null, null, $started_at, $finished_at
						);
					}
				}
			}
		}

		// Rotate CSRF token after every submission
		$_SESSION['gsp_update_csrf'] = gsp_random_token();
		$csrf_token = $_SESSION['gsp_update_csrf'];
	}
	// ---- End POST handling --------------------------------------------------

	// Gather display data
	$current_version  = gsp_get_current_version();
	$current_branch   = gsp_get_current_branch();
	$git_commit       = gsp_get_git_commit();
	$vinfo            = gsp_read_version_json();
	$releases         = gsp_fetch_github_releases($repo_owner, $repo_name);
	$latest_release   = (is_array($releases) && !empty($releases))
		? htmlspecialchars($releases[0]['tag_name'] ?? 'N/A')
		: 'N/A (could not reach GitHub)';
	$backups          = gsp_get_available_backups();

	// ---- Render UI ----------------------------------------------------------
	echo "<h2>Panel Updates</h2>\n";
	echo "<table class='administration-table'><tr><td>\n";

	// Current status table
	echo "<h3>Current Installation</h3>\n";
	echo "<table class='center'>\n";
	if ($vinfo) {
		echo "<tr><td><strong>Installed Type:</strong></td><td>" . htmlspecialchars($vinfo['installed_type'] ?? 'N/A') . "</td></tr>\n";
		echo "<tr><td><strong>Installed Source:</strong></td><td>" . htmlspecialchars($vinfo['installed_source'] ?? 'N/A') . "</td></tr>\n";
		echo "<tr><td><strong>Installed Version:</strong></td><td>" . htmlspecialchars($vinfo['installed_version'] ?? 'N/A') . "</td></tr>\n";
		if (!empty($vinfo['installed_commit'])) {
			echo "<tr><td><strong>Installed Commit:</strong></td><td>"
			   . htmlspecialchars(substr($vinfo['installed_commit'], 0, 12)) . "</td></tr>\n";
		}
		echo "<tr><td><strong>Installed / Updated At:</strong></td><td>" . htmlspecialchars($vinfo['installed_at'] ?? 'N/A') . "</td></tr>\n";
	} else {
		echo "<tr><td><strong>Installed Version:</strong></td><td>" . htmlspecialchars($current_version) . "</td></tr>\n";
		echo "<tr><td><strong>Current Branch / Type:</strong></td><td>" . htmlspecialchars($current_branch) . "</td></tr>\n";
		if ($git_commit) {
			echo "<tr><td><strong>Git Commit:</strong></td><td>"
			   . htmlspecialchars(substr($git_commit, 0, 12)) . "</td></tr>\n";
		}
	}
	echo "<tr><td><strong>Latest Release on GitHub:</strong></td><td>" . $latest_release . "</td></tr>\n";
	echo "<tr><td><strong>Repository:</strong></td><td>"
	   . htmlspecialchars("{$repo_owner}/{$repo_name}") . "</td></tr>\n";
	// Backup status rows
	echo "<tr><td><strong>Backup Directory:</strong></td><td><code>"
	   . htmlspecialchars(GSP_BACKUP_BASE) . "</code></td></tr>\n";
	if (!empty($backups)) {
		$last_bk = $backups[0];
		$last_ts = htmlspecialchars($last_bk['ts']);
		$last_status = !empty($last_bk['meta']['backup_status'])
			? htmlspecialchars($last_bk['meta']['backup_status'])
			: 'unknown';
		echo "<tr><td><strong>Last Backup:</strong></td><td>"
		   . $last_ts . " &mdash; status: " . $last_status . "</td></tr>\n";
	} else {
		echo "<tr><td><strong>Last Backup:</strong></td><td><em>None yet</em></td></tr>\n";
	}
	echo "</table>\n<br>\n";

	// ---- Backup Only --------------------------------------------------------
	echo "<h3>Create Backup</h3>\n";
	echo "<form method='POST'>\n";
	echo "<input type='hidden' name='gsp_update_action' value='backup_only'>\n";
	echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
	echo "<button type='submit'"
	   . " onclick='return confirm(\"Create a backup of panel files and the database now (no update). Continue?\");'>"
	   . "Create Backup Now</button>\n";
	echo "<span style='margin-left:10px;color:#666;'>Saves to: <code>"
	   . htmlspecialchars(GSP_BACKUP_BASE) . "</code></span>\n";
	echo "</form>\n";

	echo "<br>\n";

	// ---- Numbered Releases --------------------------------------------------
	echo "<h3>Numbered Releases</h3>\n";
	if (is_array($releases) && !empty($releases)) {
		echo "<form method='POST'>\n";
		echo "<input type='hidden' name='gsp_update_action' value='update_release'>\n";
		echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
		echo "<select name='gsp_release_version'>\n";
		foreach ($releases as $rel) {
			$tag  = htmlspecialchars($rel['tag_name'] ?? '');
			$name = htmlspecialchars($rel['name'] ?? $rel['tag_name'] ?? $tag);
			echo "<option value='{$tag}'>{$name}</option>\n";
		}
		echo "</select>\n";
		echo " <button type='submit'"
		   . " onclick='return confirm(\"Back up and update the panel to this release. Continue?\");'>"
		   . "Update to Selected Release</button>\n";
		echo "</form>\n";
	} else {
		echo "<p>No releases available (GitHub API unreachable or no releases published).</p>\n";
	}

	echo "<br>\n";

	// ---- GitHub Stable -------------------------------------------------------
	echo "<h3>GitHub Stable</h3>\n";
	echo "<p>GitHub Stable should always match the latest official numbered release.</p>\n";
	echo "<form method='POST'>\n";
	echo "<input type='hidden' name='gsp_update_action' value='update_stable'>\n";
	echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
	echo "<button type='submit'"
	   . " onclick='return confirm(\"Back up and update the panel to GitHub Stable ("
	   . htmlspecialchars($stable_branch, ENT_QUOTES)
	   . "). Continue?\");'>"
	   . "Update to GitHub Stable</button>";
	echo " <span style='margin-left:10px;color:#666;'>Branch: "
	   . htmlspecialchars($stable_branch) . "</span>\n";
	echo "</form>\n";

	echo "<br>\n";

	// ---- GitHub Unstable -----------------------------------------------------
	echo "<h3>GitHub Unstable</h3>\n";
	echo "<p>GitHub Unstable represents the latest development branch and may be unstable.</p>\n";
	echo "<p style='display:inline-block;margin:4px 0 10px;padding:6px 10px;border-radius:6px;"
	   . "border:1px solid #d9b55a;background:#fff8e6;color:#6b5420;font-size:0.92em;'>"
	   . "&#9888; Cutting-edge updates may include unfinished changes. Use stable releases for production.</p><br>\n";
	echo "<form method='POST'>\n";
	echo "<input type='hidden' name='gsp_update_action' value='update_unstable'>\n";
	echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
	echo "<button type='submit'"
	   . " onclick='return confirm(\"WARNING: This is GitHub Unstable and may contain bugs.\\n\\nBack up and update anyway?\");'>"
	   . "Update to GitHub Unstable</button>";
	echo " <span style='margin-left:10px;color:#666;'>Branch: "
	   . htmlspecialchars($unstable_branch) . "</span>\n";
	echo "</form>\n";

	// ---- Revert Section -----------------------------------------------------
	if (!empty($backups)) {
		echo "<br>\n<h3>Revert Panel Update</h3>\n";
		echo "<p>Available backups in <code>" . htmlspecialchars(GSP_BACKUP_BASE) . "</code>:</p>\n";
		echo "<form method='POST'>\n";
		echo "<input type='hidden' name='gsp_update_action' value='revert'>\n";
		echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
		echo "<select name='gsp_revert_backup'>\n";
		foreach ($backups as $bk) {
			$ts    = htmlspecialchars($bk['ts']);
			$label = $ts;
			// Support both new metadata keys (update_target_type/update_target_version)
			// and legacy keys (update_type/update_target) for backward compatibility
			$bk_type   = $bk['meta']['update_target_type']    ?? $bk['meta']['update_type']    ?? '';
			$bk_target = $bk['meta']['update_target_version'] ?? $bk['meta']['update_target'] ?? '';
			if (!empty($bk_type) && !empty($bk_target)) {
				$label .= ' (before ' . htmlspecialchars($bk_type)
				        . ': ' . htmlspecialchars($bk_target) . ')';
			}
			if (!empty($bk['meta']['installed_version'])) {
				$label .= ' [was v' . htmlspecialchars($bk['meta']['installed_version']) . ']';
			}
			echo "<option value='" . htmlspecialchars($bk['ts']) . "'>{$label}</option>\n";
		}
		echo "</select>\n";
		echo " <button type='submit'"
		   . " onclick='return confirm(\"This will RESTORE panel files AND the database from the selected backup.\\n\\nAll changes since that backup will be lost. Are you sure?\");'>"
		   . "Revert Panel Update</button>\n";
		echo "</form>\n";
	}

	echo "</td></tr></table>\n";
}
?>
