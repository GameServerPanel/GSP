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

define('LANG_error', "Hiba");
define('LANG_title', "TeamSpeak 3 web felület");
define('LANG_update_available', "<h3>Figyelem: elérhető egy új verzió (v%1) ebből a szoftverből a <a href=\"%2\" target=\"_blank\">%2</a> link alatt.</h3>");
define('LANG_head_logout', "Kijelentkezés");
define('LANG_head_vserver_switch', "vSzerver megváltoztatása");
define('LANG_head_vserver_overview', "vSzerver áttekintése");
define('LANG_head_vserver_token', "Token kezelés");
define('LANG_head_vserver_liveview', "Élő nézet");
define('LANG_e_fill_out', "Kérlek, töltsd ki az összes kötelező mezőt.");
define('LANG_e_upload_failed', "Feltöltés sikertelen.");
define('LANG_e_server_responded', "A szerver válasza:");
define('LANG_e_conn_serverquery', "Nem sikerült létrehozni a ServerQuery hozzáférést.");
define('LANG_e_conn_vserver', "Nem sikerült választani virtuális szervert.");
define('LANG_e_session_timedout', "A munkamenet ideje lejárt.");
define('LANG_js_error', "Hiba");
define('LANG_js_ajax_error', "Egy AJAX hiba történt: %1.");
define('LANG_js_confirm_server_stop', "Tényleg le akarod állítani a(z) #%1 szervert?");
define('LANG_js_confirm_server_delete', "Tényleg TÖRÖLNI akarod a(z) #%1 szervert?");
define('LANG_js_notice_server_deleted', "A(z) %1 szerver sikeresen törölve.\nAz áttekintő oldal most újratöltődik.");
define('LANG_js_prompt_banduration', "Időtartam órában (0=korlátlan):");
define('LANG_js_prompt_banreason', "Ok (opcionális):");
define('LANG_js_prompt_msg_to', "Szöveges üzenet %1 #%2:");
define('LANG_js_prompt_poke_to', "Bökő üzenet a(z) #%1 kliensnek:");
define('LANG_js_prompt_new_propvalue', "Új érték a(z) '%1':");
define('LANG_n_server_responded', "A szerver válasza:");
define('LANG_login_serverquery', "ServerQuery bejelentkezés");
define('LANG_login_name', "Felhasználónév");
define('LANG_login_password', "Jelszó");
define('LANG_login_submit', "Bejelentkezés");
define('LANG_vsselect_headline', "vSzerver kiválasztás");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Név");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "Állapot");
define('LANG_vsselect_clients', "Ügyfelek");
define('LANG_vsselect_uptime', "Üzemidő");
define('LANG_vsselect_choose', "Kiválasztás");
define('LANG_vsselect_start', "Elindítás");
define('LANG_vsselect_stop', "Leállítás");
define('LANG_vsselect_delete', "TÖRLÉS");
define('LANG_vsselect_new_headline', "Egy új virtuális szerver létrehozása");
define('LANG_vsselect_new_servername', "Szerver név");
define('LANG_vsselect_new_slots', " Kliens férőhelyek");
define('LANG_vsselect_new_create', "Létrehoz");
define('LANG_vsselect_new_added_ok', "vSzerver <span class=\"online\">%1</span> sikeresen létrehozva.");
define('LANG_vsselect_new_added_generated', "A generált token:");
define('LANG_vsoverview_virtualserver', "Virtuális szerver");
define('LANG_vsoverview_information_head', "Információ");
define('LANG_vsoverview_connection_head', "Kapcsolat");
define('LANG_vsoverview_info_general_head', "Általános beállítások");
define('LANG_vsoverview_info_servername', "Szerver név");
define('LANG_vsoverview_info_host', "Kiszolgáló");
define('LANG_vsoverview_info_state', "Állapot");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "Üzemidő");
define('LANG_vsoverview_info_welcomemsg', "Üdvözlő<br />üzenet");
define('LANG_vsoverview_info_hostmsg', "Kiszolgáló üzenet");
define('LANG_vsoverview_info_hostmsg_mode_output', "kimenet");
define('LANG_vsoverview_info_hostmsg_mode_0', "semmi");
define('LANG_vsoverview_info_hostmsg_mode_1', "a chat naplóban");
define('LANG_vsoverview_info_hostmsg_mode_2', "ablak");
define('LANG_vsoverview_info_hostmsg_mode_3', "Ablak + lekapcsolódás");
define('LANG_vsoverview_info_req_security', "Biztonsági szint");
define('LANG_vsoverview_info_req_securitylvl', "szükséges");
define('LANG_vsoverview_info_hostbanner_head', "Hostbanner");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Kép címe");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Hostbutton URL");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Figyelmeztetés bekapcsolva");
define('LANG_vsoverview_info_antiflood_kick', "Kirúgás");
define('LANG_vsoverview_info_antiflood_ban', "Kitiltás");
define('LANG_vsoverview_info_antiflood_banduration', "Kitiltás hossza");
define('LANG_vsoverview_info_antiflood_decrease', "Csökkenés");
define('LANG_vsoverview_info_antiflood_points', "pontok");
define('LANG_vsoverview_info_antiflood_in_seconds', "másodpercek");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Points per tick");
define('LANG_vsoverview_conn_total_head', "Összes");
define('LANG_vsoverview_conn_total_packets', "csomagok");
define('LANG_vsoverview_conn_total_bytes', "bájt");
define('LANG_vsoverview_conn_total_send', "küldött");
define('LANG_vsoverview_conn_total_received', "fogadott");
define('LANG_vsoverview_conn_bandwidth_head', "Sávszélesség");
define('LANG_vsoverview_conn_bandwidth_last', "utolsó");
define('LANG_vsoverview_conn_bandwidth_second', "másodperc");
define('LANG_vsoverview_conn_bandwidth_minute', "perc");
define('LANG_vsoverview_conn_bandwidth_send', "küldött");
define('LANG_vsoverview_conn_bandwidth_received', "fogadott");
define('LANG_vstoken_token_virtualserver', "Virtuális szerver");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Csoport típus");
define('LANG_vstoken_token_id1', "Szervercsoport/<br />Csatornacsoport");
define('LANG_vstoken_token_id2', "(Csatorna)");
define('LANG_vstoken_token_tokencode', "Token kód");
define('LANG_vstoken_token_delete', "Törlés");
define('LANG_vstoken_new_head', "Egy új token létrehozása");
define('LANG_vstoken_new_create', "Generál");
define('LANG_vstoken_new_tokentype', "Token típus:");
define('LANG_vstoken_new_servergroup', "Szerver csoport");
define('LANG_vstoken_new_channelgroup', "Csatorna csoport");
define('LANG_vstoken_new_select_group', "Szervercsoport");
define('LANG_vstoken_new_select_channelgroup', "Csatorna csoport");
define('LANG_vstoken_new_select_channel', "Csatorna");
define('LANG_vstoken_new_tokentype_0', "Szerver");
define('LANG_vstoken_new_tokentype_1', "Csatorna");
define('LANG_vstoken_new_added_ok', "Token generálása sikeres.");
define('LANG_vsliveview_server_virtualserver', "Virtuális szerver");
define('LANG_vsliveview_server_head', "Élő nézet");
define('LANG_vsliveview_liveview_enable_autorefresh', "Automatikus frissítés");
define('LANG_vsliveview_liveview_tooltip_to_channel', "csatorna #");
define('LANG_vsliveview_liveview_tooltip_switch', "Átvált");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Üzenet küldése");
define('LANG_vsliveview_liveview_tooltip_poke', "Bökés");
define('LANG_vsliveview_liveview_tooltip_kick', "Kirugás");
define('LANG_vsliveview_liveview_tooltip_ban', "Kitiltás");
define('LANG_vsoverview_banlist_head', "Kitiltási lista");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Név");
define('LANG_vsoverview_banlist_uid', "Egyedi azonosító");
define('LANG_vsoverview_banlist_reason', "Ok");
define('LANG_vsoverview_banlist_created', "Létrehozva");
define('LANG_vsoverview_banlist_duration', "Időtartam");
define('LANG_vsoverview_banlist_end', "Véget ér");
define('LANG_vsoverview_banlist_unlimited', "korlátlan");
define('LANG_vsoverview_banlist_never', "soha");
define('LANG_vsoverview_banlist_new_head', "Új kitiltás létrehozása");
define('LANG_vsoverview_banlist_new_create', "Létrehoz");
define('LANG_vsliveview_channelbackup_head', "Csatorna biztonsági mentés");
define('LANG_vsliveview_channelbackup_get', "Létrehozás és letöltés");
define('LANG_vsliveview_channelbackup_load', "Csatorna biztonsági mentésének a feltöltése");
define('LANG_vsliveview_channelbackup_load_submit', "Újraalkotás");
define('LANG_vsliveview_channelbackup_new_added_ok', "Csatorna biztonsági mentése sikeres.");
define('LANG_time_day', "nap");
define('LANG_time_days', "napra");
define('LANG_time_hour', "óra");
define('LANG_time_hours', "órára");
define('LANG_time_minute', "perc");
define('LANG_time_minutes', "percre");
define('LANG_time_second', "másodperc");
define('LANG_time_seconds', "másodpercre");
define('LANG_e_2568', "Nem rendelkezel megfelelő jogokkal.");
define('LANG_temp_folder_not_writable', "A sablonok mappája (%s) nem írható.");
define('LANG_unassign_from_subuser', "Eltávolítás az al-felhasználótól.");
define('LANG_assign_to_subuser', "Hozzárendelés az al-felhasználóhoz.");
define('LANG_select_subuser', "Al-felhasználó kiválasztása.");
define('LANG_no_ts3_servers_assigned_to_account', "Nincsenek szerverek hozzárendelve a fiókodhoz.");
define('LANG_change_virtual_server', "Virtuális szerver módosítása");
define('LANG_change_remote_server', "Távoli szerver módosítása");
?>