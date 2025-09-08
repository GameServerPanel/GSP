<?php
/*
 * Component of the modulemanager module
 */

function exec_ogp_module()
{
    global $db;
    global $view;

    print "<h2>".get_lang_f('updating_modules')."</h2>";

    updateAllPanelModules();

	print "<p>".get_lang_f('updating_finished')."</p>";
	
    $view->refresh("?m=modulemanager",30);
}
?>
