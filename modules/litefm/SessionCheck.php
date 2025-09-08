<?php
/*
 * Component of the litefm module
 */

if(file_exists("includes/helpers.php")){
	require_once("includes/helpers.php");
}else{
	if(file_exists(__DIR__ . "/../../includes/helpers.php")){
		require_once(__DIR__ . '/../../includes/helpers.php');
	}
}

if(function_exists("startSession")){
	startSession();
}else{
	session_name("opengamepanel_web");
	session_start();
}

if (isset($_SESSION['users_login']))
	$json['valid'] = true;
else
	$json['valid'] = false;
echo json_encode($json);
?>
