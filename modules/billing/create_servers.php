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

if (!function_exists('billing_invoke_provision')) {
	function billing_invoke_provision(array $options = array())
	{
		$GLOBALS['BILLING_PROVISION_OVERRIDE'] = $options;
		ob_start();
		exec_ogp_module();
		$output = ob_get_clean();
		$result = isset($GLOBALS['BILLING_PROVISION_LAST_RESULT']) ? $GLOBALS['BILLING_PROVISION_LAST_RESULT'] : array();
		$result['output'] = $output;
		unset($GLOBALS['BILLING_PROVISION_OVERRIDE'], $GLOBALS['BILLING_PROVISION_LAST_RESULT']);
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
		if (empty($ipIds)) {
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
				continue;
			}

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
						return array('ok' => true, 'ip_id' => $ipId, 'port' => intval($port));
					}
				}
			}
			$ips_exhausted[] = $ipId;
		}

		if (!empty($ips_with_no_range) && count($ips_with_no_range) === count($ipIds)) {
			return array('ok' => false, 'error' => "No port range found for home_cfg_id #{$home_cfg_id} on ip_id(s) [" . implode(',', $ips_with_no_range) . "] for remote server #{$remote_server_id}.");
		}
		return array('ok' => false, 'error' => "No available port in arrange_ports for remote server #{$remote_server_id}, home_cfg_id #{$home_cfg_id}, ip_id(s) [" . implode(',', !empty($ips_exhausted) ? $ips_exhausted : $ipIds) . "].");
	}
}

if (!function_exists('billing_resolve_mod_cfg_id')) {
	function billing_resolve_mod_cfg_id($db, int $home_cfg_id, int $preferred_mod_cfg_id): array
	{
		$mods = $db->getCfgMods($home_cfg_id);
		if (empty($mods)) {
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
				return array('ok' => true, 'mod_cfg_id' => $modCfgId);
			}
		}

		if ($first !== null) {
			return array('ok' => true, 'mod_cfg_id' => $first);
		}

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
	
	// Handle provision_all request - provision all Active (paid) orders for this user
	if ($provision_all) {
		if ( $isAdmin ){
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE status='Active' AND (home_id='0' OR home_id='') ORDER BY order_id" );
		} else {
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE user_id=".$db->realEscapeSingle($user_id)." AND status='Active' AND (home_id='0' OR home_id='') ORDER BY order_id" );
		}
	}
	// Handle provision_single or order_id parameter - provision specific order
	else {
		if (empty($orderIds)) {
			echo "<div class='failure'>No order ID specified.</div>";
			$GLOBALS['BILLING_PROVISION_LAST_RESULT'] = array('provisioned_count'=>0,'failed_count'=>0,'orders'=>array());
			return;
		}
		$idList = implode(',', array_map('intval', $orderIds));
		if ( $isAdmin ){
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE order_id IN ($idList) AND status='Active'" );
		} else {
			$orders = $db->resultQuery( "SELECT * FROM `{$db_prefix}billing_orders` WHERE order_id IN ($idList) AND user_id=".$db->realEscapeSingle($user_id)." AND status='Active'" );
		}
	}
	$processed_orders = array();
	if( !empty($orders) )
	{
		$provisioned_count = 0;
		$failed_count = 0;
		$failed_messages = array();
		
		foreach ((array)$orders as $order)
		{
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
			$install_mechanism = BILLING_INSTALL_MECHANISM;
			$install_result = 'pending';
			$install_message = '';
			$install_attempted = false;
			$needs_existing_home_retry = false;
			$home_info = array();
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
			//Query service info	
			$service = $db->resultQuery( "SELECT * 
							   FROM `{$db_prefix}billing_services` 
							   WHERE service_id=".$db->realEscapeSingle($service_id) );
							   
			if( !empty( $service[0] ) )
			{
				$home_cfg_id = $service[0]['home_cfg_id'];
				$mod_cfg_id = $service[0]['mod_cfg_id'];
				//remote_server_id has been stored in IP_ID
				//$remote_server_id = $service[0]['remote_server_id'];
				$remote_server_id = $order['ip'];	
				
				$ftp = $service[0]['ftp'];
				$install_method = $service[0]['install_method'];
				$manual_url = $service[0]['manual_url'];
				$access_rights = $service[0]['access_rights'];
				if (intval($home_cfg_id) <= 0) {
					$order_failed = true;
					$order_failure_reason = "Invalid home_cfg_id '{$home_cfg_id}' for service_id {$service_id}.";
				}
				if (!$order_failed && intval($remote_server_id) <= 0) {
					$order_failed = true;
					$order_failure_reason = "Invalid remote server selection '{$remote_server_id}' on order #{$order_id} for service_id {$service_id}.";
				}
			}
			else
			{
				$order_failed = true;
				$order_failure_reason = "Service ID {$service_id} not found.";
			}
						
			if(!$order_failed && $already_provisioned)
			{
				$home_id = intval($order['home_id']);
				$home_info = $db->getGameHome($home_id);
				if (empty($home_info)) {
					$order_failed = true;
					$order_failure_reason = "Order #{$order_id} references home_id {$home_id} but server_homes row is missing.";
					$db->logger('BILLING PROVISION DATA INTEGRITY ERROR: ' . $order_failure_reason);
				}
				$existingIpPort = billing_get_home_ip_port($db, $db_prefix, intval($home_id));
				if (!empty($existingIpPort['ok'])) {
					$selected_ip_id = intval($existingIpPort['ip_id']);
					$selected_port = intval($existingIpPort['port']);
				}
				$has_ip_port = !empty($existingIpPort['ok']);
				$has_mods = !empty($home_info['mods']) && is_array($home_info['mods']);
				if (!$order_failed && (!$has_ip_port || !$has_mods)) {
					$needs_existing_home_retry = true;
					$install_message = "Existing home #{$home_id} requires provisioning completion (ip_port=" . ($has_ip_port ? 'yes' : 'no') . ", mods=" . ($has_mods ? 'yes' : 'no') . ").";
				}
				if (!$order_failed && !$needs_existing_home_retry) {
					$server_xml = read_server_config(SERVER_CONFIG_LOCATION . "/" . $home_info['home_cfg_file']);
					if ($server_xml && !empty((string)$server_xml->server_exec_name)) {
						$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
						if ($remote->status_chk() === 1) {
							$exec_path = clean_path($home_info['home_path'] . "/" . (string)$server_xml->exe_location . "/" . (string)$server_xml->server_exec_name);
							if ($remote->rfile_exists($exec_path) !== 1) {
								$needs_existing_home_retry = true;
								$install_message = "Existing home #{$home_id} missing expected executable '" . basename($exec_path) . "'; retrying install.";
							}
						}
					}
				}
				if (!$order_failed && !$needs_existing_home_retry) {
					$install_result = 'completed';
					$install_message = $install_message !== '' ? $install_message : "Order #{$order_id} already provisioned and installed; no action required.";
				}
			}
			elseif(!$order_failed && $extended)
			{
				$home_id = $order['home_id'];
				
				//Get The home info without mods in 1 array (Necesary for remote connection).
				$home_info = $db->getGameHomeWithoutMods($home_id);
				
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
				}
				$game_path = "/home/gameserver/";
				if (!$order_failed) {
					$home_id = $db->addGameHome( $remote_server_id, $user_id, $home_cfg_id, $game_path, $home_name, $remote_control_password, $ftp_password);
				}
				if (!$order_failed && (!$home_id || intval($home_id) <= 0)) {
					$order_failed = true;
					$order_failure_reason = "Could not create server_homes row for order #{$order_id}.";
				}
				if (!$order_failed) {
					// Billing storefront defaults to FTP enabled for newly provisioned homes
					// so panel/account flows remain consistent immediately after checkout.
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
				}

				if (!$order_failed) {
					$install_attempted = true;
					$autoInstall = gamemanager_trigger_update_install(
						$db,
						$home_info,
						intval($mod_id),
						array('settings' => $settings)
					);
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
				if ($selected_ip_id <= 0 || $selected_port <= 0) {
					$existingIpPort = billing_get_home_ip_port($db, $db_prefix, intval($home_id));
					if (!empty($existingIpPort['ok'])) {
						$selected_ip_id = intval($existingIpPort['ip_id']);
						$selected_port = intval($existingIpPort['port']);
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
					$autoInstall = gamemanager_trigger_update_install(
						$db,
						(array)$home_info,
						intval($selected_mod_id),
						array('settings' => $settings)
					);
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
			$db->query("UPDATE `{$db_prefix}billing_orders`
						SET home_id='" . $db->realEscapeSingle($home_id) . "' WHERE order_id=".$db->realEscapeSingle($order_id));

			$db->query("UPDATE `{$db_prefix}billing_invoices`
						SET home_id=" . $db->realEscapeSingle($home_id) . ",
							billing_status='Active',
							status='paid'
						WHERE order_id=" . $db->realEscapeSingle($order_id));

			$db->query("UPDATE `{$db_prefix}billing_transactions`
						SET home_id=" . $db->realEscapeSingle($home_id) . "
						WHERE invoice_id IN (SELECT invoice_id FROM `{$db_prefix}billing_invoices` WHERE order_id=" . $db->realEscapeSingle($order_id) . ")");

			if ($home_id > 0) {
				$db->query("UPDATE `{$db_prefix}game_mods`
							SET max_players=" . $db->realEscapeSingle($max_players) . "
							WHERE home_id=" . $db->realEscapeSingle($home_id));
			}

			if ($home_id > 0) {
				// Set billing_status and next_invoice_date on server_homes
				$db->query("UPDATE `{$db_prefix}server_homes`
							SET billing_status     = 'Active',
								next_invoice_date  = '" . $db->realEscapeSingle($end_date_str) . "',
								billing_enabled    = 1
							WHERE home_id = " . $db->realEscapeSingle($home_id));
			}

			$provisionContext = array(
				'order_id' => intval($order_id),
				'invoice_id' => intval($provision_invoice_id),
				'user_id' => intval($user_id),
				'home_id' => intval($home_id),
				'home_cfg_id' => intval($home_cfg_id ?? 0),
				'mod_id' => intval($selected_mod_id),
				'ip_id' => intval($selected_ip_id),
				'port' => intval($selected_port),
				'mechanism' => $install_mechanism,
				'install_result' => $order_failed ? 'failed' : (string)$install_result,
				'error' => $order_failed ? (string)$order_failure_reason : '',
				'message' => (string)$install_message,
			);
			billing_write_provision_log($provisionContext);
			$db->logger(
				'BILLING PROVISION RESULT order_id=' . intval($order_id)
				. ' invoice_id=' . intval($provision_invoice_id)
				. ' user_id=' . intval($user_id)
				. ' home_id=' . intval($home_id)
				. ' home_cfg_id=' . intval($home_cfg_id ?? 0)
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
			} else {
				$provisioned_count++;
			}
						
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
	);
}
?>
