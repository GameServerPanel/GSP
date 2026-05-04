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
define('LANG_network_tools', "Network Tools");
define('LANG_sourcemod_admins', "Sourcemod Admins");
define('LANG_steam_converter', "SteamID Converter");
define('LANG_your_ip', "Your IP Address:");
define('LANG_loading_agents', "Loading online Agents...");
define('LANG_loading_failed', "Loading Agents failed.");
define('LANG_agents_offline', "All Agents are offline.");
define('LANG_no_commands', "Sorry, your user account has no commands available.");
define('LANG_remote_target', "Target IP Address:");
define('LANG_command', "Command:");
define('LANG_select_agent', "Select Agent:");
define('LANG_chdir_failed', "Error: chdir() returned false.");
define('LANG_agent_invalid', "Invalid Agent specified.");
define('LANG_networktools_agent_offline', "Unable to execute your command on the selected Agent, because it is offline.");
define('LANG_target_empty', "No remote target given.");
define('LANG_command_empty', "No command selected.");
define('LANG_command_unavilable', "The selected command is unavailable on the selected agent.");
define('LANG_target_invalid', "Invalid IP/hostname entered.");
define('LANG_exec_failed', "Timed out while waiting for a response.");
define('LANG_command_no_access', "You do not have access to this command. This incident will be logged.");
define('LANG_command_hacking_attempt', "Blacklisted characters entered. This incident will be logged.");
define('LANG_command_bad_characters', "Attempted to execute a command with malicious characters. Input received: %s %s");
define('LANG_command_no_permissions', "Attempted to execute a command with insufficient permissions. Input received: %s %s");
define('LANG_command_executed', "Successfully sent the following command: %s %s");
define('LANG_no_servers', "You have no servers assigned to your account.");
define('LANG_select_server', "Select Server:");
define('LANG_select_server_option', "Select...");
define('LANG_steamid', "Steam ID:");
define('LANG_immunity', "Immunity:");
define('LANG_sourcemod_perms', "Sourcemod Permissions:");
define('LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('LANG_sourcemod_perm_custom', "Sourcemod Custom Flags");
define('LANG_sourcemod_flag_a', "Reserved slot access.");
define('LANG_sourcemod_flag_b', "Generic admin; required for admins.");
define('LANG_sourcemod_flag_c', "Kick other players.");
define('LANG_sourcemod_flag_d', "Ban other players.");
define('LANG_sourcemod_flag_e', "Remove bans.");
define('LANG_sourcemod_flag_f', "Slay/harm other players.");
define('LANG_sourcemod_flag_g', "Change the map or major gameplay features.");
define('LANG_sourcemod_flag_h', "Change most CVARs.");
define('LANG_sourcemod_flag_i', "Execute config files.");
define('LANG_sourcemod_flag_j', "Special chat privileges.");
define('LANG_sourcemod_flag_k', "Start or create votes.");
define('LANG_sourcemod_flag_l', "Set a password on the server.");
define('LANG_sourcemod_flag_m', "Use RCON commands.");
define('LANG_sourcemod_flag_n', "Change sv_cheats or use cheating commands.");
define('LANG_sourcemod_flag_o', "Custom Group 1.");
define('LANG_sourcemod_flag_p', "Custom Group 2.");
define('LANG_sourcemod_flag_q', "Custom Group 3.");
define('LANG_sourcemod_flag_r', "Custom Group 4.");
define('LANG_sourcemod_flag_s', "Custom Group 5.");
define('LANG_sourcemod_flag_t', "Custom Group 6.");
define('LANG_rcon_reload_admins_failed', "Failed to reload the admin cache via RCON; is it online?");
define('LANG_reload_admins_failed', "Failed to reload the admin cache; \"sm_reloadadmins\" is an unknown command.");
define('LANG_reload_admins_success', "Successfully added %s to admins_simple.ini and reloaded the admin cache.");
define('LANG_add_success_no_rcon', "Successfully added %s to your admins_simple.ini file, but unable to reload the admin cache.");
define('LANG_writefile_error', "There was an unknown error writing to: %s");
define('LANG_remotefile_nonexistent', "Unable to add a new admin. Admin file: %s doesn\'t exist on this server.");
define('LANG_empty_flag_list', "You didn\'t select any admin flags.");
define('LANG_invalid_steam_format', "The SteamID you entered doesn\'t match the required pattern.");
define('LANG_selected_server_offline', "Unable to add an admin, the agent controlling the selected server is offline.");
define('LANG_malformed_form', "You submitted a form with malformed hidden elements - unable to add an admin.");
define('LANG_empty_form_data', "Please fill out all elements of the form.");
define('LANG_server_not_selected', "You haven\'t selected a server.");
define('LANG_invalid_steamid', "You have entered an invalid Steam ID.");
define('LANG_invalid_immunity', "You entered an invalid immunity value.");
define('LANG_submit', "Submit");
define('LANG_post_failed', "The POST action failed. Unable to retrieve a response.");
define('LANG_amx_mod_admins', "AMX mod X Admins");
define('LANG_amx_login_type', "Login Type");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Nickname + Password");
define('LANG_nickname', "Nickname");
define('LANG_amx_mod_perms', "AMX mod X Permissions:");
define('LANG_amx_mod_perm_root', "AMX mod X All Flags.");
define('LANG_amx_mod_perm_custom', "AMX mod X Custom Flags.");
define('LANG_amx_mod_flag_a', "immunity (can't be kicked/baned/slayed/slaped and affected by other commmands)");
define('LANG_amx_mod_flag_b', "reservation (can join on reserved slots)");
define('LANG_amx_mod_flag_c', "amx_kick command");
define('LANG_amx_mod_flag_d', "amx_ban and amx_unban commands");
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