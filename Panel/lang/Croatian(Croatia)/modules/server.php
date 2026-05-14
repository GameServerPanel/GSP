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

define('LANG_add_new_remote_host', "Dodajte Novi Udaljeni Poslužitelj");
define('LANG_configured_remote_hosts', "Konfigurirani Udaljeni Poslužitelj");
define('LANG_remote_host', "Udaljeni Poslužitelj");
define('LANG_remote_host_info', "Udaljeni poslužitelj mora imati odgovarajući naziv poslužitelja!");
define('LANG_remote_host_port', "Port Udaljenog Poslužitelja");
define('LANG_remote_host_port_info', "Port od strane OGP Agenta na udaljenom poslužitelju. Zadano:12679");
define('LANG_remote_host_name', "Naziv Udaljenog Poslužitelja");
define('LANG_ogp_user', "Korisničko Ime OGP Agenta");
define('LANG_remote_host_name_info', "Naziv udaljenog poslužitelja koristi se za pomoć korisnicima da identificiraju svoje poslužitelje.");
define('LANG_add_remote_host', "Dodajte Udaljeni Poslužitelj");
define('LANG_remote_encryption_key', "Udaljeni Ključ za Enkripciju");
define('LANG_remote_encryption_key_info', "Udaljeni ključ za enkripciju koristi se za enkripciju podataka između Panela i Agenta. Taj ključ mora biti isti na obje strane.");
define('LANG_server_name', "Naziv Poslužitelja");
define('LANG_agent_ip_port', "IP:Port Agenta");
define('LANG_agent_status', "Status Agenta");
define('LANG_ips', "IP adrese");
define('LANG_add_more_ips', "Ako želite unijeti više IP adresa, pritisnite 'Postaviti IPe' kada su sva polja puna i pojavit će se prazno polje.");
define('LANG_encryption_key_mismatch', "Ključ za enkripciju ne podudara se sa Agentom. Molimo provjerite konfiguracije Agenta");
define('LANG_no_ip_for_remote_host', "Morate dodati barem jednu (1) IP adresu za svaki udaljeni poslužitelj.");
define('LANG_note_remote_host', "Udaljenj poslužitelj je server na kojem se pokreće Agent OGPa. Svaki poslužitelj može imati više IP adresa na kojima korisnici mogu vezati servere.");
define('LANG_ip_administration', "Server &amp; IP Administracija :: Open Game Panel");
define('LANG_unknown_error', "Nepoznata pogreška - status_chk odbijeno");
define('LANG_remote_host_user_name', "UNIX korisnik");
define('LANG_remote_host_user_name_info', "Korisničko Ime odakle je Agent pokrenut. Primjer: Ivan");
define('LANG_remote_host_ftp_ip', "FTP IP");
define('LANG_remote_host_ftp_ip_info', "FTP <b>IP</b> poslužitelja za trenutni Agent.");
define('LANG_remote_host_ftp_port', "FTP Port");
define('LANG_remote_host_ftp_port_info', "FTP <b>port</b> poslužitelja za trenutni Agent.");
define('LANG_view_log', "Vidjeti Zapisnik");
define('LANG_status', "Status");
define('LANG_stop_firewall', "Zaustaviti Vatrozid");
define('LANG_start_firewall', "Pokrenuti Vatrozid");
define('LANG_seconds', "Sekunde");
define('LANG_reboot', "Ponovno pokretanje  Poslužitelja");
define('LANG_restart', "Ponovno pokretanje Agenta");
define('LANG_confirm_reboot', "Jeste li sigurni da želite ponovno pokrenuti fizički kompletni poslužitelj pod nazivom '%s'?");
define('LANG_confirm_restart', "Jeste li sigurno da želite ponovno pokrenuti Agent pod nazivom '%s'?");
define('LANG_restarting', "Ponovno pokretanje Agenta...Molimo pričekajte");
define('LANG_restarted', "Agent je uspješno ponovno pokrenut.");
define('LANG_reboot_success', "Poslužitelj s nazivom '%s' uspješno je ponovno pokrenut. Nećete moći pristupiti poslužitelju dok se kompletno ne pokrene.");
define('LANG_invalid_remote_host_id', "Nevažeći ID udaljenog posužitelja '%s'.");
define('LANG_remote_host_removed', "Udaljeni poslužitelj pod nazivom '%s' uspješno uklonjen.");
define('LANG_editing_remote_server', "Uređivanje udaljenog poslužitelja pod nazivom '%s'.");
define('LANG_remote_server_settings_changed', "Uspješno su promjenjene postavke udaljenog servera '%s'.");
define('LANG_save_settings', "Spremiti Postavke");
define('LANG_set_ips', "Postaviti IP adrese");
define('LANG_remote_ip', "IP Poslužitelja");
define('LANG_remote_ips_for', "IP-ovi za servere koje ćete koristiti na poslužitelju agenta '%s'");
define('LANG_ips_set_for_server', "Uspješno su postavljeni IP adrese za poslužitelja pod nazivom '%s'.");
define('LANG_could_not_remove_ip', "Nije moguće ukloniti stare IP adrese od baze podataka.");
define('LANG_could_add_ip', "Mogli bi dodati IP poslužitelja u bazu podataka.");
define('LANG_areyousure_removeagent', "Jeste li sigurni da želite ukloniti Agent pod nazivom");
define('LANG_areyousure_removeagent2', "i sve mape povezane s njim iz OGP baze podataka?");
define('LANG_error_while_remove', "Došlo je do pogreške prilikom uklanjanja udaljenog poslužitelja.");
define('LANG_add_ip', "Dodati IP");
define('LANG_remove_ip', "Ukloniti IP");
define('LANG_edit_ip', "Urediti IP");
define('LANG_wrote_changes', "Promjene uspješno sačuvane.");
define('LANG_there_are_servers_running_on_this_ip', "Na toj IP adresi postoje poslužitelji koji su več pokrenuti.");
define('LANG_enter_ip_host', "Morate upisati IP za udaljeni poslužitelj.");
define('LANG_enter_valid_ip', "Morate unijeti valjani port za udaljeni poslužitelj. Vrijednost porta može biti između 0 i 65535, no preporuka je između 1024 i 65535.");
define('LANG_could_not_add_server', "Nije moguće dodati poslužitelj");
define('LANG_to_db', "u bazi podataka.");
define('LANG_added_server', "Dodani poslužitelj");
define('LANG_with_port', "sa portom");
define('LANG_to_db_succesfully', "u bazi podataka uspješno.");
define('LANG_unable_discover', "Nije moguće automatski otkriti IP adrese");
define('LANG_set_ip_manually', "Morat ćete ih postaviti ručno.");
define('LANG_found_ips', "Pronađene IP adrese");
define('LANG_for_remote_server', "za udaljeni poslužitelj.");
define('LANG_failed_add_ip', "Dodavanje IP adrese nije uspjelo");
define('LANG_timeout', "Isteklo Vrijeme");
define('LANG_timeout_info', "Vremensko ograničenje u sekundama za dobivanje odgovora od ovog Agenta.");
define('LANG_use_nat', "Koristi NAT");
define('LANG_use_nat_info', "Omogućite ako vaš udaljeni poslužitelj koristi NAT pravila. Koristite ovu postavku ako se vaši serveri izvode na internim IP adresama privatnog LAN-a, tako da će panel koristiti vašu stvarnu udaljenu IP adresu za upite servera.");
define('LANG_arrange_ports', "Rasporediti portove");
define('LANG_assign_new_ports_range_for_ip', "Dodijelite novih raspon portova za IP %s");
define('LANG_assigned_port_ranges_for_ip', "Dodijeljeni raspon portovi za IP %s");
define('LANG_assigned_ports_for_ip', "Dodjeljeni portovi za IP %s");
define('LANG_unspecified_game_types', "Neodređene vrste igara");
define('LANG_start_port', "Početni port:");
define('LANG_end_port', "Završni port:");
define('LANG_port_increment', "Port povečanje:");
define('LANG_total_assignable_ports', "Ukupni broj priključnih portova");
define('LANG_available_range_ports', "Dostupni raspon portovi:");
define('LANG_assign_range', "Dodijeliti raspon");
define('LANG_edit_range', "Urediti raspon");
define('LANG_delete_range', "Izbrisati raspon");
define('LANG_home_id', "Home ID");
define('LANG_home_path', "Putanje direktorija");
define('LANG_game_type', "Vrsta igre");
define('LANG_port', "Port");
define('LANG_invalid_values', "Nevažeće vrijednosti");
define('LANG_ports_in_range_already_arranged', "Raspon portova već raspoređen.");
define('LANG_ports_range_already_configured_for', "Raspon portova već konfiguriran za %s.");
define('LANG_ports_range_added_successfull_for', "Uspješno dodan raspon portova za %s.");
define('LANG_ports_range_deleted_successfull', "Raspon portova uspješno je izbrisan.");
define('LANG_ports_range_edited_successfull_for', "Raspon portova je uspješno uređen za %s.");
define('LANG_editing_firewall_for_remote_server', "Uređivanje vatrozida za udaljenog poslužitelja pod nazivom '%s'");
define('LANG_default_allowed', "Dopušteno je po zadanom");
define('LANG_allow_port_command', "Dopusti naredbu porta");
define('LANG_deny_port_command', "Odbij naredbu porta");
define('LANG_allow_ip_port_command', "Dopusti IP:port naredbu");
define('LANG_deny_ip_port_command', "Odbij IP:port naredbu");
define('LANG_enable_firewall_command', "Omogući naredbu za vatrozid");
define('LANG_disable_firewall_command', "Onemogući naredbu za vatrozid");
define('LANG_get_firewall_status_command', "Nabavite status naredbe vatrozida");
define('LANG_reset_firewall_command', "Ponovo postavite naredbu vatrozida");
define('LANG_firewall_status', "Status vatrozida");
define('LANG_save_firewall_settings', "Spremiti postavke za vatrozid");
define('LANG_reset_firewall', "Ponovo postavite vatrozid");
define('LANG_firewall_settings', "Vatrozid Postavke");
define('LANG_display_public_ip', "Prikazati Javnu IP Adresu");
define('LANG_ips_can_be_internal_external', "Unesite upotrebljive IP adrese.&nbsp; Mogu se koristiti javne IP adrese i unutarnje LAN IP adrese (za NAT postavke).");
?>
