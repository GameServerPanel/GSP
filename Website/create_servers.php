<?php
require_once __DIR__ . '/../../includes/lib_remote.php';
require_once __DIR__ . '/../config_games/server_config_parser.php';
require_once __DIR__ . '/../gamemanager/update_actions.php';

if (!defined('BILLING_INSTALL_MECHANISM')) {
	define('BILLING_INSTALL_MECHANISM', 'gamemanager_trigger_update_install');
}
if (!defined('BILLING_CPU_AFFINITY_NA')) {
	define('BILLING_CPU_AFFINITY_NA', 'NA');
}
if (!defined('BILLING_NICE_DEFAULT')) {
	define('BILLING_NICE_DEFAULT', '0');
}
if (!defined('BILLING_PROVISION_TRACE_LOG')) {
	define('BILLING_PROVISION_TRACE_LOG', 'modules/billing/logs/provisioning_trace.log');
}

if (!function_exists('billing_provision_trace_relative_path')) {
	function billing_provision_trace_relative_path(): string
	{
		return BILLING_PROVISION_TRACE_LOG;
	}
}

if (!function_exists('billing_provision_trace_path')) {
	function billing_provision_trace_path(): string
	{
		return __DIR__ . '/logs/provisioning_trace.log';
	}
}

if (!function_exists('billing_set_trace_error')) {
	function billing_set_trace_error(string $message): void
	{
		$GLOBALS['BILLING_PROVISION_TRACE_ERROR'] = $message;
		error_log('billing_provision_trace: ' . $message);
	}
}

if (!function_exists('billing_format_trace_value')) {
	function billing_format_trace_value($value): string
	{
		if (is_bool($value)) {
			return $value ? 'true' : 'false';
		}
		if ($value === null) {
			return 'null';
		}
		if (is_scalar($value)) {
			return (string)$value;
		}
		$json = json_encode($value, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
		return $json === false ? '[unserializable]' : $json;
	}
}

if (!function_exists('billing_ensure_trace_log_ready')) {
	function billing_ensure_trace_log_ready(): array
	{
		$logFile = billing_provision_trace_path();
		$logDir = dirname($logFile);
		if (!is_dir($logDir) && !@mkdir($logDir, 0755, true) && !is_dir($logDir)) {
			$error = "Could not create billing trace log directory: {$logDir}";
			billing_set_trace_error($error);
			return array('ok' => false, 'error' => $error, 'path' => $logFile);
		}
		if (!is_writable($logDir)) {
			$error = "Billing trace log directory is not writable: {$logDir}";
			billing_set_trace_error($error);
			return array('ok' => false, 'error' => $error, 'path' => $logFile);
		}
		if (!file_exists($logFile)) {
			$result = @file_put_contents($logFile, '', FILE_APPEND | LOCK_EX);
			if ($result === false) {
				$error = "Could not create billing trace log file: {$logFile}";
				billing_set_trace_error($error);
				return array('ok' => false, 'error' => $error, 'path' => $logFile);
			}
		}
		if (!is_writable($logFile)) {
			$error = "Billing trace log file is not writable: {$logFile}";
			billing_set_trace_error($error);
			return array('ok' => false, 'error' => $error, 'path' => $logFile);
		}
		return array('ok' => true, 'path' => $logFile);
	}
}

if (!function_exists('billing_provision_trace')) {
	function billing_provision_trace($message, array $context = array()): bool
	{
		$ready = billing_ensure_trace_log_ready();
		if (empty($ready['ok'])) {
			return false;
		}
		$baseContext = isset($GLOBALS['BILLING_PROVISION_TRACE_CONTEXT']) && is_array($GLOBALS['BILLING_PROVISION_TRACE_CONTEXT'])
			? $GLOBALS['BILLING_PROVISION_TRACE_CONTEXT']
			: array();
		$merged = array_merge($baseContext, $context);
		$line = '[' . date('Y-m-d H:i:s') . '] ' . trim((string)$message);
		if (!empty($merged)) {
			$parts = array();
			foreach ($merged as $key => $value) {
				$parts[] = $key . '=' . billing_format_trace_value($value);
			}
			$line .= ' | ' . implode(' | ', $parts);
		}
		$result = @file_put_contents($ready['path'], $line . PHP_EOL, FILE_APPEND | LOCK_EX);
		if ($result === false) {
			billing_set_trace_error('Failed to append billing trace log: ' . $ready['path']);
			return false;
		}
		return true;
	}
}

if (!function_exists('billing_get_server_home_row')) {
	function billing_get_server_home_row($db, string $db_prefix, int $home_id): array
	{
		if ($home_id <= 0) {
			return array();
		}
		$row = $db->resultQuery(
			"SELECT * FROM `{$db_prefix}server_homes`
			 WHERE home_id=" . $db->realEscapeSingle($home_id) . "
			 LIMIT 1"
		);
		return !empty($row[0]) ? (array)$row[0] : array();
	}
}

if (!function_exists('billing_trace_home_info_summary')) {
	function billing_trace_home_info_summary(array $home_info): array
	{
		$mods = array();
		foreach ((array)($home_info['mods'] ?? array()) as $modId => $modInfo) {
			$mods[] = array(
				'mod_id' => intval($modId),
				'mod_key' => $modInfo['mod_key'] ?? '',
			);
		}
		return array(
			'home_id' => intval($home_info['home_id'] ?? 0),
			'home_cfg_id' => intval($home_info['home_cfg_id'] ?? 0),
			'home_cfg_file' => $home_info['home_cfg_file'] ?? '',
			'remote_server_id' => intval($home_info['remote_server_id'] ?? 0),
			'home_path' => $home_info['home_path'] ?? '',
			'agent_ip' => $home_info['agent_ip'] ?? '',
			'agent_port' => $home_info['agent_port'] ?? '',
			'mods' => $mods,
		);
	}
}

if (!function_exists('billing_trace_settings_summary')) {
	function billing_trace_settings_summary(array $settings): array
	{
		return array(
			'panel_name' => $settings['panel_name'] ?? '',
			'steam_user_configured' => !empty($settings['steam_user']),
			'steam_guard_configured' => !empty($settings['steam_guard']),
		);
	}
}

if (!function_exists('billing_detect_install_state')) {
	function billing_detect_install_state(array $home_info): array
	{
		$state = array(
			'complete' => false,
			'remote_status' => 'unknown',
			'update_active' => false,
			'exec_path' => '',
			'exec_exists' => false,
			'reason' => '',
		);
		if (empty($home_info['home_id'])) {
			$state['reason'] = 'home_info is missing home_id.';
			return $state;
		}
		if (empty($home_info['home_cfg_file'])) {
			$state['reason'] = 'home_cfg_file is missing; install completion cannot be verified.';
			return $state;
		}
		$xml_cfg_file = $home_info['home_cfg_file'] ?? '';
		$xml_rel = rtrim(SERVER_CONFIG_LOCATION, '/') . '/' . $xml_cfg_file;
		$xml_abs = $xml_rel;
		if (!is_readable($xml_rel)) {
			$panel_root = realpath(__DIR__ . '/../../');
			if ($panel_root !== false) {
				$xml_abs = $panel_root . '/' . ltrim($xml_rel, '/');
			}
		}
		if (function_exists('billing_provision_trace')) {
			billing_provision_trace('billing_detect_install_state: XML path resolution.', array(
				'home_id'         => intval($home_info['home_id'] ?? 0),
				'home_cfg_file'   => $xml_cfg_file,
				'xml_rel_path'    => $xml_rel,
				'xml_abs_path'    => $xml_abs,
				'cwd'             => getcwd(),
				'xml_file_exists' => file_exists($xml_abs),
				'xml_is_readable' => is_readable($xml_abs),
			));
		}
		$server_xml = read_server_config($xml_abs);
		if (!$server_xml) {
			$state['reason'] = "Could not read server config XML; install completion cannot be verified. Tried: {$xml_abs}";
			return $state;
		}
		$server_exec_name = trim((string)($server_xml->server_exec_name ?? ''));
		if ($server_exec_name === '') {
			$state['reason'] = 'server_exec_name is empty; install completion cannot be verified.';
			return $state;
		}
		$remote = new OGPRemoteLibrary($home_info['agent_ip'], $home_info['agent_port'], $home_info['encryption_key'], $home_info['timeout']);
		$hostStat = $remote->status_chk();
		$state['remote_status'] = ($hostStat === 1) ? 'online' : 'offline';
		if ($hostStat !== 1) {
			$state['reason'] = 'Agent is offline; install completion cannot be verified.';
			return $state;
		}
		$log_txt = '';
		$update_active = $remote->get_log(OGP_SCREEN_TYPE_UPDATE, intval($home_info['home_id']), clean_path($home_info['home_path']), $log_txt);
		$state['update_active'] = ($update_active == 1);
		$state['update_log'] = $log_txt;
		$execFolder = clean_path($home_info['home_path'] . "/" . (string)($server_xml->exe_location ?? ''));
		$execPath = clean_path($execFolder . "/" . $server_exec_name);
		$state['exec_path'] = $execPath;
		$state['exec_exists'] = ($remote->rfile_exists($execPath) === 1);
		$state['complete'] = $state['exec_exists'];
		if ($state['exec_exists']) {
			$state['reason'] = 'Expected executable already exists on the remote server.';
		} elseif (!empty($state['update_active'])) {
			$state['reason'] = 'Server installation is in progress.';
		} else {
			$state['reason'] = 'Expected executable is missing on the remote server.';
		}
		return $state;
	}
}

if (!function_exists('billing_store_provision_session_result')) {
	function billing_store_provision_session_result(string $key, array $payload): void
	{
		if (session_status() === PHP_SESSION_ACTIVE) {
			$_SESSION['billing_provision_results'][$key] = $payload;
		}
	}
}

if (!function_exists('billing_generate_provision_password')) {
	function billing_generate_provision_password()
	{
		$length = 6;
		$alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$alphabetLen = strlen($alphabet);
		$password = '';
		try {
			for ($i = 0; $i < $length; $i++) {
				$password .= $alphabet[random_int(0, $alphabetLen - 1)];
			}
			return $password;
		} catch (Throwable $e) {
			billing_provision_trace('Password generation fallback after exception.', array('exception' => $e->getMessage()));
			for ($i = 0; $i < $length; $i++) {
				$password .= $alphabet[mt_rand(0, $alphabetLen - 1)];
			}
			return $password;
		}
	}
}

if (!function_exists('billing_is_valid_provision_password')) {
	function billing_is_valid_provision_password($value): bool
	{
		return is_string($value) && preg_match('/^[A-Za-z0-9]{6}$/', $value) === 1;
	}
}

if (!function_exists('billing_should_regenerate_provision_password')) {
	function billing_should_regenerate_provision_password($value): bool
	{
		return !billing_is_valid_provision_password($value) || strcasecmp((string)$value, 'ChangeMe') === 0;
	}
}

if (!function_exists('billing_agent_offline_reason')) {
	function billing_agent_offline_reason(int $remote_server_id, array $home_info): string
	{
		return "Agent is offline for remote server #{$remote_server_id} (" . ($home_info['agent_ip'] ?? 'unknown') . ":" . ($home_info['agent_port'] ?? 'unknown') . ").";
	}
}

if (!function_exists('billing_detect_service_os')) {
	function billing_detect_service_os(string $cfg_file, string $game_key): string
	{
		$haystack = strtolower(trim($cfg_file !== '' ? $cfg_file : $game_key));
		if ($haystack === '') {
			return 'any';
		}
		if (preg_match('/(?:^|[_\\-])(win|windows)(?:[_\\-]|$)/i', $haystack)) {
			return 'windows';
		}
		if (preg_match('/(?:^|[_\\-])linux(?:[_\\-]|$)/i', $haystack)) {
			return 'linux';
		}
		return 'any';
	}
}

if (!function_exists('billing_normalize_node_os')) {
	function billing_normalize_node_os(string $server_os): string
	{
		$value = strtolower(trim($server_os));
		if ($value === '' || $value === 'any') {
			return 'any';
		}
		if (str_starts_with($value, 'win')) {
			return 'windows';
		}
		if (str_starts_with($value, 'lin')) {
			return 'linux';
		}
		return $value;
	}
}

if (!function_exists('billing_remote_servers_has_os_column')) {
	function billing_remote_servers_has_os_column($db, string $db_prefix): bool
	{
		static $cache = array();
		if (isset($cache[$db_prefix])) {
			return $cache[$db_prefix];
		}
		$rows = $db->resultQuery("SHOW COLUMNS FROM `{$db_prefix}remote_servers` LIKE 'server_os'");
		$cache[$db_prefix] = !empty($rows);
		return $cache[$db_prefix];
	}
}

if (!function_exists('billing_invoke_provision')) {
	function billing_invoke_provision(array $options = array())
	{
		if (empty($options['caller_source'])) {
			$trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
			$caller = $trace[1] ?? array();
			$options['caller_source'] = ($caller['file'] ?? __FILE__) . ':' . intval($caller['line'] ?? 0);
		}
		unset($GLOBALS['BILLING_PROVISION_TRACE_ERROR'], $GLOBALS['BILLING_PROVISION_TRACE_CONTEXT']);
		$GLOBALS['BILLING_PROVISION_OVERRIDE'] = $options;
		ob_start();
		exec_ogp_module();
		$output = ob_get_clean();
		$result = isset($GLOBALS['BILLING_PROVISION_LAST_RESULT']) ? $GLOBALS['BILLING_PROVISION_LAST_RESULT'] : array();
		$result['output'] = $output;
		$result['trace_log_path'] = billing_provision_trace_relative_path();
		if (!empty($GLOBALS['BILLING_PROVISION_TRACE_ERROR'])) {
			$result['trace_error'] = $GLOBALS['BILLING_PROVISION_TRACE_ERROR'];
		}
		unset($GLOBALS['BILLING_PROVISION_OVERRIDE'], $GLOBALS['BILLING_PROVISION_LAST_RESULT'], $GLOBALS['BILLING_PROVISION_TRACE_ERROR'], $GLOBALS['BILLING_PROVISION_TRACE_CONTEXT']);
		return $result;
	}
}

if (!function_exists('billing_get_remote_ip_ids')) {
	function billing_get_remote_ip_ids($db, string $db_prefix, int $remote_server_id): array
	{
		$rows = $db->resultQuery(
			"SELECT ip_id FROM `{$db_prefix}remote_server_ips` WHERE remote_server_id=" . $db->realEscapeSingle($remote_server_id) . " ORDER BY ip_id ASC"
		);
		$ipIds = array();
		foreach ((array)$rows as $row) {
			$ipId = intval($row['ip_id'] ?? 0);
			if ($ipId > 0) {
				$ipIds[] = $ipId;
			}
		}
		return $ipIds;
	}
}

if (!function_exists('billing_allocate_home_port')) {
	function billing_allocate_home_port($db, string $db_prefix, int $home_id, int $remote_server_id, int $home_cfg_id): array
	{
		$ipIds = billing_get_remote_ip_ids($db, $db_prefix, $remote_server_id);
		billing_provision_trace('Resolved remote server IP IDs for port allocation.', array(
			'home_id' => $home_id,
			'selected_remote_server_id' => $remote_server_id,
			'selected_home_cfg_id' => $home_cfg_id,
			'ip_ids' => $ipIds,
		));
		if (empty($ipIds)) {
			billing_provision_trace('Port allocation failed because no remote_server_ips rows were found.', array(
				'home_id' => $home_id,
				'selected_remote_server_id' => $remote_server_id,
				'selected_home_cfg_id' => $home_cfg_id,
			));
			return array('ok' => false, 'error' => "No IP addresses are configured for remote server #{$remote_server_id}.");
		}

		$ips_with_no_range = array();
		$ips_exhausted = array();
		foreach ($ipIds as $ipId) {
			$ranges = $db->resultQuery(
				"SELECT start_port, end_port, port_increment
				 FROM `{$db_prefix}arrange_ports`
				 WHERE ip_id=" . $db->realEscapeSingle($ipId) . "
				   AND home_cfg_id=" . $db->realEscapeSingle($home_cfg_id) . "
				 ORDER BY range_id ASC"
			);
			if (empty($ranges)) {
				$ranges = $db->resultQuery(
					"SELECT start_port, end_port, port_increment
					 FROM `{$db_prefix}arrange_ports`
					 WHERE ip_id=" . $db->realEscapeSingle($ipId) . "
					   AND home_cfg_id=0
					 ORDER BY range_id ASC"
				);
			}
			if (empty($ranges)) {
				$ips_with_no_range[] = $ipId;
				billing_provision_trace('No port range found for current ip_id.', array(
					'home_id' => $home_id,
					'selected_ip_id' => $ipId,
					'selected_home_cfg_id' => $home_cfg_id,
				));
				continue;
			}
			billing_provision_trace('Loaded port ranges for current ip_id.', array(
				'home_id' => $home_id,
				'selected_ip_id' => $ipId,
				'port_ranges_used' => $ranges,
			));

			$usedRows = $db->resultQuery(
				"SELECT port FROM `{$db_prefix}home_ip_ports` WHERE ip_id=" . $db->realEscapeSingle($ipId)
			);
			$usedPorts = array();
			foreach ((array)$usedRows as $usedRow) {
				$usedPorts[intval($usedRow['port'] ?? 0)] = true;
			}

			foreach ((array)$ranges as $range) {
				$start = intval($range['start_port'] ?? 0);
				$end = intval($range['end_port'] ?? 0);
				$increment = max(1, intval($range['port_increment'] ?? 1));
				if ($start <= 0 || $end <= 0 || $start > $end) {
					continue;
				}

				for ($port = $start; $port <= $end; $port += $increment) {
					if (isset($usedPorts[$port])) {
						continue;
					}
					$safeIpId = $db->realEscapeSingle($ipId);
					$safePort = $db->realEscapeSingle($port);
					$safeHome = $db->realEscapeSingle($home_id);
					$insertOk = $db->query(
						"INSERT INTO `{$db_prefix}home_ip_ports` (`ip_id`, `port`, `home_id`)
						 SELECT {$safeIpId}, {$safePort}, {$safeHome}
						 FROM DUAL
						 WHERE NOT EXISTS (
							SELECT 1
							FROM `{$db_prefix}home_ip_ports`
							WHERE ip_id = {$safeIpId}
							  AND port = {$safePort}
						 )"
					);
					billing_provision_trace('Attempted home_ip_ports insert.', array(
						'home_id' => $home_id,
						'selected_ip_id' => $ipId,
						'selected_port' => $port,
						'home_ip_ports_insert_succeeded' => (bool)$insertOk,
					));
					if (!$insertOk) {
						continue;
					}
					$verify = $db->resultQuery(
						"SELECT home_id FROM `{$db_prefix}home_ip_ports`
						 WHERE ip_id = {$safeIpId}
						   AND port = {$safePort}
						   AND home_id = {$safeHome}
						 LIMIT 1"
					);
					if (!empty($verify)) {
						billing_provision_trace('Port allocation succeeded.', array(
							'home_id' => $home_id,
							'selected_ip_id' => $ipId,
							'selected_port' => $port,
							'home_ip_ports_insert_succeeded' => true,
						));
						return array('ok' => true, 'ip_id' => $ipId, 'port' => intval($port));
					}
				}
			}
			$ips_exhausted[] = $ipId;
		}

		if (!empty($ips_with_no_range) && count($ips_with_no_range) === count($ipIds)) {
			billing_provision_trace('Port allocation failed because no matching arrange_ports ranges were found.', array(
				'home_id' => $home_id,
				'selected_remote_server_id' => $remote_server_id,
				'ips_with_no_range' => $ips_with_no_range,
			));
			return array('ok' => false, 'error' => "No port range found for home_cfg_id #{$home_cfg_id} on ip_id(s) [" . implode(',', $ips_with_no_range) . "] for remote server #{$remote_server_id}.");
		}
		billing_provision_trace('Port allocation failed because all ranges were exhausted.', array(
			'home_id' => $home_id,
			'selected_remote_server_id' => $remote_server_id,
			'selected_home_cfg_id' => $home_cfg_id,
			'ips_exhausted' => !empty($ips_exhausted) ? $ips_exhausted : $ipIds,
		));
		return array('ok' => false, 'error' => "No available port in arrange_ports for remote server #{$remote_server_id}, home_cfg_id #{$home_cfg_id}, ip_id(s) [" . implode(',', !empty($ips_exhausted) ? $ips_exhausted : $ipIds) . "].");
	}
}

if (!function_exists('billing_resolve_mod_cfg_id')) {
	function billing_resolve_mod_cfg_id($db, int $home_cfg_id, int $preferred_mod_cfg_id): array
	{
		$mods = $db->getCfgMods($home_cfg_id);
		billing_provision_trace('Loaded config mods for home_cfg_id.', array(
			'selected_home_cfg_id' => $home_cfg_id,
			'preferred_mod_cfg_id' => $preferred_mod_cfg_id,
			'cfg_mod_rows' => $mods,
		));
		if (empty($mods)) {
			billing_provision_trace('No config mods found for home_cfg_id.', array(
				'selected_home_cfg_id' => $home_cfg_id,
			));
			return array('ok' => false, 'error' => "No config_mods rows found for home_cfg_id #{$home_cfg_id}.");
		}

		$first = null;
		$available_mod_cfg_ids = array();
		foreach ((array)$mods as $mod) {
			$modCfgId = intval($mod['mod_cfg_id'] ?? 0);
			if ($modCfgId <= 0) {
				continue;
			}
			$available_mod_cfg_ids[] = $modCfgId;
			if ($first === null) {
				$first = $modCfgId;
			}
			if ($preferred_mod_cfg_id > 0 && $modCfgId === $preferred_mod_cfg_id) {
				billing_provision_trace('Selected preferred mod_cfg_id for provisioning.', array(
					'selected_home_cfg_id' => $home_cfg_id,
					'selected_mod_cfg_id' => $modCfgId,
				));
				return array('ok' => true, 'mod_cfg_id' => $modCfgId);
			}
		}

		if ($first !== null) {
			billing_provision_trace('Selected fallback mod_cfg_id for provisioning.', array(
				'selected_home_cfg_id' => $home_cfg_id,
				'selected_mod_cfg_id' => $first,
			));
			return array('ok' => true, 'mod_cfg_id' => $first);
		}

		billing_provision_trace('No usable mod_cfg_id was found for provisioning.', array(
			'selected_home_cfg_id' => $home_cfg_id,
			'available_mod_cfg_ids' => $available_mod_cfg_ids,
		));
		return array('ok' => false, 'error' => "No usable mod_cfg_id found for home_cfg_id #{$home_cfg_id}. Available mod_cfg_id values: [" . implode(',', $available_mod_cfg_ids) . "].");
	}
}

if (!function_exists('billing_get_home_ip_port')) {
	function billing_get_home_ip_port($db, string $db_prefix, int $home_id): array
	{
		$row = $db->resultQuery(
			"SELECT ip_id, port
			 FROM `{$db_prefix}home_ip_ports`
			 WHERE home_id=" . $db->realEscapeSingle($home_id) . "
			 ORDER BY ip_id ASC, port ASC
			 LIMIT 1"
		);
		if (!empty($row[0])) {
			return array(
				'ok' => true,
				'ip_id' => intval($row[0]['ip_id'] ?? 0),
				'port' => intval($row[0]['port'] ?? 0),
			);
		}
		return array('ok' => false, 'ip_id' => 0, 'port' => 0);
	}
}

if (!function_exists('billing_write_provision_log')) {
	/**
	 * Writes one JSON line per provisioning attempt to modules/billing/logs/provisioning.log.
	 * Fields include order/invoice/user/home/home_cfg/mod/ip/port/mechanism/install_result/error/message.
	 */
	function billing_write_provision_log(array $context): void
	{
		$logDir = __DIR__ . '/logs';
		if (!is_dir($logDir)) {
			mkdir($logDir, 0755, true);
		}
		$status = strtoupper((string)($context['install_result'] ?? 'INFO'));
		$line = '[' . date('Y-m-d H:i:s') . '] [' . $status . '] ' . json_encode($context, JSON_UNESCAPED_SLASHES) . PHP_EOL;
		$result = file_put_contents($logDir . '/provisioning.log', $line, FILE_APPEND | LOCK_EX);
		if ($result === false) {
			error_log('billing_write_provision_log: failed to append provisioning.log');
		}
	}
}

function exec_ogp_module()
{
	global $db,$view,$settings,$table_prefix;
	$db_prefix = isset($table_prefix) ? $table_prefix : '';

	// $now is used in multiple branches below — define it once here so it is
	// always a string that date() / strtotime() can handle safely (PHP 8 fix).
	$now = date('Y-m-d H:i:s');

	$override = isset($GLOBALS['BILLING_PROVISION_OVERRIDE']) ? $GLOBALS['BILLING_PROVISION_OVERRIDE'] : null;
	$user_id = isset($override['user_id']) ? intval($override['user_id']) : (isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0);
	$isAdmin = isset($override['is_admin']) ? (bool)$override['is_admin'] : $db->isAdmin($user_id);
	$provision_all = $override ? !empty($override['provision_all']) : isset($_POST['provision_all']);
	$caller_source = $override['caller_source'] ?? (__FILE__ . ':0');
	$dbNameRow = $db->resultQuery("SELECT DATABASE() AS db_name");
	$active_db_name = $dbNameRow[0]['db_name'] ?? '';
	$orderIds = array();
	if ($override && !empty($override['order_ids'])) {
		$orderIds = array_map('intval', (array)$override['order_ids']);
	}
	if (empty($orderIds)) {
		$order_id = null;
		if (isset($_POST['order_id'])) {
			$order_id = $_POST['order_id'];
		}
		if(isset($_GET['order_id'])){
			$order_id = $_GET['order_id'];
		}
		if (!empty($order_id)) {
			$orderIds = array(intval($order_id));
		}
	}
	$traceReady = billing_ensure_trace_log_ready();
	if (empty($traceReady['ok'])) {
		echo "<div class='failure'><p>" . htmlspecialchars((string)$traceReady['error'], ENT_QUOTES, 'UTF-8') . "</p></div>";
		$GLOBALS['BILLING_PROVISION_LAST_RESULT'] = array(
			'provisioned_count' => 0,
			'failed_count' => 1,
			'orders' => array(),
			'details' => array(),
			'trace_log_path' => billing_provision_trace_relative_path(),
			'trace_error' => $traceReady['error'],
		);
		return;
	}
	billing_provision_trace('START provisioning attempt', array(
		'caller_source' => $caller_source,
		'order_ids_received' => $orderIds,
		'user_id_received' => $user_id,
		'is_admin' => $isAdmin,
		'provision_all' => $provision_all,
		'active_db_name' => $active_db_name,
		'db_prefix' => $db_prefix,
	));
	
	// Handle provision_all request - provision all Active (paid) orders for this user
	if ($provision_all) {
		if ( $isAdmin ){
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE status='Active' ORDER BY order_id" );
		} else {
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE user_id=".$db->realEscapeSingle($user_id)." AND status='Active' ORDER BY order_id" );
		}
		billing_provision_trace('Loaded orders for provision_all request.', array('loaded_order_count' => count((array)$orders)));
	}
	// Handle provision_single or order_id parameter - provision specific order
	else {
		if (empty($orderIds)) {
			billing_provision_trace('END failure: provisioning request returned early because no order ID was supplied.');
			echo "<div class='failure'>No order ID specified.</div>";
			$GLOBALS['BILLING_PROVISION_LAST_RESULT'] = array('provisioned_count'=>0,'failed_count'=>0,'orders'=>array(),'details'=>array(),'trace_log_path'=>billing_provision_trace_relative_path());
			return;
		}
		$idList = implode(',', array_map('intval', $orderIds));
		if ( $isAdmin ){
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE order_id IN ($idList) AND status='Active'" );
		} else {
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE order_id IN ($idList) AND user_id=".$db->realEscapeSingle($user_id)." AND status='Active'" );
		}
		billing_provision_trace('Loaded explicit order list for provisioning.', array('loaded_order_count' => count((array)$orders)));
	}
	$processed_orders = array();
	$order_results = array();
	if( !empty($orders) )
	{
		$provisioned_count = 0;
		$failed_count = 0;
		$failed_messages = array();
		
		foreach ((array)$orders as $order)
		{
			$trace_id = uniqid('prov_', true);
			$GLOBALS['BILLING_PROVISION_TRACE_CONTEXT'] = array(
				'trace_id' => $trace_id,
				'order_id' => intval($order['order_id'] ?? 0),
			);
			billing_provision_trace('Loaded billing order for provisioning.', array('order_row' => $order));
			try {
			$home_id = 0;
			$order_failed = false;
			$order_failure_reason = '';
			$end_date = null;
			$end_date_str = null;
			$order_id = $order['order_id'];
			$processed_orders[] = intval($order_id);
			$service_id = $order['service_id'];
			$home_name = $order['home_name'];
			$remote_control_password = $order['remote_control_password'];
			$ftp_password = $order['ftp_password'];
			if (billing_should_regenerate_provision_password($remote_control_password)) {
				$remote_control_password = billing_generate_provision_password();
			}
			if (billing_should_regenerate_provision_password($ftp_password)) {
				$ftp_password = billing_generate_provision_password();
			}
			$ip = $order['ip'];
			$max_players = $order['max_players'];
			$user_id = $order['user_id'];
			$extended = isset($order['extended']) && $order['extended'] == "1" ? TRUE : FALSE;
			$already_provisioned = !$extended && intval($order['home_id'] ?? 0) > 0;
			$provision_invoice_id = 0;
			$selected_ip_id = 0;
			$selected_port = 0;
			$selected_mod_id = 0;
			$resolved_mod_cfg_id = 0;
			$home_cfg_id = 0;
			$mod_cfg_id = 0;
			$selected_config_xml = '';
			$selected_game_key = '';
			$selected_service_os = 'any';
			$install_mechanism = BILLING_INSTALL_MECHANISM;
			$install_result = 'pending';
			$install_message = '';
			$install_attempted = false;
			$needs_existing_home_retry = false;
			$skip_reason = '';
			$home_info = array();
			$home_row_before = array();
			$home_row_after = array();
			$install_state = array();
			$autoInstall = array();
			$invoiceRow = $db->resultQuery(
				"SELECT invoice_id
				 FROM `{$db_prefix}billing_invoices`
				 WHERE order_id=" . $db->realEscapeSingle($order_id) . "
				 ORDER BY invoice_id DESC
				 LIMIT 1"
			);
			if (!empty($invoiceRow[0]['invoice_id'])) {
				$provision_invoice_id = intval($invoiceRow[0]['invoice_id']);
			}
			billing_provision_trace('Resolved latest invoice row for order.', array('invoice_row' => $invoiceRow));
			//Query service info	
			$service = $db->resultQuery(
				"SELECT bs.*, ch.home_cfg_file, ch.game_key
				 FROM `{$db_prefix}billing_services` bs
				 LEFT JOIN `{$db_prefix}config_homes` ch ON ch.home_cfg_id = bs.home_cfg_id
				 WHERE bs.service_id=" . $db->realEscapeSingle($service_id)
			);
			billing_provision_trace('Loaded billing service row.', array(
				'service_id' => intval($service_id),
				'service_row_found' => !empty($service[0]),
				'service_row' => !empty($service[0]) ? $service[0] : array(),
			));
							   
			if( !empty( $service[0] ) )
			{
				$home_cfg_id = $service[0]['home_cfg_id'];
				$mod_cfg_id = $service[0]['mod_cfg_id'];
				$selected_config_xml = (string)($service[0]['home_cfg_file'] ?? '');
				$selected_game_key = (string)($service[0]['game_key'] ?? '');
				$selected_service_os = billing_detect_service_os($selected_config_xml, $selected_game_key);
				//remote_server_id has been stored in IP_ID
				//$remote_server_id = $service[0]['remote_server_id'];
				$remote_server_id = $order['ip'];	
				
				$ftp = $service[0]['ftp'];
				$install_method = $service[0]['install_method'];
				$manual_url = $service[0]['manual_url'];
				$access_rights = $service[0]['access_rights'];
				billing_provision_trace('Provisioning inputs resolved from order and service.', array(
					'service_id' => intval($service_id),
					'order_status' => $order['status'] ?? '',
					'order_home_id_before_provisioning' => intval($order['home_id'] ?? 0),
					'selected_home_cfg_id' => intval($home_cfg_id),
					'selected_config_xml' => $selected_config_xml,
					'selected_service_os' => $selected_service_os,
					'selected_remote_server_id' => intval($remote_server_id),
				));
				if (intval($home_cfg_id) <= 0) {
					$order_failed = true;
					$order_failure_reason = "Invalid home_cfg_id '{$home_cfg_id}' for service_id {$service_id}.";
				}
				if (!$order_failed && intval($remote_server_id) <= 0) {
					$order_failed = true;
					$order_failure_reason = "Invalid remote server selection '{$remote_server_id}' on order #{$order_id} for service_id {$service_id}.";
				}
				if (!$order_failed) {
					$allowedRemote = array();
					foreach (explode(',', (string)($service[0]['remote_server_id'] ?? '')) as $part) {
						$part = trim($part);
						if ($part !== '' && ctype_digit($part)) {
							$allowedRemote[(int)$part] = true;
						}
					}
					if (!empty($allowedRemote) && !isset($allowedRemote[intval($remote_server_id)])) {
						$order_failed = true;
						$order_failure_reason = "Selected remote server #{$remote_server_id} is not enabled for service_id {$service_id}.";
					}
				}
				if (!$order_failed && billing_remote_servers_has_os_column($db, $db_prefix)) {
					$remoteRow = $db->resultQuery(
						"SELECT remote_server_id, remote_server_name, server_os
						 FROM `{$db_prefix}remote_servers`
						 WHERE remote_server_id=" . $db->realEscapeSingle($remote_server_id) . "
						 LIMIT 1"
					);
					if (empty($remoteRow[0])) {
						$order_failed = true;
						$order_failure_reason = "Remote server #{$remote_server_id} not found for order #{$order_id} (service_id {$service_id}).";
					} else {
						$node_os = billing_normalize_node_os((string)($remoteRow[0]['server_os'] ?? 'any'));
						billing_provision_trace('Resolved remote server OS for compatibility check.', array(
							'selected_remote_server_id' => intval($remote_server_id),
							'selected_node_os' => $node_os,
							'selected_service_os' => $selected_service_os,
						));
						if ($selected_service_os !== 'any' && $node_os !== 'any' && $selected_service_os !== $node_os) {
							$order_failed = true;
							$order_failure_reason = $selected_service_os === 'windows'
								? 'This service requires a Windows server location.'
								: 'This service requires a Linux server location.';
						}
					}
				}
			}
			else
			{
				$order_failed = true;
				$order_failure_reason = "Service ID {$service_id} not found.";
				billing_provision_trace('Eligibility skip: billing service row is missing.', array('service_id' => intval($service_id)));
			}
						
			if(!$order_failed && $already_provisioned)
			{
				$home_id = intval($order['home_id']);
				$home_row_before = billing_get_server_home_row($db, $db_prefix, $home_id);
				billing_provision_trace('Existing home_id detected on billing order.', array(
					'order_home_id_before_provisioning' => intval($order['home_id'] ?? 0),
					'server_homes_row_before' => $home_row_before,
				));
				$home_info = $db->getGameHome($home_id);
				if (empty($home_row_before) || empty($home_info)) {
					$order_failed = true;
					$order_failure_reason = "Order #{$order_id} references home_id {$home_id} but server_homes row is missing.";
					$db->logger('BILLING PROVISION DATA INTEGRITY ERROR: ' . $order_failure_reason);
					billing_provision_trace('Eligibility failure: existing home_id is linked but server_homes row is missing.', array('home_id_after_creation_or_lookup' => $home_id));
				}
				$existingIpPort = billing_get_home_ip_port($db, $db_prefix, intval($home_id));
				if (!empty($existingIpPort['ok'])) {
					$selected_ip_id = intval($existingIpPort['ip_id']);
					$selected_port = intval($existingIpPort['port']);
				}
				billing_provision_trace('Existing home IP:port lookup completed.', array(
					'home_id_after_creation_or_lookup' => $home_id,
					'ip_port_row_found' => !empty($existingIpPort['ok']),
					'selected_ip_id' => intval($selected_ip_id),
					'selected_port' => intval($selected_port),
				));
				$has_ip_port = !empty($existingIpPort['ok']);
				$has_mods = !empty($home_info['mods']) && is_array($home_info['mods']);
				if (!$order_failed && (!$has_ip_port || !$has_mods)) {
					$needs_existing_home_retry = true;
					$install_message = "Existing home #{$home_id} requires provisioning completion (ip_port=" . ($has_ip_port ? 'yes' : 'no') . ", mods=" . ($has_mods ? 'yes' : 'no') . ").";
					billing_provision_trace('Existing home requires provisioning retry because prerequisites are incomplete.', array(
						'home_id_after_creation_or_lookup' => $home_id,
						'has_ip_port' => $has_ip_port,
						'has_mods' => $has_mods,
					));
				}
				if (!$order_failed && !$needs_existing_home_retry) {
					$install_state = billing_detect_install_state((array)$home_info);
					billing_provision_trace('Existing home install state verification completed.', array(
						'home_id_after_creation_or_lookup' => $home_id,
						'install_state' => $install_state,
					));
					if (empty($install_state['complete'])) {
						$needs_existing_home_retry = true;
						$install_message = "Existing home #{$home_id} is not fully installed yet. " . ($install_state['reason'] ?? 'Install completion could not be verified.');
					}
				}
				if (!$order_failed && !$needs_existing_home_retry && !empty($install_state['complete'])) {
					$install_result = 'completed';
					$install_message = $install_message !== '' ? $install_message : "Order #{$order_id} already provisioned and installed; no action required.";
					$skip_reason = $install_message;
					billing_provision_trace('Eligibility skip: existing home is already provisioned and install is complete.', array(
						'home_id_after_creation_or_lookup' => $home_id,
						'skip_reason' => $skip_reason,
					));
				}
			}
			elseif(!$order_failed && $extended)
			{
				$home_id = $order['home_id'];
				billing_provision_trace('Processing renewal for existing billing order.', array(
					'home_id_after_creation_or_lookup' => intval($home_id),
				));
				
				//Get The home info without mods in 1 array (Necesary for remote connection).
				$home_info = $db->getGameHomeWithoutMods($home_id);
				$home_row_before = billing_get_server_home_row($db, $db_prefix, intval($home_id));
				billing_provision_trace('Loaded renewal home state.', array(
					'server_homes_row_before' => $home_row_before,
					'home_info_summary' => billing_trace_home_info_summary((array)$home_info),
				));
				
				//Create the remote connection
				$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
				
				//Reassign the server
				$db->assignHomeTo( "user", $user_id, $home_id, $access_rights );
				
				//Reenable the FTP account
				if ($ftp == "enabled")
				{
					$remote->ftp_mgr("useradd", $home_info['home_id'], $home_info['ftp_password'], $home_info['home_path']);
					$db->changeFtpStatus('enabled',$home_info['home_id']);
				}
				echo "<h4>Server Installed, Check your Email for Details</h4><br>";

//Panel Log
  $db->logger( "RENEWED SERVER " . $home_id);
// SEND EMAIL
 $settings = $db->getSettings();
         $subject = "Gameserver Renewel at " . $settings['panel_name'];
      $email = $db->resultQuery("   SELECT DISTINCT users_email
                           FROM {$table_prefix}users, {$table_prefix}billing_orders
                           WHERE {$table_prefix}users.user_id = $user_id")[0]["users_email"];

      $message = "Your server, " . $home_name ." ID #". $home_id . " at " . $settings['panel_name'] . "  has just been renewed.<br>
                  Thank You for your continued support.<br>
				  If you have any questions or requests, visit our website  or contact us directly in our Discord Server.";

      $mail = mymail($email, $subject, $message, $settings);
	  $rundate = date('d/M/y G:i', is_numeric($now) ? (int)$now : strtotime($now));

 if (!$mail)
      $db->logger( "Email FAILED - Server Renewed " . $home_id);
// END EMAIL

  //WEBHOOK Discord
               discordmsg(array('content' => "The ". $home_name ." server ID #". $home_id . " has just been renewed."), $settings['discord_webhook_main'] ?? '');
               //end WEBHOOK Discord

			}
			elseif(!$order_failed)
			{
				billing_provision_trace('Provisioning new home because billing order has no completed install yet.', array(
					'order_home_id_before_provisioning' => intval($order['home_id'] ?? 0),
				));
				//OPTIONS, change it at your choice;
				$extra_params = "";//no extra params defined by default
				$cpu_affinity = "NA";//Affinity to one core/thread of the cpu by number, use NA to disable it
				$nice = "0";//Min priority=19 Max Priority=-19
				
				//Add Game home to database
				//HARD CODE TO /home/gameserver/
				$rserver = $db->getRemoteServer($remote_server_id);
				if (empty($rserver)) {
					$order_failed = true;
					$order_failure_reason = "Remote server #{$remote_server_id} not found for order #{$order_id} (service_id {$service_id}).";
					billing_provision_trace('Eligibility failure: selected remote server row is missing.', array(
						'selected_remote_server_id' => intval($remote_server_id),
					));
				}
				$game_path = "/home/gameserver/";
				if (!$order_failed) {
					$home_id = $db->addGameHome($remote_server_id, $user_id, $home_cfg_id, $game_path, $home_name, $remote_control_password, $ftp_password);
					billing_provision_trace('Attempted server_homes creation for billing order.', array(
						'selected_remote_server_id' => intval($remote_server_id),
						'selected_home_cfg_id' => intval($home_cfg_id),
						'home_id_after_creation_or_lookup' => intval($home_id),
					));
				}
				if (!$order_failed && (!$home_id || intval($home_id) <= 0)) {
					$order_failed = true;
					$order_failure_reason = "Could not create server_homes row for order #{$order_id}.";
				}
				if (!$order_failed) {
					$home_row_before = billing_get_server_home_row($db, $db_prefix, intval($home_id));
					billing_provision_trace('Loaded server_homes row immediately after creation.', array(
						'server_homes_row_before' => $home_row_before,
					));
				}
				if (!$order_failed) {
					// Billing storefront defaults FTP to enabled for newly provisioned homes so panel/account flows stay consistent after checkout.
					$db->changeFtpStatus('enabled', intval($home_id));
				}
				
				// Add IP:Port pair with arrange_ports exact home_cfg_id preference and home_cfg_id=0 fallback.
				if (!$order_failed) {
					$allocatedPort = billing_allocate_home_port($db, $db_prefix, intval($home_id), intval($remote_server_id), intval($home_cfg_id));
					if (empty($allocatedPort['ok'])) {
						$order_failed = true;
						$order_failure_reason = (string)($allocatedPort['error'] ?? 'Port allocation failed.');
						$db->logger("Provisioning pending install for order #{$order_id}: {$order_failure_reason}");
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					} else {
						$selected_ip_id = intval($allocatedPort['ip_id'] ?? 0);
						$selected_port = intval($allocatedPort['port'] ?? 0);
						billing_provision_trace('Selected IP:port for new home.', array(
							'selected_ip_id' => $selected_ip_id,
							'selected_port' => $selected_port,
						));
					}
				}
				
				//Assign the Game Mod to the Game Home
				$resolved_mod_cfg_id = intval($mod_cfg_id);
				if (!$order_failed) {
					$modResolution = billing_resolve_mod_cfg_id($db, intval($home_cfg_id), intval($mod_cfg_id));
					if (empty($modResolution['ok'])) {
						$order_failed = true;
						$order_failure_reason = (string)($modResolution['error'] ?? 'No mod profile available for base install.');
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					} else {
						$resolved_mod_cfg_id = intval($modResolution['mod_cfg_id']);
					}
				}
				$mod_id = false;
				if (!$order_failed) {
					$mod_id = $db->addModToGameHome( $home_id, $resolved_mod_cfg_id );
					billing_provision_trace('Attempted game_mods attach for home.', array(
						'home_id_after_creation_or_lookup' => intval($home_id),
						'selected_mod_cfg_id' => intval($resolved_mod_cfg_id),
						'selected_mod_id' => intval($mod_id),
					));
					if ($mod_id === false) {
						$order_failed = true;
						$order_failure_reason = "Could not attach mod_cfg_id {$resolved_mod_cfg_id} to home #{$home_id}.";
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					}
				}
				if (!$order_failed) {
					$db->updateGameModParams( $max_players, $extra_params, $cpu_affinity, $nice, $home_id, $resolved_mod_cfg_id );
					$db->assignHomeTo( "user", $user_id, $home_id, $access_rights );
					$selected_mod_id = intval($mod_id);
					billing_provision_trace('Updated game_mod params and assigned home to user.', array(
						'home_id_after_creation_or_lookup' => intval($home_id),
						'selected_mod_id' => intval($selected_mod_id),
						'user_id_received' => intval($user_id),
					));
				}
				
				//Get The home info without mods in 1 array (Necesary for remote connection).
				if (!$order_failed) {
					$home_info = $db->getGameHomeWithoutMods($home_id);
					if (empty($home_info)) {
						$order_failed = true;
						$order_failure_reason = "Could not load home info for home #{$home_id}.";
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					}
				}
				
				//Create the remote connection
				if (!$order_failed) {
					$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
				}
								
				//Get Full home info in 1 array
				if (!$order_failed) {
					$home_info = $db->getGameHome($home_id);
					if (empty($home_info) || empty($home_info['mods'])) {
						$order_failed = true;
						$order_failure_reason = "Mods are not configured for home #{$home_id}; base install profile could not be resolved.";
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					}
				}
				
				//Enable FTP account in remote server
				if (!$order_failed && $ftp == "enabled")
				{
					$remote->ftp_mgr("useradd", $home_info['home_id'], $home_info['ftp_password'], $home_info['home_path']);
					$db->changeFtpStatus('enabled',$home_info['home_id']);
					billing_provision_trace('Enabled FTP account for provisioned home.', array(
						'home_id_after_creation_or_lookup' => intval($home_info['home_id']),
					));
				}

				if (!$order_failed) {
					$install_attempted = true;
					billing_provision_trace('Calling gamemanager_trigger_update_install for newly provisioned home.', array(
						'exact_call' => "gamemanager_trigger_update_install(\$db, \$home_info, {$mod_id}, ['settings' => ...])",
						'home_id_after_creation_or_lookup' => intval($home_id),
						'selected_mod_id' => intval($mod_id),
						'home_info_summary' => billing_trace_home_info_summary((array)$home_info),
						'selected_settings' => billing_trace_settings_summary((array)$settings),
					));
					$autoInstall = gamemanager_trigger_update_install(
						$db,
						$home_info,
						intval($mod_id),
						array('settings' => $settings)
					);
					billing_provision_trace('gamemanager_trigger_update_install returned for newly provisioned home.', array(
						'home_id_after_creation_or_lookup' => intval($home_id),
						'selected_mod_id' => intval($mod_id),
						'gamemanager_trigger_update_install_result' => $autoInstall,
					));
					$mod_id = intval($autoInstall['mod_id'] ?? $mod_id);
					$selected_mod_id = intval($mod_id);
					$install_message = (string)($autoInstall['message'] ?? '');
					if (!empty($autoInstall['already_running'])) {
						$install_result = 'already_running';
					} elseif (!empty($autoInstall['started'])) {
						$install_result = 'started';
					} elseif (!empty($autoInstall['completed'])) {
						$install_result = 'completed';
					} else {
						$install_result = 'pending';
					}
					if (empty($autoInstall['ok'])) {
						if (stripos((string)($autoInstall['message'] ?? ''), 'Agent is offline') !== false) {
							$order_failure_reason = billing_agent_offline_reason(intval($remote_server_id), (array)$home_info);
						}
						$order_failed = true;
						$order_failure_reason = $order_failure_reason !== '' ? $order_failure_reason : ("Server files have not been installed yet. " . ($autoInstall['message'] ?? 'Auto install could not be started.'));
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					}
				}
				if (!$order_failed) {
					echo "<h4><br><p>".get_lang('starting_installations')."</p></h4><br>";
					//PANEL LOG 
	                                $db->logger( "CREATED NEW SERVER " . $home_id);
				}
				// SEND EMAIL to new server only
				if(!$order_failed && $order['end_date'] == 0){
					$settings = $db->getSettings();
					 $subject = "New Gameserver installed at " . $settings['panel_name'];
					  $email = $db->resultQuery("   SELECT DISTINCT users_email
										   FROM {$table_prefix}users, {$table_prefix}billing_orders
										   WHERE {$table_prefix}users.user_id = $user_id")[0]["users_email"];

					  $message =  "Your server, " . $home_name ." ID #". $home_id . " at " . $settings['panel_name'] . "  has just been created.<br>
					               Thank You for your continued support.<br>
                                   If you have any questions or requests, visit our website  or contact us directly in our Discord Server.
								  You can login to the Game Panel and click on Game Monitor to see your server.  <br><br>
								  Thank you!<br> ";
					  $mail = mymail($email, $subject, $message, $settings);
				  	  $rundate = date('d/M/y G:i', is_numeric($now) ? (int)$now : strtotime($now));

					  if (!$mail)
						  $db->logger( "Email FAILED - Server Created " . $home_id);

					  
	//WEBHOOK Discord
               discordmsg(array('content' => "A new server, ". $home_name ." ID #". $home_id . ", has just been created."), $settings['discord_webhook_main'] ?? '');
               //end WEBHOOK Discord
				}
				// END EMAIL
				
				
			}

			// Retry install for orders that already have home_id but never triggered installation.
			if (!$order_failed && !$extended && !$install_attempted && intval($home_id) > 0 && (!$already_provisioned || $needs_existing_home_retry)) {
				billing_provision_trace('Continuing provisioning for existing home because install is incomplete.', array(
					'home_id_after_creation_or_lookup' => intval($home_id),
					'needs_existing_home_retry' => $needs_existing_home_retry,
					'install_message' => $install_message,
				));
				if ($selected_ip_id <= 0 || $selected_port <= 0) {
					$existingIpPort = billing_get_home_ip_port($db, $db_prefix, intval($home_id));
					if (!empty($existingIpPort['ok'])) {
						$selected_ip_id = intval($existingIpPort['ip_id']);
						$selected_port = intval($existingIpPort['port']);
						billing_provision_trace('Reused existing IP:port for existing home retry.', array(
							'selected_ip_id' => $selected_ip_id,
							'selected_port' => $selected_port,
						));
					} else {
						$allocatedPort = billing_allocate_home_port($db, $db_prefix, intval($home_id), intval($remote_server_id), intval($home_cfg_id));
						if (empty($allocatedPort['ok'])) {
							$order_failed = true;
							$order_failure_reason = (string)($allocatedPort['error'] ?? 'Port allocation failed for existing home.');
							$install_result = 'failed';
							$install_message = $order_failure_reason;
						} else {
							$selected_ip_id = intval($allocatedPort['ip_id'] ?? 0);
							$selected_port = intval($allocatedPort['port'] ?? 0);
							billing_provision_trace('Allocated new IP:port for existing home retry.', array(
								'selected_ip_id' => $selected_ip_id,
								'selected_port' => $selected_port,
							));
						}
					}
				}
				if (!$order_failed) {
					if (empty($home_info)) {
						$home_info = $db->getGameHome(intval($home_id));
					}
					if (empty($home_info)) {
						$order_failed = true;
						$order_failure_reason = "Could not load home info for home #{$home_id}.";
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					}
				}
				if (!$order_failed && empty($home_info['mods'])) {
					$modResolution = billing_resolve_mod_cfg_id($db, intval($home_cfg_id), intval($mod_cfg_id));
					if (empty($modResolution['ok'])) {
						$order_failed = true;
						$order_failure_reason = (string)($modResolution['error'] ?? "Mods are not configured for home #{$home_id}; base install profile could not be resolved.");
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					} else {
						$resolved_mod_cfg_id = intval($modResolution['mod_cfg_id']);
						$selected_mod_id = intval($db->addModToGameHome(intval($home_id), intval($resolved_mod_cfg_id)));
						billing_provision_trace('Attempted to attach missing mod during existing home retry.', array(
							'home_id_after_creation_or_lookup' => intval($home_id),
							'selected_mod_cfg_id' => intval($resolved_mod_cfg_id),
							'selected_mod_id' => intval($selected_mod_id),
						));
						if ($selected_mod_id <= 0) {
							$order_failed = true;
							$order_failure_reason = "Could not attach mod_cfg_id {$resolved_mod_cfg_id} to home #{$home_id}.";
							$install_result = 'failed';
							$install_message = $order_failure_reason;
						} else {
							$db->updateGameModParams($max_players, '', BILLING_CPU_AFFINITY_NA, BILLING_NICE_DEFAULT, intval($home_id), intval($resolved_mod_cfg_id));
							$db->assignHomeTo("user", $user_id, intval($home_id), $access_rights);
							$home_info = $db->getGameHome(intval($home_id));
						}
					}
				}
				if (!$order_failed) {
					$selected_mod_id = intval(gamemanager_choose_mod_id((array)$home_info, intval($selected_mod_id)));
					$install_attempted = true;
					billing_provision_trace('Calling gamemanager_trigger_update_install for existing home retry.', array(
						'exact_call' => "gamemanager_trigger_update_install(\$db, \$home_info, {$selected_mod_id}, ['settings' => ...])",
						'home_id_after_creation_or_lookup' => intval($home_id),
						'selected_mod_id' => intval($selected_mod_id),
						'home_info_summary' => billing_trace_home_info_summary((array)$home_info),
						'selected_settings' => billing_trace_settings_summary((array)$settings),
					));
					$autoInstall = gamemanager_trigger_update_install(
						$db,
						(array)$home_info,
						intval($selected_mod_id),
						array('settings' => $settings)
					);
					billing_provision_trace('gamemanager_trigger_update_install returned for existing home retry.', array(
						'home_id_after_creation_or_lookup' => intval($home_id),
						'selected_mod_id' => intval($selected_mod_id),
						'gamemanager_trigger_update_install_result' => $autoInstall,
					));
					$selected_mod_id = intval($autoInstall['mod_id'] ?? $selected_mod_id);
					$install_message = (string)($autoInstall['message'] ?? '');
					if (!empty($autoInstall['already_running'])) {
						$install_result = 'already_running';
					} elseif (!empty($autoInstall['started'])) {
						$install_result = 'started';
					} elseif (!empty($autoInstall['completed'])) {
						$install_result = 'completed';
					} else {
						$install_result = 'pending';
					}
					if (empty($autoInstall['ok'])) {
						if (stripos((string)($autoInstall['message'] ?? ''), 'Agent is offline') !== false) {
							$order_failure_reason = billing_agent_offline_reason(intval($remote_server_id), (array)$home_info);
						}
						$order_failed = true;
						$order_failure_reason = $order_failure_reason !== '' ? $order_failure_reason : ("Server files have not been installed yet. " . ($autoInstall['message'] ?? 'Auto install could not be started.'));
						$install_result = 'failed';
						$install_message = $order_failure_reason;
					}
				}
			}
			// Set expiration date in panel database
			// Status values: Active (provisioned & current), Invoiced (renewal invoice open),
			//                 Expired (past due and awaiting deletion)
			// end_date / next_invoice_date: when the next renewal invoice should be generated
			if ($already_provisioned)
			{
				$existing_end = strtotime((string)($order['end_date'] ?? ''));
				if ($existing_end === false || $existing_end <= 0) {
					$existing_end = time();
				}
				$end_date_str = date('Y-m-d H:i:s', $existing_end);
			}
			else
			{
				$qty_days = max(1, intval($order['qty'])) * 31;
				if (empty($order['end_date']) || $order['end_date'] === NULL) {
					$end_date = strtotime('+' . $qty_days . ' day');
				} else {
					$current_end = strtotime($order['end_date']);
					if ($current_end === false) {
						$current_end = time();
					}
					$end_date = strtotime('+' . $qty_days . ' day', $current_end);
				}
			}
			if (!isset($end_date_str)) {
				$end_date_str = date('Y-m-d H:i:s', $end_date);
			}

			if ($home_id <= 0) {
				$order_failed = true;
				if ($order_failure_reason === '') {
					$order_failure_reason = "No home_id was produced for order #{$order_id}.";
				}
				billing_provision_trace('Eligibility failure: provisioning finished without a valid home_id.', array(
					'home_id_after_creation_or_lookup' => intval($home_id),
				));
			}
			if ($home_id > 0 && empty($home_row_before)) {
				$home_row_before = billing_get_server_home_row($db, $db_prefix, intval($home_id));
			}

			// Set order status to 'Active' (billing active even if install is pending)
			$db->query("UPDATE `{$db_prefix}billing_orders`
						SET status='Active' 
						WHERE order_id=".$db->realEscapeSingle($order_id));
	
			// Set the order expiration / next renewal date
			$db->query("UPDATE `{$db_prefix}billing_orders`
						SET end_date='" . $db->realEscapeSingle($end_date_str) . "',
							remote_control_password='" . $db->realEscapeSingle($remote_control_password) . "',
							ftp_password='" . $db->realEscapeSingle($ftp_password) . "'
						WHERE order_id=".$db->realEscapeSingle($order_id));
						
			// Save home_id created by this order
			$orderHomeUpdateOk = $db->query("UPDATE `{$db_prefix}billing_orders`
						SET home_id='" . $db->realEscapeSingle($home_id) . "' WHERE order_id=".$db->realEscapeSingle($order_id));
			billing_provision_trace('Updated billing_orders.home_id.', array(
				'home_id_after_creation_or_lookup' => intval($home_id),
				'billing_orders_home_id_updated' => (bool)$orderHomeUpdateOk,
			));

			$invoiceHomeUpdateOk = $db->query("UPDATE `{$db_prefix}billing_invoices`
						SET home_id=" . $db->realEscapeSingle($home_id) . ",
							billing_status='Active',
							status='paid'
						WHERE order_id=" . $db->realEscapeSingle($order_id));
			billing_provision_trace('Updated billing_invoices.home_id and billing status.', array(
				'home_id_after_creation_or_lookup' => intval($home_id),
				'billing_invoices_home_id_updated' => (bool)$invoiceHomeUpdateOk,
			));

			$db->query("UPDATE `{$db_prefix}billing_transactions`
						SET home_id=" . $db->realEscapeSingle($home_id) . "
						WHERE invoice_id IN (SELECT invoice_id FROM `{$db_prefix}billing_invoices` WHERE order_id=" . $db->realEscapeSingle($order_id) . ")");

			if ($home_id > 0) {
				$db->query("UPDATE `{$db_prefix}game_mods`
							SET max_players=" . $db->realEscapeSingle($max_players) . "
							WHERE home_id=" . $db->realEscapeSingle($home_id));
			}

			if ($home_id > 0) {
				// Set billing_status, next_invoice_date, and server_expiration_date on server_homes.
				// server_expiration_date must match end_date so the billing cron can determine
				// when to suspend / delete the server.
				$db->query("UPDATE `{$db_prefix}server_homes`
							SET billing_status          = 'Active',
								next_invoice_date       = '" . $db->realEscapeSingle($end_date_str) . "',
								server_expiration_date  = '" . $db->realEscapeSingle($end_date_str) . "',
								billing_enabled         = 1
							WHERE home_id = " . $db->realEscapeSingle($home_id));
				$home_row_after = billing_get_server_home_row($db, $db_prefix, intval($home_id));
				billing_provision_trace('Loaded server_homes row after billing linkage updates.', array(
					'server_homes_row_after' => $home_row_after,
				));
			}

			$provisionContext = array(
				'order_id' => intval($order_id),
				'invoice_id' => intval($provision_invoice_id),
				'user_id' => intval($user_id),
				'service_id' => intval($service_id),
				'home_id' => intval($home_id),
				'home_cfg_id' => intval($home_cfg_id ?? 0),
				'config_xml' => (string)$selected_config_xml,
				'mod_id' => intval($selected_mod_id),
				'ip_id' => intval($selected_ip_id),
				'port' => intval($selected_port),
				'mechanism' => $install_mechanism,
				'install_result' => $order_failed ? 'failed' : (string)$install_result,
				'error' => $order_failed ? (string)$order_failure_reason : '',
				'message' => (string)$install_message,
				'skip_reason' => (string)$skip_reason,
			);
			billing_write_provision_log($provisionContext);
			$db->logger(
				'BILLING PROVISION RESULT order_id=' . intval($order_id)
				. ' invoice_id=' . intval($provision_invoice_id)
				. ' user_id=' . intval($user_id)
				. ' service_id=' . intval($service_id)
				. ' home_id=' . intval($home_id)
				. ' home_cfg_id=' . intval($home_cfg_id ?? 0)
				. ' config_xml=' . (string)$selected_config_xml
				. ' mod_id=' . intval($selected_mod_id)
				. ' ip_id=' . intval($selected_ip_id)
				. ' port=' . intval($selected_port)
				. ' mechanism=' . $install_mechanism
				. ' install_result=' . ($order_failed ? 'failed' : (string)$install_result)
				. ($order_failed ? ' error=' . (string)$order_failure_reason : '')
			);

			if ($order_failed) {
				$failed_count++;
				$failed_messages[] = "Order #{$order_id}: {$order_failure_reason}";
				$db->logger("Provisioning pending install for order #{$order_id}: {$order_failure_reason}");
				billing_provision_trace('END failure', array(
					'home_id_after_creation_or_lookup' => intval($home_id),
					'end_reason' => $order_failure_reason,
				));
			} else {
				$provisioned_count++;
				billing_provision_trace('END success', array(
					'home_id_after_creation_or_lookup' => intval($home_id),
					'install_result' => (string)$install_result,
				));
			}
			$order_results[] = array(
				'trace_id' => $trace_id,
				'order_id' => intval($order_id),
				'user_id' => intval($user_id),
				'service_id' => intval($service_id),
				'home_id' => intval($home_id),
				'mod_id' => intval($selected_mod_id),
				'install_result' => $order_failed ? 'failed' : (string)$install_result,
				'install_message' => (string)$install_message,
				'error' => $order_failed ? (string)$order_failure_reason : '',
				'trace_log_path' => billing_provision_trace_relative_path(),
			);
			} catch (Throwable $e) {
				$failed_count++;
				$order_id = intval($order['order_id'] ?? 0);
				$message = "Order #{$order_id} threw an exception during provisioning: " . $e->getMessage();
				$failed_messages[] = $message;
				$db->logger('BILLING PROVISION EXCEPTION: ' . $message);
				billing_provision_trace('Provisioning exception caught.', array('exception' => $e->getMessage()));
				billing_provision_trace('END failure', array('end_reason' => $message));
				$order_results[] = array(
					'trace_id' => $trace_id,
					'order_id' => $order_id,
					'user_id' => intval($order['user_id'] ?? 0),
					'service_id' => intval($order['service_id'] ?? 0),
					'home_id' => intval($order['home_id'] ?? 0),
					'mod_id' => 0,
					'install_result' => 'failed',
					'install_message' => '',
					'error' => $e->getMessage(),
					'trace_log_path' => billing_provision_trace_relative_path(),
				);
			}
			unset($GLOBALS['BILLING_PROVISION_TRACE_CONTEXT']);
						
		}

	// Show results and redirect
	if ($provisioned_count > 0) {
		echo "<div class='success'>";
		echo "<h3>Server Provisioning Complete</h3>";
		echo "<p>Successfully provisioned $provisioned_count server(s). Your server(s) are now active.</p>";
		echo "</div>";
		if ($failed_count > 0) {
			echo "<div class='failure'>";
			echo "<p>{$failed_count} order(s) were linked but left pending install:</p><ul>";
			foreach ((array)$failed_messages as $failed_message) {
				echo "<li>" . htmlspecialchars($failed_message, ENT_QUOTES, 'UTF-8') . "</li>";
			}
			echo "</ul></div>";
		}
		echo "<p><a href='home.php?m=gamemanager&p=game_monitor' class='btn'>View My Servers</a></p>";
		// Auto-redirect after 3 seconds
		echo "<script>setTimeout(function(){ window.location.href='home.php?m=gamemanager&p=game_monitor'; }, 3000);</script>";
	} else {
		if ($failed_count > 0) {
			echo "<div class='failure'><p>No servers were auto-installed. Orders are active but pending install:</p><ul>";
			foreach ((array)$failed_messages as $failed_message) {
				echo "<li>" . htmlspecialchars($failed_message, ENT_QUOTES, 'UTF-8') . "</li>";
			}
			echo "</ul></div>";
		} else {
			echo "<div class='info'>";
			echo "<p>No servers to provision. All orders have already been processed.</p>";
			echo "</div>";
		}
		echo "<p><a href='home.php?m=billing&p=my_orders' class='btn'>View My Orders</a></p>";
	}
	
	} else {
		billing_provision_trace('END failure: no paid orders matched provisioning request.', array(
			'caller_source' => $caller_source,
			'order_ids_received' => $orderIds,
		));
		echo "<div class='failure'>";
		echo "<p>No paid orders found to provision.</p>";
		echo "</div>";
		echo "<p><a href='home.php?m=billing&p=my_orders' class='btn'>View My Orders</a></p>";
		$provisioned_count = 0;
		$failed_count = 0;
	}
	$GLOBALS['BILLING_PROVISION_LAST_RESULT'] = array(
		'provisioned_count' => isset($provisioned_count) ? $provisioned_count : 0,
		'failed_count' => isset($failed_count) ? $failed_count : 0,
		'orders' => $processed_orders,
		'details' => $order_results,
		'trace_log_path' => billing_provision_trace_relative_path(),
		'trace_error' => $GLOBALS['BILLING_PROVISION_TRACE_ERROR'] ?? '',
	);
	billing_provision_trace('END provisioning attempt', array(
		'provisioned_count' => intval($GLOBALS['BILLING_PROVISION_LAST_RESULT']['provisioned_count'] ?? 0),
		'failed_count' => intval($GLOBALS['BILLING_PROVISION_LAST_RESULT']['failed_count'] ?? 0),
	));
}
?>
