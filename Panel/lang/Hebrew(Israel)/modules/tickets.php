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

define('LANG_support_tickets', "כרטיסי תמיכה");
define('LANG_ticket_subject', "נושא");
define('LANG_ticket_status', "מצב");
define('LANG_ticket_updated', "עודכנו אחרונים");
define('LANG_ticket_options', "אפשרויות");
define('LANG_viewing_ticket', "צופה בכרטיס");
define('LANG_ticket_not_found', "פרטי הכרטיס שניתנו איהם תואמים עבור הכרטיס הקיים.");
define('LANG_ticket_cant_read', "אין אישור לצפייה בקריאה זו.");
define('LANG_cant_view_ticket', "לא יכול לאחזר מידע על הקריאה.");
define('LANG_ticket_id', "מזהה קריאה");
define('LANG_service_id', "מזהה שרות");
define('LANG_ticket_submitted', "קריאה נשלחה");
define('LANG_submitter_info', "פרטי שולח");
define('LANG_name', "שם");
define('LANG_ip', "אייפי");
define('LANG_role', "תפקיד משתמש");
define('LANG_ticket_submit_response', "שלח תגובה");
define('LANG_ticket_close', "סגירה");
define('LANG_no_ticket_replies', "אין תשובות לקריאה");
define('LANG_no_tickets_submitted', "לא נשלחו קריאות.");
define('LANG_submit_ticket', "שלח קריאה");
define('LANG_ticket_service', "שרות");
define('LANG_ticket_message', "הודעה");
define('LANG_ticket_errors_occured', "השגיאות הבאות התרחשו בעת הגשת הכרטיס התמיכה שלך");
define('LANG_no_ticket_subject', "אין נושא כרטיס");
define('LANG_invalid_ticket_subject_length', "אורך הנושא אינו חוקי (מ 4 עד 64 תווים)");
define('LANG_invalid_home_selected', "בית שגוי נבחר");
define('LANG_no_ticket_message', "לא סופקה הודעה לקריאה");
define('LANG_invalid_ticket_message_length', "אורך תוכן הקריאה אינו חוקי (מינימום 4 תווים)");
define('LANG_ticket_no_service', "לא נבחר שרות לקריאה זאת.");
define('LANG_failed_to_open', "נכשל בפתיחת קריאה");
define('LANG_failed_to_reply', "נכשל ביצירת תגובה לקריאה");
define('LANG_no_ticket_reply', "לא סופקה תגובה לקריאה");
define('LANG_invalid_ticket_reply_length', "אורך תוכן הכרטיס אינו חוקי (מינימום 4 תווים)");
define('LANG_ticket_closed', "קריאה נסגרה");
define('LANG_ticket_open', "קריאה פתוחה");
define('LANG_ticket_admin_response', "תגובת מנהל");
define('LANG_ticket_customer_response', "תגובת לקוח");
define('LANG_ticket_invalid_page_num', "ניסית לצפות מספר דף ללא קריאות!");
define('LANG_ticket_is_closed', "הקריאה הזו סגורה. אתה יכול להגיב לקריאה זו בכדי לפתוח אותה מחדש.");
define('LANG_reply', "הגב");
define('LANG_invalid_rating', "הדירוג שנשלח שגוי.");
define('LANG_successfully_rated_response', "תגובה דורגה בהצלחה.");
define('LANG_failed_rating_response', "נכשל בדירוג התגובה.");
define('LANG_attachment_not_all_parameters_sent', "לא כל הפרמטרים נשלחו להורדת הקובץ.");
define('LANG_requested_attachment_missing', "הקובץ המצורף המבוקש אינו קיים.");
define('LANG_requested_attachment_missing_db', "הקובץ המצורף המבוקש אינו קיים בבסיס הנתונים.");
define('LANG_ratings_disabled', "דירוג התגובות אינו פעיל.");
define('LANG_attachments', "צרופים");
define('LANG_add_file_attachment', "הוסף עוד");
define('LANG_attachment_size_info', "כל קובץ שנבחר יכול להיות מקסימום %s");
define('LANG_attachment_file_size_info', "מקסימום של %s קבצים() יכול להיות מועלה, %s כל אחד.");
define('LANG_attachment_allowed_extensions_info', "אפשר הרחבות קובץ: %s");
define('LANG_ticket_fix_before_submitting', "אנה תקין את השגיאות הבאות לפני שליחת הקריאה");
define('LANG_ticket_fix_before_replying', "אנה תקן את השגיאות הבאות לפני שתגיב לקריאה");
define('LANG_ticket_problem_with_attachments', "הייתה בעיה עם הקבצים() שצרפת");
define('LANG_ticket_attachment_invalid_extension', "%1 לא מכיל הרחבה מאושרת.");
define('LANG_ticket_attachment_invalid_size', "%1 גדול מ גודל הקובץ המותר. מקסימום %2!");
define('LANG_ticket_max_file_elements', "רק כניסות של קובץ% 1 עשויות להתקיים.");
define('LANG_ticket_attachment_multiple_files', "בכניסה לקובץ אחד או יותר נבחר מספר קבצים.");
define('LANG_attachment_err_ini_size', "%s(%s) חורג מהגדרת upload_max_filesize' setting.");
define('LANG_attachment_err_partial', "%s was only partially uploaded.");
define('LANG_attachment_err_no_tmp', "No tmp directory exists to save %s");
define('LANG_attachment_err_cant_write', "לא יכול לכתוב %s אל הדיסק");
define('LANG_attachment_err_extension', "An extension stopped the upload of %s. Review your logs.");
define('LANG_attachment_too_large', "%s (%s) is larger than the maximum allowed size of %s!");
define('LANG_attachment_forbidden_type', "סוג הקובץ של %s לא יכול לעלות.");
define('LANG_attachment_directory_not_writable', "Unable to save the attached files. The specified save directory is not writable.");
define('LANG_attachment_invalid_file_count', "The amount of files sent to the server was invalid. Only a maximum of %s may be uploaded");
define('LANG_ratings_enabled', "דירוגים");
define('LANG_ratings_enabled_info', "Set if rating responses should be allowed.");
define('LANG_attachments_enabled', "צרופים");
define('LANG_attachments_enabled_info', "Set if the attachment system should be enabled.");
define('LANG_attachment_max_size', "גודל קובץ מקסימלי");
define('LANG_attachment_max_size_info', "Sets the max file size for attachments.");
define('LANG_attachment_limit', "הגבלת צרופים");
define('LANG_attachment_limit_info', "Sets how many files may be attached at once. 0 for no limit.");
define('LANG_attachment_save_dir', "Attachment Upload Location");
define('LANG_attachment_save_dir_info', "Sets where attachments should be uploaded. Ideally, outside of the public_html folder or direct access blocked.");
define('LANG_attachment_extensions', "הרחבות צרופים");
define('LANG_attachment_extensions_info', "Sets the permitted extensions. Each extension should be separated by a comma.");
define('LANG_show_php_ini', "Show Estimated INI Settings");
define('LANG_settings_errors_occured', "The following errors occured when attempting to update the settings - not everything has been updated!");
define('LANG_invalid_max_size', "Invalid value for Max Size setting.");
define('LANG_invalid_unit', "Invalid unit type for Max Size setting. Expecting KB, MB, GB, TB, or PB.");
define('LANG_invalid_save_dir', "The specified save directory does not exist and can not be created.");
define('LANG_invalid_save_dir_not_writable', "The specified save directory exists but is not writable.");
define('LANG_invalid_extensions', "לא צוינו צרופי הרחבות.");
define('LANG_update_settings', "עדכן הגדרות");
define('LANG_notifications_enabled', "התראות");
define('LANG_notifications_enabled_info', "אפשר למשתמש/מנהל לראות אם הם קיבלו קריאה המחכה לתגובה.");
