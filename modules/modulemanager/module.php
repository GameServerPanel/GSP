<?php
/*
 * Component of the modulemanager module
 */

// Module general information
$module_title = "Module manager";
$module_version = "1.1";
$db_version = 2;
$module_required = TRUE;
$module_menus = array(
	array( 'subpage' => '', 'name'=>'Modules', 'group'=>'admin' )
	);
	
## You will need uncomment the next three lines (remove /* from the beginning and */ from the end) 
## of the next array if you are updating from a version previous or equal to 2429:
$install_queries[0] = array();
$install_queries[1] = array();
$install_queries[2] = array("DROP TABLE IF EXISTS ".OGP_DB_PREFIX."module_access_rights",
							"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."module_access_rights` (".
							"`module_id` int(11) NOT NULL COMMENT 'This references to modules.id',".
							"`flag` char(1) NOT NULL,".
							"`description` varchar(64) NOT NULL,".
							"UNIQUE (`flag`)".
							") ENGINE=MyISAM DEFAULT CHARSET=latin1;");
?>
