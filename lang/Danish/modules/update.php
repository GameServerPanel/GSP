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
define('LANG_curl_needed', "Den side påkræver PHP curl modul.");
define('LANG_no_access', "Du har brug for admin rettigheder, for at få adgang til siden.");
define('LANG_dwl_update', "Downloader opdatering...");
define('LANG_dwl_complete', "Download Færdiggjort");
define('LANG_install_update', "Installere opdatering...");
define('LANG_update_complete', "Opdatering Færdig");
define('LANG_ignored_files', "%s ignored file(s)");
define('LANG_not_updated_files_blacklisted', "Not updated/installed files (blacklisted):<br>%s");
define('LANG_latest_version', "Seneste version");
define('LANG_panel_version', "Panel version");
define('LANG_update_now', "Opdatere Nu");
define('LANG_the_panel_is_up_to_date', "The Panel is up-to-date.");
define('LANG_files_overwritten', "%s files overwritten");
define('LANG_files_not_overwritten', "%s files are NOT overwritten due to blacklist");
define('LANG_can_not_update_non_writable_files', "Kan ikke opdatere, på grund af følgende filer/mapper ikke er har skriverettigheder");
define('LANG_dwl_failed', "Den download URL er ikke tilgængelig: \"%s\".<br>Prøv igen senere.");
define('LANG_temp_folder_not_writable', "The download can not be placed, because Apache does not have write permission at the system temporary folder (%s).");
define('LANG_base_dir_not_writable', "The Panel can not update, because Apache does not have write permission on \"%s\" folder.");
define('LANG_new_files', "%s nye filer.");
define('LANG_updated_files', "Opdateret filer:<br>%s");
define('LANG_select_mirror', "Select mirror");
define('LANG_view_changes', "View changes");
define('LANG_updating_modules', "Opdatere moduler");
define('LANG_updating_finished', "Opdatering Færdig");
define('LANG_updated_module', "Opdatere modul: '%s'.");
define('LANG_blacklist_files', "Blacklist files");
define('LANG_blacklist_files_info', "All marked files will not be updated.");
define('LANG_save_to_blacklist', "Save to blacklist");
define('LANG_no_new_updates', "No new updates");
define('LANG_module_file_missing', "directory is missing the module.php file.");
define('LANG_query_failed', "Failed to execute query");
define('LANG_query_failed_2', "to database.");
define('LANG_missing_zip_extension', "The php-zip extension is not loaded. Please, enable it to use the Update module.");
?>