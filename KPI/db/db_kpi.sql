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
-- Database: `db_kpi`
--

-- --------------------------------------------------------

--
-- Table structure for table `tb_auth`
--

CREATE TABLE `tb_auth` (
  `id_auth` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(16, 24, '123', 1),
(17, 25, '123', 1),
(18, 26, '123', 1),
(19, 27, '123', 1),
(20, 28, '123', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tb_bobotkpi`
--

CREATE TABLE `tb_bobotkpi` (
  `idbobotkpi` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `bobotwhat` int(11) NOT NULL,
  `bobothow` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(11, 24, 0, 0),
(12, 25, 0, 0),
(13, 26, 0, 0),
(14, 27, 0, 0),
(15, 28, 60, 40);

-- --------------------------------------------------------

--
-- Table structure for table `tb_eviden`
--

CREATE TABLE `tb_eviden` (
  `id` int(11) NOT NULL,
  `id_user` int(12) NOT NULL,
  `nama_eviden` varchar(255) NOT NULL,
  `namafoto` varchar(200) NOT NULL,
  `keterangan` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_eviden`
--

INSERT INTO `tb_eviden` (`id`, `id_user`, `nama_eviden`, `namafoto`, `keterangan`) VALUES
(3, 4, 'Data Dummy', 'WhatsApp Image 2025-10-29 at 15.20.14.jpeg', 'Data Dummy tidak bisa digunakan'),
(4, 4, 'Data Dummy 1', 'Quotation - Karisma Indoagro Universal, PT (1).pdf', 'Data Dummy tidak bisa digunakan'),
(5, 4, 'Data Dummy 2', '1 NOVEMBER 2025 PEMBAYARAN.xlsx', 'Data dummy '),
(6, 4, 'test', 'Semua pesanan-2025-11-01-12_50.csv', 'test'),
(7, 4, 'oke', 'nota2.jpg', 'iya');

-- --------------------------------------------------------

--
-- Table structure for table `tb_hows`
--

CREATE TABLE `tb_hows` (
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
-- Dumping data for table `tb_hows`
--

INSERT INTO `tb_hows` (`id_how`, `id_user`, `id_kpi`, `p_how`, `bobot`, `hasil`, `nilai`, `total`, `indikatorhow`) VALUES
(14, 4, 5, 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '5 applikasi', 115, 40, '5 APPLIKASI	115\r\n4 APPLIKASI	110\r\n3 APPLIKASI	105\r\n2 APPLIKASI	100\r\n1 APPLIKASI	95\r\n'),
(15, 4, 9, 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5, '100% dilaporkan & timelinenya & ada nextstepnya | 115\r\n100% dilaporkan & timelinenya | 100\r\n100% dilaporkan | 90\r\n90% dilaporkan | 80\r\n80% dilaporkan | 80'),
(16, 4, 9, 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '100 % dan 0 keluhan', 115, 57.5, '0	Keluhan	115\r\n1	Keluhan	110\r\n2	Keluhan	100\r\n3	Keluhan	95\r\n4	Keluhan	90\r\n5	Keluhan	80'),
(17, 4, 5, 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, 'sesuai target', 115, 63, '0 Target Lolos	115\r\n2 Target Lolos	110\r\n3 Target Lolos	105\r\n4 Target Lolos	100\r\n5 Target Lolos	98\r\n'),
(18, 4, 5, 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, 'Rapi & bersih', 115, 11.5, 'Rapi & Bersih	115\r\nRapi	105\r\nBersih	100\r\nTdk rapi & tdk Bersih	50\r\n'),
(19, 4, 11, 'Nilai SS PIC Software Digital', 25, 'NILAI > 3.8', 110, 27.5, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(20, 4, 11, 'Nilai SS PIC Hardware Digital', 25, 'NILAI 4', 115, 28.75, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(21, 4, 11, 'Nilai SS PIC Content Creator', 25, '3.8', 110, 28, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(22, 4, 11, 'Nilai SS PIC Digital Marketing', 25, '3.5', 100, 25, 'NILAI 4	115\r\nNILAI > 3.8	110\r\nNILAI > 3.5	105\r\nNILAI 3.5	100\r\nNILAI 3	90\r\nNILAI 2.5	80\r\nNILAI 2	70\r\nNILAI 1	80\r\nNILAI <1	0\r\n'),
(23, 4, 12, 'Hadir Briefing Tepat Waktu', 25, '100% mengikuti breafing', 115, 28.75, '100% Tepat Waktu	115\r\n1 - 2 X terlambat	100\r\n3 - 4 X terlambat	90\r\n>4 X terlambat	50\r\n'),
(24, 4, 12, 'Menjalankan SOP tentang absensi', 50, '100 % sop tidak ada yang di langgar', 115, 57.5, '100% taat 115\r\n90% taat 100\r\n80% taat 90\r\n70% taat 80'),
(25, 4, 12, 'Senam pagi', 25, '100% mengikuti senam pagi', 115, 28.75, '100% hadir	115\r\n< 2X tdk hadir dg ijin	100\r\n1-2 X tdk hadir	90\r\n3-4 X tdk hadir	80\r\n> 4X tdk hadir	0'),
(26, 16, 13, 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan akhir bulan', 20, 'Rata - Rata sesuai dengan target pengerjaan', 100, 20, '1	H+0	115\r\n2	H+1	100\r\n3	H+2	90\r\n4	H+3	50\r\n5	>H+3	0'),
(27, 16, 13, 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, 'Report Progress dan done', 110, 22, '1	<H+2	115\r\n2	H+2	110\r\n3	H+4	105\r\n4	H+5	100\r\n5	H+6	90\r\n6	H+7	80\r\n7	>H+7	0'),
(28, 16, 14, 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, 'Terdokumentasi', 115, 69, '1	<H+2	115\r\n2	H+3	110\r\n3	H+4	100\r\n4	H+5	90\r\n5	H+6	80\r\n6	H+7	70\r\n7	>H+7	50'),
(29, 16, 14, 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, 'Telah terdokumentasi ', 115, 46, '1	<H+2	115\r\n2	H+3	110\r\n3	H+4	100\r\n4	H+5	90\r\n5	H+6	80\r\n6	H+7	70\r\n7	>H+7	50'),
(30, 16, 15, 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, 'Dapat deselesaikan sesuai dengan target', 115, 80.5, '1	< H+2	115\r\n2	H+2	100\r\n3	H+3	85\r\n4	H+4	50\r\n5	> H+4	0'),
(31, 16, 15, 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, 'Telah terdokumentasi', 115, 34.5, '1	<H+2	115\r\n2	H+2	100\r\n3	H+4	85\r\n4	H+5	50\r\n5	H+6	20'),
(32, 1, 23, 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '100% Terjadwal & di laporkan', 110, 110, '1	100% Terjadwal & di laporkan & (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(33, 1, 24, 'Mendata inventaris karisma per departemen', 50, '4 Dept', 100, 50, '1	4 Departement & direksi	115\r\n2	4 Departement	100\r\n3	3 Departement	80\r\n4	2 Departement	70\r\n5	1 Departement	60\r\n6	<1 Departement	0'),
(34, 1, 24, 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '100% Terjadwal & di laporkan', 110, 55, '1	100% Terjadwal & di laporkan & (ttd kabag)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(35, 1, 25, 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)', 100, 'H+2 dari kerusakan', 115, 115, '1	< H+3 dari kerusakan	115\r\n2	H+3 dari kerusakan	100\r\n3	H+5 dari kerusakan	90\r\n4	H+7 dari kerusakan	80\r\n5	>H+7 	0'),
(36, 1, 26, 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '100% Terjadwal & di laporkan & TTD', 115, 115, '1	100% Terjadwal & di laporkan & (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(37, 1, 27, 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '4', 115, 57.5, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0\r\n'),
(38, 1, 27, 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, 'nilai skill 3.5 - 3.9', 100, 25, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(39, 1, 27, 'Menguasai Support Software penilaian oleh P.Bram', 25, 'nilai skill 3.5 - 3.9', 100, 25, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(40, 1, 28, 'Hadir Briefing Tepat Waktu', 25, '100% Tepat Waktu', 115, 28.75, '1	100% Tepat Waktu	115\r\n2	1 - 2 X terlambat	100\r\n3	3 - 4 X terlambat	90\r\n4	>4 X terlambat	50'),
(41, 1, 28, 'Menjalankan SOP Ijin tidak masuk', 25, '100% taat', 115, 28.75, '1	100% taat	115\r\n2	ada pelanggaran	0'),
(42, 1, 28, 'Tidak pernah absen Briefing', 25, '100% hadir', 115, 28.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(43, 1, 28, 'Tidak pernah absen Senam sabtu', 25, '100% hadir', 115, 28.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(44, 18, 18, 'Reset informasi produk untuk dijadikan bahan dalam konsep dan script konten. Kemudian Membuat timeline planning project', 50, 'Reset & pembuatan skrip 2 episode Podcast', 115, 57.5, '1	100% Terjadwal & di laporkan & (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0'),
(45, 18, 18, 'Membuat konten video untuk sosial media TikTok, Reels, dan Story Instagram', 50, '', 115, 57.5, '1	100% Terjadwal & di laporkan & (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0\r\n'),
(46, 18, 19, 'Rutin untuk upload konten-konten desain maupun video produk Karisma dan non produk dengan tujuan memperkenalkan Karisma 20 postingan setiap bulan', 20, 'Post:40', 115, 23, '1	>30 postingan sebulan	115\r\n2	20 postingan sebulan	100\r\n3	15 postingan sebulan	90\r\n4	10 postingan sebulan	80\r\n5	5 postingan sebulan	0\r\n'),
(47, 18, 19, 'Request work task dari pihak atau tim lain untuk keperluan konten video maupun konten desain', 20, 'Request November :\r\n- Request dari tim Sales : 4 Konten selesai\r\n- Request dari sales Online : 2 Konten selesai, 2 konten on progress', 100, 20, '1	100% sesuai target	115\r\n2	90% - 100%	100\r\n3	50% - 90%	90\r\n4	25 - 50%	80\r\n5	1	0\r\n'),
(48, 18, 19, 'Menyelesaikan design konten hari hari penting Nasional & Internasional selama tiga bulan kedepan', 30, 'September : 3 Hari Penting\r\nOktober : 4 Hari penting\r\nNovember : 4 Hari Penting', 115, 34.5, '1	100% sesuai target	115\r\n2	90% - 100%	100\r\n3	50% - 90%	90\r\n4	25 - 50%	80\r\n5	1	0\r\n'),
(49, 18, 19, 'Melakukan live streaming Tiktok & Shopee', 30, 'Bulan november telah live 17 kali (Kamis (2x tiktok & Shopee) , Jumat (Shopee) & Sabtu(Tiktok))', 115, 34.5, '1	100% sesuai target	115\r\n2	90% - 100%	100\r\n3	50% - 90%	90\r\n4	25 - 50%	80\r\n5	1	0\r\n'),
(50, 18, 20, 'Upload Konten-konten desain maupun video produk Karisma dan non produk dengan tujuan memperkenalkan Karisma', 50, 'Post:40', 115, 57.5, '1	100% Terjadwal & di laporkan (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0\r\n'),
(51, 18, 20, 'Request work task dari pihak atau tim lain untuk keperluan konten video maupun konten desain', 50, 'Request November :\r\n- Request dari tim Sales : 4 Konten selesai\r\n- Request dari sales Online : 2 Konten selesai, 2 konten on progress', 110, 55, '1	100% Terjadwal & di laporkan (ttd)	115\r\n2	100% Terjadwal & di laporkan	110\r\n3	100% Terjadwal 	100\r\n4	80% Terjadwal	95\r\n5	70% Terjadwal	80\r\n6	60% Terjadwal 	75\r\n7	50 % Terjadwal	50\r\n8	< 50% terjadwal	0\r\n'),
(52, 18, 21, 'Menguasai Software Editing  penilaian oleh P.Wahyu', 50, '', 100, 50, '1	nilai skill 4	115\r\n2	nilai skill 3.9	112\r\n3	nilai skill 3.8	110\r\n4	nilai skill  3.7	105\r\n5	nilai skill  3.6	103\r\n6	nilai skill  3.5	100\r\n7	nilai skill  3.4	97\r\n8	nilai skill  3.3	94\r\n9	nilai skill  3.2	91\r\n10	nilai skill  3.1	50\r\n'),
(53, 18, 21, 'Menguasai Copywriting dan Content Writing oleh P.Wahyu', 25, '', 112, 28, '1	nilai skill 4	115\r\n2	nilai skill 3.9	112\r\n3	nilai skill 3.8	110\r\n4	nilai skill  3.7	105\r\n5	nilai skill  3.6	103\r\n6	nilai skill  3.5	100\r\n7	nilai skill  3.4	97\r\n8	nilai skill  3.3	94\r\n9	nilai skill  3.2	91\r\n10	nilai skill  3.1	50\r\n'),
(54, 18, 21, 'Menguasai Fotografi & Videografi penilaian oleh P.Wahyu', 25, '', 115, 28.75, '1	nilai skill 4	115\r\n2	nilai skill 3.9	112\r\n3	nilai skill 3.8	110\r\n4	nilai skill  3.7	105\r\n5	nilai skill  3.6	103\r\n6	nilai skill  3.5	100\r\n7	nilai skill  3.4	97\r\n8	nilai skill  3.3	94\r\n9	nilai skill  3.2	91\r\n10	nilai skill  3.1	50\r\n'),
(55, 18, 22, 'Hadir Briefing Tepat Waktu', 25, '', 115, 28.75, '1	100% Tepat Waktu	115\r\n2	1 - 2 X terlambat	100\r\n3	3 - 4 X terlambat	90\r\n4	>4 X terlambat	50\r\n'),
(56, 18, 22, 'Menjalankan SOP Ijin tidak masuk', 25, '', 115, 28.75, '1	100% taat	115\r\n2	ada pelanggaran	0\r\n'),
(57, 18, 22, 'Tidak pernah absen Briefing', 25, '', 115, 28.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0\r\n'),
(58, 18, 22, 'Tidak pernah absen Senam sabtu', 25, '', 115, 28.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0\r\n'),
(59, 17, 29, 'Setiap bulan harus capai Rp 833.333.333', 50, ' 136,574,349.00\r\n/ 833.333.333 * 100 = 16.39%', 50, 25, '1	>110%	115\r\n2	>100-110	110\r\n3	>90-100%	100\r\n4	>80-90%	85\r\n5	>70-80%	70\r\n6	>60-70%	60\r\n7	<60%	50'),
(61, 17, 29, 'Posting flayer dan konten yang sedang trend\r\n 280 perbulan di sosmed\r\n', 20, '330', 115, 23, '> 200    115% \r\n150    100%\r\n100     90%\r\n90       80%\r\n80       70%\r\n70       60%\r\n60       50%\r\n50        40%\r\n40         30%\r\n30        20%\r\n>20         10%\r\n  '),
(63, 16, 16, 'Hadir Briefing Tepat Waktu', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% Tepat Waktu	115\r\n2	1 - 2 X terlambat	100\r\n3	3 - 4 X terlambat	90\r\n4	>4 X terlambat	50'),
(64, 16, 16, 'Menjalankan SOP Ijin tidak masuk', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% taat	115\r\n2	ada pelanggaran	0'),
(65, 16, 16, 'Tidak pernah absen Briefing', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(66, 16, 16, 'Tidak pernah absen Senam sabtu', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(67, 16, 17, 'Perbaikan Maintance tanpa kesalahan', 50, 'Menyesuaikan dengan Skill Standart', 100, 50, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(68, 16, 17, 'Penilaian perkejaan hardware', 50, 'Menyesuaikan dengan Skill Standart', 100, 50, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(69, 16, 13, 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, 'Disampaikan dalam Management Meeting', 115, 40.25, '1	<H+2	115\r\n2	H+3	110\r\n3	H+4	105\r\n4	H+5	100\r\n5	H+6	90\r\n6	H+7	80'),
(70, 16, 13, 'Skill Standart IT (code programming) 4', 25, 'Menyesuaikan dengan penilaian management', 112, 28, '10	100%	115\r\n9	97% - 99%	112\r\n8	94% - 96%	110\r\n7	90% - 93%	100\r\n6	85% - 89%	90\r\n5	50 %- 84%	80\r\n4	<50%	0'),
(71, 17, 30, 'Barang yg dikirim sesuai dengan pesanan dan packingan aman sampai tujuan serta tidak terjadi keterlambatan pengiriman', 25, '100%', 115, 28.75, '100%	115\r\n95%	110\r\n90%	100\r\n85%	90\r\n80%	80\r\n70%	70\r\n60%	60\r\n50%	50\r\n'),
(72, 17, 30, 'status 3 metrik gagal 0', 25, '3 metrik aman, gagal 0', 115, 28.75, '0	115\r\n2	100\r\n1     50\r\n	\r\n'),
(73, 17, 30, 'omset shopee', 25, '20.000.000', 85, 21.25, '300 jt	115\r\n100 jt	110\r\n50 jt	100\r\n10 jt	80\r\n5 jt	60\r\n1 jt	40\r\n0	0'),
(74, 17, 31, 'Performa Toko ', 25, '3.7', 70, 17.5, '7 	115\r\n6 	100\r\n5 	90\r\n4     80\r\n3 	70\r\n2 	60\r\n1 	50\r\n'),
(75, 17, 31, 'omset tokopedia', 25, '5.000.000', 60, 15, '300 jt	115\r\n100 jt	110\r\n50 jt	100\r\n10 jt	80\r\n5 jt	60\r\n1 jt	40\r\n0	0\r\n'),
(76, 17, 32, 'Hadir Briefing Tepat Waktu	', 25, '100% mengikuti breafing	', 115, 28.75, '100% Tepat Waktu	115\r\n1 - 2 X terlambat	100\r\n3 - 4 X terlambat	90\r\n>4 X terlambat	50\r\n'),
(77, 17, 32, 'Menjalankan SOP tentang absensi	', 50, '100 % sop tidak ada yang di langgar	', 115, 57.5, '100% taat 115\r\n90% taat 100\r\n80% taat 90\r\n70% taat 80'),
(78, 17, 32, 'Senam pagi', 25, '100% mengikuti senam pagi', 115, 28.75, '100% hadir	115\r\n< 2X tdk hadir dg ijin	100\r\n1-2 X tdk hadir	90\r\n3-4 X tdk hadir	80\r\n> 4X tdk hadir	0'),
(80, 22, 33, 'Membuat Jadwal Audit SOP setiap minggu dan ada report tiap minggunya, dan audit dijalankan sesuai jadwal yang telah dibuat', 20, 'Jadwal audit dibuat per minggu dan dijalankan', 100, 20, '1	Setiap minggu semua di audit sesuai jadwal	115\r\n2	Setiap minggu , 1 SOP tidak teraudit	100\r\n3	Setiap minggu , 2 SOP tidak teraudit	50\r\n	Apabila di audit tapi tidak ada laporan maka poin -5	\r\n'),
(82, 17, 29, 'update flayer 5 produk perbulan ', 20, '5', 100, 20, '> 10   115\r\n 8 - 10     110\r\n5 - 8   105\r\n5      100\r\n4     80\r\n3     60\r\n2     40\r\n1     20\r\n0     0 '),
(83, 17, 30, 'pelayanan respon chat dan mengatur pesanan dengan cepat', 25, '100%', 115, 28.75, '100%	115\r\n95%	110\r\n90%	100\r\n85%	90\r\n80%	80\r\n70%	70\r\n60%	60\r\n50%	50'),
(84, 17, 29, 'iklan produk marketplace dilakukan 2x selama 1 bulan', 10, '1 kali iklan', 115, 11.5, '6     110\r\n5     90\r\n4     70\r\n3     50\r\n2     30\r\n1     10\r\n0     0 '),
(85, 17, 31, 'pelayanan respon chat dan mengatur pesanan dengan cepat', 25, '100%', 115, 28.75, '100%	115\r\n95%	110\r\n90%	100\r\n85%	90\r\n80%	80\r\n70%	70\r\n60%	60\r\n50%	50'),
(86, 17, 31, 'Barang yg dikirim sesuai dengan pesanan dan packingan aman sampai tujuan serta tidak terjadi keterlambatan pengiriman', 25, '100%', 115, 28.75, '100%	115\r\n95%	110\r\n90%	100\r\n85%	90\r\n80%	80\r\n70%	70\r\n60%	60\r\n50%	50'),
(87, 20, 34, 'Membuat Jadwal Audit SOP setiap minggu dan ada report tiap minggunya, dan audit dijalankan sesuai jadwal yang telah dibuat', 20, 'Jadwal audit dibuat per minggu dan dijalankan', 115, 23, '1 Setiap minggu semua di audit sesuai jadwal 115\n2 Setiap minggu , 1 SOP tidak teraudit 100\r\n3 Setiap minggu , 2 SOP tidak teraudit 50\r\n Apabila di audit tapi tidak ada laporan maka poin -5 \r\n'),
(88, 20, 34, 'Mengaudit minimal 2 SOP seminggu', 30, 'Audit 2 SOP/minggu', 100, 30, '1 5 SOP tiap minggu 115\n2 4 SOP tiap minggu 110\r\n3 3 SOP tiap minggu 105\r\n4 2 SOP tiap minggu 100\r\n5 <2 SOP tiap minggu 50'),
(89, 20, 34, 'Membuat Laporan Hasil Audit dan dilaporkan kepada Kadep HRD dan Direksi by email Maksimal H+7 tanpa koreksi', 25, 'H+8', 80, 20, '1 <H+5 tanpa koreksi 115\n2 H+5 tanpa koreksi 110\r\n3 H+6 tanpa koreksi 105\r\n4 H+7 tanpa koreksi 100\r\n5 H+8 tanpa koreksi 80\r\n6 H+9 tanpa koreksi 70\r\n7 H+10 tanpa koreksi 60\r\n8 >H+10 tanpa koreksi 0'),
(90, 20, 34, 'Membuat Laporan hasil audit tiap minggu kepada Kadep HRD', 25, 'Hasil audit dilaporkan setiap minggu beserta next step', 105, 26.25, '1 Membuat dan melaporkan hasil audit tiap minggu serta ada next step dan membuat analisa terkait SOP 115\n2 Membuat dan melaporkan hasil audit tiap minggu serta ada next step 105\r\n3 Membuat dan melaporkan hasil audit tiap minggu 100\r\n4 1 kali tidak melaporkan 80\r\n5 >1 kali tidak melaporkan 50'),
(91, 20, 35, 'Mendokumentasikan & filling KPI All Departemen di server adalah yang terupdate Maksimal H+1 setelah tgl pengumpulan', 15, 'H+0 ketika pengumpulan KPI', 105, 15.75, '1 H-2 115\n2 H-1 110\r\n3 H+0 105\r\n4 H+1 100\r\n5 H+2 90\r\n6 H+3 80\r\n7 H+4 70\r\n > H+5 50'),
(92, 20, 35, 'Meminta dan merekap Nilai KPI All Departemen ', 25, 'Done seluruh Departemen', 115, 28.75, '1 Diukur setiap 1 bln divalidasi HRD dan Kadep 115\n2 Tidak diukur setiap 1 bln & tidak divalidasi HRD dan Kadep 0'),
(93, 20, 35, 'Membuat Jadwal Audit KPI per bulan & diserahkan ke Kadep HRD utk Appv, maksimal H-7 sebelum Awal bulan', 10, 'Jadwal Audit KPI dibuat per minggu', 115, 11.5, '1 <H-7 115\n2 H-7 100\r\n3 H-6 90\r\n4 H-5 80\r\n5 H-4 70\r\n6 H-3 60\r\n7 H-2 50\r\n8 H-1 40\r\n9 H+0 30\r\n10 H+1 20'),
(94, 20, 35, 'Membuat laporan hasil Audit KPI dan dilaporkan ke Kadep HRD setiap minggu', 25, 'Belum dilakukan di Juli', 0, 0, '1 Membuat dan melaporkan hasil audit tiap minggu serta ada next step dan membuat analisa terkait SOP 115\n2 Membuat dan melaporkan hasil audit tiap minggu serta ada next step 105\r\n3 Membuat dan melaporkan hasil audit tiap minggu 100\r\n4 1 kali tidak melaporkan 80\r\n5 >1 kali tidak melaporkan 50'),
(95, 20, 35, 'Membuat Resume hasil audit KPI dan diemail kepada managemen menggunakan email kpikiu.hrd@gmail.com maksimal H+7 setelah 1ON1.', 25, 'H+8', 80, 20, '1 <H+5 115\n2 H+5 110\r\n3 H+6 105\r\n4 H+7 100\r\n5 H+8 80\r\n6 H+9 70\r\n7 H+10 60\r\n8 >H+10 0'),
(96, 20, 36, 'Merapikan tampilan SOP  sesuai dengan aturan pembuatan SOP maksimal H+2 setelah SOP di approve', 20, 'H+0', 115, 23, '1 H+0 tanpa koreksi 115\n2 H+1 tanpa koreksi 110\r\n3 H+2 tanpa koreksi 105\r\n4 H+3 tanpa koreksi 100\r\n5 H+4 tanpa koreksi 90\r\n6 H+5 tanpa koreksi 80\r\n7 >H+5 50\r\n dg koreksi -5 per poin '),
(97, 20, 36, 'Meminta TTD kepada departemen ybs (yang membuat, dan kadep ybs) maksimal H+3', 10, 'H+0', 115, 11.5, '1 H+0 tanpa koreksi 115\n2 H+1 tanpa koreksi 110\r\n3 H+2 tanpa koreksi 105\r\n4 H+3 tanpa koreksi 100\r\n5 H+4 tanpa koreksi 90\r\n6 H+5 tanpa koreksi 80\r\n7 >H+5 50\r\n dg koreksi -5 per poin '),
(98, 20, 36, 'Meminta TTD kepada Kadep Keuangan dan HRD maksimal H+3', 10, 'H+0', 115, 11.5, '1 H+0 tanpa koreksi 115\n2 H+1 tanpa koreksi 110\r\n3 H+2 tanpa koreksi 105\r\n4 H+3 tanpa koreksi 100\r\n5 H+4 tanpa koreksi 90\r\n6 H+5 tanpa koreksi 80\r\n7 >H+5 50\r\n dg koreksi -5 per poin '),
(99, 20, 36, 'Meminta TTD kepada Direktur maksimal H+3', 10, 'H+0 setelah direktur masuk kantor', 115, 11.5, '1 H+0 tanpa koreksi 115\n2 H+1 tanpa koreksi 110\r\n3 H+2 tanpa koreksi 105\r\n4 H+3 tanpa koreksi 100\r\n5 H+4 tanpa koreksi 90\r\n6 H+5 tanpa koreksi 80\r\n7 >H+5 50\r\n dg koreksi -5 per poin '),
(100, 20, 36, 'Update SOP di Kiuserver maksimal H+3', 20, 'H+0 setelah ttd direktur', 115, 23, '1 H+0 tanpa koreksi 115\n2 H+1 tanpa koreksi 110\r\n3 H+2 tanpa koreksi 105\r\n4 H+3 tanpa koreksi 100\r\n5 H+4 tanpa koreksi 90\r\n6 H+5 tanpa koreksi 80\r\n7 >H+5 50\r\n dg koreksi -5 per poin '),
(101, 20, 36, '80% SOP All Departemen adalah SOP yang masih relevan', 30, '24%', 50, 15, '1 100% SOP relevan 115\n2 90% SOP relevan 105\r\n3 80% SOP relevan 100\r\n4 70% SOP relevan 90\r\n5 60% SOP relevan 80\r\n6 50% SOP relevan 70\r\n7 <50% SOP relevan 50'),
(102, 20, 37, 'Hadir Briefing tepat waktu', 35, '0', 115, 40.25, '1 100% Tepat Waktu 115\n2 1 - 2x Terlambat 100\r\n3 2 - 4x terlambat 90\r\n4 >4x 50'),
(103, 20, 37, 'Tidak pernah ST/SP', 30, '0', 115, 34.5, '1 Tidak pernah SP 115\n2 ada pelanggaran 0'),
(104, 20, 37, 'Tidak pernah absen senam sabtu', 35, '0', 115, 40.25, '1 100% hadir 115\n2 <2x tidak hadir dengan ijin 100\r\n3 3-4 tdk hadir 90\r\n4 4-5x tidak hadir 80\r\n5 >5x tidak hadir 0'),
(105, 19, 38, 'Melaporkan Absensi harian All Karyawan di WAG HRD Karisma maksimal jam 10.00', 25, 'Isi Luk', 115, 28.75, '1 Jam 09.00  115\n2 Jam 10.00 100\r\n3 Jam 11.00 85\r\n4 >Jam 11.00 50'),
(106, 19, 38, '1 on 1 Karyawan Sakit', 25, 'Isi Luk', 115, 28.75, '1 100% 1on1 Semua Karyawan & ada next step 115\n2 90% 1on1 Semua Karyawan & ada next step 100\r\n3 80% 1on1 Semua Karyawan & ada next step 85\r\n4 <80% 1on1 Semua Karyawan & ada next step 50\r\n Tidak ada next step tiap poin -10 '),
(107, 19, 38, 'Membuat Rekap Absensi Mingguan dan di Share di WAG Kadep Disscusion with HRD', 25, 'Isi Luk', 115, 28.75, '1 Selalu share di WAG 115\n2 1x Tidak share 0'),
(108, 19, 38, 'Mengupdate Kuota Absensi setiap ada keluar masuk karyawan dan menginfokan di WAG Kadep', 10, 'Isi Luk', 115, 11.5, '1 Selalu mengupdate kuota  115\n2 1x tidak update 0'),
(109, 19, 38, 'Membuat Rekap Absensi Bulanan Maksimal H+7 dan dilaporkan ke Kadep HRD', 15, 'Isi Luk', 115, 17.25, '1 H+5 tanpa koreksi 115\n2 H+6 tanpa koreksi 110\r\n3 H+7 tanpa koreksi 100\r\n4 H+8 tanpa koreksi 90\r\n5 H+9 tanpa koreksi 80\r\n6 H+10 tanpa koreksi 50\r\n7 >H+10 0\r\n dengan koreksi tiap poin -5 '),
(110, 19, 39, 'Membuat Tagihan BPJS maksimal tanggal 7 setiap bulannya', 30, 'Isi Luk', 115, 34.5, '1 Tanggal 5 tanpa koreksi 115\n2 Tanggal 6 tanpa koreksi 111\r\n3 Tanggal 7 tanpa koreksi 100\r\n4 Tanggal 8 tanpa koreksi 90\r\n5 Tanggal 9 tanpa koreksi 80\r\n6 Tanggal 10 tanpa koreksi 50\r\n7 >Tanggal 10 tanpa koreksi 0\r\n dg koreksi -5 per poin '),
(111, 19, 39, 'Menyelesaikan semua data laporan bulanan ( Absensi, BPJS, Laporan Keluar Masuk Kary, Reward hadir dan Pemotongan gaji kary) maksimal tanggal 7 bulan berikutnya', 40, 'Isi Luk', 115, 46, '1 Tanggal 5 tanpa koreksi 115\n2 Tanggal 6 tanpa koreksi 111\r\n3 Tanggal 7 tanpa koreksi 100\r\n4 Tanggal 8 tanpa koreksi 90\r\n5 Tanggal 9 tanpa koreksi 80\r\n6 Tanggal 10 tanpa koreksi 50\r\n7 >Tanggal 10 tanpa koreksi 0\r\n dg koreksi -5 per poin '),
(112, 19, 39, 'Mengirim email ke Direktur untuk data-data tersebut maksimal tanggal 10 bulan berikutnya', 30, 'Isi Luk', 115, 34.5, '1 Tanggal 7 tanpa koreksi 115\n2 Tanggal 8 tanpa koreksi 110\r\n3 Tanggal 9 tanpa koreksi 105\r\n4 Tanggal 10 tanpa koreksi 100\r\n5 > Tanggal 10 tanpa koreksi 50\r\n dg koreksi -5 per poin '),
(113, 19, 40, 'Mengupload loker maksimal H+1 setelah approve oleh direksi', 20, 'Isi Luk', 115, 23, '1 H+0 115\n2 H+1  100\r\n3 H+2 90\r\n4 H+3 80\r\n5 H+4 50\r\n6 >H+4 0'),
(114, 19, 40, 'Share Loker di beberapa media sosial per  minggu minimal 2 media sosial, 8 media sosial per bulan', 25, 'Isi Luk', 115, 28.75, '1 >8 Media Sosial 115\n2 8 Media sosial 100\r\n3 7 Media Sosial 85\r\n4 6 Media Sosial 70\r\n5 <6 Media Sosial 50'),
(115, 19, 40, 'Membuat Jadwal Rekrutmen tiap hari sabtu dan menjalankan sesuai jadwal', 20, 'Isi Luk', 115, 23, '1 Jadwal dibuat dan dijalankan sesuai Timeline 115\n2 Jadwal dibuat, tidak berjalan sesuai Timeline 50\r\n3 Tidak membuat Jadwal 0'),
(116, 19, 40, 'Menemukan ide baru minimal 2 ide/bulan', 20, 'Isi Luk', 80, 16, '1 >3 Ide 115\n2 3 Ide 110\r\n3 2 Ide 100\r\n4 1 Ide 80\r\n5 0 0'),
(117, 19, 40, 'Tidak ada pelanggaran SOP Rekrutmen', 15, 'Isi Luk', 115, 17.25, '1 0 Pelanggaran 115\n2 1-2 Pelanggaran 100\r\n3 >2 pelanggaran 50'),
(118, 19, 41, 'Merapikan semua surat, per masing', 100, 'Isi Luk', 115, 115, '1 Tanggal 7 115\n2 Tanggal 8 110\r\n3 Tanggal 9 105\r\n4 Tanggal 10 100\r\n5 > Tanggal 10 50\r\n tidak sesuai dengan map per departemen -5 tiap poin '),
(119, 19, 42, 'Hadir Briefing tepat waktu', 35, '0', 115, 40.25, '1 100% Tepat Waktu 115\n2 1 - 2x Terlambat 100\r\n3 2 - 4x terlambat 90\r\n4 >4x 50'),
(120, 19, 42, 'Tidak pernah ST/SP', 30, '0', 115, 34.5, '1 Tidak pernah SP 115\n2 ada pelanggaran 0'),
(121, 19, 42, 'Tidak pernah absen senam sabtu', 35, '0', 115, 40.25, '1 100% hadir 115\n2 <2x tidak hadir dengan ijin 100\r\n3 3-4 tdk hadir 90\r\n4 4-5x tidak hadir 80\r\n5 >5x tidak hadir 0'),
(122, 21, 43, 'Memastikan Mekanik melakukan cek kondisi mesin,rem, kopling, kelistrikan, olie mesin, air accu, air radiator (sesuai dengan form checklist)', 20, '100% dilakukan pengecekan all kendaraan sesuai jadwal', 115, 23, '1 YTD 100% dilakukan pengecekan All Kendaaran, tidak ada temuan dan tepat waktu sesuai jadwal/ setiap hari 115\n2 YTD 100% dilakukan pengecekan All Kendaaran, tidak ada temuan 100\r\n3 YTD 96% dilakukan pengecekan All Kendaaran, tidak ada temuan 90\r\n4 YTD 92% dilakukan pengecekan All Kendaaran, tidak ada temuan 80\r\n5 <YTD 92% 0'),
(123, 21, 43, 'Tidak ada komplain dari Driver Distribusi/ kendaraan selalu siap digunakan setiap hari', 20, 'Penyelesaian komplain max H+3 All unit', 100, 20, '1 YTD Nol Komplain dari Driver Distribusi dan kendaraan tidak ada kendala setiap harinya 115\n2 Penyelesaian Komplain Max H+3 (All Unit) 100\r\n3 Penyelesaian Komplain Max H+3 (24 unit) 90\r\n4 Penyelesaian Komplain Max H+3 (23 unit) 80\r\n5 > H+3 0'),
(124, 21, 43, 'Membuat laporan tiap minggu untuk pengecekan Harian All Kendaraan', 20, 'Membuat laporan  tiap minggu dan ada analisa', 115, 23, '1 Membuat laporan tiap minggu dan ada analisa dari hasil supervisi 115\n2 Membuat laporan tiap minggu 100\r\n3 1 kali tidak membuat laporan 90\r\n4 2 kali tidak membuat laporan 50\r\n5 >2 kali tidak membuat laporan 0'),
(125, 21, 43, '100% SOP Kendaraan teraudit dan dijalankan', 20, '100% SOP kendaraan teraudit dan di pastikan sudah relevan', 100, 20, '1 100% SOP Kendaraan teraudit dan dipastikan semua relevan dan dijalankan (tidak ada temuan di setiap bulan) 115\n2 100% SOP Kendaraan  teraudit dan dipastikan semua relevan dan dijalankan  100\r\n3 95% SOP Kendaraan teraudit dan dipastikan semua relevan dan dijalankan  90\r\n4 90% SOP Kendaraan teraudit dan dipastikan semua relevan dan dijalankan  80\r\n5 80% SOP Kendaraan teraudit dan dipastikan semua relevan dan dijalankan  70\r\n6 <80% SOP Kendaraan teraudit dan dipastikan semua relevan dan dijalankan  50'),
(126, 21, 43, 'Membuat analisa terkait kendaraan prima dan dilaporkan ke Kadep maksimal tgl 7 tiap bulannya', 20, '', 0, 0, '1 Membuat analisa dan dilaporkan maksimal tgl 5 115\n2 Membuat analisa dan dilaporkan maksimal tgl 6 110\r\n3 Membuat analisa dan dilaporkan maksimal tgl 7 100\r\n4 Membuat analisa dan dilaporkan maksimal tgl 8 90\r\n5 Membuat analisa dan dilaporkan maksimal tgl 9 80\r\n6 Membuat analisa dan dilaporkan maksimal tgl 10 50\r\n7 Membuat analisa dan dilaporkan > tanggal 10 '),
(127, 21, 44, 'Melakukan supervisi setiap hari dan melaporkan hasil temuan setiap minggu', 20, 'Melakukan supervisi setiap hari dan melaporkan hasil temuan setiap minggu', 115, 23, '1 Supervisi setiap hari, melaporkan temuan serta sudah ada next step terkait temuan serta dilaporkan tiap minggu 115\n2 Supervisi setiap hari, dan melaporkan hasil temuan tiap minggu 100\r\n3 1 kali tidak supervisi dan 1 kali tidak membuat laporan 80\r\n4 2 kali tidak supervisi dan 2 kali tidak membuat laporan 50\r\n5 >2 kali tidak supervisi dan >2 kali tidak membuat laporan 0'),
(128, 21, 44, 'Tidak ada komplain dari karyawan terkait gedung dan inventaris kantor', 20, 'Nol Komplain', 100, 20, '1 Nol Komplain, dan selalu meminta feedback dari Karyawan (Minimal 2 masing-masing departemn) 115\n2 Nol Komplain 100\r\n3 1 kali komplain 80\r\n4 2 kali komplain 50\r\n5 > 2 kali komplain 0'),
(129, 21, 44, 'Memastikan SOP terkait perawatan gedung dan Inventaris kantor teraudit dan dijalankan', 20, '< 80% SOP Gedung dan inventaris teraudit dan relevan', 50, 10, '1 100% SOP Gedung dan Inventaris kantor teraudit dan dipastikan semua relevan dan dijalankan (tidak ada temuan di setiap bulan) 115\n2 100% SOP Gedung dan Inventaris kantor teraudit dan dipastikan semua relevan dan dijalankan  100\r\n3 95% SOP Gedung dan Inventaris kantor teraudit dan dipastikan semua relevan dan dijalankan  90\r\n4 90% SOP Gedung dan Inventaris kantor teraudit dan dipastikan semua relevan dan dijalankan  80\r\n5 80% SOP Gedung dan Inventaris kantor teraudit dan dipastikan semua relevan dan dijalankan  70\r\n6 <80% SOP Gedung dan Inventaris kantor teraudit dan dipastikan semua relevan dan dijalankan  50'),
(130, 21, 44, '100% Gedung terawat tiap bulannya', 15, '100% gedung terawat', 100, 15, '1 100% Gedung terawat dan selalu ada analisa terkait gedung tidap bulannya 115\n2 100% Gedung terawat 100\r\n3 90% Gedung Terawat dan  1 komplain 50\r\n4 <90% Gedung terawat dan >2 kali komplain 0'),
(131, 21, 44, '100% Inventaris Kantor Terawat dan terdata ', 15, '90% inventaris terawat dan terdata', 50, 7.5, '1 100% Inventaris kantor terawat, terdata dan selalu ada analisa terkait gedung tidap bulannya 115\n2 100% inventaris kantor terawat dan terdata  100\r\n3 90% Inventaris kantor Terawat & terdata tetapi ada 1 komplain 50\r\n4 <90% Inventaris Kantor terawat  dan terdata tapi >2 kali komplain 0'),
(132, 21, 44, 'Membuat laporan hasil audit dan melaporkan setiap bulan by email ke Kadep dan Direksi maksimal H+7', 10, '< H+5', 115, 11.5, '1 <H+5 115\n2 H+5 110\r\n3 H+6 105\r\n4 H+7 100\r\n5 H+8 95\r\n6 H+9 90\r\n7 H+10 80\r\n8 >H+10 50'),
(133, 21, 45, 'Hadir Briefing tepat waktu', 50, '0', 115, 57.5, '1 100% Tepat Waktu 115\n2 1 - 2x Terlambat 100\r\n3 2 - 4x terlambat 90\r\n4 >4x 50'),
(134, 21, 45, 'Tidak pernah absen senam sabtu', 50, '0', 115, 57.5, '1 100% hadir 115\n2 <2x tidak hadir dengan ijin 100\r\n3 3-4 tdk hadir 90\r\n4 4-5x tidak hadir 80\r\n5 >5x tidak hadir 0'),
(136, 28, 51, 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan target', 20, '-', 0, 0, '1	H+0	115\r\n2	H+1	100\r\n3	H+2	90\r\n4	H+3	50\r\n5	>H+3	0'),
(137, 28, 51, 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '-', 0, 0, '1	<H+2	115\r\n2	H+2	110\r\n3	H+4	105\r\n4	H+5	100\r\n5	H+6	90\r\n6	H+7	80\r\n7	>H+7	0'),
(138, 28, 52, 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '-\r\n', 0, 0, '1	<H+2	115\r\n2	H+3	110\r\n3	H+4	100\r\n4	H+5	90\r\n5	H+6	80\r\n6	H+7	70\r\n7	>H+7	50'),
(139, 28, 52, 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '-', 0, 0, '1	<H+2	115\r\n2	H+3	110\r\n3	H+4	100\r\n4	H+5	90\r\n5	H+6	80\r\n6	H+7	70\r\n7	>H+7	50'),
(140, 28, 53, 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '-', 0, 0, '1	< H+2	115\r\n2	H+2	100\r\n3	H+3	85\r\n4	H+4	50\r\n5	> H+4	0'),
(141, 28, 53, 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '-', 0, 0, '1	<H+2	115\r\n2	H+2	100\r\n3	H+4	85\r\n4	H+5	50\r\n5	H+6	20'),
(142, 28, 54, 'Hadir Briefing Tepat Waktu', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% Tepat Waktu	115\r\n2	1 - 2 X terlambat	100\r\n3	3 - 4 X terlambat	90\r\n4	>4 X terlambat	50'),
(143, 28, 54, 'Menjalankan SOP Ijin tidak masuk', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% taat	115\r\n2	ada pelanggaran	0'),
(144, 28, 54, 'Tidak pernah absen Briefing', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(145, 28, 54, 'Tidak pernah absen Senam sabtu', 25, 'Sesuai dengan data HRD', 107, 26.75, '1	100% hadir	115\r\n2	< 2X tdk hadir dg ijin	100\r\n3	1-2 X tdk hadir	90\r\n4	3-4 X tdk hadir	80\r\n5	> 4X tdk hadir	0'),
(146, 28, 55, 'Perbaikan Maintance tanpa kesalahan', 50, 'Menyesuaikan dengan Skill Standart', 0, 0, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(147, 28, 55, 'Penilaian perkejaan hardware', 50, 'Menyesuaikan dengan Skill Standart', 0, 0, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(148, 28, 51, 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '-', 0, 0, '1	<H+2	115\r\n2	H+3	110\r\n3	H+4	105\r\n4	H+5	100\r\n5	H+6	90\r\n6	H+7	80'),
(149, 28, 51, 'Skill Standart IT (code programming)', 25, '-', 0, 0, '10	100%	115\r\n9	97% - 99%	112\r\n8	94% - 96%	110\r\n7	90% - 93%	100\r\n6	85% - 89%	90\r\n5	50 %- 84%	80\r\n4	<50%	0');

-- --------------------------------------------------------

--
-- Table structure for table `tb_kpi`
--

CREATE TABLE `tb_kpi` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `poin` text NOT NULL,
  `bobot` double NOT NULL,
  `poin2` text NOT NULL,
  `bobot2` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
(55, 28, 'Supporting maintenance hardware', 10, 'Membantu maintenance hardware', 10);

-- --------------------------------------------------------

--
-- Table structure for table `tb_sop`
--

CREATE TABLE `tb_sop` (
  `id_sop` int(11) NOT NULL,
  `nama_sop` varchar(255) NOT NULL,
  `kode_sop` varchar(50) NOT NULL,
  `tipe_sop` varchar(50) NOT NULL,
  `namafile_sop` varchar(255) NOT NULL,
  `is_karisma` int(3) NOT NULL,
  `is_prioritas` int(3) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
  `id_poinss` int(11) NOT NULL,
  `id_user` int(10) NOT NULL,
  `poin_ss` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_ss`
--

INSERT INTO `tb_ss` (`id_poinss`, `id_user`, `poin_ss`) VALUES
(1, 1, 'Leadership'),
(2, 4, 'leadership'),
(3, 1, 'Menguasai Software'),
(4, 1, 'Menguasai Coding'),
(5, 1, 'Kerapian');

-- --------------------------------------------------------

--
-- Table structure for table `tb_sspoin`
--

CREATE TABLE `tb_sspoin` (
  `id_sspoin` int(11) NOT NULL,
  `id_user` int(10) NOT NULL,
  `id_ss` int(10) NOT NULL,
  `poinss` varchar(255) NOT NULL,
  `nilai1` varchar(255) DEFAULT NULL,
  `nilai2` varchar(255) DEFAULT NULL,
  `nilai3` varchar(255) DEFAULT NULL,
  `nilai4` varchar(255) DEFAULT NULL,
  `nilaiss` double NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_sspoin`
--

INSERT INTO `tb_sspoin` (`id_sspoin`, `id_user`, `id_ss`, `poinss`, `nilai1`, `nilai2`, `nilai3`, `nilai4`, `nilaiss`) VALUES
(1, 1, 1, 'Mampu membuat & menerapkan KPI untuk dirinya sendiri', 'belum bisa', 'setengah bisa', 'bisa', 'sangat bisa', 3),
(2, 1, 1, 'Mampu membuat & menerapkan SOP untuk dirinya sendiri', '', '', '', '', 0),
(3, 1, 1, 'Mampu memimpin dengan data', '', '', '', '', 0),
(4, 1, 1, 'Mampu melakukan coaching dengan data menggunakan Skill Standar & Mikro skill AL & EQ', '', '', '', '', 0),
(5, 1, 1, 'Mempunyai Integritas ( mampu mempertanggung jawabkan apa yang diucapkan )', '', '', '', '', 0),
(6, 1, 1, 'Mampu membuat Action Plan.  Isi Action Plan : (What) : Smart Goal , (How) : Tahapan Rencana yg terukur & ada waktunya', '', '', '', '', 0),
(7, 1, 1, 'Mempunyai Problem Solving. Next Stepnya terukur, ada waktunya & merupakan solusi permanen, dan menyelesaikannya sampai tuntas, dan tidak terjadi lagi masalah yang sama. Mampu mengidentifikasi masalah, mampu membuat next stepnya bersama team', '', '', '', '', 0),
(8, 1, 1, 'Mau dan mampu menerima tantangan & senang ilmu', '', '', '', '', 1),
(10, 1, 1, 'Agile : Banyak mempunyai ide & inisiatif untuk mencapai goalnya', '', '', '', '', 0),
(17, 4, 2, 'Bertanggung Jawab', 'tidak bertanggung jawab', 'sedang bertanggung jawab', 'lumayan bertanggung jawab', 'sangat bertanggung jawab', 3);

-- --------------------------------------------------------

--
-- Table structure for table `tb_users`
--

CREATE TABLE `tb_users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `nama_lngkp` varchar(255) NOT NULL,
  `nik` varchar(255) NOT NULL,
  `bagian` varchar(255) NOT NULL,
  `departement` varchar(255) NOT NULL,
  `jabatan` varchar(255) NOT NULL,
  `atasan` varchar(255) NOT NULL,
  `penilai` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `tb_users`
--

INSERT INTO `tb_users` (`id`, `username`, `nama_lngkp`, `nik`, `bagian`, `departement`, `jabatan`, `atasan`, `penilai`) VALUES
(1, 'rvld', 'Dhany Rifaldi Febriansah', 'Kiu21', 'IT Hardware', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(4, 'wahyu', 'Wahyu Arif Prasetyo', 'QIU1910315', 'IT', 'Keuangan & HRD', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(16, 'Bram', 'Maulana Malik Ibrahim', 'KIU12', 'IT Software', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(17, 'Sheila', 'Sheila Masdaliana Harahap', 'KIU13', 'Sales Onlineshop', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(18, 'Arinda', 'Egata Arinda Prameswari', 'KIU14', 'Konten Kreator', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(19, 'Luluk', 'Luluk Fitria', 'KIU045', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(20, 'Siwi', 'Siwi Mardlatus Syarifah', 'KIU0452', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(21, 'Amin', 'M. Amin Nudin', 'KIU042', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(22, 'Riza', 'Riza Dwi Fitrianingtyas', 'KIU046', 'HRD', 'Keuangan & HRD', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(23, 'Diana', 'Diana Wulandari', 'KIU92', 'Kepala Departemen', 'Keuangan & HRD', 'Kadep', 'Direksi', 'Direksi'),
(24, 'Vita', 'Vita Ari Puspita', 'QIU1101054', 'Team Collection', 'Keuangan & HRD', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(25, 'Arini', 'Arini Dina Yasmin', 'QIU1503089', 'Purchasing', 'Keuangan & HRD', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(26, 'Kurniawan', 'Kurniawan Pratama Arifin', 'QIU2104259', 'Logistik', 'Logistik', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(27, 'Evi', 'Evi Yulia Purnama Sari', 'QIU0511030', 'Sales', 'Sales & Marketing', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(28, 'prayoga', 'Anang Prayoga', 'lalala123', 'IT', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari');

-- --------------------------------------------------------

--
-- Table structure for table `tb_whats`
--

CREATE TABLE `tb_whats` (
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
-- Dumping data for table `tb_whats`
--

INSERT INTO `tb_whats` (`id_what`, `id_user`, `id_kpi`, `p_what`, `bobot`, `hasil`, `nilai`, `total`, `indikatorwhat`) VALUES
(14, 4, 5, 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '5 applikasi', 115, 115, '5 APPLIKASI	115\r\n4 APPLIKASI	110\r\n3 APPLIKASI	105\r\n2 APPLIKASI	100\r\n1 APPLIKASI	95'),
(15, 4, 9, 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, 'beberapa tidak sesusai dengan target', 100, 60, '100% selesai tepat waktu & dibawah target yang sudah di sepakati | 115\r\n100% selesai tepat waktu | 100\r\n90 % selesai tepat waktu | 90\r\n80 % selesai tepat waktu | 80\r\n70 % selesai tepat waktu | 70\r\n'),
(16, 4, 9, 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '100%', 115, 46, '100% bisa di applikasi dan digunakan tanpa bug | 115\r\n100% bisa di applikasi dan masih ada bug | 100\r\n90& bisa di applikasi  | 90\r\n80& bisa di applikasi  | 80\r\n70& bisa di applikasi  | 70'),
(17, 4, 11, 'Nilai KPI PIC Software Digital', 25, 'Excellent', 115, 28.75, '1	EXCELENT	115\r\n2	VERY GOOD	100\r\n3	GOOD	80\r\n4	POOR	50'),
(18, 4, 11, 'Nilai KPI PIC Hardware Digital', 25, 'EXCELLENT	', 115, 28.75, 'EXCELLENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(19, 4, 11, 'Nilai KPI PIC Content Creator', 25, 'Excelent', 115, 28.75, 'EXCELENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(20, 4, 11, 'Nilai KPI PIC Digital Marketing', 25, 'Poor', 50, 12.5, 'EXCELENT	115\r\nVERY GOOD	100\r\nGOOD	80\r\nPOOR	50\r\n'),
(21, 4, 12, 'NILAI ABSENSI ', 100, '99.5', 120, 120, 'SESUAI DATA DARI HRD'),
(22, 16, 13, 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 70, 'Deliver Order , Daily Stock  , Digital ICS , Stock Opname', 115, 80.5, '1	> 4 applikasi	115\r\n2	4 applikasi	100\r\n3	3 applikasi	80\r\n4	2 applikasi	60\r\n5	1 applikasi	40\r\n6	0 applikasi	0'),
(23, 16, 13, 'Setiap Applikasi terdiri dari 5 fitur', 30, 'Rata - Rata setiap applikasi 6 - 7 Fitur', 115, 34.5, '1	>6 Fitur	115\r\n2	6 Fitur	110\r\n3	5 Fitur	100\r\n4	4 Fitur	100\r\n5	3 Fitur	90\r\n6	2 Fitur	80\r\n7	1 Fitur	50'),
(24, 16, 14, 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, 'Sesuai dengan target kelayakan pengguna', 110, 55, '1	100%	115\r\n2	>95%	110\r\n3	90-95%	100\r\n3	80%-90%	90\r\n4	51%-80%	50\r\n5	<50%	0'),
(25, 16, 14, 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, 'Dokumentasi apabila terjadi kesalahan / bug sistem', 110, 55, '1	<H	115\r\n2	H+0	110\r\n3	H+1	108\r\n4	H+2	103\r\n5	H+3	100\r\n6	H+4	90\r\n7	H+7	0'),
(26, 16, 15, 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, 'Tidak ada kendala pada penggunaan digital aplikasi', 115, 80.5, '1	0	115\r\n2	2	100\r\n3	5	50\r\n4	>5	0'),
(27, 16, 15, 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, 'Semua kegagalan proses aplikasi digital dapat cepat terselesaikan', 115, 34.5, '1	< H+2	115\r\n2	H+2	100\r\n3	H+3	85\r\n4	H+4	50\r\n5	> H+4	0'),
(28, 1, 23, 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '135 Konten', 115, 115, '1	> 120 konten & livestream	115\r\n2	100 Konten & livestream	110\r\n3	80 Konten & livestream	100\r\n4	60 Konten & livestream	90\r\n5	40 Konten & livestream	80\r\n6	10 Konten & livestream	70\r\n7	<10 konten & livestream	0'),
(29, 1, 24, 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '100%', 115, 57.5, '1	100%	115\r\n2	95%	110\r\n3	90%	100\r\n4	85%	95\r\n5	80%	80\r\n6	75%	75\r\n7	70%	50\r\n8	<70%	0'),
(30, 1, 24, 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '100%', 115, 57.5, '1	100%	115\r\n2	95%	110\r\n3	90%	100\r\n4	85%	95\r\n5	80%	80\r\n6	75%	75\r\n7	70%	50\r\n8	<70%	0'),
(31, 1, 25, 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV bisa merekam', 100, '100%', 100, 100, '1	100% sesuai target	115\r\n2	90% - 100%	100\r\n3	50% - 90%	90\r\n4	25 - 50%	80\r\n5	1	0'),
(32, 1, 26, 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '100% bisa', 115, 57.5, '1	100% sesuai target	115\r\n2	90% - 99%	110\r\n3	80% - 90%	100\r\n4	70% - 80%	90\r\n5	60% - 70%	80\r\n6	50% - 60%	50\r\n7	<50%	0'),
(33, 1, 26, 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '6 modul', 115, 57.5, '1	>5 fasilitas / modul (applikasi)	115\r\n2	5 fasilitas / modul (applikasi)	100\r\n3	4 fasilitas / modul (applikasi)	90\r\n4	3 fasilitas / modul (applikasi)	80\r\n5	2 fasilitas / modul (applikasi)	70\r\n6	1 fasilitas / modul (applikasi)	50\r\n7	0 fasilitas / modul (applikasi)	0'),
(34, 1, 27, '3 Skill standart IT ', 100, '3,9', 100, 100, '1	nilai skill standart 4	115\r\n2	nilai skill standart 3.5 - 3.9	100\r\n3	nilai skill standart 3 - 3.4	90\r\n4	nilai skill standart < 3	80\r\n5	nilai skill standart <1	0'),
(37, 1, 28, 'Absensi', 100, '130 dari HRD', 130, 130, '10		115\r\n9		110\r\n8		100\r\n7		75\r\n6		50\r\n5		25\r\n4		0\r\n3		125\r\n2		130\r\n1		135'),
(39, 18, 18, 'Membuat project selama 1 tahun 300', 100, '369 konten termasuk dengan live streaming', 115, 115, '1	>300 Konten	115\r\n2	250 Konten	110\r\n3	200 Konten	105\r\n4	150 Konten	100\r\n5	100 Konten	90\r\n6	<100 Konten	80\r\n7	0 Konten	0\r\n'),
(40, 18, 19, 'Peningkatan insight tayangan instagram selama satu tahun', 50, '90 hari= 248.451Tayangan', 115, 57.5, '1	>120rb tayangan	115\r\n2	100rb tayangan	110\r\n3	80rb tayangan	105\r\n4	60rb tayangan	100\r\n5	40rb tayangan	90\r\n6	20rb tayangan	80\r\n7	<20rb tayangan	0\r\n'),
(44, 18, 19, 'Peningkatan insight interaksi instagram selama satu tahun', 35, '90 hari= 3.523 interaksi', 115, 40.25, '1	>2000 interaksi	115\r\n2	1500 interaksi	110\r\n3	1000 interaksi	105\r\n4	800 interaksi	100\r\n5	600 interaksi	90\r\n6	500 interaksi	80\r\n7	<300 interaksi	0\r\n'),
(45, 18, 19, 'Peningkatan insight follower TikTok & Instagram selama satu tahun', 15, 'TOTAL = (TT) 642 + (IG) 432 = 1.074 Follower Tiktok : awal 533 update saat ini 1175 (+642) Instagram : awal 2.300 update saat ini 2732 (+432)', 0, 0, '1	>50.000 Follower	115\r\n2	40.000 Follower	110\r\n3	30.000 Follower	105\r\n4	20.000 Follower	100\r\n5	10.000 Follower	90\r\n6	5.000 Follower	80\r\n7	>4.000 Follower	0\r\n'),
(46, 18, 20, 'Peningkatan insight tayangan TikTok selama satu tahun', 40, '365 hari = 140k Tayangan', 115, 46, '1	>48rb tayangan	115\r\n2	44rb tayangan	110\r\n3	42rb tayangan	105\r\n4	40rb tayangan	100\r\n5	38rb tayangan	90\r\n6	36rb tayangan	80\r\n7	<30rb tayangan	0\r\n'),
(47, 18, 20, 'Peningkatan insight Like TikTok selama satu tahun', 30, '365 hari =1.954 Like', 115, 34.5, '1	>1100 Like	115\r\n2	1000 Like	110\r\n3	900 Like	105\r\n4	800 Like	100\r\n5	700 Like	90\r\n6	600 Like	80\r\n7	<450 Like	0\r\n'),
(48, 18, 20, 'Peningkatan insight tampil profile TikTok selama satu tahun', 30, '365 hari = 3.076 tampilan profil', 115, 34.5, '1	>1600 tampil profile	115\r\n2	1500 tampil profile	110\r\n3	1300 tampil profile	105\r\n4	1100 tampil profile	100\r\n5	900 tampil profile	90\r\n6	700 tampil profile	80\r\n7	<500 tampil profile	0\r\n'),
(49, 18, 21, 'Skill standart Content Creator', 100, 'nilai = 3.81', 110, 110, '1	nilai skill 4	115\r\n2	nilai skill 3.9	112\r\n3	nilai skill 3.8	110\r\n4	nilai skill  3.7	105\r\n5	nilai skill  3.6	103\r\n6	nilai skill  3.5	100\r\n7	nilai skill  3.4	97\r\n8	nilai skill  3.3	94\r\n9	nilai skill  3.2	91\r\n10	nilai skill  3.1	50\r\n'),
(50, 18, 22, 'Absensi', 100, '', 115, 115, '10		115\r\n9		110\r\n8		100\r\n7		75\r\n6		50\r\n5		25\r\n4		0\r\n'),
(51, 17, 29, 'Berapa total omzet dari 01 januari sampai dengan Saat ini VS 10M', 100, 'Rp.  2,313,025,590.08\r\ndari 10M \r\n22% ', 50, 50, '1	>110%	115\r\n2	>100-110	110\r\n3	>90-100%	100\r\n4	>80-90%	85\r\n5	>70-80%	70\r\n6	>60-70%	60\r\n7	<60%	50'),
(53, 16, 16, 'Absensi ( sesuai absensi & nilai dari hrd)', 100, 'Sesuai dengan data HRD', 122, 122, '1	sesuai absensi hrd	115\r\n2	sesuai absensi hrd	110\r\n3	sesuai absensi hrd	100\r\n4		75\r\n'),
(54, 16, 17, 'Supporting maintenance hardware', 100, 'Menyesuaikan dengan Skill Standart', 100, 100, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(55, 17, 30, 'Penilaian toko perbulan', 50, '4.9', 110, 55, '5	 115 \r\n49	 110 \r\n48	 105 \r\n45	 100 \r\n40	 90 \r\n35	 80 \r\n30	 70 \r\n25	 60 \r\n20	 50 \r\n'),
(56, 17, 30, 'Kesehatan toko', 25, '10', 115, 28.75, 'Sangat Baik	115\r\nBaik	100\r\nPerlu ditingkatkan	80\r\nBuruk	50\r\n'),
(57, 17, 30, 'Status penjualan', 25, 'Star Plus ', 115, 28.75, 'Star +	115\r\nStar	100\r\nNon Star	50\r\n'),
(58, 17, 31, 'Level toko 4', 50, 'Level toko 3', 100, 50, 'lv 4	115\r\nlv 3	100\r\nlv 2	80\r\nlv 1	50\r\n'),
(59, 17, 31, 'Parameter Score performa toko', 50, '85 / 100', 95, 47.5, 'score 100	115\r\nscore 95>	110\r\nscore 95	105\r\nscore 90	100\r\nscore 85	95\r\nscore 80	90\r\nscore 75	85\r\nscore 70	80\r\nscore <40	0\r\n'),
(60, 17, 32, 'absen dari website hrd', 100, '116', 116, 116, ''),
(62, 22, 33, '95 SOP Prioritas yang telah disetujui oleh manajemen diaudit dalam 1 tahun ', 100, '80%', 80, 80, '1	100% SOP telah di audit sd <Oktober	115\r\n2	100% SOP telah di audit sd Oktober	110\r\n3	100% SOP telah di audit sd November	105\r\n4	100% SOP telah di audit sd Desember 	100\r\n5	90% SOP teraudit	90\r\n6	80% SOP teraudit	80\r\n7	70% SOP teraudit	70\r\n8	60% SOP teraudit	60\r\n9	50% SOP teraudit	50\r\n10	<50% SOP teraudit	40\r\n'),
(64, 20, 34, '95 SOP Prioritas yang telah disetujui oleh manajemen diaudit dalam 1 tahun ', 100, '57%', 50, 50, '1 100% SOP telah di audit sd <Oktober 115\n2 100% SOP telah di audit sd Oktober 110\r\n3 100% SOP telah di audit sd November 105\r\n4 100% SOP telah di audit sd Desember  100\r\n5 90% SOP teraudit 90\r\n6 80% SOP teraudit 80\r\n7 70% SOP teraudit 70\r\n8 60% SOP teraudit 60\r\n9 50% SOP teraudit 50\r\n10 <50% SOP teraudit 40\r\n'),
(65, 20, 35, '60% Karyawan Karisma mengumpulkan KPI;30;KPI Juni terkumpul 92%', 30, '1 90-100% 115\n2 80% 110\r\n3 60% 100\r\n4 50% 90\r\n5 40% 60\r\n6 30% 50\r\n7 <30% 0', 115, 34.5, ''),
(66, 20, 35, 'Audit Kualitas KPI minimal 3 KPI Karywan per minggu/ 12 KPI per bulan', 30, 'Audit KPI All Karyawan', 115, 34.5, '1 >13 KPI 115\n2 13 KPI 110\r\n3 12 KPI 100\r\n4 11 KPI 90\r\n5 10 KPI 80\r\n6 9 KPI 70\r\n7 8 KPI 60\r\n8 <8KPI 50\r\n9 0 KPI 0'),
(67, 20, 35, 'KPI All karyawan terisi dengan benar dan sesuai data', 40, 'KPI 90% karyawan terisi dan sesuai data', 90, 36, '1 KPI 100% All Karyawan terisi dan sesuai data dan ada data pendukung yang dilampirkan 115\n2 KPI 100% All Karyawan terisi dan sesuai data 100\r\n3 KPI 90% All Karyawan terisi dan sesuai data 90\r\n4 KPI <90% All Karyawan terisi dan sesuai data '),
(68, 20, 36, 'Update SOP baru atau revisi maksimal H+3 setelah SOP di approve oleh Direktur', 40, 'Rata-rata H+0 (Kertas Kerja di Luluk > SOP > Data SOP all)', 115, 46, '1 H+0 tanpa koreksi 115\n2 H+1 tanpa koreksi 110\r\n3 H+2 tanpa koreksi 105\r\n4 H+3 tanpa koreksi 100\r\n5 H+4 tanpa koreksi 90\r\n6 H+5 tanpa koreksi 80\r\n7 >H+5 50\r\n dg koreksi -5 per poin '),
(69, 20, 36, '80% SOP All Departemen adalah SOP yang masih relevan', 60, 'SOP GA dan HRD telah diperiksa = 24%', 50, 30, '1 100% SOP relevan 115\n2 90% SOP relevan 105\r\n3 80% SOP relevan 100\r\n4 70% SOP relevan 90\r\n5 60% SOP relevan 80\r\n6 50% SOP relevan 70\r\n7 <50% SOP relevan 50'),
(70, 20, 37, 'Absensi', 100, '0 absen', 115, 115, '1 0 absen 115\n2 1 absen 111\r\n3 2 absen 110\r\n4 3 absen 109\r\n5 4 absen 108\r\n6 5 absen 107\r\n7 6 absen 106\r\n8 7 absen 107\r\n9 8 absen 106'),
(71, 19, 38, 'Mencapai Target Absensi All Karyawan 99%', 60, 'Isi luk', 120, 72, '1 99,15% - 99,20% 120\n2 99,10% - 99,15% 115\r\n3 99,05% - 99,1% 110\r\n4 99,01% - 99,05% 105\r\n5 99% 100\r\n6 98,8% - 98,9% 95\r\n7 98,6% - 98,7% 90\r\n8 98,4% - 98,5% 80\r\n9 < 98,5% 50'),
(72, 19, 38, 'Mencapai Target Absensi All Karyawan 98,3% (Control)', 40, 'Isi luk', 120, 48, '1 98.50% 120\n2 98.45% 115\r\n3 98.40% 110\r\n4 98.35% 105\r\n5 98.30% 100\r\n6 98.25% 95\r\n7 98.20% 90\r\n8 98% 80\r\n9 <98% 50'),
(73, 19, 39, 'Melaporkan Data absensi terselesaikan maksimal H+10 bulan berikutnya', 25, 'Isi luk', 115, 28.75, '1 Tanggal 7 tanpa koreksi 115\n2 Tanggal 8 tanpa koreksi 110\r\n3 Tanggal 9 tanpa koreksi 105\r\n4 Tanggal 10 tanpa koreksi 100\r\n5 > Tanggal 10 tanpa koreksi 50\r\n dg koreksi -5 per poin '),
(74, 19, 39, 'Melaporkan Data BPJS terselesaikan maksimal H+10 bulan berikutnya', 25, 'Isi luk', 115, 28.75, '1 Tanggal 7 tanpa koreksi 115\n2 Tanggal 8 tanpa koreksi 110\r\n3 Tanggal 9 tanpa koreksi 105\r\n4 Tanggal 10 tanpa koreksi 100\r\n5 > Tanggal 10 tanpa koreksi 50\r\n dg koreksi -5 per poin '),
(75, 19, 39, 'Melaporkan Data Laporan Karyawan Keluar-Masuk maksimal terselesaikan maksimal H+10 bulan berikutnya', 25, 'Isi luk', 115, 28.75, '1 Tanggal 7 tanpa koreksi 115\n2 Tanggal 8 tanpa koreksi 110\r\n3 Tanggal 9 tanpa koreksi 105\r\n4 Tanggal 10 tanpa koreksi 100\r\n5 > Tanggal 10 tanpa koreksi 50\r\n dg koreksi -5 per poin '),
(76, 19, 39, 'Melaporkan Data Reward Hadir dan Pemotongan Gaji Karyawan terselesaikan maksimal H+10 bulan berikutnya', 25, 'Isi luk', 115, 28.75, '1 Tanggal 7 tanpa koreksi 115\n2 Tanggal 8 tanpa koreksi 110\r\n3 Tanggal 9 tanpa koreksi 105\r\n4 Tanggal 10 tanpa koreksi 100\r\n5 > Tanggal 10 tanpa koreksi 50\r\n dg koreksi -5 per poin '),
(77, 19, 40, '100% kebutuhan permintaan karyawan di masing-masing departemen terpenuhi maksimal 2 bulan dan sesuai kriteria (Karyawan Kantor)', 50, 'Isi luk', 110, 55, '1 <1 bulan 115\n2 1 bulan - 1,4 bulan 110\r\n3 1,5 bulan - 1.9 bulan 105\r\n4 2 bulan 100\r\n5 2,1 bulan - 2,5 bulan 95\r\n6 2,5 bulan - 3 bulan 90\r\n7 >3 bulan 50'),
(78, 19, 40, '100% kebutuhan permintaan karyawan di masing-masing departemen terpenuhi maksimal 3 bulan dan sesuai kriteria (Karyawan Lapangan)', 50, 'Isi luk', 80, 40, '1 <2 bulan 115\n2 2 bulan 110\r\n3 2,5 bulan 105\r\n4 3 bulan 100\r\n5 3,5 bulan 95\r\n6 4 bulan 90\r\n7 4,5 80\r\n8 5% 70\r\n9 >5 50'),
(79, 19, 41, 'Merapikan dan mengarsip semua dokumen maksimal H+10 bulan berikutnya;100', 100, 'Isi luk', 115, 115, ''),
(80, 19, 42, 'Absensi', 100, '0 absen', 115, 115, '1 0 absen 115\n2 1 absen 111\r\n3 2 absen 110\r\n4 3 absen 109\r\n5 4 absen 108\r\n6 5 absen 107\r\n7 6 absen 106\r\n8 7 absen 107\r\n9 8 absen 106'),
(81, 21, 43, '100 % Kendaraan Distribusi dalam kondisi PRIMA', 100, '100% Kendaraan distribusi dalam kondisi prima', 100, 100, '1 YTD Kendaraan Distribusi 100% Tidak ada Kendala 115\n2 100% Kendaraan Distribusi dalam kondisi Prima (25 Unit) 100\r\n3 96% Kendaraan Distribusi dalam kondisi Prima (24 unit) 90\r\n4 92% Kendaraan Distribusi dalam kondisi Prima (23 unit) 80\r\n5 <92% 0'),
(82, 21, 44, '100% Gedung Terawat dan Nol Komplain', 50, '90% gedung terawat dan 1 komplain', 50, 25, '1 YTD 100% Gedung Terawat dan tidak ada komplain (serta ada analisa terkait gedung) 115\n2 YTD 100% Gedung Terawat dan tidak ada komplain 100\r\n3 YTD 90% Gedung Terawat dan  1 komplain 50\r\n4 YTD <90% Gedung terawat dan >2 kali komplain 0'),
(83, 21, 44, '100% Inventaris Kantor Terawat, dan Nol Komplain', 50, '90% inventaris terawat dan 1 komplain', 50, 25, '1 YTD 100% Inventaris Terawat dan tidak ada komplain (serta ada analisa & 100% inventaris terdata ) 115\n2 YTD 100% Inventaris Terawat dan tidak ada komplain 100\r\n3 YTD 90% Inventaris Terawat dan  1 komplain 50\r\n4 YTD <90% Inventaris terawat dan >2 kali komplain 0'),
(84, 21, 45, 'Absensi', 100, '0 absen', 115, 115, '1 0 absen 115\n2 1 absen 111\r\n3 2 absen 110\r\n4 3 absen 109\r\n5 4 absen 108\r\n6 5 absen 107\r\n7 6 absen 106\r\n8 7 absen 107\r\n9 8 absen 106'),
(87, 28, 51, 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 20, '-\r\n', 100, 20, '1	> 4 applikasi	115\r\n2	4 applikasi	100\r\n3	3 applikasi	80\r\n4	2 applikasi	60\r\n5	1 applikasi	40\r\n6	0 applikasi	0'),
(88, 28, 51, 'Setiap Applikasi terdiri dari 5 fitur', 30, '-', 115, 34.5, '1	>6 Fitur	115\r\n2	6 Fitur	110\r\n3	5 Fitur	100\r\n4	4 Fitur	100\r\n5	3 Fitur	90\r\n6	2 Fitur	80\r\n7	1 Fitur	50'),
(89, 28, 52, 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '-\r\n', 0, 0, '1	100%	115\r\n2	>95%	110\r\n3	90-95%	100\r\n3	80%-90%	90\r\n4	51%-80%	50\r\n5	<50%	0'),
(90, 28, 52, 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '-\r\n', 0, 0, '1	<H	115\r\n2	H+0	110\r\n3	H+1	108\r\n4	H+2	103\r\n5	H+3	100\r\n6	H+4	90\r\n7	H+7	0'),
(91, 28, 53, 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '-', 0, 0, '1	0	115\r\n2	2	100\r\n3	5	50\r\n4	>5	0'),
(92, 28, 53, 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '-', 0, 0, '1	< H+2	115\r\n2	H+2	100\r\n3	H+3	85\r\n4	H+4	50\r\n5	> H+4	0'),
(93, 28, 54, 'Absensi ( sesuai absensi & nilai dari hrd)', 100, 'Sesuai dengan data HRD', 122, 122, '1	sesuai absensi hrd	115\r\n2	sesuai absensi hrd	110\r\n3	sesuai absensi hrd	100\r\n4		75\r\n'),
(94, 28, 55, 'Supporting maintenance hardware', 100, 'Menyesuaikan dengan Skill Standart', 100, 100, '1	nilai skill 4	115\r\n2	nilai skill 3.5 - 3.9	100\r\n3	nilai skill 3 - 3.4	90\r\n4	nilai skill  < 3	80\r\n5	nilai skill  <1	0'),
(95, 28, 51, 'membuat aplikasi zahir', 50, 'aplikasi masih 90%', 100, 50, 'selesai 100% = 115, 90% = 100');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  ADD PRIMARY KEY (`id`);

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
-- AUTO_INCREMENT for table `tb_auth`
--
ALTER TABLE `tb_auth`
  MODIFY `id_auth` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `tb_bobotkpi`
--
ALTER TABLE `tb_bobotkpi`
  MODIFY `idbobotkpi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_eviden`
--
ALTER TABLE `tb_eviden`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tb_hows`
--
ALTER TABLE `tb_hows`
  MODIFY `id_how` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=151;

--
-- AUTO_INCREMENT for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=56;

--
-- AUTO_INCREMENT for table `tb_sop`
--
ALTER TABLE `tb_sop`
  MODIFY `id_sop` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_ss`
--
ALTER TABLE `tb_ss`
  MODIFY `id_poinss` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_sspoin`
--
ALTER TABLE `tb_sspoin`
  MODIFY `id_sspoin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `tb_whats`
--
ALTER TABLE `tb_whats`
  MODIFY `id_what` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=96;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
