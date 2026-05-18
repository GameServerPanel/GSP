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
function reloadJobs($server_homes, $remote_servers, $getAllJobs = true)
{
	global $db;
	$remote_servers_offline = array();
	$jobsArray = array();
	foreach ((array)$remote_servers as $rhost_id => $remote_server )
	{
		$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
		if($remote->status_chk() != 1)
		{
			$remote_servers_offline[$rhost_id] = $remote_server;
			continue;
		}
		else
		{
			$jobs = $remote->scheduler_list_tasks();
			if($jobs != -1)
			{
				foreach ((array)$jobs as $jobId => $job)
				{
					list($minute,$hour,$dayOfTheMonth,$month,$dayOfTheWeek,$command) = explode(" ", $job, 6);
					if(preg_match('/'.preg_quote('wget -qO- "','/').'([^"]+)'.preg_quote('" --no-check-certificate > /dev/null 2>&1','/').'/', $command))
					{
						list($wget,$wget_args,$url,$wget_nocert,$gt,$devnull,$err2out) =  explode(" ", $command, 7);
						
						parse_str(parse_url(trim($url,'"'), PHP_URL_QUERY), $url_query);
						$home_info = false;
						if(isset($url_query['ip']) && isset($url_query['port']))
						{
							$home_info = $db->getGameHomeByIP($url_query['ip'], $url_query['port']);
						}
						elseif(isset($url_query['home_id']))
						{
							$home_info = $db->getGameHome((int)$url_query['home_id'], true);
						}
						if(!$home_info)
							continue;
						if(!$getAllJobs && !hasAccess($home_info))
							continue;
						
						$action = key($url_query);
						if($action == "gamemanager/update"){
							$action = "steam_auto_update";
						}else if($action == "gamemanager/stop"){
							$action = "stop";
						}else if($action == "gamemanager/start"){
							$action = "start";
						}else if($action == "gamemanager/restart"){
							$action = "restart";
						}else if($action == "server_content/run_scheduled_action" && isset($url_query['action']) && $url_query['action'] != ""){
							$action = $url_query['action'];
						}
						
						$jobsArray[$rhost_id][$jobId] = array( 'job' => $job, 
															   'minute' => $minute, 
															   'hour' => $hour, 
															   'dayOfTheMonth' => $dayOfTheMonth, 
															   'month' => $month, 
															   'dayOfTheWeek' => $dayOfTheWeek,
															   'command' => $command,
															   'action' => $action,
															   'home_id' => $home_info['home_id'],
															   'ip' => $home_info['ip'],
															   'port' => $home_info['port'],
															   'mod_key' => isset($url_query['mod_key']) ? $url_query['mod_key'] : '');
					}
					else
					{	
						if(!$getAllJobs && !$db->isAdmin($_SESSION['user_id'])){
							continue;
						}			
						
						$jobsArray[$rhost_id][$jobId] = array( 'job' => $job, 
															   'minute' => $minute, 
															   'hour' => $hour, 
															   'dayOfTheMonth' => $dayOfTheMonth, 
															   'month' => $month, 
															   'dayOfTheWeek' => $dayOfTheWeek, 
															   'command' => $command);
					}
				}
			}
		}
	}
	return array($jobsArray, $remote_servers_offline);
}

function get_server_content_scheduled_actions() {
	return array(
		'server_content_check_updates',
		'server_content_check_workshop_updates',
		'server_content_install_updates_if_stopped',
		'server_content_install_updates_next_restart',
		'server_content_install_updates_now',
		'server_content_install_updates_and_restart',
		'server_content_notify_updates_only',
		'server_content_update_all',
		'server_content_validate_files',
		'server_content_backup_before_update',
	);
}

function build_cron_scheduler_command($panelURL, $token, $game_home, $action) {
	$ip = $game_home['ip'];
	$port = $game_home['port'];
	$mod_key = isset($game_home['mod_key']) ? $game_home['mod_key'] : '';
	$home_id = isset($game_home['home_id']) ? (int)$game_home['home_id'] : 0;
	if(in_array($action, get_server_content_scheduled_actions()))
	{
		$options = array('triggered_by' => 'scheduler');
		if($action == 'server_content_install_updates_and_restart')
		{
			$options['safe_restart'] = true;
			$options['restart_delay_seconds'] = 60;
		}
		$options_json = urlencode(json_encode($options));
		return "wget -qO- \"${panelURL}/ogp_api.php?server_content/run_scheduled_action&token=${token}&home_id=${home_id}&action=${action}&options=${options_json}\" --no-check-certificate > /dev/null 2>&1";
	}
	switch ($action) {
		case "stop":
			return "wget -qO- \"${panelURL}/ogp_api.php?gamemanager/stop&token=${token}&ip=${ip}&port=${port}&mod_key=${mod_key}\" --no-check-certificate > /dev/null 2>&1";
		case "start":
			return "wget -qO- \"${panelURL}/ogp_api.php?gamemanager/start&token=${token}&ip=${ip}&port=${port}&mod_key=${mod_key}\" --no-check-certificate > /dev/null 2>&1";
		case "restart":
			return "wget -qO- \"${panelURL}/ogp_api.php?gamemanager/restart&token=${token}&ip=${ip}&port=${port}&mod_key=${mod_key}\" --no-check-certificate > /dev/null 2>&1";
		case "steam_auto_update":
			return "wget -qO- \"${panelURL}/ogp_api.php?gamemanager/update&token=${token}&ip=${ip}&port=${port}&mod_key=${mod_key}&type=steam\" --no-check-certificate > /dev/null 2>&1";
	}
	return false;
}

function updateCronJobTokens($old_token, $token){
	global $db;
	$remote_servers = $db->getRemoteServers();
	foreach ((array)$remote_servers as $remote_server)
	{
		$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
		$jobs = $remote->scheduler_list_tasks();
		foreach ((array)$jobs as $job_id => $job)
		{
			if(strstr($job, $old_token))
			{
				$remote->scheduler_edit_task($job_id, str_replace($old_token, $token, $job));
			}
		}
	}
}

function deleteJobsByHomeServerID($home_id){
	global $db;
	$jobIdsToDel = array();
	$homeInfo = $db->getGameHome($home_id, true);
	if($homeInfo){
		$remote_servers = $db->getRemoteServers();
		foreach ((array)$remote_servers as $remote_server)
		{
			$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
			$jobs = $remote->scheduler_list_tasks();
			foreach ((array)$jobs as $job_id => $job)
			{
				if(strstr($job, "homeid=" . $home_id))
				{
					$jobIdsToDel[] = $job_id;
				}else if(strstr($job, "ip=" . $homeInfo["ip"]) && strstr($job, "port=" . $homeInfo["port"])){
					$jobIdsToDel[] = $job_id;	
				}
			}
		}
	}
	
	if(is_array($jobIdsToDel) && count((array)$jobIdsToDel) > 0){
		// Only make one call
		$remote->scheduler_del_task(implode(",", $jobIdsToDel));
	}
}

function get_action_selector($action = false, $server_homes = false, $homeid_ip_port = false) {
	$server_actions = array('restart','stop','start');
	if($server_homes and $homeid_ip_port)
	{
		$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$server_homes[$homeid_ip_port]['home_cfg_file']);
		if( $server_xml->installer == "steamcmd" )
			$server_actions[] = 'steam_auto_update';
	}
	global $db;
	if($db->isModuleInstalled('addonsmanager'))
	{
		$server_actions = array_merge($server_actions, get_server_content_scheduled_actions());
	}
	$select_action = '<select name="action" style="width: 100%;">';
	foreach ((array)$server_actions as $server_action)
	{
		$selected = ($action and $action == $server_action) ? 'selected="selected"' : '';
		$select_action .= '<option value="'.$server_action.'" '.$selected.'>'.get_lang($server_action).'</option>';
	}
	return $select_action .= '</select>';
}

function get_server_selector($server_homes, $homeid_ip_port = FALSE, $onchange = FALSE, $includeRemoteName = false) {
	$onchange_this_form_submit = $onchange ? 'onchange="this.form.submit();"' : '';
	$select_game = "<select style='text-overflow: ellipsis; width: 100%;' name='homeid_ip_port' $onchange_this_form_submit>\n";
	if($server_homes != FALSE)
	{
		foreach ((array)$server_homes as $server_home)
		{
			$selected = ($homeid_ip_port and ($homeid_ip_port == $server_home['home_id']."_".$server_home['ip']."_".$server_home['port'] || trim($homeid_ip_port) == trim($server_home['home_id']))) ? 'selected="selected"' : '';
			$select_game .= "<option value='". $server_home['home_id'] . "_" . $server_home['ip'] .
							"_" . $server_home['port'] . "' $selected >" . $server_home['home_name'] . 
							" - " . checkDisplayPublicIP($server_home['display_public_ip'],$server_home['ip'] != $server_home['agent_ip'] ? $server_home['ip'] : $server_home['agent_ip']) . ":" .$server_home['port'];
			if($includeRemoteName){
				$select_game .= " ( " . $server_home['remote_server_name'] . " )";
			}
			
			$select_game .= "</option>\n";
		}
	}
	return $select_game .= "</select>\n";
}

function get_remote_server_selector($r_servers, $remote_servers_offline, $remote_server_id = FALSE, $onchange = FALSE, $first_empty = FALSE ) {
	$onchange_this_form_submit = $onchange ? 'onchange="this.form.submit();"' : '';
	$select_rserver = "<select id='r_server_id' style='width: 100%;' name='r_server_id' $onchange_this_form_submit>\n";
	if($first_empty) $select_rserver .= '<option></option>';
	foreach ((array)$r_servers as $r_server)
	{
		$selected = ($remote_server_id and $remote_server_id == $r_server['remote_server_id']) ? 'selected="selected"' : '';
		$offline = isset($remote_servers_offline[$r_server['remote_server_id']]) ? ' (' . offline . ')' : '';
		$select_rserver .= "<option value='". $r_server['remote_server_id'] . "' $selected>" . $r_server['remote_server_name'] . "$offline</option>\n";
	}
	return $select_rserver .= "</select>\n";
}

function checkCronInput($min, $hour, $day, $month, $dayOfWeek) {
    $blacklist = '"#$%^&()+=[]\';{}|:<>?~';
    $returns = array();
    
    $args = func_get_args();
    
    foreach ((array)$args as $k => $arg) {
        if (strlen($arg) == 0 || strpbrk($arg, $blacklist) || preg_match('/\\s/', $arg)) {
            $returns[$k] = false;
        }
    }
    
    return (empty($returns) ? true : false);
}

function hasAccess($home_info){
	global $db;
	return ($home_info and $db->isAdmin($_SESSION['user_id'])) ? true : ($home_info and $db->getUserGameHome($_SESSION['user_id'], $home_info['home_id']));
}

function updateCronJobsToNewApi()
{
	$check_file = "modules/cron/update.check";
	if(!file_exists($check_file))
	{
		require_once 'includes/lib_remote.php';
		
		$panelURL = getOGPSiteURL();
		if($panelURL === false)
			return false;
		
		global $db;
		$remote_servers = $db->getRemoteServers();
		$regex = '/'.preg_quote('action=','/').'([a-zA-Z]+)'.preg_quote('&homeid=','/').'([0-9]+)'.preg_quote('&controlpass=','/').'([^"]+)/';
		$token = $db->getApiToken($_SESSION['user_id']);
		$mod_key = '';
		foreach ((array)$remote_servers as $remote_server)
		{
			$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
			$jobs = $remote->scheduler_list_tasks();
			if(!is_array($jobs))
				continue;
			foreach ((array)$jobs as $job_id => $job)
			{
				if(preg_match($regex, $job, $matches))
				{
					list($full_match, $action, $home_id, $control_password) = $matches;
					$home_ip_ports = $db->getHomeIpPorts($home_id);
					if(isset($home_ip_ports[0]))
					{
						$port = $home_ip_ports[0]["port"];
						$ip = $home_ip_ports[0]["ip"];
						
						switch ($action) {
							case "stopServer":
								$command = "wget -qO- \"{$panelURL}/ogp_api.php?gamemanager/stop&token={$token}&ip={$ip}&port={$port}&mod_key={$mod_key}\" --no-check-certificate > /dev/null 2>&1";
								break;
							case "startServer":
								$command = "wget -qO- \"{$panelURL}/ogp_api.php?gamemanager/start&token={$token}&ip={$ip}&port={$port}&mod_key={$mod_key}\" --no-check-certificate > /dev/null 2>&1";
								break;
							case "restartServer":
								$command = "wget -qO- \"{$panelURL}/ogp_api.php?gamemanager/restart&token={$token}&ip={$ip}&port={$port}&mod_key={$mod_key}\" --no-check-certificate > /dev/null 2>&1";
								break;
							case "autoUpdateSteamHome":
								$command = "wget -qO- \"{$panelURL}/ogp_api.php?gamemanager/update&token={$token}&ip={$ip}&port={$port}&mod_key={$mod_key}&type=steam\" --no-check-certificate > /dev/null 2>&1";
								break;
						}
						list($minute,$hour,$dayOfTheMonth,$month,$dayOfTheWeek,$old_command) = explode(" ", $job, 6);
						$new_job = $minute." ".
								   $hour." ".
								   $dayOfTheMonth." ".
								   $month." ".
								   $dayOfTheWeek." ".
								   $command;
						
						$remote->scheduler_edit_task($job_id, $new_job);
					}
				}
			}
		}
		file_put_contents($check_file, "updated");
	}
}

?>
