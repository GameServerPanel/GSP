<?php
/*
 * Component of the status module
 */

// Setup the remote connection
include_once("modules/status/config.php");
if( isset($_GET['remote_server_id']) && $_GET['remote_server_id'] != "webhost")
{
	require_once('includes/lib_remote.php');
	$rhost_id = $_GET['remote_server_id'];
	$remote_server = $db->getRemoteServer($rhost_id);
	$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
	$taskoutput = $remote->shell_action('get_tasklist', 'tasks');
}else{
	if ($os == "windows" || $cygwin === true)
	{
		$taskoutput = array();
		$taskoutput["task"] = shell_exec ("tasklist /fo TABLE");
	}else{
		if($os == "linux"){
			$taskoutput = array();
			$taskoutput["task"] = shell_exec ("top -b -c -i -w512 -n2 -o+%CPU | awk '/^top/{i++}i==2' | grep 'PID' -A 30");
		}
	}
}
?>
