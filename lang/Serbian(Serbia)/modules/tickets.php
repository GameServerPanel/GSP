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

define('LANG_support_tickets', "Тикети за подршку");
define('LANG_ticket_subject', "Назив");
define('LANG_ticket_status', "Статус");
define('LANG_ticket_updated', "Ажурирано последњи пут");
define('LANG_ticket_options', "Опције");
define('LANG_viewing_ticket', "Преглед тикета");
define('LANG_ticket_not_found', "Дати параметри тикета се не подударају са постојећом тикетом.");
define('LANG_ticket_cant_read', "Недовољна дозвола за преглед тикета.");
define('LANG_cant_view_ticket', "Није могуће преузети информације о тикету.");
define('LANG_ticket_id', "ID тикета");
define('LANG_service_id', "ID сервиса");
define('LANG_ticket_submitted', "Тикет послат");
define('LANG_submitter_info', "Информације о пошиљаоцу");
define('LANG_name', "Име");
define('LANG_ip', "IP");
define('LANG_role', "Улога корисника");
define('LANG_ticket_submit_response', "Пошаљи одговор");
define('LANG_ticket_close', "Затвори");
define('LANG_no_ticket_replies', "Нема одговора на тикет");
define('LANG_no_tickets_submitted', "Нема послатих тикета.");
define('LANG_submit_ticket', "Пошаљи тикет");
define('LANG_ticket_service', "Сервис");
define('LANG_ticket_message', "Порука");
define('LANG_ticket_errors_occured', "Следеће грешке су настале приликом подношења вашег тикета:");
define('LANG_no_ticket_subject', "Нема назива тикета");
define('LANG_invalid_ticket_subject_length', "Невалидна дужина назива тикета (4 до 64 карактера)");
define('LANG_invalid_home_selected', "Invalid Home Selected");
define('LANG_no_ticket_message', "Није наведена порука у тикету");
define('LANG_invalid_ticket_message_length', "Није валидна дужина поруке тикета (минимум 4 карактера)");
define('LANG_ticket_no_service', "Није наведен сервис за овај тикет.");
define('LANG_failed_to_open', "Отварање тикета није успело.");
define('LANG_failed_to_reply', "Одговарање на тикет није успело.");
define('LANG_no_ticket_reply', "Није наведен одговор на тикет");
define('LANG_invalid_ticket_reply_length', "Одговор на тикет није валидан (минимум 4 карактера)");
define('LANG_ticket_closed', "Затворен тикет");
define('LANG_ticket_open', "Отворен тикет");
define('LANG_ticket_admin_response', "Одговор администратора");
define('LANG_ticket_customer_response', "Одговор корисника");
define('LANG_ticket_invalid_page_num', "Покушали сте да погледате број странице без тикета!");
define('LANG_ticket_is_closed', "Тикет је затворен. Можете одговорити на овај тикет да би сте га поново отворили.");
define('LANG_reply', "Одговор");
define('LANG_invalid_rating', "Примљена оцена није важећа.");
define('LANG_successfully_rated_response', "Успешно је оцењен одговор.");
define('LANG_failed_rating_response', "Одговор није успешно оцењен.");
define('LANG_attachment_not_all_parameters_sent', "Нису сви параметри послати да би датотека била преузета.");
define('LANG_requested_attachment_missing', "Затражен прилог није доступан.");
define('LANG_requested_attachment_missing_db', "Затражен прилог не постоји у бази података.");
define('LANG_ratings_disabled', "Оцењивање одговора није омогућено.");
define('LANG_attachments', "Прилози");
define('LANG_add_file_attachment', "Додај још");
define('LANG_attachment_size_info', "Each selected file may be a maximum of %s");
define('LANG_attachment_file_size_info', "A maximum of %s file(s) may be uploaded, %s each.");
define('LANG_attachment_allowed_extensions_info', "Allowed File Extensions: %s");
define('LANG_ticket_fix_before_submitting', "Please fix the following errors before submitting the ticket");
define('LANG_ticket_fix_before_replying', "Please fix the following errors before replying to the ticket");
define('LANG_ticket_problem_with_attachments', "There was a problem with the file(s) you attached");
define('LANG_ticket_attachment_invalid_extension', "%1 does not contain a permitted extension.");
define('LANG_ticket_attachment_invalid_size', "%1 is larger than the allowed file size. %2 maximum!");
define('LANG_ticket_max_file_elements', "Only a maximum of %1 file inputs may exist.");
define('LANG_ticket_attachment_multiple_files', "One or more file inputs have multiple files selected.");
define('LANG_attachment_err_ini_size', "%s (%s) exceeds the 'upload_max_filesize' setting.");
define('LANG_attachment_err_partial', "%s was only partially uploaded.");
define('LANG_attachment_err_no_tmp', "No tmp directory exists to save %s");
define('LANG_attachment_err_cant_write', "Unable to write %s to disk.");
define('LANG_attachment_err_extension', "An extension stopped the upload of %s. Review your logs.");
define('LANG_attachment_too_large', "%s (%s) is larger than the maximum allowed size of %s!");
define('LANG_attachment_forbidden_type', "The file type of %s may not be uploaded.");
define('LANG_attachment_directory_not_writable', "Unable to save the attached files. The specified save directory is not writable.");
define('LANG_attachment_invalid_file_count', "The amount of files sent to the server was invalid. Only a maximum of %s may be uploaded");
define('LANG_ratings_enabled', "Ratings");
define('LANG_ratings_enabled_info', "Set if rating responses should be allowed.");
define('LANG_attachments_enabled', "Attachments");
define('LANG_attachments_enabled_info', "Set if the attachment system should be enabled.");
define('LANG_attachment_max_size', "Max File Size");
define('LANG_attachment_max_size_info', "Sets the max file size for attachments.");
define('LANG_attachment_limit', "Attachment Limit");
define('LANG_attachment_limit_info', "Sets how many files may be attached at once. 0 for no limit.");
define('LANG_attachment_save_dir', "Attachment Upload Location");
define('LANG_attachment_save_dir_info', "Sets where attachments should be uploaded. Ideally, outside of the public_html folder or direct access blocked.");
define('LANG_attachment_extensions', "Attachment Extensions");
define('LANG_attachment_extensions_info', "Sets the permitted extensions. Each extension should be separated by a comma.");
define('LANG_show_php_ini', "Show Estimated INI Settings");
define('LANG_settings_errors_occured', "The following errors occured when attempting to update the settings - not everything has been updated!");
define('LANG_invalid_max_size', "Invalid value for Max Size setting.");
define('LANG_invalid_unit', "Invalid unit type for Max Size setting. Expecting KB, MB, GB, TB, or PB.");
define('LANG_invalid_save_dir', "The specified save directory does not exist and can not be created.");
define('LANG_invalid_save_dir_not_writable', "The specified save directory exists but is not writable.");
define('LANG_invalid_extensions', "No attachment extensions have been specified.");
define('LANG_update_settings', "Update Settings");
define('LANG_notifications_enabled', "Notifications");
define('LANG_notifications_enabled_info', "Allow the user/admin to see if they have got a ticket awaiting reply.");
