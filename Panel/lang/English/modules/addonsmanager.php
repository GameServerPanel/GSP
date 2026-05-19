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

// --- Server Content Manager (formerly Addons Manager) ---
// UI labels are updated to use "Server Content" terminology.
// Internal keys remain unchanged for backward compatibility with other languages.

define('LANG_install_plugin', "Install Plugin / Mod");
define('LANG_install_mappack', "Install Map Pack");
define('LANG_install_config', "Install Config Pack");
define('LANG_game_name', "Game Name");
define('LANG_directory', "Directory Path");
define('LANG_remote_server', "Remote server");
define('LANG_select_addon', "Select Server Content Item");
define('LANG_install', "Install");
define('LANG_failed_to_start_file_download', "Failed to start file download.");
define('LANG_no_games_servers_available', "There are no game servers available in your account.");
define('LANG_addon_installed_successfully', "Server content item installed successfully");
define('LANG_path', "Path");
define('LANG_wait_while_decompressing', "Wait while the file %s is decompressed.");
define('LANG_addon_name', "Content Item Name");
define('LANG_url', "URL");
define('LANG_select_game_type', "Select Game Type");
define('LANG_plugin', "Downloadable Mod");
define('LANG_mappack', "Steam Workshop Item");
define('LANG_config', "Configuration Package");
if (!defined('LANG_version')) {
	define('LANG_version', "Version");
}
define('LANG_server_content_version', "Server Versions");
define('LANG_modpack', "Modpacks");
define('LANG_workshop', "Workshop Content");
define('LANG_script', "Scripted Installer");
define('LANG_profile', "Server Profiles");
define('LANG_type', "Content Type");
define('LANG_game', "Game");
define('LANG_show_all_addons', "Show All Server Content");
define('LANG_show_addons_for_selected_type', "Show Content For Selected Type");
define('LANG_show_addons_for_selected_game', "Show Content For Selected Game");
define('LANG_linux_games', "Linux Games:");
define('LANG_windows_games', "Windows Games:");
define('LANG_create_addon', "Create Server Content Item");
define('LANG_addons_db', "Server Content Database");
define('LANG_addon_has_been_created', "The server content item \"%s\" has been created.");
define('LANG_remove_addon', "Remove");
define('LANG_fill_the_url_address_to_a_compressed_file', "Please enter a download URL.");
define('LANG_fill_the_download_url', "Please enter a download URL.");
define('LANG_fill_the_workshop_id', "Please enter a Workshop ID.");
define('LANG_fill_the_target_install_path', "Please enter the config target and edit action.");
define('LANG_fill_the_script_action_body', "Please enter the installer script/action.");
define('LANG_fill_the_config_edit_rule', "Please enter the config target and edit action.");
define('LANG_fill_the_addon_name', "Please enter a name for the server content item.");
define('LANG_select_an_addon_type', "Please select a content type.");
define('LANG_select_a_game_type', "Please select a game type.");
define('LANG_select_a_content_type', "Please select a content type.");
define('LANG_edit_addon', "Edit");
define('LANG_invalid_addon', "Invalid server content item or access denied.");
define('LANG_invalid_addon_type', "Invalid content type selected.");
define('LANG_post-script', "Post-install script (bash)");
define('LANG_replacements', "Replacements:");
define('LANG_addon_name_info', "Enter a display name for this server content item.");
define('LANG_url_info', "Enter a download URL for a compressed file (.zip or .tar.gz). It will be extracted into the server root or the path specified below.");
define('LANG_path_info', "Path relative to the server folder, with no leading or trailing slashes (e.g. cstrike/cfg). Leave blank to use the server root.");
define('LANG_post-script_info', "Enter a Bash script to run after installation. Use the replacement variables listed on the left to inject server-specific values. The script runs from the server root or the specified path.");
define('LANG_show_to_group', "Show to group");
define('LANG_all_groups', "All groups");
define('LANG_show_addons_for_selected_group', "Show content for selected group");
define('LANG_group', "Group");
define('LANG_content_type', "Content Type");
define('LANG_workshop_id', "Workshop ID");
define('LANG_target_path_template', "Target Path");
define('LANG_optional_folder_name', "Optional Folder Name");
define('LANG_config_edit_rule', "Config Edit Rule");
define('LANG_launch_param_additions', "Launch Parameter Additions");
define('LANG_content_type_help_download_zip', "Download and extract a ZIP, RAR, or archive file.");
define('LANG_content_type_help_steam_workshop', "Install a Steam Workshop mod using Workshop ID.");
define('LANG_content_type_help_post_script', "Run a custom scripted installation process.");
define('LANG_content_type_help_config_edit', "Install configuration files, profiles, or templates.");
?>
