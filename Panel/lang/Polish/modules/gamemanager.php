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

define('LANG_no_games_to_monitor', "Nie masz żadnych gier, które mogą być monitorowane.");
define('LANG_status', "Status");
define('LANG_fail_no_mods', "Brak aktywnych modów do tej gry! Musisz zwrócić się do admin OGP by dodać mod dla wybranej dla ciebie gry.");
define('LANG_no_game_homes_assigned', "Nie posiadasz żadnych serwerów przypisanych do Twojego konta.");
define('LANG_select_game_home_to_configure', "Wybierz serwer gry, który chcesz skonfigurować");
define('LANG_file_manager', "Manager Plików");
define('LANG_configure_mods', "Konfiguruj Mody");
define('LANG_install_update_steam', "Instaluj/Aktualizuj przez SteamCMD");
define('LANG_install_update_manual', "Instaluj/Aktualizuj Ręcznie");
define('LANG_assign_game_homes', "Przypisywanie serwerów gier");
define('LANG_user', "Użytkownik");
define('LANG_group', "Grupa");
define('LANG_start', "Start");
define('LANG_ogp_agent_ip', "IP Agenta OGP");
define('LANG_max_players', "Maks Graczy");
define('LANG_max', "Maks");
define('LANG_ip_and_port', "IP i Port");
define('LANG_available_maps', "Dostępne mapy");
define('LANG_map_path', "Ścieżka Map");
define('LANG_available_parameters', "Dostępne paramentry");
define('LANG_start_server', "Start");
define('LANG_start_wait_note', "Uruchomienie serwera może trochę potrwać. Proszę nie zamykaj przeglądarki.");
define('LANG_game_type', "Rodzaj gry");
define('LANG_map', "Mapa");
define('LANG_starting_server', "Uruchamianie serwera, proszę czekać...");
define('LANG_starting_server_settings', "Uruchamianie serwera z następującymi ustawieniami");
define('LANG_startup_params', "Parametry startowe");
define('LANG_startup_cpu', "CPU na którym uruchomiony jest serwer");
define('LANG_startup_nice', "Priorytet serwera");
define('LANG_game_home', "Ścieżka domowa");
define('LANG_server_started', "Serwer został uruchomiony pomyślnie.");
define('LANG_no_parameter_access', "Nie masz dostępu do ustawień.");
define('LANG_extra_parameters', "Parametry dodatkowe");
define('LANG_no_extra_param_access', "Nie masz dostępu do dodatkowych parametrów.");
define('LANG_extra_parameters_info', "Parametry te są wprowadzane do końca linii poleceń podczas uruchamiania gry.");
define('LANG_game_exec_not_found', "plik gry wykonywalny %s nie znależiono w katalogu gry");
define('LANG_select_params_and_start', "Wybierz parametry uruchomienia serwera i naciśnij '%s'.");
define('LANG_no_ip_port_pairs_assigned', "Nr IP par Port przypisane do tego hosta. Jeśli nie masz dostępu do edycji hosta skontaktuj się z administratorem..");
define('LANG_unable_to_get_log', "Nie można uzyskać logu, retval %s.");
define('LANG_server_binary_not_executable', "Plik binarny nie jest wykonywalny. Proszę sprawdzić uprawnienia chmod w katalogu serwera.");
define('LANG_server_not_running_log_found', "Serwer nie działa, ale znaleziono logi serwera. INFO: Może to oznaczać że logi pochodzą z poprzedniego uruchomienia serwera.");
define('LANG_ip_port_pair_not_owned', "IP:PORT par nie jesteś włascielem.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Nieodpowiednie wartość maxplayers, maksymalne, osiągalne liczba slotów została ustawiona.");
define('LANG_server_running_not_responding', "Serwer jest uruchomiony, ale nie odpowiada, <br>może być jakiś problem, a może chcesz ");
define('LANG_update_started', "Aktualizacje rozpoczęte, proszę czekać ...");
define('LANG_failed_to_start_steam_update', "Nie udało się uruchomić update Steam. Zobacz Log Agenta.");
define('LANG_failed_to_start_rsync_update', "Nie udało się uruchomić update Rsync. Zobacz Log Agenta.");
define('LANG_update_completed', "Aktualizacja została zakończona pomyślnie.");
define('LANG_update_in_progress', "Aktualizacja w toku, proszę czekać ...");
define('LANG_refresh_steam_status', "Odśwież status Steam");
define('LANG_refresh_rsync_status', "Refresh Rsync status");
define('LANG_server_running_cant_update', "Aktualizacja serwera nie jest możliwa. Zatrzymaj serwer przed aktualizacją.");
define('LANG_xml_steam_error', "Wybrany serwer nie obsługuje instalacji/aktualizacji przez Steam.");
define('LANG_mod_key_not_found_from_xml', "Mod klucz '%s' nie znaleziony w pliku XML.");
define('LANG_stop_update', "Zatrzymaj aktualizacje");
define('LANG_statistics', "Statystyki");
define('LANG_servers', "Serwery");
define('LANG_players', "Gracze");
define('LANG_current_map', "Aktualna mapa");
define('LANG_stop_server', "Zatrzymaj");
define('LANG_server_ip_port', "IP:Port Serwera");
define('LANG_server_name', "Nazwa Serwera");
define('LANG_server_id', "id");
define('LANG_player_name', "Nazwa Gracza");
define('LANG_score', "Pkt");
define('LANG_time', "Czas");
define('LANG_no_rights_to_stop_server', "Nie masz uprawnień, aby zatrzymać ten serwer..");
define('LANG_no_ogp_lgsl_support', "Ten serwer. (działa: %s) nie ma LGSL wsparcia w OGP i jego statystyki nie mogą być pokazane.");
define('LANG_server_status', "Serwer na %s jest %s.");
define('LANG_server_stopped', "Server '%s' został zatrzymany.");
define('LANG_if_want_to_start_homes', "Jeśli chcesz rozpocząć grę przejdź do %s.");
define('LANG_view_log', "Log Viewer");
define('LANG_if_want_manage', "Jeśli chcesz zarządzać grą możesz to zrobić w");
define('LANG_columns', "kolumny");
define('LANG_group_users', "Grupa użytkowników:");
define('LANG_assigned_to', "Przypisany do:");
define('LANG_restart_server', "Restart");
define('LANG_restarting_server', "Restartowanie serwera, proszę czekać...");
define('LANG_server_restarted', "Serwer '%s' został zrestartowany.");
define('LANG_server_not_running', "Serwer nie jest uruchomiony.");
define('LANG_address', "Adres");
define('LANG_owner', "Właściciel");
define('LANG_operations', "Operator");
define('LANG_search', "Szukaj");
define('LANG_maps_read_from', "Mapy wczytane z");
define('LANG_file', "plik");
define('LANG_folder', "katalog");
define('LANG_unable_retrieve_mod_info', "Nie można pobrać informacji moda z bazy danych.");
define('LANG_unexpected_result_libremote', "Niespodziewany wynik libremote, proszę poinformować administratora.");
define('LANG_unable_get_info', "Nie można uzyskać wymaganych informacji dotyczących uruchamiania, blokowania uruchamiania.");
define('LANG_server_already_running', "Serwer jest już uruchomiony. Jeśli nie widzisz serwera w Statusie Serwerów, może to oznaczać problem, może chcesz");
define('LANG_already_running_stop_server', "Zatrzymaj");
define('LANG_error_server_already_running', "BŁĄD: Serwer już działa w porcie");
define('LANG_failed_start_server_code', "Nie można uruchomić zdalnego serwera. Kod Błędu: %s");
define('LANG_disabled', "wyłączony");
define('LANG_not_found_server', "Nie można znaleźć zdalnego serwera z ID");
define('LANG_rcon_command_title', "Komenda RCON");
define('LANG_has_sent_to', "został wysłany do");
define('LANG_need_set_remote_pass', "Musisz ustawić hasło RCON");
define('LANG_before_sending_rcon_com', "przed wysłaniem do niego poleceń rcon.");
define('LANG_retry', "Powtórz");
define('LANG_page', "strona");
define('LANG_server_cant_start', "nie można uruchomić serwera");
define('LANG_server_cant_stop', "nie można zatrzymać serwera");
define('LANG_error_occured_remote_host', "Wystąpił błąd zdalnego hosta");
define('LANG_follow_server_status', "Możesz śledzić stan serwera z");
define('LANG_addons', "Dodatki");
define('LANG_hostname', "Nazwa hosta");
define('LANG_rsync_install', "[Instalacja Rsync]");
define('LANG_ping', "Ping");
define('LANG_team', "Team");
define('LANG_deaths', "Zgony");
define('LANG_pid', "PID");
define('LANG_skill', "Skill");
define('LANG_AIBot', "BOT");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Gracz");
define('LANG_port', "Port");
define('LANG_rcon_presets', "Konsola RCON");
define('LANG_update_from_local_master_server', "Update from local Master Server");
define('LANG_update_from_selected_rsync_server', "Update from selected Rsync server");
define('LANG_execute_selected_server_operations', "Wykonaj wybrane operacje serwera");
define('LANG_execute_operations', "Wykonaj operacje");
define('LANG_account_expiration', "Konto wygaśnie");
define('LANG_mysql_databases', "Bazy MySQL");
define('LANG_failed_querying_server', "* Nie udało się zapytać serwera.");
define('LANG_query_protocol_not_supported', "* W OGP nie ma protokołu zapytania, który może obsługiwać ten serwer.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Zapytania wyłączone przez ustawienie: Wyłącz zapytania po: %s, ponieważ posiadasz %s serwerów.<br>");
define('LANG_presets_for_game_and_mod', "Konsola RCON dla gry %s mod %s");
define('LANG_name', "Nazwa");
define('LANG_command', "Komenda&nbsp;RCON");
define('LANG_add_preset', "Dodaj preset");
define('LANG_edit_presets', "Edytuj presets");
define('LANG_del_preset', "Usuń");
define('LANG_change_preset', "Zmień");
define('LANG_send_command', "Wyślij komendę");
define('LANG_starting_copy_with_master_server_named', "Uruchamianie kopii z nazwą serwera głównego '%s'...");
define('LANG_starting_sync_with', "Uruchamianie synchronizacji z %s...");
define('LANG_refresh_interval', "Czas odświeżania logów");
define('LANG_finished_manual_update', "Ręczna aktualizacja ukończona.");
define('LANG_failed_to_start_file_download', "Nie udało się rozpocząć pobierania pliku");
define('LANG_game_name', "Nazwa Gry");
define('LANG_dest_dir', "Katalog docelowy");
define('LANG_remote_server', "Serwer Zdalny");
define('LANG_file_url', "Adres Pliku");
define('LANG_file_url_info', "Adres URL pliku przesłanego i nieskompresowanego do katalogu.");
define('LANG_dest_filename', "Docelowa nazwa pliku");
define('LANG_dest_filename_info', "Nazwa pliku docelowego.");
define('LANG_update_server', "Aktualizuj Serwer");
define('LANG_unavailable', "Niedostępne");
define('LANG_upload_map_image', "Wyślij obrazek mapy");
define('LANG_upload_image', "Wyślij obrazek");
define('LANG_jpg_gif_png_less_than_1mb', "Zapisywanie obrazu w formacie jpg, gif lub png oraz rozmiar < 1MB");
define('LANG_check_dev_console', "Błąd podczas przesyłania pliku sprawdź w konsoli programisty przeglądarki.");
define('LANG_uploaded_successfully', "Wysyłanie kompletne.");
define('LANG_cant_create_folder', "Nie można utworzyć folderu:<br><b>%s</b>");
define('LANG_cant_write_file', "Nie można zapisać pliku:<br><b>%s</b>");
define('LANG_exceeded_php_directive', "Przekroczono dyrektywę PHP.<br><b>%s</b>.");
define('LANG_unknown_errors', "Nieznane błędy.");
define('LANG_directory', "Ścieżka katalogu");
define('LANG_view_player_commands', "Pokaż Komendy Gracza");
define('LANG_hide_player_commands', "Ukryj Komendy Gracza");
define('LANG_no_online_players', "Brak graczy online.");
define('LANG_invalid_game_mod_id', "Nieprawidłowa identyfikator gry/mod.");
define('LANG_auto_update_title_popup', "Auto Aktualizacja Steam Link");
define('LANG_auto_update_popup_html', "<p>Skorzystaj z poniższego linku, aby sprawdzić i automatycznie aktualizować serwer gry poprzez Steam, jeśli to konieczne.&nbsp; Można ją zapytać przy użyciu narzędzia CRON lub ręcznie zainicjować proces.</p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Kopiuj");
define('LANG_auto_update_copy_me_success', "Skopiowano!");
define('LANG_auto_update_copy_me_fail', "Nie udało się skopiować. Proszę ręcznie skopiować link.");
define('LANG_get_steam_autoupdate_api_link', "Auto Aktualizacja Link");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "Użytkownik %s próbował zaktualizować home_id %d z serwera innego niż serwer źródłowy. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "Próbujesz zaktualizować ten serwer z serwera innego niż źródłowy.");
define('LANG_cannot_update_from_own_self', "Aktualizacja lokalnego serwera może nie działać na serwerze głównym.");
define('LANG_show_server_id', "Pokaż ID Serwerów");
define('LANG_hide_server_id', "Ukryj ID Serwerów");
define('LANG_edit_configuration_files', "Edytuj pliki konfiguracyjne");
define('LANG_admin', "Administrator");
define('LANG_cid', "CID");
define('LANG_phan', "Phantom");
define('LANG_sec', "Sekundy");
define('LANG_unknown_rsync_mirror', "Podjęto próbę uruchomienia aktualizacji z nieistniejącego serwera lustrzanego.");
define('LANG_custom_fields', "Pola niestandardowe");
?>
