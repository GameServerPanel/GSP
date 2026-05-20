<script type="text/javascript" src="js/modules/addonsmanager.js"></script>
<?php
/*
 *
 * GSP - Game Server Panel (a heavily customized fork of OGP maintained by WDS)
 *
 * Admin page: Server Content Manager (module: addonsmanager)
 * ─────────────────────────────────────────────────────────────────────────────
 * This page lets admins create, edit, and remove Server Content items.
 *
 * A "Server Content item" is anything that can be pushed to a game server:
 *   1. A zip/file package extracted into the server directory.
 *   2. A downloaded file placed into the server directory.
 *   3. A script-driven installer (post_script only, no download required).
 *   4. A Minecraft server jar / version switcher (future: install_method=minecraft_jar).
 *   5. A DayZ/Epoch/Arma profile copy (future: install_method=profile_copy).
 *   6. A Steam Workshop content bundle (future: install_method=steam_workshop).
 *   7. A config preset (type=config).
 *   8. A full server profile built from multiple actions (type=profile).
 *
 * DB table: OGP_DB_PREFIXaddons (unchanged for backward compatibility).
 * See SERVER_CONTENT_ROADMAP.md for the full migration plan.
 *
 */

// Central category map — defines all valid addon_type values and their labels.
require_once(dirname(__FILE__) . '/server_content_categories.php');
require_once(dirname(__FILE__) . '/server_content_helpers.php');

function exec_ogp_module() {

	global $db;

	// Ensure Phase 2 schema is present (idempotent).
	scm_ensure_phase2_schema($db);

	// Build the complete list of allowed content types from the category map.
	// Admins can create items of any registered type; the original three types
	// (plugin, mappack, config) are always included.
	$addon_types       = get_server_content_type_keys();        // all keys
	$addon_type_labels = get_server_content_categories();       // key => label
	$install_methods   = scm_get_install_methods();             // install_method keys => labels

	if (isset($_POST['create_addon']))
	{
		$valid_install_methods = array_keys($install_methods);
		$fields['name']                = isset($_POST['name']) ? trim((string)$_POST['name']) : '';
		$fields['url']                 = isset($_POST['url']) ? trim((string)$_POST['url']) : '';
		$fields['path']                = isset($_POST['path']) ? trim((string)$_POST['path']) : '';
		$fields['addon_type']          = '';
		$fields['home_cfg_id']         = isset($_POST['home_cfg_id']) ? (int)$_POST['home_cfg_id'] : 0;
		$fields['post_script']         = isset($_POST['post_script']) ? trim((string)$_POST['post_script']) : '';
		$fields['group_id']            = isset($_POST['group_id']) ? (int)$_POST['group_id'] : 0;
		$posted_install_method         = isset($_POST['install_method']) ? $_POST['install_method'] : '';
		$fields['install_method']      = in_array($posted_install_method, $valid_install_methods) ? $posted_install_method : 'download_zip';
		$fields['content_version']     = isset($_POST['content_version'])     ? $_POST['content_version']              : '';
		$fields['requires_stop']       = !empty($_POST['requires_stop'])       ? 1 : 0;
		$fields['backup_before_install'] = !empty($_POST['backup_before_install']) ? 1 : 0;
		$fields['restart_after_install'] = !empty($_POST['restart_after_install']) ? 1 : 0;
		$fields['is_cacheable']        = !empty($_POST['is_cacheable'])        ? 1 : 0;
		$fields['description']         = isset($_POST['description'])         ? $_POST['description']                  : '';
		$fields['workshop_item_id']    = isset($_POST['workshop_item_id']) ? trim((string)$_POST['workshop_item_id']) : '';
		$fields['workshop_app_id']     = isset($_POST['workshop_app_id']) ? trim((string)$_POST['workshop_app_id']) : '';
		$fields['target_path_template']= isset($_POST['target_path_template']) ? trim((string)$_POST['target_path_template']) : '';
		$fields['optional_folder_name']= isset($_POST['optional_folder_name']) ? trim((string)$_POST['optional_folder_name']) : '';
		$fields['config_edit_rule']    = isset($_POST['config_edit_rule']) ? trim((string)$_POST['config_edit_rule']) : '';
		$fields['launch_param_additions'] = isset($_POST['launch_param_additions']) ? trim((string)$_POST['launch_param_additions']) : '';
		$fields['addon_type']          = scm_get_addon_type_from_install_method($fields['install_method']);

		if ($fields['name'] === '')
		{
			print_failure(get_lang("fill_the_addon_name"));
		}
		elseif (empty($fields['home_cfg_id']))
		{
			print_failure(get_lang("select_a_game_type"));
		}
		else
		{
			$validation_payload = array(
				'url' => $fields['url'],
				'path' => $fields['path'],
				'workshop_item_id' => $fields['workshop_item_id'],
				'target_path_template' => $fields['target_path_template'],
				'post_script' => $fields['post_script'],
				'config_edit_rule' => $fields['config_edit_rule'],
			);
			$validation_message = '';
			if (!scm_validate_install_method_payload($fields['install_method'], $validation_payload, $validation_message))
			{
				print_failure($validation_message);
			}
			elseif (is_numeric($db->resultInsertId('addons', $fields)))
			{
				print_success(get_lang_f("addon_has_been_created", $fields['name']));
				if (isset($_POST['addon_id']) && (int)$_POST['addon_id'] > 0 && isset($_POST['edit']))
					$db->query("DELETE FROM OGP_DB_PREFIXaddons WHERE addon_id=" . (int)$_POST['addon_id']);
			}
		}
	}

	echo "<h2>".get_lang('addons_manager')."</h2>";
	$name                  = isset($_POST['name'])                  ? $_POST['name']                  : "";
	$url                   = isset($_POST['url'])                   ? $_POST['url']                   : "";
	$path                  = isset($_POST['path'])                  ? $_POST['path']                  : "";
	$post_script           = isset($_POST['post_script'])           ? $_POST['post_script']           : "";
	$home_cfg_id           = isset($_POST['home_cfg_id'])           ? $_POST['home_cfg_id']           : "";
	$addon_type            = isset($_POST['addon_type'])            ? $_POST['addon_type']            : "";
	$group_id              = isset($_POST['group_id'])              ? $_POST['group_id']              : "";
	$install_method        = isset($_POST['install_method'])        ? $_POST['install_method']        : "download_zip";
	$content_version       = isset($_POST['content_version'])       ? $_POST['content_version']       : "";
	$requires_stop         = isset($_POST['requires_stop'])         ? (int)$_POST['requires_stop']    : 1;
	$backup_before_install = isset($_POST['backup_before_install']) ? (int)$_POST['backup_before_install'] : 1;
	$restart_after_install = isset($_POST['restart_after_install']) ? (int)$_POST['restart_after_install'] : 0;
	$is_cacheable          = isset($_POST['is_cacheable'])          ? (int)$_POST['is_cacheable']     : 0;
	$description           = isset($_POST['description'])           ? $_POST['description']           : "";
	$workshop_item_id      = isset($_POST['workshop_item_id'])      ? $_POST['workshop_item_id']      : "";
	$workshop_app_id       = isset($_POST['workshop_app_id'])       ? $_POST['workshop_app_id']       : "";
	$target_path_template  = isset($_POST['target_path_template'])  ? $_POST['target_path_template']  : "";
	$optional_folder_name  = isset($_POST['optional_folder_name'])  ? $_POST['optional_folder_name']  : "";
	$config_edit_rule      = isset($_POST['config_edit_rule'])      ? $_POST['config_edit_rule']      : "";
	$launch_param_additions = isset($_POST['launch_param_additions']) ? $_POST['launch_param_additions'] : "";

	if (isset($_POST['addon_id']) && (int)$_POST['addon_id'] > 0 && isset($_POST['edit']))
	{
		$addons_rows = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXaddons WHERE addon_id=".(int)$_POST['addon_id']);
		if (!is_array($addons_rows)) {
			$addons_rows = [];
		}
		$addon_info            = $addons_rows[0];
		$name                  = isset($addon_info['name'])                  ? $addon_info['name']                  : "";
		$url                   = isset($addon_info['url'])                   ? $addon_info['url']                   : "";
		$path                  = isset($addon_info['path'])                  ? $addon_info['path']                  : "";
		$post_script           = isset($addon_info['post_script'])           ? $addon_info['post_script']           : "";
		$home_cfg_id           = isset($addon_info['home_cfg_id'])           ? $addon_info['home_cfg_id']           : "";
		$addon_type            = scm_normalize_addon_type(isset($addon_info['addon_type']) ? $addon_info['addon_type'] : "", $install_method);
		$group_id              = isset($addon_info['group_id'])              ? $addon_info['group_id']              : "";
		$install_method        = isset($addon_info['install_method'])        ? $addon_info['install_method']        : "download_zip";
		$content_version       = isset($addon_info['content_version'])       ? $addon_info['content_version']       : "";
		$requires_stop         = isset($addon_info['requires_stop'])         ? (int)$addon_info['requires_stop']    : 1;
		$backup_before_install = isset($addon_info['backup_before_install']) ? (int)$addon_info['backup_before_install'] : 1;
		$restart_after_install = isset($addon_info['restart_after_install']) ? (int)$addon_info['restart_after_install'] : 0;
		$is_cacheable          = isset($addon_info['is_cacheable'])          ? (int)$addon_info['is_cacheable']     : 0;
		$description           = isset($addon_info['description'])           ? $addon_info['description']           : "";
		$workshop_item_id      = isset($addon_info['workshop_item_id'])      ? $addon_info['workshop_item_id']      : "";
		$workshop_app_id       = isset($addon_info['workshop_app_id'])       ? $addon_info['workshop_app_id']       : "";
		$target_path_template  = isset($addon_info['target_path_template'])  ? $addon_info['target_path_template']  : "";
		$optional_folder_name  = isset($addon_info['optional_folder_name'])  ? $addon_info['optional_folder_name']  : "";
		$config_edit_rule      = isset($addon_info['config_edit_rule'])      ? $addon_info['config_edit_rule']      : "";
		$launch_param_additions = isset($addon_info['launch_param_additions']) ? $addon_info['launch_param_additions'] : "";
	}
	?>
	<form action="" method="post">
		<table class="center">
			<tr>				
				<td align="right">
					<b><?php print_lang('addon_name'); ?></b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo $name; ?>" name="name" size="85" title="<?php print_lang('addon_name_info'); ?>" />
				</td>
			</tr>
			<tr id="scm-row-install-method">
				<td align="right">
					<b><?php print_lang('content_type'); ?></b>
				</td>
				<td align="left">
					<select name="install_method" id="scm-install-method">
					<?php
					$install_help = scm_get_install_method_help_text();
					foreach ((array)$install_methods as $method_key => $method_label) {
						$sel = ($method_key == $install_method) ? 'selected="selected"' : '';
						$help = isset($install_help[$method_key]) ? $install_help[$method_key] : '';
						echo '<option value="'.htmlspecialchars($method_key).'" data-help="'.htmlspecialchars($help, ENT_QUOTES, 'UTF-8').'" '.$sel.'>'.htmlspecialchars($method_label).'</option>'."\n";
					}
					?>
					</select>
					<div id="scm-install-method-help" style="color:#666;margin-top:4px;"></div>
				</td>
			</tr>
			<tr id="scm-row-url">					
				<td align="right">
					<b><?php print_lang('url'); ?></b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo $url; ?>" name="url" size="85" title="<?php print_lang('url_info'); ?>" />
				</td>
			</tr>
			<!-- Destination path — must be relative to the game server home directory.
			     Path traversal (../) is not allowed; the agent enforces this. -->
			<tr id="scm-row-path">					
				<td align="right">
					<b id="scm-path-label"><?php print_lang('path'); ?></b>
					</td>
					<td align="left">
					<input type="text" value="<?php echo $path; ?>" name="path" size="85" title="<?php print_lang('path_info'); ?>" />
				</td>
			</tr>
			<tr id="scm-row-workshop-id">
				<td align="right">
					<b>Default Workshop IDs (Optional)</b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo htmlspecialchars($workshop_item_id, ENT_QUOTES, 'UTF-8'); ?>" name="workshop_item_id" size="85" placeholder="Leave blank – users enter Workshop IDs on their server page" />
					<small style="color:#666;">Optional. Users enter the actual Workshop IDs they want installed from their own server page. This field is not required.</small>
				</td>
			</tr>
			<tr id="scm-row-workshop-app-id">
				<td align="right">
					<b>Game Compatibility (Workshop App ID)</b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo htmlspecialchars($workshop_app_id, ENT_QUOTES, 'UTF-8'); ?>" name="workshop_app_id" size="85" placeholder="Optional App ID override, e.g. 221100" />
				</td>
			</tr>
			<tr id="scm-row-target-path-template">
				<td align="right">
					<b><?php print_lang('target_path_template'); ?></b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo htmlspecialchars($target_path_template, ENT_QUOTES, 'UTF-8'); ?>" name="target_path_template" size="85" placeholder="{SERVER_ROOT}/{MOD_FOLDER}" />
					<small style="color:#666;">Supported placeholders: {HOME_ID}, {SERVER_ROOT}, {GAME_ROOT}, {WORKSHOP_ID}, {WORKSHOP_APP_ID}, {STEAM_APP_ID}, {FOLDER_NAME}, {MOD_FOLDER}</small>
				</td>
			</tr>
			<tr id="scm-row-optional-folder-name">
				<td align="right">
					<b><?php print_lang('optional_folder_name'); ?></b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo htmlspecialchars($optional_folder_name, ENT_QUOTES, 'UTF-8'); ?>" name="optional_folder_name" size="85" placeholder="@MyWorkshopMod" />
				</td>
			</tr>
			<tr id="scm-row-post-script">					
				<td align="right">
					<b>Post-Install Script / Action</b><br>
					<u><?php print_lang('replacements'); ?></u><br>
					%home_path%<br>
					%home_name%<br>
					%control_password%<br>
					%max_players%<br>
					%ip%<br>
					%port%<br>
					%query_port%<br>
					%incremental%<br>
				</td>
				<td align="left">
					<textarea name="post_script" style="width:99%;height:175px;" title="<?php print_lang('post-script_info'); ?>" ><?php echo strip_real_escape_string($post_script); ?></textarea>
				</td>
			</tr>
			<tr id="scm-row-config-edit-rule">
				<td align="right">
					<b><?php print_lang('config_edit_rule'); ?></b>
				</td>
				<td align="left">
					<textarea name="config_edit_rule" style="width:99%;height:90px;" placeholder="Text/rules to append or apply to the target config."><?php echo htmlspecialchars($config_edit_rule, ENT_QUOTES, 'UTF-8'); ?></textarea>
				</td>
			</tr>
			<tr id="scm-row-launch-param-additions">
				<td align="right">
					<b><?php print_lang('launch_param_additions'); ?></b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo htmlspecialchars($launch_param_additions, ENT_QUOTES, 'UTF-8'); ?>" name="launch_param_additions" size="85" placeholder="-mod=@myMod;@anotherMod" />
				</td>
			</tr>
			<tr>
				<td align="right">
					<b><?php print_lang('select_game_type'); ?></b>
				</td>
				<td align="left">
				<select name='home_cfg_id'>
		<?php
		$game_cfgs = $db->getGameCfgs();
		if (!is_array($game_cfgs)) {
			$game_cfgs = [];
		}
		echo "<option style='background:black;color:white;' value=''>".get_lang('linux_games')."</option>\n";
		
		foreach ((array)$game_cfgs as $row)
		{
			if ( preg_match("/linux/", $row['game_key']) )
			{
				$selected = (isset($home_cfg_id) AND $row['home_cfg_id'] == $home_cfg_id) ? 'selected="selected"' : '';
				echo "<option $selected value='".$row['home_cfg_id']."'>".$row['game_name'];
				if  ( preg_match("/64/", $row['game_key']) ) echo " (64bit)";
				echo "</option>\n";
			}
		}
		echo "<option style='background:black;color:white;' value=''>".get_lang('windows_games')."</option>\n";
		foreach ((array)$game_cfgs as $row)
		{
			if ( preg_match("/win/", $row['game_key']) )
			{
				$selected = (isset($home_cfg_id) AND $row['home_cfg_id'] == $home_cfg_id) ? 'selected=selected' : '';
				echo "<option $selected value='".$row['home_cfg_id']."'>".$row['game_name'];
				if  ( preg_match("/64/", $row['game_key']) ) echo " (64bit)";
				echo "</option>\n";
			}
		}
		?>
				</select>
				</td>
			</tr>
			<tr>
				<td align="right">
					<b><?php print_lang('show_to_group'); ?></b>
				</td>
				<td align="left">
				<select name='group_id'>
				<option value="0"><?php print_lang('all_groups'); ?></option>
		<?php
		$groups = $db->getGroupList();
		if (!is_array($groups)) {
			$groups = [];
		}
		foreach ((array)$groups as $group)
		{
			$selected = (isset($group_id) AND $group['group_id'] == $group_id) ? 'selected=selected' : '';
			echo "<option value='".$group['group_id']."' $selected>".$group['group_name']."</option>\n";
		}
		?>
				</select>
				</td>
			</tr>
			<tr>
				<td align="right">
					<b>Content Version</b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo htmlspecialchars($content_version, ENT_QUOTES, 'UTF-8'); ?>" name="content_version" size="40" placeholder="e.g. 1.21.1 or 2024-05-01" />
					<small style="color:#666;"> Optional version tag shown in the installed-content list.</small>
				</td>
			</tr>
			<tr>
				<td align="right">
					<b>Description</b>
				</td>
				<td align="left">
					<textarea name="description" style="width:99%;height:60px;" placeholder="Short description shown to users."><?php echo htmlspecialchars($description, ENT_QUOTES, 'UTF-8'); ?></textarea>
				</td>
			</tr>
			<tr>
				<td align="right">
					<b>Behaviour Options</b>
				</td>
				<td align="left">
					<label>
						<input type="checkbox" name="requires_stop" value="1" <?php echo $requires_stop       ? 'checked' : ''; ?> />
						Stop server before installing
					</label>
					&nbsp;&nbsp;
					<label>
						<input type="checkbox" name="backup_before_install" value="1" <?php echo $backup_before_install ? 'checked' : ''; ?> />
						Backup target path before installing
					</label>
					&nbsp;&nbsp;
					<label>
						<input type="checkbox" name="restart_after_install" value="1" <?php echo $restart_after_install ? 'checked' : ''; ?> />
						Restart server after successful install
					</label>
				</td>
			</tr>
			<tr>
				<td align="right">
					<b>Content Reuse</b>
				</td>
				<td align="left">
					<label>
						<input type="checkbox" name="is_cacheable" value="1" <?php echo $is_cacheable ? 'checked' : ''; ?> />
						Mark as cacheable / reusable
					</label>
					<small style="color:#666;">
						Only check this for public, non-sensitive content (maps, mods, jars).
						<strong>Never</strong> check for configs, saves, credentials, or user-edited files.
						Caching only activates when the <em>Server Content Cache Mode</em> panel
						setting (in Panel Settings) is set to something other than <em>Disabled</em>.
					</small>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center">
				<?php 
				if (isset($_POST['addon_id']) && isset($_POST['edit']))
				{
					echo '<input type="hidden" name="addon_id" value="'.$_POST['addon_id'].'" >';
					echo '<input type="hidden" name="edit" value="'.$_POST['edit'].'" >';
					?>
					<button name="create_addon" type="submit">
					<?php print_lang('edit_addon'); ?>
					</button>
				<?php
				}
				else
				{
				?>
					<button name="create_addon" type="submit">
					<?php print_lang('create_addon'); ?>
					</button>
				<?php
				}
				?>
				</td>
			</tr>
		</table>
	</form>
	<br>
	<h2><?php print_lang('addons_db'); ?></h2>
	<table class="center">
		<tr>	
			<td align="center">
				<form name="remove" action="" method="get">
				<input name="m" type="hidden" value="addonsmanager"/>
				<input name="p" type="hidden" value="addons_manager"/>
				<b><?php print_lang('game'); ?></b> <select name='home_cfg_id'>
				<?php
				echo "<option style='background:black;color:white;' value=''>".get_lang('linux_games')."</option>\n";
				foreach ((array)$game_cfgs as $row)
				{
					if ( preg_match("/linux/", $row['game_key']) )
					{
						if(isset($_GET['home_cfg_id']) AND $row['home_cfg_id'] == $_GET['home_cfg_id']) 
							$selected = "selected='selected'";
						else
							$selected = "";
						echo "<option value='".$row['home_cfg_id']."' $selected >".$row['game_name'];
						if  ( preg_match("/64/", $row['game_key']) ) echo " (64bit)";
						echo "</option>\n";
					}
				}
				echo "<option style='background:black;color:white;' value=''>".get_lang('windows_games')."</option>\n";
				foreach ((array)$game_cfgs as $row)
				{
						if(isset($_GET['home_cfg_id']) AND $row['home_cfg_id'] == $_GET['home_cfg_id']) 
							$selected = "selected='selected'";
						else
							$selected = "";
					if ( preg_match("/win/", $row['game_key']) )
					{
						echo "<option value='".$row['home_cfg_id']."' $selected >".$row['game_name'];
						if  ( preg_match("/64/", $row['game_key']) ) echo " (64bit)";
						echo "</option>\n";
					}
				}
				
				?>
				</select>
				<b><?php print_lang('type'); ?></b>
				<select name="addon_type">
				<?php
					$option = '';

					foreach ((array)$addon_type_labels as $k => $label) {
						$option .= '<option';

						if (isset($_GET['addon_type']) && $_GET['addon_type'] == $k) {
							$option .= ' selected';
						}

						$option .= ' value="'. htmlspecialchars($k) .'">'.htmlspecialchars($label).'</option>';
					}

					echo $option;
				?>
		
				</select>
				<b><?php print_lang('group'); ?></b>
				<select name='group_id'>
				<option value="0"><?php print_lang('all_groups'); ?></option>
				<?php
				foreach ((array)$groups as $group)
				{
					$selected = (isset($_GET['group_id']) AND $group['group_id'] == $_GET['group_id']) ? 'selected=selected' : '';
					echo "<option value='".$group['group_id']."' $selected>".$group['group_name']."</option>\n";
				}
				?>
				</select>	
				<button name="show" type="submit">
				<?php print_lang('show'); ?>
				</button>
			</td>
		</tr>
		<tr>
			<td>
				<input name="show_game" type="submit" value="<?php print_lang('show_addons_for_selected_game'); ?>"/>
				<input name="show_type" type="submit" value="<?php print_lang('show_addons_for_selected_type'); ?>"/>
				<input name="show_group" type="submit" value="<?php print_lang('show_addons_for_selected_group'); ?>"/>
				<input name="show_all" type="submit" value="<?php print_lang('show_all_addons'); ?>"/>
			</td>
		</tr>
	</form>
	</table>
	<?php 
	if (isset($_POST['addon_id']) && (int)$_POST['addon_id'] > 0 && isset($_POST['remove']))
	{
		if (!$db->query("DELETE FROM OGP_DB_PREFIXaddons WHERE addon_id=" . (int)$_POST['addon_id']))
			print_lang('can_not_remove_addon');
	}
	
	$home_cfg_id = !empty($_GET['home_cfg_id']) && (int)$_GET['home_cfg_id'] > 0 ? (int)$_GET['home_cfg_id'] : 0;
	$addon_type  = !empty($_GET['addon_type']) && in_array($_GET['addon_type'], $addon_types) ? $_GET['addon_type'] : "";
	$group_id    = isset($_GET['group_id']) && is_numeric($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
	
	if ( isset($_GET['show']) )
	{
		$result = $db->resultQuery("SELECT DISTINCT addon_id, name, game_name, url, path, group_id FROM OGP_DB_PREFIXaddons NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE addon_type='".$addon_type."' AND home_cfg_id=".$home_cfg_id);
	}
	elseif ( isset($_GET['show_all']) )
	{
		$result = $db->resultQuery("SELECT DISTINCT addon_id, name, game_name, url, path, group_id FROM OGP_DB_PREFIXaddons NATURAL JOIN OGP_DB_PREFIXconfig_homes");
	}
	elseif ( isset($_GET['show_type']))
	{
		$result = $db->resultQuery("SELECT DISTINCT addon_id, name, game_name, url, path, group_id FROM OGP_DB_PREFIXaddons NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE addon_type='".$addon_type."'");
	}
	elseif ( isset($_GET['show_game']))
	{
		$result = $db->resultQuery("SELECT DISTINCT addon_id, name, game_name, url, path, group_id FROM OGP_DB_PREFIXaddons NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE home_cfg_id=".$home_cfg_id);
	}
	elseif ( isset($_GET['show_group']))
	{
		$group_id = $group_id == '0' ? $group_id." OR group_id IS NULL" : $group_id;
		$result = $db->resultQuery("SELECT DISTINCT addon_id, name, game_name, url, path, group_id FROM OGP_DB_PREFIXaddons NATURAL JOIN OGP_DB_PREFIXconfig_homes WHERE group_id=".$group_id);
	}
	if (isset($result) && !is_array($result)) {
		$result = [];
	}
	?>	
	<table class="center">
	<?php
	$group_names = array();
	foreach ((array)$groups as $group)
		$group_names[$group['group_id']] = $group['group_name'];
	
	if (isset($result) and is_array($result) and (is_array($result) ? count((array)$result) : 0) > 0)
	{
		foreach ((array)$result as $row)
		{
		?>
		<tr>
		<form action="" method="post">
		 <td class='left'>
		  <b><?php echo $row['game_name']; ?></b>
		 </td>
		 <td>
		  <?php echo $row['name'];?>
		 </td>
		 <td>
		  <?php echo "[".get_lang('group').": ". (isset($group_names[$row['group_id']])?$group_names[$row['group_id']]:get_lang('all_groups')) ."]";?>
		 </td>
		 <td>
		  <input name="addon_id" type="hidden" value="<?php echo $row['addon_id'];?>"/>
		  <input name="edit" type="submit" value="<?php print_lang('edit_addon'); ?>"/>
		  <input name="remove" type="submit" value="<?php print_lang('remove_addon'); ?>"/>
		 </td>
		</form>
		</tr>
		<?php
		}
	}
	?>
	</table>
	<?php
}
?>
