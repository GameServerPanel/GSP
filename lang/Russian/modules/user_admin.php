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

define('LANG_your_profile', "Ваш профиль");
define('LANG_new_password', "Новый пароль");
define('LANG_retype_new_password', "Повторите новый пароль");
define('LANG_login_name', "Имя пользователя");
define('LANG_language', "Язык");
define('LANG_first_name', "Имя");
define('LANG_page_limit', "Элементов на страницу");
define('LANG_page_limit_info', "Количество позиций, отображаемых на странице. Количество позиций  не может быть меньше 10.");
define('LANG_last_name', "Фамилия");
define('LANG_phone_number', "Номер телефона");
define('LANG_email_address', "Адрес e-mail");
define('LANG_city', "Город");
define('LANG_province', "Регион");
define('LANG_country', "Страна");
define('LANG_comment', "Комментарий");
define('LANG_expires', "Истекает");
define('LANG_save_profile', "Сохранить профиль");
define('LANG_new_password_info', "Если оставить поле пароля пустое, то пароль изменен не будет.");
define('LANG_theme', "Тема");
define('LANG_theme_info', "Выбранная тема будет установлена всем пользователям по умолчанию, но они смогут ее сменить в настройках своего профиля.");
define('LANG_expires_info', "Дата когда истекает срок действия аккаунта. Аккаунт не будет удален, но на него нельзя будет зайти.");
define('LANG_password_mismatch', "Пароли не совпадают.");
define('LANG_current_password', "Текущий пароль");
define('LANG_current_password_info', "Ваш текущий пароль.");
define('LANG_current_password_mismatch', "Пароль не верный.");
define('LANG_add_new_user', "Добавить нового пользователя");
define('LANG_edit_user_groups', "Редактировать группу пользователя");
define('LANG_users', "Пользователи");
define('LANG_user_role', "Права пользователя");
define('LANG_full_name', "Полное имя");
define('LANG_edit_games', "Редактировать игры");
define('LANG_edit_profile', "Редактировать профиль");
define('LANG_confirm_password', "Подтвердите пароль");
define('LANG_you_need_to_enter_both_passwords', "Вам нужно ввести оба пароля.");
define('LANG_passwords_did_not_match', "Пароли не совпадают.");
define('LANG_could_not_add_user_because_user_already_exists', "Не удалось добавить пользователя, потому что пользователь <em>%s</em>уже существует.");
define('LANG_successfully_added_user', "Пользователь <em>%s</em> успешно добавлен.");
define('LANG_add_a_new_user', "Добавление нового пользователя");
define('LANG_admin', "Администратор");
define('LANG_user', "Пользователь");
define('LANG_user_with_id_does_not_exist', "Пользователь с ID %s не существует.");
define('LANG_are_you_sure_you_want_to_delete_user', "Вы уверены что хотите удалить этого пользователя <em>%s</em>?");
define('LANG_unable_to_delete_user', "Не удалось удалить пользователя %s.");
define('LANG_successfully_deleted_user', "Пользователь <b>%s</b> успешно удален.");
define('LANG_failed_to_update_user_profile_error', "Не удалось обновить профиль пользователя. Ошибка: %s");
define('LANG_profile_of_user_modified_successfully', "Профиль пользователя <b>%s</b> успешно изменен.");
define('LANG_no_subusers', "Нет суб-пользователей, которые могут быть назначены группе. Пожалуйста создайте аккаунт суб-пользователей.");
define('LANG_ownedby', "Родительский владелец");
define('LANG_andSubUsers', "  И всех его суб-пользователей?");
define('LANG_subusers', "Суб-пользователей");
define('LANG_show_subusers', "Показать Суб-пользователей");
define('LANG_hide_subusers', "Скрыть Суб-пользователей");
define('LANG_info_group', "Здесь вы можете настроить группы и пользователей. Пользователи, вступившие в группы будут иметь все права этой группы.");
define('LANG_add_new_group', "Добавить новую группу");
define('LANG_group_name', "Имя группы");
define('LANG_add_group', "Добавить группу");
define('LANG_no_groups_available', "Нет доступных групп.");
define('LANG_delete_group', "Удалить группу");
define('LANG_add_user_to_group', "Добавить пользователя в группу");
define('LANG_add_user', "Добавить пользователя");
define('LANG_remove_from_group', "Удалить из группы");
define('LANG_add_server_to_group', "Добавить сервер в группу");
define('LANG_add_server', "Добавить сервер");
define('LANG_servers_in_group', "Серверов в группе");
define('LANG_no_servers_in_group', "Нет доступных серверов в группе %s.");
define('LANG_available_groups', "Доступные группы");
define('LANG_assign_homes', "Привязать сервер");
define('LANG_successfully_added_group', "Группа была добавлена успешно %s.");
define('LANG_group_name_empty', "Имя группы не может быть пустым.");
define('LANG_failed_to_add_group', "Не удалось добавить группу %s.");
define('LANG_could_not_add_user_to_group', "Не удалось добавить пользователя %s в группу %s, потому что он уже в этой группе.");
define('LANG_successfully_added_to_group', ">Успешно добавлено %sв группу <em>%s</em>.");
define('LANG_could_not_add_server_to_group', "Не удалось добавить сервер в группу %s, потому что он уже в этой группе.");
define('LANG_successfully_added_server_to_group', "Сервер успешно добавлено в группу <em>%s</em>.");
define('LANG_successfully_removed_from_group', "Успешно удаление %s из группы <em>%s</em>.");
define('LANG_could_not_delete_server_from_group', "Не удалось удалить сервер %sиз группы <em>%s</em>.");
define('LANG_successfully_removed_server_from_group', "Сервер успешно удален %s из группы <em>%s</em>.");
define('LANG_group_with_id_does_not_exist', "%s группа не существует.");
define('LANG_are_you_sure_you_want_to_delete_group', "Вы уверенны что хотите удалить группу <em>%s</em>?");
define('LANG_unable_to_delete_group', "Не удалось удалить группу %s.");
define('LANG_successfully_deleted_group', "Группа <b>%s</b>успешно удалена.");
define('LANG_editing_profile', "Редактирование профиля: %s");
define('LANG_valid_user', "Укажите действительного пользователя.");
define('LANG_enter_valid_username', "Пожалуйста введите действительное имя пользователя.");
define('LANG_unexpected_role', "Неизвестная роль пользователя");
define('LANG_search', "Поиск");
define('LANG_api_token', "API токен");
define('LANG_user_receives_emails', "Receive emails");
?>
