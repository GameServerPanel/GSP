<?php
/*
 *
 * GSP - Server Content helpers (addonsmanager)
 *
 */

if (!defined('SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT')) {
	define('SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT', 'generic_steam_workshop_linux.sh');
}
if (!defined('SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT')) {
	define('SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT', 'generic_steam_workshop_windows_cygwin.sh');
}

function scm_ensure_workshop_schema($db)
{
	static $schema_checked = false;
	if ($schema_checked) {
		return true;
	}
	$schema_checked = true;

	$db->query("ALTER TABLE `".OGP_DB_PREFIX."addons` MODIFY `addon_type` VARCHAR(32) NOT NULL");
	return (bool)$db->query(
		"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."server_content_workshop` (
			`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`home_id` INT NOT NULL,
			`home_cfg_id` INT NOT NULL,
			`remote_server_id` INT NULL,
			`workshop_app_id` VARCHAR(32) NULL,
			`workshop_item_id` VARCHAR(64) NOT NULL,
			`title` VARCHAR(255) NULL,
			`install_state` VARCHAR(32) NOT NULL DEFAULT 'selected',
			`last_installed_at` DATETIME NULL,
			`last_updated_at` DATETIME NULL,
			`last_error` TEXT NULL,
			`created_by` INT NULL,
			`created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`updated_at` DATETIME NULL,
			UNIQUE KEY `uniq_home_workshop_item` (`home_id`, `workshop_item_id`),
			KEY `idx_home_id` (`home_id`),
			KEY `idx_home_cfg_id` (`home_cfg_id`),
			KEY `idx_install_state` (`install_state`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
	);
}

function scm_get_home_for_user($db, $home_id, $user_id)
{
	$home_id = (int)$home_id;
	$user_id = (int)$user_id;
	if ($home_id <= 0 || $user_id <= 0) {
		return false;
	}
	if ($db->isAdmin($user_id)) {
		return $db->getGameHome($home_id);
	}
	return $db->getUserGameHome($user_id, $home_id);
}

function scm_get_workshop_saved_count($db, $home_id)
{
	$home_id = (int)$home_id;
	if ($home_id <= 0 || !scm_ensure_workshop_schema($db)) {
		return 0;
	}
	$rows = $db->resultQuery(
		"SELECT COUNT(*) AS cnt FROM `".OGP_DB_PREFIX."server_content_workshop` WHERE home_id=".$home_id." AND install_state<>'removed'"
	);
	if (!is_array($rows) || !isset($rows[0]['cnt'])) {
		return 0;
	}
	return (int)$rows[0]['cnt'];
}

function scm_get_workshop_rows($db, $home_id)
{
	$home_id = (int)$home_id;
	if ($home_id <= 0 || !scm_ensure_workshop_schema($db)) {
		return array();
	}
	$rows = $db->resultQuery(
		"SELECT * FROM `".OGP_DB_PREFIX."server_content_workshop` WHERE home_id=".$home_id." ORDER BY created_at DESC, workshop_item_id ASC"
	);
	return is_array($rows) ? $rows : array();
}

function scm_parse_workshop_ids($raw, &$invalid = array())
{
	$invalid = array();
	$ids = array();
	$parts = explode(',', (string)$raw);
	foreach ((array)$parts as $part) {
		$value = trim((string)$part);
		if ($value === '') {
			continue;
		}
		if (!preg_match('/^[0-9]+$/', $value)) {
			$invalid[] = $value;
			continue;
		}
		$ids[$value] = $value;
	}
	return array_values($ids);
}

function scm_parse_selected_workshop_ids($selected)
{
	$ids = array();
	if (!is_array($selected)) {
		return $ids;
	}
	foreach ($selected as $item_id) {
		$item_id = trim((string)$item_id);
		if ($item_id !== '' && preg_match('/^[0-9]+$/', $item_id)) {
			$ids[$item_id] = $item_id;
		}
	}
	return array_values($ids);
}

function scm_h($value)
{
	return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function scm_is_windows_home(array $home_info)
{
	$game_key = isset($home_info['game_key']) ? strtolower((string)$home_info['game_key']) : '';
	$cfg_file = isset($home_info['home_cfg_file']) ? strtolower((string)$home_info['home_cfg_file']) : '';
	return (strpos($game_key, 'win') !== false) || (strpos($cfg_file, 'win') !== false);
}

function scm_path_is_under_home($home_path, $candidate_path)
{
	$home_path = rtrim(clean_path((string)$home_path), '/');
	$candidate_path = clean_path((string)$candidate_path);
	if ($home_path === '' || $candidate_path === '') {
		return false;
	}
	return strpos($candidate_path.'/', $home_path.'/') === 0;
}

function scm_get_workshop_manifest_path(array $home_info)
{
	$home_path = rtrim(clean_path((string)$home_info['home_path']), '/');
	$manifest_path = clean_path($home_path . '/gsp_server_content/workshop_manifest.json');
	if (!scm_path_is_under_home($home_path, $manifest_path)) {
		return false;
	}
	return $manifest_path;
}

function scm_extract_workshop_app_id($server_xml)
{
	$candidates = array(
		'workshop_app_id',
		'workshop_appid',
		'steam_workshop_app_id',
		'steam_workshop_appid',
	);
	foreach ((array)$candidates as $candidate) {
		if (isset($server_xml->$candidate)) {
			$value = trim((string)$server_xml->$candidate);
			if ($value !== '' && preg_match('/^[0-9]+$/', $value)) {
				return $value;
			}
		}
	}
	return "";
}

function scm_get_workshop_script_path(array $home_info, $server_xml)
{
	$key = scm_is_windows_home($home_info) ? 'workshop_script_windows' : 'workshop_script_linux';
	if (isset($server_xml->$key)) {
		$xml_path = trim((string)$server_xml->$key);
		if ($xml_path !== '' && preg_match('/^[^\\r\\n\\0]+$/', $xml_path)) {
			return $xml_path;
		}
	}
	return scm_is_windows_home($home_info) ? SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT : SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT;
}

function scm_get_bundled_workshop_script_source(array $home_info)
{
	$filename = scm_is_windows_home($home_info) ? SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT : SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT;
	return dirname(__FILE__) . '/scripts/workshop/' . $filename;
}

function scm_is_legacy_panel_workshop_script_path($script_path)
{
	$script_path = trim((string)$script_path);
	if ($script_path === '') {
		return false;
	}
	return (strpos($script_path, '/var/www/html/') === 0 && strpos($script_path, '/Panel/modules/addonsmanager/scripts/workshop/') !== false)
		|| (strpos($script_path, '/OGP_User_Files/modules/addonsmanager/scripts/') !== false);
}

function scm_get_agent_managed_workshop_script_path(array $home_info)
{
	$home_path = rtrim(clean_path((string)$home_info['home_path']), '/');
	$filename = scm_is_windows_home($home_info) ? SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT : SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT;
	$remote_path = clean_path($home_path . '/gsp_server_content/scripts/workshop/' . $filename);
	if (!scm_path_is_under_home($home_path, $remote_path)) {
		return false;
	}
	return $remote_path;
}

function scm_prepare_workshop_script_for_agent($remote, array $home_info, $server_xml, &$error = '')
{
	$error = '';
	$configured_path = trim((string)scm_get_workshop_script_path($home_info, $server_xml));
	if ($configured_path !== '' && !scm_is_legacy_panel_workshop_script_path($configured_path) && preg_match('/^[^\\r\\n\\0]+$/', $configured_path)) {
		if ((int)$remote->rfile_exists($configured_path) === 1) {
			return $configured_path;
		}
		$error = 'Configured workshop script not found on agent host: ' . $configured_path;
		return false;
	}

	$source_path = scm_get_bundled_workshop_script_source($home_info);
	if (!is_file($source_path)) {
		$error = 'Bundled workshop script is missing from the panel: ' . $source_path;
		return false;
	}

	$remote_path = scm_get_agent_managed_workshop_script_path($home_info);
	if ($remote_path === false) {
		$error = 'Unable to resolve an agent-managed workshop script path for this server.';
		return false;
	}

	$script_body = @file_get_contents($source_path);
	if ($script_body === false || $script_body === '') {
		$error = 'Failed to read bundled workshop script: ' . $source_path;
		return false;
	}

	$remote_dir = dirname($remote_path);
	$remote->exec("mkdir -p " . escapeshellarg($remote_dir));
	if ((int)$remote->remote_writefile($remote_path, $script_body) !== 1) {
		$error = 'Failed to sync workshop script to agent host.';
		return false;
	}
	$remote->exec("chmod 755 " . escapeshellarg($remote_path) . " >/dev/null 2>&1 || true");
	return $remote_path;
}

function scm_get_csrf_token()
{
	if (empty($_SESSION['addonsmanager_workshop_csrf'])) {
		$_SESSION['addonsmanager_workshop_csrf'] = md5(uniqid((string)mt_rand(), true));
	}
	return $_SESSION['addonsmanager_workshop_csrf'];
}

function scm_validate_csrf_token($token)
{
	if (!isset($_SESSION['addonsmanager_workshop_csrf'])) {
		return false;
	}
	return hash_equals((string)$_SESSION['addonsmanager_workshop_csrf'], (string)$token);
}

// ─────────────────────────────────────────────────────────────────────────────
// Phase 2 helpers – schema guard, cache mode, manifest, install history
// ─────────────────────────────────────────────────────────────────────────────

/**
 * Returns the allowed values for the server_content_cache_mode panel setting.
 *
 * disabled              – Always install from the configured source. No scanning,
 *                         no shared cache. (DEFAULT – safest choice)
 * search_existing_servers – Agent may scan other local game-server folders for
 *                         matching cacheable content and copy directly if safe.
 * shared_cache          – Agent may store cacheable content in a shared cache
 *                         folder and reuse it on future installs.
 * shared_cache_and_search – Both shared_cache and search_existing_servers are
 *                         active simultaneously.
 *
 * Security note: only content explicitly marked is_cacheable=1 on the addon
 * record may ever be shared or cached.  Private configs, user-edited files,
 * saves, databases, logs, and credentials must never be included.
 *
 * @return array<string,string> key => human-readable label
 */
function scm_get_valid_cache_modes()
{
	return array(
		'disabled'                 => 'Disabled (always install from source)',
		'search_existing_servers'  => 'Search existing servers (copy from local installs)',
		'shared_cache'             => 'Shared cache (store and reuse cached copies)',
		'shared_cache_and_search'  => 'Shared cache + search existing servers',
	);
}

/**
 * Reads the current server_content_cache_mode panel setting.
 * Returns 'disabled' if not set.
 *
 * @param  object $db  Panel DB handle
 * @return string      One of the scm_get_valid_cache_modes() keys
 */
function scm_get_cache_mode($db)
{
	$valid = scm_get_valid_cache_modes();
	$value = '';
	if (method_exists($db, 'getSetting')) {
		$value = (string)$db->getSetting('server_content_cache_mode');
	}
	return array_key_exists($value, $valid) ? $value : 'disabled';
}

/**
 * Returns allowed install_method values and their display labels.
 *
 * @return array<string,string>
 */
function scm_get_install_methods()
{
	return array(
		'download_zip'   => 'Downloadable Mod',
		'steam_workshop' => 'Steam Workshop Item',
		'config_edit'    => 'Configuration Package',
		'post_script'    => 'Scripted Installer',
	);
}

function scm_get_install_method_help_text()
{
	return array(
		'download_zip'   => 'Download and extract a ZIP, RAR, or archive file.',
		'steam_workshop' => 'Install a Steam Workshop mod using Workshop ID.',
		'config_edit'    => 'Install configuration files, profiles, or templates.',
		'post_script'    => 'Run a custom scripted installation process.',
	);
}

function scm_get_install_method_required_fields()
{
	return array(
		'download_zip' => array('url'),
		'steam_workshop' => array('workshop_item_id'),
		'post_script' => array('post_script'),
		'config_edit' => array('path', 'config_edit_rule'),
	);
}

function scm_get_install_method_validation_errors()
{
	return array(
		'download_zip' => 'Please enter a download URL.',
		'steam_workshop' => 'Please enter a Workshop ID.',
		'config_edit' => 'Please enter the config target and edit action.',
		'post_script' => 'Please enter the installer script/action.',
	);
}

function scm_get_install_method_default($value = '')
{
	$value = trim((string)$value);
	if ($value === 'download_file') {
		$value = 'download_zip';
	}
	if ($value === 'create_folder') {
		$value = 'config_edit';
	}
	$methods = scm_get_install_methods();
	return isset($methods[$value]) ? $value : 'download_zip';
}

function scm_get_install_payload_keys()
{
	return array(
		'url',
		'path',
		'workshop_item_id',
		'workshop_app_id',
		'target_path_template',
		'optional_folder_name',
		'post_script',
		'config_edit_rule',
		'launch_param_additions',
	);
}

function scm_collect_install_payload(array $defaults = array(), array $request = array(), array $override_keys = array())
{
	$payload = array();
	foreach (scm_get_install_payload_keys() as $key) {
		$payload[$key] = isset($defaults[$key]) ? trim((string)$defaults[$key]) : '';
	}
	foreach ($override_keys as $key) {
		if (array_key_exists($key, $request)) {
			$payload[$key] = trim((string)$request[$key]);
		}
	}
	return $payload;
}

function scm_validate_numeric_content_value($value, $error_message, &$message, $allow_blank = false)
{
	$value = trim((string)$value);
	if ($value === '') {
		if ($allow_blank) {
			$message = '';
			return true;
		}
		$message = $error_message;
		return false;
	}
	if (!preg_match('/^[0-9]+$/', $value)) {
		$message = $error_message;
		return false;
	}
	$message = '';
	return true;
}

function scm_validate_download_content(array $payload, &$message = '')
{
	$url = isset($payload['url']) ? trim((string)$payload['url']) : '';
	if ($url === '') {
		$message = 'Please enter a download URL.';
		return false;
	}
	$message = '';
	return true;
}

function scm_validate_workshop_content(array $payload, &$message = '')
{
	if (!scm_validate_numeric_content_value(isset($payload['workshop_item_id']) ? $payload['workshop_item_id'] : '', 'Please enter a Workshop ID.', $message, false)) {
		return false;
	}
	if (!scm_validate_numeric_content_value(isset($payload['workshop_app_id']) ? $payload['workshop_app_id'] : '', 'Workshop App ID must be numeric.', $message, true)) {
		return false;
	}
	$folder_name = isset($payload['optional_folder_name']) ? trim((string)$payload['optional_folder_name']) : '';
	if ($folder_name !== '' && (strpos($folder_name, '..') !== false || preg_match('/[\\\\\\/]/', $folder_name))) {
		$message = 'Optional folder name must be a single folder name.';
		return false;
	}
	$message = '';
	return true;
}

function scm_validate_scripted_installer(array $payload, &$message = '')
{
	$script = isset($payload['post_script']) ? trim((string)$payload['post_script']) : '';
	if ($script === '') {
		$message = 'Please enter the installer script/action.';
		return false;
	}
	$message = '';
	return true;
}

function scm_validate_configuration_package(array $payload, &$message = '')
{
	$path = isset($payload['path']) ? trim((string)$payload['path']) : '';
	$rule = isset($payload['config_edit_rule']) ? trim((string)$payload['config_edit_rule']) : '';
	if ($path === '' || $rule === '') {
		$message = 'Please enter the config target and edit action.';
		return false;
	}
	$message = '';
	return true;
}

function scm_validate_install_method_payload($install_method, array $payload, &$message = '')
{
	$install_method = scm_get_install_method_default($install_method);
	if (!isset(scm_get_install_method_required_fields()[$install_method])) {
		$message = 'Invalid install/content type selected.';
		return false;
	}

	if ($install_method === 'download_zip') {
		return scm_validate_download_content($payload, $message);
	}
	if ($install_method === 'steam_workshop') {
		return scm_validate_workshop_content($payload, $message);
	}
	if ($install_method === 'post_script') {
		return scm_validate_scripted_installer($payload, $message);
	}
	if ($install_method === 'config_edit') {
		return scm_validate_configuration_package($payload, $message);
	}
	$message = '';
	return true;
}

function scm_build_workshop_runtime_context($db, array $home_info, $server_xml, array $payload, &$message = '')
{
	if (!scm_validate_workshop_content($payload, $message)) {
		return false;
	}

	$workshop_item_id = trim((string)$payload['workshop_item_id']);
	$target_path_template = trim((string)$payload['target_path_template']);
	$optional_folder_name = trim((string)$payload['optional_folder_name']);
	$workshop_app_id_override = trim((string)$payload['workshop_app_id']);
	$fallback_profile = function_exists('sw_get_profile_for_home') ? sw_get_profile_for_home($db, (int)$home_info['home_id']) : false;
	$resolved = function_exists('steam_workshop_install_item_to_home')
		? steam_workshop_install_item_to_home($db, $home_info, $workshop_item_id, $target_path_template, array(
			'optional_folder_name' => $optional_folder_name,
			'workshop_app_id' => $workshop_app_id_override,
		))
		: array('ok' => false);

	if (!empty($resolved['ok'])) {
		$profile = (isset($resolved['profile']) && is_array($resolved['profile'])) ? $resolved['profile'] : array();
		$message = '';
		return array(
			'workshop_item_id' => $workshop_item_id,
			'workshop_app_id' => isset($resolved['workshop_app_id']) ? (string)$resolved['workshop_app_id'] : '',
			'steam_app_id' => isset($resolved['steam_app_id']) ? (string)$resolved['steam_app_id'] : '',
			'folder_name' => isset($resolved['folder_name']) ? (string)$resolved['folder_name'] : ($optional_folder_name !== '' ? $optional_folder_name : '@' . $workshop_item_id),
			'target_path_template' => isset($resolved['target_path_template']) ? (string)$resolved['target_path_template'] : $target_path_template,
			'target_path_resolved' => isset($resolved['target_path_resolved']) ? (string)$resolved['target_path_resolved'] : '',
			'server_root' => rtrim((string)$home_info['home_path'], '/'),
			'steamcmd_path' => isset($profile['steamcmd_path']) ? trim((string)$profile['steamcmd_path']) : '',
			'workshop_download_dir' => (isset($profile['workshop_download_dir_template']) && trim((string)$profile['workshop_download_dir_template']) !== '')
				? sw_apply_template((string)$profile['workshop_download_dir_template'], (array)$resolved['vars'])
				: '',
		);
	}

	$fallback_workshop_app_id = $workshop_app_id_override;
	if ($fallback_workshop_app_id === '' && is_array($fallback_profile) && !empty($fallback_profile['workshop_app_id'])) {
		$fallback_workshop_app_id = (string)$fallback_profile['workshop_app_id'];
	}
	if ($fallback_workshop_app_id === '') {
		$fallback_workshop_app_id = scm_extract_workshop_app_id($server_xml);
	}
	$steam_app_id = (is_array($fallback_profile) && !empty($fallback_profile['steam_app_id'])) ? (string)$fallback_profile['steam_app_id'] : '';
	$folder_name = ($optional_folder_name !== '') ? $optional_folder_name : '@' . $workshop_item_id;
	$effective_template = $target_path_template;
	if ($effective_template === '') {
		$effective_template = (is_array($fallback_profile) && !empty($fallback_profile['install_path_template']))
			? (string)$fallback_profile['install_path_template']
			: '{SERVER_ROOT}/{MOD_FOLDER}';
	}
	$placeholder_map = scm_build_placeholder_map($home_info, array('exe_location' => isset($server_xml->exe_location) ? (string)$server_xml->exe_location : ''), array(
		'WORKSHOP_ID' => $workshop_item_id,
		'WORKSHOP_APP_ID' => $fallback_workshop_app_id,
		'STEAM_APP_ID' => $steam_app_id,
		'FOLDER_NAME' => $folder_name,
		'MOD_FOLDER' => $folder_name,
	));
	$message = '';
	return array(
		'workshop_item_id' => $workshop_item_id,
		'workshop_app_id' => $fallback_workshop_app_id,
		'steam_app_id' => $steam_app_id,
		'folder_name' => $folder_name,
		'target_path_template' => $effective_template,
		'target_path_resolved' => scm_apply_placeholders($effective_template, $placeholder_map),
		'server_root' => rtrim((string)$home_info['home_path'], '/'),
		'steamcmd_path' => (is_array($fallback_profile) && !empty($fallback_profile['steamcmd_path'])) ? (string)$fallback_profile['steamcmd_path'] : '',
		'workshop_download_dir' => '',
	);
}

function scm_build_placeholder_map(array $home_info, array $server_context = array(), array $overrides = array())
{
	$home_id = (int)(isset($home_info['home_id']) ? $home_info['home_id'] : 0);
	$server_root = rtrim(clean_path((string)(isset($home_info['home_path']) ? $home_info['home_path'] : '')), '/');
	$game_root = $server_root;
	if (!empty($server_context['exe_location'])) {
		$exe_location = clean_path((string)$server_context['exe_location']);
		$exe_dir = dirname($exe_location);
		if ($exe_dir !== '.' && $exe_dir !== '/') {
			$game_root = clean_path($server_root . '/' . ltrim($exe_dir, '/'));
		}
	}
	$map = array(
		'{HOME_ID}' => (string)$home_id,
		'{SERVER_ROOT}' => $server_root,
		'{GAME_ROOT}' => $game_root,
		'{WORKSHOP_ID}' => '',
		'{WORKSHOP_APP_ID}' => '',
		'{STEAM_APP_ID}' => '',
		'{FOLDER_NAME}' => '',
		'{MOD_FOLDER}' => '',
	);
	foreach ($overrides as $key => $value) {
		$token = '{' . strtoupper(trim((string)$key, '{}')) . '}';
		$map[$token] = (string)$value;
	}
	return $map;
}

function scm_apply_placeholders($template, array $placeholder_map)
{
	$template = (string)$template;
	if ($template === '') {
		return '';
	}
	return str_replace(array_keys($placeholder_map), array_values($placeholder_map), $template);
}

function scm_content_logs_dir()
{
	return dirname(__FILE__) . '/logs';
}

function scm_content_log_file()
{
	return scm_content_logs_dir() . '/content_install.log';
}

function scm_log_content_install_action(array $context)
{
	$dir = scm_content_logs_dir();
	if (!is_dir($dir)) {
		@mkdir($dir, 0775, true);
	}
	$context['logged_at'] = date('Y-m-d H:i:s');
	$line = json_encode($context);
	if ($line === false) {
		$line = '{"logged_at":"' . date('Y-m-d H:i:s') . '","error":"json_encode_failed"}';
	}
	@error_log($line . PHP_EOL, 3, scm_content_log_file());
	return true;
}

/**
 * Idempotently ensures the Phase 2 schema is present.
 * Called from pages that use manifest / history data so that existing
 * installs that have not yet run the module updater are covered.
 *
 * @param  object $db  Panel DB handle
 * @return bool
 */
function scm_ensure_phase2_schema($db)
{
	static $phase2_checked = false;
	if ($phase2_checked) {
		return true;
	}
	$phase2_checked = true;
	$prefix = OGP_DB_PREFIX;

	// ── Extend addons table ───────────────────────────────────────────────────
	$new_columns = array(
		'install_method'        => "VARCHAR(32) NOT NULL DEFAULT 'download_zip'",
		'content_version'       => "VARCHAR(64) NULL",
		'requires_stop'         => "TINYINT(1) NOT NULL DEFAULT 1",
		'backup_before_install' => "TINYINT(1) NOT NULL DEFAULT 1",
		'restart_after_install' => "TINYINT(1) NOT NULL DEFAULT 0",
		'is_cacheable'          => "TINYINT(1) NOT NULL DEFAULT 0",
		'description'           => "TEXT NULL",
		'workshop_item_id'      => "VARCHAR(64) NULL",
		'workshop_app_id'       => "VARCHAR(32) NULL",
		'target_path_template'  => "VARCHAR(255) NULL",
		'optional_folder_name'  => "VARCHAR(255) NULL",
		'config_edit_rule'      => "TEXT NULL",
		'launch_param_additions'=> "VARCHAR(255) NULL",
	);
	foreach ($new_columns as $col => $definition) {
		$escaped_col   = $db->realEscapeSingle($col);
		$escaped_table = $db->realEscapeSingle($prefix . 'addons');
		$check = $db->resultQuery(
			"SELECT 1 FROM INFORMATION_SCHEMA.COLUMNS
			  WHERE TABLE_SCHEMA = DATABASE()
			    AND TABLE_NAME   = '{$escaped_table}'
			    AND COLUMN_NAME  = '{$escaped_col}'"
		);
		if (empty($check)) {
			$db->query("ALTER TABLE `{$prefix}addons` ADD COLUMN `{$col}` {$definition}");
		}
	}

	// ── Per-server manifest ───────────────────────────────────────────────────
	$db->query(
		"CREATE TABLE IF NOT EXISTS `{$prefix}server_content_manifest` (
			`id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`home_id`         INT NOT NULL,
			`addon_id`        INT NOT NULL,
			`install_method`  VARCHAR(32)  NOT NULL DEFAULT 'download_zip',
			`content_version` VARCHAR(64)  NULL,
			`install_state`   VARCHAR(32)  NOT NULL DEFAULT 'installed',
			`checksum_sha256` VARCHAR(64)  NULL,
			`source_url`      VARCHAR(255) NULL,
			`installed_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`installed_by`    INT          NULL,
			`updated_at`      DATETIME     NULL,
			`notes`           TEXT         NULL,
			UNIQUE KEY `uniq_home_addon`  (`home_id`, `addon_id`),
			KEY `idx_home_id`             (`home_id`),
			KEY `idx_addon_id`            (`addon_id`),
			KEY `idx_install_state`       (`install_state`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
	);

	// ── Install history ───────────────────────────────────────────────────────
	$db->query(
		"CREATE TABLE IF NOT EXISTS `{$prefix}server_content_install_history` (
			`id`              INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
			`home_id`         INT          NOT NULL,
			`addon_id`        INT          NOT NULL,
			`installed_by`    INT          NULL,
			`started_at`      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
			`completed_at`    DATETIME     NULL,
			`install_state`   VARCHAR(32)  NOT NULL DEFAULT 'started',
			`install_method`  VARCHAR(32)  NULL,
			`content_version` VARCHAR(64)  NULL,
			`source_url`      VARCHAR(255) NULL,
			`cache_mode_used` VARCHAR(32)  NULL,
			`result_code`     INT          NULL,
			`log_output`      MEDIUMTEXT   NULL,
			KEY `idx_home_id`    (`home_id`),
			KEY `idx_addon_id`   (`addon_id`),
			KEY `idx_started_at` (`started_at`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
	);

	return true;
}

/**
 * Returns all manifest rows for a given server home.
 *
 * @param  object $db
 * @param  int    $home_id
 * @return array
 */
function scm_get_manifest_rows($db, $home_id)
{
	$home_id = (int)$home_id;
	if ($home_id <= 0 || !scm_ensure_phase2_schema($db)) {
		return array();
	}
	$rows = $db->resultQuery(
		"SELECT m.*, a.name AS addon_name, a.addon_type, a.install_method AS addon_install_method
		   FROM `".OGP_DB_PREFIX."server_content_manifest` m
		   LEFT JOIN `".OGP_DB_PREFIX."addons` a ON a.addon_id = m.addon_id
		  WHERE m.home_id = {$home_id}
		  ORDER BY m.installed_at DESC"
	);
	return is_array($rows) ? $rows : array();
}

/**
 * Creates a new install history row and returns its insert ID.
 * Returns 0 on failure.
 *
 * @param  object $db
 * @param  int    $home_id
 * @param  int    $addon_id
 * @param  int    $user_id
 * @param  string $source_url
 * @param  string $content_version
 * @param  string $install_method
 * @param  string $cache_mode_used
 * @return int    history row ID, or 0 on failure
 */
function scm_record_install_start($db, $home_id, $addon_id, $user_id, $source_url = '', $content_version = '', $install_method = 'download_zip', $cache_mode_used = 'disabled')
{
	$home_id         = (int)$home_id;
	$addon_id        = (int)$addon_id;
	$user_id         = (int)$user_id;
	$source_url      = $db->realEscapeSingle((string)$source_url);
	$content_version = $db->realEscapeSingle((string)$content_version);
	$install_method  = $db->realEscapeSingle((string)$install_method);
	$cache_mode_used = $db->realEscapeSingle((string)$cache_mode_used);

	if (!scm_ensure_phase2_schema($db)) {
		return 0;
	}
	$id = $db->resultInsertId(
		'server_content_install_history',
		array(
			'home_id'         => $home_id,
			'addon_id'        => $addon_id,
			'installed_by'    => $user_id,
			'install_state'   => 'started',
			'install_method'  => $install_method,
			'content_version' => $content_version,
			'source_url'      => $source_url,
			'cache_mode_used' => $cache_mode_used,
		)
	);
	return is_numeric($id) ? (int)$id : 0;
}

/**
 * Updates an existing install history row with the final result.
 *
 * @param  object $db
 * @param  int    $history_id
 * @param  string $state       'installed' | 'failed' | 'cancelled'
 * @param  int    $result_code Exit code (0 = success)
 * @param  string $log_output  Script/download log snippet
 * @return bool
 */
function scm_record_install_done($db, $history_id, $state = 'installed', $result_code = 0, $log_output = '')
{
	$history_id  = (int)$history_id;
	$state       = $db->realEscapeSingle((string)$state);
	$result_code = (int)$result_code;
	$log_output  = $db->realEscapeSingle((string)$log_output);
	if ($history_id <= 0) {
		return false;
	}
	return (bool)$db->query(
		"UPDATE `".OGP_DB_PREFIX."server_content_install_history`
		    SET install_state = '{$state}',
		        result_code   = {$result_code},
		        log_output    = '{$log_output}',
		        completed_at  = NOW()
		  WHERE id = {$history_id}"
	);
}

/**
 * Inserts or updates a server_content_manifest row for a successful install.
 *
 * @param  object $db
 * @param  int    $home_id
 * @param  int    $addon_id
 * @param  array  $fields  Optional overrides: install_method, content_version,
 *                          install_state, source_url, checksum_sha256, installed_by
 * @return bool
 */
function scm_upsert_manifest($db, $home_id, $addon_id, array $fields = array())
{
	$home_id  = (int)$home_id;
	$addon_id = (int)$addon_id;
	if ($home_id <= 0 || $addon_id <= 0 || !scm_ensure_phase2_schema($db)) {
		return false;
	}
	$install_method  = $db->realEscapeSingle((string)(isset($fields['install_method'])  ? $fields['install_method']  : 'download_zip'));
	$content_version = $db->realEscapeSingle((string)(isset($fields['content_version']) ? $fields['content_version'] : ''));
	$install_state   = $db->realEscapeSingle((string)(isset($fields['install_state'])   ? $fields['install_state']   : 'installed'));
	$source_url      = $db->realEscapeSingle((string)(isset($fields['source_url'])      ? $fields['source_url']      : ''));
	$checksum        = $db->realEscapeSingle((string)(isset($fields['checksum_sha256']) ? $fields['checksum_sha256'] : ''));
	$installed_by    = isset($fields['installed_by']) ? (int)$fields['installed_by'] : 'NULL';
	if ($installed_by !== 'NULL' && $installed_by <= 0) {
		$installed_by = 'NULL';
	}

	return (bool)$db->query(
		"INSERT INTO `".OGP_DB_PREFIX."server_content_manifest`
		    (`home_id`,`addon_id`,`install_method`,`content_version`,`install_state`,`source_url`,`checksum_sha256`,`installed_by`,`installed_at`,`updated_at`)
		 VALUES
		    ({$home_id},{$addon_id},'{$install_method}','{$content_version}','{$install_state}','{$source_url}','{$checksum}',{$installed_by},NOW(),NOW())
		 ON DUPLICATE KEY UPDATE
		    install_method  = VALUES(install_method),
		    content_version = VALUES(content_version),
		    install_state   = VALUES(install_state),
		    source_url      = VALUES(source_url),
		    checksum_sha256 = VALUES(checksum_sha256),
		    installed_at    = NOW(),
		    updated_at      = NOW()"
	);
}
