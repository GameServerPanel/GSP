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

define('LANG_your_profile', "Dein Profil");
define('LANG_new_password', "Neues Passwort");
define('LANG_retype_new_password', "Passwort wiederholen");
define('LANG_login_name', "Benutzername");
define('LANG_language', "Sprache");
define('LANG_first_name', "Vorname");
define('LANG_page_limit', "Objekte pro Seite");
define('LANG_page_limit_info', "Anzahl der Items die pro Seite angezeigt werden. Die Nummer kann nicht kleiner als 10 sein.");
define('LANG_last_name', "Nachname");
define('LANG_phone_number', "Telefonnummer");
define('LANG_email_address', "E-Mail Adresse");
define('LANG_city', "Stadt");
define('LANG_province', "Bundesland/Kanton");
define('LANG_country', "Land");
define('LANG_comment', "Kommentar");
define('LANG_expires', "Läuft ab");
define('LANG_save_profile', "Profil speichern");
define('LANG_new_password_info', "When password field is empty the password will not be updated.");
define('LANG_theme', "Thema");
define('LANG_theme_info', "Wenn Theme leer ist, wird der globale Wert verwendet.");
define('LANG_expires_info', "Datum, an dem das Benutzerkonto abläuft. Das Konto wird nicht gelöscht, aber der Benutzer kann sich nicht mehr einloggen.");
define('LANG_password_mismatch', "Die Passwörter stimmen nicht überein.");
define('LANG_current_password', "Aktuelles Passwort");
define('LANG_current_password_info', "Dein aktuelles Passwort.");
define('LANG_current_password_mismatch', "Ihr aktuelles Passwort stimmt nicht mit dem in der Datenbank überein.");
define('LANG_add_new_user', "Neuen Benutzer hinzufügen");
define('LANG_edit_user_groups', "Benutzergruppen bearbeiten");
define('LANG_users', "Benutzer");
define('LANG_user_role', "Rolle");
define('LANG_full_name', "Vollständiger Name");
define('LANG_edit_games', "Spiele bearbeiten");
define('LANG_edit_profile', "Profil bearbeiten");
define('LANG_confirm_password', "Passwort wiederholen");
define('LANG_you_need_to_enter_both_passwords', "Sie müssen beide Passwörter eingeben.");
define('LANG_passwords_did_not_match', "Die Passwörter stimmen nicht überein.");
define('LANG_could_not_add_user_because_user_already_exists', "Benutzer konnte nicht hinzugefügt werden, da Benutzer <em> %s </em> bereits vorhanden ist.");
define('LANG_successfully_added_user', "Successfully added user <em>%s</em>.");
define('LANG_add_a_new_user', "Einen neuen Benutzer hinzufügen");
define('LANG_admin', "Admin");
define('LANG_user', "Benutzer");
define('LANG_user_with_id_does_not_exist', "User mit der ID %s existiert nicht.");
define('LANG_are_you_sure_you_want_to_delete_user', "Bist du sicher das du den User %s löschen möchtest?");
define('LANG_unable_to_delete_user', "Benutzer %skann nicht gelöscht werden.");
define('LANG_successfully_deleted_user', "Der User %s wurde erfolgreich gelöscht.");
define('LANG_failed_to_update_user_profile_error', "Das Profile konnte nicht aktualisierst werden. Error: %s");
define('LANG_profile_of_user_modified_successfully', "Profile von %swurde erfolgreich geändert.");
define('LANG_no_subusers', "Es sind keine Unterbenutzer verfügbar, die einer Gruppe zugeordnet werden können. Bitte erstellen Sie ein Subbenutzerkonto.");
define('LANG_ownedby', "Übergeordneter Besitzer");
define('LANG_andSubUsers', "Und alle seine Unterbenutzer?");
define('LANG_subusers', "Unterbenutzer");
define('LANG_show_subusers', "Zeige Unterbenutzer");
define('LANG_hide_subusers', "Verstecke Unterbenutzer");
define('LANG_info_group', "Auf dieser Seite können Benutzergruppen bestimmt werden. Sie können Gruppe, Servern zuweisen, sodass sie für alle Gruppenbenutzer verfügbar sind.");
define('LANG_add_new_group', "Neue Gruppe hinzufügen");
define('LANG_group_name', "Gruppen Name");
define('LANG_add_group', "Gruppe hinzufügen");
define('LANG_no_groups_available', "Keine Gruppen verfügbar.");
define('LANG_delete_group', "Gruppe löschen");
define('LANG_add_user_to_group', "Benutzer zur Gruppe hinzufügen");
define('LANG_add_user', "Benutzer hinzufügen");
define('LANG_remove_from_group', "Benutzer von Gruppe entfernen");
define('LANG_add_server_to_group', "Server zur Gruppe hinzufügen");
define('LANG_add_server', "Server hinzufügen");
define('LANG_servers_in_group', "Server in der Gruppe");
define('LANG_no_servers_in_group', "Keine Server in der Gruppe: %s");
define('LANG_available_groups', "Verfügbare Gruppen");
define('LANG_assign_homes', "Homes zuweisen");
define('LANG_successfully_added_group', "Erfolgreich zur Gruppe %s hinzugefügt.");
define('LANG_group_name_empty', "Der Gruppen Name darf nicht leer sein.");
define('LANG_failed_to_add_group', "Fehler beim hinzufügen der Gruppe %s");
define('LANG_could_not_add_user_to_group', "Der User %s konnte nicht zur Gruppe %s hinzugefügt werden weil er schon dazu gehört.");
define('LANG_successfully_added_to_group', "%s erfolgreich zur Gruppe %shinzugefügt.");
define('LANG_could_not_add_server_to_group', "Der Server konnten nicht zur Gruppe %s hinzugefügt werden weil er schon dazu gehört.");
define('LANG_successfully_added_server_to_group', "Server Erfolgreich zur Gruppe %shinzugefügt.");
define('LANG_successfully_removed_from_group', "%s erfolgreich von der Gruppe %s entfernt");
define('LANG_could_not_delete_server_from_group', "Der Server %s konnte nicht von der Gruppe %s entfernt werden.");
define('LANG_successfully_removed_server_from_group', "Der Server %s wurde Erfolgreich von der Gruppe %s entfernt.");
define('LANG_group_with_id_does_not_exist', "Die Gruppe %s existiert nicht.");
define('LANG_are_you_sure_you_want_to_delete_group', "Bist du sicher das du die Gruppe %s löschen möchtest?");
define('LANG_unable_to_delete_group', "Die Gruppe %s kann nicht gelöscht werden.");
define('LANG_successfully_deleted_group', "Gruppe %s erfolgreich gelöscht.");
define('LANG_editing_profile', "Profile %s bearbeiten");
define('LANG_valid_user', "Bitte geben Sie einen gültigen Benutzer an.");
define('LANG_enter_valid_username', "Bitte geben Sie einen gültigen Benutzername ein.");
define('LANG_unexpected_role', "Unerwartete Benutzerrolle erhalten.");
define('LANG_search', "Suche");
define('LANG_api_token', "API Schlüssel");
define('LANG_user_receives_emails', "Receive emails");
?>
