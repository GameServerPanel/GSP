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

define('LANG_support_tickets', "Támogatási hibajegyek");
define('LANG_ticket_subject', "Tárgy");
define('LANG_ticket_status', "Állapot");
define('LANG_ticket_updated', "Utolsó frissítés");
define('LANG_ticket_options', "Opciók");
define('LANG_viewing_ticket', "Hibajegy megtekintése");
define('LANG_ticket_not_found', "Az adott ticket paraméterei nem egyeznek meg a meglévő tickettel.");
define('LANG_ticket_cant_read', "A ticket megtekintése megtagadva.");
define('LANG_cant_view_ticket', "Nem sikerült lekérni a ticket információt.");
define('LANG_ticket_id', "Jegy azonosító");
define('LANG_service_id', "Szolgáltatás azonosító");
define('LANG_ticket_submitted', "Ticket elküldve.");
define('LANG_submitter_info', "Beküldő adatai");
define('LANG_name', "Név");
define('LANG_ip', "IP");
define('LANG_role', "Felhasználói szerep");
define('LANG_ticket_submit_response', "Válasz küldése");
define('LANG_ticket_close', "Bezárás");
define('LANG_no_ticket_replies', "Nincs ticket válasz");
define('LANG_no_tickets_submitted', "Nincsenek beküldött ticketek.");
define('LANG_submit_ticket', "Ticket beküldése");
define('LANG_ticket_service', "Szolgáltatás");
define('LANG_ticket_message', "Üzenet");
define('LANG_ticket_errors_occured', "A következő hibák merültek fel a ticket beküldése alatt");
define('LANG_no_ticket_subject', "Nincs ticket tárgy");
define('LANG_invalid_ticket_subject_length', "Nem megfelelő ticket tárgy (4 és 64 karakter hosszúságú)");
define('LANG_invalid_home_selected', "Érvénytelen szerver kiválasztva");
define('LANG_no_ticket_message', "Nem adott meg ticket üzenetet");
define('LANG_invalid_ticket_message_length', "Nem megfelelő ticket üzenet hosszúság (Minimum 4 karakter)");
define('LANG_ticket_no_service', "No service selected for this ticket.");
define('LANG_failed_to_open', "Nem sikerült a hibajegy megnyitása.");
define('LANG_failed_to_reply', "Nem sikerült létrehozni a ticket választ");
define('LANG_no_ticket_reply', "No Ticket Reply Provided");
define('LANG_invalid_ticket_reply_length', "Nem megfelelő ticket válasz hosszúság (Minimum 4 karakter)");
define('LANG_ticket_closed', "Zárt ticket");
define('LANG_ticket_open', "Nyitott ticket");
define('LANG_ticket_admin_response', "Adminisztrátor válasza");
define('LANG_ticket_customer_response', "Ügyfél válasza");
define('LANG_ticket_invalid_page_num', "You have attempted to view a page number with no tickets!");
define('LANG_ticket_is_closed', "This ticket is closed. You may reply to this ticket to reopen it.");
define('LANG_reply', "Válasz");
define('LANG_invalid_rating', "A beérkezett értékelés nem megfelelő.");
define('LANG_successfully_rated_response', "Sikeres értékelés.");
define('LANG_failed_rating_response', "Nem sikerült a választ értékelni.");
define('LANG_attachment_not_all_parameters_sent', "Not all parameters have been sent to download the file.");
define('LANG_requested_attachment_missing', "The requested attachment does not exist.");
define('LANG_requested_attachment_missing_db', "The requested attachment does not exist in the database.");
define('LANG_ratings_disabled', "Rating responses is not enabled.");
define('LANG_attachments', "Mellékletek");
define('LANG_add_file_attachment', "Add More");
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
define('LANG_ratings_enabled', "Értékelések");
define('LANG_ratings_enabled_info', "Set if rating responses should be allowed.");
define('LANG_attachments_enabled', "Mellékletek");
define('LANG_attachments_enabled_info', "Set if the attachment system should be enabled.");
define('LANG_attachment_max_size', "Max. fájlméret");
define('LANG_attachment_max_size_info', "Sets the max file size for attachments.");
define('LANG_attachment_limit', "Melléklet korlátozása");
define('LANG_attachment_limit_info', "Sets how many files may be attached at once. 0 for no limit.");
define('LANG_attachment_save_dir', "Attachment Upload Location");
define('LANG_attachment_save_dir_info', "Sets where attachments should be uploaded. Ideally, outside of the public_html folder or direct access blocked.");
define('LANG_attachment_extensions', "Melléklet kiterjesztések");
define('LANG_attachment_extensions_info', "Sets the permitted extensions. Each extension should be separated by a comma.");
define('LANG_show_php_ini', "Show Estimated INI Settings");
define('LANG_settings_errors_occured', "The following errors occured when attempting to update the settings - not everything has been updated!");
define('LANG_invalid_max_size', "Invalid value for Max Size setting.");
define('LANG_invalid_unit', "Invalid unit type for Max Size setting. Expecting KB, MB, GB, TB, or PB.");
define('LANG_invalid_save_dir', "The specified save directory does not exist and can not be created.");
define('LANG_invalid_save_dir_not_writable', "The specified save directory exists but is not writable.");
define('LANG_invalid_extensions', "No attachment extensions have been specified.");
define('LANG_update_settings', "Beállítások frissítése");
define('LANG_notifications_enabled', "Notifications");
define('LANG_notifications_enabled_info', "Allow the user/admin to see if they have got a ticket awaiting reply.");
