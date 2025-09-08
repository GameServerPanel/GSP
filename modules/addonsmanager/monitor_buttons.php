<?php
/*
 * Component of the addonsmanager module
 */
 
$query_groups = "";
if($_SESSION['users_role'] != "admin")
{
	$groups = $db->getUsersGroups($_SESSION['user_id']);
	$query_groups .= " AND (";
	foreach($groups as $group)
		$query_groups .= "group_id=".$group['group_id']." OR ";
	$query_groups .= "group_id=0 OR group_id IS NULL)";
}
$addons = $db->resultQuery("SELECT addon_id FROM OGP_DB_PREFIXaddons WHERE home_cfg_id=".$server_home['home_cfg_id'].$query_groups);
$addons_qty = count($addons);
if($addons and $addons_qty >= 1){
	$module_buttons = array(
		"<a class='monitorbutton' href='?m=addonsmanager&amp;p=user_addons&amp;home_id=".
			$server_home['home_id']."&amp;mod_id=".$server_home['mod_id'].
			"&amp;ip=".$server_home['ip']."&amp;port=".$server_home['port']."'>
			<img src='" . check_theme_image("modules/administration/images/addons_manager.png") . "' title='". get_lang("addons") ."'>
			<span>". get_lang("addons") ." (".$addons_qty.")</span>
		</a>"
	);
}
else
	$module_buttons = array();
?>