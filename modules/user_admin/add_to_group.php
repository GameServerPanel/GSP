<?php
/*
 * Component of the user_admin module
 */

function exec_ogp_module()
{
    global $db;
    global $view;

	$group_id = $_REQUEST['group_id'];
	
	if ( !$db->isAdmin($_SESSION['user_id']) )
	{
		$result = $db->getUserGroupList($_SESSION['user_id']);
		foreach ( $result as $row ) #loop through the groups
		{
			if ( $row['group_id'] == $group_id )
			{
				$own_group = TRUE;
			}
		}
	}
	
	if(!$db->isAdmin($_SESSION['user_id']) && !isset($own_group)) 
	{
		echo "<p class='note'>".get_lang('not_available')."</p>";
		return;
	}
	
    if( isset($_POST['add_user_to_group']) )
    {
        $group_id = trim($_POST['group_id']);
        $user_id = trim($_POST['user_to_add']);
        $user = $db->getUserById($user_id);
        $group = $db->getGroupById($group_id);
        if ( $group['main_user_id'] == $user_id or !$db->addUsertoGroup($user_id,$group_id))
        {
            print_failure(get_lang_f('could_not_add_user_to_group', $user['users_login'], $group['group_name']));
            $view->refresh("?m=user_admin&amp;p=show_groups");
            return;
        }

        echo "<p class='success'>".get_lang_f('successfully_added_to_group', $user['users_login'], $group['group_name'])."</p>";
		$db->logger(get_lang_f('successfully_added_to_group', $user['users_login'], $group['group_name']));
        $view->refresh("?m=user_admin&amp;p=show_groups");
    }
}
?>
