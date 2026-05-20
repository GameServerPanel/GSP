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

define('LANG_module_name', "Segédprogramok");
define('LANG_ping', "Ping");
define('LANG_traceroute', "Traceroute");
define('LANG_network_tools', "Hálózati eszközök");
define('LANG_sourcemod_admins', "Sourcemod adminisztrátorok");
define('LANG_steam_converter', "SteamID konvertáló");
define('LANG_your_ip', "Az IP címed:");
define('LANG_loading_agents', "Online Agentek betöltése...");
define('LANG_loading_failed', "Az Agentek betöltése sikertelen.");
define('LANG_agents_offline', "Az összes Agent offline.");
define('LANG_no_commands', "Sajnálom, de a felhasználói fiókodnak nincsennek elérhető parancsai.");
define('LANG_remote_target', "Cél IP-címe:");
define('LANG_command', "Parancs:");
define('LANG_select_agent', "Válassz Agentet:");
define('LANG_chdir_failed', "Hiba: a chdir() visszatérése hamis.");
define('LANG_agent_invalid', "Érvénytelen Agent megadva.");
define('LANG_networktools_agent_offline', "Nem sikerült végrehajtani a parancsot a kiválasztott Agenten, mert az offline.");
define('LANG_target_empty', "Nincs távoli célpont megadva.");
define('LANG_command_empty', "Nincs parancs kiválasztva.");
define('LANG_command_unavilable', "A kiválasztott parancs nem érhető el a kiválasztott Agenten.");
define('LANG_target_invalid', "Érvénytelen a megadott IP/állomásnév.");
define('LANG_exec_failed', "Időtúllépés a válaszra várás alatt.");
define('LANG_command_no_access', "Nincs hozzáférésed ehhez a parancshoz. Ez az eset jelentésre kerül.");
define('LANG_command_hacking_attempt', "Feketelistás karakterek vannak megadva. Ez az eset naplózásra kerül.");
define('LANG_command_bad_characters', "Attempted to execute a command with malicious characters. Input received: %s %s");
define('LANG_command_no_permissions', "Megpróbált végrehajtani egy parancsot megfelelő jogosultság nélkül. Kapott bemenet: %s %s");
define('LANG_command_executed', "Az alábbi parancs sikeresen elküldve: %s %s");
define('LANG_no_servers', "Nincsenek szerverek hozzárendelve a fiókodhoz.");
define('LANG_select_server', "Válassz szervert:");
define('LANG_select_server_option', "Válassz...");
define('LANG_steamid', "Steam azonosító:");
define('LANG_immunity', "Immunitás:");
define('LANG_sourcemod_perms', "Sourcemod engedélyek");
define('LANG_sourcemod_perm_root', "Sourcemod Root zászló");
define('LANG_sourcemod_perm_custom', "Sourcemod egyéni zászlók");
define('LANG_sourcemod_flag_a', "Fenntartott slot hozzáférés.");
define('LANG_sourcemod_flag_b', "Általános adminisztrátor; a rendszergazdák számára szükséges.");
define('LANG_sourcemod_flag_c', "Más játékos kirúgása.");
define('LANG_sourcemod_flag_d', "Más játékos kitiltása.");
define('LANG_sourcemod_flag_e', "Kitiltások eltávolítása.");
define('LANG_sourcemod_flag_f', "Megölni/ártani más játékosnak.");
define('LANG_sourcemod_flag_g', "Pályaválasztás vagy egyéb játék beállítasok módosítása.");
define('LANG_sourcemod_flag_h', "CVAR-ok módosítása.");
define('LANG_sourcemod_flag_i', "Konfigurációs fájlok végrehajtása.");
define('LANG_sourcemod_flag_j', "Speciális chat jogok.");
define('LANG_sourcemod_flag_k', "Szavazások indítása vagy létrehozása.");
define('LANG_sourcemod_flag_l', "Egy jelszó beállítása a szerveren.");
define('LANG_sourcemod_flag_m', "RCON parancsok használata.");
define('LANG_sourcemod_flag_n', "Változtasd meg az sv_cheats változót vagy használj cheat parancsokat.");
define('LANG_sourcemod_flag_o', "1. Egyéni csoport");
define('LANG_sourcemod_flag_p', "2. egyéni csoport");
define('LANG_sourcemod_flag_q', "3. egyéni csoport.");
define('LANG_sourcemod_flag_r', "4. egyéni csoport.");
define('LANG_sourcemod_flag_s', "5. egyéni csoport.");
define('LANG_sourcemod_flag_t', "6. egyéni csoport.");
define('LANG_rcon_reload_admins_failed', "Nem sikerült újratölteni az admin gyorsítótárat az RCON-ból, a szerver elérhető?");
define('LANG_reload_admins_failed', "Nem sikerült újratölteni az admin gyorsítótárat az RCON-ból; \"sm_reloadadmins\" ismeretlen parancs.");
define('LANG_reload_admins_success', "Sikeresen hozzáadva %s az admins_simple.ini-hez, és újratöltve az admin gyorsítótár.");
define('LANG_add_success_no_rcon', "Sikeresen hozzáadva %s az admins_simple.ini-hez, de nem sikerült újratölteni az admin gyorsítótárat.");
define('LANG_writefile_error', "Ismeretlen írási hiba itt: %s");
define('LANG_remotefile_nonexistent', "Nem sikerült az új admin hozzáadása. Admin fájl: %s nem található.");
define('LANG_empty_flag_list', "Nem választottál ki adminisztrátori jogokat.");
define('LANG_invalid_steam_format', "A megadott SteamID nem egyezik meg a kívánt mintával.");
define('LANG_selected_server_offline', "Nem sikerült az admin hozzáadása, az ügynök szerint a kiválasztott szerver nem fut.");
define('LANG_malformed_form', "You submitted a form with malformed hidden elements - unable to add an admin.");
define('LANG_empty_form_data', "Kérlek, töltsd ki az összes mezőt.");
define('LANG_server_not_selected', "Nem választottál ki szervert.");
define('LANG_invalid_steamid', "Érvénytelen Steam IDt adtál meg.");
define('LANG_invalid_immunity', "Érvénytelen védettségi értéket adtál meg.");
define('LANG_submit', "Beküldés");
define('LANG_post_failed', "A POST művelet sikertelen. Nem sikerült lekérni a választ.");
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