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
define('GSP_PANEL_DIR',     realpath(dirname(__FILE__) . '/../../'));
define('GSP_BACKUP_BASE',   '/var/backups/gsp-panel');
define('GSP_UPDATE_LOG',    GSP_PANEL_DIR . '/logs/panel_updates.log');
define('GSP_VERSION_FILE',  GSP_PANEL_DIR . '/includes/panel_version.php');

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
// Backup: dump the MySQL database into $backup_dir
// ---------------------------------------------------------------------------
function gsp_backup_database($backup_dir)
{
	// Load DB credentials from config
	@include(GSP_PANEL_DIR . '/includes/config.inc.php');
	if (empty($db_user) || empty($db_name)) {
		return false;
	}

	// Write credentials to a temporary file to avoid exposing them in process listings
	$creds_file = tempnam(sys_get_temp_dir(), 'gsp_db_');
	if ($creds_file === false) {
		return false;
	}
	file_put_contents($creds_file,
		"[client]\nuser=" . addcslashes($db_user, "\\\n\"'") . "\n"
		. "password=" . addcslashes($db_pass, "\\\n\"'") . "\n"
	);
	chmod($creds_file, 0600);

	$sql_file = $backup_dir . '/' . $db_name . '_backup.sql';
	$command  = 'mysqldump --defaults-extra-file=' . escapeshellarg($creds_file)
	          . ' --skip-opt --single-transaction --add-drop-table'
	          . ' --create-options --extended-insert --quick --set-charset'
	          . ' '   . escapeshellarg($db_name)
	          . ' > ' . escapeshellarg($sql_file)
	          . ' 2>&1';
	@system($command);
	@unlink($creds_file);

	if (!file_exists($sql_file) || filesize($sql_file) < 100) {
		return false;
	}
	return $sql_file;
}

// ---------------------------------------------------------------------------
// Backup: recursively copy panel files (excluding noise dirs) into $dst_dir
// ---------------------------------------------------------------------------
function gsp_backup_files($src_dir, $dst_dir)
{
	$exclude_top = ['.git', 'logs', 'backups', 'cache', 'tmp'];

	if (!is_dir($dst_dir) && !@mkdir($dst_dir, 0750, true)) {
		return false;
	}

	$iter = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($src_dir, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::SELF_FIRST
	);

	foreach ($iter as $item) {
		$rel   = substr($item->getPathname(), strlen($src_dir) + 1);
		$parts = preg_split('#[/\\\\]#', $rel);

		// Skip excluded top-level directories
		if (in_array($parts[0], $exclude_top)) {
			continue;
		}

		// Skip *.log files
		if (!$item->isDir() && substr($item->getFilename(), -4) === '.log') {
			continue;
		}

		$dst_path = $dst_dir . DIRECTORY_SEPARATOR . $rel;

		if ($item->isDir()) {
			if (!is_dir($dst_path)) {
				@mkdir($dst_path, 0755, true);
			}
		} else {
			$dst_parent = dirname($dst_path);
			if (!is_dir($dst_parent)) {
				@mkdir($dst_parent, 0755, true);
			}
			if (!@copy($item->getPathname(), $dst_path)) {
				return false;
			}
		}
	}
	return true;
}

// ---------------------------------------------------------------------------
// Backup: create a full timestamped backup (DB + files + metadata)
// ---------------------------------------------------------------------------
function gsp_create_full_backup($update_type, $update_target)
{
	$ts         = date('Y-m-d_H-i-s');
	$backup_dir = GSP_BACKUP_BASE . '/' . $ts;

	// Ensure backup base exists
	if (!is_dir(GSP_BACKUP_BASE) && !@mkdir(GSP_BACKUP_BASE, 0750, true)) {
		return [
			'success' => false,
			'error'   => 'Cannot create backup directory ' . GSP_BACKUP_BASE
			           . '. Run: sudo mkdir -p ' . GSP_BACKUP_BASE
			           . ' && sudo chown www-data:www-data ' . GSP_BACKUP_BASE,
		];
	}

	if (!@mkdir($backup_dir, 0750, true)) {
		return ['success' => false, 'error' => 'Cannot create backup directory: ' . $backup_dir];
	}

	// 1. Database backup
	$sql_file = gsp_backup_database($backup_dir);
	if ($sql_file === false) {
		return [
			'success' => false,
			'error'   => 'Database backup failed. Check that mysqldump is installed and credentials are correct.',
		];
	}

	// 2. File backup
	$backup_files_dir = $backup_dir . '/files';
	if (!gsp_backup_files(GSP_PANEL_DIR, $backup_files_dir)) {
		return ['success' => false, 'error' => 'Panel file backup failed.'];
	}

	// 3. Metadata
	$meta = [
		'backup_timestamp'  => $ts,
		'git_commit'        => gsp_get_git_commit(),
		'installed_version' => gsp_get_current_version(),
		'update_type'       => $update_type,
		'update_target'     => $update_target,
	];
	file_put_contents($backup_dir . '/backup.json', json_encode($meta, JSON_PRETTY_PRINT));

	return [
		'success'    => true,
		'backup_dir' => $backup_dir,
		'sql_file'   => $sql_file,
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

	// Step 2 — download
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

	// Step 3 — apply
	$apply = gsp_apply_update($zip_file);
	@unlink($zip_file);
	@rmdir($temp_dir);
	if (!$apply['success']) {
		return $apply;
	}
	gsp_update_log("Applied update: {$apply['files_copied']} files written");

	// Step 4 — housekeeping
	gsp_fix_permissions($panel_dir);
	gsp_clear_panel_cache($panel_dir);
	gsp_write_version_file($ref, $update_type);
	$db->setSettings(['ogp_version' => $ref, 'version_type' => $update_type]);

	// Step 5 — post-update module handling (mirrors updating.php behaviour)
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
	return ['success' => true, 'files_copied' => $apply['files_copied']];
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

	$backup_files_dir = $backup_dir . '/files';
	if (!is_dir($backup_files_dir)) {
		return ['success' => false, 'error' => 'Backup files directory not found.'];
	}

	$sql_files = glob($backup_dir . '/*.sql');
	if (!$sql_files) {
		return ['success' => false, 'error' => 'No SQL dump found in backup.'];
	}
	$sql_file = $sql_files[0];

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
	$copied = 0;
	$iter   = new RecursiveIteratorIterator(
		new RecursiveDirectoryIterator($backup_files_dir, RecursiveDirectoryIterator::SKIP_DOTS),
		RecursiveIteratorIterator::SELF_FIRST
	);
	foreach ($iter as $item) {
		$rel     = substr($item->getPathname(), strlen($backup_files_dir));
		$dst     = $panel_dir . $rel;
		if ($item->isDir()) {
			if (!is_dir($dst)) {
				@mkdir($dst, 0755, true);
			}
		} else {
			$dst_dir = dirname($dst);
			if (!is_dir($dst_dir)) {
				@mkdir($dst_dir, 0755, true);
			}
			if (@copy($item->getPathname(), $dst)) {
				$copied++;
			}
		}
	}
	gsp_update_log("Revert: restored {$copied} files from backup {$backup_ts}");

	// Restore database
	@include(GSP_PANEL_DIR . '/includes/config.inc.php');
	if (!empty($db_user) && !empty($db_name)) {
		// Write credentials to a temp file to avoid exposing them in process listings
		$creds_file = tempnam(sys_get_temp_dir(), 'gsp_db_');
		if ($creds_file !== false) {
			file_put_contents($creds_file,
				"[client]\nuser=" . addcslashes($db_user, "\\\n\"'") . "\n"
				. "password=" . addcslashes($db_pass, "\\\n\"'") . "\n"
			);
			chmod($creds_file, 0600);
			$cmd = 'mysql --defaults-extra-file=' . escapeshellarg($creds_file)
			     . ' '  . escapeshellarg($db_name)
			     . ' < ' . escapeshellarg($sql_file)
			     . ' 2>&1';
			@system($cmd, $ret);
			@unlink($creds_file);
			if ($ret !== 0) {
				gsp_update_log("Revert warning: database restore exited with code {$ret}");
			}
		}
	}

	// Housekeeping
	gsp_fix_permissions($panel_dir);
	gsp_clear_panel_cache($panel_dir);

	// Turn off maintenance mode (unless it was already on before we started)
	if (!$had_maintenance) {
		$db->setSettings(['maintenance_mode' => '0']);
	}

	gsp_update_log("Revert to backup {$backup_ts} complete");
	return ['success' => true, 'files_restored' => $copied];
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

			if ($action === 'update_release') {
				$version = isset($_POST['gsp_release_version']) ? trim($_POST['gsp_release_version']) : '';
				if (!preg_match('/^[a-zA-Z0-9._\-]+$/', $version) || strlen($version) > 80) {
					print_failure('Invalid release tag selected.');
				} else {
					$result = gsp_do_update($repo_owner, $repo_name, $version, 'release');
					if ($result['success']) {
						print_success(
							'Panel updated to release <strong>' . htmlspecialchars($version) . '</strong>. '
							. intval($result['files_copied']) . ' file(s) updated.'
						);
						gsp_update_log("Admin {$user_label} updated panel to release {$version}");
					} else {
						print_failure('Update failed: ' . htmlspecialchars($result['error']));
						gsp_update_log("Admin {$user_label} update to release {$version} FAILED: {$result['error']}");
					}
				}

			} elseif ($action === 'update_stable') {
				$result = gsp_do_update($repo_owner, $repo_name, $stable_branch, 'stable');
				if ($result['success']) {
					print_success(
						'Panel updated to development version (<strong>' . htmlspecialchars($stable_branch) . '</strong>). '
						. intval($result['files_copied']) . ' file(s) updated.'
					);
					gsp_update_log("Admin {$user_label} updated panel to stable branch {$stable_branch}");
				} else {
					print_failure('Update failed: ' . htmlspecialchars($result['error']));
					gsp_update_log("Admin {$user_label} update to stable branch {$stable_branch} FAILED: {$result['error']}");
				}

			} elseif ($action === 'update_unstable') {
				$result = gsp_do_update($repo_owner, $repo_name, $unstable_branch, 'unstable');
				if ($result['success']) {
					print_success(
						'Panel updated to cutting edge version (<strong>' . htmlspecialchars($unstable_branch) . '</strong>). '
						. intval($result['files_copied']) . ' file(s) updated.'
					);
					gsp_update_log("Admin {$user_label} updated panel to unstable branch {$unstable_branch}");
				} else {
					print_failure('Update failed: ' . htmlspecialchars($result['error']));
					gsp_update_log("Admin {$user_label} update to unstable branch {$unstable_branch} FAILED: {$result['error']}");
				}

			} elseif ($action === 'revert') {
				$backup_ts = isset($_POST['gsp_revert_backup']) ? trim($_POST['gsp_revert_backup']) : '';
				if (!preg_match('/^\d{4}-\d{2}-\d{2}_\d{2}-\d{2}-\d{2}$/', $backup_ts)) {
					print_failure('Invalid backup timestamp selected.');
				} else {
					$result = gsp_do_revert($backup_ts);
					if ($result['success']) {
						print_success(
							'Panel reverted to backup from <strong>' . htmlspecialchars($backup_ts) . '</strong>. '
							. intval($result['files_restored']) . ' file(s) restored.'
						);
						gsp_update_log("Admin {$user_label} reverted panel to backup {$backup_ts}");
					} else {
						print_failure('Revert failed: ' . htmlspecialchars($result['error']));
						gsp_update_log("Admin {$user_label} revert to backup {$backup_ts} FAILED: {$result['error']}");
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
	$releases         = gsp_fetch_github_releases($repo_owner, $repo_name);
	$latest_release   = (is_array($releases) && !empty($releases))
		? htmlspecialchars($releases[0]['tag_name'] ?? 'N/A')
		: 'N/A (could not reach GitHub)';
	$backups          = gsp_get_available_backups();

	// ---- Render UI ----------------------------------------------------------
	echo "<h2>Panel Updates</h2>\n";
	echo "<table class='administration-table'><tr><td>\n";

	// Current status table
	echo "<table class='center'>\n";
	echo "<tr><td><strong>Installed Version:</strong></td><td>" . htmlspecialchars($current_version) . "</td></tr>\n";
	echo "<tr><td><strong>Current Branch / Type:</strong></td><td>" . htmlspecialchars($current_branch) . "</td></tr>\n";
	if ($git_commit) {
		echo "<tr><td><strong>Git Commit:</strong></td><td>"
		   . htmlspecialchars(substr($git_commit, 0, 12)) . "</td></tr>\n";
	}
	echo "<tr><td><strong>Latest Release on GitHub:</strong></td><td>" . $latest_release . "</td></tr>\n";
	echo "<tr><td><strong>Repository:</strong></td><td>"
	   . htmlspecialchars("{$repo_owner}/{$repo_name}") . "</td></tr>\n";
	echo "</table>\n<br>\n";

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

	// ---- Development Version ------------------------------------------------
	echo "<h3>Development Version</h3>\n";
	echo "<form method='POST'>\n";
	echo "<input type='hidden' name='gsp_update_action' value='update_stable'>\n";
	echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
	echo "<button type='submit'"
	   . " onclick='return confirm(\"Back up and update the panel to the "
	   . htmlspecialchars($stable_branch, ENT_QUOTES)
	   . " branch. Continue?\");'>"
	   . "Update to Development Version</button>";
	echo " <span style='margin-left:10px;color:#666;'>Branch: "
	   . htmlspecialchars($stable_branch) . "</span>\n";
	echo "</form>\n";

	echo "<br>\n";

	// ---- Cutting Edge Version -----------------------------------------------
	echo "<h3>Cutting Edge Version</h3>\n";
	echo "<p class='failure' style='display:inline-block;padding:5px 10px;'>"
	   . "&#9888; Warning: The cutting edge version may be unstable or contain bugs. Use with caution in production.</p><br><br>\n";
	echo "<form method='POST'>\n";
	echo "<input type='hidden' name='gsp_update_action' value='update_unstable'>\n";
	echo "<input type='hidden' name='gsp_update_csrf' value='" . htmlspecialchars($csrf_token) . "'>\n";
	echo "<button type='submit'"
	   . " onclick='return confirm(\"WARNING: This is the cutting-edge (unstable) branch and may contain bugs.\\n\\nBack up and update anyway?\");'>"
	   . "Update to Cutting Edge Version</button>";
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
			if (!empty($bk['meta']['update_type']) && !empty($bk['meta']['update_target'])) {
				$label .= ' (before ' . htmlspecialchars($bk['meta']['update_type'])
				        . ': ' . htmlspecialchars($bk['meta']['update_target']) . ')';
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
