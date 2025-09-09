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

// Module general information
$module_title = "billing";
$module_version = "1.0";
$db_version = 0;
$module_required = FALSE;
$module_menus = array(
    array( 'subpage' => 'orders', 'name'=>'Orders', 'group'=>'user,admin' ),
    array( 'subpage' => 'services', 'name'=>'Services', 'group'=>'admin' ),
    array( 'subpage' => 'shop_settings', 'name'=>'Shop Settings', 'group'=>'admin' ),
    array( 'subpage' => 'coupons', 'name'=>'Coupons', 'group'=>'admin' )
);

?>
