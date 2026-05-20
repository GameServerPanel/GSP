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

define('LANG_create_alias', "Álnév és mappa létrehozása");
define('LANG_save_as', "Mentés mint");
define('LANG_failure', "Hiba, nem sikerült az álnév fájl generálása");
define('LANG_success', "Siker");
define('LANG_fast_download_service_for', "Letöltések átirányítási szolgáltatása a %s-hoz");
define('LANG_to_the_path', "Útvonalhoz");
define('LANG_at_url', "Webcímen");
define('LANG_create_alias_for', "Álnév létrehozása ehhez");
define('LANG_fast_dl', "Átirányított letöltések (FastDL)");
define('LANG_current_aliases_at_remote_server', "Jelenlegi álnevek a távoli szerveren");
define('LANG_delete_selected_aliases', "Kiválasztott álnevek törlése");
define('LANG_no_aliases_defined', "There are no web aliases defined by OGP for this remote server yet.");
define('LANG_fastdl_port', "Port");
define('LANG_fastdl_port_info', "A port, amelyen a gyors letöltési démonod el fog indulni.");
define('LANG_fastdl_ip', "Cím");
define('LANG_fastdl_ip_info', "Az IP cím vagy domain amin a Gyorsletöltési Démon szolgáltatás elindul. A domainek az '/etc/hosts' fájlban listázva kell lennie.");
define('LANG_listing', "Felsorolás");
define('LANG_listing_info', "Ha 'bekapcsolva', a szerver listázni fogja a mappák tartalmát.");
define('LANG_fast_dl_advanced', "További beállítások");
define('LANG_apply_settings_and_restart_fastdl', "Mentsd a démon konfigurációját és indítsd újra");
define('LANG_stop_fastdl', "Gyors letöltési démon leállítása");
define('LANG_fast_download_daemon_running', "A gyors letöltés démon fut.");
define('LANG_fast_download_daemon_not_running', "A gyors letöltés démon nem fut.");
define('LANG_fastdl_could_not_be_restarted', "A gyors letöltés szolgáltatást nem lehet újraindítani.");
define('LANG_configuration_file_could_not_be_written', "A konfigurációs fájl nem írható.");
define('LANG_remove_folders', "Mappa eltávolítása a kiválasztott álnevektől.");
define('LANG_remove_folder', "Mappa eltávolítása");
define('LANG_delete_alias', "Álnév törlése");
define('LANG_no_game_homes_assigned', "Nincsenek szerverek hozzárendelve a fiókodhoz.");
define('LANG_select_remote_server', "Válassz távoli szervert");
define('LANG_access_rules', "Hozzáférési szabályok");
define('LANG_create_aliases', "Álnevek létrehozása");
define('LANG_select_game', "Válassz játékot");
define('LANG_games_without_specified_rules', "Játékok speciális szabályok nélkül.");
define('LANG_match_file_extension', "Fájl kiterjesztés párosítás");
define('LANG_match_file_extension_info', "Kiterjesztések meghatározása vesszővel elválasztva,<br>a megfelelő fájlok hozzáférhetőek lesznek.<br><b>Hagyd üresen a korlátlan hozzáféréshez</ b>.");
define('LANG_match_client_ip', "Kliens IP párosítás");
define('LANG_match_client_ip_info', "Connections with matching IP will be granted,<br>blank for unrestricted access. You can use<br>multiple IPs or ranges separated by coma:<br>/xx subnets<br>Example: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx subnets<br>Example: 10.0.0.0/255.0.0.0<br>Hyphen ranges<br>Example: 10.0.0.5-230<br>Asterisk matching<br>Example: 10.0.*.*");
define('LANG_save_access_rules', "Hozzáférési szabályok mentése");
define('LANG_create_access_rules', "Hozzáférési szabályok létrehozása");
define('LANG_invalid_entries_found', "Érvénytelen bejegyzés található");
define('LANG_game_name', "Játék neve");
define('LANG_alias_already_exists', "A(z) %s álnév már létezik.");
define('LANG_warning_access_rules_applied_once_alias_created', "WARNING: Access rules are applied when the alias is created. No changes will be applied to the current aliases.");
define('LANG_autostart_on_agent_startup', "Automata indítás az Agent elindulásakor");
define('LANG_autostart_on_agent_startup_info', "A gyors letöltési démon automatikus elindítása, amikor az Agent elindul.");
define('LANG_port_forwarded_to_80', "Port továbbítása 80-ról");
define('LANG_port_forwarded_to_80_info', "Enable this option if the port configured for this fast download daemon has been forwarded from port 80, so the port will be hidden at URLs.");
define('LANG_current_access_rules', "Jelenlegi hozzáférési szabályok");
?>