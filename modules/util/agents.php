<?php
/*
 * Component of the util module
 */
include 'includes/lib_remote.php';
function exec_ogp_module() 
{
	global $db;
	$remoteServers = $db->getRemoteServers();
	$servers = array();
	
	if(is_array($remoteServers)){
		foreach($remoteServers as $server){
			$remote = new OGPRemoteLibrary($server['agent_ip'], $server['agent_port'], $server['encryption_key'], 1);
			$status = (int)$remote->status_chk();
			
			$servers[] = array(
				'id'		=>	$server['remote_server_id'],
				'name'		=>	$server['remote_server_name'] .' '. (($status) === 0 ? '('.get_lang('offline').')' : '('.get_lang('online').')'),
				'status'	=>	$status,
			);
		}
	}
	echo json_encode($servers);
}
?>