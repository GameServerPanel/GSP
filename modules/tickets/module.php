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

$module_title = "Tickets";
$module_version = "1.0";
$db_version = 0;
$module_required = false;
$module_menus = array(
					array(
						'name'		=>	'Support Tickets',
						'group'		=>	'user',
					),

					array(
						'name'		=>	'Support Ticket Settings',
						'group'		=>	'admin',
						'subpage'	=>	'ticket_settings',
					),
				);

?>
