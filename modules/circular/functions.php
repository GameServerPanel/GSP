<?php
/*
 * Component of the circular module
 */
function get_usernames_not_read_circular($circular_id)
{
	global $db;
	$circular_id = $db->real_escape_string($circular_id);
	$query = "SELECT user_id ".
			 "FROM `OGP_DB_PREFIXcircular_recipients` ".
			 "WHERE `circular_id` = '$circular_id' ".
			 "AND `status` = '0'";
	$users = $db->resultQuery($query);
	if($users)
	{
		$user_names = array();
		foreach($users as $user)
		{
			$user_info = $db->getUserById($user['user_id']);
			$user_names[] = $user_info['users_login'];
		}
		return implode(', ', $user_names);
	}
	return false;
} 

function remove_circular($circular_id, $admin = false)
{
	global $db;
	$circular_id = $db->real_escape_string($circular_id);
	
	if($admin)
	{
		$db->query("DELETE FROM `OGP_DB_PREFIXcircular_recipients` ".
				   "WHERE `circular_id` = '$circular_id'");
		$db->query("DELETE FROM `OGP_DB_PREFIXcircular` ".
				   "WHERE `circular_id` = '$circular_id'");
	}
	else
	{
		$db->query("DELETE FROM `OGP_DB_PREFIXcircular_recipients` ".
				   "WHERE `circular_id` = '$circular_id' ".
				   "AND `user_id` = '$_SESSION[user_id]'");
	}
}

function set_circular_readed($circular_id)
{
	global $db;
	$circular_id = $db->real_escape_string($circular_id);
	$db->query("UPDATE `OGP_DB_PREFIXcircular_recipients` ".
			   "SET `status` = '1' ".
			   "WHERE `circular_id` = '$circular_id' ".
			   "AND `user_id` = '$_SESSION[user_id]'");
}

function get_circulars($all = false)
{
	global $db;
	if($all)
		$query = "SELECT * FROM `OGP_DB_PREFIXcircular`";
	else
		$query = "SELECT * ".
				 "FROM `OGP_DB_PREFIXcircular_recipients` ".
				 "NATURAL JOIN `OGP_DB_PREFIXcircular` ".
				 "WHERE user_id = $_SESSION[user_id]";
	
	return $db->resultQuery($query);
}
 
function send_to_user($user_id, $circular_id)
{
	global $db;
	$user_id = $db->real_escape_string($user_id);
	$circular_id = $db->real_escape_string($circular_id);
	return $db->query("INSERT INTO `OGP_DB_PREFIXcircular_recipients` VALUES('$user_id','$circular_id','0');");
}

function get_user_ids($type, $ids, &$user_ids)
{
	global $db;
	foreach($ids as $id)
	{
		if($type == 'admins' or $type == 'users')
		{
			if(!in_array($id, $user_ids))
				$user_ids[] = $id;
		}
		elseif($type == 'groups')
		{
			$group_users = $db->listUsersInGroup($id);
			if($group_users and !empty($group_users))
			{
				foreach($group_users as $user)
				{
					if(!in_array($user['user_id'], $user_ids))
						$user_ids[] = $user['user_id'];
				}
			}
		}
		elseif($type == 'subusers_of_users')
		{
			$sub_users_ids = $db->getUsersSubUsersIds($id);
			if($sub_users_ids and !empty($sub_users_ids))
			{
				foreach($sub_users_ids as $user_id)
				{
					if(!in_array($user_id, $user_ids))
						$user_ids[] = $user_id;
				}
			}
		}
	}		
}

function send_circular($data)
{
	global $db;
	$circular_id = $db->resultInsertId('circular', array('subject' => $data['subject'], 'message' => $data['message']));
	$user_ids = array();
	unset($data['subject'], $data['message']);
	foreach($data as $type => $ids)
	{
		if(is_array($ids) and !empty($ids))
			get_user_ids($type, $ids, $user_ids);
	}
	$failed_recipients = array();
	foreach($user_ids as $user_id)
	{
		if(!send_to_user($user_id, $circular_id))
			$failed_recipients[] = $user_id;
	}
	if(empty($failed_recipients))
	{
		echo "Circular sent to all recipients.";
	}
	else
	{
		echo "Failed to send circular to users with the following ids:\n". implode(", ", $failed_recipients) . ".";
	}
}