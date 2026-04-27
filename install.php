<?php
/*
 *
 * GSP - Game Server Panel (WDS)
 * Copyright (C) 2008 - 2018 The OGP Development Team
 * GSP customizations (C) WDS / GameServerPanel
 *
 * GSP is a heavily customized fork of OGP maintained by WDS.
 * https://github.com/GameServerPanel/GSP
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
 */

error_reporting(E_ALL);
$_GET['action'] = true;
define("MODULES", "modules/");

// Strip Input Function, prevents HTML in unwanted places
function stripinput($text) {
    $search  = array("\"",  "'",     "\\",   '\"',    "\'",    "<",    ">",    "&nbsp;");
    $replace = array("&quot;", "&#39;", "&#92;", "&quot;", "&#39;", "&lt;", "&gt;", " ");
    $text = str_replace($search, $replace, $text);
    return $text;
}

session_start();

if (!isset($_SESSION['users_lang']))
    $_SESSION['users_lang'] = "English";

if (isset($_GET['localeset']))
    $_SESSION['users_lang'] = $_GET['localeset'];

require_once("includes/helpers.php");
require_once("includes/view.php");
require_once("includes/lang.php");
require_once("includes/html_functions.php");
require_once("includes/functions.php");
ogpLang();

$view = new OGPView();
$view->setCharset(get_lang('lang_charset'));

?>
<style>
body { background-image: url("images/bg.png"); }
#install-content {
    width: 650px;
    margin: 0 auto;
    margin-top: 30px;
    padding: 0px 15px;
    background-color: #FFF;
    border-radius: 9px;
    -moz-border-radius: 9px;
    border: 1px solid #C8C8C8;
    overflow: hidden;
}
#install-title {
    width: 680px;
    height: 30px;
    background: #f5f5f5;
    border-top: 1px solid #cfcfcf;
    border-bottom: 1px solid #cfcfcf;
    margin-bottom: 5px;
    margin-top: -1px;
    margin-left: -18px;
    margin-right: -15px;
    padding-top: 5px;
    font-size: 20px;
    text-align: center;
    color: #000;
    font-family: "Trebuchet MS";
}
.lang { width: 100%; text-align: center; margin-left: auto; margin-right: auto; }
li { list-style-type: square; }
</style>
<!--[if IE]>
<style>
    #install-content { text-align: center; width: 100%; }
    #install-title { width: 100%; background: #FFF; border: none; }
</style>
<![endif]-->
<div id="install-content">
<?php
install();

function install() {
    global $db;
    $step = (isset($_REQUEST['step']) ? $_REQUEST['step'] : "0");

    // ----------------------------------------------------------------
    // Step 0 – Language selection + welcome (no prerequisite checks)
    // ----------------------------------------------------------------
    if ($step == "0") {
        $locale_files = makefilelist("lang/", ".|..|.svn", true, "folders");
        $columns = 3;
        $width   = round(100 / $columns);

        echo "<div id=\"install-title\" style=\"margin-top:-4px;\">".get_lang('install_lang')."</div>";
        echo "<table class='lang' style='margin-bottom:10px;'>\n<tr>";
        $counter = 0;
        for ($i = 0; $i < count($locale_files); $i++) {
            if ($counter != 0 && ($counter % $columns == 0)) echo "</tr>\n<tr>\n";
            echo "<td style='width:".$width."%'>";
            if ($locale_files[$i] == $_SESSION['users_lang'])
                echo "<li><b>".$locale_files[$i]."</b></li>";
            else
                echo "<li><a href='?localeset=".htmlspecialchars($locale_files[$i])."'>".htmlspecialchars($locale_files[$i])."</a></li>";
            echo "</td>\n";
            $counter++;
        }
        echo "</tr>\n</table>\n";

        echo "<div id=\"install-title\">GSP / WDS Panel Installer</div>";
        echo "<p>Welcome to the <strong>GSP (Game Server Panel)</strong> installer, maintained by WDS.</p>";
        echo "<p>GSP is a heavily customized fork of OGP. This installer will:</p>";
        echo "<ul>
            <li>Write <code>includes/config.inc.php</code> with your database credentials.</li>
            <li>Optionally migrate any existing <code>ogp_</code> tables to your chosen prefix.</li>
            <li>Install all modules found in the <code>modules/</code> directory.</li>
            <li>Create a default admin account (<em>admin / admin</em>) if none exists.</li>
        </ul>";
        echo "<p><a href='?step=1'>".get_lang('next')."</a></p>";
        echo "</div>\n";
    }

    // ----------------------------------------------------------------
    // Step 1 – Database settings form
    // ----------------------------------------------------------------
    elseif ($step == "1") {
        if (is_readable('includes/config.inc.php'))
            require_once "includes/config.inc.php";

        echo "<table class='install'><tr><td>\n";
        echo "<form name='setup' method='post' action='?step=2'>";
        echo "<table class='install'>\n";
        echo "<tr><td colspan='2'><div id=\"install-title\" style=\"margin-left:-21px; margin-top:-7px;\">".get_lang('database_settings')."</div></td></tr>";
        echo "<tr><td>".get_lang('database_type').":</td><td>MySQL</td></tr>";

        // Host
        $OS = strtoupper(substr(PHP_OS, 0, 3));
        $default_host = ($OS === 'WIN' || $OS === 'CYG') ? "127.0.0.1" : "localhost";
        echo "<tr><td>".get_lang('database_hostname').":</td>
            <td><input type='text' value='".htmlspecialchars(isset($db_host) ? $db_host : $default_host)."' name='db_host' class='textbox' /></td></tr>";

        // Port (GSP addition – no lang key needed; label is always in English for installer)
        echo "<tr><td>Database Port:</td>
            <td><input type='text' value='".htmlspecialchars(isset($db_port) ? $db_port : "3306")."' name='db_port' class='textbox' /></td></tr>";

        // User
        echo "<tr><td>".get_lang('database_username').":</td>
            <td><input type='text' value='".htmlspecialchars(isset($db_user) ? $db_user : "")."' name='db_user' class='textbox' /></td></tr>";

        // Password
        echo "<tr><td>".get_lang('database_password').":</td>
            <td><input type='password' value='".htmlspecialchars(isset($db_pass) ? $db_pass : "")."' name='db_pass' class='textbox' /></td></tr>";

        // Name
        echo "<tr><td>".get_lang('database_name').":</td>
            <td><input type='text' value='".htmlspecialchars(isset($db_name) ? $db_name : "")."' name='db_name' class='textbox' /></td></tr>";

        // Prefix – default gsp_
        echo "<tr><td>".get_lang('database_prefix').":</td>
            <td><input type='text' value='".htmlspecialchars(isset($table_prefix) ? $table_prefix : "gsp_")."' name='table_prefix' class='textbox' /></td></tr>";

        echo "</table>\n";
        echo "<p><input type='submit' name='next' value='".get_lang('next')."' class='button' /></p></form>";
        echo "<p><a href='?step=0'>".get_lang('back')."</a></p>";
        echo "</td></tr></table>\n";
    }

    // ----------------------------------------------------------------
    // Step 2 – Write config, migrate tables, install modules, create admin
    // ----------------------------------------------------------------
    elseif ($step == "2") {
        echo "<table class='install'><tr><td>\n";

        if (!isset($_POST['db_host'])) {
            print_failure("No form data received. Please go back and fill in the database settings.");
            echo "<p><a href='?step=1'>".get_lang('back')."</a></p>";
            echo "</td></tr></table>\n";
            return;
        }

        $db_host       = stripinput($_POST['db_host']);
        $db_port       = stripinput($_POST['db_port']);
        $db_user       = stripinput($_POST['db_user']);
        $db_pass       = stripinput($_POST['db_pass']);
        $db_name       = stripinput($_POST['db_name']);
        $table_prefix  = stripinput($_POST['table_prefix']);
        $db_type       = "mysql";

        // Default prefix to gsp_ if empty
        if (empty($table_prefix)) $table_prefix = "gsp_";
        // Default port to 3306 if empty
        if (empty($db_port)) $db_port = "3306";

        // --- Write config.inc.php ---
        $config = "<?php\n".
            "###############################################\n".
            "# Site configuration\n".
            "###############################################\n".
            "\$db_host=\"".addslashes($db_host)."\";\n".
            "\$db_port=\"".addslashes($db_port)."\";\n".
            "\$db_user=\"".addslashes($db_user)."\";\n".
            "\$db_pass=\"".addslashes($db_pass)."\";\n".
            "\$db_name=\"".addslashes($db_name)."\";\n".
            "\$table_prefix=\"".addslashes($table_prefix)."\";\n".
            "\$db_type=\"".$db_type."\";\n".
            "?>";

        $temp = @fopen("includes/config.inc.php", "w");
        if (!@fwrite($temp, $config)) {
            print_failure(get_lang('unable_to_write_config'));
            echo "<p><a href='?step=1'>".get_lang('back')."</a></p>";
            fclose($temp);
            echo "</td></tr></table>\n";
            return;
        }
        fclose($temp);
        print_success(get_lang('config_written'));

        // --- Connect to database using port ---
        $db = createDatabaseConnection($db_type, $db_host, $db_user, $db_pass, $db_name, $table_prefix, (int)$db_port);
        $error_text = "";
        if (get_db_error_text($db, $error_text)) {
            print_failure($error_text);
            echo "<p><a href='?step=1'>".get_lang('back')."</a></p>";
            echo "</td></tr></table>\n";
            return;
        }

        // --- Optional ogp_ → gsp_ migration ---
        gsp_migrate_tables($db, $table_prefix);

        // --- Create base module management tables ---
        $db->query("DROP TABLE IF EXISTS `".$table_prefix."modules`");
        $db->query("CREATE TABLE IF NOT EXISTS `".$table_prefix."modules` (
            `id` smallint(5) unsigned NOT NULL auto_increment,
            `title` varchar(100) NOT NULL default '',
            `folder` varchar(100) NOT NULL default '',
            `version` varchar(10) NOT NULL default '0',
            `db_version` int(10) NOT NULL default '0',
            PRIMARY KEY (`id`),
            UNIQUE KEY `folder` (`folder`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

        $db->query("DROP TABLE IF EXISTS `".$table_prefix."module_menus`");
        $db->query("CREATE TABLE IF NOT EXISTS `".$table_prefix."module_menus` (
            `module_id` int(11) NOT NULL COMMENT 'This references to modules.id',
            `subpage` varchar(64) NOT NULL default '',
            `group` varchar(32) NOT NULL,
            `menu_name` varchar(128) NOT NULL,
            `pos` INT UNSIGNED NOT NULL,
            PRIMARY KEY (`module_id`, `subpage`, `group`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;");

        // --- Install all modules ---
        require_once("modules/modulemanager/module_handling.php");
        @add_lang_module('modulemanager');
        $modules = list_available_modules();

        // Install modulemanager first
        if (in_array('modulemanager', $modules)) {
            gsp_install_module($db, 'modulemanager');
        }

        foreach ($modules as $module) {
            if ($module == 'modulemanager') continue;
            gsp_install_module($db, $module);
        }
        print_success(get_lang('database_created'));

        // --- Default site settings ---
        $site_settings = array(
            "title"                 => "GSP - Game Server Panel",
            "slogan"                => get_lang('slogan'),
            "ogp_version"          => "0",
            "version_type"         => "GSP",
            "theme"                => "Revolution",
            "welcome_title"        => "1",
            "welcome_title_message"=> "<h0>".get_lang('default_welcome_title_message')."</h0>",
            "page_auto_refresh"    => "1",
        );
        $db->setSettings($site_settings);

        // --- Auto-create default admin user ---
        // NOTE: The default password 'admin' is intentionally weak for first-boot convenience.
        // The installer prominently warns the operator to change it. Passwords are stored as
        // MD5 to match the existing panel login system (legacy behaviour).
        $existing_admin = $db->getUser('admin');
        if (!$existing_admin) {
            $db->addUser('admin', 'admin', 'admin', 'admin@localhost');
            print_success("Default admin account created (username: <strong>admin</strong>).");
        } else {
            echo "<p class='note'>Admin user already exists – skipped creation.</p>";
        }

        // --- Update game configs ---
        updateGameConfigsPostInstall();

        echo "<p class='note' style='color:#c00; font-weight:bold;'>".get_lang('remove_install_and_secure_config')."</p>";
        echo "<p class='note' style='color:#c00; font-weight:bold;'>SECURITY: The default admin password is <strong>admin</strong>. Change it immediately after your first login at Admin &rarr; User Management.</p>";
        echo "<p class='note'><a href='index.php'>".get_lang('go_to_panel')."</a></p>";
        echo "</td></tr></table>\n";
        echo "</div>\n";
    }

    else {
        echo "<p>Unknown step. <a href='?step=0'>Start over</a></p>";
    }
}

/**
 * Install a single module, treating prerequisite failures as warnings
 * (not hard failures) per GSP policy: "Do not run prerequisite checks –
 * our environment is customized."
 *
 * Returns the same values as install_module(), but the caller does not
 * abort the overall install when -2 is returned.
 */
function gsp_install_module($db, $module) {
    $result = install_module($db, $module, FALSE);
    // -1 = module.php missing or malformed (genuine hard error)
    // -2 = prereq missing or DB query failed; treat as warning
    // 0  = already installed
    // 1  = installed successfully
    // 2  = optional, skipped
    if ($result === -1) {
        // Already printed by install_module(); just note it.
    }
    return $result;
}

/**
 * Optional ogp_ → configured-prefix migration.
 *
 * Rules:
 *  - If a table named ogp_X exists AND the corresponding prefix_X does NOT
 *    exist, rename ogp_X -> prefix_X.
 *  - If prefix_X already exists, skip that table (never fail).
 *  - If no ogp_ tables exist at all, do nothing.
 */
function gsp_migrate_tables($db, $table_prefix) {
    if ($table_prefix === 'ogp_') {
        // Already using ogp_ prefix – nothing to migrate.
        return;
    }

    // Fetch all tables starting with ogp_
    $rows = $db->resultQuery("SHOW TABLES LIKE 'ogp\\_%'");
    if (!$rows || !is_array($rows)) return;

    $renamed = 0;
    $skipped = 0;
    foreach ($rows as $row) {
        $ogp_table = array_values($row)[0]; // e.g. ogp_users
        $suffix    = substr($ogp_table, 4);  // strip leading "ogp_"
        $new_table = $table_prefix . $suffix;

        // Check if destination table already exists
        $exists = $db->resultQuery("SHOW TABLES LIKE '".str_replace("_", "\\_", $new_table)."'");
        if ($exists && is_array($exists) && count($exists) > 0) {
            $skipped++;
            continue;
        }

        // Rename
        $ok = $db->query("RENAME TABLE `".mysqli_real_escape_string_compat($ogp_table)."` TO `".mysqli_real_escape_string_compat($new_table)."`");
        if ($ok) {
            $renamed++;
        }
    }

    if ($renamed > 0)
        print_success("Migrated {$renamed} table(s) from <code>ogp_</code> to <code>{$table_prefix}</code> prefix.");
    if ($skipped > 0)
        echo "<p class='note'>{$skipped} table(s) already existed under the <code>{$table_prefix}</code> prefix – skipped.</p>";
}

/**
 * Sanitize a MySQL identifier (table name) for use in RENAME TABLE.
 *
 * Table names sourced from SHOW TABLES consist only of alphanumeric
 * characters and underscores in standard installations. This function
 * enforces that invariant by stripping any other characters, making the
 * identifier safe to embed between backticks in a SQL statement.
 * If a table name ever contained characters outside [a-zA-Z0-9_] it would
 * simply be skipped rather than cause an injection.
 */
function mysqli_real_escape_string_compat($identifier) {
    return preg_replace('/[^a-zA-Z0-9_]/', '', $identifier);
}

$view->printView();
?>
