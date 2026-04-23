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

define("SERVER_CONFIG_LOCATION","modules/config_games/server_configs/");
define("XML_SCHEMA","modules/config_games/schema_server_config.xml");

if (!function_exists('ogp_render_config_error')) {
	function ogp_render_config_error($title, $details = "")
	{
		$log_message = "[OGP] $title" . (empty($details) ? "" : " Details: $details");
		error_log($log_message);

		if (PHP_SAPI === 'cli') {
			fwrite(STDERR, $log_message . PHP_EOL);
			exit(1);
		}

		if (!headers_sent()) {
			header('Content-Type: text/html; charset=utf-8', true, 500);
		}

		echo "<div style=\"font-family:Arial,Helvetica,sans-serif;max-width:720px;margin:4rem auto;padding:2rem;border:1px solid #c0392b;border-radius:6px;background:#fff7f7;color:#333;\">";
		echo "<h1 style=\"margin-top:0;color:#c0392b;\">Open Game Panel</h1>";
		echo "<h2 style=\"color:#c0392b;\">$title</h2>";
		if (!empty($details)) {
			echo "<p>" . nl2br(htmlspecialchars($details, ENT_QUOTES, 'UTF-8')) . "</p>";
		}
		echo "<p>Please install the missing PHP requirements or restore the referenced configuration file, then reload this page.</p>";
		echo "</div>";
		exit;
	}
}

if (!function_exists('ogp_render_missing_xml_extensions')) {
	function ogp_render_missing_xml_extensions($missing_extensions)
	{
		$pretty = implode(', ', $missing_extensions);
		$details = "Missing PHP extension(s): {$pretty}\n"
				 . "Install the php-xml package and restart Apache/PHP-FPM.\n"
				 . "Debian/Ubuntu: sudo apt install php-xml\n"
				 . "RHEL/CentOS: sudo dnf install php-xml";
		ogp_render_config_error("Required PHP XML extensions are not available.", $details);
	}
}

if (!function_exists('ogp_format_libxml_errors')) {
	function ogp_format_libxml_errors()
	{
		$errors = libxml_get_errors();
		if (empty($errors)) {
			return "No additional libxml details are available.";
		}
		$messages = array();
		foreach ((array)$errors as $error) {
			$messages[] = trim($error->message) . " (Line {$error->line}, Column {$error->column})";
		}
		libxml_clear_errors();
		return implode("\n", $messages);
	}
}

if (!function_exists('ogp_ensure_xml_support')) {
	function ogp_ensure_xml_support()
	{
		static $checked = false;
		if ($checked) {
			return;
		}

		$missing = array();
		if (!extension_loaded('libxml')) {
			$missing[] = 'libxml';
		}
		if (!class_exists('DOMDocument')) {
			$missing[] = 'dom';
		}
		if (!function_exists('simplexml_load_file')) {
			$missing[] = 'simplexml';
		}

		if (!empty($missing)) {
			ogp_render_missing_xml_extensions($missing);
		}

		$checked = true;
	}
}

/// \return FALSE in case of failure in parsing.
/// \return array containing the elements on success.
function read_server_config( $filename )
{
	ogp_ensure_xml_support();

	if (!is_readable($filename)) {
		ogp_render_config_error(
			"Game configuration file is missing or unreadable.",
			"Expected at: {$filename}"
		);
	}

	$previous_error_state = libxml_use_internal_errors(true);
	$dom = new DOMDocument();
	if ($dom->load($filename) === FALSE)
	{
		ogp_render_config_error(
			"Unable to load XML configuration.",
			"File: {$filename}\n".ogp_format_libxml_errors()
		);
	}

	if ( $dom->schemaValidate(XML_SCHEMA) !== TRUE )
	{
		ogp_render_config_error(
			"XML configuration failed schema validation.",
			"File: {$filename}\nSchema: ".XML_SCHEMA."\n".ogp_format_libxml_errors()
		);
	}

	$xml = simplexml_import_dom($dom);
	if($xml === false){
		ogp_render_config_error(
			"Failed to parse XML configuration.",
			"File: {$filename}\n".ogp_format_libxml_errors()
		);
	}

	$xml->addChild('home_cfg_file',basename($filename));
	libxml_use_internal_errors($previous_error_state);
	return $xml;
}

function xml_get_mod( $server_xml, $mod_key )
{
    foreach ( $server_xml->mods->mod as $xml_mod_tmp )
    {
        if ($xml_mod_tmp['key'] == $mod_key)
        {
            return $xml_mod_tmp;
        }
    }
    return FALSE;
}

?>
