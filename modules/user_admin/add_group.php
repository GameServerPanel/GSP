<?php
/*
 * Component of the user_admin module
 */

function exec_ogp_module()
{
    global $db;
    global $view;

    if( isset($_POST['add_group']) )
    {
        $group = sanitizeInputStr($_POST['group_name']);

        if (empty($group))
        {
            print_failure(get_lang('group_name_empty'));
            return;
        }

        if ( !$db->addGroup($group,$_SESSION['user_id']) )
        {
            print_failure(get_lang_f("failed_to_add_group",$group));
            $view->refresh("?m=user_admin&amp;p=show_groups");
            return;
        }

        print_success(get_lang_f('successfully_added_group',$group));
		$db->logger(get_lang_f('successfully_added_group',$group));
        $view->refresh("?m=user_admin&amp;p=show_groups");
    }
}
?>
