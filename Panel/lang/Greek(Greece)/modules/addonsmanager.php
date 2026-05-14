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

define('LANG_install_plugin', "Εγκαταστήστε Πρόσθετα");
define('LANG_install_mappack', "Εγκαταστήστε Χάρτες");
define('LANG_install_config', "Εγκαταστήστε Αρχεία Διαμόρφωσης");
define('LANG_game_name', "Όνομα Παιχνιδιού");
define('LANG_directory', "Διαδρομή Καταλόγου");
define('LANG_remote_server', "Απομακρυσμένος Διακομιστής");
define('LANG_select_addon', "Επιλέξτε Πρόσθετο");
define('LANG_install', "Εγκαταστήστε");
define('LANG_failed_to_start_file_download', "Αποτυχία εκκίνησης κατεβάσματος αρχείου.");
define('LANG_no_games_servers_available', "Δεν υπάρχουν διαθέσιμοι διακομιστές παιχνιδιού στον λογαριασμό σας.");
define('LANG_addon_installed_successfully', "Το πρόσθετο εγκαταστάθηκε επιτυχώς.");
define('LANG_path', "Διαδρομή");
define('LANG_wait_while_decompressing', "Περιμένετε καθώς το αρχείο %s αποσυμπιέζεται.");
define('LANG_addon_name', "Όνομα Πρόσθετου");
define('LANG_url', "URL");
define('LANG_select_game_type', "Επιλέξτε Τύπο Παιχνιδιού");
define('LANG_plugin', "Πρόσθετο");
define('LANG_mappack', "Πακέτο Χαρτών");
define('LANG_config', "Αρχείο Διαμόρφωσης");
define('LANG_type', "Τύπος Πρόσθετου");
define('LANG_game', "Παιχνίδι");
define('LANG_show_all_addons', "Εμφανίστε Όλα Τα Πρόσθετα");
define('LANG_show_addons_for_selected_type', "Εμφανίστε Πρόσθετα Για Τον Επιλεγμένο Τύπο");
define('LANG_show_addons_for_selected_game', "Εμφανίστε Πρόσθετα Για Το Επιλεγμένο Παιχνίδι");
define('LANG_linux_games', "Παιχνίδια Για Linux:");
define('LANG_windows_games', "Παιχνίδια Για Windows:");
define('LANG_create_addon', "Δημιουργήστε Πρόσθετο");
define('LANG_addons_db', "Addons Database");
define('LANG_addon_has_been_created', "Το πρόσθετο %s δημιουργήθηκε.");
define('LANG_remove_addon', "Καταργήστε Το Πρόσθετο");
define('LANG_fill_the_url_address_to_a_compressed_file', "Παρακαλώ, συμπληρώστε μια διεύθυνση URL για ένα συμπιεσμένο αρχείο.");
define('LANG_fill_the_addon_name', "Παρακαλώ, συμπληρώστε ένα όνομα για το πακέτο πρόσθετου.");
define('LANG_select_an_addon_type', "Παρακαλώ, επιλέξτε έναν τύπο πρόσθετου.");
define('LANG_select_a_game_type', "Παρακαλώ, επιλέξτε έναν τύπο παιχνιδιού.");
define('LANG_edit_addon', "Επεξεργαστείτε Το Πρόσθετο");
define('LANG_post-script', "Δέσμη ενεργειών (bash) για μετά την εγκατάσταση.");
define('LANG_replacements', "Αντικαταστάτες:");
define('LANG_addon_name_info', "Βάλτε ένα όνομα για αυτό το πρόσθετο, αυτό είναι το όνομα που βλέπει ο χρήστης.");
define('LANG_url_info', "Βάλτε μια διεύθυνση ιστού που περιέχει ένα αρχείο για κατέβασμα, αν είναι συμπιεσμένο σε zip ή tar.gz θα αποσυμπιεστεί στον ριζικό κατάλογο του διακομιστή ή στην διαδρομή που δίνεται παρακάτω.");
define('LANG_path_info', "Η διαδρομή πρέπει να είναι συγγενική με τον φάκελο του διακομιστή και να μην περιέχει κάθετους στην αρχή ή στο τέλος, π.χ.: cstrike/cfg. Αν αφεθεί κενό θα χρησιμοποιηθεί η ριζική διαδρομή.");
define('LANG_post-script_info', "Βάλτε κώδικα γλώσσας bash, θα εκτελεστεί σαν δέσμη ενεργειών, μπορείτε να χρησιμοποιήσετε αντικαταστάτες κειμένου για να προσαρμώσετε την εγκατάσταση, θα αντικατασταθούν από δεδομένα από τον διακομιστή στον οποίο εγκαταστήσατε το πρόσθετο. Η δέσμη ενεργειών θα ξεκινήσει από τον ριζικό φάκελο του διακομιστή ή την καθορισμένη διαδρομή.");
define('LANG_show_to_group', "Show to group");
define('LANG_all_groups', "All groups");
define('LANG_show_addons_for_selected_group', "Show addons for selected group");
define('LANG_group', "Group");
?>