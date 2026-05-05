<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

function get_query_port($server_xml, $server_port) {
	if ($server_xml->query_port) {
		if ($server_xml->query_port['type'] == 'add') {
			return $server_port + $server_xml->query_port;
		}

		if ($server_xml->query_port['type'] == 'subtract') {
			return $server_port - $server_xml->query_port;
		}
	}

	return $server_port;
}

function get_start_cmd($remote,$server_xml,$home_info,$mod_id,$ip,$port,$db)
{	
	$last_param = json_decode($home_info['last_param'], True);
	
	$os = $remote->what_os();
	
	$isAdmin = false;
	if(hasValue($_SESSION) && hasValue($_SESSION['user_id'])){
		$isAdmin = $db->isAdmin($_SESSION['user_id']);
	}
	
	$cli_param_data['GAME_TYPE'] = $home_info['mods'][$mod_id]['mod_key'];
	$cli_param_data['IP'] = $ip;
	$cli_param_data['PORT'] = $port;
	$cli_param_data['HOSTNAME'] = $home_info['home_name'];
	$cli_param_data['PID_FILE'] = "ogp_game_startup.pid";
	
	// Linux
	if( preg_match("/Linux/", $os) )
	{
		if(preg_match("/_win(32|64)?$/", $home_info['game_key']))
		{
			$home_path_wine = $remote->exec("winepath -w ".$home_info['home_path']);
			$home_path_wine = str_replace("\\","\\\\", $home_path_wine);
			$home_path_wine = trim($home_path_wine);
			$cli_param_data['BASE_PATH'] = $home_path_wine;
			$cli_param_data['HOME_PATH'] = $home_path_wine;
			$cli_param_data['SAVE_PATH'] = $home_path_wine;
			$cli_param_data['OUTPUT_PATH'] = $home_path_wine;
			$cli_param_data['USER_PATH'] = $home_path_wine;
		}
		else
		{
			$cli_param_data['BASE_PATH'] = $home_info['home_path'];
			$cli_param_data['HOME_PATH'] = $home_info['home_path'];
			$cli_param_data['SAVE_PATH'] = $home_info['home_path'];
			$cli_param_data['OUTPUT_PATH'] = $home_info['home_path'];
			$cli_param_data['USER_PATH'] = $home_info['home_path'];
		}
	}
	// Windows
	elseif( preg_match("/CYGWIN/", $os) )
	{
		$home_path_win = $remote->exec("cygpath -w ".$home_info['home_path']);
		$home_path_win = str_replace("\\","\\\\", $home_path_win);
		$home_path_win = trim($home_path_win);
		$cli_param_data['BASE_PATH'] = $home_path_win;
		$cli_param_data['HOME_PATH'] = $home_path_win;
		$cli_param_data['SAVE_PATH'] = $home_path_win;
		$cli_param_data['OUTPUT_PATH'] = $home_path_win;
		$cli_param_data['USER_PATH'] = $home_path_win;
	}
	
	if ($server_xml->protocol == "gameq")
	{
		$cli_param_data['QUERY_PORT'] = get_query_port ($server_xml, $port);
	}
	elseif ($server_xml->protocol == "lgsl")
	{
		require('protocol/lgsl/lgsl_protocol.php');
		$get_ports = lgsl_port_conversion((string)$server_xml->lgsl_query_name, $port, "", "");
		$cli_param_data['QUERY_PORT'] = $get_ports['1'];
	}
	elseif ($server_xml->protocol == "teamspeak3")
	{
		$cli_param_data['QUERY_PORT'] = "10011";
	}
	
	$cli_param_data['MAP'] = ($last_param === NULL or !isset($last_param['map'])) ?  "" : $last_param['map'];
	$cli_param_data['PLAYERS'] = ($last_param === NULL or !isset($last_param['players'])) ? 
								 isset($home_info['mods'][$mod_id]['max_players']) ? 
								 $home_info['mods'][$mod_id]['max_players'] : "1" : $last_param['players'];
	$cli_param_data['CONTROL_PASSWORD'] = $home_info['control_password'];
	
	$start_cmd = "";
	// If the template is empty then these are not needed.
	if ( $server_xml->cli_template )
	{
		$start_cmd = $server_xml->cli_template;
		if ( $server_xml->cli_params )
		{
			foreach ( $server_xml->cli_params->cli_param as $cli )
			{
				// If s is found the param is seperated with space
				$add_space = preg_match( "/s/", $cli['options'] ) > 0 ? " " : "";
				$cli_value = $cli_param_data[(string) $cli['id'] ];
				// If q is found we add quotes around the value.
				if ( preg_match( "/q/", $cli['options'] ) > 0 )
				{
					$cli_value = "\"".$cli_value."\"";
				}
				$start_cmd = preg_replace( "/%".$cli['id']."%/",
					$cli['cli_string'].$add_space.$cli_value, $start_cmd );
			}
		}
		
		if ( $server_xml->reserve_ports )
		{
			foreach ( $server_xml->reserve_ports->port as $reserve_port )
			{
				// If s is found the param is seperated with space
				$add_space = preg_match( "/s/", $reserve_port['options'] ) > 0 ? " " : "";
				$cli_value = $reserve_port['type'] == "add" ? $port + (string) $reserve_port:
															  $port - (string) $reserve_port;
				// If q is found we add quotes around the value.
				if ( preg_match( "/q/", $reserve_port['options'] ) > 0 )
				{
					$cli_value = "\"".$cli_value."\"";
				}
				$start_cmd = preg_replace( "/%".$reserve_port['id']."%/",
					$reserve_port['cli_string'].$add_space.$cli_value, $start_cmd );
			}
		}
	}

	if ( $isAdmin )
	{
		$home_info['access_rights'] = "ufpet";
	}
						
	$param_access_enabled = preg_match("/p/",$home_info['access_rights']) > 0 ? TRUE : FALSE; 
	
	if ($param_access_enabled && $last_param !== NULL and isset($server_xml->server_params->param) )
	{
		foreach($server_xml->server_params->param as $param)
		{						
			foreach ((array)$last_param as $paramKey => $paramValue)
			{
				if (!isset($paramValue))
					$paramValue = (string)$param->default;
				
				if ($param['key'] == $paramKey)
				{	
					if (0 == strlen($paramValue))
						continue;
					if ($param['key'] == $paramValue) // it's a checkbox
						$new_param = $paramKey;
					elseif($param->option == "ns" or $param->options == "ns")
						$new_param = $paramKey.clean_server_param_value($paramValue, $server_xml->cli_allow_chars);
					elseif($param->option == "q" or $param->options == "q")
						$new_param = $paramKey . '"' . clean_server_param_value($paramValue, $server_xml->cli_allow_chars) . '"';
					elseif($param->option == "s" or $param->options == "s")
						$new_param = $paramKey . ' ' . clean_server_param_value($paramValue, $server_xml->cli_allow_chars);
					else
						$new_param = $paramKey . ' "' . clean_server_param_value($paramValue, $server_xml->cli_allow_chars) . '"';
				  
					if ($param['id'] == NULL || $param['id'] == "")
						$start_cmd .= ' '.$new_param;
					else
						$start_cmd = preg_replace( "/%".$param['id']."%/", $new_param, $start_cmd );
				}			  
			}
			
			if ($param['id'] != NULL && $param['id'] != ""){
				$start_cmd = preg_replace( "/%".$param['id']."%/", '', $start_cmd );
			}
		} 
	}
	
	$extra_param_access_enabled = preg_match("/e/",$home_info['access_rights']) > 0 ? TRUE:FALSE;
			
	if ( array_key_exists('extra', (array)$last_param) && $extra_param_access_enabled )
		$extra_default = $last_param['extra'];
	else
		$extra_default = $home_info['mods'][$mod_id]['extra_params'];
		
	$start_cmd .= " ".str_replace("\\\\", "\\", clean_server_param_value($extra_default, $server_xml->cli_allow_chars));

	return $start_cmd;
}

// This function is used to batch stop/restart servers in background.
function exec_operation( $action, $home_id, $mod_id, $ip, $port, $override = false )
{
    if(!is_numeric($port))
		return FALSE;
	if(!preg_match("/[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}/",$ip))
		return FALSE;
	
	global $db;
	
	$isAdmin = false;
	if(hasValue($_SESSION) && hasValue($_SESSION['user_id'])){
		$isAdmin = $db->isAdmin($_SESSION['user_id']);
	}
	
	if($override || $isAdmin) 
		$home_info = $db->getGameHome($home_id);
	else
		$home_info = $db->getUserGameHome($_SESSION['user_id'],$home_id);
	
    if( $home_info === FALSE )
        return FALSE;
    
    $server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);

    if ( !$server_xml )
        return FALSE;
	
	require_once('includes/lib_remote.php');
	$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
	$os = $remote->what_os();
		
	if ( $action != "stop" )
	{
		if( $server_xml->replace_texts )
		{
			$server_home = $home_info;
			if(	isset($server_xml->lgsl_query_name) )
				require_once('protocol/lgsl/lgsl_protocol.php');
			require_once("modules/gamemanager/cfg_text_replace.php");
		}
	}

	$screen_running = $remote->is_screen_running(OGP_SCREEN_TYPE_HOME,$home_info['home_id']) === 1;
	
	if ( $action == "stop" AND $screen_running )
	{
		$remote_retval = $remote->remote_stop_server($home_info['home_id'],
			$ip, $port, $server_xml->control_protocol,
			$home_info['control_password'],$server_xml->control_protocol_type, $home_info['home_path']);
		$db->logger(get_lang_f('server_stopped', $home_info['home_name'] ) . "($ip:$port)");
		if ( $remote_retval === -1 )
			return FALSE;
		elseif( $remote_retval === -2 )
			return FALSE;
		else
		{
				$firewall_settings = $db->getFirewallSettings($home_info['remote_server_id']);
				if ($firewall_settings['status'] == "enable")
				{
					$ip_ports = $db->getHomeIpPorts($home_info['home_id']);
					if (!is_array($ip_ports)) {
						$ip_ports = [];
					}
					foreach ((array)$ip_ports as $ip_port)
					{
					if ($server_xml->protocol == "gameq")
					{
						$query_port = get_query_port($server_xml, $ip_port['port']);
					}
					elseif ($server_xml->protocol == "lgsl")
					{
						require('protocol/lgsl/lgsl_protocol.php');
						$get_ports = lgsl_port_conversion((string)$server_xml->lgsl_query_name, $ip_port['port'], "", "");
						$query_port = $get_ports['1'];
					}
					elseif ($server_xml->protocol == "teamspeak3")
					{
						$query_port = "10011";
					}
					set_firewall($remote, $firewall_settings, 'deny', $ip_port['port'], $ip_port['ip']);
					if(isset($query_port) and $query_port != "" and $query_port != $ip_port['port'])
						set_firewall($remote, $firewall_settings, 'deny', $query_port, $ip_port['ip']);
				}
			}
			return TRUE;
		}
	}
	elseif ( $action == "restart" AND $screen_running )
	{
		$start_cmd = get_start_cmd($remote,$server_xml,$home_info,$mod_id,$ip,$port,$db);
		// Do text replacements in cfg file
		if( $server_xml->replace_texts )
		{
			if (is_array($home_info['mods'][$mod_id])) {
				foreach($home_info['mods'][$mod_id] as $key => $value)
				{
					$home_info[$key] = $value;
				}
			}
			$server_home = $home_info;
			if(	isset($server_xml->lgsl_query_name) )
				require_once('protocol/lgsl/lgsl_protocol.php');
			require_once("modules/gamemanager/cfg_text_replace.php");
		}
		
		// Run pre-start commands
		if(isset($server_xml->pre_start) && !empty($server_xml->pre_start)){
			$preStart = trim($server_xml->pre_start);
		}else{
			$preStart = "";
		}
			
		// Environment variables
		if(isset($server_xml->environment_variables) && !empty($server_xml->environment_variables)){
			$envVars = trim($server_xml->environment_variables);
		}else{
			$envVars = "";
		}
		
		// Additional files to lock
		if(isset($server_xml->lock_files) && !empty($server_xml->lock_files)){
			$lockFiles = trim($server_xml->lock_files);
		}else{
			$lockFiles = "";
		}
		
		if(!empty($lockFiles)){
			// Linux only call
			if(preg_match("/Linux/", $os)){
				$lockedFilesStatus = $remote->lock_additional_home_files($home_info['home_path'], $lockFiles, "lock");
			}
		}
		
		$remote_retval = $remote->remote_restart_server($home_info['home_id'],$ip,$port,$server_xml->control_protocol,
														$home_info['control_password'],$server_xml->control_protocol_type,$home_info['home_path'],
														$server_xml->server_exec_name,$server_xml->exe_location,$start_cmd,
														$home_info['mods'][$mod_id]['cpu_affinity'],
														$home_info['mods'][$mod_id]['nice'],
														$preStart,
														$envVars,
														$server_xml->game_key,
														(isset( $server_xml->console_log ) ? $server_xml->console_log : ""));
		$db->logger(get_lang_f('server_restarted', $home_info['home_name']) . "($ip:$port)");
		if ( $remote_retval === -1 )
			return FALSE;
		else if ( $remote_retval === -2 )
			return FALSE;
		else
		{
			$ip_id = $db->getIpIdByIp($ip);
			$db->delServerStatusCache($ip_id,$port);
		}
	}
	elseif ( $action == "start" AND ! $screen_running )
	{
		$start_cmd = get_start_cmd($remote,$server_xml,$home_info,$mod_id,$ip,$port,$db);
		// Do text replacements in cfg file
		if( $server_xml->replace_texts )
		{
			if (is_array($home_info['mods'][$mod_id])) {
				foreach($home_info['mods'][$mod_id] as $key => $value)
				{
					$home_info[$key] = $value;
				}
			}
			$server_home = $home_info;
			if(	isset($server_xml->lgsl_query_name) )
				require_once('protocol/lgsl/lgsl_protocol.php');
			require_once("modules/gamemanager/cfg_text_replace.php");
		}
		
		// Run pre-start commands
		if(isset($server_xml->pre_start) && !empty($server_xml->pre_start)){
			$preStart = trim($server_xml->pre_start);
		}else{
			$preStart = "";
		}
		
		// Environment variables
		if(isset($server_xml->environment_variables) && !empty($server_xml->environment_variables)){
			$envVars = trim($server_xml->environment_variables);
		}else{
			$envVars = "";
		}
		
		// Additional files to lock
		if(isset($server_xml->lock_files) && !empty($server_xml->lock_files)){
			$lockFiles = trim($server_xml->lock_files);
		}else{
			$lockFiles = "";
		}
		
		if(!empty($lockFiles)){
			// Linux only call
			if(preg_match("/Linux/", $os)){
				$lockedFilesStatus = $remote->lock_additional_home_files($home_info['home_path'], $lockFiles, "lock");
			}
		}
		
		$start_retval = $remote->universal_start($home_info['home_id'],
												 $home_info['home_path'],
												 $server_xml->server_exec_name, 
												 $server_xml->exe_location,
												 $start_cmd, $port, $ip,
												 $home_info['mods'][$mod_id]['cpu_affinity'],
												 $home_info['mods'][$mod_id]['nice'],
												 $preStart,
												 $envVars,
												 $server_xml->game_key,
												 (isset( $server_xml->console_log ) ? $server_xml->console_log : "")
												 );
		$db->logger(get_lang('server_started') . " (".$home_info['home_name']." $ip:$port)");
		if( $start_retval == AGENT_ERROR_NOT_EXECUTABLE or $start_retval <= 0)
			return FALSE;
		else
		{
				$firewall_settings = $db->getFirewallSettings($home_info['remote_server_id']);
				if ($firewall_settings['status'] == "enable")
				{
					$ip_ports = $db->getHomeIpPorts($home_info['home_id']);
					if (!is_array($ip_ports)) {
						$ip_ports = [];
					}
					foreach ((array)$ip_ports as $ip_port)
					{
					if ($server_xml->protocol == "gameq")
					{
						$query_port = get_query_port($server_xml, $ip_port['port']);
					}
					elseif ($server_xml->protocol == "lgsl")
					{
						require('protocol/lgsl/lgsl_protocol.php');
						$get_ports = lgsl_port_conversion((string)$server_xml->lgsl_query_name, $ip_port['port'], "", "");
						$query_port = $get_ports['1'];
					}
					elseif ($server_xml->protocol == "teamspeak3")
					{
						$query_port = "10011";
					}
					set_firewall($remote, $firewall_settings, 'allow', $ip_port['port'], $ip_port['ip']);
					if(isset($query_port) and $query_port != "" and $query_port != $ip_port['port'])
						set_firewall($remote, $firewall_settings, 'allow', $query_port, $ip_port['ip']);
				}
			}
			$ip_id = $db->getIpIdByIp($ip);
			$db->delServerStatusCache($ip_id,$port);
			return TRUE;
		}
	}
}

function get_monitor_buttons($server_home, $server_xml)
{
	global $db;
	$buttons = array();
	$installed_modules = $db->getInstalledModules();
	if (!is_array($installed_modules)) {
		$installed_modules = [];
	}
	foreach ((array)$installed_modules as $installed_module)
	{
		$buttons_file = "modules/$installed_module[folder]/monitor_buttons.php";
		if(file_exists($buttons_file))
		{
			require($buttons_file);
			if (is_array($module_buttons)) {
				$buttons = array_merge($buttons, $module_buttons);
			}
			unset($module_buttons);
		}
	}
	$buttons_html = "";
	if (is_array($buttons)) {
		foreach ((array)$buttons as $button)
		$buttons_html .= $button."\n";
	}
	return $buttons_html;
}

/**
 * Returns an HTML-formatted expiration label for the given server home_id.
 *
 * Source of truth: billing_orders.end_date (DATETIME, NULL means no date set).
 * The most recent active billing order for the home is resolved via a LEFT JOIN
 * from server_homes to billing_orders — the same relationship used by the billing
 * cron (cron-shop.php) and order-provisioning logic (create_servers.php).
 *
 * billing_orders.home_id is VARCHAR(255); server_homes.home_id is INT.
 * MySQL handles the implicit cast in the JOIN condition automatically.
 * Only rows where home_id != '0' are considered (home_id = '0' means not yet
 * provisioned).
 *
 * Color thresholds:
 *   green  – more than 10 days remaining  (shows actual date)
 *   yellow – 4–10 days remaining          (shows "X days remaining")
 *   red    – 1–3 days remaining           (shows "X days remaining")
 *   red    – less than 1 day but not yet expired  (shows "Less than 1 day remaining")
 *   red    – already expired/suspended    (shows "Suspended")
 *   red    – no order or NULL/invalid end_date (shows "No expiration date found")
 *
 * @param int $home_id  The server home ID.
 * @return string       Safe HTML string ready for inline display.
 */
function get_server_billing_expiration_html(int $home_id): string
{
	global $db;

	// Use a LEFT JOIN from server_homes to billing_orders — the same join pattern
	// used throughout the billing module (cron-shop.php, create_servers.php).
	// billing_orders.home_id is VARCHAR; server_homes.home_id is INT.  MySQL
	// performs the implicit cast for the equality comparison.
	// We exclude billing_orders rows where home_id = '0' (not yet provisioned).
	// OGP_DB_PREFIX is replaced at runtime by the panel DB wrapper (str_replace).
	$rows = $db->resultQuery(
		"SELECT bo.end_date
		   FROM OGP_DB_PREFIXserver_homes sh
		   LEFT JOIN OGP_DB_PREFIXbilling_orders bo
		          ON bo.home_id = sh.home_id
		         AND bo.home_id != '0'
		         AND bo.status IN ('Active','Invoiced')
		  WHERE sh.home_id = " . intval($home_id) . "
		  ORDER BY bo.end_date DESC
		  LIMIT 1"
	);

	// If the server_homes row itself does not exist, or the query failed, bail out.
	// empty($rows) is true when resultQuery returns FALSE (0 rows or error).
	if ($rows === false) {
		// Query error — billing_orders table may be missing or schema mismatch.
		return "<span style='color:red;'>No expiration date found</span>";
	}

	// A LEFT JOIN row always comes back (sh row exists), but bo.end_date may be NULL
	// when there is no matching billing_orders record for this server.
	if (empty($rows[0]['end_date'])) {
		return "<span style='color:red;'>No expiration date found</span>";
	}

	// Parse end_date using DateTime for PHP 8.3+ compatibility.
	try {
		$end_dt = new DateTime($rows[0]['end_date']);
	} catch (\Exception $e) {
		return "<span style='color:red;'>No expiration date found</span>";
	}

	$now  = new DateTime();
	$diff = $now->diff($end_dt);

	if ($end_dt <= $now) {
		// Server billing period has already passed — treat as suspended.
		return "<span style='color:red;'>Suspended</span>";
	}

	// $diff->days is the total number of whole days between $now and $end_dt.
	$days_remaining = (int)$diff->days;
	$display_date   = htmlentities($end_dt->format('Y-m-d H:i'), ENT_QUOTES, 'UTF-8');

	if ($days_remaining > 10) {
		// More than 10 days: show the actual expiration date in green.
		return "<span style='color:green;'>" . $display_date . "</span>";
	} elseif ($days_remaining >= 4) {
		// 4–10 days: yellow warning (darkgoldenrod for WCAG contrast).
		return "<span style='color:#B8860B;'>" . htmlentities($days_remaining, ENT_QUOTES, 'UTF-8') . " days remaining</span>";
	} elseif ($days_remaining >= 1) {
		// 1–3 days: urgent red warning.
		return "<span style='color:red;'>" . htmlentities($days_remaining, ENT_QUOTES, 'UTF-8') . " days remaining</span>";
	} else {
		// Less than one full day but not yet expired.
		return "<span style='color:red;'>Less than 1 day remaining</span>";
	}
}
?>
