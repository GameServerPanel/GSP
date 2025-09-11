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

define('OGP_LANG_create_alias', "Créer l&apos;Alias et le dossier");
define('OGP_LANG_save_as', "Enregistrer sous");
define('OGP_LANG_failure', "Erreur, impossible de générer le fichier d&#39;Alias");
define('OGP_LANG_success', "Succès");
define('OGP_LANG_fast_download_service_for', "Service de redirection de téléchargements pour %s");
define('OGP_LANG_to_the_path', "Dans le dossier");
define('OGP_LANG_at_url', "à l&#39;URL");
define('OGP_LANG_create_alias_for', "Créer un Alias pour");
define('OGP_LANG_fast_dl', "Redirection de téléchargements (Fast Download)");
define('OGP_LANG_current_aliases_at_remote_server', "Alias actuels du serveur distant");
define('OGP_LANG_delete_selected_aliases', "Supprimer les Alias sélectionnés");
define('OGP_LANG_no_aliases_defined', "Il n&#39;y a pas encore d&#39;Alias créés pour ce serveur.");
define('OGP_LANG_fastdl_port', "Port");
define('OGP_LANG_fastdl_port_info', "Le port sur lequel le serveur de redirection Fast Download va démarrer.");
define('OGP_LANG_fastdl_ip', "Adresse");
define('OGP_LANG_fastdl_ip_info', "L&apos;adresse IP ou le domaine sur lequel le serveur de Fast Download va démarrer, le domaine doit être listé dans le fichier /etc/hosts.");
define('OGP_LANG_listing', "Liste");
define('OGP_LANG_listing_info', "Si elle est activée, le serveur va lister le contenu des dossiers.");
define('OGP_LANG_fast_dl_advanced', "Configuration avancée de Fast Download");
define('OGP_LANG_apply_settings_and_restart_fastdl', "Mises à jour de la configuration et redémarre");
define('OGP_LANG_stop_fastdl', "Arrêter le service Fast Download");
define('OGP_LANG_fast_download_daemon_running', "Le service Fast Download est en cours d'exécution.");
define('OGP_LANG_fast_download_daemon_not_running', "Le service Fast Download est arrêté.");
define('OGP_LANG_fastdl_could_not_be_restarted', "Impossible de redémarrer le service Fast Download.");
define('OGP_LANG_configuration_file_could_not_be_written', "Impossible d'écrire le fichier de configuration.");
define('OGP_LANG_remove_folders', "Retirer le dossier pour les Alias sélectionnés.");
define('OGP_LANG_remove_folder', "Supprimer le dossier");
define('OGP_LANG_delete_alias', "Supprimer l&apos;Alias");
define('OGP_LANG_no_game_homes_assigned', "Aucun serveur de jeu ne vous est assigné.");
define('OGP_LANG_select_remote_server', "Sélectionner le serveur distant");
define('OGP_LANG_access_rules', "Règles d&apos;accès");
define('OGP_LANG_create_aliases', "Créer des Alias");
define('OGP_LANG_select_game', "Sélectionner le jeu");
define('OGP_LANG_games_without_specified_rules', "Jeux sans règles spécifiées");
define('OGP_LANG_match_file_extension', "Correspond aux extensions de fichiers");
define('OGP_LANG_match_file_extension_info', "Spécifier des extensions séparées par une virgule,<br> les fichiers correspondants seront accessibles.<br><b>Laisser vide pour un accès sans restriction</b>.");
define('OGP_LANG_match_client_ip', "Correspond à l'IP client");
define('OGP_LANG_match_client_ip_info', "Les connexions avec IP correspondantes seront autorisées,<br>laisser vide pour un accès sans restriction.<br>Vous pouvez utiliser plusieurs IPs<br>ou plages d&apos;IPs séparées par une virgule:<br>/xx subnets<br>Exemple: 10.0.0.0/16<br>/xxx.xxx.xxx.xxx subnets<br>Exemple: 10.0.0.0/255.0.0.0<br>Hyphen ranges<br>Exemple: 10.0.0.5-230<br>Asterisk matching<br>Exemple: 10.0.*.*");
define('OGP_LANG_save_access_rules', "Enregistrer les règles d&apos;accès");
define('OGP_LANG_create_access_rules', "Créer les règles d&apos;accès");
define('OGP_LANG_invalid_entries_found', "Entrées invalides trouvées");
define('OGP_LANG_game_name', "Nom du jeu");
define('OGP_LANG_alias_already_exists', "l'Alias %s existe déjà.");
define('OGP_LANG_warning_access_rules_applied_once_alias_created', "AVERTISSEMENT: Les règles d'accès sont appliquées lorsque l'Alias est créé. Aucun changement ne sera appliqué aux Alias actuels.");
define('OGP_LANG_autostart_on_agent_startup', "Démarrer avec l&apos;Agent");
define('OGP_LANG_autostart_on_agent_startup_info', "Démarrer automatiquement la deamon Fast Download au lancement de l&apos;Agent");
define('OGP_LANG_port_forwarded_to_80', "Port redirigé depuis 80");
define('OGP_LANG_port_forwarded_to_80_info', "Activer cette option si le port configuré pour le deamon Fast Download a été redirigé depuis le port 80, comme ça le port sera caché dans les URLs.");
define('OGP_LANG_current_access_rules', "Règles d&apos;accès actuelles");
?>