<?php
/*
 * Component of the update module
 */

 // todo, make checking and updating functions for updateing on the background.
 // todo, more specified updates in smaller packages

function exec_ogp_module()
{
	global $db, $settings;
	runPostUpdateOperations();
}
?>
