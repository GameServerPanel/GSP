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

define('LANG_add_mods_note', "Вам нужно добавить конфигурации для сервера. Это можно сделать в настройках сервера.");
define('LANG_game_servers', "Игровые Сервера");
define('LANG_game_path', "Путь");
define('LANG_game_path_info', "Полный и абсолютный путь к серверу. Пример /home/ogpbot/OGP_User_Files/My_Server");
define('LANG_game_server_name_info', "Название может помочь определить сервер.");
define('LANG_control_password', "Пароль управления");
define('LANG_control_password_info', "Пароль используется для управления сервером, как RCON например. Если оставить пустым, то будут использоваться другие методы.");
define('LANG_add_game_home', "Добавить игровой сервер");
define('LANG_game_path_empty', "Путь к серверу не может быть пустым.");
define('LANG_game_home_added', "Сервер добавлен успешно. Перенаправление на страницу редактирования...");
define('LANG_failed_to_add_home_to_db', "Ошибка добавления сервера в базу данных. Ошибка: %s");
define('LANG_caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms', "<b>Внимание!</b> Агент в автономном режиме не может получить тип ОС и архитектуры,<br> Показаны серверы для всех платформ:");
define('LANG_select_remote_server', "Выберите удаленный хост");
define('LANG_no_remote_servers_configured', "Не добавлено ни одного физического сервера");
define('LANG_no_game_configurations_found', "Файлы конфигурации не найдены");
define('LANG_game_configurations', "Установка конфигураций игры");
define('LANG_add_remote_server', "Добавить физический сервер");
define('LANG_wine_games', "Wine игры");
define('LANG_home_path', "Путь к серверу");
define('LANG_change_home_info', "Путь установленного сервера. Например: /home/ogp/my_server/");
define('LANG_game_server_name', "Название игрового сервера");
define('LANG_change_name_info', "Название может помочь определить сервер.");
define('LANG_game_control_password', "Пароль управления");
define('LANG_change_control_password_info', "Пароль используется для управления сервером, как RCON например.");
define('LANG_available_mods', "Доступные конфигурации");
define('LANG_note_no_mods', "Вы не выбрали ни одной конфигурации. Установите конфигурацию, прежде чем пользователи смогут использовать сервер.");
define('LANG_change_home', "Сохранить путь");
define('LANG_change_control_password', "Сохранить пароль управления");
define('LANG_change_name', "Сохранить название");
define('LANG_add_mod', "Добавить конфигурацию");
define('LANG_set_ip', "Сохранить IP");
define('LANG_ips_and_ports', "IP и порты");
define('LANG_mod_name', "Название конфига");
define('LANG_max_players', "Лимит игроков");
define('LANG_extra_cmd_line_args', "Дополнительные команды");
define('LANG_extra_cmd_line_info', "Дополнительные команды позволяют вам вводить любые значения, которые будут прописаны при запуске сервера.");
define('LANG_cpu_affinity', "Ядро процессора");
define('LANG_nice_level', "Приоритет сервера");
define('LANG_set_options', "Сохранить");
define('LANG_remove_mod', "Удалить конфигурацию");
define('LANG_mods', "Конфигурация");
define('LANG_ip', "IP");
define('LANG_port', "Порт");
define('LANG_no_ip_ports_assigned', "Как минимум один IP:Порт должны быть привязаны к серверу.");
define('LANG_successfully_assigned_ip_port', "IP:Port Удачно привязаны к серверу.");
define('LANG_port_range_error', "Значение порта должно быть между 0 и 65535.");
define('LANG_failed_to_assing_mod_to_home', "Не удалось привязать конфигурацию с id %d к серверу.");
define('LANG_successfully_assigned_mod_to_home', "Конфигурация была удачно привязана к id %d серверу.");
define('LANG_successfully_modified_mod', "Конфигурация сохранена успешно.");
define('LANG_back_to_game_monitor', "Назад к Мониторингу");
define('LANG_back_to_game_servers', "Перейти к Игровым Серверам");
define('LANG_user_id_main', "Основные владельцы");
define('LANG_change_user_id_main', "Сменить основного владельца");
define('LANG_change_user_id_main_info', "Основные владельцы сервера.");
define('LANG_server_ftp_password', "FTP-пароли");
define('LANG_change_ftp_password', "Сохранить пароля FTP");
define('LANG_change_ftp_password_info', "Это пароль для входа на FTP-сервер для этого игрового сервера.");
define('LANG_Delete_old_user_assigned_homes', "Удалить привязку сервера для всех текущих пользователей.");
define('LANG_editing_home_called', "Редактирование сервера");
define('LANG_control_password_updated_successfully', "Пароль управления успешно обновлен");
define('LANG_control_password_update_failed', "Не удалось обновить Пароль управления");
define('LANG_successfully_changed_game_server', "Игровой сервер успешно изменен");
define('LANG_error_ocurred_on_remote_server', "Ошибка на удаленном сервере,");
define('LANG_ftp_password_can_not_be_changed', "FTP пароль не возможно изменить");
define('LANG_ftp_can_not_be_switched_on', "FTP не возможно перевести в режим ON");
define('LANG_ftp_can_not_be_switched_off', "FTP не возможно перевести в режим OFF");
define('LANG_invalid_home_id_entered', "Не верно введен home id");
define('LANG_ip_port_already_in_use', "%s:%sуже используется. Выберите другой.");
define('LANG_successfully_assigned_ip_port_to_server_id', "IP-порт успешно назначены для сервера %s:%s c ID - %s");
define('LANG_no_ip_addresses_configured', "На Вашем игровом сервер нет настроенных IP-адресов. Вы можете настроить их из ");
define('LANG_server_page', "Станица серверов");
define('LANG_successfully_removed_mod', "Игровой мод успешно удален.");
define('LANG_warning_agent_offline_defaulting_CPU_count_to_1', "Предупреждение - Агент Выключен, Значение CPU по умолчанию - 1.");
define('LANG_mod_install_cmds', "Установка мода CMDs");
define('LANG_cmds_for', "Команды для");
define('LANG_preinstall_cmds', "Команды предустановкы");
define('LANG_postinstall_cmds', "Команды после установки");
define('LANG_edit_preinstall_cmds', "Редактировать Команды предустановкы");
define('LANG_edit_postinstall_cmds', "Редактировать Команды после установки");
define('LANG_save_as_default_for_this_mod', "Сохранить по умолчанию для этого мода");
define('LANG_empty', "пусто");
define('LANG_master_server_for_clon_update', "Мастер-сервер для локального обновления");
define('LANG_set_as_master_server', "Установить как Мастер-сервер");
define('LANG_set_as_master_server_for_local_clon_update', "Установить как Мастер-сервер для локального обновления");
define('LANG_only_available_for', "Доступно только для '%s' размещенный на удаленном сервере '%s'.");
define('LANG_ftp_on', "Включить FTP");
define('LANG_ftp_off', "Выключить FTP");
define('LANG_change_ftp_account_status', "Изменение статуса FTP аккаунта");
define('LANG_change_ftp_account_status_info', "Как только учетная запись FTP включена или отключена, Он добавляется или удаляется из базы данных FTP.");
define('LANG_server_ftp_login', "FTP Логин");
define('LANG_change_ftp_login_info', "Измените FTP Логин вписав в поле свой.");
define('LANG_change_ftp_login', "Изменить FTP Логин");
define('LANG_ftp_login_can_not_be_changed', "Невозможно изменить FTP Логин");
define('LANG_server_is_running_change_addresses_not_available', "Сервер сейчас запущен, не возможно сменить IP.");
define('LANG_change_game_type', "Изменить тип игры");
define('LANG_change_game_type_info', "Изменяя тип игры, текущая конфигурация мода будет удалена.");
define('LANG_force_mod_on_this_address', "Force mod on this address");
define('LANG_successfully_assigned_mod_to_address', "Мод был удачно привязан к адресу");
define('LANG_switch_mods', "Переключить мод");
define('LANG_switch_mod_for_address', "Изменить мод для адреса %s");
define('LANG_invalid_path', "Неверный Путь");
define('LANG_add_new_game_home', "Добавить новый сервер");
define('LANG_no_game_homes_found', "Игровых серверов не обнаружено");
define('LANG_available_game_homes', "Доступные игровые сервера");
define('LANG_home_id', "ID Сервера");
define('LANG_game_server', "Игровой сервер");
define('LANG_game_type', "Игра");
define('LANG_game_home', "Путь к серверу");
define('LANG_game_home_name', "Название игрового сервера");
define('LANG_clone', "Копировать");
define('LANG_unassign', "Отменить");
define('LANG_access_rights', "Права доступа");
define('LANG_assigned_homes', "Уже назначенные сервера");
define('LANG_assign', "Назначить");
define('LANG_allow_updates', "Разрешить обновление игры");
define('LANG_allow_updates_info', "Разрешить пользователю обновлять игу, если это доступно.");
define('LANG_allow_file_management', "Разрешить доступ к Файловому менеджеру");
define('LANG_allow_file_management_info', "Разрешить пользователю доступ к файлам сервера через модуль.");
define('LANG_allow_parameter_usage', "Разрешить использование параметров запуска");
define('LANG_allow_parameter_usage_info', "Разрешить пользователю использовать заданные параметры запуска.");
define('LANG_allow_extra_params', "Разрешить дополнительные параметры");
define('LANG_allow_extra_params_info', "Разрешить пользователю использовать дополнительные параметры запуска.");
define('LANG_allow_ftp', "Разрешить FTP");
define('LANG_allow_ftp_info', "Показывать информацию о доступе к FTP пользователю.");
define('LANG_allow_custom_fields', "Разрешить дополнительные команды");
define('LANG_allow_custom_fields_info', "Позволяет пользователю получать доступ к дополнительным командам игрового сервера, если таковые имеются.");
define('LANG_select_home', "Выбрать сервер для назначения");
define('LANG_assign_new_home_to_user', "Назначить новый сервер пользователю  %s");
define('LANG_assign_new_home_to_group', "Назначить новый сервер группе %s");
define('LANG_assigned_home_to_user', "Сервер привязан (ID: %d) к пользователю %s.");
define('LANG_failed_to_assign_home_to_user', "Не удалось назначить (ID: %d) пользователю %s.");
define('LANG_assigned_home_to_group', "Сервер привязан(ID: %d) к группе %s.");
define('LANG_unassigned_home_from_user', "Назначение сервера (ID: %d) отменено для пользователя%s.");
define('LANG_unassigned_home_from_group', "Назначение сервера (ID: %d) отменено для группы %s.");
define('LANG_no_homes_assigned_to_user', "Привязанных серверов для пользователя %s нет.");
define('LANG_no_homes_assigned_to_group', "Привязанных серверов для группы %s нет.");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_user', "Нет больше серверов, которые можно привязать к данному пользователю");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_group', "Нет больше серверов, которые можно привязать к данной группе");
define('LANG_you_can_add_a_new_game_server_from', "Вы можете добавить новый игровой сервер через ");
define('LANG_no_remote_servers_available_please_add_at_least_one', "Нет доступных удаленных серверов, добавьте хотя бы один!");
define('LANG_cloning_of_home_failed', "Ошибка при копировании сервера '%s'.");
define('LANG_no_mods_to_clone', "Нет настроенных конфигураций для этого сервера. Он копирован не будет.");
define('LANG_failed_to_add_mod', "Ошибка при добавлении конфигурации ID '%s' к серверу  id '%s'.");
define('LANG_failed_to_update_mod_settings', "Ошибка при обновлении конфигураций сервера '%s'.");
define('LANG_successfully_cloned_mods', "Удачно копированы конфигурации для сервера '%s'.");
define('LANG_successfully_copied_home_database', "Удачно скопирована база данных сервера.");
define('LANG_copying_home_remotely', "Копирование сервера на удалённом хосте из '%s' в '%s'.");
define('LANG_cloning_home', "Копирование отменено '%s'");
define('LANG_current_home_path', "Текущая директория сервера");
define('LANG_current_home_path_info', "Текущий путь копируемого сервера.");
define('LANG_clone_home', "Копировать сервер");
define('LANG_new_home_name', "Новое имя сервера");
define('LANG_new_home_path', "Новый путь к серверу");
define('LANG_agent_ip', "Ip агента");
define('LANG_game_server_copy_is_running', "Выполняется копия игрового сервера ...");
define('LANG_game_server_copy_was_successful', "Копирование игрового сервера выполнено успешно.");
define('LANG_game_server_copy_failed_with_return_code', "Ошибка копирования серверного сервера, код %s");
define('LANG_clone_mods', "Клонировать мод");
define('LANG_game_server_owner', "Владелец игрового сервера");
define('LANG_the_name_of_the_server_to_help_users_to_identify_it', "Название может помочь определить сервер.");
define('LANG_ips_and_ports_used_in_this_home', "IP и порты используемые тут");
define('LANG_note_ips_and_ports_are_not_cloned', "Заметка - IP и порт не будут скопированы");
define('LANG_mods_and_settings_for_this_game_server', "Моды и настройки для этого игрового сервера");
define('LANG_sure_to_delete_serverid_from_remoteip_and_directory', "Уверены что хотите удалить сервер из базы данных? Пользователь %s ip %s путь %s ");
define('LANG_yes_and_delete_the_files', "Да и удалить все файлы");
define('LANG_failed_to_remove_gamehome_from_database', "Не удалось удалить игровой сервер из базы данных.");
define('LANG_successfully_deleted_game_server_with_id', "Игровой сервер с ID %s успешно удален.");
define('LANG_failed_to_remove_ftp_account_from_remote_server', "Ну удалось удалить FTP аккаунт из удаленного сервера.");
define('LANG_remove_it_anyway', "Вы все равно хотите его удалить?");
define('LANG_sucessfully_deleted', "Успешное удаление %s");
define('LANG_the_agent_had_a_problem_deleting', "У агента возникла проблема с удалением %s, посмотрите логи Агента");
define('LANG_connection_timeout_or_problems_reaching_the_agent', "Время соединения вышло или проблемы связи с Агентом");
define('LANG_does_not_exist_yet', "Пока не существует.");
define('LANG_update_settings', "Обновить настройки");
define('LANG_settings_updated', "Настройки обновлены");
define('LANG_selected_path_already_in_use', "Выбранный путь уже используется");
define('LANG_browse', "Обзор");
define('LANG_cancel', "Отмена");
define('LANG_set_this_path', "Установить этот путь");
define('LANG_select_home_path', "Выбрать домашний путь-каталог");
define('LANG_folder', "Папка");
define('LANG_owner', "Владелец");
define('LANG_group', "Группа");
define('LANG_level_up', "На уровень вверх");
define('LANG_level_up_info', "Назад к предыдущей папке.");
define('LANG_add_folder', "Добавить папку");
define('LANG_add_folder_info', "Напишите имя для новой папки, затем щелкните на значок.");
define('LANG_valid_user', "Укажите действительного пользователя.");
define('LANG_valid_group', "Укажите действительную группу.");
define('LANG_set_affinity', "Установить привязку сервера");
define('LANG_cpu_affinity_info', "Выберите Ядро (Ядра) процессора, которое вы хотите назначить этому игровому серверу.");
define('LANG_expiration_date_changed', "Дата истечения срока действия для выбранного сервера была изменена.");
define('LANG_expiration_date_could_not_be_changed', "Дата истечения срока действия для выбранного дома не может быть изменена.");
define('LANG_search', "Поиск");
define('LANG_ftp_account_username_too_long', "Имя FTP-пользователя слишком длинное. Название должно быть не более 20 символов.");
define('LANG_ftp_account_password_too_long', "Пароль FTP-пользователя слишком длинный. Пароль должно быть не более 20 символов.");
define('LANG_other_servers_exist_with_path_please_change', "Другие сервера существуют с одним и тем же путем. Рекомендуется (но не обязательно), чтобы вы изменили этот путь на нечто уникальное. У вас могут быть проблемы, если вы не сделаете этого.");
define('LANG_change_access_rights_for_selected_servers', "Изменить права доступа для выбранных серверов");
?>