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

define('LANG_no_games_to_monitor', "ليس لديك أي ألعاب مخصصة لك يمكنك مراقبتها.");
define('LANG_status', "الحالة");
define('LANG_fail_no_mods', "لا يوجد مودات مفعلة لهذه اللعبة! يجب أن تطلب من المشرف أو مدير لوحة التحكم إضافة مودات للعبة المخصصة لك.");
define('LANG_no_game_homes_assigned', "لا يوجد أي خوادم تم تعيينها لحسابك.");
define('LANG_select_game_home_to_configure', "حدد خادم اللعبة الذي تريد تكوينه");
define('LANG_file_manager', "مدير الملفات");
define('LANG_configure_mods', "تهيئة الـمودات");
define('LANG_install_update_steam', "تثبيت / تحديث عن طريق Steam");
define('LANG_install_update_manual', "تثبيت / تحديث يدويا");
define('LANG_assign_game_homes', "تعيين خوادم اللعبة");
define('LANG_user', "المستخدم");
define('LANG_group', "مجموعة");
define('LANG_start', "تشغيل");
define('LANG_ogp_agent_ip', "اي بي خادم OGP");
define('LANG_max_players', "الحد الأقصى للاعبين");
define('LANG_max', "الحد الأقصى");
define('LANG_ip_and_port', "ايبي و البورت");
define('LANG_available_maps', "الخرائط المتاحة");
define('LANG_map_path', "مسار الخريطة");
define('LANG_available_parameters', "المعلمات المتاحة");
define('LANG_start_server', "تشغيل السيرفر");
define('LANG_start_wait_note', "عملية تشغيل السيرفر قد تأخذ وقتاً أطول من المعتاد، الرجاء الإبقاء على المتصفح مفتوحاً.");
define('LANG_game_type', "نوع اللعبة");
define('LANG_map', "خريطة");
define('LANG_starting_server', "جار تشغيل السيرفر، الرجاء الانتظار ...");
define('LANG_starting_server_settings', "تشغيل السيرفر مع الإعدادات التالية");
define('LANG_startup_params', "معلمات البدء");
define('LANG_startup_cpu', "المعالج الذي سيعمل عليه السيرفر");
define('LANG_startup_nice', "قيمة nice للخادم");
define('LANG_game_home', "المسار الأساسي");
define('LANG_server_started', "تم تشغيل السيرفر بنجاح.");
define('LANG_no_parameter_access', "ليس لديك إمكانية الوصول إلى المعلمات.");
define('LANG_extra_parameters', "معلمات اضافية");
define('LANG_no_extra_param_access', "ليس لديك إمكانية الوصول إلى المعلمات الإضافية.");
define('LANG_extra_parameters_info', "يتم وضع هذه المعلمات في نهاية سطر الأوامر عند بدء السيرفر.");
define('LANG_game_exec_not_found', "الملف التنفيذي للعبة %s لم يتم العثور عليه في السيرفر.");
define('LANG_select_params_and_start', "اختر معاملات بدء التشغيل الخاصة بالسيرفر ثم اضغط '%s'.");
define('LANG_no_ip_port_pairs_assigned', "لم يتم تعيين عنوان IP أو بورت لهذا المسار. إذا كنت لا تستطيع الوصول إلى خيارات تعديل المسار الأساسي عليك بالإتصال بمدير لوحة التحكم.");
define('LANG_unable_to_get_log', "غير قادر على الحصول على السجل ، retval %s.");
define('LANG_server_binary_not_executable', "ملف تشغيل السيرفر لا يمكن تنفيذه. عليك بالتحقق من أنه لديك الصلاحيات اللازمة الخاصة بالمجلد الرئيسي للسيرفر.");
define('LANG_server_not_running_log_found', "السيرفر ليس قيد التشغيل، ولكن السجل موجود. ملاحظة: هذا السجل يمكن أن لا يكون مرتبطاً بآخر عملية تشغيل للسيرفر.");
define('LANG_ip_port_pair_not_owned', "IP:PORT زوج غير مملوك.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "قيمة maxplayers غير مناسبة، لقد قمت بتعيين أقصى قيمة لعدد اللاعبين.");
define('LANG_server_running_not_responding', "السيرفر قيد التشغيل، ولكنه لا يستجيب. <br>يمكن أن يكون بسبب مشكلة ما وانت تريد أن");
define('LANG_update_started', "بدأ التحديث، يرجى الانتظار ...");
define('LANG_failed_to_start_steam_update', "فشل بدء تحديث Steam. انظر سجل الوكيل.");
define('LANG_failed_to_start_rsync_update', "فشل بدء تحديث Rsync. انظر سجل الوكيل.");
define('LANG_update_completed', "اكتمل التحديث بنجاح.");
define('LANG_update_in_progress', "جار التحديث، يرجى الانتظار ...");
define('LANG_refresh_steam_status', "تحديث حالة Steam");
define('LANG_refresh_rsync_status', "تحديث حالة Rsync");
define('LANG_server_running_cant_update', "الخادم شغال إذا التحديث ليس معقول. أوقف الخادم قبل التحديث.");
define('LANG_xml_steam_error', "نوع الخادم المحدد لا يدعم التثبيت / التحديث Steam.");
define('LANG_mod_key_not_found_from_xml', "مفتاح التعديل '%s' لم يتم العثور عليه من ملف XML.");
define('LANG_stop_update', "إيقاف التحديث");
define('LANG_statistics', "الإحصاءات");
define('LANG_servers', "سيرفرات");
define('LANG_players', "لاعبين");
define('LANG_current_map', "الخريطة الحالية");
define('LANG_stop_server', "إيقاف السيرفر");
define('LANG_server_ip_port', "سيرفر ايبي:بورت");
define('LANG_server_name', "إسم السيرفر");
define('LANG_server_id', "معرف السيرفر");
define('LANG_player_name', "إسم اللاعب");
define('LANG_score', "النقاط");
define('LANG_time', "الوقت");
define('LANG_no_rights_to_stop_server', "ليس لديك حقوق لإيقاف هذا السيرفر.");
define('LANG_no_ogp_lgsl_support', "هذا السيرفر (يعمل: %s) لا يملك دعم LGSL في OGP وبالتالي لا يمكن عرض إحصائياته او بياناته.");
define('LANG_server_status', "الخادم على %s هو %s.");
define('LANG_server_stopped', "تم إيقاف السيرفر '%s' .");
define('LANG_if_want_to_start_homes', "إذا كنت ترغب في بدء تشغيل سيرفرات الألعاب اذهب إلى %s.");
define('LANG_view_log', "سجل المشاهد");
define('LANG_if_want_manage', "إذا كنت ترغب في إدارة الألعاب الخاصة بك يمكنك القيام بذلك في");
define('LANG_columns', "أعمدة");
define('LANG_group_users', "مستخدمو المجموعة:");
define('LANG_assigned_to', "مخصص ل:");
define('LANG_restart_server', "إعادة تشغيل السيرفر");
define('LANG_restarting_server', "جار إعادة تشغيل السيرفر، يرجى الانتظار ...");
define('LANG_server_restarted', "تمت إعادة تشغيل الخادم '%s'.");
define('LANG_server_not_running', "الخادم ليس قيد التشغيل.");
define('LANG_address', "العنوان");
define('LANG_owner', "الصاحب");
define('LANG_operations', "عمليات");
define('LANG_search', "بحث");
define('LANG_maps_read_from', "قراءة الخرائط من");
define('LANG_file', "ملف");
define('LANG_folder', "مجلد");
define('LANG_unable_retrieve_mod_info', "لا يمكن استجلاب معلومات التعديل mod من قاعدة البيانات.");
define('LANG_unexpected_result_libremote', "نتيجة غير متوقعة من libremote، الرجاء إعلام المطورين.");
define('LANG_unable_get_info', "لا يمكن الحصول على معلومات بدء التشغيل. سيتم الإيقاف.");
define('LANG_server_already_running', "السيرفر يعمل بالفعل. إذا كنت لا ترى السيرفر في نافذة مراقبة اللعبة، يمكن أن يكون هناك خطأ ما، وقد تريد أن");
define('LANG_already_running_stop_server', "إيقاف السيرفر.");
define('LANG_error_server_already_running', "خطأ: الخادم يعمل بالفعل على port");
define('LANG_failed_start_server_code', "فشل بدء تشغيل السيرفر المتحكم. رمز الخطأ: %s");
define('LANG_disabled', "معطل");
define('LANG_not_found_server', "لا يمكن العثور على السيرفر بالهوية");
define('LANG_rcon_command_title', "أمر RCON");
define('LANG_has_sent_to', "تم إرسالها إلى");
define('LANG_need_set_remote_pass', "عليك بتعيين كلمة المرور الخاصة بالتحكم عن بعد");
define('LANG_before_sending_rcon_com', "قبل إرسال أمر rcon");
define('LANG_retry', "إعادة المحاولة");
define('LANG_page', "صفحة");
define('LANG_server_cant_start', "لا يمكن تشغيل السيرفر");
define('LANG_server_cant_stop', "لا يمكن إيقاف السيرفر");
define('LANG_error_occured_remote_host', "خطأ في المستضيف");
define('LANG_follow_server_status', "يمكنك متابعة حالة السيرفر من");
define('LANG_addons', "إضافات");
define('LANG_hostname', "اسم المضيف");
define('LANG_rsync_install', "[Rsync Install]");
define('LANG_ping', "بينغ");
define('LANG_team', "فريق");
define('LANG_deaths', "Deaths");
define('LANG_pid', "PID");
define('LANG_skill', "الخبرة");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam ID");
define('LANG_player', "لاعب");
define('LANG_port', "بورت");
define('LANG_rcon_presets', "إعدادات RCON المسبقة");
define('LANG_update_from_local_master_server', "التحديث من السيرفر المحلي الرئيسي");
define('LANG_update_from_selected_rsync_server', "التحديث من سيرفر Rsync المحدد");
define('LANG_execute_selected_server_operations', "تنفيذ عمليات السيرفر المحددة");
define('LANG_execute_operations', "تنفيذ العمليات");
define('LANG_account_expiration', "انتهاء صلاحية الحساب");
define('LANG_mysql_databases', "قواعد بيانات MySQL");
define('LANG_failed_querying_server', "* فشل الاستعلام عن السيرفر.");
define('LANG_query_protocol_not_supported', "* لا يوجد بروتوكول استعلام في OGP يمكنه دعم هذا السيرفر.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "تم تعطيل الاستعلامات من خلال الإعداد: تعطيل طلبات البحث بعد: %s, بما لديك %s خادم.<br>");
define('LANG_presets_for_game_and_mod', "RCON presets for %s and mod %s");
define('LANG_name', "الاسم");
define('LANG_command', "RCON&nbsp;Command");
define('LANG_add_preset', "أضف مسبقا");
define('LANG_edit_presets', "تحرير الإعدادات المسبقة");
define('LANG_del_preset', "حذف");
define('LANG_change_preset', "تغيير");
define('LANG_send_command', "إرسال الأمر");
define('LANG_starting_copy_with_master_server_named', "Starting copy with master server named '%s'...");
define('LANG_starting_sync_with', "Starting sync with %s...");
define('LANG_refresh_interval', "فترة تحديث السجل");
define('LANG_finished_manual_update', "انتهى التحديث اليدوي.");
define('LANG_failed_to_start_file_download', "فشل بدء تحميل الملف");
define('LANG_game_name', "إسم اللعبة");
define('LANG_dest_dir', "دليل الوجهة");
define('LANG_remote_server', "السيرفر المتحكم");
define('LANG_file_url', "رابط الملف");
define('LANG_file_url_info', "The URL of the file that is uploaded and uncompressed to the directory.");
define('LANG_dest_filename', "وجهة اسم الملف");
define('LANG_dest_filename_info', "اسم الملف للملف الوجهة.");
define('LANG_update_server', "تحديث السيرفر");
define('LANG_unavailable', "غير متوفره");
define('LANG_upload_map_image', "رفع صورة الخريطة");
define('LANG_upload_image', "رفع صورة");
define('LANG_jpg_gif_png_less_than_1mb', "يجب أن تكون الصورة بتنسيق jpg أو gif أو png وأقل من 1 ميغابايت.");
define('LANG_check_dev_console', "خطأ أثناء رفع الملف ، يرجى التحقق من وحدة تحكم مطور المتصفح.");
define('LANG_uploaded_successfully', "تم الرفع بنجاح.");
define('LANG_cant_create_folder', "لا يمكن إنشاء مجلد:<br> <b>%s</b>");
define('LANG_cant_write_file', "لا يمكن كتابة الملف:<br> <b>%s</b>");
define('LANG_exceeded_php_directive', "تم تجاوز أمر PHP .<br> <b>%s</b>.");
define('LANG_unknown_errors', "أخطاء غير معروفة.");
define('LANG_directory', "الدليل");
define('LANG_view_player_commands', "عرض أوامر لاعب");
define('LANG_hide_player_commands', "إخفاء أوامر اللاعب");
define('LANG_no_online_players', "لا يوجد لاعبين متواجدين.");
define('LANG_invalid_game_mod_id', "Invalid Game/Mod ID specified.");
define('LANG_auto_update_title_popup', "رابط تحديث تلقائي لSteam");
define('LANG_auto_update_popup_html', "<p>استخدم الرابط أدناه للتحقق من خادم اللعبة وتحديثه تلقائيًا عبر Steam إذا لزم الأمر.&nbsp; يمكنك الاستعلام عنها باستخدام cronjob أو بدء العملية يدويًا.</p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "نسخ");
define('LANG_auto_update_copy_me_success', "تم النسخ!");
define('LANG_auto_update_copy_me_fail', "فشل النسخ. يرجى نسخ الرابط يدويًا.");
define('LANG_get_steam_autoupdate_api_link', "رابط التحديث التلقائي");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "روابط واجهة برمجة التطبيقات");
define('LANG_update_attempt_from_nonmaster_server', "User %s attempted to update home_id %d from a non-master server. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "You are attempting to update this server from a non-master server.");
define('LANG_cannot_update_from_own_self', "قد لا يعمل تحديث الخادم المحلي على الخادم رئيسي.");
define('LANG_show_server_id', "إظهار معرفات الخادم");
define('LANG_hide_server_id', "إخفاء معرفات الخادم");
define('LANG_edit_configuration_files', "تعديل ملفات التكوين");
define('LANG_admin', "مشرف");
define('LANG_cid', "CID");
define('LANG_phan', "وهمي");
define('LANG_sec', "ثواني");
define('LANG_unknown_rsync_mirror', "لقد حاولت بدء تحديث من مرآة غير موجودة.");
define('LANG_custom_fields', "الحقول المخصصة");
?>
