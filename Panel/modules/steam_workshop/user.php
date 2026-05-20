<?php
/*
 * GSP – Steam Workshop standalone user page (deprecated)
 * Workshop user workflows now live in Server Content Manager.
 */

function exec_ogp_module()
{
    $home_id = isset($_REQUEST['home_id']) ? (int)$_REQUEST['home_id'] : 0;
    $mod_id = isset($_REQUEST['mod_id']) ? (int)$_REQUEST['mod_id'] : 0;
    $ip = isset($_REQUEST['ip']) ? rawurlencode((string)$_REQUEST['ip']) : '';
    $port = isset($_REQUEST['port']) ? rawurlencode((string)$_REQUEST['port']) : '';

    $target = 'home.php?m=addonsmanager&p=user_addons';
    if ($home_id > 0) {
        $target .= '&home_id=' . $home_id;
    }
    if ($mod_id > 0) {
        $target .= '&mod_id=' . $mod_id;
    }
    if ($ip !== '') {
        $target .= '&ip=' . $ip;
    }
    if ($port !== '') {
        $target .= '&port=' . $port;
    }

    echo '<h2>Steam Workshop &ndash; Deprecated</h2>';
    echo '<p>The standalone Steam Workshop page has been removed. Use Server Content Manager instead.</p>';
    echo '<p><a class="button" href="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '">Open Server Content</a></p>';
}
