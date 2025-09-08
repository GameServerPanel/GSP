<?php
/*
 * Component of the litefm module
 */

function exec_ogp_module()
{
	require_once(MODULES."/litefm/litefm.php");

	$home_id = $_REQUEST['home_id'];

	if (empty($home_id))
		return;
	
	global $db;
	$isAdmin = $db->isAdmin( $_SESSION['user_id'] );

	if($isAdmin) 
		$home_cfg = $db->getGameHome($home_id);
	else
		$home_cfg = $db->getUserGameHome($_SESSION['user_id'],$home_id);

	if ($home_cfg === FALSE)
		return;
	
	if ( preg_match("/f/",$home_cfg['access_rights']) != 1 )
		return;
	
	$downloads_folder = "modules/litefm/downloads";
	
	if(isset($_GET['remove_did']))
	{
		$did = $_GET['remove_did'];
		if(file_exists("$downloads_folder/$did"))
			unlink("$downloads_folder/$did");
		if(isset($_SESSION['download'][$did]))
			unset($_SESSION['download'][$did]);
		return;
	}
	
	if(!file_exists($downloads_folder))
	{
		if(!mkdir($downloads_folder))
			return;
	}
	else
	{
		if(!is_writable($downloads_folder))
			return;
	}
	
	$did = $_GET['did'];
	
	if(!isset($_SESSION['download'][$did]))
	{	
		if (litefm_check($home_id) === FALSE)
			return;
		$_SESSION['download'][$did]['fileph'] = $_SESSION['fm_cwd_'.$home_id];
		$_SESSION['fm_cwd_'.$home_id] = dirname($_SESSION['fm_cwd_'.$home_id]);
		$_SESSION['download'][$did]['offset'] = 0;
	}
	
	set_time_limit(0);
	$remote = new OGPRemoteLibrary($home_cfg['agent_ip'], $home_cfg['agent_port'], $home_cfg['encryption_key'], $home_cfg['timeout']);
	$fp = fopen("$downloads_folder/$did", "w");
	$_SESSION['download'][$did]['offset'] = $remote->remote_get_file_part($home_cfg['home_path']."/".$_SESSION['download'][$did]['fileph'], $_SESSION['download'][$did]['offset'], $data);
	if($_SESSION['download'][$did]['offset'] != -1)
		fwrite($fp,$data);
	fclose($fp);
	header("Location: $downloads_folder/$did");
}
?>