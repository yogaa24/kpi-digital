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
-- Database: `db_kpiarchive`
--

-- --------------------------------------------------------

--
-- Table structure for table `tbar_archive`
--

CREATE TABLE `tbar_archive` (
  `id_archive` int NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `id_user` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_archive`
--

INSERT INTO `tbar_archive` (`id_archive`, `bulan`, `id_user`) VALUES
(1, '10/2025', 4),
(2, '10/2025', 1),
(3, '11/2025', 4),
(9, '12/2025', 28);

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
(9, 28, 9, 60, 40);

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
  `total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_hows`
--

INSERT INTO `tbar_hows` (`id_how`, `id_user`, `id_kpi`, `tipe_how`, `p_how`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`) VALUES
(1, 4, 1, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40),
(2, 4, 1, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsbrewrytuyiuioprewrytuyiuioprewrytuyiuioprewrytuyiuiop', 55, '0.00', 'sesuai target', 115, 63),
(3, 4, 1, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5),
(4, 4, 2, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5),
(5, 4, 2, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5),
(6, 4, 3, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', '4', 115, 28.75),
(7, 4, 3, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', '3.9', 110, 28),
(8, 4, 3, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28),
(9, 4, 3, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25),
(10, 4, 4, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75),
(11, 4, 4, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5),
(12, 4, 4, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75),
(13, 1, 5, 'A', 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '0.00', '100% Terjadwal & di laporkan', 110, 110),
(14, 1, 6, 'A', 'Mendata inventaris karisma per departemen', 50, '0.00', '4 Dept', 100, 50),
(15, 1, 6, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '0.00', '100% Terjadwal & di laporkan', 110, 55),
(16, 1, 7, 'A', 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)rewrytuyiuioprewrytuyiuioprewrytuyiuiops', 100, '0.00', 'H+5 dari kerusakan', 90, 90),
(17, 1, 8, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '0.00', '100% Terjadwal & di laporkan & TTD', 115, 115),
(18, 1, 9, 'A', 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '0.00', '4', 115, 57.5),
(19, 1, 9, 'A', 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25),
(20, 1, 9, 'A', 'Menguasai Support Software penilaian oleh P.Bram', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25),
(21, 1, 10, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% Tepat Waktu', 115, 28.75),
(22, 1, 10, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '100% taat', 115, 28.75),
(23, 1, 10, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '100% hadir', 115, 28.75),
(24, 1, 10, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '100% hadir', 115, 28.75),
(25, 4, 11, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40),
(26, 4, 11, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, '0.00', 'sesuai target', 115, 63),
(27, 4, 11, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5),
(28, 4, 12, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5),
(29, 4, 12, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5),
(30, 4, 13, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', 'NILAI > 3.8', 110, 27.5),
(31, 4, 13, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', 'NILAI 4', 115, 28.75),
(32, 4, 13, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28),
(33, 4, 13, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25),
(34, 4, 14, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75),
(35, 4, 14, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5),
(36, 4, 14, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75),
(67, 28, 40, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan target', 20, '0.00', 'sesuai', 100, 20),
(68, 28, 40, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'sesuai', 100, 20),
(69, 28, 40, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', '-', 0, 0),
(70, 28, 40, 'A', 'Skill Standart IT (code programming)', 25, '0.00', '-', 0, 0),
(71, 28, 40, 'A', 'abcde', 20, '0.00', 'agak lumayan', 90, 18),
(72, 28, 41, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'sesuai', 100, 60),
(73, 28, 41, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', '-', 0, 0),
(74, 28, 42, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', '-', 0, 0),
(75, 28, 42, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', '-', 0, 0),
(76, 28, 43, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(77, 28, 43, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(78, 28, 43, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(79, 28, 43, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(80, 28, 44, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0),
(81, 28, 44, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0);

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
(40, 9, 28, 'Pembuatan Produk digital (4)', 40, 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', 40),
(41, 9, 28, 'Pemeliharaan sistem', 25, 'Produk digital dapat digunakan tanpa hambatan', 25),
(42, 9, 28, '0% Troubleshooting sistem', 15, 'Melakukan pengecekan berkala ', 15),
(43, 9, 28, 'Absensi', 10, 'Penilaian absensi oleh HRD', 10),
(44, 9, 28, 'Supporting maintenance hardware', 10, 'Membantu maintenance hardware', 10);

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
  `total` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbar_whats`
--

INSERT INTO `tbar_whats` (`id_what`, `id_user`, `id_kpi`, `tipe_what`, `p_what`, `bobot`, `target_omset`, `hasil`, `nilai`, `total`) VALUES
(1, 4, 1, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)rewrytuyiuioprewrytuyiuiop', 100, '0.00', '5 applikasi', 115, 115),
(2, 4, 2, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 90, 54),
(3, 4, 2, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46),
(4, 4, 3, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75),
(5, 4, 3, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'VERY GOOD', 100, 25),
(6, 4, 3, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75),
(7, 4, 3, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5),
(8, 4, 4, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120),
(9, 1, 5, 'A', 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '0.00', '135 Konten', 115, 115),
(10, 1, 6, 'A', 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '0.00', '100%', 115, 57.5),
(11, 1, 6, 'A', 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '0.00', '100%', 115, 57.5),
(12, 1, 7, 'A', 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV Hidup Semua', 100, '0.00', '60%', 90, 90),
(13, 1, 8, 'A', 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '0.00', '100% bisa', 115, 57.5),
(14, 1, 8, 'A', 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '0.00', '5 modul', 100, 50),
(15, 1, 9, 'A', '3 Skill standart IT ', 100, '0.00', '3,9', 100, 100),
(16, 1, 10, 'A', 'Absensi', 100, '0.00', '130 dari HRD', 130, 130),
(17, 4, 11, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '0.00', '5 applikasi', 115, 115),
(18, 4, 12, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 100, 60),
(19, 4, 12, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46),
(20, 4, 13, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75),
(21, 4, 13, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'EXCELLENT	', 115, 28.75),
(22, 4, 13, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75),
(23, 4, 13, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5),
(24, 4, 14, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120),
(47, 28, 40, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 20, '0.00', '-\r\n', 100, 20),
(48, 28, 40, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 30, '0.00', 'ok', 1, 0.3),
(49, 28, 40, 'A', 'membuat aplikasi zahir', 50, '0.00', 'aplikasi masih 90%', 100, 50),
(50, 28, 40, 'A', 'plafon', 50, '0.00', '> 4 aplikasi', 100, 50),
(51, 28, 40, 'B', 'Target All Omset', 50, '103000.00', ' Hasil Tercapai: 100,000.00', 97.09, 48.55),
(52, 28, 41, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'sesuai ', 115, 57.5),
(53, 28, 41, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'sesuai ', 100, 50),
(54, 28, 42, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'sesuai', 100, 70),
(55, 28, 42, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'sesuai', 100, 30),
(56, 28, 43, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122),
(57, 28, 44, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbar_archive`
--
ALTER TABLE `tbar_archive`
  ADD PRIMARY KEY (`id_archive`);

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
  MODIFY `id_archive` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbar_bobotkpi`
--
ALTER TABLE `tbar_bobotkpi`
  MODIFY `idbobotkpi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tbar_hows`
--
ALTER TABLE `tbar_hows`
  MODIFY `id_how` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT for table `tbar_kpi`
--
ALTER TABLE `tbar_kpi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `tbar_whats`
--
ALTER TABLE `tbar_whats`
  MODIFY `id_what` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
