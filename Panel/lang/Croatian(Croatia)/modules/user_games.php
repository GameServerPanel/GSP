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

define('LANG_add_mods_note', "Možete dodati modove nakon dodavanja servera korisniku. To se može učiniti uređivanjem servera.");
define('LANG_game_servers', "Serveri");
define('LANG_game_path', "Putanje Igre");
define('LANG_game_path_info', "Apsolutno putanje servera. Primjer: /home/ogpbot/OGP_User_Files / My_Server");
define('LANG_game_server_name_info', "Ime servera pomaže korisnicima da identificiraju svoje servere.");
define('LANG_control_password', "Upravljačka lozinka");
define('LANG_control_password_info', "Ova se lozinka koristi za kontrolu servera, kao što je RCON lozinka. Ako je lozinka prazna, upotrebljavaju se druga sredstva.");
define('LANG_add_game_home', "Dodati Igru");
define('LANG_game_path_empty', "Putanje igre ne smije biti prazna.");
define('LANG_game_home_added', "Igrica je uspješno dodana. Preusmjeravanje na stranicu uređivanja Home-a.");
define('LANG_failed_to_add_home_to_db', "Dodavanje Home-a u bazu podataka nije uspjelo. Pogreška: %s");
define('LANG_caution_agent_offline_can_not_get_os_and_arch_showing_servers_for_all_platforms', "<b>Oprez!</b>Agent je izvan mreže, ne može dobiti vrstu i arhitekturu OS-a,<br> Prikaz servera za sve platforme:");
define('LANG_select_remote_server', "Odaberite Udaljeni Server");
define('LANG_no_remote_servers_configured', "Nijedan udaljeni poslužitelj nije konfiguriran za Open Game Panel. Najprije morate dodati udaljene poslužitelje pa tek onda možete korisnicima dizati servere.");
define('LANG_no_game_configurations_found', "Nije pronađena konfiguracija igre. Morate dodati konfiguracije igara iz");
define('LANG_game_configurations', "> stranice za konfiguraciju igre");
define('LANG_add_remote_server', "Dodati Server");
define('LANG_wine_games', "Vinske Igre");
define('LANG_home_path', "Putanje direktorija");
define('LANG_change_home_info', "Mjesto instaliranog servera. Primjer: /home/ogp/my_server/");
define('LANG_game_server_name', "Naziv Servera");
define('LANG_change_name_info', "Mjesto instaliranog servera. Naziv servera koji korisnicima olakšava identifikaciju.");
define('LANG_game_control_password', "Kontrolna lozinka za igru");
define('LANG_change_control_password_info', "Kontrolna lozinka je npr. Lozinka za rcon.");
define('LANG_available_mods', "Dostupni Modovi");
define('LANG_note_no_mods', "Nema dostupnih mod(a) za ovu igru.");
define('LANG_change_home', "Promjeni Direktorij");
define('LANG_change_control_password', "Promjeniti Kontrolnu Lozinku");
define('LANG_change_name', "Promijeniti Naziv");
define('LANG_add_mod', "Dodajte Mod");
define('LANG_set_ip', "Postavite IP");
define('LANG_ips_and_ports', "IP adrese i Portovi");
define('LANG_mod_name', "Naziv Moda");
define('LANG_max_players', "Maksimalno Igrača");
define('LANG_extra_cmd_line_args', "Dodatni naredbeni redak");
define('LANG_extra_cmd_line_info', "Dodatna naredba linija omogućuje način unosa dodatnih argumenata u naredbu za igru kada se pokrene.");
define('LANG_cpu_affinity', "CPU");
define('LANG_nice_level', "Uljepšavanje Razine");
define('LANG_set_options', "Postavite Opcije");
define('LANG_remove_mod', "Ukloniti Mod");
define('LANG_mods', "Modovi");
define('LANG_ip', "IP");
define('LANG_port', "Port");
define('LANG_no_ip_ports_assigned', "Najmanje jedan IP: Port par mora biti dodijeljen serveru.");
define('LANG_successfully_assigned_ip_port', "Uspješno dodijeljen IP: Port serveru.");
define('LANG_port_range_error', "Port mora biti između raspona 0 i 65535.");
define('LANG_failed_to_assing_mod_to_home', "Dodjeljivanje moda s ID-om %d nije uspjelo.");
define('LANG_successfully_assigned_mod_to_home', "Uspješno dodijeljen mod sa ID %d serveru.");
define('LANG_successfully_modified_mod', "Uspješno izmijenjene informacije o modu.");
define('LANG_back_to_game_monitor', "Natrag na Monitor Igara");
define('LANG_back_to_game_servers', "Natrag na Igrice");
define('LANG_user_id_main', "Glavni Vlasnik");
define('LANG_change_user_id_main', "Promijenite glavnog vlasnika");
define('LANG_change_user_id_main_info', "Glavni Vlasnik Servera");
define('LANG_server_ftp_password', "FTP lozinka");
define('LANG_change_ftp_password', "Promijenite FTP lozinku");
define('LANG_change_ftp_password_info', "Ovo je lozinka za prijavu na FTP poslužitelj za ovaj server.");
define('LANG_Delete_old_user_assigned_homes', "Uklonite dodijelu servera trenutnim korisnicima.");
define('LANG_editing_home_called', "Uređivanje Servera pod nazivom");
define('LANG_control_password_updated_successfully', "Kontrolna lozinka uspješno ažurirana.");
define('LANG_control_password_update_failed', "Ažuriranje kontrolne lozinke nije uspjelo");
define('LANG_successfully_changed_game_server', "Uspješno promijenjene postavke.");
define('LANG_error_ocurred_on_remote_server', "Došlo je do pogreške na udaljenom poslužitelju,");
define('LANG_ftp_password_can_not_be_changed', "FTP lozinka ne može se mijenjati.");
define('LANG_ftp_can_not_be_switched_on', "FTP se ne može uključiti.");
define('LANG_ftp_can_not_be_switched_off', "FTP se ne može isključiti.");
define('LANG_invalid_home_id_entered', "Unesen je nevažeći Home ID.");
define('LANG_ip_port_already_in_use', "%s:%s je već u upotrebi. Odaberite neki drugi.");
define('LANG_successfully_assigned_ip_port_to_server_id', "Uspješno dodijeljen %s:%s kod Home sa ID-om %s.");
define('LANG_no_ip_addresses_configured', "Vaš server nema nikakve IP adrese koje su mu konfigurirane. Možete ih konfigurirati iz");
define('LANG_server_page', "stranice servera");
define('LANG_successfully_removed_mod', "Uspješno uklonjen mod igre.");
define('LANG_warning_agent_offline_defaulting_CPU_count_to_1', "Upozorenje - Agent je izvan mreže, prebacivanje CPU-a na zadanom u 1.");
define('LANG_mod_install_cmds', "Inst.Mod po Naredbi");
define('LANG_cmds_for', "Naredbe za");
define('LANG_preinstall_cmds', "Predinstalirajte Naredbe");
define('LANG_postinstall_cmds', "Naredbe za Naknadnu Instalaciju");
define('LANG_edit_preinstall_cmds', "Uređivanje Naredbi za Predinstaliranje");
define('LANG_edit_postinstall_cmds', "Uređivanje naredbi za Naknadnu Instalaciju");
define('LANG_save_as_default_for_this_mod', "Spremi kao zadano za ovaj mod");
define('LANG_empty', "prazno");
define('LANG_master_server_for_clon_update', "Master Server za lokalno ažuriranje");
define('LANG_set_as_master_server', "Postaviti kao Master Server");
define('LANG_set_as_master_server_for_local_clon_update', "Postaviti kao master server za lokalno ažuriranje");
define('LANG_only_available_for', "Dostupno samo za '%s' za hosting na udaljenom poslužitelju nazvanom '%s'.");
define('LANG_ftp_on', "Omogućiti FTP");
define('LANG_ftp_off', "Onemogućiti FTP");
define('LANG_change_ftp_account_status', "Promjena statusa FTP računa");
define('LANG_change_ftp_account_status_info', "Jednom kada je FTP račun omogućen ili onemogućen, dodaje se ili uklanja iz FTP-ove baze podataka.");
define('LANG_server_ftp_login', "FTP Prijava za Server");
define('LANG_change_ftp_login_info', "Promijenite FTP prijavu s prilagođenim.");
define('LANG_change_ftp_login', "Promjeniti FTP Prijavu");
define('LANG_ftp_login_can_not_be_changed', "Nije moguće promijeniti FTP prijavu.");
define('LANG_server_is_running_change_addresses_not_available', "Dok je server pokrenut, IP se ne može mijenjati.");
define('LANG_change_game_type', "Promijeniti Vrstu Igre");
define('LANG_change_game_type_info', "Promjenom tipa igre, trenutna konfiguracija moda bit će izbrisana.");
define('LANG_force_mod_on_this_address', "Forsirajte mod na ovoj adresi");
define('LANG_successfully_assigned_mod_to_address', "Uspješno dodijeljen mod adresi");
define('LANG_switch_mods', "Prebaciti modove");
define('LANG_switch_mod_for_address', "Prebaci mod za adresu %s");
define('LANG_invalid_path', "Nevažeće putanje");
define('LANG_add_new_game_home', "Dodajte novu igru");
define('LANG_no_game_homes_found', "Nisu pronađene igre");
define('LANG_available_game_homes', "Dostupne igre");
define('LANG_home_id', "ID Servera");
define('LANG_game_server', "IP Servera");
define('LANG_game_type', "Vrsta Igre");
define('LANG_game_home', "Putanje Servera");
define('LANG_game_home_name', "Naziv Servera");
define('LANG_clone', "Klonirati");
define('LANG_unassign', "Ukloniti Server");
define('LANG_access_rights', "Prava pristupa");
define('LANG_assigned_homes', "Trenutni Dodijeljeni Serveri");
define('LANG_assign', "Dodijeliti");
define('LANG_allow_updates', "Dopustiti Ažuriranja Igre");
define('LANG_allow_updates_info', "Omogućuje korisniku ažuriranje instalacije igre ako je to moguće.");
define('LANG_allow_file_management', "Dopustiti Upravljanje Datotekama");
define('LANG_allow_file_management_info', "Omogućuje korisniku pristup serveru s modulima za upravljanje datotekama.");
define('LANG_allow_parameter_usage', "Dopustiti Upotrebu Parametara");
define('LANG_allow_parameter_usage_info', "Omogućuje korisniku promjenu dostupnih parametara naredbenog retka.");
define('LANG_allow_extra_params', "Dopustiti Dodatne Parametre");
define('LANG_allow_extra_params_info', "Korisniku omogućuje izmjenu dodatnih parametara naredbenog retka.");
define('LANG_allow_ftp', "Dopustiti FTP Upotrebu");
define('LANG_allow_ftp_info', "Pokažite informacije za FTP pristup korisniku.");
define('LANG_allow_custom_fields', "Dopustiti Prilagodljiva Polja");
define('LANG_allow_custom_fields_info', "Korisnicima omogućuje pristup prilagođenim poljima servera ako ih ima.");
define('LANG_select_home', "Odaberite Server za Dodjelivanje");
define('LANG_assign_new_home_to_user', "Dodijelivanje Novi Server korisniku %s");
define('LANG_assign_new_home_to_group', "Dodijelivanje Novi Server grupi %s");
define('LANG_assigned_home_to_user', "Uspješno dodijeljen server (ID: %d) korisniku %s.");
define('LANG_failed_to_assign_home_to_user', "Nije uspjelo dodijeljivanje servera (ID: %d) korisniku %s.");
define('LANG_assigned_home_to_group', "Uspješno dodijeljen server (ID: %d) grupi%s.");
define('LANG_unassigned_home_from_user', "Uspješno uklonjen server (ID: %d) od korisnika %s.");
define('LANG_unassigned_home_from_group', "Uspješno uklonjen server (ID: %d) od grupe %s.");
define('LANG_no_homes_assigned_to_user', "Nema dodijeljenih servera za korisnika %s.");
define('LANG_no_homes_assigned_to_group', "Nema dodijeljenih servera za grupu %s.");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_user', "Nema više dostupnih servera koji se mogu dodijeliti ovom korisniku");
define('LANG_no_more_homes_available_that_can_be_assigned_for_this_group', "Nema više dostupnih servera koji se mogu dodijeliti ovoj grupi");
define('LANG_you_can_add_a_new_game_server_from', "Možete dodati novu igru iz %s.");
define('LANG_no_remote_servers_available_please_add_at_least_one', "Nemamo dostupnih udaljenih poslužitelja, molimo dodajte najmanje jedan!");
define('LANG_cloning_of_home_failed', "Kloniranje servera sa ID-om '%s' nije uspjelo.");
define('LANG_no_mods_to_clone', "Još nema omogućenih mod(a) za ovu igru. Nije moguće klonirati.");
define('LANG_failed_to_add_mod', "Nije uspjelo dodavanje moda sa ID-om '%s'  serveru sa ID-om '%s'.");
define('LANG_failed_to_update_mod_settings', "Ažuriranje mod postavki za server sa ID-om '%s' nije uspjelo.");
define('LANG_successfully_cloned_mods', "Uspješno kloniran mod za server sa ID-om '%s'.");
define('LANG_successfully_copied_home_database', "Uspješno kopiran Direktorij baze podataka.");
define('LANG_copying_home_remotely', "Kopiranje Servera na udaljenom poslužitelju od '%s' na '%s'.");
define('LANG_cloning_home', "Kloniranje Servera pod nazivom '%s'.");
define('LANG_current_home_path', "Trenutni Direktorij Servera");
define('LANG_current_home_path_info', "Trenutni lokacija kopiranog Servera na udaljenom poslužitelju.");
define('LANG_clone_home', "Klonirati Server");
define('LANG_new_home_name', "Novi Naziv Servera");
define('LANG_new_home_path', "Novo putanje Servera");
define('LANG_agent_ip', "IP Agenta");
define('LANG_game_server_copy_is_running', "Kopiranje servera se izvodi ...");
define('LANG_game_server_copy_was_successful', "Kopiranje servera bilo je uspješno");
define('LANG_game_server_copy_failed_with_return_code', "Kopija servera nije uspjela s povratnim kodom %s");
define('LANG_clone_mods', "Klonirati Modove");
define('LANG_game_server_owner', "Vlasnik Servera");
define('LANG_the_name_of_the_server_to_help_users_to_identify_it', "Naziv servera koji korisnicima olakšava identifikaciju.");
define('LANG_ips_and_ports_used_in_this_home', "IP adrese i portovi koji se koriste u ovom serveru");
define('LANG_note_ips_and_ports_are_not_cloned', "Napomena - IP adrese i portovi neče bit klonirani");
define('LANG_mods_and_settings_for_this_game_server', "Modovi i postavke za ovaj server");
define('LANG_sure_to_delete_serverid_from_remoteip_and_directory', "Jeste li sigurni da želite izbrisati server (ID: %s) s poslužitelja %s i direktorij %s");
define('LANG_yes_and_delete_the_files', "Da i Izbriši datoteke");
define('LANG_failed_to_remove_gamehome_from_database', "Uklanjanje direktorij servera iz baze podataka nije uspjelo.");
define('LANG_successfully_deleted_game_server_with_id', "Uspješno izbrisan server sa ID-om %s.");
define('LANG_failed_to_remove_ftp_account_from_remote_server', "Neuspješno uklanjanje FTP računa s udaljenog poslužitelja.");
define('LANG_remove_it_anyway', "Želite li ga ipak ukloniti?");
define('LANG_sucessfully_deleted', "Uspješno izbrisan %s");
define('LANG_the_agent_had_a_problem_deleting', "Agent je imao problema prilikom brisanja %s. Molimo vas, provjerite zapisnik agenta.");
define('LANG_connection_timeout_or_problems_reaching_the_agent', "Vremenski prekid veze ili problemi s dosezanjem Agenta");
define('LANG_does_not_exist_yet', "Još ne postoji.");
define('LANG_update_settings', "Ažurirati postavke");
define('LANG_settings_updated', "Postavke su ažurirane");
define('LANG_selected_path_already_in_use', "Odabrano putanje je već u uporabi.");
define('LANG_browse', "Tražiti");
define('LANG_cancel', "Otkazati");
define('LANG_set_this_path', "Postaviti ovo putanje");
define('LANG_select_home_path', "Odaberite putanje ");
define('LANG_folder', "Mapa");
define('LANG_owner', "Vlasnik");
define('LANG_group', "Grupa");
define('LANG_level_up', "Viša razina");
define('LANG_level_up_info', "Vratiti se na prethodnu mapu.");
define('LANG_add_folder', "Dodati Mapu");
define('LANG_add_folder_info', "Napišite naziv nove mape, a zatim kliknite ikonu.");
define('LANG_valid_user', "Molimo vas navedite važećeg korisnika.");
define('LANG_valid_group', "Molimo vas navedite valjanu grupu.");
define('LANG_set_affinity', "Postavite CPU Servera");
define('LANG_cpu_affinity_info', "Odaberite jezgru CPU-(a) koju želite dodijeliti serveru.");
define('LANG_expiration_date_changed', "Datum isteka odabranog servera je promijenjen.");
define('LANG_expiration_date_could_not_be_changed', "Nije moguće mijenjati datum isteka odabranog servera.");
define('LANG_search', "Tražiti");
define('LANG_ftp_account_username_too_long', "FTP korisničko ime je predugo. Isprobajte kraće korisničko ime najviše 20 znakova.");
define('LANG_ftp_account_password_too_long', "FTP lozinka je predugačka. Isprobajte kraću lozinku koja nije duža od 20 znakova.");
define('LANG_other_servers_exist_with_path_please_change', "Drugi serveri postoje s istim putanjem. Preporuča se (ali ne i potreban) da ovo putanje promijenite u nešto jedinstveno. Možete imati problema ako to ne učinite.");
define('LANG_change_access_rights_for_selected_servers', "Promijenite prava pristupa za odabrane servere");
?>