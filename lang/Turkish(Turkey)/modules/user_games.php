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

define('LANG_add_mods_note', "Kullanıcıya sunucu ekledikten sonra mod eklemeniz gerekir. Bu, sunucunun düzenlenmesiyle yapılabilir.");
define('LANG_game_servers', "Oyun Sunucuları");
define('LANG_game_path', "Oyun Yolu");
define('LANG_game_path_info', "Mutlak bir sunucu yolu. Örnek: /home /ogpbot/OGP_User_Files /My_Server");
define('LANG_game_server_name_info', "Sunucu adı, kullanıcıların sunucularını tanımlamasına yardımcı olur.");
define('LANG_control_password', "Şifre Kontrol");
define('LANG_control_password_info', "Bu şifre RCON şifresi gibi sunucu kontrolü için kullanılır. Şifre boş ise, başka araçlar kullanılır.");
define('LANG_add_game_home', "Oyun sunucusu ekle");
define('LANG_game_path_empty', "Oyun yolu boş bırakılamaz.");
define('LANG_game_home_added', "Oyun sunucusu başarıyla eklendi. Ana sayfa düzenleme sayfasına yönlendiriliyor.");
define('LANG_failed_to_add_home_to_db', "Veritabanına ev eklenemedi. Hata: %s");
define('LANG_caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms', "<b>Caution!</b> The Agent is offline, can not get OS type and architecture,<br> Showing servers for all platforms:");
define('LANG_select_remote_server', "Uzak Sunucuyu Seçin");
define('LANG_no_remote_servers_configured', "No remote servers configured to the Open Game Panel.<br>You need to add remote servers before you can add servers for the users.");
define('LANG_no_game_configurations_found', "No game configuration found. You need to add game configurations from the");
define('LANG_game_configurations', ">game configuration page");
define('LANG_add_remote_server', "Oyun sunucusu ekle");
define('LANG_wine_games', "Wine Games");
define('LANG_home_path', "Oyun Konumu");
define('LANG_change_home_info', "The location of the installed game server. Example: /home/ogp/my_server/");
define('LANG_game_server_name', "Sunucu adı");
define('LANG_change_name_info', "The name of the server to help users to identify it.");
define('LANG_game_control_password', "Sunucu şifre");
define('LANG_change_control_password_info', "Control password is for example rcon password.");
define('LANG_available_mods', "Available mods");
define('LANG_note_no_mods', "No mod(s) available for this game.");
define('LANG_change_home', "Değiştir");
define('LANG_change_control_password', "Sunucu şifresini değiştir");
define('LANG_change_name', "Sunucu adını değiştir");
define('LANG_add_mod', "Eklenti ekle");
define('LANG_set_ip', "IP adresini ayarla");
define('LANG_ips_and_ports', "IP adresi ve Port");
define('LANG_mod_name', "Eklenti Adı");
define('LANG_max_players', "Maksimum Oyuncu");
define('LANG_extra_cmd_line_args', "Extra Command Line Args");
define('LANG_extra_cmd_line_info', "The Extra command line args provides a way to enter extra arguments to the game command line when it is started.");
define('LANG_cpu_affinity', "CPU Affinity");
define('LANG_nice_level', "Nice Level");
define('LANG_set_options', "Set Options");
define('LANG_remove_mod', "Eklenti Kaldır");
define('LANG_mods', "Mods");
define('LANG_ip', "IP");
define('LANG_port', "Port");
define('LANG_no_ip_ports_assigned', "At least one IP:Port pair must be assigned to the home.");
define('LANG_successfully_assigned_ip_port', "Successfully assigned IP:Port pair to home.");
define('LANG_port_range_error', "Port needs to be between range 0 and 65535.");
define('LANG_failed_to_assing_mod_to_home', "Failed to assing mod with id %d to home.");
define('LANG_successfully_assigned_mod_to_home', "Successfully assigned mod with id %d to home.");
define('LANG_successfully_modified_mod', "Successfully modified mod information.");
define('LANG_back_to_game_monitor', "Back to Game Monitor");
define('LANG_back_to_game_servers', "Oyun Sunucuları");
define('LANG_user_id_main', "Main owner");
define('LANG_change_user_id_main', "Change main owner");
define('LANG_change_user_id_main_info', "The main server home owner.");
define('LANG_server_ftp_password', "FTP Şifresi");
define('LANG_change_ftp_password', "FTP Şifresini değiştir");
define('LANG_change_ftp_password_info', "This is the password to login to FTP server for this home.");
define('LANG_Delete_old_user_assigned_homes', "Unassign home to current users.");
define('LANG_editing_home_called', "Editing home called");
define('LANG_control_password_updated_successfully', "Control password updated successfully.");
define('LANG_control_password_update_failed', "Control password update failed");
define('LANG_successfully_changed_game_server', "Successfully changed game server.");
define('LANG_error_ocurred_on_remote_server', "Error ocurred on remote server,");
define('LANG_ftp_password_can_not_be_changed', "FTP Şifresin değiştirilemez.");
define('LANG_ftp_can_not_be_switched_on', "FTP can not be switched ON.");
define('LANG_ftp_can_not_be_switched_off', "FTP can not be switched OFF.");
define('LANG_invalid_home_id_entered', "Invalid home id entered.");
define('LANG_ip_port_already_in_use', "The %s:%s is already in use. Choose another one.");
define('LANG_successfully_assigned_ip_port_to_server_id', "Successfully assigned %s:%s to home with ID %s.");
define('LANG_no_ip_addresses_configured', "Your game server does not have any IP-addresses configured to it. You can configure them from ");
define('LANG_server_page', "sunucu sayfası");
define('LANG_successfully_removed_mod', "Successfully removed game mod.");
define('LANG_warning_agent_offline_defaulting_CPU_count_to_1', "Warning - Agent offline, defaulting CPU count to 1.");
define('LANG_mod_install_cmds', "Mod Install CMDs");
define('LANG_cmds_for', "Commands for");
define('LANG_preinstall_cmds', "Preinstall Commands");
define('LANG_postinstall_cmds', "Postinstall Commands");
define('LANG_edit_preinstall_cmds', "Edit Preinstall Commands");
define('LANG_edit_postinstall_cmds', "Edit Postinstall Commands");
define('LANG_save_as_default_for_this_mod', "Save as default for this mod");
define('LANG_empty', "boş");
define('LANG_master_server_for_clon_update', "Master server for local update");
define('LANG_set_as_master_server', "Set as master server");
define('LANG_set_as_master_server_for_local_clon_update', "Set as master server for local update.");
define('LANG_only_available_for', "Only available for '%s' hosted on the remote server named '%s'.");
define('LANG_ftp_on', "FTP Aktif");
define('LANG_ftp_off', "FTP Devredışı");
define('LANG_change_ftp_account_status', "Change FTP account status");
define('LANG_change_ftp_account_status_info', "Once a FTP account is enabled or disabled, it is added or removed from the FTP's database.");
define('LANG_server_ftp_login', "Server FTP Login");
define('LANG_change_ftp_login_info', "Change the FTP Login with a customized one.");
define('LANG_change_ftp_login', "Change FTP Login");
define('LANG_ftp_login_can_not_be_changed', "Can not change FTP Login.");
define('LANG_server_is_running_change_addresses_not_available', "The server is actually running, the IP cannot be changed.");
define('LANG_change_game_type', "Change Game Type");
define('LANG_change_game_type_info', "By changing the game type the current the mods configuration will be deleted.");
define('LANG_force_mod_on_this_address', "Force mod on this address");
define('LANG_successfully_assigned_mod_to_address', "Successfully assigned mod to address");
define('LANG_switch_mods', "Switch mods");
define('LANG_switch_mod_for_address', "Switch mod for address %s");
define('LANG_invalid_path', "Invalid Path");
define('LANG_add_new_game_home', "Add new game server");
define('LANG_no_game_homes_found', "No game servers found");
define('LANG_available_game_homes', "Available game servers");
define('LANG_home_id', "Home ID");
define('LANG_game_server', "Game Server");
define('LANG_game_type', "Game Type");
define('LANG_game_home', "Home Path");
define('LANG_game_home_name', "Game Server Name");
define('LANG_clone', "Clone");
define('LANG_unassign', "Unassign");
define('LANG_access_rights', "Access Rights");
define('LANG_assigned_homes', "Currently Assigned Homes");
define('LANG_assign', "Assign");
define('LANG_allow_updates', "Allow Game Updates");
define('LANG_allow_updates_info', "Allows user to update the game installation if that is possible.");
define('LANG_allow_file_management', "Allow File Management");
define('LANG_allow_file_management_info', "Allows user to access the game server with file management modules.");
define('LANG_allow_parameter_usage', "Allow Parameter Usage");
define('LANG_allow_parameter_usage_info', "Allows user to change available command line parameters.");
define('LANG_allow_extra_params', "Allow Extra parametrs");
define('LANG_allow_extra_params_info', "Allows user to modify extra command line parameters.");
define('LANG_allow_ftp', "Allow FTP");
define('LANG_allow_ftp_info', "Show the FTP access information to the user.");
define('LANG_allow_custom_fields', "Allow Custom Fields");
define('LANG_allow_custom_fields_info', "Allows user to access custom fields of the game server if any.");
define('LANG_select_home', "Select Home to Assign");
define('LANG_assign_new_home_to_user', "Assign New Home to user %s");
define('LANG_assign_new_home_to_group', "Assign New Home to group %s");
define('LANG_assigned_home_to_user', "Successfully assigned home (ID: %d) to user %s.");
define('LANG_failed_to_assign_home_to_user', "Failed to assign home (ID: %d) to user %s.");
define('LANG_assigned_home_to_group', "Successfully assigned home (ID: %d) to group %s.");
define('LANG_unassigned_home_from_user', "Successfully unassigned home (ID: %d) from user %s.");
define('LANG_unassigned_home_from_group', "Successfully unassigned home (ID: %d) from group %s.");
define('LANG_no_homes_assigned_to_user', "No homes assigned for user %s.");
define('LANG_no_homes_assigned_to_group', "No homes assigned for group %s.");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_user', "No more homes available that can be assigned for this user");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_group', "No more homes available that can be assigned for this group");
define('LANG_you_can_add_a_new_game_server_from', "You can add a new game server from %s.");
define('LANG_no_remote_servers_available_please_add_at_least_one', "There are no remote servers available, please add at least one!");
define('LANG_cloning_of_home_failed', "Cloning of home with id '%s' failed.");
define('LANG_no_mods_to_clone', "No enabled mod(s) for this game yet. None will be cloned.");
define('LANG_failed_to_add_mod', "Failed to add mod with id '%s' to home with id '%s'.");
define('LANG_failed_to_update_mod_settings', "Failed to update mod settings for home with id '%s'.");
define('LANG_successfully_cloned_mods', "Successfully cloned mods for home with id '%s'.");
define('LANG_successfully_copied_home_database', "Successfully copied home database.");
define('LANG_copying_home_remotely', "Copying the home on remote server from '%s' to '%s'.");
define('LANG_cloning_home', "Cloning home called '%s'");
define('LANG_current_home_path', "Current home path");
define('LANG_current_home_path_info', "The current location of the copied home on remote server.");
define('LANG_clone_home', "Clone Home");
define('LANG_new_home_name', "New Home Name");
define('LANG_new_home_path', "New Home Path");
define('LANG_agent_ip', "Agent IP");
define('LANG_game_server_copy_is_running', "Game server copy is running...");
define('LANG_game_server_copy_was_successful', "Game server copy was successful");
define('LANG_game_server_copy_failed_with_return_code', "Game server copy failed with return code %s");
define('LANG_clone_mods', "Clone Mods");
define('LANG_game_server_owner', "Game server owner");
define('LANG_the_name_of_the_server_to_help_users_to_identify_it', "The name of the server to help users to identify it.");
define('LANG_ips_and_ports_used_in_this_home', "IPs and Ports used in this home");
define('LANG_note_ips_and_ports_are_not_cloned', "Note - IPs and Ports are not cloned");
define('LANG_mods_and_settings_for_this_game_server', "Mods and settings for this game server");
define('LANG_sure_to_delete_serverid_from_remoteip_and_directory', "Are you sure you want to delete game server (ID: %s) from server %s and is in directory %s");
define('LANG_yes_and_delete_the_files', "Yes and Delete the files");
define('LANG_failed_to_remove_gamehome_from_database', "Failed to remove gamehome from database.");
define('LANG_successfully_deleted_game_server_with_id', "Successfully deleted game server with ID %s.");
define('LANG_failed_to_remove_ftp_account_from_remote_server', "Failed to remove FTP account from remote server.");
define('LANG_remove_it_anyway', "Would you like to remove it anyway?");
define('LANG_sucessfully_deleted', "Sucessfully deleted %s");
define('LANG_the_agent_had_a_problem_deleting', "The Agent had a problem while deleting %s. Please, check the Agent's log.");
define('LANG_connection_timeout_or_problems_reaching_the_agent', "Connection timeout or problems with reaching the Agent");
define('LANG_does_not_exist_yet', "Does not exist yet.");
define('LANG_update_settings', "Update settings");
define('LANG_settings_updated', "Settings updated.");
define('LANG_selected_path_already_in_use', "The selected path is already in use.");
define('LANG_browse', "Browse");
define('LANG_cancel', "Cancel");
define('LANG_set_this_path', "Set this path");
define('LANG_select_home_path', "Select home path");
define('LANG_folder', "Folder");
define('LANG_owner', "Owner");
define('LANG_group', "Group");
define('LANG_level_up', "Level up");
define('LANG_level_up_info', "Back to the previous folder.");
define('LANG_add_folder', "Add folder");
define('LANG_add_folder_info', "Write the name for the new folder, then click on the icon.");
define('LANG_valid_user', "Please specify a valid user.");
define('LANG_valid_group', "Please specify a valid group.");
define('LANG_set_affinity', "Set Server Affinity");
define('LANG_cpu_affinity_info', "Select the CPU core(s) you want to assign to the game server.");
define('LANG_expiration_date_changed', "Expiration date for selected home has been changed.");
define('LANG_expiration_date_could_not_be_changed', "Expiration date for selected home could not be changed.");
define('LANG_search', "Search");
define('LANG_ftp_account_username_too_long', "FTP username is too long. Try a shorter username no longer than 20 characters.");
define('LANG_ftp_account_password_too_long', "FTP password is too long. Try a shorter password no longer than 20 characters.");
define('LANG_other_servers_exist_with_path_please_change', "Other homes exist with the same path. It is recommended (but not required) that you change this path to something unique. You may have problems if you do NOT.");
define('LANG_change_access_rights_for_selected_servers', "Change access rights for selected servers");
?>