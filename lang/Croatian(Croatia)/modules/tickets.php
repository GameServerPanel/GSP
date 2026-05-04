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

define('LANG_support_tickets', "Korisnička Podrška");
define('LANG_ticket_subject', "Predmet");
define('LANG_ticket_status', "Status");
define('LANG_ticket_updated', "Zadnje Ažurirano");
define('LANG_ticket_options', "Opcije");
define('LANG_viewing_ticket', "Pregledavanje Upita");
define('LANG_ticket_not_found', "Parametri upita ne odgovaraju postojećem upitu.");
define('LANG_ticket_cant_read', "Nedovoljna dozvola za pregledavanje upita.");
define('LANG_cant_view_ticket', "Nije moguće dohvatiti podatke o upitu.");
define('LANG_ticket_id', "ID Upita");
define('LANG_service_id', "ID Usluge");
define('LANG_ticket_submitted', "Upit je poslan");
define('LANG_submitter_info', "Podaci o podnositelju");
define('LANG_name', "Ime");
define('LANG_ip', "IP Adresa");
define('LANG_role', "Korisnička uloga");
define('LANG_ticket_submit_response', "Pošaljite odgovor");
define('LANG_ticket_close', "Zatvoriti");
define('LANG_no_ticket_replies', "Nema odgovoreni upita");
define('LANG_no_tickets_submitted', "Nema poslani upita");
define('LANG_submit_ticket', "Pošalji Upit");
define('LANG_ticket_service', "Usluga");
define('LANG_ticket_message', "Poruka");
define('LANG_ticket_errors_occured', "Sljedeće su se pogreške pojavile prilikom slanja vašeg upita");
define('LANG_no_ticket_subject', "Upit Bet Predmeta");
define('LANG_invalid_ticket_subject_length', "Nevažeća dužina predmeta (4 do 64 znaka)");
define('LANG_invalid_home_selected', "Nevažeći odabrani Home");
define('LANG_no_ticket_message', "Nema predbilježbe za upit");
define('LANG_invalid_ticket_message_length', "Nevažeća duljina poruke (minimalno 4 znaka)");
define('LANG_ticket_no_service', "Nijedna usluga nije odabrana za ovaj upit.");
define('LANG_failed_to_open', "Otvaranje upita nije uspjelo.");
define('LANG_failed_to_reply', "Izrada odgovora na upit nije uspjela.");
define('LANG_no_ticket_reply', "Ne postoji odgovor na upit");
define('LANG_invalid_ticket_reply_length', "Nevažeća duljina odgovora za upit (minimalno 4 znaka)");
define('LANG_ticket_closed', "Upit Zaključan");
define('LANG_ticket_open', "Upit Otvoren");
define('LANG_ticket_admin_response', "Odgovor Tehničke Podrške");
define('LANG_ticket_customer_response', "Odgovor Korisnika");
define('LANG_ticket_invalid_page_num', "Pokušali ste pregledati broj stranice bez upita!");
define('LANG_ticket_is_closed', "Ovaj upit je zatvoren. Možete odgovoriti na ovaj upit kako biste ponovno otvorili.");
define('LANG_reply', "Odgovoriti");
define('LANG_invalid_rating', "Primljena ocjena nije valjana.");
define('LANG_successfully_rated_response', "Uspješno ocijenjen odgovor.");
define('LANG_failed_rating_response', "Nije uspjelo ocijeniti odgovor.");
define('LANG_attachment_not_all_parameters_sent', "Nisu poslani svi parametri za preuzimanje datoteke.");
define('LANG_requested_attachment_missing', "Zatraženi privitak ne postoji.");
define('LANG_requested_attachment_missing_db', "Zatraženi privitak ne postoji u bazi podataka.");
define('LANG_ratings_disabled', "Ociienjivanje odgovora nije omogućeno.");
define('LANG_attachments', "Privitci");
define('LANG_add_file_attachment', "Dodati Više");
define('LANG_attachment_size_info', "Svaka odabrana datoteka mora biti maksimalno do %s");
define('LANG_attachment_file_size_info', "Najviše %s datoteka(e) mogu biti učitane, %ssvaka.");
define('LANG_attachment_allowed_extensions_info', "Dopušteno proširenje datoteke: %s");
define('LANG_ticket_fix_before_submitting', "Ispravite sljedeće pogreške prije slanja upita");
define('LANG_ticket_fix_before_replying', "Ispravite sljedeće pogreške prije nego što odgovorite na upit");
define('LANG_ticket_problem_with_attachments', "Došlo je do problema s datotekom(ama) koje ste priložili");
define('LANG_ticket_attachment_invalid_extension', "% 1 ne sadrži dopušteno proširenje.");
define('LANG_ticket_attachment_invalid_size', "% 1 je veći od dopuštene veličine datoteke. % 2 maksimalno!");
define('LANG_ticket_max_file_elements', "Može postojati samo najviše 1% ulaznih datoteka.");
define('LANG_ticket_attachment_multiple_files', "Jedan ili više ulaznih datoteka ima više datoteka odabranih.");
define('LANG_attachment_err_ini_size', "%s (%s) premašuje postavku \"upload_max_filesize\".");
define('LANG_attachment_err_partial', "%s je djelomično prenesen.");
define('LANG_attachment_err_no_tmp', "Nema tmp mape za spremanje %s");
define('LANG_attachment_err_cant_write', "Nije moguće zapisati %s na disku.");
define('LANG_attachment_err_extension', "Proširenje je zaustavilo prijenos %s. Pregledajte svoje zapisnike.");
define('LANG_attachment_too_large', "%s (%s) je veća od maksimalne dopuštene veličine od %s!");
define('LANG_attachment_forbidden_type', "Vrsta datoteke %s ne može se učitati.");
define('LANG_attachment_directory_not_writable', "Nije moguće spremiti priložene datoteke. Navedeni direktorij za spremanje nije moguće pisati.");
define('LANG_attachment_invalid_file_count', "Količina datoteka poslana poslužitelju bila je nevažeća. Može se prenijeti najviše %s");
define('LANG_ratings_enabled', "Ocijene");
define('LANG_ratings_enabled_info', "Postavite dali bi trebalo biti dopušteno ocijeniti odgovore.");
define('LANG_attachments_enabled', "Privitci");
define('LANG_attachments_enabled_info', "Postavite dali sustav privitka treba biti omogućen.");
define('LANG_attachment_max_size', "Maksimalna Veličina Datoteke");
define('LANG_attachment_max_size_info', "Postavite maksimalnu veličinu za privitke.");
define('LANG_attachment_limit', "Ograničenje privitka");
define('LANG_attachment_limit_info', "Odrediti koliko datoteka može biti dodano odjednom. 0 bez ograničenja.");
define('LANG_attachment_save_dir', "Lokacija Prijenosa Privitka");
define('LANG_attachment_save_dir_info', "Postavlja mjesta na koje treba prenijeti privitke. U svakom slučaju, izvan mape public_html ili izravnog pristupa blokiran je.");
define('LANG_attachment_extensions', "Proširenja privitaka");
define('LANG_attachment_extensions_info', "Postavlja dopuštena proširenja. Svako proširenje treba odvojiti zarezom.");
define('LANG_show_php_ini', "Prikaži Procijenjene INI Postavke");
define('LANG_settings_errors_occured', "Sljedeće su se pogreške pojavile prilikom pokušaja ažuriranja postavki - nije sve kompletno ažurirano!");
define('LANG_invalid_max_size', "Nevažeća vrijednost za postavku Maksimalna veličina.");
define('LANG_invalid_unit', "Nevažeća vrsta jedinice za postavku Maksimalna veličina. Očekujući KB, MB, GB, TB ili PB.");
define('LANG_invalid_save_dir', "Navedeni direktorij za spremanje ne postoji i ne može se izraditi.");
define('LANG_invalid_save_dir_not_writable', "Navedeni direktorij za spremanje postoji ali nije moguće pisati.");
define('LANG_invalid_extensions', "Nije navedeno proširenje privitaka.");
define('LANG_update_settings', "Ažurirati postavke");
define('LANG_notifications_enabled', "Obavijesti");
define('LANG_notifications_enabled_info', "Dopustite korisniku/administratoru da vidi je li dobio upit koji čeka odgovor.");
