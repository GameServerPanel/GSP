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

define('LANG_error', "خطأ");
define('LANG_title', "واجهة ويب TeamSpeak 3");
define('LANG_update_available', "<h3>تنبيه: يتوفر إصدار جديد (v%1) متاح تحت <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "تسجيل الخروج");
define('LANG_head_vserver_switch', "تغيير vServer");
define('LANG_head_vserver_overview', "vServer نظرة عامة");
define('LANG_head_vserver_token', "إدارة الرموز");
define('LANG_head_vserver_liveview', "المعاينة الحية");
define('LANG_e_fill_out', "من فضلك إملأ كل الخانات المطلوبة.");
define('LANG_e_upload_failed', "الرفع غير ناجح.");
define('LANG_e_server_responded', "استجابة السيرفر:");
define('LANG_e_conn_serverquery', "تعذر إنشاء صلاحيات ServerQuery.");
define('LANG_e_conn_vserver', "لا يمكن اختيار الخادم الإفتراضي.");
define('LANG_e_session_timedout', "انتهت صلاحية الجلسة.");
define('LANG_js_error', "خطأ");
define('LANG_js_ajax_error', "خطأ AJAX قد حدث: %1.");
define('LANG_js_confirm_server_stop', "هل تريد بالفعل إيقاف السيرفر #%1؟");
define('LANG_js_confirm_server_delete', "هل تريد حقاً حذف الخادم #%1؟");
define('LANG_js_notice_server_deleted', "تم حذف الخادم %1 بنجاح.\nستتم إعادة تحميل صفحة النظرة العامة الآن.");
define('LANG_js_prompt_banduration', "المدة بالساعات (0= غير محدود):");
define('LANG_js_prompt_banreason', "السبب (اختياري):");
define('LANG_js_prompt_msg_to', "رسالة نصية إلى %1 #%2:");
define('LANG_js_prompt_poke_to', "رسالة نكز إلى المستخدم #%1:");
define('LANG_js_prompt_new_propvalue', "قيمة جديدة لـ '%1': ");
define('LANG_n_server_responded', "السيرفر استجاب:");
define('LANG_login_serverquery', "تسجيل دخول ServerQuery");
define('LANG_login_name', "إسم المستخدم");
define('LANG_login_password', "كلمة المرور");
define('LANG_login_submit', "تسجيل الدخول");
define('LANG_vsselect_headline', "اختيار vServer");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "إسم");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "الحالة");
define('LANG_vsselect_clients', "عملاء");
define('LANG_vsselect_uptime', "مدة التشغيل");
define('LANG_vsselect_choose', "اختر");
define('LANG_vsselect_start', "إبدأ");
define('LANG_vsselect_stop', "توقف");
define('LANG_vsselect_delete', "حذف");
define('LANG_vsselect_new_headline', "إنشاء سيرفر افتراضي جديد");
define('LANG_vsselect_new_servername', "إسم السيرفر");
define('LANG_vsselect_new_slots', "عدد المستخدمين");
define('LANG_vsselect_new_create', "انشاء");
define('LANG_vsselect_new_added_ok', "تم إنشاء السيرفر <span class=\"online\">%1</span> بنجاح.");
define('LANG_vsselect_new_added_generated', "الرمز الذي تم إنشاؤه هو:");
define('LANG_vsoverview_virtualserver', "خادم إفتراضي");
define('LANG_vsoverview_information_head', "معلومات");
define('LANG_vsoverview_connection_head', "اتصال");
define('LANG_vsoverview_info_general_head', "الإعدادت العامة");
define('LANG_vsoverview_info_servername', "إسم السيرفر");
define('LANG_vsoverview_info_host', "المضيف");
define('LANG_vsoverview_info_state', "الحالة");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "مدة التشغيل");
define('LANG_vsoverview_info_welcomemsg', "رسالة<br />الترحيب");
define('LANG_vsoverview_info_hostmsg', "رسالة المضيف");
define('LANG_vsoverview_info_hostmsg_mode_output', "الناتج");
define('LANG_vsoverview_info_hostmsg_mode_0', "لاشيء");
define('LANG_vsoverview_info_hostmsg_mode_1', "في سجل المحادثات");
define('LANG_vsoverview_info_hostmsg_mode_2', "نافذة");
define('LANG_vsoverview_info_hostmsg_mode_3', "نافذة + قطع الإتصال");
define('LANG_vsoverview_info_req_security', "مستوى الأمان");
define('LANG_vsoverview_info_req_securitylvl', "مطلوب");
define('LANG_vsoverview_info_hostbanner_head', "شعار المضيف");
define('LANG_vsoverview_info_hostbanner_url', "الرابط");
define('LANG_vsoverview_info_hostbanner_imgurl', "عنوان الصورة");
define('LANG_vsoverview_info_hostbanner_buttonurl', "عنوان URL لزر المضيف");
define('LANG_vsoverview_info_antiflood_head', "مكافحة الفيضانات");
define('LANG_vsoverview_info_antiflood_warning', "تحذير عند");
define('LANG_vsoverview_info_antiflood_kick', "طرد عند");
define('LANG_vsoverview_info_antiflood_ban', "حظر عند");
define('LANG_vsoverview_info_antiflood_banduration', "مدة الحظر");
define('LANG_vsoverview_info_antiflood_decrease', "إنقاص");
define('LANG_vsoverview_info_antiflood_points', "نقاط");
define('LANG_vsoverview_info_antiflood_in_seconds', "ثواني");
define('LANG_vsoverview_info_antiflood_points_per_tick', "نقاط لكل جزء من الثانية");
define('LANG_vsoverview_conn_total_head', "المجموع");
define('LANG_vsoverview_conn_total_packets', "حِزَمْ");
define('LANG_vsoverview_conn_total_bytes', "بايت");
define('LANG_vsoverview_conn_total_send', "تم الإرسال");
define('LANG_vsoverview_conn_total_received', "تم الإستلام");
define('LANG_vsoverview_conn_bandwidth_head', "النطاق");
define('LANG_vsoverview_conn_bandwidth_last', "الأخير");
define('LANG_vsoverview_conn_bandwidth_second', "ثانية");
define('LANG_vsoverview_conn_bandwidth_minute', "دقيقة");
define('LANG_vsoverview_conn_bandwidth_send', "تم الإرسال");
define('LANG_vsoverview_conn_bandwidth_received', "تم الإستلام");
define('LANG_vstoken_token_virtualserver', "السيرفر الإفتراضي");
define('LANG_vstoken_token_head', "الرمز");
define('LANG_vstoken_token_type', "نوع المجموعة");
define('LANG_vstoken_token_id1', "مجموعة السيرفر\<br />مجموعة القناة");
define('LANG_vstoken_token_id2', "(قناة)");
define('LANG_vstoken_token_tokencode', "رمز الشفرة");
define('LANG_vstoken_token_delete', "حذف");
define('LANG_vstoken_new_head', "إنشاء رمز جديد");
define('LANG_vstoken_new_create', "توليد");
define('LANG_vstoken_new_tokentype', "نوع الرمز:");
define('LANG_vstoken_new_servergroup', "مجموعة السيرفر");
define('LANG_vstoken_new_channelgroup', "مجموعة القناة");
define('LANG_vstoken_new_select_group', "مجموعة السيرفر");
define('LANG_vstoken_new_select_channelgroup', "مجموعة القناة ");
define('LANG_vstoken_new_select_channel', "القناة");
define('LANG_vstoken_new_tokentype_0', "السيرفر");
define('LANG_vstoken_new_tokentype_1', "القناة");
define('LANG_vstoken_new_added_ok', "تم إنشاء الرمز بنجاح.");
define('LANG_vsliveview_server_virtualserver', "السيرفر الإفتراضي");
define('LANG_vsliveview_server_head', "مشاهدة حية");
define('LANG_vsliveview_liveview_enable_autorefresh', "تحديث تلقائي");
define('LANG_vsliveview_liveview_tooltip_to_channel', "إلى القناة #");
define('LANG_vsliveview_liveview_tooltip_switch', "تبديل");
define('LANG_vsliveview_liveview_tooltip_send_msg', "إرسال رسالة");
define('LANG_vsliveview_liveview_tooltip_poke', "نكز");
define('LANG_vsliveview_liveview_tooltip_kick', "طرد");
define('LANG_vsliveview_liveview_tooltip_ban', "حظر");
define('LANG_vsoverview_banlist_head', "قائمة الحظر");
define('LANG_vsoverview_banlist_id', "الأيدي #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "الإسم");
define('LANG_vsoverview_banlist_uid', "أيدي فريد");
define('LANG_vsoverview_banlist_reason', "السبب");
define('LANG_vsoverview_banlist_created', "تم الإنشاء");
define('LANG_vsoverview_banlist_duration', "المدة");
define('LANG_vsoverview_banlist_end', "تنتهي");
define('LANG_vsoverview_banlist_unlimited', "غير محدود");
define('LANG_vsoverview_banlist_never', "أبداً");
define('LANG_vsoverview_banlist_new_head', "إنشاء حظر جديد");
define('LANG_vsoverview_banlist_new_create', "إنشاء");
define('LANG_vsliveview_channelbackup_head', "قناة النسخ الإحتياطي");
define('LANG_vsliveview_channelbackup_get', "إنشاء و تحميل");
define('LANG_vsliveview_channelbackup_load', "رفع قناة النسخ الاحتياطي");
define('LANG_vsliveview_channelbackup_load_submit', "إعادة إنشاء");
define('LANG_vsliveview_channelbackup_new_added_ok', "النسخة إحطياطية للقناتة بنجاح.");
define('LANG_time_day', "يوم");
define('LANG_time_days', "أيام");
define('LANG_time_hour', "ساعة");
define('LANG_time_hours', "ساعات");
define('LANG_time_minute', "دقيقة");
define('LANG_time_minutes', "دقائق");
define('LANG_time_second', "ثانية");
define('LANG_time_seconds', "ثواني");
define('LANG_e_2568', "ليس لديك الصلاحيات اللازمة.");
define('LANG_temp_folder_not_writable', "مجلد القوالب (%s) غير قابلة للكتابة.");
define('LANG_unassign_from_subuser', "إلغاء تعيين من المستخدم الفرعي.");
define('LANG_assign_to_subuser', "تعين إلى المستخدم الفرعي.");
define('LANG_select_subuser', "تعين المستخدم الفرعي.");
define('LANG_no_ts3_servers_assigned_to_account', "لا يوجد لديك خوادم تم تعيينها لحسابك.");
define('LANG_change_virtual_server', "تغيير الخادم الافتراضي");
define('LANG_change_remote_server', "تغيير الخادم عن بعد");
?>