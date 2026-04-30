<?php
require_once __DIR__ . '/../../includes/lib_remote.php';
require_once __DIR__ . '/../config_games/server_config_parser.php';

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

function exec_ogp_module()
{
	global $db,$view,$settings;

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
	
	// Handle provision_all request - provision all paid orders for this user
	if ($provision_all) {
		if ( $isAdmin ){
			$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE status='paid' ORDER BY order_id" );
		} else {
			$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE user_id=".$db->realEscapeSingle($user_id)." AND status='paid' ORDER BY order_id" );
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
			$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE order_id IN ($idList) AND status='paid'" );
		} else {
			$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE order_id IN ($idList) AND user_id=".$db->realEscapeSingle($user_id)." AND status='paid'" );
		}
	}
	$processed_orders = array();
	if( !empty($orders) )
	{
		$provisioned_count = 0;
		$failed_count = 0;
		
		foreach ((array)$orders as $order)
		{
			$order_id = $order['order_id'];
			$processed_orders[] = intval($order_id);
			$service_id = $order['service_id'];
			$home_name = $order['home_name'];
			$remote_control_password = $order['remote_control_password'];
			$ftp_password = $order['ftp_password'];
			$ip = $order['ip'];
			$max_players = $order['max_players'];
			$user_id = $order['user_id'];
			$extended = isset($order['extended']) && $order['extended'] == "1" ? TRUE : FALSE;
			//Query service info	
			$service = $db->resultQuery( "SELECT * 
							   FROM OGP_DB_PREFIXbilling_services 
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
						
			if($extended)
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

  //WEBHOOK Discord======================================================================================= 
               
    
               $webhookurl = $settings['webhookurl'];
             
               $msg = "The ". $home_name ." server ID #". $home_id . " has just been renewed.";
               $json_data = array ('content'=>"$msg");
               $make_json = json_encode($json_data);
               $ch = curl_init( $webhookurl );
               curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
               curl_setopt( $ch, CURLOPT_POST, 1);
               curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);
               curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
               curl_setopt( $ch, CURLOPT_HEADER, 0);
               curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
               $response = curl_exec( $ch );
               //If you need to debug, or find out why you can't send message uncomment line below, and execute script.
               //echo $response;
               //end WEBHOOK Discord

			}
			else
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
				
				//Add IP:Port Pair to the Game Home
			//need to get the IP_ID for this remote server.
				$result = $db->resultQuery("SELECT ip_id FROM OGP_DB_PREFIXremote_server_ips WHERE remote_server_id=".$ip);
			    	foreach ((array)$result as $rs)
			{
				$ip_id = $rs['ip_id'];
			}
				$add_port = $db->addGameIpPort( $home_id, $ip_id, $db->getNextAvailablePort($ip_id,$home_cfg_id) );
				
				//Assign the Game Mod to the Game Home
				$mod_id = $db->addModToGameHome( $home_id, $mod_cfg_id );
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
				// -Steam
				$exec_folder_path = clean_path($home_info['home_path'] . "/" . $server_xml->exe_location );
				$exec_path = clean_path($exec_folder_path . "/" . $server_xml->server_exec_name );

				if ($install_method == "steam")
				{
					if ( $server_xml->installer == "steamcmd" )
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
				}
				// -Rsync
				elseif ($install_method == "rsync")
				{

					//Rsync Server
					$url = "files.iaregamer.com";
					//OS
					if( preg_match("/win32/", $server_xml->game_key) OR preg_match("/win64/", $server_xml->game_key) ) 
						$os = "windows";
					elseif( preg_match("/linux/", $server_xml->game_key) )
						$os = "linux";
					//Rsync Game Name
					//JUST SET RS_GNAME TO GAME xml NAME
					$rs_gname =  $server_xml->game_key;

					//Starting Sync
					$full_url = "$url/rsync_installer/$rs_gname/$os/";



					$remote->start_rsync_install($home_id,$home_info['home_path'],"$full_url",$exec_folder_path,$exec_path,$precmd,$postcmd);
				}
				// -Manual
				elseif ($install_method == "manual")
				{
					// Start File Download and uncompress
					$filename = !empty($manual_url) ? substr($manual_url, -9) : "";
					$remote->start_file_download($manual_url,$home_info['home_path'],$filename,"uncompress");
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

					  
	//WEBHOOK Discord======================================================================================= 
               
               $webhookurl = "https://discord.com/api/webhooks/710275918274363412/g5Tr-EUdEnLfFryOlscxJ6FuPiSJuE6EMKRYmh9UGMiqTUxU5-y9CQrBlDJW7znr0Tol";
	       //$settings['webhookurl'];

             
               $msg = "A new server, ". $home_name ." ID #". $home_id . ", has just been created.";
               $json_data = array ('content'=>"$msg");
               $make_json = json_encode($json_data);
               $ch = curl_init( $webhookurl );
               curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
               curl_setopt( $ch, CURLOPT_POST, 1);
               curl_setopt( $ch, CURLOPT_POSTFIELDS, $make_json);
               curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
               curl_setopt( $ch, CURLOPT_HEADER, 0);
               curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
               $response = curl_exec( $ch );
               //If you need to debug, or find out why you can't send message uncomment line below, and execute script.
               //echo $response;
               //end WEBHOOK Discord
				}
				// END EMAIL
				
				
			}
			// Set expiration date in ogp database
			//status is: in-cart, paid, installed, invoiced, suspended, deleted
			// 'paid' - order has been paid but server not yet created
			// 'installed' - server created and active
			// 'invoiced' - invoice created for renewal
			// 'suspended' - server suspended for non-payment
			// 'deleted' - server deleted after extended suspension
			//end_date the server will be suspended 
			//in cron_shop the end_date is used to delete the server
			//several days after being suspended
			if ($order['invoice_duration'] == "day")
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
				$end_date = strtotime('+'.$order['qty'].' month'); 

				}
			else{
			//this is a renewel, start from end of previous order
				$current_end = strtotime($order['end_date']);
				if ($current_end === false) {
					$current_end = time(); // fallback to now if date is invalid
				}
                $end_date = strtotime('+'.$order['qty'].' month', $current_end); 
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
			// set order status to 'installed' to indicate server has been provisioned
			$db->query("UPDATE OGP_DB_PREFIXbilling_orders
						SET status='installed' 
						WHERE order_id=".$db->realEscapeSingle($order_id));
	
			// set the order expiration
			$db->query("UPDATE OGP_DB_PREFIXbilling_orders
						SET end_date='" . $db->realEscapeSingle($end_date) . "' 
						WHERE order_id=".$db->realEscapeSingle($order_id));
						
			// Save home id created by this order
			$db->query("UPDATE OGP_DB_PREFIXbilling_orders
						SET home_id='" . $db->realEscapeSingle($home_id) . "' WHERE order_id=".$db->realEscapeSingle($order_id));
			
			$provisioned_count++;
						
		}
					
        $db->query( "UPDATE OGP_DB_PREFIXgame_mods SET max_players= ".$order['max_players']." WHERE home_id=".$db->realEscapeSingle($home_id));

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




