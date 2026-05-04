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

define('LANG_maintenance_mode', "Održavanje");
define('LANG_maintenance_mode_info', "Onemogućite Panel za obične korisnike. Samo administratori mogu pristupiti tijekom održavanja.");
define('LANG_maintenance_title', "Naslov održavanja");
define('LANG_maintenance_title_info', "Naslov koji se običnim korisnicima prikazuje tijekom održavanja.");
define('LANG_maintenance_message', "Poruka o održavanju");
define('LANG_maintenance_message_info', "Poruka koja se prikazuje običnim korisnicima tijekom održavanja.");
define('LANG_update_settings', "Ažurirati postavke");
define('LANG_settings_updated', "Postavke su uspješno ažurirane.");
define('LANG_panel_language', "Jezik Panela");
define('LANG_panel_language_info', "Ovaj jezik je zadani jezik Panela. Korisnici mogu promijeniti svoj jezik na stranici za uređivanje profila.");
define('LANG_page_auto_refresh', "Automatsko Osvježavanje Stranice");
define('LANG_page_auto_refresh_info', "Postavke automatskog osvježavanja stranice uglavnom se upotrebljavaju u debugu ploče. U normalnoj upotrebi to bi trebalo biti postavljeno na Uključeno.");
define('LANG_smtp_server', "Poslužitelj za odlazne e-pošte");
define('LANG_smtp_server_info', "Ovo je poslužitelj odlazne pošte (SMTP poslužitelj) koji se koristi, na primjer, za slanje zaboravljenih zaporki korisnicima, lokalni host prema zadanim postavkama.");
define('LANG_panel_email_address', "Odlazna e-pošta");
define('LANG_panel_email_address_info', "Ovo je adresa e-pošte koja je u polju kada se lozinke šalju korisnicima.");
define('LANG_panel_name', "Naziv Panela");
define('LANG_panel_name_info', "Naziv Panela prikazanog u naslovu stranice. Ova će vrijednost odbiti sve naslove stranica, ako nije prazna.");
define('LANG_feed_enable', "Omogući LGSL feed");
define('LANG_feed_enable_info', "Ako vaš webhost ima vatrozid koji blokira port upita, onda ručno ga morate otvoriti port.");
define('LANG_feed_url', "URL feeda");
define('LANG_feed_url_info', "GrayCube.com dijeli LGSL feed na URL:<br><b>http://www.greycube.co.uk/lgsl/feed/lgsl_files/lgsl_feed.php</b>");
define('LANG_charset', "Kodiranje znakova");
define('LANG_charset_info', "UTF8, ISO, ASCII, itd...Prebacuje kodiranje znakova definiranog jezičnim datotekama. Ostavite ga prazno da biste koristili zadanu postavku jezika.");
define('LANG_steam_user', "Steam Korisnik");
define('LANG_steam_user_info', "Ovaj korisnik je potreban za prijavu u Steam-u za preuzimanje nekih novih igara poput CS: GO.");
define('LANG_steam_pass', "Steam Lozinka");
define('LANG_steam_pass_info', "Ovdje napišite lozinku za Steam račun.");
define('LANG_steam_guard', "Steam Guard");
define('LANG_steam_guard_info', "Neki korisnici imaju aktivirano Steam Guard kako bi zaštitili svoje račune od hakera,<br>ovaj kôd šalje se e-pošti računa kada započne prvo ažuriranje Steam-a.");
define('LANG_smtp_port', "SMTP Port");
define('LANG_smtp_port_info', "Ako SMTP port nije zadani port (25) Ovdje unesite SMTP port.");
define('LANG_smtp_login', "SMTP Korisnik");
define('LANG_smtp_login_info', "Ako vaš SMTP poslužitelj zahtijeva provjeru autentičnosti, ovdje unesite korisničko ime.");
define('LANG_smtp_passw', "SMTP Lozinka");
define('LANG_smtp_passw_info', "Ako ne postavite lozinku, SMTP autentikacija će biti onemogućena.");
define('LANG_smtp_secure', "SMTP Sigurnost");
define('LANG_smtp_secure_info', "Koristite SSL/TLS za povezivanje sa SMTP poslužiteljem");
define('LANG_time_zone', "Vremenska Zona");
define('LANG_time_zone_info', "Postavlja zadanu vremensku zonu koju upotrebljavaju sve funkcije datuma vremena.");
define('LANG_query_cache_life', "Život predmemorije");
define('LANG_query_cache_life_info', "Postavlja vremensko ograničenje u sekundama prije osvježavanja statusa poslužitelja.");
define('LANG_query_num_servers_stop', "Onemogući upite igara nakon");
define('LANG_query_num_servers_stop_info', "Pomoću ove postavke onemogućite upite ako korisnik posjeduje više igrica od navedenog iznosa kako bi se ubrzao učitavanje panela.");
define('LANG_editable_email', "E-pošta za uređivanje");
define('LANG_editable_email_info', "Odaberite ako korisnici mogu urediti svoju adresu e-pošte ili ne.");
define('LANG_old_dashboard_behavior', "Prikaz stare verzije Nadzorne Ploče");
define('LANG_old_dashboard_behavior_info', "Stara nadzorna ploča je dosta sporija, ali prikazuje više informacija o serveru (npr. Trenutni igrači i karte).");
define('LANG_rsync_available', "Dostupni Rsync poslužitelji");
define('LANG_rsync_available_info', "Odaberite koji će poslužitelj biti prikazan u rsync instalaciji.");
define('LANG_all_available_servers', "Svi dostupni poslužitelji ( rsync_sites.list + rsync_sites_local.list )");
define('LANG_only_remote_servers', "Samo udaljeni poslužitelji ( rsync_sites.list )");
define('LANG_only_local_servers', "Samo lokalni poslužitelji (rsync_sites_local.list )");
define('LANG_header_code', "Šifra zaglavlja");
define('LANG_header_code_info', "Ovdje možete napisati vlastiti kôd zaglavlja (poput HTML koda, Embed Code itd.) Bez uređivanja izgleda tema.");
define('LANG_support_widget_title', "Naslov widgeta korisničke podrške");
define('LANG_support_widget_title_info', "Prilagođeni naslov widgeta za podršku na nadzornoj ploči.");
define('LANG_support_widget_content', "Sadržaj  widgeta za Podršku");
define('LANG_support_widget_content_info', "Sadržaj widgeta za podršku (dopušten HTML kôd).");
define('LANG_support_widget_link', "Poveznica widgeta za Podršku");
define('LANG_support_widget_link_info', "URL vaše stranice za podršku.");
define('LANG_recaptcha_site_key', "Ključ Recaptcha Stranice");
define('LANG_recaptcha_site_key_info', "Ključ stranice koji vam je Google pružio.");
define('LANG_recaptcha_secret_key', "Recaptcha Tajni Ključ");
define('LANG_recaptcha_secret_key_info', "Tajni ključ koji vam Google pruža.");
define('LANG_recaptcha_use_login', "Upotrijebiti Recaptcha kada se korisnici prijave");
define('LANG_recaptcha_use_login_info', "Ako je omogućeno, korisnici će morati riješiti Nisam Robot Recaptcha prilikom pokušaja prijave.");
define('LANG_login_attempts_before_banned', "Broj neuspjelih pokušaja prijave prije nego što korisnik dobije zabranu");
define('LANG_login_attempts_before_banned_info', "Ako se korisnik više puta pokuša prijaviti s nevažećim detaljima za prijavu, Panel će privremeno zabraniti korisnika.");
define('LANG_custom_github_update_username', "Korisničko Ime za GitHub ažuriranje");
define('LANG_custom_github_update_username_info', "Unesite GitHub korisničko ime SAMO za korištenje vlastitih repozitoriji za ažuriranje OGP-a. To bi trebalo mijenjati samo razvojni programeri koji žele koristiti vlastiti repozitorij za razvoj, umjesto da provjere eventualno pogrešan kod u glavnu granu.");
define('LANG_remote_query', "Udaljeni upit");
define('LANG_remote_query_info', "Upotrijebite udaljeni poslužitelj (agent) za upite na igre (samo GameQ i LGSL).");
define('LANG_check_expiry_by', "Provjerite istek");
define('LANG_check_expiry_by_info', "Ako je postavljeno na once_logged_in, igre koje su dodjeljenje korisnicma automatski će se izbrisati nakon isteka datuma. Ako je postavljeno na cron_job, trebat ćete napraviti cron zadatak pomoću cron modula kako biste provjerili datum isteka u konfiguriranom intervalu.");
define('LANG_once_logged_in', "Kada se prijavi");
define('LANG_cron_job', "Cron Zadatak");
define('LANG_theme_settings', "Postavke teme");
define('LANG_theme', "Tema");
define('LANG_theme_info', "Tema odabrana ovdje bit će zadana tema za sve korisnike. Korisnici mogu promijeniti temu s njihove stranice profila.");
define('LANG_welcome_title', "Naslov Dobrodošlice");
define('LANG_welcome_title_info', "Omogućuje naslov koji se prikazuje na vrhu Nadzorne ploče.");
define('LANG_welcome_title_message', "Poruka za Naslov Dobrodošlice");
define('LANG_welcome_title_message_info', "Naslovna poruka koja se prikazuje na vrhu Nadzorne ploče (dopušteni HTML kôd).");
define('LANG_logo_link', "Link za Logotip");
define('LANG_logo_link_info', "Hyperlink logotipa. <b style='font-size:10px; font-weight:normal;'>(Ostavljajući ga prazno povezat će ga s nadzornom pločom)</b>");
define('LANG_custom_tab', "Prilagođena kartica");
define('LANG_custom_tab_info', "Na kraju izbornika dodaje se prilagodljiva kartica. <b style='font-size:10px; font-weight:normal;'>(Primijeni i osvježite ovu stranicu da biste uredili postavke kartica)</b>");
define('LANG_custom_tab_name', "Naziv Prilagođene Kartice");
define('LANG_custom_tab_name_info', "Naziv koji če se prikazati za priagođene kartice.");
define('LANG_custom_tab_link', "Poveznica Prilagođene Kartice");
define('LANG_custom_tab_link_info', "Hiperlinkovi kartica.");
define('LANG_custom_tab_sub', "Prilagođene podkartice");
define('LANG_custom_tab_sub_info', "Dodavanje prilagodljivih podkartica ispod \"Prilagođene kartice\".");
define('LANG_custom_tab_sub_name', "Podkartica #1 Naziv");
define('LANG_custom_tab_sub_link', "Podkartica #1 Poveznica");
define('LANG_custom_tab_sub_name2', "Podkartica #2 Naziv");
define('LANG_custom_tab_sub_link2', "Podkartica #2 Poveznica");
define('LANG_custom_tab_sub_name3', "Podkartica #3 Naziv");
define('LANG_custom_tab_sub_link3', "Podkartica #3 Poveznica");
define('LANG_custom_tab_sub_name4', "Podkartica #4 Naziv");
define('LANG_custom_tab_sub_link4', "Podkartica #4 Poveznica");
define('LANG_custom_tab_target_blank', "Opcija Otvaranja Prilagođene Kartice");
define('LANG_custom_tab_target_blank_info', "Opcija otvaranja prilagođene stranice. <b style='font-size:10px; font-weight:normal;'>(Ista_Stranica = otvara vezu na istoj stranici. Nova_Stranica = otvara vezu na novoj kartici.)</b>");
define('LANG_bg_wrapper', "Pozadinska slika Wrappera");
define('LANG_bg_wrapper_info', "Pozadinska slika wrappera. <b style='font-size:10px; font-weight:normal;'>(Dostupno samo za neke teme.)</b>");
define('LANG_show_server_id_game_monitor', "Prikaži ID servera na stranici Monitor igara");
define('LANG_show_server_id_game_monitor_info', "Pokažite stupac ID servera na Monitor Igara za podudaranje datoteka stvorenih od strane Agenta na aktualnom serveru.");
define('LANG_default_game_server_home_path_prefix', "Zadani prefiks Home direktorija za server");
define('LANG_default_game_server_home_path_prefix_info', "Unesite prefiks putanja za mjesto na kojem želite da Home direktoriji servera budu izrađeni prema zadanim postavkama. Možete upotrebljavati \"{USERNAME}\" na putu koji će biti zamijenjen korisničkim imenom OGP-a kojem se dodjeljuje server. Možete koristiti \"{GAMEKEY}\" na putu koji će biti zamijenjen imenom sa malim slovima. Možete upotrebljavati \"{SKIPID}\" bilo gdje na putu da preskočite dodavanje Home ID-a na putnju. Primjer: /ogp/games/{USERNAME}/{GAMEKEY}{SKIPID} će postati /ogp/games/username/arkse/. Primjer 2: /ogp/games će postati /ogp/games/1 gdje je 1 ID servera.");
define('LANG_use_authorized_hosts', "Ograničite API na Definirane ovlaštene hostove");
define('LANG_use_authorized_hosts_info', "Omogućite ovu postavku da biste dozvolili samo API pozive s unaprijed definiranih i odobrenih IP adresa.&nbsp; Odobrene adrese možete postaviti na ovoj stranici nakon što je postavka omogućena.&nbsp; Ako je ova postavka onemogućena, korisnik koji koristi važeći ključ imat će pristup API-ju s bilo koje IP adrese.&nbsp; Korisnici koji koriste valjani ključ moći će koristiti API za upravljanje bilo kojim poslužiteljem za igre koje imaju dozvole za administriranje.");
define('LANG_setup_api_authorized_hosts', "Postavke API za autorizaciju poslužitelja");
define('LANG_autohorized_hosts', "Ovlašteni poslužitelji");
define('LANG_add', "Dodati");
define('LANG_remove', "Ukloniti");
define('LANG_default_trusted_hosts', "Zadani Pouzdani Poslužitelji");
define('LANG_trusted_host_or_proxy_addresses_or_cidr', "Pouzdani Poslužitelji ili Proxy (IPv4/IPv6 Adrese ili CIDR)");
define('LANG_trusted_forwarded_ip_addresses_or_cidr', "Pouzdani prosljeđeni IP-ovi (IPv4/IPv6 Addresses or CIDR)");
define('LANG_reset_game_server_order', "Resetiranje redoslijeda poslužitelja igara");
define('LANG_reset_game_server_order_info', "Poništava naredbu redoslijeda igara na zadane postavke ID poslužitelja");


?>
