<?php
function get_function_args($main_request)
{
	//______________ Token
	$functions["token/test"] = array("token" => true);
	$functions["token/create"] = array("user" => true, "password" => true);
	
	//______________ Remote Servers
	$functions["server/list"] = array("token" => true);
	$functions["server/status"] = array("token" => true, "remote_server_id" => true);
	$functions["server/restart"] = array("token" => true, "remote_server_id" => true);
	$functions["server/create"] = array("token" => true, "agent_name" => true, "agent_ip" => true, "agent_port" => true, "agent_user" => true, "encryption_key" => true, "ftp_ip" => true, "ftp_port" => true, "timeout" => true, "use_nat" => true, "display_public_ip" => true);
	$functions["server/remove"] = array("token" => true, "remote_server_id" => true);
	$functions["server/add_ip"] = array("token" => true, "remote_server_id" => true, "ip" => true);
	$functions["server/remove_ip"] = array("token" => true, "remote_server_id" => true, "ip" => true);
	$functions["server/list_ips"] = array("token" => true, "remote_server_id" => true);
	$functions["server/edit_ip"] = array("token" => true, "remote_server_id" => true, "old_ip" => true, "new_ip" => true);

	//______________ Game Servers
	$functions["user_games/list_games"] = array("token" => true,"system" => true,"architecture" => true);
	$functions["user_games/list_servers"] = array("token" => true);
	$functions["user_games/create"] = array("token" => true, "remote_server_id" => true, "server_name" => true, "home_cfg_id" => true, "mod_cfg_id" => true, "ip" => true, "port" => true, "control_password" => true, "enable_ftp" => true, "ftp_password" => true, "slots" => true, "affinity" => true, "nice" => true);
	$functions["user_games/clone"] = array("token" => true, "origin_home_id" => true, "new_server_name" => true, "new_ip" => true, "new_port" => true, "control_password" => true, "enable_ftp" => true, "ftp_password" => true, "slots" => true, "affinity" => true, "nice" => true);
	$functions["user_games/set_expiration"] = array("token" => true, "home_id" => true, "timestamp" => true);

	//______________ Users
	$functions["user_admin/list"] = array("token" => true);
	$functions["user_admin/get"] = array("token" => true, "email" => true);
	$functions["user_admin/create"] = array("token" => true, "name" => true, "password" => true, "email" => true);
	$functions["user_admin/remove"] = array("token" => true, "email" => true);
	$functions["user_admin/set_expiration"] = array("token" => true, "email" => true, "timestamp" => true);
	$functions["user_admin/list_assigned"] = array("token" => true, "email" => true);
	$functions["user_admin/assign"] = array("token" => true, "home_id" => true, "email" => true, "timestamp" => true);
	$functions["user_admin/remove_assign"] = array("token" => true, "home_id" => true, "email" => true);

	//______________ Game Manager
	$functions["gamemanager/start"] = array("token" => true, "ip" => true, "port" => true, "mod_key" => false);
	$functions["gamemanager/stop"] = array("token" => true, "ip" => true, "port" => true, "mod_key" => false);
	$functions["gamemanager/restart"] = array("token" => true, "ip" => true, "port" => true, "mod_key" => false);
	$functions["gamemanager/rcon"] = array("token" => true, "ip" => true, "port" => true, "mod_key" => false, "command" => true);
	$functions["gamemanager/update"] = array("token" => true, "ip" => true, "port" => true, "mod_key" => false, "type" => true, "manual_url" => false);
	
	//______________ Game Manager Admin
	$functions["gamemanager_admin/reorder"] = array("token" => true);

	//______________ Lite File Manager
	$functions["litefm/list"] = array("token" => true, "ip" => true, "port" => true, "relative_path" => true);
	$functions["litefm/get"] = array("token" => true, "ip" => true, "port" => true, "relative_path" => true);
	$functions["litefm/save"] = array("token" => true, "ip" => true, "port" => true, "relative_path" => true, "contents" => true);
	$functions["litefm/remove"] = array("token" => true, "ip" => true, "port" => true, "relative_path" => true);

	//______________ Addons Manager
	$functions["addonsmanager/list"] = array("token" => true);
	$functions["addonsmanager/install"] = array("token" => true, "ip" => true, "port" => true, "addon_id" => true);

	//______________ Steam Workshop
	$functions["steam_workshop/install"] = array("token" => true, "ip" => true, "port" => true, "mod_key" => false, "mods_list" => true);

	//______________ Server Content
	$functions["server_content/run_scheduled_action"] = array("token" => true, "home_id" => true, "action" => true, "options" => false);
	
	//______________ Settings
	$functions["setting/get"] = array("token" => true, "setting_name" => true);
	
	if($main_request == "all")
		return $functions;
	return isset($functions["$main_request"])?$functions["$main_request"]:false;
}

function get_query_port($server_xml, $server_port)
{
	if ($server_xml->query_port)
	{
		if ($server_xml->query_port['type'] == 'add')
			return $server_port + $server_xml->query_port;
		if ($server_xml->query_port['type'] == 'subtract')
			return $server_port - $server_xml->query_port;
	}
	return $server_port;
}

function gsp_get_home_layout_paths($remote, $home_info)
{
	$home_path = rtrim((string)$home_info['home_path'], '/\\');
	$home_id = isset($home_info['home_id']) ? (int)$home_info['home_id'] : 0;
	$layout = array(
		'home_id' => $home_id,
		'home_path' => $home_path,
		'is_new_layout' => false,
		'game_path' => $home_path,
		'game_root' => $home_path,
		'control_path' => $home_path . '/gsp_control',
		'gsp_control_path' => $home_path . '/gsp_control',
		'pid_dir' => $home_path . '/gsp_control/pids',
		'log_dir' => $home_path . '/gsp_control/logs',
		'backup_path' => $home_path . '/gsp_control/backups',
	);

	if ($home_path === '') {
		return $layout;
	}

	$home_path_escaped = escapeshellarg($home_path . '/gamefiles');
	$check_cmd = '[ -d ' . $home_path_escaped . ' ] && echo 1 || echo 0';
	$has_gamefiles = trim((string)$remote->exec($check_cmd)) === '1';

	if ($has_gamefiles) {
		$layout['is_new_layout'] = true;
		$layout['game_path'] = $home_path . '/gamefiles';
		$layout['game_root'] = $home_path . '/gamefiles';
	}

	return $layout;
}

function gsp_convert_layout_for_cli($remote, $layout, $os, $home_info)
{
	$is_windows_binary_on_linux = preg_match('/_win(32|64)?$/', (string)$home_info['game_key']) && preg_match('/Linux/', $os);
	$is_cygwin = preg_match('/CYGWIN/', $os);

	if (!$is_windows_binary_on_linux && !$is_cygwin) {
		return $layout;
	}

	$converter = $is_windows_binary_on_linux ? 'winepath -w ' : 'cygpath -w ';
	$home_cli = trim((string)$remote->exec($converter . escapeshellarg($layout['home_path'])));
	$home_cli = str_replace('\\', '\\\\', $home_cli);

	$to_windows_path = function ($raw_path) use ($layout, $home_cli) {
		if ($raw_path === $layout['home_path']) {
			return $home_cli;
		}
		$suffix = substr($raw_path, strlen($layout['home_path']));
		$suffix = ltrim(str_replace('/', '\\\\', $suffix), '\\\\');
		return $suffix === '' ? $home_cli : $home_cli . '\\\\' . $suffix;
	};

	$layout['home_path'] = $to_windows_path($layout['home_path']);
	$layout['game_path'] = $to_windows_path($layout['game_path']);
	$layout['game_root'] = $to_windows_path($layout['game_root']);
	$layout['control_path'] = $to_windows_path($layout['control_path']);
	$layout['gsp_control_path'] = $to_windows_path($layout['gsp_control_path']);
	$layout['pid_dir'] = $to_windows_path($layout['pid_dir']);
	$layout['log_dir'] = $to_windows_path($layout['log_dir']);
	$layout['backup_path'] = $to_windows_path($layout['backup_path']);

	return $layout;
}

function get_start_cmd($user_info,$remote,$server_xml,$home_info,$mod_id,$ip,$port,$db)
{	
	$last_param = json_decode($home_info['last_param'], True);
	
	$os = $remote->what_os();
	
	$isAdmin = false;
	if(hasValue($user_info) && hasValue($user_info['user_id'])){
		$isAdmin = $db->isAdmin($user_info['user_id']);
	}
	
	$cli_param_data['GAME_TYPE'] = $home_info['mods'][$mod_id]['mod_key'];
	$cli_param_data['IP'] = $ip;
	$cli_param_data['PORT'] = $port;
	$cli_param_data['HOSTNAME'] = $home_info['home_name'];
	$cli_param_data['PID_FILE'] = "ogp_game_startup.pid";
	$layout = gsp_get_home_layout_paths($remote, $home_info);
	$layout = gsp_convert_layout_for_cli($remote, $layout, $os, $home_info);

	$cli_param_data['HOME_ID'] = (string)$layout['home_id'];
	$cli_param_data['BASE_PATH'] = $layout['home_path'];
	$cli_param_data['HOME_PATH'] = $layout['home_path'];
	$cli_param_data['GAME_PATH'] = $layout['game_path'];
	$cli_param_data['GAME_ROOT'] = $layout['game_root'];
	$cli_param_data['CONTROL_PATH'] = $layout['control_path'];
	$cli_param_data['GSP_CONTROL_PATH'] = $layout['gsp_control_path'];
	$cli_param_data['PID_DIR'] = $layout['pid_dir'];
	$cli_param_data['LOG_DIR'] = $layout['log_dir'];
	$cli_param_data['BACKUP_PATH'] = $layout['backup_path'];
	$cli_param_data['SAVE_PATH'] = $layout['is_new_layout'] ? $layout['game_path'] : $layout['home_path'];
	$cli_param_data['OUTPUT_PATH'] = $layout['is_new_layout'] ? $layout['log_dir'] : $layout['home_path'];
	$cli_param_data['USER_PATH'] = $layout['is_new_layout'] ? $layout['game_path'] : $layout['home_path'];
	
	if ($server_xml->protocol == "gameq")
	{
		$cli_param_data['QUERY_PORT'] = get_query_port($server_xml, $port);
	}
	elseif ($server_xml->protocol == "lgsl")
	{
		require('protocol/lgsl/lgsl_protocol.php');
		$get_ports = lgsl_port_conversion((string)$server_xml->lgsl_query_name, $port, "", "");
		$cli_param_data['QUERY_PORT'] = $get_ports['1'];
	}
	elseif ($server_xml->protocol == "teamspeak3")
	{
		$cli_param_data['QUERY_PORT'] = $port + 24;
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

function send_rcon_command($command, $remote, $server_xml, $home_info, $home_id, $ip, $port)
{
	if( $server_xml->gameq_query_name and $server_xml->gameq_query_name == "minecraft" )
	{
		require_once("modules/gamemanager/MinecraftRcon.class.php");
		$server_properties_file = clean_path($home_info['home_path']."/server.properties");
		$retval = $remote->remote_readfile($server_properties_file, $data);
		if($retval == 1 and strpos($data, 'rcon.port') !== FALSE)
		{
			$server_properties = parse_ini_string($data);
			$rcon_port = $server_properties['rcon.port'];
		}
		else
		{
			$rcon_port = $port+10;
		}
		$rcon = new MinecraftRcon;
		if( $rcon->Connect($ip, $rcon_port, $home_info['control_password']) )
		{
			$return = $rcon->Command($command);
			if($return)
				return $return;
			else
				return FALSE;
			$rcon->Disconnect();
		}
		else
			return FALSE;
	}
	elseif( $server_xml->lgsl_query_name and  $server_xml->lgsl_query_name == "7dtd" )
	{
		$query_port = $port + 1;
		$return = $remote->exec('exec 3<>/dev/tcp/'.$ip.'/'. $query_port .' && echo -en "'.$command.'\\nexit\\n" >&3 && cat <&3');
		if(preg_match("/Connected with 7DTD server/",$return))	
			return $return;
		else
			return FALSE;
	}
	else
	{
		$remote_retval = $remote->remote_send_rcon_command( $home_id, $ip, $port, $server_xml->control_protocol, $home_info['control_password'],$server_xml->control_protocol_type,$command,$return);
		if ( $remote_retval === 1 )
			return $return;
		elseif ( $remote_retval === -10 )
			return FALSE;
		else
			return FALSE;
	}
}

function get_download_filename($url)
{
	if(empty($url) or !filter_var($url, FILTER_VALIDATE_URL))
		return FALSE;
	$headers = get_headers($url, 1);
	if($headers['Server'] == 'cloudflare')
		return basename($url);
	if(isset($headers[0]) and preg_match('/200|302/', $headers[0]))
	{
		if(isset($headers['Content-Disposition']))
		{
			list($type, $filename) = explode('filename=',$headers['Content-Disposition']);
		}
	}
	else
		$filename = basename($url);
	return trim($filename);
}

function getClientForwardedIP(){
	if(isset($_SERVER['HTTP_CF_CONNECTING_IP']) and !empty($_SERVER['HTTP_CF_CONNECTING_IP']))
		return $_SERVER['HTTP_CF_CONNECTING_IP'];
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR']) and !empty($_SERVER['HTTP_X_FORWARDED_FOR']))
		return $_SERVER['HTTP_X_FORWARDED_FOR'];
	if(isset($_SERVER['HTTP_X_REAL_IP']) and !empty($_SERVER['HTTP_X_REAL_IP']))
		return $_SERVER['HTTP_X_REAL_IP'];
	return false;
}

function is_valid_ipv4($ip)
{
	if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4))
		return true;
	return false;
}

function is_valid_ipv6($ip)
{
	if(filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6))
		return true;
	return false;
}

// https://github.com/rmccue/Requests/blob/master/library/Requests/IPv6.php
function ipv6_uncompress($ip)
{
	if (substr_count($ip, '::') !== 1) {
		return $ip;
	}
	list($ip1, $ip2) = explode('::', $ip);
	$c1 = ($ip1 === '') ? -1 : substr_count($ip1, ':');
	$c2 = ($ip2 === '') ? -1 : substr_count($ip2, ':');
	if (strpos($ip2, '.') !== false) {
		$c2++;
	}
	// ::
	if ($c1 === -1 && $c2 === -1) {
		$ip = '0:0:0:0:0:0:0:0';
	}
	// ::xxx
	else if ($c1 === -1) {
		$fill = str_repeat('0:', 7 - $c2);
		$ip = str_replace('::', $fill, $ip);
	}
	// xxx::
	else if ($c2 === -1) {
		$fill = str_repeat(':0', 7 - $c1);
		$ip = str_replace('::', $fill, $ip);
	}
	// xxx::xxx
	else {
		$fill = ':' . str_repeat('0:', 6 - $c2 - $c1);
		$ip = str_replace('::', $fill, $ip);
	}
	return $ip;
}

function split_v6_v4($ip) {
	if (strpos($ip, '.') !== false) {
		$pos = strrpos($ip, ':');
		$ipv6_part = substr($ip, 0, $pos);
		$ipv4_part = substr($ip, $pos + 1);
		return array($ipv6_part, $ipv4_part);
	}
	else {
		return array($ip, '');
	}
}

function ipv6_compress($ip)
{ 
	// Prepare the IP to be compressed
	$ip = ipv6_uncompress($ip);
	$ip_parts = split_v6_v4($ip);
	// Replace all leading zeros
	$ip_parts[0] = preg_replace('/(^|:)0+([0-9])/', '\1\2', $ip_parts[0]);
	// Find bunches of zeros
	if (preg_match_all('/(?:^|:)(?:0(?::|$))+/', $ip_parts[0], $matches, PREG_OFFSET_CAPTURE)) {
		$max = 0;
		$pos = null;
		foreach ((array)$matches[0] as $match) {
			if (strlen($match[0]) > $max) {
				$max = strlen($match[0]);
				$pos = $match[1];
			}
		}
		$ip_parts[0] = substr_replace($ip_parts[0], '::', $pos, $max);
	}
	if ($ip_parts[1] !== '') {
		return implode(':', $ip_parts);
	}
	else {
		return $ip_parts[0];
	}
}

function is_authorized()
{
	require_once 'includes/ip_in_range.php';
	$api_hosts_file = 'api_authorized.hosts';
	$api_fwd_hosts_file = 'api_authorized.fwd_hosts';
	global $db, $settings;
	
	if(!@$settings['use_authorized_hosts']){
		return true;
	}
	
	$authorized_hosts = array();
	$ip = getHostByName(getHostName());
	if(is_valid_ipv4($ip))
		$authorized_hosts['address']['ipv4'][] = $ip;
	elseif(is_valid_ipv6($ip))
		$authorized_hosts['address']['ipv6'][] = $ip;
	
	$remote_servers = $db->getRemoteServers();
	foreach ((array)$remote_servers as $remote_server)
	{
		$ip = getHostByName($remote_server['agent_ip']);
		if(is_valid_ipv4($ip) and !in_array($ip, $authorized_hosts['address']['ipv4']))
			$authorized_hosts['address']['ipv4'][] = $ip;
		elseif(is_valid_ipv6($ip) and !in_array($ip, $authorized_hosts['address']['ipv6']))
			$authorized_hosts['address']['ipv6'][] = $ip;
		unset($ip);
	}
	
	if(file_exists($api_hosts_file))
	{
		$hosts_list = file_get_contents($api_hosts_file);
		$hosts = preg_split("/[\r\n]+/", $hosts_list);
		foreach ((array)$hosts as $host)
		{
			$host = trim($host);
			
			if($host == '')
				continue;

			if(strstr($host, '/'))
			{
				list($ip, $range) = explode('/', $host, 2);
				if(is_valid_ipv4($ip) and !in_array($host, $authorized_hosts['cidr']['ipv4']))
					$authorized_hosts['cidr']['ipv4'][] = $host;
				elseif(is_valid_ipv6($ip) and !in_array(ipv6_compress($ip)."/".$range, $authorized_hosts['cidr']['ipv6']))
					$authorized_hosts['cidr']['ipv6'][] = ipv6_compress($ip)."/".$range;
				unset($ip, $range);
			}
			else
			{
				$ip = getHostByName($host);
				if(is_valid_ipv4($ip) and !in_array($ip, $authorized_hosts['address']['ipv4']))
					$authorized_hosts['address']['ipv4'][] = $ip;
				elseif(is_valid_ipv6($ip) and !in_array(ipv6_compress($ip), $authorized_hosts['address']['ipv6']))
					$authorized_hosts['address']['ipv6'][] = ipv6_compress($ip);
				unset($ip);
			}
		}
	}
		
	$client_forwarded_ip = getClientForwardedIP();
	$client_ip = $_SERVER['REMOTE_ADDR'];
	
	## Check authorized_hosts
	$authorized_host = false;
	if(is_valid_ipv4($client_ip))
	{
		if(in_array($client_ip, $authorized_hosts['address']['ipv4']))
			$authorized_host = true;
		else
		{
			foreach($authorized_hosts['cidr']['ipv4'] as $ipv4_cidr)
				if(ipv4_in_range($client_ip, $ipv4_cidr))
					$authorized_host = true;
		}
	}
	elseif(is_valid_ipv6($client_ip))
	{
		if(in_array(ipv6_compress($client_ip), $authorized_hosts['address']['ipv6']))
			$authorized_host = true;
		else
		{
			foreach($authorized_hosts['cidr']['ipv6'] as $ipv6_cidr)
				if(ipv6_in_range(ipv6_compress($client_ip), $ipv6_cidr))
					$authorized_host = true;
		}
	}
	
	if($authorized_host)
	{
		if($client_forwarded_ip)
		{
			## Check also authorized_fwd_hosts
			$authorized_fwd_hosts = array();
			if(file_exists($api_fwd_hosts_file))
			{
				$fwd_hosts_list = file_get_contents($api_fwd_hosts_file);
				$fwd_hosts = preg_split("/[\r\n]+/", $fwd_hosts_list);
				foreach ((array)$fwd_hosts as $fwd_host)
				{
					$fwd_host = trim($fwd_host);
					
					if($fwd_host == '')
						continue;

					if(strstr($fwd_host, '/'))
					{
						list($ip, $range) = explode('/', $fwd_host, 2);
						if(is_valid_ipv4($ip) and !in_array($fwd_host, $authorized_fwd_hosts['cidr']['ipv4']))
							$authorized_fwd_hosts['cidr']['ipv4'][] = $fwd_host;
						elseif(is_valid_ipv6($ip) and !in_array(ipv6_compress($ip)."/".$range, $authorized_fwd_hosts['cidr']['ipv6']))
							$authorized_fwd_hosts['cidr']['ipv6'][] = ipv6_compress($ip)."/".$range;
						unset($ip, $range);
					}
					else
					{
						$ip = getHostByName($fwd_host);
						if(is_valid_ipv4($ip) and !in_array($ip, $authorized_fwd_hosts['address']['ipv4']))
							$authorized_fwd_hosts['address']['ipv4'][] = $ip;
						elseif(is_valid_ipv6($ip) and !in_array(ipv6_compress($ip), $authorized_fwd_hosts['address']['ipv6']))
							$authorized_fwd_hosts['address']['ipv6'][] = ipv6_compress($ip);
						unset($ip);
					}
				}
				
				if(is_valid_ipv4($client_forwarded_ip))
				{
					if(in_array($client_forwarded_ip, $authorized_fwd_hosts['address']['ipv4']))
						return true;
					else
					{
						foreach($authorized_fwd_hosts['cidr']['ipv4'] as $ipv4_cidr)
							if(ipv4_in_range($client_forwarded_ip, $ipv4_cidr))
								return true;
					}
				}
				elseif(is_valid_ipv6($client_forwarded_ip))
				{
					if(in_array(ipv6_compress($client_forwarded_ip), $authorized_fwd_hosts['address']['ipv6']))
						return true;
					else
					{
						foreach($authorized_fwd_hosts['cidr']['ipv6'] as $ipv6_cidr)
							if(ipv6_in_range(ipv6_compress($client_forwarded_ip), $ipv6_cidr))
								return true;
					}
				}
			}
		}
		else
			return true;
	}
	return false;
}

?>
