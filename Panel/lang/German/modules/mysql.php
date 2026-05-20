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

define('LANG_configured_mysql_hosts', "Konfigurierte MySQL-Hosts");
define('LANG_add_new_mysql_host', "MySQL-Host hinzufügen");
define('LANG_enter_mysql_ip', "Geben Sie MySQL IP ein.");
define('LANG_enter_valid_port', "Geben Sie einen gültigen Port ein.");
define('LANG_enter_mysql_root_password', "Geben Sie MySQL root password ein.");
define('LANG_enter_mysql_name', "Geben Sie den MySQL namen ein.");
define('LANG_could_not_add_mysql_server', "MySQL Server konnte nicht hinzugefügt werden.");
define('LANG_game_server_name_info', "Der Servername hilft Benutzern, Ihre Server zu identifizieren.");
define('LANG_note_mysql_host', "Hinweis: mit einer \"direkten Verbindung\" muss der Server externe Verbindungen annehmen, damit die Server eine Remoteverbindung herstellen können, wohingegen eine Verbindung über einen Remoteserver hergestellt wird, die gerade als lokale Verbindung verwendet wird.");
define('LANG_direct_connection', "Direkte Verbindung");
define('LANG_connection_through_remote_server_named', "Verbindung über Remoteserver mit dem Namen %s");
define('LANG_add_mysql_server', "MySQL Server hinzufügen");
define('LANG_mysql_online', "MySQL online");
define('LANG_mysql_offline', "MySQL offline");
define('LANG_encryption_key_mismatch', "Nichtübereinstimmung der Verschlüsselungsschlüssel");
define('LANG_unknown_error', "Unbekannter Fehler");
define('LANG_remove', "Löschen");
define('LANG_assign_db', "Datenbank zuweisen
");
define('LANG_mysql_server_name', "MySQL server name");
define('LANG_server_status', "Server status");
define('LANG_mysql_ip_port', "MySQL IP:port");
define('LANG_mysql_root_passwd', "MySQL root passwort");
define('LANG_connection_method', "Verbindungsmethode");
define('LANG_user_privilegies', "Benutzerberechtigungen");
define('LANG_current_dbs', "Aktuelle Datenbanken");
define('LANG_mysql_name', "MySQL server name");
define('LANG_mysql_ip', "MySQL IP");
define('LANG_mysql_port', "MySQL port");
define('LANG_privilegies', "privilegien");
define('LANG_all', "Alle");
define('LANG_custom', "Benutzerdefiniert");
define('LANG_server_added', "Server hinzugefügt.");
define('LANG_sql_alter', "ÄNDERN");
define('LANG_sql_create', "ERSTELLEN");
define('LANG_sql_create_temporary_tables', "TEMPORÄRE TABELLEN ERSTELLEN");
define('LANG_sql_drop', "VERWERFEN");
define('LANG_sql_index', "INDEX");
define('LANG_sql_insert', "EINFÜGEN");
define('LANG_sql_lock_tables', "TABELLE SPERREN");
define('LANG_sql_select', "AUSWÄHLEN");
define('LANG_sql_grant_option', "RECHTE OPTIONEN");
define('LANG_sql_update', "AKTUALISIEREN");
define('LANG_sql_delete', "LÖSCHEN");
define('LANG_sql_alter_info', "<B>Aktiviert die Verwendung von TABELLE ÄNDERN.</ B>");	
define('LANG_sql_create_info', "<B>Aktiviert die Verwendung von TABELLE ERSTELLEN.</ B>");	
define('LANG_sql_create_temporary_tables_info', "<B>Aktiviert die Verwendung von TABELLE VERWERFEN.</ B>");
define('LANG_sql_delete_info', "<b>Aktiviert die Verwendung von LÖSCHEN.</b>");
define('LANG_sql_drop_info', "<b>Aktiviert die Verwendung LÖSCHEN.</b>");	
define('LANG_sql_index_info', "<b>Aktiviert die Verwendung INDEX ERSTELLEN und INDEX VERWERFEN.</b>");	
define('LANG_sql_insert_info', "<b>Aktiviert die Verwendung EINFÜGEN.</b>");	
define('LANG_sql_lock_tables_info', "<b>Aktiviert die Verwendung TABELLE SPERREN Auf Tabellen, für die du das SELECT Berechtigung hast.</b>");	
define('LANG_sql_select_info', "<b>Enables use of AUSWÄHLEN.</b>");
define('LANG_sql_update_info', "<b>Enables use of AKTUALISIEREN.</b>");	
define('LANG_sql_grant_option_info', "<b>Ermöglicht die Erteilung von Berechtigungen.</b>");
define('LANG_select_game_server', "Spielserver auswählen");
define('LANG_invalid_mysql_server_id', "Ungültige MySQL Server ID.");
define('LANG_there_is_another_db_named_or_user_named', "Es gibt eine andere Datenbank mit dem Namen <b>%s</ b> oder einen anderen Benutzer mit dem Namen <b>%s</ b>.");
define('LANG_db_added_for_home_id', "Datenbank für Heim ID <b>%s</b> wurde hinzugefügt.");
define('LANG_could_not_remove_db', "Die ausgewählte Datenbank konnte nicht entfernt werden.");
define('LANG_db_removed_successfully_from_mysql_server_named', "Die Datenbank wurde vom Server %s entfernt.");
define('LANG_areyousure_remove_mysql_server', "Sind Sie sicher, dass Sie den MySQL Server mit dem Namen <b>%s</ b> entfernen möchten?");
define('LANG_db_changed_successfully', "Die Datenbank %s wurde erfolgreich geändert.");
define('LANG_error_while_remove', "Fehler beim Entfernen.");
define('LANG_mysql_server_removed', "Der MySQL Server mit dem Namen <b>%s</ b> wurde erfolgreich entfernt.");
define('LANG_unable_to_set_changes_to', "Kann nicht auf MySQL Server mit dem Namen <b>%s</ b> setzen.");
define('LANG_mysql_server_settings_changed', "Kann nicht auf MySQL Server mit dem Namen <b>%s</ b> setzen.");
define('LANG_editing_mysql_server', "Bearbeiten des MySQL Servers mit dem Namen <b>%s</ b>.");
define('LANG_save_settings', "Einstellungen speichern");
define('LANG_mysql_dbs_for', "Datenbanken für Server %s");
define('LANG_edit_dbs', "Datenbanken bearbeiten");
define('LANG_edit_db_settings', "Datenbankeinstellungen bearbeiten");
define('LANG_remove_db', "Datenbank entfernen");
define('LANG_save_db_changes', "Datenbankänderungen speichern");
define('LANG_add_db', "Datenbank hinzufügen");
define('LANG_select_db', "Datenbank auswählen");
define('LANG_db_user', "DB Nutzer");
define('LANG_db_passwd', "DB Passwort");
define('LANG_db_name', "DB Name");
define('LANG_enabled', "Aktiviert");
define('LANG_game_server', "Spielserver");
define('LANG_there_are_no_databases_assigned_for', "Es sind keine Datenbanken für <b>%s</ b> zugeordnet.");
define('LANG_unable_to_connect_to_mysql_server_as', "Kann nicht mit dem MySQL Server als %s verbunden werden.");
define('LANG_unable_to_create_db', "Datenbank kann nicht erstellt werden.");
define('LANG_unable_to_select_db', "Datenbank %s kann nicht ausgewählt werden.");
define('LANG_db_info', "Datenbankinformationen");
define('LANG_db_tables', "Datenbanktabellen");
define('LANG_db_backup', "DB Sicherung");
define('LANG_download_db_backup', "DB Sicherung herunterladen");
define('LANG_restore_db_backup', "DB Sicherung wiederherstellen");
define('LANG_sql_file', "datei(.sql)");
?>