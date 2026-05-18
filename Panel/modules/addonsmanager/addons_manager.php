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

function exec_ogp_module() {

	global $db;

	// Build the complete list of allowed content types from the category map.
	// Admins can create items of any registered type; the original three types
	// (plugin, mappack, config) are always included.
	$addon_types       = get_server_content_type_keys();        // all keys
	$addon_type_labels = get_server_content_categories();       // key => label

	if (isset($_POST['create_addon']) AND isset($_POST['name']) AND $_POST['url']=="")
	{
		print_failure(get_lang("fill_the_url_address_to_a_compressed_file"));
	}
	elseif(isset($_POST['create_addon']) AND isset($_POST['url']) AND $_POST['name']=="")
	{
		print_failure(get_lang("fill_the_addon_name"));
	}	
	elseif(isset($_POST['create_addon']) AND isset($_POST['name']) and isset($_POST['url']) and empty($_POST['addon_type']) )	
	{	
		print_failure(get_lang("select_an_addon_type"));
	}
	elseif(isset($_POST['create_addon']) AND isset($_POST['name']) and isset($_POST['url']) and isset($_POST['addon_type']) and empty($_POST['home_cfg_id']) )	
	{	
		print_failure(get_lang("select_a_game_type"));
	}
	elseif (isset($_POST['create_addon']) AND isset($_POST['name']) AND isset($_POST['url']) AND isset($_POST['addon_type']) and isset($_POST['home_cfg_id']) )
	{	
		$fields['name']        = $_POST['name'];
		$fields['url']         = $_POST['url'];
		$fields['path']        = $_POST['path'];
		$fields['addon_type']  = $_POST['addon_type'];
		$fields['home_cfg_id'] = $_POST['home_cfg_id'];
		$fields['post_script'] = $_POST['post_script'];
		$fields['group_id']    = $_POST['group_id'];
		if( is_numeric($db->resultInsertId( 'addons', $fields )) )
		{
			print_success(get_lang_f("addon_has_been_created",$_POST['name']));
			if (isset($_POST['addon_id']) && (int)$_POST['addon_id'] > 0 && isset($_POST['edit']))
				$db->query("DELETE FROM OGP_DB_PREFIXaddons WHERE addon_id=" . (int)$_POST['addon_id']);
		}
	}

	echo "<h2>".get_lang('addons_manager')."</h2>";
	$name        = isset($_POST['name'])        ? $_POST['name']        : "";
	$url         = isset($_POST['url'])         ? $_POST['url']         : "";
	$path        = isset($_POST['path'])        ? $_POST['path']        : "";
	$post_script = isset($_POST['post_script']) ? $_POST['post_script'] : "";
	$home_cfg_id = isset($_POST['home_cfg_id']) ? $_POST['home_cfg_id'] : "";
	$addon_type  = isset($_POST['addon_type'])  ? $_POST['addon_type']  : "";
	$group_id    = isset($_POST['group_id'])    ? $_POST['group_id']    : "";

	if (isset($_POST['addon_id']) && (int)$_POST['addon_id'] > 0 && isset($_POST['edit']))
	{
		$addons_rows = $db->resultQuery("SELECT * FROM OGP_DB_PREFIXaddons WHERE addon_id=".(int)$_POST['addon_id']);
		if (!is_array($addons_rows)) {
			$addons_rows = [];
		}
		$addon_info  = $addons_rows[0];
		$name        = isset($addon_info['name'])        ? $addon_info['name']        : "";
		$url         = isset($addon_info['url'])         ? $addon_info['url']         : "";
		$path        = isset($addon_info['path'])        ? $addon_info['path']        : "";
		$post_script = isset($addon_info['post_script']) ? $addon_info['post_script'] : "";
		$home_cfg_id = isset($addon_info['home_cfg_id']) ? $addon_info['home_cfg_id'] : "";
		$addon_type  = isset($addon_info['addon_type'])  ? $addon_info['addon_type']  : "";
		$group_id    = isset($addon_info['group_id'])    ? $addon_info['group_id']    : "";
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
			<tr>					
				<td align="right">
					<b><?php print_lang('url'); ?></b>
				</td>
				<td align="left">
					<input type="text" value="<?php echo $url; ?>" name="url" size="85" title="<?php print_lang('url_info'); ?>" />
				</td>
			</tr>
			<!-- Destination path — must be relative to the game server home directory.
			     Path traversal (../) is not allowed; the agent enforces this. -->
			<tr>					
				<td align="right">
					<b><?php print_lang('path'); ?></b>
					</td>
					<td align="left">
					<input type="text" value="<?php echo $path; ?>" name="path" size="85" title="<?php print_lang('path_info'); ?>" />
				</td>
			</tr>
			<tr>					
				<td align="right">
					<b><?php print_lang('post-script'); ?></b><br>
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
				<b><?php print_lang('type'); ?></b>	
				</td>
				<td align="left">
		<?php
		// Render a radio button for every registered content type.
		// New types automatically appear here once added to server_content_categories.php.
		foreach ((array)$addon_type_labels as $type_key => $type_label)
		{
			$checked = ( isset($addon_type) AND $type_key == $addon_type) ? 'checked' : '';
			echo '<input type="radio" name="addon_type" value="'.htmlspecialchars($type_key).'" '.$checked.'>'.htmlspecialchars($type_label).' &nbsp; ';
		}
		?>
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
	// Validate the requested addon_type against the full category map so new types are accepted.
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
