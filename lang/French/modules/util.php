<?php
/*
 *
 * OGP - Open Game Panel
 * Copyright (C) 2008 - 2017 The OGP Development Team
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

define('LANG_module_name', "Utilitaires");
define('LANG_ping', "Ping");
define('LANG_traceroute', "Traceroute");
define('LANG_network_tools', "Outils Réseau");
define('LANG_sourcemod_admins', "Admins Sourcemod");
define('LANG_steam_converter', "Convertisseur SteamID");
define('LANG_your_ip', "Votre adresse IP:");
define('LANG_loading_agents', "Chargement des Agents en ligne...");
define('LANG_loading_failed', "Echec du chargement des Agents.");
define('LANG_agents_offline', "Tous les Agents sont hors-ligne.");
define('LANG_no_commands', "Désolé, votre compte d'utilisateur n'a aucune commande disponible.");
define('LANG_remote_target', "Adresse IP cible:");
define('LANG_command', "Commande:");
define('LANG_select_agent', "Sélectionner un Agent:");
define('LANG_chdir_failed', "Erreur: chdir() returned false.");
define('LANG_agent_invalid', "Agent spécifié invalide.");
define('LANG_networktools_agent_offline', "Impossible d'exécuter votre commande sur l'Agent sélectionné car il est hors-ligne.");
define('LANG_target_empty', "Aucune cible distante spécifiée.");
define('LANG_command_empty', "Aucune commande sélectionnée.");
define('LANG_command_unavilable', "La commande spécifiée est indisponible sur l'Agent sélectionné.");
define('LANG_target_invalid', "IP/Nom d'Hôte entré invalide.");
define('LANG_exec_failed', "Temps d'attente de la requête expiré.");
define('LANG_command_no_access', "Vous n'avez pas accès à cette commande. L'incident va être enregistré.");
define('LANG_command_hacking_attempt', "Caractères entrés interdits. L'incident va être enregistré.");
define('LANG_command_bad_characters', "Tentative d'exécution d'une commande avec des caractères malveillants. Entrée reçue: %s %s");
define('LANG_command_no_permissions', "Tentative d'exécution d'une commande avec des permissions insuffisantes. Entrée reçue: %s %s");
define('LANG_command_executed', "La commande suivante a bien été envoyée: %s %s");
define('LANG_no_servers', "Il n'y a pas de serveur de jeu disponible sur votre compte.");
define('LANG_select_server', "Choisissez un serveur:");
define('LANG_select_server_option', "Sélectionner...");
define('LANG_steamid', "Steam ID:");
define('LANG_immunity', "Immunité:");
define('LANG_sourcemod_perms', "Permissions Sourcemod:");
define('LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('LANG_sourcemod_perm_custom', "Sourcemod Custom Flags");
define('LANG_sourcemod_flag_a', "Place Réservée.");
define('LANG_sourcemod_flag_b', "Admin générique; requis pour les admins.");
define('LANG_sourcemod_flag_c', "Kicker d'autres joueurs.");
define('LANG_sourcemod_flag_d', "Bannir d'autres joueurs.");
define('LANG_sourcemod_flag_e', "Enlever les bannissements.");
define('LANG_sourcemod_flag_f', "Tuer / blesser d'autres joueurs.");
define('LANG_sourcemod_flag_g', "Changer la carte ou des fonctionnalités majeures de gameplay.");
define('LANG_sourcemod_flag_h', "Changer la plupart des CVARs.");
define('LANG_sourcemod_flag_i', "Exécuter les fichiers de configuration.");
define('LANG_sourcemod_flag_j', "Privilèges spéciaux du chat.");
define('LANG_sourcemod_flag_k', "Initier ou créer des votes.");
define('LANG_sourcemod_flag_l', "Définir un mot de passe sur le serveur.");
define('LANG_sourcemod_flag_m', "Utiliser des commandes RCON.");
define('LANG_sourcemod_flag_n', "Changer sv_cheats ou utiliser des commandes de triche.");
define('LANG_sourcemod_flag_o', "Groupe Perso 1.");
define('LANG_sourcemod_flag_p', "Groupe Perso 2.");
define('LANG_sourcemod_flag_q', "Groupe Perso 3.");
define('LANG_sourcemod_flag_r', "Groupe Perso 4.");
define('LANG_sourcemod_flag_s', "Groupe Perso 5.");
define('LANG_sourcemod_flag_t', "Groupe Perso 6.");
define('LANG_rcon_reload_admins_failed', "Echec du rechargement du cache admin via RCON; est-il en ligne?");
define('LANG_reload_admins_failed', "Echec du rechargement du cache admin; \"sm_reloadadmins\" est une commande inconnue.");
define('LANG_reload_admins_success', "Ajout de %s à admins_simple.ini réussi, ainsi que le rechargement du cache admin.");
define('LANG_add_success_no_rcon', "Ajout de %s à votre fichier admins_simple.ini réussi, mais impossible de recharger le cache admin.");
define('LANG_writefile_error', "Il y a eu une erreur inconnue lors de l'écriture de: %s");
define('LANG_remotefile_nonexistent', "Impossible d'ajouter un nouvel admin. Le fichier d'admin: %s n'existe pas sur ce serveur");
define('LANG_empty_flag_list', "Vous n'avez sélectionné aucun admin flags.");
define('LANG_invalid_steam_format', "Le SteamID entré ne correspond pas à ce qui est requis.");
define('LANG_selected_server_offline', "Impossible d'ajouter un admin, l'Agent qui contrôle le serveur sélectionné est déconnecté.");
define('LANG_malformed_form', "Vous avez soumis un formulaire avec des éléments cachés mal formés - impossible d'ajouter un administrateur.");
define('LANG_empty_form_data', "Veuillez remplir tous les éléments du formulaire.");
define('LANG_server_not_selected', "Vous n'avez pas sélectionné de serveur.");
define('LANG_invalid_steamid', "Vous avez entré un Steam ID invalide.");
define('LANG_invalid_immunity', "Vous avez entré une valeur d'immunité invalide.");
define('LANG_submit', "Envoyer");
define('LANG_post_failed', "L'action POST a échoué. Impossible d'obtenir une réponse.");
define('LANG_amx_mod_admins', "Admins AMX mod X");
define('LANG_amx_login_type', "Type de login");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Pseudo + Password");
define('LANG_nickname', "Pseudo");
define('LANG_amx_mod_perms', "AMX mod X Permissions:");
define('LANG_amx_mod_perm_root', "AMX mod X All Flags.");
define('LANG_amx_mod_perm_custom', "AMX mod X Custom Flags.");
define('LANG_amx_mod_flag_a', "immunity (ne peut pas être kicked/baned/slayed/slaped et affecté par d&apos;autres commandes)");
define('LANG_amx_mod_flag_b', "reservation (peut joindre des slots réservés)");
define('LANG_amx_mod_flag_c', "commande amx_kick");
define('LANG_amx_mod_flag_d', "commandes amx_ban et amx_unban");
define('LANG_amx_mod_flag_e', "commandes amx_slay et amx_slap");
define('LANG_amx_mod_flag_f', "commande amx_map");
define('LANG_amx_mod_flag_g', "commande amx_cvar (toutes les cvars ne seront pas disponibles)");
define('LANG_amx_mod_flag_h', "commande amx_cfg");
define('LANG_amx_mod_flag_i', "amx_chat et autres commandes de chat");
define('LANG_amx_mod_flag_j', "amx_vote et autres commandes de vote");
define('LANG_amx_mod_flag_k', "accès à la cvar sv_password (par commande amx_cvar)");
define('LANG_amx_mod_flag_l', "accès à la commande amx_rcon et la cvar rcon_password (par commande amx_cvar)");
define('LANG_amx_mod_flag_m', "custom level A (pour plugins additionnels)");
define('LANG_amx_mod_flag_n', "custom level B");
define('LANG_amx_mod_flag_o', "custom level C");
define('LANG_amx_mod_flag_p', "custom level D");
define('LANG_amx_mod_flag_q', "custom level E");
define('LANG_amx_mod_flag_r', "custom level F");
define('LANG_amx_mod_flag_s', "custom level G");
define('LANG_amx_mod_flag_t', "custom level H");
define('LANG_amx_mod_flag_u', "accès menu");
?>