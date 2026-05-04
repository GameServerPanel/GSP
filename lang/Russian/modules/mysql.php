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

define('LANG_configured_mysql_hosts', "Настроенные хосты MySQL");
define('LANG_add_new_mysql_host', "Добавить хост MySQL");
define('LANG_enter_mysql_ip', "Введите IP MySQL.");
define('LANG_enter_valid_port', "Введите правильный порт");
define('LANG_enter_mysql_root_password', "Введите root пароль для MySQL.");
define('LANG_enter_mysql_name', "Введите имя MySQL.");
define('LANG_could_not_add_mysql_server', "Не удалось добавить MySQL-сервер");
define('LANG_game_server_name_info', "Название может помочь определить сервер.");
define('LANG_note_mysql_host', "ПРИМЕЧАНИЕ: Используя 'Прямое соединение', сервер должен принимать внешние подключения, чтобы серверы могли подключаться удаленно. При 'Подключение к удаленному игровому серверу' он будет использоваться как локальное соединение на игровом сервер.");
define('LANG_direct_connection', "Прямое подключение");
define('LANG_connection_through_remote_server_named', "Подключение к удаленному игровому серверу %s");
define('LANG_add_mysql_server', "Добавить MySQL-сервер");
define('LANG_mysql_online', "MySQL в сети");
define('LANG_mysql_offline', "MySQL не в сети");
define('LANG_encryption_key_mismatch', "Зашифрованный ключ на удаленной машине не совпадает с ключем агента. Пересмотрите ваши файлы конфигурации.");
define('LANG_unknown_error', "Неизвестная ошибка - status_chk возвращен");
define('LANG_remove', "Удалить");
define('LANG_assign_db', "Назначить Базу Данных");
define('LANG_mysql_server_name', "Имя MySQL-сервер");
define('LANG_server_status', "Статус сервера");
define('LANG_mysql_ip_port', "MySQL IP:Port");
define('LANG_mysql_root_passwd', "MySQL root пароль");
define('LANG_connection_method', "Способ подключения");
define('LANG_user_privilegies', "Пользовательские привилегии");
define('LANG_current_dbs', "Текущие Базы Данных");
define('LANG_mysql_name', "Имя MySQL-сервер");
define('LANG_mysql_ip', "MySQL IP");
define('LANG_mysql_port', "MySQL Порт");
define('LANG_privilegies', "привилегии");
define('LANG_all', "Все");
define('LANG_custom', "Пользовательские");
define('LANG_server_added', "Сервер добавлен.");
define('LANG_sql_alter', "Изменять 'ALTER'");
define('LANG_sql_create', "Создавать 'CREATE'");
define('LANG_sql_create_temporary_tables', "Создавать временные таблицы 'CREATE TEMPORARY TABLES'");
define('LANG_sql_drop', "Удалять 'DROP'");
define('LANG_sql_index', "INDEX");
define('LANG_sql_insert', "Вставлять 'INSERT'");
define('LANG_sql_lock_tables', "Блокировать 'LOCK TABLES'");
define('LANG_sql_select', "Выбирать 'SELECT'");
define('LANG_sql_grant_option', "Изменять привилегии 'GRANT OPTION'");
define('LANG_sql_update', "Обновлять 'UPDATE'");
define('LANG_sql_delete', "Удалять 'DELETE'");
define('LANG_sql_alter_info', "<b>Позволяет использовать ALTER TABLE.</b>");	
define('LANG_sql_create_info', "<b>Позволяет использовать CREATE TABLE.</b>");	
define('LANG_sql_create_temporary_tables_info', "<b>Позволяет использовать CREATE TEMPORARY TABLE.</b>");
define('LANG_sql_delete_info', "<b>Позволяет использовать DELETE.</b>");
define('LANG_sql_drop_info', "<b>Позволяет использовать DROP TABLE.</b>");	
define('LANG_sql_index_info', "<b>Позволяет использовать CREATE INDEX и DROP INDEX.</b>");	
define('LANG_sql_insert_info', "<b>Позволяет использовать INSERT.</b>");	
define('LANG_sql_lock_tables_info', "<b>Позволяет использовать LOCK TABLES на таблицах, для которых у Вас есть привилегии SELECT.</b>");	
define('LANG_sql_select_info', "<b>Позволяет использовать SELECT.</b>");
define('LANG_sql_update_info', "<b>Позволяет использовать UPDATE.</b>");	
define('LANG_sql_grant_option_info', "<b>Позволяет предоставлять привилегии.</b>");
define('LANG_select_game_server', "Выбрать игровой сервер");
define('LANG_invalid_mysql_server_id', "Не верный ID сервера MySQL.");
define('LANG_there_is_another_db_named_or_user_named', "Существует другая база данных с именем <b>%s</b> или другой пользователь с именем <b>%s</b>.");
define('LANG_db_added_for_home_id', "Добавить Базу Данных для home ID <b>%s</b>.");
define('LANG_could_not_remove_db', "Выбранная База Данных не может быть удалена.");
define('LANG_db_removed_successfully_from_mysql_server_named', "База данных была удалена с сервера%s.");
define('LANG_areyousure_remove_mysql_server', "Вы уверены, что хотите удалить MySQL-сервер с именем <b>%s</b>?");
define('LANG_db_changed_successfully', "База данных %s был успешно изменен.");
define('LANG_error_while_remove', "Ошибка при удалении сервера.");
define('LANG_mysql_server_removed', "MySQL-сервер <b>%s</b> успешно УДАЛЕН.");
define('LANG_unable_to_set_changes_to', "Не удалось внести изменения на MySQL-сервер <b>%s</b>.");
define('LANG_mysql_server_settings_changed', "MySQL-сервер <b>%s</b> был успешно изменен.");
define('LANG_editing_mysql_server', "Редактирование MySQL-сервера <b>%s</b>.");
define('LANG_save_settings', "Сохранить настройки");
define('LANG_mysql_dbs_for', "База Данных для сервер %s");
define('LANG_edit_dbs', "Редактирование Базы Данных");
define('LANG_edit_db_settings', "Редактировать настройки Базы Данных");
define('LANG_remove_db', "Удалить Базу Данных");
define('LANG_save_db_changes', "Сохранение изменений в Базе Данных.");
define('LANG_add_db', "Добавить Базу Данных");
define('LANG_select_db', "Выбрать Базу данных");
define('LANG_db_user', "Пользователь БД");
define('LANG_db_passwd', "Пароль БД");
define('LANG_db_name', "Название БД");
define('LANG_enabled', "Включить");
define('LANG_game_server', "Игровой сервер");
define('LANG_there_are_no_databases_assigned_for', "Нет назначенной Базы Данных для <b>%s</b>.");
define('LANG_unable_to_connect_to_mysql_server_as', "Невозможно подключиться к серверу MySQL как %s.");
define('LANG_unable_to_create_db', "Не удалось создать базу данных.");
define('LANG_unable_to_select_db', "Невозможно выбрать базу данных %s.");
define('LANG_db_info', "Информация о базе данных");
define('LANG_db_tables', "Таблицы базы данных");
define('LANG_db_backup', "Бэкап БД");
define('LANG_download_db_backup', "Скачать бэкап БД");
define('LANG_restore_db_backup', "Восстановить БД из бэкапа");
define('LANG_sql_file', "файл(.sql)");
?>