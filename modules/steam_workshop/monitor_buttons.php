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

if (isset($server_xml->installer) && $server_xml->installer === "steamcmd")
{
	$homeId = isset($server_home['home_id']) ? (int)$server_home['home_id'] : 0;
	if ($homeId > 0)
	{
		$label = get_lang('steam_workshop');
		if ($label === 'steam_workshop')
		{
			$label = 'Steam Workshop';
		}
		$href = "?m=steam_workshop&p=main&action=edit&home_id=" . $homeId;
		$module_buttons = array(
			"<a class='monitorbutton' href='" . $href . "'>
				<img src='" . check_theme_image("images/steam_workshop.png") . "' title='" . $label . "'>
				<span>" . $label . "</span>
			</a>"
		);
	}
	else
	{
		$module_buttons = array();
	}
}
else
{
	$module_buttons = array();
}
?>