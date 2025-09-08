<?php
/*
 * Component of the update module
 */

// Module general information
$module_title = "Update";
$module_version = "1.1";
$db_version = 2; // avoid 'duplicate table' error message.
$module_required = TRUE;
$module_menus = array(
    array( 'subpage' => '', 'name'=>'Update', 'group'=>'admin' )
);

$install_queries = array();
$install_queries[0] = array();
$install_queries[1] = array(
    "CREATE TABLE IF NOT EXISTS ".OGP_DB_PREFIX."update_blacklist (
        `file_path` VARCHAR(1000) UNIQUE NOT NULL
    ) ENGINE=MyISAM DEFAULT CHARSET=latin1;");
$install_queries[2] = array(
	"DELETE FROM ".OGP_DB_PREFIX."update_blacklist
WHERE file_path IN (SELECT * 
             FROM (SELECT file_path FROM ".OGP_DB_PREFIX."update_blacklist 
                   GROUP BY file_path HAVING (COUNT(*) > 1)
                  ) AS A
            );",
    "ALTER TABLE ".OGP_DB_PREFIX."update_blacklist MODIFY file_path VARCHAR(1000);",
	"ALTER TABLE ".OGP_DB_PREFIX."update_blacklist ADD UNIQUE (file_path);"
);
?>
