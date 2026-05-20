<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2018 The OGP Development Team
 *
 * http://www.opengamepanel.org/
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 *
 */

//Open Game Panel Free User Registration Add On By
//  MarkDogg18769

function checkEmail($email) {
	if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email))
	{
		return true;
	}
	return false;
}

require_once("includes/functions.php");
require_once(dirname(__FILE__) . '/register_helpers.php');
function exec_ogp_module()
{
	global $db,$view,$settings;
	startSession();
		
	$adminEmailList = '';
	// Get email address of all admins to inform him when a user has registered.
	//DISABLE! ANNOYING
	/*
	$admins = $db->getAdmins();
	if(is_array($admins) && count((array)$admins) > 0){
		foreach ((array)$admins as $admin){
			if($admin['user_receives_emails']){
				$adminEmail = $admin['users_email'];
				$adminEmailList .= $adminEmail . ',';
			}
		}
	}*/
	
	//Array to store validation errors
	$errmsg_arr = array();
	
	//Array to store input values
	$input = array();
	
	//Validation error flag
	$errflag = false;
	
	//Function to sanitize values received from the form. Prevents SQL injection
	function clean($str) {
		$str = @trim($str);
		return $str;
	}
		
	//Sanitize the POST values
	$users_fname = sanitizeInputStr(isset($_POST['users_fname']) ? $_POST['users_fname'] : '');
	$users_lname = sanitizeInputStr(isset($_POST['users_lname']) ? $_POST['users_lname'] : '');
	$users_login = sanitizeInputStr(isset($_POST['login_name']) ? $_POST['login_name'] : '');
	$users_passwd = clean(isset($_POST['users_passwd']) ? $_POST['users_passwd'] : '');
	$users_cpasswd = clean(isset($_POST['users_cpasswd']) ? $_POST['users_cpasswd'] : '');
	$users_email = clean(isset($_POST['users_email']) ? $_POST['users_email'] : '');
	$users_comment = clean(isset($_POST['users_comment']) ? $_POST['users_comment'] : '');
	$gRecaptchaResponse = clean(isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '');
	$recaptcha_widget_error = clean(isset($_POST['recaptcha_widget_error']) ? $_POST['recaptcha_widget_error'] : '0');
	register_log_event('registration_started', array('username' => $users_login, 'email' => $users_email));

	if( !empty($users_fname) ) {
	$input['users_fname'] = $users_fname;
	}
	if( !empty($users_lname) ) {
	$input['users_lname'] = $users_lname;
	}
	if( !empty($users_login) ) {
	$input['users_login'] = $users_login;
	}
	if( !empty($users_email) ) {
	$input['users_email'] = $users_email;
	}
	
	$lang_switch = (isset($_GET['lang']) AND $_GET['lang'] != "-" )? "lang=".$_GET['lang'] : "";
	
	//Input Validations
	if($users_fname == '') {
		$errmsg_arr[] = get_lang('err_first_name');
		$errflag = true;
	}
	if($users_lname == '') {
		$errmsg_arr[] = get_lang('err_last_name');
		$errflag = true;
	}
	if( $users_email == '' OR !checkEmail($users_email) ) {
		$errmsg_arr[] = get_lang('err_email_address');
		$errflag = true;
	}
	elseif($db->getUserByEmail($users_email) != FALSE)
	{
		$user = $db->getUserByEmail($users_email);
		$errmsg_arr[] = get_lang_f('err_email_address_already_in_use_by',$user['users_login']);
		$errflag = true;
	}
	if($users_login == '') {
		$errmsg_arr[] = get_lang('err_login_name');
		$errflag = true;
	}
	elseif($db->getUser($users_login) != FALSE)
	{
		$errmsg_arr[] = get_lang('err_login_name');
		$errflag = true;
		register_log_event('registration_validation_failed', array('reason' => 'duplicate_username', 'username' => $users_login));
	}
	if($users_passwd == '') {
		$errmsg_arr[] = get_lang('err_password');
		$errflag = true;
	}else{
		// 6 char password minimum
		if(strlen($users_passwd) < 6){
			$errmsg_arr[] = get_lang('passwd_len');
			$errflag = true;
		}
	}
	if($users_cpasswd == '') {
		$errmsg_arr[] = get_lang('err_confirm_password');
		$errflag = true;
	}
	
	
	
	if( strcmp($users_passwd, $users_cpasswd) != 0 ) {
		$errmsg_arr[] = get_lang('err_password_mismatch');
		$errflag = true;
	}
	
	$recaptcha = register_get_recaptcha_config($settings);
	if($recaptcha['enabled'] && $recaptcha_widget_error !== '1')
	{
		require_once('includes/classes/recaptcha/autoload.php');
		try {
			$captcha = new \ReCaptcha\ReCaptcha($recaptcha['secretkey']);
			$resp = $captcha->verify($gRecaptchaResponse, isset($_SERVER["REMOTE_ADDR"]) ? $_SERVER["REMOTE_ADDR"] : '');
			if (empty($gRecaptchaResponse) || !$resp->isSuccess())
			{
				$errmsg_arr[] = get_lang('err_captcha');
				$errflag = true;
				register_log_event('registration_captcha_failed', array('username' => $users_login));
			}
			else
			{
				register_log_event('registration_captcha_validated', array('username' => $users_login));
			}
		}
		catch (Exception $e)
		{
			register_log_event('registration_captcha_exception', array('error' => $e->getMessage()));
			register_log_event('registration_captcha_skipped', array('reason' => 'verification_exception'));
		}
	}
	else
	{
		register_log_event('registration_captcha_skipped', array('reason' => $recaptcha['reason'], 'widget_error' => $recaptcha_widget_error));
	}
	
	//Create INSERT query
	if( !$errflag )
	{
		if(!$db->addUser($users_login,$users_passwd,"user",$users_email) )
		{
			$errmsg_arr[] = get_lang('err_login_name');
			$errflag = true;
			register_log_event('registration_db_insert_failed', array('username' => $users_login));
		}
		else
		{
			$user = $db->getUser($users_login);
			$user_id = $user['user_id'];
			register_log_event('registration_db_insert_succeeded', array('username' => $users_login, 'user_id' => $user_id));

			$users_pass_hash = password_hash($users_passwd, PASSWORD_DEFAULT);
			if($users_pass_hash !== false)
			{
				$hash_column = $db->resultQuery("SHOW COLUMNS FROM `OGP_DB_PREFIXusers` LIKE 'users_pass_hash';");
				if($hash_column)
				{
					$db->query("UPDATE `OGP_DB_PREFIXusers` SET `users_pass_hash` = '".$db->realEscapeSingle($users_pass_hash)."' WHERE `user_id` = ".(int)$user_id." LIMIT 1;");
				}
			}

			$fields['users_fname'] = $users_fname;
			$fields['users_lname'] = $users_lname;
			$fields['users_comment'] = $users_comment;
			if(isset($_GET['lang']))
				$fields['users_lang'] = $_GET['lang'];
			else
				$fields['users_lang'] = $settings['panel_language'];
			
			if($db->editUser($fields,$user_id))
			{
				if(isset($adminEmailList) && !empty($adminEmailList)){
					$to = $adminEmailList . $users_email;
				}else{
					$to = $users_email;
				}
				
				if( empty( $settings['panel_name'] ) )
					$subject = get_lang_f('subject',"XP Game Host");
				else
					$subject = get_lang_f('subject',$settings['panel_name']);
				
				$message = "Hello,<br><br>Your XP Game Host account has been created and you may now login</a>.<br><br>Username: " .  $users_login . "<br>Please do not reply to this email!<br><br>Level UP your gaming!!<br>";
				
				$mail = mymail($to, $subject, $message, $settings);
				
				if($mail)
				{
					print_success(get_lang_f('your_account_details_has_been_sent_by_email_to',$users_email));
					$view->refresh("index.php",8);
				}else{
					print_success(get_lang('account_created'));
					$view->refresh("index.php",8);
				}
				register_log_event('registration_completed', array('username' => $users_login, 'user_id' => $user_id));
			}
			else
			{
				$user = $db->getUser($users_login);
				$user_id = $user['user_id'];
				$db->delUser($user_id);
				print_failure('Unable to complete registration details. Please try again.');
				$view->refresh("index.php?m=register&p=form&".$lang_switch,8);
				register_log_event('registration_profile_update_failed', array('username' => $users_login, 'user_id' => $user_id));
			}
		}
	}
	//If there are input validations, redirect back to the registration form
	if($errflag) {
		$_SESSION['ERRMSG_ARR'] = $errmsg_arr;
		$_SESSION['INPUT'] = $input;
		register_log_event('registration_validation_failed', array('username' => $users_login, 'errors' => $errmsg_arr));
		$view->refresh("index.php?m=register&p=form&".$lang_switch,0);
	}
}
?>
