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

define('LANG_add_new_remote_host', "Tilføj Ny Fjernvært");
define('LANG_configured_remote_hosts', "Konfigurer Fjernvært");
define('LANG_remote_host', "Fjernvært");
define('LANG_remote_host_info', "Fjernhost skal være et pingable værtsnavn!");
define('LANG_remote_host_port', "Fjernvært Port");
define('LANG_remote_host_port_info', "The port that is listened by the OGP Agent on remote host. Default: 12679.");
define('LANG_remote_host_name', "Fjernværtens Navn");
define('LANG_ogp_user', "OGP Agent Username");
define('LANG_remote_host_name_info', "Fjernværtens navn er brugt til, at hjælpe brugere til at identificere deres servere.");
define('LANG_add_remote_host', "Tilføj Fjernværten");
define('LANG_remote_encryption_key', "Fjernværtens Krypterings nøgle");
define('LANG_remote_encryption_key_info', "Fjern kryptering, er brugt til at kryptere data mellem hjemmesider og agenten. Denne nøgle skal være ens begge steder.");
define('LANG_server_name', "Server Navn");
define('LANG_agent_ip_port', "Agent IP:Port");
define('LANG_agent_status', "Agent Status");
define('LANG_ips', "IPs");
define('LANG_add_more_ips', "Hvis du ønsker at tilføje flere IPs tryk 'Vælg IPs' når alle felter er udfyldt, ville et ny tomt felt komme frem.");
define('LANG_encryption_key_mismatch', "Encryption key does not match with the Agent. Please recheck your Agent configuration.");
define('LANG_no_ip_for_remote_host', "Du er nødtil at filføje mindst en (1) IP addresse , for hver fjernvært.");
define('LANG_note_remote_host', "A remote host is a server where the OGP Agent is running on. Each host can have multiple number of IP addresses on which users can bind servers to.");
define('LANG_ip_administration', "Server &amp; IP Administration :: Open Game Panel");
define('LANG_unknown_error', "Ukendt fejl - status_chk retueret");
define('LANG_remote_host_user_name', "UNIX bruger");
define('LANG_remote_host_user_name_info', "Brugernavn, hvor agenten kører fra. Eksempel: Jonhy");
define('LANG_remote_host_ftp_ip', "FTP IP");
define('LANG_remote_host_ftp_ip_info', "The FTP server <b>IP</b> for the current Agent.");
define('LANG_remote_host_ftp_port', "FTP Port");
define('LANG_remote_host_ftp_port_info', "The FTP server <b>port</b> for the current Agent.");
define('LANG_view_log', "View Log");
define('LANG_status', "Status");
define('LANG_stop_firewall', "Stop Firewall");
define('LANG_start_firewall', "Start Firewall");
define('LANG_seconds', "Sekunder");
define('LANG_reboot', "Remote Server Reboot");
define('LANG_restart', "Restart Agent");
define('LANG_confirm_reboot', "Are you sure you want to remotely reboot the entire physical server named '%s'?");
define('LANG_confirm_restart', "Are you sure you want to restart the Agent named '%s'?");
define('LANG_restarting', "Restarting Agent... Please wait.");
define('LANG_restarted', "Agent successfully restarted.");
define('LANG_reboot_success', "Server named '%s' was successfully rebooted. You will not be able to access the server until it has successfully booted.");
define('LANG_invalid_remote_host_id', "Ugyldig fjernvært id '%s' givet.");
define('LANG_remote_host_removed', "Fjernværten kaldet '%s' fjernet succesfuldt.");
define('LANG_editing_remote_server', "Regidere fjern server kaldet '%s'");
define('LANG_remote_server_settings_changed', "Skift indstillinger på fjern server '%s' succesfuldt.");
define('LANG_save_settings', "Gem Indstilinger");
define('LANG_set_ips', "Set IPs");
define('LANG_remote_ip', "Fjern IP");
define('LANG_remote_ips_for', "IPs for Game Servers To Use on Agent Server '%s'");
define('LANG_ips_set_for_server', "IPs er sat for server kaldet '%s' successfully.");
define('LANG_could_not_remove_ip', "Kunne ikke fjerne gamle IP's fra database.");
define('LANG_could_add_ip', "Kunne ikke tilføje fjern server IP til database.");
define('LANG_areyousure_removeagent', "Are you sure you want to remove the Agent called");
define('LANG_areyousure_removeagent2', "og alle dets hjem, som er til den server, fra ogp database?");
define('LANG_error_while_remove', "Fejl opstod, ved fjernelse af fjern server.");
define('LANG_add_ip', "Tilføj IP");
define('LANG_remove_ip', "Fjern IP");
define('LANG_edit_ip', "Redigere IP");
define('LANG_wrote_changes', "Changes saved successfully.");
define('LANG_there_are_servers_running_on_this_ip', "Der er servere der kører på denne IP addresse.");
define('LANG_enter_ip_host', "Du må skrive IP til fjernværten.");
define('LANG_enter_valid_ip', "Du må indtaste en aktiv port til fjernværten. Portens værdi, skal være mellem 0 og 65535, dog anbefales det, at sætte den mellem 1024 og 65535.");
define('LANG_could_not_add_server', "Kunne ikke filføje server");
define('LANG_to_db', "til databasen.");
define('LANG_added_server', "Tilføj server");
define('LANG_with_port', "med port");
define('LANG_to_db_succesfully', "til databasen succesfuldt.");
define('LANG_unable_discover', "ikke muligt at automatisere IPs til");
define('LANG_set_ip_manually', "Du er nødtil at sætte dem manuelt.");
define('LANG_found_ips', "Fundet IPs");
define('LANG_for_remote_server', "til fjernserveren.");
define('LANG_failed_add_ip', "Fejlet I at tilføje IP");
define('LANG_timeout', "Tiden Udløb");
define('LANG_timeout_info', "The time limit in seconds to get response from this Agent.");
define('LANG_use_nat', "Brug NAT");
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
define('LANG_home_id', "Hjemme ID");
define('LANG_home_path', "Hjemme sti");
define('LANG_game_type', "Spil Type");
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
