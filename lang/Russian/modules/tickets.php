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

define('LANG_support_tickets', "Тех.Поддержка");
define('LANG_ticket_subject', "Тема");
define('LANG_ticket_status', "Статус");
define('LANG_ticket_updated', "Последнее сообщение");
define('LANG_ticket_options', "Настройки");
define('LANG_viewing_ticket', "Просмотр запроса");
define('LANG_ticket_not_found', "Указанные параметры не соответствуют существующему запросу.");
define('LANG_ticket_cant_read', "Не достаточно прав для просмотра запроса.");
define('LANG_cant_view_ticket', "Не удалось получить информацию о запросах.");
define('LANG_ticket_id', "Номер запроса");
define('LANG_service_id', "ID Сервера");
define('LANG_ticket_submitted', "Запрос отправлен");
define('LANG_submitter_info', "Информация об отправителе");
define('LANG_name', "Имя");
define('LANG_ip', "IP");
define('LANG_role', "Права пользователя");
define('LANG_ticket_submit_response', "Отправить запрос");
define('LANG_ticket_close', "Закрыть");
define('LANG_no_ticket_replies', "Нет ответов");
define('LANG_no_tickets_submitted', "Нет отправленных запросов.");
define('LANG_submit_ticket', "Отправить запрос");
define('LANG_ticket_service', "Сервер");
define('LANG_ticket_message', "Сообщение");
define('LANG_ticket_errors_occured', "При отправке запроса произошли следующие ошибки");
define('LANG_no_ticket_subject', "Нет темы запроса");
define('LANG_invalid_ticket_subject_length', "Недопустимая длина темы (от 4 до 64 символов)");
define('LANG_invalid_home_selected', "Invalid Home Selected");
define('LANG_no_ticket_message', "Нет сообщения запроса");
define('LANG_invalid_ticket_message_length', "Недопустимая длина темы (Минимум 4 символа)");
define('LANG_ticket_no_service', "Не выбран сервер для данного запроса.");
define('LANG_failed_to_open', "Не удалось открыть запрос.");
define('LANG_failed_to_reply', "Не удалось создать ответ на запрос.");
define('LANG_no_ticket_reply', "Нет  ответа на запрос");
define('LANG_invalid_ticket_reply_length', "Не допустимая длина ответа в запросе (минимум 4 символа)");
define('LANG_ticket_closed', "Запрос закрыт");
define('LANG_ticket_open', "Запрос открыт");
define('LANG_ticket_admin_response', "Ответ администратора");
define('LANG_ticket_customer_response', "Ответ клиента");
define('LANG_ticket_invalid_page_num', "Вы попытались посмотреть страницу без обращений!");
define('LANG_ticket_is_closed', "Это запрос закрыт. Вы можете ответить на этот запрос, чтобы снова открыть его.");
define('LANG_reply', "Ответить");
define('LANG_invalid_rating', "Полученный рейтинг недействителен.");
define('LANG_successfully_rated_response', "Ответ успешно оценен.");
define('LANG_failed_rating_response', "Не удалось оценить ответ.");
define('LANG_attachment_not_all_parameters_sent', "Не все параметры были отправлены для загрузки файла.");
define('LANG_requested_attachment_missing', "Запрошенное вложение не существует.");
define('LANG_requested_attachment_missing_db', "Запрошенное вложение не существует в базе данных.");
define('LANG_ratings_disabled', "Оценка ответов отключена.");
define('LANG_attachments', "Вложения");
define('LANG_add_file_attachment', "Добавить больше");
define('LANG_attachment_size_info', "Каждый выбранный файл может быть максимум %s");
define('LANG_attachment_file_size_info', "Максимум %s файлов может быть загружено, по %s каждый.");
define('LANG_attachment_allowed_extensions_info', "Разрешены следующие расширения файлов: %s");
define('LANG_ticket_fix_before_submitting', "Пожалуйста, исправьте следующие ошибки перед отправкой запроса");
define('LANG_ticket_fix_before_replying', "Пожалуйста, исправьте следующие ошибки, прежде чем ответить на запрос");
define('LANG_ticket_problem_with_attachments', "Возникла проблема с прикрепленными вами файлами");
define('LANG_ticket_attachment_invalid_extension', "%1 не содержит разрешенного расширения.");
define('LANG_ticket_attachment_invalid_size', "%1 больше допустимого размера файла. %2 максимум!");
define('LANG_ticket_max_file_elements', "Можно использовать только %1 входных файлов");
define('LANG_ticket_attachment_multiple_files', "Одно или несколько полей загрузки файлов имеют несколько выбранных файлов.");
define('LANG_attachment_err_ini_size', "%s(%s) превышает параметр 'upload_max_filesize'.");
define('LANG_attachment_err_partial', "%sбыл загружен только частично.");
define('LANG_attachment_err_no_tmp', "Не существует временного каталога для сохранения %s");
define('LANG_attachment_err_cant_write', "Не удалость записать %s на диск.");
define('LANG_attachment_err_extension', "Расширение остановило загрузку %s. Просмотрите логи.");
define('LANG_attachment_too_large', "%s(%s) больше максимально допустимого размера %s! ");
define('LANG_attachment_forbidden_type', "Файл типа %s не может быть загружен.");
define('LANG_attachment_directory_not_writable', "Не удалось сохранить прикрепленные файлы. Указанный каталог сохранения недоступен для записи.");
define('LANG_attachment_invalid_file_count', "Количество файлов, отправленных на сервер, было недопустимым. Только %s файлов может быть загружено");
define('LANG_ratings_enabled', "Оценки");
define('LANG_ratings_enabled_info', " Установите, если ответы могут быть оценены.");
define('LANG_attachments_enabled', "Вложения");
define('LANG_attachments_enabled_info', "Установите, должна ли быть включена система вложений.");
define('LANG_attachment_max_size', "Максимальный размер файла.");
define('LANG_attachment_max_size_info', "Устанавливает максимальный размер файла для вложений.");
define('LANG_attachment_limit', "Предел вложений.");
define('LANG_attachment_limit_info', "Устанавливает, сколько файлов может быть прикреплено одновременно. 0 - без ограничений.");
define('LANG_attachment_save_dir', "Место загрузки вложений");
define('LANG_attachment_save_dir_info', "Устанавливает, куда вложения должны быть загружены. В идеале, вне папки public_html или в месте, куда прямой доступа заблокирован.");
define('LANG_attachment_extensions', "Расширения вложений");
define('LANG_attachment_extensions_info', "Устанавливает разрешенные расширения. Каждое расширение должно быть разделено запятой.");
define('LANG_show_php_ini', "Показать предполагаемые параметры INI");
define('LANG_settings_errors_occured', "При попытке обновления настроек возникли следующие ошибки - не все было обновлено!");
define('LANG_invalid_max_size', "Недопустимое значение для параметра «Максимальный размер».");
define('LANG_invalid_unit', "Ошибка. Используйте KB, MB, GB, TB или PB.");
define('LANG_invalid_save_dir', "Указанный каталог не существует и не может быть создан.");
define('LANG_invalid_save_dir_not_writable', "Указанный каталог сохранения существует, но не доступен для записи.");
define('LANG_invalid_extensions', "Расширения вложений не указаны.");
define('LANG_update_settings', "Обновить параметры");
define('LANG_notifications_enabled', "Уведомления");
define('LANG_notifications_enabled_info', "Разрешить пользователю/администратору увидеть, получил ли он запрос, ожидающий ответа.");
