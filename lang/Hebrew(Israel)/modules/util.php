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

define('LANG_module_name', "כלי עזר");
define('LANG_ping', "פינג");
define('LANG_traceroute', "עקוב מסלול");
define('LANG_network_tools', "כלי רשת");
define('LANG_sourcemod_admins', "מנהלי סורסמוד");
define('LANG_steam_converter', "ממיר סטים איידי");
define('LANG_your_ip', "כתובת האייפי שלך:");
define('LANG_loading_agents', "טוען סוכנים מקוונים...");
define('LANG_loading_failed', "טעינת סוכנים נכשלה.");
define('LANG_agents_offline', "כל הסוכנים אינם מקוונים.");
define('LANG_no_commands', "סליחה, למשתמש החשבון שלך אין פקודות זמינות.");
define('LANG_remote_target', "יעד כתובת אייפי:");
define('LANG_command', "פקודה:");
define('LANG_select_agent', "בחר סוכן:");
define('LANG_chdir_failed', "שגיאה: chdir() החזיר שקר");
define('LANG_agent_invalid', "צוין סוכן שגוי.");
define('LANG_networktools_agent_offline', "לא ניתן לבצע את הפקודה שלך על הסוכן שנבחר, מכיוון שהוא מכובה.");
define('LANG_target_empty', "לא ניתנה מטרה מרוחקת.");
define('LANG_command_empty', "לא נבחרה פקודה.");
define('LANG_command_unavilable', "הפקודה שנבחרה אינה זמינה בסוכן שנבחר.");
define('LANG_target_invalid', "אייפי/שם מארח לא חוקי הוכנס.");
define('LANG_exec_failed', "פסק זמן בזמן שהמתין לתגובה.");
define('LANG_command_no_access', "אין לך גישה לפקודה זו. האירוע הזה יירשם.");
define('LANG_command_hacking_attempt', "הוכנסו תווים ברשימה שחורה. מקרה זה יכתב ביומן.");
define('LANG_command_bad_characters', "ניסה לבצע פקודה עם תווים זדוניים. קלט שהתקבל: %s %s");
define('LANG_command_no_permissions', "ניסה לבצע פקודה ללא הרשאות מספיקות. קלט שהתקבל: %s %s");
define('LANG_command_executed', "הפקודה הבאה נשלחה בהצלחה: %s %s");
define('LANG_no_servers', "אין לך שרתים מוקצים לחשבון שלך.");
define('LANG_select_server', "בחר שרת:");
define('LANG_select_server_option', "בחר...");
define('LANG_steamid', "מזהה סטים:");
define('LANG_immunity', "חסינות:");
define('LANG_sourcemod_perms', "הרשאות הסורסמוד:");
define('LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('LANG_sourcemod_perm_custom', "Sourcemod Custom Flags");
define('LANG_sourcemod_flag_a', "גישה לסלוטים שמורים.");
define('LANG_sourcemod_flag_b', "מנהל כללי; נדרש למנהלים.");
define('LANG_sourcemod_flag_c', "העף קיק שחקנים אחרים.");
define('LANG_sourcemod_flag_d', "הרחק שחקנים אחרים.");
define('LANG_sourcemod_flag_e', "הסר הרחקות - באנים.");
define('LANG_sourcemod_flag_f', "להרוג/להזיק לשחקנים אחרים.");
define('LANG_sourcemod_flag_g', "שנה את המפה או את תכונות המשחק העיקריות.");
define('LANG_sourcemod_flag_h', "שנה את רוב ה CVARS.");
define('LANG_sourcemod_flag_i', "הפעל קבצי קונפיג.");
define('LANG_sourcemod_flag_j', "הרשאות צאט מיוחדות.");
define('LANG_sourcemod_flag_k', "התחל או צור הצבעה.");
define('LANG_sourcemod_flag_l', "קבע סיסמה לשרת.");
define('LANG_sourcemod_flag_m', "השתמש בפקודות רקון.");
define('LANG_sourcemod_flag_n', "שנה sv_cheats או השתמש בפקודות רמאות.");
define('LANG_sourcemod_flag_o', "קבוצה מותאמת אישית 1.");
define('LANG_sourcemod_flag_p', "קבוצה מותאמת אישית 2.");
define('LANG_sourcemod_flag_q', "קבוצה מותאמת אישית 3.");
define('LANG_sourcemod_flag_r', "קבוצה מותאמת אישית 4.");
define('LANG_sourcemod_flag_s', "קבוצה מותאמת אישית 5.");
define('LANG_sourcemod_flag_t', "קבוצה מותאמת אישית 6.");
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
define('LANG_submit', "שלח");
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
define('LANG_amx_mod_flag_e', "פקודות amx_slay ו amx_slap");
define('LANG_amx_mod_flag_f', "פקודת amx_map");
define('LANG_amx_mod_flag_g', "amx_cvar command (not all cvars will be available)");
define('LANG_amx_mod_flag_h', "פקודת amx_cfg");
define('LANG_amx_mod_flag_i', "amx_chat ופקודות צאט אחרות");
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
define('LANG_amx_mod_flag_u', "גישת תפריט");
?>