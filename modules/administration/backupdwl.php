<?php
/*
 * Component of the administration module
 */

function exec_ogp_module() 
{
	$path = getcwd()."/".$_GET['randir']."/"; // change the path to fit your websites document structure
	$fullPath = $path.$_GET['dwfile'];

	if ($fd = fopen ($fullPath, "r")) {
		header("Content-Disposition: attachment; filename=\"".$_GET['dwfile']."\"");
		header("Content-length: ".filesize($fullPath));
		while(!feof($fd)) {
			$buffer = fread($fd, 2048);
			echo $buffer;
		}
		fclose($fd);
	}
	unlink($fullPath);
	rmdir($path);
	exit;
}
?>