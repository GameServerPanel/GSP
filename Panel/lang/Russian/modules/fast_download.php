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

define('LANG_create_alias', "Создать альянс и папку.");
define('LANG_save_as', "Сохранить как...");
define('LANG_failure', "Ошибка, не удалось сгенерировать файл альяса");
define('LANG_success', "Успешно");
define('LANG_fast_download_service_for', "Служба перенаправления загрузок для %s");
define('LANG_to_the_path', "Путь к файлам");
define('LANG_at_url', "URL-адрес");
define('LANG_create_alias_for', "Создать альяс для");
define('LANG_fast_dl', "Перенаправление загрузки (FastDL)");
define('LANG_current_aliases_at_remote_server', "Текущие альяс на удаленном сервере");
define('LANG_delete_selected_aliases', "Удалить выбранные альясы");
define('LANG_no_aliases_defined', "Нет никаких сетевых альясов, определенных OGP для этого удаленного сервера.");
define('LANG_fastdl_port', "Порт");
define('LANG_fastdl_port_info', "Порт, на котором будет запущен ваш сервер быстрой загрузки 'Fast Download'.");
define('LANG_fastdl_ip', "Адрес");
define('LANG_fastdl_ip_info', "IP-адрес или домен, в котором будет запущен ваш сервер Fast Download, должен быть указан в /etc/hosts.");
define('LANG_listing', "Список");
define('LANG_listing_info', "Если «включено», сервер отобразит содержимое папок.");
define('LANG_fast_dl_advanced', "Расширенные настройки");
define('LANG_apply_settings_and_restart_fastdl', "Сохраните конфигурацию службы и перезапустите его.");
define('LANG_stop_fastdl', "Остановить клиент модуля Быстрая Загрузка");
define('LANG_fast_download_daemon_running', "Служба Быстрой загрузки 'Fast Download' запущена.");
define('LANG_fast_download_daemon_not_running', "Служба Быстрой загрузки 'Fast Download' НЕ запущена.");
define('LANG_fastdl_could_not_be_restarted', "Службу Быстрой загрузки 'Fast Download' не удалось перезапустить.");
define('LANG_configuration_file_could_not_be_written', "Файл конфигурации не может быть записан.");
define('LANG_remove_folders', "Удалить папку для выбранных алаясов");
define('LANG_remove_folder', "Удалить папку");
define('LANG_delete_alias', "Удалить Аляс");
define('LANG_no_game_homes_assigned', "У вас нет серверов назначенных специально для вашего аккаунта.");
define('LANG_select_remote_server', "Выберите удаленный хост");
define('LANG_access_rules', "Правила доступа");
define('LANG_create_aliases', "Создать Аляс");
define('LANG_select_game', "Выберите игру");
define('LANG_games_without_specified_rules', "Игры без заданных правил");
define('LANG_match_file_extension', "Разрешить файлы с расширением");
define('LANG_match_file_extension_info', "Доступные файлы для скачивания будут только те,<br> у которых будет совпадать расширение с тем что вписано тут через запятую ',' <br><b>Оставте поле пустым что бы разрешить все!</b>.");
define('LANG_match_client_ip', "Разрешенные IP-адреса клиента");
define('LANG_match_client_ip_info', "Подключаться смогут только те, IP у которых будет доступ, вписав их через запятую ',' <br> Оставте поле пустым что бы разрешить подключением ВСЕМ!<br>Вы можете разрешить несколько IP-адресов<br>подсеть /xx <br> Например: 10.0.0.0/16 <br> подсеть  /xxx.xxx.xxx.xxx <br> Пример: 10.0.0.0/255.0.0.0 <br> Диапазоны <br>Пример: 10.0.0.5-230<br>Маску <br>Пример: 10.0.*.*");
define('LANG_save_access_rules', "Сохранить правила доступа");
define('LANG_create_access_rules', "Создать правила доступа");
define('LANG_invalid_entries_found', "Найдены не правильные записи");
define('LANG_game_name', "Название игры");
define('LANG_alias_already_exists', "Аляс %s уже существует.");
define('LANG_warning_access_rules_applied_once_alias_created', "ВНИМАНИЕ: Правила доступа применяются при создании аляса. Никакие изменения не будут применены к текущим алясам.");
define('LANG_autostart_on_agent_startup', "Автозапуск при запуске агента");
define('LANG_autostart_on_agent_startup_info', "Запускать службу Быстрой загрузки 'Fast Download' автоматически при запуске Агента");
define('LANG_port_forwarded_to_80', "Порт переадресован из 80");
define('LANG_port_forwarded_to_80_info', "Включите эту опцию, если порт, настроенный для этой службы Быстрой загрузки 'Fast Download', был перенаправлен из порта 80, тогда порт будет скрыт в URL-адресах.");
define('LANG_current_access_rules', "Текущие правила доступа");
?>