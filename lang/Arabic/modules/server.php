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

define('LANG_add_new_remote_host', "إضافة مضيف بعيد جديد");
define('LANG_configured_remote_hosts', "تكوين المضيف البعيد");
define('LANG_remote_host', "المضيف البعيد");
define('LANG_remote_host_info', "يجب أن يكون المضيف البعيد اسم مضيف قابل للتوسيع!");
define('LANG_remote_host_port', "بورت المضيف البعيد");
define('LANG_remote_host_port_info', "The port that is listened by the OGP Agent on remote host. Default: 12679.");
define('LANG_remote_host_name', "إسم المضيف البعيد");
define('LANG_ogp_user', "اسم مستخدم وكيل OGP");
define('LANG_remote_host_name_info', "يستخدم اسم المضيف البعيد لمساعدة المستخدمين على تحديد خوادمهم.");
define('LANG_add_remote_host', "إضافة المضيف البعيد");
define('LANG_remote_encryption_key', "مفتاح التشفير عن بعد");
define('LANG_remote_encryption_key_info', "يستخدم مفتاح التشفير عن بعد لتشفير البيانات بين الفريق والوكيل. يجب أن يكون هذا المفتاح نفسه في كلا الجانبين.");
define('LANG_server_name', "إسم السيرفر");
define('LANG_agent_ip_port', "وكيل أيبي:بورت");
define('LANG_agent_status', "حالة الوكيل");
define('LANG_ips', "الأيبيهات");
define('LANG_add_more_ips', "إذا كنت ترغب في إدخال المزيد من الأيبيهات اضغط على \"تعيين الأيبي\" عندما تكون جميع الحقول ممتلئة وسوف يظهر حقل فارغ.");
define('LANG_encryption_key_mismatch', "لا يتطابق مفتاح التشفير مع الوكيل. يرجى إعادة التحقق من تهيئة الوكيل.");
define('LANG_no_ip_for_remote_host', "You need to add at least one (1) IP address for each remote host.");
define('LANG_note_remote_host', "A remote host is a server where the OGP Agent is running on. Each host can have multiple number of IP addresses on which users can bind servers to.");
define('LANG_ip_administration', "Server &amp; IP Administration :: Open Game Panel");
define('LANG_unknown_error', "خطأ غير معروف - تم إرجاع status_chk");
define('LANG_remote_host_user_name', "UNIX user");
define('LANG_remote_host_user_name_info', "Username where the Agent is running. Example: Jonhy");
define('LANG_remote_host_ftp_ip', "أيبي الأف تي بي");
define('LANG_remote_host_ftp_ip_info', "خادم بروتوكول نقل الملفات <b>IP</b> للوكيل الحالي.");
define('LANG_remote_host_ftp_port', "FTP Port");
define('LANG_remote_host_ftp_port_info', "The FTP server <b>port</b> for the current Agent.");
define('LANG_view_log', "View Log");
define('LANG_status', "الحالة");
define('LANG_stop_firewall', "إيقاف جدار الحماية");
define('LANG_start_firewall', "تشغيل جدار الحماية");
define('LANG_seconds', "ثواني");
define('LANG_reboot', "إعادة تشغيل سيرفر التحكم");
define('LANG_restart', "إعادة تشغبل الوكيل");
define('LANG_confirm_reboot', "Are you sure you want to remotely reboot the entire physical server named '%s'?");
define('LANG_confirm_restart', "Are you sure you want to restart the Agent named '%s'?");
define('LANG_restarting', "Restarting Agent... Please wait.");
define('LANG_restarted', "تم إعادة تشغيل الوكيل بنجاح");
define('LANG_reboot_success', "Server named '%s' was successfully rebooted. You will not be able to access the server until it has successfully booted.");
define('LANG_invalid_remote_host_id', "Invalid remote host id '%s' given.");
define('LANG_remote_host_removed', "Remote host called '%s' removed successfully.");
define('LANG_editing_remote_server', "Editing remote server called '%s'");
define('LANG_remote_server_settings_changed', "Changed settings for remote server '%s' successfully.");
define('LANG_save_settings', "Save Settings");
define('LANG_set_ips', "Set IPs");
define('LANG_remote_ip', "Remote IP");
define('LANG_remote_ips_for', "IPs for Game Servers To Use on Agent Server '%s'");
define('LANG_ips_set_for_server', "IPs set for server called '%s' successfully.");
define('LANG_could_not_remove_ip', "Could not remove old IP's from database.");
define('LANG_could_add_ip', "Could add remote server IP to database.");
define('LANG_areyousure_removeagent', "Are you sure you want to remove the Agent called");
define('LANG_areyousure_removeagent2', "and all the homes related to it from the ogp database?");
define('LANG_error_while_remove', "Error occurred while removing remote server.");
define('LANG_add_ip', "Add IP");
define('LANG_remove_ip', "Remove IP");
define('LANG_edit_ip', "Edit IP");
define('LANG_wrote_changes', "Changes saved successfully.");
define('LANG_there_are_servers_running_on_this_ip', "There are servers running on this IP address.");
define('LANG_enter_ip_host', "You must enter IP for the remote host.");
define('LANG_enter_valid_ip', "You must enter valid port for the remote host. The port value can be between 0 and 65535, however recommendation is between 1024 and 65535.");
define('LANG_could_not_add_server', "Could not add server");
define('LANG_to_db', "to the database.");
define('LANG_added_server', "Added server");
define('LANG_with_port', "with port");
define('LANG_to_db_succesfully', "to the database successfully.");
define('LANG_unable_discover', "Unable to auto discover IPs on");
define('LANG_set_ip_manually', "You'll have to set them manually.");
define('LANG_found_ips', "Found IPs");
define('LANG_for_remote_server', "for the remote server.");
define('LANG_failed_add_ip', "Failed to add IP");
define('LANG_timeout', "Time Out");
define('LANG_timeout_info', "The time limit in seconds to get response from this Agent.");
define('LANG_use_nat', "Use NAT");
define('LANG_use_nat_info', "Enable if your remote server is using NAT rules. Use this setting if your game servers are running on internal private LAN IP addresses so that the panel will use your real remote IP address to query the game servers.");
define('LANG_arrange_ports', "Arrange ports");
define('LANG_assign_new_ports_range_for_ip', "Assign new ports range for IP %s");
define('LANG_assigned_port_ranges_for_ip', "Assigned port ranges for IP %s");
define('LANG_assigned_ports_for_ip', "Assigned ports for IP %s");
define('LANG_unspecified_game_types', "Unspecified game types");
define('LANG_start_port', "Start port:");
define('LANG_end_port', "End port:");
define('LANG_port_increment', "Port increment:");
define('LANG_total_assignable_ports', "Total assignable ports:");
define('LANG_available_range_ports', "Available range ports:");
define('LANG_assign_range', "Assign range");
define('LANG_edit_range', "Edit range");
define('LANG_delete_range', "Delete range");
define('LANG_home_id', "Home ID");
define('LANG_home_path', "Home path");
define('LANG_game_type', "Game type");
define('LANG_port', "Port");
define('LANG_invalid_values', "Invalid values.");
define('LANG_ports_in_range_already_arranged', "Ports in range already arranged.");
define('LANG_ports_range_already_configured_for', "Ports range already configured for %s.");
define('LANG_ports_range_added_successfull_for', "Ports range added successfully for %s.");
define('LANG_ports_range_deleted_successfull', "Ports range deleted successfully.");
define('LANG_ports_range_edited_successfull_for', "Ports range edited successfully for %s.");
define('LANG_editing_firewall_for_remote_server', "Editing Firewall for remote server named '%s'");
define('LANG_default_allowed', "Allowed by default");
define('LANG_allow_port_command', "Allow port command");
define('LANG_deny_port_command', "Deny port command");
define('LANG_allow_ip_port_command', "Allow IP:port command");
define('LANG_deny_ip_port_command', "Deny IP:port command");
define('LANG_enable_firewall_command', "Enable firewall command");
define('LANG_disable_firewall_command', "Disable firewall command");
define('LANG_get_firewall_status_command', "Get firewall status command");
define('LANG_reset_firewall_command', "Reset firewall command");
define('LANG_firewall_status', "Firewall status");
define('LANG_save_firewall_settings', "Save firewall settings");
define('LANG_reset_firewall', "Reset Firewall");
define('LANG_firewall_settings', "Firewall Settings");
define('LANG_display_public_ip', "Display Public IP");
define('LANG_ips_can_be_internal_external', "Enter usable IP addresses.&nbsp; Public IP addresses and internal LAN IP addresses (for NAT setups) can be used.");
?>
