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

include('litefm.php');
define('LANG_curl_needed', "هذه الصفحة تتطلب PHP curl module .");
define('LANG_no_access', "أنت بحاجة إلى صلاحيات الأدمن للدخول إلى هذه الصفحة.");
define('LANG_dwl_update', "تحميل التحديث...");
define('LANG_dwl_complete', "تم التحميل");
define('LANG_install_update', "تثبيت التحديث...");
define('LANG_update_complete', "تم التحديث");
define('LANG_ignored_files', "%s ملفات تم تجاهلها");
define('LANG_not_updated_files_blacklisted', "لم يتم تحديث / تثبيت الملفات (القائمة السوداء):<br>%s");
define('LANG_latest_version', "احدث اصدار");
define('LANG_panel_version', "نسخة اللوحة");
define('LANG_update_now', "تحديث الان");
define('LANG_the_panel_is_up_to_date', "اللوحة محدثة لاخر إصدار.");
define('LANG_files_overwritten', "%s ملف استبدل");
define('LANG_files_not_overwritten', "%s لا يتم الكتابة فوق الملفات بسبب القائمة السوداء");
define('LANG_can_not_update_non_writable_files', "لايمكن تحديث الملفات/المجلدات التالية لانها غير قابلة لإستبدال");
define('LANG_dwl_failed', "رابط التحميل غير متوفر: \"%s\".<br>جرب مجدداً في وقت لاحق.");
define('LANG_temp_folder_not_writable', "لا يمكن وضع التنزيل، لأن أباتشي ليس لديه إذن كتابة في المجلد المؤقت للنظام (%s).");
define('LANG_base_dir_not_writable', "لا يمكن تحديث اللوحة، لأن أباتشي ليس لديه إذن الكتابة على مجلد \"%s\".");
define('LANG_new_files', "%s ملف جديد");
define('LANG_updated_files', "تحديث الملفات:<br>%s");
define('LANG_select_mirror', "إختر مرآه");
define('LANG_view_changes', "شاهد التغيرات");
define('LANG_updating_modules', "تحديث الموديولات");
define('LANG_updating_finished', "انتهى التحديث");
define('LANG_updated_module', "تحديث الموديول : '%s'.");
define('LANG_blacklist_files', "ملفات القائمة السوداء");
define('LANG_blacklist_files_info', "لن يتم تحديث كافة الملفات المعلمة.");
define('LANG_save_to_blacklist', "حفظ إلى القائمة السوداء");
define('LANG_no_new_updates', "لا توجد تحديثات جديدة");
define('LANG_module_file_missing', "الدليل مفقود في ملف module.php.");
define('LANG_query_failed', "أخفق تنفيذ طلب البحث");
define('LANG_query_failed_2', "إلى قاعدة البيانات.");
define('LANG_missing_zip_extension', "لم يتم تحميل ملحق php-zip. الرجاء تفعيله لتمكينه من استخدام وحدة التحديث.");
?>