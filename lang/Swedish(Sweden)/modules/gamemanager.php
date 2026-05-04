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

define('LANG_no_games_to_monitor', "Du har inga spel upplagda som du kan kontrollera. ");
define('LANG_status', "Status");
define('LANG_fail_no_mods', "Inga moddar är aktiverade för detta spel! Du måste fråga din OGP-administratör för att lägga till modd(ar) för det spel som tilldelats till dig. ");
define('LANG_no_game_homes_assigned', "You don't have any servers assigned to your account.");
define('LANG_select_game_home_to_configure', "Välj en spelserver du vill konfigurera");
define('LANG_file_manager', "Filhanterare");
define('LANG_configure_mods', "Konfigurera moddar ");
define('LANG_install_update_steam', "Installera/uppdatera via Steam");
define('LANG_install_update_manual', "Installera/uppdatera manuellt ");
define('LANG_assign_game_homes', "Tilldela spelservrar");
define('LANG_user', "Användare");
define('LANG_group', "Grupp");
define('LANG_start', "Starta");
define('LANG_ogp_agent_ip', "OGP-Agent IP");
define('LANG_max_players', "Max antal spelare");
define('LANG_max', "Max");
define('LANG_ip_and_port', "IP och port");
define('LANG_available_maps', "Tillgängliga kartor");
define('LANG_map_path', "Kartsökväg");
define('LANG_available_parameters', "Tillgängliga parametrar ");
define('LANG_start_server', "Starta servern");
define('LANG_start_wait_note', "Serveruppstarten kan ta en stund. Vänta utan att stänga ned din webbläsare. ");
define('LANG_game_type', "Speltyp");
define('LANG_map', "Karta");
define('LANG_starting_server', "Startar servern, vänta...");
define('LANG_starting_server_settings', "Startar servern med följande inställningar");
define('LANG_startup_params', "Uppstartsparametrar");
define('LANG_startup_cpu', "CPU som servern körs på");
define('LANG_startup_nice', "Prioritetsvärde på servern");
define('LANG_game_home', "Hemkatalog");
define('LANG_server_started', "Serverstarten lyckades.");
define('LANG_no_parameter_access', "Du har inte tillgång till dessa parametrar. ");
define('LANG_extra_parameters', "Extra parametrar");
define('LANG_no_extra_param_access', "Du har inte tillgång till dessa extra parametrar. ");
define('LANG_extra_parameters_info', "Dessa parametrar läggs till i slutet av kommandoraden när spelet startas. ");
define('LANG_game_exec_not_found', "Spelets exekverbara filer %s hittades inte på fjärrservern. ");
define('LANG_select_params_and_start', "Välj startparametrar för servern och tryck på '%s'. ");
define('LANG_no_ip_port_pairs_assigned', "Inget IP och portpar har blivit tilldelade för denna hemkatalog. Om du inte har tillgång till att kunna ändra på hemkataloger, kontakta administratören. ");
define('LANG_unable_to_get_log', "Kunde inte läsa loggen, retval %s. ");
define('LANG_server_binary_not_executable', "Serverns binär är inte körbar. Kontrollera att du har rätta behörigheter i serverns hemkatalog.");
define('LANG_server_not_running_log_found', "Servern körs inte, men en logg har hittats. NOTERA: Denna logg är kanske inte relaterad till senaste serverstart. ");
define('LANG_ip_port_pair_not_owned', "IP:PORT pair ej ägd. ");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Ogiltigt maxspelarvärde, maximalt antal spelarplatser har ställts in. ");
define('LANG_server_running_not_responding', "Servern körs, men svarar inte, <br>det kan vara något problem och du borde");
define('LANG_update_started', "Uppdatering startades, vänta...");
define('LANG_failed_to_start_steam_update', "Misslyckades med att starta Steam-uppdateringen. Se agentloggen. ");
define('LANG_failed_to_start_rsync_update', "Misslyckades med att starta Rsync-uppdateringen. Se agentlogg. ");
define('LANG_update_completed', "Uppdateringen lyckades. ");
define('LANG_update_in_progress', "Pågående uppdatering, vänta...");
define('LANG_refresh_steam_status', "Refresh Steam status");
define('LANG_refresh_rsync_status', "Refresh Rsync status");
define('LANG_server_running_cant_update', "Servern körs, så uppdatering är ej möjlig. Stoppa servern innan du uppdaterar. ");
define('LANG_xml_steam_error', "Den valda servertypen stödjer inte steamuppdatering/installation. ");
define('LANG_mod_key_not_found_from_xml', "Moddnyckel '%s' hittades inte i XML-filen. ");
define('LANG_stop_update', "Stoppa uppdatering");
define('LANG_statistics', "Statistik");
define('LANG_servers', "Servrar");
define('LANG_players', "Spelare");
define('LANG_current_map', "Nuvarande karta");
define('LANG_stop_server', "Stoppa servern");
define('LANG_server_ip_port', "Server IP:Port");
define('LANG_server_name', "Servernamn");
define('LANG_server_id', "Server-ID");
define('LANG_player_name', "Spelarnamn");
define('LANG_score', "Poäng");
define('LANG_time', "Tid");
define('LANG_no_rights_to_stop_server', "Du har inte rättigheter att stoppa denna server. ");
define('LANG_no_ogp_lgsl_support', "Denna Servern (körs: %s) har inte LGSL-support i OGP, så statistik kan inte visas. ");
define('LANG_server_status', "Servern på %s är%s.");
define('LANG_server_stopped', "Servern '%s' har stoppats. ");
define('LANG_if_want_to_start_homes', "Om du vill starta spelservrar går du till %s.");
define('LANG_view_log', "Loggvisning ");
define('LANG_if_want_manage', "Om du vill hantera dina spel, kan du göra detta på");
define('LANG_columns', "Kolumner");
define('LANG_group_users', "Gruppanvändare: ");
define('LANG_assigned_to', "Tilldelat till:");
define('LANG_restart_server', "Starta om servern");
define('LANG_restarting_server', "Startar om server, vänta...");
define('LANG_server_restarted', "Servern '%s' har blivit omstartad.");
define('LANG_server_not_running', "Servern körs inte. ");
define('LANG_address', "Adress ");
define('LANG_owner', "Ägare");
define('LANG_operations', "Åtgärder");
define('LANG_search', "Sök");
define('LANG_maps_read_from', "Kartor läses från");
define('LANG_file', "fil");
define('LANG_folder', "mapp");
define('LANG_unable_retrieve_mod_info', "Kunde inte hämta modd-information från databas. ");
define('LANG_unexpected_result_libremote', "Oväntat resultat från libremote, informera utvecklarna. ");
define('LANG_unable_get_info', "Kunde inte få nödvändig information för uppstart, blockerar uppstart. ");
define('LANG_server_already_running', "Servern körs redan. Om du inte kan se servern i spelhanteraren, kan det vara något fel, och du borde kanske");
define('LANG_already_running_stop_server', "Stoppa servern. ");
define('LANG_error_server_already_running', "FEL: Servern körs redan på port");
define('LANG_failed_start_server_code', "Misslyckades med att starta fjärrservern. Felkod: %s");
define('LANG_disabled', "inaktiverad");
define('LANG_not_found_server', "Kunde inte hitta fjärrservern med ID");
define('LANG_rcon_command_title', "RCON-Kommando ");
define('LANG_has_sent_to', "har skickats till");
define('LANG_need_set_remote_pass', "Du måste sätta fjärrkontroll lösenord på");
define('LANG_before_sending_rcon_com', "innan du skickar rcon-kommandon till den. ");
define('LANG_retry', "Försök igen");
define('LANG_page', "sida");
define('LANG_server_cant_start', "servern kan inte starta");
define('LANG_server_cant_stop', "servern kan inte stoppa");
define('LANG_error_occured_remote_host', "Fel uppstod på fjärrvärden ");
define('LANG_follow_server_status', "Du kan följa serverstatus från");
define('LANG_addons', "Tillägg");
define('LANG_hostname', "Hostnamn");
define('LANG_rsync_install', "[Rsync-installation]");
define('LANG_ping', "Ping");
define('LANG_team', "Lag");
define('LANG_deaths', "Dödsfall");
define('LANG_pid', "PID");
define('LANG_skill', "Skicklighetsnivå");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam-ID");
define('LANG_player', "Spelare");
define('LANG_port', "Port");
define('LANG_rcon_presets', "RCON-förinställningar");
define('LANG_update_from_local_master_server', "Uppdatera från lokal Master-server. ");
define('LANG_update_from_selected_rsync_server', "Uppdatera från vald Rsync-server. ");
define('LANG_execute_selected_server_operations', "Utför valda server åtgärder");
define('LANG_execute_operations', "Utför åtgärder");
define('LANG_account_expiration', "Kontot upphör");
define('LANG_mysql_databases', "MySQL Databaser");
define('LANG_failed_querying_server', "* Misslyckades med att skicka fråga till servern. ");
define('LANG_query_protocol_not_supported', "* Det finns inget frågeprotokoll i OGP som supportar denna servern. ");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Serverfrågor avaktiverat i inställningar: Avaktivera frågor efter: %s, eftersom du har %s servers.<br>");
define('LANG_presets_for_game_and_mod', "RCON-förinställningar för %s och modd %s");
define('LANG_name', "Namn");
define('LANG_command', "RCON&nbsp;Kommando");
define('LANG_add_preset', "Lägg till förinställning");
define('LANG_edit_presets', "Ändra förinställning");
define('LANG_del_preset', "Ta bort");
define('LANG_change_preset', "Byt");
define('LANG_send_command', "Skicka kommando");
define('LANG_starting_copy_with_master_server_named', "Startar kopiering med masterservern som har namn '%s'...");
define('LANG_starting_sync_with', "Startar synkronisering med %s...");
define('LANG_refresh_interval', "Log-uppdateringsintervall ");
define('LANG_finished_manual_update', "Färdig med manuell uppdatering.");
define('LANG_failed_to_start_file_download', "Misslyckades med att starta filhämtning");
define('LANG_game_name', "Spelnamn");
define('LANG_dest_dir', "Destinationskatalog");
define('LANG_remote_server', "Fjärrserver");
define('LANG_file_url', "Fil-URL");
define('LANG_file_url_info', "URL'en av filen som är uppladdad och uppackad till katalogen. ");
define('LANG_dest_filename', "Destinationsfilnamn ");
define('LANG_dest_filename_info', "Filnamnet för destinationsfilen.");
define('LANG_update_server', "Uppdatera server");
define('LANG_unavailable', "Otillgänglig");
define('LANG_upload_map_image', "Ladda upp kartimage");
define('LANG_upload_image', "Ladda upp bild");
define('LANG_jpg_gif_png_less_than_1mb', "Bilden måste vara av formaten jpg, gif eller png, och mindre än 1 MB. ");
define('LANG_check_dev_console', "Fel vid uppladdning av fil, var god kontrollera webbläsarens utvecklarkonsol. ");
define('LANG_uploaded_successfully', "Uppladdningen lyckades.");
define('LANG_cant_create_folder', "Kan inte skapa mapp: <br><b>%s</b>");
define('LANG_cant_write_file', "Kan inte skriva fil: <br><b>%s</b>");
define('LANG_exceeded_php_directive', "Överskred PHP direktiv. <br><b>%s</b>.");
define('LANG_unknown_errors', "Okända fel. ");
define('LANG_directory', "Katalogsökväg");
define('LANG_view_player_commands', "Se spelarkommandon ");
define('LANG_hide_player_commands', "Göm spelarkommandon");
define('LANG_no_online_players', "Det är inga spelare online.");
define('LANG_invalid_game_mod_id', "Ogiltig Spel/Modds ID specificerad. ");
define('LANG_auto_update_title_popup', "Steam-autouppdateringslänk ");
define('LANG_auto_update_popup_html', "<p>Använd länken nedan för att automatiskt uppdatera din spelserver via steam. Om det krävs kan du använda dig av cronjob eller manuellt starta uppdateringsprocessen. </p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Kopiera");
define('LANG_auto_update_copy_me_success', "Kopierad!");
define('LANG_auto_update_copy_me_fail', "Misslyckades med att kopiera. Kopiera länken manuellt. ");
define('LANG_get_steam_autoupdate_api_link', "Autouppdateringslänk");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "Användaren %s försökte att uppdatera hem_id %d från en icke-Masterserver. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "Du försöker att uppdatera denna server fårn en icke-masterserver. ");
define('LANG_cannot_update_from_own_self', "Lokal uppdatering kanske inte kan köras på en Masterserver. ");
define('LANG_show_server_id', "Visa server-ID's ");
define('LANG_hide_server_id', "Göm server-ID's");
define('LANG_edit_configuration_files', "Ändra konfigurationsfilerna ");
define('LANG_admin', "Admin");
define('LANG_cid', "CID");
define('LANG_phan', "Fantom");
define('LANG_sec', "Sekunder");
define('LANG_unknown_rsync_mirror', "You attempted to start an update from a mirror which doesn't exist.");
define('LANG_custom_fields', "Custom Fields");
?>
