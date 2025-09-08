<?php
/*
 * Component of the server module
 */

require_once('includes/lib_remote.php');
function exec_ogp_module() {

    global $view;
    global $db;
	echo "<h2>". get_lang("view_log") ."</h2>";
	$rhost_id = @$_REQUEST['rhost_id'];
    $remote_server = $db->getRemoteServer($rhost_id);
	$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout'] );
	if(isset($_POST['save_file']))
	{
		$file_info = $remote->remote_writefile('./ogp_agent.log', strip_real_escape_string($_REQUEST['file_content']));
		if ( $file_info === 1 )
		{
            print_success( get_lang("wrote_changes") );
		}
        else if ( $file_info === 0 )
            print_failure( get_lang("failed_write") );
        else
		{
            print_failure( get_lang("agent_offline") );
		}
	}
    $data = "";
    $file_info =  $remote->remote_readfile('./ogp_agent.log',$data);
    if ( $file_info === 0 )
    {
        print_failure( get_lang("not_found") );
        return;
    }
    else if ( $file_info === -1 )
    {
        print_failure( get_lang("agent_offline") );
        return;
    }
    else if ( $file_info === -2 )
    {
        print_failure( get_lang("failed_read") );
        return;
    }
    echo "<form action='?m=server&amp;p=log&amp;rhost_id=".$rhost_id."' method='post'>";
    echo "<textarea name='file_content' style='width:98%;' rows='40'>$data</textarea>";
    echo "<p><input type='submit' name='save_file' value='Save' /></p>";
    echo "</form>";
	echo create_back_button($_GET['m']);
}
?>
