<?php

$panel_root = realpath(__DIR__ . '/../../');
if ($panel_root === false || !is_dir($panel_root)) {
	http_response_code(500);
	exit;
}

require_once($panel_root . '/includes/functions.php');
require_once($panel_root . '/includes/helpers.php');
require_once($panel_root . '/includes/config.inc.php');
require_once($panel_root . '/modules/config_games/server_config_parser.php');
require_once($panel_root . '/includes/lib_remote.php');

startSession();

header('Content-Type: text/plain; charset=UTF-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (!isset($_SESSION['user_id'])) {
	http_response_code(403);
	exit;
}

$home_id = isset($_GET['home_id']) ? (int)$_GET['home_id'] : 0;
$mod_id = isset($_GET['mod_id']) ? (int)$_GET['mod_id'] : 0;
$raw_ip = isset($_GET['ip']) ? trim($_GET['ip']) : '';
$port = isset($_GET['port']) ? (int)$_GET['port'] : 0;

if ($home_id <= 0 || $mod_id <= 0) {
	http_response_code(400);
	exit;
}
if ($raw_ip !== '' && filter_var($raw_ip, FILTER_VALIDATE_IP) === false) {
	http_response_code(400);
	exit;
}
$ip = $raw_ip !== '' ? sanitizeInputStr($raw_ip) : '';

$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix, isset($db_port) ? $db_port : NULL);
if (!$db instanceof OGPDatabase) {
	http_response_code(500);
	exit;
}

$user_id = (int)$_SESSION['user_id'];
$isAdmin = $db->isAdmin($user_id);
$home_info = $isAdmin ? $db->getGameHome($home_id) : $db->getUserGameHome($user_id, $home_id);
if ($home_info === FALSE) {
	http_response_code(403);
	exit;
}

if ($ip !== '' && $port > 0) {
	$hasMatchingIpPort = false;
	foreach ((array)$home_info['ipports'] as $home_ip_port) {
		if (isset($home_ip_port['ip']) && isset($home_ip_port['port']) && $home_ip_port['ip'] == $ip && (int)$home_ip_port['port'] == $port) {
			$hasMatchingIpPort = true;
			break;
		}
	}
	if (!$hasMatchingIpPort) {
		http_response_code(403);
		exit;
	}
}

$server_xml = read_server_config(SERVER_CONFIG_LOCATION . "/" . $home_info['home_cfg_file']);
if (!$server_xml) {
	http_response_code(404);
	exit;
}

$remote = new OGPRemoteLibrary($home_info['agent_ip'], $home_info['agent_port'], $home_info['encryption_key'], $home_info['timeout']);
$home_log = "";

if (isset($server_xml->console_log)) {
	$log_retval = $remote->get_log(
		OGP_SCREEN_TYPE_HOME,
		$home_info['home_id'],
		clean_path($home_info['home_path']),
		$home_log,
		100,
		(string)$server_xml->console_log
	);
} else {
	$log_retval = $remote->get_log(
		OGP_SCREEN_TYPE_HOME,
		$home_info['home_id'],
		clean_path($home_info['home_path'] . "/" . $server_xml->exe_location),
		$home_log
	);
}

if ($log_retval == 1 || $log_retval == 2) {
	if (hasValue($home_log) && !mb_check_encoding($home_log, 'UTF-8')) {
		if (function_exists('mb_convert_encoding')) {
			$home_log = mb_convert_encoding($home_log, 'UTF-8', 'ISO-8859-1');
		} elseif (function_exists('iconv')) {
			$converted_log = @iconv('ISO-8859-1', 'UTF-8//IGNORE', $home_log);
			if ($converted_log !== false) {
				$home_log = $converted_log;
			}
		}
	}
	echo $home_log;
	exit;
}

http_response_code(204);
exit;
