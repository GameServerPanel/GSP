<?php
/*
 * Component of the util module
 */
require 'modules/util/functions.php';
function exec_ogp_module() 
{
	require 'modules/util/util_config.php';
	convert($_POST['steam_input']);
}
?>