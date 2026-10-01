-- Migration untuk tabel tb_surat_peringatan di server online
-- Kolom alasan_2 dan file_sp sudah ada di database online
-- Jalankan query berikut di phpMyAdmin untuk menambahkan 5 kolom yang belum ada:

ALTER TABLE `tb_surat_peringatan` 
ADD COLUMN `aturan_dilanggar` TEXT NULL AFTER `alasan_2`,
ADD COLUMN `tanggal_kejadian` DATE NULL AFTER `aturan_dilanggar`,
ADD COLUMN `penandatangan` VARCHAR(255) NULL AFTER `keterangan`,
ADD COLUMN `jabatan_penandatangan` VARCHAR(255) NULL AFTER `penandatangan`,
ADD COLUMN `tembusan` TEXT NULL AFTER `jabatan_penandatangan`;

