<?php
/*
 *
 * GSP - Server Content helpers (addonsmanager)
 *
 */

if (!defined('SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT')) {
	define('SCM_WORKSHOP_SCRIPT_LINUX_DEFAULT', '/home/gameserver/OGP_User_Files/modules/addonsmanager/scripts/workshop/generic_steam_workshop_linux.sh');
}
if (!defined('SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT')) {
	define('SCM_WORKSHOP_SCRIPT_WINDOWS_DEFAULT', '/home/gameserver/OGP_User_Files/modules/addonsmanager/scripts/workshop/generic_steam_workshop_windows_cygwin.sh');
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

