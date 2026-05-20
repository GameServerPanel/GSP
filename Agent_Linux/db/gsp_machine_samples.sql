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
-- Table structure for table `gsp_machine_samples`
--

CREATE TABLE `gsp_machine_samples` (
  `id` bigint(20) NOT NULL,
  `machine_id` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ts` datetime NOT NULL,
  `load1` decimal(6,2) DEFAULT NULL,
  `load5` decimal(6,2) DEFAULT NULL,
  `load15` decimal(6,2) DEFAULT NULL,
  `cpu_pct` decimal(6,2) DEFAULT NULL,
  `mem_used_bytes` bigint(20) DEFAULT NULL,
  `mem_total_bytes` bigint(20) DEFAULT NULL,
  `mem_used_pct` decimal(6,2) DEFAULT NULL,
  `swap_used_bytes` bigint(20) DEFAULT NULL,
  `swap_total_bytes` bigint(20) DEFAULT NULL,
  `disk_path` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `disk_total_bytes` bigint(20) DEFAULT NULL,
  `disk_used_bytes` bigint(20) DEFAULT NULL,
  `disk_used_pct` decimal(6,2) DEFAULT NULL,
  `net_iface` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rx_bytes` bigint(20) DEFAULT NULL,
  `tx_bytes` bigint(20) DEFAULT NULL,
  `iface_speed_mbps` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `gsp_machine_samples`
--
ALTER TABLE `gsp_machine_samples`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_machine_ts` (`machine_id`,`ts`),
  ADD KEY `idx_ts` (`ts`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `gsp_machine_samples`
--
ALTER TABLE `gsp_machine_samples`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
