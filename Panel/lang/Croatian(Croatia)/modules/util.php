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

define('LANG_module_name', "Brze Usluge za Igrice");
define('LANG_ping', "Ping");
define('LANG_traceroute', "Trag Rute");
define('LANG_network_tools', "Mrežni Alati");
define('LANG_sourcemod_admins', "Sourcemod Admini");
define('LANG_steam_converter', "SteamID Konverter");
define('LANG_your_ip', "Vaša IP Adresa");
define('LANG_loading_agents', "Učitavanje Agenata koji su na mreži...");
define('LANG_loading_failed', "Učitavanje Agenata nije uspjelo.");
define('LANG_agents_offline', "Svi Agenti su izvan mreže.");
define('LANG_no_commands', "Žao nam je, vaš korisnički račun nema dostupne naredbe.");
define('LANG_remote_target', "Ciljane IP Adrese:");
define('LANG_command', "Naredba:");
define('LANG_select_agent', "Odaberite Agent");
define('LANG_chdir_failed', "Pogreška: chdir () je vraćen neispravno.");
define('LANG_agent_invalid', "Navedeno je nevažeći Agent.");
define('LANG_networktools_agent_offline', "Nije moguće izvršiti naredbu na odabranom Agentu jer je izvan mreže.");
define('LANG_target_empty', "Nije dodjeljen udaljeni cilj.");
define('LANG_command_empty', "Nije odabrana nijedna naredba.");
define('LANG_command_unavilable', "Odabrana naredba nije dostupna na odabranom Agentu.");
define('LANG_target_invalid', "Uneseni su nevažeći IP/Naziv poslužitelja.");
define('LANG_exec_failed', "Isteklo vrijeme dok ste čekali za odgovor.");
define('LANG_command_no_access', "Nemate pristup toj naredbi. Ovaj događaj će biti zapisan u zapisniku.");
define('LANG_command_hacking_attempt', "Uneseni su znakovi koji se nalaze na crnoj listi. Ovaj događaj će biti zapisan u zapisniku.");
define('LANG_command_bad_characters', "Pokušalo se izvršiti naredba zlonamjernim znakovima. Primljeni unos: %s %s");
define('LANG_command_no_permissions', "Pokušalo se izvršiti naredba s nedovoljnim dozvolama. Primljeni unos: %s %s");
define('LANG_command_executed', "Uspješno je poslana sljedeća naredba: %s %s");
define('LANG_no_servers', "Na vašem računu nije dodjeljen nijedan server.");
define('LANG_select_server', "Odaberite Server:");
define('LANG_select_server_option', "Odabrati...");
define('LANG_steamid', "Steam ID:");
define('LANG_immunity', "Imunitet:");
define('LANG_sourcemod_perms', "Sourcemod Privilegije:");
define('LANG_sourcemod_perm_root', "Sourcemod Root Privilegije");
define('LANG_sourcemod_perm_custom', "Sourcemod Određene Privilegije");
define('LANG_sourcemod_flag_a', "A - Reserved slot access.");
define('LANG_sourcemod_flag_b', "B - Generic admin; required for admins.");
define('LANG_sourcemod_flag_c', "C - Kick other players.");
define('LANG_sourcemod_flag_d', "D - Ban other players.");
define('LANG_sourcemod_flag_e', "E - Remove bans.");
define('LANG_sourcemod_flag_f', "F - Slay/harm other players.");
define('LANG_sourcemod_flag_g', "G - Change the map or major gameplay features.");
define('LANG_sourcemod_flag_h', "H - Change most CVARs.");
define('LANG_sourcemod_flag_i', "I - Execute config files.");
define('LANG_sourcemod_flag_j', "J - Special chat privileges.");
define('LANG_sourcemod_flag_k', "K - Start or create votes.");
define('LANG_sourcemod_flag_l', "L - Set a password on the server.");
define('LANG_sourcemod_flag_m', "M - Use RCON commands.");
define('LANG_sourcemod_flag_n', "N - Change sv_cheats or use cheating commands.");
define('LANG_sourcemod_flag_o', "O - Custom Group 1.");
define('LANG_sourcemod_flag_p', "P - Custom Group 2.");
define('LANG_sourcemod_flag_q', "Q - Custom Group 3.");
define('LANG_sourcemod_flag_r', "R - Custom Group 4.");
define('LANG_sourcemod_flag_s', "S - Custom Group 5.");
define('LANG_sourcemod_flag_t', "T - Custom Group 6.");
define('LANG_rcon_reload_admins_failed', "Nije uspjelo ponovno učitavanje administratorske predmemorije putem RCON-a; je li on online?");
define('LANG_reload_admins_failed', "Nije uspjelo ponovno učitavanje administratorske predmemorije; \"sm_reloadadmins\" je nepoznata naredba.");
define('LANG_reload_admins_success', "Uspješno ste dodali %s na admins_simple.ini i ponovno učitao administratorsku predmemoriju.");
define('LANG_add_success_no_rcon', "Uspješno ste dodali %s u datoteku admins_simple.ini, ali ne možete ponovo učitati administratorsku predmemoriju.");
define('LANG_writefile_error', "Došlo je do nepoznate pogreške u pisanju na: %s");
define('LANG_remotefile_nonexistent', "Nije moguće dodati novog Admina. Administratorska datoteka: %s ne postoji na ovom serveru.");
define('LANG_empty_flag_list', "Niste odabrali nijednu Vip/Admin privilegiju.");
define('LANG_invalid_steam_format', "SteamID koji ste unijeli ne odgovara potrebnom uzorku.");
define('LANG_selected_server_offline', "Nije moguće dodati Admina, Agent koji kontrolira odabrani server je izvan mreže.");
define('LANG_malformed_form', "Poslali ste obrazac s nepravilnim skrivenim elementima - ne možete dodati Admina.");
define('LANG_empty_form_data', "Ispunite sve elemente obrasca.");
define('LANG_server_not_selected', "Niste odabrali Server.");
define('LANG_invalid_steamid', "Unijeli ste nevažeći Steam ID.");
define('LANG_invalid_immunity', "Unijeli ste nevažeću vrijednost imuniteta.");
define('LANG_submit', "Potvrditi");
define('LANG_post_failed', "POST akcija nije uspjela. Nije moguće dobiti odgovor.");
define('LANG_amx_mod_admins', "AMX mod X Admini");
define('LANG_amx_login_type', "Način Prijave");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Nadimak + Lozinka");
define('LANG_nickname', "Nadimak");
define('LANG_amx_mod_perms', "AMX mod X Dozvole:");
define('LANG_amx_mod_perm_root', "AMX mod X Sve Privilegije");
define('LANG_amx_mod_perm_custom', "AMX mod X Određene Privilegije");
define('LANG_amx_mod_flag_a', "A - immunity (can't be kicked/baned/slayed/slaped and affected by other commmands)");
define('LANG_amx_mod_flag_b', "B - reservation (can join on reserved slots)");
define('LANG_amx_mod_flag_c', "C - amx_kick command");
define('LANG_amx_mod_flag_d', "D - amx_ban and amx_unban commands");
define('LANG_amx_mod_flag_e', "E - amx_slay and amx_slap commands");
define('LANG_amx_mod_flag_f', "F - amx_map command");
define('LANG_amx_mod_flag_g', "G - amx_cvar command (not all cvars will be available)");
define('LANG_amx_mod_flag_h', "H - amx_cfg command");
define('LANG_amx_mod_flag_i', "I - amx_chat and other chat commands");
define('LANG_amx_mod_flag_j', "J - amx_vote and other vote commands");
define('LANG_amx_mod_flag_k', "K - access to sv_password cvar (by amx_cvar command)");
define('LANG_amx_mod_flag_l', "L - access to amx_rcon command and rcon_password cvar (by amx_cvar command)");
define('LANG_amx_mod_flag_m', "M - custom level A (for additional plugins)");
define('LANG_amx_mod_flag_n', "N - custom level B");
define('LANG_amx_mod_flag_o', "O - custom level C");
define('LANG_amx_mod_flag_p', "P - custom level D");
define('LANG_amx_mod_flag_q', "Q - custom level E");
define('LANG_amx_mod_flag_r', "R - custom level F");
define('LANG_amx_mod_flag_s', "S - custom level G");
define('LANG_amx_mod_flag_t', "T - custom level H");
define('LANG_amx_mod_flag_u', "U - menu access");
?>