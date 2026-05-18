<?php
/*
 *
 * GSP - Workshop Content actions (Phase 1)
 *
 */

require_once("includes/lib_remote.php");
require_once("modules/config_games/server_config_parser.php");
require_once(dirname(__FILE__) . '/server_content_helpers.php');

function scm_workshop_log_action($db, $home_id, $user_id, $message)
{
	$db->logger("server_content_workshop home_id=".(int)$home_id." user_id=".(int)$user_id." ".$message);
}

function scm_workshop_update_rows_state($db, $home_id, array $item_ids, $state, $error = null, $mark_install = false, $mark_update = false)
{
	if (empty($item_ids)) {
		return true;
	}
	$escaped_ids = array();
	foreach ($item_ids as $item_id) {
		$escaped_ids[] = "'" . $db->realEscapeSingle((string)$item_id) . "'";
	}
	$set = array(
		"install_state='" . $db->realEscapeSingle($state) . "'",
		"updated_at=NOW()",
	);
	if ($mark_install) {
		$set[] = "last_installed_at=NOW()";
	}
	if ($mark_update) {
		$set[] = "last_updated_at=NOW()";
	}
	if ($error === null) {
		$set[] = "last_error=NULL";
	} else {
		$set[] = "last_error='" . $db->realEscapeSingle($error) . "'";
	}

	$query = "UPDATE `".OGP_DB_PREFIX."server_content_workshop`
		SET ".implode(", ", $set)."
		WHERE home_id=".(int)$home_id." AND workshop_item_id IN (".implode(",", $escaped_ids).")";
	return (bool)$db->query($query);
}

function scm_workshop_filter_existing_ids($db, $home_id, array $item_ids)
{
	if (empty($item_ids)) {
		return array();
	}
	$escaped_ids = array();
	foreach ($item_ids as $item_id) {
		$escaped_ids[] = "'" . $db->realEscapeSingle((string)$item_id) . "'";
	}
	$rows = $db->resultQuery(
		"SELECT workshop_item_id FROM `".OGP_DB_PREFIX."server_content_workshop`
		 WHERE home_id=".(int)$home_id." AND workshop_item_id IN (".implode(",", $escaped_ids).")"
	);
	$allowed = array();
	if (is_array($rows)) {
		foreach ((array)$rows as $row) {
			$allowed[(string)$row['workshop_item_id']] = (string)$row['workshop_item_id'];
		}
	}
	return array_values($allowed);
}

function scm_workshop_write_manifest_and_run($db, array $home_info, $server_xml, $action, array $item_ids, &$error = '')
{
	$error = '';
	if (empty($item_ids)) {
		$error = 'No Workshop IDs were selected for this action.';
		return false;
	}

	$manifest_path = scm_get_workshop_manifest_path($home_info);
	if ($manifest_path === false) {
		$error = 'Manifest path validation failed for this server home.';
		return false;
	}

	$script_path = scm_get_workshop_script_path($home_info, $server_xml);
	$script_path = trim((string)$script_path);
	if ($script_path === '' || !preg_match('/^[^\\r\\n\\0]+$/', $script_path)) {
		$error = 'Workshop script path is invalid.';
		return false;
	}

	$home_path = rtrim(clean_path((string)$home_info['home_path']), '/');
	if (!scm_path_is_under_home($home_path, $manifest_path)) {
		$error = 'Manifest path is outside of the server home.';
		return false;
	}

	$manifest_dir = dirname($manifest_path);
	$manifest = array(
		'action' => (string)$action,
		'home_id' => (int)$home_info['home_id'],
		'home_cfg_id' => (int)$home_info['home_cfg_id'],
		'workshop_app_id' => scm_extract_workshop_app_id($server_xml),
		'items' => array_values($item_ids),
	);
	$manifest_json = json_encode($manifest);
	if ($manifest_json === false) {
		$error = 'Failed to encode workshop manifest JSON.';
		return false;
	}

	$remote = new OGPRemoteLibrary(
		$home_info['agent_ip'],
		$home_info['agent_port'],
		$home_info['encryption_key'],
		$home_info['timeout']
	);

	$remote->exec("mkdir -p " . escapeshellarg($manifest_dir));
	if ((int)$remote->remote_writefile($manifest_path, $manifest_json) !== 1) {
		$error = 'Failed to write workshop manifest to remote server.';
		return false;
	}
	if ((int)$remote->rfile_exists($script_path) !== 1) {
		$error = 'Configured workshop script not found on agent host: ' . $script_path;
		return false;
	}

	$command = "bash " . escapeshellarg($script_path) . " " . escapeshellarg($manifest_path) . " ; echo __GSP_WORKSHOP_EXIT:$?";
	$output = $remote->exec($command);
	if (!is_string($output) || $output === '') {
		$error = 'Workshop script did not return an execution status.';
		return false;
	}
	if (!preg_match('/__GSP_WORKSHOP_EXIT:(\d+)/', $output, $matches)) {
		$error = 'Workshop script exit marker not found in output.';
		return false;
	}
	$exit_code = (int)$matches[1];
	if ($exit_code !== 0) {
		$error = 'Workshop script failed (exit '.$exit_code.'): '.trim($output);
		return false;
	}
	return true;
}

function scm_workshop_handle_action($db, array $home_info, $user_id, $action, $raw_ids, array $selected_ids, &$message, &$is_error)
{
	$message = '';
	$is_error = true;
	if (!scm_ensure_workshop_schema($db)) {
		$message = 'Workshop schema migration failed.';
		return false;
	}

	$home_id = (int)$home_info['home_id'];
	$user_id = (int)$user_id;
	$server_xml = read_server_config(SERVER_CONFIG_LOCATION . "/" . $home_info['home_cfg_file']);
	if ($server_xml === false) {
		$message = 'Unable to read server configuration for workshop action.';
		return false;
	}

	if ($action === 'install_new') {
		$invalid = array();
		$item_ids = scm_parse_workshop_ids($raw_ids, $invalid);
		if (!empty($invalid)) {
			$message = 'Invalid Workshop IDs: ' . implode(', ', $invalid);
			return false;
		}
		if (empty($item_ids)) {
			$message = 'Enter at least one numeric Workshop ID.';
			return false;
		}

		foreach ($item_ids as $item_id) {
			$query = "INSERT INTO `".OGP_DB_PREFIX."server_content_workshop`
				(home_id, home_cfg_id, remote_server_id, workshop_app_id, workshop_item_id, install_state, created_by, created_at, updated_at)
				VALUES (
					".$home_id.",
					".(int)$home_info['home_cfg_id'].",
					".(int)$home_info['remote_server_id'].",
					'".$db->realEscapeSingle(scm_extract_workshop_app_id($server_xml))."',
					'".$db->realEscapeSingle($item_id)."',
					'selected',
					".$user_id.",
					NOW(),
					NOW()
				)
				ON DUPLICATE KEY UPDATE
					home_cfg_id=VALUES(home_cfg_id),
					remote_server_id=VALUES(remote_server_id),
					workshop_app_id=VALUES(workshop_app_id),
					install_state='selected',
					last_error=NULL,
					updated_at=NOW()";
			$db->query($query);
		}

		scm_workshop_update_rows_state($db, $home_id, $item_ids, 'installing', null, false, false);
		$error = '';
		$ok = scm_workshop_write_manifest_and_run($db, $home_info, $server_xml, 'install', $item_ids, $error);
		if ($ok) {
			scm_workshop_update_rows_state($db, $home_id, $item_ids, 'installed', null, true, true);
			scm_workshop_log_action($db, $home_id, $user_id, "install_new ids=".implode(',', $item_ids)." status=success");
			$is_error = false;
			$message = 'Workshop IDs installed successfully.';
			return true;
		}
		scm_workshop_update_rows_state($db, $home_id, $item_ids, 'failed', $error, false, false);
		scm_workshop_log_action($db, $home_id, $user_id, "install_new ids=".implode(',', $item_ids)." status=failed error=".$error);
		$message = $error;
		return false;
	}

	if ($action === 'update_selected' || $action === 'remove_selected') {
		$item_ids = scm_workshop_filter_existing_ids($db, $home_id, scm_parse_selected_workshop_ids($selected_ids));
		if (empty($item_ids)) {
			$message = 'Select one or more saved Workshop IDs.';
			return false;
		}
		$target_action = ($action === 'remove_selected') ? 'remove' : 'update';
		scm_workshop_update_rows_state($db, $home_id, $item_ids, 'installing', null, false, false);
		$error = '';
		$ok = scm_workshop_write_manifest_and_run($db, $home_info, $server_xml, $target_action, $item_ids, $error);
		if ($ok) {
			if ($target_action === 'remove') {
				scm_workshop_update_rows_state($db, $home_id, $item_ids, 'removed', null, false, true);
			} else {
				scm_workshop_update_rows_state($db, $home_id, $item_ids, 'installed', null, false, true);
			}
			scm_workshop_log_action($db, $home_id, $user_id, $action." ids=".implode(',', $item_ids)." status=success");
			$is_error = false;
			$message = ($target_action === 'remove') ? 'Selected Workshop IDs marked removed.' : 'Selected Workshop IDs updated successfully.';
			return true;
		}
		scm_workshop_update_rows_state($db, $home_id, $item_ids, 'failed', $error, false, false);
		scm_workshop_log_action($db, $home_id, $user_id, $action." ids=".implode(',', $item_ids)." status=failed error=".$error);
		$message = $error;
		return false;
	}

	if ($action === 'update_all') {
		$rows = $db->resultQuery(
			"SELECT workshop_item_id FROM `".OGP_DB_PREFIX."server_content_workshop`
			 WHERE home_id=".$home_id." AND install_state<>'removed'"
		);
		$item_ids = array();
		if (is_array($rows)) {
			foreach ((array)$rows as $row) {
				$item_ids[] = (string)$row['workshop_item_id'];
			}
		}
		$item_ids = scm_parse_selected_workshop_ids($item_ids);
		if (empty($item_ids)) {
			$message = 'No Workshop IDs are currently saved for this server.';
			return false;
		}
		scm_workshop_update_rows_state($db, $home_id, $item_ids, 'installing', null, false, false);
		$error = '';
		$ok = scm_workshop_write_manifest_and_run($db, $home_info, $server_xml, 'update', $item_ids, $error);
		if ($ok) {
			scm_workshop_update_rows_state($db, $home_id, $item_ids, 'installed', null, false, true);
			scm_workshop_log_action($db, $home_id, $user_id, "update_all ids=".implode(',', $item_ids)." status=success");
			$is_error = false;
			$message = 'All saved Workshop IDs updated successfully.';
			return true;
		}
		scm_workshop_update_rows_state($db, $home_id, $item_ids, 'failed', $error, false, false);
		scm_workshop_log_action($db, $home_id, $user_id, "update_all ids=".implode(',', $item_ids)." status=failed error=".$error);
		$message = $error;
		return false;
	}

	$message = 'Invalid workshop action.';
	return false;
}

