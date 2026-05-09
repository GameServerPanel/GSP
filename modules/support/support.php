<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

if (!defined('GSP_WEBSITE_DOCS_BASE_URL')) {
	define('GSP_WEBSITE_DOCS_BASE_URL', 'https://gameservers.world/docs/');
}

function gsp_support_docs_url_for_game_key($game_key)
{
	$baseDocsUrl = rtrim(GSP_WEBSITE_DOCS_BASE_URL, '/');
	$game_key = trim((string)$game_key);
	if ($game_key !== '') {
		$docPath = __DIR__ . '/../billing/docs/' . $game_key . '/index.php';
		if (is_file($docPath)) {
			return $baseDocsUrl . '/' . rawurlencode($game_key) . '/';
		}
	}
	return $baseDocsUrl . '/';
}

function exec_ogp_module() {

	global $db, $settings;

	$isAdmin = $db->isAdmin($_SESSION['user_id']);
	if ( $isAdmin )
		$server_homes = $db->getIpPorts();
	else
		$server_homes = $db->getIpPortsForUser($_SESSION['user_id']);
		
	$user = $db->getUserById($_SESSION['user_id']);

	if(isset($_POST["submit"])){
		
		$email = isset($_POST["email"]) ? trim($_POST["email"]) : '';
		$gameserver = $_POST['gameserver'];
		$subjectRaw = isset($_POST["subject"]) ? trim($_POST["subject"]) : '';
		$subject = get_lang('support') . ($subjectRaw !== '' ? ": " . $subjectRaw : '');
		$message = isset($_POST["message"]) ? trim($_POST["message"]) : '';

		if ($message === '') {
			$errMsg = get_lang('message_must_be_filled_out');
			$errTitle = get_lang('error');
			echo "<script>$(document).ready(function(){\$('#dialog').html('<p><img src=\"modules/support/images/error.png\">" . htmlspecialchars($errMsg, ENT_QUOTES) . "</p>').attr('title', '" . htmlspecialchars($errTitle, ENT_QUOTES) . "').dialog();});</script>";
		} else {
		
//TICKET SUBMITTED, POST ON DISCORD and log
//logger
	//$db->logger( "SUPPORT TICKET SUBMITTED ");
	$db->logger( "TICKET SUBMITTED by " . $_SESSION['user_id']);


// Post to Discord support webhook (configured in Admin > Settings)
$webhook = !empty($settings['discord_webhook_main']) ? $settings['discord_webhook_main'] : '';
if (!empty($webhook)) {
	$panel_name = !empty($settings['panel_name']) ? $settings['panel_name'] : 'GSP';
	$msg = array(
		'username' => $panel_name,
		'content'  => 'SUPPORT TICKET: [' . htmlspecialchars($subject, ENT_QUOTES) . '] from ' . htmlspecialchars($_SESSION['users_login'] ?? '', ENT_QUOTES),
	);
	discordmsg($msg, $webhook);
}
//end discord

		$content = get_lang_f('support_email_content', $user['users_login'], $email, $gameserver, $message);
		if ($email === '' || mymail($email, $subject, $content, $settings, $user['users_login']) == TRUE)
		{
			?>
			<script type="text/javascript">
			$( document ).ready(function() {
				$('#dialog').html('<p><img src="modules/support/images/info.png" ><?php print_lang('message_has_been_sent'); ?></p>').dialog();
			});
			</script>
			<?php
		}
	} // end else (message not empty)
	} // end if submit
	echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />';
	echo "<h2>".get_lang('support')."</h2>";
	$defaultDocsUrl = GSP_WEBSITE_DOCS_BASE_URL;
	if (!empty($server_homes) && is_array($server_homes)) {
		foreach ((array)$server_homes as $server_home_row) {
			if (!empty($server_home_row['game_key'])) {
				$defaultDocsUrl = gsp_support_docs_url_for_game_key($server_home_row['game_key']);
				break;
			}
		}
	}
	echo "<div style='margin:0 auto 16px auto;max-width:600px;padding:10px 12px;border:1px solid #2d2d2d;border-radius:6px;background:#171717;'>"
		. "<a id='support-doc-link' href='" . htmlspecialchars($defaultDocsUrl, ENT_QUOTES) . "' target='_blank' rel='noopener noreferrer' style='color:#8cb9ff;text-decoration:none;font-weight:600;'>Game Documentation</a>"
		. "<span style='color:#a9a9a9;margin-left:8px;'>Open setup and troubleshooting docs in a new tab.</span>"
		. "</div>";
	echo '
	<div style="background:#5865F2;border-radius:8px;padding:14px 20px;margin:0 auto 20px auto;max-width:600px;display:flex;align-items:center;gap:16px;box-shadow:0 2px 8px rgba(0,0,0,0.18);">
		<i class="fa-brands fa-discord" style="font-size:2.4em;color:#fff;flex-shrink:0;"></i>
		<div style="flex:1;">
			<div style="color:#fff;font-size:1.1em;font-weight:bold;margin-bottom:4px;">Need help faster?</div>
			<div style="color:#dde0ff;font-size:0.95em;">Join our Discord server and post in the support channel for quick assistance from our team and community.</div>
		</div>
		<a href="' . (!empty($settings['discord_invite_url']) ? htmlspecialchars($settings['discord_invite_url'], ENT_QUOTES) : 'https://discord.com') . '" target="_blank"
		   style="background:#fff;color:#5865F2;font-weight:bold;padding:8px 18px;border-radius:6px;text-decoration:none;white-space:nowrap;font-size:0.97em;flex-shrink:0;">
			<i class="fa-brands fa-discord"></i> Join Discord
		</a>
	</div>';
	echo '<center><form class="contactForm" name="contactForm" action="" method="post"><p style="font-size:12px;text-align:center;">'.get_lang('please_describe_your_issue_below').'</p>';
	echo get_lang('select_server').":<br /><select name='gameserver' id='gameserver'>";
	foreach ((array)$server_homes as $server_home)
	{
		$docUrl = gsp_support_docs_url_for_game_key($server_home['game_key'] ?? '');
		echo "<option value='".htmlspecialchars($server_home['home_name'], ENT_QUOTES)."' data-doc-url='".htmlspecialchars($docUrl, ENT_QUOTES)."'>".htmlspecialchars($server_home['home_name'], ENT_QUOTES)."</option>";
	}
	echo "</select><br /><br />";
		
	if(!isset($user['users_email']) or $user['users_email'] == "")
	{
		echo get_lang('email_address').' <em>('.get_lang('optional').')</em>:
			<br />
			<input type="text" name="email" id="email" style="width: 250px;" />
			<br />
			<br />';
	}
	else
	{
		echo '<input type="hidden" name="email" id="email" value="'.$user['users_email'].'" />';
	}
	
	echo get_lang('subject').' <em>('.get_lang('optional').')</em>:
	<br />
	<input type="text" name="subject" id="subject" style="width: 250px;" />
	<br />
	<br />
	'.get_lang('message').':
	<br />
	<textarea name="message" id="message" style="width:400px; height:200px;"></textarea>
	<br />
	<br />
	<input type="submit" name="submit" value="'.get_lang('send').'" style="width:100px; height:30px; font-size:18px;" onclick="return validateForm()" />
	</form></center><br><br>';
	echo '<div id="dialog" title="'.get_lang('info').'"></div>';
	?>
	<script type="text/javascript">
	function validateForm()
	{
		var $message=document.forms["contactForm"]["message"].value;
		if ($message==null || $message=="")
		{
			$('#dialog').html('<p><img src="modules/support/images/error.png" ><?php print_lang('message_must_be_filled_out'); ?></p>').attr('title', '<?php print_lang('error'); ?>').dialog();
			return false;
		}
	}
	$(document).ready(function(){
		function updateSupportDocLink(){
			var selected = $('#gameserver option:selected');
			var url = selected.data('doc-url') || '<?php echo GSP_WEBSITE_DOCS_BASE_URL; ?>';
			$('#support-doc-link').attr('href', url);
		}
		$('#gameserver').on('change', updateSupportDocLink);
		updateSupportDocLink();
	});
	</script>
	<?php 
} // End function
