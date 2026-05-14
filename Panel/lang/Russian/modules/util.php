<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

define('LANG_module_name', "Utilities");
define('LANG_ping', "пинг");
define('LANG_traceroute', "Трассировка");
define('LANG_network_tools', "Сетевые утилиты");
define('LANG_sourcemod_admins', "Админы Sourcemod");
define('LANG_steam_converter', "Конвертер SteamID");
define('LANG_your_ip', "Ваш IP-адрес");
define('LANG_loading_agents', "Загрузка Агентов 'в сети'...");
define('LANG_loading_failed', "Ошибка Загрузки Агентов.");
define('LANG_agents_offline', "Все Агенты не в сети.");
define('LANG_no_commands', "Извините, у Вашей учетной записи нет прав доступа.");
define('LANG_remote_target', "Целевой IP-адрес");
define('LANG_command', "Команда:");
define('LANG_select_agent', "Выберите Агента:");
define('LANG_chdir_failed', "Ошибка: chdir() вернул ошибку.");
define('LANG_agent_invalid', "Указан не верный Агент.");
define('LANG_networktools_agent_offline', "Не удалось выполнить команду, потому что выбранный Агент не в сети.");
define('LANG_target_empty', "Цель не выбрана.");
define('LANG_command_empty', "Команда не выбрана.");
define('LANG_command_unavilable', "Выбранная команда недоступна для выбранного агента.");
define('LANG_target_invalid', "Не верно введен IP или хост");
define('LANG_exec_failed', "Время ожидания ответа вышло.");
define('LANG_command_no_access', "У вас нет доступа к этой команде. Это будет записано в Лог.");
define('LANG_command_hacking_attempt', "Введены символы из черного списка. Это будет записано в Лог.");
define('LANG_command_bad_characters', "Попытка выполнить команду с искаженными/неправильными символами. Получено на вход:%s%s");
define('LANG_command_no_permissions', "Попытка выполнить команду с недостаточными разрешениями. Получено на вход: %s %s");
define('LANG_command_executed', "Команда успешно отправлена: %s%s");
define('LANG_no_servers', "У вас нет серверов, назначенных для вашей учетной записи.");
define('LANG_select_server', "Выбрать Сервер");
define('LANG_select_server_option', "Выбрать....");
define('LANG_steamid', "Steam ID:");
define('LANG_immunity', "Иммунитет:");
define('LANG_sourcemod_perms', "Право доступа Sourcemod:");
define('LANG_sourcemod_perm_root', "root флаг: z Sourcemod");
define('LANG_sourcemod_perm_custom', "Выбрать флаги Sourcemod ");
define('LANG_sourcemod_flag_a', "Резервный слот флаг: a");
define('LANG_sourcemod_flag_b', "Общий Админ; требуется для администраторов. флаг: b");
define('LANG_sourcemod_flag_c', "Кик игроков флаг: c");
define('LANG_sourcemod_flag_d', "Бан игроков флаг: d");
define('LANG_sourcemod_flag_e', "Разбан игроков флаг: e");
define('LANG_sourcemod_flag_f', "Убить/ранить игроков флаг: f");
define('LANG_sourcemod_flag_g', "Смена карт флаг: g");
define('LANG_sourcemod_flag_h', "Смена CVARs флаг: h");
define('LANG_sourcemod_flag_i', "Исполнение конфигов флаг: i");
define('LANG_sourcemod_flag_j', "Специальный админский чат флаг: j");
define('LANG_sourcemod_flag_k', "Голосования флаг: k");
define('LANG_sourcemod_flag_l', "Пароль сервера флаг: L");
define('LANG_sourcemod_flag_m', "РКОН команды флаг: m");
define('LANG_sourcemod_flag_n', "Разрешать sv_cheats ли использование чит-команд флаг: n");
define('LANG_sourcemod_flag_o', "Дополнительная группа 1 флаг: o");
define('LANG_sourcemod_flag_p', "Дополнительная группа 2 флаг p");
define('LANG_sourcemod_flag_q', "Дополнительная группа 3 флаг q");
define('LANG_sourcemod_flag_r', "Дополнительная группа 4 флаг r");
define('LANG_sourcemod_flag_s', "Дополнительная группа 5 флаг s");
define('LANG_sourcemod_flag_t', "Дополнительная группа 6 флаг t");
define('LANG_rcon_reload_admins_failed', "Не удалось перезагрузить кеш администратора через RCON; он в сети???");
define('LANG_reload_admins_failed', "Не удалось перезагрузить кеш администратора;  \"sm_reloadadmins\" неизвестная команда.");
define('LANG_reload_admins_success', "Успешно добавлен %s в admins_simple.ini и кеш администратора перезагрузили.");
define('LANG_add_success_no_rcon', "Успешно добавлен %s в admins_simple.ini файл, но не могу перезагрузить кеш администратора.");
define('LANG_writefile_error', "Неизвестная ошибка при записи в: %s");
define('LANG_remotefile_nonexistent', "Не удалось добавить нового администратора. Админ файл: %s на этом сервере не существует.");
define('LANG_empty_flag_list', "Вы не выбрали никаких флагов администратора.");
define('LANG_invalid_steam_format', "Введенный SteamID не соответствует требуемому параметру.");
define('LANG_selected_server_offline', "Не удалось добавить администратора, Агент, управляющий выбранным сервером, не в сети.");
define('LANG_malformed_form', "Вы отправили форму с искаженными скрытыми элементами - Не удалось добавить администратора.");
define('LANG_empty_form_data', "Пожалуйста, заполните все элементы формы.");
define('LANG_server_not_selected', "Вы не выбрали сервер.");
define('LANG_invalid_steamid', "Вы ввели недопустимый Steam ID.");
define('LANG_invalid_immunity', "Вы ввели недопустимое значение иммунитета.");
define('LANG_submit', "Отправить");
define('LANG_post_failed', "Действие POST не выполнено. Не удалось получить ответ.");
define('LANG_amx_mod_admins', "AMX mod X Admins");
define('LANG_amx_login_type', "Login Type");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Nickname + Password");
define('LANG_nickname', "Nickname");
define('LANG_amx_mod_perms', "AMX mod X Permissions:");
define('LANG_amx_mod_perm_root', "AMX mod X All Flags.");
define('LANG_amx_mod_perm_custom', "AMX mod X Custom Flags.");
define('LANG_amx_mod_flag_a', "immunity (can't be kicked/baned/slayed/slaped and affected by other commmands)");
define('LANG_amx_mod_flag_b', "reservation (can join on reserved slots)");
define('LANG_amx_mod_flag_c', "amx_kick command");
define('LANG_amx_mod_flag_d', "amx_ban and amx_unban commands");
define('LANG_amx_mod_flag_e', "amx_slay and amx_slap commands");
define('LANG_amx_mod_flag_f', "amx_map command");
define('LANG_amx_mod_flag_g', "amx_cvar command (not all cvars will be available)");
define('LANG_amx_mod_flag_h', "amx_cfg command");
define('LANG_amx_mod_flag_i', "amx_chat and other chat commands");
define('LANG_amx_mod_flag_j', "amx_vote and other vote commands");
define('LANG_amx_mod_flag_k', "Доступ к sv_password cvar (по команде amx_cvar)");
define('LANG_amx_mod_flag_l', "Доступ к команде amx_rcon и rcon_password cvar (по команде amx_cvar)");
define('LANG_amx_mod_flag_m', "custom level A (for additional plugins)");
define('LANG_amx_mod_flag_n', "Уровень прав B");
define('LANG_amx_mod_flag_o', "Уровень прав C");
define('LANG_amx_mod_flag_p', "Уровень прав D");
define('LANG_amx_mod_flag_q', "Уровень прав E");
define('LANG_amx_mod_flag_r', "Уровень прав F");
define('LANG_amx_mod_flag_s', "Уровень прав G");
define('LANG_amx_mod_flag_t', "Уровень прав H");
define('LANG_amx_mod_flag_u', "Меню доступа");
?>