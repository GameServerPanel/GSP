<script type="text/javascript" src="js/jquery/plugins/jquery.json-2.3.min.js"></script>
<script type="text/javascript" src="js/modules/dashboard.js"></script>
<?php
/*
 * Component of the dashboard module
 */

require_once('includes/lib_remote.php');


function exec_ogp_module() 
{
	global $db, $settings, $loggedInUserInfo;
	
	$isAdmin = $db->isAdmin($_SESSION['user_id']);
	$user_id = $_SESSION['user_id'];

	$page_user = (isset($_GET['page']) && (int)$_GET['page'] > 0) ? (int)$_GET['page'] : 1; // thanks for Adjokip
	$limit_user = isset($_GET['limit']) ? $_GET['limit'] : 10;

	
		

	
	if(hasValue($loggedInUserInfo) && is_array($loggedInUserInfo) && $loggedInUserInfo["users_page_limit"] && !(isset($_GET['limit']) and !empty($_GET['limit']))){
 		$limit_user = $loggedInUserInfo["users_page_limit"];
 	}	
	

    //show if new server created
	$cartresult = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_carts WHERE user_id='".$_SESSION['user_id']."' AND paid='1' ");
	 $newServices=0;
        foreach($cartresult as $res){
        $newServices=$newServices + 1;
                }
    //Popup notification
        if($newServices > 0) {
        echo'<div class="alert alert-success alert-dismissible">
		
            <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
			
            <strong>GOOD NEWS!</strong> You have a new server! <br>
			<form method="post" action="home.php?m=billing&p=create_servers">
            <input type="hidden" name="cart_id" value="'. $res['cart_id'].'">
			<button type="submit" name="submit_param" value="submit_value" class="link-button">
			Create Server
			</button>
			</form>

        </div>';
        }
    //Invoice is due. 
	$orderresult = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE user_id='".$_SESSION['user_id']."' AND status = -1");
	$invoicesDue=0;
	foreach($orderresult as $res){
	$invoicesDue=$invoicesDue + 1;
		}
    //Popup notification WARNING
	if($invoicesDue > 0) {
        echo'<div class="alert alert-warning alert-dismissible">
	    <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
            You have an Invoice Due <br>
			<form method="post" action="home.php?m=billing&p=create_servers">
            <input type="hidden" name="cart_id" value="'. $res['cart_id'].'">
			<button type="submit" name="submit_param" value="submit_value" class="link-button">
			View Invoice
			</button>
			
			</div>'; 
	}
	//Server is suspended DANGER
	$orderresult = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXbilling_orders WHERE user_id='".$_SESSION['user_id']."' AND status = -2");
	$invoicesDue=0;
	foreach($orderresult as $res){
	$invoicesDue=$invoicesDue + 1;
		}
    //Popup notification 
	if($invoicesDue > 0) {
        echo'<div class="alert alert-danger alert-dismissible">
	    <a href="#" class="close" data-dismiss="alert" aria-label="close"></a>
            <strong>Warning!</strong> You have an OVERDUE invoice<br>
			<form method="post" action="home.php?m=billing&p=create_servers">
            <input type="hidden" name="cart_id" value="'. $res['cart_id'].'">
			<button type="submit" name="submit_param" value="submit_value" class="link-button">
			View Overdue Invoice
			</button>
			</div>'; 
	}
	
	if( isset($settings['welcome_title']) && $settings['welcome_title'] == "1" )
	{
		if( isset($settings['welcome_title_message']) && !empty($settings['welcome_title_message'] ))
		{
			echo "<div>" . $settings['welcome_title_message'] . "</div>";
		}
	}


	require_once("includes/refreshed.php");
	$refresh = new refreshed();
        $OnlineServers .= "<p>Recent updates and changes</p>";
	?>
	<div style="margin-top:20px;">
	<?php 
	//$title[$id] = "The Title";
	//$content[$id] = "Content of the Widget";
	$title = array();
	$content = array();
	$href = array();
	// Order History
	$title[1] = "Order History"; // get_lang('orders');
	$content[1] = 'View all your orders, invoices and expiration dates. ';
	$href[1] = 'home.php?m=billing&p=orders';
	
	// Recent News
    //$xml=simplexml_load_file("modules/news/data/listings.xml");
	//$lastnews = count($xml)-1;
	//$title[2] = "Recent News";
	//$content[2] = $xml->listing[$lastnews]->title;
	//$href[2] = 'home.php?m=news&p=news';
	
	// Notifications
	$title[3] = "Notifications"; // get_lang('orders');
	$content[3] = 'View all your notifications. ';
	$href[3] = 'home.php?m=circular&p=show_circular&list=true';

	// Invoices
	$title[4] = 'Current Invoices';
	$content[4] ='An Invoice will be created before your server expires. Click here to view current invoices.<br>Invoices Due : '. $invoicesDue ;
	$href[4] = 'home.php?m=billing&p=cart';

	// Support
	$title[5] = (isset($settings['support_widget_title']) && $settings['support_widget_title'] != "") ?
				 $settings['support_widget_title'] : get_lang('support');
	$content[5] = '<img src="themes/' . $settings['theme'] . '/images/icons/support.png" style="width:48px;float:right;margin:0 0 0 8px" />	Submit a SUPPORT TICKET or use our Discord Chat at the bottom right.  Click this box to JOIN our Discord';
	$href[5] = 'https://discord.gg/cWHAbav';
	
	


	$widgets = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXwidgets_users WHERE user_id='".$_SESSION['user_id']."' ORDER BY sort_no");
	
	if(!$widgets)
	{
		if($db->createUserWidgets($_SESSION['user_id']))
			$widgets = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXwidgets_users WHERE user_id='".$_SESSION['user_id']."' ORDER BY sort_no");
	}
	
	if($widgets)
	{
		$colhtml[1] = '<div class="column one_fourth" id="column1" >';
		$colhtml[2] = '<div class="column one_two" id="column2" >';
		$colhtml[3] = '<div class="column one_fourth" id="column3" >';
		foreach($widgets as $widget)
		{
			if(array_key_exists($widget["widget_id"], $title)){
				if( (!isset($settings['old_dashboard_behavior']) or $settings['old_dashboard_behavior'] == 0) AND $widget['widget_id'] == "3" )
					continue;
				$colhtml[$widget['column_id']] .= '<div class="dragbox bloc rounded" id="item'.$widget['widget_id'].'">'.
												  '<h4><span class="configure"></span>';
				if(!is_null($title[$widget['widget_id']]))
					$colhtml[$widget['column_id']] .= $title[$widget['widget_id']];
				
				$colhtml[$widget['column_id']] .= '</h4><div class="dragbox-content" '; 
				if(!is_null($href[$widget['widget_id']]))
				{
					$colhtml[$widget['column_id']] .= "onclick=\"location.href='". $href[$widget['widget_id']] . "'\" style=\"cursor:pointer;";
					if($widget['collapsed']==1)  
						$colhtml[$widget['column_id']] .= 'display:none;';
					$colhtml[$widget['column_id']] .= '"';
				}
				elseif($widget['collapsed']==1)  
					$colhtml[$widget['column_id']] .= 'style="display:none;"';

				$colhtml[$widget['column_id']] .= '>';

				if(!is_null($content[$widget['widget_id']]))
					$colhtml[$widget['column_id']] .= $content[$widget['widget_id']];

				$colhtml[$widget['column_id']] .= '</div></div>'; 
			}
		}
		foreach($colhtml as $html )
			echo $html.'</div>';
	}
	
	// Server Status Link - Available to all users
	echo "<div style='margin-top:20px;'>
		   <div class='bloc rounded' >
		   <h4>Server Status</h4>
			<div style='text-align: center; padding: 20px;'>
				<p>View the status of all game servers</p>
				<a href='server_status.php' target='_blank' style='background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; display: inline-block; margin: 10px;'>
					🖥️ View Server Status
				</a>
				<br><small style='color: #666;'>Opens in a new window</small>
			</div>
		   </div>
		  </div>";
	
	if( $isAdmin AND $db->isModuleInstalled('status') )
	{
		echo "<h0>".get_lang('server_status')."</h0><br>";
		$servers = $db->getRemoteServers();
		
		echo "<div id='column4' style='float:left;width:40%;' >
			   <div class='bloc rounded' >
			   <h4>".get_lang('select_remote_server')."</h4>
				<div>
				<br>
				<center>
				<form action='' method='GET'>
				<input type='hidden' name='m' value='".$_GET['m']."'/>
				<input type='hidden' name='p' value='".$_GET['p']."'/>
				<select name='remote_server_id' onchange=".'"this.form.submit()"'.">\n";
		
		$agents_ips = array();
		foreach ( $servers as $server_row )
		{
			$agents_ips[$server_row['remote_server_id']] = gethostbyname($server_row['agent_ip']);
			if( !empty( $server_row['remote_server_id'] ) and !isset( $_GET['remote_server_id'] ) OR !empty( $server_row['remote_server_id'] ) and empty( $_GET['remote_server_id'] ) ) 
			{
				$_GET['remote_server_id'] = $server_row['remote_server_id'];
			}

			if( isset($_GET['remote_server_id']) AND $_GET['remote_server_id'] == $server_row['remote_server_id'] )
			{
				$remote = new OGPRemoteLibrary( $server_row['agent_ip'], $server_row['agent_port'], 
												$server_row['encryption_key'], $server_row['timeout'] );
				$host_stat = $remote->status_chk();
				if( $host_stat === 1 )
				{
					$checked = "selected='selected'";
				}
				else
				{
					$checked = '';
					$_GET['remote_server_id'] = 'webhost';
				}
			}
			else
			{
				$checked = '';
			}
			echo "<option value='".$server_row['remote_server_id']."' $checked >".$server_row['remote_server_name']."</option>\n";
		}

		if ( function_exists('exec') )
		{
			$host_ip = isset($_SERVER['LOCAL_ADDR']) ? $_SERVER['LOCAL_ADDR'] : $_SERVER['SERVER_ADDR'];
			$remote_server_id = array_search($host_ip,$agents_ips);
			$show_webhost = true;
			if($remote_server_id)
			{
				$remote_server = $db->getRemoteServer($remote_server_id);
				$remote = new OGPRemoteLibrary( $remote_server['agent_ip'], $remote_server['agent_port'], 
												$remote_server['encryption_key'], $remote_server['timeout'] );
				$host_stat = $remote->status_chk();
				if( $host_stat === 1 )
					$show_webhost = false;
			}
			if($show_webhost)
			{
				$checked = ( isset($_GET['remote_server_id']) AND $_GET['remote_server_id'] == 'webhost' ) ? "selected='selected'" : "";
				echo "<option value='webhost' $checked >Webhost Status</option>";
			}
		}

		echo "	</select>
				</form>
				</center>
				<br><br>
				</div>
			   </div>
			  </div>\n";

		if( isset($_GET['remote_server_id']) AND ( $_GET['remote_server_id'] == "webhost" or $_GET['remote_server_id'] == "" ) )
			unset($_GET['remote_server_id']);
		
		if( isset($_GET['remote_server_id']) )
			$remote_server = "&remote_server_id=".$_GET['remote_server_id'];
		else
			$remote_server = "";
		
		if( isset($_GET['remote_server_id']) OR function_exists('exec') )
			echo $refresh->getdiv($refresh->add("home.php?m=status&type=cleared".$remote_server));
	}

?>
</div>
<script type="text/javascript">
$(document).ready(function(){ 
	<?php echo $refresh->build(isset($settings['query_cache_life']) ? $settings['query_cache_life'] * 2000 : 60000); ?>
});
</script>
<?php

}
?>





