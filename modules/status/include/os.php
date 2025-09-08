<?php
/*
 * Component of the status module
 */

include_once ("modules/status/config.php");

global $db;
$cygwin = FALSE;
if( isset($_GET['remote_server_id']) AND $_GET['remote_server_id'] != "webhost" ) {
	$rhost_id = $_GET['remote_server_id'];
	$remote_server = $db->getRemoteServer($rhost_id);
	require_once('includes/lib_remote.php');
	$remote = new OGPRemoteLibrary($remote_server['agent_ip'], $remote_server['agent_port'], $remote_server['encryption_key'], $remote_server['timeout']);
	$os_string = $remote->what_os();
	if( preg_match("/Linux/", $os_string) ) {
		$os = "linux";
		$goodforuptime = "1";
		if( preg_match("/64/", $os_string) ) 
			$osbuild = "64bit";
		else
			$osbuild = "32bit";
	}elseif( preg_match("/CYGWIN/", $os_string) ) {
		$os = "linux";
		$cygwin = TRUE;
		if( preg_match("/64/", $os_string) ) 
			$osbuild = "64bit";
		else
			$osbuild = "32bit";
	} else {
		$modulecpu = "0";
		$modulememory = "0";
		$modulestorage = "0";
		$modulesystemuptime = "0";
	}	
} elseif (PHP_OS == "WINNT") {
    $os = "windows";
    $osbuild = php_uname('v');
} elseif (PHP_OS == "Linux") {
    $os = "linux";
    $goodforuptime = "1";
    $osbuild = php_uname('r');
} elseif(PHP_OS == "CYGWIN") {
    $os = "linux";
	$cygwin = TRUE;
    $goodforuptime = "1";
    $osbuild = php_uname('r');
} else {
    $os = "nocpu";
    $osbuild = php_uname('r');
}
?>