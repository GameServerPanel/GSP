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

define('LANG_no_games_to_monitor', "Du har ikke konfigureret nogen spil, som du ka iagttage.");
define('LANG_status', "Status");
define('LANG_fail_no_mods', "Ingen mods er aktiveret til dette spil! Spørg en OGP admin om hjælp, for at få tildelt nogle mod(s) til det spil, som er tildelt til dig.");
define('LANG_no_game_homes_assigned', "You don't have any servers assigned to your account.");
define('LANG_select_game_home_to_configure', "Vælg den spil server, som du vil konfigurer");
define('LANG_file_manager', "Fil Håndtering");
define('LANG_configure_mods', "Konfigure mods");
define('LANG_install_update_steam', "Installere/Opdatere via Steam");
define('LANG_install_update_manual', "Installere/Opdatere manualt");
define('LANG_assign_game_homes', "Tildel spil server");
define('LANG_user', "Bruger");
define('LANG_group', "Gruppe");
define('LANG_start', "Start");
define('LANG_ogp_agent_ip', "OGP Agent IP");
define('LANG_max_players', "Max Spillere");
define('LANG_max', "Max");
define('LANG_ip_and_port', "IP og Port");
define('LANG_available_maps', "Tilgængelig Maps");
define('LANG_map_path', "Kort Sti");
define('LANG_available_parameters', "Tilgængelig Parametere");
define('LANG_start_server', "Start Server");
define('LANG_start_wait_note', "The server startup might take a while. Please wait without closing your browser.");
define('LANG_game_type', "Spil Type");
define('LANG_map', "Kort");
define('LANG_starting_server', "Starter server, vent venligst...");
define('LANG_starting_server_settings', "Starter serveren med følgende opsætning");
define('LANG_startup_params', "Start parametere");
define('LANG_startup_cpu', "CPU som serveren kører med");
define('LANG_startup_nice', "God værdi af serveren");
define('LANG_game_home', "Home Path");
define('LANG_server_started', "Server startet korrekt.");
define('LANG_no_parameter_access', "Du har ikke rettigheder til parameter.");
define('LANG_extra_parameters', "Ekstra Parameter");
define('LANG_no_extra_param_access', "Du har ikke rettigheder til ekstra parameter.");
define('LANG_extra_parameters_info', "Disse parameter bliver tilføjet i enden af kommandoen linjen, når spillet starter.");
define('LANG_game_exec_not_found', "Spillets eksekvebar %s blev ikke fundet på fjern serveren.");
define('LANG_select_params_and_start', "Vælg de start parametere til serveren og tryk '%s'.");
define('LANG_no_ip_port_pairs_assigned', "Ingen IP Porte tildelt assigned til dette hjem. Hvis du ikke har rettigheder for at editere hjem, kontakt venligst din administrator.");
define('LANG_unable_to_get_log', "Ikke muligt at få fat på loggen, interval %s.");
define('LANG_server_binary_not_executable', "Server binær er ikke eksekverbar. Tjek are du har de korrekte tilladelser til server home mappen.");
define('LANG_server_not_running_log_found', "Server kører ikke, men log er fundet. NOTE: Denne log, er måske ikke relateret til serveren sidste opstart.");
define('LANG_ip_port_pair_not_owned', "IP:PORT tildeling ejes ikke.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Upassende maxspiller værdi, maximum antal numre af slots er bleven tildelt.");
define('LANG_server_running_not_responding', "Serveren kører, men svarer ikke,<br>det er muligt, at der er en form for problem, og at det måske skulle");
define('LANG_update_started', "Opdatering startet, vent venligst...");
define('LANG_failed_to_start_steam_update', "Fejlet I at starte steam opdatering. Se agent log.");
define('LANG_failed_to_start_rsync_update', "Failed to start Rsync update. See agent log.");
define('LANG_update_completed', "Opdatering færdiggjort succesfuldt.");
define('LANG_update_in_progress', "Opdatering er igang, vent venligst...");
define('LANG_refresh_steam_status', "Refresh Steam status");
define('LANG_refresh_rsync_status', "Refresh Rsync status");
define('LANG_server_running_cant_update', "Serveren kører, så updatering er ikke mulig. Stop serveren før opdatering.");
define('LANG_xml_steam_error', "Den valgte server type, supportere ikke steam installering/opdatering.");
define('LANG_mod_key_not_found_from_xml', "Mod nøgle '%s' ikke fundet på XML fil.");
define('LANG_stop_update', "Stop opdatering");
define('LANG_statistics', "Statestikker");
define('LANG_servers', "Servere");
define('LANG_players', "Spillere");
define('LANG_current_map', "Nuværrende kort");
define('LANG_stop_server', "Stop Server");
define('LANG_server_ip_port', "Server IP:Port");
define('LANG_server_name', "Server Navn");
define('LANG_server_id', "Server ID");
define('LANG_player_name', "Spiller Navn");
define('LANG_score', "Score");
define('LANG_time', "Tid");
define('LANG_no_rights_to_stop_server', "Du har ikke rettigheder, til at stoppe denne server.");
define('LANG_no_ogp_lgsl_support', "Denne server (kører: %s) og har ikke LGSL support i OGP, og det statestikker, kan ikke blive vist.");
define('LANG_server_status', "Server on %s is %s.");
define('LANG_server_stopped', "Serveren '%s' er bleven stoppet.");
define('LANG_if_want_to_start_homes', "Hvis du vil starte spille servere, gå til %s.");
define('LANG_view_log', "Log Viewer");
define('LANG_if_want_manage', "Hvis du vil håndtere dine spil, kan du gøre dette I");
define('LANG_columns', "kolonner");
define('LANG_group_users', "Gruppe:");
define('LANG_assigned_to', "Tildelt:");
define('LANG_restart_server', "Genstart Server");
define('LANG_restarting_server', "Genstartning af server, vent venligst...");
define('LANG_server_restarted', "Server '%s' er bleven genstartet.");
define('LANG_server_not_running', "Denne server kører ikke.");
define('LANG_address', "Addresse");
define('LANG_owner', "Ejer");
define('LANG_operations', "Operationer");
define('LANG_search', "Søg");
define('LANG_maps_read_from', "Korte læses fra ");
define('LANG_file', "fil");
define('LANG_folder', "mappe");
define('LANG_unable_retrieve_mod_info', "Ude af stand til, at modtage mod informationer fra databasen.");
define('LANG_unexpected_result_libremote', "Uventet resultat fra libremote, venlig informere udviklerne.");
define('LANG_unable_get_info', "Ude af stand til, at hente krævende information til opstart, blokere opstart.");
define('LANG_server_already_running', "Server kører allerede. Hvis du ikke ser serveren I spille monitoren, kan det skyldes et eller andet problem, som du nok ville");
define('LANG_already_running_stop_server', "Stop server.");
define('LANG_error_server_already_running', "FEJL: Server kører allerede på port");
define('LANG_failed_start_server_code', "Failed to start the remote server. Error code: %s");
define('LANG_disabled', "Slået fra ");
define('LANG_not_found_server', "Kunne ikke finde fjernserveren med ID");
define('LANG_rcon_command_title', "RCON Kommando");
define('LANG_has_sent_to', "er bleven sendt til");
define('LANG_need_set_remote_pass', "Du er nødtil at sætte fjern kontrol adgangskode på");
define('LANG_before_sending_rcon_com', "før der sendes rcon kommandoer til det.");
define('LANG_retry', "Forsøg igen");
define('LANG_page', "side");
define('LANG_server_cant_start', "server kan ikke starte");
define('LANG_server_cant_stop', "server kan ikke stoppe");
define('LANG_error_occured_remote_host', "Fejl opstod på fjern værten");
define('LANG_follow_server_status', "Du ka følge serverens status fra");
define('LANG_addons', "Addons");
define('LANG_hostname', "Værtsnavn");
define('LANG_rsync_install', "[Rsync Installalering]");
define('LANG_ping', "Ping");
define('LANG_team', "Hold");
define('LANG_deaths', "Døde");
define('LANG_pid', "PID");
define('LANG_skill', "Evne");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Spiller");
define('LANG_port', "Port");
define('LANG_rcon_presets', "RCON standard indstillinger");
define('LANG_update_from_local_master_server', "Update from local Master Server");
define('LANG_update_from_selected_rsync_server', "Update from selected Rsync server");
define('LANG_execute_selected_server_operations', "Udfør valgte server operation");
define('LANG_execute_operations', "Udfør operationer");
define('LANG_account_expiration', "Account expiration");
define('LANG_mysql_databases', "MySQL Databases");
define('LANG_failed_querying_server', "* Failed querying the server.");
define('LANG_query_protocol_not_supported', "* There is no query protocol in OGP that can support this server.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Queries disabled by setting: Disable queries after: %s, since you have %s servers.<br>");
define('LANG_presets_for_game_and_mod', "RCON standard indstillinger for %s og mod %s");
define('LANG_name', "Navn");
define('LANG_command', "RCON&nbsp;Kommando");
define('LANG_add_preset', "Tilføj standard indstillinger");
define('LANG_edit_presets', "Edit standard indstillinger");
define('LANG_del_preset', "Slet");
define('LANG_change_preset', "Vælg");
define('LANG_send_command', "Send kommando");
define('LANG_starting_copy_with_master_server_named', "Start kopi af mester server navn '%s'...");
define('LANG_starting_sync_with', "Start synkronisering med %s...");
define('LANG_refresh_interval', "Log genopfrisker interval");
define('LANG_finished_manual_update', "Manual opdatering færdiggjort.");
define('LANG_failed_to_start_file_download', "Fejlet i at starte fil download.");
define('LANG_game_name', "Spille navn");
define('LANG_dest_dir', "Mappe destinations");
define('LANG_remote_server', "Fjern Server");
define('LANG_file_url', "Fil URL");
define('LANG_file_url_info', "URL af den fil, der er uploadet og pakket ud til mappen.");
define('LANG_dest_filename', "Destinations Fil navn");
define('LANG_dest_filename_info', "Et filnavn for destinations fil.");
define('LANG_update_server', "Updatere server");
define('LANG_unavailable', "Utilgængelig");
define('LANG_upload_map_image', "Upload map image");
define('LANG_upload_image', "Upload image");
define('LANG_jpg_gif_png_less_than_1mb', "The image must be jpg, gif or png and less than 1 MB.");
define('LANG_check_dev_console', "Error during uploading file, please check the browser developer console.");
define('LANG_uploaded_successfully', "Uploaded successfully.");
define('LANG_cant_create_folder', "Can't create folder:<br><b>%s</b>");
define('LANG_cant_write_file', "Can't write file:<br><b>%s</b>");
define('LANG_exceeded_php_directive', "Exceeded PHP directive.<br><b>%s</b>.");
define('LANG_unknown_errors', "Unknown errors.");
define('LANG_directory', "Mappe Sti");
define('LANG_view_player_commands', "View Player Commands");
define('LANG_hide_player_commands', "Hide Player Commands");
define('LANG_no_online_players', "There are no online players.");
define('LANG_invalid_game_mod_id', "Invalid Game/Mod ID specified.");
define('LANG_auto_update_title_popup', "Steam Auto Update Link");
define('LANG_auto_update_popup_html', "<p>Use the link below to check and automatically update your game server via Steam if needed.&nbsp; You can query it using a cronjob or manually initiate the process.</p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Copy");
define('LANG_auto_update_copy_me_success', "Copied!");
define('LANG_auto_update_copy_me_fail', "Failed to copy. Please, manually copy the link.");
define('LANG_get_steam_autoupdate_api_link', "Auto Update Link");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "User %s attempted to update home_id %d from a non-master server. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "You are attempting to update this server from a non-master server.");
define('LANG_cannot_update_from_own_self', "Local server update may not run on a Master server.");
define('LANG_show_server_id', "Show Server IDs");
define('LANG_hide_server_id', "Hide Server IDs");
define('LANG_edit_configuration_files', "Edit Configuration Files");
define('LANG_admin', "Admin");
define('LANG_cid', "CID");
define('LANG_phan', "Phantom");
define('LANG_sec', "Seconds");
define('LANG_unknown_rsync_mirror', "You attempted to start an update from a mirror which doesn't exist.");
define('LANG_custom_fields', "Custom Fields");
?>
