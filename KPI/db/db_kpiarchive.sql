-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 16, 2025 at 05:48 AM
-- Server version: 10.4.17-MariaDB
-- PHP Version: 7.2.34

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
  `id_archive` int(11) NOT NULL,
  `bulan` varchar(255) NOT NULL,
  `id_user` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbar_archive`
--

INSERT INTO `tbar_archive` (`id_archive`, `bulan`, `id_user`) VALUES
(1, '10/2025', 4),
(2, '10/2025', 1),
(3, '11/2025', 4);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_bobotkpi`
--

CREATE TABLE `tbar_bobotkpi` (
  `idbobotkpi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_arcv` int(11) NOT NULL,
  `bobotwhat` int(11) NOT NULL,
  `bobothow` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbar_bobotkpi`
--

INSERT INTO `tbar_bobotkpi` (`idbobotkpi`, `id_user`, `id_arcv`, `bobotwhat`, `bobothow`) VALUES
(1, 4, 1, 60, 40),
(2, 1, 2, 60, 40),
(3, 4, 3, 60, 40);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_hows`
--

CREATE TABLE `tbar_hows` (
  `id_how` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kpi` int(11) NOT NULL,
  `p_how` text NOT NULL,
  `bobot` double NOT NULL,
  `hasil` text NOT NULL,
  `nilai` double NOT NULL,
  `total` double NOT NULL,
  `indikatorhow` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbar_hows`
--

INSERT INTO `tbar_hows` (`id_how`, `id_user`, `id_kpi`, `p_how`, `bobot`, `hasil`, `nilai`, `total`, `indikatorhow`) VALUES
(1, 4, 1, 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '5 applikasi', 115, 40, '5 APPLIKASI	115\r\n4 APPLIKASI	110\r\n3 APPLIKASI	105\r\n2 APPLIKASI	100\r\n1 APPLIKASI	95\r\n'),
(2, 4, 1, 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsbrewrytuyiuioprewrytuyiuioprewrytuyiuioprewrytuyiuiop', 55, 'sesuai target', 115, 63, '0 Target Lolos	115\r\n2 Target Lolos	110\r\n3 Target Lolos	105\r\n4 Target Lolos	100\r\n5 Target Lolos	98\r\n'),
(3, 4, 1, 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, 'Rapi & bersih', 115, 11.5, 'Rapi & Bersih	115\r\nRapi	105\r\nBersih	100\r\nTdk rapi & tdk Bersih	50\r\n'),
(4, 4, 2, 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5, '100% dilaporkan & timelinenya & ada nextstepnya | 115\r\n100% dilaporkan & timelinenya | 100\r\n100% dilaporkan | 90\r\n90% dilaporkan | 80\r\n80% dilaporkan | 80'),
(5, 4, 2, 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '100 % dan 0 keluhan', 115, 57.5, '0	Keluhan	115\r\n1	Keluhan	110\r\n2	Keluhan	100\r\n3	Keluhan	95\r\n4	Keluhan	90\r\n5	Keluhan	80'),
(6, 4, 3, 'Nilai SS PIC Software Digital', 25, '4', 115, 28.75, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(7, 4, 3, 'Nilai SS PIC Hardware Digital', 25, '3.9', 110, 28, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(8, 4, 3, 'Nilai SS PIC Content Creator', 25, '3.8', 110, 28, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(9, 4, 3, 'Nilai SS PIC Digital Marketing', 25, '3.5', 100, 25, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(10, 4, 4, 'Hadir Briefing Tepat Waktu', 25, '100% mengikuti breafing', 115, 28.75, '100% Tepat Waktu	115\r\n1 - 2 X terlambat	100\r\n3 - 4 X terlambat	90\r\n>4 X terlambat	50\r\n'),
(11, 4, 4, 'Menjalankan SOP tentang absensi', 50, '100 % sop tidak ada yang di langgar', 115, 57.5, '100% taat 115\r\n90% taat 100\r\n80% taat 90\r\n70% taat 80'),
(12, 4, 4, 'Senam pagi', 25, '100% mengikuti senam pagi', 115, 28.75, '100% hadir	115\r\n< 2X tdk hadir dg ijin	100\r\n1-2 X tdk hadir	90\r\n3-4 X tdk hadir	80\r\n> 4X tdk hadir	0'),
(13, 1, 5, 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '100% Terjadwal & di laporkan', 110, 110, '1	100% Terjadwal & di laporkan & (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(14, 1, 6, 'Mendata inventaris karisma per departemen', 50, '4 Dept', 100, 50, '1	4 Departement & direksi	115\r\n2	4 Departement	100\r\n3	3 Departement	80\r\n4	2 Departement	70\r\n5	1 Departement	60\r\n6	<1 Departement	0'),
(15, 1, 6, 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '100% Terjadwal & di laporkan', 110, 55, '1	100% Terjadwal & di laporkan & (ttd kabag)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(16, 1, 7, 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)rewrytuyiuioprewrytuyiuioprewrytuyiuiops', 100, 'H+5 dari kerusakan', 90, 90, '1	< H+3 dari kerusakan	115\r\n2	H+3 dari kerusakan	100\r\n3	H+5 dari kerusakan	90\r\n4	H+7 dari kerusakan	80\r\n5	>H+7 	0'),
(17, 1, 8, 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '100% Terjadwal & di laporkan & TTD', 115, 115, '1	100% Terjadwal & di laporkan & (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(18, 1, 9, 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '4', 115, 57.5, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0\r\n'),
(19, 1, 9, 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, 'nilai skill 3.5 - 3.9', 100, 25, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(20, 1, 9, 'Menguasai Support Software penilaian oleh P.Bram', 25, 'nilai skill 3.5 - 3.9', 100, 25, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(21, 1, 10, 'Hadir Briefing Tepat Waktu', 25, '100% Tepat Waktu', 115, 28.75, '1	100% Tepat Waktu	115\r\n2	1 - 2 X terlambat	100\r\n3	3 - 4 X terlambat	90\r\n4	>4 X terlambat	50'),
(22, 1, 10, 'Menjalankan SOP Ijin tidak masuk', 25, '100% taat', 115, 28.75, '1	100% taat	115\r\n2	ada pelanggaran	0'),
(23, 1, 10, 'Tidak pernah absen Briefing', 25, '100% hadir', 115, 28.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(24, 1, 10, 'Tidak pernah absen Senam sabtu', 25, '100% hadir', 115, 28.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(25, 4, 11, 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '5 applikasi', 115, 40, '5 APPLIKASI	115\r\n4 APPLIKASI	110\r\n3 APPLIKASI	105\r\n2 APPLIKASI	100\r\n1 APPLIKASI	95\r\n'),
(26, 4, 11, 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, 'sesuai target', 115, 63, '0 Target Lolos	115\r\n2 Target Lolos	110\r\n3 Target Lolos	105\r\n4 Target Lolos	100\r\n5 Target Lolos	98\r\n'),
(27, 4, 11, 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, 'Rapi & bersih', 115, 11.5, 'Rapi & Bersih	115\r\nRapi	105\r\nBersih	100\r\nTdk rapi & tdk Bersih	50\r\n'),
(28, 4, 12, 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5, '100% dilaporkan & timelinenya & ada nextstepnya | 115\r\n100% dilaporkan & timelinenya | 100\r\n100% dilaporkan | 90\r\n90% dilaporkan | 80\r\n80% dilaporkan | 80'),
(29, 4, 12, 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '100 % dan 0 keluhan', 115, 57.5, '0	Keluhan	115\r\n1	Keluhan	110\r\n2	Keluhan	100\r\n3	Keluhan	95\r\n4	Keluhan	90\r\n5	Keluhan	80'),
(30, 4, 13, 'Nilai SS PIC Software Digital', 25, 'NILAI > 3.8', 110, 27.5, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(31, 4, 13, 'Nilai SS PIC Hardware Digital', 25, 'NILAI 4', 115, 28.75, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(32, 4, 13, 'Nilai SS PIC Content Creator', 25, '3.8', 110, 28, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(33, 4, 13, 'Nilai SS PIC Digital Marketing', 25, '3.5', 100, 25, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(34, 4, 14, 'Hadir Briefing Tepat Waktu', 25, '100% mengikuti breafing', 115, 28.75, '100% Tepat Waktu	115\r\n1 - 2 X terlambat	100\r\n3 - 4 X terlambat	90\r\n>4 X terlambat	50\r\n'),
(35, 4, 14, 'Menjalankan SOP tentang absensi', 50, '100 % sop tidak ada yang di langgar', 115, 57.5, '100% taat 115\r\n90% taat 100\r\n80% taat 90\r\n70% taat 80'),
(36, 4, 14, 'Senam pagi', 25, '100% mengikuti senam pagi', 115, 28.75, '100% hadir	115\r\n< 2X tdk hadir dg ijin	100\r\n1-2 X tdk hadir	90\r\n3-4 X tdk hadir	80\r\n> 4X tdk hadir	0');

-- --------------------------------------------------------

--
-- Table structure for table `tbar_kpi`
--

CREATE TABLE `tbar_kpi` (
  `id` int(11) NOT NULL,
  `id_arcv` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `poin` text NOT NULL,
  `bobot` double NOT NULL,
  `poin2` text NOT NULL,
  `bobot2` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(14, 3, 4, 'Absensi', 10, 'Absensi', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tbar_whats`
--

CREATE TABLE `tbar_whats` (
  `id_what` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `id_kpi` int(11) NOT NULL,
  `p_what` text NOT NULL,
  `bobot` double NOT NULL,
  `hasil` text NOT NULL,
  `nilai` double NOT NULL,
  `total` double NOT NULL,
  `indikatorwhat` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tbar_whats`
--

INSERT INTO `tbar_whats` (`id_what`, `id_user`, `id_kpi`, `p_what`, `bobot`, `hasil`, `nilai`, `total`, `indikatorwhat`) VALUES
(1, 4, 1, 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)rewrytuyiuioprewrytuyiuiop', 100, '5 applikasi', 115, 115, '5 APPLIKASI	115\r\n4 APPLIKASI	110\r\n3 APPLIKASI	105\r\n2 APPLIKASI	100\r\n1 APPLIKASI	95'),
(2, 4, 2, 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, 'beberapa tidak sesusai dengan target', 90, 54, '100% selesai tepat waktu & dibawah target yang sudah di sepakati | 115\r\n100% selesai tepat waktu | 100\r\n90 % selesai tepat waktu | 90\r\n80 % selesai tepat waktu | 80\r\n70 % selesai tepat waktu | 70\r\n'),
(3, 4, 2, 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '100%', 115, 46, '100% bisa di applikasi dan digunakan tanpa bug | 115\r\n100% bisa di applikasi dan masih ada bug | 100\r\n90& bisa di applikasi  | 90\r\n80& bisa di applikasi  | 80\r\n70& bisa di applikasi  | 70'),
(4, 4, 3, 'Nilai KPI PIC Software Digital', 25, 'Excellent', 115, 28.75, '1	EXCELENT	115\r\n2	VERY GOOD	100\r\n3	GOOD	80\r\n4	POOR	50'),
(5, 4, 3, 'Nilai KPI PIC Hardware Digital', 25, 'VERY GOOD', 100, 25, 'EXCELLENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(6, 4, 3, 'Nilai KPI PIC Content Creator', 25, 'Excelent', 115, 28.75, 'EXCELENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(7, 4, 3, 'Nilai KPI PIC Digital Marketing', 25, 'Poor', 50, 12.5, 'EXCELENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(8, 4, 4, 'NILAI ABSENSI ', 100, '99.5', 120, 120, 'SESUAI DATA DARI HRD'),
(9, 1, 5, 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '135 Konten', 115, 115, '1	> 120 konten & livestream	115\r\n2	100 Konten & livestream	110\r\n3	80 Konten & livestream	100\r\n4	60 Konten & livestream	90\r\n5	40 Konten & livestream	80\r\n6	10 Konten & livestream	70\r\n7	<10 konten & livestream	0'),
(10, 1, 6, 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '100%', 115, 57.5, '1	100%	115\r\n2	95%	110\r\n3	90%	100\r\n4	85%	95\r\n5	80%	80\r\n6	75%	75\r\n7	70%	50\r\n8	<70%	0'),
(11, 1, 6, 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '100%', 115, 57.5, '1	100%	115\r\n2	95%	110\r\n3	90%	100\r\n4	85%	95\r\n5	80%	80\r\n6	75%	75\r\n7	70%	50\r\n8	<70%	0'),
(12, 1, 7, 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV Hidup Semua', 100, '60%', 90, 90, '1	100% sesuai target	115\r\n2	90% - 100%	100\r\n3	50% - 90%	90\r\n4	25 - 50%	80\r\n5	1	0'),
(13, 1, 8, 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '100% bisa', 115, 57.5, '1	100% sesuai target	115\r\n2	90% - 99%	110\r\n3	80% - 90%	100\r\n4	70% - 80%	90\r\n5	60% - 70%	80\r\n6	50% - 60%	50\r\n7	<50%	0'),
(14, 1, 8, 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '5 modul', 100, 50, '1	>5 fasilitas / modul (applikasi)	115\r\n2	5 fasilitas / modul (applikasi)	100\r\n3	4 fasilitas / modul (applikasi)	90\r\n4	3 fasilitas / modul (applikasi)	80\r\n5	2 fasilitas / modul (applikasi)	70\r\n6	1 fasilitas / modul (applikasi)	50\r\n7	0 fasilitas / modul (applikasi)	0'),
(15, 1, 9, '3 Skill standart IT ', 100, '3,9', 100, 100, '1	nilai skill standart 4	115\r\n2	nilai skill standart 3.5 - 3.9	100\r\n3	nilai skill standart 3 - 3.4	90\r\n4	nilai skill standart < 3	80\r\n5	nilai skill standart <1	0'),
(16, 1, 10, 'Absensi', 100, '130 dari HRD', 130, 130, '10		115\r\n9		110\r\n8		100\r\n7		75\r\n6		50\r\n5		25\r\n4		0\r\n3		125\r\n2		130\r\n1		135'),
(17, 4, 11, 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '5 applikasi', 115, 115, '5 APPLIKASI	115\r\n4 APPLIKASI	110\r\n3 APPLIKASI	105\r\n2 APPLIKASI	100\r\n1 APPLIKASI	95'),
(18, 4, 12, 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, 'beberapa tidak sesusai dengan target', 100, 60, '100% selesai tepat waktu & dibawah target yang sudah di sepakati | 115\r\n100% selesai tepat waktu | 100\r\n90 % selesai tepat waktu | 90\r\n80 % selesai tepat waktu | 80\r\n70 % selesai tepat waktu | 70\r\n'),
(19, 4, 12, 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '100%', 115, 46, '100% bisa di applikasi dan digunakan tanpa bug | 115\r\n100% bisa di applikasi dan masih ada bug | 100\r\n90& bisa di applikasi  | 90\r\n80& bisa di applikasi  | 80\r\n70& bisa di applikasi  | 70'),
(20, 4, 13, 'Nilai KPI PIC Software Digital', 25, 'Excellent', 115, 28.75, '1	EXCELENT	115\r\n2	VERY GOOD	100\r\n3	GOOD	80\r\n4	POOR	50'),
(21, 4, 13, 'Nilai KPI PIC Hardware Digital', 25, 'EXCELLENT	', 115, 28.75, 'EXCELLENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(22, 4, 13, 'Nilai KPI PIC Content Creator', 25, 'Excelent', 115, 28.75, 'EXCELENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(23, 4, 13, 'Nilai KPI PIC Digital Marketing', 25, 'Poor', 50, 12.5, 'EXCELENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(24, 4, 14, 'NILAI ABSENSI ', 100, '99.5', 120, 120, 'SESUAI DATA DARI HRD');

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
  MODIFY `id_archive` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbar_bobotkpi`
--
ALTER TABLE `tbar_bobotkpi`
  MODIFY `idbobotkpi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbar_hows`
--
ALTER TABLE `tbar_hows`
  MODIFY `id_how` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `tbar_kpi`
--
ALTER TABLE `tbar_kpi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbar_whats`
--
ALTER TABLE `tbar_whats`
  MODIFY `id_what` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
