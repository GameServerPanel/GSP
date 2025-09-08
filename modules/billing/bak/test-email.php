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

chdir(realpath(dirname(__FILE__))); /* Change to the current file path */
chdir("../.."); /* Base path to ogp web files */
// Report all PHP errors
error_reporting(E_ALL);
// Path definitions
define("CONFIG_FILE","includes/config.inc.php");
//Requiere
require_once("includes/functions.php");
require_once("includes/helpers.php");
require_once("includes/html_functions.php");
require_once("modules/config_games/server_config_parser.php");
require_once("includes/lib_remote.php");
require_once CONFIG_FILE;
// Connect to the database server and select database.
$db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix);

$panel_settings = $db->getSettings();
if( isset($panel_settings['time_zone']) && $panel_settings['time_zone'] != "" )
        date_default_timezone_set($panel_settings['time_zone']);


//these dates are configured in the Shop Settings page
$today=time();
$invoice_date = strtotime('+ 7 days'); //this many days until the finish_date
$suspend_date = $today; //suspend when overdue
$removal_date = strtotime('+ 7 days'); //finish_date is passed 7 days ago
$rundate = date('d/M/y G:i',$today);


//THESE SERVERS HAVE REACHED THE DATE FOR INVOICE, FINISH_DATE - 7 (OR WHAT IS IN SETTINGS)
//SET STATUS -1 MEANING INVOICED
//LOOP THROUGH ALL SERVERS WITH STATUS = 1 (ACTIVE) -----------------------------------------------------------
					$settings = $db->getSettings();
					$subject = "Test Email";
				    $emailto = "iaretechnician@gmail.com";
				    $message = "WooHoo<br><br><br>Email Works<br>Thanks!<br>";
				    $mail = mymail($emailto, $subject, $message, $settings);


				// END EMAIL 
				
				
