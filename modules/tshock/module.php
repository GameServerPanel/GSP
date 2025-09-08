<?php
/*
 * Component of the tshock module
 */

// Module general information
$module_title = "tshock";
$module_version = "Alpha";
$db_version = 0;
$module_required = FALSE;
$module_menus = array( array( 'subpage' => '', 'name'=>'tshock', 'group'=>'user' ) );

$install_queries = array();
$install_queries[0] = array(
    "DROP TABLE IF EXISTS ".OGP_DB_PREFIX."tshock;",
    "CREATE TABLE ".OGP_DB_PREFIX."tshock (
        `token_id` int(11) NOT NULL auto_increment,
		`ip` varchar(255) NOT NULL,
		`port` int(11) NOT NULL,
		`token` varchar(64) NOT NULL,
        PRIMARY KEY  (`token_id`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
?>
