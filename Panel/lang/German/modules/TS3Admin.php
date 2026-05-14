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

define('LANG_error', "Fehler");
define('LANG_title', "TeamSpeak 3 Web Interface");
define('LANG_update_available', "<h3>Achtung: Eine neue Version (v%1) dieser Software ist verfügbar unter <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Ausloggen");
define('LANG_head_vserver_switch', "vServer ändern");
define('LANG_head_vserver_overview', "vServer Übersicht");
define('LANG_head_vserver_token', "Tokan Verwaltung");
define('LANG_head_vserver_liveview', "Live Ansicht");
define('LANG_e_fill_out', "Bitte füllen Sie alle Pflichtfelder.");
define('LANG_e_upload_failed', "Upload fehlgeschlagen.");
define('LANG_e_server_responded', "Der Server hat geantwortet: ");
define('LANG_e_conn_serverquery', "ServerQuery Zugriff konnte nicht erstellt werden.");
define('LANG_e_conn_vserver', "Der virtuelle Server konnte nicht ausgewählt werden.");
define('LANG_e_session_timedout', "Sitzung abgelaufen.");
define('LANG_js_error', "Fehler");
define('LANG_js_ajax_error', "Ein AJAX fehler ist aufgetreten: %1.");
define('LANG_js_confirm_server_stop', "Möchten Sie Server #%1 wirklich beenden?");
define('LANG_js_confirm_server_delete', "Wollen Sie den Server #%1 wirklich LÖSCHEN?");
define('LANG_js_notice_server_deleted', "Server %1 wurde erfolgreich gelöscht.\nDie Übersichtsseite wird jetzt neu geladen.");
define('LANG_js_prompt_banduration', "Dauer in Stunden (0=unbegrenzt): ");
define('LANG_js_prompt_banreason', "Grund (optional):");
define('LANG_js_prompt_msg_to', "Text Nachricht an %1 #%2: ");
define('LANG_js_prompt_poke_to', "Anstups Nachricht an Client #%1: ");
define('LANG_js_prompt_new_propvalue', "Neuen Wert für '%1': ");
define('LANG_n_server_responded', "Der Server antwortete: ");
define('LANG_login_serverquery', "ServerQuery Login");
define('LANG_login_name', "Benutzername");
define('LANG_login_password', "Passwort");
define('LANG_login_submit', "Anmelden");
define('LANG_vsselect_headline', "vServer Auswahl");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Name");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "Status");
define('LANG_vsselect_clients', "Kunden");
define('LANG_vsselect_uptime', "Betriebszeit");
define('LANG_vsselect_choose', "Auswählen");
define('LANG_vsselect_start', "Start");
define('LANG_vsselect_stop', "Stop");
define('LANG_vsselect_delete', "LÖSCHEN");
define('LANG_vsselect_new_headline', "Neuen virtuellen Server erstellen");
define('LANG_vsselect_new_servername', "Server Name");
define('LANG_vsselect_new_slots', "Client slots");
define('LANG_vsselect_new_create', "Erstellen");
define('LANG_vsselect_new_added_ok', "vServer <span class=\"online\">%1</span> wurde erfolgreich erstellt.");
define('LANG_vsselect_new_added_generated', "Das erzeugte Token ist:");
define('LANG_vsoverview_virtualserver', "Virtueller Server");
define('LANG_vsoverview_information_head', "Information");
define('LANG_vsoverview_connection_head', "Verbindung");
define('LANG_vsoverview_info_general_head', "Allgemeine Einstellungen");
define('LANG_vsoverview_info_servername', "Server Name");
define('LANG_vsoverview_info_host', "Host");
define('LANG_vsoverview_info_state', "Status");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "Betriebszeit");
define('LANG_vsoverview_info_welcomemsg', "Willkommen<br />nachricht");
define('LANG_vsoverview_info_hostmsg', "Host nachricht");
define('LANG_vsoverview_info_hostmsg_mode_output', "ausgabe");
define('LANG_vsoverview_info_hostmsg_mode_0', "nichts");
define('LANG_vsoverview_info_hostmsg_mode_1', "Im Chat-Protokoll");
define('LANG_vsoverview_info_hostmsg_mode_2', "fenster");
define('LANG_vsoverview_info_hostmsg_mode_3', "Fenster + Trennen");
define('LANG_vsoverview_info_req_security', "Sicherheitsstufe");
define('LANG_vsoverview_info_req_securitylvl', "Erforderlich");
define('LANG_vsoverview_info_hostbanner_head', "Hostbanner");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Bildadresse");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Hostbutton URL");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Warnen an");
define('LANG_vsoverview_info_antiflood_kick', "Kicken an");
define('LANG_vsoverview_info_antiflood_ban', "Bannen an");
define('LANG_vsoverview_info_antiflood_banduration', "Bann länge");
define('LANG_vsoverview_info_antiflood_decrease', "Verringern");
define('LANG_vsoverview_info_antiflood_points', "punkte");
define('LANG_vsoverview_info_antiflood_in_seconds', "sekunden");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Punkte pro tick");
define('LANG_vsoverview_conn_total_head', "Insgesamt");
define('LANG_vsoverview_conn_total_packets', "pakete");
define('LANG_vsoverview_conn_total_bytes', "bytes");
define('LANG_vsoverview_conn_total_send', "senden");
define('LANG_vsoverview_conn_total_received', "empfangen");
define('LANG_vsoverview_conn_bandwidth_head', "Bandbreite");
define('LANG_vsoverview_conn_bandwidth_last', "letzte");
define('LANG_vsoverview_conn_bandwidth_second', "sekunden");
define('LANG_vsoverview_conn_bandwidth_minute', "minuten");
define('LANG_vsoverview_conn_bandwidth_send', "senden");
define('LANG_vsoverview_conn_bandwidth_received', "empfangen");
define('LANG_vstoken_token_virtualserver', "Virtueller Server");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Gruppentyp");
define('LANG_vstoken_token_id1', "Server Gruppe/<br />Channel Gruppe");
define('LANG_vstoken_token_id2', "(Channel)");
define('LANG_vstoken_token_tokencode', "Token-Code");
define('LANG_vstoken_token_delete', "Löschen");
define('LANG_vstoken_new_head', "Neuen token erstellen");
define('LANG_vstoken_new_create', "Generieren");
define('LANG_vstoken_new_tokentype', "Token typ:");
define('LANG_vstoken_new_servergroup', "Server Gruppe");
define('LANG_vstoken_new_channelgroup', "Channel Gruppe");
define('LANG_vstoken_new_select_group', "Servergruppe");
define('LANG_vstoken_new_select_channelgroup', "Channelgruppe");
define('LANG_vstoken_new_select_channel', "Channel");
define('LANG_vstoken_new_tokentype_0', "Server");
define('LANG_vstoken_new_tokentype_1', "Channel");
define('LANG_vstoken_new_added_ok', "Token wurde erfolgreich generiert.");
define('LANG_vsliveview_server_virtualserver', "Virtueller Server");
define('LANG_vsliveview_server_head', "Live Ansicht");
define('LANG_vsliveview_liveview_enable_autorefresh', "Automatische Aktualisierung");
define('LANG_vsliveview_liveview_tooltip_to_channel', "zu channel #");
define('LANG_vsliveview_liveview_tooltip_switch', "Wechsel");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Nachricht senden");
define('LANG_vsliveview_liveview_tooltip_poke', "Anstupsen");
define('LANG_vsliveview_liveview_tooltip_kick', "Kicken");
define('LANG_vsliveview_liveview_tooltip_ban', "Bannen");
define('LANG_vsoverview_banlist_head', "Ban Liste");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Name");
define('LANG_vsoverview_banlist_uid', "Eindeutige ID");
define('LANG_vsoverview_banlist_reason', "Grund");
define('LANG_vsoverview_banlist_created', "Erstellt");
define('LANG_vsoverview_banlist_duration', "Dauer");
define('LANG_vsoverview_banlist_end', "Endet");
define('LANG_vsoverview_banlist_unlimited', "unbegrenzt");
define('LANG_vsoverview_banlist_never', "niemals");
define('LANG_vsoverview_banlist_new_head', "Neues ban erstellen");
define('LANG_vsoverview_banlist_new_create', "Erstellen");
define('LANG_vsliveview_channelbackup_head', "Channel Backup");
define('LANG_vsliveview_channelbackup_get', "Erstellen und Herunterladen");
define('LANG_vsliveview_channelbackup_load', "Channel Backup Hochladen");
define('LANG_vsliveview_channelbackup_load_submit', "Neu erstellen");
define('LANG_vsliveview_channelbackup_new_added_ok', "Channel Backup erfolgreich.");
define('LANG_time_day', "tag");
define('LANG_time_days', "tage");
define('LANG_time_hour', "stunde");
define('LANG_time_hours', "stunden");
define('LANG_time_minute', "minute");
define('LANG_time_minutes', "minuten");
define('LANG_time_second', "sekunde");
define('LANG_time_seconds', "sekunden");
define('LANG_e_2568', "Sie haben nicht genügend Rechte.");
define('LANG_temp_folder_not_writable', "Der Vorlagenordner (%s) ist nicht beschreibbar.");
define('LANG_unassign_from_subuser', "Zuweisung vom subuser entziehen.");
define('LANG_assign_to_subuser', "An subuser zuweisen.");
define('LANG_select_subuser', "Subuser auswählen");
define('LANG_no_ts3_servers_assigned_to_account', "Sie haben keine Server, die Ihrem Konto zugeordnet sind.");
define('LANG_change_virtual_server', "Virtuellen Server ändern");
define('LANG_change_remote_server', "Entfernten Server ändern");
?>