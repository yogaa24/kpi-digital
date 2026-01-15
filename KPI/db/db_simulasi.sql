-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 15, 2026 at 07:36 AM
-- Server version: 8.0.30
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_simulasi`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_bobotkpi`
--

CREATE TABLE `tb_bobotkpi` (
  `idbobotkpi` int NOT NULL,
  `id_user` int NOT NULL,
  `bobotwhat` int NOT NULL,
  `bobothow` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_bobotkpi`
--

INSERT INTO `tb_bobotkpi` (`idbobotkpi`, `id_user`, `bobotwhat`, `bobothow`) VALUES
(2, 1, 60, 40),
(3, 18, 60, 40),
(4, 16, 60, 40),
(5, 17, 60, 40),
(6, 19, 60, 40),
(7, 20, 60, 40),
(8, 21, 60, 40),
(11, 24, 0, 0),
(12, 25, 0, 0),
(13, 26, 0, 0),
(14, 27, 0, 0),
(15, 28, 60, 40),
(16, 37, 70, 40),
(17, 39, 0, 0),
(18, 40, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_hows`
--

CREATE TABLE `tb_hows` (
  `id_how` int NOT NULL,
  `id_user` int NOT NULL,
  `id_kpi` int NOT NULL,
  `tipe_how` enum('A','B') DEFAULT 'A',
  `p_how` text NOT NULL,
  `bobot` double NOT NULL,
  `target_omset` decimal(15,2) DEFAULT '0.00',
  `hasil` text NOT NULL,
  `nilai` double NOT NULL,
  `total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_hows`
--

INSERT INTO `tb_hows` (`id_how`, `id_user`, `id_kpi`, `tipe_how`, `p_how`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`) VALUES
(1, 28, 1, 'A', 'membuat aplikasi kpi selesai tepat waktu', 10, '0.00', '', 0, 0),
(2, 28, 1, 'A', 'aplikasi pas', 10, '0.00', '', 0, 0),
(3, 37, 2, 'A', 'membuat kpi2', 50, '0.00', 'okee', 100, 50);

-- --------------------------------------------------------

--
-- Table structure for table `tb_indikator_hows`
--

CREATE TABLE `tb_indikator_hows` (
  `id_indikator` int NOT NULL,
  `id_how` int NOT NULL,
  `keterangan` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `urutan` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_indikator_hows`
--

INSERT INTO `tb_indikator_hows` (`id_indikator`, `id_how`, `keterangan`, `nilai`, `urutan`, `created_at`) VALUES
(1, 3, 'okee', '100.00', 1, '2026-01-02 07:37:22');

-- --------------------------------------------------------

--
-- Table structure for table `tb_indikator_whats`
--

CREATE TABLE `tb_indikator_whats` (
  `id_indikator` int NOT NULL,
  `id_what` int NOT NULL,
  `keterangan` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `urutan` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_indikator_whats`
--

INSERT INTO `tb_indikator_whats` (`id_indikator`, `id_what`, `keterangan`, `nilai`, `urutan`, `created_at`) VALUES
(1, 1, '2 aplikasi', '100.00', 1, '2025-12-22 06:40:44'),
(2, 1, '1 aplikasi', '90.00', 2, '2025-12-22 06:40:44'),
(3, 1, '0 aplikasi ', '0.00', 3, '2025-12-22 06:40:44'),
(5, 3, 'okeee', '100.00', 1, '2026-01-02 07:34:08');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kpi`
--

CREATE TABLE `tb_kpi` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `poin` text NOT NULL,
  `bobot` double NOT NULL,
  `poin2` text NOT NULL,
  `bobot2` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kpi`
--

INSERT INTO `tb_kpi` (`id`, `id_user`, `poin`, `bobot`, `poin2`, `bobot2`) VALUES
(1, 28, 'pembuatan produk digital', 40, 'pembuatan produk digital', 20),
(2, 37, 'membuat kpi', 30, 'membuat kpi2', 30);

-- --------------------------------------------------------

--
-- Table structure for table `tb_whats`
--

CREATE TABLE `tb_whats` (
  `id_what` int NOT NULL,
  `id_user` int NOT NULL,
  `id_kpi` int NOT NULL,
  `tipe_what` enum('A','B') DEFAULT 'A',
  `p_what` text NOT NULL,
  `bobot` double NOT NULL,
  `target_omset` decimal(15,2) DEFAULT '0.00',
  `hasil` text NOT NULL,
  `nilai` double NOT NULL,
  `total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_whats`
--

INSERT INTO `tb_whats` (`id_what`, `id_user`, `id_kpi`, `tipe_what`, `p_what`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`) VALUES
(1, 28, 1, 'A', 'aplikasi plafon', 40, '0.00', '2 aplikasi', 100, 40),
(3, 37, 2, 'A', 'membuat kpi', 50, '0.00', 'okeee', 100, 50);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tb_bobotkpi`
--
ALTER TABLE `tb_bobotkpi`
  ADD PRIMARY KEY (`idbobotkpi`);

--
-- Indexes for table `tb_hows`
--
ALTER TABLE `tb_hows`
  ADD PRIMARY KEY (`id_how`);

--
-- Indexes for table `tb_indikator_hows`
--
ALTER TABLE `tb_indikator_hows`
  ADD PRIMARY KEY (`id_indikator`),
  ADD KEY `idx_id_how` (`id_how`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indexes for table `tb_indikator_whats`
--
ALTER TABLE `tb_indikator_whats`
  ADD PRIMARY KEY (`id_indikator`),
  ADD KEY `fk_indikator_whats` (`id_what`);

--
-- Indexes for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_whats`
--
ALTER TABLE `tb_whats`
  ADD PRIMARY KEY (`id_what`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tb_bobotkpi`
--
ALTER TABLE `tb_bobotkpi`
  MODIFY `idbobotkpi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tb_hows`
--
ALTER TABLE `tb_hows`
  MODIFY `id_how` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_indikator_hows`
--
ALTER TABLE `tb_indikator_hows`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tb_indikator_whats`
--
ALTER TABLE `tb_indikator_whats`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tb_whats`
--
ALTER TABLE `tb_whats`
  MODIFY `id_what` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tb_indikator_hows`
--
ALTER TABLE `tb_indikator_hows`
  ADD CONSTRAINT `tb_indikator_hows_ibfk_1` FOREIGN KEY (`id_how`) REFERENCES `tb_hows` (`id_how`) ON DELETE CASCADE;

--
-- Constraints for table `tb_indikator_whats`
--
ALTER TABLE `tb_indikator_whats`
  ADD CONSTRAINT `fk_indikator_whats` FOREIGN KEY (`id_what`) REFERENCES `tb_whats` (`id_what`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
