-- Migration: Penilaian Karakter
-- Purpose:
-- 1. Store reviewer assignments for character assessments.
-- 2. Store answers for 11 character questions per assessment month.
--
-- Safe to run more than once.

CREATE TABLE IF NOT EXISTS `tb_penilaian_karakter_assignment` (
  `id_assignment` int NOT NULL AUTO_INCREMENT,
  `id_user_dinilai` int NOT NULL,
  `id_penilai` int NOT NULL,
  `id_atasan` int NOT NULL,
  `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_assignment`),
  UNIQUE KEY `unique_karakter_assignment` (`id_user_dinilai`,`id_penilai`),
  KEY `idx_karakter_assignment_penilai` (`id_penilai`,`status`),
  KEY `idx_karakter_assignment_atasan` (`id_atasan`,`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

CREATE TABLE IF NOT EXISTS `tb_penilaian_karakter_response` (
  `id_response` int NOT NULL AUTO_INCREMENT,
  `id_assignment` int NOT NULL,
  `bulan` varchar(7) NOT NULL,
  `q1_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q1_fakta` text,
  `q2_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q2_fakta` text,
  `q3_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q3_fakta` text,
  `q4_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q4_fakta` text,
  `q5_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q5_fakta` text,
  `q6_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q6_fakta` text,
  `q7_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q7_fakta` text,
  `q8_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q8_fakta` text,
  `q9_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q9_fakta` text,
  `q10_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q10_fakta` text,
  `q11_jawaban` enum('Ya','Tidak') DEFAULT NULL,
  `q11_fakta` text,
  `submitted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_response`),
  UNIQUE KEY `unique_karakter_response_month` (`id_assignment`,`bulan`),
  KEY `idx_karakter_response_bulan` (`bulan`,`submitted_at`),
  CONSTRAINT `fk_karakter_response_assignment`
    FOREIGN KEY (`id_assignment`)
    REFERENCES `tb_penilaian_karakter_assignment` (`id_assignment`)
    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
