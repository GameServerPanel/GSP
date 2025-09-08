<?php
/*
 * Component of the ts3admin module
 */

// Module general information
$module_title = "TS3Admin";
$module_version = "0.2";
$db_version = 2;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => '', 'name'=>'ts3admin', 'group'=>'user' ) );
$install_queries = array();
$install_queries[0] = array(
"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."ts3_homes`;",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."ts3_homes`
  (`ts3_id` int(50) NOT NULL auto_increment,
  `rserver_id` int(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `pwd` varchar(40) NOT NULL,
  `vserver_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  PRIMARY KEY (`ts3_id`),
UNIQUE KEY user_id (user_id,vserver_id)) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$install_queries[1] = array(
"ALTER TABLE `".OGP_DB_PREFIX."ts3_homes` DROP INDEX `user_id` ,
 ADD UNIQUE `rserver_id` ( `rserver_id` , `vserver_id` , `user_id` );");
$install_queries[2] = array(
"ALTER TABLE `".OGP_DB_PREFIX."ts3_homes` ADD `port` int(11) DEFAULT '10011'"
);
?>
