<?php
/*
 * Component of the modulemanager module
 */

function exec_ogp_module()
{
    global $db;
    global $view;

    print "<h2>".get_lang_f('uninstalling_module',$_REQUEST['module'])."</h2>";

    require_once('modules/modulemanager/module_handling.php');

    if ( isset($_REQUEST['id']) && isset($_REQUEST['module']) &&
        uninstall_module($db, $_REQUEST['id'], $_REQUEST['module']) === TRUE )
        print_success(get_lang_f("successfully_uninstalled_module",$_REQUEST['module']));
    else
        print_failure(get_lang_f("failed_to_uninstall_module",$_REQUEST['module']));

    $view->refresh("?m=modulemanager");
}
?>
