<?php
/*
 *
 * GSP - Server Content Workshop page
 *
 * Users enter Steam Workshop IDs to install on their server.
 * The admin defines the content template (game, app ID, install path).
 *
 */

require_once(dirname(__FILE__) . '/server_content_helpers.php');
require_once(dirname(__FILE__) . '/workshop_action.php');

function exec_ogp_module() {
	global $db;

	$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
	$home_id = isset($_REQUEST['home_id']) ? (int)$_REQUEST['home_id'] : 0;
	$mod_id  = isset($_REQUEST['mod_id']) ? (int)$_REQUEST['mod_id'] : 0;
	$ip      = isset($_REQUEST['ip']) ? (string)$_REQUEST['ip'] : '';
	$port    = isset($_REQUEST['port']) ? (string)$_REQUEST['port'] : '';
	$addon_id = isset($_REQUEST['addon_id']) ? (int)$_REQUEST['addon_id'] : 0;

	if ($home_id <= 0 || $user_id <= 0) {
		print_failure(get_lang('no_rights'));
		echo create_back_button("addonsmanager","user_addons");
		return;
	}

	$home_info = scm_get_home_for_user($db, $home_id, $user_id);
	if ($home_info === false) {
		print_failure(get_lang('no_rights'));
		echo create_back_button("addonsmanager","user_addons");
		return;
	}

	if (!scm_ensure_workshop_schema($db)) {
		print_failure('Failed to initialize Workshop Content storage.');
		return;
	}

	// Load the admin content template if an addon_id was provided.
	$addon_template = null;
	if ($addon_id > 0) {
		$template_rows = $db->resultQuery(
			"SELECT addon_id, name, workshop_app_id, target_path_template, optional_folder_name, description
			   FROM `" . OGP_DB_PREFIX . "addons`
			  WHERE addon_id=" . $addon_id . " AND install_method='steam_workshop'"
		);
		if (is_array($template_rows) && !empty($template_rows)) {
			$addon_template = $template_rows[0];
		}
	}

	$message = '';
	$is_error = false;
	$entered_ids = '';

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$posted_home_id = isset($_POST['home_id']) ? (int)$_POST['home_id'] : 0;
		$csrf_token = isset($_POST['workshop_csrf']) ? (string)$_POST['workshop_csrf'] : '';
		$entered_ids = isset($_POST['workshop_ids']) ? (string)$_POST['workshop_ids'] : '';
		$selected_ids = isset($_POST['selected_ids']) ? $_POST['selected_ids'] : array();
		$action = isset($_POST['workshop_action']) ? (string)$_POST['workshop_action'] : '';
		$posted_addon_id = isset($_POST['addon_id']) ? (int)$_POST['addon_id'] : 0;

		if ($posted_home_id !== $home_id) {
			$is_error = true;
			$message = 'Invalid server context for workshop action.';
		}
		elseif (!scm_validate_csrf_token($csrf_token)) {
			$is_error = true;
			$message = 'Invalid CSRF token for workshop action.';
		}
		else {
			scm_workshop_handle_action($db, $home_info, $user_id, $action, $entered_ids, (array)$selected_ids, $message, $is_error, $posted_addon_id > 0 ? $posted_addon_id : $addon_id);
		}
	}

	$rows = scm_get_workshop_rows($db, $home_id);
	$csrf_token = scm_get_csrf_token();

	echo "<h2>Workshop Mods: " . scm_h($home_info['home_name']) . "</h2>";
	if ($addon_template !== null) {
		echo "<p class='info'>Content template: <strong>" . scm_h($addon_template['name']) . "</strong>";
		if (!empty($addon_template['description'])) {
			echo " – " . scm_h($addon_template['description']);
		}
		echo "</p>";
	}

	if ($message !== '') {
		if ($is_error) {
			print_failure($message);
		} else {
			print_success($message);
		}
	}
	?>
	<table class='center'>
		<tr><td align='right'><strong>Server Name:</strong></td><td align='left'><?php echo scm_h($home_info['home_name']); ?></td></tr>
		<tr><td align='right'><strong>Game Name:</strong></td><td align='left'><?php echo scm_h($home_info['game_name']); ?></td></tr>
	</table>

	<form method='post' action=''>
		<input type='hidden' name='m' value='addonsmanager' />
		<input type='hidden' name='p' value='workshop_content' />
		<input type='hidden' name='home_id' value='<?php echo (int)$home_id; ?>' />
		<input type='hidden' name='mod_id' value='<?php echo (int)$mod_id; ?>' />
		<input type='hidden' name='ip' value='<?php echo scm_h($ip); ?>' />
		<input type='hidden' name='port' value='<?php echo scm_h($port); ?>' />
		<input type='hidden' name='addon_id' value='<?php echo (int)$addon_id; ?>' />
		<input type='hidden' name='workshop_csrf' value='<?php echo scm_h($csrf_token); ?>' />

		<table class='center'>
			<tr>
				<td align='right'><strong>Workshop Item IDs</strong></td>
				<td align='left'>
					<textarea name='workshop_ids' rows='4' cols='72' placeholder='450814997&#10;463939057&#10;...'><?php echo scm_h($entered_ids); ?></textarea>
					<br><small style="color:#666;">Enter one or more Steam Workshop IDs, one per line or comma-separated.<br>Example for Arma 3 CBA_A3: <code>450814997</code></small>
				</td>
				<td align='left' style='vertical-align:top;padding-top:4px;'>
					<button type='submit' name='workshop_action' value='install_new'>Install / Queue</button>
				</td>
			</tr>
		</table>

		<br>
		<table class='center'>
			<tr>
				<th></th>
				<th>Workshop ID</th>
				<th>Title</th>
				<th>State</th>
				<th>Last Installed</th>
				<th>Last Updated</th>
				<th>Last Error</th>
			</tr>
			<?php if (empty($rows)): ?>
				<tr><td colspan='7' class='info'>No Workshop IDs saved for this server yet.</td></tr>
			<?php else: ?>
				<?php foreach ((array)$rows as $row): ?>
					<tr>
						<td><input type='checkbox' name='selected_ids[]' value='<?php echo scm_h($row['workshop_item_id']); ?>'></td>
						<td><?php echo scm_h($row['workshop_item_id']); ?></td>
						<td><?php echo scm_h($row['title']); ?></td>
						<td><?php echo scm_h($row['install_state']); ?></td>
						<td><?php echo scm_h($row['last_installed_at']); ?></td>
						<td><?php echo scm_h($row['last_updated_at']); ?></td>
						<td><?php echo scm_h($row['last_error']); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php endif; ?>
		</table>
		<br>
		<table class='center'>
			<tr>
				<td><button type='submit' name='workshop_action' value='update_selected'>Update Selected</button></td>
				<td><button type='submit' name='workshop_action' value='remove_selected'>Remove Selected</button></td>
				<td><button type='submit' name='workshop_action' value='update_all'>Update All</button></td>
			</tr>
		</table>
	</form>

	<form method='get' action=''>
		<input type='hidden' name='m' value='addonsmanager' />
		<input type='hidden' name='p' value='user_addons' />
		<input type='hidden' name='home_id' value='<?php echo (int)$home_id; ?>' />
		<input type='hidden' name='mod_id' value='<?php echo (int)$mod_id; ?>' />
		<input type='hidden' name='ip' value='<?php echo scm_h($ip); ?>' />
		<input type='hidden' name='port' value='<?php echo scm_h($port); ?>' />
		<input type='submit' value='Back to Server Content' />
	</form>
	<?php
}

