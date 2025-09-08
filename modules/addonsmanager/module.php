<?php
/*
 * Component of the addonsmanager module
 */

// Module general information
$module_title = "Addons Manager";
$module_version = "1.2";
$db_version = 2;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => 'addons_manager', 'name'=>'Addons Manager', 'group'=>'admin' ) );

$install_queries = array();
$install_queries[0] = array(
"DROP TABLE IF EXISTS `".OGP_DB_PREFIX."addons`;",
"CREATE TABLE IF NOT EXISTS ".OGP_DB_PREFIX."addons 
 (addon_id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(80) NOT NULL,
  url VARCHAR(200) NOT NULL,
  path VARCHAR(80) NOT NULL,
  addon_type VARCHAR(7) NOT NULL,
  home_cfg_id VARCHAR(7) NOT NULL) ENGINE=MyISAM;");

$install_queries[1] = array(
	"ALTER TABLE `".OGP_DB_PREFIX."addons` ADD `post_script` longtext NOT NULL;");
	
$install_queries[2] = array(
	"ALTER TABLE `".OGP_DB_PREFIX."addons` ADD `group_id` int(11) NULL;");
?>
