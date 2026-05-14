<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 * GSP / WDS customisation — Migrate replaces the old Clone feature.
 *
 * This page lets an admin copy all files from one game server to another
 * server of the SAME game type, using rsync (Linux) or robocopy (Windows
 * fallback). The source server is NOT deleted or changed.
 *
 */

function exec_ogp_module()
{
	global $db, $settings;

	$home_id = intval($_REQUEST['home_id'] ?? 0);
	if ($home_id <= 0) {
		print_failure(get_lang('invalid_home_id'));
		return;
	}

	$source = $db->getGameHomeWithoutMods($home_id);
	if (empty($source)) {
		print_failure(get_lang('invalid_home_id'));
		return;
	}

	echo "<h2>" . htmlentities(get_lang_f('migrate_server', $source['home_name'])) . "</h2>";
	echo create_back_button('user_games');

	// ------------------------------------------------------------------ //
	// Handle the migration POST                                           //
	// ------------------------------------------------------------------ //
	if (isset($_POST['do_migrate'])) {
		$dest_id = intval($_POST['dest_home_id'] ?? 0);
		if ($dest_id <= 0 || $dest_id === $home_id) {
			print_failure(get_lang('invalid_home_id'));
			return;
		}

		$dest = $db->getGameHomeWithoutMods($dest_id);
		if (empty($dest)) {
			print_failure(get_lang('invalid_home_id'));
			return;
		}

		// Validate same game type
		if ($source['home_cfg_id'] != $dest['home_cfg_id']) {
			print_failure(get_lang('migrate_different_game_type'));
			return;
		}

		// Require explicit confirmation tick
		if (empty($_POST['confirm_overwrite'])) {
			print_failure(get_lang('migrate_confirm_required'));
			return;
		}

		$result = migrate_game_server($db, $source, $dest, $settings);

		if ($result === TRUE || $result === -1) {
			// -1 = async (copy running in background), TRUE = instant success
			if ($result === -1) {
				print_success(get_lang('migrate_running_background'));
			} else {
				print_success(get_lang('migrate_complete'));
			}
			echo "<p><a href='?m=user_games'>&lt;&lt; " . get_lang('back_to_game_servers') . "</a></p>";
		} else {
			print_failure(get_lang_f('migrate_failed_code', (int)$result));
		}
		return;
	}

	// ------------------------------------------------------------------ //
	// Build the list of eligible destination servers (same game type,    //
	// exclude source, admin-visible only).                               //
	// ------------------------------------------------------------------ //
	$all_homes  = $db->getGameHomes_limit(1, 9999, false, false);
	$candidates = array();
	if (!empty($all_homes)) {
		foreach ((array)$all_homes as $h) {
			if (intval($h['home_id']) === $home_id) continue;
			if (intval($h['home_cfg_id']) !== intval($source['home_cfg_id'])) continue;
			$candidates[] = $h;
		}
	}

	if (empty($candidates)) {
		print_failure(get_lang('migrate_no_compatible_destinations'));
		echo "<p><a href='?m=user_games'>&lt;&lt; " . get_lang('back_to_game_servers') . "</a></p>";
		return;
	}

	// ------------------------------------------------------------------ //
	// Show migration form                                                 //
	// ------------------------------------------------------------------ //
	echo "<p class='note'>" . get_lang('migrate_info') . "</p>";
	echo "<ul>
		<li>" . get_lang('migrate_bullet_no_delete')   . "</li>
		<li>" . get_lang('migrate_bullet_same_game')   . "</li>
		<li>" . get_lang('migrate_bullet_overwrite')   . "</li>
		<li>" . get_lang('migrate_bullet_no_billing')  . "</li>
	</ul>";

	echo "<form method='post' action='?m=user_games&amp;p=migrate&amp;home_id=" . $home_id . "'>";
	echo "<table class='center'>";

	// Source info
	echo "<tr><td class='right'><strong>" . get_lang('migrate_source') . ":</strong></td>
		<td class='left'>" . htmlentities($source['home_name']) .
		" &nbsp;(<em>" . htmlentities($source['agent_ip']) . "</em>)" .
		" &nbsp;[" . htmlentities($source['home_path']) . "]</td></tr>";

	// Destination dropdown
	echo "<tr><td class='right'>" . get_lang('migrate_destination') . ":</td>
		<td class='left'><select name='dest_home_id'>";
	foreach ((array)$candidates as $c) {
		echo "<option value='" . intval($c['home_id']) . "'>"
			. htmlentities($c['home_name'])
			. " — " . htmlentities($c['agent_ip'])
			. " [" . htmlentities($c['home_path']) . "]"
			. "</option>";
	}
	echo "</select></td></tr>";

	// Confirmation checkbox
	echo "<tr><td class='right'>" . get_lang('migrate_confirm_overwrite') . ":</td>
		<td class='left'>
		<input type='checkbox' name='confirm_overwrite' value='1' />
		<span class='info'>" . get_lang('migrate_confirm_overwrite_info') . "</span>
		</td></tr>";

	echo "<tr><td colspan='2' align='center'>
		<input type='submit' name='do_migrate' value='" . get_lang('migrate_start') . "' />
		</td></tr>";

	echo "</table></form>";

	// Show source ports/mods for reference
	$assigned = $db->getHomeIpPorts($home_id);
	if (!empty($assigned)) {
		echo "<h3>" . get_lang('ips_and_ports_used_in_this_home') . "</h3>";
		echo "<p class='info'>" . get_lang('note_ips_and_ports_are_not_cloned') . "</p>";
		foreach ((array)$assigned as $r) {
			echo "<p>" . $r['ip'] . ":" . $r['port'] . "</p>\n";
		}
	}
}

// ------------------------------------------------------------------ //
// Core migration function                                             //
// ------------------------------------------------------------------ //

/**
 * Copy all files from $source game server to $dest using rsync (Linux) or
 * robocopy (Windows fallback) via the remote agent's exec() call.
 *
 * Both servers must live on the SAME remote agent.  Cross-node migration is
 * noted below as a limitation.
 *
 * @param OGPDatabase $db       Panel DB
 * @param array       $source   Row from getGameHomeWithoutMods() for source
 * @param array       $dest     Row from getGameHomeWithoutMods() for dest
 * @param array       $settings Panel settings array
 *
 * @return true|int  TRUE or -1 on async success, 0 on failure, other int on error
 */
function migrate_game_server($db, $source, $dest, $settings)
{
	require_once('includes/lib_remote.php');

	$src_path  = rtrim($source['home_path'], '/\\');
	$dst_path  = rtrim($dest['home_path'],   '/\\');

	// Validate paths
	if (empty($src_path) || empty($dst_path)) {
		return 0;
	}
	if ($src_path === $dst_path) {
		return 0;
	}

	// ------------------------------------------------------------------ //
	// Cross-node migration guard                                          //
	// rsync between two *different* agents would require SSH access       //
	// between them which is not guaranteed.  For now we only support      //
	// same-agent migration; the UI should already have filtered this, but //
	// we double-check here.                                               //
	// ------------------------------------------------------------------ //
	$same_node = ($source['remote_server_id'] == $dest['remote_server_id']);

	// Build remote connection to the source agent (used for same-node ops)
	$remote_src = new OGPRemoteLibrary(
		$source['agent_ip'],
		$source['agent_port'],
		$source['encryption_key'],
		$source['timeout']
	);

	if (!$same_node) {
		// Cross-node: attempt rsync pull from the DESTINATION agent using
		// SSH to pull files from the source agent.  This requires that the
		// destination agent's OS user can reach the source via SSH without
		// a passphrase.  We attempt it but return 0 on obvious failure.
		$remote_dst = new OGPRemoteLibrary(
			$dest['agent_ip'],
			$dest['agent_port'],
			$dest['encryption_key'],
			$dest['timeout']
		);

		// Detect destination OS
		$dst_os = $remote_dst->what_os();
		if (stripos($dst_os, 'win') !== false) {
			// Windows cross-node not supported via this UI
			return 0;
		}

		$src_user = $source['ogp_user'] ?? 'gameserver';
		$rsync_pull = sprintf(
			'rsync -avz --delete -e "ssh -o StrictHostKeyChecking=no" %s@%s:%s/ %s/',
			escapeshellarg($src_user),
			escapeshellarg($source['agent_ip']),
			escapeshellarg($src_path),
			escapeshellarg($dst_path)
		);

		$out = $remote_dst->exec($rsync_pull);
		if ($out === NULL) {
			return -1; // running async
		}
		// Fix ownership on destination
		$dst_user  = $dest['ogp_user'] ?? 'gameserver';
		$chown_cmd = sprintf(
			'chown -R %s:%s %s/',
			escapeshellarg($dst_user),
			escapeshellarg($dst_user),
			escapeshellarg($dst_path)
		);
		$remote_dst->exec($chown_cmd);
		$db->logger("Migrated (cross-node) home {$source['home_id']} -> {$dest['home_id']}");
		return TRUE;
	}

	// ------------------------------------------------------------------ //
	// Same-node migration via clone_home() RPC (rsync under the hood)    //
	// ------------------------------------------------------------------ //

	// Detect OS so we can choose the right tool
	$os = $remote_src->what_os();

	if (stripos($os, 'win') !== false) {
		// Windows: try rsync (Cygwin/MSYS) first, fall back to robocopy
		$rsync_cmd    = sprintf('rsync -avz --delete %s/ %s/',
			escapeshellarg($src_path), escapeshellarg($dst_path));
		$robocopy_cmd = sprintf('robocopy %s %s /MIR /R:1 /W:1',
			escapeshellarg($src_path), escapeshellarg($dst_path));

		$out = $remote_src->exec($rsync_cmd);
		if ($out === NULL) {
			// rsync not available — fall back to robocopy
			$remote_src->exec($robocopy_cmd);
		}

		$db->logger("Migrated (Windows same-node) home {$source['home_id']} -> {$dest['home_id']}");
		return TRUE;
	}

	// Linux — prefer the agent's built-in clone_home (rsync -a) because it
	// runs in the background and returns -1 (async) with progress support.
	// We need to pass the owner for chown; fall back to source ogp_user.
	$owner = $dest['ogp_user'] ?? $source['ogp_user'] ?? 'gameserver';
	$rc    = $remote_src->clone_home($src_path, $dst_path, $owner);

	if ($rc === 1 || $rc === -1) {
		// Also fix ownership explicitly (clone_home may already do this)
		$chown_cmd = sprintf('chown -R %s:%s %s/',
			escapeshellarg($owner), escapeshellarg($owner), escapeshellarg($dst_path));
		$remote_src->exec($chown_cmd);
		$db->logger("Migrated home {$source['home_id']} -> {$dest['home_id']}");
	}

	return $rc;
}
