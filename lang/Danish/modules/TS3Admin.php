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

define('LANG_error', "Fejl");
define('LANG_title', "Teamspeak 3 Hjemmeside Interface");
define('LANG_update_available', "<h3>OBS OBS OBS: En new version (v%1) af dette software, er muligt at hente her<a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Log ud");
define('LANG_head_vserver_switch', "Skift vServer");
define('LANG_head_vserver_overview', "vServer Oversigt");
define('LANG_head_vserver_token', "Token Management");
define('LANG_head_vserver_liveview', "Live View");
define('LANG_e_fill_out', "Venligst udfyld alle påkrævet felter.");
define('LANG_e_upload_failed', "Upload var ikke successfuldt.");
define('LANG_e_server_responded', "Serveren svarede: ");
define('LANG_e_conn_serverquery', "Kunne ikke oprette ServerQuery adgang.");
define('LANG_e_conn_vserver', "Kunne ikke vælge virtual server.");
define('LANG_e_session_timedout', "Session udløb.");
define('LANG_js_error', "Fejl");
define('LANG_js_ajax_error', "An AJAX error has occurred: %1.");
define('LANG_js_confirm_server_stop', "Vil du virkelig stoppe serveren #%1?");
define('LANG_js_confirm_server_delete', "Vil du virkelig slette serveren #%1?");
define('LANG_js_notice_server_deleted', "Server %1 blev slettet successfuldt.\nSelve oversigts siden, bliver genindlæst nu.");
define('LANG_js_prompt_banduration', "Duration in hours (0=unlimited): ");
define('LANG_js_prompt_banreason', "Grund (optional): ");
define('LANG_js_prompt_msg_to', "Text Message to %1 #%2: ");
define('LANG_js_prompt_poke_to', "Poke Message to Client #%1: ");
define('LANG_js_prompt_new_propvalue', "Ny værdi til '%1': ");
define('LANG_n_server_responded', "Serveren svarede: ");
define('LANG_login_serverquery', "ServerQuery Log ind");
define('LANG_login_name', "Brugerens navn");
define('LANG_login_password', "Kodeord");
define('LANG_login_submit', "Log ind");
define('LANG_vsselect_headline', "vServer udvalg");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Navn");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "Status");
define('LANG_vsselect_clients', "Kienter");
define('LANG_vsselect_uptime', "Uptid");
define('LANG_vsselect_choose', "Select");
define('LANG_vsselect_start', "Start");
define('LANG_vsselect_stop', "Stop");
define('LANG_vsselect_delete', "SLET");
define('LANG_vsselect_new_headline', "Opret en ny vituel server");
define('LANG_vsselect_new_servername', "Server Navn");
define('LANG_vsselect_new_slots', "Klient slots");
define('LANG_vsselect_new_create', "Opret");
define('LANG_vsselect_new_added_ok', "vServer <span class=\"online\">%1</span> blev oprettet successfuldt.");
define('LANG_vsselect_new_added_generated', "Det genereret token er:");
define('LANG_vsoverview_virtualserver', "Virtual Server");
define('LANG_vsoverview_information_head', "Information");
define('LANG_vsoverview_connection_head', "Tilsluttet");
define('LANG_vsoverview_info_general_head', "Generalle indstillinger");
define('LANG_vsoverview_info_servername', "Server Navn");
define('LANG_vsoverview_info_host', "Vært");
define('LANG_vsoverview_info_state', "Status");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "Optid");
define('LANG_vsoverview_info_welcomemsg', "Velkommen<br />besked");
define('LANG_vsoverview_info_hostmsg', "Vært besked");
define('LANG_vsoverview_info_hostmsg_mode_output', "output");
define('LANG_vsoverview_info_hostmsg_mode_0', "ingen");
define('LANG_vsoverview_info_hostmsg_mode_1', "I chat loggen");
define('LANG_vsoverview_info_hostmsg_mode_2', "vindue");
define('LANG_vsoverview_info_hostmsg_mode_3', "Vindue + Disconnect");
define('LANG_vsoverview_info_req_security', "Sikkerhes level");
define('LANG_vsoverview_info_req_securitylvl', "påkrævet");
define('LANG_vsoverview_info_hostbanner_head', "Værtsbanner");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Billed address");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Værtsknap URL");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Advarsels på");
define('LANG_vsoverview_info_antiflood_kick', "Spark slået til");
define('LANG_vsoverview_info_antiflood_ban', "Banning slået til");
define('LANG_vsoverview_info_antiflood_banduration', "Ban længde");
define('LANG_vsoverview_info_antiflood_decrease', "Decrease");
define('LANG_vsoverview_info_antiflood_points', "point");
define('LANG_vsoverview_info_antiflood_in_seconds', "sekunder");
define('LANG_vsoverview_info_antiflood_points_per_tick', "point pr tik");
define('LANG_vsoverview_conn_total_head', "Total");
define('LANG_vsoverview_conn_total_packets', "pakker");
define('LANG_vsoverview_conn_total_bytes', "bytes");
define('LANG_vsoverview_conn_total_send', "sendt");
define('LANG_vsoverview_conn_total_received', "modtaget");
define('LANG_vsoverview_conn_bandwidth_head', "Båndbredde");
define('LANG_vsoverview_conn_bandwidth_last', "sidst");
define('LANG_vsoverview_conn_bandwidth_second', "sekund");
define('LANG_vsoverview_conn_bandwidth_minute', "minut");
define('LANG_vsoverview_conn_bandwidth_send', "sendt");
define('LANG_vsoverview_conn_bandwidth_received', "modtaget");
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
define('LANG_vstoken_new_added_ok', "Token blev genereret succesfuldt.");
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
define('LANG_temp_folder_not_writable', "Download kan ikke placeres, pga Apache ikke har nogen skrive tilladelse, på systemets midlertidig mappe(%s).");
define('LANG_unassign_from_subuser', "Unassign from subuser.");
define('LANG_assign_to_subuser', "Assign to subuser.");
define('LANG_select_subuser', "Select subuser.");
define('LANG_no_ts3_servers_assigned_to_account', "You have no servers assigned to your account.");
define('LANG_change_virtual_server', "Change Virtual Server");
define('LANG_change_remote_server', "Change Remote Server");
?>