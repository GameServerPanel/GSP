<?php
/*
 *
 * GSP - Server Content Workshop page (Phase 1)
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

	$message = '';
	$is_error = false;
	$entered_ids = '';

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {
		$posted_home_id = isset($_POST['home_id']) ? (int)$_POST['home_id'] : 0;
		$csrf_token = isset($_POST['workshop_csrf']) ? (string)$_POST['workshop_csrf'] : '';
		$entered_ids = isset($_POST['workshop_ids']) ? (string)$_POST['workshop_ids'] : '';
		$selected_ids = isset($_POST['selected_ids']) ? $_POST['selected_ids'] : array();
		$action = isset($_POST['workshop_action']) ? (string)$_POST['workshop_action'] : '';

		if ($posted_home_id !== $home_id) {
			$is_error = true;
			$message = 'Invalid server context for workshop action.';
		}
		elseif (!scm_validate_csrf_token($csrf_token)) {
			$is_error = true;
			$message = 'Invalid CSRF token for workshop action.';
		}
		else {
			scm_workshop_handle_action($db, $home_info, $user_id, $action, $entered_ids, (array)$selected_ids, $message, $is_error);
		}
	}

	$rows = scm_get_workshop_rows($db, $home_id);
	$csrf_token = scm_get_csrf_token();

	echo "<h2>Workshop Content: ".scm_h($home_info['home_name'])."</h2>";
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
		<input type='hidden' name='workshop_csrf' value='<?php echo scm_h($csrf_token); ?>' />

		<table class='center'>
			<tr>
				<td align='right'><strong>Enter Workshop IDs</strong></td>
				<td align='left'>
					<input type='text' name='workshop_ids' size='72' value='<?php echo scm_h($entered_ids); ?>' placeholder='1234567890, 9876543210, 555555555' />
				</td>
				<td align='left'>
					<button type='submit' name='workshop_action' value='install_new'>Install New</button>
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

