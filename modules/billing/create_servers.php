<?php
require_once("includes/lib_remote.php");
require_once("modules/config_games/server_config_parser.php");

function createMysqlDatabase($home_id, $settings, $db) {
	// Generate database name and user based on server ID
	$dbID = "server_" . $home_id;
	$dbPass = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789"), 0, 12);
	
	// Check if MySQL auto-creation is enabled
	if (!isset($settings['mysql_auto_create']) || $settings['mysql_auto_create'] != '1') {
		return false;
	}
	
	// Check if we have the required MySQL settings
	if (empty($settings['mysql_root_user']) || empty($settings['mysql_root_password']) || empty($settings['mysql_host'])) {
		return false;
	}
	
	$mysql_host = $settings['mysql_host'];
	$mysql_user = $settings['mysql_root_user'];
	$mysql_pass = $settings['mysql_root_password'];
	$mysql_port = isset($settings['mysql_port']) ? $settings['mysql_port'] : '3306';
	
	try {
		// Create MySQL connection
		if (function_exists('mysqli_connect')) {
			$link = mysqli_connect($mysql_host, $mysql_user, $mysql_pass, "", $mysql_port);
			if (!$link) {
				$db->logger("MYSQL DB CREATION FAILED - Could not connect to MySQL server for server " . $home_id);
				return false;
			}
			
			// Create database
			$query = "CREATE DATABASE IF NOT EXISTS `" . mysqli_real_escape_string($link, $dbID) . "`";
			mysqli_query($link, $query);
			
			// Grant privileges to database user locally
			$query = "GRANT ALL ON `" . mysqli_real_escape_string($link, $dbID) . "`.* TO '" . mysqli_real_escape_string($link, $dbID) . "'@'localhost' IDENTIFIED BY '" . mysqli_real_escape_string($link, $dbPass) . "'";
			mysqli_query($link, $query);
			
			// Grant privileges to database user remotely
			$query = "GRANT ALL ON `" . mysqli_real_escape_string($link, $dbID) . "`.* TO '" . mysqli_real_escape_string($link, $dbID) . "'@'%' IDENTIFIED BY '" . mysqli_real_escape_string($link, $dbPass) . "'";
			mysqli_query($link, $query);
			
			// If there's a special user defined, grant it access too (like dayzhivemind in the original script)
			if (!empty($settings['mysql_special_user']) && !empty($settings['mysql_special_password'])) {
				$query = "GRANT ALL ON `" . mysqli_real_escape_string($link, $dbID) . "`.* TO '" . mysqli_real_escape_string($link, $settings['mysql_special_user']) . "'@'%' IDENTIFIED BY '" . mysqli_real_escape_string($link, $settings['mysql_special_password']) . "'";
				mysqli_query($link, $query);
			}
			
			// Flush privileges
			mysqli_query($link, "FLUSH PRIVILEGES");
			
			// Import SQL file if specified
			if (!empty($settings['mysql_init_sql_file'])) {
				$sql_file = $settings['mysql_init_sql_file'];
				if (file_exists($sql_file)) {
					$sql_content = file_get_contents($sql_file);
					mysqli_select_db($link, $dbID);
					mysqli_multi_query($link, $sql_content);
				}
			}
			
			mysqli_close($link);
		} else {
			// Fallback to old mysql functions
			$link = mysql_connect($mysql_host . ':' . $mysql_port, $mysql_user, $mysql_pass);
			if (!$link) {
				$db->logger("MYSQL DB CREATION FAILED - Could not connect to MySQL server for server " . $home_id);
				return false;
			}
			
			// Create database
			$query = "CREATE DATABASE IF NOT EXISTS `" . mysql_real_escape_string($dbID, $link) . "`";
			mysql_query($query, $link);
			
			// Grant privileges
			$query = "GRANT ALL ON `" . mysql_real_escape_string($dbID, $link) . "`.* TO '" . mysql_real_escape_string($dbID, $link) . "'@'localhost' IDENTIFIED BY '" . mysql_real_escape_string($dbPass, $link) . "'";
			mysql_query($query, $link);
			
			$query = "GRANT ALL ON `" . mysql_real_escape_string($dbID, $link) . "`.* TO '" . mysql_real_escape_string($dbID, $link) . "'@'%' IDENTIFIED BY '" . mysql_real_escape_string($dbPass, $link) . "'";
			mysql_query($query, $link);
			
			if (!empty($settings['mysql_special_user']) && !empty($settings['mysql_special_password'])) {
				$query = "GRANT ALL ON `" . mysql_real_escape_string($dbID, $link) . "`.* TO '" . mysql_real_escape_string($settings['mysql_special_user'], $link) . "'@'%' IDENTIFIED BY '" . mysql_real_escape_string($settings['mysql_special_password'], $link) . "'";
				mysql_query($query, $link);
			}
			
			mysql_query("FLUSH PRIVILEGES", $link);
			
			if (!empty($settings['mysql_init_sql_file'])) {
				$sql_file = $settings['mysql_init_sql_file'];
				if (file_exists($sql_file)) {
					$sql_content = file_get_contents($sql_file);
					mysql_select_db($dbID, $link);
					mysql_query($sql_content, $link);
				}
			}
			
			mysql_close($link);
		}
		
		// Add database to OGP tracking if mysql_server_id is configured
		if (!empty($settings['mysql_default_server_id'])) {
			// Try to add to the mysql_databases table directly using the main db connection
			$db->query("DELETE FROM OGP_DB_PREFIXmysql_databases WHERE db_user = '" . $db->realEscapeSingle($dbID) . "'");
			
			// Insert new database record directly
			$query = "INSERT INTO OGP_DB_PREFIXmysql_databases (mysql_server_id, home_id, db_user, db_passwd, db_name, enabled) VALUES (" .
					 $db->realEscapeSingle($settings['mysql_default_server_id']) . ", " .
					 $db->realEscapeSingle($home_id) . ", '" .
					 $db->realEscapeSingle($dbID) . "', '" .
					 $db->realEscapeSingle($dbPass) . "', '" .
					 $db->realEscapeSingle($dbID) . "', 1)";
			$db->query($query);
		}
		
		$db->logger("MYSQL DB CREATED - Database " . $dbID . " created for server " . $home_id);
		return true;
		
	} catch (Exception $e) {
		$db->logger("MYSQL DB CREATION FAILED - Error creating database for server " . $home_id . ": " . $e->getMessage());
		return false;
	}
}

function exec_ogp_module()
{
	global $db,$view,$settings;
	$user_id = $_SESSION['user_id'];
	if (isset($_POST['cart_id'])) {
		$cart_id = $_POST['cart_id'];
	}
	if(isset($_GET['cart_id'])){
		$cart_id = $_GET['cart_id'];
	}
	$cart_paid = $db->resultQuery( "SELECT paid FROM OGP_DB_PREFIXbilling_carts WHERE cart_id=".$db->realEscapeSingle($cart_id) );
	$isAdmin = $db->isAdmin( $_SESSION['user_id'] );
	if ( $isAdmin ){
		$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart_id) );
	} else {
		$orders = $db->resultQuery( "SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE cart_id=".$db->realEscapeSingle($cart_id)." AND user_id=".$db->realEscapeSingle($user_id) );
	}
	if( !empty($orders) and !empty($cart_paid) )
	{
		
		foreach($orders as $order)
		{
			$order_id = $order['order_id'];
			$service_id = $order['service_id'];
			$home_name = $order['home_name'];
			$remote_control_password = $order['remote_control_password'];
			$ftp_password = $order['ftp_password'];
			$ip = $order['ip'];
			$max_players = $order['max_players'];
			$user_id = $order['user_id'];
			$extended = $order['extended'] == "1" ? TRUE : FALSE;
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
                           FROM ogp_users, ogp_billing_orders
                           WHERE ogp_users.user_id = $user_id")[0]["users_email"];

      $message = "Your server, " . $home_name ." ID #". $home_id . " at " . $settings['panel_name'] . "  has just been renewed.<br>
                  Thank You for your continued support.<br>
				  If you have any questions or requests, visit our website  or contact us directly in our Discord Server.";

      $mail = mymail($email, $subject, $message, $settings);
	  $rundate = date('d/M/y G:i',$now);

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
			    	foreach ($result as $rs)
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
				
				// CREATE MYSQL DATABASE FOR NEW SERVERS
				if($order['finish_date'] == 0){
					$settings = $db->getSettings();
					createMysqlDatabase($home_id, $settings, $db);
				}
				
				// SEND EMAIL to new server only
				if($order['finish_date'] == 0){
					$settings = $db->getSettings();
					 $subject = "New Gameserver installed at " . $settings['panel_name'];
					  $email = $db->resultQuery("   SELECT DISTINCT users_email
										   FROM ogp_users, ogp_billing_orders
										   WHERE ogp_users.user_id = $user_id")[0]["users_email"];

					  $message =  "Your server, " . $home_name ." ID #". $home_id . " at " . $settings['panel_name'] . "  has just been created.<br>
					               Thank You for your continued support.<br>
                                   If you have any questions or requests, visit our website  or contact us directly in our Discord Server.
								  You can login to the Game Panel and click on Game Monitor to see your server.  <br><br>
								  Thank you!<br> ";
					  $mail = mymail($email, $subject, $message, $settings);
				  	  $rundate = date('d/M/y G:i',$now);

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
			//status is -3 -2 -1 0 and 1 
			// deleted, suspended, invoiced, inactive, active
			//finish_date the server will be suspended 
			//in cron_shop the finish_date is used to delete the server
			//several days after being suspended
			if ($order['invoice_duration'] == "day")
			{
				
				if($order['finish_date'] == 0){
				$finish_date = strtotime('+'.$order['qty'].' day'); 
			    $status = 1;
				}
			else{
			//this is a renewel, start from end of previous order
				$finish_date = strtotime('+'.$order['qty'].' day',$order['finish_date']); 
			    $status = 1;			
				}	
				
			}
			elseif ($order['invoice_duration'] == "month")
			{
			// this is a new order
			if($order['finish_date'] == 0){
				$finish_date = strtotime('+'.$order['qty'].' month'); 
			    $status = 1;

				}
			else{
			//this is a renewel, start from end of previous order
                $finish_date = strtotime('+'.$order['qty'].' month',$order['finish_date']); 
				$status = 1;
				}	
			}
			elseif ($order['invoice_duration'] == "year")
			{
				// this is a new order
			if($order['finish_date'] == 0){
				$finish_date = strtotime('+'.$order['qty'].' year'); 
				$status = 1;
				}
			else{
			//this is a renewel, start from end of previous order
                $finish_date = strtotime('+'.$order['qty'].' year',$order['finish_date']); 
				$status = 1;
				
				}	
				
			}
			// set order status
			$db->query("UPDATE OGP_DB_PREFIXbilling_orders
						SET status='" . $db->realEscapeSingle($status) . "' 
						WHERE order_id=".$db->realEscapeSingle($order_id));
	
			// set the order expiration
			$db->query("UPDATE OGP_DB_PREFIXbilling_orders
						SET finish_date='" . $db->realEscapeSingle($finish_date) . "' 
						WHERE order_id=".$db->realEscapeSingle($order_id));
						
			// Save home id created by this order
			$db->query("UPDATE OGP_DB_PREFIXbilling_orders
						SET home_id='" . $db->realEscapeSingle($home_id) . "' WHERE order_id=".$db->realEscapeSingle($order_id));
						
		}

		//Update Cart Payment Status as 3(paid and installed)
		$db->query("UPDATE OGP_DB_PREFIXbilling_carts
					SET paid=3
					WHERE cart_id=".$db->realEscapeSingle($cart_id));

		// Set payment/creation date
		$date = date('d M Y');
		$db->query("UPDATE OGP_DB_PREFIXbilling_carts 
					SET date='" . $db->realEscapeSingle($date) . "' 
					WHERE cart_id=".$db->realEscapeSingle($cart_id));
					
        $db->query( "UPDATE OGP_DB_PREFIXgame_mods SET max_players= ".$order['max_players']." WHERE home_id=".$db->realEscapeSingle($home_id));


		//Refresh to Game Monitor.
		$view->refresh("home.php?m=gamemanager&p=game_monitor");

	}
	
}
?>




