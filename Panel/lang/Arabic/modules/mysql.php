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

define('LANG_configured_mysql_hosts', "Configured MySQL Hosts");
define('LANG_add_new_mysql_host', "إضافة مضيف ميسكل");
define('LANG_enter_mysql_ip', "تفعيل ايبي ميسكل.");
define('LANG_enter_valid_port', "أدخل بورت صالح.");
define('LANG_enter_mysql_root_password', "أدخل كلمة مرور روت الميسكل.");
define('LANG_enter_mysql_name', "أدخل إسم الميسكل.");
define('LANG_could_not_add_mysql_server', "تعذر إضافة خادم ميسكل.");
define('LANG_game_server_name_info', "Server name helps users to identify their servers.");
define('LANG_note_mysql_host', "Note: Using a 'Direct connection' the server must accept external connections so the servers can connect remotely, whereas connecting through a remote server it will be used just as a local connection.");
define('LANG_direct_connection', "اتصال مباشر");
define('LANG_connection_through_remote_server_named', "Connection through remote server named %s");
define('LANG_add_mysql_server', "إضافة سيرفر ميسكل");
define('LANG_mysql_online', "ميسكل متصل");
define('LANG_mysql_offline', "ميسكل غير-متصل");
define('LANG_encryption_key_mismatch', "عدم تطابق مفتاح التشفير");
define('LANG_unknown_error', "خطأ غير معروف");
define('LANG_remove', "حذف");
define('LANG_assign_db', "تعيين قاعدة البيانات");
define('LANG_mysql_server_name', "اسم سيرفر ميسكل");
define('LANG_server_status', "حالة السيرفر");
define('LANG_mysql_ip_port', "ميسكل أيبي:بورت");
define('LANG_mysql_root_passwd', "كلمة مرور روت الميسكل");
define('LANG_connection_method', "Connection method");
define('LANG_user_privilegies', "User privileges");
define('LANG_current_dbs', "Current databases");
define('LANG_mysql_name', "MySQL server name");
define('LANG_mysql_ip', "MySQL IP");
define('LANG_mysql_port', "MySQL port");
define('LANG_privilegies', "privileges");
define('LANG_all', "All");
define('LANG_custom', "Custom");
define('LANG_server_added', "Server added.");
define('LANG_sql_alter', "ALTER");
define('LANG_sql_create', "CREATE");
define('LANG_sql_create_temporary_tables', "CREATE TEMPORARY TABLES");
define('LANG_sql_drop', "DROP");
define('LANG_sql_index', "INDEX");
define('LANG_sql_insert', "INSERT");
define('LANG_sql_lock_tables', "LOCK TABLES");
define('LANG_sql_select', "SELECT");
define('LANG_sql_grant_option', "GRANT OPTION");
define('LANG_sql_update', "UPDATE");
define('LANG_sql_delete', "DELETE");
define('LANG_sql_alter_info', "<b>Enables use of ALTER TABLE.</b>");	
define('LANG_sql_create_info', "<b>Enables use of CREATE TABLE.</b>");	
define('LANG_sql_create_temporary_tables_info', "<b>Enables use of CREATE TEMPORARY TABLE.</b>");
define('LANG_sql_delete_info', "<b>Enables use of DELETE.</b>");
define('LANG_sql_drop_info', "<b>Enables use of DROP TABLE.</b>");	
define('LANG_sql_index_info', "<b>Enables use of CREATE INDEX and DROP INDEX.</b>");	
define('LANG_sql_insert_info', "<b>Enables use of INSERT.</b>");	
define('LANG_sql_lock_tables_info', "<b>Enables use of LOCK TABLES on tables for which you have the SELECT privilege.</b>");	
define('LANG_sql_select_info', "<b>Enables use of SELECT.</b>");
define('LANG_sql_update_info', "<b>Enables use of UPDATE.</b>");	
define('LANG_sql_grant_option_info', "<b>Enables privileges to be granted.</b>");
define('LANG_select_game_server', "Select game server");
define('LANG_invalid_mysql_server_id', "Invalid MySQL server ID.");
define('LANG_there_is_another_db_named_or_user_named', "There is another database named <b>%s</b> or another user named <b>%s</b>.");
define('LANG_db_added_for_home_id', "Added database for home ID <b>%s</b>.");
define('LANG_could_not_remove_db', "The selected database could not be removed.");
define('LANG_db_removed_successfully_from_mysql_server_named', "The database was removed from server %s.");
define('LANG_areyousure_remove_mysql_server', "Are you sure that you want remove MySQL server named <b>%s</b>?");
define('LANG_db_changed_successfully', "The database named %s was changed successfully.");
define('LANG_error_while_remove', "Error while remove.");
define('LANG_mysql_server_removed', "MySQL server named <b>%s</b> has been removed successfully.");
define('LANG_unable_to_set_changes_to', "Unable to set changes to MySQL server named <b>%s</b>.");
define('LANG_mysql_server_settings_changed', "MySQL server named <b>%s</b> has been changed successfully.");
define('LANG_editing_mysql_server', "Editing MySQL server named <b>%s</b>.");
define('LANG_save_settings', "Save settings");
define('LANG_mysql_dbs_for', "Databases for server %s");
define('LANG_edit_dbs', "Edit databases");
define('LANG_edit_db_settings', "Edit database settings");
define('LANG_remove_db', "Remove database");
define('LANG_save_db_changes', "Save database changes.");
define('LANG_add_db', "Add database");
define('LANG_select_db', "Select database");
define('LANG_db_user', "DB User");
define('LANG_db_passwd', "DB Password");
define('LANG_db_name', "DB name");
define('LANG_enabled', "Enabled");
define('LANG_game_server', "Game server");
define('LANG_there_are_no_databases_assigned_for', "There are no databases assigned for <b>%s</b>.");
define('LANG_unable_to_connect_to_mysql_server_as', "Unable to connect to MySQL server as %s.");
define('LANG_unable_to_create_db', "Unable to create database.");
define('LANG_unable_to_select_db', "Unable to select database %s.");
define('LANG_db_info', "Database Information");
define('LANG_db_tables', "Database tables");
define('LANG_db_backup', "DB Backup");
define('LANG_download_db_backup', "Download DB Backup");
define('LANG_restore_db_backup', "Restore DB Backup");
define('LANG_sql_file', "file(.sql)");
?>