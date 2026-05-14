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

include('litefm.php');
define('LANG_curl_needed', "Ta strona wymaga modułu PHP Curl.");
define('LANG_no_access', "Potrzebujesz uprawnień administratora, aby uzyskać dostęp do tej strony.");
define('LANG_dwl_update', "Pobieranie Aktualizacji...");
define('LANG_dwl_complete', "Pobieranie zakończone.");
define('LANG_install_update', "Instalowanie aktualizacji...");
define('LANG_update_complete', "Aktualizacja zakonczona.");
define('LANG_ignored_files', "%s ignored file(s)");
define('LANG_not_updated_files_blacklisted', "Not updated/installed files (blacklisted):<br>%s");
define('LANG_latest_version', "Najnowsza wersja");
define('LANG_panel_version', "Wersja panelu");
define('LANG_update_now', "Aktualizuj Teraz");
define('LANG_the_panel_is_up_to_date', "Panel jest Aktualny.");
define('LANG_files_overwritten', "%s nadpisane pliki");
define('LANG_files_not_overwritten', "%s files are NOT overwritten due to blacklist");
define('LANG_can_not_update_non_writable_files', "Nie można zaktualizować, ponieważ następujące pliki/foldery nie mają praw do zapisu.");
define('LANG_dwl_failed', "Adres pobierania nie jest dostepny: \"%s\".<br> Spróbuj ponownie później.");
define('LANG_temp_folder_not_writable', "The download can not be placed, because Apache does not have write permission at the system temporary folder (%s).");
define('LANG_base_dir_not_writable', "Panel nie może zostać zaktualizowany, ponieważ Apache nie ma uprawnień do zapisu w folderze \"%s\".");
define('LANG_new_files', "%s nowych plików.");
define('LANG_updated_files', "Zaktualizowane pliki:<br>%s");
define('LANG_select_mirror', "Wybierz żródło");
define('LANG_view_changes', "Pokaż zmiany");
define('LANG_updating_modules', "Aktualizacja modułów");
define('LANG_updating_finished', "Aktualizacja zakończona");
define('LANG_updated_module', "Zaktualizowany moduł: '%s'.");
define('LANG_blacklist_files', "<b>Czarna Lista</b>");
define('LANG_blacklist_files_info', "- wszystkie wybrane pliki nie zostaną zaktualizowane.");
define('LANG_save_to_blacklist', "Zapisz na czarnej liście");
define('LANG_no_new_updates', "Brak nowych aktualizacji");
define('LANG_module_file_missing', "directory is missing the module.php file.");
define('LANG_query_failed', "Failed to execute query");
define('LANG_query_failed_2', "to database.");
define('LANG_missing_zip_extension', "The php-zip extension is not loaded. Please, enable it to use the Update module.");
?>