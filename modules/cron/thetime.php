<?php
/*
 * Component of the cron module
 */

require_once('includes/lib_remote.php');
function exec_ogp_module() 
{
	global $db;
	$remote_server = $db->getRemoteServer($_GET['r_server_id']);
	$remote = new OGPRemoteLibrary( $remote_server['agent_ip'], $remote_server['agent_port'],
									$remote_server['encryption_key'], $remote_server['timeout'] );
	
	if($remote->status_chk() == 1)
	{
		list($i, $H, $d, $m, $w, $date_rfc_2822, $time_zone) = explode('|', date('i|H|d|n|N|r|e', $remote->shell_action('get_timestamp', '')));
		echo '<table class="center">'.
			 '<tr><td style="width: 35px;" >'.$i.
			 '</td><td style="width: 35px;" >'.$H.
			 '</td><td style="width: 35px;" >'.$d.
			 '</td><td style="width: 35px;" >'.$m.
			 '</td><td style="width: 35px;" >'.$w.
			 '</td><td></b>('.$date_rfc_2822.') '.$time_zone.
			 '</td></tr></table>';
	}
	else
		echo get_lang("agent_offline");
}
?>
