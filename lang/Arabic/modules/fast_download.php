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

define('OGP_LANG_create_alias', "إنشاء الإسم المستعار والمجلد");
define('OGP_LANG_save_as', "الحفظ في");
define('OGP_LANG_failure', "حدث خطأ، فشلت في إنشاء ملف الاسم المستعار");
define('OGP_LANG_success', "نجح");
define('OGP_LANG_fast_download_service_for', "خدمة إعادة توجيه التحميلات ل %s");
define('OGP_LANG_to_the_path', "إلى مسار");
define('OGP_LANG_at_url', "في رابط");
define('OGP_LANG_create_alias_for', "إنشاء اسم مستعار ل");
define('OGP_LANG_fast_dl', "إعادة توجيه التحميلات (تحميل سريع)");
define('OGP_LANG_current_aliases_at_remote_server', "الأسماء المستعارة الحالية في سيرفر التحكم");
define('OGP_LANG_delete_selected_aliases', "حذف الأسماء المستعارة المحددة");
define('OGP_LANG_no_aliases_defined', "لا توجد أسماء مستعارة على شبكة الإنترنت تحددها OGP لسيرفر التحكم.");
define('OGP_LANG_fastdl_port', "بورت");
define('OGP_LANG_fastdl_port_info', "البورت الذي سيعمل عليه خادم Fast Download");
define('OGP_LANG_fastdl_ip', "عنوان");
define('OGP_LANG_fastdl_ip_info', "عنوان الأي بي أو المجال الذي سيبدأ فيه خادم التحميل السريع، يجب أن يكون المجال مدرجا في / /etc/hosts.");
define('OGP_LANG_listing', "قائمة");
define('OGP_LANG_listing_info', "إذا كان \" قيد التشغيل\"، فإن السيرفر سيعرض قائمة بمحتويات المجلد.");
define('OGP_LANG_fast_dl_advanced', "إعدادات متقدمة");
define('OGP_LANG_apply_settings_and_restart_fastdl', "حفظ التكوين الخفي وإعادة تشغيله");
define('OGP_LANG_stop_fastdl', "توقيف التحميل السريع الخفي");
define('OGP_LANG_fast_download_daemon_running', "التحميل السريع الخفي قيد التشغيل.");
define('OGP_LANG_fast_download_daemon_not_running', "التحميل السريع الخفي ليس قيد التشغيل.");
define('OGP_LANG_fastdl_could_not_be_restarted', "تعذر إعادة تشغيل خدمة التحميل السريع.");
define('OGP_LANG_configuration_file_could_not_be_written', "لم يتم حفظ ملف الإعدادات.");
define('OGP_LANG_remove_folders', "إزالة المجلد للأسماء المستعارة المحددة.");
define('OGP_LANG_remove_folder', "إزالة المجلد");
define('OGP_LANG_delete_alias', "حذف الاسم المستعار");
define('OGP_LANG_no_game_homes_assigned', "ليس لديك أي خوادم تم تعيينه لحسابك.");
define('OGP_LANG_select_remote_server', "حدد سيرفر التحكم");
define('OGP_LANG_access_rules', "شروط الوصول.");
define('OGP_LANG_create_aliases', "إنشاء الأسماء المستعارة");
define('OGP_LANG_select_game', "حدد اللعبة");
define('OGP_LANG_games_without_specified_rules', "ألعاب بدون قواعد محددة");
define('OGP_LANG_match_file_extension', "مطابقة امتداد الملف.");
define('OGP_LANG_match_file_extension_info', "حدد الإضافات مفصولة بفاصلة, <br> وستكون الملفات المطابقة قابلة للوصول إليها. <br> <b> فراغ للوصول الغير مشروط </b>.");
define('OGP_LANG_match_client_ip', "مطابقة عنوان IP الخاص بالعميل.");
define('OGP_LANG_match_client_ip_info', "سيتم منح الاتصالات مع IP مطابقة ،<br>فارغة للوصول غير المقيد. يمكنك استخدام<br>عناوين IP أو نطاقات متعددة مفصولة بفاصلة:<br>/xx subnets<br>مثال: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx subnets<br>مثال: 10.0.0.0/255.0.0.0<br>النطاقات الواصلة<br>مثال: 10.0.0.5-230<br>مطابقة النجمة<br>مثال: 10.0.*.*");
define('OGP_LANG_save_access_rules', "حفظ قواعد الوصول");
define('OGP_LANG_create_access_rules', "إنشاء قواعد الوصول");
define('OGP_LANG_invalid_entries_found', "تم العثور على إدخالات غير صالحة");
define('OGP_LANG_game_name', "إسم اللعبة");
define('OGP_LANG_alias_already_exists', "الاسم المستعار %s موجود من قبل.");
define('OGP_LANG_warning_access_rules_applied_once_alias_created', "تحذير: شروط الوصول سيتم تطبيقها عند انشاء الإسم المستعار. لا تعديلات سيتم تطبيقها على الأسماء المستعارة الحالية.");
define('OGP_LANG_autostart_on_agent_startup', "بدء التشغيل التلقائي على بدء تشغيل الوكيل");
define('OGP_LANG_autostart_on_agent_startup_info', "تشغيل خادم Fast Download تلقائياً عند بدء خادم لوحة التحكم.");
define('OGP_LANG_port_forwarded_to_80', "تم توجيه المنفذ من 80");
define('OGP_LANG_port_forwarded_to_80_info', "قم بتفعيل هذا الخيار اذا كان البورت المعد لخادم الــ Fast Download هذا قد تم تحويله من البورت 80، ولذلك سيتم اخفاء البورت من رابط عنوان الموقع.");
define('OGP_LANG_current_access_rules', "قواعد الوصول الحالية");
?>