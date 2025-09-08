<?php
/*
 * Component of the settings module
 */

// Module general information
$module_title = "Settings";
$module_version = "1.1";
$db_version = 1;
$module_required = TRUE;
$module_menus = array(
    array( 'subpage' => '', 'name'=>'Panel Settings', 'group'=>'admin' ),
	array( 'subpage' => 'themes', 'name'=>'Theme Settings', 'group'=>'admin' )
);

$install_queries = array();
$install_queries[0] = array(
    "DROP TABLE IF EXISTS ".OGP_DB_PREFIX."settings;",
    "CREATE TABLE ".OGP_DB_PREFIX."settings (
        `setting` varchar(63) NOT NULL,
        `value` varchar(255) NOT NULL,
        PRIMARY KEY  (`setting`)
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

$install_queries[1] = array(
    "ALTER TABLE `".OGP_DB_PREFIX."settings` CHANGE `value` `value` VARCHAR( 1024 ) NOT NULL;");
?>
