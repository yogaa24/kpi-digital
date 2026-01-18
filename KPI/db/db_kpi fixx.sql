-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 18, 2026 at 03:20 PM
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
-- Database: `db_kpi`
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
  `approved_at` timestamp NULL DEFAULT NULL,
  `nilai_asli` decimal(10,2) DEFAULT '0.00',
  `nilai_akhir` decimal(10,2) DEFAULT '0.00',
  `sp_id` int DEFAULT NULL,
  `sp_jenis` varchar(10) DEFAULT NULL,
  `sp_pengurangan` decimal(10,2) DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_archive`
--

INSERT INTO `tbar_archive` (`id_archive`, `bulan`, `id_user`, `status`, `reviewed_by`, `reviewed_at`, `approved_by`, `approved_at`, `nilai_asli`, `nilai_akhir`, `sp_id`, `sp_jenis`, `sp_pengurangan`) VALUES
(1, '10/2025', 4, 1, NULL, NULL, NULL, NULL, '0.00', '0.00', NULL, NULL, '0.00'),
(2, '10/2025', 1, 3, 4, '2026-01-16 08:32:06', 4, '2026-01-16 08:32:47', '0.00', '0.00', NULL, NULL, '0.00'),
(3, '11/2025', 4, 1, NULL, NULL, NULL, NULL, '0.00', '0.00', NULL, NULL, '0.00'),
(11, '12/2025', 28, 3, 4, '2026-01-16 08:09:14', 4, '2026-01-16 08:18:21', '0.00', '0.00', NULL, NULL, '0.00'),
(12, '12/2025', 1, 1, NULL, NULL, NULL, NULL, '0.00', '0.00', NULL, NULL, '0.00');

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

-- --------------------------------------------------------

--
-- Table structure for table `tbsim_bobotkpi`
--

CREATE TABLE `tbsim_bobotkpi` (
  `idbobotkpi` int NOT NULL,
  `id_user` int NOT NULL,
  `bobotwhat` int NOT NULL,
  `bobothow` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbsim_bobotkpi`
--

INSERT INTO `tbsim_bobotkpi` (`idbobotkpi`, `id_user`, `bobotwhat`, `bobothow`) VALUES
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
(16, 33, 0, 0),
(17, 40, 0, 0),
(18, 41, 0, 0),
(19, 28, 60, 40),
(20, 4, 60, 40);

-- --------------------------------------------------------

--
-- Table structure for table `tbsim_hows`
--

CREATE TABLE `tbsim_hows` (
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
-- Dumping data for table `tbsim_hows`
--

INSERT INTO `tbsim_hows` (`id_how`, `id_user`, `id_kpi`, `tipe_how`, `p_how`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`) VALUES
(1, 29, 2, 'A', 'membuat kpi', 50, '0.00', 'oke', 115, 57.5),
(7, 28, 8, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan target', 20, '0.00', 'sesuai', 100, 20),
(8, 28, 8, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'sesuai', 100, 20),
(9, 28, 8, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', '-', 0, 0),
(10, 28, 8, 'A', 'Skill Standart IT (code programming)', 25, '0.00', '-', 0, 0),
(11, 28, 8, 'A', 'abcde', 20, '0.00', 'agak lumayan', 90, 18),
(12, 28, 9, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'sesuai', 100, 60),
(13, 28, 9, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', '-', 0, 0),
(14, 28, 10, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', '-', 0, 0),
(15, 28, 10, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', '-', 0, 0),
(16, 28, 11, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(17, 28, 11, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(18, 28, 11, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(19, 28, 11, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(20, 28, 12, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0),
(21, 28, 12, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0),
(22, 4, 13, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40),
(23, 4, 13, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, '0.00', 'sesuai target', 115, 63),
(24, 4, 13, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5),
(25, 4, 14, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5),
(26, 4, 14, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5),
(27, 4, 15, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', 'NILAI > 3.8', 110, 27.5),
(28, 4, 15, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', 'NILAI 4', 115, 28.75),
(29, 4, 15, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28),
(30, 4, 15, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25),
(31, 4, 16, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75),
(32, 4, 16, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5),
(33, 4, 16, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75);

-- --------------------------------------------------------

--
-- Table structure for table `tbsim_indikator_hows`
--

CREATE TABLE `tbsim_indikator_hows` (
  `id_indikator` int NOT NULL,
  `id_how` int NOT NULL,
  `keterangan` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `urutan` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbsim_indikator_hows`
--

INSERT INTO `tbsim_indikator_hows` (`id_indikator`, `id_how`, `keterangan`, `nilai`, `urutan`, `created_at`) VALUES
(1, 1, 'oke', '115.00', 1, '2025-12-25 13:12:44'),
(2, 3, 'sesuai', '115.00', 1, '2026-01-04 03:17:06'),
(3, 4, 'sesuai', '100.00', 1, '2026-01-04 03:33:03'),
(4, 5, 'sesuai', '100.00', 1, '2026-01-04 03:33:23'),
(5, 6, 'sesuai', '100.00', 1, '2026-01-04 03:35:24'),
(6, 7, 'sesuai', '100.00', 1, '2026-01-18 13:37:04'),
(7, 8, 'sesuai', '100.00', 1, '2026-01-18 13:37:04'),
(8, 11, 'bisa', '115.00', 1, '2026-01-18 13:37:04'),
(9, 11, 'lumayan', '100.00', 2, '2026-01-18 13:37:04'),
(10, 11, 'agak lumayan', '90.00', 3, '2026-01-18 13:37:04'),
(11, 12, 'sesuai', '100.00', 1, '2026-01-18 13:37:04');

-- --------------------------------------------------------

--
-- Table structure for table `tbsim_indikator_whats`
--

CREATE TABLE `tbsim_indikator_whats` (
  `id_indikator` int NOT NULL,
  `id_what` int NOT NULL,
  `keterangan` text NOT NULL,
  `nilai` decimal(5,2) NOT NULL,
  `urutan` int DEFAULT '1',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbsim_indikator_whats`
--

INSERT INTO `tbsim_indikator_whats` (`id_indikator`, `id_what`, `keterangan`, `nilai`, `urutan`, `created_at`) VALUES
(1, 1, 'oke', '115.00', 1, '2025-12-25 13:09:50'),
(2, 3, 'sesuai', '115.00', 1, '2026-01-04 03:16:43'),
(3, 4, 'sesuai', '100.00', 1, '2026-01-04 03:32:24'),
(4, 5, 'sesuai', '100.00', 1, '2026-01-04 03:32:39'),
(5, 6, 'sesuai', '100.00', 1, '2026-01-04 03:35:01'),
(6, 7, '4 aplikasi', '115.00', 1, '2026-01-18 13:37:04'),
(7, 7, '3 aplikasi', '100.00', 2, '2026-01-18 13:37:04'),
(8, 7, '2 aplikasi', '90.00', 3, '2026-01-18 13:37:04'),
(9, 7, '1 aplikasi', '80.00', 4, '2026-01-18 13:37:04'),
(10, 8, '> 4 Aplikasi', '115.00', 1, '2026-01-18 13:37:04'),
(11, 8, '3 Aplikasi', '100.00', 2, '2026-01-18 13:37:04'),
(12, 8, '2 Aplikasi', '90.00', 3, '2026-01-18 13:37:04'),
(13, 8, '1 Aplikasi', '80.00', 4, '2026-01-18 13:37:04'),
(14, 9, '4 fitur', '115.00', 1, '2026-01-18 13:37:04'),
(15, 9, '3 fitur', '100.00', 2, '2026-01-18 13:37:04'),
(16, 9, '2 fitur', '90.00', 3, '2026-01-18 13:37:04'),
(17, 9, '1 fitur', '50.00', 4, '2026-01-18 13:37:04'),
(18, 24, 'manis', '115.00', 1, '2026-01-18 13:42:43'),
(19, 24, 'sedang ', '100.00', 2, '2026-01-18 13:42:43');

-- --------------------------------------------------------

--
-- Table structure for table `tbsim_kpi`
--

CREATE TABLE `tbsim_kpi` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `poin` text NOT NULL,
  `bobot` double NOT NULL,
  `poin2` text NOT NULL,
  `bobot2` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbsim_kpi`
--

INSERT INTO `tbsim_kpi` (`id`, `id_user`, `poin`, `bobot`, `poin2`, `bobot2`) VALUES
(2, 29, 'test', 50, 'tost', 50),
(3, 30, 'okeeee', 20, 'okooooooo', 30),
(8, 28, 'Pembuatan Produk digital (4)', 40, 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', 40),
(9, 28, 'Pemeliharaan sistem', 25, 'Produk digital dapat digunakan tanpa hambatan', 25),
(10, 28, '0% Troubleshooting sistem', 15, 'Melakukan pengecekan berkala ', 15),
(11, 28, 'Absensi', 10, 'Penilaian absensi oleh HRD', 10),
(12, 28, 'Supporting maintenance hardware', 10, 'Membantu maintenance hardware', 10),
(13, 4, 'Meningkatkan Performa Team IT', 30, 'Meningkatkan Performa IT', 30),
(14, 4, 'Penyelesaian Pembuatan/Pengembangan Program', 30, 'Penyelesaian pembuatan/pengembangan program', 30),
(15, 4, 'People Development', 30, 'People Development', 30),
(16, 4, 'Absensi', 10, 'Absensi', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tbsim_whats`
--

CREATE TABLE `tbsim_whats` (
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
-- Dumping data for table `tbsim_whats`
--

INSERT INTO `tbsim_whats` (`id_what`, `id_user`, `id_kpi`, `tipe_what`, `p_what`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`) VALUES
(1, 29, 2, 'A', 'membuat kpi', 40, '0.00', 'oke', 115, 46),
(7, 28, 8, 'A', 'mambuat aplikasi kpi versi 2.1.0', 10, '0.00', '4 aplikasi', 115, 11.5),
(8, 28, 8, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 20, '0.00', '> 4 Aplikasi', 115, 23),
(9, 28, 8, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 20, '0.00', '', 0, 0),
(10, 28, 9, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'sesuai ', 115, 57.5),
(11, 28, 9, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'sesuai ', 100, 50),
(12, 28, 10, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'sesuai', 100, 70),
(13, 28, 10, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'sesuai', 100, 30),
(14, 28, 11, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122),
(15, 28, 12, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100),
(16, 4, 13, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '0.00', '5 applikasi', 115, 115),
(17, 4, 14, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 100, 60),
(18, 4, 14, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46),
(19, 4, 15, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75),
(20, 4, 15, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'EXCELLENT	', 115, 28.75),
(21, 4, 15, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75),
(22, 4, 15, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5),
(23, 4, 16, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120),
(24, 28, 8, 'A', 'membuat eskrim', 10, '0.00', '', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_auth`
--

CREATE TABLE `tb_auth` (
  `id_auth` int NOT NULL,
  `id_user` int NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_auth`
--

INSERT INTO `tb_auth` (`id_auth`, `id_user`, `password`, `level`) VALUES
(1, 1, '123', 1),
(3, 4, 'wahyu123', 2),
(8, 16, '123', 1),
(9, 17, '123', 1),
(10, 18, '123', 1),
(11, 19, '123', 1),
(12, 20, '123', 1),
(13, 21, '123', 1),
(14, 22, '123', 2),
(15, 23, 'karisma123', 4),
(16, 24, '123', 3),
(17, 25, '123', 1),
(18, 26, '123', 1),
(19, 27, '123', 1),
(20, 28, '123', 1),
(21, 29, '123', 5),
(22, 30, '123', 1),
(23, 31, '123', 1),
(25, 33, '123', 1),
(27, 35, '123', 2),
(28, 36, '123', 1),
(29, 37, '123', 1),
(30, 38, '123', 1),
(31, 39, '666', 5),
(32, 40, '123', 1),
(33, 41, '123', 1);

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
(1, 4, 60, 40),
(2, 1, 60, 40),
(3, 18, 60, 40),
(4, 16, 60, 40),
(5, 17, 60, 40),
(6, 19, 60, 40),
(7, 20, 60, 40),
(8, 21, 60, 40),
(9, 22, 60, 40),
(10, 23, 0, 0),
(11, 24, 60, 40),
(12, 25, 0, 0),
(13, 26, 0, 0),
(14, 27, 0, 0),
(15, 28, 60, 40),
(16, 29, 20, 0),
(17, 30, 0, 0),
(18, 31, 0, 0),
(19, 32, 0, 0),
(20, 33, 0, 0),
(21, 34, 0, 0),
(22, 35, 60, 40),
(23, 36, 60, 40),
(24, 37, 60, 40),
(25, 38, 60, 40),
(26, 40, 0, 0),
(27, 41, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_eviden`
--

CREATE TABLE `tb_eviden` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `nama_eviden` varchar(255) NOT NULL,
  `namafoto` varchar(200) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_eviden`
--

INSERT INTO `tb_eviden` (`id`, `id_user`, `nama_eviden`, `namafoto`, `keterangan`) VALUES
(3, 4, 'Data Dummy', 'WhatsApp Image 2025-10-29 at 15.20.14.jpeg', 'Data Dummy tidak bisa digunakan'),
(4, 4, 'Data Dummy 1', 'Quotation - Karisma Indoagro Universal, PT (1).pdf', 'Data Dummy tidak bisa digunakan'),
(5, 4, 'Data Dummy 2', '1 NOVEMBER 2025 PEMBAYARAN.xlsx', 'Data dummy '),
(6, 4, 'test', 'Semua pesanan-2025-11-01-12_50.csv', 'test'),
(7, 4, 'oke', 'nota2.jpg', 'iya'),
(8, 28, 'data kpi januari', 'images.jpg', 'data kpi januari');

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
  `total` double NOT NULL,
  `is_edited` tinyint(1) DEFAULT '0',
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `original_p_how` text,
  `original_bobot` double DEFAULT NULL,
  `original_hasil` text,
  `original_nilai` double DEFAULT NULL,
  `original_total` double DEFAULT NULL,
  `original_target_omset` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_hows`
--

INSERT INTO `tb_hows` (`id_how`, `id_user`, `id_kpi`, `tipe_how`, `p_how`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`, `is_edited`, `edited_by`, `edited_at`, `original_p_how`, `original_bobot`, `original_hasil`, `original_nilai`, `original_total`, `original_target_omset`) VALUES
(14, 4, 5, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 4, 9, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 4, 9, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 4, 5, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, '0.00', 'sesuai target', 115, 63, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 4, 5, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 4, 11, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', 'NILAI > 3.8', 110, 27.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 4, 11, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', 'NILAI 4', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 4, 11, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 4, 11, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 4, 12, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 4, 12, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 4, 12, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 16, 13, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan akhir bulan', 20, '0.00', 'Rata - Rata sesuai dengan target pengerjaan', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 16, 13, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'Report Progress dan done', 110, 22, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 16, 14, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'Terdokumentasi', 115, 69, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 16, 14, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', 'Telah terdokumentasi ', 115, 46, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 16, 15, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', 'Dapat deselesaikan sesuai dengan target', 115, 80.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 16, 15, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', 'Telah terdokumentasi', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 1, 23, 'A', 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '0.00', '100% Terjadwal & di laporkan', 110, 110, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 1, 24, 'A', 'Mendata inventaris karisma per departemen', 50, '0.00', '4 Dept', 100, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 1, 24, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '0.00', '100% Terjadwal & di laporkan', 110, 55, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(35, 1, 25, 'A', 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)', 100, '0.00', 'H+2 dari kerusakan', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(36, 1, 26, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '0.00', '100% Terjadwal & di laporkan & TTD', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 1, 27, 'A', 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '0.00', '4', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(38, 1, 27, 'A', 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 1, 27, 'A', 'Menguasai Support Software penilaian oleh P.Bram', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 1, 28, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% Tepat Waktu', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(41, 1, 28, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '100% taat', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(42, 1, 28, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '100% hadir', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(43, 1, 28, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '100% hadir', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 18, 18, 'A', 'Reset informasi produk untuk dijadikan bahan dalam konsep dan script konten. Kemudian Membuat timeline planning project', 50, '0.00', 'Reset & pembuatan skrip 2 episode Podcast', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 18, 18, 'A', 'Membuat konten video untuk sosial media TikTok, Reels, dan Story Instagram', 50, '0.00', '', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 18, 19, 'A', 'Rutin untuk upload konten-konten desain maupun video produk Karisma dan non produk dengan tujuan memperkenalkan Karisma 20 postingan setiap bulan', 20, '0.00', 'Post:40', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 18, 19, 'A', 'Request work task dari pihak atau tim lain untuk keperluan konten video maupun konten desain', 20, '0.00', 'Request November :\r\n- Request dari tim Sales : 4 Konten selesai\r\n- Request dari sales Online : 2 Konten selesai, 2 konten on progress', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 18, 19, 'A', 'Menyelesaikan design konten hari hari penting Nasional & Internasional selama tiga bulan kedepan', 30, '0.00', 'September : 3 Hari Penting\r\nOktober : 4 Hari penting\r\nNovember : 4 Hari Penting', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 18, 19, 'A', 'Melakukan live streaming Tiktok & Shopee', 30, '0.00', 'Bulan november telah live 17 kali (Kamis (2x tiktok & Shopee) , Jumat (Shopee) & Sabtu(Tiktok))', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 18, 20, 'A', 'Upload Konten-konten desain maupun video produk Karisma dan non produk dengan tujuan memperkenalkan Karisma', 50, '0.00', 'Post:40', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 18, 20, 'A', 'Request work task dari pihak atau tim lain untuk keperluan konten video maupun konten desain', 50, '0.00', 'Request November :\r\n- Request dari tim Sales : 4 Konten selesai\r\n- Request dari sales Online : 2 Konten selesai, 2 konten on progress', 110, 55, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(52, 18, 21, 'A', 'Menguasai Software Editing  penilaian oleh P.Wahyu', 50, '0.00', '', 100, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 18, 21, 'A', 'Menguasai Copywriting dan Content Writing oleh P.Wahyu', 25, '0.00', '', 112, 28, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 18, 21, 'A', 'Menguasai Fotografi & Videografi penilaian oleh P.Wahyu', 25, '0.00', '', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 18, 22, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 18, 22, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 18, 22, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 18, 22, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 17, 29, 'A', 'Setiap bulan harus capai Rp 833.333.333', 50, '0.00', ' 136,574,349.00\r\n/ 833.333.333 * 100 = 16.39%', 50, 25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(61, 17, 29, 'A', 'Posting flayer dan konten yang sedang trend\r\n 280 perbulan di sosmed\r\n', 20, '0.00', '330', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(63, 16, 16, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 16, 16, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 16, 16, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 16, 16, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 16, 17, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 16, 17, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 16, 13, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', 'Disampaikan dalam Management Meeting', 115, 40.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 16, 13, 'A', 'Skill Standart IT (code programming) 4', 25, '0.00', 'Menyesuaikan dengan penilaian management', 112, 28, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 17, 30, 'A', 'Barang yg dikirim sesuai dengan pesanan dan packingan aman sampai tujuan serta tidak terjadi keterlambatan pengiriman', 25, '0.00', '100%', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 17, 30, 'A', 'status 3 metrik gagal 0', 25, '0.00', '3 metrik aman, gagal 0', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 17, 30, 'A', 'omset shopee', 25, '0.00', '20.000.000', 85, 21.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 17, 31, 'A', 'Performa Toko ', 25, '0.00', '3.7', 70, 17.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 17, 31, 'A', 'omset tokopedia', 25, '0.00', '5.000.000', 60, 15, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 17, 32, 'A', 'Hadir Briefing Tepat Waktu	', 25, '0.00', '100% mengikuti breafing	', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 17, 32, 'A', 'Menjalankan SOP tentang absensi	', 50, '0.00', '100 % sop tidak ada yang di langgar	', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 17, 32, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 22, 33, 'A', 'Membuat Jadwal Audit SOP setiap minggu dan ada report tiap minggunya, dan audit dijalankan sesuai jadwal yang telah dibuat', 20, '0.00', 'Jadwal audit dibuat per minggu dan dijalankan', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 17, 29, 'A', 'update flayer 5 produk perbulan ', 20, '0.00', '5', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 17, 30, 'A', 'pelayanan respon chat dan mengatur pesanan dengan cepat', 25, '0.00', '100%', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 17, 29, 'A', 'iklan produk marketplace dilakukan 2x selama 1 bulan', 10, '0.00', '1 kali iklan', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(85, 17, 31, 'A', 'pelayanan respon chat dan mengatur pesanan dengan cepat', 25, '0.00', '100%', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(86, 17, 31, 'A', 'Barang yg dikirim sesuai dengan pesanan dan packingan aman sampai tujuan serta tidak terjadi keterlambatan pengiriman', 25, '0.00', '100%', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(87, 20, 34, 'A', 'Membuat Jadwal Audit SOP setiap minggu dan ada report tiap minggunya, dan audit dijalankan sesuai jadwal yang telah dibuat', 20, '0.00', 'Jadwal audit dibuat per minggu dan dijalankan', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(88, 20, 34, 'A', 'Mengaudit minimal 2 SOP seminggu', 30, '0.00', 'Audit 2 SOP/minggu', 100, 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 20, 34, 'A', 'Membuat Laporan Hasil Audit dan dilaporkan kepada Kadep HRD dan Direksi by email Maksimal H+7 tanpa koreksi', 25, '0.00', 'H+8', 80, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 20, 34, 'A', 'Membuat Laporan hasil audit tiap minggu kepada Kadep HRD', 25, '0.00', 'Hasil audit dilaporkan setiap minggu beserta next step', 105, 26.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 20, 35, 'A', 'Mendokumentasikan & filling KPI All Departemen di server adalah yang terupdate Maksimal H+1 setelah tgl pengumpulan', 15, '0.00', 'H+0 ketika pengumpulan KPI', 105, 15.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 20, 35, 'A', 'Meminta dan merekap Nilai KPI All Departemen ', 25, '0.00', 'Done seluruh Departemen', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 20, 35, 'A', 'Membuat Jadwal Audit KPI per bulan & diserahkan ke Kadep HRD utk Appv, maksimal H-7 sebelum Awal bulan', 10, '0.00', 'Jadwal Audit KPI dibuat per minggu', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 20, 35, 'A', 'Membuat laporan hasil Audit KPI dan dilaporkan ke Kadep HRD setiap minggu', 25, '0.00', 'Belum dilakukan di Juli', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(95, 20, 35, 'A', 'Membuat Resume hasil audit KPI dan diemail kepada managemen menggunakan email kpikiu.hrd@gmail.com maksimal H+7 setelah 1ON1.', 25, '0.00', 'H+8', 80, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(96, 20, 36, 'A', 'Merapikan tampilan SOP  sesuai dengan aturan pembuatan SOP maksimal H+2 setelah SOP di approve', 20, '0.00', 'H+0', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(97, 20, 36, 'A', 'Meminta TTD kepada departemen ybs (yang membuat, dan kadep ybs) maksimal H+3', 10, '0.00', 'H+0', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 20, 36, 'A', 'Meminta TTD kepada Kadep Keuangan dan HRD maksimal H+3', 10, '0.00', 'H+0', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 20, 36, 'A', 'Meminta TTD kepada Direktur maksimal H+3', 10, '0.00', 'H+0 setelah direktur masuk kantor', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 20, 36, 'A', 'Update SOP di Kiuserver maksimal H+3', 20, '0.00', 'H+0 setelah ttd direktur', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 20, 36, 'A', '80% SOP All Departemen adalah SOP yang masih relevan', 30, '0.00', '24%', 50, 15, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(102, 20, 37, 'A', 'Hadir Briefing tepat waktu', 35, '0.00', '0', 115, 40.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(103, 20, 37, 'A', 'Tidak pernah ST/SP', 30, '0.00', '0', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(104, 20, 37, 'A', 'Tidak pernah absen senam sabtu', 35, '0.00', '0', 115, 40.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(105, 19, 38, 'A', 'Melaporkan Absensi harian All Karyawan di WAG HRD Karisma maksimal jam 10.00', 25, '0.00', 'Isi Luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 19, 38, 'A', '1 on 1 Karyawan Sakit', 25, '0.00', 'Isi Luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(107, 19, 38, 'A', 'Membuat Rekap Absensi Mingguan dan di Share di WAG Kadep Disscusion with HRD', 25, '0.00', 'Isi Luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 19, 38, 'A', 'Mengupdate Kuota Absensi setiap ada keluar masuk karyawan dan menginfokan di WAG Kadep', 10, '0.00', 'Isi Luk', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 19, 38, 'A', 'Membuat Rekap Absensi Bulanan Maksimal H+7 dan dilaporkan ke Kadep HRD', 15, '0.00', 'Isi Luk', 115, 17.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(110, 19, 39, 'A', 'Membuat Tagihan BPJS maksimal tanggal 7 setiap bulannya', 30, '0.00', 'Isi Luk', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(111, 19, 39, 'A', 'Menyelesaikan semua data laporan bulanan ( Absensi, BPJS, Laporan Keluar Masuk Kary, Reward hadir dan Pemotongan gaji kary) maksimal tanggal 7 bulan berikutnya', 40, '0.00', 'Isi Luk', 115, 46, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 19, 39, 'A', 'Mengirim email ke Direktur untuk data-data tersebut maksimal tanggal 10 bulan berikutnya', 30, '0.00', 'Isi Luk', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 19, 40, 'A', 'Mengupload loker maksimal H+1 setelah approve oleh direksi', 20, '0.00', 'Isi Luk', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 19, 40, 'A', 'Share Loker di beberapa media sosial per  minggu minimal 2 media sosial, 8 media sosial per bulan', 25, '0.00', 'Isi Luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 19, 40, 'A', 'Membuat Jadwal Rekrutmen tiap hari sabtu dan menjalankan sesuai jadwal', 20, '0.00', 'Isi Luk', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(116, 19, 40, 'A', 'Menemukan ide baru minimal 2 ide/bulan', 20, '0.00', 'Isi Luk', 80, 16, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 19, 40, 'A', 'Tidak ada pelanggaran SOP Rekrutmen', 15, '0.00', 'Isi Luk', 115, 17.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 19, 41, 'A', 'Merapikan semua surat, per masing', 100, '0.00', 'Isi Luk', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 19, 42, 'A', 'Hadir Briefing tepat waktu', 35, '0.00', '0', 115, 40.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(120, 19, 42, 'A', 'Tidak pernah ST/SP', 30, '0.00', '0', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(121, 19, 42, 'A', 'Tidak pernah absen senam sabtu', 35, '0.00', '0', 115, 40.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(122, 21, 43, 'A', 'Memastikan Mekanik melakukan cek kondisi mesin,rem, kopling, kelistrikan, olie mesin, air accu, air radiator (sesuai dengan form checklist)', 20, '0.00', '100% dilakukan pengecekan all kendaraan sesuai jadwal', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(123, 21, 43, 'A', 'Tidak ada komplain dari Driver Distribusi/ kendaraan selalu siap digunakan setiap hari', 20, '0.00', 'Penyelesaian komplain max H+3 All unit', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(124, 21, 43, 'A', 'Membuat laporan tiap minggu untuk pengecekan Harian All Kendaraan', 20, '0.00', 'Membuat laporan  tiap minggu dan ada analisa', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(125, 21, 43, 'A', '100% SOP Kendaraan teraudit dan dijalankan', 20, '0.00', '100% SOP kendaraan teraudit dan di pastikan sudah relevan', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(126, 21, 43, 'A', 'Membuat analisa terkait kendaraan prima dan dilaporkan ke Kadep maksimal tgl 7 tiap bulannya', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(127, 21, 44, 'A', 'Melakukan supervisi setiap hari dan melaporkan hasil temuan setiap minggu', 20, '0.00', 'Melakukan supervisi setiap hari dan melaporkan hasil temuan setiap minggu', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(128, 21, 44, 'A', 'Tidak ada komplain dari karyawan terkait gedung dan inventaris kantor', 20, '0.00', 'Nol Komplain', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(129, 21, 44, 'A', 'Memastikan SOP terkait perawatan gedung dan Inventaris kantor teraudit dan dijalankan', 20, '0.00', '< 80% SOP Gedung dan inventaris teraudit dan relevan', 50, 10, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(130, 21, 44, 'A', '100% Gedung terawat tiap bulannya', 15, '0.00', '100% gedung terawat', 100, 15, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(131, 21, 44, 'A', '100% Inventaris Kantor Terawat dan terdata ', 15, '0.00', '90% inventaris terawat dan terdata', 50, 7.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(132, 21, 44, 'A', 'Membuat laporan hasil audit dan melaporkan setiap bulan by email ke Kadep dan Direksi maksimal H+7', 10, '0.00', '< H+5', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(133, 21, 45, 'A', 'Hadir Briefing tepat waktu', 50, '0.00', '0', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(134, 21, 45, 'A', 'Tidak pernah absen senam sabtu', 50, '0.00', '0', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(136, 28, 51, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan target', 20, '0.00', 'sesuai', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(137, 28, 51, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'sesuai', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(138, 28, 52, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'sesuai', 100, 60, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(139, 28, 52, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', '-', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(140, 28, 53, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', '-', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(141, 28, 53, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', '-', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(142, 28, 54, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(143, 28, 54, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(144, 28, 54, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(145, 28, 54, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(146, 28, 55, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(147, 28, 55, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(148, 28, 51, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', '-', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(149, 28, 51, 'A', 'Skill Standart IT (code programming)', 25, '0.00', '-', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(153, 28, 51, 'A', 'abcde', 20, '0.00', 'agak lumayan', 90, 18, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(154, 29, 56, 'A', 'mambuat kpi sesuai jadwal', 30, '0.00', 'oke2', 100, 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(155, 24, 60, 'A', 'sesuai rilis', 30, '0.00', 'okee', 100, 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_edited` tinyint(1) DEFAULT '0',
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `original_keterangan` text,
  `original_nilai` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_indikator_hows`
--

INSERT INTO `tb_indikator_hows` (`id_indikator`, `id_how`, `keterangan`, `nilai`, `urutan`, `created_at`, `is_edited`, `edited_by`, `edited_at`, `original_keterangan`, `original_nilai`) VALUES
(1, 153, 'bisa', '115.00', 1, '2025-12-22 13:38:38', 0, NULL, NULL, NULL, NULL),
(2, 153, 'lumayan', '100.00', 2, '2025-12-22 13:38:38', 0, NULL, NULL, NULL, NULL),
(3, 153, 'agak lumayan', '90.00', 3, '2025-12-22 13:38:38', 0, NULL, NULL, NULL, NULL),
(4, 154, 'oke1', '115.00', 1, '2025-12-25 13:04:15', 0, NULL, NULL, NULL, NULL),
(5, 154, 'oke2', '100.00', 2, '2025-12-25 13:04:15', 0, NULL, NULL, NULL, NULL),
(6, 154, 'oke2', '90.00', 3, '2025-12-25 13:04:15', 0, NULL, NULL, NULL, NULL),
(7, 138, 'sesuai', '100.00', 1, '2026-01-04 02:59:54', 0, NULL, NULL, NULL, NULL),
(8, 136, 'sesuai', '100.00', 1, '2026-01-04 03:00:24', 0, NULL, NULL, NULL, NULL),
(9, 137, 'sesuai', '100.00', 1, '2026-01-04 03:00:33', 0, NULL, NULL, NULL, NULL),
(10, 155, 'okee', '100.00', 1, '2026-01-06 12:02:34', 0, NULL, NULL, NULL, NULL);

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
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_edited` tinyint(1) DEFAULT '0',
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `original_keterangan` text,
  `original_nilai` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_indikator_whats`
--

INSERT INTO `tb_indikator_whats` (`id_indikator`, `id_what`, `keterangan`, `nilai`, `urutan`, `created_at`, `is_edited`, `edited_by`, `edited_at`, `original_keterangan`, `original_nilai`) VALUES
(5, 108, '100% SOP telah di audit sd <Oktober', '115.00', 1, '2026-01-09 10:31:44', 0, NULL, NULL, NULL, NULL),
(6, 108, '100% SOP telah di audit sd Oktober', '110.00', 2, '2026-01-09 10:31:44', 0, NULL, NULL, NULL, NULL),
(7, 108, '100% SOP telah di audit sd November', '100.00', 3, '2026-01-09 10:31:44', 0, NULL, NULL, NULL, NULL),
(8, 108, '100% SOP telah di audit sd Desember', '90.00', 4, '2026-01-09 10:31:44', 0, NULL, NULL, NULL, NULL),
(10, 109, '4 aplikasi', '115.00', 1, '2026-01-09 11:54:19', 1, 4, '2026-01-17 15:06:58', '4 aplikasi', '115.00'),
(11, 109, '3 aplikasi', '100.00', 2, '2026-01-09 11:54:19', 0, NULL, NULL, NULL, NULL),
(12, 109, '2 aplikasi', '90.00', 3, '2026-01-09 11:54:19', 0, NULL, NULL, NULL, NULL),
(13, 109, '1 aplikasi', '80.00', 4, '2026-01-09 11:54:19', 0, NULL, NULL, NULL, NULL),
(17, 111, 'okeee', '115.00', 1, '2026-01-09 13:13:26', 0, NULL, NULL, NULL, NULL),
(18, 112, '100% SOP telah di audit sd <Oktober', '115.00', 1, '2026-01-09 13:15:56', 0, NULL, NULL, NULL, NULL),
(19, 112, '100% SOP telah di audit sd Oktober', '100.00', 2, '2026-01-09 13:15:56', 0, NULL, NULL, NULL, NULL),
(20, 112, '100% SOP telah di audit sd November', '90.00', 3, '2026-01-09 13:15:56', 0, NULL, NULL, NULL, NULL),
(21, 112, '100% SOP telah di audit sd Desember', '80.00', 4, '2026-01-09 13:15:56', 0, NULL, NULL, NULL, NULL),
(22, 113, '100% SOP telah di audit sd <Oktober', '100.00', 1, '2026-01-09 13:19:29', 0, NULL, NULL, NULL, NULL),
(23, 113, '90% SOP telah di audit sd Oktober', '80.00', 2, '2026-01-09 13:19:29', 0, NULL, NULL, NULL, NULL),
(24, 114, '100% SOP telah di audit', '100.00', 1, '2026-01-09 13:20:43', 0, NULL, NULL, NULL, NULL),
(25, 114, '90% SOP telah di audit', '90.00', 2, '2026-01-09 13:20:43', 0, NULL, NULL, NULL, NULL),
(26, 115, '4 aplikasi', '100.00', 1, '2026-01-09 15:12:34', 0, NULL, NULL, NULL, NULL),
(28, 117, 'Kendaraan bersih', '100.00', 1, '2026-01-13 15:46:37', 0, NULL, NULL, NULL, NULL),
(29, 118, '> 4 Aplikasi', '115.00', 1, '2026-01-18 12:46:54', 0, NULL, NULL, NULL, NULL),
(30, 118, '3 Aplikasi', '100.00', 2, '2026-01-18 12:46:54', 0, NULL, NULL, NULL, NULL),
(31, 118, '2 Aplikasi', '90.00', 3, '2026-01-18 12:46:54', 0, NULL, NULL, NULL, NULL),
(32, 118, '1 Aplikasi', '80.00', 4, '2026-01-18 12:46:54', 0, NULL, NULL, NULL, NULL),
(33, 119, '4 fitur', '115.00', 1, '2026-01-18 12:50:49', 0, NULL, NULL, NULL, NULL),
(34, 119, '3 fitur', '100.00', 2, '2026-01-18 12:50:49', 0, NULL, NULL, NULL, NULL),
(35, 119, '2 fitur', '90.00', 3, '2026-01-18 12:50:49', 0, NULL, NULL, NULL, NULL),
(36, 119, '1 fitur', '50.00', 4, '2026-01-18 12:50:49', 0, NULL, NULL, NULL, NULL);

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
(5, 4, 'Meningkatkan Performa Team IT', 30, 'Meningkatkan Performa IT', 30),
(9, 4, 'Penyelesaian Pembuatan/Pengembangan Program', 30, 'Penyelesaian pembuatan/pengembangan program', 30),
(11, 4, 'People Development', 30, 'People Development', 30),
(12, 4, 'Absensi', 10, 'Absensi', 10),
(13, 16, 'Pembuatan Produk digital (4)', 40, 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', 40),
(14, 16, 'Pemeliharaan sistem', 25, 'Produk digital dapat digunakan tanpa hambatan', 25),
(15, 16, '0% Troubleshooting sistem', 15, 'Melakukan pengecekan berkala ', 15),
(16, 16, 'Absensi', 10, 'Penilaian absensi oleh HRD', 10),
(17, 16, 'Supporting maintenance hardware', 10, 'Membantu maintenance hardware', 10),
(18, 18, 'Konten Video dan Desain Sosial Media 300', 30, 'Riset Konsep & Membuat konten', 30),
(19, 18, 'Tayangan Instagram, Interaksi Instagram, & follow Instagram', 25, 'Peningkatan Insight Sosial Media Instagram', 25),
(20, 18, 'Tayangan Tiktok, Like Tiktok,Tampilan Profile Tiktok & Followers Tiktok', 20, 'Peningkatan Live Streaming & Insight Sosial Media TikTok', 20),
(21, 18, 'Menguasai Skill standart Content Creator', 15, 'Menguasai 3 Skill standart', 15),
(22, 18, 'Absensi', 10, 'Absensi', 10),
(23, 1, 'Support Konten Kreator', 15, 'Preparing hardware konten & livestream', 15),
(24, 1, 'Maintenance Hardware ', 25, 'Update Hardware', 25),
(25, 1, 'CCTV Karisma 100%', 30, 'Update CCTV Karisma', 30),
(26, 1, 'Support Software Digital', 10, 'Mampu Membuat Aplikasi', 10),
(27, 1, 'Menguasai 5 Skill Standart', 10, 'Menguasai 5 Skill Standart', 10),
(28, 1, 'Absensi', 10, 'Absensi', 10),
(29, 17, 'Mencapai total omzet 10 M', 40, 'Mencapai total omzet 10 M', 40),
(30, 17, 'Performa Rating Shopee', 25, 'Performa Rating Shopee', 25),
(31, 17, 'Performa Rating TikTok Shop', 25, 'Performa Rating TikTok Shop', 25),
(32, 17, 'Absensi', 10, 'Absensi', 10),
(34, 20, 'AUDIT SOP KIU', 30, 'Jadwal audit, audit minimal 8 SOP per bulan, melaporkan hasil audit', 30),
(35, 20, 'AUDIT KPI ALL KARYAWAN', 30, 'Dokumentasi KPI, Rekap Nilai KPI, Jadwal Audit, Hasil Audit', 30),
(36, 20, 'PROSES ADMINISTRASI SOP CEPAT DAN TERUPDATE, SOP di KIUSERVER adalah yang TERUPDATE', 30, 'Merapikan, meminta ttd, update kiuserver max H+3', 30),
(37, 20, 'Absensi', 10, 'Absensi', 10),
(38, 19, 'ABSENSI SEMUA KARYAWAN 99% (control) 98,3% (Uncontrol)', 25, 'Melaporkan Absensi Harian, Mingguan, Bulanan, update Kuota, 1on1 karyawan', 25),
(39, 19, 'Pelaporan Data Bulanan Kepada Direksi dengan Tepat dan Cepat', 20, 'Pelaporan Data Bulanan Kepada Direksi dengan Tepat dan Cepat', 20),
(40, 19, 'BIROKRASI DAN ADMINISTRASI PROSES RECRUITMENT ', 25, 'Upload Loker, Share Loker, Jadwal Recrutmen, Ide', 25),
(41, 19, 'ADMINISTRASI & DATA-DATA HRD TERDOKUMENTASI DAN TERFILING DENGAN RAPI DAN UPDATE', 20, 'Membuat map per masing-masing klasifikasi', 20),
(42, 19, 'Absensi', 10, 'Absensi', 10),
(43, 21, 'KENDARAAN DISTRIBUSI 100% PRIMA', 45, 'KENDARAAN DISTRIBUSI 100% PRIMA', 45),
(44, 21, '100% Gedung & Inventaris Kantor Terawat, dan Nol Komplain', 45, '100% Gedung & Inventaris Kantor Terawat, dan Nol Komplain', 45),
(45, 21, 'Absensi', 10, 'Absensi', 10),
(51, 28, 'Pembuatan Produk digital (4)', 40, 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', 40),
(52, 28, 'Pemeliharaan sistem', 25, 'Produk digital dapat digunakan tanpa hambatan', 25),
(53, 28, '0% Troubleshooting sistem', 15, 'Melakukan pengecekan berkala ', 15),
(54, 28, 'Absensi', 10, 'Penilaian absensi oleh HRD', 10),
(55, 28, 'Supporting maintenance hardware', 10, 'Membantu maintenance hardware', 10),
(56, 29, 'test', 30, 'tost', 30),
(57, 30, 'oke', 10, 'okee', 20),
(58, 31, 'mencapai omset 150 m', 25, 'mencapai 150m', 25),
(59, 36, 'NOL BARANG KEMBALI', 10, 'NOL BARANG KEMBALI', 10),
(60, 24, 'membuat kpi', 30, 'membuat kpi2', 30),
(61, 40, 'Produk digital', 40, 'Produk Digital', 40),
(65, 37, 'PERAWATAN KENDARAAN', 15, 'PERAWATAN KENDARAAN', 15);

-- --------------------------------------------------------

--
-- Table structure for table `tb_kpi_history`
--

CREATE TABLE `tb_kpi_history` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `id_kpi` int DEFAULT NULL,
  `bulan` varchar(7) NOT NULL,
  `poin_what` text,
  `poin_how` text,
  `bobot_what` decimal(5,2) DEFAULT '0.00',
  `bobot_how` decimal(5,2) DEFAULT '0.00',
  `total_what_raw` decimal(10,2) DEFAULT '0.00',
  `total_how_raw` decimal(10,2) DEFAULT '0.00',
  `nilai_what` decimal(10,2) DEFAULT '0.00',
  `nilai_how` decimal(10,2) DEFAULT '0.00',
  `is_summary` tinyint(1) DEFAULT '0',
  `total_kpi_real` decimal(10,2) DEFAULT '0.00',
  `total_kpi_target` decimal(10,2) DEFAULT '0.00',
  `total_what` decimal(10,2) DEFAULT '0.00',
  `total_how` decimal(10,2) DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kpi_history`
--

INSERT INTO `tb_kpi_history` (`id`, `id_user`, `id_kpi`, `bulan`, `poin_what`, `poin_how`, `bobot_what`, `bobot_how`, `total_what_raw`, `total_how_raw`, `nilai_what`, `nilai_how`, `is_summary`, `total_kpi_real`, `total_kpi_target`, `total_what`, `total_how`, `created_at`) VALUES
(9, 4, NULL, '2025-12', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '105.60', '102.00', '106.00', '105.20', '2025-12-31 03:00:00'),
(14, 4, 5, '2026-01', 'Meningkatkan Performa Team IT', 'Meningkatkan Performa IT', '30.00', '30.00', '115.00', '114.50', '34.50', '34.35', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 10:44:02'),
(15, 4, 9, '2026-01', 'Penyelesaian Pembuatan/Pengembangan Program', 'Penyelesaian pembuatan/pengembangan program', '30.00', '30.00', '106.00', '115.00', '31.80', '34.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 10:44:02'),
(16, 4, 11, '2026-01', 'People Development', 'People Development', '30.00', '30.00', '98.75', '109.25', '29.63', '32.78', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 10:44:02'),
(17, 4, 12, '2026-01', 'Absensi', 'Absensi', '10.00', '10.00', '120.00', '115.00', '12.00', '11.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 10:44:02'),
(18, 4, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '110.01', '110.01', '107.93', '113.13', '2026-01-11 10:48:41'),
(19, 4, 5, '2025-12', 'Meningkatkan Performa Team IT', 'Meningkatkan Performa IT', '28.00', '28.00', '110.00', '108.00', '35.80', '30.24', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(20, 4, 9, '2025-12', 'Penyelesaian Pembuatan/Pengembangan Program', 'Penyelesaian pembuatan/pengembangan program', '25.00', '25.00', '102.00', '100.00', '25.50', '25.00', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(21, 4, 11, '2025-12', 'People Development', 'People Development', '27.00', '27.00', '95.00', '97.50', '25.65', '26.33', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(22, 4, 12, '2025-12', 'Absensi', 'Absensi', '10.00', '10.00', '118.00', '112.00', '11.80', '11.20', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(23, 23, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-11 11:26:10'),
(24, 28, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '60.77', '66.29', '68.68', '48.90', '2026-01-11 15:54:23'),
(25, 28, 51, '2026-01', 'Pembuatan Produk digital (4)', 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', '40.00', '40.00', '11.50', '58.00', '4.60', '23.20', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(26, 28, 52, '2026-01', 'Pemeliharaan sistem', 'Produk digital dapat digunakan tanpa hambatan', '25.00', '25.00', '107.50', '60.00', '26.88', '15.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(27, 28, 53, '2026-01', '0% Troubleshooting sistem', 'Melakukan pengecekan berkala ', '15.00', '15.00', '100.00', '0.00', '15.00', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(28, 28, 54, '2026-01', 'Absensi', 'Penilaian absensi oleh HRD', '10.00', '10.00', '122.00', '107.00', '12.20', '10.70', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(29, 28, 55, '2026-01', 'Supporting maintenance hardware', 'Membantu maintenance hardware', '10.00', '10.00', '100.00', '0.00', '10.00', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(30, 40, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '9.20', '0.00', '2026-01-12 10:19:23'),
(31, 40, 61, '2026-01', 'Produk digital', 'Produk Digital', '40.00', '40.00', '23.00', '0.00', '9.20', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:19:23'),
(32, 20, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '85.57', '0.00', '80.80', '92.73', '2026-01-12 10:20:39'),
(33, 20, 34, '2026-01', 'AUDIT SOP KIU', 'Jadwal audit, audit minimal 8 SOP per bulan, melaporkan hasil audit', '30.00', '30.00', '50.00', '99.25', '15.00', '29.78', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(34, 20, 35, '2026-01', 'AUDIT KPI ALL KARYAWAN', 'Dokumentasi KPI, Rekap Nilai KPI, Jadwal Audit, Hasil Audit', '30.00', '30.00', '105.00', '76.00', '31.50', '22.80', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(35, 20, 36, '2026-01', 'PROSES ADMINISTRASI SOP CEPAT DAN TERUPDATE, SOP di KIUSERVER adalah yang TERUPDATE', 'Merapikan, meminta ttd, update kiuserver max H+3', '30.00', '30.00', '76.00', '95.50', '22.80', '28.65', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(36, 20, 37, '2026-01', 'Absensi', 'Absensi', '10.00', '10.00', '115.00', '115.00', '11.50', '11.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(37, 35, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-13 12:24:00'),
(38, 22, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-14 15:40:51'),
(39, 27, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-14 15:49:34'),
(40, 1, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '110.70', '0.00', '110.50', '111.00', '2026-01-16 09:50:29'),
(41, 1, 23, '2026-01', 'Support Konten Kreator', 'Preparing hardware konten & livestream', '15.00', '15.00', '115.00', '110.00', '17.25', '16.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 09:50:29'),
(42, 1, 24, '2026-01', 'Maintenance Hardware ', 'Update Hardware', '25.00', '25.00', '115.00', '105.00', '28.75', '26.25', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 09:50:29'),
(43, 1, 25, '2026-01', 'CCTV Karisma 100%', 'Update CCTV Karisma', '30.00', '30.00', '100.00', '115.00', '30.00', '34.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 09:50:29'),
(44, 1, 26, '2026-01', 'Support Software Digital', 'Mampu Membuat Aplikasi', '10.00', '10.00', '115.00', '115.00', '11.50', '11.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 09:50:29'),
(45, 1, 27, '2026-01', 'Menguasai 5 Skill Standart', 'Menguasai 5 Skill Standart', '10.00', '10.00', '100.00', '107.50', '10.00', '10.75', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 09:50:29'),
(46, 1, 28, '2026-01', 'Absensi', 'Absensi', '10.00', '10.00', '130.00', '115.00', '13.00', '11.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 09:50:29'),
(47, 24, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '7.20', '0.00', '6.00', '9.00', '2026-01-16 15:07:24'),
(48, 24, 60, '2026-01', 'membuat kpi', 'membuat kpi2', '30.00', '30.00', '20.00', '30.00', '6.00', '9.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-16 15:07:24'),
(49, 17, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '87.53', '0.00', '84.10', '92.68', '2026-01-18 08:00:23'),
(50, 17, 29, '2026-01', 'Mencapai total omzet 10 M', 'Mencapai total omzet 10 M', '40.00', '40.00', '50.00', '79.50', '20.00', '31.80', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-18 08:00:23'),
(51, 17, 30, '2026-01', 'Performa Rating Shopee', 'Performa Rating Shopee', '25.00', '25.00', '112.50', '107.50', '28.13', '26.88', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-18 08:00:23'),
(52, 17, 31, '2026-01', 'Performa Rating TikTok Shop', 'Performa Rating TikTok Shop', '25.00', '25.00', '97.50', '90.00', '24.38', '22.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-18 08:00:23'),
(53, 17, 32, '2026-01', 'Absensi', 'Absensi', '10.00', '10.00', '116.00', '115.00', '11.60', '11.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-18 08:00:23');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kpi_verified`
--

CREATE TABLE `tb_kpi_verified` (
  `id` int NOT NULL,
  `id_user` int NOT NULL,
  `bulan` varchar(10) NOT NULL,
  `verified_by` int NOT NULL,
  `verified_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `keterangan` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_kpi_verified`
--

INSERT INTO `tb_kpi_verified` (`id`, `id_user`, `bulan`, `verified_by`, `verified_at`, `keterangan`) VALUES
(2, 1, '01/2026', 4, '2026-01-16 21:54:31', ''),
(3, 4, '01/2026', 23, '2026-01-16 22:30:50', ''),
(4, 17, '01/2026', 4, '2026-01-18 15:01:11', '');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sop`
--

CREATE TABLE `tb_sop` (
  `id_sop` int NOT NULL,
  `nama_sop` varchar(255) NOT NULL,
  `kode_sop` varchar(50) NOT NULL,
  `tipe_sop` varchar(50) NOT NULL,
  `namafile_sop` varchar(255) NOT NULL,
  `is_karisma` int NOT NULL,
  `is_prioritas` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_sop`
--

INSERT INTO `tb_sop` (`id_sop`, `nama_sop`, `kode_sop`, `tipe_sop`, `namafile_sop`, `is_karisma`, `is_prioritas`) VALUES
(3, 'SOP Membuat Sales Program atau Kontrak jual Baru #0', 'Sales 282', 'Sales', 'Sales 282.pdf', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tb_ss`
--

CREATE TABLE `tb_ss` (
  `id_poinss` int NOT NULL,
  `id_user` int NOT NULL,
  `poin_ss` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_ss`
--

INSERT INTO `tb_ss` (`id_poinss`, `id_user`, `poin_ss`) VALUES
(1, 1, 'Leadership'),
(2, 4, 'leadership'),
(3, 1, 'Menguasai Software'),
(6, 28, 'platform digital'),
(7, 1, 'menguasai coding');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sspoin`
--

CREATE TABLE `tb_sspoin` (
  `id_sspoin` int NOT NULL,
  `id_user` int NOT NULL,
  `id_ss` int NOT NULL,
  `poinss` varchar(255) NOT NULL,
  `nilai1` varchar(255) DEFAULT NULL,
  `nilai2` varchar(255) DEFAULT NULL,
  `nilai3` varchar(255) DEFAULT NULL,
  `nilai4` varchar(255) DEFAULT NULL,
  `nilaiss` double NOT NULL DEFAULT '0',
  `deskripsi` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_sspoin`
--

INSERT INTO `tb_sspoin` (`id_sspoin`, `id_user`, `id_ss`, `poinss`, `nilai1`, `nilai2`, `nilai3`, `nilai4`, `nilaiss`, `deskripsi`) VALUES
(1, 1, 1, 'Mampu membuat & menerapkan KPI untuk dirinya sendiri', 'belum bisa', 'setengah bisa', 'bisa', 'sangat bisa', 3, ''),
(2, 1, 1, 'Mampu membuat & menerapkan SOP untuk dirinya sendiri', '', '', '', '', 0, ''),
(3, 1, 1, 'Mampu memimpin dengan data', '', '', '', '', 0, ''),
(4, 1, 1, 'Mampu melakukan coaching dengan data menggunakan Skill Standar & Mikro skill AL & EQ', '', '', '', '', 0, ''),
(5, 1, 1, 'Mempunyai Integritas ( mampu mempertanggung jawabkan apa yang diucapkan )', '', '', '', '', 0, ''),
(6, 1, 1, 'Mampu membuat Action Plan.  Isi Action Plan : (What) : Smart Goal , (How) : Tahapan Rencana yg terukur & ada waktunya', '', '', '', '', 0, ''),
(7, 1, 1, 'Mempunyai Problem Solving. Next Stepnya terukur, ada waktunya & merupakan solusi permanen, dan menyelesaikannya sampai tuntas, dan tidak terjadi lagi masalah yang sama. Mampu mengidentifikasi masalah, mampu membuat next stepnya bersama team', '', '', '', '', 0, ''),
(8, 1, 1, 'Mau dan mampu menerima tantangan & senang ilmu', '', '', '', '', 1, ''),
(10, 1, 1, 'Agile : Banyak mempunyai ide & inisiatif untuk mencapai goalnya', '', '', '', '', 0, ''),
(17, 4, 2, 'Bertanggung Jawab', 'tidak bertanggung jawab', 'sedang bertanggung jawab', 'lumayan bertanggung jawab', 'sangat bertanggung jawab', 3, ''),
(18, 28, 6, 'coding', 'bisa', 'lumayan bisa', 'bisa', 'sangat bisa', 4, ''),
(19, 1, 1, 'kerapian', 'belum oke', 'sedikit belum', 'agak', 'sudah oke', 0, ''),
(22, 4, 6, 'disiplin', NULL, NULL, NULL, NULL, 0, ''),
(23, 28, 6, 'disiplin234', NULL, NULL, NULL, NULL, 3, 'okeee'),
(24, 4, 2, 'tepat waktu', NULL, NULL, NULL, NULL, 3, 'belum');

-- --------------------------------------------------------

--
-- Table structure for table `tb_surat_peringatan`
--

CREATE TABLE `tb_surat_peringatan` (
  `id_sp` int NOT NULL,
  `id_user` int NOT NULL,
  `jenis_sp` enum('SP1','SP2','SP3') NOT NULL,
  `nomor_sp` varchar(100) NOT NULL,
  `tanggal_sp` date NOT NULL,
  `masa_berlaku_mulai` date NOT NULL,
  `masa_berlaku_selesai` date NOT NULL,
  `alasan` text NOT NULL,
  `keterangan` text,
  `status` enum('aktif','selesai','dihapus') DEFAULT 'aktif',
  `created_by` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_surat_peringatan`
--

INSERT INTO `tb_surat_peringatan` (`id_sp`, `id_user`, `jenis_sp`, `nomor_sp`, `tanggal_sp`, `masa_berlaku_mulai`, `masa_berlaku_selesai`, `alasan`, `keterangan`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 23, 'SP1', 'sp/001/hrd', '2026-01-02', '2026-01-02', '2026-01-03', 'merokok', '', 'dihapus', 29, '2026-01-02 14:07:47', '2026-01-02 14:11:37'),
(2, 4, 'SP1', '10/00/00', '2026-01-02', '2026-01-02', '2026-02-01', 'merokok', '', 'aktif', 29, '2026-01-02 14:13:36', '2026-01-02 14:13:36'),
(3, 1, 'SP2', 'sp/01', '2026-01-08', '2026-01-08', '2026-07-08', 'merikok', '', 'aktif', 29, '2026-01-08 12:32:54', '2026-01-08 12:32:54');

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int NOT NULL,
  `username` varchar(255) NOT NULL,
  `nama_lngkp` varchar(255) NOT NULL,
  `nik` varchar(255) NOT NULL,
  `bagian` varchar(255) NOT NULL,
  `departement` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `atasan` varchar(255) NOT NULL,
  `penilai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `username`, `nama_lngkp`, `nik`, `bagian`, `departement`, `jabatan`, `atasan`, `penilai`) VALUES
(1, 'rvld', 'Dhany Rifaldi Febriansah', 'Kiu21', 'IT Hardware', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(4, 'wahyu', 'Wahyu Arif Prasetyo', 'QIU1910315', 'IT', 'IT', 'Manager', 'Diana Wulandari', 'Diana Wulandari'),
(16, 'Bram', 'Maulana Malik Ibrahim', 'KIU12', 'IT Software', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(17, 'Sheila', 'Sheila Masdaliana Harahap', 'KIU13', 'Sales Onlineshop', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(18, 'Arinda', 'Egata Arinda Prameswari', 'KIU14', 'Konten Kreator', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(19, 'Luluk', 'Luluk Fitria', 'KIU045', 'HRD', 'HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(20, 'Siwi', 'Siwi Mardlatus Syarifah', 'KIU0452', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(21, 'Amin', 'M. Amin Nudin', 'KIU042', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(22, 'Riza', 'Riza Dwi Fitrianingtyas', 'KIU046', 'HRD', 'Keuangan & HRD', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(23, 'Diana', 'Diana Wulandari', 'KIU92', 'Kepala Departemen', 'Keuangan & HRD', 'Direktur', 'Direksi', 'Direksi'),
(24, 'Vita', 'Vita Ari Puspita', 'QIU1101054', 'Team Collection', 'Keuangan & HRD', 'Kadep', 'Diana Wulandari', 'Diana Wulandari'),
(25, 'Arini', 'Arini Dina Yasmin', 'QIU1503089', 'Purchasing', 'Keuangan & HRD', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(26, 'Kurniawan', 'Kurniawan Pratama Arifin', 'QIU2104259', 'Logistik', 'Logistik', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(27, 'Evi', 'Evi Yulia Purnama Sari', 'QIU0511030', 'Sales', 'Sales & Marketing', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(28, 'prayoga', 'Anang Prayoga', 'lalala123', 'IT', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(29, 'adminhrd', 'Admin HRD', '00000', 'HRD', 'Keuangan & HRD', 'Admin HRD', '-', '-'),
(30, 'test', 'testok', '1234', 'IT', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(31, 'sales', 'sales1', '1234', 'sc', 'Sales & Marketing', 'Karyawan', 'Evi Yulia', 'Heru Sucahyo'),
(33, 'sales2', 'sales2', '1234', 'SC', 'Sales & Marketing', 'Karyawan', 'Evi Yulia', 'Heru Sucahyo'),
(35, 'adminedp', 'Wildan Ma\'ruf N. W.', 'EDP001', 'Logistik', 'Logistik', 'Manager', 'Kurniawan Pratama Arifin', 'Diana Wulandari'),
(36, 'driver1', 'Budi Santoso', 'DRV001', 'Driver Distribusi', 'Logistik', 'Karyawan', 'Wildan Ma\'ruf N. W.', 'Kurniawan Pratama Arifin'),
(37, 'driver2', 'Agus Wijaya', 'DRV002', 'Driver Distribusi', 'Logistik', 'Karyawan', 'Wildan Ma\'ruf N. W.', 'Kurniawan Pratama Arifin'),
(38, 'driver3', 'Slamet Riyadi', 'DRV003', 'Driver Distribusi', 'Logistik', 'Karyawan', 'Wildan Ma\'ruf N. W.', 'Kurniawan Pratama Arifin'),
(39, 'itboy', 'itboy', 'IT001', 'System', 'IT', 'Admin IT', '-', '-'),
(40, 'siwiw', 'siwiw', 'QIU1101054', 'HRD', 'HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(41, 'driver4', 'drver4', '321', 'Driver Distribusi', 'Logistik', 'Karyawan', 'Wildan Ma\'ruf N. W.', '');

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
  `total` double NOT NULL,
  `is_edited` tinyint(1) DEFAULT '0',
  `edited_by` int DEFAULT NULL,
  `edited_at` timestamp NULL DEFAULT NULL,
  `original_p_what` text,
  `original_bobot` double DEFAULT NULL,
  `original_hasil` text,
  `original_nilai` double DEFAULT NULL,
  `original_total` double DEFAULT NULL,
  `original_target_omset` decimal(15,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_whats`
--

INSERT INTO `tb_whats` (`id_what`, `id_user`, `id_kpi`, `tipe_what`, `p_what`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`, `is_edited`, `edited_by`, `edited_at`, `original_p_what`, `original_bobot`, `original_hasil`, `original_nilai`, `original_total`, `original_target_omset`) VALUES
(14, 4, 5, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '0.00', '5 applikasi', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(15, 4, 9, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 100, 60, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(16, 4, 9, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(17, 4, 11, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(18, 4, 11, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'EXCELLENT	', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 4, 11, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 4, 11, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 4, 12, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 16, 13, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 70, '0.00', 'Deliver Order , Daily Stock  , Digital ICS , Stock Opname', 115, 80.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 16, 13, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 30, '0.00', 'Rata - Rata setiap applikasi 6 - 7 Fitur', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(24, 16, 14, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'Sesuai dengan target kelayakan pengguna', 110, 55, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(25, 16, 14, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'Dokumentasi apabila terjadi kesalahan / bug sistem', 110, 55, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(26, 16, 15, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'Tidak ada kendala pada penggunaan digital aplikasi', 115, 80.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(27, 16, 15, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'Semua kegagalan proses aplikasi digital dapat cepat terselesaikan', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(28, 1, 23, 'A', 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '0.00', '135 Konten', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 1, 24, 'A', 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '0.00', '100%', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 1, 24, 'A', 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '0.00', '100%', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 1, 25, 'A', 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV bisa merekam', 100, '0.00', '100%', 100, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(32, 1, 26, 'A', 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '0.00', '100% bisa', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(33, 1, 26, 'A', 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '0.00', '6 modul', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(34, 1, 27, 'A', '3 Skill standart IT ', 100, '0.00', '3,9', 100, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(37, 1, 28, 'A', 'Absensi', 100, '0.00', '130 dari HRD', 130, 130, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(39, 18, 18, 'A', 'Membuat project selama 1 tahun 300', 100, '0.00', '369 konten termasuk dengan live streaming', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(40, 18, 19, 'A', 'Peningkatan insight tayangan instagram selama satu tahun', 50, '0.00', '90 hari= 248.451Tayangan', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(44, 18, 19, 'A', 'Peningkatan insight interaksi instagram selama satu tahun', 35, '0.00', '90 hari= 3.523 interaksi', 115, 40.25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(45, 18, 19, 'A', 'Peningkatan insight follower TikTok & Instagram selama satu tahun', 15, '0.00', 'TOTAL = (TT) 642 + (IG) 432 = 1.074 Follower Tiktok : awal 533 update saat ini 1175 (+642) Instagram : awal 2.300 update saat ini 2732 (+432)', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(46, 18, 20, 'A', 'Peningkatan insight tayangan TikTok selama satu tahun', 40, '0.00', '365 hari = 140k Tayangan', 115, 46, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(47, 18, 20, 'A', 'Peningkatan insight Like TikTok selama satu tahun', 30, '0.00', '365 hari =1.954 Like', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(48, 18, 20, 'A', 'Peningkatan insight tampil profile TikTok selama satu tahun', 30, '0.00', '365 hari = 3.076 tampilan profil', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(49, 18, 21, 'A', 'Skill standart Content Creator', 100, '0.00', 'nilai = 3.81', 110, 110, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(50, 18, 22, 'A', 'Absensi', 100, '0.00', '', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(51, 17, 29, 'A', 'Berapa total omzet dari 01 januari sampai dengan Saat ini VS 10M', 100, '0.00', 'Rp.  2,313,025,590.08\r\ndari 10M \r\n22% ', 50, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(53, 16, 16, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(54, 16, 17, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(55, 17, 30, 'A', 'Penilaian toko perbulan', 50, '0.00', '4.9', 110, 55, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(56, 17, 30, 'A', 'Kesehatan toko', 25, '0.00', '10', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(57, 17, 30, 'A', 'Status penjualan', 25, '0.00', 'Star Plus ', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(58, 17, 31, 'A', 'Level toko 4', 50, '0.00', 'Level toko 3', 100, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(59, 17, 31, 'A', 'Parameter Score performa toko', 50, '0.00', '85 / 100', 95, 47.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(60, 17, 32, 'A', 'absen dari website hrd', 100, '0.00', '116', 116, 116, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(62, 22, 33, 'A', '95 SOP Prioritas yang telah disetujui oleh manajemen diaudit dalam 1 tahun ', 100, '0.00', '80%', 80, 80, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(64, 20, 34, 'A', '95 SOP Prioritas yang telah disetujui oleh manajemen diaudit dalam 1 tahun ', 100, '0.00', '57%', 50, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(65, 20, 35, 'A', '60% Karyawan Karisma mengumpulkan KPI;30;KPI Juni terkumpul 92%', 30, '0.00', '1 90-100% 115\n2 80% 110\r\n3 60% 100\r\n4 50% 90\r\n5 40% 60\r\n6 30% 50\r\n7 <30% 0', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(66, 20, 35, 'A', 'Audit Kualitas KPI minimal 3 KPI Karywan per minggu/ 12 KPI per bulan', 30, '0.00', 'Audit KPI All Karyawan', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(67, 20, 35, 'A', 'KPI All karyawan terisi dengan benar dan sesuai data', 40, '0.00', 'KPI 90% karyawan terisi dan sesuai data', 90, 36, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(68, 20, 36, 'A', 'Update SOP baru atau revisi maksimal H+3 setelah SOP di approve oleh Direktur', 40, '0.00', 'Rata-rata H+0 (Kertas Kerja di Luluk > SOP > Data SOP all)', 115, 46, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(69, 20, 36, 'A', '80% SOP All Departemen adalah SOP yang masih relevan', 60, '0.00', 'SOP GA dan HRD telah diperiksa = 24%', 50, 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(70, 20, 37, 'A', 'Absensi', 100, '0.00', '0 absen', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(71, 19, 38, 'A', 'Mencapai Target Absensi All Karyawan 99%', 60, '0.00', 'Isi luk', 120, 72, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(72, 19, 38, 'A', 'Mencapai Target Absensi All Karyawan 98,3% (Control)', 40, '0.00', 'Isi luk', 120, 48, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(73, 19, 39, 'A', 'Melaporkan Data absensi terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(74, 19, 39, 'A', 'Melaporkan Data BPJS terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(75, 19, 39, 'A', 'Melaporkan Data Laporan Karyawan Keluar-Masuk maksimal terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(76, 19, 39, 'A', 'Melaporkan Data Reward Hadir dan Pemotongan Gaji Karyawan terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(77, 19, 40, 'A', '100% kebutuhan permintaan karyawan di masing-masing departemen terpenuhi maksimal 2 bulan dan sesuai kriteria (Karyawan Kantor)', 50, '0.00', 'Isi luk', 110, 55, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(78, 19, 40, 'A', '100% kebutuhan permintaan karyawan di masing-masing departemen terpenuhi maksimal 3 bulan dan sesuai kriteria (Karyawan Lapangan)', 50, '0.00', 'Isi luk', 80, 40, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(79, 19, 41, 'A', 'Merapikan dan mengarsip semua dokumen maksimal H+10 bulan berikutnya;100', 100, '0.00', 'Isi luk', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(80, 19, 42, 'A', 'Absensi', 100, '0.00', '0 absen', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(81, 21, 43, 'A', '100 % Kendaraan Distribusi dalam kondisi PRIMA', 100, '0.00', '100% Kendaraan distribusi dalam kondisi prima', 100, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(82, 21, 44, 'A', '100% Gedung Terawat dan Nol Komplain', 50, '0.00', '90% gedung terawat dan 1 komplain', 50, 25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(83, 21, 44, 'A', '100% Inventaris Kantor Terawat, dan Nol Komplain', 50, '0.00', '90% inventaris terawat dan 1 komplain', 50, 25, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(84, 21, 45, 'A', 'Absensi', 100, '0.00', '0 absen', 115, 115, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(89, 28, 52, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'sesuai ', 115, 57.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(90, 28, 52, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'sesuai ', 100, 50, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(91, 28, 53, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'sesuai', 100, 70, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(92, 28, 53, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'sesuai', 100, 30, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(93, 28, 54, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(94, 28, 55, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(98, 29, 56, 'A', 'membuat kpi', 30, '0.00', 'oke', 115, 34.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(99, 31, 58, 'A', 'target omset november', 20, '0.00', 'okeee', 111, 22.2, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(100, 31, 58, 'B', 'target omset november', 80, '103000.00', ' | Hasil Tercapai: 70,352.00', 68.3, 68.3, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(101, 36, 59, 'A', 'NOL BARANG KEMBALI', 10, '0.00', '0', 115, 11.5, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(106, 24, 60, 'A', 'target rilis ', 20, '0.00', 'okee', 100, 20, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(108, 40, 61, 'A', 'Audit sop prioritas dalam 1 tahun', 20, '0.00', '100% SOP telah di audit sd <Oktober', 115, 23, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(109, 28, 51, 'A', 'mambuat aplikasi kpi versi 2.1.0', 10, '0.00', '4 aplikasi', 115, 11.5, 1, 4, '2026-01-18 05:07:17', 'mambuat aplikasi kpi versi 2.1', 10, '3 aplikasi', 100, 10, NULL),
(111, 40, 61, 'A', 'Audit sop prioritas dalam 1 tahun', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(112, 40, 61, 'A', 'Audit sop prioritas dalam 5 tahun', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(113, 40, 61, 'A', 'Audit sop prioritas dalam 3 tahun', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(114, 40, 61, 'A', 'Audit sop prioritas dalam 4 tahun', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(115, 40, 61, 'A', 'aplikasi', 10, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(117, 37, 65, 'A', 'PERAWATAN KENDARAAN', 100, '0.00', 'Kendaraan bersih', 100, 100, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(118, 28, 51, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(119, 28, 51, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 20, '0.00', '', 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

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
-- Indexes for table `tbsim_bobotkpi`
--
ALTER TABLE `tbsim_bobotkpi`
  ADD PRIMARY KEY (`idbobotkpi`);

--
-- Indexes for table `tbsim_hows`
--
ALTER TABLE `tbsim_hows`
  ADD PRIMARY KEY (`id_how`);

--
-- Indexes for table `tbsim_indikator_hows`
--
ALTER TABLE `tbsim_indikator_hows`
  ADD PRIMARY KEY (`id_indikator`),
  ADD KEY `idx_id_how` (`id_how`),
  ADD KEY `idx_urutan` (`urutan`);

--
-- Indexes for table `tbsim_indikator_whats`
--
ALTER TABLE `tbsim_indikator_whats`
  ADD PRIMARY KEY (`id_indikator`),
  ADD KEY `fk_indikator_whats` (`id_what`);

--
-- Indexes for table `tbsim_kpi`
--
ALTER TABLE `tbsim_kpi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbsim_whats`
--
ALTER TABLE `tbsim_whats`
  ADD PRIMARY KEY (`id_what`);

--
-- Indexes for table `tb_auth`
--
ALTER TABLE `tb_auth`
  ADD PRIMARY KEY (`id_auth`);

--
-- Indexes for table `tb_bobotkpi`
--
ALTER TABLE `tb_bobotkpi`
  ADD PRIMARY KEY (`idbobotkpi`);

--
-- Indexes for table `tb_eviden`
--
ALTER TABLE `tb_eviden`
  ADD PRIMARY KEY (`id`);

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
  ADD KEY `id_what` (`id_what`);

--
-- Indexes for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tb_kpi_history`
--
ALTER TABLE `tb_kpi_history`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_kpi_month` (`id_user`,`id_kpi`,`bulan`),
  ADD KEY `idx_summary` (`id_user`,`bulan`,`is_summary`);

--
-- Indexes for table `tb_kpi_verified`
--
ALTER TABLE `tb_kpi_verified`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_verified` (`id_user`,`bulan`),
  ADD KEY `verified_by` (`verified_by`);

--
-- Indexes for table `tb_sop`
--
ALTER TABLE `tb_sop`
  ADD PRIMARY KEY (`id_sop`);

--
-- Indexes for table `tb_ss`
--
ALTER TABLE `tb_ss`
  ADD PRIMARY KEY (`id_poinss`);

--
-- Indexes for table `tb_sspoin`
--
ALTER TABLE `tb_sspoin`
  ADD PRIMARY KEY (`id_sspoin`);

--
-- Indexes for table `tb_surat_peringatan`
--
ALTER TABLE `tb_surat_peringatan`
  ADD PRIMARY KEY (`id_sp`),
  ADD KEY `id_user` (`id_user`),
  ADD KEY `status` (`status`);

--
-- Indexes for table `tb_users`
--
ALTER TABLE `tb_users`
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

--
-- AUTO_INCREMENT for table `tbsim_bobotkpi`
--
ALTER TABLE `tbsim_bobotkpi`
  MODIFY `idbobotkpi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tbsim_hows`
--
ALTER TABLE `tbsim_hows`
  MODIFY `id_how` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tbsim_indikator_hows`
--
ALTER TABLE `tbsim_indikator_hows`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `tbsim_indikator_whats`
--
ALTER TABLE `tbsim_indikator_whats`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbsim_kpi`
--
ALTER TABLE `tbsim_kpi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `tbsim_whats`
--
ALTER TABLE `tbsim_whats`
  MODIFY `id_what` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tb_auth`
--
ALTER TABLE `tb_auth`
  MODIFY `id_auth` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `tb_bobotkpi`
--
ALTER TABLE `tb_bobotkpi`
  MODIFY `idbobotkpi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `tb_eviden`
--
ALTER TABLE `tb_eviden`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_hows`
--
ALTER TABLE `tb_hows`
  MODIFY `id_how` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `tb_indikator_hows`
--
ALTER TABLE `tb_indikator_hows`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tb_indikator_whats`
--
ALTER TABLE `tb_indikator_whats`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `tb_kpi_history`
--
ALTER TABLE `tb_kpi_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `tb_kpi_verified`
--
ALTER TABLE `tb_kpi_verified`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tb_sop`
--
ALTER TABLE `tb_sop`
  MODIFY `id_sop` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_ss`
--
ALTER TABLE `tb_ss`
  MODIFY `id_poinss` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_sspoin`
--
ALTER TABLE `tb_sspoin`
  MODIFY `id_sspoin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tb_surat_peringatan`
--
ALTER TABLE `tb_surat_peringatan`
  MODIFY `id_sp` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `tb_whats`
--
ALTER TABLE `tb_whats`
  MODIFY `id_what` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=120;

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
  ADD CONSTRAINT `tb_indikator_whats_ibfk_1` FOREIGN KEY (`id_what`) REFERENCES `tb_whats` (`id_what`) ON DELETE CASCADE;

--
-- Constraints for table `tb_kpi_verified`
--
ALTER TABLE `tb_kpi_verified`
  ADD CONSTRAINT `tb_kpi_verified_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id`),
  ADD CONSTRAINT `tb_kpi_verified_ibfk_2` FOREIGN KEY (`verified_by`) REFERENCES `tb_users` (`id`);

--
-- Constraints for table `tb_surat_peringatan`
--
ALTER TABLE `tb_surat_peringatan`
  ADD CONSTRAINT `fk_sp_user` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
