<?php
/*
 * Component of the config_games module
 */

include_once("server_config_parser.php");

// Module general information
$module_title = "Config games";
$module_version = "1.0";
$db_version = 0;
$module_required = TRUE;

$module_menus = array(
	array( 'subpage' => '', 'name'=>'Game/Mod Config', 'group'=>'admin' )
	);

$install_queries = array();

$install_queries[0] = array(
"DROP TABLE IF EXISTS ".OGP_DB_PREFIX."config_homes;",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."config_homes` (
  `home_cfg_id` int(20) NOT NULL auto_increment,
  `game_key` varchar(64) NOT NULL,
  `game_name` varchar(255) NOT NULL,
  `home_cfg_file` varchar(64) NULL,
  PRIMARY KEY  (`home_cfg_id`),
  UNIQUE KEY `game_key` (`game_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1;",

"DROP TABLE IF EXISTS ".OGP_DB_PREFIX."config_mods;",
"CREATE TABLE IF NOT EXISTS `".OGP_DB_PREFIX."config_mods` (
  `mod_cfg_id` int(50) NOT NULL auto_increment,
  `home_cfg_id` varchar(50) NOT NULL,
  `mod_key` varchar(100) NOT NULL COMMENT 'mod short name - used by the game server for startup commands - ex cstrike',
  `mod_name` varchar(255) NOT NULL COMMENT 'Mod value is user defined string - like Counter-Strike',
  `def_precmd` TEXT,
  `def_postcmd` TEXT,
  PRIMARY KEY  (`mod_cfg_id`),
  UNIQUE KEY `home_cfg_id` (`home_cfg_id`,`mod_key`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;");

?>
