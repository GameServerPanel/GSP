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

define('LANG_no_games_to_monitor', "Nincs egyetlen játék sem konfigurálva neked, amit felügyelhetnél.");
define('LANG_status', "Állapot");
define('LANG_fail_no_mods', "Nincs mod engedélyezve ehhez a játékhoz! Meg kell kérned az adminisztrátort, hogy legalább egy játék modot engedélyezzen a számodra.");
define('LANG_no_game_homes_assigned', "Nincsenek szerverek hozzárendelve a fiókodhoz.");
define('LANG_select_game_home_to_configure', "Válasszd ki a konfigurálni kívánt szervert");
define('LANG_file_manager', "Fájlkezelő");
define('LANG_configure_mods', "Modok beállítása");
define('LANG_install_update_steam', "Telepítés/Frissítés Steamon keresztül");
define('LANG_install_update_manual', "Telepítés/Frissítés manuálisan");
define('LANG_assign_game_homes', "Játékszerverek hozzárendelése");
define('LANG_user', "Felhasználó");
define('LANG_group', "Csoport");
define('LANG_start', "Elindítás");
define('LANG_ogp_agent_ip', "OGP Agent IP");
define('LANG_max_players', "Max. játékos");
define('LANG_max', "Max.");
define('LANG_ip_and_port', "IP és Port");
define('LANG_available_maps', "Elérhető pályák");
define('LANG_map_path', "Pálya útvonal");
define('LANG_available_parameters', "Elérhető paraméterek");
define('LANG_start_server', "Szerver elindítása");
define('LANG_start_wait_note', "A szerver elindítása eltarthat egy ideig. Kérlek, várj mielőtt bezárod a böngésződet.");
define('LANG_game_type', "Játék típusa");
define('LANG_map', "Pálya");
define('LANG_starting_server', "Szerver indítása, kérlek várj...");
define('LANG_starting_server_settings', "Szerver indítása a következő beállításokkal");
define('LANG_startup_params', "Indítási paraméterek");
define('LANG_startup_cpu', "CPU amelyen a szerver fut");
define('LANG_startup_nice', "Nice értéke a szervernek");
define('LANG_game_home', "Szerver elérési útja");
define('LANG_server_started', "Szerver sikeresen elindult.");
define('LANG_no_parameter_access', "Nincs hozzáférésed a paraméterekhez.");
define('LANG_extra_parameters', "Extra paraméterek");
define('LANG_no_extra_param_access', "Nincs hozzáférésed az extra paraméterekhez.");
define('LANG_extra_parameters_info', "Ezek a paraméterek az indítóparancs végére kerülnek, amikor a játék szerver elindult.");
define('LANG_game_exec_not_found', "A játék indítófájla %s helyen nem található.");
define('LANG_select_params_and_start', "Válaszd ki az indítási paramétereket a szerver számára és nyomj '%s'-t.");
define('LANG_no_ip_port_pairs_assigned', "No IP Port pairs assigned for this home. If you do not have access to home editing contact your admin.");
define('LANG_unable_to_get_log', "Nem lehet megkapni a naplót, a retval %s.");
define('LANG_server_binary_not_executable', "A szerver bináris nem futtatható. Ellenőrizd, hogy megfelelő engedélyek vannak e a szerver mappában.");
define('LANG_server_not_running_log_found', "A szerver nem fut, de napló található. MEGJEGYZÉS: ez a napló esetleg nem kapcsolódik az utolsó szerver indításához.");
define('LANG_ip_port_pair_not_owned', "IP:PORT páros nincs birtokolva.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Alkalmatlan maxplayers érték maximum elérheto számú slot van állítva.");
define('LANG_server_running_not_responding', "Szerver fut, de nem válaszol,<br>előfordulhat, hogy valamilyen hibát ejtett, ezért érdemes átnézni a konfigurációt");
define('LANG_update_started', "Frissítés elkezdődött, kérlek várj...");
define('LANG_failed_to_start_steam_update', "A Steam frissítés elindítása sikertelen. Nézd meg az Agent naplóját.");
define('LANG_failed_to_start_rsync_update', "Az Rsync frissítés elindítása sikertelen. Nézd meg az Agent naplóját.");
define('LANG_update_completed', "Frissítés sikeresen befejezve.");
define('LANG_update_in_progress', "Frissítés folyamatban, kérlek várj...");
define('LANG_refresh_steam_status', "Steam állapot frissítése");
define('LANG_refresh_rsync_status', "Refresh Rsync status");
define('LANG_server_running_cant_update', "Szerver fut, így a frissítés nem lehetséges. Állítsd le a szervert frissítés előtt.");
define('LANG_xml_steam_error', "A kiválasztott szerver típus nem támogatja a Steam telepítést/frissítést.");
define('LANG_mod_key_not_found_from_xml', "A(z) '%s' mod kulcs nem található az XML fájlban.");
define('LANG_stop_update', "Frissítés leállítása");
define('LANG_statistics', "Statisztika");
define('LANG_servers', "Szerverek");
define('LANG_players', "Játékosok");
define('LANG_current_map', "Aktuális pálya");
define('LANG_stop_server', "Szerver leállítása");
define('LANG_server_ip_port', "Szerver IP:Port");
define('LANG_server_name', "Szerver neve");
define('LANG_server_id', "Szerver ID");
define('LANG_player_name', "Játékos név");
define('LANG_score', "Pontszám");
define('LANG_time', "Idő");
define('LANG_no_rights_to_stop_server', "Nincs engedélyed, hogy leállítsd ezt a szervert.");
define('LANG_no_ogp_lgsl_support', "Ehhez a játék szerverhez (%s) nincs LGSL támogatás az OGP-ben és ezért nem jelenítheto meg hozzá a statisztika.");
define('LANG_server_status', "Szerver státusz");
define('LANG_server_stopped', "A(z) '%s' szerver leállt.");
define('LANG_if_want_to_start_homes', "Ha játék szervereket akarsz elindítani, akkor menj a %s-ba.");
define('LANG_view_log', "Naplónéző");
define('LANG_if_want_manage', "Ha szeretné kezelni a játékokat meg tudod csinálni a");
define('LANG_columns', "oszlopok");
define('LANG_group_users', "Csoport felhasználók:");
define('LANG_assigned_to', "Hozzárendelve:");
define('LANG_restart_server', "Szerver újraindítása");
define('LANG_restarting_server', "Szerver újraindítása, kérlek várj...");
define('LANG_server_restarted', "A(z) '%s' szerver újraindítva.");
define('LANG_server_not_running', "A szerver nem fut.");
define('LANG_address', "Cím");
define('LANG_owner', "Tulajdonos");
define('LANG_operations', "Műveletek");
define('LANG_search', "Keresés");
define('LANG_maps_read_from', "A pályák olvasódnak a");
define('LANG_file', "fájl");
define('LANG_folder', "mappa");
define('LANG_unable_retrieve_mod_info', "Nem sikerült lekérni a mod információt az adatbázisból.");
define('LANG_unexpected_result_libremote', "Váratlan eredmény a libremote-val, kérlek, tájékoztasd a fejlesztőket.");
define('LANG_unable_get_info', "Nem sikerült megkapni a szükséges információt az indításhoz, az indítás blokkolása.");
define('LANG_server_already_running', "A szerver már fut. Ha nem látod a szervert a Játékfigyelőben, előfordulhat, hogy valamilyen probléma merült fel és");
define('LANG_already_running_stop_server', "Szerver leállítása.");
define('LANG_error_server_already_running', "HIBA: már fut szerver ezen a porton");
define('LANG_failed_start_server_code', "Nem sikerült elindítani a távoli szervert. Hibakód: %s");
define('LANG_disabled', "tiltva");
define('LANG_not_found_server', "Nem található távoli szerver az ID-vel");
define('LANG_rcon_command_title', "RCON parancs");
define('LANG_has_sent_to', "küldtünk");
define('LANG_need_set_remote_pass', "Be kell állítanod a távoli vezérlő jelszót");
define('LANG_before_sending_rcon_com', "mielőtt elküldjük az RCON parancsot neki.");
define('LANG_retry', "Újra");
define('LANG_page', "oldal");
define('LANG_server_cant_start', "a szerver nem tud elindulni");
define('LANG_server_cant_stop', "a szerver nem tud leállni");
define('LANG_error_occured_remote_host', "Hiba történt a távoli kiszolgálón");
define('LANG_follow_server_status', "A szerver státuszt követheted a");
define('LANG_addons', "Kiegészítők");
define('LANG_hostname', "Állomásnév");
define('LANG_rsync_install', "[Rsync telepítés]");
define('LANG_ping', "Ping");
define('LANG_team', "Csapat");
define('LANG_deaths', "Halálok");
define('LANG_pid', "PID");
define('LANG_skill', "Képesség");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam azonosító");
define('LANG_player', "Játékos");
define('LANG_port', "Port");
define('LANG_rcon_presets', "Előre beállított RCON");
define('LANG_update_from_local_master_server', "Frissítés a helyi Mester szerverről");
define('LANG_update_from_selected_rsync_server', "Frissítés a kiválasztott Rsync szerverről");
define('LANG_execute_selected_server_operations', "A kiválasztott szerver műveletek végrehajtása");
define('LANG_execute_operations', "Műveletek végrehajtása");
define('LANG_account_expiration', "Fiók lejárat");
define('LANG_mysql_databases', "MySQL adatbázis");
define('LANG_failed_querying_server', "* Nem sikerült lekérdezni a szervert.");
define('LANG_query_protocol_not_supported', "* Nincs lekérdezési protokoll az OGP-ben, ami támogatná ezt a szervert.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Lekérdezések letiltható beállításával: Letiltása lekérdezések után: %s, mert van %s szerver.<br>");
define('LANG_presets_for_game_and_mod', "RCON előre beállított parancs a játékhoz: %s és a modhoz: %s");
define('LANG_name', "Név");
define('LANG_command', "RCON&nbsp;parancs");
define('LANG_add_preset', "Új előre beállítás");
define('LANG_edit_presets', "Beállításkészlet szerkesztése");
define('LANG_del_preset', "Törlés");
define('LANG_change_preset', "Megváltoztatás");
define('LANG_send_command', "Parancs küldés");
define('LANG_starting_copy_with_master_server_named', "Kezdve másolatot master szerver neve '%s'...");
define('LANG_starting_sync_with', "Szinkronizálás elkezdése a(z) %s-vel...");
define('LANG_refresh_interval', "Napló frissítési intervallum");
define('LANG_finished_manual_update', "Befejezve a manuális frissítés.");
define('LANG_failed_to_start_file_download', "Nem sikerült elindítani a fájl letöltést");
define('LANG_game_name', "Játék neve");
define('LANG_dest_dir', "Cél könyvtár");
define('LANG_remote_server', "Távoli szerver");
define('LANG_file_url', "Fájl URL");
define('LANG_file_url_info', "A feltöltött és tömörítetlen fájl URL-címe a könyvtárhoz.");
define('LANG_dest_filename', "Célfájlnév");
define('LANG_dest_filename_info', "A célfájl fájlneve.");
define('LANG_update_server', "Szerver frissítés");
define('LANG_unavailable', "Nem elérhető");
define('LANG_upload_map_image', "Pálya kép feltöltése");
define('LANG_upload_image', "Kép feltöltése");
define('LANG_jpg_gif_png_less_than_1mb', "A kép legyen jpg, gif vagy png és kevesebb mint 1 MB.");
define('LANG_check_dev_console', "Hiba a fájl feltöltése során, kérlek, ellenőrizd a böngésző fejlesztői konzolját.");
define('LANG_uploaded_successfully', "Sikeres feltöltés.");
define('LANG_cant_create_folder', "Nem hozható létre a(z) <br><b>%s</b> mappa.");
define('LANG_cant_write_file', "Nem írható a <br>%s<br> fájl.");
define('LANG_exceeded_php_directive', "PHP direktíva túllépve.<br><b>%s</b>.");
define('LANG_unknown_errors', "Ismeretlen hibák.");
define('LANG_directory', "könyvtárból");
define('LANG_view_player_commands', "Játékos parancsok mutatása");
define('LANG_hide_player_commands', "Játékos parancsok elrejtése");
define('LANG_no_online_players', "Nincsenek online játékosok.");
define('LANG_invalid_game_mod_id', "Érvénytelen játék/mod ID meghatározva.");
define('LANG_auto_update_title_popup', "Steam automatikus frissítési link");
define('LANG_auto_update_popup_html', "<p>Használd az alábbi linket az ellenőrzéshez és az automatikus frissítéshez a játékszerveredhez a Steamen keresztül, ha szükséges.&nbsp; A cronjob segítségével lekérdezheted vagy manuálisan is elindíthatod a folyamatot.</p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Másolás");
define('LANG_auto_update_copy_me_success', "Másolva!");
define('LANG_auto_update_copy_me_fail', "Nem sikerült másolni. Kérlek, másold át manuálisan a linket.");
define('LANG_get_steam_autoupdate_api_link', "Automatikus frissítési link");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "User %s attempted to update home_id %d from a non-master server. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "Megpróbálod frissíteni ezt a szervert egy nem Mester szerverről.");
define('LANG_cannot_update_from_own_self', "A helyi szerver frissítés nem működik Mester szerveren.");
define('LANG_show_server_id', "Szerver ID mutatása");
define('LANG_hide_server_id', "Szerver ID elrejtése");
define('LANG_edit_configuration_files', "Konfigurációs fájlok szerkesztése");
define('LANG_admin', "Adminisztrátor");
define('LANG_cid', "CID");
define('LANG_phan', "Fantom");
define('LANG_sec', "Másodpercek");
define('LANG_unknown_rsync_mirror', "You attempted to start an update from a mirror which doesn't exist.");
define('LANG_custom_fields', "Egyéni mezők");
?>
