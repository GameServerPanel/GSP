<?php
/*
 * Component of the administration module
 */

// Module general information
$module_title = "Administration";
$module_version = "1.1";
$db_version = 1;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => 'watch_logger', 'name'=>'Watch Logger', 'group'=>'admin' ) );
$install_queries = array();
$install_queries[0] = array(
"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."adminExternalLinks`;",
"CREATE TABLE IF NOT EXISTS ".OGP_DB_PREFIX."adminExternalLinks
(
  link_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  url VARCHAR(200) NOT NULL,
  user_id VARCHAR(7) NOT NULL
) ENGINE=MyISAM;");

$install_queries[1] = array(
"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."logger`;",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."logger` 
(
  `log_id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,  
  `date` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,  
  `ip` varchar(15) NOT NULL,
  `message` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
?>

