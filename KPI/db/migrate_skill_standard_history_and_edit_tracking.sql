-- Migration: Skill Standard history and superior edit tracking
-- Purpose:
-- 1. Store monthly Skill Standard snapshots in tb_ss_history.
-- 2. Track Skill Standard changes made by a superior on member records.
--
-- Safe to run more than once.

DELIMITER $$

DROP PROCEDURE IF EXISTS add_column_if_not_exists $$
CREATE PROCEDURE add_column_if_not_exists(
    IN p_table_name VARCHAR(64),
    IN p_column_name VARCHAR(64),
    IN p_column_definition TEXT
)
BEGIN
    IF NOT EXISTS (
        SELECT 1
        FROM information_schema.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = p_table_name
          AND COLUMN_NAME = p_column_name
    ) THEN
        SET @sql = CONCAT('ALTER TABLE `', p_table_name, '` ADD COLUMN `', p_column_name, '` ', p_column_definition);
        PREPARE stmt FROM @sql;
        EXECUTE stmt;
        DEALLOCATE PREPARE stmt;
    END IF;
END $$

DELIMITER ;

CALL add_column_if_not_exists('tb_ss', 'original_poin_ss', 'VARCHAR(255) DEFAULT NULL');
CALL add_column_if_not_exists('tb_ss', 'is_edited', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL add_column_if_not_exists('tb_ss', 'edited_by', 'INT DEFAULT NULL');
CALL add_column_if_not_exists('tb_ss', 'edited_at', 'DATETIME DEFAULT NULL');

CALL add_column_if_not_exists('tb_sspoin', 'original_poinss', 'TEXT NULL');
CALL add_column_if_not_exists('tb_sspoin', 'original_nilaiss', 'DECIMAL(10,2) DEFAULT NULL');
CALL add_column_if_not_exists('tb_sspoin', 'original_deskripsi', 'TEXT NULL');
CALL add_column_if_not_exists('tb_sspoin', 'is_edited', 'TINYINT(1) NOT NULL DEFAULT 0');
CALL add_column_if_not_exists('tb_sspoin', 'edited_by', 'INT DEFAULT NULL');
CALL add_column_if_not_exists('tb_sspoin', 'edited_at', 'DATETIME DEFAULT NULL');

DROP PROCEDURE IF EXISTS add_column_if_not_exists;

CREATE TABLE IF NOT EXISTS `tb_ss_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` int NOT NULL,
  `id_ss` int NOT NULL,
  `id_sspoin` int NOT NULL,
  `bulan` varchar(7) NOT NULL,
  `kategori_ss` varchar(255) DEFAULT NULL,
  `poinss` text,
  `nilaiss` decimal(10,2) NOT NULL DEFAULT '0.00',
  `deskripsi` text,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_ss_history_month` (`id_user`,`id_sspoin`,`bulan`),
  KEY `idx_ss_history_user_month` (`id_user`,`bulan`),
  KEY `idx_ss_history_category` (`id_user`,`id_ss`,`bulan`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
