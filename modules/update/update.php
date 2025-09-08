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

 // todo, make checking and updating functions for updateing on the background.
 // todo, more specified updates in smaller packages
function exec_ogp_module()
{
        global $db, $settings;
        define('REPONAME', 'OGP-Website');

        if ($_SESSION['users_group'] != "admin")
        {
                print_failure(get_lang('no_access'));
                return;
        }
echo "To update the panel, visit our git at http://git.iaregamer.com:3000, download the panel and replace your files.";

}
