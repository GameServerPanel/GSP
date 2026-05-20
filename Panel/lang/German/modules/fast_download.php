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

define('LANG_create_alias', "Alias und Ordner erstellen");
define('LANG_save_as', "Speichern als");
define('LANG_failure', "Fehler, konnte die Alias-Datei nicht generieren");
define('LANG_success', "Erfolg");
define('LANG_fast_download_service_for', "Downloads Umleitungsservice für %s");
define('LANG_to_the_path', "Zum Pfad");
define('LANG_at_url', "zu URL");
define('LANG_create_alias_for', "Alias erstellen für");
define('LANG_fast_dl', "Weiterleiten von Downloads (FastDL)");
define('LANG_current_aliases_at_remote_server', "Aktuelle Aliase auf dem entfernten Server");
define('LANG_delete_selected_aliases', "Ausgewählte Aliase löschen");
define('LANG_no_aliases_defined', "Derzeit wurde kein Web-Alias via OGP für diesen Remote Server definiert.");
define('LANG_fastdl_port', "Port");
define('LANG_fastdl_port_info', "Port, auf dem der Fast Download Daemon gestartet wird.");
define('LANG_fastdl_ip', "Adresse");
define('LANG_fastdl_ip_info', "Die IP Addresse oder Domain Name, in welchem Ihr Fast Download Server starten möchte, muss unter /etc/hosts aufgeführt sein.");
define('LANG_listing', "Auflistung");
define('LANG_listing_info', "Wenn 'an', wird der Server den Inhalt der Ordner auflisten.");
define('LANG_fast_dl_advanced', "Erweiterte Einstellungen");
define('LANG_apply_settings_and_restart_fastdl', "Daemon Konfiguration speichern und neustarten");
define('LANG_stop_fastdl', "FastDL Daemon starten.");
define('LANG_fast_download_daemon_running', "FastDL Daemon läuft.");
define('LANG_fast_download_daemon_not_running', "FastDL Daemon läuft nicht.");
define('LANG_fastdl_could_not_be_restarted', "Der FastDL Dienst konnte nicht neu gestartet werden.");
define('LANG_configuration_file_could_not_be_written', "Die Konfigurationsdatei konnte nicht geschrieben werden.");
define('LANG_remove_folders', "Ordner für ausgewählte Aliase entfernen.");
define('LANG_remove_folder', "Ordner löschen");
define('LANG_delete_alias', "Alias löschen");
define('LANG_no_game_homes_assigned', "Ihrem Account sind keine Server zugewiesen.");
define('LANG_select_remote_server', "Remote Server auswählen");
define('LANG_access_rules', "Zugriffsregeln");
define('LANG_create_aliases', "Aliase erstellen");
define('LANG_select_game', "Spiel auswählen");
define('LANG_games_without_specified_rules', "Spiele ohne spezifizierte Regeln");
define('LANG_match_file_extension', "Identische Dateierweiterung");
define('LANG_match_file_extension_info', "Zugriffe via Kommagetrennte Dateierweiterungen erlauben.<br> <b>Leer für uneingeschränkten Zugriff</b>.");
define('LANG_match_client_ip', "Client IP identisch");
define('LANG_match_client_ip_info', "Zugriff für eingegebene IP's erlauben,<br>leer für uneingeschränkten Zugriff. Sie können auch<br>mehrere IP Adressen oder ganze IP Ranges kommagetrennt einfügen.<br>/xx Subnetze<br>Beispiel: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx Subnetze<br>Beispiel: 10.0.0.0/255.0.0.0<br>Hyphen Ranges<br>Beispiel: 10.0.0.5-230<br>Asterisk Treffer<br>Beispiel: 10.0.*.*");
define('LANG_save_access_rules', "Zugriffsregeln speichern");
define('LANG_create_access_rules', "Zugriffsregeln erstellen");
define('LANG_invalid_entries_found', "Ungültige Einträge gefunden");
define('LANG_game_name', "Spielname");
define('LANG_alias_already_exists', "Alias %s existiert bereits.");
define('LANG_warning_access_rules_applied_once_alias_created', "WARNUNG: beim Erstellen des Alias werden Zugriffsregeln angewendet. Auf die aktuellen Aliase werden keine Änderungen übernommen.");
define('LANG_autostart_on_agent_startup', "Autostart beim Agentenstart");
define('LANG_autostart_on_agent_startup_info', "Starten Sie den schnellen Download Daemon automatisch, wenn der Agent startet.");
define('LANG_port_forwarded_to_80', "Port weitergeleitet von 80");
define('LANG_port_forwarded_to_80_info', "Aktivieren Sie diese Option, wenn der für diesen FastDL Daemon konfigurierte Port von Port 80 aus weitergeleitet wurde, sodass der Port bei URLs verborgen bleibt.");
define('LANG_current_access_rules', "Aktuelle Zugriffsregeln");
?>