<?php
/*
 * Component of the user_admin module
 */

function exec_ogp_module()
{
    global $db;
    global $view;
	
	$group_id = $_GET['group_id'];
	
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
	
    // Delete user from group.
    if( isset($_GET['group_id']) && isset($_GET['user_id']) )
    {
        $group_id = trim($_GET['group_id']);
        $user_id = trim($_GET['user_id']);

        if ( !$db->delUserFromGroup($user_id, $group_id))
        {
            print_failure(get_lang_f('could_not_delete_user_from_group', $user_id, $group_id));
            $view->refresh("?m=user_admin");
            return;
        }

        echo "<p class='success'>".get_lang_f('successfully_removed_from_group', $user_id, $group_id)."</p>";
		$db->logger(get_lang_f('successfully_removed_from_group', $user_id, $group_id));
        $view->refresh("?m=user_admin&amp;p=show_groups");
    }
}
?>
