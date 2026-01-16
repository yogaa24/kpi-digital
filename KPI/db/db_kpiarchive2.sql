-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 16, 2026 at 03:38 PM
-- Server version: 8.0.30
-- PHP Version: 7.4.20

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_kpiarchive`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbar_archive`
--

CREATE TABLE `tbar_archive` (
  `id_archive` int NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `id_user` int NOT NULL,
  `status` tinyint(1) DEFAULT '1',
  `reviewed_by` int DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_archive`
--

INSERT INTO `tbar_archive` (`id_archive`, `bulan`, `id_user`, `status`, `reviewed_by`, `reviewed_at`, `approved_by`, `approved_at`) VALUES
(1, '10/2025', 4, 1, NULL, NULL, NULL, NULL),
(2, '10/2025', 1, 3, 4, '2026-01-16 08:32:06', 4, '2026-01-16 08:32:47'),
(3, '11/2025', 4, 1, NULL, NULL, NULL, NULL),
(11, '12/2025', 28, 3, 4, '2026-01-16 08:09:14', 4, '2026-01-16 08:18:21'),
(12, '12/2025', 1, 1, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_bobotkpi`
--

CREATE TABLE `tbar_bobotkpi` (
  `idbobotkpi` int NOT NULL,
  `id_user` int NOT NULL,
  `id_arcv` int NOT NULL,
  `bobotwhat` int NOT NULL,
  `bobothow` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_bobotkpi`
--

INSERT INTO `tbar_bobotkpi` (`idbobotkpi`, `id_user`, `id_arcv`, `bobotwhat`, `bobothow`) VALUES
(1, 4, 1, 60, 40),
(2, 1, 2, 60, 40),
(3, 4, 3, 60, 40),
(11, 28, 11, 60, 40),
(12, 1, 12, 60, 40);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_hows`
--

CREATE TABLE `tbar_hows` (
  `id_how` int NOT NULL,
  `id_user` int NOT NULL,
  `id_kpi` int NOT NULL,
  `tipe_how` enum('A','B') DEFAULT 'A',
  `p_how` text NOT NULL,
  `bobot` double NOT NULL,
  `target_omset` decimal(15,2) DEFAULT '0.00',
  `hasil` text NOT NULL,
  `nilai` double NOT NULL,
  `total` double NOT NULL,
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `is_edited` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_hows`
--

INSERT INTO `tbar_hows` (`id_how`, `id_user`, `id_kpi`, `tipe_how`, `p_how`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`, `edited_by`, `edited_at`, `is_edited`) VALUES
(1, 4, 1, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40, NULL, NULL, 0),
(2, 4, 1, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsbrewrytuyiuioprewrytuyiuioprewrytuyiuioprewrytuyiuiop', 55, '0.00', 'sesuai target', 115, 63, NULL, NULL, 0),
(3, 4, 1, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5, NULL, NULL, 0),
(4, 4, 2, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5, NULL, NULL, 0),
(5, 4, 2, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5, NULL, NULL, 0),
(6, 4, 3, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', '4', 115, 28.75, NULL, NULL, 0),
(7, 4, 3, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', '3.9', 110, 28, NULL, NULL, 0),
(8, 4, 3, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28, NULL, NULL, 0),
(9, 4, 3, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25, NULL, NULL, 0),
(10, 4, 4, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75, NULL, NULL, 0),
(11, 4, 4, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5, NULL, NULL, 0),
(12, 4, 4, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75, NULL, NULL, 0),
(13, 1, 5, 'A', 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '0.00', '100% Terjadwal & di laporkan', 110, 110, NULL, NULL, 0),
(14, 1, 6, 'A', 'Mendata inventaris karisma per departemen', 50, '0.00', '4 Dept', 100, 50, NULL, NULL, 0),
(15, 1, 6, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '0.00', '100% Terjadwal & di laporkan', 110, 55, NULL, NULL, 0),
(16, 1, 7, 'A', 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)rewrytuyiuioprewrytuyiuioprewrytuyiuiops', 100, '0.00', 'H+5 dari kerusakan', 90, 90, NULL, NULL, 0),
(17, 1, 8, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '0.00', '100% Terjadwal & di laporkan & TTD', 115, 115, NULL, NULL, 0),
(18, 1, 9, 'A', 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '0.00', '4', 115, 57.5, NULL, NULL, 0),
(19, 1, 9, 'A', 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25, NULL, NULL, 0),
(20, 1, 9, 'A', 'Menguasai Support Software penilaian oleh P.Bram', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25, NULL, NULL, 0),
(21, 1, 10, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% Tepat Waktu', 115, 28.75, NULL, NULL, 0),
(22, 1, 10, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '100% taat', 115, 28.75, NULL, NULL, 0),
(23, 1, 10, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '100% hadir', 115, 28.75, NULL, NULL, 0),
(24, 1, 10, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '100% hadir', 115, 28.75, NULL, NULL, 0),
(25, 4, 11, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40, NULL, NULL, 0),
(26, 4, 11, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, '0.00', 'sesuai target', 115, 63, NULL, NULL, 0),
(27, 4, 11, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5, NULL, NULL, 0),
(28, 4, 12, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5, NULL, NULL, 0),
(29, 4, 12, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5, NULL, NULL, 0),
(30, 4, 13, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', 'NILAI > 3.8', 110, 27.5, NULL, NULL, 0),
(31, 4, 13, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', 'NILAI 4', 115, 28.75, NULL, NULL, 0),
(32, 4, 13, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28, NULL, NULL, 0),
(33, 4, 13, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25, NULL, NULL, 0),
(34, 4, 14, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75, NULL, NULL, 0),
(35, 4, 14, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5, NULL, NULL, 0),
(36, 4, 14, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75, NULL, NULL, 0),
(99, 28, 50, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan target', 20, '0.00', 'sesuai', 100, 20, NULL, NULL, 0),
(100, 28, 50, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'sesuai', 100, 20, NULL, NULL, 0),
(101, 28, 50, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', '-', 0, 0, NULL, NULL, 0),
(102, 28, 50, 'A', 'Skill Standart IT (code programming)', 25, '0.00', '-', 0, 0, NULL, NULL, 0),
(103, 28, 50, 'A', 'abcde', 20, '0.00', 'agak lumayan', 90, 18, NULL, NULL, 0),
(104, 28, 50, 'B', 'target omset november', 10, '100000.00', ' Hasil Tercapai: 63,000.00', 63, 6.3, NULL, NULL, 0),
(105, 28, 50, 'A', 'ok', 10, '0.00', '', 0, 0, NULL, NULL, 0),
(106, 28, 51, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'sesuai', 100, 60, NULL, NULL, 0),
(107, 28, 51, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', '-', 0, 0, NULL, NULL, 0),
(108, 28, 52, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', '-', 0, 0, NULL, NULL, 0),
(109, 28, 52, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', '-', 0, 0, NULL, NULL, 0),
(110, 28, 53, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, NULL, NULL, 0),
(111, 28, 53, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, NULL, NULL, 0),
(112, 28, 53, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, NULL, NULL, 0),
(113, 28, 53, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, NULL, NULL, 0),
(114, 28, 54, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0, NULL, NULL, 0),
(115, 28, 54, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0, NULL, NULL, 0),
(116, 1, 55, 'A', 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '0.00', '100% Terjadwal & di laporkan', 110, 110, NULL, NULL, 0),
(117, 1, 56, 'A', 'Mendata inventaris karisma per departemen', 50, '0.00', '4 Dept', 100, 50, NULL, NULL, 0),
(118, 1, 56, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '0.00', '100% Terjadwal & di laporkan', 110, 55, NULL, NULL, 0),
(119, 1, 57, 'A', 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)', 100, '0.00', 'H+2 dari kerusakan', 115, 115, NULL, NULL, 0),
(120, 1, 58, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '0.00', '100% Terjadwal & di laporkan & TTD', 115, 115, NULL, NULL, 0),
(121, 1, 59, 'A', 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '0.00', '4', 115, 57.5, NULL, NULL, 0),
(122, 1, 59, 'A', 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25, NULL, NULL, 0),
(123, 1, 59, 'A', 'Menguasai Support Software penilaian oleh P.Bram', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25, NULL, NULL, 0),
(124, 1, 60, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% Tepat Waktu', 115, 28.75, NULL, NULL, 0),
(125, 1, 60, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '100% taat', 115, 28.75, NULL, NULL, 0),
(126, 1, 60, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '100% hadir', 115, 28.75, NULL, NULL, 0),
(127, 1, 60, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '100% hadir', 115, 28.75, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_indikator_hows`
--

CREATE TABLE `tbar_indikator_hows` (
  `id_indikator` int NOT NULL,
  `id_how` int NOT NULL,
  `keterangan` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `urutan` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `is_edited` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_indikator_hows`
--

INSERT INTO `tbar_indikator_hows` (`id_indikator`, `id_how`, `keterangan`, `nilai`, `urutan`, `created_at`, `edited_by`, `edited_at`, `is_edited`) VALUES
(8, 99, 'sesuai', '100.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0),
(9, 100, 'sesuai', '100.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0),
(10, 103, 'bisa', '115.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0),
(11, 103, 'lumayan', '100.00', 2, '2026-01-15 16:07:02', NULL, NULL, 0),
(12, 103, 'agak lumayan', '90.00', 3, '2026-01-15 16:07:02', NULL, NULL, 0),
(13, 105, 'oke', '100.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0),
(14, 106, 'sesuai', '100.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_indikator_whats`
--

CREATE TABLE `tbar_indikator_whats` (
  `id_indikator` int NOT NULL,
  `id_what` int NOT NULL,
  `keterangan` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `urutan` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `is_edited` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_indikator_whats`
--

INSERT INTO `tbar_indikator_whats` (`id_indikator`, `id_what`, `keterangan`, `nilai`, `urutan`, `created_at`, `edited_by`, `edited_at`, `is_edited`) VALUES
(11, 76, '4 aplikasi', '115.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0),
(12, 76, '3 aplikasi', '100.00', 2, '2026-01-15 16:07:02', NULL, NULL, 0),
(13, 76, '2 aplikasi', '90.00', 3, '2026-01-15 16:07:02', NULL, NULL, 0),
(14, 76, '1 aplikasi', '80.00', 4, '2026-01-15 16:07:02', NULL, NULL, 0),
(15, 77, 'bersih dan rapi', '100.00', 1, '2026-01-15 16:07:02', NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_kpi`
--

CREATE TABLE `tbar_kpi` (
  `id` int NOT NULL,
  `id_arcv` int NOT NULL,
  `id_user` int NOT NULL,
  `poin` text NOT NULL,
  `bobot` double NOT NULL,
  `poin2` text NOT NULL,
  `bobot2` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_kpi`
--

INSERT INTO `tbar_kpi` (`id`, `id_arcv`, `id_user`, `poin`, `bobot`, `poin2`, `bobot2`) VALUES
(1, 1, 4, 'Meningkatkan Performa Team IT', 30, 'Meningkatkan Performa IT', 30),
(2, 1, 4, 'Penyelesaian Pembuatan/Pengembangan Program', 30, 'Penyelesaian pembuatan/pengembangan program', 30),
(3, 1, 4, 'People Development', 30, 'People Development', 30),
(4, 1, 4, 'Absensi', 10, 'Absensi', 10),
(5, 2, 1, 'Support Konten Kreatorewrytuyiuiop', 15, 'Preparing hardware konten & livestream', 15),
(6, 2, 1, 'Maintenance Hardware rewrytuyiuiop', 25, 'Update Hardware', 25),
(7, 2, 1, 'CCTV Karisma 100%', 30, 'Update CCTV Karisma', 30),
(8, 2, 1, 'Support Software Digital', 10, 'Mampu Membuat Aplikasi', 10),
(9, 2, 1, 'Menguasai 5 Skill Standart rewrytuyiuiop', 10, 'Menguasai 5 Skill Standartrewrytuyiuiop', 10),
(10, 2, 1, 'Absensi', 10, 'Absensi', 10),
(11, 3, 4, 'Meningkatkan Performa Team IT', 30, 'Meningkatkan Performa IT', 30),
(12, 3, 4, 'Penyelesaian Pembuatan/Pengembangan Program', 30, 'Penyelesaian pembuatan/pengembangan program', 30),
(13, 3, 4, 'People Development', 30, 'People Development', 30),
(14, 3, 4, 'Absensi', 10, 'Absensi', 10),
(50, 11, 28, 'Pembuatan Produk digital (4)', 40, 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', 40),
(51, 11, 28, 'Pemeliharaan sistem', 25, 'Produk digital dapat digunakan tanpa hambatan', 25),
(52, 11, 28, '0% Troubleshooting sistem', 15, 'Melakukan pengecekan berkala ', 15),
(53, 11, 28, 'Absensi', 10, 'Penilaian absensi oleh HRD', 10),
(54, 11, 28, 'Supporting maintenance hardware', 10, 'Membantu maintenance hardware', 10),
(55, 12, 1, 'Support Konten Kreator', 15, 'Preparing hardware konten & livestream', 15),
(56, 12, 1, 'Maintenance Hardware ', 25, 'Update Hardware', 25),
(57, 12, 1, 'CCTV Karisma 100%', 30, 'Update CCTV Karisma', 30),
(58, 12, 1, 'Support Software Digital', 10, 'Mampu Membuat Aplikasi', 10),
(59, 12, 1, 'Menguasai 5 Skill Standart', 10, 'Menguasai 5 Skill Standart', 10),
(60, 12, 1, 'Absensi', 10, 'Absensi', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_whats`
--

CREATE TABLE `tbar_whats` (
  `id_what` int NOT NULL,
  `id_user` int NOT NULL,
  `id_kpi` int NOT NULL,
  `tipe_what` enum('A','B') DEFAULT 'A',
  `p_what` text NOT NULL,
  `bobot` double NOT NULL,
  `target_omset` decimal(15,2) DEFAULT '0.00',
  `hasil` text NOT NULL,
  `nilai` double NOT NULL,
  `total` double NOT NULL,
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `is_edited` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_whats`
--

INSERT INTO `tbar_whats` (`id_what`, `id_user`, `id_kpi`, `tipe_what`, `p_what`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`, `edited_by`, `edited_at`, `is_edited`) VALUES
(1, 4, 1, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)rewrytuyiuioprewrytuyiuiop', 100, '0.00', '5 applikasi', 115, 115, NULL, NULL, 0),
(2, 4, 2, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 90, 54, NULL, NULL, 0),
(3, 4, 2, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46, NULL, NULL, 0),
(4, 4, 3, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75, NULL, NULL, 0),
(5, 4, 3, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'VERY GOOD', 100, 25, NULL, NULL, 0),
(6, 4, 3, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75, NULL, NULL, 0),
(7, 4, 3, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5, NULL, NULL, 0),
(8, 4, 4, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120, NULL, NULL, 0),
(9, 1, 5, 'A', 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '0.00', '135 Konten', 115, 115, NULL, NULL, 0),
(10, 1, 6, 'A', 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '0.00', '100%', 115, 57.5, NULL, NULL, 0),
(11, 1, 6, 'A', 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '0.00', '100%', 115, 57.5, NULL, NULL, 0),
(12, 1, 7, 'A', 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV Hidup Semua', 100, '0.00', '60%', 90, 90, NULL, NULL, 0),
(13, 1, 8, 'A', 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '0.00', '100% bisa', 115, 57.5, NULL, NULL, 0),
(14, 1, 8, 'A', 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '0.00', '5 modul', 100, 50, NULL, NULL, 0),
(15, 1, 9, 'A', '3 Skill standart IT ', 100, '0.00', '3,9', 100, 100, NULL, NULL, 0),
(16, 1, 10, 'A', 'Absensi', 100, '0.00', '130 dari HRD', 130, 130, NULL, NULL, 0),
(17, 4, 11, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '0.00', '5 applikasi', 115, 115, NULL, NULL, 0),
(18, 4, 12, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 100, 60, NULL, NULL, 0),
(19, 4, 12, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46, NULL, NULL, 0),
(20, 4, 13, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75, NULL, NULL, 0),
(21, 4, 13, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'EXCELLENT	', 115, 28.75, NULL, NULL, 0),
(22, 4, 13, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75, NULL, NULL, 0),
(23, 4, 13, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5, NULL, NULL, 0),
(24, 4, 14, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120, NULL, NULL, 0),
(72, 28, 50, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 20, '0.00', '-\r\n', 100, 20, NULL, NULL, 0),
(73, 28, 50, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 30, '0.00', 'ok', 1, 0.3, NULL, NULL, 0),
(74, 28, 50, 'A', 'membuat aplikasi zahir', 50, '0.00', 'aplikasi masih 90%', 100, 50, NULL, NULL, 0),
(75, 28, 50, 'B', 'Target All Omset', 10, '103000.00', ' Hasil Tercapai: 83,000.00', 80.58, 8.06, 4, '2026-01-16 03:34:10', 1),
(76, 28, 50, 'A', 'mambuat aplikasi kpi', 10, '0.00', '3 aplikasi', 100, 10, 4, '2026-01-16 03:31:17', 1),
(77, 28, 50, 'A', 'PERAWATAN KENDARAAN', 100, '0.00', '', 0, 0, NULL, NULL, 0),
(78, 28, 51, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'sesuai ', 115, 57.5, NULL, NULL, 0),
(79, 28, 51, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'sesuai ', 100, 50, NULL, NULL, 0),
(80, 28, 52, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'sesuai', 100, 70, NULL, NULL, 0),
(81, 28, 52, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'sesuai', 100, 30, NULL, NULL, 0),
(82, 28, 53, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122, NULL, NULL, 0),
(83, 28, 54, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100, NULL, NULL, 0),
(84, 1, 55, 'A', 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '0.00', '135 Konten', 115, 115, NULL, NULL, 0),
(85, 1, 56, 'A', 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '0.00', '100%', 115, 57.5, NULL, NULL, 0),
(86, 1, 56, 'A', 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '0.00', '100%', 115, 57.5, NULL, NULL, 0),
(87, 1, 57, 'A', 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV bisa merekam', 100, '0.00', '100%', 100, 100, NULL, NULL, 0),
(88, 1, 58, 'A', 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '0.00', '100% bisa', 115, 57.5, NULL, NULL, 0),
(89, 1, 58, 'A', 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '0.00', '6 modul', 115, 57.5, NULL, NULL, 0),
(90, 1, 59, 'A', '3 Skill standart IT ', 100, '0.00', '3,9', 100, 100, NULL, NULL, 0),
(91, 1, 60, 'A', 'Absensi', 100, '0.00', '130 dari HRD', 130, 130, NULL, NULL, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbar_archive`
--
ALTER TABLE `tbar_archive`
  ADD PRIMARY KEY (`id_archive`),
  ADD UNIQUE KEY `unique_archive` (`bulan`,`id_user`),
  ADD KEY `idx_user_status` (`id_user`);

--
-- Indexes for table `tbar_bobotkpi`
--
ALTER TABLE `tbar_bobotkpi`
  ADD PRIMARY KEY (`idbobotkpi`);

--
-- Indexes for table `tbar_hows`
--
ALTER TABLE `tbar_hows`
  ADD PRIMARY KEY (`id_how`);

--
-- Indexes for table `tbar_indikator_hows`
--
ALTER TABLE `tbar_indikator_hows`
  ADD PRIMARY KEY (`id_indikator`),
  ADD KEY `idx_id_how` (`id_how`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indexes for table `tbar_indikator_whats`
--
ALTER TABLE `tbar_indikator_whats`
  ADD PRIMARY KEY (`id_indikator`),
  ADD KEY `id_what` (`id_what`);

--
-- Indexes for table `tbar_kpi`
--
ALTER TABLE `tbar_kpi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbar_whats`
--
ALTER TABLE `tbar_whats`
  ADD PRIMARY KEY (`id_what`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbar_archive`
--
ALTER TABLE `tbar_archive`
  MODIFY `id_archive` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbar_bobotkpi`
--
ALTER TABLE `tbar_bobotkpi`
  MODIFY `idbobotkpi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `tbar_hows`
--
ALTER TABLE `tbar_hows`
  MODIFY `id_how` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT for table `tbar_indikator_hows`
--
ALTER TABLE `tbar_indikator_hows`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbar_indikator_whats`
--
ALTER TABLE `tbar_indikator_whats`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbar_kpi`
--
ALTER TABLE `tbar_kpi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `tbar_whats`
--
ALTER TABLE `tbar_whats`
  MODIFY `id_what` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
