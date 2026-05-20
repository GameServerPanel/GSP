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

require_once(dirname(__FILE__) . '/register_helpers.php');

function exec_ogp_module()
{
	global $db,$view,$settings;
	
	startSession();

	if( isset($_SESSION['ERRMSG_ARR']) && is_array($_SESSION['ERRMSG_ARR']) && count((array)$_SESSION['ERRMSG_ARR']) >0 ) 
		{
		$errmsg = '<table>';
		foreach ((array)$_SESSION['ERRMSG_ARR'] as $msg) 
			{
			$errmsg .= "<tr><td><img width='8px' src='images/offline.png'/></td><td style='text-align:left;color:red;'>".$msg.'</td></tr>'; 
			}
		$errmsg .= '</table><br>';
		unset($_SESSION['ERRMSG_ARR']);
		}
	echo "<h2>".get_lang('user_registration')."</h2>";
	if(isset($errmsg))
	{
		echo $errmsg;
		$input = $_SESSION['INPUT'];
	}
	
	$lang_switch = (isset($_GET['lang']) AND $_GET['lang'] != "-" )? "&lang=".$_GET['lang'] : "";
	?>
	<style>
	.left {
		margin-left:auto;
		margin-right:auto;
		text-align:left;
	}

	.right
	{
		margin-left:auto;
		margin-right:auto;
		text-align:right;
	}
	</style>
	<?php
	require_once('includes/form_table_class.php');
	
	$ft = new FormTable();
    $ft->start_form("?m=register&p=exec$lang_switch", "post", "name=\"loginForm\"");
    $ft->start_table();
	
	$ft->add_field('string','login_name',@$input['users_login']);
	$ft->add_field('password','users_passwd','');
	$ft->add_field('password','users_cpasswd','');
	$ft->add_field('string','users_fname',@$input['users_fname']);
	$ft->add_field('string','users_lname',@$input['users_lname']);
	$ft->add_field('string','users_email',@$input['users_email']);
	$ft->add_field_hidden('users_comment',get_lang_f('registered_on', date("d/m/Y")) );
	echo "<tr><td>&nbsp;</td><td align='right'>";
	
	$recaptcha = register_get_recaptcha_config($settings);
	if($recaptcha['enabled']){
		echo recaptcha_get_html($recaptcha['sitekey']);
		echo '<input type="hidden" id="recaptcha_widget_error" name="recaptcha_widget_error" value="0" />';
		echo '<script type="text/javascript">
		function registerRecaptchaWidgetError() {
			var flag = document.getElementById("recaptcha_widget_error");
			if (flag) flag.value = "1";
		}
		</script>';
		register_log_event('registration_form_captcha_enabled');
	}else{
		echo "<div style='color:#f8e58c;font-size:12px;'>Captcha is temporarily unavailable. Registration is still available.</div>";
		echo '<input type="hidden" id="recaptcha_widget_error" name="recaptcha_widget_error" value="1" />';
		register_log_event('registration_form_captcha_disabled', array('reason' => $recaptcha['reason']));
	}
	echo "</td></tr>";
    $ft->end_table();
    $ft->add_button("submit","Submit",get_lang('register_a_new_user'));
    $ft->end_form();
}

function recaptcha_get_html ($pubkey, $error = null, $use_ssl = false)
{
	if ($pubkey == null || $pubkey == '') {
		return "";
	}

	$errorpart = "";
	if ($error) {
		$errorpart = "&amp;error=" . $error;
	}

	return "<script src='https://www.google.com/recaptcha/api.js' async defer></script><div style='display: inline-block;' class='g-recaptcha' data-sitekey='" . $pubkey . "' data-error-callback='registerRecaptchaWidgetError'></div>";
        
}
?>
