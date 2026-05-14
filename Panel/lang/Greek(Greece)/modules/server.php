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

define('LANG_add_new_remote_host', "Προσθέστε νέο απομακρυσμένο κεντρικό υπολογιστή.");
define('LANG_configured_remote_hosts', "Διαμορφωμένος απομακρυσμένος κεντρικός υπολογιστής");
define('LANG_remote_host', "Απομακρυσμένος Κεντρικός Υπολογιστής");
define('LANG_remote_host_info', "Ο απομακρυσμένος κεντρικός υπολογιστής πρέπει να είναι ένα pingable όνομα κεντρικού υπολογιστή!");
define('LANG_remote_host_port', "Θύρα απομακρυσμένου κεντρικού υπολογιστή.");
define('LANG_remote_host_port_info', "Η θύρα που ακούει ο OGP Agent στον απομακρυσμένο κεντρικό υπολογιστή. Προεπιλογή: 12679.");
define('LANG_remote_host_name', "Όνομα Απομακρυσμένου Κεντρικόυ Υπολογιστή");
define('LANG_ogp_user', "Όνομα χρήστη OGP Agent");
define('LANG_remote_host_name_info', "Το όνομα απομακρυσμένου κεντρικού υπολογιστή χρησιμοποιείται για να βοηθήσει τους χρήστες να αναγνωρίσουν τους διακομιστές τους.");
define('LANG_add_remote_host', "Προσθέστε Απομακρυσμένου Κεντρικού Υπολογιστή");
define('LANG_remote_encryption_key', "Κλειδί Απομακρυσμένης Κρυπτογράφησης");
define('LANG_remote_encryption_key_info', "Το κλειδί απομακρυσμένης κρυπτογράφησης χρησιμοποιείται για την κρυπτογράφηση των δεδομένων μεταξύ του Panel και του Agent. Αυτό το κλειδί πρέπει να είναι το ίδιο και στις δύο πλευρές.");
define('LANG_server_name', "Όνομα Διακομιστή");
define('LANG_agent_ip_port', "Agent IP:Port");
define('LANG_agent_status', "Κατάσταση Agent");
define('LANG_ips', "IPs");
define('LANG_add_more_ips', "Εάν θέλετε να εισαγάγετε περισσότερες διευθύνσεις IP, πατήστε 'Set IPs' όταν όλα τα πεδία είναι γεμάτα και θα εμφανιστεί ένα κενό πεδίο.");
define('LANG_encryption_key_mismatch', "Το κλειδί κρυπτογράφησης δεν ταιριάζει με τον Agent. Παρακαλώ ελέγξτε ξανά τη διαμόρφωση του Agent σας.");
define('LANG_no_ip_for_remote_host', "Πρέπει να προσθέσετε τουλάχιστον μία (1) διεύθυνση IP για κάθε απομακρυσμένο κεντρικό υπολογιστή.");
define('LANG_note_remote_host', "Ένας απομακρυσμένος κεντρικός υπολογιστής είναι ένας διακομιστής στον οποίο εκτελείται ο OGP Agent. Κάθε κεντρικός υπολογιστής μπορεί να έχει πολλαπλούς αριθμούς διευθύνσεων IP στους οποίους οι χρήστες μπορούν να δεσμεύσουν διακομιστές.");
define('LANG_ip_administration', "Διακομιστής &amp; Διαχείριση IP :: Open Game Panel");
define('LANG_unknown_error', "Άγνωστο σφάλμα - επέστρεψε status_chk");
define('LANG_remote_host_user_name', "Χρήστης UNIX");
define('LANG_remote_host_user_name_info', "Όνομα χρήστη όπου εκτελείται ο Agent. Παράδειγμα: Τζόνι");
define('LANG_remote_host_ftp_ip', "Διεύθυνση IP FTP");
define('LANG_remote_host_ftp_ip_info', "The FTP server <b>IP</b> for the current Agent.");
define('LANG_remote_host_ftp_port', "FTP Port");
define('LANG_remote_host_ftp_port_info', "The FTP server <b>port</b> for the current Agent.");
define('LANG_view_log', "View Log");
define('LANG_status', "Status");
define('LANG_stop_firewall', "Stop Firewall");
define('LANG_start_firewall', "Start Firewall");
define('LANG_seconds', "Seconds");
define('LANG_reboot', "Remote Server Reboot");
define('LANG_restart', "Restart Agent");
define('LANG_confirm_reboot', "Are you sure you want to remotely reboot the entire physical server named '%s'?");
define('LANG_confirm_restart', "Are you sure you want to restart the Agent named '%s'?");
define('LANG_restarting', "Restarting Agent... Please wait.");
define('LANG_restarted', "Agent successfully restarted.");
define('LANG_reboot_success', "Server named '%s' was successfully rebooted. You will not be able to access the server until it has successfully booted.");
define('LANG_invalid_remote_host_id', "Invalid remote host id '%s' given.");
define('LANG_remote_host_removed', "Remote host called '%s' removed successfully.");
define('LANG_editing_remote_server', "Editing remote server called '%s'");
define('LANG_remote_server_settings_changed', "Changed settings for remote server '%s' successfully.");
define('LANG_save_settings', "Save Settings");
define('LANG_set_ips', "Set IPs");
define('LANG_remote_ip', "Remote IP");
define('LANG_remote_ips_for', "IPs for Game Servers To Use on Agent Server '%s'");
define('LANG_ips_set_for_server', "IPs set for server called '%s' successfully.");
define('LANG_could_not_remove_ip', "Could not remove old IP's from database.");
define('LANG_could_add_ip', "Could add remote server IP to database.");
define('LANG_areyousure_removeagent', "Are you sure you want to remove the Agent called");
define('LANG_areyousure_removeagent2', "and all the homes related to it from the ogp database?");
define('LANG_error_while_remove', "Error occurred while removing remote server.");
define('LANG_add_ip', "Add IP");
define('LANG_remove_ip', "Remove IP");
define('LANG_edit_ip', "Edit IP");
define('LANG_wrote_changes', "Changes saved successfully.");
define('LANG_there_are_servers_running_on_this_ip', "There are servers running on this IP address.");
define('LANG_enter_ip_host', "You must enter IP for the remote host.");
define('LANG_enter_valid_ip', "You must enter valid port for the remote host. The port value can be between 0 and 65535, however recommendation is between 1024 and 65535.");
define('LANG_could_not_add_server', "Could not add server");
define('LANG_to_db', "to the database.");
define('LANG_added_server', "Added server");
define('LANG_with_port', "with port");
define('LANG_to_db_succesfully', "to the database successfully.");
define('LANG_unable_discover', "Unable to auto discover IPs on");
define('LANG_set_ip_manually', "You'll have to set them manually.");
define('LANG_found_ips', "Found IPs");
define('LANG_for_remote_server', "for the remote server.");
define('LANG_failed_add_ip', "Failed to add IP");
define('LANG_timeout', "Time Out");
define('LANG_timeout_info', "The time limit in seconds to get response from this Agent.");
define('LANG_use_nat', "Use NAT");
define('LANG_use_nat_info', "Enable if your remote server is using NAT rules. Use this setting if your game servers are running on internal private LAN IP addresses so that the panel will use your real remote IP address to query the game servers.");
define('LANG_arrange_ports', "Arrange ports");
define('LANG_assign_new_ports_range_for_ip', "Assign new ports range for IP %s");
define('LANG_assigned_port_ranges_for_ip', "Assigned port ranges for IP %s");
define('LANG_assigned_ports_for_ip', "Assigned ports for IP %s");
define('LANG_unspecified_game_types', "Unspecified game types");
define('LANG_start_port', "Start port:");
define('LANG_end_port', "End port:");
define('LANG_port_increment', "Port increment:");
define('LANG_total_assignable_ports', "Total assignable ports:");
define('LANG_available_range_ports', "Available range ports:");
define('LANG_assign_range', "Assign range");
define('LANG_edit_range', "Edit range");
define('LANG_delete_range', "Delete range");
define('LANG_home_id', "Home ID");
define('LANG_home_path', "Home path");
define('LANG_game_type', "Game type");
define('LANG_port', "Port");
define('LANG_invalid_values', "Invalid values.");
define('LANG_ports_in_range_already_arranged', "Ports in range already arranged.");
define('LANG_ports_range_already_configured_for', "Ports range already configured for %s.");
define('LANG_ports_range_added_successfull_for', "Ports range added successfully for %s.");
define('LANG_ports_range_deleted_successfull', "Ports range deleted successfully.");
define('LANG_ports_range_edited_successfull_for', "Ports range edited successfully for %s.");
define('LANG_editing_firewall_for_remote_server', "Editing Firewall for remote server named '%s'");
define('LANG_default_allowed', "Allowed by default");
define('LANG_allow_port_command', "Allow port command");
define('LANG_deny_port_command', "Deny port command");
define('LANG_allow_ip_port_command', "Allow IP:port command");
define('LANG_deny_ip_port_command', "Deny IP:port command");
define('LANG_enable_firewall_command', "Enable firewall command");
define('LANG_disable_firewall_command', "Disable firewall command");
define('LANG_get_firewall_status_command', "Get firewall status command");
define('LANG_reset_firewall_command', "Reset firewall command");
define('LANG_firewall_status', "Firewall status");
define('LANG_save_firewall_settings', "Save firewall settings");
define('LANG_reset_firewall', "Reset Firewall");
define('LANG_firewall_settings', "Firewall Settings");
define('LANG_display_public_ip', "Display Public IP");
define('LANG_ips_can_be_internal_external', "Enter usable IP addresses.&nbsp; Public IP addresses and internal LAN IP addresses (for NAT setups) can be used.");
?>
