<?php
/*
 * Component of the subusers module
 */

//Open Game Panel Sub User Registration Add On By
//  own3mall

function exec_ogp_module()
{
	global $db,$view;

	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count($_SESSION['ERRMSG_ARR']) >0 ) 
	{
		$errmsg = '<table>';
		foreach($_SESSION['ERRMSG_ARR'] as $msg) 
		{
			$errmsg .= "<tr><td><img width='8px' src='images/offline.png'/></td><td style='text-align:left;color:red;'>".$msg.'</td></tr>'; 
		}
		$errmsg .= '</table><br>';
		unset($_SESSION['ERRMSG_ARR']);
	}
	echo "<h2>".get_lang('create_sub_user')."</h2>";
	if(isset($errmsg))
	{
		echo $errmsg;
		$input = $_SESSION['INPUT'];
	}

	?>
	<form name="loginForm" method="post" action="?m=subusers&p=adduser">
	<table>
	<tr>
	  <td class="right">
		<?php echo get_lang('login_name'); ?>
	  </td>
	  <td class="left">
		<input name="users_login" type="text" size="25" id="users_login" value="<?php if(isset($input['users_login'])) echo $input['users_login']; ?>" />
	  </td>
	</tr>
	<tr>
	<td class="right">
		<?php echo get_lang('subuser_password'); ?>
	  </td>
	   <td class="left">
		   <input name="users_passwd" type="password" size="25" id="users_passwd" />
		   </td>
	</tr>
	<tr>
	<td class="right">
		<?php echo get_lang('confirm_password'); ?>
	  </td>
	   <td class="left">
		   <input name="users_cpasswd" type="password" size="25" id="users_cpasswd" />
		   </td>
	</tr>
	<tr>
	  <td>&nbsp;</td>
	  <td class="left">
		  <input type="submit" name="Submit" value="<?php echo get_lang('create_sub_user'); ?>" /> 
	  </td>
	</tr>
	</table>
	</form>
	<?php
}
?>
