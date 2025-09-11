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

define('OGP_LANG_module_name', "Dienstprogramme");
define('OGP_LANG_ping', "Ping");
define('OGP_LANG_traceroute', "Traceroute");
define('OGP_LANG_network_tools', "Netzwerk-Tools");
define('OGP_LANG_sourcemod_admins', "Sourcemod Admins ");
define('OGP_LANG_steam_converter', "SteamID Konverter");
define('OGP_LANG_your_ip', "Ihre IP-Adresse:");
define('OGP_LANG_loading_agents', "Lade online Agenten...");
define('OGP_LANG_loading_failed', "Laden der Agenten fehlgeschlagen.");
define('OGP_LANG_agents_offline', "Alle Agenten sind offline.");
define('OGP_LANG_no_commands', "Es tut uns leid, Ihr Benutzerkonto hat keine Befehle verfügbar.");
define('OGP_LANG_remote_target', "Ziel IP Adresse:");
define('OGP_LANG_command', "Befehl:");
define('OGP_LANG_select_agent', "Agent auswählen:");
define('OGP_LANG_chdir_failed', "Error: chdir() returned false.");
define('OGP_LANG_agent_invalid', "Invalid Agent specified.");
define('OGP_LANG_networktools_agent_offline', "Unable to execute your command on the selected Agent, because it is offline.");
define('OGP_LANG_target_empty', "No remote target given.");
define('OGP_LANG_command_empty', "Kein Befehl ausgewählt.");
define('OGP_LANG_command_unavilable', "The selected command is unavailable on the selected agent.");
define('OGP_LANG_target_invalid', "Ungültiger IP/Hostname eingegeben.");
define('OGP_LANG_exec_failed', "Zeitüberschreitung beim Warten auf eine Antwort.");
define('OGP_LANG_command_no_access', "Du hast keinen Zugriff auf diesen Befehl. Dieser Vorfall wird protokolliert.");
define('OGP_LANG_command_hacking_attempt', "Blacklisted characters entered. This incident will be logged.");
define('OGP_LANG_command_bad_characters', "Attempted to execute a command with malicious characters. Input received: %s %s");
define('OGP_LANG_command_no_permissions', "Attempted to execute a command with insufficient permissions. Input received: %s %s");
define('OGP_LANG_command_executed', "Der folgende Befehl wurde erfolgreich gesendet: %s %s");
define('OGP_LANG_no_servers', "You have no servers assigned to your account.");
define('OGP_LANG_select_server', "Server auswählen:");
define('OGP_LANG_select_server_option', "Auswählen...");
define('OGP_LANG_steamid', "Steam ID:");
define('OGP_LANG_immunity', "Immunität:");
define('OGP_LANG_sourcemod_perms', "Sourcemod Rechte:");
define('OGP_LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('OGP_LANG_sourcemod_perm_custom', "Sourcemod Benutzerdefinierte Flags");
define('OGP_LANG_sourcemod_flag_a', "Reservierter Slot Zugriffe.");
define('OGP_LANG_sourcemod_flag_b', "Generic admin; required for admins.");
define('OGP_LANG_sourcemod_flag_c', "Kick andere Spieler.");
define('OGP_LANG_sourcemod_flag_d', "Bann andere Spieler.");
define('OGP_LANG_sourcemod_flag_e', "Remove bans.");
define('OGP_LANG_sourcemod_flag_f', "Slay/harm other players.");
define('OGP_LANG_sourcemod_flag_g', "Change the map or major gameplay features.");
define('OGP_LANG_sourcemod_flag_h', "Change most CVARs.");
define('OGP_LANG_sourcemod_flag_i', "Execute config files.");
define('OGP_LANG_sourcemod_flag_j', "Special chat privileges.");
define('OGP_LANG_sourcemod_flag_k', "Start or create votes.");
define('OGP_LANG_sourcemod_flag_l', "Set a password on the server.");
define('OGP_LANG_sourcemod_flag_m', "Use RCON commands.");
define('OGP_LANG_sourcemod_flag_n', "Change sv_cheats or use cheating commands.");
define('OGP_LANG_sourcemod_flag_o', "Custom Group 1.");
define('OGP_LANG_sourcemod_flag_p', "Custom Group 2.");
define('OGP_LANG_sourcemod_flag_q', "Custom Group 3.");
define('OGP_LANG_sourcemod_flag_r', "Custom Group 4.");
define('OGP_LANG_sourcemod_flag_s', "Custom Group 5.");
define('OGP_LANG_sourcemod_flag_t', "Custom Group 6.");
define('OGP_LANG_rcon_reload_admins_failed', "Failed to reload the admin cache via RCON; is it online?");
define('OGP_LANG_reload_admins_failed', "Failed to reload the admin cache; \"sm_reloadadmins\" is an unknown command.");
define('OGP_LANG_reload_admins_success', "Successfully added %s to admins_simple.ini and reloaded the admin cache.");
define('OGP_LANG_add_success_no_rcon', "Successfully added %s to your admins_simple.ini file, but unable to reload the admin cache.");
define('OGP_LANG_writefile_error', "There was an unknown error writing to: %s");
define('OGP_LANG_remotefile_nonexistent', "Unable to add a new admin. Admin file: %s doesn\'t exist on this server.");
define('OGP_LANG_empty_flag_list', "You didn\'t select any admin flags.");
define('OGP_LANG_invalid_steam_format', "The SteamID you entered doesn\'t match the required pattern.");
define('OGP_LANG_selected_server_offline', "Unable to add an admin, the agent controlling the selected server is offline.");
define('OGP_LANG_malformed_form', "You submitted a form with malformed hidden elements - unable to add an admin.");
define('OGP_LANG_empty_form_data', "Please fill out all elements of the form.");
define('OGP_LANG_server_not_selected', "You haven\'t selected a server.");
define('OGP_LANG_invalid_steamid', "You have entered an invalid Steam ID.");
define('OGP_LANG_invalid_immunity', "You entered an invalid immunity value.");
define('OGP_LANG_submit', "Senden");
define('OGP_LANG_post_failed', "The POST action failed. Unable to retrieve a response.");
define('OGP_LANG_amx_mod_admins', "AMX mod X Admins");
define('OGP_LANG_amx_login_type', "Login Type");
define('OGP_LANG_amx_login_steamid', "Steam ID");
define('OGP_LANG_amx_login_nick_pass', "Nickname + Password");
define('OGP_LANG_nickname', "Nickname");
define('OGP_LANG_amx_mod_perms', "AMX mod X Permissions:");
define('OGP_LANG_amx_mod_perm_root', "AMX mod X All Flags.");
define('OGP_LANG_amx_mod_perm_custom', "AMX mod X Custom Flags.");
define('OGP_LANG_amx_mod_flag_a', "immunity (can't be kicked/baned/slayed/slaped and affected by other commmands)");
define('OGP_LANG_amx_mod_flag_b', "reservation (can join on reserved slots)");
define('OGP_LANG_amx_mod_flag_c', "amx_kick command");
define('OGP_LANG_amx_mod_flag_d', "amx_ban and amx_unban commands");
define('OGP_LANG_amx_mod_flag_e', "amx_slay and amx_slap commands");
define('OGP_LANG_amx_mod_flag_f', "amx_map command");
define('OGP_LANG_amx_mod_flag_g', "amx_cvar command (not all cvars will be available)");
define('OGP_LANG_amx_mod_flag_h', "amx_cfg command");
define('OGP_LANG_amx_mod_flag_i', "amx_chat and other chat commands");
define('OGP_LANG_amx_mod_flag_j', "amx_vote and other vote commands");
define('OGP_LANG_amx_mod_flag_k', "access to sv_password cvar (by amx_cvar command)");
define('OGP_LANG_amx_mod_flag_l', "access to amx_rcon command and rcon_password cvar (by amx_cvar command)");
define('OGP_LANG_amx_mod_flag_m', "custom level A (for additional plugins)");
define('OGP_LANG_amx_mod_flag_n', "custom level B");
define('OGP_LANG_amx_mod_flag_o', "custom level C");
define('OGP_LANG_amx_mod_flag_p', "custom level D");
define('OGP_LANG_amx_mod_flag_q', "custom level E");
define('OGP_LANG_amx_mod_flag_r', "custom level F");
define('OGP_LANG_amx_mod_flag_s', "custom level G");
define('OGP_LANG_amx_mod_flag_t', "custom level H");
define('OGP_LANG_amx_mod_flag_u', "menu access");
?>