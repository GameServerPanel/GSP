<?php
/*
 * Component of the mysql module
 */

// Module general information
$module_title = "MySQL";
$module_version = "0.1";
$db_version = 0;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => 'mysql_admin', 'name'=>'MySQL Admin', 'group'=>'admin' ) );

$install_queries[0] = array(
    "DROP TABLE IF EXISTS `".OGP_DB_PREFIX."mysql_servers`;",
    "CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."mysql_servers` (
	`mysql_server_id` int(11) NOT NULL auto_increment,
	`remote_server_id` int(11) NOT NULL,
	`mysql_name` varchar(100) NOT NULL,
	`mysql_ip` varchar(255) NOT NULL,
	`mysql_port` int(11) NOT NULL,
	`mysql_root_passwd` VARCHAR( 32 ) NULL,
	`privilegies_str` LONGTEXT NULL,
	PRIMARY KEY  (`mysql_server_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

"DROP TABLE IF EXISTS ".OGP_DB_PREFIX."mysql_databases",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."mysql_databases` (
	`db_id` int(11) NOT NULL auto_increment,
	`mysql_server_id` int(11) NOT NULL,
	`home_id` int(11) NOT NULL,
	`db_user` varchar(50) NOT NULL,
	`db_passwd` varchar(50) NOT NULL,
	`db_name` varchar(50) NOT NULL,
	`enabled` int(11) NOT NULL,
	PRIMARY KEY  (`db_id`),
	UNIQUE KEY (`mysql_server_id`,`db_name`),
	UNIQUE KEY (`mysql_server_id`,`db_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

?>
