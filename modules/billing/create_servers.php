<?php
require_once __DIR__ . '/../../includes/lib_remote.php';
require_once __DIR__ . '/../config_games/server_config_parser.php';

if (!function_exists('billing_generate_provision_password')) {
	function billing_generate_provision_password(int $bytes = 12)
	{
		try {
			return substr(bin2hex(random_bytes($bytes)), 0, $bytes * 2);
		} catch (Throwable $e) {
			return substr(hash('sha256', uniqid('gsp-provision', true) . microtime(true)), 0, $bytes * 2);
		}
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
		}

		return array('ok' => false, 'error' => "No available port in arrange_ports for remote server #{$remote_server_id} and home_cfg_id #{$home_cfg_id}.");
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
		foreach ((array)$mods as $mod) {
			$modCfgId = intval($mod['mod_cfg_id'] ?? 0);
			if ($modCfgId <= 0) {
				continue;
			}
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

		return array('ok' => false, 'error' => "No usable mod_cfg_id found for home_cfg_id #{$home_cfg_id}.");
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
			if ($remote_control_password === '' || strcasecmp((string)$remote_control_password, 'ChangeMe') === 0) {
				$remote_control_password = billing_generate_provision_password();
			}
			if ($ftp_password === '' || strcasecmp((string)$ftp_password, 'ChangeMe') === 0) {
				$ftp_password = billing_generate_provision_password();
			}
			$ip = $order['ip'];
			$max_players = $order['max_players'];
			$user_id = $order['user_id'];
			$extended = isset($order['extended']) && $order['extended'] == "1" ? TRUE : FALSE;
			$alreadyProvisioned = !$extended && intval($order['home_id'] ?? 0) > 0;
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
			}
			else
			{
				$order_failed = true;
				$order_failure_reason = "Service ID {$service_id} not found.";
			}
						
			if(!$order_failed && $alreadyProvisioned)
			{
				$home_id = intval($order['home_id']);
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
				$game_path = "/home/gameserver/";
				$home_id = $db->addGameHome( $remote_server_id, $user_id, $home_cfg_id, $game_path, $home_name, $remote_control_password, $ftp_password);
				if (!$home_id || intval($home_id) <= 0) {
					$order_failed = true;
					$order_failure_reason = "Could not create server_homes row for order #{$order_id}.";
				}
				
				// Add IP:Port pair with arrange_ports exact home_cfg_id preference and home_cfg_id=0 fallback.
				if (!$order_failed) {
					$allocatedPort = billing_allocate_home_port($db, $db_prefix, intval($home_id), intval($remote_server_id), intval($home_cfg_id));
					if (empty($allocatedPort['ok'])) {
						$order_failed = true;
						$order_failure_reason = (string)($allocatedPort['error'] ?? 'Port allocation failed.');
						$db->logger("Provisioning pending install for order #{$order_id}: {$order_failure_reason}");
					}
				}
				
				//Assign the Game Mod to the Game Home
				$resolved_mod_cfg_id = intval($mod_cfg_id);
				if (!$order_failed) {
					$modResolution = billing_resolve_mod_cfg_id($db, intval($home_cfg_id), intval($mod_cfg_id));
					if (empty($modResolution['ok'])) {
						$order_failed = true;
						$order_failure_reason = (string)($modResolution['error'] ?? 'No mod profile available for base install.');
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
					}
				}
				if (!$order_failed) {
					$db->updateGameModParams( $max_players, $extra_params, $cpu_affinity, $nice, $home_id, $resolved_mod_cfg_id );
					$db->assignHomeTo( "user", $user_id, $home_id, $access_rights );
				}
				
				//Get The home info without mods in 1 array (Necesary for remote connection).
				if (!$order_failed) {
					$home_info = $db->getGameHomeWithoutMods($home_id);
					if (empty($home_info)) {
						$order_failed = true;
						$order_failure_reason = "Could not load home info for home #{$home_id}.";
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
					}
				}
				
				//Read the Game Config from the XML file
				if (!$order_failed) {
					$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
					if ($server_xml === false) {
						$order_failed = true;
						$order_failure_reason = "Could not read server XML for home #{$home_id}.";
					}
				}
				
				//Get Values from XML
				$mod_xml = false;
				$modkey = '';
				$installer_name = '';
				if (!$order_failed) {
					$selected_mod = $home_info['mods'][$mod_id] ?? reset($home_info['mods']);
					if (empty($selected_mod) || empty($selected_mod['mod_key'])) {
						$order_failed = true;
						$order_failure_reason = "No valid mod profile found for home #{$home_id}.";
					} else {
						$modkey = (string)$selected_mod['mod_key'];
						$mod_xml = xml_get_mod($server_xml, $modkey);
						if ($mod_xml === false && isset($server_xml->mods->mod[0])) {
							$mod_xml = $server_xml->mods->mod[0];
							$modkey = (string)$mod_xml['key'];
						}
						if ($mod_xml === false) {
							$order_failed = true;
							$order_failure_reason = "No installable mod profile exists in XML for home #{$home_id}.";
						} else {
							$installer_name = (string)$mod_xml->installer_name;
							$resolved_mod_cfg_id = intval($selected_mod['mod_cfg_id'] ?? $resolved_mod_cfg_id);
						}
					}
				}
				
			//Get Preinstall commands from xml
				$precmd = !$order_failed ? $server_xml->pre_install : '';

					
				//Get Postinstall commands from xml
				$postcmd = !$order_failed ? $server_xml->post_install : ''; 


				//Enable FTP account in remote server
				if (!$order_failed && $ftp == "enabled")
				{
					$remote->ftp_mgr("useradd", $home_info['home_id'], $home_info['ftp_password'], $home_info['home_path']);
					$db->changeFtpStatus('enabled',$home_info['home_id']);
				}
				
				//Install files for this service in the remote server
				if (!$order_failed) {
					$exec_folder_path = clean_path($home_info['home_path'] . "/" . $server_xml->exe_location );
					$exec_path = clean_path($exec_folder_path . "/" . $server_xml->server_exec_name );
				}

				if (!$order_failed && (string)$server_xml->installer === "steamcmd" && !empty((string)$installer_name) )
				{
					if( preg_match("/win32/", $server_xml->game_key) OR preg_match("/win64/", $server_xml->game_key) ) 
						$cfg_os = "windows";
					elseif( preg_match("/linux/", $server_xml->game_key) )
						$cfg_os = "linux";
					
					// Some games like L4D2 require anonymous login
					if(!empty($mod_xml->installer_login)){
						$login = $mod_xml->installer_login;
						$pass = '';
					}else{
						$login = $settings['steam_user'];
						$pass = $settings['steam_pass'];
					}
					
					$modname = ( $installer_name == '90' and !preg_match("/(cstrike|valve)/", $modkey) ) ? $modkey : '';
					$betaname = isset($mod_xml->betaname) ? $mod_xml->betaname : '';
					$betapwd = isset($mod_xml->betapwd) ? $mod_xml->betapwd : '';
					$arch = isset($mod_xml->steam_bitness) ? $mod_xml->steam_bitness : '';
					
					$remote->steam_cmd( $home_id,$home_info['home_path'],$installer_name,$modname,
										$betaname,$betapwd,$login,$pass,$settings['steam_guard'],
										$exec_folder_path,$exec_path,$precmd,$postcmd,$cfg_os,'',$arch); 
				}
				elseif (!$order_failed)
				{
					// No SteamCMD installer — run pre/post install scripts only.
					if (!empty((string)$precmd)) {
						$result = $remote->exec((string)$precmd);
						if ($result === NULL)
							$db->logger("Script-only install: pre_install script returned no output for home_id $home_id");
					}
					if (!empty((string)$postcmd)) {
						$result = $remote->exec((string)$postcmd);
						if ($result === NULL)
							$db->logger("Script-only install: post_install script returned no output for home_id $home_id");
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
			// Set expiration date in panel database
			// Status values: Active (provisioned & current), Invoiced (renewal invoice open),
			//                 Expired (past due and awaiting deletion)
			// end_date / next_invoice_date: when the next renewal invoice should be generated
			if ($alreadyProvisioned)
			{
				$existing_end = strtotime((string)($order['end_date'] ?? ''));
				if ($existing_end === false || $existing_end <= 0) {
					$existing_end = time();
				}
				$end_date_str = date('Y-m-d H:i:s', $existing_end);
			}
			elseif ($order['invoice_duration'] == "day")
			{
				
				if(empty($order['end_date']) || $order['end_date'] === NULL){
				$end_date = strtotime('+'.$order['qty'].' day'); 
				}
			else{
			//this is a renewel, start from end of previous order
				$current_end = strtotime($order['end_date']);
				if ($current_end === false) {
					$current_end = time(); // fallback to now if date is invalid
				}
				$end_date = strtotime('+'.$order['qty'].' day', $current_end); 		
				}	
				
			}
			elseif ($order['invoice_duration'] == "month")
			{
			// this is a new order
			if(empty($order['end_date']) || $order['end_date'] === NULL){
				$end_date = strtotime('+'.(intval($order['qty']) * 31).' day'); 

				}
			else{
			//this is a renewel, start from end of previous order
				$current_end = strtotime($order['end_date']);
				if ($current_end === false) {
					$current_end = time(); // fallback to now if date is invalid
				}
				$end_date = strtotime('+'.(intval($order['qty']) * 31).' day', $current_end); 
				}	
			}
			elseif ($order['invoice_duration'] == "year")
			{
				// this is a new order
			if(empty($order['end_date']) || $order['end_date'] === NULL){
				$end_date = strtotime('+'.$order['qty'].' year'); 
				}
			else{
			//this is a renewel, start from end of previous order
				$current_end = strtotime($order['end_date']);
				if ($current_end === false) {
					$current_end = time(); // fallback to now if date is invalid
				}
                $end_date = strtotime('+'.$order['qty'].' year', $current_end); 
				
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
