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

define('LANG_error', "Pogreška");
define('LANG_title', "Web sučelje TeamSpeak 3");
define('LANG_update_available', "<h3>Pozor: nova verzija (v%1) ovog softvera je dostupan pod <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Odjaviti se");
define('LANG_head_vserver_switch', "Promijeniti vServer");
define('LANG_head_vserver_overview', "vServer Pregled");
define('LANG_head_vserver_token', "Token Upravljanje");
define('LANG_head_vserver_liveview', "Pogled Uživo");
define('LANG_e_fill_out', "Molimo da spunite sva potrebna polja.");
define('LANG_e_upload_failed', "Učitavanje nije uspjelo.");
define('LANG_e_server_responded', "Server je odgovorio:");
define('LANG_e_conn_serverquery', "Nije moguće stvoriti  ServerQuery pristup.");
define('LANG_e_conn_vserver', "Ne može se odabrati virtualni server.");
define('LANG_e_session_timedout', "Vrijeme isteklo.");
define('LANG_js_error', "Pogreška");
define('LANG_js_ajax_error', "Došlo je do AJAX pogreške: %1.");
define('LANG_js_confirm_server_stop', "Želite li stvarno zaustaviti server #%1?");
define('LANG_js_confirm_server_delete', "Želite li stvarno IZBRISATI server #%1?");
define('LANG_js_notice_server_deleted', "Server %1 je uspješno izbrisan. Prikazna stranica sada će biti ponovno učitana.");
define('LANG_js_prompt_banduration', "Trajanje u satima (0=neograničeno):");
define('LANG_js_prompt_banreason', "Razlog (neobavezan):");
define('LANG_js_prompt_msg_to', "Tekstualna poruka na %1 #%2:");
define('LANG_js_prompt_poke_to', "Poke Poruka Klijentu #%1:");
define('LANG_js_prompt_new_propvalue', "Nova vrijednost za '%1':");
define('LANG_n_server_responded', "Server je odgovorio:");
define('LANG_login_serverquery', "ServerQuery Prijava");
define('LANG_login_name', "Korisničko Ime");
define('LANG_login_password', "Lozinka");
define('LANG_login_submit', "Prijava");
define('LANG_vsselect_headline', "vServer izbor");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Ime");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "Status");
define('LANG_vsselect_clients', "Klijenti");
define('LANG_vsselect_uptime', "Vrijeme Rada");
define('LANG_vsselect_choose', "Odabrati");
define('LANG_vsselect_start', "Pokrenuti");
define('LANG_vsselect_stop', "Zaustaviti");
define('LANG_vsselect_delete', "IZBRISATI");
define('LANG_vsselect_new_headline', "Kreirati novi virtualni server");
define('LANG_vsselect_new_servername', "Naziv Servera");
define('LANG_vsselect_new_slots', "Mjesta za klijente");
define('LANG_vsselect_new_create', "Kreirati");
define('LANG_vsselect_new_added_ok', "vServer <span class=\"online\">%1</span> uspješno kreiran.");
define('LANG_vsselect_new_added_generated', "Generirani token je:");
define('LANG_vsoverview_virtualserver', "Virtualni Server");
define('LANG_vsoverview_information_head', "Informacije");
define('LANG_vsoverview_connection_head', "Veza");
define('LANG_vsoverview_info_general_head', "Opće postavke");
define('LANG_vsoverview_info_servername', "Naziv Servera");
define('LANG_vsoverview_info_host', "Verzija OS-a");
define('LANG_vsoverview_info_state', "Status");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "Vrijeme Rada");
define('LANG_vsoverview_info_welcomemsg', "Poruka dobodošlice<br />");
define('LANG_vsoverview_info_hostmsg', "Poruka Servera");
define('LANG_vsoverview_info_hostmsg_mode_output', "pokazivanje");
define('LANG_vsoverview_info_hostmsg_mode_0', "ništa");
define('LANG_vsoverview_info_hostmsg_mode_1', "na čavrljanju");
define('LANG_vsoverview_info_hostmsg_mode_2', "prozor");
define('LANG_vsoverview_info_hostmsg_mode_3', "Prozor + Odjava");
define('LANG_vsoverview_info_req_security', "Sigurnosna Razina");
define('LANG_vsoverview_info_req_securitylvl', "potrebno");
define('LANG_vsoverview_info_hostbanner_head', "Banner Servera");
define('LANG_vsoverview_info_hostbanner_url', "Poveznica");
define('LANG_vsoverview_info_hostbanner_imgurl', "Adresa slike");
define('LANG_vsoverview_info_hostbanner_buttonurl', "Poveznica gumba za server");
define('LANG_vsoverview_info_antiflood_head', "Uznemiravanje");
define('LANG_vsoverview_info_antiflood_warning', "Upozorenje nakon");
define('LANG_vsoverview_info_antiflood_kick', "Izbaciti nakon");
define('LANG_vsoverview_info_antiflood_ban', "Zabraniti nakon");
define('LANG_vsoverview_info_antiflood_banduration', "Trajanje zabrane");
define('LANG_vsoverview_info_antiflood_decrease', "Smanjenje");
define('LANG_vsoverview_info_antiflood_points', "bodova");
define('LANG_vsoverview_info_antiflood_in_seconds', "sekunde");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Bodova po tiku");
define('LANG_vsoverview_conn_total_head', "Totalno");
define('LANG_vsoverview_conn_total_packets', "paketi");
define('LANG_vsoverview_conn_total_bytes', "bajtova");
define('LANG_vsoverview_conn_total_send', "poslano");
define('LANG_vsoverview_conn_total_received', "primljeno");
define('LANG_vsoverview_conn_bandwidth_head', "Internet Promet");
define('LANG_vsoverview_conn_bandwidth_last', "zadnje");
define('LANG_vsoverview_conn_bandwidth_second', "po sekundi");
define('LANG_vsoverview_conn_bandwidth_minute', "po minuti");
define('LANG_vsoverview_conn_bandwidth_send', "poslano");
define('LANG_vsoverview_conn_bandwidth_received', "primljeno");
define('LANG_vstoken_token_virtualserver', "Virtualni Server");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Vrsta Grupe");
define('LANG_vstoken_token_id1', "Grupa Servera/<br />Grupa Kanala");
define('LANG_vstoken_token_id2', "(Kanal)");
define('LANG_vstoken_token_tokencode', "Kod Tokena");
define('LANG_vstoken_token_delete', "Izbrisati");
define('LANG_vstoken_new_head', "Kreirati novi token");
define('LANG_vstoken_new_create', "Proizvesti");
define('LANG_vstoken_new_tokentype', "Vrsta tokena:");
define('LANG_vstoken_new_servergroup', "Grupa Servera");
define('LANG_vstoken_new_channelgroup', "Grupa Kanala");
define('LANG_vstoken_new_select_group', "Server Grupa");
define('LANG_vstoken_new_select_channelgroup', "Kanal Grupa");
define('LANG_vstoken_new_select_channel', "Kanal");
define('LANG_vstoken_new_tokentype_0', "Server");
define('LANG_vstoken_new_tokentype_1', "Kanal");
define('LANG_vstoken_new_added_ok', "Token je uspješno generiran");
define('LANG_vsliveview_server_virtualserver', "Virtualni Server");
define('LANG_vsliveview_server_head', "Pogled Uživo");
define('LANG_vsliveview_liveview_enable_autorefresh', "Automatsko osvježavanje");
define('LANG_vsliveview_liveview_tooltip_to_channel', "na kanal #");
define('LANG_vsliveview_liveview_tooltip_switch', "Prebaciti");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Poslati Poruku");
define('LANG_vsliveview_liveview_tooltip_poke', "Poke");
define('LANG_vsliveview_liveview_tooltip_kick', "Izbaciti");
define('LANG_vsliveview_liveview_tooltip_ban', "Zabraniti");
define('LANG_vsoverview_banlist_head', "Popis zabrane");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Ime");
define('LANG_vsoverview_banlist_uid', "Jedinstveni ID");
define('LANG_vsoverview_banlist_reason', "Razlog");
define('LANG_vsoverview_banlist_created', "Stvoren");
define('LANG_vsoverview_banlist_duration', "Trajanje");
define('LANG_vsoverview_banlist_end', "Završava");
define('LANG_vsoverview_banlist_unlimited', "neograničeno");
define('LANG_vsoverview_banlist_never', "nikada");
define('LANG_vsoverview_banlist_new_head', "Stvoriti novu zabranu");
define('LANG_vsoverview_banlist_new_create', "Stvoriti");
define('LANG_vsliveview_channelbackup_head', "Sigurnosno kopiranje kanala");
define('LANG_vsliveview_channelbackup_get', "Stvaranje i preuzimanje");
define('LANG_vsliveview_channelbackup_load', "Učitati sigurnosnu kopiju kanala");
define('LANG_vsliveview_channelbackup_load_submit', "Ponovo stvoriti");
define('LANG_vsliveview_channelbackup_new_added_ok', "Sigurnosno Kopiranje Kanala uspješno.");
define('LANG_time_day', "dan");
define('LANG_time_days', "dana");
define('LANG_time_hour', "sat");
define('LANG_time_hours', "sati");
define('LANG_time_minute', "minutu");
define('LANG_time_minutes', "minute");
define('LANG_time_second', "sekunda");
define('LANG_time_seconds', "sekunde");
define('LANG_e_2568', "Nemate dovoljno prava.");
define('LANG_temp_folder_not_writable', "Mapa predložaka (%s) nije pristupno pisana.");
define('LANG_unassign_from_subuser', "Ukloniti od podkorisnika");
define('LANG_assign_to_subuser', "Dodijeliti podkorisniku.");
define('LANG_select_subuser', "Odaberite podkorisnika.");
define('LANG_no_ts3_servers_assigned_to_account', "Na vašem računu nije dodjeljen nijedan server.");
define('LANG_change_virtual_server', "Promijeniti virtualni server");
define('LANG_change_remote_server', "Promijeniti Udaljeni Server");
?>