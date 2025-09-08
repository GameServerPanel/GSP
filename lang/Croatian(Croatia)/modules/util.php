<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

define('OGP_LANG_module_name', "Brze Usluge za Igrice");
define('OGP_LANG_ping', "Ping");
define('OGP_LANG_traceroute', "Trag Rute");
define('OGP_LANG_network_tools', "Mrežni Alati");
define('OGP_LANG_sourcemod_admins', "Sourcemod Admini");
define('OGP_LANG_steam_converter', "SteamID Konverter");
define('OGP_LANG_your_ip', "Vaša IP Adresa");
define('OGP_LANG_loading_agents', "Učitavanje Agenata koji su na mreži...");
define('OGP_LANG_loading_failed', "Učitavanje Agenata nije uspjelo.");
define('OGP_LANG_agents_offline', "Svi Agenti su izvan mreže.");
define('OGP_LANG_no_commands', "Žao nam je, vaš korisnički račun nema dostupne naredbe.");
define('OGP_LANG_remote_target', "Ciljane IP Adrese:");
define('OGP_LANG_command', "Naredba:");
define('OGP_LANG_select_agent', "Odaberite Agent");
define('OGP_LANG_chdir_failed', "Pogreška: chdir () je vraćen neispravno.");
define('OGP_LANG_agent_invalid', "Navedeno je nevažeći Agent.");
define('OGP_LANG_networktools_agent_offline', "Nije moguće izvršiti naredbu na odabranom Agentu jer je izvan mreže.");
define('OGP_LANG_target_empty', "Nije dodjeljen udaljeni cilj.");
define('OGP_LANG_command_empty', "Nije odabrana nijedna naredba.");
define('OGP_LANG_command_unavilable', "Odabrana naredba nije dostupna na odabranom Agentu.");
define('OGP_LANG_target_invalid', "Uneseni su nevažeći IP/Naziv poslužitelja.");
define('OGP_LANG_exec_failed', "Isteklo vrijeme dok ste čekali za odgovor.");
define('OGP_LANG_command_no_access', "Nemate pristup toj naredbi. Ovaj događaj će biti zapisan u zapisniku.");
define('OGP_LANG_command_hacking_attempt', "Uneseni su znakovi koji se nalaze na crnoj listi. Ovaj događaj će biti zapisan u zapisniku.");
define('OGP_LANG_command_bad_characters', "Pokušalo se izvršiti naredba zlonamjernim znakovima. Primljeni unos: %s %s");
define('OGP_LANG_command_no_permissions', "Pokušalo se izvršiti naredba s nedovoljnim dozvolama. Primljeni unos: %s %s");
define('OGP_LANG_command_executed', "Uspješno je poslana sljedeća naredba: %s %s");
define('OGP_LANG_no_servers', "Na vašem računu nije dodjeljen nijedan server.");
define('OGP_LANG_select_server', "Odaberite Server:");
define('OGP_LANG_select_server_option', "Odabrati...");
define('OGP_LANG_steamid', "Steam ID:");
define('OGP_LANG_immunity', "Imunitet:");
define('OGP_LANG_sourcemod_perms', "Sourcemod Privilegije:");
define('OGP_LANG_sourcemod_perm_root', "Sourcemod Root Privilegije");
define('OGP_LANG_sourcemod_perm_custom', "Sourcemod Određene Privilegije");
define('OGP_LANG_sourcemod_flag_a', "A - Reserved slot access.");
define('OGP_LANG_sourcemod_flag_b', "B - Generic admin; required for admins.");
define('OGP_LANG_sourcemod_flag_c', "C - Kick other players.");
define('OGP_LANG_sourcemod_flag_d', "D - Ban other players.");
define('OGP_LANG_sourcemod_flag_e', "E - Remove bans.");
define('OGP_LANG_sourcemod_flag_f', "F - Slay/harm other players.");
define('OGP_LANG_sourcemod_flag_g', "G - Change the map or major gameplay features.");
define('OGP_LANG_sourcemod_flag_h', "H - Change most CVARs.");
define('OGP_LANG_sourcemod_flag_i', "I - Execute config files.");
define('OGP_LANG_sourcemod_flag_j', "J - Special chat privileges.");
define('OGP_LANG_sourcemod_flag_k', "K - Start or create votes.");
define('OGP_LANG_sourcemod_flag_l', "L - Set a password on the server.");
define('OGP_LANG_sourcemod_flag_m', "M - Use RCON commands.");
define('OGP_LANG_sourcemod_flag_n', "N - Change sv_cheats or use cheating commands.");
define('OGP_LANG_sourcemod_flag_o', "O - Custom Group 1.");
define('OGP_LANG_sourcemod_flag_p', "P - Custom Group 2.");
define('OGP_LANG_sourcemod_flag_q', "Q - Custom Group 3.");
define('OGP_LANG_sourcemod_flag_r', "R - Custom Group 4.");
define('OGP_LANG_sourcemod_flag_s', "S - Custom Group 5.");
define('OGP_LANG_sourcemod_flag_t', "T - Custom Group 6.");
define('OGP_LANG_rcon_reload_admins_failed', "Nije uspjelo ponovno učitavanje administratorske predmemorije putem RCON-a; je li on online?");
define('OGP_LANG_reload_admins_failed', "Nije uspjelo ponovno učitavanje administratorske predmemorije; \"sm_reloadadmins\" je nepoznata naredba.");
define('OGP_LANG_reload_admins_success', "Uspješno ste dodali %s na admins_simple.ini i ponovno učitao administratorsku predmemoriju.");
define('OGP_LANG_add_success_no_rcon', "Uspješno ste dodali %s u datoteku admins_simple.ini, ali ne možete ponovo učitati administratorsku predmemoriju.");
define('OGP_LANG_writefile_error', "Došlo je do nepoznate pogreške u pisanju na: %s");
define('OGP_LANG_remotefile_nonexistent', "Nije moguće dodati novog Admina. Administratorska datoteka: %s ne postoji na ovom serveru.");
define('OGP_LANG_empty_flag_list', "Niste odabrali nijednu Vip/Admin privilegiju.");
define('OGP_LANG_invalid_steam_format', "SteamID koji ste unijeli ne odgovara potrebnom uzorku.");
define('OGP_LANG_selected_server_offline', "Nije moguće dodati Admina, Agent koji kontrolira odabrani server je izvan mreže.");
define('OGP_LANG_malformed_form', "Poslali ste obrazac s nepravilnim skrivenim elementima - ne možete dodati Admina.");
define('OGP_LANG_empty_form_data', "Ispunite sve elemente obrasca.");
define('OGP_LANG_server_not_selected', "Niste odabrali Server.");
define('OGP_LANG_invalid_steamid', "Unijeli ste nevažeći Steam ID.");
define('OGP_LANG_invalid_immunity', "Unijeli ste nevažeću vrijednost imuniteta.");
define('OGP_LANG_submit', "Potvrditi");
define('OGP_LANG_post_failed', "POST akcija nije uspjela. Nije moguće dobiti odgovor.");
define('OGP_LANG_amx_mod_admins', "AMX mod X Admini");
define('OGP_LANG_amx_login_type', "Način Prijave");
define('OGP_LANG_amx_login_steamid', "Steam ID");
define('OGP_LANG_amx_login_nick_pass', "Nadimak + Lozinka");
define('OGP_LANG_nickname', "Nadimak");
define('OGP_LANG_amx_mod_perms', "AMX mod X Dozvole:");
define('OGP_LANG_amx_mod_perm_root', "AMX mod X Sve Privilegije");
define('OGP_LANG_amx_mod_perm_custom', "AMX mod X Određene Privilegije");
define('OGP_LANG_amx_mod_flag_a', "A - immunity (can't be kicked/baned/slayed/slaped and affected by other commmands)");
define('OGP_LANG_amx_mod_flag_b', "B - reservation (can join on reserved slots)");
define('OGP_LANG_amx_mod_flag_c', "C - amx_kick command");
define('OGP_LANG_amx_mod_flag_d', "D - amx_ban and amx_unban commands");
define('OGP_LANG_amx_mod_flag_e', "E - amx_slay and amx_slap commands");
define('OGP_LANG_amx_mod_flag_f', "F - amx_map command");
define('OGP_LANG_amx_mod_flag_g', "G - amx_cvar command (not all cvars will be available)");
define('OGP_LANG_amx_mod_flag_h', "H - amx_cfg command");
define('OGP_LANG_amx_mod_flag_i', "I - amx_chat and other chat commands");
define('OGP_LANG_amx_mod_flag_j', "J - amx_vote and other vote commands");
define('OGP_LANG_amx_mod_flag_k', "K - access to sv_password cvar (by amx_cvar command)");
define('OGP_LANG_amx_mod_flag_l', "L - access to amx_rcon command and rcon_password cvar (by amx_cvar command)");
define('OGP_LANG_amx_mod_flag_m', "M - custom level A (for additional plugins)");
define('OGP_LANG_amx_mod_flag_n', "N - custom level B");
define('OGP_LANG_amx_mod_flag_o', "O - custom level C");
define('OGP_LANG_amx_mod_flag_p', "P - custom level D");
define('OGP_LANG_amx_mod_flag_q', "Q - custom level E");
define('OGP_LANG_amx_mod_flag_r', "R - custom level F");
define('OGP_LANG_amx_mod_flag_s', "S - custom level G");
define('OGP_LANG_amx_mod_flag_t', "T - custom level H");
define('OGP_LANG_amx_mod_flag_u', "U - menu access");
?>