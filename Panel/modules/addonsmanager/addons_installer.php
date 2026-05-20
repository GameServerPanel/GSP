<script type="text/javascript" src="js/modules/addonsmanager.js"></script>
<?php
/*
 *
 * GSP - Game Server Panel (a heavily customized fork of OGP maintained by WDS)
 *
 * Server Content Installer (module: addonsmanager, page: addons)
 * ─────────────────────────────────────────────────────────────────────────────
 * This file handles the actual download+extraction and post-install script
 * execution for a Server Content item selected by a user.
 *
 * CURRENT FLOW:
 *   1. User selects a content type (plugin / mappack / config / ...) from
 *      user_addons.php which links here with addon_type=<type>.
 *   2. User picks a specific content item from a dropdown.
 *   3. On form submit, state=start is set and start_file_download() is called
 *      on the remote agent with the configured URL and target path.
 *   4. The agent downloads and extracts the archive.
 *   5. If a post_script is defined it is run on the agent after extraction.
 *   6. The page auto-refreshes (state=refresh) to show download/script progress.
 *
 * POST-INSTALL SCRIPT REPLACEMENT VARIABLES:
 *   %home_path%        – absolute path of the game server home directory
 *   %home_name%        – display name of the game server home
 *   %control_password% – RCON / control password for this server instance
 *   %max_players%      – maximum player count configured for this mod slot
 *   %ip%               – IP address bound to this server instance
 *   %port%             – game port bound to this server instance
 *   %query_port%       – query/status port (derived from game XML rules)
 *   %incremental%      – internal incremental run counter for this mod/home
 *
 * SECURITY NOTES:
 *   - Users CANNOT supply arbitrary scripts; only the admin-defined post_script
 *     is executed.  Users only pick from the approved list.
 *   - Paths are passed to the agent which is responsible for enforcing that
 *     all paths stay inside the assigned home directory.
 *   - TODO (next phase): add explicit server-side path validation before
 *     sending the command to the agent to block ../  traversal at the panel.
 *
 * ─── FUTURE WORK (TODO – next phase) ────────────────────────────────────────
 * The items below are intentionally NOT implemented here yet.  They are
 * documented so the next contributor knows exactly where to add them.
 *
 * TODO: requires_stop flag
 *   If the content item sets requires_stop=1, stop the server before
 *   initiating the download.  Poll is_server_running() and abort if it
 *   cannot be stopped within a timeout.
 *
 * TODO: backup_before_install flag
 *   If backup_before_install=1, call the agent's backup function or
 *   compress the target path into a timestamped .tar.gz before extraction.
 *
 * TODO: restart_after_install flag
 *   If restart_after_install=1, trigger a server start after a successful
 *   install (i.e. after post_script completes with exit code 0).
 *
 * TODO: install_method field
 *   Current method is always 'download_zip'.  Future methods:
 *     'download_file'  – single-file download, no extraction
 *     'post_script'    – run only the post_script, no download
 *     'steam_workshop' – pass workshop item IDs to the agent's workshop helper
 *     'minecraft_jar'  – download a Minecraft server jar + update start script
 *     'profile_copy'   – copy a profile directory tree into the server home
 *
 * TODO: content_version field
 *   Store the installed version tag so the UI can display "installed: 1.21.1"
 *   and detect whether an update is available.
 *
 * TODO: safe script templates
 *   Provide a set of admin-approved script templates so admins do not have to
 *   write raw bash from scratch.  Templates are stored in the DB and referenced
 *   by content items.
 *
 * TODO: install history / logging
 *   Write a row to a new install_history table (or log file) each time a
 *   content item is installed:
 *     home_id, addon_id, installed_by (user_id), installed_at, result, log_output
 *
 * TODO: user-friendly status output
 *   Replace the raw progress-bar with a card-style status block showing:
 *     content item name, version, download progress, script output, final status.
 *
 * TODO: Steam Workshop integration
 *   When install_method='steam_workshop', pass the workshop item ID list to
 *   the agent.  See SERVER_CONTENT_ROADMAP.md – Part 6 for the full design.
 *
 * TODO: Minecraft jar / version switching
 *   When install_method='minecraft_jar', download the jar from Mojang/Paper/
 *   Purpur/Fabric API, place it at the configured server path, and patch the
 *   startup command line.  See SERVER_CONTENT_ROADMAP.md – Part 7.
 * ─────────────────────────────────────────────────────────────────────────────
 */

function do_progress($kbytes,$totalsize)
{
	$mbytes = round($kbytes / 1024, 2);
					
	if($kbytes > 0)
	{
		$pct = round(( $kbytes / $totalsize ) * 100, 2);
	}
	else
	{
		$pct = "-";
	}
	#echo "Percent is $pct";
	return "$totalsize;$mbytes;$pct"; 
}

require_once("includes/lib_remote.php");
require_once("modules/config_games/server_config_parser.php");
require_once("protocol/lgsl/lgsl_protocol.php");
// Central category map — all valid addon_type values and their labels.
require_once(dirname(__FILE__) . '/server_content_categories.php');
require_once(dirname(__FILE__) . '/server_content_helpers.php');
require_once(dirname(__FILE__) . '/workshop_action.php');

function exec_ogp_module() {

    global $db,$view;
	$home_id = $_REQUEST['home_id'];
	$mod_id  = $_REQUEST['mod_id'];
	$ip      = $_REQUEST['ip'];
	$port    = $_REQUEST['port'];
	$user_id = $_SESSION['user_id'];
	
    $isAdmin = $db->isAdmin( $_SESSION['user_id'] );
	$query_groups = "";
	if($isAdmin) 
		$home_info = $db->getGameHome($home_id);
	else
	{
		$home_info = $db->getUserGameHome($user_id,$home_id);
		$groups = $db->getUsersGroups($_SESSION['user_id']);
		if (!is_array($groups)) {
			$groups = [];
		}
		$query_groups .= " AND (";
		foreach ((array)$groups as $group)
			$query_groups .= "group_id=".$group['group_id']." OR ";
		$query_groups .= "group_id=0 OR group_id IS NULL)";
	}
	
    if ( $home_info === FALSE )
    {
        print_failure(get_lang('no_rights'));
        echo create_back_button("addonsmanager","user_addons");
        return;
    }
	
	$home_cfg_id = $home_info['home_cfg_id'];
	$server_xml  = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
	
	// Use the full category map so newly added types are accepted without
	// editing this file.  The original three types are always present.
	$addon_types = get_server_content_type_keys();
	$addon_type  = isset($_REQUEST['addon_type']) ? scm_normalize_addon_type($_REQUEST['addon_type']) : "";

    $state = isset($_REQUEST['state']) ? $_REQUEST['state'] : "";
    $pid   = isset($_REQUEST['pid'])   ? $_REQUEST['pid']   : -1;
	
    if ( $state != "" )
    {
        $addon_id = (int)$_REQUEST['addon_id'];
		
		$addons_rows = $db->resultQuery("SELECT url, path, post_script, addon_type, install_method, content_version, requires_stop, restart_after_install, workshop_item_id, workshop_app_id, target_path_template, optional_folder_name, config_edit_rule, launch_param_additions, name FROM OGP_DB_PREFIXaddons WHERE addon_id=".$addon_id.$query_groups);
		if (!is_array($addons_rows)) {
			$addons_rows = [];
		}

		if (!$addons_rows) {
			print_failure(get_lang('invalid_addon'));
			$view->refresh('?m=addonsmanager&p=user_addons&home_id='. $home_id .'&mod_id='. $mod_id .'&ip='. $ip .'&port='.$port);
			return;
		}
		
		$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);
		
		$addon_info      = $addons_rows[0];
		$install_method  = scm_get_install_method_default(isset($addon_info['install_method']) ? $addon_info['install_method'] : 'download_zip');
		$content_version = isset($addon_info['content_version']) ? $addon_info['content_version'] : '';
		$requires_stop   = !empty($addon_info['requires_stop']) ? 1 : 0;
		$user_override_keys = ($install_method === 'steam_workshop')
			? array('workshop_item_id', 'workshop_app_id', 'target_path_template', 'optional_folder_name')
			: array();
		$install_payload = scm_collect_install_payload($addon_info, $_REQUEST, $user_override_keys);
		$post_script = '';
		$validation_message = '';
		if ($state == "start" && !scm_validate_install_method_payload($install_method, $install_payload, $validation_message)) {
			print_failure($validation_message);
			return;
		}

		// ── requires_stop guard ───────────────────────────────────────────────
		// If the content item requires the server to be stopped first, check
		// whether the server is currently running and block the install if so.
		// (Phase 2 blocks install; automatic stop/start is Phase 3.)
		if ( $state == "start" && $requires_stop ) {
			$is_running = $remote->is_screen_running( $home_info['home_name'], $home_info['home_id'] );
			if ( $is_running === 1 ) {
				print_failure('This content item requires the server to be stopped before installing. Please stop the server and try again.');
				echo "<p><a href=\"?m=addonsmanager&amp;p=addons&amp;addon_type=".urlencode($addon_info['addon_type'] ?? '')."&amp;home_id=$home_id&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port\">".get_lang('back')."</a></p>";
				return;
			}
		}
		$url = $install_payload['url'];
		$filename = basename($url);
		#### Replace template variables in the post-install script with
		#### live server data before sending to the agent.
		#### Each variable is replaced case-insensitively.
		#### SECURITY: only admin-defined variables are substituted; users
		#### cannot inject additional commands through these fields.
		if($addon_info['post_script'] != "")
		{
			$addon_info['post_script'] = strip_real_escape_string($addon_info['post_script']);
			$check_passed = FALSE;
			$address_at_post = $ip.":".$port;
			$ip_ports = $db->getHomeIpPorts($home_info['home_id']);
			if (!is_array($ip_ports)) {
				$ip_ports = [];
			}
			foreach ((array)$ip_ports as $ip_port);
			{
				$address_owned = $ip_port['ip'].":".$ip_port['port'];
				if($address_owned == $address_at_post)
				{
					$check_passed = TRUE;
					$ip = $ip_port['ip'];
					$port = $ip_port['port'];
				}
			}
			if($check_passed)
			{
				$home_info['ip'] = $ip;
				$home_info['port'] = $port;
				
				if(	isset($server_xml->gameq_query_name) )
				{
					require_once("modules/gamemanager/home_handling_functions.php");
					$home_info['query_port'] = get_query_port($server_xml, $home_info['port']);
				}
				elseif(	isset($server_xml->lgsl_query_name) )
				{
					$get_q_and_s = lgsl_port_conversion((string)$server_xml->lgsl_query_name, $home_info['port'], "", "");
					$home_info['query_port'] = $get_q_and_s['1'];
				}
				
				$home_info["incremental"] = $db->incrementalNumByHomeId( $home_info['home_id'], $home_info['mods'][$mod_id]['mod_cfg_id'], $home_info['remote_server_id'] );
				
				$post_script = preg_replace( "/\%home_path\%/i", $home_info['home_path'], $addon_info['post_script']);
				$post_script = preg_replace( "/\%home_name\%/i", $home_info['home_name'], $post_script);
				$post_script = preg_replace( "/\%control_password\%/i", $home_info['control_password'], $post_script);
				$post_script = preg_replace( "/\%max_players\%/i", $home_info['mods'][$mod_id]['max_players'], $post_script);
				$post_script = preg_replace( "/\%ip\%/i", $home_info['ip'], $post_script);
				$post_script = preg_replace( "/\%port\%/i", $home_info['port'], $post_script);
				$post_script = preg_replace( "/\%query_port\%/i", $home_info['query_port'], $post_script);
				$post_script = preg_replace( "/\%incremental\%/i", $home_info['incremental'], $post_script);
			}
		}

		#### end of replacements
		if ( $state == "start" AND $addon_id != "" ) {
			// Record install attempt in history before triggering download.
			$cache_mode = scm_get_cache_mode($db);
			$history_id = scm_record_install_start(
				$db,
				$home_id,
				$addon_id,
				$user_id,
				$addon_info['url'],
				$content_version,
				$install_method,
				$cache_mode
			);
			$_SESSION['scm_history_id_' . $home_id . '_' . $addon_id] = $history_id;
			scm_log_content_install_action(array(
				'addon_id' => (int)$addon_id,
				'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '',
				'content_type' => $install_method,
				'home_id' => (int)$home_id,
				'home_cfg_id' => (int)$home_info['home_cfg_id'],
				'workshop_id' => isset($install_payload['workshop_item_id']) ? (string)$install_payload['workshop_item_id'] : '',
				'target_path' => ($install_method === 'steam_workshop')
					? (string)$install_payload['target_path_template']
					: (string)$install_payload['path'],
				'action' => 'started',
			));
			if ($install_method === 'steam_workshop') {
				scm_ensure_workshop_schema($db);
				$workshop_runtime = scm_build_workshop_runtime_context($db, $home_info, $server_xml, $install_payload, $validation_message);
				if ($workshop_runtime === false) {
					print_failure($validation_message);
					echo "<p><a href=\"?m=addonsmanager&amp;p=user_addons&amp;home_id=$home_id&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port\">".get_lang('back')."</a></p>";
					return;
				}
				$workshop_item_id = (string)$workshop_runtime['workshop_item_id'];
				$target_path_template = (string)$workshop_runtime['target_path_template'];
				$workshop_app_id = (string)$workshop_runtime['workshop_app_id'];
				$steam_app_id = (string)$workshop_runtime['steam_app_id'];
				$target_path_resolved = (string)$workshop_runtime['target_path_resolved'];
				$extra_manifest = array(
					'addon_id' => (int)$addon_id,
					'target_path_template' => $target_path_template,
					'target_path_resolved' => $target_path_resolved,
					'optional_folder_name' => trim((string)$install_payload['optional_folder_name']),
					'config_edit_rule' => trim((string)$addon_info['config_edit_rule']),
					'launch_param_additions' => trim((string)$addon_info['launch_param_additions']),
					'workshop_app_id' => $workshop_app_id,
					'steam_app_id' => $steam_app_id,
					'steamcmd_path' => isset($workshop_runtime['steamcmd_path']) ? (string)$workshop_runtime['steamcmd_path'] : '',
					'workshop_download_dir' => isset($workshop_runtime['workshop_download_dir']) ? (string)$workshop_runtime['workshop_download_dir'] : '',
					'server_root' => isset($workshop_runtime['server_root']) ? (string)$workshop_runtime['server_root'] : rtrim((string)$home_info['home_path'], '/'),
					'post_install_script' => trim((string)$post_script),
				);
				$workshop_error = '';
				$workshop_ok = scm_workshop_write_manifest_and_run($db, $home_info, $server_xml, 'install', array($workshop_item_id), $workshop_error, $extra_manifest);
				if ($workshop_ok) {
					scm_record_install_done($db, (int)$history_id, 'installed', 0, 'workshop_install_ok');
					scm_upsert_manifest($db, $home_id, $addon_id, array(
						'install_method' => $install_method,
						'content_version' => $content_version,
						'install_state' => 'installed',
						'source_url' => 'steam://workshop/' . $workshop_item_id,
						'installed_by' => $user_id,
					));
					scm_log_content_install_action(array(
						'addon_id' => (int)$addon_id,
						'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '',
						'content_type' => $install_method,
						'home_id' => (int)$home_id,
						'home_cfg_id' => (int)$home_info['home_cfg_id'],
						'workshop_id' => $workshop_item_id,
						'steam_app_id' => $steam_app_id,
						'workshop_app_id' => $workshop_app_id,
						'target_path' => $target_path_resolved,
						'final_folder_path' => $target_path_resolved,
						'action' => 'succeeded',
					));
					print_success(get_lang('addon_installed_successfully'));
				} else {
					scm_record_install_done($db, (int)$history_id, 'failed', 1, $workshop_error);
					scm_log_content_install_action(array(
						'addon_id' => (int)$addon_id,
						'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '',
						'content_type' => $install_method,
						'home_id' => (int)$home_id,
						'home_cfg_id' => (int)$home_info['home_cfg_id'],
						'workshop_id' => $workshop_item_id,
						'steam_app_id' => $steam_app_id,
						'workshop_app_id' => $workshop_app_id,
						'target_path' => $target_path_resolved,
						'action' => 'failed',
						'error' => $workshop_error,
					));
					print_failure($workshop_error);
				}
				echo "<p><a href=\"?m=addonsmanager&amp;p=user_addons&amp;home_id=$home_id&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port\">".get_lang('back')."</a></p>";
				return;
			}
			if ($install_method === 'post_script') {
				$script_command = "cd " . escapeshellarg($home_info['home_path']) . " && /bin/bash -lc " . escapeshellarg((string)$post_script) . " ; echo __GSP_SCRIPT_EXIT:$?";
				$script_output = $remote->exec($script_command);
				$script_ok = is_string($script_output) && preg_match('/__GSP_SCRIPT_EXIT:(\d+)/', $script_output, $sm) && (int)$sm[1] === 0;
				if ($script_ok) {
					scm_record_install_done($db, (int)$history_id, 'installed', 0, trim((string)$script_output));
					scm_upsert_manifest($db, $home_id, $addon_id, array(
						'install_method' => $install_method,
						'content_version' => $content_version,
						'install_state' => 'installed',
						'source_url' => '',
						'installed_by' => $user_id,
					));
					scm_log_content_install_action(array('addon_id' => (int)$addon_id, 'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '', 'content_type' => $install_method, 'home_id' => (int)$home_id, 'home_cfg_id' => (int)$home_info['home_cfg_id'], 'action' => 'succeeded'));
					print_success(get_lang('addon_installed_successfully'));
				} else {
					$error_msg = 'Script/action failed.';
					scm_record_install_done($db, (int)$history_id, 'failed', 1, trim((string)$script_output));
					scm_log_content_install_action(array('addon_id' => (int)$addon_id, 'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '', 'content_type' => $install_method, 'home_id' => (int)$home_id, 'home_cfg_id' => (int)$home_info['home_cfg_id'], 'action' => 'failed', 'error' => trim((string)$script_output)));
					print_failure($error_msg);
				}
				echo "<p><a href=\"?m=addonsmanager&amp;p=user_addons&amp;home_id=$home_id&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port\">".get_lang('back')."</a></p>";
				return;
			}
			if ($install_method === 'config_edit' || $install_method === 'create_folder') {
				$placeholder_map = scm_build_placeholder_map($home_info, array('exe_location' => (string)$server_xml->exe_location));
				$target_template = trim((string)$addon_info['path']);
				$resolved_path = scm_apply_placeholders($target_template, $placeholder_map);
				if ($resolved_path === '' || strpos($resolved_path, '/') !== 0) {
					$resolved_path = clean_path(rtrim($home_info['home_path'], '/') . '/' . ltrim($resolved_path, '/'));
				}
				$ok = false;
				if ($install_method === 'create_folder') {
					$ok = is_string($remote->exec("mkdir -p " . escapeshellarg($resolved_path) . " && echo __GSP_FOLDER_OK"));
				} else {
					$config_rule = trim((string)$addon_info['config_edit_rule']);
					$dir = dirname($resolved_path);
					$cmd = "mkdir -p " . escapeshellarg($dir) . " && touch " . escapeshellarg($resolved_path) . " && printf %s " . escapeshellarg($config_rule . PHP_EOL) . " >> " . escapeshellarg($resolved_path) . " && echo __GSP_CONFIG_OK";
					$out = $remote->exec($cmd);
					$ok = is_string($out) && strpos($out, '__GSP_CONFIG_OK') !== false;
				}
				if ($ok) {
					scm_record_install_done($db, (int)$history_id, 'installed', 0, $install_method . '_ok');
					scm_upsert_manifest($db, $home_id, $addon_id, array('install_method' => $install_method, 'content_version' => $content_version, 'install_state' => 'installed', 'source_url' => '', 'installed_by' => $user_id));
					scm_log_content_install_action(array('addon_id' => (int)$addon_id, 'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '', 'content_type' => $install_method, 'home_id' => (int)$home_id, 'home_cfg_id' => (int)$home_info['home_cfg_id'], 'target_path' => $resolved_path, 'action' => 'succeeded'));
					print_success(get_lang('addon_installed_successfully'));
				} else {
					scm_record_install_done($db, (int)$history_id, 'failed', 1, $install_method . '_failed');
					scm_log_content_install_action(array('addon_id' => (int)$addon_id, 'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '', 'content_type' => $install_method, 'home_id' => (int)$home_id, 'home_cfg_id' => (int)$home_info['home_cfg_id'], 'target_path' => $resolved_path, 'action' => 'failed', 'error' => $install_method . '_failed'));
					print_failure('Content action failed.');
				}
				echo "<p><a href=\"?m=addonsmanager&amp;p=user_addons&amp;home_id=$home_id&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port\">".get_lang('back')."</a></p>";
				return;
			}
			$download_action = ($install_method === 'download_file') ? "" : "uncompress";
			$pid = $remote->start_file_download( $addon_info['url'], $home_info['home_path']."/".$addon_info['path'], $filename, $download_action, $post_script);
		}

		$headers = get_headers($url, 1);

		$download_available = !$headers ? FALSE : TRUE;
		// Check if any error occured
		if($download_available)
		{
			$bytes = is_array($headers['Content-Length']) ? $headers['Content-Length'][1] : $headers['Content-Length'];
			// Display the File Size
			$totalsize = $bytes / 1024;
			clearstatcache();
		}

		$kbytes = $remote->rsync_progress($home_info['home_path']."/".$addon_info['path']."/".$filename);
        list($totalsize,$mbytes,$pct) = explode(";",do_progress($kbytes,$totalsize));
		$totalmbytes = round($totalsize / 1024, 2);
		$pct = $pct > 100 ? 100 : $pct;
		echo "<h2>" . htmlentities($home_info['home_name']) . "</h2>";
		echo '<div class="dragbox bloc rounded" style="background-color:#dce9f2;" >
				<h4>'.get_lang('install')." ".$filename." ${mbytes}MB/${totalmbytes}MB</h4>
			  <div style='background-color:#dce9f2;' >
			  ";
		$bar = '';
 		for( $i = 1; $i <= $pct; $i++ )
		{
			$bar .= '<img style="width:0.92%;vertical-align:middle;" src="images/progressBar.png">';
		}
		echo "<center>$bar <b style='vertical-align:top;display:inline;font-size:1.2em;color:red;' >$pct%</b></center>
				</div>
			  </div>";
		
		if ( ( $pct == "100" or !$download_available ) AND $post_script != "" )
		{
			$log_retval = $remote->get_log( "post_script",
											$pid,
											clean_path($home_info['home_path']."/".$server_xml->exe_location),
											$script_log);
			if ($log_retval == 0)
			{
				print_failure(get_lang('agent_offline'));
			}
			elseif ($log_retval == 1 || $log_retval == 2)
			{
				echo "<pre class='log'>".$script_log."</pre>";
			}
			elseif( $remote->is_screen_running("post_script",$pid) == 1 )
			{
				print_failure(get_lang_f('unable_to_get_log',$log_retval));
			}
		}
		
		if( $pct == "100" or !$download_available or ( $download_available and $pct == "-" and $pid > 0 ) )
		{
			if(!$download_available)
			{
				print_failure(get_lang('failed_to_start_file_download'));
				scm_log_content_install_action(array(
					'addon_id' => (int)$addon_id,
					'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '',
					'content_type' => $install_method,
					'home_id' => (int)$home_id,
					'home_cfg_id' => (int)$home_info['home_cfg_id'],
					'action' => 'failed',
					'error' => 'failed_to_start_file_download',
				));
			}
			elseif( $remote->is_file_download_in_progress($pid) === 1 )
			{
				if ($install_method === 'download_zip')
					print_success(get_lang_f('wait_while_decompressing', $filename));
				else
					print_success(get_lang('install') . " " . $filename . "...");
				echo "<p><a href=\"?m=addonsmanager&amp;p=addons&amp;state=refresh&amp;home_id=$home_id&amp;mod_id=$mod_id".
					 "&amp;ip=$ip&amp;port=$port&amp;addon_id=$addon_id&amp;pid=$pid\">".get_lang('refresh')."</a></p>";
				$view->refresh("?m=addonsmanager&amp;p=addons&amp;state=refresh&amp;home_id=$home_id&amp;mod_id=$mod_id".
							   "&amp;ip=$ip&amp;port=$port&addon_id=$addon_id&amp;pid=$pid",5);
			}
			elseif( $remote->is_file_download_in_progress($pid) === 0 AND $remote->is_screen_running("post_script",$pid) === 0 )
			{
				print_success(get_lang('addon_installed_successfully'));
				// Update install history and manifest on successful completion.
				$history_key = 'scm_history_id_' . $home_id . '_' . $addon_id;
				if (!empty($_SESSION[$history_key])) {
					scm_record_install_done($db, (int)$_SESSION[$history_key], 'installed', 0);
					unset($_SESSION[$history_key]);
				}
				scm_upsert_manifest($db, $home_id, $addon_id, array(
					'install_method'  => $install_method,
					'content_version' => $content_version,
					'install_state'   => 'installed',
					'source_url'      => $addon_info['url'],
					'installed_by'    => $user_id,
				));
				scm_log_content_install_action(array(
					'addon_id' => (int)$addon_id,
					'addon_name' => isset($addon_info['name']) ? $addon_info['name'] : '',
					'content_type' => $install_method,
					'home_id' => (int)$home_id,
					'home_cfg_id' => (int)$home_info['home_cfg_id'],
					'target_path' => isset($addon_info['path']) ? (string)$addon_info['path'] : '',
					'action' => 'succeeded',
				));
				echo "<p><a href=\"?m=addonsmanager&amp;p=user_addons&amp;home_id=$home_id".
					 "&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port\">".get_lang('back')."</a></p>";
				$view->refresh("?m=addonsmanager&amp;p=user_addons&amp;home_id=$home_id".
							   "&amp;mod_id=$mod_id&amp;ip=$ip&amp;port=$port",10);
				return;
			}
		}
		else
		{
			echo "<p><a href=\"?m=addonsmanager&amp;p=addons&amp;state=refresh&amp;home_id=$home_id&amp;mod_id=$mod_id".
				 "&amp;ip=$ip&amp;port=$port&amp;addon_id=$addon_id&amp;pid=$pid\">".get_lang('refresh')."</a></p>";
			$view->refresh("?m=addonsmanager&amp;p=addons&amp;state=refresh&amp;home_id=$home_id&amp;mod_id=$mod_id".
						   "&amp;ip=$ip&amp;port=$port&amp;addon_id=$addon_id&amp;pid=$pid",5);
		}
		
    }
    elseif( $addon_type != "" )
    {

    	if (!(is_array($addon_types) && in_array($addon_type, $addon_types))) {
    		print_failure(get_lang('invalid_addon_type'));
    		$view->refresh('?m=addonsmanager&p=user_addons&home_id='. $home_id .'&mod_id='. $mod_id .'&ip='. $ip .'&port='.$port);

    		return;
    	}

		// Workshop items are managed through the dedicated workshop_content page
		// where users enter their own Workshop IDs.  Redirect there immediately.
		if ($addon_type === 'workshop_item') {
			$first_addon_id = 0;
			$wk_addons = $db->resultQuery(
				"SELECT addon_id FROM OGP_DB_PREFIXaddons
				  WHERE addon_type='workshop_item' AND home_cfg_id=" . (int)$home_cfg_id . $query_groups . "
				  ORDER BY name ASC LIMIT 1"
			);
			if (is_array($wk_addons) && !empty($wk_addons[0]['addon_id'])) {
				$first_addon_id = (int)$wk_addons[0]['addon_id'];
			}
			$redirect = "?m=addonsmanager&p=workshop_content&home_id=" . (int)$home_id .
				"&mod_id=" . (int)$mod_id . "&ip=" . urlencode($ip) . "&port=" . urlencode($port) .
				($first_addon_id > 0 ? "&addon_id=" . $first_addon_id : '');
			$view->refresh($redirect);
			echo "<p>Redirecting to Workshop Content manager…<br>";
			echo "<a href='" . htmlspecialchars($redirect, ENT_QUOTES, 'UTF-8') . "'>Click here if not redirected.</a></p>";
			return;
		}
		?>
			<?php
				$category_labels = get_server_content_categories();
				$addon_type_lang = isset($category_labels[$addon_type]) ? $category_labels[$addon_type] : ucfirst(str_replace('_', ' ', $addon_type));
				$addons = $db->resultQuery(
					"SELECT addon_id, name, install_method, workshop_item_id, workshop_app_id, target_path_template, optional_folder_name
					   FROM OGP_DB_PREFIXaddons
					  WHERE addon_type='".$addon_type."' AND home_cfg_id=" . $home_cfg_id . $query_groups . "
					  ORDER BY name ASC"
				);
				if (!is_array($addons)) {
					$addons = [];
				}
				$selected_addon = isset($addons[0]) ? $addons[0] : array();
				$default_install_method = isset($selected_addon['install_method']) ? scm_get_install_method_default($selected_addon['install_method']) : '';
				$is_workshop_default = ($default_install_method === 'steam_workshop');
				$workshop_profile = function_exists('sw_get_profile_for_home') ? sw_get_profile_for_home($db, (int)$home_id) : false;
				$default_workshop_app_id = !empty($selected_addon['workshop_app_id'])
					? trim((string)$selected_addon['workshop_app_id'])
					: ((is_array($workshop_profile) && !empty($workshop_profile['workshop_app_id'])) ? (string)$workshop_profile['workshop_app_id'] : scm_extract_workshop_app_id($server_xml));
				$default_target_template = !empty($selected_addon['target_path_template'])
					? trim((string)$selected_addon['target_path_template'])
					: ((is_array($workshop_profile) && !empty($workshop_profile['install_path_template'])) ? (string)$workshop_profile['install_path_template'] : '{SERVER_ROOT}/{MOD_FOLDER}');
				$default_optional_folder_name = !empty($selected_addon['optional_folder_name']) ? trim((string)$selected_addon['optional_folder_name']) : '';
			?>
			<h2><?php echo htmlentities($home_info['home_name'])."&nbsp;".$addon_type_lang ;?></h2>
            <table class='center'>
			<form method='get'>
			<input type='hidden' name='m' value='addonsmanager' />
            <input type='hidden' name='p' value='addons' />
            <input type='hidden' name='home_id' value='<?php echo $home_id; ?>' />
			<input type='hidden' name='mod_id' value='<?php echo $mod_id; ?>' />
			<input type='hidden' name='ip' value='<?php  echo $ip; ?>' />
			<input type='hidden' name='port' value='<?php  echo $port; ?>' />
            <input type='hidden' name='state' value='start' />
            <tr><td align='right'><?php print_lang('game_name'); ?>: </td><td align='left'><?php  echo $home_info['game_name']; ?></td></tr>
            <tr><td align='right'><?php print_lang('directory'); ?>: </td><td align='left'><?php  echo $home_info['home_path']; ?></td></tr>
            <tr><td align='right'><?php print_lang('remote_server'); ?>: </td>
            <td align='left'><?php  echo "$home_info[remote_server_name] ($home_info[agent_ip]:$home_info[agent_port])"; ?></td></tr>
            <tr><td align='right'><?php print_lang('select_addon'); ?>: </td>
            <td align='left'>
			<select name="addon_id" id="scm-user-addon-select">
			<?php foreach ((array)$addons as $addon) { ?>
			<option
				value="<?php echo (int)$addon['addon_id']; ?>"
				data-install-method="<?php echo htmlspecialchars(scm_get_install_method_default(isset($addon['install_method']) ? $addon['install_method'] : ''), ENT_QUOTES, 'UTF-8'); ?>"
				data-workshop-item-id="<?php echo htmlspecialchars(isset($addon['workshop_item_id']) ? $addon['workshop_item_id'] : '', ENT_QUOTES, 'UTF-8'); ?>"
				data-workshop-app-id="<?php echo htmlspecialchars(isset($addon['workshop_app_id']) ? $addon['workshop_app_id'] : '', ENT_QUOTES, 'UTF-8'); ?>"
				data-target-path-template="<?php echo htmlspecialchars(isset($addon['target_path_template']) ? $addon['target_path_template'] : '', ENT_QUOTES, 'UTF-8'); ?>"
				data-optional-folder-name="<?php echo htmlspecialchars(isset($addon['optional_folder_name']) ? $addon['optional_folder_name'] : '', ENT_QUOTES, 'UTF-8'); ?>"
			><?php echo htmlspecialchars($addon['name']); ?></option>
			<?php } ?>
			</select>
			</td></tr>
            <tr class="scm-user-workshop-row" <?php echo $is_workshop_default ? '' : 'style="display:none;"'; ?>><td align='right'><strong><?php print_lang('workshop_id'); ?></strong></td><td align='left'>
				<input type="text" id="scm-user-workshop-id" name="workshop_item_id" size="50" value="<?php echo htmlspecialchars(isset($selected_addon['workshop_item_id']) ? $selected_addon['workshop_item_id'] : '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="Example Arma 3 Workshop ID: 450814997" />
				<div class="info" style="margin-top:4px;">Install a Steam Workshop mod using Workshop ID. URL is not required.</div>
			</td></tr>
            <tr class="scm-user-workshop-row" <?php echo $is_workshop_default ? '' : 'style="display:none;"'; ?>><td align='right'><strong>Workshop App ID Override</strong></td><td align='left'>
				<input type="text" id="scm-user-workshop-app-id" name="workshop_app_id" size="50" value="<?php echo htmlspecialchars($default_workshop_app_id, ENT_QUOTES, 'UTF-8'); ?>" data-default-app-id="<?php echo htmlspecialchars($default_workshop_app_id, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Optional App ID override" />
			</td></tr>
            <tr class="scm-user-workshop-row" <?php echo $is_workshop_default ? '' : 'style="display:none;"'; ?>><td align='right'><strong><?php print_lang('optional_folder_name'); ?></strong></td><td align='left'>
				<input type="text" id="scm-user-optional-folder-name" name="optional_folder_name" size="50" value="<?php echo htmlspecialchars($default_optional_folder_name, ENT_QUOTES, 'UTF-8'); ?>" placeholder="@myWorkshopMod (optional)" />
			</td></tr>
            <tr class="scm-user-workshop-row" <?php echo $is_workshop_default ? '' : 'style="display:none;"'; ?>><td align='right'><strong><?php print_lang('target_path_template'); ?></strong></td><td align='left'>
				<input type="text" id="scm-user-target-path-template" name="target_path_template" size="85" value="<?php echo htmlspecialchars($default_target_template, ENT_QUOTES, 'UTF-8'); ?>" data-default-template="<?php echo htmlspecialchars($default_target_template, ENT_QUOTES, 'UTF-8'); ?>" placeholder="{SERVER_ROOT}/{MOD_FOLDER}" />
				<div class="info" style="margin-top:4px;">Supported placeholders: {SERVER_ROOT}, {GAME_ROOT}, {WORKSHOP_ID}, {WORKSHOP_APP_ID}, {STEAM_APP_ID}, {FOLDER_NAME}, {MOD_FOLDER}</div>
			</td></tr>
            <tr class="scm-user-workshop-row" <?php echo $is_workshop_default ? '' : 'style="display:none;"'; ?>><td align='right'><strong>Target Path Preview</strong></td><td align='left'>
				<code id="scm-user-target-path-preview"
					data-server-root="<?php echo htmlspecialchars(rtrim((string)$home_info['home_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
					data-game-root="<?php echo htmlspecialchars(rtrim((string)$home_info['home_path'], '/'), ENT_QUOTES, 'UTF-8'); ?>"
					data-steam-app-id="<?php echo htmlspecialchars((is_array($workshop_profile) && !empty($workshop_profile['steam_app_id'])) ? (string)$workshop_profile['steam_app_id'] : '', ENT_QUOTES, 'UTF-8'); ?>"
				><?php echo htmlspecialchars($default_target_template, ENT_QUOTES, 'UTF-8'); ?></code>
			</td></tr>
            <tr><td colspan='2' class='info'>&nbsp;</td></tr>
            <td align='left'>
			&nbsp;
			</td></tr><tr><td align="right">
            <input type="submit" name="update" value="<?php print_lang('install'); ?>" />
            </form></td><td>
			<form method="get">
			<input type="hidden" name="m" value="addonsmanager" />
            <input type="hidden" name="p" value="user_addons" />
			<input type="hidden" name="home_id" value="<?php  echo $home_id; ?>" />
			<input type="hidden" name="mod_id" value="<?php  echo $mod_id; ?>" />
			<input type="hidden" name="ip" value="<?php  echo $ip; ?>" />
			<input type="hidden" name="port" value="<?php  echo $port; ?>" />
			<input type="submit" value="<?php print_lang('back'); ?>" />
			</form>
			</td></tr>
			</table>
<?php 
    }
}
?>
