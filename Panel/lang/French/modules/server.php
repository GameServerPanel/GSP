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

define('LANG_add_new_remote_host', "Ajouter un nouveau hôte distant");
define('LANG_configured_remote_hosts', "Hôtes distants configurés");
define('LANG_remote_host', "Hôte distant");
define('LANG_remote_host_info', "L&apos;hôte distant doit avoir un nom d&apos;hôte (hostname) pingable!");
define('LANG_remote_host_port', "Port de l'hôte distant");
define('LANG_remote_host_port_info', "Le port depuis lequel l&apos;Agent OGP écoute sur l&apos;hôte distant. Défaut: 12679.");
define('LANG_remote_host_name', "Nom de l'hôte distant");
define('LANG_ogp_user', "Nom d'utilisateur de l'Agent OGP");
define('LANG_remote_host_name_info', "Le nom de l&apos;hôte distant sera utilisé pour faciliter sa reconnaissance dans tout le Panneau.");
define('LANG_add_remote_host', "Ajouter un hôte distant");
define('LANG_remote_encryption_key', "Clé de chiffrement distante");
define('LANG_remote_encryption_key_info', "La clé de chiffrement distante est utilisé pour crypter les données entre le Panneau et l&apos;hôte distant (l&apos;Agent).<br>Cette clé doit être la même sur les deux machines.");
define('LANG_server_name', "Nom du serveur");
define('LANG_agent_ip_port', "IP:Port de l'Agent");
define('LANG_agent_status', "Statut de l'Agent");
define('LANG_ips', "IPs");
define('LANG_add_more_ips', "Si vous voulez entrer plus d'IPs, cliquez sur 'Définir IPs' quand tous les champs sont remplis et un champ vide apparaîtra.");
define('LANG_encryption_key_mismatch', "La clé de chiffrement ne correspond pas à celle de l'Agent. Veuillez vérifier la configuration de l'Agent.");
define('LANG_no_ip_for_remote_host', "Vous devez ajouter au moins une (1) adresse IP pour chaque hôte distant.");
define('LANG_note_remote_host', "Un hôte distant est un serveur où l'Agent OGP tourne. Chaque hôte peut avoir plusieurs adresses IPs que les utilisateurs utiliseront pour leurs serveurs de jeux.");
define('LANG_ip_administration', "Administration IP &amp; Serveur :: Open Game Panel");
define('LANG_unknown_error', "Erreur inconnue - status_chk a retourné");
define('LANG_remote_host_user_name', "Utilisateur UNIX");
define('LANG_remote_host_user_name_info', "Utilisateur qui fait tourner l'Agent. Exemple: ogp_agent");
define('LANG_remote_host_ftp_ip', "IP FTP");
define('LANG_remote_host_ftp_ip_info', "L&apos;adresse <b>IP</b> du serveur FTP pour cet Agent.");
define('LANG_remote_host_ftp_port', "Port FTP");
define('LANG_remote_host_ftp_port_info', "Le <b>port</b> du serveur FTP pour cet Agent.");
define('LANG_view_log', "Voir le log");
define('LANG_status', "Statut");
define('LANG_stop_firewall', "Arrêter le Firewall (pare-feu)");
define('LANG_start_firewall', "Démarrer le Firewall (pare-feu)");
define('LANG_seconds', "Secondes");
define('LANG_reboot', "Redémarrer le Serveur Distant");
define('LANG_restart', "Redémarrer l'Agent");
define('LANG_confirm_reboot', "Etes-vous sûr de vouloir redémarrer physiquement la machine distante nommée '%s'?");
define('LANG_confirm_restart', "Etes-vous sûr de vouloir redémarrer l'Agent nommé '%s'?");
define('LANG_restarting', "Redémarrage de l'Agent... Veuillez patienter.");
define('LANG_restarted', "Agent redémarré avec succès.");
define('LANG_reboot_success', "Le serveur nommé '%s' a été redémarré avec succès. Vous ne pourrez pas accéder au serveur tant qu'il n'est pas complètement redémarré.");
define('LANG_invalid_remote_host_id', "ID '%s' de l'hôte distant invalide.");
define('LANG_remote_host_removed', "Hôte distant '%s' supprimé avec succès.");
define('LANG_editing_remote_server', "Edition de l'hôte distant nommé '%s'");
define('LANG_remote_server_settings_changed', "Changement des paramètres pour le serveur distant '%s' effectué avec succès.");
define('LANG_save_settings', "Sauvegarder les paramètres");
define('LANG_set_ips', "Définir IPs");
define('LANG_remote_ip', "IP distante");
define('LANG_remote_ips_for', "Adresses IPs pour les Serveurs de Jeu à utiliser pour l&apos;Agent '%s'");
define('LANG_ips_set_for_server', "IPs pour le serveur nommé '%s' définies avec succès.");
define('LANG_could_not_remove_ip', "Impossible de supprimer l'IP de la base de données.");
define('LANG_could_add_ip', "Peut ajouter l'IP du serveur distant à la base de données.");
define('LANG_areyousure_removeagent', "Etes-vous sûr de vouloir supprimer l'Agent nommé");
define('LANG_areyousure_removeagent2', "et tous les serveurs qui lui sont reliés dans la base de données OGP?");
define('LANG_error_while_remove', "Erreur lors de la suppression du serveur distant.");
define('LANG_add_ip', "Ajouer IP");
define('LANG_remove_ip', "Supprimer IP");
define('LANG_edit_ip', "Editer IP");
define('LANG_wrote_changes', "Changements effectués avec succès.");
define('LANG_there_are_servers_running_on_this_ip', "Il y a des serveurs démarrés avec cette adresse IP.");
define('LANG_enter_ip_host', "Vous devez entrer une IP pour l'hôte distant.");
define('LANG_enter_valid_ip', "Vous devez entrer un port valide pour l'hôte distant. La valeur du port peut être comprise entre 0 et 65535, cependant les valeurs recommandées sont celles comprises entre 1024 et 65535.");
define('LANG_could_not_add_server', "Impossible d'ajouter le serveur");
define('LANG_to_db', "à la base de données.");
define('LANG_added_server', "Serveur ajouté");
define('LANG_with_port', "avec le port");
define('LANG_to_db_succesfully', "dans la base de données avec succès.");
define('LANG_unable_discover', "Impossible de découvrir automatiquement les IPs sur");
define('LANG_set_ip_manually', "Vous devez les entrer manuellement.");
define('LANG_found_ips', "IPs trouvés");
define('LANG_for_remote_server', "pour le serveur distant.");
define('LANG_failed_add_ip', "Impossible d'ajouter l'IP");
define('LANG_timeout', "Time Out");
define('LANG_timeout_info', "La limite de temps en secondes pour avoir une réponse de l&apos;Agent.");
define('LANG_use_nat', "Utiliser le NAT");
define('LANG_use_nat_info', "Activer si votre serveur distant utilise les règles NAT. Utiliser ce paramètre si vos serveurs de jeu sont lancés sur des adresses IP privées LAN internes pour que le Panel utilise votre adresse IP distante réelle pour interroger les Serveurs de Jeu.");
define('LANG_arrange_ports', "Arranger les ports");
define('LANG_assign_new_ports_range_for_ip', "Assigner nouvelle plage de ports pour l'IP %s");
define('LANG_assigned_port_ranges_for_ip', "Plages de ports assignées pour l'IP %s");
define('LANG_assigned_ports_for_ip', "Ports assignés pour l'IP %s");
define('LANG_unspecified_game_types', "Types de jeu non spécifié");
define('LANG_start_port', "Port de début:");
define('LANG_end_port', "Port de fin:");
define('LANG_port_increment', "Incrément de Port:");
define('LANG_total_assignable_ports', "Ports assignables au Total:");
define('LANG_available_range_ports', "Plage de ports disponible:");
define('LANG_assign_range', "Assigner plage");
define('LANG_edit_range', "Editer plage");
define('LANG_delete_range', "Supprimer plage");
define('LANG_home_id', "ID Serveur");
define('LANG_home_path', "Chemin du serveur");
define('LANG_game_type', "Type de Jeu");
define('LANG_port', "Port");
define('LANG_invalid_values', "Valeurs invalides.");
define('LANG_ports_in_range_already_arranged', "Ports dans la Plage déjà arrangés.");
define('LANG_ports_range_already_configured_for', "Plage de ports déjà configurée pour %s.");
define('LANG_ports_range_added_successfull_for', "Plage de ports ajoutée avec succès pour %s.");
define('LANG_ports_range_deleted_successfull', "Plage de ports supprimée avec succès.");
define('LANG_ports_range_edited_successfull_for', "Plage de ports éditée avec succès pour %s.");
define('LANG_editing_firewall_for_remote_server', "Editer le Firewall pour le serveur distant nommé '%s'");
define('LANG_default_allowed', "Autorisé par défaut");
define('LANG_allow_port_command', "Commande d'autorisation port");
define('LANG_deny_port_command', "Commande de blocage port");
define('LANG_allow_ip_port_command', "Commande d'autorisation IP:port");
define('LANG_deny_ip_port_command', "Commande de blocage IP:port");
define('LANG_enable_firewall_command', "Commande pour activer le Firewall");
define('LANG_disable_firewall_command', "Commande pour désactiver le Firewall");
define('LANG_get_firewall_status_command', "Commande pour le statut du Firewall");
define('LANG_reset_firewall_command', "Commande pour le Reset du Firewall");
define('LANG_firewall_status', "Statut du Firewall");
define('LANG_save_firewall_settings', "Enregistrer les paramètres du Firewall");
define('LANG_reset_firewall', "Reset du Firewall");
define('LANG_firewall_settings', "Paramètres du Firewall");
define('LANG_display_public_ip', "Adresse IP Publique par Défaut");
define('LANG_ips_can_be_internal_external', "Entrer des adresses IP utilisables.&nbsp; Des adresses IP publiques et adresses IP LAN internes (pour le NAT) peuvent être utilisées.");
?>
