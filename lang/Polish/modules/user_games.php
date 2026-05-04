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

define('LANG_add_mods_note', "Trzeba dodać mody po dodaniu serwera do użytkownika. Można to zrobić poprzez edycję serwera..");
define('LANG_game_servers', "Game Servers");
define('LANG_game_path', "Game Path");
define('LANG_game_path_info', "An absolute server path. Example: /home/ogpbot/OGP_User_Files/My_Server");
define('LANG_game_server_name_info', "Nazwa serwera ułatwia użytkownikom do jednoznacznej identyfikacji swoich serwerów.");
define('LANG_control_password', "Hasło Control");
define('LANG_control_password_info', "To hasło jest używane do kontroli serwera, takich jak hasła RCON. Jeśli hasło jest puste to w inny sposób są wykorzystywane.");
define('LANG_add_game_home', "Dodaj dom gry");
define('LANG_game_path_empty', "Gra ścieżka nie może być puste.");
define('LANG_game_home_added', "Strona główna Game pomyślnie dodana. Przekierowanie do domu edycji strony");
define('LANG_failed_to_add_home_to_db', "Nie można dodać do domu do bazy danych. Błąd: %s");
define('LANG_caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms', "<b>Caution!</b> The Agent is offline, can not get OS type and architecture,<br> Showing servers for all platforms:");
define('LANG_select_remote_server', "Select Remote Server");
define('LANG_no_remote_servers_configured', "No remote servers configured to the Open Game Panel.<br>You need to add remote servers before you can add servers for the users.");
define('LANG_no_game_configurations_found', "No game configuration found. You need to add game configurations from the");
define('LANG_game_configurations', ">game configuration page");
define('LANG_add_remote_server', "Add a server.");
define('LANG_wine_games', "Wine Games");
define('LANG_home_path', "główna ścieżka:");
define('LANG_change_home_info', "Położenie zainstalowanych serwerów. Przykład: / home / OGP / nazwa_hosta /");
define('LANG_game_server_name', "Nazwa serwera gry");
define('LANG_change_name_info', "nazwę serwera, aby pomóc użytkownikom, aby zidentyfikować go.");
define('LANG_game_control_password', "Hasło kontrola gry");
define('LANG_change_control_password_info', "Control jest hasło rcon password przykład.");
define('LANG_available_mods', "Dostępne mody");
define('LANG_note_no_mods', "Nr aktywny mod (y) dla tej gry. Musisz włączyć mod (y) przed użytkownicy mogą rozpocząć korzystanie z serwera.");
define('LANG_change_home', "Change Home");
define('LANG_change_control_password', "Zmień hasło kontrolne");
define('LANG_change_name', "Zmień nazwe");
define('LANG_add_mod', "Dodaj Mod");
define('LANG_set_ip', "Zmień IP");
define('LANG_ips_and_ports', "IPs i Ports");
define('LANG_mod_name', "Nazwa Mod ");
define('LANG_max_players', "Max Players");
define('LANG_extra_cmd_line_args', "Dodatkowe argumenty linii poleceń");
define('LANG_extra_cmd_line_info', "Dodatkowe argumenty wiersza poleceń to sposób, aby wprowadzić dodatkowe argumenty do polecenia gra, gdy jest uruchamiany.");
define('LANG_cpu_affinity', "Działa na Procesorze");
define('LANG_nice_level', "Nice Level");
define('LANG_set_options', "Zmień Ustawienia");
define('LANG_remove_mod', "Usuń Mod");
define('LANG_mods', "Mody");
define('LANG_ip', "IP");
define('LANG_port', "Port");
define('LANG_no_ip_ports_assigned', "Przynajmniej jeden IP: para Port musi być przypisany do domu.");
define('LANG_successfully_assigned_ip_port', "Pomyślnie przypisane IP: Port parę do domu.");
define('LANG_port_range_error', "Port musi być pomiędzy 0 a 65535 zakresie.");
define('LANG_failed_to_assing_mod_to_home', "Nie udało się przypisać mod z id %d do domu");
define('LANG_successfully_assigned_mod_to_home', "Prawidłowo ustawione mod z id %d do domu.");
define('LANG_successfully_modified_mod', "Pomyślnie zmodyfikowane informacje mod.");
define('LANG_back_to_game_monitor', "z powrotem do Game Monitor");
define('LANG_back_to_game_servers', "z powrotem do Game Servers");
define('LANG_user_id_main', "Główny właściciel");
define('LANG_change_user_id_main', "Zmiana głównego właściciela");
define('LANG_change_user_id_main_info', "Głównym właścicielem domu serwer.");
define('LANG_server_ftp_password', "FTP hasło");
define('LANG_change_ftp_password', "Zmień hasło FTP");
define('LANG_change_ftp_password_info', "To jest hasło do logowania do serwera FTP do tego domu.");
define('LANG_Delete_old_user_assigned_homes', "Cofanie serwer gry do obecnych użytkowników.");
define('LANG_editing_home_called', "Editing home called");
define('LANG_control_password_updated_successfully', "Control password updated successfully.");
define('LANG_control_password_update_failed', "Control password update failed");
define('LANG_successfully_changed_game_server', "Successfully changed game server.");
define('LANG_error_ocurred_on_remote_server', "Error ocurred on remote server,");
define('LANG_ftp_password_can_not_be_changed', "FTP password can not be changed.");
define('LANG_ftp_can_not_be_switched_on', "FTP can not be switched ON.");
define('LANG_ftp_can_not_be_switched_off', "FTP can not be switched OFF.");
define('LANG_invalid_home_id_entered', "Invalid home id entered.");
define('LANG_ip_port_already_in_use', "The %s:%s is already in use. Choose another one.");
define('LANG_successfully_assigned_ip_port_to_server_id', "Successfully assigned %s:%s to home with ID %s.");
define('LANG_no_ip_addresses_configured', "Your game server does not have any IP-addresses configured to it. You can configure them from ");
define('LANG_server_page', "server page");
define('LANG_successfully_removed_mod', "Successfully removed game mod.");
define('LANG_warning_agent_offline_defaulting_CPU_count_to_1', "Warning - Agent offline, defaulting CPU count to 1.");
define('LANG_mod_install_cmds', "Mod Install CMDs");
define('LANG_cmds_for', "Commands for");
define('LANG_preinstall_cmds', "Preinstall Commands");
define('LANG_postinstall_cmds', "Postinstall Commands");
define('LANG_edit_preinstall_cmds', "Edit Preinstall Commands");
define('LANG_edit_postinstall_cmds', "Edit Postinstall Commands");
define('LANG_save_as_default_for_this_mod', "Save as default for this mod");
define('LANG_empty', "empty");
define('LANG_master_server_for_clon_update', "Master server for local update");
define('LANG_set_as_master_server', "Set as master server");
define('LANG_set_as_master_server_for_local_clon_update', "Set as master server for local update.");
define('LANG_only_available_for', "Only available for '%s' hosted on the remote server named '%s'.");
define('LANG_ftp_on', "Enable FTP");
define('LANG_ftp_off', "Disable FTP");
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
define('LANG_add_new_game_home', "Dodaj nowe gry");
define('LANG_no_game_homes_found', "Nie znaleziono gry");
define('LANG_available_game_homes', "Dostępne Gry");
define('LANG_home_id', "Home ID");
define('LANG_game_server', "Game Server");
define('LANG_game_type', "Game Type");
define('LANG_game_home', "Home Path");
define('LANG_game_home_name', "Game Server Name");
define('LANG_clone', "Sklonuj");
define('LANG_unassign', "Unassign");
define('LANG_access_rights', "Access Rights");
define('LANG_assigned_homes', "Currently Assigned Homes");
define('LANG_assign', "Assign");
define('LANG_allow_updates', "Allow Game Updates");
define('LANG_allow_updates_info', "Pozwala użytkownikowi na aktualizację instalacji gry jeżeli jest to możliwe.");
define('LANG_allow_file_management', "Allow File Management");
define('LANG_allow_file_management_info', "Pozwala użytkownikowi na dostęp do domu gry z modułów do zarządzania plikami.");
define('LANG_allow_parameter_usage', "Pozwól Użycie parametru");
define('LANG_allow_parameter_usage_info', "Pozwala użytkownikowi zmienić dostępne parametry wiersza polecenia.");
define('LANG_allow_extra_params', "Pozwól parametry Extra");
define('LANG_allow_extra_params_info', "Pozwala użytkownikowi na modyfikowanie dodatkowych parametrów linii poleceń.");
define('LANG_allow_ftp', "Dostęp FTP");
define('LANG_allow_ftp_info', "Pokaż dostęp do FTP dla użytkownika.");
define('LANG_allow_custom_fields', "Allow Custom Fields");
define('LANG_allow_custom_fields_info', "Allows user to access custom fields of the game server if any.");
define('LANG_select_home', "Strona główna wybrać, aby przypisać");
define('LANG_assign_new_home_to_user', "Przypisywanie do użytkownika Nowy Dom %s");
define('LANG_assign_new_home_to_group', "Przypisz do grupy New Home %s");
define('LANG_assigned_home_to_user', "Pomyślnie przydzielony do domu (ID: %d) do gracza %s.");
define('LANG_failed_to_assign_home_to_user', "Failed to assign home (ID: %d) to user %s.");
define('LANG_assigned_home_to_group', "Pomyślnie przydzielony do domu (ID: %d) do grupy %s.");
define('LANG_unassigned_home_from_user', "Pomyślnie przydzielony do domu (ID: %d) z graczem %s.");
define('LANG_unassigned_home_from_group', "Pomyślnie przydzielony do domu (ID: %d) z grupą %s.");
define('LANG_no_homes_assigned_to_user', "Nr mieszkania przeznaczone dla użytkownika %s.");
define('LANG_no_homes_assigned_to_group', "Nr mieszkania przeznaczone dla grupy %s.");
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
define('LANG_selected_path_already_in_use', "Wybrana ścieżka jest już w użyciu.");
define('LANG_browse', "Przeglądaj");
define('LANG_cancel', "Anuluj");
define('LANG_set_this_path', "Ustaw tę ścieżkę");
define('LANG_select_home_path', "Wybierz ścieżkę domową");
define('LANG_folder', "Folder");
define('LANG_owner', "Owner");
define('LANG_group', "Group");
define('LANG_level_up', "Do góry");
define('LANG_level_up_info', "Powrót do poprzedniego folderu.");
define('LANG_add_folder', "Dodaj Folder");
define('LANG_add_folder_info', "Wpisz nazwę nowego folderu, a następnie kliknij na ikonę.");
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