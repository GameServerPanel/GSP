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

define('LANG_error', "Erreur");
define('LANG_title', "Interface web TeamSpeak 3");
define('LANG_update_available', "<h3>Attention : Une nouvelle version (v%1) de ce logiciel est disponible : <a href=\"%2\" target=\"_blank\">%2</a>.</h3>");
define('LANG_head_logout', "Se déconnecter");
define('LANG_head_vserver_switch', "Changer vServeur");
define('LANG_head_vserver_overview', "Vue d'ensemble vServer");
define('LANG_head_vserver_token', "Gestion des Tokens");
define('LANG_head_vserver_liveview', "Vue en direct");
define('LANG_e_fill_out', "Veuillez remplir tous les champs requis.");
define('LANG_e_upload_failed', "Erreur lors de l'upload.");
define('LANG_e_server_responded', "Le serveur a répondu: ");
define('LANG_e_conn_serverquery', "Impossible de créer l'accès ServerQuery.");
define('LANG_e_conn_vserver', "Impossible de choisir le serveur virtuel.");
define('LANG_e_session_timedout', "Session expirée.");
define('LANG_js_error', "Erreur");
define('LANG_js_ajax_error', "Une erreur AJAX s&apos;est produite: %1.");
define('LANG_js_confirm_server_stop', "Voulez-vous vraiment arrêter le serveur #%1 ?");
define('LANG_js_confirm_server_delete', "Voulez-vous vraiment SUPPRIMER le serveur #%1 ?");
define('LANG_js_notice_server_deleted', "Le serveur %1 a été supprimé avec succès.\nLa vue d'ensemble va être rechargée maintenant.");
define('LANG_js_prompt_banduration', "Durée en heures (0=illimitée): ");
define('LANG_js_prompt_banreason', "Raison (optionel) : ");
define('LANG_js_prompt_msg_to', "Message texte à %1 #%2: ");
define('LANG_js_prompt_poke_to', "Message Poke au client #%1: ");
define('LANG_js_prompt_new_propvalue', "Nouvelle valeur pour '%1': ");
define('LANG_n_server_responded', "Le serveur a répondu: ");
define('LANG_login_serverquery', "Connexion ServerQuery");
define('LANG_login_name', "Nom d'utilisateur");
define('LANG_login_password', "Mot de passe");
define('LANG_login_submit', "Se connecter");
define('LANG_vsselect_headline', "Sélection vServer");
define('LANG_vsselect_id', "ID #");
define('LANG_vsselect_name', "Nom");
define('LANG_vsselect_ip', "IP");
define('LANG_vsselect_port', "Port");
define('LANG_vsselect_state', "Statut");
define('LANG_vsselect_clients', "Clients");
define('LANG_vsselect_uptime', "Uptime");
define('LANG_vsselect_choose', "Sélectionner");
define('LANG_vsselect_start', "Démarrer");
define('LANG_vsselect_stop', "Arrêter");
define('LANG_vsselect_delete', "SUPPRIMER");
define('LANG_vsselect_new_headline', "Créer un nouveau serveur virtuel");
define('LANG_vsselect_new_servername', "Nom du serveur");
define('LANG_vsselect_new_slots', "Slots du client");
define('LANG_vsselect_new_create', "Créer");
define('LANG_vsselect_new_added_ok', "vServer <span class=\"online\">%1</span> a été créé avec succès.");
define('LANG_vsselect_new_added_generated', "Le token généré est:");
define('LANG_vsoverview_virtualserver', "Serveur Virtuel");
define('LANG_vsoverview_information_head', "Information");
define('LANG_vsoverview_connection_head', "Connexion");
define('LANG_vsoverview_info_general_head', "Paramètres généraux");
define('LANG_vsoverview_info_servername', "Nom du serveur");
define('LANG_vsoverview_info_host', "Hôte");
define('LANG_vsoverview_info_state', "Statut");
define('LANG_vsoverview_info_state_port', "Port");
define('LANG_vsoverview_info_uptime', "Uptime");
define('LANG_vsoverview_info_welcomemsg', "Message de<br />bienvenue");
define('LANG_vsoverview_info_hostmsg', "Message d'hôte");
define('LANG_vsoverview_info_hostmsg_mode_output', "sortie");
define('LANG_vsoverview_info_hostmsg_mode_0', "aucune");
define('LANG_vsoverview_info_hostmsg_mode_1', "dans le log du chat");
define('LANG_vsoverview_info_hostmsg_mode_2', "fenêtre");
define('LANG_vsoverview_info_hostmsg_mode_3', "Fenêtre + Déconnexion");
define('LANG_vsoverview_info_req_security', "Niveau de sécurité");
define('LANG_vsoverview_info_req_securitylvl', "obligatoire");
define('LANG_vsoverview_info_hostbanner_head', "Bannière d'hôte");
define('LANG_vsoverview_info_hostbanner_url', "URL");
define('LANG_vsoverview_info_hostbanner_imgurl', "Adresse de l'image");
define('LANG_vsoverview_info_hostbanner_buttonurl', "URL de la bannière d'hôte");
define('LANG_vsoverview_info_antiflood_head', "Anti-Flood");
define('LANG_vsoverview_info_antiflood_warning', "Avertissement activé");
define('LANG_vsoverview_info_antiflood_kick', "Kick activé");
define('LANG_vsoverview_info_antiflood_ban', "Ban activé");
define('LANG_vsoverview_info_antiflood_banduration', "Durée du ban");
define('LANG_vsoverview_info_antiflood_decrease', "Réduire");
define('LANG_vsoverview_info_antiflood_points', "points");
define('LANG_vsoverview_info_antiflood_in_seconds', "secondes");
define('LANG_vsoverview_info_antiflood_points_per_tick', "Point par tick");
define('LANG_vsoverview_conn_total_head', "Total");
define('LANG_vsoverview_conn_total_packets', "paquets");
define('LANG_vsoverview_conn_total_bytes', "octets");
define('LANG_vsoverview_conn_total_send', "envoyés");
define('LANG_vsoverview_conn_total_received', "reçus");
define('LANG_vsoverview_conn_bandwidth_head', "Bande passante");
define('LANG_vsoverview_conn_bandwidth_last', "dernière");
define('LANG_vsoverview_conn_bandwidth_second', "seconde");
define('LANG_vsoverview_conn_bandwidth_minute', "minute");
define('LANG_vsoverview_conn_bandwidth_send', "envoyée");
define('LANG_vsoverview_conn_bandwidth_received', "reçue");
define('LANG_vstoken_token_virtualserver', "Serveur Virtuel");
define('LANG_vstoken_token_head', "Token");
define('LANG_vstoken_token_type', "Type de Groupe");
define('LANG_vstoken_token_id1', "Groupe Serveur/<br />Groupe Canal");
define('LANG_vstoken_token_id2', "(Canal)");
define('LANG_vstoken_token_tokencode', "Token");
define('LANG_vstoken_token_delete', "Supprimer");
define('LANG_vstoken_new_head', "Créer un nouveau token");
define('LANG_vstoken_new_create', "Générer");
define('LANG_vstoken_new_tokentype', "Type de token:");
define('LANG_vstoken_new_servergroup', "Groupe Serveur");
define('LANG_vstoken_new_channelgroup', "Groupe Canal");
define('LANG_vstoken_new_select_group', "GroupeServeur");
define('LANG_vstoken_new_select_channelgroup', "GroupeCanal");
define('LANG_vstoken_new_select_channel', "Canal");
define('LANG_vstoken_new_tokentype_0', "Serveur");
define('LANG_vstoken_new_tokentype_1', "Canal");
define('LANG_vstoken_new_added_ok', "Le token a été généré avec succès.");
define('LANG_vsliveview_server_virtualserver', "Serveur Virtuel");
define('LANG_vsliveview_server_head', "Vue en direct");
define('LANG_vsliveview_liveview_enable_autorefresh', "Rafraîchissement auto");
define('LANG_vsliveview_liveview_tooltip_to_channel', "vers channel #");
define('LANG_vsliveview_liveview_tooltip_switch', "Changer");
define('LANG_vsliveview_liveview_tooltip_send_msg', "Envoyer Message");
define('LANG_vsliveview_liveview_tooltip_poke', "Poke");
define('LANG_vsliveview_liveview_tooltip_kick', "Kick");
define('LANG_vsliveview_liveview_tooltip_ban', "Ban");
define('LANG_vsoverview_banlist_head', "Liste des bans");
define('LANG_vsoverview_banlist_id', "ID #");
define('LANG_vsoverview_banlist_ip', "IP");
define('LANG_vsoverview_banlist_name', "Nom");
define('LANG_vsoverview_banlist_uid', "UniqueID");
define('LANG_vsoverview_banlist_reason', "Raison");
define('LANG_vsoverview_banlist_created', "Crée");
define('LANG_vsoverview_banlist_duration', "Durée");
define('LANG_vsoverview_banlist_end', "Fin");
define('LANG_vsoverview_banlist_unlimited', "illimitée");
define('LANG_vsoverview_banlist_never', "jamais");
define('LANG_vsoverview_banlist_new_head', "Créer un nouveau ban");
define('LANG_vsoverview_banlist_new_create', "Créer");
define('LANG_vsliveview_channelbackup_head', "Sauvegarde de Canal");
define('LANG_vsliveview_channelbackup_get', "Créer et Télécharger");
define('LANG_vsliveview_channelbackup_load', "Envoyer une Sauvegarde de Canal");
define('LANG_vsliveview_channelbackup_load_submit', "Recréer");
define('LANG_vsliveview_channelbackup_new_added_ok', "Sauvegarde de Canal réussie.");
define('LANG_time_day', "jour");
define('LANG_time_days', "jours");
define('LANG_time_hour', "heure");
define('LANG_time_hours', "heures");
define('LANG_time_minute', "minute");
define('LANG_time_minutes', "minutes");
define('LANG_time_second', "seconde");
define('LANG_time_seconds', "secondes");
define('LANG_e_2568', "Vous n'avez pas les droits suffisants.");
define('LANG_temp_folder_not_writable', "Le dossier templates (%s) n'est pas inscriptible.");
define('LANG_unassign_from_subuser', "Désattribuer de l'Utilisateur Secondaire.");
define('LANG_assign_to_subuser', "Attribuer à l'Utilisateur Secondaire.");
define('LANG_select_subuser', "Sélectionner un Utilisateur Secondaire.");
define('LANG_no_ts3_servers_assigned_to_account', "Il n'y a pas de serveur assigné sur votre compte.");
define('LANG_change_virtual_server', "Changer de Serveur Virtuel");
define('LANG_change_remote_server', "Changer de Serveur Distant");
?>