<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';

    $autoload_path = __DIR__ . '/../../vendor/autoload.php';
    $ss_import_ready = is_file($autoload_path);
    if ($ss_import_ready) {
        require_once $autoload_path;
    }

    $id_sf = intval($_GET['id']);

    $rsd = mysqli_fetch_assoc(mysqli_query($conn, 'Select * from tb_users where id = ' . $id_sf));
    $is_editing_member_ss = intval($id_user) !== intval($id_sf);

    function ensureSSAnggotaEditColumns($conn)
    {
        $sspoin_columns = [
            'original_poinss' => "ALTER TABLE tb_sspoin ADD COLUMN original_poinss TEXT NULL",
            'original_nilaiss' => "ALTER TABLE tb_sspoin ADD COLUMN original_nilaiss DECIMAL(10,2) NULL",
            'original_deskripsi' => "ALTER TABLE tb_sspoin ADD COLUMN original_deskripsi TEXT NULL",
            'is_edited' => "ALTER TABLE tb_sspoin ADD COLUMN is_edited TINYINT(1) NOT NULL DEFAULT 0",
            'edited_by' => "ALTER TABLE tb_sspoin ADD COLUMN edited_by INT NULL",
            'edited_at' => "ALTER TABLE tb_sspoin ADD COLUMN edited_at DATETIME NULL"
        ];

        $ss_columns = [
            'original_poin_ss' => "ALTER TABLE tb_ss ADD COLUMN original_poin_ss VARCHAR(255) NULL",
            'is_edited' => "ALTER TABLE tb_ss ADD COLUMN is_edited TINYINT(1) NOT NULL DEFAULT 0",
            'edited_by' => "ALTER TABLE tb_ss ADD COLUMN edited_by INT NULL",
            'edited_at' => "ALTER TABLE tb_ss ADD COLUMN edited_at DATETIME NULL"
        ];

        foreach ($sspoin_columns as $column => $query) {
            $check = mysqli_query($conn, "SHOW COLUMNS FROM tb_sspoin LIKE '$column'");
            if ($check && mysqli_num_rows($check) == 0) {
                mysqli_query($conn, $query);
            }
        }

        foreach ($ss_columns as $column => $query) {
            $check = mysqli_query($conn, "SHOW COLUMNS FROM tb_ss LIKE '$column'");
            if ($check && mysqli_num_rows($check) == 0) {
                mysqli_query($conn, $query);
            }
        }
    }

    function ssNormalizeImportValue($value)
    {
        return trim((string) $value);
    }

    function ssEnsureTipeColumn($conn)
    {
        $check = mysqli_query($conn, "SHOW COLUMNS FROM tb_ss LIKE 'tipe_ss'");
        if ($check && mysqli_num_rows($check) == 0) {
            mysqli_query($conn, "ALTER TABLE tb_ss ADD COLUMN tipe_ss ENUM('umum','teknis') NOT NULL DEFAULT 'umum' AFTER poin_ss");
        }
    }

    function ssFindOrCreateCategoryForMember($conn, $id_target_user, $id_editor_user, $category, $is_editing, $tipe_ss = 'umum')
    {
        $id_target_user = intval($id_target_user);
        $id_editor_user = intval($id_editor_user);
        $category_safe = mysqli_real_escape_string($conn, $category);
        $tipe_ss_safe = mysqli_real_escape_string($conn, $tipe_ss);
        $result = mysqli_query($conn, "SELECT id_poinss FROM tb_ss WHERE id_user=$id_target_user AND poin_ss='$category_safe' AND tipe_ss='$tipe_ss_safe' LIMIT 1");

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            return intval($row['id_poinss']);
        }

        if ($is_editing) {
            $insert = mysqli_query($conn, "INSERT INTO tb_ss (id_user, poin_ss, tipe_ss, is_edited, edited_by, edited_at) VALUES ($id_target_user, '$category_safe', '$tipe_ss_safe', 1, $id_editor_user, NOW())");
        } else {
            $insert = mysqli_query($conn, "INSERT INTO tb_ss (id_user, poin_ss, tipe_ss) VALUES ($id_target_user, '$category_safe', '$tipe_ss_safe')");
        }
        if (!$insert) {
            return null;
        }

        return mysqli_insert_id($conn);
    }

    function ssImportSkillStandardForMember($conn, $id_target_user, $id_editor_user, $file_path, $is_editing, $tipe_ss = 'umum')
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        $sheet = $spreadsheet->getActiveSheet();
        $highest_row = $sheet->getHighestDataRow();
        $imported = 0;
        $skipped = 0;
        $errors = [];

        for ($row = 2; $row <= $highest_row; $row++) {
            $category        = ssNormalizeImportValue($sheet->getCell('A' . $row)->getCalculatedValue());
            $point           = ssNormalizeImportValue($sheet->getCell('B' . $row)->getCalculatedValue());
            $nilai1          = ssNormalizeImportValue($sheet->getCell('C' . $row)->getCalculatedValue());
            $nilai2          = ssNormalizeImportValue($sheet->getCell('D' . $row)->getCalculatedValue());
            $nilai3          = ssNormalizeImportValue($sheet->getCell('E' . $row)->getCalculatedValue());
            $nilai4          = ssNormalizeImportValue($sheet->getCell('F' . $row)->getCalculatedValue());
            $nilai_bulan_ini = ssNormalizeImportValue($sheet->getCell('G' . $row)->getCalculatedValue());
            $deskripsi       = ssNormalizeImportValue($sheet->getCell('H' . $row)->getCalculatedValue());

            if ($category === '' && $point === '') {
                continue;
            }

            if ($category === '' || $point === '') {
                $skipped++;
                $errors[] = "Baris $row dilewati: Kategori SS dan Poin SS wajib diisi.";
                continue;
            }

            $category_id = ssFindOrCreateCategoryForMember($conn, $id_target_user, $id_editor_user, $category, $is_editing, $tipe_ss);
            if (!$category_id) {
                $skipped++;
                $errors[] = "Baris $row gagal: kategori tidak bisa dibuat.";
                continue;
            }

            $point_safe = mysqli_real_escape_string($conn, $point);
            $duplicate = mysqli_query($conn, "SELECT id_sspoin FROM tb_sspoin WHERE id_user=$id_target_user AND id_ss=$category_id AND poinss='$point_safe' LIMIT 1");
            if ($duplicate && mysqli_num_rows($duplicate) > 0) {
                $skipped++;
                continue;
            }

            $nilai1_safe   = mysqli_real_escape_string($conn, $nilai1);
            $nilai2_safe   = mysqli_real_escape_string($conn, $nilai2);
            $nilai3_safe   = mysqli_real_escape_string($conn, $nilai3);
            $nilai4_safe   = mysqli_real_escape_string($conn, $nilai4);
            $nilai_bulan_ini = str_replace(',', '.', $nilai_bulan_ini);
            $nilai_bulan_ini = is_numeric($nilai_bulan_ini) ? max(0, min(4, (float) $nilai_bulan_ini)) : 0;
            $nilai_bulan_ini_safe = mysqli_real_escape_string($conn, $nilai_bulan_ini);
            $deskripsi_safe = mysqli_real_escape_string($conn, $deskripsi);

            if ($is_editing) {
                $sql = "INSERT INTO tb_sspoin
                    (id_user, id_ss, poinss, nilai1, nilai2, nilai3, nilai4, nilaiss, deskripsi, is_edited, edited_by, edited_at)
                    VALUES ($id_target_user, $category_id, '$point_safe', '$nilai1_safe', '$nilai2_safe', '$nilai3_safe', '$nilai4_safe', '$nilai_bulan_ini_safe', '$deskripsi_safe', 1, $id_editor_user, NOW())";
            } else {
                $sql = "INSERT INTO tb_sspoin
                    (id_user, id_ss, poinss, nilai1, nilai2, nilai3, nilai4, nilaiss, deskripsi)
                    VALUES ($id_target_user, $category_id, '$point_safe', '$nilai1_safe', '$nilai2_safe', '$nilai3_safe', '$nilai4_safe', '$nilai_bulan_ini_safe', '$deskripsi_safe')";
            }

            if (mysqli_query($conn, $sql)) {
                $imported++;
            } else {
                $skipped++;
                $errors[] = "Baris $row gagal disimpan.";
            }
        }

        return [
            'imported' => $imported,
            'skipped'  => $skipped,
            'errors'   => $errors
        ];
    }

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

    function shortSSValue($value, $length = 55)
    {
        $value = (string) $value;
        return htmlspecialchars(strlen($value) > $length ? substr($value, 0, $length) . '...' : $value);
    }

    function ssFormatValue($value)
    {
        if ($value === null || $value === '') {
            return 'Belum dinilai';
        }

        return number_format((float) $value, 2);
    }

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

    function ssSyncCurrentMonthHistory($conn, $id_user)
    {
        $id_user = intval($id_user);
        $bulan = date('Y-m', strtotime('-1 month'));

        if (!ssEnsureHistoryTable($conn)) {
            return false;
        }

        $sql = "SELECT sp.id_sspoin, sp.id_ss, sp.poinss, sp.nilaiss, sp.deskripsi, s.poin_ss
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
                VALUES ($id_user, $id_ss, $id_sspoin, '$bulan', '$kategori', '$poinss', '$nilai', '$deskripsi')
                ON DUPLICATE KEY UPDATE
                    id_ss = VALUES(id_ss),
                    kategori_ss = VALUES(kategori_ss),
                    poinss = VALUES(poinss),
                    nilaiss = VALUES(nilaiss),
                    deskripsi = VALUES(deskripsi)");
        }

        return true;
    }

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

    function ssGetPreviousScores($conn, $id_user, $bulan)
    {
        $id_user = intval($id_user);
        $bulan = mysqli_real_escape_string($conn, $bulan);
        $scores = [];

        if (!ssEnsureHistoryTable($conn)) {
            return $scores;
        }

        $result = mysqli_query($conn, "SELECT id_sspoin, nilaiss FROM tb_ss_history WHERE id_user = $id_user AND bulan = '$bulan'");
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $scores[$row['id_sspoin']] = (float) $row['nilaiss'];
            }
        }

        return $scores;
    }

    ensureSSAnggotaEditColumns($conn);
    ssEnsureTipeColumn($conn);

    if (isset($_POST['submitSS'])) {
        $poin = $_POST['poin'];
        $poin_safe = mysqli_real_escape_string($conn, $poin);
        $tipe_ss = in_array($_POST['tipe_ss'] ?? '', ['umum', 'teknis']) ? $_POST['tipe_ss'] : 'umum';
        if ($is_editing_member_ss) {
            $sql = "INSERT INTO tb_ss (id_user, poin_ss, tipe_ss, is_edited, edited_by, edited_at)
            VALUES ($id_sf, '$poin_safe', '$tipe_ss', 1, $id_user, NOW())";
        } else {
            $sql = "INSERT INTO tb_ss (id_user, poin_ss, tipe_ss)
            VALUES ($id_sf, '$poin_safe', '$tipe_ss')";
        }
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ssanggotadetail?id=' . $id_sf . '&tab=' . $tipe_ss);
            exit();
        } else {
            echo "<script>alert('Gagal, Tambah Skill Standard')</script>";
        }
    }
    if (isset($_POST['addsspoin'])) {
        $poin = $_POST['tujuan'];
        $id = $_POST['idss'];
        $poin_safe = mysqli_real_escape_string($conn, $poin);
        $indikator_1 = mysqli_real_escape_string($conn, $_POST['indikator_1'] ?? '');
        $indikator_2 = mysqli_real_escape_string($conn, $_POST['indikator_2'] ?? '');
        $indikator_3 = mysqli_real_escape_string($conn, $_POST['indikator_3'] ?? '');
        $indikator_4 = mysqli_real_escape_string($conn, $_POST['indikator_4'] ?? '');

        if ($is_editing_member_ss) {
            $sql = "INSERT INTO tb_sspoin (`id_user`, `id_ss`, `poinss`, `nilai1`, `nilai2`, `nilai3`, `nilai4`, `nilaiss`, `deskripsi`, `is_edited`, `edited_by`, `edited_at`)
            VALUES ($id_sf, $id, '$poin_safe', '$indikator_1', '$indikator_2', '$indikator_3', '$indikator_4', 0, '', 1, $id_user, NOW())";
        } else {
            $sql = "INSERT INTO tb_sspoin (`id_user`, `id_ss`, `poinss`, `nilai1`, `nilai2`, `nilai3`, `nilai4`, `nilaiss`, `deskripsi`)
            VALUES ($id_sf, $id, '$poin_safe', '$indikator_1', '$indikator_2', '$indikator_3', '$indikator_4', 0, '')";
        }
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Gagal, Tambah Skill Standard')</script>";
        }
    }
    if (isset($_POST['ss_edit'])) {
        $poin = $_POST['poinsss'];
        $id = $_POST['idsss'];
        $poin_safe = mysqli_real_escape_string($conn, $poin);

        if ($is_editing_member_ss) {
            $sql = "UPDATE tb_sspoin 
            SET original_poinss = CASE WHEN original_poinss IS NOT NULL THEN original_poinss ELSE poinss END,
                poinss='$poin_safe',
                is_edited=1,
                edited_by=$id_user,
                edited_at=NOW()
            WHERE id_sspoin=$id";
        } else {
            $sql = "UPDATE tb_sspoin 
            SET poinss='$poin_safe'
            WHERE id_sspoin=$id";
        }
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Gagal Edit Skill')</script>";
        }
    }
    
    if (isset($_POST['ss_hapus'])) {
        $id = $_POST['idpoin'];
    
        $sql = "DELETE FROM tb_sspoin WHERE id_sspoin=$id";  // Pastikan DELETE ditulis kapital
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Hapus Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal Hapus Skill')</script>";
        }
    }
    if (isset($_POST['ss_nilai'])) {
        $id = $_POST['idnilai'];
        $nilai = $_POST['nilai'];
        $keterangan = $_POST['keterangan'];
        $nilai_safe = mysqli_real_escape_string($conn, $nilai);
        $keterangan_safe = mysqli_real_escape_string($conn, $keterangan);
    
        if ($is_editing_member_ss) {
            $sql = "UPDATE tb_sspoin 
            SET original_nilaiss = CASE WHEN original_nilaiss IS NOT NULL THEN original_nilaiss ELSE nilaiss END,
                original_deskripsi = CASE WHEN original_deskripsi IS NOT NULL THEN original_deskripsi ELSE deskripsi END,
                nilaiss='$nilai_safe',
                deskripsi='$keterangan_safe',
                is_edited=1,
                edited_by=$id_user,
                edited_at=NOW()
            WHERE id_sspoin=$id";
        } else {
            $sql = "UPDATE tb_sspoin 
            SET nilaiss='$nilai_safe', deskripsi='$keterangan_safe' 
            WHERE id_sspoin=$id";
        }
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Gagal Memberikan Nilai')</script>";
        }
    }
    if (isset($_POST['update'])) {
    $idk = $_POST['idk'];
    $poinn = $_POST['poin'];
    $poinn_safe = mysqli_real_escape_string($conn, $poinn);

    if ($is_editing_member_ss) {
        $sql = "UPDATE `tb_ss` 
                SET original_poin_ss = CASE WHEN original_poin_ss IS NOT NULL THEN original_poin_ss ELSE poin_ss END,
                    poin_ss = '$poinn_safe',
                    is_edited = 1,
                    edited_by = $id_user,
                    edited_at = NOW()
                WHERE id_poinss=$idk";
    } else {
        $sql = "UPDATE `tb_ss` set poin_ss = '$poinn_safe' where id_poinss=$idk";
    }
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Edit Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Edit Poin')</script>";
    }
}
if (isset($_POST['hapus_kategori_ss'])) {
    $id = $_POST['id_kategori'];

    // Hapus semua poin di kategori ini terlebih dahulu
    $sql1 = "DELETE FROM tb_sspoin WHERE id_ss=$id";
    mysqli_query($conn, $sql1);
    
    // Kemudian hapus kategori
    $sql2 = "DELETE FROM tb_ss WHERE id_poinss=$id";
    $result = mysqli_query($conn, $sql2);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Hapus Kategori Skill Standard')</script>";
    } else {
        echo "<script>alert('Gagal Hapus Kategori')</script>";
    }
}

    if (isset($_POST['import_ss'])) {
        if (!$ss_import_ready || !class_exists('\PhpOffice\PhpSpreadsheet\IOFactory')) {
            $_SESSION['ss_import_message'] = [
                'type' => 'danger',
                'text' => 'Import Excel belum bisa digunakan karena dependency Composer belum terpasang. Jalankan composer install di folder KPI.'
            ];
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }

        if (!isset($_FILES['file_ss']) || $_FILES['file_ss']['error'] !== UPLOAD_ERR_OK) {
            $_SESSION['ss_import_message'] = [
                'type' => 'danger',
                'text' => 'Gagal upload file import.'
            ];
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }

        $allowed_extensions = ['xlsx', 'xls', 'csv'];
        $file_name = $_FILES['file_ss']['name'];
        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            $_SESSION['ss_import_message'] = [
                'type' => 'danger',
                'text' => 'Format file tidak didukung. Gunakan .xlsx, .xls, atau .csv.'
            ];
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        }

        try {
            $tipe_ss_import = in_array($_POST['tipe_ss'] ?? '', ['umum', 'teknis']) ? $_POST['tipe_ss'] : 'umum';
            $summary = ssImportSkillStandardForMember($conn, $id_sf, $id_user, $_FILES['file_ss']['tmp_name'], $is_editing_member_ss, $tipe_ss_import);
            $error_text = '';
            if (!empty($summary['errors'])) {
                $error_text = ' Detail: ' . implode(' ', array_slice($summary['errors'], 0, 5));
            }
            $_SESSION['ss_import_message'] = [
                'type' => $summary['imported'] > 0 ? 'success' : 'warning',
                'text' => "Import SS anggota selesai. Berhasil: {$summary['imported']}, dilewati/gagal: {$summary['skipped']}.$error_text"
            ];
        } catch (Throwable $e) {
            $_SESSION['ss_import_message'] = [
                'type' => 'danger',
                'text' => 'Gagal membaca file Excel. Pastikan format sesuai template.'
            ];
        }

        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit();
    }

ssSyncCurrentMonthHistory($conn, $id_sf);
$bulan_ini_ss = date('Y-m', strtotime('-1 month'));
$bulan_lalu_ss = date('Y-m', strtotime('-2 month'));
$label_bulan_ini_pendek_ss = ssShortMonthLabel($bulan_ini_ss);
$label_bulan_lalu_pendek_ss = ssShortMonthLabel($bulan_lalu_ss);
$ss_previous_scores = ssGetPreviousScores($conn, $id_sf, $bulan_lalu_ss);
$active_tab_ss = in_array($_GET['tab'] ?? '', ['umum', 'teknis']) ? $_GET['tab'] : 'umum';
} ?>
<html lang="en">

<head>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KPI Digital</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="KPI Digital">
    <meta name="author" content="Rvld">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="assets/css/adminlte.css">

    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />

    <script type="text/javascript" src="datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
    <style>
        .divider-line {
            height: 0;
            /* Menghilangkan tinggi default elemen */
            border-bottom: 1px solid #ccc;
            /* Membuat garis horizontal dengan ketebalan dan warna */
            margin: 20px 0;
            /* Menambahkan jarak di atas dan bawah */
            width: 100%;
            /* Mengatur lebar garis agar tidak penuh */
            margin-left: auto;
            /* Memusatkan garis secara horizontal */
            margin-right: auto;
        }
        .edited-badge {
            display: inline-block;
            background-color: #ffc107;
            color: #000;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 10px;
            font-weight: 700;
            margin-left: 6px;
        }
        .edited-row {
            background-color: #fff8e1 !important;
        }
        .change-info {
            margin-top: 4px;
            padding: 5px 7px;
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
            border-radius: 4px;
            font-size: 10px;
        }
        .change-info .old-val {
            color: #dc3545;
            text-decoration: line-through;
        }
        .change-info .new-val {
            color: #198754;
            font-weight: 700;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <?php if ($leveel == 7) { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="skill-standard-adminhrd" class="nav-link">Kembali</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="ssanggota" class="nav-link">Kembali</a>
                        </li>
                    <?php } ?>

                    <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#SSmodal"
                            class="nav-link">Tambah Poin SS</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#ImportSSAnggotaModal"
                            class="nav-link"><i class="bi bi-file-earmark-arrow-up"></i> Import SS</a> </li>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->

                <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png"
                                class="user-image rounded-circle shadow" alt="User Image"> <span
                                class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end"> <!--begin::User Image-->
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                                </center>
                            </li> <!--end::Menu Footer-->
                        </ul>
                    </li> <!--end::User Menu Dropdown-->
                </ul> <!--end::End Navbar Links-->

            </div> <!--end::Container-->
        </nav>
        <div class="modal fade" id="SSmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Poin Skill Standard </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" class="input">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Skill Standard</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_ss" id="tipe_umum" value="umum" <?= $active_tab_ss === 'umum' ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="tipe_umum"><span class="badge bg-primary">Umum</span></label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_ss" id="tipe_teknis" value="teknis" <?= $active_tab_ss === 'teknis' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="tipe_teknis"><span class="badge bg-success">Teknis</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="input-group mb-3">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Poin :</span>
                                <input type="input" class="form-control" name="poin" placeholder="Poin Skill Standard"
                                    aria-label="Poin Skill Standard" aria-describedby="poin">
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="input" name="submitSS" class="btn btn-primary">Tambah</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Import SS Anggota -->
        <div class="modal fade" id="ImportSSAnggotaModal" tabindex="-1" aria-labelledby="ImportSSAnggotaLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title fw-bold" id="ImportSSAnggotaLabel">
                            <i class="bi bi-file-earmark-spreadsheet me-1"></i>Import Skill Standard
                            <?php if ($is_editing_member_ss) { ?>
                                &mdash; <small class="fw-normal"><?= htmlspecialchars($rsd['nama_lngkp']); ?></small>
                            <?php } ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="modal-body">
                            <?php if ($is_editing_member_ss) { ?>
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                Anda sedang mengimport SS sebagai <strong>atasan</strong> untuk <strong><?= htmlspecialchars($rsd['nama_lngkp']); ?></strong>.
                                Data yang diimport akan ditandai <em>"DITAMBAH ATASAN"</em>.
                            </div>
                            <?php } ?>
                            <div class="alert alert-info">
                                <strong>Format kolom:</strong> Kategori SS, Poin SS, Nilai 1, Nilai 2, Nilai 3, Nilai 4, Nilai Bulan Ini, Deskripsi Penilaian.
                                Baris pertama adalah header dan data dimulai dari baris kedua.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Jenis Skill Standard</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_ss" id="import_tipe_umum" value="umum" <?= $active_tab_ss === 'umum' ? 'checked' : ''; ?> required>
                                        <label class="form-check-label" for="import_tipe_umum"><span class="badge bg-primary">Umum</span></label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="tipe_ss" id="import_tipe_teknis" value="teknis" <?= $active_tab_ss === 'teknis' ? 'checked' : ''; ?>>
                                        <label class="form-check-label" for="import_tipe_teknis"><span class="badge bg-success">Teknis</span></label>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">File Excel</label>
                                <input type="file" class="form-control" name="file_ss" accept=".xlsx,.xls,.csv" required>
                            </div>
                            <a href="assets/template/template_import_skill_standard.xlsx" class="btn btn-outline-primary btn-sm" download>
                                <i class="bi bi-download me-1"></i>Download Template
                            </a>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" name="import_ss" class="btn btn-success">
                                <i class="bi bi-upload me-1"></i>Import
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <?php include("pages/part/p_aside.php"); ?>
        <div class="m-3">
            <div class="container-fluid" style="font-size:13px;">
                <div class="card mb-3">
                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-danger">
                        <h5 style="color:white;" class="card-title fw-bolder">Profil Karyawan</h5>
                        <div class="card-tools">
                            <!-- <button style="color: white;" type="button" class="btn btn-tool">
                    <i class="bi bi-pencil"></i>
                </button> -->
                            <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="input-group mb-1 w-75" style="margin-right: 20px;">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="nama-addon">Nama : </span>
                                <input disabled type="text" value="<?php echo $rsd['nama_lngkp']; ?>" class="form-control" placeholder="Nama"
                                    aria-label="Nama" aria-describedby="nama-addon">
                            </div>
                            <div class="input-group mb-1">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="depart-addon">Departement :</span>
                                <input disabled type="text" value="<?php echo $rsd['departement']; ?>" class="form-control"
                                    placeholder="Departement" aria-label="Departement" aria-describedby="depart-addon">
                            </div>
                        </div>
                    </div>
                </div>
                <?php if (isset($_SESSION['ss_import_message'])) { ?>
                    <div class="alert alert-<?= $_SESSION['ss_import_message']['type']; ?> alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['ss_import_message']['text']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['ss_import_message']); ?>
                <?php } ?>

                <!-- Tabs Umum / Teknis -->
                <ul class="nav nav-tabs mb-3">
                    <li class="nav-item">
                        <a class="nav-link fw-bold <?= $active_tab_ss === 'umum' ? 'active' : ''; ?>" href="ssanggotadetail?id=<?= $id_sf ?>&tab=umum">
                            <i class="bi bi-person-check me-1"></i>Skill Standard Umum
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link fw-bold <?= $active_tab_ss === 'teknis' ? 'active' : ''; ?>" href="ssanggotadetail?id=<?= $id_sf ?>&tab=teknis">
                            <i class="bi bi-tools me-1"></i>Skill Standard Teknis
                        </a>
                    </li>
                </ul>

                <?php
                $no = 1;
                $tipe_filter = mysqli_real_escape_string($conn, $active_tab_ss);
                $sqler = "select * from tb_ss where id_user=$id_sf AND tipe_ss='$tipe_filter'";
                $tewg = mysqli_query($conn, $sqler);
                while ($hasil = mysqli_fetch_assoc($tewg)) {
                    $current_category_average = ssGetAverage($conn, $id_sf, $hasil['id_poinss']);
                    $previous_category_average = ssGetAverage($conn, $id_sf, $hasil['id_poinss'], $bulan_lalu_ss);
                    $is_category_edited_by_superior = !empty($hasil['is_edited']) && !empty($hasil['edited_by']) && intval($hasil['edited_by']) !== intval($id_sf);
                    $category_editor_name = $is_category_edited_by_superior ? getSSEditorName($conn, $hasil['edited_by']) : '';
                    $category_edit_label = empty($hasil['original_poin_ss']) ? 'DITAMBAH ATASAN' : 'DIUBAH ATASAN';
                ?>
                    <div class="row">
                        <div class="col-lg connectedSortable">
                            <div class="d-flex">
                                <div class="card mb-4 w-100">
                                    <div class="card-header bg-primary d-flex align-items-center justify-content-between gap-2 flex-wrap" style="min-height: 52px;">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h5 style="color:white;" class="card-title mb-0">
                                                <?= $no . '. ' . $hasil['poin_ss']; ?>
                                            </h5>
                                            <span class="badge text-bg-warning fw-bolder">
                                                Ini (<?= $label_bulan_ini_pendek_ss; ?>): <?= ssFormatValue($current_category_average); ?>
                                            </span>
                                            <span class="badge text-bg-light fw-bolder">
                                                Lalu (<?= $label_bulan_lalu_pendek_ss; ?>): <?= ssFormatValue($previous_category_average); ?>
                                            </span>
                                            <?= ssTrendBadge($current_category_average ?? 0, $previous_category_average); ?>
                                            <?php if ($is_category_edited_by_superior) { ?>
                                                <span class="edited-badge" title="Diubah <?= !empty($hasil['edited_at']) ? date('d/m/Y H:i', strtotime($hasil['edited_at'])) : ''; ?><?= !empty($category_editor_name) ? ' oleh ' . htmlspecialchars($category_editor_name) : ''; ?>">
                                                    <i class="bi bi-pencil-fill"></i> <?= $category_edit_label; ?>
                                                </span>
                                            <?php } ?>
                                        </div>
                                        <div class="card-tools d-flex align-items-center gap-1 ms-auto">
                                            <button style="color: white;" type="button"
                                                data-bs-toggle="modal" data-bs-target="#EditASSS<?= $hasil['id_poinss']; ?>" class="btn btn-tool">
                                                <i class="bi bi-pencil fs-6"></i>
                                            </button>
                                            <button style="color: white;"
                                                type="button" data-bs-toggle="dropdown" 
                                                class="btn btn-tool dropdown-toggle">
                                                <i class="bi bi-plus-circle fs-6"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#TambahSS<?= $hasil['id_poinss']; ?>">Tambah Poin</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#HapusKategoriSS<?= $hasil['id_poinss']; ?>">Hapus Kategori</a>
                                            </div>
                                            <button style="color: white;" type="button" class="btn btn-tool"
                                                data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 5%">No</th>
                                                    <th style="padding-left : 30px">Poin</th>
                                                    <th style="width: 10%">
                                                        <center>Bulan Lalu (<?= $label_bulan_lalu_pendek_ss; ?>)</center>
                                                    </th>
                                                    <th style="width: 10%">
                                                        <center>Bulan Ini (<?= $label_bulan_ini_pendek_ss; ?>)</center>
                                                    </th>
                                                    <th style="width: 10%">
                                                        <center>Selisih</center>
                                                    </th>
                                                    <th style="width: 25%">
                                                        <center>Deskripsi</center>
                                                    </th>
                                                    <th style="width: 5%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql1 = "SELECT * FROM tb_sspoin WHERE id_user='$id_sf' AND id_ss='" . $hasil['id_poinss'] . "'";
                                                $ql = mysqli_query($conn, $sql1);
                                                $nodd = 1;
                                                while ($res = mysqli_fetch_assoc($ql)) {
                                                    $is_edited_by_superior = !empty($res['is_edited']) && !empty($res['edited_by']) && intval($res['edited_by']) !== intval($id_sf);
                                                    $row_class = $is_edited_by_superior ? 'edited-row' : '';
                                                    $has_original_change = !empty($res['original_poinss'])
                                                        || $res['original_nilaiss'] !== null
                                                        || $res['original_deskripsi'] !== null;
                                                    $previous_score = array_key_exists($res['id_sspoin'], $ss_previous_scores)
                                                        ? $ss_previous_scores[$res['id_sspoin']]
                                                        : null;
                                                ?>
                                                    <tr class="align-middle <?= $row_class ?>">
                                                        <td><?= $no . '.' . $nodd ?></td>
                                                        <td>
                                                            <?= $res['poinss']; ?>
                                                            <?php if ($is_edited_by_superior) { ?>
                                                                <span class="edited-badge">
                                                                    <i class="bi bi-pencil-fill"></i> <?= $has_original_change ? 'DIUBAH ATASAN' : 'DITAMBAH ATASAN'; ?>
                                                                </span>
                                                            <?php } ?>
                                                            <?php if ($is_edited_by_superior && !empty($res['original_poinss']) && $res['original_poinss'] != $res['poinss']) { ?>
                                                                <div class="change-info">
                                                                    <strong>Sebelum:</strong>
                                                                    <span class="old-val"><?= shortSSValue($res['original_poinss']); ?></span><br>
                                                                    <strong>Sesudah:</strong>
                                                                    <span class="new-val"><?= shortSSValue($res['poinss']); ?></span>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?php if ($previous_score !== null) { ?>
                                                                    <span class="badge bg-secondary fs-8">
                                                                        <?= number_format($previous_score, 2); ?>
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-light text-dark fs-8">N/A</span>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?php if ($res['nilaiss'] != 0) { ?>
                                                                    <span class="badge bg-success fs-8">
                                                                        <?= number_format($res['nilaiss'], 2); ?>
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-warning fs-8">Belum Dinilai</span>
                                                                <?php } ?>
                                                                <?php if ($is_edited_by_superior && $res['original_nilaiss'] !== null && $res['original_nilaiss'] != $res['nilaiss']) { ?>
                                                                    <div class="change-info" style="text-align:left;">
                                                                        <span class="old-val"><?= number_format($res['original_nilaiss'], 2); ?></span>
                                                                        &rarr;
                                                                        <span class="new-val"><?= number_format($res['nilaiss'], 2); ?></span>
                                                                    </div>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?= ssTrendBadge($res['nilaiss'], $previous_score); ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($res['deskripsi'])) { ?>
                                                                <small><?= $res['deskripsi']; ?></small>
                                                            <?php } else { ?>
                                                                <small class="text-muted fst-italic">Belum ada deskripsi. Klik "Nilai" untuk menambahkan.</small>
                                                            <?php } ?>
                                                            <?php if ($is_edited_by_superior && $res['original_deskripsi'] !== null && $res['original_deskripsi'] != $res['deskripsi']) { ?>
                                                                <div class="change-info">
                                                                    <strong>Sebelum:</strong>
                                                                    <span class="old-val"><?= shortSSValue($res['original_deskripsi']); ?></span><br>
                                                                    <strong>Sesudah:</strong>
                                                                    <span class="new-val"><?= shortSSValue($res['deskripsi']); ?></span>
                                                                </div>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown"
                                                                class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#NilaiSSS<?= $res['id_sspoin'] ?>">Nilai</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a name="how_edit"
                                                                    class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#EditSSS<?= $res['id_sspoin'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusSSS<?= $res['id_sspoin'] ?>">Hapus</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <!-- Modal Edit Poin SS -->
                                                    <div class="modal fade" id="EditSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="EditModalLabel">Edit Poin Skill Standard</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input type="hidden" value="<?= $res['id_sspoin']; ?>" name="idsss">
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Poin :</span>
                                                                            <input type="text" value="<?= $res['poinss']; ?>" class="form-control" 
                                                                                name="poinsss" placeholder="Poin SS" required>
                                                                        </div>
                                                                        
                                                                        <div class="alert alert-info">
                                                                            <i class="bi bi-info-circle"></i> Untuk mengubah nilai, gunakan menu "Nilai"
                                                                        </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" name="ss_edit" class="btn btn-primary">Simpan</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Nilai Skill Standard -->
                                                    <div class="modal fade" id="NilaiSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="NilaiModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title fw-bold" id="NilaiModalLabel">
                                                                        <i class="bi bi-star-fill"></i> Beri Nilai Skill Standard
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input type="hidden" value="<?= $res['id_sspoin']; ?>" name="idnilai">
                                                                        
                                                                        <div class="alert alert-info">
                                                                            <strong>Poin:</strong> <?= $res['poinss']; ?>
                                                                        </div>
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color:#343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                                            <input type="number" 
                                                                                step="0.01" 
                                                                                min="0"
                                                                                max="4"
                                                                                value="<?= $res['nilaiss'] != 0 ? $res['nilaiss'] : ''; ?>" 
                                                                                class="form-control" 
                                                                                name="nilai" 
                                                                                placeholder="Masukkan nilai 1-4"
                                                                                required>
                                                                        </div>
                                                                        
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Keterangan / Next Step :</label>
                                                                            <textarea class="form-control" name="keterangan" rows="5" 
                                                                                    placeholder="Jelaskan pencapaian, bukti, atau alasan pemberian nilai ini..." 
                                                                                    required><?= !empty($res['deskripsi']) ? $res['deskripsi'] : ''; ?></textarea>
                                                                        </div>
                                                                        
                                                                        <!-- Tampilkan Indikator Penilaian -->
                                                                        <?php if (!empty($res['nilai1']) || !empty($res['nilai2']) || !empty($res['nilai3']) || !empty($res['nilai4'])) { ?>
                                                                        <div class="alert alert-light border">

                                                                            <h5 class="fw-bold mb-3">
                                                                                <i class="bi bi-bar-chart-fill me-1"></i> Indikator Penilaian
                                                                            </h5>

                                                                            <?php if (!empty($res['nilai1'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge bg-danger me-2">Nilai 1</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai1']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                            <?php if (!empty($res['nilai2'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge me-2" style="background-color:#fd7e14; color:white;">Nilai 2</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai2']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                            <?php if (!empty($res['nilai3'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge bg-warning me-2">Nilai 3</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai3']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                            <?php if (!empty($res['nilai4'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge bg-success me-2">Nilai 4</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai4']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <?php } ?>

                                                                        
                                                                        <div class="alert alert-warning">
                                                                            <i class="bi bi-info-circle"></i> Nilai dan keterangan yang Anda berikan dapat diubah kapan saja
                                                                        </div>
                                                                        
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="ss_nilai" class="btn btn-primary">
                                                                        <i class="bi bi-save"></i> Simpan Nilai
                                                                    </button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="HapusSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="EditModalLabel"><?= $res['poinss']; ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input hidden type="input" value="<?= $res['id_sspoin']; ?>" class="form-control" name="idpoin">
                                                                        <div class="container">
                                                                            <p>Apa Kamu Yakin Hapus Poin Ini?</p>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit" name="ss_hapus" class="btn btn-danger">Hapus</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    <?php $nodd++;
                                                } ?>

                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php include('pages/kpi/k_modalTambahSS.php'); ?>
                    <div class="modal fade" id="EditASSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="EditModalLabel">Edit Poin Skill Standard</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" class="input">
                                <input type="input" value="<?= $hasil['id_poinss'];?>" class="form-control" name="idk" hidden>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Poin :</span>
                                        <input type="input" value="<?= $hasil['poin_ss'];?>" class="form-control" name="poin" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="poin">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="input" name="update" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                    <!-- Modal Hapus Kategori -->
                    <div class="modal fade" id="HapusKategoriSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="HapusKategoriLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold" id="HapusKategoriLabel">Konfirmasi Hapus Kategori</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <input hidden type="input" value="<?= $hasil['id_poinss']; ?>" name="id_kategori">
                                        <div class="container">
                                            <p class="fw-bold"><?= $hasil['poin_ss']; ?></p>
                                            <div class="alert alert-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Menghapus kategori akan menghapus <strong>semua poin</strong> di dalamnya!
                                            </div>
                                            <p>Apakah Anda yakin ingin menghapus kategori ini beserta seluruh poinnya?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="hapus_kategori_ss" class="btn btn-danger">Ya, Hapus Kategori</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php
                    $no++;
                } ?>
            </div>
        </div>
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>

</html>
