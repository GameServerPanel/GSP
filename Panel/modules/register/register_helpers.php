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

function register_log_path()
{
	$log_dir = dirname(__FILE__) . '/logs';
	if (!is_dir($log_dir)) {
		@mkdir($log_dir, 0775, true);
	}
	return $log_dir . '/register.log';
}

function register_log_event($event, $context = array())
{
	$line = date('Y-m-d H:i:s') . ' | ' . $event;
	if (!empty($context)) {
		$line .= ' | ' . json_encode($context);
	}
	$line .= PHP_EOL;
	@file_put_contents(register_log_path(), $line, FILE_APPEND | LOCK_EX);
}

function register_get_recaptcha_config($settings)
{
	$sitekey = isset($settings['recaptcha_site_key']) ? trim($settings['recaptcha_site_key']) : '';
	$secretkey = isset($settings['recaptcha_secret_key']) ? trim($settings['recaptcha_secret_key']) : '';

	if ($sitekey === '' || $secretkey === '') {
		return array(
			'enabled' => false,
			'sitekey' => '',
			'secretkey' => '',
			'reason' => 'missing_settings'
		);
	}

	// Legacy demo keys are frequently left in place and cause "Invalid site key".
	if ($sitekey === '6Lc4osYSAAAAAHtYbHvsXIl0h1auXeiqPhagTXAj' && $secretkey === '6Lc4osYSAAAAAHK56NE9ZHLgw3ZuESHhF26bMoNx') {
		return array(
			'enabled' => false,
			'sitekey' => '',
			'secretkey' => '',
			'reason' => 'legacy_demo_keys'
		);
	}

	return array(
		'enabled' => true,
		'sitekey' => $sitekey,
		'secretkey' => $secretkey,
		'reason' => 'configured'
	);
}
?>
