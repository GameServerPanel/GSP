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
define('LANG_game', "Игра");
define('LANG_select_mod', "Выбрать мод");
define('LANG_manual_workshop_mod_id', "ID мода вручную");
define('LANG_manual_workshop_mod_id_info', "Вы найдете ID мода по URL-адресу мода, например 1379153273 для ARK. Вы можете установить сразу несколько модов, разделяя их через запятую.");
define('LANG_update_in_progress', "Обновление в процессе");
define('LANG_refresh_steam_workshop_status', "Обновить статус Мастерской Steam");
define('LANG_update_completed', "Обновление успешно завершено");
define('LANG_mod_does_not_belong_to_workshop', "Мода %s нет в Мастерской Steam");
define('LANG_mod_installation_started', "Установка мода началась.");
define('LANG_failed_to_start_steam_workshop', "Ошибка работы с Мастерской Steam");
define('LANG_connection_error', "Ошибка подключения");
define('LANG_install_mod', "Установить моды");
define('LANG_show_mod_info', "Показать информацию о модах");
define('LANG_select_game', "Выбрать игру");
define('LANG_save_config', "Сохранить конфигурацию");
define('LANG_mod_key_not_found_from_xml', "Мод %s не найден в xml.");
define('LANG_workshop_id', "ID Мастерской");
define('LANG_workshop_id_info', "Вы найдете ID Мастерской по URL-адресу Мастерской, например, 440900 для Conan Exiles.");
define('LANG_mods_path', "Путь модов");
define('LANG_mods_path_info', "Относительный путь для папки с модами.");
define('LANG_regex', "Регулярное выражение");
define('LANG_regex_info', "Регулярное выражение, которое соответствует модам в файле конфигурации");
define('LANG_mods_backreference_index', "Обратная ссылка модов");
define('LANG_mods_backreference_index_info', "Положение обратной ссылки со стороны регулярных выражений, которые соответствуют списку модов, начиная от 0.");
define('LANG_variable', "Переменная");
define('LANG_variable_info', "Переменная, содержащая список модов, если таковой имеется.");
define('LANG_place_after', "Место после");
define('LANG_place_after_info', "Раздел файла конфигурации, в котором отображается список модов, если таковой имеется. Он будет добавлен в конфигурационный файл, если он еще не существует. Если данная переменная отсутствует, она будет помещена в строку после этого раздела.");
define('LANG_mod_string', "Строка мода");
define('LANG_mod_string_info', "Строка, отображающая мод в списке модов. Действительные замены: %workshop_mod_id%, %first_file% (первый файл - это первый файл, найденный в папке модов, загруженный через SteamCMD)");
define('LANG_string_separator', "Разделитель строк");
define('LANG_string_separator_info', "Символ, который разделяет моды в файле конфигурации, например, новый символ строки (\\n) или запятая (,).");
define('LANG_filepath', "Путь файла");
define('LANG_filepath_info', "Путь файла конфигурации, в котором должны быть указаны моды.");
define('LANG_post_install', "Постустановочный скрипт");
define('LANG_post_install_info', "Необходимые команды в bash для перемещения модов в папку модов. Действительные замены: %mods_full_path% (полный путь к папке мод Мастерской), %workshop_mod_id%, %first_file% (первый файл - это первый файл, найденный в папке мод, загруженный через SteamCMD)");
define('LANG_install_mods', "Установить моды");
define('LANG_uninstall_mods', "Удалить моды");
define('LANG_failed_uninstalling_mod', "Ошибка удаления мода %s");
define('LANG_uninstall', "Удалить скрипт");
define('LANG_uninstall_info', "Это скрипт, который вызывается при удалении мода. Действительные замены: %mods_full_path% (полный путь к папке модов Мастерской), %mod_string% (строка мода - это имя, указанное в файле конфигурации для этого мода).");
define('LANG_remove_mods', "Удалить моды");
define('LANG_do_not_close_this_page_while_mods_are_being_installed', "Не закрывайте эту страницу во время установки модов.");
define('LANG_no_game_server_selected', "Нет выбранных серверов");
define('LANG_there_are_no_mods_installed_on_this_game_server', "Нет установленных модов на этом сервере");
define('LANG_workshop_configuration_not_found', "Файл конфигурации Мастерской не найден");
define('LANG_download_method', "Метод загрузки");
define('LANG_anonymous_login', "Анонимный вход");
define('LANG_select_at_least_one_mod_or_enter_mod_id', "Выберите минимум один мод или введите ID мода.");
define('LANG_no_game_servers_assigned', "У вас нет серверов назначенных специально для вашего аккаунта.");
?>