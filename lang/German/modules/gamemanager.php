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

define('LANG_no_games_to_monitor', "Es gibt derzeit keine Gameserver die online/offline sind");
define('LANG_status', "Status");
define('LANG_fail_no_mods', "Keine Mods für dieses Spiel aktiviert! Sie müssen einen OGP Admin bitten, Mods für das Spiel hinzuzufügen, das Ihnen zugeteilt wird.");
define('LANG_no_game_homes_assigned', "Ihrem Konto sind keine Server zugewiesen.");
define('LANG_select_game_home_to_configure', "Wählen Sie einen Gameserver aus, den Sie konfigurieren möchten");
define('LANG_file_manager', "Dateimanager");
define('LANG_configure_mods', "Mods verwalten");
define('LANG_install_update_steam', "Installation/Aktualisierung via Steam");
define('LANG_install_update_manual', "Manuelle Installation/Aktualisierung");
define('LANG_assign_game_homes', "Spielserver zuweisen
");
define('LANG_user', "Benutzer");
define('LANG_group', "Gruppe");
define('LANG_start', "Start");
define('LANG_ogp_agent_ip', "OGP Agent IP");
define('LANG_max_players', "Max Spieler");
define('LANG_max', "Max");
define('LANG_ip_and_port', "IP und Port");
define('LANG_available_maps', "Verfügbare Maps");
define('LANG_map_path', "Map Pfad");
define('LANG_available_parameters', "Verfügbare Parameter");
define('LANG_start_server', "Server starten");
define('LANG_start_wait_note', "Der Server start kann eine Weile dauern. Bitte warten Sie, ohne Ihren Browser zu schließen.");
define('LANG_game_type', "Game Typ");
define('LANG_map', "Karte");
define('LANG_starting_server', "Starte Server, bitte warten...");
define('LANG_starting_server_settings', "Starte Server mit den folgenden Einstellungen");
define('LANG_startup_params', "Startparameter");
define('LANG_startup_cpu', "CPU auf dem der Server läuft");
define('LANG_startup_nice', "Nice Wert des Servers
");
define('LANG_game_home', "Game Pfad");
define('LANG_server_started', "Server erfolgreich gestartet.");
define('LANG_no_parameter_access', "Sie haben keinen Zugriff auf die Parameter.");
define('LANG_extra_parameters', "Zusätzliche Parameter");
define('LANG_no_extra_param_access', "Sie haben keinen Zugriff auf die zusätzlichen Parameter.");
define('LANG_extra_parameters_info', "Diese Parameter werden am Ende der Befehlszeile gesetzt, wenn das Spiel gestartet wird.");
define('LANG_game_exec_not_found', "Die ausführbare Datei %s wurde vom Remoteserver nicht gefunden.");
define('LANG_select_params_and_start', "Wählen Sie die start parameter für den Server aus und drücken Sie '%s'.");
define('LANG_no_ip_port_pairs_assigned', "Diesem home wurde keine IP und kein Port zugewiesen. Wenn du keinen Zugriff auf die Bearbeitung eines Homes hast, kontaktiere einen Admin");
define('LANG_unable_to_get_log', "Log konnte nicht abgerufen werden, retval %s.");
define('LANG_server_binary_not_executable', "Server-Binärdatei ist nicht ausführbar.
Prüfe deine Rechte für das home Verzeichnis.");
define('LANG_server_not_running_log_found', "Server wurde nicht gestartet, ein Protokoll wurde erstellt. HINWEIS: Das Protokoll wurde möglicherweise seit dem letzten Serverstart nicht erstellt.");
define('LANG_ip_port_pair_not_owned', "IP:PORT Port möglicherweise geschlossen");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Ungültiger maxplayers Wert, maximal mögliche Anzahl von Slots wurde gesetzt.");
define('LANG_server_running_not_responding', "Server wurde gestartet aber antwortet nicht.<br>Es besteht möglicherweise ein bekanntes Problem.");
define('LANG_update_started', "Update gestartet, bitte warten...");
define('LANG_failed_to_start_steam_update', "Steam update ist fehlgeschlagen. Siehe Agenten Log.");
define('LANG_failed_to_start_rsync_update', "Rsync update ist fehlgeschlagen. Siehe Agenten Log.");
define('LANG_update_completed', "Update erfolgreich abgeschlossen.
");
define('LANG_update_in_progress', "Update im Gange, bitte warten...");
define('LANG_refresh_steam_status', "Steam-Status aktualisieren");
define('LANG_refresh_rsync_status', "Aktualisiere Rsync-Status");
define('LANG_server_running_cant_update', "Ein Update ist nicht möglich, da der Server derzeit läuft. Stoppe den Server bevor du ihn updatest.");
define('LANG_xml_steam_error', "Der ausgewählte Servertyp unterstützt die Installation / Aktualisierung von Steam nicht.");
define('LANG_mod_key_not_found_from_xml', "Mod key '%s' wurde von der XML Datei nicht gefunden.");
define('LANG_stop_update', "Update stoppen");
define('LANG_statistics', "Statistik");
define('LANG_servers', "Servers");
define('LANG_players', "Spieler");
define('LANG_current_map', "Aktuelle Map");
define('LANG_stop_server', "Server stoppen");
define('LANG_server_ip_port', "Server-IP: Port");
define('LANG_server_name', "Server Name");
define('LANG_server_id', "Server-ID");
define('LANG_player_name', "Spielername");
define('LANG_score', "Punkte");
define('LANG_time', "Zeit");
define('LANG_no_rights_to_stop_server', "Du hast nicht die Berechtigung den Server zu stoppen.");
define('LANG_no_ogp_lgsl_support', "Dieser Server ( %s ) hat keine LGSL-Unterstützung in OGP und seine Statistiken können nicht angezeigt werden.");
define('LANG_server_status', "Server auf %s ist %s.");
define('LANG_server_stopped', "Server '%s' wurde gestoppt.
");
define('LANG_if_want_to_start_homes', "Wenn du Spieleserver starten willst, gehtst zu %s.");
define('LANG_view_log', "Log");
define('LANG_if_want_manage', "Wenn du deine Spiele verwalten möchtest kannst du das in der");
define('LANG_columns', "Spalten");
define('LANG_group_users', "Gruppenbenutzer: ");
define('LANG_assigned_to', "Zugewiesen an:");
define('LANG_restart_server', "Server neustarten");
define('LANG_restarting_server', "Server wird neugestartet, bitte warten...");
define('LANG_server_restarted', "Server '%s' wurde neugestartet.");
define('LANG_server_not_running', "Der Server läuft nicht.");
define('LANG_address', "Adresse");
define('LANG_owner', "Besitzer");
define('LANG_operations', "Aktionen");
define('LANG_search', "Suche");
define('LANG_maps_read_from', "Karten lesen aus");
define('LANG_file', "Datei");
define('LANG_folder', "Ordner");
define('LANG_unable_retrieve_mod_info', "Konnte mod Informationen nicht aus der Datenbank abrufen.");
define('LANG_unexpected_result_libremote', "Unerwartetes Ergebnis von libremote, bitte informiere den Entwickler.");
define('LANG_unable_get_info', "Die erforderlichen Informationen für den Start konnten nicht abgerufen werden, wodurch der Startvorgang blockiert wurde.");
define('LANG_server_already_running', "Server läuft bereits. Wenn du den Server nicht im Spielemonitor sehen kannst, besteht möglicherweise ein Problem, das du selbst beheben kannst.");
define('LANG_already_running_stop_server', "Server stoppen.");
define('LANG_error_server_already_running', "FEHLER: Der Server läuft bereits auf Port");
define('LANG_failed_start_server_code', "Der Remoteserver konnte nicht gestartet werden. Fehlercode: %s");
define('LANG_disabled', "deaktiviert");
define('LANG_not_found_server', "Konnte Remoteserver nicht finden. ID ");
define('LANG_rcon_command_title', "RCON Befehle");
define('LANG_has_sent_to', "Wurde gesendet zu");
define('LANG_need_set_remote_pass', "Das remote control Passwort muss aktiviert werden");
define('LANG_before_sending_rcon_com', "bevor du rcon Befehle sendest.");
define('LANG_retry', "Wiederholen");
define('LANG_page', "seite");
define('LANG_server_cant_start', "Server kann nicht starten");
define('LANG_server_cant_stop', "Server kann nicht stoppen");
define('LANG_error_occured_remote_host', "Auf dem entfernten Host ist Fehler aufgetreten");
define('LANG_follow_server_status', "Sie können dem Serverstatus folgen von");
define('LANG_addons', "Addons");
define('LANG_hostname', "Hostnamen");
define('LANG_rsync_install', "[Rsync Install]");
define('LANG_ping', "Ping");
define('LANG_team', "Team");
define('LANG_deaths', "Tote");
define('LANG_pid', "PID");
define('LANG_skill', "Skill");
define('LANG_AIBot', "AiBot");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Spieler");
define('LANG_port', "Port");
define('LANG_rcon_presets', "RCON Voreinstellungen
");
define('LANG_update_from_local_master_server', "Update von lokalen Hauptserver");
define('LANG_update_from_selected_rsync_server', "Update von gewählten Rsync Server");
define('LANG_execute_selected_server_operations', "Führe ausgewählte Serveroperationen aus");
define('LANG_execute_operations', "Operationen ausführen");
define('LANG_account_expiration', "Kontoablauf");
define('LANG_mysql_databases', "MySQL Datenbank");
define('LANG_failed_querying_server', "* Fehler beim Abfragen des Servers.");
define('LANG_query_protocol_not_supported', "* Es gibt kein Abfrageprotokoll in OGP, das diesen Server unterstützen kann.
");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Abfragen durch Einstellung abgeschaltet: Abfragen nach %s abgeschaltet, da sie %s Server haben.");
define('LANG_presets_for_game_and_mod', "RCON Voreinstellungen für %s und mod %s");
define('LANG_name', "Name");
define('LANG_command', "RCON&nbsp;Command");
define('LANG_add_preset', "Voreinstellung hinzufügen");
define('LANG_edit_presets', "Voreinstellungen bearbeiten");
define('LANG_del_preset', "Löschen");
define('LANG_change_preset', "Ändern");
define('LANG_send_command', "Befehl senden");
define('LANG_starting_copy_with_master_server_named', "Starte Kopie mit Masterserver '%s'...");
define('LANG_starting_sync_with', "Starte sync mit %s...");
define('LANG_refresh_interval', "Aktualisierungsrate des Logs");
define('LANG_finished_manual_update', "Manuelle Aktualisierung beendet.");
define('LANG_failed_to_start_file_download', "Konnte Datei-Download nicht starten.");
define('LANG_game_name', "Spiel name");
define('LANG_dest_dir', "Zielverzeichnis");
define('LANG_remote_server', "Remote-Server");
define('LANG_file_url', "Datei-URL");
define('LANG_file_url_info', "Die URL der Datei, die in das Verzeichnis hochgeladen wurde wird nicht komprimiert.");
define('LANG_dest_filename', "Ziel Dateiname");
define('LANG_dest_filename_info', "Dateiname der Zieldatei.");
define('LANG_update_server', "Server aktualisieren");
define('LANG_unavailable', "Nicht verfügbar");
define('LANG_upload_map_image', "Kartenbild hochladen");
define('LANG_upload_image', "Bild hochladen");
define('LANG_jpg_gif_png_less_than_1mb', "Das Bild muss jpg, gif oder png und weniger als 1 MB sein.");
define('LANG_check_dev_console', "Fehler beim Hochladen der Datei, überprüfen Sie die Browser-Entwicklerkonsole.");
define('LANG_uploaded_successfully', "Erfolgreich hochgeladen.");
define('LANG_cant_create_folder', "Ordner konnte nicht erstellt werden: <br><b>%s</b>");
define('LANG_cant_write_file', "Datei konnte nicht erstellst werden: <br><b>%s</b>");
define('LANG_exceeded_php_directive', "PHP-Direktive überschritten. <br><b>%s</b>.");
define('LANG_unknown_errors', "Unbekannte Fehler.");
define('LANG_directory', "Ordner-Pfad");
define('LANG_view_player_commands', "Befehle für Spieler anzeigen");
define('LANG_hide_player_commands', "Befehle für Spieler verstecken");
define('LANG_no_online_players', "Es sind keine Spieler online.");
define('LANG_invalid_game_mod_id', "Ungültige Spiel/Mod ID angegeben.");
define('LANG_auto_update_title_popup', "Steam Auto Update Link");
define('LANG_auto_update_popup_html', "<p>Verwenden Sie den Link unten, um Ihren Spieleserver bei Bedarf zu überprüfen und automatisch über Steam zu aktualisieren. &Nbsp; Sie können es mit einem Cron-Job abfragen oder den Prozess manuell einleiten.</p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Kopieren");
define('LANG_auto_update_copy_me_success', "Kopiert!");
define('LANG_auto_update_copy_me_fail', "Kopieren fehlgeschlagen. Bitte kopieren Sie den Link von Hand");
define('LANG_get_steam_autoupdate_api_link', "Auto Update Link");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "Benutzer %s hat versucht, home_id %d zu aktualisieren von einem Nicht-Master-Server.
(Verzeichnis ID: %d)");
define('LANG_attempting_nonmaster_update', "Sie versuchen, diesen Server von einem Nicht-Master-Server zu aktualisieren.");
define('LANG_cannot_update_from_own_self', "Das lokale Serverupdate wird möglicherweise nicht auf einem Masterserver ausgeführt.");
define('LANG_show_server_id', "Zeige Server-IDs");
define('LANG_hide_server_id', "Verstecke Server-IDs");
define('LANG_edit_configuration_files', "Konfigurationsdateien bearbeiten");
define('LANG_admin', "Admin");
define('LANG_cid', "CID");
define('LANG_phan', "Phantom");
define('LANG_sec', "Sekunden");
define('LANG_unknown_rsync_mirror', "Sie haben versucht, ein Update von einem nicht vorhandenen Backup zu starten.");
define('LANG_custom_fields', "Benutzerdefinierte Felder");
?>
