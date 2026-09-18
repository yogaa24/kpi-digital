-- ==========================================================
-- Migration: Tabel Arsip Bulanan Skill Standard (SS)
-- Deskripsi: Menyimpan snapshot evaluasi Skill Standard bulanan 
--            (Header, Kategori, dan Butir Penilaian)
-- ==========================================================

-- 1. Tabel Header Arsip Skill Standard
CREATE TABLE IF NOT EXISTS `tbar_ss_archive` (
    `id_ss_archive` INT NOT NULL AUTO_INCREMENT,
    `bulan` VARCHAR(10) NOT NULL COMMENT 'Periode arsip format MM/YYYY, contoh: 08/2026',
    `id_user` INT NOT NULL COMMENT 'ID Karyawan yang dinilai (tb_users.id)',
    `rata_rata_umum` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Rata-rata kategori Umum',
    `rata_rata_teknis` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Rata-rata kategori Teknis',
    `rata_rata_total` DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Rata-rata keseluruhan',
    `status` TINYINT(1) DEFAULT 1 COMMENT '1 = Terverifikasi, 0 = Belum Terverifikasi',
    `verified_by` INT DEFAULT NULL COMMENT 'ID Atasan yang memverifikasi (tb_users.id)',
    `verified_at` DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Waktu verifikasi',
    `keterangan` TEXT DEFAULT NULL COMMENT 'Catatan / komentar verifikasi',
    PRIMARY KEY (`id_ss_archive`),
    UNIQUE KEY `unique_user_bulan_ss` (`id_user`, `bulan`),
    KEY `idx_ss_archive_user` (`id_user`),
    KEY `idx_ss_archive_bulan` (`bulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Tabel Kategori Arsip Skill Standard
CREATE TABLE IF NOT EXISTS `tbar_ss` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `id_ss_archive` INT NOT NULL COMMENT 'Relasi ke tbar_ss_archive.id_ss_archive',
    `id_user` INT NOT NULL COMMENT 'ID Karyawan',
    `id_poinss_ref` INT DEFAULT NULL COMMENT 'ID referensi dari tb_ss.id_poinss',
    `poin_ss` VARCHAR(255) NOT NULL COMMENT 'Nama Kategori SS',
    `tipe_ss` ENUM('umum', 'teknis') NOT NULL DEFAULT 'umum' COMMENT 'Tipe Kategori',
    PRIMARY KEY (`id`),
    KEY `idx_tbar_ss_archive` (`id_ss_archive`),
    KEY `idx_tbar_ss_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabel Butir / Indikator Penilaian Arsip Skill Standard
CREATE TABLE IF NOT EXISTS `tbar_sspoin` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `id_tbar_ss` INT NOT NULL COMMENT 'Relasi ke tbar_ss.id',
    `id_user` INT NOT NULL COMMENT 'ID Karyawan',
    `id_sspoin_ref` INT DEFAULT NULL COMMENT 'ID referensi dari tb_sspoin.id_sspoin',
    `poinss` TEXT NOT NULL COMMENT 'Pernyataan / Indikator SS',
    `nilai1` TEXT DEFAULT NULL COMMENT 'Deskripsi Rubrik Level 1',
    `nilai2` TEXT DEFAULT NULL COMMENT 'Deskripsi Rubrik Level 2',
    `nilai3` TEXT DEFAULT NULL COMMENT 'Deskripsi Rubrik Level 3',
    `nilai4` TEXT DEFAULT NULL COMMENT 'Deskripsi Rubrik Level 4',
    `nilaiss` DECIMAL(5,2) NOT NULL DEFAULT 0.00 COMMENT 'Nilai yang diperoleh (skala 1-4)',
    `deskripsi` TEXT DEFAULT NULL COMMENT 'Deskripsi / Catatan Penilaian',
    PRIMARY KEY (`id`),
    KEY `idx_tbar_sspoin_ss` (`id_tbar_ss`),
    KEY `idx_tbar_sspoin_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
