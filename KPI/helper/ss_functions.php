<?php
// helper/ss_functions.php - Helper terpusat untuk modul Skill Standard (SS)

if (!function_exists('ssMonthLabel')) {
    function ssMonthLabel($month)
    {
        $timestamp = strtotime($month . '-01');
        $nama_bulan = [
            'January' => 'Januari',
            'February' => 'Februari',
            'March' => 'Maret',
            'April' => 'April',
            'May' => 'Mei',
            'June' => 'Juni',
            'July' => 'Juli',
            'August' => 'Agustus',
            'September' => 'September',
            'October' => 'Oktober',
            'November' => 'November',
            'December' => 'Desember'
        ];
        $month_name = date('F', $timestamp);

        return ($nama_bulan[$month_name] ?? $month_name) . ' ' . date('Y', $timestamp);
    }
}

if (!function_exists('ssShortMonthLabel')) {
    function ssShortMonthLabel($month)
    {
        $timestamp = strtotime($month . '-01');
        $nama_bulan = [
            'Jan' => 'Jan',
            'Feb' => 'Feb',
            'Mar' => 'Mar',
            'Apr' => 'Apr',
            'May' => 'Mei',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Agu',
            'Sep' => 'Sep',
            'Oct' => 'Okt',
            'Nov' => 'Nov',
            'Dec' => 'Des'
        ];
        $month_name = date('M', $timestamp);

        return $nama_bulan[$month_name] ?? $month_name;
    }
}

if (!function_exists('ssFormatValue')) {
    function ssFormatValue($value)
    {
        if ($value === null || $value === '') {
            return 'Belum dinilai';
        }

        return number_format((float) $value, 2);
    }
}

if (!function_exists('ssTrendBadge')) {
    function ssTrendBadge($current, $previous)
    {
        if ($previous === null || $previous === '') {
            return '<span class="badge bg-secondary">N/A</span>';
        }

        $difference = (float) $current - (float) $previous;
        if ($difference > 0) {
            return '<span class="badge bg-success">+' . number_format($difference, 2) . '</span>';
        }

        if ($difference < 0) {
            return '<span class="badge bg-danger">' . number_format($difference, 2) . '</span>';
        }

        return '<span class="badge bg-secondary">0.00</span>';
    }
}

if (!function_exists('getSSEditorName')) {
    function getSSEditorName($conn, $editor_id)
    {
        $editor_id = intval($editor_id);
        if ($editor_id <= 0) {
            return '';
        }

        $result = mysqli_query($conn, "SELECT nama_lngkp FROM tb_users WHERE id = $editor_id LIMIT 1");
        $row = $result ? mysqli_fetch_assoc($result) : null;

        return $row['nama_lngkp'] ?? '';
    }
}

if (!function_exists('shortSSValue')) {
    function shortSSValue($value, $length = 55)
    {
        $value = (string) $value;
        return htmlspecialchars(strlen($value) > $length ? substr($value, 0, $length) . '...' : $value);
    }
}

if (!function_exists('ssEnsureTipeColumn')) {
    function ssEnsureTipeColumn($conn)
    {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM tb_ss LIKE 'tipe_ss'");
        if ($check && mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "ALTER TABLE tb_ss ADD COLUMN tipe_ss ENUM('umum','teknis') NOT NULL DEFAULT 'umum' AFTER poin_ss");
        }
    }
}

if (!function_exists('ssEnsureHistoryTable')) {
    function ssEnsureHistoryTable($conn)
    {
        return mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_ss_history` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `id_user` INT NOT NULL,
            `id_ss` INT NOT NULL,
            `id_sspoin` INT NOT NULL,
            `bulan` VARCHAR(7) NOT NULL,
            `kategori_ss` VARCHAR(255) DEFAULT NULL,
            `poinss` TEXT,
            `nilaiss` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
            `deskripsi` TEXT,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_ss_history_month` (`id_user`, `id_sspoin`, `bulan`),
            KEY `idx_ss_history_user_month` (`id_user`, `bulan`),
            KEY `idx_ss_history_category` (`id_user`, `id_ss`, `bulan`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
}

if (!function_exists('ssSyncCurrentMonthHistory')) {
    function ssSyncCurrentMonthHistory($conn, $id_user)
    {
        $id_user = intval($id_user);
        $period = getAppCurrentPeriod();
        $bulan_ini = $period['formatted_ym'];

        if (!ssEnsureHistoryTable($conn)) {
            return false;
        }

        $sql = "SELECT sp.id_sspoin, sp.id_user, sp.id_ss, sp.poinss, sp.nilaiss, sp.deskripsi, s.poin_ss
                FROM tb_sspoin sp
                INNER JOIN tb_ss s ON s.id_poinss = sp.id_ss
                WHERE sp.id_user = $id_user";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            return false;
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $id_ss = intval($row['id_ss']);
            $id_sspoin = intval($row['id_sspoin']);
            $kategori = mysqli_real_escape_string($conn, $row['poin_ss']);
            $poinss = mysqli_real_escape_string($conn, $row['poinss']);
            $nilai = mysqli_real_escape_string($conn, $row['nilaiss']);
            $deskripsi = mysqli_real_escape_string($conn, $row['deskripsi'] ?? '');

            mysqli_query($conn, "INSERT INTO tb_ss_history
                (id_user, id_ss, id_sspoin, bulan, kategori_ss, poinss, nilaiss, deskripsi)
                VALUES ($id_user, $id_ss, $id_sspoin, '$bulan_ini', '$kategori', '$poinss', '$nilai', '$deskripsi')
                ON DUPLICATE KEY UPDATE
                    id_ss = VALUES(id_ss),
                    kategori_ss = VALUES(kategori_ss),
                    poinss = VALUES(poinss),
                    nilaiss = VALUES(nilaiss),
                    deskripsi = VALUES(deskripsi)");
        }

        return true;
    }
}

if (!function_exists('ssGetAverage')) {
    function ssGetAverage($conn, $id_user, $id_ss = null, $bulan = null)
    {
        $id_user = intval($id_user);
        $where = "id_user = $id_user";

        if ($id_ss !== null) {
            $where .= " AND id_ss = " . intval($id_ss);
        }

        if ($bulan !== null) {
            $bulan = mysqli_real_escape_string($conn, $bulan);
            $table = 'tb_ss_history';
            $where .= " AND bulan = '$bulan'";
        } else {
            $table = 'tb_sspoin';
        }

        $result = mysqli_query($conn, "SELECT SUM(nilaiss) AS total, COUNT(nilaiss) AS total_poin FROM $table WHERE $where");
        $row = $result ? mysqli_fetch_assoc($result) : null;

        if ($row && $row['total'] && $row['total_poin']) {
            return (float) $row['total'] / (float) $row['total_poin'];
        }

        return null;
    }
}

if (!function_exists('ssGetScoresByMonth')) {
    function ssGetScoresByMonth($conn, $id_user, $bulan)
    {
        $id_user = intval($id_user);
        $bulan = mysqli_real_escape_string($conn, $bulan);
        $scores = [];

        if (!ssEnsureHistoryTable($conn)) {
            return $scores;
        }

        $result = mysqli_query($conn, "SELECT id_sspoin, nilaiss, deskripsi FROM tb_ss_history WHERE id_user = $id_user AND bulan = '$bulan'");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $scores[$row['id_sspoin']] = [
                    'nilaiss' => (float) $row['nilaiss'],
                    'deskripsi' => $row['deskripsi']
                ];
            }
        }

        return $scores;
    }
}

if (!function_exists('ssEnsureSimulasiTable')) {
    function ssEnsureSimulasiTable($conn)
    {
        return mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_ss_simulasi` (
            `id` INT NOT NULL AUTO_INCREMENT,
            `id_user` INT NOT NULL,
            `id_ss` INT NOT NULL,
            `id_sspoin` INT NOT NULL,
            `nilaiss` DECIMAL(10,2) NOT NULL DEFAULT '0.00',
            `deskripsi` TEXT,
            `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `unique_simulasi_user_sspoin` (`id_user`, `id_sspoin`),
            KEY `idx_simulasi_user_category` (`id_user`, `id_ss`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }
}

if (!function_exists('ssGetSimulationScores')) {
    function ssGetSimulationScores($conn, $id_user)
    {
        $id_user = intval($id_user);
        $scores = [];

        if (!ssEnsureSimulasiTable($conn)) {
            return $scores;
        }

        $result = mysqli_query($conn, "SELECT id_sspoin, nilaiss, deskripsi FROM tb_ss_simulasi WHERE id_user = $id_user");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $scores[$row['id_sspoin']] = [
                    'nilaiss' => (float) $row['nilaiss'],
                    'deskripsi' => $row['deskripsi']
                ];
            }
        }

        return $scores;
    }
}

if (!function_exists('ssSaveSimulationScore')) {
    function ssSaveSimulationScore($conn, $id_user, $id_ss, $id_sspoin, $nilai, $deskripsi)
    {
        $id_user = intval($id_user);
        $id_ss = intval($id_ss);
        $id_sspoin = intval($id_sspoin);
        $nilai_safe = mysqli_real_escape_string($conn, $nilai);
        $deskripsi_safe = mysqli_real_escape_string($conn, $deskripsi);

        if (!ssEnsureSimulasiTable($conn)) {
            return false;
        }

        $sql = "INSERT INTO tb_ss_simulasi (id_user, id_ss, id_sspoin, nilaiss, deskripsi)
                VALUES ($id_user, $id_ss, $id_sspoin, '$nilai_safe', '$deskripsi_safe')
                ON DUPLICATE KEY UPDATE
                    id_ss = VALUES(id_ss),
                    nilaiss = VALUES(nilaiss),
                    deskripsi = VALUES(deskripsi)";

        return mysqli_query($conn, $sql);
    }
}

if (!function_exists('ssGetPreviousScores')) {
    function ssGetPreviousScores($conn, $id_user, $bulan)
    {
        $scores_data = ssGetScoresByMonth($conn, $id_user, $bulan);
        $scores = [];
        foreach ($scores_data as $id_sspoin => $item) {
            $scores[$id_sspoin] = $item['nilaiss'];
        }
        return $scores;
    }
}

if (!function_exists('ssNormalizeImportValue')) {
    function ssNormalizeImportValue($value)
    {
        return trim((string) $value);
    }
}

if (!function_exists('ssFindOrCreateCategory')) {
    function ssFindOrCreateCategory($conn, $id_user, $category, $tipe_ss = 'umum')
    {
        $id_user = intval($id_user);
        $category_safe = mysqli_real_escape_string($conn, $category);
        $tipe_ss_safe = mysqli_real_escape_string($conn, $tipe_ss);
        $result = mysqli_query($conn, "SELECT id_poinss FROM tb_ss WHERE id_user=$id_user AND poin_ss='$category_safe' AND tipe_ss='$tipe_ss_safe' LIMIT 1");

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return intval($row['id_poinss']);
        }

        $insert = mysqli_query($conn, "INSERT INTO tb_ss (id_user, poin_ss, tipe_ss) VALUES ($id_user, '$category_safe', '$tipe_ss_safe')");
        if ($insert) {
            return intval(mysqli_insert_id($conn));
        }

        return false;
    }
}
?>
