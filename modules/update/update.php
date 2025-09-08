<?php
/*
 * Component of the update module
 */

 // todo, make checking and updating functions for updateing on the background.
 // todo, more specified updates in smaller packages
function exec_ogp_module()
{
        global $db, $settings;
        define('REPONAME', 'OGP-Website');

        if ($_SESSION['users_group'] != "admin")
        {
                print_failure(get_lang('no_access'));
                return;
        }
echo "To update the panel, visit our git at http://git.iaregamer.com:3000, download the panel and replace your files.";

}
