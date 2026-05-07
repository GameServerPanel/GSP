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

/**
 * Log a provisioning failure to billing_provisioning_errors.
 * All parameters are sanitised inside this function.
 */
if (!function_exists('billing_log_provision_error')) {
	function billing_log_provision_error(
		$db,
		string $db_prefix,
		int $billing_order_id,
		int $home_id,
		int $user_id,
		int $remote_server_id,
		int $ip_id,
		int $attempted_port,
		int $mod_cfg_id,
		string $failure_message
	): void {
		try {
			$db->query(
				"INSERT INTO `{$db_prefix}billing_provisioning_errors`
				 (`billing_order_id`,`home_id`,`user_id`,`remote_server_id`,`ip_id`,`attempted_port`,`mod_cfg_id`,`failure_message`,`created_at`)
				 VALUES ("
				. intval($billing_order_id) . ","
				. intval($home_id) . ","
				. intval($user_id) . ","
				. intval($remote_server_id) . ","
				. intval($ip_id) . ","
				. intval($attempted_port) . ","
				. intval($mod_cfg_id) . ","
				. "'" . $db->realEscapeSingle($failure_message) . "',"
				. "NOW())"
			);
		} catch (Throwable $e) {
			// Never let logging itself break provisioning
			error_log('billing_log_provision_error: ' . $e->getMessage());
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
		
		foreach ((array)$orders as $order)
		{
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
				return;
						
			if($alreadyProvisioned)
			{
				$home_id = intval($order['home_id']);
			}
			elseif($extended)
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
			else
			{
				//OPTIONS, change it at your choice;
				$extra_params = "";//no extra params defined by default
				$cpu_affinity = "NA";//Affinity to one core/thread of the cpu by number, use NA to disable it
				$nice = "0";//Min priority=19 Max Priority=-19

				// ---------------------------------------------------------------
				// Resolve IP: find the first IP address configured for this
				// remote server.  The order.ip column stores remote_server_id.
				// ---------------------------------------------------------------
				$resolved_remote_server_id = intval($remote_server_id);
				$ip_id = null;
				$ipRows = $db->resultQuery(
					"SELECT ip_id FROM `{$db_prefix}remote_server_ips`
					 WHERE remote_server_id=" . $resolved_remote_server_id . "
					 ORDER BY ip_id ASC LIMIT 1"
				);
				if (!empty($ipRows[0]['ip_id'])) {
					$ip_id = intval($ipRows[0]['ip_id']);
				}
				if ($ip_id === null) {
					$errMsg = "No IP address configured for remote server ID {$resolved_remote_server_id} (order_id={$order_id}). "
					        . "Please add an IP to that remote server in the panel.";
					billing_log_provision_error($db, $db_prefix, intval($order_id), 0, intval($user_id), $resolved_remote_server_id, 0, 0, intval($mod_cfg_id), $errMsg);
					echo "<div class='failure'><p><strong>Provisioning failed for order #" . intval($order_id) . ":</strong> " . htmlspecialchars($errMsg) . "</p></div>";
					$failed_count++;
					continue;
				}

				// ---------------------------------------------------------------
				// Resolve mod/build in priority order:
				//   1. Explicit mod_cfg_id from billing_services (if > 0 and valid)
				//   2. Admin-configured is_default_for_billing on config_mods
				//   3. Only one mod available for this game — use it automatically
				//   4. Fail gracefully with an admin-visible error
				// ---------------------------------------------------------------
				$resolved_mod_cfg_id = null;

				if (!empty($mod_cfg_id) && intval($mod_cfg_id) > 0) {
					$modCheck = $db->resultQuery(
						"SELECT mod_cfg_id FROM `{$db_prefix}config_mods`
						 WHERE mod_cfg_id=" . intval($mod_cfg_id) . "
						   AND home_cfg_id=" . intval($home_cfg_id)
					);
					if (!empty($modCheck[0]['mod_cfg_id'])) {
						$resolved_mod_cfg_id = intval($modCheck[0]['mod_cfg_id']);
					}
				}

				if ($resolved_mod_cfg_id === null) {
					$defaultModRow = $db->resultQuery(
						"SELECT mod_cfg_id FROM `{$db_prefix}config_mods`
						 WHERE home_cfg_id=" . intval($home_cfg_id) . "
						   AND is_default_for_billing=1
						 LIMIT 1"
					);
					if (!empty($defaultModRow[0]['mod_cfg_id'])) {
						$resolved_mod_cfg_id = intval($defaultModRow[0]['mod_cfg_id']);
					}
				}

				if ($resolved_mod_cfg_id === null) {
					$allMods = $db->resultQuery(
						"SELECT mod_cfg_id FROM `{$db_prefix}config_mods`
						 WHERE home_cfg_id=" . intval($home_cfg_id)
					);
					if (!empty($allMods) && count($allMods) === 1) {
						$resolved_mod_cfg_id = intval($allMods[0]['mod_cfg_id']);
					}
				}

				if ($resolved_mod_cfg_id === null) {
					$errMsg = "No default mod/build configured for game type (home_cfg_id={$home_cfg_id}, order_id={$order_id}). "
					        . "Visit Admin -> Game Defaults to mark a mod/build as the billing default.";
					billing_log_provision_error($db, $db_prefix, intval($order_id), 0, intval($user_id), $resolved_remote_server_id, $ip_id, 0, 0, $errMsg);
					echo "<div class='failure'><p><strong>Provisioning failed for order #" . intval($order_id) . ":</strong> " . htmlspecialchars($errMsg) . "</p></div>";
					$failed_count++;
					continue;
				}

				// Use resolved values for the rest of the provisioning flow
				$mod_cfg_id = $resolved_mod_cfg_id;

				//Add Game home to database
				//HARD CODE TO /home/gameserver/
				$rserver = $db->getRemoteServer($resolved_remote_server_id);
				$game_path = "/home/gameserver/";
				$home_id = $db->addGameHome( $resolved_remote_server_id, $user_id, $home_cfg_id, $game_path, $home_name, $remote_control_password, $ftp_password);

				if (!$home_id) {
					$errMsg = "Failed to create game home record for order_id={$order_id}, user_id={$user_id}, home_cfg_id={$home_cfg_id}.";
					billing_log_provision_error($db, $db_prefix, intval($order_id), 0, intval($user_id), $resolved_remote_server_id, $ip_id, 0, $mod_cfg_id, $errMsg);
					echo "<div class='failure'><p><strong>Provisioning failed for order #" . intval($order_id) . ":</strong> " . htmlspecialchars($errMsg) . "</p></div>";
					$failed_count++;
					continue;
				}

				// ---------------------------------------------------------------
				// Assign next available port to the new server home.
				// ---------------------------------------------------------------
				$next_port = $db->getNextAvailablePort($ip_id, $home_cfg_id);
				if ($next_port === false || $next_port === null) {
					$errMsg = "No available port for ip_id={$ip_id}, home_cfg_id={$home_cfg_id} (order_id={$order_id}). "
					        . "Configure a port range for this IP/game type in the panel.";
					$db->deleteGameHome($home_id);
					billing_log_provision_error($db, $db_prefix, intval($order_id), 0, intval($user_id), $resolved_remote_server_id, $ip_id, 0, $mod_cfg_id, $errMsg);
					echo "<div class='failure'><p><strong>Provisioning failed for order #" . intval($order_id) . ":</strong> " . htmlspecialchars($errMsg) . "</p></div>";
					$failed_count++;
					continue;
				}

				$add_port = $db->addGameIpPort($home_id, $ip_id, $next_port);
				if (!$add_port) {
					$errMsg = "Failed to assign port {$next_port} to home_id={$home_id} (ip_id={$ip_id}, order_id={$order_id}).";
					$db->deleteGameHome($home_id);
					billing_log_provision_error($db, $db_prefix, intval($order_id), 0, intval($user_id), $resolved_remote_server_id, $ip_id, $next_port, $mod_cfg_id, $errMsg);
					echo "<div class='failure'><p><strong>Provisioning failed for order #" . intval($order_id) . ":</strong> " . htmlspecialchars($errMsg) . "</p></div>";
					$failed_count++;
					continue;
				}
				
				//Assign the Game Mod to the Game Home
				$mod_id = $db->addModToGameHome( $home_id, $mod_cfg_id );
				if (!$mod_id) {
					$errMsg = "Failed to assign mod_cfg_id={$mod_cfg_id} to home_id={$home_id} (order_id={$order_id}). The mod may already be assigned or does not exist.";
					// Try to recover the mod_id if it already exists (e.g. duplicate provisioning attempt)
					$existingMod = $db->resultQuery(
						"SELECT mod_id FROM `{$db_prefix}game_mods`
						 WHERE home_id=" . intval($home_id) . "
						   AND mod_cfg_id=" . intval($mod_cfg_id) . "
						 LIMIT 1"
					);
					if (!empty($existingMod[0]['mod_id'])) {
						$mod_id = intval($existingMod[0]['mod_id']);
					} else {
						$db->delGameIpPort($home_id, $ip_id, $next_port);
						$db->deleteGameHome($home_id);
						billing_log_provision_error($db, $db_prefix, intval($order_id), intval($home_id), intval($user_id), $resolved_remote_server_id, $ip_id, $next_port, $mod_cfg_id, $errMsg);
						echo "<div class='failure'><p><strong>Provisioning failed for order #" . intval($order_id) . ":</strong> " . htmlspecialchars($errMsg) . "</p></div>";
						$failed_count++;
						continue;
					}
				}
				$db->updateGameModParams( $max_players, $extra_params, $cpu_affinity, $nice, $home_id, $mod_cfg_id );
				$db->assignHomeTo( "user", $user_id, $home_id, $access_rights );
				
				//Get The home info without mods in 1 array (Necesary for remote connection).
				$home_info = $db->getGameHomeWithoutMods($home_id);
				
				//Create the remote connection
				$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
								
				//Get Full home info in 1 array
				$home_info = $db->getGameHome($home_id);
				
				//Read the Game Config from the XML file
				$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
				
				//Get Values from XML
				$modkey = $home_info['mods'][$mod_id]['mod_key'];
				$mod_xml = xml_get_mod($server_xml, $modkey);
				$installer_name = $mod_xml->installer_name;
				$mod_cfg_id = $home_info['mods'][$mod_id]['mod_cfg_id'];
				
			//Get Preinstall commands from xml
				$precmd = $server_xml->pre_install;

					
				//Get Postinstall commands from xml
				$postcmd = $server_xml->post_install; 


				//Enable FTP account in remote server
				if ($ftp == "enabled")
				{
					$remote->ftp_mgr("useradd", $home_info['home_id'], $home_info['ftp_password'], $home_info['home_path']);
					$db->changeFtpStatus('enabled',$home_info['home_id']);
				}
				
				//Install files for this service in the remote server
				$exec_folder_path = clean_path($home_info['home_path'] . "/" . $server_xml->exe_location );
				$exec_path = clean_path($exec_folder_path . "/" . $server_xml->server_exec_name );

				if ( (string)$server_xml->installer === "steamcmd" && !empty((string)$installer_name) )
				{
					if( preg_match("/win32/", $server_xml->game_key) OR preg_match("/win64/", $server_xml->game_key) ) 
						$cfg_os = "windows";
					elseif( preg_match("/linux/", $server_xml->game_key) )
						$cfg_os = "linux";
					
					// Some games like L4D2 require anonymous login
					if($mod_xml->installer_login){
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
				else
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
				echo "<h4><br><p>".get_lang('starting_installations')."</p></h4><br>";
				//PANEL LOG 
                                $db->logger( "CREATED NEW SERVER " . $home_id);
				// SEND EMAIL to new server only
				if($order['end_date'] == 0){
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

			// Set order status to 'Active' (server provisioned and current)
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
							billing_status='Active'
						WHERE order_id=" . $db->realEscapeSingle($order_id));

			$db->query("UPDATE `{$db_prefix}billing_transactions`
						SET home_id=" . $db->realEscapeSingle($home_id) . "
						WHERE invoice_id IN (SELECT invoice_id FROM `{$db_prefix}billing_invoices` WHERE order_id=" . $db->realEscapeSingle($order_id) . ")");

			// Set billing_status and next_invoice_date on server_homes
			$db->query("UPDATE `{$db_prefix}server_homes`
						SET billing_status     = 'Active',
							next_invoice_date  = '" . $db->realEscapeSingle($end_date_str) . "',
							billing_enabled    = 1
						WHERE home_id = " . $db->realEscapeSingle($home_id));
			
			$provisioned_count++;
						
		}
					
        $db->query( "UPDATE `{$db_prefix}game_mods` SET max_players= ".$order['max_players']." WHERE home_id=".$db->realEscapeSingle($home_id));

	// Show results and redirect
	if ($provisioned_count > 0) {
		echo "<div class='success'>";
		echo "<h3>Server Provisioning Complete</h3>";
		echo "<p>Successfully provisioned $provisioned_count server(s). Your server(s) are now active.</p>";
		echo "</div>";
		echo "<p><a href='home.php?m=gamemanager&p=game_monitor' class='btn'>View My Servers</a></p>";
		// Auto-redirect after 3 seconds
		echo "<script>setTimeout(function(){ window.location.href='home.php?m=gamemanager&p=game_monitor'; }, 3000);</script>";
	} else {
		echo "<div class='info'>";
		echo "<p>No servers to provision. All orders have already been processed.</p>";
		echo "</div>";
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
