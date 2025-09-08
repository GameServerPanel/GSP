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

define('OGP_LANG_module_name', "Utilitaires");
define('OGP_LANG_ping', "Ping");
define('OGP_LANG_traceroute', "Traceroute");
define('OGP_LANG_network_tools', "Outils Réseau");
define('OGP_LANG_sourcemod_admins', "Admins Sourcemod");
define('OGP_LANG_steam_converter', "Convertisseur SteamID");
define('OGP_LANG_your_ip', "Votre adresse IP:");
define('OGP_LANG_loading_agents', "Chargement des Agents en ligne...");
define('OGP_LANG_loading_failed', "Echec du chargement des Agents.");
define('OGP_LANG_agents_offline', "Tous les Agents sont hors-ligne.");
define('OGP_LANG_no_commands', "Désolé, votre compte d'utilisateur n'a aucune commande disponible.");
define('OGP_LANG_remote_target', "Adresse IP cible:");
define('OGP_LANG_command', "Commande:");
define('OGP_LANG_select_agent', "Sélectionner un Agent:");
define('OGP_LANG_chdir_failed', "Erreur: chdir() returned false.");
define('OGP_LANG_agent_invalid', "Agent spécifié invalide.");
define('OGP_LANG_networktools_agent_offline', "Impossible d'exécuter votre commande sur l'Agent sélectionné car il est hors-ligne.");
define('OGP_LANG_target_empty', "Aucune cible distante spécifiée.");
define('OGP_LANG_command_empty', "Aucune commande sélectionnée.");
define('OGP_LANG_command_unavilable', "La commande spécifiée est indisponible sur l'Agent sélectionné.");
define('OGP_LANG_target_invalid', "IP/Nom d'Hôte entré invalide.");
define('OGP_LANG_exec_failed', "Temps d'attente de la requête expiré.");
define('OGP_LANG_command_no_access', "Vous n'avez pas accès à cette commande. L'incident va être enregistré.");
define('OGP_LANG_command_hacking_attempt', "Caractères entrés interdits. L'incident va être enregistré.");
define('OGP_LANG_command_bad_characters', "Tentative d'exécution d'une commande avec des caractères malveillants. Entrée reçue: %s %s");
define('OGP_LANG_command_no_permissions', "Tentative d'exécution d'une commande avec des permissions insuffisantes. Entrée reçue: %s %s");
define('OGP_LANG_command_executed', "La commande suivante a bien été envoyée: %s %s");
define('OGP_LANG_no_servers', "Il n'y a pas de serveur de jeu disponible sur votre compte.");
define('OGP_LANG_select_server', "Choisissez un serveur:");
define('OGP_LANG_select_server_option', "Sélectionner...");
define('OGP_LANG_steamid', "Steam ID:");
define('OGP_LANG_immunity', "Immunité:");
define('OGP_LANG_sourcemod_perms', "Permissions Sourcemod:");
define('OGP_LANG_sourcemod_perm_root', "Sourcemod Root Flag");
define('OGP_LANG_sourcemod_perm_custom', "Sourcemod Custom Flags");
define('OGP_LANG_sourcemod_flag_a', "Place Réservée.");
define('OGP_LANG_sourcemod_flag_b', "Admin générique; requis pour les admins.");
define('OGP_LANG_sourcemod_flag_c', "Kicker d'autres joueurs.");
define('OGP_LANG_sourcemod_flag_d', "Bannir d'autres joueurs.");
define('OGP_LANG_sourcemod_flag_e', "Enlever les bannissements.");
define('OGP_LANG_sourcemod_flag_f', "Tuer / blesser d'autres joueurs.");
define('OGP_LANG_sourcemod_flag_g', "Changer la carte ou des fonctionnalités majeures de gameplay.");
define('OGP_LANG_sourcemod_flag_h', "Changer la plupart des CVARs.");
define('OGP_LANG_sourcemod_flag_i', "Exécuter les fichiers de configuration.");
define('OGP_LANG_sourcemod_flag_j', "Privilèges spéciaux du chat.");
define('OGP_LANG_sourcemod_flag_k', "Initier ou créer des votes.");
define('OGP_LANG_sourcemod_flag_l', "Définir un mot de passe sur le serveur.");
define('OGP_LANG_sourcemod_flag_m', "Utiliser des commandes RCON.");
define('OGP_LANG_sourcemod_flag_n', "Changer sv_cheats ou utiliser des commandes de triche.");
define('OGP_LANG_sourcemod_flag_o', "Groupe Perso 1.");
define('OGP_LANG_sourcemod_flag_p', "Groupe Perso 2.");
define('OGP_LANG_sourcemod_flag_q', "Groupe Perso 3.");
define('OGP_LANG_sourcemod_flag_r', "Groupe Perso 4.");
define('OGP_LANG_sourcemod_flag_s', "Groupe Perso 5.");
define('OGP_LANG_sourcemod_flag_t', "Groupe Perso 6.");
define('OGP_LANG_rcon_reload_admins_failed', "Echec du rechargement du cache admin via RCON; est-il en ligne?");
define('OGP_LANG_reload_admins_failed', "Echec du rechargement du cache admin; \"sm_reloadadmins\" est une commande inconnue.");
define('OGP_LANG_reload_admins_success', "Ajout de %s à admins_simple.ini réussi, ainsi que le rechargement du cache admin.");
define('OGP_LANG_add_success_no_rcon', "Ajout de %s à votre fichier admins_simple.ini réussi, mais impossible de recharger le cache admin.");
define('OGP_LANG_writefile_error', "Il y a eu une erreur inconnue lors de l'écriture de: %s");
define('OGP_LANG_remotefile_nonexistent', "Impossible d'ajouter un nouvel admin. Le fichier d'admin: %s n'existe pas sur ce serveur");
define('OGP_LANG_empty_flag_list', "Vous n'avez sélectionné aucun admin flags.");
define('OGP_LANG_invalid_steam_format', "Le SteamID entré ne correspond pas à ce qui est requis.");
define('OGP_LANG_selected_server_offline', "Impossible d'ajouter un admin, l'Agent qui contrôle le serveur sélectionné est déconnecté.");
define('OGP_LANG_malformed_form', "Vous avez soumis un formulaire avec des éléments cachés mal formés - impossible d'ajouter un administrateur.");
define('OGP_LANG_empty_form_data', "Veuillez remplir tous les éléments du formulaire.");
define('OGP_LANG_server_not_selected', "Vous n'avez pas sélectionné de serveur.");
define('OGP_LANG_invalid_steamid', "Vous avez entré un Steam ID invalide.");
define('OGP_LANG_invalid_immunity', "Vous avez entré une valeur d'immunité invalide.");
define('OGP_LANG_submit', "Envoyer");
define('OGP_LANG_post_failed', "L'action POST a échoué. Impossible d'obtenir une réponse.");
define('OGP_LANG_amx_mod_admins', "Admins AMX mod X");
define('OGP_LANG_amx_login_type', "Type de login");
define('OGP_LANG_amx_login_steamid', "Steam ID");
define('OGP_LANG_amx_login_nick_pass', "Pseudo + Password");
define('OGP_LANG_nickname', "Pseudo");
define('OGP_LANG_amx_mod_perms', "AMX mod X Permissions:");
define('OGP_LANG_amx_mod_perm_root', "AMX mod X All Flags.");
define('OGP_LANG_amx_mod_perm_custom', "AMX mod X Custom Flags.");
define('OGP_LANG_amx_mod_flag_a', "immunity (ne peut pas être kicked/baned/slayed/slaped et affecté par d&apos;autres commandes)");
define('OGP_LANG_amx_mod_flag_b', "reservation (peut joindre des slots réservés)");
define('OGP_LANG_amx_mod_flag_c', "commande amx_kick");
define('OGP_LANG_amx_mod_flag_d', "commandes amx_ban et amx_unban");
define('OGP_LANG_amx_mod_flag_e', "commandes amx_slay et amx_slap");
define('OGP_LANG_amx_mod_flag_f', "commande amx_map");
define('OGP_LANG_amx_mod_flag_g', "commande amx_cvar (toutes les cvars ne seront pas disponibles)");
define('OGP_LANG_amx_mod_flag_h', "commande amx_cfg");
define('OGP_LANG_amx_mod_flag_i', "amx_chat et autres commandes de chat");
define('OGP_LANG_amx_mod_flag_j', "amx_vote et autres commandes de vote");
define('OGP_LANG_amx_mod_flag_k', "accès à la cvar sv_password (par commande amx_cvar)");
define('OGP_LANG_amx_mod_flag_l', "accès à la commande amx_rcon et la cvar rcon_password (par commande amx_cvar)");
define('OGP_LANG_amx_mod_flag_m', "custom level A (pour plugins additionnels)");
define('OGP_LANG_amx_mod_flag_n', "custom level B");
define('OGP_LANG_amx_mod_flag_o', "custom level C");
define('OGP_LANG_amx_mod_flag_p', "custom level D");
define('OGP_LANG_amx_mod_flag_q', "custom level E");
define('OGP_LANG_amx_mod_flag_r', "custom level F");
define('OGP_LANG_amx_mod_flag_s', "custom level G");
define('OGP_LANG_amx_mod_flag_t', "custom level H");
define('OGP_LANG_amx_mod_flag_u', "accès menu");
?>