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

define('LANG_no_games_to_monitor', "У вас еще нету настроенных серверов, которые могли бы быть просмотрены.");
define('LANG_status', "Статус");
define('LANG_fail_no_mods', "Конфигурация не была установлена для данной игры. Обратитесь к администратору для решения этой проблемы.");
define('LANG_no_game_homes_assigned', "У вас нет серверов назначенных специально для вашего аккаунта.");
define('LANG_select_game_home_to_configure', "Выберите сервер который вы хотите настроить.");
define('LANG_file_manager', "Файлы");
define('LANG_configure_mods', "Настройка конфигураций");
define('LANG_install_update_steam', "Установить/Обновить через Steam");
define('LANG_install_update_manual', "Установить/Обновить вручную");
define('LANG_assign_game_homes', "Назначить игровой сервер");
define('LANG_user', "Пользователь");
define('LANG_group', "Группа");
define('LANG_start', "Запуск");
define('LANG_ogp_agent_ip', "IP Агента OGP");
define('LANG_max_players', "Макс. кол-во игроков");
define('LANG_max', "Макс.");
define('LANG_ip_and_port', "IP и порт");
define('LANG_available_maps', "Доступные карты");
define('LANG_map_path', "Адрес карты");
define('LANG_available_parameters', "Доступные параметры");
define('LANG_start_server', "Запуск сервера");
define('LANG_start_wait_note', "Запуск сервера может занять некоторое время. Ждите, не закрывайте браузер.");
define('LANG_game_type', "Игра");
define('LANG_map', "Карта");
define('LANG_starting_server', "Запуск сервера, пожалуйста, подождите...");
define('LANG_starting_server_settings', "Запуск сервера со следующими параметрами");
define('LANG_startup_params', "Параметры запуска");
define('LANG_startup_cpu', "Ядро процессора, на котором будет запущен сервер");
define('LANG_startup_nice', "Приоритетное значение сервера");
define('LANG_game_home', "Путь к серверу");
define('LANG_server_started', "Сервер запущен успешно.");
define('LANG_no_parameter_access', "У Вас нет доступа к этим параметрам.");
define('LANG_extra_parameters', "Дополнительные параметры");
define('LANG_no_extra_param_access', "У Вас нет доступа к дополнительным параметрам.");
define('LANG_extra_parameters_info', "Эти параметры будут прописаны в конце, после запуска сервера.");
define('LANG_game_exec_not_found', "Файл запуска сервера %s не был найден.");
define('LANG_select_params_and_start', "Выберите параметры запуска и нажмите '%s'.");
define('LANG_no_ip_port_pairs_assigned', "Для этого сервера не были привязаны IP и порт. Если у Вас нет доступа к конфигурации сервера то обратитесь к администратору за помощью.");
define('LANG_unable_to_get_log', "Невозможно получить лог, retval %s.");
define('LANG_server_binary_not_executable', "Файл запуска сервера не доступен. Проверьте права доступа к файлу.");
define('LANG_server_not_running_log_found', "Сервер НЕ запущен, но лог файл найден. Примечание: логи могли остаться после прошлого запуска сервера.");
define('LANG_ip_port_pair_not_owned', "IP:PORT не принадлежат вам");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Неудовлетворительные значения maxplayers. Максимально доступное количество слотов было установлено.");
define('LANG_server_running_not_responding', "Сервер запущен, но не отвечает, <br> может возникла какая-то проблема, и вы хотите выполнить - ");
define('LANG_update_started', "Обновление начато, пожалуйста подождите...");
define('LANG_failed_to_start_steam_update', "Ошибка при обновлении через steam, подробности смотрите в логе.");
define('LANG_failed_to_start_rsync_update', "Ошибка при обновлении через Rsync, подробности смотрите в логе.");
define('LANG_update_completed', "Обновление прошло успешно.");
define('LANG_update_in_progress', "Обновление в процессе, пожалуйста подождите...");
define('LANG_refresh_steam_status', "Обновить статус steam");
define('LANG_refresh_rsync_status', "Обновить статус Rsync");
define('LANG_server_running_cant_update', "При запущеном сервере обновление невозможно. Остановите его прежде чем запускать обновление.");
define('LANG_xml_steam_error', "Выбранный сервер не поддерживает установку/обновление через steam.");
define('LANG_mod_key_not_found_from_xml', "Ключ '%s' не найден в XML файле.");
define('LANG_stop_update', "Остановить обновление");
define('LANG_statistics', "Статистика");
define('LANG_servers', "Серверы");
define('LANG_players', "Игроки");
define('LANG_current_map', "Текущая карта");
define('LANG_stop_server', "Остановить сервер");
define('LANG_server_ip_port', "IP:Port сервера");
define('LANG_server_name', "Название сервера");
define('LANG_server_id', "ID сервера");
define('LANG_player_name', "Имя игрока");
define('LANG_score', "Счет");
define('LANG_time', "Время");
define('LANG_no_rights_to_stop_server', "У вас не достаточно прав для остановки этого сервера.");
define('LANG_no_ogp_lgsl_support', "Этот сервер (%s)не поддерживает LGSL и статистика не может быть показана.");
define('LANG_server_status', "Статус сервера");
define('LANG_server_stopped', "Сервер '%s' был остановлен.");
define('LANG_if_want_to_start_homes', "Запускайте сервера из %s.");
define('LANG_view_log', "Просмотр журнала");
define('LANG_if_want_manage', "Настроить сервера можно здесь");
define('LANG_columns', "столбцов");
define('LANG_group_users', "Группа:");
define('LANG_assigned_to', "Назначен:");
define('LANG_restart_server', "Перезапустить сервер");
define('LANG_restarting_server', "Перезапуск сервера, пожалуйста подождите...");
define('LANG_server_restarted', "Сервер '%s' перезапущен.");
define('LANG_server_not_running', "Сервер не запущен.");
define('LANG_address', "IP-адрес");
define('LANG_owner', "Владелец");
define('LANG_operations', "Операции");
define('LANG_search', "Поиск");
define('LANG_maps_read_from', "Карты считываются с ");
define('LANG_file', "файла");
define('LANG_folder', "папка");
define('LANG_unable_retrieve_mod_info', "Не удалось получить информацию о моде из БД");
define('LANG_unexpected_result_libremote', "Неожиданный результат libremote, пожалуйста, сообщите разработчикам.");
define('LANG_unable_get_info', "Не удается получить необходимую информацию для запуска. Блокировка запуска.");
define('LANG_server_already_running', "Сервер уже запущен. Если вы не видите сервер в Мониторинге, это может быть связано с...");
define('LANG_already_running_stop_server', "Остановка сервера");
define('LANG_error_server_already_running', "Ошибка: сервер уже запущен на данном порту");
define('LANG_failed_start_server_code', "Ошибка запуска удаленного сервер. Код ошибки: %s");
define('LANG_disabled', "отключён");
define('LANG_not_found_server', "Не удалось найти сервер с таким ID");
define('LANG_rcon_command_title', "RCON команда");
define('LANG_has_sent_to', "отправил в");
define('LANG_need_set_remote_pass', "требуется установить удалённый пароль");
define('LANG_before_sending_rcon_com', "перед отправкой RCON команды");
define('LANG_retry', "Повтор");
define('LANG_page', "страница");
define('LANG_server_cant_start', "сервер не может быть запущен");
define('LANG_server_cant_stop', "сервер не может быть остановлен");
define('LANG_error_occured_remote_host', "Ошибка удалённого сервера");
define('LANG_follow_server_status', "Вы можете следить за состоянием сервера из");
define('LANG_addons', "Аддоны");
define('LANG_hostname', "Название сервера");
define('LANG_rsync_install', "[Установить rsync]");
define('LANG_ping', "пинг");
define('LANG_team', "Команда");
define('LANG_deaths', "Смертей");
define('LANG_pid', "PID");
define('LANG_skill', "Скилл");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam ID");
define('LANG_player', "игрок");
define('LANG_port', "Порт");
define('LANG_rcon_presets', "RCON команды");
define('LANG_update_from_local_master_server', "Обновление с локального Мастер Сервера");
define('LANG_update_from_selected_rsync_server', "Обновление с выбранного сервера-rsync");
define('LANG_execute_selected_server_operations', "Выполнить операции на выбранных серверах");
define('LANG_execute_operations', "Выполнение операций");
define('LANG_account_expiration', "Истечение срока действия учетной записи");
define('LANG_mysql_databases', "База Данных MySQL");
define('LANG_failed_querying_server', "* Не удалось выполнить запрос к серверу.");
define('LANG_query_protocol_not_supported', "* В OGP нет протокола запросов, который может поддерживать этот сервер.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Запросы отключены в настройках: Запросы отключены после: %s, потому что есть %s сервера. <br>");
define('LANG_presets_for_game_and_mod', "RCON команды для %s и мода %s");
define('LANG_name', "Название");
define('LANG_command', "RCON&nbsp;команда");
define('LANG_add_preset', "Добавить команду");
define('LANG_edit_presets', "Редактировать команды");
define('LANG_del_preset', "Удалить");
define('LANG_change_preset', "Изменить");
define('LANG_send_command', "Послать команду");
define('LANG_starting_copy_with_master_server_named', "Начато копирование с мастер сервером '%s'...");
define('LANG_starting_sync_with', "Начало синхронизации с %s...");
define('LANG_refresh_interval', "Интервал обновления консоли");
define('LANG_finished_manual_update', "Готовые обновление вручную.");
define('LANG_failed_to_start_file_download', "Не удалось начать закачку файла.");
define('LANG_game_name', "Название игры");
define('LANG_dest_dir', "Назначение каталога");
define('LANG_remote_server', "Удалённый сервер");
define('LANG_file_url', "Файл URL");
define('LANG_file_url_info', "URL файла, в который будет загружен, и для несжатых в каталог.");
define('LANG_dest_filename', "имя файла назначения");
define('LANG_dest_filename_info', "имя файла для конечного файла.");
define('LANG_update_server', "Обновление сервера");
define('LANG_unavailable', "Недоступен");
define('LANG_upload_map_image', "Загрузить картинку карты");
define('LANG_upload_image', "Загрузить картинку");
define('LANG_jpg_gif_png_less_than_1mb', "Картинка должна быть jpg, gif или png и не больше 1 MB.");
define('LANG_check_dev_console', "Ошибка при загрузке файла, пожалуйста посмотрите консоль разработчика браузера.");
define('LANG_uploaded_successfully', "Загрузка завершена.");
define('LANG_cant_create_folder', "Не удается создать папку: <br><b>%s</b>");
define('LANG_cant_write_file', "Не удается записать файл: <br><b>%s</b>");
define('LANG_exceeded_php_directive', "Превышена директива PHP. <br><b>%s</b>.");
define('LANG_unknown_errors', "Неизвестная ошибка.");
define('LANG_directory', "Путь к каталогу");
define('LANG_view_player_commands', "Показать игроков 'status'");
define('LANG_hide_player_commands', "Скрыть игроков");
define('LANG_no_online_players', "Нит ни одного игрока online");
define('LANG_invalid_game_mod_id', "Не правильный ID Игры или Мода");
define('LANG_auto_update_title_popup', "Ссылка для автоматического обновления Steam");
define('LANG_auto_update_popup_html', "<p>Используйте приведенную ниже ссылку, чтобы проверить и автоматически обновить игровой сервер через Steam, если необходимо.&nbsp; Вы можете это сделать через планировщик задач-cron или вручную выполнив это. </p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Копировать");
define('LANG_auto_update_copy_me_success', "Скопировано!");
define('LANG_auto_update_copy_me_fail', "Ошибка копирования. Пожалуйста, скопируйте линк вручную.");
define('LANG_get_steam_autoupdate_api_link', "Ссылка на автообновление");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "Пользователь %sпопытался обновить home_id %dс сервер,  не являющегося мастером. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "Вы пытаетесь обновить этот сервер с не мастера сервера.");
define('LANG_cannot_update_from_own_self', "Обновление с локального сервера не может выполняться на мастер-сервере.");
define('LANG_show_server_id', "Показать ID серверов");
define('LANG_hide_server_id', "Скрыть ID серверов");
define('LANG_edit_configuration_files', "Редактировать конфигурационный файл");
define('LANG_admin', "Админ");
define('LANG_cid', "CID");
define('LANG_phan', "Фантом");
define('LANG_sec', "Секунд");
define('LANG_unknown_rsync_mirror', "Вы попытались запустить обновление с зеркала, которого не существует.");
define('LANG_custom_fields', "Кастомные поля");
?>
