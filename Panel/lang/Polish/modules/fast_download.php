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

define('LANG_create_alias', "Utwórz alias i folder");
define('LANG_save_as', "Zapisz jako");
define('LANG_failure', "Błąd, nie udało się wygenerować pliku dla aliasu");
define('LANG_success', "Powodzenie");
define('LANG_fast_download_service_for', "Pobieranie usługi przekierowania dla %s");
define('LANG_to_the_path', "Do ścieżki");
define('LANG_at_url', "URL");
define('LANG_create_alias_for', "Stwórz alias dla");
define('LANG_fast_dl', "Przekierowywanie pobrań (FastDL)");
define('LANG_current_aliases_at_remote_server', "Bieżące aliasy na serwerze hosta");
define('LANG_delete_selected_aliases', "Usuń wybrane aliasy");
define('LANG_no_aliases_defined', "Nie istnieją jeszcze aliasy zdefiniowane przez OGP dla tego serwera hosta.");
define('LANG_fastdl_port', "Port");
define('LANG_fastdl_port_info', "Port na którym będzie działał/nasłuchiwał Fast Download.");
define('LANG_fastdl_ip', "Adres");
define('LANG_fastdl_ip_info', "Adres IP lub domeny, w której twój serwer Fast Download będzie działał, domena musi znajdować się w /etc/hosts.");
define('LANG_listing', "Lista");
define('LANG_listing_info', "Jeśli 'włączony', serwer wyświetli zawartość folderów.");
define('LANG_fast_dl_advanced', "Zaawansowane Ustawienia");
define('LANG_apply_settings_and_restart_fastdl', "Zapisz konfigurację oraz wykonaj restart daemona.");
define('LANG_stop_fastdl', "Zatrzymaj daemon szybkiego pobierania");
define('LANG_fast_download_daemon_running', "Fast Download Daemon jest uruchomiony.");
define('LANG_fast_download_daemon_not_running', "Fast Download Daemon jest zatrzymany.");
define('LANG_fastdl_could_not_be_restarted', "Nie można zrestartować usługi Fast Download.");
define('LANG_configuration_file_could_not_be_written', "Nie można zapisać pliku konfiguracyjnego.");
define('LANG_remove_folders', "Usuń folder dla wybranych aliasów.");
define('LANG_remove_folder', "Usuń folder");
define('LANG_delete_alias', "Usuń alias");
define('LANG_no_game_homes_assigned', "Nie masz żadnych serwerów przypisanych do twojego konta.");
define('LANG_select_remote_server', "Wybierz serwer hosta");
define('LANG_access_rules', "Zasady dostępu");
define('LANG_create_aliases', "Stwórz Alias");
define('LANG_select_game', "Wybierz grę");
define('LANG_games_without_specified_rules', "Gry bez określonych reguł");
define('LANG_match_file_extension', "Dopasować rozszerzenie pliku");
define('LANG_match_file_extension_info', "Określ rozszerzenia, oddzielaj przecinkami, <br>które będzie możliwość pobrać<br>, <b>pozostałe będą niedostępne</b>.");
define('LANG_match_client_ip', "Dopasuj klienta IP");
define('LANG_match_client_ip_info', "Połączenia z pasującym adresem IP zostaną przyznane,<br>
puste dla nieograniczonego dostępu.<br>Możesz użyć wiele adresów IP lub zakresów oddzielonych przecinkami:<br>
/xx podsieci<br> Przykład: 10.0.0.0/16<br> /xxx.xxx.xxx.xxx podsieci<br> Przykład: 10.0.0.0/255.0.0.0<br> Dzielniki<br> Przykład: 10.0.0.5-230<br> Gwiazdki<br> Przykład: 10.0.*.*");
define('LANG_save_access_rules', "Zapisz zasady dostępu");
define('LANG_create_access_rules', "Stwórz reguły dostępu");
define('LANG_invalid_entries_found', "Znaleziono nieprawidłowe wpisy");
define('LANG_game_name', "Nazwa Gry");
define('LANG_alias_already_exists', "Alias %s już istnieje.");
define('LANG_warning_access_rules_applied_once_alias_created', "UWAGA: Reguły dostępu są stosowane przy tworzeniu aliasu. W obecnych aliasach nie będą stosowane żadne zmiany.");
define('LANG_autostart_on_agent_startup', "Autostart podczas uruchamiania agenta");
define('LANG_autostart_on_agent_startup_info', "Start the fast download daemon automatically when the Agent starts.");
define('LANG_port_forwarded_to_80', "Przekierowanie portu na 80");
define('LANG_port_forwarded_to_80_info', "Włącz tą opcję gdy Fast Download będzie działał na domyślnym porcie 80,  port nie będzie wyświetlony w adresie URL");
define('LANG_current_access_rules', "Aktualne zasady dostępu");
?>