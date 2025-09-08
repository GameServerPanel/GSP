<?php
/*
 * Component of the gamemanager module
 */

function exec_ogp_module() {

	global $db;

	if(isset($_GET['home_id']))
	{
		$user_id = $_SESSION['user_id'];
		
		$home_id = $_GET['home_id'];
		
		$mod_id = $_GET['mod_id'];
		
		$isAdmin = $db->isAdmin($user_id);
		
		if($isAdmin) 
			$home_info = $db->getGameHome($home_id);
		else
			$home_info = $db->getUserGameHome($user_id,$home_id);
		
		$mods = $home_info['mods'];
		$current_mod_info = $mods[$mod_id];
		$mod_name = $current_mod_info['mod_name'];
		$mod_key = $current_mod_info['mod_key'];
		
		if ( strtolower($mod_name) == "none")
			$mod = $mod_key;
		else 
			$mod = $mod_name;
			
		$game = $home_info['game_name'];
		$home_cfg_id = $current_mod_info['home_cfg_id'];
		$mod_cfg_id = $current_mod_info['mod_cfg_id'];
		
		if($home_cfg_id === null && $mod_cfg_id === null){
			print_failure(get_lang('invalid_game_mod_id'));
			return;
		}
		
		echo "<h2>".get_lang_f( "presets_for_game_and_mod",$game,$mod)."</h2>";
		
		if(isset($_POST['add_rcon_preset']))
		{
			$name = $_POST['name'];
			$command = $_POST['command'];
			$db->addRconPreset($name,$command,$home_cfg_id,$mod_cfg_id);
		}
		if(isset($_POST['del_rcon_preset']))
		{
			$preset_id = $_POST['preset_id'];
			$db->delRconPreset($preset_id);
		}
		if(isset($_POST['change_rcon_preset']))
		{
			$name = $_POST['name'];
			$command = $_POST['command'];
			$preset_id = $_POST['preset_id'];
			$db->changeRconPreset($name,$command,$preset_id);
		}
?>
<table>

 <tr>
  <td>
	<form action="" method="post">
	<?php print_lang('name'); ?>
  </td>
  <td>
	<input type="text" name="name" style="width:120px;" />
  </td>
  <td>
<?php print_lang('command'); ?>
  </td>
  <td>
	<input type="text" name="command" style="width:430px;" />
  </td>
  <td>
	<input type="submit" value="<?php print_lang('add_preset'); ?>" name="add_rcon_preset" />
	</form>
  </td>
 </tr>
</table>
<br>
<?php
		$presets = $db->getRconPresets($home_cfg_id,$mod_cfg_id);
		if($presets > 0)
		{
		
			echo "<h2>".get_lang("edit_presets")."</h2>";
			echo "<table>";
			foreach ($presets as $preset)
			{
				?>
 <tr>
  <td>
	<form action="" method="post">
	<?php print_lang('name'); ?>
  </td>
  <td>
	<input type="text" name="name" style="width:120px;" value="<?php echo $preset['name']; ?>" />
  </td>
  <td>
	<?php print_lang('command'); ?>
  </td>
  <td>
	<input type="text" name="command" style="width:430px;" value="<?php echo $preset['command']; ?>"/>
  </td>
  <td>
	<input type="hidden" name="preset_id" value="<?php echo $preset['preset_id']; ?>" />
	<input type="submit" value="<?php print_lang('change_preset'); ?>" name="change_rcon_preset" />
	<input type="submit" value="<?php print_lang('del_preset'); ?>" name="del_rcon_preset" />
	</form>
  </td>
 </tr>
				<?php
			}
		}
		echo "</table>";
		echo "<table class='center'><tr><td><a href='?m=gamemanager&amp;p=game_monitor&amp;home_id=".$home_id."'><< ".get_lang('back')."</a></td></tr></table>";
	}
}
?>
