<?php

if (!function_exists('gamemanager_choose_mod_id')) {
	function gamemanager_choose_mod_id(array $home_info, int $preferred_mod_id = 0): int
	{
		$mods = $home_info['mods'] ?? array();
		if (!is_array($mods) || empty($mods)) {
			return 0;
		}
		if ($preferred_mod_id > 0 && isset($mods[$preferred_mod_id])) {
			return $preferred_mod_id;
		}
		$keys = array_keys($mods);
		return intval(reset($keys));
	}
}

if (!function_exists('gamemanager_trigger_update_install')) {
	function gamemanager_trigger_update_install($db, array $home_info, int $mod_id, array $options = array()): array
	{
		$home_id = intval($home_info['home_id'] ?? 0);
		$mod_id = gamemanager_choose_mod_id($home_info, $mod_id);
		$resultBase = array(
			'trigger' => 'gamemanager_trigger_update_install',
			'home_id' => $home_id,
			'mod_id' => $mod_id,
			'output' => null,
		);
		if ($home_id <= 0) {
			return $resultBase + array('ok' => false, 'pending' => true, 'message' => 'Invalid home_id.');
		}
		if ($mod_id <= 0 || empty($home_info['mods'][$mod_id])) {
			return $resultBase + array('ok' => false, 'pending' => true, 'message' => "No mod profile configured for home #{$home_id}.");
		}

		// Build the XML path and resolve it to an absolute location so provisioning
		// paths that do not run from the panel root CWD (e.g. billing capture_order.php)
		// can still locate the file.  SERVER_CONFIG_LOCATION is a relative constant
		// ("modules/config_games/server_configs/") that only works when CWD equals the
		// panel root; update_actions.php lives in modules/gamemanager/ so the panel
		// root is __DIR__ . '/../../'.
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
			billing_provision_trace('gamemanager_trigger_update_install: XML path resolution.', array(
				'home_id'               => $home_id,
				'home_cfg_file'         => $xml_cfg_file,
				'server_config_location' => SERVER_CONFIG_LOCATION,
				'xml_rel_path'          => $xml_rel,
				'xml_abs_path'          => $xml_abs,
				'cwd'                   => getcwd(),
				'xml_file_exists'       => file_exists($xml_abs),
				'xml_is_readable'       => is_readable($xml_abs),
			));
		}
		$server_xml = read_server_config($xml_abs);
		if (!$server_xml) {
			return $resultBase + array('ok' => false, 'pending' => true, 'message' => "Could not read server config XML for home #{$home_id}. Tried: {$xml_abs}");
		}

		$remote = new OGPRemoteLibrary($home_info['agent_ip'], $home_info['agent_port'], $home_info['encryption_key'], $home_info['timeout']);
		if ($remote->status_chk() === 0) {
			return $resultBase + array('ok' => false, 'pending' => true, 'message' => 'Agent is offline.');
		}
		if ($remote->is_screen_running(OGP_SCREEN_TYPE_HOME, $home_id) == 1) {
			return $resultBase + array('ok' => false, 'pending' => false, 'message' => 'Server is running and cannot be updated.');
		}

		$log_txt = '';
		$update_active = $remote->get_log(OGP_SCREEN_TYPE_UPDATE, $home_id, clean_path($home_info['home_path']), $log_txt);
		if ($update_active == 1) {
			return $resultBase + array('ok' => true, 'started' => true, 'already_running' => true, 'message' => 'Update already in progress.', 'output' => $log_txt);
		}

		$modkey = $home_info['mods'][$mod_id]['mod_key'] ?? '';
		$mod_xml = xml_get_mod($server_xml, $modkey);
		if (!$mod_xml) {
			return $resultBase + array('ok' => false, 'pending' => true, 'message' => "Mod key '{$modkey}' not found in XML.");
		}

		$installer_name = isset($mod_xml->installer_name) ? (string)$mod_xml->installer_name : (string)$modkey;
		$precmd = $home_info['mods'][$mod_id]['precmd'] == ""
			? ($home_info['mods'][$mod_id]['def_precmd'] == "" ? $server_xml->pre_install : $home_info['mods'][$mod_id]['def_precmd'])
			: $home_info['mods'][$mod_id]['precmd'];
		$postcmd = $home_info['mods'][$mod_id]['postcmd'] == ""
			? ($home_info['mods'][$mod_id]['def_postcmd'] == "" ? $server_xml->post_install : $home_info['mods'][$mod_id]['def_postcmd'])
			: $home_info['mods'][$mod_id]['postcmd'];
		$exec_folder_path = clean_path($home_info['home_path'] . "/" . $server_xml->exe_location);
		$exec_path = clean_path($exec_folder_path . "/" . $server_xml->server_exec_name);

		$master_server_home_id = intval($options['master_server_home_id'] ?? 0);
		if ($master_server_home_id > 0) {
			if ($db->getMasterServer($home_info['remote_server_id'], $home_info['home_cfg_id']) != $master_server_home_id) {
				return $resultBase + array('ok' => false, 'pending' => false, 'message' => 'Attempting update from non-master server.');
			}
			if ($master_server_home_id == $home_id) {
				return $resultBase + array('ok' => false, 'pending' => false, 'message' => 'Cannot update from own self.');
			}
			$ms_info = $db->getGameHome($master_server_home_id);
			$steam_out = $remote->masterServerUpdate($home_id, $home_info['home_path'], $master_server_home_id, $ms_info['home_path'], $exec_folder_path, $exec_path, $precmd, $postcmd);
			if ($steam_out === 0) {
				return $resultBase + array('ok' => false, 'pending' => true, 'message' => 'Failed to start master update.', 'output' => $steam_out);
			}
			return $resultBase + array('ok' => true, 'started' => true, 'message' => 'Update started.', 'output' => $steam_out);
		}

		$use_steamcmd = ((string)$server_xml->installer === "steamcmd");
		if ($use_steamcmd && !empty((string)$installer_name)) {
			$cfg_os = '';
			if (preg_match("/win32/", $server_xml->game_key) || preg_match("/win64/", $server_xml->game_key)) {
				$cfg_os = "windows";
			} elseif (preg_match("/linux/", $server_xml->game_key)) {
				$cfg_os = "linux";
			}

			$settings = is_array($options['settings'] ?? null) ? $options['settings'] : $db->getSettings();
			if (!empty($mod_xml->installer_login)) {
				$login = (string)$mod_xml->installer_login;
				$pass = '';
			} else {
				$login = (string)($settings['steam_user'] ?? '');
				$pass = (string)($settings['steam_pass'] ?? '');
			}

			$modname = ($installer_name == '90') ? $modkey : '';
			$betaname = isset($mod_xml->betaname) ? (string)$mod_xml->betaname : '';
			$betapwd = isset($mod_xml->betapwd) ? (string)$mod_xml->betapwd : '';
			$arch = isset($mod_xml->steam_bitness) ? (string)$mod_xml->steam_bitness : '';
			$lockFiles = (isset($server_xml->lock_files) && !empty($server_xml->lock_files)) ? trim((string)$server_xml->lock_files) : "";
			$steam_out = $remote->steam_cmd($home_id, $home_info['home_path'], $installer_name, $modname,
											 $betaname, $betapwd, $login, $pass, $settings['steam_guard'] ?? '',
											 $exec_folder_path, $exec_path, $precmd, $postcmd, $cfg_os, $lockFiles, $arch);
			if ($steam_out === 0) {
				return $resultBase + array('ok' => false, 'pending' => true, 'message' => 'Failed to start SteamCMD update.', 'output' => $steam_out);
			}
			return $resultBase + array('ok' => true, 'started' => true, 'message' => 'Update started.', 'output' => $steam_out);
		}

		$ran_scripts = false;
		if (!empty((string)$precmd)) {
			$remote->exec((string)$precmd);
			$ran_scripts = true;
		}
		if (!empty((string)$postcmd)) {
			$remote->exec((string)$postcmd);
			$ran_scripts = true;
		}
		return array(
			'trigger' => 'gamemanager_trigger_update_install',
			'ok' => true,
			'started' => $ran_scripts,
			'completed' => !$ran_scripts,
			'message' => $ran_scripts ? 'Script install started.' : 'No installer command was required.',
			'mod_id' => $mod_id,
			'home_id' => $home_id,
			'output' => array(
				'precmd' => (string)$precmd,
				'postcmd' => (string)$postcmd,
			),
		);
	}
}
