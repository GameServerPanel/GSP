<?php
/*
 * GSP – Steam Workshop standalone admin page (deprecated)
 * Workshop administration now lives in Server Content Manager.
 */

function exec_ogp_module()
{
    $target = 'home.php?m=addonsmanager&p=addons_manager';
    echo '<h2>Steam Workshop &ndash; Deprecated</h2>';
    echo '<p>The standalone Steam Workshop admin page has been removed. Use Server Content Manager instead.</p>';
    echo '<p><a class="button" href="' . htmlspecialchars($target, ENT_QUOTES, 'UTF-8') . '">Open Server Content Manager</a></p>';
}
