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

define('LANG_maintenance_mode', "Vedligeholdelse");
define('LANG_maintenance_mode_info', "Slå panelet fra for normale brugere. Kun administratore kan tilgå det under Vedligeholdelse ");
define('LANG_maintenance_title', "Vedligeholdes Title");
define('LANG_maintenance_title_info', "Denne title bliver vist, til normale brugere, under vedligeholdes.");
define('LANG_maintenance_message', "Vedligeholde Besked");
define('LANG_maintenance_message_info', "Denne besked bliver vist til almindelige brugere imens vedligeholde fortages.");
define('LANG_update_settings', "Opdatere Indstillinger");
define('LANG_settings_updated', "Indstillinger opdateret");
define('LANG_panel_language', "Panel Sprog");
define('LANG_panel_language_info', "Dette sprog er standard sprog for panel. Brugere ka det, til deres eget sprog, fra redigere profil.");
define('LANG_page_auto_refresh', "Siden genopfrisker automatisk");
define('LANG_page_auto_refresh_info', "Automatisk genopfrisk sidens indehold, bliver primært brugt til panel fejlfinding. Under normal brug, burde den været sat til aktiveret.");
define('LANG_smtp_server', "Udgående E-Mail Server");
define('LANG_smtp_server_info', "Dette er en udgående mail server (SMTP server), som bliver brugt til, f.eks, til at sende glemte adgangskoder til brugere, lokalvært er som standard.");
define('LANG_panel_email_address', "Udgående E-Mail Addresse");
define('LANG_panel_email_address_info', "Dette er e-mail adressen, som er fra feltet, hvor adgangskoder bliver sendt til brugerne.");
define('LANG_panel_name', "Panel navn");
define('LANG_panel_name_info', "Navnet af panelet som er vist på side titlen. Denne Værdi vil overskive alle side titlerne, Hvis ikke den er tom.");
define('LANG_feed_enable', "Aktivere LGSL Feed");
define('LANG_feed_enable_info', "Hvis din internetudbyder har en firewall  som blokere query porten, så skal du åbne porten manuelt.");
define('LANG_feed_url', "Feed URL");
define('LANG_feed_url_info', "GrayCube.com deler LGSL feed lin til:<br><b>http://www.greycube.co.uk/lgsl/feed/lgsl_files/lgsl_feed.php</b>");
define('LANG_charset', "Character Encoding");
define('LANG_charset_info', "UTF8, ISO, ASCII, etc... Overrides the character encoding defined in language files. Leave it blank to use language default.");
define('LANG_steam_user', "Steam Bruger");
define('LANG_steam_user_info', "Denne bruger bruges til at logge ind på steam, for at downloade nogle nye spil, så som CS: GO.");
define('LANG_steam_pass', "Steam Adgangskode");
define('LANG_steam_pass_info', "Skriv din steam adgangskode her.");
define('LANG_steam_guard', "Steam Guard");
define('LANG_steam_guard_info', "Nogle brugere har steam guard aktiveret, for at beskyttet deres konto mod hackning,<br>denne kode er sent til steam konto's email, når den første steam opdatering starter.");
define('LANG_smtp_port', "SMTP Port");
define('LANG_smtp_port_info', "Hvis SMTP porten ikke er sat standard port (25) Så skriv SMTP porten her.");
define('LANG_smtp_login', "SMTP Bruger");
define('LANG_smtp_login_info', "Hvis din SMTP server behøver godkendelse, så skriv din brugernavn her.");
define('LANG_smtp_passw', "SMTP Kodeord");
define('LANG_smtp_passw_info', "Hvis du ikke har sat et kodeord, vil SMTP godkendelse blive slået fra.");
define('LANG_smtp_secure', "SMTP Secure");
define('LANG_smtp_secure_info', "Brug SSL/TLS til at forbinde med SMTP server");
define('LANG_time_zone', "Tids Zone");
define('LANG_time_zone_info', "Sætter den standarde tidszone, bruges af alle dato/tids funktioner.");
define('LANG_query_cache_life', "Query cache liv");
define('LANG_query_cache_life_info', "Indstil timeout i sekunder, før serveren status bliver genopfrisket.");
define('LANG_query_num_servers_stop', "fraslå Game Server Queries efter");
define('LANG_query_num_servers_stop_info', "brug denne indstilling til at fraslå queries hvis en bruger ejer flere Spil servere end dette antal specificeret til at få panelet til at indlæse hurtigere.");
define('LANG_editable_email', "Redigerbar E-mail Addrese");
define('LANG_editable_email_info', "Vælg om brugere kan redigere deres e-mail adresser eller ikke.");
define('LANG_old_dashboard_behavior', "Gammel Instrumentpanel adfærd");
define('LANG_old_dashboard_behavior_info', "Det gamle instrumentpanel kørte langsommere, men viser mere server information  (f.eks. Nuværende spillere og maps).");
define('LANG_rsync_available', "Tilgængelige Rsync servere");
define('LANG_rsync_available_info', "Vælg hvilke serverliste der vil blive vist i rsync installationen");
define('LANG_all_available_servers', "Alle tilgængelige servere ( rsync_sites.list + rsync_sites_local.list )");
define('LANG_only_remote_servers', "Only remote servers ( rsync_sites.list )");
define('LANG_only_local_servers', "Only local servers ( rsync_sites_local.list )");
define('LANG_header_code', "Header code");
define('LANG_header_code_info', "Here you can write your own header code (like HTML code, Embed Code etc.) without editing the theme layout.");
define('LANG_support_widget_title', "Support widget title");
define('LANG_support_widget_title_info', "A custom title for the support widget in the Dashboard.");
define('LANG_support_widget_content', "Support widget content");
define('LANG_support_widget_content_info', "The content of the support widget (HTML code allowed).");
define('LANG_support_widget_link', "Support widget link");
define('LANG_support_widget_link_info', "The URL of your support site.");
define('LANG_recaptcha_site_key', "Recaptcha Site Key");
define('LANG_recaptcha_site_key_info', "The site key provided to you by Google.");
define('LANG_recaptcha_secret_key', "Recaptcha Secret Key");
define('LANG_recaptcha_secret_key_info', "The secret key provided to you by Google.");
define('LANG_recaptcha_use_login', "Use Recaptcha on Login");
define('LANG_recaptcha_use_login_info', "If enabled, users will have to solve the Not a Robot Recaptcha when attempting to login.");
define('LANG_login_attempts_before_banned', "Number of failed login attempts before user is banned");
define('LANG_login_attempts_before_banned_info', "If a user tries to login with invalid credentials more than this many times, the user will be banned temporarily by the panel.");
define('LANG_custom_github_update_username', "GitHub update username");
define('LANG_custom_github_update_username_info', "Enter your GitHub username ONLY to use your own forked repositories to update OGP. This should only be changed by developers who wish to use their own repos for development rather than checking in possibly buggy code into the main branch.");
define('LANG_remote_query', "Remote query");
define('LANG_remote_query_info', "Use the remote server (agent) to make queries to the game servers (Only GameQ and LGSL).");
define('LANG_check_expiry_by', "Check expiration using");
define('LANG_check_expiry_by_info', "If set to once_logged_in, the user's game server assignments will be automatically deleted if past the expiration date. If set to cron_job, you will need to create a cron task using the cron module to check for the expiration date at a configured interval.");
define('LANG_once_logged_in', "Once Logged In");
define('LANG_cron_job', "Cron Job");
define('LANG_theme_settings', "Tema Indstillinger");
define('LANG_theme', "Tema");
define('LANG_theme_info', "Dette tema, ville være det standarde tema for alle brugere. Brugere kan ændre deres tema, fra profil siden.");
define('LANG_welcome_title', "Velkommenst Titel");
define('LANG_welcome_title_info', "Enables the title that is displayed at the top of the Dashboard.");
define('LANG_welcome_title_message', "Velkomst Titel besked");
define('LANG_welcome_title_message_info', "The title message that is displayed at the top of the Dashboard (HTML code allowed).");
define('LANG_logo_link', "Logos Link");
define('LANG_logo_link_info', "Diverse logo links. <b style='font-size:10px; font-weight:normal;'>(Lad den stå blank, ville linke det til instrumentpanel)</b>");
define('LANG_custom_tab', "Tilpas Faneblad");
define('LANG_custom_tab_info', "Tilføjer tilpasset faneblad, for enden af menuen. <b style='font-size:10px; font-weight:normal;'>(Anvend og genfrisk side for at redigere indstillinger)</b>");
define('LANG_custom_tab_name', "Tilpasset Faneblad Navn");
define('LANG_custom_tab_name_info', "Faneblad vis navn.");
define('LANG_custom_tab_link', "Tilpasset Faneblad Link");
define('LANG_custom_tab_link_info', "Faneblads hyperlink.");
define('LANG_custom_tab_sub', "Tilpasset Under-Faner");
define('LANG_custom_tab_sub_info', "Tilføjer tilpasset under-faner, når musen føres over 'Tilpas Fanblad'.");
define('LANG_custom_tab_sub_name', "Under-Fane #1 Name");
define('LANG_custom_tab_sub_link', "Under-Fane #1 Link");
define('LANG_custom_tab_sub_name2', "Under-Fane #2 Name");
define('LANG_custom_tab_sub_link2', "Under-Fane #2 Link");
define('LANG_custom_tab_sub_name3', "Under-Fane #3 Name");
define('LANG_custom_tab_sub_link3', "Under-Fane #3 Link");
define('LANG_custom_tab_sub_name4', "Under-Fane #4 Name");
define('LANG_custom_tab_sub_link4', "Under-Fane #4 Link");
define('LANG_custom_tab_target_blank', "Tilpasset Fane-blade henvisning");
define('LANG_custom_tab_target_blank_info', "Sets all the tabs target. <b style='font-size:10px; font-weight:normal;'>(Self_Page = Opens link on same page. New_Page  =  Opens link on new tab.)</b>");
define('LANG_bg_wrapper', "Indpaknings Baggrund");
define('LANG_bg_wrapper_info', "Indpaknings baggrunds billed. <b style='font-size:10px; font-weight:normal;'>(Fungere kun på nogle temaer.)</b>");
define('LANG_show_server_id_game_monitor', "Show Server IDs on Game Monitor page");
define('LANG_show_server_id_game_monitor_info', "Show the game server ID column on the Game Monitor for matching up files created by the Agent to the actual game server.");
define('LANG_default_game_server_home_path_prefix', "Default game server home directory prefix");
define('LANG_default_game_server_home_path_prefix_info', "Enter a path prefix for where you want game server homes to be created by default. You can use \"{USERNAME}\" in the path which will be replaced with the OGP username the game server is being assigned to.  You can use \"{GAMEKEY}\" in the path which will be replaced with a friendly lowercase name.  You can use \"{SKIPID}\" anywhere in the path to skip appending the home ID to the path.  Example: /ogp/games/{USERNAME}/{GAMEKEY}{SKIPID} will become /ogp/games/username/arkse/.  Example 2:  /ogp/games will become /ogp/games/1 where 1 is the game servers ID.");
define('LANG_use_authorized_hosts', "Limit API to Defined Authorized Hosts");
define('LANG_use_authorized_hosts_info', "Enable this setting to only allow API calls from pre-defined and approved IP addresses.&nbsp; Approved addresses can be set on this page once the setting has been enabled.&nbsp; If this setting is disabled, a user using a valid key will have access to the API from any IP address.&nbsp; Users using a valid key will be able to use the API to manage any game server they have permissions to administrate.");
define('LANG_setup_api_authorized_hosts', "Setup API authorized hosts");
define('LANG_autohorized_hosts', "Authorized hosts");
define('LANG_add', "Add");
define('LANG_remove', "Remove");
define('LANG_default_trusted_hosts', "Default Trusted Hosts");
define('LANG_trusted_host_or_proxy_addresses_or_cidr', "Trusted Hosts or Proxies (IPv4/IPv6 Addresses or CIDR)");
define('LANG_trusted_forwarded_ip_addresses_or_cidr', "Trusted Forwarded IPs (IPv4/IPv6 Addresses or CIDR)");
define('LANG_reset_game_server_order', "Reset Game Server Ordering");
define('LANG_reset_game_server_order_info', "Resets game server ordering back to the default of using the server ID");


?>
