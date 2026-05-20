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

define('LANG_error', "Ошибка");
define('LANG_title', "TeamSpeak 3 Веб-панель");
define('LANG_update_available', "<h3>Внимание: новая версия (v%1)  программного обеспечения <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Выйти");
define('LANG_head_vserver_switch', "Изменить виртСервер");
define('LANG_head_vserver_overview', "Обзор виртуального сервера.");
define('LANG_head_vserver_token', "Управление токеном");
define('LANG_head_vserver_liveview', "Live View");
define('LANG_e_fill_out', "Please fill out all required fields.");
define('LANG_e_upload_failed', "Не удачная загрузка");
define('LANG_e_server_responded', "The server responded: ");
define('LANG_e_conn_serverquery', "Could not create ServerQuery access.");
define('LANG_e_conn_vserver', "Could not choose virtual server.");
define('LANG_e_session_timedout', " Сессия истекла.");
define('LANG_js_error', "Ошибка");
define('LANG_js_ajax_error', "Произошла ошибка AJAX: %1.");
define('LANG_js_confirm_server_stop', "Do you really want to stop server #%1?");
define('LANG_js_confirm_server_delete', "Do you really want to DELETE server #%1?");
define('LANG_js_notice_server_deleted', "Server %1 was deleted successfully.\nThe overview page will be getting reloaded now.");
define('LANG_js_prompt_banduration', "Продолжительность в часах (0=неограниченно):");
define('LANG_js_prompt_banreason', "Причина (необязательно):");
define('LANG_js_prompt_msg_to', "Текстовое сообщение для %1 #%2: ");
define('LANG_js_prompt_poke_to', "Отправить сообщение клиенту #%1: ");
define('LANG_js_prompt_new_propvalue', "Новое значение для '%1':");
define('LANG_n_server_responded', "The server responded: ");
define('LANG_login_serverquery', "ServerQuery Login");
define('LANG_login_name', "Имя пользователя");
define('LANG_login_password', "Пароль");
define('LANG_login_submit', "Логин");
define('LANG_vsselect_headline', "vServer selection");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Название");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Порт");
define('LANG_vsselect_state', "Статус");
define('LANG_vsselect_clients', "Клиенты");
define('LANG_vsselect_uptime', "Время работы");
define('LANG_vsselect_choose', "Выбрать");
define('LANG_vsselect_start', "Старт");
define('LANG_vsselect_stop', "Стоп");
define('LANG_vsselect_delete', "УДАЛИТЬ");
define('LANG_vsselect_new_headline', "Создать новый виртуальный сервер");
define('LANG_vsselect_new_servername', "Название сервера");
define('LANG_vsselect_new_slots', "Клиентские слоты");
define('LANG_vsselect_new_create', "Создать");
define('LANG_vsselect_new_added_ok', "Вирт.Сервер <span class=\"online\">%1</span>успешно создан.");
define('LANG_vsselect_new_added_generated', "Сгенерированный токен:");
define('LANG_vsoverview_virtualserver', "Виртуальный Сервер");
define('LANG_vsoverview_information_head', "Информация");
define('LANG_vsoverview_connection_head', "Соединение");
define('LANG_vsoverview_info_general_head', "Общие настройки");
define('LANG_vsoverview_info_servername', "Название сервера");
define('LANG_vsoverview_info_host', "Хост");
define('LANG_vsoverview_info_state', "Статус");
define('LANG_vsoverview_info_state_port', "Порт");
define('LANG_vsoverview_info_uptime', "Время работы");
define('LANG_vsoverview_info_welcomemsg', "Приветственное сообщение");
define('LANG_vsoverview_info_hostmsg', "Сообщение хоста");
define('LANG_vsoverview_info_hostmsg_mode_output', "вывод");
define('LANG_vsoverview_info_hostmsg_mode_0', "none");
define('LANG_vsoverview_info_hostmsg_mode_1', "в лог чата");
define('LANG_vsoverview_info_hostmsg_mode_2', "окошко");
define('LANG_vsoverview_info_hostmsg_mode_3', "Окно + Отключить");
define('LANG_vsoverview_info_req_security', "Уровень безопасности");
define('LANG_vsoverview_info_req_securitylvl', "обязательный");
define('LANG_vsoverview_info_hostbanner_head', "Баннер хоста");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Адрес изображения");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Hostbutton URL");
define('LANG_vsoverview_info_antiflood_head', "Анти-флуд");
define('LANG_vsoverview_info_antiflood_warning', "Предупреждение о");
define('LANG_vsoverview_info_antiflood_kick', "Kick on");
define('LANG_vsoverview_info_antiflood_ban', "Ban on");
define('LANG_vsoverview_info_antiflood_banduration', "Продолжительность Бана");
define('LANG_vsoverview_info_antiflood_decrease', "Decrease");
define('LANG_vsoverview_info_antiflood_points', "points");
define('LANG_vsoverview_info_antiflood_in_seconds', "секунды");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Points per tick");
define('LANG_vsoverview_conn_total_head', "Всего");
define('LANG_vsoverview_conn_total_packets', "пакеты");
define('LANG_vsoverview_conn_total_bytes', "байты");
define('LANG_vsoverview_conn_total_send', "послано");
define('LANG_vsoverview_conn_total_received', "получено");
define('LANG_vsoverview_conn_bandwidth_head', "Пропускная способность");
define('LANG_vsoverview_conn_bandwidth_last', "последний");
define('LANG_vsoverview_conn_bandwidth_second', "секунды");
define('LANG_vsoverview_conn_bandwidth_minute', "минуты");
define('LANG_vsoverview_conn_bandwidth_send', "послано");
define('LANG_vsoverview_conn_bandwidth_received', "получено");
define('LANG_vstoken_token_virtualserver', "Виртуальный Сервер");
define('LANG_vstoken_token_head', "Токен");
define('LANG_vstoken_token_type', "Тип группы");
define('LANG_vstoken_token_id1', "Группа серверов/<br />Группа каналов");
define('LANG_vstoken_token_id2', "(Канал)");
define('LANG_vstoken_token_tokencode', "Код токена");
define('LANG_vstoken_token_delete', "Delete");
define('LANG_vstoken_new_head', "Создать новый Токен");
define('LANG_vstoken_new_create', "Сгенерировать");
define('LANG_vstoken_new_tokentype', "Тип токена:");
define('LANG_vstoken_new_servergroup', "Группа серверов");
define('LANG_vstoken_new_channelgroup', "Группа каналов");
define('LANG_vstoken_new_select_group', "Servergroup");
define('LANG_vstoken_new_select_channelgroup', "Channelgroup");
define('LANG_vstoken_new_select_channel', "Канал");
define('LANG_vstoken_new_tokentype_0', "Сервер");
define('LANG_vstoken_new_tokentype_1', "Канал");
define('LANG_vstoken_new_added_ok', "Токен был сгенерирован успешно.");
define('LANG_vsliveview_server_virtualserver', "Виртуальный Сервер");
define('LANG_vsliveview_server_head', "Live View");
define('LANG_vsliveview_liveview_enable_autorefresh', "Автообновление");
define('LANG_vsliveview_liveview_tooltip_to_channel', "на канал #");
define('LANG_vsliveview_liveview_tooltip_switch', "Переключить");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Отправить Сообщение");
define('LANG_vsliveview_liveview_tooltip_poke', "Poke");
define('LANG_vsliveview_liveview_tooltip_kick', "Выкинуть");
define('LANG_vsliveview_liveview_tooltip_ban', "Забанить");
define('LANG_vsoverview_banlist_head', "Бан Лист");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Имя");
define('LANG_vsoverview_banlist_uid', "Уникальный ID");
define('LANG_vsoverview_banlist_reason', "Причина");
define('LANG_vsoverview_banlist_created', "Создан");
define('LANG_vsoverview_banlist_duration', "Продолжительность");
define('LANG_vsoverview_banlist_end', "Окончание");
define('LANG_vsoverview_banlist_unlimited', "неограниченный");
define('LANG_vsoverview_banlist_never', "никогда");
define('LANG_vsoverview_banlist_new_head', "Создать новый Бан");
define('LANG_vsoverview_banlist_new_create', "Создать");
define('LANG_vsliveview_channelbackup_head', "Резервирование каналов");
define('LANG_vsliveview_channelbackup_get', "Создание и загрузка");
define('LANG_vsliveview_channelbackup_load', "Загрузка резервной копии канала");
define('LANG_vsliveview_channelbackup_load_submit', "Пересоздать");
define('LANG_vsliveview_channelbackup_new_added_ok', "Успешное резервирование канала.");
define('LANG_time_day', "день");
define('LANG_time_days', "дни");
define('LANG_time_hour', "час");
define('LANG_time_hours', "часов");
define('LANG_time_minute', "минуты");
define('LANG_time_minutes', "минут");
define('LANG_time_second', "секунды");
define('LANG_time_seconds', "секунд");
define('LANG_e_2568', "У вас недостаточно прав.");
define('LANG_temp_folder_not_writable', "Папка шаблонов (%s) не доступен для записи.");
define('LANG_unassign_from_subuser', "Отменить назначение от суб-пользователя.");
define('LANG_assign_to_subuser', "Назначить суб-пользователю.");
define('LANG_select_subuser', "Выбрать суб-пользователя.");
define('LANG_no_ts3_servers_assigned_to_account', "У вас нет серверов, назначенных для вашей учетной записи.");
define('LANG_change_virtual_server', "Изменить виртуальный сервер");
define('LANG_change_remote_server', "Изменить удаленный сервер");
?>