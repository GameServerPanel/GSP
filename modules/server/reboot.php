<?php
/*
 * Component of the server module
 */

require_once('includes/lib_remote.php');
function exec_ogp_module() {

    global $view;
    global $db;
	echo "<h2>".get_lang('reboot')."</h2>";
	$rhost_id = @$_REQUEST['rhost_id'];
    $remote_server = $db->getRemoteServer($rhost_id);
	$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
	$ipAndName = $remote_server['remote_server_name'] . " " . "(" . $remote_server['agent_ip'] . ")";

	// Confirm user wants to reboot the server
	if (!isset($_POST['re_check'])) {
		echo "<table class='center' style='width:100%;' ><tr>\n" . "<td>" . get_lang_f('confirm_reboot', $ipAndName) . "</td>" . "</tr><tr><td>" . '<form method="post" >' . "\n" . '<input type="hidden" name="rhost_id" value="' . $rhost_id . '">' . "\n" . '<button name="re_check" value="yes" >' . get_lang('yes') . "</button>\n" . '<button name="re_check" value="no" >' . get_lang('no') . "</button>\n" . "</form>\n" . "</td>\n" . "</tr>\n" . "</table><br>\n";
	} else if($_POST['re_check'] == "yes") {
		// Confirmed... so reboot the server in 10 seconds
		$file_info =  $remote->remote_rebootnow();
		echo "<p>" . get_lang_f('reboot_success', $ipAndName) . "</p>";
		
		// 150 seconds should be enough for the server to come back up?
		$view->refresh("?m=server",150);

	} else if ($_POST['re_check'] == "no"){
		$view->refresh("?m=server",0);
	}


    
	
    
}
?>
