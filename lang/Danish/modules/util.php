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

define('LANG_module_name', "Utilities");
define('LANG_ping', "Ping");
define('LANG_traceroute', "Traceroute");
define('LANG_network_tools', "Netværks redskaber");
define('LANG_sourcemod_admins', "Sourcemod administrator");
define('LANG_steam_converter', "SteamID Converter");
define('LANG_your_ip', "Din IP adresse:");
define('LANG_loading_agents', "Indlæser online Agenter...");
define('LANG_loading_failed', "Indlæsning af Agenter fejlede");
define('LANG_agents_offline', "Alle Agenter er utilgængelige");
define('LANG_no_commands', "Undskyld, din bruger account har ingen kommandoer tilgængelige");
define('LANG_remote_target', "Mål ip-adresse");
define('LANG_command', "Kommando:");
define('LANG_select_agent', "Vælg Agent:");
define('LANG_chdir_failed', "Error: chdir() returned false.");
define('LANG_agent_invalid', "Ugyldig Agent specificeret.");
define('LANG_networktools_agent_offline', "Kan ikke udføre din kommando på den valgte Agent, fordi den er utilgængelig.");
define('LANG_target_empty', "Intet fjernt mål angivet.");
define('LANG_command_empty', "Ingen kommando valgt.");
define('LANG_command_unavilable', "Den valgte kommando er utilgængelig på den valgte Agent");
define('LANG_target_invalid', "Ugyldig IP/hostname indtasted.");
define('LANG_exec_failed', "Sat på pause, mens du venter på et svar. ");
define('LANG_command_no_access', "Du har ikke adgang til denne kommando. Denne handling vil blive skrevet ned.");
define('LANG_command_hacking_attempt', "Banlyste bogstaver indskrevet. Denne handling vil blive indskrevet.");
define('LANG_command_bad_characters', "Forsøger at udføre en kommando med ondsindede tegn. Indtasted: %s%s");
define('LANG_command_no_permissions', "Forsøger at udføre en kommando med utilstrækelige tilladelser. Indtasted: %s%s");
define('LANG_command_executed', "Sendt følgende kommando successivt: %s%s");
define('LANG_no_servers', "Du har ingen server tildelt din account.");
define('LANG_select_server', "Vælg Server:");
define('LANG_select_server_option', "Vælg...");
define('LANG_steamid', "Steam ID:");
define('LANG_immunity', "Immunitet:");
define('LANG_sourcemod_perms', "Sourcemod tilladelser:");
define('LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('LANG_sourcemod_perm_custom', "Sourcemod Custom Flags");
define('LANG_sourcemod_flag_a', "Reserverede pladser adgang.");
define('LANG_sourcemod_flag_b', "Generisk admin; kræves til admins.");
define('LANG_sourcemod_flag_c', "Smid andre spillere ud.");
define('LANG_sourcemod_flag_d', "Udeluk andre spillere.");
define('LANG_sourcemod_flag_e', "Fjerne udelukelse.");
define('LANG_sourcemod_flag_f', "Dræb/skad andre spillere.");
define('LANG_sourcemod_flag_g', "Skift map eller store spille funktioner.");
define('LANG_sourcemod_flag_h', "Ændre de fleste CVARs.");
define('LANG_sourcemod_flag_i', "Udfør config filer.");
define('LANG_sourcemod_flag_j', "Speciale chat tilladelser.");
define('LANG_sourcemod_flag_k', "Start eller lav stemmer.");
define('LANG_sourcemod_flag_l', "Sæt et kodeord på servern.");
define('LANG_sourcemod_flag_m', "Brug RCON kommandoer");
define('LANG_sourcemod_flag_n', "Ændre sv_cheats eller brug snyde kommandoer.");
define('LANG_sourcemod_flag_o', "Brugerdefinerede gruppe 1.");
define('LANG_sourcemod_flag_p', "Brugerdefinerede gruppe 2.");
define('LANG_sourcemod_flag_q', "Brugerdefinerede gruppe 3.");
define('LANG_sourcemod_flag_r', "Brugerdefinerede gruppe 4.");
define('LANG_sourcemod_flag_s', "Brugerdefinerede gruppe 5.");
define('LANG_sourcemod_flag_t', "Brugerdefinerede gruppe 6.");
define('LANG_rcon_reload_admins_failed', "Fejlede genindlæsning af admin cahce via RCON; er den tilgængelig?");
define('LANG_reload_admins_failed', "Fejlede genindlæsningen af admin cache; \"sm_reloadadmin\" er en ukent kommando.");
define('LANG_reload_admins_success', "Tilføj succesfuldt %s til admins_simple.ini og genindlæste admin cache.");
define('LANG_add_success_no_rcon', "Tilføj succesfuldt %s til din admins_simple.ini fil og udeafstand til at  genindlæse admin cache.");
define('LANG_writefile_error', "Der var en ukendt fejl skrevet til: %s ");
define('LANG_remotefile_nonexistent', "Udeafstand til at tilføje en ny admin. Admin fil: %s eksistere ikke på denne server.");
define('LANG_empty_flag_list', "Du valgte ingen admin flag.");
define('LANG_invalid_steam_format', "Det SteamID du indskrev matcher ikke det krevede mønster.");
define('LANG_selected_server_offline', "Udeafstand til at tilføje en admin. Den agent der styre denne valgte server er utilgængelig.");
define('LANG_malformed_form', "Du har indsendt en formular med misdannede skjulte elementer - kan ikke tilføje en administrator.");
define('LANG_empty_form_data', "Venligst udfyld alle elementer i fomen.");
define('LANG_server_not_selected', "Du har ikke valgt en server.");
define('LANG_invalid_steamid', "Du har indtasted en ugyldigt Steam ID.");
define('LANG_invalid_immunity', "Du har indtasted et ugyldigt immunitet værdi.");
define('LANG_submit', "Indsend");
define('LANG_post_failed', "POST handlingen fejlede. Ude af stand til at modtage svar.");
define('LANG_amx_mod_admins', "AMX mod X Admins");
define('LANG_amx_login_type', "Log på type");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Alias + Kodeord");
define('LANG_nickname', "Alias");
define('LANG_amx_mod_perms', "AMX mod X Tilladelser:");
define('LANG_amx_mod_perm_root', "AMX mod X Alle Flag.");
define('LANG_amx_mod_perm_custom', "AMX mod X Brugerdefineret Flag.");
define('LANG_amx_mod_flag_a', "Immunitet ( kan ikke blive smidt ud/udelukket/drabt/slået eller blive påvirket af andre kommandoer)");
define('LANG_amx_mod_flag_b', "reservation (kan deltage i reserverede pladser)");
define('LANG_amx_mod_flag_c', "amx_kick kommando");
define('LANG_amx_mod_flag_d', "amx_ban og amx_unban kommandoer");
define('LANG_amx_mod_flag_e', "amx_slay and amx_slap commands");
define('LANG_amx_mod_flag_f', "amx_map command");
define('LANG_amx_mod_flag_g', "amx_cvar command (not all cvars will be available)");
define('LANG_amx_mod_flag_h', "amx_cfg command");
define('LANG_amx_mod_flag_i', "amx_chat and other chat commands");
define('LANG_amx_mod_flag_j', "amx_vote and other vote commands");
define('LANG_amx_mod_flag_k', "access to sv_password cvar (by amx_cvar command)");
define('LANG_amx_mod_flag_l', "access to amx_rcon command and rcon_password cvar (by amx_cvar command)");
define('LANG_amx_mod_flag_m', "custom level A (for additional plugins)");
define('LANG_amx_mod_flag_n', "custom level B");
define('LANG_amx_mod_flag_o', "custom level C");
define('LANG_amx_mod_flag_p', "custom level D");
define('LANG_amx_mod_flag_q', "custom level E");
define('LANG_amx_mod_flag_r', "custom level F");
define('LANG_amx_mod_flag_s', "custom level G");
define('LANG_amx_mod_flag_t', "custom level H");
define('LANG_amx_mod_flag_u', "menu access");
?>