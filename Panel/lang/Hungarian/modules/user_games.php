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

define('LANG_add_mods_note', "Hozzáadás után mod-ot is hozzá kell adni a szerverhez. Ez a szerver szerkesztésével lehetséges.");
define('LANG_game_servers', "Játék szerverek");
define('LANG_game_path', "Szerver elérési útja");
define('LANG_game_path_info', "A szerver abszolút elérési útja. Példa: /home/ogpbot/OGP_User_Files/My_Server");
define('LANG_game_server_name_info', "Szerver neve segít a felhasználóknak beazonosítani a szervereiket.");
define('LANG_control_password', "Vezérlőjelszó");
define('LANG_control_password_info', "Ez a jelszó a szerver vezérléséhez használandó, mint például az RCON jelszó. Ha a jelszó üres, akkor más eszközöket használnak.");
define('LANG_add_game_home', "Játék szerver hozzáadása");
define('LANG_game_path_empty', "Játék elérési útja nem lehet üres.");
define('LANG_game_home_added', "A játékszerver sikeresen hozzáadva. Átirányítás a szerkesztési oldalra.");
define('LANG_failed_to_add_home_to_db', "Nem sikerült a szerver hozzáadása az adatbázisba. Hiba: %s");
define('LANG_caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms', "<b>Vigyázat!</b> Az Agent nem elérhető, nem kapható meg az operációs rendszer típusa és architektúrája,<br> A szerverek bemutatása az összes platformhoz:");
define('LANG_select_remote_server', "Válassz távoli szervert");
define('LANG_no_remote_servers_configured', "Nincs távoli szerver az Open Game Panelhoz.<br>Adj hozzá távoli szervert mielőtt játékszervereket adnál a felhasználóknak.");
define('LANG_no_game_configurations_found', "Nem található játék konfiguráció. Hozzá kell adnod a játék konfigurációkat a");
define('LANG_game_configurations', ">játék konfigurációs oldal");
define('LANG_add_remote_server', "Szerver hozzáadása.");
define('LANG_wine_games', "Wine játékok");
define('LANG_home_path', "Szerver elérési útja");
define('LANG_change_home_info', "A telepített játék szerver elérési útvonala. Példa: /home/ogp/szerverem/");
define('LANG_game_server_name', "Játék szerver neve");
define('LANG_change_name_info', "A szerver neve ami segít a felhasználóknak az azonosításukban.");
define('LANG_game_control_password', "Játékvezérlő jelszó");
define('LANG_change_control_password_info', "Vezérlő jelszó, mint például az RCON jelszó.");
define('LANG_available_mods', "Elérhető modok");
define('LANG_note_no_mods', "Nincsenek elérhető mod(ok) ehhez a játékhoz.");
define('LANG_change_home', "Útvonal megváltoztatása");
define('LANG_change_control_password', "Vezérlő jelszó megváltoztatása");
define('LANG_change_name', "Név megváltoztatása");
define('LANG_add_mod', "Mod hozzáadása");
define('LANG_set_ip', "IP beállítása");
define('LANG_ips_and_ports', "IP-k és Portok");
define('LANG_mod_name', "Mod név");
define('LANG_max_players', "Max. játékos");
define('LANG_extra_cmd_line_args', "Extra parancssori paraméterek");
define('LANG_extra_cmd_line_info', "Az extra parancssori paramétert megadva a szerver indulásakor megadhatsz extra paramétereket.");
define('LANG_cpu_affinity', "CPU affinitás");
define('LANG_nice_level', "Nice Szint");
define('LANG_set_options', "Beállítások beállítása");
define('LANG_remove_mod', "Mod eltávolítása");
define('LANG_mods', "Modok");
define('LANG_ip', "IP");
define('LANG_port', "Port");
define('LANG_no_ip_ports_assigned', "Legalább egy IP:Port párost hozzá kell csatolni a játék szerverhez.");
define('LANG_successfully_assigned_ip_port', "sikeresen hozzácsatoltad az IP:Port párt a játék szerverhez.");
define('LANG_port_range_error', "A portnak 0 és 65535 között kell lennie.");
define('LANG_failed_to_assing_mod_to_home', "Nem sikerült hozzáadni a modot %d azonosítóval a szerverhez.");
define('LANG_successfully_assigned_mod_to_home', "Mod sikeresen hozzárendelve %d azonosítóval a szerverhez.");
define('LANG_successfully_modified_mod', "A mod információk sikeresen módosítva.");
define('LANG_back_to_game_monitor', "Vissza a Játékfigyelőbe");
define('LANG_back_to_game_servers', "Vissza a játék szerverekhez");
define('LANG_user_id_main', "Fő tulajdonos");
define('LANG_change_user_id_main', "Fő tulajdonos váltás");
define('LANG_change_user_id_main_info', "The main server home owner.");
define('LANG_server_ftp_password', "FTP jelszó");
define('LANG_change_ftp_password', "FTP jelszó megváltoztatása");
define('LANG_change_ftp_password_info', "Ez az a jelszó, hogy belépni az FTP szerver a játék szerver.");
define('LANG_Delete_old_user_assigned_homes', "Távolítsa el a szerver az aktuális felhasználó");
define('LANG_editing_home_called', "Editing home called");
define('LANG_control_password_updated_successfully', "A vezérlő jelszó sikeresen frissítve.");
define('LANG_control_password_update_failed', "A vezérlő jelszó frissítése sikertelen");
define('LANG_successfully_changed_game_server', "A játék szervert sikeresen megváltoztattuk.");
define('LANG_error_ocurred_on_remote_server', "Hiba történt a távoli szerveren,");
define('LANG_ftp_password_can_not_be_changed', "Az FTP jelszavát nem lehet megváltoztatni.");
define('LANG_ftp_can_not_be_switched_on', "Az FTPt lehet bekapcsolni.");
define('LANG_ftp_can_not_be_switched_off', "Az FTPt nem lehet kikapcsolni.");
define('LANG_invalid_home_id_entered', "Invalid home id entered.");
define('LANG_ip_port_already_in_use', "Az %s:%s már használatban van. Válassz másikat.");
define('LANG_successfully_assigned_ip_port_to_server_id', "Successfully assigned %s:%s to home with ID %s.");
define('LANG_no_ip_addresses_configured', "Your game server does not have any IP-addresses configured to it. You can configure them from ");
define('LANG_server_page', "szerver oldal");
define('LANG_successfully_removed_mod', "A játékmód sikeresen eltávolítva.");
define('LANG_warning_agent_offline_defaulting_CPU_count_to_1', "Figyelem - Az Agent nem elérhető, defaulting CPU count to 1.");
define('LANG_mod_install_cmds', "Mod telepítési parancsok");
define('LANG_cmds_for', "Parancsok a");
define('LANG_preinstall_cmds', "Előtelepítési parancsok");
define('LANG_postinstall_cmds', "Utótelepítési parancsok");
define('LANG_edit_preinstall_cmds', "Előtelepítési parancsok szerkesztése");
define('LANG_edit_postinstall_cmds', "Utótelepítési parancsok szerkesztése");
define('LANG_save_as_default_for_this_mod', "Mentés alapértelmezettnek ehhez a modhoz");
define('LANG_empty', "üres");
define('LANG_master_server_for_clon_update', "Mester szerver a helyi frissítéshez");
define('LANG_set_as_master_server', "Beállítás mester szerverként");
define('LANG_set_as_master_server_for_local_clon_update', "Beállítás mester szervernek a helyi frissítéshez.");
define('LANG_only_available_for', "Only available for '%s' hosted on the remote server named '%s'.");
define('LANG_ftp_on', "FTP engedélyezése");
define('LANG_ftp_off', "FTP letiltása");
define('LANG_change_ftp_account_status', "FTP fiók állapot negváltoztatása");
define('LANG_change_ftp_account_status_info', "Ha egy FTP fiók engedélyezve vagy tiltva van, akkor az hozzáadásra vagy eltávolításra kerül az FTP adatbázisából.");
define('LANG_server_ftp_login', "Szerver FTP bejelentkezés");
define('LANG_change_ftp_login_info', "Az FTP bejelentkezés megváltoztatása egy személyre szabottra.");
define('LANG_change_ftp_login', "FTP bejelentkezés megváltoztatása");
define('LANG_ftp_login_can_not_be_changed', "Nem lehet megváltoztatni az FTP bejelentkezést.");
define('LANG_server_is_running_change_addresses_not_available', "A szerver jelenleg fut, az IPt nem lehet megváltoztatni.");
define('LANG_change_game_type', "Játék típusának megváltoztatása");
define('LANG_change_game_type_info', "A játék típus megváltoztatásával a jelenlegi modok konfigurációja törlésre kerül.");
define('LANG_force_mod_on_this_address', "Mód kényszerítése ezen a címen");
define('LANG_successfully_assigned_mod_to_address', "A mod sikeresen hozzárendelve a címhez");
define('LANG_switch_mods', "Módok váltása");
define('LANG_switch_mod_for_address', "Mód váltása a(z) %s címhez");
define('LANG_invalid_path', "Érvénytelen útvonal");
define('LANG_add_new_game_home', "Új játék szerver hozzáadása");
define('LANG_no_game_homes_found', "Nem találhatóak játék szerverek");
define('LANG_available_game_homes', "Elérhető játék szerverek");
define('LANG_home_id', "Azonosító");
define('LANG_game_server', "Játék szerver");
define('LANG_game_type', "Játék típus");
define('LANG_game_home', "Szerver elérési útja");
define('LANG_game_home_name', "Játék szerver neve");
define('LANG_clone', "Klónozás");
define('LANG_unassign', "Eltávolítás");
define('LANG_access_rights', "Hozzáférési jogok");
define('LANG_assigned_homes', "Társított játék szerverek");
define('LANG_assign', "Hozzárendelés");
define('LANG_allow_updates', "Játék frissítés engedélyezése");
define('LANG_allow_updates_info', "Engedélyezi a felhasználónak, hogy frissítse a játék telepítését, ha az lehetséges.");
define('LANG_allow_file_management', "Fájlkezelés engedélyezése");
define('LANG_allow_file_management_info', "Engedélyezi a felhasználónak, hogy hozzáférjen a játék szerverhez a fájlkezelő modullal.");
define('LANG_allow_parameter_usage', "Paraméter használat engedélyezése");
define('LANG_allow_parameter_usage_info', "Engedélyezi a felhasználónak, hogy megváltoztathassa az elérhető parancssori paramétereket.");
define('LANG_allow_extra_params', "Extra paraméterek engedélyezése");
define('LANG_allow_extra_params_info', "Engedélyezi a felhasználónak, hogy módosítsa az extra parancssori paramétereket.");
define('LANG_allow_ftp', "FTP engedélyezése");
define('LANG_allow_ftp_info', "Mutasd az FTP hozzáférési információkat a felhasználónak.");
define('LANG_allow_custom_fields', "Egyéni mezők engedélyezése");
define('LANG_allow_custom_fields_info', "Engedélyezi a felhasználónak, hogy hozzáférjen a játék szerver egyedi mezőihez, ha van ilyen.");
define('LANG_select_home', "Válaszd ki a társítani kívánt szervert");
define('LANG_assign_new_home_to_user', "Assign New Home to user %s");
define('LANG_assign_new_home_to_group', "Assign New Home to group %s");
define('LANG_assigned_home_to_user', "Sikeresen hozzáadott szerver (azonosító: %d) %s felhasználóhoz.");
define('LANG_failed_to_assign_home_to_user', "Failed to assign home (ID: %d) to user %s.");
define('LANG_assigned_home_to_group', "Successfully assigned home (ID: %d) to group %s.");
define('LANG_unassigned_home_from_user', "Sikeresen eltávolítva a játék szerver (azonosító: %d) %s felhasználótól.");
define('LANG_unassigned_home_from_group', "Successfully unassigned home (ID: %d) from group %s.");
define('LANG_no_homes_assigned_to_user', "No homes assigned for user %s.");
define('LANG_no_homes_assigned_to_group', "No homes assigned for group %s.");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_user', "No more homes available that can be assigned for this user");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_group', "No more homes available that can be assigned for this group");
define('LANG_you_can_add_a_new_game_server_from', "Új játék szervert a %s-ban tudsz hozzáadni.");
define('LANG_no_remote_servers_available_please_add_at_least_one', "Nincsenek elérhető távoli szerverek, kérlek, adj meg legalább egyet!");
define('LANG_cloning_of_home_failed', "Cloning of home with id '%s' failed.");
define('LANG_no_mods_to_clone', "Nincsen mód engedélyezve ehhez a játék szerverhez még. Semmi se lesz klónozva.");
define('LANG_failed_to_add_mod', "Failed to add mod with id '%s' to home with id '%s'.");
define('LANG_failed_to_update_mod_settings', "Failed to update mod settings for home with id '%s'.");
define('LANG_successfully_cloned_mods', "Successfully cloned mods for home with id '%s'.");
define('LANG_successfully_copied_home_database', "Successfully copied home database.");
define('LANG_copying_home_remotely', "Copying the home on remote server from '%s' to '%s'.");
define('LANG_cloning_home', "Cloning home called '%s'");
define('LANG_current_home_path', "Az aktuális szerver elérési útja");
define('LANG_current_home_path_info', "The current location of the copied home on remote server.");
define('LANG_clone_home', "Szerver klónozása");
define('LANG_new_home_name', "Új szerver név");
define('LANG_new_home_path', "Új elérési útja a szervernek");
define('LANG_agent_ip', "Agent IP");
define('LANG_game_server_copy_is_running', "A játék szerver másolás folyamatban...");
define('LANG_game_server_copy_was_successful', "A játék szerver sikeresen átmásolva");
define('LANG_game_server_copy_failed_with_return_code', "Játékszerver másolása sikertelen a(z) %s visszatérési kóddal.");
define('LANG_clone_mods', "Modok klónozása");
define('LANG_game_server_owner', "Játékszerver tulajdonos");
define('LANG_the_name_of_the_server_to_help_users_to_identify_it', "A szerver neve segít a felhasználóknak a szerver azonosításában.");
define('LANG_ips_and_ports_used_in_this_home', "Az IP-k és portok ehhez a szerverhez használva");
define('LANG_note_ips_and_ports_are_not_cloned', "Megjegyzés - Az IP-címek és a portok nincsenek klónozva");
define('LANG_mods_and_settings_for_this_game_server', "Modok és beállítások ehhez a játékszerverhez");
define('LANG_sure_to_delete_serverid_from_remoteip_and_directory', "Biztosan törölni akarod a játékszervert (ID: %s) a(z) %s szerverről és a(z) %s mappából.");
define('LANG_yes_and_delete_the_files', "Igen és töröld a fájlokat");
define('LANG_failed_to_remove_gamehome_from_database', "Failed to remove gamehome from database.");
define('LANG_successfully_deleted_game_server_with_id', "Sikeresen törölted a játékszervert a(z) %s-es azonosítóval.");
define('LANG_failed_to_remove_ftp_account_from_remote_server', "Nem sikerült eltávolítani az FTP fiókot a távoli szerverről.");
define('LANG_remove_it_anyway', "Szeretnéd ezt egyébként eltávolítani?");
define('LANG_sucessfully_deleted', "A(z) %s sikeresen törölve.");
define('LANG_the_agent_had_a_problem_deleting', "Az Agentnek problémája volt a(z) %s törlése közben. Kérlek, ellenőrizd az Agent naplóját.");
define('LANG_connection_timeout_or_problems_reaching_the_agent', "Kapcsolat időtúllépés vagy problémák az Agent elérésével");
define('LANG_does_not_exist_yet', "Még nem létezik.");
define('LANG_update_settings', "Beállítások frissítése");
define('LANG_settings_updated', "Beállítások frissítve.");
define('LANG_selected_path_already_in_use', "A kiválasztott útvonal már használatban van.");
define('LANG_browse', "Tallózás");
define('LANG_cancel', "Mégse");
define('LANG_set_this_path', "Állítsd be ezt az útvonalat");
define('LANG_select_home_path', "Szerver elérési útjának a kiválasztása");
define('LANG_folder', "Mappa");
define('LANG_owner', "Tulajdonos");
define('LANG_group', "Csoport");
define('LANG_level_up', "Szintlépés");
define('LANG_level_up_info', "Vissza az előző mappához.");
define('LANG_add_folder', "Mappa hozzáadása");
define('LANG_add_folder_info', "Írd le a nevét az új mappának, majd kattints az ikonra.");
define('LANG_valid_user', "Kérlek, adj meg egy érvényes felhasználót.");
define('LANG_valid_group', "Kérlek, adj meg egy érvényes csoportot.");
define('LANG_set_affinity', "Szerver affinitás beállítása");
define('LANG_cpu_affinity_info', "Válaszd ki a játékszerverhez hozzárendelni kívánt CPU magot(magokat).");
define('LANG_expiration_date_changed', "Expiration date for selected home has been changed.");
define('LANG_expiration_date_could_not_be_changed', "Expiration date for selected home could not be changed.");
define('LANG_search', "Keresés");
define('LANG_ftp_account_username_too_long', "Az FTP felhasználónév túl hosszú. Próbálkozz rövidebb, legfeljebb 20 karakter hosszú felhasználónévvel.");
define('LANG_ftp_account_password_too_long', "Az FTP jelszó túl hosszú. Próbálkozz rövidebb, legfeljebb 20 karakter hosszú jelszóval.");
define('LANG_other_servers_exist_with_path_please_change', "Other homes exist with the same path. It is recommended (but not required) that you change this path to something unique. You may have problems if you do NOT.");
define('LANG_change_access_rights_for_selected_servers', "Change access rights for selected servers");
?>