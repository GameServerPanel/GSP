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
define('LANG_curl_needed', "Diese Seite erfordert das PHP-Curl Modul.");
define('LANG_no_access', "Sie benötigen Administratorrechte um auf diese Seite zuzugreifen.");
define('LANG_dwl_update', "Update herunterladen...");
define('LANG_dwl_complete', "Download abgeschlossen");
define('LANG_install_update', "Update wird installiert...");
define('LANG_update_complete', "Aktualisierung abgeschlossen");
define('LANG_ignored_files', "%s ignorierte Datei(en)");
define('LANG_not_updated_files_blacklisted', "Nicht aktualisierte / installierte Dateien (Blacklist):<br>%s");
define('LANG_latest_version', "Neueste version");
define('LANG_panel_version', "Panel Version");
define('LANG_update_now', "Jetzt aktualisieren");
define('LANG_the_panel_is_up_to_date', "Das Panel ist aktuell.");
define('LANG_files_overwritten', "%s Dateien überschrieben");
define('LANG_files_not_overwritten', "%s Dateien wurde NICHT überschrieben, da sie auf der Blacklist sind.");
define('LANG_can_not_update_non_writable_files', "Kann nicht aktualisiert werden, da die folgenden Dateien/Ordner nicht beschreibbar sind");
define('LANG_dwl_failed', "Der Download-Link ist nicht verfügbar: \"%s\".<br>Versuchen Sie es später erneut.");
define('LANG_temp_folder_not_writable', "Das Update kann nicht abgelegt werden, da Apache keine Schreibrechte für den Temporären Ordner des Systems hat (%s).");
define('LANG_base_dir_not_writable', "Das Panel kann nicht aktualisiert werden, da Apache keine Schreibrechte für den Ordner \"%s\" hat.");
define('LANG_new_files', "%s neue Dateien.");
define('LANG_updated_files', "Aktualisierte Dateien:<br>%s");
define('LANG_select_mirror', "Mirror wählen");
define('LANG_view_changes', "Änderungen anzeigen");
define('LANG_updating_modules', "Aktualisierung der Module");
define('LANG_updating_finished', "Aktualisierung abgeschlossen");
define('LANG_updated_module', "Aktualisierte Module: '%s'.");
define('LANG_blacklist_files', "Ausgeschlossene Dateien");
define('LANG_blacklist_files_info', "Alle markierten Dateien werden nicht aktualisiert.");
define('LANG_save_to_blacklist', "Zur Blacklist speichern");
define('LANG_no_new_updates', "Keine neuen updates");
define('LANG_module_file_missing', "Im Verzeichnis fehlt die module.php Datei.");
define('LANG_query_failed', "Abfrage ist fehlgeschlagen");
define('LANG_query_failed_2', "zur datenbank");
define('LANG_missing_zip_extension', "Die php-zip Erweiterung ist nicht geladen. Bitte aktiviere diese, um das Update Modul zu nutzen.");
?>