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

define('LANG_error', "Error");
define('LANG_title', "TeamSpeak 3 Web Interface");
define('LANG_update_available', "<h3>Attention: a new version (v%1) of this software is available under <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Logout");
define('LANG_head_vserver_switch', "Change vServer");
define('LANG_head_vserver_overview', "vServer Overview");
define('LANG_head_vserver_token', "Token Management");
define('LANG_head_vserver_liveview', "Live View");
define('LANG_e_fill_out', "Please fill out all required fields.");
define('LANG_e_upload_failed', "Upload unsuccessfull.");
define('LANG_e_server_responded', "The server responded: ");
define('LANG_e_conn_serverquery', "Could not create ServerQuery access.");
define('LANG_e_conn_vserver', "Could not choose virtual server.");
define('LANG_e_session_timedout', "Session expired.");
define('LANG_js_error', "Error");
define('LANG_js_ajax_error', "An AJAX error has occurred: %1.");
define('LANG_js_confirm_server_stop', "Do you really want to stop server #%1?");
define('LANG_js_confirm_server_delete', "Do you really want to DELETE server #%1?");
define('LANG_js_notice_server_deleted', "Server %1 was deleted successfully.\nThe overview page will be getting reloaded now.");
define('LANG_js_prompt_banduration', "Duration in hours (0=unlimited): ");
define('LANG_js_prompt_banreason', "Reason (optional): ");
define('LANG_js_prompt_msg_to', "Text Message to %1 #%2: ");
define('LANG_js_prompt_poke_to', "Poke Message to Client #%1: ");
define('LANG_js_prompt_new_propvalue', "New value for '%1': ");
define('LANG_n_server_responded', "The server responded: ");
define('LANG_login_serverquery', "ServerQuery Login");
define('LANG_login_name', "Username");
define('LANG_login_password', "Password");
define('LANG_login_submit', "Login");
define('LANG_vsselect_headline', "vServer selection");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Name");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "Status");
define('LANG_vsselect_clients', "Clients");
define('LANG_vsselect_uptime', "Uptime");
define('LANG_vsselect_choose', "Select");
define('LANG_vsselect_start', "Start");
define('LANG_vsselect_stop', "Stop");
define('LANG_vsselect_delete', "DELETE");
define('LANG_vsselect_new_headline', "Create a new virtual server");
define('LANG_vsselect_new_servername', "Server Name");
define('LANG_vsselect_new_slots', "Client slots");
define('LANG_vsselect_new_create', "Create");
define('LANG_vsselect_new_added_ok', "vServer <span class=\"online\">%1</span> was created successfully.");
define('LANG_vsselect_new_added_generated', "The generated token is:");
define('LANG_vsoverview_virtualserver', "Virtual Server");
define('LANG_vsoverview_information_head', "Information");
define('LANG_vsoverview_connection_head', "Connection");
define('LANG_vsoverview_info_general_head', "General settings");
define('LANG_vsoverview_info_servername', "Server Name");
define('LANG_vsoverview_info_host', "Host");
define('LANG_vsoverview_info_state', "Status");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "Uptime");
define('LANG_vsoverview_info_welcomemsg', "Welcome<br />message");
define('LANG_vsoverview_info_hostmsg', "Host message");
define('LANG_vsoverview_info_hostmsg_mode_output', "output");
define('LANG_vsoverview_info_hostmsg_mode_0', "none");
define('LANG_vsoverview_info_hostmsg_mode_1', "in the chat log");
define('LANG_vsoverview_info_hostmsg_mode_2', "window");
define('LANG_vsoverview_info_hostmsg_mode_3', "Window + Disconnect");
define('LANG_vsoverview_info_req_security', "Security level");
define('LANG_vsoverview_info_req_securitylvl', "required");
define('LANG_vsoverview_info_hostbanner_head', "Hostbanner");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Image address");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Hostbutton URL");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Warning on");
define('LANG_vsoverview_info_antiflood_kick', "Kick on");
define('LANG_vsoverview_info_antiflood_ban', "Ban on");
define('LANG_vsoverview_info_antiflood_banduration', "Ban length");
define('LANG_vsoverview_info_antiflood_decrease', "Decrease");
define('LANG_vsoverview_info_antiflood_points', "points");
define('LANG_vsoverview_info_antiflood_in_seconds', "seconds");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Points per tick");
define('LANG_vsoverview_conn_total_head', "Total");
define('LANG_vsoverview_conn_total_packets', "packages");
define('LANG_vsoverview_conn_total_bytes', "bytes");
define('LANG_vsoverview_conn_total_send', "sent");
define('LANG_vsoverview_conn_total_received', "received");
define('LANG_vsoverview_conn_bandwidth_head', "Bandwidth");
define('LANG_vsoverview_conn_bandwidth_last', "last");
define('LANG_vsoverview_conn_bandwidth_second', "second");
define('LANG_vsoverview_conn_bandwidth_minute', "minute");
define('LANG_vsoverview_conn_bandwidth_send', "sent");
define('LANG_vsoverview_conn_bandwidth_received', "received");
define('LANG_vstoken_token_virtualserver', "Virtual Server");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Group type");
define('LANG_vstoken_token_id1', "Server Group/<br />Channel Group");
define('LANG_vstoken_token_id2', "(Channel)");
define('LANG_vstoken_token_tokencode', "Token Code");
define('LANG_vstoken_token_delete', "Delete");
define('LANG_vstoken_new_head', "Create a new token");
define('LANG_vstoken_new_create', "Generate");
define('LANG_vstoken_new_tokentype', "Token type:");
define('LANG_vstoken_new_servergroup', "Server Group");
define('LANG_vstoken_new_channelgroup', "Channel Group");
define('LANG_vstoken_new_select_group', "Servergroup");
define('LANG_vstoken_new_select_channelgroup', "Channelgroup");
define('LANG_vstoken_new_select_channel', "Channel");
define('LANG_vstoken_new_tokentype_0', "Server");
define('LANG_vstoken_new_tokentype_1', "Channel");
define('LANG_vstoken_new_added_ok', "Token was generated successfully.");
define('LANG_vsliveview_server_virtualserver', "Virtual Server");
define('LANG_vsliveview_server_head', "Live View");
define('LANG_vsliveview_liveview_enable_autorefresh', "Auto refresh");
define('LANG_vsliveview_liveview_tooltip_to_channel', "to channel #");
define('LANG_vsliveview_liveview_tooltip_switch', "Switch");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Send Message");
define('LANG_vsliveview_liveview_tooltip_poke', "Poke");
define('LANG_vsliveview_liveview_tooltip_kick', "Kick");
define('LANG_vsliveview_liveview_tooltip_ban', "Ban");
define('LANG_vsoverview_banlist_head', "Ban list");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Name");
define('LANG_vsoverview_banlist_uid', "UniqueID");
define('LANG_vsoverview_banlist_reason', "Reason");
define('LANG_vsoverview_banlist_created', "Created");
define('LANG_vsoverview_banlist_duration', "Duration");
define('LANG_vsoverview_banlist_end', "Ends");
define('LANG_vsoverview_banlist_unlimited', "unlimited");
define('LANG_vsoverview_banlist_never', "never");
define('LANG_vsoverview_banlist_new_head', "Create new ban");
define('LANG_vsoverview_banlist_new_create', "Create");
define('LANG_vsliveview_channelbackup_head', "Channel Backup");
define('LANG_vsliveview_channelbackup_get', "Create and Download");
define('LANG_vsliveview_channelbackup_load', "Upload Channel Backup");
define('LANG_vsliveview_channelbackup_load_submit', "Recreate");
define('LANG_vsliveview_channelbackup_new_added_ok', "Channel Backup successful.");
define('LANG_time_day', "day");
define('LANG_time_days', "days");
define('LANG_time_hour', "hour");
define('LANG_time_hours', "hours");
define('LANG_time_minute', "minute");
define('LANG_time_minutes', "minutes");
define('LANG_time_second', "second");
define('LANG_time_seconds', "seconds");
define('LANG_e_2568', "You do not have sufficient rights.");
define('LANG_temp_folder_not_writable', "The templates folder (%s) is not writable.");
define('LANG_unassign_from_subuser', "Unassign from subuser.");
define('LANG_assign_to_subuser', "Assign to subuser.");
define('LANG_select_subuser', "Select subuser.");
define('LANG_no_ts3_servers_assigned_to_account', "You have no servers assigned to your account.");
define('LANG_change_virtual_server', "Change Virtual Server");
define('LANG_change_remote_server', "Change Remote Server");
?>