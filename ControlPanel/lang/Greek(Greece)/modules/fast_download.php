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

define('OGP_LANG_create_alias', "Create alias and folder");
define('OGP_LANG_save_as', "Αποθηκεύστε Ως");
define('OGP_LANG_failure', "Σφάλμα, αποτυχεία δημηουργίας του αρχείου ψευδωνύμου.");
define('OGP_LANG_success', "Επιτυχία");
define('OGP_LANG_fast_download_service_for', "Λήψη υπηρεσίας ανακατεύθυνσης για %s.");
define('OGP_LANG_to_the_path', "Στη διαδρομή");
define('OGP_LANG_at_url', "Στο URL");
define('OGP_LANG_create_alias_for', "Δημιουργίστε ψευδώνυμο για");
define('OGP_LANG_fast_dl', "Ανακατεύθυνση λήψεων (FastDL)");
define('OGP_LANG_current_aliases_at_remote_server', "Τρέχοντα ψευδώνυμα σε απομακρυσμένο διακομιστή");
define('OGP_LANG_delete_selected_aliases', "Διαγράψτε επιλεγμένα ψευδώνυμα");
define('OGP_LANG_no_aliases_defined', "Δεν υπάρχουν ψευδώνυμα ιστού που έχουν οριστεί από το OGP για αυτόν τον απομακρυσμένο διακομιστή ακόμα.");
define('OGP_LANG_fastdl_port', "Θύρα");
define('OGP_LANG_fastdl_port_info', "Θύρα στην οποία θα ξεκινήσει ο Fast Download Daemon σας.");
define('OGP_LANG_fastdl_ip', "Διεύθυνση");
define('OGP_LANG_fastdl_ip_info', "Η διεύθυνση IP ή ο τομέας στον οποίο θα ξεκινήσει ο Fast Download διακομιστής σας, ο τομέας πρέπει να είναι καταχωρημένος στο /etc/hosts.");
define('OGP_LANG_listing', "Καταγραφή");
define('OGP_LANG_listing_info', "Αν είναι ενεργοποιημένο, ο διακομιστής θα εμφανίσει το περιεχόμενο των φακέλων.");
define('OGP_LANG_fast_dl_advanced', "Προχωρημένες Ρυθμίσεις");
define('OGP_LANG_apply_settings_and_restart_fastdl', "Αποθηκεύστε τη διαμόρφωση του daemon και επανεκκινήστε τον.");
define('OGP_LANG_stop_fastdl', "Σταματήστε Τον Fast Download Daemon");
define('OGP_LANG_fast_download_daemon_running', "Ο Fast Download Daemon είναι σε λειτουργία.");
define('OGP_LANG_fast_download_daemon_not_running', "Ο Fast Download Daemon δεν είναι σε λειτουργία.");
define('OGP_LANG_fastdl_could_not_be_restarted', "Δεν ήταν δυνατή η επανεκκίνηση της υπηρεσίας Fast Download.");
define('OGP_LANG_configuration_file_could_not_be_written', "Δεν ήταν δυνατή η εγγραφή του αρχείου διαμορφώσεων.");
define('OGP_LANG_remove_folders', "Καταργήστε το φάκελο για επιλεγμένα ψευδώνυμα.");
define('OGP_LANG_remove_folder', "Καταργήστε το φάκελο");
define('OGP_LANG_delete_alias', "Διαγράψτε το ψευδώνυμο");
define('OGP_LANG_no_game_homes_assigned', "You don't have any servers assigned to your account.");
define('OGP_LANG_select_remote_server', "Επιλέξτε απομακρυσμένο διακομιστή");
define('OGP_LANG_access_rules', "Access Rules");
define('OGP_LANG_create_aliases', "Δημιουργίστε Ψευδώνυμα");
define('OGP_LANG_select_game', "Επιλέξτε Παιχνίδι");
define('OGP_LANG_games_without_specified_rules', "Παιχνίδια χωρίς καθορισμένους κανόνες");
define('OGP_LANG_match_file_extension', "Ταιριάξτε την επέκταση αρχείου");
define('OGP_LANG_match_file_extension_info', "Καθορίστε επεκτάσεις χωρίζοντάς τες με κόμμα,<br>τα αρχεία που ταιριάζουν θα είναι προσβάσιμα.<br><b>Κενό για απεριόριστη πρόσβαση</b>.");
define('OGP_LANG_match_client_ip', "Ταιριάξτε την IP πελάτη");
define('OGP_LANG_match_client_ip_info', "Συνδέσεις με διεύθυνση IP που ταιριάζει θα επιτρέπονται,<br>κενές για απεριόριστη πρόσβαση. Μπορείτε να χρησιμοποιήσετε<br>πολλαπλές διευθύνσεις IP ή εύροι που διαχωρίζονται με κόμμα:<br>/xx υποδίκτυα<br>Παράδειγμα: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx υποδίκτυα<br>Παράδειγμα: 10.0.0.0/255.0.0.0<br>Hyphen εύροι<br>Παράδειγμα: 10.0.0.5-230<br>Ταίριασμα με Αστερίσκο<br>Παράδειγμα: 10.0.*.*");
define('OGP_LANG_save_access_rules', "Save Access Rules");
define('OGP_LANG_create_access_rules', "Δημιουργήστε κανόνες πρόσβασης");
define('OGP_LANG_invalid_entries_found', "Βρέθηκαν μη έγκυρες καταχωρήσεις");
define('OGP_LANG_game_name', "Όνομα παιχνιδιού");
define('OGP_LANG_alias_already_exists', "Το ψευδώνυμο %s υπάρχει ήδη.");
define('OGP_LANG_warning_access_rules_applied_once_alias_created', "ΠΡΟΕΙΔΟΠΟΙΗΣΗ: Οι κανόνες πρόσβασης εφαρμόζονται όταν δημιουργείται το ψευδώνυμο. Δεν θα εφαρμοστούν αλλαγές στα τρέχοντα ψευδώνυμα.");
define('OGP_LANG_autostart_on_agent_startup', "Αυτόματη εκκίνηση στην εκκίνηση του Agent");
define('OGP_LANG_autostart_on_agent_startup_info', "Ξεκινήστε το fast download daemon αυτόματα όταν ξεκινά ο Agent.");
define('OGP_LANG_port_forwarded_to_80', "Η θύρα προωθήθηκε από 80");
define('OGP_LANG_port_forwarded_to_80_info', "Ενεργοποιήστε αυτήν την επιλογή αν η θύρα που έχει ρυθμιστεί για αυτό το fast download daemon έχει προωθηθεί από τη θύρα 80, οπότε η θύρα θα είναι κρυμμένη στις διευθύνσεις URL.");
define('OGP_LANG_current_access_rules', "Τρέχον κανόνες πρόσβασης");
?>