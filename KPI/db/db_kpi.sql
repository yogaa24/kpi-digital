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
-- Database: `db_kpi`
--

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
(14, 22, '123', 3),
(15, 23, 'karisma123', 4),
(16, 24, '123', 3),
(17, 25, '123', 1),
(18, 26, '123', 3),
(19, 27, '123', 2),
(20, 28, '123', 1),
(21, 29, '123', 5),
(28, 36, '123', 1),
(29, 37, '123', 1),
(30, 38, '123', 1),
(31, 39, '123', 1),
(32, 40, '123', 2),
(33, 41, '123', 1),
(34, 42, '666', 5),
(35, 43, '123', 1);

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
(11, 24, 0, 0),
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
(26, 39, 0, 0),
(27, 40, 0, 0),
(28, 41, 0, 0),
(29, 43, 0, 0);

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
(8, 28, 'test', 'Penggunaan aplikasi kpi digital.docx', 'eviden desember');

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
(14, 4, 5, 'A', 'Koordinasi dengan kadep & Kadep user tentang permasalahn IT dan system yg bisa dibantu', 35, '0.00', '5 applikasi', 115, 40),
(15, 4, 9, 'A', 'applikasi ada timeline & dan dilaporkan saat meeting', 50, '0.00', '100% dilaporkan & timelinenya & ada nextstepnya', 115, 57.5),
(16, 4, 9, 'A', 'Maintenance program, memastikan semua program yang sudah jalan, bisa berjalan dengan baik', 50, '0.00', '100 % dan 0 keluhan', 115, 57.5),
(17, 4, 5, 'A', 'Membuat Planing Project Aplikasi, berisi Nama aplikasi, tujuan, pemakai . Sbg start pengerjaan.  setelah pesanan atau inisiatif disampaikan pada saat Meeting bulanan target tidak boleh lebih dari 3H kecuali sudah di sepekati dan di atur ulang target tsb', 55, '0.00', 'sesuai target', 115, 63),
(18, 4, 5, 'A', 'Memperhatikan Ruang Kerja IT dan koordinasi dengan atasan terkait kebersihan , alat alat yang sudah tidak terpakai di ruang IT', 10, '0.00', 'Rapi & bersih', 115, 11.5),
(19, 4, 11, 'A', 'Nilai SS PIC Software Digital', 25, '0.00', 'NILAI > 3.8', 110, 27.5),
(20, 4, 11, 'A', 'Nilai SS PIC Hardware Digital', 25, '0.00', 'NILAI 4', 115, 28.75),
(21, 4, 11, 'A', 'Nilai SS PIC Content Creator', 25, '0.00', '3.8', 110, 28),
(22, 4, 11, 'A', 'Nilai SS PIC Digital Marketing', 25, '0.00', '3.5', 100, 25),
(23, 4, 12, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% mengikuti breafing', 115, 28.75),
(24, 4, 12, 'A', 'Menjalankan SOP tentang absensi', 50, '0.00', '100 % sop tidak ada yang di langgar', 115, 57.5),
(25, 4, 12, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75),
(26, 16, 13, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan akhir bulan', 20, '0.00', 'Rata - Rata sesuai dengan target pengerjaan', 100, 20),
(27, 16, 13, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'Report Progress dan done', 110, 22),
(28, 16, 14, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'Terdokumentasi', 115, 69),
(29, 16, 14, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', 'Telah terdokumentasi ', 115, 46),
(30, 16, 15, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', 'Dapat deselesaikan sesuai dengan target', 115, 80.5),
(31, 16, 15, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', 'Telah terdokumentasi', 115, 34.5),
(32, 1, 23, 'A', 'Preparing alat & periksa kelengkapan yang dibutuhkan dalam membuat dalam proses pembuatan konten & selama livestream start - finish', 100, '0.00', '100% Terjadwal & di laporkan', 110, 110),
(33, 1, 24, 'A', 'Mendata inventaris karisma per departemen', 50, '0.00', '4 Dept', 100, 50),
(34, 1, 24, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Hardware per departement', 50, '0.00', '100% Terjadwal & di laporkan', 110, 55),
(35, 1, 25, 'A', 'Apabila cctv trouble, segera maintenance | h+3 dari kerusakan ( terkecuali yang membutuhkan pihak luar / vendor)', 100, '0.00', 'H+2 dari kerusakan', 115, 115),
(36, 1, 26, 'A', 'Maintance terjadwal sesuai target 1 bulan itu 10 Software per departement', 100, '0.00', '100% Terjadwal & di laporkan & TTD', 115, 115),
(37, 1, 27, 'A', 'Menguasai Support Hardware penilaian oleh P.Wahyu', 50, '0.00', '4', 115, 57.5),
(38, 1, 27, 'A', 'Menguasai Support konten kreator penilaian oleh Mbak Arin', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25),
(39, 1, 27, 'A', 'Menguasai Support Software penilaian oleh P.Bram', 25, '0.00', 'nilai skill 3.5 - 3.9', 100, 25),
(40, 1, 28, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '100% Tepat Waktu', 115, 28.75),
(41, 1, 28, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '100% taat', 115, 28.75),
(42, 1, 28, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '100% hadir', 115, 28.75),
(43, 1, 28, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '100% hadir', 115, 28.75),
(44, 18, 18, 'A', 'Reset informasi produk untuk dijadikan bahan dalam konsep dan script konten. Kemudian Membuat timeline planning project', 50, '0.00', 'Reset & pembuatan skrip 2 episode Podcast', 115, 57.5),
(45, 18, 18, 'A', 'Membuat konten video untuk sosial media TikTok, Reels, dan Story Instagram', 50, '0.00', '', 115, 57.5),
(46, 18, 19, 'A', 'Rutin untuk upload konten-konten desain maupun video produk Karisma dan non produk dengan tujuan memperkenalkan Karisma 20 postingan setiap bulan', 20, '0.00', 'Post:40', 115, 23),
(47, 18, 19, 'A', 'Request work task dari pihak atau tim lain untuk keperluan konten video maupun konten desain', 20, '0.00', 'Request November :\r\n- Request dari tim Sales : 4 Konten selesai\r\n- Request dari sales Online : 2 Konten selesai, 2 konten on progress', 100, 20),
(48, 18, 19, 'A', 'Menyelesaikan design konten hari hari penting Nasional & Internasional selama tiga bulan kedepan', 30, '0.00', 'September : 3 Hari Penting\r\nOktober : 4 Hari penting\r\nNovember : 4 Hari Penting', 115, 34.5),
(49, 18, 19, 'A', 'Melakukan live streaming Tiktok & Shopee', 30, '0.00', 'Bulan november telah live 17 kali (Kamis (2x tiktok & Shopee) , Jumat (Shopee) & Sabtu(Tiktok))', 115, 34.5),
(50, 18, 20, 'A', 'Upload Konten-konten desain maupun video produk Karisma dan non produk dengan tujuan memperkenalkan Karisma', 50, '0.00', 'Post:40', 115, 57.5),
(51, 18, 20, 'A', 'Request work task dari pihak atau tim lain untuk keperluan konten video maupun konten desain', 50, '0.00', 'Request November :\r\n- Request dari tim Sales : 4 Konten selesai\r\n- Request dari sales Online : 2 Konten selesai, 2 konten on progress', 110, 55),
(52, 18, 21, 'A', 'Menguasai Software Editing  penilaian oleh P.Wahyu', 50, '0.00', '', 100, 50),
(53, 18, 21, 'A', 'Menguasai Copywriting dan Content Writing oleh P.Wahyu', 25, '0.00', '', 112, 28),
(54, 18, 21, 'A', 'Menguasai Fotografi & Videografi penilaian oleh P.Wahyu', 25, '0.00', '', 115, 28.75),
(55, 18, 22, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', '', 115, 28.75),
(56, 18, 22, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', '', 115, 28.75),
(57, 18, 22, 'A', 'Tidak pernah absen Briefing', 25, '0.00', '', 115, 28.75),
(58, 18, 22, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', '', 115, 28.75),
(59, 17, 29, 'A', 'Setiap bulan harus capai Rp 833.333.333', 50, '0.00', ' 136,574,349.00\r\n/ 833.333.333 * 100 = 16.39%', 50, 25),
(61, 17, 29, 'A', 'Posting flayer dan konten yang sedang trend\r\n 280 perbulan di sosmed\r\n', 20, '0.00', '330', 115, 23),
(63, 16, 16, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(64, 16, 16, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(65, 16, 16, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(66, 16, 16, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(67, 16, 17, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 50),
(68, 16, 17, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 50),
(69, 16, 13, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', 'Disampaikan dalam Management Meeting', 115, 40.25),
(70, 16, 13, 'A', 'Skill Standart IT (code programming) 4', 25, '0.00', 'Menyesuaikan dengan penilaian management', 112, 28),
(71, 17, 30, 'A', 'Barang yg dikirim sesuai dengan pesanan dan packingan aman sampai tujuan serta tidak terjadi keterlambatan pengiriman', 25, '0.00', '100%', 115, 28.75),
(72, 17, 30, 'A', 'status 3 metrik gagal 0', 25, '0.00', '3 metrik aman, gagal 0', 115, 28.75),
(73, 17, 30, 'A', 'omset shopee', 25, '0.00', '20.000.000', 85, 21.25),
(74, 17, 31, 'A', 'Performa Toko ', 25, '0.00', '3.7', 70, 17.5),
(75, 17, 31, 'A', 'omset tokopedia', 25, '0.00', '5.000.000', 60, 15),
(76, 17, 32, 'A', 'Hadir Briefing Tepat Waktu	', 25, '0.00', '100% mengikuti breafing	', 115, 28.75),
(77, 17, 32, 'A', 'Menjalankan SOP tentang absensi	', 50, '0.00', '100 % sop tidak ada yang di langgar	', 115, 57.5),
(78, 17, 32, 'A', 'Senam pagi', 25, '0.00', '100% mengikuti senam pagi', 115, 28.75),
(80, 22, 33, 'A', 'Membuat Jadwal Audit SOP setiap minggu dan ada report tiap minggunya, dan audit dijalankan sesuai jadwal yang telah dibuat', 20, '0.00', 'Jadwal audit dibuat per minggu dan dijalankan', 100, 20),
(82, 17, 29, 'A', 'update flayer 5 produk perbulan ', 20, '0.00', '5', 100, 20),
(83, 17, 30, 'A', 'pelayanan respon chat dan mengatur pesanan dengan cepat', 25, '0.00', '100%', 115, 28.75),
(84, 17, 29, 'A', 'iklan produk marketplace dilakukan 2x selama 1 bulan', 10, '0.00', '1 kali iklan', 115, 11.5),
(85, 17, 31, 'A', 'pelayanan respon chat dan mengatur pesanan dengan cepat', 25, '0.00', '100%', 115, 28.75),
(86, 17, 31, 'A', 'Barang yg dikirim sesuai dengan pesanan dan packingan aman sampai tujuan serta tidak terjadi keterlambatan pengiriman', 25, '0.00', '100%', 115, 28.75),
(87, 20, 34, 'A', 'Membuat Jadwal Audit SOP setiap minggu dan ada report tiap minggunya, dan audit dijalankan sesuai jadwal yang telah dibuat', 20, '0.00', 'Jadwal audit dibuat per minggu dan dijalankan', 115, 23),
(88, 20, 34, 'A', 'Mengaudit minimal 2 SOP seminggu', 30, '0.00', 'Audit 2 SOP/minggu', 100, 30),
(89, 20, 34, 'A', 'Membuat Laporan Hasil Audit dan dilaporkan kepada Kadep HRD dan Direksi by email Maksimal H+7 tanpa koreksi', 25, '0.00', 'H+8', 80, 20),
(90, 20, 34, 'A', 'Membuat Laporan hasil audit tiap minggu kepada Kadep HRD', 25, '0.00', 'Hasil audit dilaporkan setiap minggu beserta next step', 105, 26.25),
(91, 20, 35, 'A', 'Mendokumentasikan & filling KPI All Departemen di server adalah yang terupdate Maksimal H+1 setelah tgl pengumpulan', 15, '0.00', 'H+0 ketika pengumpulan KPI', 105, 15.75),
(92, 20, 35, 'A', 'Meminta dan merekap Nilai KPI All Departemen ', 25, '0.00', 'Done seluruh Departemen', 115, 28.75),
(93, 20, 35, 'A', 'Membuat Jadwal Audit KPI per bulan & diserahkan ke Kadep HRD utk Appv, maksimal H-7 sebelum Awal bulan', 10, '0.00', 'Jadwal Audit KPI dibuat per minggu', 115, 11.5),
(94, 20, 35, 'A', 'Membuat laporan hasil Audit KPI dan dilaporkan ke Kadep HRD setiap minggu', 25, '0.00', 'Belum dilakukan di Juli', 0, 0),
(95, 20, 35, 'A', 'Membuat Resume hasil audit KPI dan diemail kepada managemen menggunakan email kpikiu.hrd@gmail.com maksimal H+7 setelah 1ON1.', 25, '0.00', 'H+8', 80, 20),
(96, 20, 36, 'A', 'Merapikan tampilan SOP  sesuai dengan aturan pembuatan SOP maksimal H+2 setelah SOP di approve', 20, '0.00', 'H+0', 115, 23),
(97, 20, 36, 'A', 'Meminta TTD kepada departemen ybs (yang membuat, dan kadep ybs) maksimal H+3', 10, '0.00', 'H+0', 115, 11.5),
(98, 20, 36, 'A', 'Meminta TTD kepada Kadep Keuangan dan HRD maksimal H+3', 10, '0.00', 'H+0', 115, 11.5),
(99, 20, 36, 'A', 'Meminta TTD kepada Direktur maksimal H+3', 10, '0.00', 'H+0 setelah direktur masuk kantor', 115, 11.5),
(100, 20, 36, 'A', 'Update SOP di Kiuserver maksimal H+3', 20, '0.00', 'H+0 setelah ttd direktur', 115, 23),
(101, 20, 36, 'A', '80% SOP All Departemen adalah SOP yang masih relevan', 30, '0.00', '24%', 50, 15),
(102, 20, 37, 'A', 'Hadir Briefing tepat waktu', 35, '0.00', '0', 115, 40.25),
(103, 20, 37, 'A', 'Tidak pernah ST/SP', 30, '0.00', '0', 115, 34.5),
(104, 20, 37, 'A', 'Tidak pernah absen senam sabtu', 35, '0.00', '0', 115, 40.25),
(105, 19, 38, 'A', 'Melaporkan Absensi harian All Karyawan di WAG HRD Karisma maksimal jam 10.00', 25, '0.00', 'Isi Luk', 115, 28.75),
(106, 19, 38, 'A', '1 on 1 Karyawan Sakit', 25, '0.00', 'Isi Luk', 115, 28.75),
(107, 19, 38, 'A', 'Membuat Rekap Absensi Mingguan dan di Share di WAG Kadep Disscusion with HRD', 25, '0.00', 'Isi Luk', 115, 28.75),
(108, 19, 38, 'A', 'Mengupdate Kuota Absensi setiap ada keluar masuk karyawan dan menginfokan di WAG Kadep', 10, '0.00', 'Isi Luk', 115, 11.5),
(109, 19, 38, 'A', 'Membuat Rekap Absensi Bulanan Maksimal H+7 dan dilaporkan ke Kadep HRD', 15, '0.00', 'Isi Luk', 115, 17.25),
(110, 19, 39, 'A', 'Membuat Tagihan BPJS maksimal tanggal 7 setiap bulannya', 30, '0.00', 'Isi Luk', 115, 34.5),
(111, 19, 39, 'A', 'Menyelesaikan semua data laporan bulanan ( Absensi, BPJS, Laporan Keluar Masuk Kary, Reward hadir dan Pemotongan gaji kary) maksimal tanggal 7 bulan berikutnya', 40, '0.00', 'Isi Luk', 115, 46),
(112, 19, 39, 'A', 'Mengirim email ke Direktur untuk data-data tersebut maksimal tanggal 10 bulan berikutnya', 30, '0.00', 'Isi Luk', 115, 34.5),
(113, 19, 40, 'A', 'Mengupload loker maksimal H+1 setelah approve oleh direksi', 20, '0.00', 'Isi Luk', 115, 23),
(114, 19, 40, 'A', 'Share Loker di beberapa media sosial per  minggu minimal 2 media sosial, 8 media sosial per bulan', 25, '0.00', 'Isi Luk', 115, 28.75),
(115, 19, 40, 'A', 'Membuat Jadwal Rekrutmen tiap hari sabtu dan menjalankan sesuai jadwal', 20, '0.00', 'Isi Luk', 115, 23),
(116, 19, 40, 'A', 'Menemukan ide baru minimal 2 ide/bulan', 20, '0.00', 'Isi Luk', 80, 16),
(117, 19, 40, 'A', 'Tidak ada pelanggaran SOP Rekrutmen', 15, '0.00', 'Isi Luk', 115, 17.25),
(118, 19, 41, 'A', 'Merapikan semua surat, per masing', 100, '0.00', 'Isi Luk', 115, 115),
(119, 19, 42, 'A', 'Hadir Briefing tepat waktu', 35, '0.00', '0', 115, 40.25),
(120, 19, 42, 'A', 'Tidak pernah ST/SP', 30, '0.00', '0', 115, 34.5),
(121, 19, 42, 'A', 'Tidak pernah absen senam sabtu', 35, '0.00', '0', 115, 40.25),
(122, 21, 43, 'A', 'Memastikan Mekanik melakukan cek kondisi mesin,rem, kopling, kelistrikan, olie mesin, air accu, air radiator (sesuai dengan form checklist)', 20, '0.00', '100% dilakukan pengecekan all kendaraan sesuai jadwal', 115, 23),
(123, 21, 43, 'A', 'Tidak ada komplain dari Driver Distribusi/ kendaraan selalu siap digunakan setiap hari', 20, '0.00', 'Penyelesaian komplain max H+3 All unit', 100, 20),
(124, 21, 43, 'A', 'Membuat laporan tiap minggu untuk pengecekan Harian All Kendaraan', 20, '0.00', 'Membuat laporan  tiap minggu dan ada analisa', 115, 23),
(125, 21, 43, 'A', '100% SOP Kendaraan teraudit dan dijalankan', 20, '0.00', '100% SOP kendaraan teraudit dan di pastikan sudah relevan', 100, 20),
(126, 21, 43, 'A', 'Membuat analisa terkait kendaraan prima dan dilaporkan ke Kadep maksimal tgl 7 tiap bulannya', 20, '0.00', '', 0, 0),
(127, 21, 44, 'A', 'Melakukan supervisi setiap hari dan melaporkan hasil temuan setiap minggu', 20, '0.00', 'Melakukan supervisi setiap hari dan melaporkan hasil temuan setiap minggu', 115, 23),
(128, 21, 44, 'A', 'Tidak ada komplain dari karyawan terkait gedung dan inventaris kantor', 20, '0.00', 'Nol Komplain', 100, 20),
(129, 21, 44, 'A', 'Memastikan SOP terkait perawatan gedung dan Inventaris kantor teraudit dan dijalankan', 20, '0.00', '< 80% SOP Gedung dan inventaris teraudit dan relevan', 50, 10),
(130, 21, 44, 'A', '100% Gedung terawat tiap bulannya', 15, '0.00', '100% gedung terawat', 100, 15),
(131, 21, 44, 'A', '100% Inventaris Kantor Terawat dan terdata ', 15, '0.00', '90% inventaris terawat dan terdata', 50, 7.5),
(132, 21, 44, 'A', 'Membuat laporan hasil audit dan melaporkan setiap bulan by email ke Kadep dan Direksi maksimal H+7', 10, '0.00', '< H+5', 115, 11.5),
(133, 21, 45, 'A', 'Hadir Briefing tepat waktu', 50, '0.00', '0', 115, 57.5),
(134, 21, 45, 'A', 'Tidak pernah absen senam sabtu', 50, '0.00', '0', 115, 57.5),
(136, 28, 51, 'A', 'Waktu pengerjaan dapat terselesaikan maksimal H+1 sesuai dengan target', 20, '0.00', 'sesuai', 100, 20),
(137, 28, 51, 'A', 'Membuat laporan produk digital yang telah dibuat , diupdate Max H+5 Bulan berikutnya yang dilaporkan kepada atasan dan Kadep', 20, '0.00', 'sesuai', 100, 20),
(138, 28, 52, 'A', 'Membuat pendjadwalan pemeliharaan sistem secara berkala yang dilaporkan kepada atasan dan kadep maksimal H+5 bulan berikutnya', 60, '0.00', 'sesuai', 100, 60),
(139, 28, 52, 'A', 'Melakukan evaluasi dan laporan hasil pemeliharan / perbaikan sistem untuk membuat produk baru yang ditunjukan kepada Atasan dan Kadep setelah melakukan perawatan', 40, '0.00', '-', 0, 0),
(140, 28, 53, 'A', 'Menyelesaikan pekerjaan troubleshooting cepat dan tepat waktu Max H+1', 70, '0.00', '-', 0, 0),
(141, 28, 53, 'A', 'Membuat laporan troubleshooting yang dilaporkan kepada Atasan dan Kadep Max H+2 Setelah troubleshooting selesai dilakukan', 30, '0.00', '-', 0, 0),
(142, 28, 54, 'A', 'Hadir Briefing Tepat Waktu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(143, 28, 54, 'A', 'Menjalankan SOP Ijin tidak masuk', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(144, 28, 54, 'A', 'Tidak pernah absen Briefing', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(145, 28, 54, 'A', 'Tidak pernah absen Senam sabtu', 25, '0.00', 'Sesuai dengan data HRD', 107, 26.75),
(146, 28, 55, 'A', 'Perbaikan Maintance tanpa kesalahan', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0),
(147, 28, 55, 'A', 'Penilaian perkejaan hardware', 50, '0.00', 'Menyesuaikan dengan Skill Standart', 0, 0),
(148, 28, 51, 'A', 'Membuat Jadwal timeline dan konsep produk digital yang akan dibuatkan. Jadwal dan konsep  dilaporkan ke Atasan bersama Kadep Max H+5 Bulan berikutnya', 35, '0.00', '-', 0, 0),
(149, 28, 51, 'A', 'Skill Standart IT (code programming)', 25, '0.00', '-', 0, 0),
(153, 28, 51, 'A', 'abcde', 20, '0.00', 'agak lumayan', 90, 18),
(154, 29, 56, 'A', 'mambuat kpi sesuai jadwal', 30, '0.00', 'oke2', 100, 30),
(155, 43, 61, 'A', 'Membuat jadwal audit setiap bulan', 50, '0.00', '', 0, 0),
(156, 43, 61, 'A', 'Melaporkan hasil audit H+7 bulan berikutnya melalui email', 50, '0.00', '', 0, 0);

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
(1, 153, 'bisa', '115.00', 1, '2025-12-22 13:38:38'),
(2, 153, 'lumayan', '100.00', 2, '2025-12-22 13:38:38'),
(3, 153, 'agak lumayan', '90.00', 3, '2025-12-22 13:38:38'),
(4, 154, 'oke1', '115.00', 1, '2025-12-25 13:04:15'),
(5, 154, 'oke2', '100.00', 2, '2025-12-25 13:04:15'),
(6, 154, 'oke2', '90.00', 3, '2025-12-25 13:04:15'),
(7, 138, 'sesuai', '100.00', 1, '2026-01-04 02:59:54'),
(8, 136, 'sesuai', '100.00', 1, '2026-01-04 03:00:24'),
(9, 137, 'sesuai', '100.00', 1, '2026-01-04 03:00:33'),
(10, 155, 'Jadwal audit dilaporkan kepada kadep setiap minggu', '115.00', 1, '2026-01-09 08:47:33'),
(11, 155, 'Jadwal audit dilaporkan kepada kadep setiap bulan', '100.00', 2, '2026-01-09 08:47:33'),
(12, 155, 'Jadwal audit tidak dilaporkan', '50.00', 3, '2026-01-09 08:47:33'),
(13, 156, '<H+7', '115.00', 1, '2026-01-09 08:48:32'),
(14, 156, 'H+7', '100.00', 2, '2026-01-09 08:48:32'),
(15, 156, '>H+7', '50.00', 3, '2026-01-09 08:48:32');

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
(4, 97, '> 4 aplikasi', '100.00', 1, '2025-12-21 13:56:16'),
(5, 97, '3 Aplikasi ', '90.00', 2, '2025-12-21 13:56:16'),
(6, 97, '2 aplikasi', '80.00', 3, '2025-12-21 13:56:16'),
(7, 97, '1 aplikasi', '70.00', 4, '2025-12-21 13:56:16'),
(8, 98, 'oke', '115.00', 1, '2025-12-25 13:03:17'),
(9, 98, 'oke2', '100.00', 2, '2025-12-25 13:03:17'),
(10, 98, 'oke3', '90.00', 3, '2025-12-25 13:03:17'),
(11, 99, 'okeee', '111.00', 1, '2026-01-01 10:57:26'),
(12, 89, 'sesuai ', '115.00', 1, '2026-01-04 02:58:11'),
(13, 90, 'sesuai ', '100.00', 1, '2026-01-04 02:58:34'),
(14, 91, 'sesuai', '100.00', 1, '2026-01-04 02:59:13'),
(15, 92, 'sesuai', '100.00', 1, '2026-01-04 02:59:31'),
(16, 101, '0 barang kembali', '115.00', 1, '2026-01-04 14:37:08'),
(21, 104, 'Siwiw 1', '115.00', 1, '2026-01-10 02:25:46'),
(22, 104, 'Siwiw 2', '100.00', 2, '2026-01-10 02:25:46'),
(23, 104, 'Siwiw 3', '90.00', 3, '2026-01-10 02:25:46'),
(24, 104, 'Siwiw 4', '80.00', 4, '2026-01-10 02:25:46');

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
(60, 20, 'jurnal bank', 30, 'menjurnal setiap hari', 30),
(61, 43, 'Audit SOP', 30, 'Membuat jadwal audit, melaporkan hasil audit', 30);

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
(18, 4, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '110.01', '0.00', '107.93', '113.13', '2026-01-11 10:48:41'),
(19, 4, 5, '2025-12', 'Meningkatkan Performa Team IT', 'Meningkatkan Performa IT', '28.00', '28.00', '110.00', '108.00', '35.80', '30.24', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(20, 4, 9, '2025-12', 'Penyelesaian Pembuatan/Pengembangan Program', 'Penyelesaian pembuatan/pengembangan program', '25.00', '25.00', '102.00', '100.00', '25.50', '25.00', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(21, 4, 11, '2025-12', 'People Development', 'People Development', '27.00', '27.00', '95.00', '97.50', '25.65', '26.33', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(22, 4, 12, '2025-12', 'Absensi', 'Absensi', '10.00', '10.00', '118.00', '112.00', '11.80', '11.20', 0, '0.00', '0.00', '0.00', '0.00', '2025-12-30 20:00:00'),
(23, 23, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-11 11:26:10'),
(24, 28, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '98.53', '9.60', '131.62', '48.90', '2026-01-11 15:54:23'),
(25, 28, 51, '2026-01', 'Pembuatan Produk digital (4)', 'Pembuatan produk digital sesuai dengan timeline dan terdokumentasi', '40.00', '40.00', '168.85', '58.00', '67.54', '23.20', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(26, 28, 52, '2026-01', 'Pemeliharaan sistem', 'Produk digital dapat digunakan tanpa hambatan', '25.00', '25.00', '107.50', '60.00', '26.88', '15.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(27, 28, 53, '2026-01', '0% Troubleshooting sistem', 'Melakukan pengecekan berkala ', '15.00', '15.00', '100.00', '0.00', '15.00', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(28, 28, 54, '2026-01', 'Absensi', 'Penilaian absensi oleh HRD', '10.00', '10.00', '122.00', '107.00', '12.20', '10.70', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(29, 28, 55, '2026-01', 'Supporting maintenance hardware', 'Membantu maintenance hardware', '10.00', '10.00', '100.00', '0.00', '10.00', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-11 15:54:23'),
(30, 40, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:19:23'),
(31, 40, 61, '2026-01', 'Produk digital', 'Produk Digital', '40.00', '40.00', '23.00', '0.00', '9.20', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:19:23'),
(32, 20, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '85.57', '0.00', '80.80', '92.73', '2026-01-12 10:20:39'),
(33, 20, 34, '2026-01', 'AUDIT SOP KIU', 'Jadwal audit, audit minimal 8 SOP per bulan, melaporkan hasil audit', '30.00', '30.00', '50.00', '99.25', '15.00', '29.78', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(34, 20, 35, '2026-01', 'AUDIT KPI ALL KARYAWAN', 'Dokumentasi KPI, Rekap Nilai KPI, Jadwal Audit, Hasil Audit', '30.00', '30.00', '105.00', '76.00', '31.50', '22.80', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(35, 20, 36, '2026-01', 'PROSES ADMINISTRASI SOP CEPAT DAN TERUPDATE, SOP di KIUSERVER adalah yang TERUPDATE', 'Merapikan, meminta ttd, update kiuserver max H+3', '30.00', '30.00', '76.00', '95.50', '22.80', '28.65', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(36, 20, 37, '2026-01', 'Absensi', 'Absensi', '10.00', '10.00', '115.00', '115.00', '11.50', '11.50', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-12 10:20:39'),
(37, 43, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '0.00', '0.00', '0.00', '0.00', '2026-01-13 01:22:46'),
(38, 43, 61, '2026-01', 'Audit SOP', 'Membuat jadwal audit, melaporkan hasil audit', '30.00', '30.00', '0.00', '0.00', '0.00', '0.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-13 01:22:46'),
(39, 29, NULL, '2026-01', NULL, NULL, '0.00', '0.00', '0.00', '0.00', '0.00', '0.00', 1, '2.07', '0.00', '10.35', '9.00', '2026-01-13 02:02:38'),
(40, 29, 56, '2026-01', 'test', 'tost', '30.00', '30.00', '34.50', '30.00', '10.35', '9.00', 0, '0.00', '0.00', '0.00', '0.00', '2026-01-13 02:02:38');

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
(4, 1, 'Menguasai Coding'),
(5, 1, 'Kerapian'),
(6, 28, 'digital platform'),
(7, 28, 'bertanggung jawab'),
(8, 43, 'Leadership'),
(9, 43, 'Skill HRD');

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
  `deskripsi` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tb_sspoin`
--

INSERT INTO `tb_sspoin` (`id_sspoin`, `id_user`, `id_ss`, `poinss`, `nilai1`, `nilai2`, `nilai3`, `nilai4`, `nilaiss`, `deskripsi`) VALUES
(1, 1, 1, 'Mampu membuat & menerapkan KPI untuk dirinya sendiri', 'belum bisa', 'setengah bisa', 'bisa', 'sangat bisa', 3, NULL),
(2, 1, 1, 'Mampu membuat & menerapkan SOP untuk dirinya sendiri', '', '', '', '', 0, NULL),
(3, 1, 1, 'Mampu memimpin dengan data', '', '', '', '', 0, NULL),
(4, 1, 1, 'Mampu melakukan coaching dengan data menggunakan Skill Standar & Mikro skill AL & EQ', '', '', '', '', 0, NULL),
(5, 1, 1, 'Mempunyai Integritas ( mampu mempertanggung jawabkan apa yang diucapkan )', '', '', '', '', 0, NULL),
(6, 1, 1, 'Mampu membuat Action Plan.  Isi Action Plan : (What) : Smart Goal , (How) : Tahapan Rencana yg terukur & ada waktunya', '', '', '', '', 0, NULL),
(7, 1, 1, 'Mempunyai Problem Solving. Next Stepnya terukur, ada waktunya & merupakan solusi permanen, dan menyelesaikannya sampai tuntas, dan tidak terjadi lagi masalah yang sama. Mampu mengidentifikasi masalah, mampu membuat next stepnya bersama team', '', '', '', '', 0, NULL),
(8, 1, 1, 'Mau dan mampu menerima tantangan & senang ilmu', '', '', '', '', 1, NULL),
(10, 1, 1, 'Agile : Banyak mempunyai ide & inisiatif untuk mencapai goalnya', '', '', '', '', 0, NULL),
(17, 4, 2, 'Bertanggung Jawab', 'tidak bertanggung jawab', 'sedang bertanggung jawab', 'lumayan bertanggung jawab', 'sangat bertanggung jawab', 3, NULL),
(18, 4, 2, 'beranii', 'belum bisa', 'lumayan', 'bisa', 'sangat bisa', 0, NULL),
(19, 28, 7, 'berani ', 'belumbisa', 'agak bisa', 'lumayan bisa', 'bisa', 3.5, 'bertanggung jawab terhadap pekerjaanya'),
(20, 4, 2, 'menerapkan kpi', NULL, NULL, NULL, NULL, 3.5, 'bisa mambuat dan menarapkan untuk dirinya sendiri'),
(21, 4, 7, 'disiplin', NULL, NULL, NULL, NULL, 0, ''),
(22, 4, 7, 'berani2', NULL, NULL, NULL, NULL, 0, ''),
(23, 43, 8, 'Mampu membuat & menerapkan KPI untuk dirinya sendiri', 'Mengisi KPI Sendiri dari bukti pendukung', 'Membuat Simulasi', 'Membuat Simulasi & Next Step', 'Membuat Simulasi, Membuat Next Step, bisa menganalisa terkait How atau ada poin yang  sudah tidak sesuai', 3.8, 'Dapat membuat dan menerapkan KPI untuk diri sendiri'),
(24, 43, 9, 'komunikasi', NULL, NULL, NULL, NULL, 2.5, 'komunikasi'),
(25, 28, 6, 'Mampu membuat & menerapkan KPI untuk dirinya sendiri', 'Mengisi KPI Sendiri dari bukti pendukung', 'Membuat Simulasi', 'Membuat Simulasi & Next Step', 'Membuat Simulasi, Membuat Next Step, bisa menganalisa terkait How atau ada poin yang  sudah tidak sesuai', 1.2, 'membuat simulasi what dan how pada halaman kpi simulasi');

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
(2, 4, 'SP1', '10/00/00', '2026-01-02', '2026-01-02', '2026-02-01', 'merokok', '', 'dihapus', 29, '2026-01-02 14:13:36', '2026-01-09 07:51:54'),
(3, 4, 'SP2', 'sp2/10/2026', '2026-01-08', '2026-01-08', '2026-02-09', 'merokok', '', 'dihapus', 29, '2026-01-08 02:08:37', '2026-01-09 07:52:04'),
(4, 4, 'SP3', 'sp/001/hrd', '2026-01-08', '2026-01-08', '2026-02-08', 'merokok', '', 'aktif', 29, '2026-01-08 06:56:25', '2026-01-08 06:56:25'),
(5, 1, 'SP2', 'sp/001/hrd', '2026-01-08', '2026-01-08', '2026-02-08', 'merokok', '', 'aktif', 29, '2026-01-08 07:05:54', '2026-01-08 07:05:54');

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
(1, 'rvld', 'Dhany Rifaldi Febriansah', 'Kiu21', 'IT Hardware', 'IT', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(4, 'wahyu', 'Wahyu Arif Prasetyo', 'QIU1910315', 'IT', 'IT', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(16, 'Bram', 'Maulana Malik Ibrahim', 'KIU12', 'IT Software', 'IT', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(17, 'Sheila', 'Sheila Masdaliana Harahap', 'KIU13', 'Sales Onlineshop', 'IT', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(18, 'Arinda', 'Egata Arinda Prameswari', 'KIU14', 'Konten Kreator', 'IT', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(19, 'Luluk', 'Luluk Fitria', 'KIU045', 'HRD', 'HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(20, 'Siwi', 'Siwi Mardlatus Syarifah', 'KIU0452', 'HRD', 'HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(21, 'Amin', 'M. Amin Nudin', 'KIU042', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari'),
(22, 'Riza', 'Riza Dwi Fitrianingtyas', 'KIU046', 'HRD', 'HRD', 'Kadep', 'Diana Wulandari', 'Diana Wulandari'),
(23, 'Diana', 'Diana Wulandari', 'KIU92', 'Kepala Departemen', 'Keuangan & HRD', 'Direktur', 'Direksi', 'Direksi'),
(24, 'Vita', 'Vita Ari Puspita', 'QIU1101054', 'Team Collection', 'Keuangan & Sales', 'Kadep', 'Diana Wulandari', 'Diana Wulandari'),
(25, 'Arini', 'Arini Dina Yasmin', 'QIU1503089', 'Purchasing', 'Keuangan & HRD', 'Kadep', 'Diana Wulandari', 'Diana Wulandari'),
(26, 'Kurniawan', 'Kurniawan Pratama Arifin', 'QIU2104259', 'Logistik', 'Logistik', 'Kadep', 'Diana Wulandari', 'Diana Wulandari'),
(27, 'Evi', 'Evi Yulia Purnama Sari', 'QIU0511030', 'Sales', 'Keuangan & Sales', 'Kabag', 'Diana Wulandari', 'Diana Wulandari'),
(28, 'prayoga', 'Anang Prayoga', 'lalala123', 'IT', 'Keuangan & Sales', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(29, 'adminhrd', 'Admin HRD', '00000', 'HRD', 'Keuangan & HRD', 'Admin HRD', '-', '-'),
(36, 'driver1', 'Budi Santoso', 'DRV001', 'Driver Distribusi', 'Logistik', 'Driver', 'Wildan Ma\'ruf N. W.', 'Kurniawan Pratama Arifin'),
(37, 'driver2', 'Agus Wijaya', 'DRV002', 'Driver Distribusi', 'Logistik', 'Driver', 'Wildan Ma\'ruf N. W.', 'Kurniawan Pratama Arifin'),
(38, 'driver3', 'Slamet Riyadi', 'DRV003', 'Driver Distribusi', 'Logistik', 'Driver', 'Wildan Ma\'ruf N. W.', 'Kurniawan Pratama Arifin'),
(39, 'sales', 'sales1', '321', 'sc', 'Sales & Marketing', 'Karyawan', 'Evi Yulia', 'Heru Sucahyo'),
(40, 'adminedp', 'Wildan Ma\'ruf N. W.', '1234', 'AdminEdp', 'Logistik', 'Kabag', 'Kurniawan', 'Diana Wulandari'),
(41, 'tester1', 'Tester User', 'test1234', 'IT', 'Keuangan & HRD', 'Karyawan', 'Wahyu Arif Prasetyo', 'Diana Wulandari'),
(42, 'itboy', 'itboy', 'IT001', 'System', 'IT', 'Admin IT', '-', '-'),
(43, 'siwiw', 'siwiw siwi', 'kiu1234', 'HRD', 'Keuangan & HRD', 'Karyawan', 'Riza Dwi Fitrianingtyas', 'Diana Wulandari');

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
(14, 4, 5, 'A', 'Membuat aplikasi sederhana untuk mendukung efektifitas pekerjaan departemen lain, (inisiatif baru harus dapat approval )\r\nTarget Minimal 2 aplikasi baru dalam setahun. (60%)', 100, '0.00', '5 applikasi', 115, 115),
(15, 4, 9, 'A', 'Target Waktu Pembuatan Program selesai pada tanggal  yang telah di sepekati di Meeting management\r\nNilai bisa diatas 100 jika 100% selesai sebelum tgl yg disepakati \r\n', 60, '0.00', 'beberapa tidak sesusai dengan target', 100, 60),
(16, 4, 9, 'A', 'Program bisa diaplikasikan oleh user dan mendapat konfirmasi saat meeting bulanan\r\nTarget 100% sdh bisa diaplikasikan', 40, '0.00', '100%', 115, 46),
(17, 4, 11, 'A', 'Nilai KPI PIC Software Digital', 25, '0.00', 'Excellent', 115, 28.75),
(18, 4, 11, 'A', 'Nilai KPI PIC Hardware Digital', 25, '0.00', 'EXCELLENT	', 115, 28.75),
(19, 4, 11, 'A', 'Nilai KPI PIC Content Creator', 25, '0.00', 'Excelent', 115, 28.75),
(20, 4, 11, 'A', 'Nilai KPI PIC Digital Marketing', 25, '0.00', 'Poor', 50, 12.5),
(21, 4, 12, 'A', 'NILAI ABSENSI ', 100, '0.00', '99.5', 120, 120),
(22, 16, 13, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 70, '0.00', 'Deliver Order , Daily Stock  , Digital ICS , Stock Opname', 115, 80.5),
(23, 16, 13, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 30, '0.00', 'Rata - Rata setiap applikasi 6 - 7 Fitur', 115, 34.5),
(24, 16, 14, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'Sesuai dengan target kelayakan pengguna', 110, 55),
(25, 16, 14, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'Dokumentasi apabila terjadi kesalahan / bug sistem', 110, 55),
(26, 16, 15, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'Tidak ada kendala pada penggunaan digital aplikasi', 115, 80.5),
(27, 16, 15, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'Semua kegagalan proses aplikasi digital dapat cepat terselesaikan', 115, 34.5),
(28, 1, 23, 'A', 'Support dalam pembuatan konten & support hardware selama livestream dalam 1 tahun (Prepare hardware untuk melakukan livestream start - finish)', 100, '0.00', '135 Konten', 115, 115),
(29, 1, 24, 'A', 'Inventaris IT terdata 100% , (pembelian , kerusakan , dibuang) dalam 1 tahun', 50, '0.00', '100%', 115, 57.5),
(30, 1, 24, 'A', 'Memastikan Hardware yang di gunakan itu layak 100% dan sudah di analisa', 50, '0.00', '100%', 115, 57.5),
(31, 1, 25, 'A', 'Monitoring cctv setiap hari (Pagi, Siang, Sore) & memastikan CCTV bisa merekam', 100, '0.00', '100%', 100, 100),
(32, 1, 26, 'A', 'bisa maintance software yang digunakan (windows , zahir , zahirdigital , product)', 50, '0.00', '100% bisa', 115, 57.5),
(33, 1, 26, 'A', 'mampu membuat fasilitas / modul  untuk memper mudah pekerjaan 5 dalam 1 tahun', 50, '0.00', '6 modul', 115, 57.5),
(34, 1, 27, 'A', '3 Skill standart IT ', 100, '0.00', '3,9', 100, 100),
(37, 1, 28, 'A', 'Absensi', 100, '0.00', '130 dari HRD', 130, 130),
(39, 18, 18, 'A', 'Membuat project selama 1 tahun 300', 100, '0.00', '369 konten termasuk dengan live streaming', 115, 115),
(40, 18, 19, 'A', 'Peningkatan insight tayangan instagram selama satu tahun', 50, '0.00', '90 hari= 248.451Tayangan', 115, 57.5),
(44, 18, 19, 'A', 'Peningkatan insight interaksi instagram selama satu tahun', 35, '0.00', '90 hari= 3.523 interaksi', 115, 40.25),
(45, 18, 19, 'A', 'Peningkatan insight follower TikTok & Instagram selama satu tahun', 15, '0.00', 'TOTAL = (TT) 642 + (IG) 432 = 1.074 Follower Tiktok : awal 533 update saat ini 1175 (+642) Instagram : awal 2.300 update saat ini 2732 (+432)', 0, 0),
(46, 18, 20, 'A', 'Peningkatan insight tayangan TikTok selama satu tahun', 40, '0.00', '365 hari = 140k Tayangan', 115, 46),
(47, 18, 20, 'A', 'Peningkatan insight Like TikTok selama satu tahun', 30, '0.00', '365 hari =1.954 Like', 115, 34.5),
(48, 18, 20, 'A', 'Peningkatan insight tampil profile TikTok selama satu tahun', 30, '0.00', '365 hari = 3.076 tampilan profil', 115, 34.5),
(49, 18, 21, 'A', 'Skill standart Content Creator', 100, '0.00', 'nilai = 3.81', 110, 110),
(50, 18, 22, 'A', 'Absensi', 100, '0.00', '', 115, 115),
(51, 17, 29, 'A', 'Berapa total omzet dari 01 januari sampai dengan Saat ini VS 10M', 100, '0.00', 'Rp.  2,313,025,590.08\r\ndari 10M \r\n22% ', 50, 50),
(53, 16, 16, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122),
(54, 16, 17, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100),
(55, 17, 30, 'A', 'Penilaian toko perbulan', 50, '0.00', '4.9', 110, 55),
(56, 17, 30, 'A', 'Kesehatan toko', 25, '0.00', '10', 115, 28.75),
(57, 17, 30, 'A', 'Status penjualan', 25, '0.00', 'Star Plus ', 115, 28.75),
(58, 17, 31, 'A', 'Level toko 4', 50, '0.00', 'Level toko 3', 100, 50),
(59, 17, 31, 'A', 'Parameter Score performa toko', 50, '0.00', '85 / 100', 95, 47.5),
(60, 17, 32, 'A', 'absen dari website hrd', 100, '0.00', '116', 116, 116),
(62, 22, 33, 'A', '95 SOP Prioritas yang telah disetujui oleh manajemen diaudit dalam 1 tahun ', 100, '0.00', '80%', 80, 80),
(64, 20, 34, 'A', '95 SOP Prioritas yang telah disetujui oleh manajemen diaudit dalam 1 tahun ', 100, '0.00', '57%', 50, 50),
(65, 20, 35, 'A', '60% Karyawan Karisma mengumpulkan KPI;30;KPI Juni terkumpul 92%', 30, '0.00', '1 90-100% 115\n2 80% 110\r\n3 60% 100\r\n4 50% 90\r\n5 40% 60\r\n6 30% 50\r\n7 <30% 0', 115, 34.5),
(66, 20, 35, 'A', 'Audit Kualitas KPI minimal 3 KPI Karywan per minggu/ 12 KPI per bulan', 30, '0.00', 'Audit KPI All Karyawan', 115, 34.5),
(67, 20, 35, 'A', 'KPI All karyawan terisi dengan benar dan sesuai data', 40, '0.00', 'KPI 90% karyawan terisi dan sesuai data', 90, 36),
(68, 20, 36, 'A', 'Update SOP baru atau revisi maksimal H+3 setelah SOP di approve oleh Direktur', 40, '0.00', 'Rata-rata H+0 (Kertas Kerja di Luluk > SOP > Data SOP all)', 115, 46),
(69, 20, 36, 'A', '80% SOP All Departemen adalah SOP yang masih relevan', 60, '0.00', 'SOP GA dan HRD telah diperiksa = 24%', 50, 30),
(70, 20, 37, 'A', 'Absensi', 100, '0.00', '0 absen', 115, 115),
(71, 19, 38, 'A', 'Mencapai Target Absensi All Karyawan 99%', 60, '0.00', 'Isi luk', 120, 72),
(72, 19, 38, 'A', 'Mencapai Target Absensi All Karyawan 98,3% (Control)', 40, '0.00', 'Isi luk', 120, 48),
(73, 19, 39, 'A', 'Melaporkan Data absensi terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75),
(74, 19, 39, 'A', 'Melaporkan Data BPJS terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75),
(75, 19, 39, 'A', 'Melaporkan Data Laporan Karyawan Keluar-Masuk maksimal terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75),
(76, 19, 39, 'A', 'Melaporkan Data Reward Hadir dan Pemotongan Gaji Karyawan terselesaikan maksimal H+10 bulan berikutnya', 25, '0.00', 'Isi luk', 115, 28.75),
(77, 19, 40, 'A', '100% kebutuhan permintaan karyawan di masing-masing departemen terpenuhi maksimal 2 bulan dan sesuai kriteria (Karyawan Kantor)', 50, '0.00', 'Isi luk', 110, 55),
(78, 19, 40, 'A', '100% kebutuhan permintaan karyawan di masing-masing departemen terpenuhi maksimal 3 bulan dan sesuai kriteria (Karyawan Lapangan)', 50, '0.00', 'Isi luk', 80, 40),
(79, 19, 41, 'A', 'Merapikan dan mengarsip semua dokumen maksimal H+10 bulan berikutnya;100', 100, '0.00', 'Isi luk', 115, 115),
(80, 19, 42, 'A', 'Absensi', 100, '0.00', '0 absen', 115, 115),
(81, 21, 43, 'A', '100 % Kendaraan Distribusi dalam kondisi PRIMA', 100, '0.00', '100% Kendaraan distribusi dalam kondisi prima', 100, 100),
(82, 21, 44, 'A', '100% Gedung Terawat dan Nol Komplain', 50, '0.00', '90% gedung terawat dan 1 komplain', 50, 25),
(83, 21, 44, 'A', '100% Inventaris Kantor Terawat, dan Nol Komplain', 50, '0.00', '90% inventaris terawat dan 1 komplain', 50, 25),
(84, 21, 45, 'A', 'Absensi', 100, '0.00', '0 absen', 115, 115),
(87, 28, 51, 'A', 'Pembuatan Applikasi dalam 1 tahun : 4 Applikasi', 20, '0.00', '-\r\n', 100, 20),
(88, 28, 51, 'A', 'Setiap Applikasi terdiri dari 5 fitur', 30, '0.00', 'ok', 1, 0.3),
(89, 28, 52, 'A', 'Produk digital dapat digunakan dengan optimal dan tidak memiliki kesalahan data proses', 50, '0.00', 'sesuai ', 115, 57.5),
(90, 28, 52, 'A', 'Melaporkan Hasil Pemeliharaan Sistem pada Atasan tgl 5 bulan depan', 50, '0.00', 'sesuai ', 100, 50),
(91, 28, 53, 'A', 'Tidak ada permasalahan / troubleshooting sistem yang dilaporkan', 70, '0.00', 'sesuai', 100, 70),
(92, 28, 53, 'A', 'Progress perbaikan troubleshooting sistem 100% penyelesaian done dilakukan sesuai jadwal setelah mendpat laporan trouble sistem dari user', 30, '0.00', 'sesuai', 100, 30),
(93, 28, 54, 'A', 'Absensi ( sesuai absensi & nilai dari hrd)', 100, '0.00', 'Sesuai dengan data HRD', 122, 122),
(94, 28, 55, 'A', 'Supporting maintenance hardware', 100, '0.00', 'Menyesuaikan dengan Skill Standart', 100, 100),
(95, 28, 51, 'A', 'membuat aplikasi zahir', 50, '0.00', 'aplikasi masih 90%', 100, 50),
(97, 28, 51, 'A', 'plafon', 50, '0.00', '> 4 aplikasi', 100, 50),
(98, 29, 56, 'A', 'membuat kpi', 30, '0.00', 'oke', 115, 34.5),
(99, 31, 58, 'A', 'target omset november', 20, '0.00', 'okeee', 111, 22.2),
(100, 31, 58, 'B', 'target omset november', 80, '103000.00', ' | Hasil Tercapai: 70,352.00', 68.3, 68.3),
(101, 36, 59, 'A', 'NOL BARANG KEMBALI', 100, '0.00', '0', 115, 115),
(102, 28, 51, 'B', 'Target All Omset', 50, '103000.00', ' Hasil Tercapai: 100,000.00', 97.09, 48.55),
(104, 43, 61, 'A', 'Audit sebelum desember', 20, '0.00', '', 0, 0);

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
-- AUTO_INCREMENT for table `tb_auth`
--
ALTER TABLE `tb_auth`
  MODIFY `id_auth` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `tb_bobotkpi`
--
ALTER TABLE `tb_bobotkpi`
  MODIFY `idbobotkpi` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `tb_eviden`
--
ALTER TABLE `tb_eviden`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `tb_hows`
--
ALTER TABLE `tb_hows`
  MODIFY `id_how` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=157;

--
-- AUTO_INCREMENT for table `tb_indikator_hows`
--
ALTER TABLE `tb_indikator_hows`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tb_indikator_whats`
--
ALTER TABLE `tb_indikator_whats`
  MODIFY `id_indikator` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `tb_kpi`
--
ALTER TABLE `tb_kpi`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `tb_kpi_history`
--
ALTER TABLE `tb_kpi_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `tb_sop`
--
ALTER TABLE `tb_sop`
  MODIFY `id_sop` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tb_ss`
--
ALTER TABLE `tb_ss`
  MODIFY `id_poinss` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `tb_sspoin`
--
ALTER TABLE `tb_sspoin`
  MODIFY `id_sspoin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `tb_surat_peringatan`
--
ALTER TABLE `tb_surat_peringatan`
  MODIFY `id_sp` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tb_users`
--
ALTER TABLE `tb_users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `tb_whats`
--
ALTER TABLE `tb_whats`
  MODIFY `id_what` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

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
-- Constraints for table `tb_surat_peringatan`
--
ALTER TABLE `tb_surat_peringatan`
  ADD CONSTRAINT `fk_sp_user` FOREIGN KEY (`id_user`) REFERENCES `tb_users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
