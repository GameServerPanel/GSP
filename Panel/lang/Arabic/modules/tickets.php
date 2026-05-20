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

define('LANG_support_tickets', "تذاكر الدعم");
define('LANG_ticket_subject', "الموضوع");
define('LANG_ticket_status', "الحالة");
define('LANG_ticket_updated', "آخر تحديث");
define('LANG_ticket_options', "الخيارات");
define('LANG_viewing_ticket', "عرض تذكرة");
define('LANG_ticket_not_found', "معلمات التذكرة لا تتطابق مع التذكرة الموجودة.");
define('LANG_ticket_cant_read', "عدم كفاية الإذن لعرض التذاكر.");
define('LANG_cant_view_ticket', "غير قادر على استرداد معلومات التذكرة.");
define('LANG_ticket_id', "اي دي التذكرة");
define('LANG_service_id', "اي دي الخدمة");
define('LANG_ticket_submitted', "تم إرسال التذكرة");
define('LANG_submitter_info', "معلومات المرسل");
define('LANG_name', "الاسم");
define('LANG_ip', "الأيبي");
define('LANG_role', "دور المستخدم");
define('LANG_ticket_submit_response', "إرسال الرد");
define('LANG_ticket_close', "أغلق");
define('LANG_no_ticket_replies', "لا توجد أية تعليقات");
define('LANG_no_tickets_submitted', "لم يتم تقديم تذاكر.");
define('LANG_submit_ticket', "قدم التذكرة");
define('LANG_ticket_service', "خدمة");
define('LANG_ticket_message', "رسالة");
define('LANG_ticket_errors_occured', "حدثت الأخطاء التالية عند إرسال التذكرة");
define('LANG_no_ticket_subject', "لا موضوع للتذكرة");
define('LANG_invalid_ticket_subject_length', "طول الموضوع غير صالح (4 إلى 64 حرفا)");
define('LANG_invalid_home_selected', "الصفحة المحددّة غير صالحة");
define('LANG_no_ticket_message', "لا رسالة تذكرة مقدمة");
define('LANG_invalid_ticket_message_length', "طول رسالة التذكرة غير صالح (بحد أدنى 4 أحرف)");
define('LANG_ticket_no_service', "لم يتم تحديد أي خدمة لهذه التذكرة.");
define('LANG_failed_to_open', "أخفق فتح التذكرة.");
define('LANG_failed_to_reply', "أخفقت في إنشاء استجابة للتذكرة.");
define('LANG_no_ticket_reply', "لا رد للتذكرة المقدّمة");
define('LANG_invalid_ticket_reply_length', "طول رد تذكرة غير صالح (بحد أدنى 4 أحرف)");
define('LANG_ticket_closed', "تذكرة مغلقة");
define('LANG_ticket_open', "تذكرة مفتوحة");
define('LANG_ticket_admin_response', "رد المشرف");
define('LANG_ticket_customer_response', "استجابة العملاء");
define('LANG_ticket_invalid_page_num', "لقد حاولت عرض رقم الصفحة بدون تذاكر!");
define('LANG_ticket_is_closed', "تم إغلاق هذه التذكرة. يمكنك الرد على هذه التذكرة لإعادة فتحها.");
define('LANG_reply', "الرد");
define('LANG_invalid_rating', "التقييم المستلم غير صالح.");
define('LANG_successfully_rated_response', "تم تقييم الاستجابة بنجاح.");
define('LANG_failed_rating_response', "أخفق تقييم الاستجابة.");
define('LANG_attachment_not_all_parameters_sent', "لم يتم إرسال جميع المعلمات لتنزيل الملف.");
define('LANG_requested_attachment_missing', "المرفق المطلوب غير موجود.");
define('LANG_requested_attachment_missing_db', "المرفق المطلوب غير موجود في قاعدة البيانات.");
define('LANG_ratings_disabled', "تقييم الردود غير مفعل");
define('LANG_attachments', "مرفقات");
define('LANG_add_file_attachment', "أضف المزيد");
define('LANG_attachment_size_info', "قد يكون كل ملف محدد بحد أقصى من %s");
define('LANG_attachment_file_size_info', "سيتم رفع %s ملف على الأكثر، %sلكل ملف.");
define('LANG_attachment_allowed_extensions_info', "إضافات الملفات المسموح بها: %s");
define('LANG_ticket_fix_before_submitting', "يرجى تصحيح الأخطاء التالية قبل إرسال التذكرة");
define('LANG_ticket_fix_before_replying', "يرجى تصحيح الأخطاء التالية قبل الرد على التذكرة");
define('LANG_ticket_problem_with_attachments', "حدثت مشكلة في الملف (الملفات) المرفقة");
define('LANG_ticket_attachment_invalid_extension', "%1  لا يحتوي على إضافة مسموح بها.");
define('LANG_ticket_attachment_invalid_size', "%1 أكبر من حجم الملف المسموح به. %2 كحد أقصى!");
define('LANG_ticket_max_file_elements', "قد لا توجد سوى %1 من مدخلات الملف كحد أقصى.");
define('LANG_ticket_attachment_multiple_files', "يحتوي ملف واحد أو أكثر من ملفات الإدخال على ملفات متعددة محددة");
define('LANG_attachment_err_ini_size', "%s (%s) يتجاوز إعداد 'upload_max_filesize' setting.");
define('LANG_attachment_err_partial', "%sتم تحميلها جزئيا فقط. ");
define('LANG_attachment_err_no_tmp', "لا يوجد مجلد مؤقت لحفظ %s");
define('LANG_attachment_err_cant_write', "تعذر كتابة %s إلى القرص.");
define('LANG_attachment_err_extension', "إضافة قامت بإيقاف رفع %s. راجع السجلات.");
define('LANG_attachment_too_large', "%s(%s) أكبر من الحجم المسموح به %s!");
define('LANG_attachment_forbidden_type', "نوع الملف %s يمكن ان لا يتم رفعه.");
define('LANG_attachment_directory_not_writable', "لا يمكن حفظ الملفات المرفقة. المسار المحدد للحفظ لا يمكن الكتابة فيه.");
define('LANG_attachment_invalid_file_count', "عدد الملفات المسموح بنقلها إلى الخادم غير صحيح. يمكن رفع عدد %sمن الملفات فقط.");
define('LANG_ratings_enabled', "تصنيفات");
define('LANG_ratings_enabled_info', "حدد إذا كنت تريد السماح بالتصنيفات.");
define('LANG_attachments_enabled', "مرفقات");
define('LANG_attachments_enabled_info', "حدد إذا كنت تريد تفعيل نظام المرفقات.");
define('LANG_attachment_max_size', "الحد الأقصى لحجم الملف");
define('LANG_attachment_max_size_info', "قم بتحديد أقصى حجم للمرفقات.");
define('LANG_attachment_limit', "حد المرفق");
define('LANG_attachment_limit_info', "قم بتحديد عدد الملفات التي يمكن إرفاقها في نفس الوقت. ضع 0 لإلغاء الحد الأعلى للملفات.");
define('LANG_attachment_save_dir', "موقع تحميل المرفقات");
define('LANG_attachment_save_dir_info', "حدد المسار الذي سيتم رفع المرفقات إليه. من الناحية المثالية، خارج مجلد public_html أو حجب الوصول المباشر.");
define('LANG_attachment_extensions', "ملحقات المرفقات");
define('LANG_attachment_extensions_info', "تحدد الإمتدادات  المسموح بها. كل إمتداد يجب أن يكون مفصولا بفاصلة (,).");
define('LANG_show_php_ini', "إظهار إعدادات INI المقدَّرة");
define('LANG_settings_errors_occured', "الأخطاء التالية حدثت عند محاولة تحديث الإعدادات. لم يتم تحديث الكل.");
define('LANG_invalid_max_size', "قيمة غير صالحة لإعدادات الحجم الأقصى.");
define('LANG_invalid_unit', "وحدة غير صالحة في ضبط أقصى حجم. نتوقع كيلوبايت KB ، ميجابايت MB ، جيجابايت GB ، تيرابايت TB ، بيتابايت PB.");
define('LANG_invalid_save_dir', "لم يتم العثور على المسار المحدد للحفظ أو لا يمكن إنشاءه.");
define('LANG_invalid_save_dir_not_writable', "المسار المحدد للحفظ موجود، لكن لا يمكن الكتابة فيه.");
define('LANG_invalid_extensions', "لم يتم تحديد أي امتدادات لملفات المرفقات.");
define('LANG_update_settings', "إعدادات التحديث");
define('LANG_notifications_enabled', "إشعارات");
define('LANG_notifications_enabled_info', "اسمح للمستخدم / المسؤول لمعرفة ما إذا كان لديه تذكرة في انتظار الرد.");
