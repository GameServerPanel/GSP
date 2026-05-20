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

define('LANG_create_alias', "Skapa alias och mappar");
define('LANG_save_as', "Spara som");
define('LANG_failure', "Fel, misslyckades med att skapa alias filen");
define('LANG_success', "Lyckades");
define('LANG_fast_download_service_for', "Nedladdningar omdirigeringstjänst för %s");
define('LANG_to_the_path', "Till sökvägen");
define('LANG_at_url', "vid URL");
define('LANG_create_alias_for', "Skapa ett alias för");
define('LANG_fast_dl', "Omdirigera nedladdningar (FastDL)");
define('LANG_current_aliases_at_remote_server', "Nuvarande alias på fjärrservern");
define('LANG_delete_selected_aliases', "Ta bort valda alias");
define('LANG_no_aliases_defined', "Det finns inga web-alias definerade av OGP för denna fjärrserver än. ");
define('LANG_fastdl_port', "Port");
define('LANG_fastdl_port_info', "Vilken port som används när din Snabbnedladdnings-daemon startar. ");
define('LANG_fastdl_ip', "Adress");
define('LANG_fastdl_ip_info', "IP-Adress eller domän som används när din Snabbnedladdnings-server startar, domänen måste vara listad i /etc/hosts. ");
define('LANG_listing', "Lista");
define('LANG_listing_info', "Om \"på\", kommer servern lista innehållet i mappar. ");
define('LANG_fast_dl_advanced', "Avancerade alternativ");
define('LANG_apply_settings_and_restart_fastdl', "Spara daemon-konfigurationen och starta om den ");
define('LANG_stop_fastdl', "Stoppa snabbnedladdningstjänsten. ");
define('LANG_fast_download_daemon_running', "Snabbnedladdningstjänsten körs. ");
define('LANG_fast_download_daemon_not_running', "Snabbnedladdningstjänsten körs inte. ");
define('LANG_fastdl_could_not_be_restarted', "Snabbnedladdnings-tjänsten kunde inte startas om. ");
define('LANG_configuration_file_could_not_be_written', "Det gick inte att skriva till konfigurationsfilen. ");
define('LANG_remove_folders', "Ta bort mappar för de valda aliasen. ");
define('LANG_remove_folder', "Ta bort mapp");
define('LANG_delete_alias', "Ta bort alias ");
define('LANG_no_game_homes_assigned', "You don't have any servers assigned to your account.");
define('LANG_select_remote_server', "Välj fjärrserver ");
define('LANG_access_rules', "Åtkomstregler");
define('LANG_create_aliases', "Skapa alias ");
define('LANG_select_game', "Välj spel");
define('LANG_games_without_specified_rules', "Spel utan specificerade regler");
define('LANG_match_file_extension', "Matcha filändelsen");
define('LANG_match_file_extension_info', "Specificera filtillägg separerade av coma, <br> de matchande filerna kommer att vara tillgängliga. <br> <b>Blanka för obegränsad åtkomst </b>. ");
define('LANG_match_client_ip', "Matcha klient-IP ");
define('LANG_match_client_ip_info', "Connections with matching IP will be granted,<br>blank for unrestricted access. You can use<br>multiple IPs or ranges separated by coma:<br>/xx subnets<br>Example: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx subnets<br>Example: 10.0.0.0/255.0.0.0<br>Hyphen ranges<br>Example: 10.0.0.5-230<br>Asterisk matching<br>Example: 10.0.*.*");
define('LANG_save_access_rules', "Spara åtkomstregler");
define('LANG_create_access_rules', "Skapa accessregler");
define('LANG_invalid_entries_found', "Ogiltiga poster hittades ");
define('LANG_game_name', "Spelnamn");
define('LANG_alias_already_exists', "Alias %s finns redan. ");
define('LANG_warning_access_rules_applied_once_alias_created', "VARNING: Accessregler läggs till när aliaset skapas. Inga ändringar kommer att tillämpas på det nuvarande aliaset. ");
define('LANG_autostart_on_agent_startup', "Autostarta vid Agent-start ");
define('LANG_autostart_on_agent_startup_info', "Starta snabbnedladdnings-daemonen automatiskt när agenten startar. ");
define('LANG_port_forwarded_to_80', "Vidarebefodrad från port 80");
define('LANG_port_forwarded_to_80_info', "Aktivera detta alternativ om porten som är konfigurerad för denna snabbnedladdnings-daemon är vidarebefodrad från port 80, så att porten blir gömd i URL'er. ");
define('LANG_current_access_rules', "Nuvarande åtkomstregler");
?>