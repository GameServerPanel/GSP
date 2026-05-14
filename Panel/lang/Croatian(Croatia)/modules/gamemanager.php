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

define('LANG_no_games_to_monitor', "Nemate nijednu konfiguriranu igricu koju možete nadzirati.");
define('LANG_status', "Status");
define('LANG_fail_no_mods', "Nijedan Mod nije omogućen za ovu igru! Morate zatražiti od administratora OGP-a da dodate Mod(ove) za igru koja vam je dodjeljena.");
define('LANG_no_game_homes_assigned', "Nemate nijedan server dodijeljen vašem računu.");
define('LANG_select_game_home_to_configure', "Odaberite server koji želite konfigurirati");
define('LANG_file_manager', "Upravitelj datoteka");
define('LANG_configure_mods', "Konfigurirajte modove");
define('LANG_install_update_steam', "Instalirati/Ažurirati preko Steam-a");
define('LANG_install_update_manual', "Instalirati/Ručno ažurirati");
define('LANG_assign_game_homes', "Dodijeli server");
define('LANG_user', "Korisnik");
define('LANG_group', "Grupa");
define('LANG_start', "Početak");
define('LANG_ogp_agent_ip', "OGP IP adresa Agenta");
define('LANG_max_players', "Maksimalno Igrača");
define('LANG_max', "Maksimalno");
define('LANG_ip_and_port', "IP i Port");
define('LANG_available_maps', "Dostupne Mape");
define('LANG_map_path', "Putanje do Mape");
define('LANG_available_parameters', "Dostupni parametri");
define('LANG_start_server', "Pokrenuti Server");
define('LANG_start_wait_note', "Pokretanje servera može potrajati neko vrijeme. Pričekajte bez zatvaranja preglednika.");
define('LANG_game_type', "Vrsta Igre");
define('LANG_map', "Mapa");
define('LANG_starting_server', "Pokretanje Servera, molimo pričekajte...");
define('LANG_starting_server_settings', "Pokretanje Servera sa sljedećim postavkama");
define('LANG_startup_params', "Pokretanje parametri");
define('LANG_startup_cpu', "CPU na kojem je server pokrenut");
define('LANG_startup_nice', "Vrijednost servera");
define('LANG_game_home', "Home Putanje");
define('LANG_server_started', "Server je uspješno pokrenut");
define('LANG_no_parameter_access', "Nemate pristup parametrima.");
define('LANG_extra_parameters', "Dodatni Parametri");
define('LANG_no_extra_param_access', "Nemate pristup dodatnim parametrima.");
define('LANG_extra_parameters_info', "Ovi parametri se stavljaju na kraj naredbenog retka kada se igra započne.");
define('LANG_game_exec_not_found', "izvršna datoteka igre %s nije pronađena s udaljenog servera.");
define('LANG_select_params_and_start', "Odaberite parametre za pokretanje servera i pritisnite tipku '%s'.");
define('LANG_no_ip_port_pairs_assigned', "Nema  IP Portova dodijeljenih za ovu Home maou. Ako nemate pristup uređivanju Home mape, obratite se administratoru.");
define('LANG_unable_to_get_log', "Unable to get log, retval %s.");
define('LANG_server_binary_not_executable', "Binarni poslužitelj nije izvršiv. Provjerite imate li odgovarajuća dopuštenja u Home direktoriju poslužitelja.");
define('LANG_server_not_running_log_found', "Poslužitelj se ne izvodi, ali se pronalazi zapisnik. NAPOMENA: Ovaj zapisnik možda nije povezan s posljednjim pokretanjem poslužitelja.");
define('LANG_ip_port_pair_not_owned', "IP: PORT par nije u vlasništvu.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Neodgovarajuća maksimalna vrijednost igrača,  postavljen je maksimalni broj dostupnih mjesta.");
define('LANG_server_running_not_responding', "Serverje pokrenut, ali ne reagira,<br>možda postoji nekakav problem i možda želite");
define('LANG_update_started', "Ažuriranje je počelo, molimo pričekajte...");
define('LANG_failed_to_start_steam_update', "Neuspješno pokretanje Steam ažuriranje. Pogledajte zapisnik agenta.");
define('LANG_failed_to_start_rsync_update', "Neuspješno pokretanje Rsync ažuriranje. Pogledajte zapisnik agenta.");
define('LANG_update_completed', "Ažuriranje je uspješno dovršeno.");
define('LANG_update_in_progress', "Ažuriranje u tijeku, molimo pričekajte...");
define('LANG_refresh_steam_status', "Osvježiti Steam status");
define('LANG_refresh_rsync_status', "Osvježiti Rsync status");
define('LANG_server_running_cant_update', "Server je pokrenut tako da ažuriranje nije moguće. Zaustavite Server prije ažuriranja.");
define('LANG_xml_steam_error', "Odabrana vrsta Servera ne podržava Steam Instaliracija/Ažuriranje");
define('LANG_mod_key_not_found_from_xml', "Mod ključ '%s' nije pronađen iz XML datoteke.");
define('LANG_stop_update', "Zaustaviti ažuriranje");
define('LANG_statistics', "Statistika");
define('LANG_servers', "Serveri");
define('LANG_players', "Igrači");
define('LANG_current_map', "Trenutna Mapa");
define('LANG_stop_server', "Zaustaviti Server");
define('LANG_server_ip_port', "IP:Port Servera");
define('LANG_server_name', "Ime Servera");
define('LANG_server_id', "ID Servera");
define('LANG_player_name', "Ime Igrača");
define('LANG_score', "Rezultat");
define('LANG_time', "Vrijeme");
define('LANG_no_rights_to_stop_server', "Nemate prava za zaustavljanje ovog servera.");
define('LANG_no_ogp_lgsl_support', "Ovaj server (pokrenut: %s) nema LGSL podršku u OGP-u i njegova se statistika ne može prikazati.");
define('LANG_server_status', "Server na %s je %s.");
define('LANG_server_stopped', "Server '%s'  je zaustavljen.");
define('LANG_if_want_to_start_homes', "Ako želite pokrenuti Igrice idite na %s.");
define('LANG_view_log', "Pogledati Zapisnik");
define('LANG_if_want_manage', "Ako želite upravljati svojim igrama, to možete učiniti u");
define('LANG_columns', "stupcima");
define('LANG_group_users', "Korisnici grupe:");
define('LANG_assigned_to', "Dodijeljen:");
define('LANG_restart_server', "Ponovno pokrenuti");
define('LANG_restarting_server', "Pokretanje Servera, molimo pričekajte...");
define('LANG_server_restarted', "Server '%s' je ponovno pokrenut.");
define('LANG_server_not_running', "Server nije pokrenut.");
define('LANG_address', "Adresa");
define('LANG_owner', "Vlasnik");
define('LANG_operations', "Radnje");
define('LANG_search', "Tražiti");
define('LANG_maps_read_from', "Mapa učitana od");
define('LANG_file', "datoteka");
define('LANG_folder', "mapa");
define('LANG_unable_retrieve_mod_info', "Nije moguće dohvatiti mod podatke iz baze podataka.");
define('LANG_unexpected_result_libremote', "Neočekivani rezultat iz libremotea, molimo obavijestite programere.");
define('LANG_unable_get_info', "Nije moguće dobiti potrebne informacije za pokretanje, pokretanje blokirano.");
define('LANG_server_already_running', "Server je već pokrenut. Ako server ne vidite u Monitor Igara, vjerojatno postoji nekakav problem i možda biste htjeli");
define('LANG_already_running_stop_server', "Zaustaviti Server");
define('LANG_error_server_already_running', "POGREŠKA: Server već radi na port");
define('LANG_failed_start_server_code', "Pokretanje udaljenog servera nije uspjelo. Kod pogreške: %s");
define('LANG_disabled', "onemogućeno");
define('LANG_not_found_server', "Nije moguće pronaći udaljeni Server sa ID");
define('LANG_rcon_command_title', "RCON Naredba");
define('LANG_has_sent_to', "poslano za");
define('LANG_need_set_remote_pass', "Morate postaviti RCON lozinku na");
define('LANG_before_sending_rcon_com', "prije slanja RCON naredbi ");
define('LANG_retry', "Ponovi");
define('LANG_page', "stranica");
define('LANG_server_cant_start', "server se ne može pokrenuti");
define('LANG_server_cant_stop', "server se ne može zaustaviti");
define('LANG_error_occured_remote_host', "Došlo je do pogreške na udaljenom poslužitelju");
define('LANG_follow_server_status', "Možete pratiti status servera iz");
define('LANG_addons', "Dodaci");
define('LANG_hostname', "Naziv Hosta");
define('LANG_rsync_install', "[Rsync Instalacija]");
define('LANG_ping', "Ping");
define('LANG_team', "Tim");
define('LANG_deaths', "Smrti");
define('LANG_pid', "PID");
define('LANG_skill', "Skill");
define('LANG_AIBot', "AIBot");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Igrač");
define('LANG_port', "Port");
define('LANG_rcon_presets', "RCON predpodešavanje");
define('LANG_update_from_local_master_server', "Ažurirati s lokalnog Master Servera");
define('LANG_update_from_selected_rsync_server', "Ažurirati s odabranog Rsync servera");
define('LANG_execute_selected_server_operations', "Izvršavanje odabranih operacija servera");
define('LANG_execute_operations', "Izvršiti operacije");
define('LANG_account_expiration', "Račun ističe");
define('LANG_mysql_databases', "MySQL Baza Podataka");
define('LANG_failed_querying_server', "* Server nije uspješno pokrenut.");
define('LANG_query_protocol_not_supported', "Ne postoji upitni protokol u OGP-u koji može podržati ovaj server.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Upiti su onemogućeni postavljanjem: Onemogući upite nakon: %s, jer imate %s servera.<br>");
define('LANG_presets_for_game_and_mod', "RCON predpodešavanje za %s i mod %s");
define('LANG_name', "Ime");
define('LANG_command', "RCON&nbsp;Naredba");
define('LANG_add_preset', "Dodati predpodešavanje");
define('LANG_edit_presets', "Uređivanje postavki");
define('LANG_del_preset', "Izbrisati");
define('LANG_change_preset', "Promijeniti");
define('LANG_send_command', "Poslati naredbu");
define('LANG_starting_copy_with_master_server_named', "Pokretanje kopiranje sa Master Server pod nazivom '%s'...");
define('LANG_starting_sync_with', "Pokretanje sinkronizacije sa %s...");
define('LANG_refresh_interval', "Zapisnik intervala");
define('LANG_finished_manual_update', "Završeno ručno ažuriranje");
define('LANG_failed_to_start_file_download', "Preuzimanje datoteke nije uspjelo");
define('LANG_game_name', "Ime Igre");
define('LANG_dest_dir', "Odredišni direktorij");
define('LANG_remote_server', "Udaljeni Server");
define('LANG_file_url', "URL Datoteka");
define('LANG_file_url_info', "URL datoteke koja je prenošena i nekomprimirana u direktorij.");
define('LANG_dest_filename', "Odredišni naziv datoteke");
define('LANG_dest_filename_info', "Naziv datoteke za odredišnu datoteku.");
define('LANG_update_server', "Ažuriraj Server");
define('LANG_unavailable', "Nedostupan");
define('LANG_upload_map_image', "Učitaj sliku karte");
define('LANG_upload_image', "Učitaj sliku");
define('LANG_jpg_gif_png_less_than_1mb', "Slika mora biti jpg, gir ili png i manje od 1 MB.");
define('LANG_check_dev_console', "Pogreška prilikom prijenosa datoteke, provjerite konzolu razvojnih programera preglednika.");
define('LANG_uploaded_successfully', "Učitano je uspješno.");
define('LANG_cant_create_folder', "Nije moguće izraditi mapu:<br><b>%s</b>");
define('LANG_cant_write_file', "Nije moguće pisati datoteku:<br><b>%s</b>");
define('LANG_exceeded_php_directive', "Premašila je PHP direktivu.<br><b>%s</b>");
define('LANG_unknown_errors', "Nepoznate pogreške.");
define('LANG_directory', "Direktorij");
define('LANG_view_player_commands', "Pogledati naredbe igrača");
define('LANG_hide_player_commands', "Sakriti naredbe igrača");
define('LANG_no_online_players', "Nema igrača na mreži.");
define('LANG_invalid_game_mod_id', "Navedeno je nevažeći ID Igre/Moda.");
define('LANG_auto_update_title_popup', "Link za Steam Automatsko Ažuriranje");
define('LANG_auto_update_popup_html', "<p>Koristite vezu u nastavku da biste provjerili i automatski ažurirali svoj server putem Steam-a ako je potrebno. & Nbsp; Možete upotrijebiti korištenjem cronjob-a ili ručno pokrenuti postupak.");
define('LANG_api_links_popup_html', "<p>Odaberite radnju koju želite izvršiti pomoću OGP API-ja za ovaj poslužitelj igre.&nbsp; Zatim pomoću donje veze izvedite željenu radnju.&nbsp; Željenu radnju možete pokrenuti pomoću cronjoba ili izravnim zahtjevom.</p>");
define('LANG_auto_update_copy_me', "Kopiraj");
define('LANG_auto_update_copy_me_success', "Kopirano!");
define('LANG_auto_update_copy_me_fail', "Kopiranje nije uspjelo. Molimo ručno kopirajte vezu.");
define('LANG_get_steam_autoupdate_api_link', "Link za Automatsko Ažuriranje");
define('LANG_show_api_actions', "Prikaži akcije API-ja");
define('LANG_api_links', "API Linkovi");
define('LANG_update_attempt_from_nonmaster_server', "Korisnik %s pokušao je ažurirati home_id %dpreko non-master servera. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "Pokušavate ažuritati ovaj server preko non-master servera.");
define('LANG_cannot_update_from_own_self', "Lokalno ažuriranje servera možda neće raditi na glavnom Master Serveru.");
define('LANG_show_server_id', "Pokaži ID-ove Servera");
define('LANG_hide_server_id', "Sakrij ID-ove Servera");
define('LANG_edit_configuration_files', "Uredi konfiguracijske datoteke");
define('LANG_admin', "Admin");
define('LANG_cid', "CID");
define('LANG_phan', "Fantom");
define('LANG_sec', "Sekunde");
define('LANG_unknown_rsync_mirror', "Pokušali ste pokrenuti ažuriranje iz nečega koje ne postoji.");
define('LANG_custom_fields', "Prilagodljiva Polja");
?>
