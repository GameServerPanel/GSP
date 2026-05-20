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

define('LANG_configured_mysql_hosts', "Konfigurirani MySQL hostovi");
define('LANG_add_new_mysql_host', "Dodati MySQL host");
define('LANG_enter_mysql_ip', "Upisati MySQL IP");
define('LANG_enter_valid_port', "Unesite valjani port.");
define('LANG_enter_mysql_root_password', "Unesite root lozinku za MySQL");
define('LANG_enter_mysql_name', "Unesite korisničko ime za MySQL");
define('LANG_could_not_add_mysql_server', "Nije moguće dodati MySQL poslužitelj.");
define('LANG_game_server_name_info', "Ime poslužitelja pomaže korisnicima da identificiraju svoje poslužitelje.");
define('LANG_note_mysql_host', "Napomena: Korištenjem \"Izravne veze\" poslužitelj mora prihvatiti vanjske veze tako da se poslužitelji mogu povezati na daljinu, dok će se povezivanje putem udaljenog poslužitelja koristiti kao lokalna veza.");
define('LANG_direct_connection', "Izravna veza");
define('LANG_connection_through_remote_server_named', "Povezivanje putem udaljenog poslužitelja nazvanog %s");
define('LANG_add_mysql_server', "Dodati MySQL poslužitelj");
define('LANG_mysql_online', "MySQL na mreži");
define('LANG_mysql_offline', "MySQL nije na mreži");
define('LANG_encryption_key_mismatch', "Nepodudaranje ključa za šifriranje");
define('LANG_unknown_error', "Nepoznata pogreška");
define('LANG_remove', "Izbrisati");
define('LANG_assign_db', "Dodijeli bazu podataka");
define('LANG_mysql_server_name', "Naziv MySQL poslužitelja");
define('LANG_server_status', "Status poslužitelja");
define('LANG_mysql_ip_port', "MySQL IP:port");
define('LANG_mysql_root_passwd', "MySQL root lozinka");
define('LANG_connection_method', "Način povezivanja");
define('LANG_user_privilegies', "Korisničke privilegije");
define('LANG_current_dbs', "Trenutne baze podataka");
define('LANG_mysql_name', "Naziv MySQL poslužitelja");
define('LANG_mysql_ip', "MySQL IP");
define('LANG_mysql_port', "MySQL port");
define('LANG_privilegies', "privilegije");
define('LANG_all', "Sve");
define('LANG_custom', "Prilagođeno");
define('LANG_server_added', "Poslužitelj uspješno dodan.");
define('LANG_sql_alter', "ALTER");
define('LANG_sql_create', "KREIRATI");
define('LANG_sql_create_temporary_tables', "IZRADE TEMPORARNE TABLICE");
define('LANG_sql_drop', "OTPUSTITI");
define('LANG_sql_index', "INDEKS");
define('LANG_sql_insert', "UBACIVATI");
define('LANG_sql_lock_tables', "ZAKLJUČATI TABLICE");
define('LANG_sql_select', "ODABRATI");
define('LANG_sql_grant_option', "POTVRDA OPCIJA");
define('LANG_sql_update', "AŽURIRATI");
define('LANG_sql_delete', "IZBRISATI");
define('LANG_sql_alter_info', "<b>Omogućuje upotrebu ALTER TABLICE.</b>");	
define('LANG_sql_create_info', "<b>Omogućuje upotrebu KREIRAJ TABLICE.</b>");	
define('LANG_sql_create_temporary_tables_info', "<b>Omogućuje upotrebu IZRADE TEMPORARNE TABLICE.</b>");
define('LANG_sql_delete_info', "<b>Omogućuje upotrebu IZBRISATI.</b>");
define('LANG_sql_drop_info', "<b>Omogućuje upotrebu OTPUSTITI TABLICE.</b>");	
define('LANG_sql_index_info', "<b>Omogućuje upotrebu KREIRAJ INDEKS i OTPUSTITI INDEKS.</b>");	
define('LANG_sql_insert_info', "<b>Omogućuje upotrebu UBACIVATI.</b>");	
define('LANG_sql_lock_tables_info', "<b>Omogućuje upotrebu ZAKLJUČATI TABLICE na tablicama na koje imate ODABRATI privilegije.</b>");	
define('LANG_sql_select_info', "<b>Omogućuje upotrebu ODABRATI.</b>");
define('LANG_sql_update_info', "<b>Omogućuje upotrebu AŽURIRATI.</b>");	
define('LANG_sql_grant_option_info', "<b>Omogućuje privilegije za potvrdu.</b>");
define('LANG_select_game_server', "Odaberite igre");
define('LANG_invalid_mysql_server_id', "Nevažeći ID MySQL poslužitelja.");
define('LANG_there_is_another_db_named_or_user_named', "Postoji još jedna baza podataka pod nazivom <b>%s</b> ili drugog imenovanog korisnika<b>%s</b>.");
define('LANG_db_added_for_home_id', "Dodana baza podataka za Home ID <b>%s</b>.");
define('LANG_could_not_remove_db', "Odabrana baza podataka nije mogla biti uklonjena.");
define('LANG_db_removed_successfully_from_mysql_server_named', "Baza podataka je uklonjena s poslužitelja %s.");
define('LANG_areyousure_remove_mysql_server', "Jeste li sigurni da želite ukloniti MySQL poslužitelj nazvan <b>%s</b>?");
define('LANG_db_changed_successfully', "Baza podataka pod nazivom %s je uspješno promijenjena.");
define('LANG_error_while_remove', "Pogreška prilikom uklanjanja.");
define('LANG_mysql_server_removed', "MySQL poslužitelj nazvan <b>%s</b> je uspješno uklonjen.");
define('LANG_unable_to_set_changes_to', "Nije moguće postaviti promjene na MySQL poslužitelj nazvan <b>%s</b>.");
define('LANG_mysql_server_settings_changed', "MySQL poslužitelj nazvan <b>%s</b> je uspješno promijenjen.");
define('LANG_editing_mysql_server', "Uređivanje MySQL poslužitelja nazvanog <b>%s</b>.");
define('LANG_save_settings', "Spremi postavke");
define('LANG_mysql_dbs_for', "Baze podataka za poslužitelj %s");
define('LANG_edit_dbs', "Uredite baze podataka");
define('LANG_edit_db_settings', "Uredite postavke baze podataka");
define('LANG_remove_db', "Ukloniti bazu podataka");
define('LANG_save_db_changes', "Spremi promjene baze podataka.");
define('LANG_add_db', "Dodaj bazu podataka");
define('LANG_select_db', "Odaberite bazu podataka");
define('LANG_db_user', "BP Korisnik");
define('LANG_db_passwd', "BP Lozinka");
define('LANG_db_name', "BP Naziv");
define('LANG_enabled', "Omogućeno");
define('LANG_game_server', "Igrice");
define('LANG_there_are_no_databases_assigned_for', "Nema dodijeljenih baza podataka za <b>%s</b>.");
define('LANG_unable_to_connect_to_mysql_server_as', "Nije moguće povezati se na MySQL poslužitelj kao %s.");
define('LANG_unable_to_create_db', "Nije moguće stvoriti bazu podataka.");
define('LANG_unable_to_select_db', "Nije moguće odabrati %s bazu podataka.");
define('LANG_db_info', "Informacije o bazama podataka");
define('LANG_db_tables', "Tablice baza podataka");
define('LANG_db_backup', "BP Backup");
define('LANG_download_db_backup', "Skini BP Backup");
define('LANG_restore_db_backup', "Vratite DB Backup");
define('LANG_sql_file', "datoteka(.sql)");
?>