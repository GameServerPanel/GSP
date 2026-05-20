-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2025 at 05:31 PM
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
-- Database: `panel`
--

-- --------------------------------------------------------

--
-- Table structure for table `gsp_process_samples`
--

CREATE TABLE `gsp_process_samples` (
  `id` bigint(20) NOT NULL,
  `machine_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ts` datetime NOT NULL,
  `server_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `server_path` varchar(512) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pid` int(11) NOT NULL,
  `proc_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cmd` text COLLATE utf8mb4_unicode_ci,
  `cpu_pct` decimal(7,2) DEFAULT NULL,
  `rss_bytes` bigint(20) DEFAULT NULL,
  `vms_bytes` bigint(20) DEFAULT NULL,
  `mem_pct` decimal(6,2) DEFAULT NULL,
  `io_read_bytes` bigint(20) DEFAULT NULL,
  `io_write_bytes` bigint(20) DEFAULT NULL,
  `open_fds` int(11) DEFAULT NULL,
  `listening_ports` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `folder_size_bytes` bigint(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gsp_process_samples`
--
ALTER TABLE `gsp_process_samples`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_proc_server` (`machine_id`,`server_name`,`ts`),
  ADD KEY `idx_proc_pid` (`machine_id`,`pid`,`ts`),
  ADD KEY `idx_ts` (`ts`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gsp_process_samples`
--
ALTER TABLE `gsp_process_samples`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
