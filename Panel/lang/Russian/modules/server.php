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

define('LANG_add_new_remote_host', "Добавить удаленный хост");
define('LANG_configured_remote_hosts', "Настроенные удаленные хосты");
define('LANG_remote_host', "Удаленный хост");
define('LANG_remote_host_info', "Удаленный хост должен пинговаться!");
define('LANG_remote_host_port', "Порт удаленного хоста");
define('LANG_remote_host_port_info', "Порт, прослушиваемый OGP агентом на удалённом хосте. По умолчанию: 12679.");
define('LANG_remote_host_name', "Название удаленного хоста");
define('LANG_ogp_user', "Пользователь OGP Агента");
define('LANG_remote_host_name_info', "Название удаленного хоста используется, чтобы помочь пользователям идентифицировать свои сервера.");
define('LANG_add_remote_host', "Добавить удаленный сервер");
define('LANG_remote_encryption_key', "Зашифрованный ключ на удаленной машине");
define('LANG_remote_encryption_key_info', "Зашифрованный ключ на удаленной машине, используется для шифрования данных между веб-панелью и агентом. Этот ключ должен быть одинаковым с обеих сторон");
define('LANG_server_name', "Название сервера");
define('LANG_agent_ip_port', "IP:Порт Агента");
define('LANG_agent_status', "Статус агента");
define('LANG_ips', "IP-адрес");
define('LANG_add_more_ips', "Если вы хотите ввести больше IP-адресов, введите ниже в поле 'Удаленный IP:' еще один IP и нажмите 'Добавить IP'");
define('LANG_encryption_key_mismatch', "Зашифрованный ключ не совпадает с ключем Агента. Пожалуйста проверти правильность ввода ключа и ключа Агента.");
define('LANG_no_ip_for_remote_host', "Вам нужно добавить хотя бы один (1) IP-адрес для каждого удаленного хоста.");
define('LANG_note_remote_host', "Удаленный хост сервера, где запущен OGP агент. Каждый хост может иметь несколько IP адресов, к которым пользователи будут привязывать сервера.");
define('LANG_ip_administration', "Сервер &amp; IP Администрации :: Open Game Panel");
define('LANG_unknown_error', "Неизвестная ошибка - status_chk возвращен");
define('LANG_remote_host_user_name', "UNIX пользователь");
define('LANG_remote_host_user_name_info', "Имя пользователя, под которым запущен агент.");
define('LANG_remote_host_ftp_ip', "FTP-IP");
define('LANG_remote_host_ftp_ip_info', "FTP-сервер <b>IP</b> для текущего агента.");
define('LANG_remote_host_ftp_port', "FTP-порт");
define('LANG_remote_host_ftp_port_info', "FTP-<b>порт</b> сервера для текущего агента.");
define('LANG_view_log', "Просмотр лога");
define('LANG_status', "Статус");
define('LANG_stop_firewall', "Остановить Firewall");
define('LANG_start_firewall', "Запустить Firewall");
define('LANG_seconds', "Секунд");
define('LANG_reboot', "Перезагрузить Удаленный Сервер");
define('LANG_restart', "Перезапустить Агента");
define('LANG_confirm_reboot', "Вы действительно хотите перезагрузить удаленный сервер '%s' ???");
define('LANG_confirm_restart', "Вы действительно хотите перезапустить Агента на '%s' ???");
define('LANG_restarting', "Перезапуск Агента.... Пожалуйста подождите.");
define('LANG_restarted', "Агент Успешно перезапущен.");
define('LANG_reboot_success', "Сервер '%s' перезагружаеться. Вы не сможете получить доступ к серверу до тех пор, пока он не будет успешно загружен.");
define('LANG_invalid_remote_host_id', "Не верный host id '%s' given.");
define('LANG_remote_host_removed', "Удаленный хост: '%s' успешно удален.");
define('LANG_editing_remote_server', "Редактирование удаленного сервера: '%s'");
define('LANG_remote_server_settings_changed', "Изменены параметры для удаленного сервера '%s' успешно.");
define('LANG_save_settings', "Сохранить настройки");
define('LANG_set_ips', "Настроить IP-адреса");
define('LANG_remote_ip', "Удаленный IP");
define('LANG_remote_ips_for', "IP для игровых серверов для использования на сервере агента '%s'");
define('LANG_ips_set_for_server', "IP-адреса, для сервера '%s'  установлены успешно.");
define('LANG_could_not_remove_ip', "Не удалось удалить старый IP-адрес из базы данных.");
define('LANG_could_add_ip', "Вы можете добавить IP-адрес удаленного сервера в базу данных.");
define('LANG_areyousure_removeagent', "Вы действительно хотите удалить агента ?");
define('LANG_areyousure_removeagent2', "и все сервера привязаные к нему их базы данных OGP?");
define('LANG_error_while_remove', "Ошибка при удалении сервера.");
define('LANG_add_ip', "Добавить IP");
define('LANG_remove_ip', "Удалить IP");
define('LANG_edit_ip', "Изменить IP");
define('LANG_wrote_changes', "Изменения записаны успешно.");
define('LANG_there_are_servers_running_on_this_ip', "IP в настоящее время используется.");
define('LANG_enter_ip_host', "Вы должны ввести IP удаленного хоста.");
define('LANG_enter_valid_ip', "Вы должны ввести верный порт на удалённом хосте. Это значение может быть между 0 и 65535, в любом случае мы рекомендуем ставить его между 1024 и 65535 во избежание дальнейших проблем.");
define('LANG_could_not_add_server', "Не удалось добавить сервер");
define('LANG_to_db', "в базу данных.");
define('LANG_added_server', "Сервер добавлен");
define('LANG_with_port', "с поротом");
define('LANG_to_db_succesfully', "в базу данных удачно.");
define('LANG_unable_discover', "Не удалось автоматически определить IP адрес на");
define('LANG_set_ip_manually', "Вам придётся выставить его вручную.");
define('LANG_found_ips', "Найденые IP");
define('LANG_for_remote_server', "для удалённого хоста.");
define('LANG_failed_add_ip', "Ошибка при добавлении IP");
define('LANG_timeout', "Тайм-аут");
define('LANG_timeout_info', "Срок для получения ответа от агента. В секундах");
define('LANG_use_nat', "Использовать NAT");
define('LANG_use_nat_info', "Включите, если удаленный сервер использует правила NAT. Используйте этот параметр, если игровые серверы работают на внутренних частных IP-адресах LAN, чтобы панель использовала ваш реальный удаленный IP-адрес для запроса игровых серверов.");
define('LANG_arrange_ports', "Управление портами");
define('LANG_assign_new_ports_range_for_ip', "Назначьте новый диапазон портов для IP %s");
define('LANG_assigned_port_ranges_for_ip', "Назначенные диапазоны портов для IP %s");
define('LANG_assigned_ports_for_ip', "Назначенные порты для IP %s");
define('LANG_unspecified_game_types', "Неопределенный тип игр");
define('LANG_start_port', "Начальный порт:");
define('LANG_end_port', "Конечный порт:");
define('LANG_port_increment', "Шаг увеличение порта");
define('LANG_total_assignable_ports', "Всего назначаемых портов:");
define('LANG_available_range_ports', "Доступные диапазоны портов:");
define('LANG_assign_range', "Назначить диапазон");
define('LANG_edit_range', "Изменить диапазон");
define('LANG_delete_range', "Удалить диапазон");
define('LANG_home_id', "ID Сервера");
define('LANG_home_path', "Путь к серверу");
define('LANG_game_type', "Игра");
define('LANG_port', "Порт");
define('LANG_invalid_values', "Недопустимые значения.");
define('LANG_ports_in_range_already_arranged', "Диапазон портов уже назначен!");
define('LANG_ports_range_already_configured_for', "Диапазон портов, уже настроенный для %s.");
define('LANG_ports_range_added_successfull_for', "Диапазон портов, успешно Добавлен для %s.");
define('LANG_ports_range_deleted_successfull', "Диапазон портов, успешно Удален.");
define('LANG_ports_range_edited_successfull_for', "Диапазон портов, успешно отредактирован для %s.");
define('LANG_editing_firewall_for_remote_server', "Редактирование Firewall для удаленного сервер '%s'");
define('LANG_default_allowed', "Разрешено по умолчанию");
define('LANG_allow_port_command', "Разрешение на порт - команда:");
define('LANG_deny_port_command', "Запрет на порт - команда:");
define('LANG_allow_ip_port_command', "Разрешение на IP и порт - команда:");
define('LANG_deny_ip_port_command', "Запрет на IP и порт - команда:");
define('LANG_enable_firewall_command', "Включение Firewall - команда:");
define('LANG_disable_firewall_command', "Выключение Firewall - команда:");
define('LANG_get_firewall_status_command', "Статус Firewall - команда:");
define('LANG_reset_firewall_command', "Сбросить Firewall - команда:");
define('LANG_firewall_status', "Статус Firewall");
define('LANG_save_firewall_settings', "Сохранить настройки Firewall");
define('LANG_reset_firewall', "Сбросить Firewall");
define('LANG_firewall_settings', "Настройки Firewall");
define('LANG_display_public_ip', "Внешний IP-адрес");
define('LANG_ips_can_be_internal_external', "Enter usable IP addresses.&nbsp; Public IP addresses and internal LAN IP addresses (for NAT setups) can be used.");
?>
