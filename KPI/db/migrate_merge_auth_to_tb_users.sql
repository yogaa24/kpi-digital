-- Migrasi tb_auth ke tb_users
-- Jalankan di database KPI setelah backup database.
-- Catatan penting:
-- 1. SQL ini menggabungkan kolom password dan level dari tb_auth ke tb_users.
-- 2. MySQL/MariaDB tidak bisa membuat hash password_hash() PHP secara native.
-- 3. Setelah menjalankan SQL ini, jalankan:
--    php db/migrate_merge_auth_to_tb_users.php
--    untuk memastikan semua password plaintext berubah menjadi hash bcrypt.

START TRANSACTION;

-- Tambahkan kolom auth ke master user.
ALTER TABLE tb_users
    ADD COLUMN IF NOT EXISTS password varchar(255) NULL AFTER username,
    ADD COLUMN IF NOT EXISTS level int NULL AFTER password;

-- Salin data auth lama ke tb_users.
UPDATE tb_users u
INNER JOIN tb_auth a ON a.id_user = u.id
SET
    u.password = a.password,
    u.level = a.level;

-- Isi level kosong berdasarkan jabatan.
UPDATE tb_users
SET level = CASE
    WHEN jabatan = 'Admin HRD' THEN 7
    WHEN jabatan = 'Direktur' THEN 5
    WHEN jabatan = 'Kadep' THEN 4
    WHEN jabatan = 'Manager' THEN 3
    WHEN jabatan = 'Koordinator' THEN 2
    ELSE 1
END
WHERE level IS NULL;

-- Password sementara untuk user yang belum punya password.
-- Wajib segera di-hash lewat script PHP migrasi.
UPDATE tb_users
SET password = '123456'
WHERE password IS NULL OR password = '';

-- Jadikan kolom wajib setelah data terisi.
ALTER TABLE tb_users
    MODIFY password varchar(255) NOT NULL,
    MODIFY level int NOT NULL DEFAULT 1;

-- Backup tb_auth sebelum dihapus.
SET @backup_table = CONCAT('tb_auth_backup_', DATE_FORMAT(NOW(), '%Y%m%d_%H%i%s'));
SET @sql = CONCAT('CREATE TABLE ', @backup_table, ' AS SELECT * FROM tb_auth');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Hapus tabel auth lama karena data sudah pindah ke tb_users.
DROP TABLE tb_auth;

COMMIT;
