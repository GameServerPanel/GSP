<?php
require_once('../../includes/lib_remote.php');
require_once('../../modules/config_games/server_config_parser.php');
require_once('../../includes/functions.php');

// Get parameters from GET
$home_id = isset($_GET['home_id']) ? $_GET['home_id'] : null;
$mod_id = isset($_GET['mod_id']) ? $_GET['mod_id'] : null;

if (!$home_id || !$mod_id) {
    echo "<pre>No home_id or mod_id provided.</pre>";
    exit;
}

// You may need to adjust this to match your DB access
$db = getOGPDBInstance(); // Replace with your DB instance
$home_info = $db->getGameHome($home_id);
$server_xml = read_server_config(SERVER_CONFIG_LOCATION."/".$home_info['home_cfg_file']);
$remote = new OGPRemoteLibrary($home_info['agent_ip'],$home_info['agent_port'],$home_info['encryption_key'],$home_info['timeout']);

$home_log = "";
if( isset( $server_xml->console_log ) ) {
    $log_path = preg_replace("/%mod%/i", $home_info['mods'][$mod_id]['mod_key'], $server_xml->console_log);
    $log_retval = $remote->remote_readfile( $home_info['home_path'].'/'.$log_path, $home_log );
} else {
    $log_retval = $remote->get_log(OGP_SCREEN_TYPE_HOME, $home_info['home_id'], clean_path($home_info['home_path']."/".$server_xml->exe_location), $home_log);
}
$lines = explode("\n", $home_log);
$home_log = implode("\n", array_slice($lines, -40));

if ($log_retval > 0) {
    echo "<pre style='background:black;color:white;'>".htmlspecialchars($home_log)."</pre>";
} else {
    echo "<pre style='background:black;color:white;'>Unable to get log: $log_retval</pre>";
}
?>
