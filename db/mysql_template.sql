-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 08, 2025 at 11:28 AM
-- Server version: 5.7.42-log
-- PHP Version: 7.4.3-4ubuntu2.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `panel_template`
--

-- --------------------------------------------------------

--
-- Table structure for table `ogp_addons`
--

CREATE TABLE `ogp_addons` (
  `addon_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `url` varchar(200) NOT NULL,
  `path` varchar(80) NOT NULL,
  `addon_type` varchar(7) NOT NULL,
  `home_cfg_id` varchar(7) NOT NULL,
  `post_script` longtext NOT NULL,
  `group_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_adminexternallinks`
--

CREATE TABLE `ogp_adminexternallinks` (
  `link_id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `url` varchar(200) NOT NULL,
  `user_id` varchar(7) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_adminlte_serverstats`
--

CREATE TABLE `ogp_adminlte_serverstats` (
  `home_id` int(4) NOT NULL,
  `users_online` int(4) NOT NULL,
  `current_stamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_adminlte_settings`
--

CREATE TABLE `ogp_adminlte_settings` (
  `id` int(20) NOT NULL,
  `user` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_api_tokens`
--

CREATE TABLE `ogp_api_tokens` (
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_arrange_ports`
--

CREATE TABLE `ogp_arrange_ports` (
  `range_id` int(11) NOT NULL,
  `ip_id` int(11) NOT NULL,
  `home_cfg_id` int(11) NOT NULL,
  `start_port` smallint(11) UNSIGNED NOT NULL,
  `end_port` smallint(11) UNSIGNED NOT NULL,
  `port_increment` smallint(11) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Remote servers and IPs';

-- --------------------------------------------------------

--
-- Table structure for table `ogp_backup_restore`
--

CREATE TABLE `ogp_backup_restore` (
  `id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_ban_list`
--

CREATE TABLE `ogp_ban_list` (
  `client_ip` varchar(255) NOT NULL,
  `logging_attempts` int(11) NOT NULL DEFAULT '0',
  `banned_until` varchar(16) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_billing_carts`
--

CREATE TABLE `ogp_billing_carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paid` int(11) DEFAULT '0',
  `date` varchar(16) NOT NULL DEFAULT '0',
  `tax_amount` varchar(16) NOT NULL DEFAULT '0',
  `currency` varchar(3) NOT NULL DEFAULT '0',
  `coupon_id` varchar(11) DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_billing_coupons`
--

CREATE TABLE `ogp_billing_coupons` (
  `id` int(11) NOT NULL,
  `code` varchar(16) NOT NULL,
  `name` varchar(255) NOT NULL,
  `discount` int(11) NOT NULL DEFAULT '0',
  `recurring` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '-1',
  `expires` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_billing_orders`
--

CREATE TABLE `ogp_billing_orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `home_name` varchar(255) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `qty` int(11) NOT NULL,
  `invoice_duration` varchar(16) NOT NULL,
  `max_players` int(11) NOT NULL,
  `price` float(15,2) NOT NULL,
  `remote_control_password` varchar(10) DEFAULT NULL,
  `ftp_password` varchar(10) DEFAULT NULL,
  `cart_id` int(11) NOT NULL,
  `home_id` varchar(255) NOT NULL DEFAULT '0',
  `status` varchar(16) NOT NULL DEFAULT '0',
  `finish_date` varchar(16) NOT NULL DEFAULT '0',
  `extended` tinyint(1) NOT NULL,
  `coupon_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_billing_services`
--

CREATE TABLE `ogp_billing_services` (
  `service_id` int(11) NOT NULL,
  `home_cfg_id` int(11) NOT NULL,
  `mod_cfg_id` int(11) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `remote_server_id` varchar(255) NOT NULL,
  `out_of_stock` varchar(255) NOT NULL,
  `slot_max_qty` int(11) NOT NULL,
  `slot_min_qty` int(11) NOT NULL,
  `price_daily` float(15,4) NOT NULL,
  `price_monthly` float(15,4) NOT NULL,
  `price_year` float(15,4) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `img_url` varchar(255) NOT NULL,
  `ftp` varchar(255) NOT NULL,
  `install_method` varchar(255) NOT NULL,
  `manual_url` varchar(255) NOT NULL,
  `access_rights` varchar(255) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_circular`
--

CREATE TABLE `ogp_circular` (
  `circular_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_circular_recipients`
--

CREATE TABLE `ogp_circular_recipients` (
  `user_id` int(11) NOT NULL,
  `circular_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_config_homes`
--

CREATE TABLE `ogp_config_homes` (
  `home_cfg_id` int(20) NOT NULL,
  `game_key` varchar(64) NOT NULL,
  `game_name` varchar(255) NOT NULL,
  `home_cfg_file` varchar(64) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_config_mods`
--

CREATE TABLE `ogp_config_mods` (
  `mod_cfg_id` int(50) NOT NULL,
  `home_cfg_id` varchar(50) NOT NULL,
  `mod_key` varchar(100) NOT NULL COMMENT 'mod short name - used by the game server for startup commands - ex cstrike',
  `mod_name` varchar(255) NOT NULL COMMENT 'Mod value is user defined string - like Counter-Strike',
  `def_precmd` text,
  `def_postcmd` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_game_mods`
--

CREATE TABLE `ogp_game_mods` (
  `mod_id` int(50) NOT NULL,
  `home_id` int(255) NOT NULL,
  `mod_cfg_id` int(11) NOT NULL,
  `max_players` smallint(3) DEFAULT NULL,
  `extra_params` varchar(255) DEFAULT NULL,
  `cpu_affinity` varchar(64) DEFAULT NULL,
  `nice` smallint(3) DEFAULT '0',
  `precmd` text,
  `postcmd` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='utf8mb4_general_ci';

-- --------------------------------------------------------

--
-- Table structure for table `ogp_home_ip_ports`
--

CREATE TABLE `ogp_home_ip_ports` (
  `ip_id` int(11) NOT NULL,
  `port` int(11) NOT NULL,
  `home_id` int(11) NOT NULL,
  `force_mod_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_lgsl`
--

CREATE TABLE `ogp_lgsl` (
  `id` int(11) NOT NULL,
  `type` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `ip` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `c_port` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `q_port` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `s_port` varchar(5) COLLATE utf8_unicode_ci NOT NULL DEFAULT '0',
  `zone` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `disabled` tinyint(1) NOT NULL DEFAULT '0',
  `comment` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint(1) NOT NULL DEFAULT '0',
  `cache` text COLLATE utf8_unicode_ci NOT NULL,
  `cache_time` text COLLATE utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_logger`
--

CREATE TABLE `ogp_logger` (
  `log_id` int(10) UNSIGNED NOT NULL,
  `date` varchar(20) NOT NULL,
  `user_id` int(11) NOT NULL,
  `ip` varchar(15) NOT NULL,
  `message` varchar(250) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_master_server_homes`
--

CREATE TABLE `ogp_master_server_homes` (
  `home_id` int(11) NOT NULL,
  `home_cfg_id` int(11) NOT NULL,
  `remote_server_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_modules`
--

CREATE TABLE `ogp_modules` (
  `id` smallint(5) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '',
  `folder` varchar(100) NOT NULL DEFAULT '',
  `version` varchar(10) NOT NULL DEFAULT '0',
  `db_version` int(10) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_module_access_rights`
--

CREATE TABLE `ogp_module_access_rights` (
  `module_id` int(11) NOT NULL COMMENT 'This references to modules.id',
  `flag` char(1) NOT NULL,
  `description` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_module_menus`
--

CREATE TABLE `ogp_module_menus` (
  `module_id` int(11) NOT NULL COMMENT 'This references to modules.id',
  `subpage` varchar(64) NOT NULL DEFAULT '',
  `group` varchar(32) NOT NULL,
  `menu_name` varchar(128) NOT NULL,
  `pos` int(10) UNSIGNED NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_mysql_databases`
--

CREATE TABLE `ogp_mysql_databases` (
  `db_id` int(11) NOT NULL,
  `mysql_server_id` int(11) NOT NULL,
  `home_id` int(11) NOT NULL,
  `db_user` varchar(50) NOT NULL,
  `db_passwd` varchar(50) NOT NULL,
  `db_name` varchar(50) NOT NULL,
  `enabled` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_mysql_servers`
--

CREATE TABLE `ogp_mysql_servers` (
  `mysql_server_id` int(11) NOT NULL,
  `remote_server_id` int(11) NOT NULL,
  `mysql_name` varchar(100) NOT NULL,
  `mysql_ip` varchar(255) NOT NULL,
  `mysql_port` int(11) NOT NULL,
  `mysql_root_passwd` varchar(32) DEFAULT NULL,
  `privilegies_str` longtext
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_notification`
--

CREATE TABLE `ogp_notification` (
  `notification_id` int(11) NOT NULL,
  `subject` text NOT NULL,
  `message` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_notification_recipients`
--

CREATE TABLE `ogp_notification_recipients` (
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_rcon_presets`
--

CREATE TABLE `ogp_rcon_presets` (
  `preset_id` int(50) NOT NULL,
  `name` varchar(20) NOT NULL,
  `command` varchar(100) NOT NULL,
  `home_cfg_id` int(50) NOT NULL,
  `mod_cfg_id` int(50) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_remote_servers`
--

CREATE TABLE `ogp_remote_servers` (
  `remote_server_id` int(11) NOT NULL,
  `remote_server_name` varchar(100) NOT NULL,
  `ogp_user` varchar(100) NOT NULL,
  `agent_ip` varchar(255) NOT NULL,
  `agent_port` int(11) NOT NULL,
  `ftp_port` int(11) NOT NULL,
  `encryption_key` varchar(50) NOT NULL,
  `timeout` int(11) NOT NULL,
  `use_nat` int(11) NOT NULL,
  `ftp_ip` varchar(255) NOT NULL,
  `firewall_settings` longtext,
  `display_public_ip` varchar(255) NOT NULL,
  `enabled` int(11) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Remote servers and IPs';

-- --------------------------------------------------------

--
-- Table structure for table `ogp_remote_server_ips`
--

CREATE TABLE `ogp_remote_server_ips` (
  `ip_id` int(11) NOT NULL,
  `remote_server_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_reseller_accounts`
--

CREATE TABLE `ogp_reseller_accounts` (
  `account_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `qty` int(11) DEFAULT NULL,
  `invoice_duration` varchar(7) NOT NULL,
  `discount` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `payment_date` varchar(20) NOT NULL DEFAULT '0',
  `cart_id` int(11) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT '0',
  `available_months` int(11) NOT NULL DEFAULT '0',
  `available_slots` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_reseller_carts`
--

CREATE TABLE `ogp_reseller_carts` (
  `cart_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `paid` int(11) DEFAULT NULL,
  `tax_amount` varchar(20) NOT NULL DEFAULT '0',
  `currency` varchar(3) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_reseller_discount_codes`
--

CREATE TABLE `ogp_reseller_discount_codes` (
  `discount_id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `percentage` int(11) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '0',
  `code` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_reseller_homes`
--

CREATE TABLE `ogp_reseller_homes` (
  `home_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `account_id` int(11) NOT NULL,
  `assigned_slots` int(11) NOT NULL,
  `status` varchar(16) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_reseller_services`
--

CREATE TABLE `ogp_reseller_services` (
  `service_id` int(11) NOT NULL,
  `service_name` varchar(60) NOT NULL,
  `slot_max_qty` int(11) NOT NULL,
  `price_per_month` float(15,4) NOT NULL,
  `price_per_year` float(15,4) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `remote_server_id` int(11) NOT NULL,
  `start_port` int(11) NOT NULL,
  `end_port` int(11) NOT NULL,
  `max_access_rights` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_server_homes`
--

CREATE TABLE `ogp_server_homes` (
  `home_id` int(50) NOT NULL,
  `remote_server_id` int(11) NOT NULL,
  `user_id_main` int(11) NOT NULL,
  `home_path` varchar(500) DEFAULT NULL,
  `home_cfg_id` int(50) NOT NULL,
  `home_name` varchar(500) DEFAULT NULL,
  `control_password` varchar(128) DEFAULT NULL,
  `ftp_password` varchar(128) DEFAULT NULL,
  `last_param` longtext,
  `ftp_login` varchar(32) DEFAULT NULL,
  `ftp_status` int(11) NOT NULL DEFAULT '0',
  `custom_fields` longtext,
  `server_expiration_date` varchar(21) NOT NULL DEFAULT 'X',
  `home_user_order` int(11) NOT NULL DEFAULT '99999'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_settings`
--

CREATE TABLE `ogp_settings` (
  `setting` varchar(63) NOT NULL,
  `value` varchar(1024) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_status_cache`
--

CREATE TABLE `ogp_status_cache` (
  `date_timestamp` char(16) NOT NULL,
  `ip_id` char(3) NOT NULL,
  `port` char(6) NOT NULL,
  `server_status_cache` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_tickets`
--

CREATE TABLE `ogp_tickets` (
  `tid` int(11) NOT NULL,
  `uid` varchar(32) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `subject` varchar(64) NOT NULL,
  `service_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_updated` varchar(22) DEFAULT NULL,
  `status` tinyint(4) NOT NULL,
  `assigned_to` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_ticket_attachments`
--

CREATE TABLE `ogp_ticket_attachments` (
  `attachment_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `reply_id` int(11) DEFAULT NULL,
  `original_name` varchar(255) NOT NULL,
  `unique_name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_ticket_messages`
--

CREATE TABLE `ogp_ticket_messages` (
  `reply_id` int(11) NOT NULL,
  `ticket_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_ip` varbinary(16) NOT NULL,
  `message` text NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `rating` tinyint(4) DEFAULT '0',
  `is_admin` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_ticket_settings`
--

CREATE TABLE `ogp_ticket_settings` (
  `id` int(11) NOT NULL,
  `setting_name` varchar(32) NOT NULL,
  `setting_value` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_ts3_homes`
--

CREATE TABLE `ogp_ts3_homes` (
  `ts3_id` int(50) NOT NULL,
  `rserver_id` int(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  `pwd` varchar(40) NOT NULL,
  `vserver_id` int(50) NOT NULL,
  `user_id` int(50) NOT NULL,
  `port` int(11) DEFAULT '10011'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_tshock`
--

CREATE TABLE `ogp_tshock` (
  `token_id` int(11) NOT NULL,
  `ip` varchar(255) NOT NULL,
  `port` int(11) NOT NULL,
  `token` varchar(64) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_update_blacklist`
--

CREATE TABLE `ogp_update_blacklist` (
  `file_path` varchar(1000) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_users`
--

CREATE TABLE `ogp_users` (
  `user_id` int(11) NOT NULL,
  `users_login` varchar(255) NOT NULL,
  `users_passwd` varchar(255) NOT NULL,
  `users_lang` varchar(20) NOT NULL DEFAULT 'English',
  `users_role` varchar(30) NOT NULL DEFAULT 'user',
  `users_group` varchar(100) DEFAULT NULL,
  `users_fname` varchar(255) DEFAULT NULL,
  `users_lname` varchar(255) DEFAULT NULL,
  `users_email` varchar(255) DEFAULT NULL,
  `users_phone` varchar(12) DEFAULT NULL,
  `users_city` varchar(255) DEFAULT NULL,
  `users_province` varchar(255) DEFAULT NULL,
  `users_country` varchar(255) DEFAULT NULL,
  `users_comment` text,
  `users_theme` varchar(255) DEFAULT NULL,
  `user_expires` varchar(30) NOT NULL DEFAULT 'X',
  `users_parent` int(11) DEFAULT NULL,
  `users_page_limit` int(11) DEFAULT '25',
  `user_receives_emails` tinyint(1) NOT NULL DEFAULT '1'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_user_groups`
--

CREATE TABLE `ogp_user_groups` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  `group_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_user_group_homes`
--

CREATE TABLE `ogp_user_group_homes` (
  `home_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_rights` varchar(63) DEFAULT NULL,
  `user_group_expiration_date` varchar(21) NOT NULL DEFAULT 'X'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_user_group_info`
--

CREATE TABLE `ogp_user_group_info` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(255) DEFAULT NULL,
  `main_user_id` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_user_group_remote_servers`
--

CREATE TABLE `ogp_user_group_remote_servers` (
  `remote_server_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `access_rights` varchar(63) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_user_homes`
--

CREATE TABLE `ogp_user_homes` (
  `home_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `access_rights` varchar(63) DEFAULT NULL,
  `user_expiration_date` varchar(21) NOT NULL DEFAULT 'X'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_user_role_info`
--

CREATE TABLE `ogp_user_role_info` (
  `role_id` int(11) NOT NULL,
  `role_name` varchar(100) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_widgets`
--

CREATE TABLE `ogp_widgets` (
  `id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `sort_no` int(11) NOT NULL,
  `collapsed` tinyint(4) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `ogp_widgets_users`
--

CREATE TABLE `ogp_widgets_users` (
  `user_id` int(11) NOT NULL,
  `widget_id` int(11) NOT NULL,
  `column_id` int(11) NOT NULL,
  `sort_no` int(11) NOT NULL,
  `collapsed` tinyint(4) NOT NULL,
  `title` varchar(100) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ogp_addons`
--
ALTER TABLE `ogp_addons`
  ADD PRIMARY KEY (`addon_id`);

--
-- Indexes for table `ogp_adminexternallinks`
--
ALTER TABLE `ogp_adminexternallinks`
  ADD PRIMARY KEY (`link_id`);

--
-- Indexes for table `ogp_adminlte_settings`
--
ALTER TABLE `ogp_adminlte_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UniqueSetting` (`user`,`name`);

--
-- Indexes for table `ogp_api_tokens`
--
ALTER TABLE `ogp_api_tokens`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `ogp_arrange_ports`
--
ALTER TABLE `ogp_arrange_ports`
  ADD PRIMARY KEY (`range_id`),
  ADD UNIQUE KEY `ip_id` (`ip_id`,`home_cfg_id`);

--
-- Indexes for table `ogp_backup_restore`
--
ALTER TABLE `ogp_backup_restore`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ogp_ban_list`
--
ALTER TABLE `ogp_ban_list`
  ADD PRIMARY KEY (`client_ip`);

--
-- Indexes for table `ogp_billing_carts`
--
ALTER TABLE `ogp_billing_carts`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `ogp_billing_coupons`
--
ALTER TABLE `ogp_billing_coupons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ogp_billing_orders`
--
ALTER TABLE `ogp_billing_orders`
  ADD PRIMARY KEY (`order_id`);

--
-- Indexes for table `ogp_billing_services`
--
ALTER TABLE `ogp_billing_services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `ogp_circular`
--
ALTER TABLE `ogp_circular`
  ADD PRIMARY KEY (`circular_id`);

--
-- Indexes for table `ogp_circular_recipients`
--
ALTER TABLE `ogp_circular_recipients`
  ADD PRIMARY KEY (`user_id`,`circular_id`);

--
-- Indexes for table `ogp_config_homes`
--
ALTER TABLE `ogp_config_homes`
  ADD PRIMARY KEY (`home_cfg_id`),
  ADD UNIQUE KEY `game_key` (`game_key`);

--
-- Indexes for table `ogp_config_mods`
--
ALTER TABLE `ogp_config_mods`
  ADD PRIMARY KEY (`mod_cfg_id`),
  ADD UNIQUE KEY `home_cfg_id` (`home_cfg_id`,`mod_key`);

--
-- Indexes for table `ogp_game_mods`
--
ALTER TABLE `ogp_game_mods`
  ADD PRIMARY KEY (`mod_id`),
  ADD UNIQUE KEY `home_id` (`home_id`,`mod_cfg_id`);

--
-- Indexes for table `ogp_home_ip_ports`
--
ALTER TABLE `ogp_home_ip_ports`
  ADD PRIMARY KEY (`ip_id`,`port`);

--
-- Indexes for table `ogp_lgsl`
--
ALTER TABLE `ogp_lgsl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ogp_logger`
--
ALTER TABLE `ogp_logger`
  ADD PRIMARY KEY (`log_id`);

--
-- Indexes for table `ogp_master_server_homes`
--
ALTER TABLE `ogp_master_server_homes`
  ADD PRIMARY KEY (`remote_server_id`,`home_cfg_id`);

--
-- Indexes for table `ogp_modules`
--
ALTER TABLE `ogp_modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `folder` (`folder`);

--
-- Indexes for table `ogp_module_access_rights`
--
ALTER TABLE `ogp_module_access_rights`
  ADD UNIQUE KEY `flag` (`flag`);

--
-- Indexes for table `ogp_module_menus`
--
ALTER TABLE `ogp_module_menus`
  ADD PRIMARY KEY (`module_id`,`subpage`,`group`);

--
-- Indexes for table `ogp_mysql_databases`
--
ALTER TABLE `ogp_mysql_databases`
  ADD PRIMARY KEY (`db_id`),
  ADD UNIQUE KEY `mysql_server_id` (`mysql_server_id`,`db_name`),
  ADD UNIQUE KEY `mysql_server_id_2` (`mysql_server_id`,`db_user`);

--
-- Indexes for table `ogp_mysql_servers`
--
ALTER TABLE `ogp_mysql_servers`
  ADD PRIMARY KEY (`mysql_server_id`);

--
-- Indexes for table `ogp_notification`
--
ALTER TABLE `ogp_notification`
  ADD PRIMARY KEY (`notification_id`);

--
-- Indexes for table `ogp_notification_recipients`
--
ALTER TABLE `ogp_notification_recipients`
  ADD PRIMARY KEY (`user_id`,`notification_id`);

--
-- Indexes for table `ogp_rcon_presets`
--
ALTER TABLE `ogp_rcon_presets`
  ADD PRIMARY KEY (`preset_id`);

--
-- Indexes for table `ogp_remote_servers`
--
ALTER TABLE `ogp_remote_servers`
  ADD PRIMARY KEY (`remote_server_id`),
  ADD UNIQUE KEY `agent_ip` (`agent_ip`,`agent_port`);

--
-- Indexes for table `ogp_remote_server_ips`
--
ALTER TABLE `ogp_remote_server_ips`
  ADD PRIMARY KEY (`ip_id`);

--
-- Indexes for table `ogp_reseller_accounts`
--
ALTER TABLE `ogp_reseller_accounts`
  ADD PRIMARY KEY (`account_id`);

--
-- Indexes for table `ogp_reseller_carts`
--
ALTER TABLE `ogp_reseller_carts`
  ADD PRIMARY KEY (`cart_id`);

--
-- Indexes for table `ogp_reseller_discount_codes`
--
ALTER TABLE `ogp_reseller_discount_codes`
  ADD PRIMARY KEY (`discount_id`);

--
-- Indexes for table `ogp_reseller_homes`
--
ALTER TABLE `ogp_reseller_homes`
  ADD PRIMARY KEY (`home_id`);

--
-- Indexes for table `ogp_reseller_services`
--
ALTER TABLE `ogp_reseller_services`
  ADD PRIMARY KEY (`service_id`);

--
-- Indexes for table `ogp_server_homes`
--
ALTER TABLE `ogp_server_homes`
  ADD PRIMARY KEY (`home_id`);

--
-- Indexes for table `ogp_settings`
--
ALTER TABLE `ogp_settings`
  ADD PRIMARY KEY (`setting`);

--
-- Indexes for table `ogp_tickets`
--
ALTER TABLE `ogp_tickets`
  ADD PRIMARY KEY (`tid`),
  ADD UNIQUE KEY `uid` (`uid`);

--
-- Indexes for table `ogp_ticket_attachments`
--
ALTER TABLE `ogp_ticket_attachments`
  ADD PRIMARY KEY (`attachment_id`),
  ADD UNIQUE KEY `unique_name` (`unique_name`);

--
-- Indexes for table `ogp_ticket_messages`
--
ALTER TABLE `ogp_ticket_messages`
  ADD PRIMARY KEY (`reply_id`),
  ADD KEY `ogp_ticket_messages_fk0` (`ticket_id`);

--
-- Indexes for table `ogp_ticket_settings`
--
ALTER TABLE `ogp_ticket_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_name` (`setting_name`);

--
-- Indexes for table `ogp_ts3_homes`
--
ALTER TABLE `ogp_ts3_homes`
  ADD PRIMARY KEY (`ts3_id`),
  ADD UNIQUE KEY `rserver_id` (`rserver_id`,`vserver_id`,`user_id`);

--
-- Indexes for table `ogp_tshock`
--
ALTER TABLE `ogp_tshock`
  ADD PRIMARY KEY (`token_id`);

--
-- Indexes for table `ogp_update_blacklist`
--
ALTER TABLE `ogp_update_blacklist`
  ADD UNIQUE KEY `file_path` (`file_path`),
  ADD UNIQUE KEY `file_path_2` (`file_path`);

--
-- Indexes for table `ogp_users`
--
ALTER TABLE `ogp_users`
  ADD PRIMARY KEY (`users_login`),
  ADD UNIQUE KEY `id` (`user_id`),
  ADD UNIQUE KEY `email` (`users_email`);

--
-- Indexes for table `ogp_user_groups`
--
ALTER TABLE `ogp_user_groups`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- Indexes for table `ogp_user_group_homes`
--
ALTER TABLE `ogp_user_group_homes`
  ADD PRIMARY KEY (`home_id`,`group_id`);

--
-- Indexes for table `ogp_user_group_info`
--
ALTER TABLE `ogp_user_group_info`
  ADD PRIMARY KEY (`group_id`),
  ADD UNIQUE KEY `group_name` (`group_name`);

--
-- Indexes for table `ogp_user_group_remote_servers`
--
ALTER TABLE `ogp_user_group_remote_servers`
  ADD PRIMARY KEY (`remote_server_id`,`group_id`);

--
-- Indexes for table `ogp_user_homes`
--
ALTER TABLE `ogp_user_homes`
  ADD PRIMARY KEY (`user_id`,`home_id`);

--
-- Indexes for table `ogp_user_role_info`
--
ALTER TABLE `ogp_user_role_info`
  ADD PRIMARY KEY (`role_id`),
  ADD UNIQUE KEY `role_name` (`role_name`);

--
-- Indexes for table `ogp_widgets`
--
ALTER TABLE `ogp_widgets`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ogp_addons`
--
ALTER TABLE `ogp_addons`
  MODIFY `addon_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=134;

--
-- AUTO_INCREMENT for table `ogp_adminexternallinks`
--
ALTER TABLE `ogp_adminexternallinks`
  MODIFY `link_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ogp_adminlte_settings`
--
ALTER TABLE `ogp_adminlte_settings`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `ogp_arrange_ports`
--
ALTER TABLE `ogp_arrange_ports`
  MODIFY `range_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=259;

--
-- AUTO_INCREMENT for table `ogp_backup_restore`
--
ALTER TABLE `ogp_backup_restore`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_billing_carts`
--
ALTER TABLE `ogp_billing_carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=556;

--
-- AUTO_INCREMENT for table `ogp_billing_coupons`
--
ALTER TABLE `ogp_billing_coupons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ogp_billing_orders`
--
ALTER TABLE `ogp_billing_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=540;

--
-- AUTO_INCREMENT for table `ogp_billing_services`
--
ALTER TABLE `ogp_billing_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=124;

--
-- AUTO_INCREMENT for table `ogp_circular`
--
ALTER TABLE `ogp_circular`
  MODIFY `circular_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `ogp_config_homes`
--
ALTER TABLE `ogp_config_homes`
  MODIFY `home_cfg_id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=280;

--
-- AUTO_INCREMENT for table `ogp_config_mods`
--
ALTER TABLE `ogp_config_mods`
  MODIFY `mod_cfg_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=874;

--
-- AUTO_INCREMENT for table `ogp_game_mods`
--
ALTER TABLE `ogp_game_mods`
  MODIFY `mod_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1666;

--
-- AUTO_INCREMENT for table `ogp_lgsl`
--
ALTER TABLE `ogp_lgsl`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_logger`
--
ALTER TABLE `ogp_logger`
  MODIFY `log_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63213;

--
-- AUTO_INCREMENT for table `ogp_modules`
--
ALTER TABLE `ogp_modules`
  MODIFY `id` smallint(5) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `ogp_mysql_databases`
--
ALTER TABLE `ogp_mysql_databases`
  MODIFY `db_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=348;

--
-- AUTO_INCREMENT for table `ogp_mysql_servers`
--
ALTER TABLE `ogp_mysql_servers`
  MODIFY `mysql_server_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ogp_notification`
--
ALTER TABLE `ogp_notification`
  MODIFY `notification_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_rcon_presets`
--
ALTER TABLE `ogp_rcon_presets`
  MODIFY `preset_id` int(50) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_remote_servers`
--
ALTER TABLE `ogp_remote_servers`
  MODIFY `remote_server_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `ogp_remote_server_ips`
--
ALTER TABLE `ogp_remote_server_ips`
  MODIFY `ip_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `ogp_reseller_accounts`
--
ALTER TABLE `ogp_reseller_accounts`
  MODIFY `account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ogp_reseller_carts`
--
ALTER TABLE `ogp_reseller_carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ogp_reseller_discount_codes`
--
ALTER TABLE `ogp_reseller_discount_codes`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_reseller_services`
--
ALTER TABLE `ogp_reseller_services`
  MODIFY `service_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ogp_server_homes`
--
ALTER TABLE `ogp_server_homes`
  MODIFY `home_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1516;

--
-- AUTO_INCREMENT for table `ogp_tickets`
--
ALTER TABLE `ogp_tickets`
  MODIFY `tid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `ogp_ticket_attachments`
--
ALTER TABLE `ogp_ticket_attachments`
  MODIFY `attachment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `ogp_ticket_messages`
--
ALTER TABLE `ogp_ticket_messages`
  MODIFY `reply_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `ogp_ticket_settings`
--
ALTER TABLE `ogp_ticket_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ogp_ts3_homes`
--
ALTER TABLE `ogp_ts3_homes`
  MODIFY `ts3_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ogp_tshock`
--
ALTER TABLE `ogp_tshock`
  MODIFY `token_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_users`
--
ALTER TABLE `ogp_users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=636;

--
-- AUTO_INCREMENT for table `ogp_user_group_info`
--
ALTER TABLE `ogp_user_group_info`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `ogp_user_role_info`
--
ALTER TABLE `ogp_user_role_info`
  MODIFY `role_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ogp_widgets`
--
ALTER TABLE `ogp_widgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `ogp_ticket_messages`
--
ALTER TABLE `ogp_ticket_messages`
  ADD CONSTRAINT `ogp_ticket_messages_fk0` FOREIGN KEY (`ticket_id`) REFERENCES `ogp_tickets` (`tid`);

--
-- Dumping data for consolidated modules
--

-- Dashboard widgets
INSERT INTO `ogp_widgets` (`id`, `column_id`, `sort_no`, `collapsed`, `title`) VALUES 
(1, 1, 1, 0, 'Game Monitor'),  
(2, 2, 0, 0, 'Online Server'),  
(3, 2, 1, 0, 'Currently Online'),  
(4, 3, 0, 0, 'FTP'),
(5, 3, 1, 0, 'Support');

-- Ticket system default settings
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('ratings_enabled', 'true') ON DUPLICATE KEY UPDATE `setting_name` = 'ratings_enabled', `setting_value` = 'true';
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('attachments_enabled', 'true') ON DUPLICATE KEY UPDATE `setting_name` = 'attachments_enabled', `setting_value` = 'true';
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('attachment_max_size', '52428800') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_max_size', `setting_value` = '52428800';
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('attachment_limit', '5') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_limit', `setting_value` = '5';
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('attachment_save_dir', 'modules/tickets/uploads') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_save_dir', `setting_value` = 'modules/tickets/uploads';
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('attachment_extensions', 'jpg, gif, jpeg, jpg, png, pdf, txt, sql, zip') ON DUPLICATE KEY UPDATE `setting_name` = 'attachment_extensions', `setting_value` = 'jpg, gif, jpeg, jpg, png, pdf, txt, sql, zip';
INSERT INTO `ogp_ticket_settings` (setting_name, setting_value) VALUES ('notifications_enabled', 'true') ON DUPLICATE KEY UPDATE `setting_name` = 'notifications_enabled', `setting_value` = 'true';

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
