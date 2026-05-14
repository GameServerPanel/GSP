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
define('LANG_curl_needed', "Cette page requiert le module PHP curl.");
define('LANG_no_access', "Vous devez avoir les droits d'administration pour accéder à cette page.");
define('LANG_dwl_update', "Téléchargement de la mise à jour...");
define('LANG_dwl_complete', "Téléchargement complété");
define('LANG_install_update', "Mise à jour en cours...");
define('LANG_update_complete', "Mise à jour effectuée avec succès");
define('LANG_ignored_files', "%s fichier(s) ignoré(s).");
define('LANG_not_updated_files_blacklisted', "Fichiers non mis à jour/installés (Blacklistés):<br>%s");
define('LANG_latest_version', "Dernière version");
define('LANG_panel_version', "Version du Panneau");
define('LANG_update_now', "Mettre à jour maintenant");
define('LANG_the_panel_is_up_to_date', "Le Panneau est à jour.");
define('LANG_files_overwritten', "%s fichiers remplacés");
define('LANG_files_not_overwritten', "%s fichiers NON remplacés à cause de la liste noire");
define('LANG_can_not_update_non_writable_files', "Impossible de mettre à jour car les fichiers/dossiers suivants ne peuvent pas être modifiés");
define('LANG_dwl_failed', "L'URL de téléchargement n'est pas accessible : \"%s\".<br>Réessayer plus tard.");
define('LANG_temp_folder_not_writable', "Le téléchargement ne peut démarré car le serveur Web n'a pas la permission d'écrire dans le dossier temporaire système (%s).");
define('LANG_base_dir_not_writable', "Le panneau ne peut être mis à jour car le serveur Web n'a pas les droits d'écriture sur le dossier \"%s\".");
define('LANG_new_files', "%s nouveaux fichiers.");
define('LANG_updated_files', "Fichiers mis à jour:<br>%s");
define('LANG_select_mirror', "Sélectionner le mirroir");
define('LANG_view_changes', "Voir les changements");
define('LANG_updating_modules', "Mise à jour des modules");
define('LANG_updating_finished', "Mise à jour terminée");
define('LANG_updated_module', "Module mis à jour: '%s'.");
define('LANG_blacklist_files', "Liste Noire des fichiers");
define('LANG_blacklist_files_info', "Tous les fichiers marqués ne seront pas mis à jour.");
define('LANG_save_to_blacklist', "Enregistrer dans la Liste Noire");
define('LANG_no_new_updates', "Pas de nouvelles mises à jour");
define('LANG_module_file_missing', "Le fichier module.php est manquant dans le dossier.");
define('LANG_query_failed', "Impossible d’exécuter la requête");
define('LANG_query_failed_2', "sur la base de données.");
define('LANG_missing_zip_extension', "L'extension php-zip n'est pas chargée. Veuillez l'activer pour utiliser le module de mise à jour.");
?>