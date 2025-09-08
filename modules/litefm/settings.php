<?php
/*
 * Settings management for the litefm module
 */

require_once(MODULES."/litefm/functions.php");
function exec_ogp_module()
{
	require_once('includes/form_table_class.php');
	global $db;
	echo "<h2>".get_lang('litefm_settings')."</h2>";
	
	// Get File Operations Keys
	$fo_keys = get_file_operations_keys();

	if ( isset($_REQUEST['update_settings']) )
	{
		$file_operations = array();
		foreach($fo_keys as $key)
		{
			$file_operations[$key] = $_POST[$key];
		}
		$lfm_file_operations = json_encode($file_operations);
		
		$litefm_settings = array(
			"lfm_file_operations" => $lfm_file_operations,
		);
		
		$db->setSettings($litefm_settings);
		print_success(get_lang('settings_updated'));
	}

	$settings = $db->getSettings();
	// Get File Operations Settings
	$fo = get_fo_settings($settings,$fo_keys);

	$ft = new FormTable();
	$ft->start_form("?m=litefm&amp;p=litefm_settings", "post", "autocomplete=\"off\"");
	$ft->start_table();
	foreach($fo_keys as $key)
	{
		$ft->add_field('on_off',$key,$fo[$key]);
	}
	$ft->end_table();
	$ft->add_button("submit","update_settings",get_lang('update_settings'));
	$ft->end_form();
}
?>