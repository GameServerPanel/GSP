<?php

require_once('home_handling_functions.php');
require_once("modules/config_games/server_config_parser.php");

    global $view;


	echo "home id = " .$home_id;
        $user_id = $_SESSION['user_id'];
	
	
	$isAdmin = $db->isAdmin( $user_id );
	if($isAdmin) 
		$home_info = $db->getGameHome($home_id);
	else
		$home_info = $db->getUserGameHome($user_id,$home_id);
	
	$current_mod_info = $home_info['mods'][$mod_id];	
	$home_cfg_id = $current_mod_info['home_cfg_id'];
	$mod_cfg_id = $current_mod_info['mod_cfg_id'];
	
	if($home_cfg_id === null && $mod_cfg_id === null){
	       $home_cfg_id = 67;
               $mod_cfg_id = 68;
               //print_failure(get_lang('invalid_game_mod_id'));
		//return;
	}
	
    if ( $home_info === FALSE )
    {
        //print_failure( get_lang("no_access_to_home") );
        //return;
    }

    $server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);

    if ( !$server_xml )
    {
        echo create_back_button( $_GET['m'], 'game_monitor&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'] );
        return;
    }

    require_once('includes/lib_remote.php');
    $remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);

    $home_log = "";
	
	if( isset( $server_xml->console_log ) )
	{
		$log_retval = $remote->get_log(OGP_SCREEN_TYPE_HOME,
			$home_info['home_id'],
			clean_path($home_info['home_path']),
			$home_log, 100, (string) $server_xml->console_log);
	}
	else
	{
		$log_retval = $remote->get_log(OGP_SCREEN_TYPE_HOME,
			$home_info['home_id'],
			clean_path($home_info['home_path']."/".$server_xml->exe_location),
			$home_log);
	}

    if ($log_retval == 0)
    {
        print_failure( get_lang("agent_offline") );
		echo create_back_button( $_GET['m'], 'game_monitor&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'] );
    }
    elseif ($log_retval == 1 || $log_retval == 2)
    {
		// Force log file contents to be UTF-8 (fixes http://www.opengamepanel.org/forum/viewthread.php?thread_id=5379)
		if(hasValue($home_log)){
			$home_log = utf8_encode($home_log);
		}
		
		echo "<h2>".htmlentities($home_info['home_name'])."</h2>";
		if($log_retval == 1)
		{
			if( isset( $_GET['size'] ) and $_GET['size'] == "+" )
			{
				$height = "100%";
				$size_control = "-";
			}
			else
			{
				$height = "500px";
				$size_control = "+";
			}
			
			$intervals = array( "1s" => "1000",
								"2s" => "2000",
								"4s" => "4000",
								"8s" => "8000",
								"30s" => "30000",
								"2m" => "120000",
								"5m" => "300000" );
			$allowed_intervals = array_values($intervals);
			$minimum_interval = (int)min($allowed_intervals);
			$setInterval = isset($_GET['setInterval']) ? (int)$_GET['setInterval'] : 4000;
			if( !in_array((string)$setInterval, $allowed_intervals, true) )
			{
				$setInterval = 4000;
			}

			$intSel = get_lang("refresh_interval") . ':<select id="gm-log-refresh-interval" name="setInterval">';
			foreach ((array)$intervals as $interval => $value )
			{
				$selected = ($setInterval == (int)$value) ? 'selected="selected"' : "";
				$intSel .= '<option value="'.$value.'" '.$selected.' >'.$interval.'</option>';
			}
			$intSel .= "</select>";

			$ajax_home_id = isset($home_id) ? (int)$home_id : 0;
			$ajax_mod_id = isset($mod_id) ? (int)$mod_id : 0;
			$ajax_ip = isset($ip) ? rawurlencode($ip) : "";
			$ajax_port = isset($port) ? rawurlencode($port) : "";
			$ajax_log_url = "modules/gamemanager/ajax_log.php?home_id=".$ajax_home_id."&mod_id=".$ajax_mod_id."&ip=".$ajax_ip."&port=".$ajax_port;

			echo "<table class='center' ><tr><td>$intSel</td><td><button type='button' id='gm-log-size-toggle'>".$size_control."</button></td></tr></table>";
			echo "<pre id='gm-log-output' class='log' style='height:".$height.";overflow:auto;max-width:1600px;'>".htmlentities($home_log)."</pre>";
			?>
			<script type="text/javascript">
			(function($) {
				var $log = $('#gm-log-output');
				var $interval = $('#gm-log-refresh-interval');
				var $sizeToggle = $('#gm-log-size-toggle');
				var endpoint = '<?php echo $ajax_log_url; ?>';
				var pollTimer = null;
				var minimumInterval = <?php echo $minimum_interval; ?>;
				var lastLogText = $log.text();

				function isFollowingBottom() {
					var node = $log.get(0);
					return (node.scrollHeight - node.scrollTop - node.clientHeight) <= 50;
				}

				function scrollToBottom() {
					var node = $log.get(0);
					node.scrollTop = node.scrollHeight;
				}

				function refreshLog() {
					console.log('Log refresh started...');
					var shouldFollow = isFollowingBottom();
					$.ajax({
						url: endpoint,
						cache: false,
						dataType: 'text'
					}).done(function(data) {
						if (typeof data !== 'string') {
							data = '';
						}
						if (data !== lastLogText) {
							$log.text(data);
							lastLogText = data;
							if (shouldFollow) {
								scrollToBottom();
							}
						}
						console.log('Log refresh successful...');
					}).fail(function(jqXHR, textStatus, errorThrown) {
						console.log('Log refresh failed...', textStatus, errorThrown);
					});
				}

				function restartPolling() {
					var selectedInterval = parseInt($interval.val(), 10);
					if (isNaN(selectedInterval) || selectedInterval < minimumInterval) {
						selectedInterval = 4000;
						$interval.val('4000');
					}
					if (pollTimer !== null) {
						clearInterval(pollTimer);
					}
					pollTimer = setInterval(refreshLog, selectedInterval);
				}

				$interval.on('change', function() {
					restartPolling();
					refreshLog();
				});

				$sizeToggle.on('click', function() {
					var isCollapsed = $log.css('height') === '500px';
					if (isCollapsed) {
						$log.css('height', '100%');
						$sizeToggle.text('-');
					} else {
						$log.css('height', '500px');
						$sizeToggle.text('+');
					}
				});

				scrollToBottom();
				restartPolling();
			})(jQuery);
			</script>
			<?php
			if(	($server_xml->control_protocol and preg_match("/^r?l?con2?$/", $server_xml->control_protocol)) OR
				($server_xml->gameq_query_name and $server_xml->gameq_query_name == "minecraft") OR 
				($server_xml->lgsl_query_name  and $server_xml->lgsl_query_name == "7dtd") )
				require('modules/gamemanager/rcon.php');
		}
		else
		{
			echo "<pre class='log'>" . htmlentities($home_log) . "</pre>";
			print_failure( get_lang("server_not_running") );
		}
		echo create_back_button( $_GET['m'], 'game_monitor&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'] );
    }
    else
    {
        print_failure(get_lang_f('unable_to_get_log',$log_retval));
		echo create_back_button( $_GET['m'], 'game_monitor&home_id-mod_id-ip-port='.$_GET['home_id-mod_id-ip-port'] );
    }
?>
