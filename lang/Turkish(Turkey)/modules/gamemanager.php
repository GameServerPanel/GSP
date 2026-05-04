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

define('LANG_no_games_to_monitor', "Size Gösterilecek Henüz Bir Oyun Sunucusu Verilmedi.");
define('LANG_status', "Durum");
define('LANG_fail_no_mods', "Bu oyun için etkin mod yok! OGP yöneticinizden size atanan oyuna mod (modlar) eklemesini istemeniz gerekir.");
define('LANG_no_game_homes_assigned', "Hesabınıza Verilmiş Henüz Bir Sunucu Bulunmuyor");
define('LANG_select_game_home_to_configure', "Özelleştirmek İstediğiniz Sunucuyu Seçiniz.");
define('LANG_file_manager', "Dosya Yöneticisi");
define('LANG_configure_mods', "Modları konfigüre et");
define('LANG_install_update_steam', "Steam ile Kur/Güncelle");
define('LANG_install_update_manual', "Elle Kur/Güncelle");
define('LANG_assign_game_homes', "Oyun sunucusu ata");
define('LANG_user', "Kullanıcı");
define('LANG_group', "Grup");
define('LANG_start', "Başlat");
define('LANG_ogp_agent_ip', "OGP Temsilci IP");
define('LANG_max_players', "Maksimum Oyuncu");
define('LANG_max', "Maksimum");
define('LANG_ip_and_port', "IP ve Port");
define('LANG_available_maps', "Kullanılabilir Haritalar");
define('LANG_map_path', "Harita Yolu");
define('LANG_available_parameters', "Kullanılabilir Parametreler");
define('LANG_start_server', "Sunucuyu Başlat");
define('LANG_start_wait_note', "Sunucunun başlaması biraz zaman alabilir. Lütfen tarayıcınızı kapatmadan bekleyiniz.");
define('LANG_game_type', "Oyun Türü");
define('LANG_map', "Harita");
define('LANG_starting_server', "Sunucu başlatılıyor, lütfen bekleyin...");
define('LANG_starting_server_settings', "Sunucu belirtilen ayarlarla başlatılıyor");
define('LANG_startup_params', "Başlama Parametreleri");
define('LANG_startup_cpu', "CPU the server is running on");
define('LANG_startup_nice', "Nice value of the server");
define('LANG_game_home', "Home Path");
define('LANG_server_started', "Server started successfully.");
define('LANG_no_parameter_access', "You do not have access to parameters.");
define('LANG_extra_parameters', "Extra Parameters");
define('LANG_no_extra_param_access', "You do not have access to extra parameters.");
define('LANG_extra_parameters_info', "These parameters are put to the end of the command line when the game is started.");
define('LANG_game_exec_not_found', "The game executable %s was not found from the remote server.");
define('LANG_select_params_and_start', "Select the startup parameters for the server and press '%s'.");
define('LANG_no_ip_port_pairs_assigned', "No IP Port pairs assigned for this home. If you do not have access to home editing contact your admin.");
define('LANG_unable_to_get_log', "Unable to get log, retval %s.");
define('LANG_server_binary_not_executable', "Server binary is not executable. Check you have proper permissions in the server home directory.");
define('LANG_server_not_running_log_found', "Server is not running, but log is found. NOTE: This log might not be related to the last server startup.");
define('LANG_ip_port_pair_not_owned', "IP:PORT pair not owned.");
define('LANG_unsuitable_maxplayers_value_maximum_reachable_number_of_slots_has_been_set', "Unsuitable maxplayers value, maximum reachable number of slots has been set.");
define('LANG_server_running_not_responding', "Server is running, but its not responding,<br>there might be a some kind of problem and you might want to ");
define('LANG_update_started', "Update started, please wait...");
define('LANG_failed_to_start_steam_update', "Failed to start Steam update. See agent log.");
define('LANG_failed_to_start_rsync_update', "Failed to start Rsync update. See agent log.");
define('LANG_update_completed', "Update completed successfully.");
define('LANG_update_in_progress', "Update in progress, please wait...");
define('LANG_refresh_steam_status', "Refresh Steam status");
define('LANG_refresh_rsync_status', "Refresh Rsync status");
define('LANG_server_running_cant_update', "Server running so update is not possible. Stop the server before update.");
define('LANG_xml_steam_error', "Selected server type does not support steam install/update.");
define('LANG_mod_key_not_found_from_xml', "Mod key '%s' not found from the XML file.");
define('LANG_stop_update', "Güncellemeyi durdur");
define('LANG_statistics', "İstatiskler");
define('LANG_servers', "Sunucular");
define('LANG_players', "Oyuncular");
define('LANG_current_map', "Şu Anki Harita");
define('LANG_stop_server', "Sunucuyu Durdur");
define('LANG_server_ip_port', "Sunucu IP:Port");
define('LANG_server_name', "Sunucu Adı");
define('LANG_server_id', "Sunucu ID'si");
define('LANG_player_name', "Oyuncu Adı");
define('LANG_score', "Skor");
define('LANG_time', "Zaman");
define('LANG_no_rights_to_stop_server', "Bu sunucuyu durdurmak için yetkiniz yok.");
define('LANG_no_ogp_lgsl_support', "This server (running: %s) does not have LGSL support in OGP and its statistics can not be shown.");
define('LANG_server_status', "Server on %s is %s.");
define('LANG_server_stopped', "Server '%s' has been stopped.");
define('LANG_if_want_to_start_homes', "If you want to start game servers go to %s.");
define('LANG_view_log', "Log Viewer");
define('LANG_if_want_manage', "If you want to manage your games you can do it in the");
define('LANG_columns', "columns");
define('LANG_group_users', "Grup kullanıcıları:");
define('LANG_assigned_to', "Atanan Kişi:");
define('LANG_restart_server', "Sunucuyu Yeniden Başlat");
define('LANG_restarting_server', "Sunucu yeniden başlatılıyor, lütfen bekleyin...");
define('LANG_server_restarted', "Sunucu %s yeniden başlatıldı.");
define('LANG_server_not_running', "Sunucu çalışmıyor");
define('LANG_address', "Adres");
define('LANG_owner', "Sahip");
define('LANG_operations', "İşlemler");
define('LANG_search', "Ara");
define('LANG_maps_read_from', "Maps read from ");
define('LANG_file', "dosya");
define('LANG_folder', "klasör");
define('LANG_unable_retrieve_mod_info', "Unable to retrieve mod information from database.");
define('LANG_unexpected_result_libremote', "Unexpected result from libremote, please inform developers.");
define('LANG_unable_get_info', "Unable to get the required information for startup, blocking startup.");
define('LANG_server_already_running', "Server already running. If you do not see the server in the Game Monitor, there might be a somekind of problem and you might want to");
define('LANG_already_running_stop_server', "Sunucuyu durdur.");
define('LANG_error_server_already_running', "ERROR: Server already running on port");
define('LANG_failed_start_server_code', "Failed to start the remote server. Error code: %s");
define('LANG_disabled', "devredışı");
define('LANG_not_found_server', "Could not find the remote server with ID");
define('LANG_rcon_command_title', "RCON Komutu");
define('LANG_has_sent_to', "has been sent to");
define('LANG_need_set_remote_pass', "You need to set the remote control password on");
define('LANG_before_sending_rcon_com', "before sending rcon commands to it.");
define('LANG_retry', "Retry");
define('LANG_page', "sayfa");
define('LANG_server_cant_start', "sunucu başlatılamaz");
define('LANG_server_cant_stop', "sunucu durdurulamaz");
define('LANG_error_occured_remote_host', "Error occurred on the remote host");
define('LANG_follow_server_status', "You can follow the server status from");
define('LANG_addons', "Eklentiler");
define('LANG_hostname', "Hostname");
define('LANG_rsync_install', "[Rsync Install]");
define('LANG_ping', "Ping");
define('LANG_team', "Takım");
define('LANG_deaths', "Ölümler");
define('LANG_pid', "PID");
define('LANG_skill', "Yetenek");
define('LANG_AIBot', "Yapay Zeka Botu");
define('LANG_steamid', "Steam ID");
define('LANG_player', "Oyuncu");
define('LANG_port', "Port");
define('LANG_rcon_presets', "RCON önayarları");
define('LANG_update_from_local_master_server', "Update from local Master Server");
define('LANG_update_from_selected_rsync_server', "Update from selected Rsync server");
define('LANG_execute_selected_server_operations', "Execute selected server operations");
define('LANG_execute_operations', "Execute operations");
define('LANG_account_expiration', "Account expiration");
define('LANG_mysql_databases', "MySQL Databases");
define('LANG_failed_querying_server', "* Failed querying the server.");
define('LANG_query_protocol_not_supported', "* There is no query protocol in OGP that can support this server.");
define('LANG_queries_disabled_by_setting_disable_queries_after', "Queries disabled by setting: Disable queries after: %s, since you have %s servers.<br>");
define('LANG_presets_for_game_and_mod', "RCON presets for %s and mod %s");
define('LANG_name', "Ad");
define('LANG_command', "RCON&nbsp;Command");
define('LANG_add_preset', "Önayar ekle");
define('LANG_edit_presets', "Önayarları düzenle");
define('LANG_del_preset', "Sil");
define('LANG_change_preset', "Değiştir");
define('LANG_send_command', "Komut gönder");
define('LANG_starting_copy_with_master_server_named', "Starting copy with master server named '%s'...");
define('LANG_starting_sync_with', "Starting sync with %s...");
define('LANG_refresh_interval', "Log refreshing interval");
define('LANG_finished_manual_update', "Finished manual update.");
define('LANG_failed_to_start_file_download', "Failed to start file download");
define('LANG_game_name', "Oyun adı");
define('LANG_dest_dir', "Destination directory");
define('LANG_remote_server', "Uzak Sunucu");
define('LANG_file_url', "Dosya URL");
define('LANG_file_url_info', "The URL of the file that is uploaded and uncompressed to the directory.");
define('LANG_dest_filename', "Destination Filename");
define('LANG_dest_filename_info', "The filename for the destination file.");
define('LANG_update_server', "Sunucuyu Güncelle");
define('LANG_unavailable', "Kullanım dışı");
define('LANG_upload_map_image', "Harita görüntüsü yükle");
define('LANG_upload_image', "Görüntü yükle");
define('LANG_jpg_gif_png_less_than_1mb', "Görüntü 1 mb.tan küçük ve jpg, gif ya da png olmalıdır.");
define('LANG_check_dev_console', "Error during uploading file, please check the browser developer console.");
define('LANG_uploaded_successfully', "Başarıyla yüklendi");
define('LANG_cant_create_folder', "Can't create folder:<br><b>%s</b>");
define('LANG_cant_write_file', "Can't write file:<br><b>%s</b>");
define('LANG_exceeded_php_directive', "Exceeded PHP directive.<br><b>%s</b>.");
define('LANG_unknown_errors', "Bilinmeyen hatalar.");
define('LANG_directory', "Directory");
define('LANG_view_player_commands', "Oyuncu Komutlarını Görüntüle");
define('LANG_hide_player_commands', "Oyuncu Komutlarını Gizle");
define('LANG_no_online_players', "Çevrimiçi oyuncu yok");
define('LANG_invalid_game_mod_id', "Invalid Game/Mod ID specified.");
define('LANG_auto_update_title_popup', "Steam Auto Update Link");
define('LANG_auto_update_popup_html', "<p>Use the link below to check and automatically update your game server via Steam if needed.&nbsp; You can query it using a cronjob or manually initiate the process.</p>");
define('LANG_api_links_popup_html', "<p>Select an action you would like to perform using the OGP API for this game server.&nbsp; Then, use the link below to perform your desired action.&nbsp; You can run your desired action using a cronjob or by making a direct request to it.</p>");
define('LANG_auto_update_copy_me', "Kopyala");
define('LANG_auto_update_copy_me_success', "Kopyalandı");
define('LANG_auto_update_copy_me_fail', "Failed to copy. Please, manually copy the link.");
define('LANG_get_steam_autoupdate_api_link', "Auto Update Link");
define('LANG_show_api_actions', "Show API Actions");
define('LANG_api_links', "API Links");
define('LANG_update_attempt_from_nonmaster_server', "User %s attempted to update home_id %d from a non-master server. (Home ID: %d)");
define('LANG_attempting_nonmaster_update', "You are attempting to update this server from a non-master server.");
define('LANG_cannot_update_from_own_self', "Local server update may not run on a Master server.");
define('LANG_show_server_id', "Sunucu ID'lerini Göster");
define('LANG_hide_server_id', "Sunucu ID'lerini Gizle");
define('LANG_edit_configuration_files', "Konfigürasyon Dosyalarını Düzenle");
define('LANG_admin', "Yönetici");
define('LANG_cid', "CID");
define('LANG_phan', "Phantom");
define('LANG_sec', "Saniye");
define('LANG_unknown_rsync_mirror', "You attempted to start an update from a mirror which doesn't exist.");
define('LANG_custom_fields', "Custom Fields");
?>
