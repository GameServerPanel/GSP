<?php
/*
 * Component of the mysql module
 */
 

$mysql_dbs = $db->resultQuery("SELECT db_id FROM OGP_DB_PREFIXmysql_databases WHERE enabled=1 AND home_id=".$server_home['home_id']);
if(!empty($mysql_dbs))
{
	$module_buttons = array(
		"<a class='monitorbutton' href='?m=mysql&p=user_db&home_id=".$server_home['home_id']."'>
			<img src='" . check_theme_image("modules/administration/images/mysql_admin.png") . "' title='". get_lang("mysql_databases") ."'>
			<span>". get_lang("mysql_databases") ."</span>
		</a>"
	);
}
else
	$module_buttons = array();
?>