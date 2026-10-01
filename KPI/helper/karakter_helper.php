<?php
// Helper untuk Penilaian Karakter (Penilai Tetap & Penilai Sementara)

if (!function_exists('karakterEnsureTables')) {
    function karakterEnsureTables($conn)
    {
        $assignment = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_penilaian_karakter_assignment` (
            `id_assignment` int NOT NULL AUTO_INCREMENT,
            `id_user_dinilai` int NOT NULL,
            `id_penilai` int NOT NULL,
            `id_atasan` int NOT NULL,
            `bulan` varchar(7) NOT NULL,
            `tipe_penilai` enum('tetap','sementara') NOT NULL DEFAULT 'sementara',
            `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_assignment`),
            UNIQUE KEY `unique_karakter_assignment` (`id_user_dinilai`,`id_penilai`,`bulan`),
            KEY `idx_karakter_assignment_penilai` (`id_penilai`,`status`,`bulan`),
            KEY `idx_karakter_assignment_atasan` (`id_atasan`,`status`,`bulan`),
            KEY `idx_karakter_assignment_tipe` (`tipe_penilai`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $check_bulan = mysqli_query($conn, "SHOW COLUMNS FROM `tb_penilaian_karakter_assignment` LIKE 'bulan'");
        if ($check_bulan && mysqli_num_rows($check_bulan) == 0) {
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` DROP INDEX `unique_karakter_assignment`");
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` DROP INDEX `idx_karakter_assignment_penilai`");
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` DROP INDEX `idx_karakter_assignment_atasan`");
            
            $bulan_sekarang = date('Y-m', strtotime(date('Y-m-01') . ' -1 month'));
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` ADD `bulan` varchar(7) NOT NULL DEFAULT '$bulan_sekarang' AFTER `id_atasan`");
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` ADD UNIQUE KEY `unique_karakter_assignment` (`id_user_dinilai`,`id_penilai`,`bulan`)");
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` ADD KEY `idx_karakter_assignment_penilai` (`id_penilai`,`status`,`bulan`)");
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` ADD KEY `idx_karakter_assignment_atasan` (`id_atasan`,`status`,`bulan`)");
        }

        $check_tipe = mysqli_query($conn, "SHOW COLUMNS FROM `tb_penilaian_karakter_assignment` LIKE 'tipe_penilai'");
        if ($check_tipe && mysqli_num_rows($check_tipe) == 0) {
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` ADD `tipe_penilai` enum('tetap','sementara') NOT NULL DEFAULT 'sementara' AFTER `bulan`");
            mysqli_query($conn, "ALTER TABLE `tb_penilaian_karakter_assignment` ADD KEY `idx_karakter_assignment_tipe` (`tipe_penilai`)");
        }

        $tetap = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_penilaian_karakter_tetap` (
            `id_tetap` int NOT NULL AUTO_INCREMENT,
            `id_user_dinilai` int NOT NULL,
            `id_penilai` int NOT NULL,
            `id_atasan` int NOT NULL,
            `status` enum('aktif','nonaktif') NOT NULL DEFAULT 'aktif',
            `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_tetap`),
            UNIQUE KEY `unique_penilai_tetap` (`id_user_dinilai`,`id_penilai`),
            KEY `idx_penilai_tetap_atasan` (`id_atasan`,`status`),
            KEY `idx_penilai_tetap_penilai` (`id_penilai`,`status`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $response = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_penilaian_karakter_response` (
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
            KEY `idx_karakter_response_bulan` (`bulan`,`submitted_at`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        return $assignment && $tetap && $response;
    }
}

if (!function_exists('karakterSyncPenilaiTetap')) {
    /**
     * Memastikan semua penilai tetap yang aktif otomatis memiliki assignment aktif
     * pada bulan penilaian tertentu.
     */
    function karakterSyncPenilaiTetap($conn, $bulan)
    {
        $bulan_safe = mysqli_real_escape_string($conn, $bulan);
        if (empty($bulan_safe)) {
            return false;
        }

        // Auto-assign penilai tetap ke tb_penilaian_karakter_assignment
        $sync_sql = "INSERT INTO tb_penilaian_karakter_assignment (id_user_dinilai, id_penilai, id_atasan, bulan, tipe_penilai, status)
            SELECT t.id_user_dinilai, t.id_penilai, t.id_atasan, '$bulan_safe', 'tetap', 'aktif'
            FROM tb_penilaian_karakter_tetap t
            INNER JOIN tb_users dinilai ON dinilai.id = t.id_user_dinilai
            INNER JOIN tb_users penilai ON penilai.id = t.id_penilai
            WHERE t.status = 'aktif'
            ON DUPLICATE KEY UPDATE 
                tipe_penilai = 'tetap',
                id_atasan = VALUES(id_atasan)";
        return mysqli_query($conn, $sync_sql);
    }
}
