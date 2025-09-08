<?php
/*
 * Component of the fast_download module
 */

// Module general information
$module_title = "Fast Download";
$module_version = "2.1";
$db_version = 4;
$module_required = TRUE;
$module_menus = array( array( 'subpage' => '', 'name'=>'Fast Download', 'group'=>'admin' ) );
$module_access_rights = array('d' => 'allow_fast_download');

$install_queries[0] = array("SELECT NOW();");
$install_queries[1] = array("DROP TABLE IF EXISTS `".OGP_DB_PREFIX."fastdl`;");
$install_queries[2] = array("SELECT NOW();");
$install_queries[3] = array(
	"CREATE TABLE ".OGP_DB_PREFIX."fastdl_access_rules (
		`home_cfg_id` varchar(32) NOT NULL,
		`match_file_extension` TEXT,
		`match_client_ip` TEXT,
		UNIQUE KEY (`home_cfg_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$install_queries[4] = array(
	"CREATE TABLE ".OGP_DB_PREFIX."fastdl_settings (
		`remote_server_id` int(11) NOT NULL,
		`setting` varchar(63) NOT NULL,
        `value` varchar(255) NOT NULL,
		UNIQUE KEY remote_server_id (remote_server_id,setting)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$uninstall_queries = array("DROP TABLE IF EXISTS `".OGP_DB_PREFIX."fastdl_access_rules`;",
						   "DROP TABLE IF EXISTS `".OGP_DB_PREFIX."fastdl_settings`;");
?>
