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

define('LANG_module_name', "Araçlar");
define('LANG_ping', "Ping");
define('LANG_traceroute', "Traceroute");
define('LANG_network_tools', "Ağ Araçları");
define('LANG_sourcemod_admins', "Sourcemod Yöneticileri");
define('LANG_steam_converter', "SteamID Dönüştürücü");
define('LANG_your_ip', "IP Adresiniz:");
define('LANG_loading_agents', "Çevrimiçi Temsilciler yükleniyor...");
define('LANG_loading_failed', "Temsilcileri yükleme başarısız oldu.");
define('LANG_agents_offline', "Tüm Temsilciler çevrimdışı.");
define('LANG_no_commands', "Üzgünüz, kullanıcı hesabınızın mevcut bir komutu yok.");
define('LANG_remote_target', "Hedef IP Adresi:");
define('LANG_command', "Komut:");
define('LANG_select_agent', "Temsilci Seç:");
define('LANG_chdir_failed', "Hata: chdir() yanlış döndürüldü.");
define('LANG_agent_invalid', "Geçersiz Temsilci belirtildi.");
define('LANG_networktools_agent_offline', "Çevrimdışı olduğundan, komutunuz seçilen Temsilcide yürütülemiyor.");
define('LANG_target_empty', "Verilen hiçbir uzak hedef yok.");
define('LANG_command_empty', "Hiçbir komut seçilmedi.");
define('LANG_command_unavilable', "Seçilen komut seçilen temsilcide kullanılamaz.");
define('LANG_target_invalid', "Geçersiz IP / ana makine adı girildi.");
define('LANG_exec_failed', "Yanıt beklerken zaman aşımına uğradı.");
define('LANG_command_no_access', "Bu komuta erişiminiz yok. Bu olay günlüğe kaydedilecek.");
define('LANG_command_hacking_attempt', "Kara listeye alınmış karakterler girildi. Bu olay günlüğe kaydedilecek.");
define('LANG_command_bad_characters', "Kötü amaçlı karakterlerle bir komut yürütmeye çalışıldı. Giriş alındı: %s %s");
define('LANG_command_no_permissions', "Yetersiz izinlere sahip bir komut yürütmeye çalışıldı. Giriş alındı: %s %s");
define('LANG_command_executed', "Aşağıdaki komut başarıyla gönderildi: %s %s");
define('LANG_no_servers', "Hesabınıza atanan sunucunuz yok.");
define('LANG_select_server', "Sunucu Seç:");
define('LANG_select_server_option', "Seç...");
define('LANG_steamid', "Steam ID:");
define('LANG_immunity', "Dokunulmazlık.");
define('LANG_sourcemod_perms', "Sourcemod İzinleri:");
define('LANG_sourcemod_perm_root', "Sourcemod Kök Bayrağı");
define('LANG_sourcemod_perm_custom', "Sourcemod Özel Bayrakları");
define('LANG_sourcemod_flag_a', "Ayrılmış slot erişimi.");
define('LANG_sourcemod_flag_b', "Genel yönetici; Yöneticiler için gerekli.");
define('LANG_sourcemod_flag_c', "Diğer oyuncuları at.");
define('LANG_sourcemod_flag_d', "Diğer oyuncuları yasakla.");
define('LANG_sourcemod_flag_e', "Yasakları kaldır.");
define('LANG_sourcemod_flag_f', "Diğer oyuncuları öldürün/zarar verin.");
define('LANG_sourcemod_flag_g', "Haritayı veya büyük oyun özelliklerini değiştirin.");
define('LANG_sourcemod_flag_h', "Çoğu CVAR'ı değiştirin.");
define('LANG_sourcemod_flag_i', "Yapılandırma dosyalarını yürütün.");
define('LANG_sourcemod_flag_j', "Özel sohbet ayrıcalıkları.");
define('LANG_sourcemod_flag_k', "Başlayın veya oy oluşturun.");
define('LANG_sourcemod_flag_l', "Sunucuda bir şifre ayarlayın.");
define('LANG_sourcemod_flag_m', "RCON komutlarını kullanın.");
define('LANG_sourcemod_flag_n', "sv_cheats değiştirin veya hile komutlarını kullanın.");
define('LANG_sourcemod_flag_o', "Özel Grup 1.");
define('LANG_sourcemod_flag_p', "Özel Grup 2.");
define('LANG_sourcemod_flag_q', "Özel Grup 3.");
define('LANG_sourcemod_flag_r', "Özel Grup 4.");
define('LANG_sourcemod_flag_s', "Özel Grup 5.");
define('LANG_sourcemod_flag_t', "Özel Grup 6.");
define('LANG_rcon_reload_admins_failed', "Yönetici önbelleği RCON aracılığıyla yeniden yüklenemedi; çevrimiçi mi?");
define('LANG_reload_admins_failed', "Yönetici önbelleği yeniden yüklenemedi; \"sm_reloadadmins\" bilinmeyen bir komuttur.");
define('LANG_reload_admins_success', "%sBaşarıyla Eklendi admins_simple.ini dosyasına ve admin önbelleğini yeniden yükledi.");
define('LANG_add_success_no_rcon', "%sadmins_simple.ini dosyanıza Başarıyla Eklendi. Ancak yönetici önbelleğini yeniden yükleyemiyor.");
define('LANG_writefile_error', "Bilinmeyen bir hata oluştu: %s");
define('LANG_remotefile_nonexistent', "Yeni yönetici eklenemedi. Yönetici dosyası:  %s Bu sunucuda mevcut değil.");
define('LANG_empty_flag_list', "Herhangi bir yönetici bayrağı seçmediniz.");
define('LANG_invalid_steam_format', "Girdiğiniz SteamID, istenen desenle eşleşmiyor.");
define('LANG_selected_server_offline', "Bir yönetici eklenemedi, seçilen sunucuyu kontrol eden temsilci çevrimdışı.");
define('LANG_malformed_form', "Yanlış biçimlendirilmiş gizli öğeler içeren bir form gönderdiniz.");
define('LANG_empty_form_data', "Lütfen formun tüm unsurlarını doldurun.");
define('LANG_server_not_selected', "Bir sunucu seçmediniz.");
define('LANG_invalid_steamid', "Geçersiz bir Steam ID girdiniz.");
define('LANG_invalid_immunity', "Geçersiz bir dokunulmazlık değeri girdiniz.");
define('LANG_submit', "Gönder");
define('LANG_post_failed', "POST işlemi başarısız oldu. Yanıt alınamadı.");
define('LANG_amx_mod_admins', "AMX mod X Yöneticileri");
define('LANG_amx_login_type', "Giriş Türü");
define('LANG_amx_login_steamid', "Steam ID");
define('LANG_amx_login_nick_pass', "Kullanıcı adı + şifre");
define('LANG_nickname', "Kullanıcı adı");
define('LANG_amx_mod_perms', "AMX mod X Yetkileri:");
define('LANG_amx_mod_perm_root', "AMX mod X Tüm Bayraklar.");
define('LANG_amx_mod_perm_custom', "AMX mod X Özel Bayraklar.");
define('LANG_amx_mod_flag_a', "dokunulmazlık (tekme / yasaklanamaz / öldürülemez / tokatlanamaz ve diğer komutlardan etkilenmez)");
define('LANG_amx_mod_flag_b', "Rezervasyon (ayrılmış yuvalara katılabilir)");
define('LANG_amx_mod_flag_c', "amx_kick komutu");
define('LANG_amx_mod_flag_d', "amx_ban ve amx_unban komutları");
define('LANG_amx_mod_flag_e', "amx_slay ve amx_slap komutları");
define('LANG_amx_mod_flag_f', "amx_map komutu");
define('LANG_amx_mod_flag_g', "amx_cvar komutu (tüm cvarlar mevcut olmayacaktır)");
define('LANG_amx_mod_flag_h', "amx_cfg komutu");
define('LANG_amx_mod_flag_i', "amx_chat ve diğer sohbet komutları");
define('LANG_amx_mod_flag_j', "amx_vote ve diğer oy komutları");
define('LANG_amx_mod_flag_k', "sv_password cvar'a erişim (amx_cvar komutuyla)");
define('LANG_amx_mod_flag_l', "amx_rcon komutu ve rcon_password cvar'a erişim (amx_cvar komutuyla)");
define('LANG_amx_mod_flag_m', "özel seviye A (ek eklentiler için)");
define('LANG_amx_mod_flag_n', "özel seviye B");
define('LANG_amx_mod_flag_o', "özel seviye C");
define('LANG_amx_mod_flag_p', "özel seviye D");
define('LANG_amx_mod_flag_q', "özel seviye E");
define('LANG_amx_mod_flag_r', "özel seviye F");
define('LANG_amx_mod_flag_s', "özel seviye G");
define('LANG_amx_mod_flag_t', "özel seviye H");
define('LANG_amx_mod_flag_u', "menü erişimi");
?>