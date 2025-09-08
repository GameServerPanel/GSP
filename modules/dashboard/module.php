<?php
/*
 * Component of the dashboard module
 */

// Module general information
$module_title = "Dashboard";
$module_version = "1.0";
$db_version = 0;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => 'dashboard', 'name'=>'Dashboard', 'group'=>'user' ) );
$install_queries = array();
$install_queries[0] = array(
"DROP TABLE IF EXISTS ".OGP_DB_PREFIX."widgets;",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."widgets` (  
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `column_id` int(11) NOT NULL,  
  `sort_no` int(11) NOT NULL,  
  `collapsed` tinyint(4) NOT NULL,
  `title` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

"DROP TABLE IF EXISTS ".OGP_DB_PREFIX."widgets_users",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."widgets_users` (  
  `user_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,  
  `column_id` int(11) NOT NULL,  
  `sort_no` int(11) NOT NULL,  
  `collapsed` tinyint(4) NOT NULL,  
  `title` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;",

"INSERT INTO `".OGP_DB_PREFIX."widgets` (`id`, `column_id`, `sort_no`, `collapsed`, `title`) VALUES 
(1, 1, 1, 0, 'Game Monitor'),  
(2, 2, 0, 0, 'Online Server'),  
(3, 2, 1, 0, 'Currently Online'),  
(4, 3, 0, 0, 'FTP'),
(5, 3, 1, 0, 'Support'); ");
?>

