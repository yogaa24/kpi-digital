<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

function karakterQuestions()
{
    return [
        ['kategori' => 'Tanggung jawab', 'poin' => 'Tidak mengeluh', 'ideal' => 'Tidak', 'tanya' => 'Apakah ybs masih mengeluh ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin mengeluh'],
        ['kategori' => 'Tanggung jawab', 'poin' => 'Tidak menyalahkan', 'ideal' => 'Tidak', 'tanya' => 'Apakah Ybs masih menyalahkan ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin menyalahkan'],
        ['kategori' => 'Tanggung jawab', 'poin' => 'Selalu berpegang pada hasil', 'ideal' => 'Ya', 'tanya' => 'Apakah Ybs Selalu berpegang kepada hasil ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Selalu berpegang kepada hasil'],
        ['kategori' => 'Tanggung jawab', 'poin' => 'Terus melakukan perbaikan', 'ideal' => 'Ya', 'tanya' => 'Apakah Ybs Terus melakukan perbaikan ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Terus melakukan perbaikan'],
        ['kategori' => 'Persisten', 'poin' => 'Daya juang tinggi', 'ideal' => 'Ya', 'tanya' => 'Apakah Ybs memiliki daya juang tinggi ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Daya Juang Tinggi'],
        ['kategori' => 'Persisten', 'poin' => 'Tidak mudah menyerah', 'ideal' => 'Ya', 'tanya' => 'Apakah Ybs tidak mudah menyerah ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Tidak mudah menyerah'],
        ['kategori' => 'Persisten', 'poin' => 'Tidak mudah dijatuhkan', 'ideal' => 'Ya', 'tanya' => 'Apakah Ybs tidak mudah dijatuhkan ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Tidak mudah dijatuhkan'],
        ['kategori' => 'Komunikasi', 'poin' => 'Berani mengungkapkan kejujuran apa adanya', 'ideal' => 'Ya', 'tanya' => 'Apakah dalam berkomunikasi ybs berani mengungkapkan kejujuran apa adanya ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Komunikasi "Berani mengungkapkan kejujuran apa adanya"'],
        ['kategori' => 'Komunikasi', 'poin' => 'Keterbukaan', 'ideal' => 'Ya', 'tanya' => 'Apakah dalam berkomunikasi ybs sdh menerapkan "Keterbukaan" ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Komunikasi "Keterbukaan"'],
        ['kategori' => 'Komunikasi', 'poin' => 'Tidak defensif', 'ideal' => 'Ya', 'tanya' => 'Apakah dalam berkomunikasi ybs "Tidak Defensif" ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Komunikasi "Tidak Defensif"'],
        ['kategori' => 'Realistis', 'poin' => 'Memilih dan bertindak menghadapi hambatan', 'ideal' => 'Ya', 'tanya' => 'Apakah ybs memilih dan bertindak untuk menghadapi hambatan atau kesulitan, bukan menghindar atau menunggu ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Realistis'],
    ];
}

function karakterCategories($questions)
{
    $categories = [];
    foreach ($questions as $question) {
        $categories[$question['kategori']] = $question['kategori'];
    }
    return array_values($categories);
}

function karakterScoreResponse($response, $questions)
{
    $score = [
        'total' => 0,
        'submitted' => !empty($response['submitted_at']),
        'categories' => []
    ];

    foreach (karakterCategories($questions) as $category) {
        $score['categories'][$category] = ['correct' => 0, 'total' => 0, 'score' => 0];
    }

    if (!$score['submitted']) {
        return $score;
    }

    foreach ($questions as $index => $question) {
        $number = $index + 1;
        $category = $question['kategori'];
        $answer = $response['q' . $number . '_jawaban'] ?? null;

        $score['categories'][$category]['total']++;
        if ($answer === $question['ideal']) {
            $score['categories'][$category]['correct']++;
        }
    }

    foreach ($score['categories'] as $category => $category_score) {
        if ($category_score['total'] > 0) {
            $score['categories'][$category]['score'] = $category_score['correct'] / $category_score['total'];
            $score['total'] += $score['categories'][$category]['score'];
        }
    }

    return $score;
}

function karakterAverageScores($rows, $questions)
{
    $summary = [
        'count' => 0,
        'total' => null,
        'categories' => []
    ];

    foreach (karakterCategories($questions) as $category) {
        $summary['categories'][$category] = null;
    }

    $category_totals = [];
    foreach ($summary['categories'] as $category => $value) {
        $category_totals[$category] = 0;
    }

    $total = 0;
    foreach ($rows as $row) {
        $score = karakterScoreResponse($row, $questions);
        if (!$score['submitted']) {
            continue;
        }

        $summary['count']++;
        $total += $score['total'];
        foreach ($score['categories'] as $category => $category_score) {
            $category_totals[$category] += $category_score['score'];
        }
    }

    if ($summary['count'] > 0) {
        $summary['total'] = $total / $summary['count'];
        foreach ($category_totals as $category => $category_total) {
            $summary['categories'][$category] = $category_total / $summary['count'];
        }
    }

    return $summary;
}

function karakterFormatScore($value)
{
    return $value === null ? '-' : number_format((float) $value, 2);
}

function karakterTrendBadge($current, $previous)
{
    if ($current === null || $previous === null) {
        return '<span class="badge bg-secondary">N/A</span>';
    }

    $difference = $current - $previous;
    if ($difference > 0) {
        return '<span class="badge bg-success">+' . number_format($difference, 2) . '</span>';
    }
    if ($difference < 0) {
        return '<span class="badge bg-danger">' . number_format($difference, 2) . '</span>';
    }

    return '<span class="badge bg-secondary">0.00</span>';
}

function karakterFetchAssignmentRows($conn, $nama_atasan, $bulan)
{
    $nama_atasan = mysqli_real_escape_string($conn, $nama_atasan);
    $bulan = mysqli_real_escape_string($conn, $bulan);
    $rows = [];
    $by_user = [];

    $result = mysqli_query($conn, "SELECT a.id_assignment, a.id_user_dinilai, a.id_penilai, a.status, dinilai.nama_lngkp AS nama_dinilai, dinilai.bagian AS bagian_dinilai,
            dinilai.departement AS departement_dinilai, penilai.nama_lngkp AS nama_penilai, penilai.bagian AS bagian_penilai,
            penilai.departement AS departement_penilai,
            r.submitted_at,
            r.q1_jawaban, r.q1_fakta, r.q2_jawaban, r.q2_fakta, r.q3_jawaban, r.q3_fakta,
            r.q4_jawaban, r.q4_fakta, r.q5_jawaban, r.q5_fakta, r.q6_jawaban, r.q6_fakta,
            r.q7_jawaban, r.q7_fakta, r.q8_jawaban, r.q8_fakta, r.q9_jawaban, r.q9_fakta,
            r.q10_jawaban, r.q10_fakta, r.q11_jawaban, r.q11_fakta
        FROM tb_penilaian_karakter_assignment a
        INNER JOIN tb_users dinilai ON dinilai.id = a.id_user_dinilai
        INNER JOIN tb_users penilai ON penilai.id = a.id_penilai
        LEFT JOIN tb_penilaian_karakter_response r ON r.id_assignment = a.id_assignment AND r.bulan = '$bulan'
        WHERE dinilai.atasan = '$nama_atasan' AND a.status = 'aktif'
        ORDER BY dinilai.nama_lngkp, penilai.nama_lngkp");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
            $by_user[$row['id_user_dinilai']][] = $row;
        }
    }

    return ['rows' => $rows, 'by_user' => $by_user];
}

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function karakterEnsureTables($conn)
{
    $assignment = mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_penilaian_karakter_assignment` (
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

    return $assignment && $response;
}

function karakterCanManageUser($conn, $id_user_dinilai, $nama_atasan)
{
    $id_user_dinilai = intval($id_user_dinilai);
    $nama_atasan = mysqli_real_escape_string($conn, $nama_atasan);
    $result = mysqli_query($conn, "SELECT id FROM tb_users WHERE id = $id_user_dinilai AND atasan = '$nama_atasan' LIMIT 1");
    return $result && mysqli_num_rows($result) > 0;
}

function karakterFlash($type, $text)
{
    $_SESSION['karakter_message'] = ['type' => $type, 'text' => $text];
}

function karakterRedirect()
{
    header('Location: penilaian-karakter');
    exit();
}

karakterEnsureTables($conn);

$questions = karakterQuestions();
$categories = karakterCategories($questions);
$id_user_login = intval($id_user);
$bulan_penilaian = date('Y-m');
$bulan_lalu = date('Y-m', strtotime($bulan_penilaian . '-01 -1 month'));
$can_manage = in_array($jabatan, ['Manager', 'Kadep', 'Koordinator', 'Direktur', 'Wadir Utama', 'Kabag']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['tambah_penilai'])) {
        $id_user_dinilai = intval($_POST['id_user_dinilai'] ?? 0);
        $id_penilai_values = $_POST['id_penilai'] ?? [];
        if (!is_array($id_penilai_values)) {
            $id_penilai_values = [$id_penilai_values];
        }

        $id_penilai_list = [];
        foreach ($id_penilai_values as $id_penilai_value) {
            $id_penilai_value = intval($id_penilai_value);
            if ($id_penilai_value > 0 && $id_penilai_value !== $id_user_dinilai) {
                $id_penilai_list[$id_penilai_value] = $id_penilai_value;
            }
        }

        if (!$can_manage || !karakterCanManageUser($conn, $id_user_dinilai, $nama_lngkp)) {
            karakterFlash('danger', 'Anda tidak memiliki akses mengatur penilai untuk user ini.');
            karakterRedirect();
        }

        if ($id_user_dinilai <= 0) {
            karakterFlash('warning', 'Pilih anggota yang valid.');
            karakterRedirect();
        }

        $id_penilai_sql = implode(',', $id_penilai_list);
        if (!empty($id_penilai_list)) {
            $check_penilai = mysqli_query($conn, "SELECT id FROM tb_users WHERE id IN ($id_penilai_sql)");
            if (!$check_penilai || mysqli_num_rows($check_penilai) !== count($id_penilai_list)) {
                karakterFlash('warning', 'Sebagian data penilai tidak ditemukan.');
                karakterRedirect();
            }
        }

        $saved_count = 0;
        foreach ($id_penilai_list as $id_penilai) {
            $sql = "INSERT INTO tb_penilaian_karakter_assignment (id_user_dinilai, id_penilai, id_atasan, status)
                    VALUES ($id_user_dinilai, $id_penilai, $id_user_login, 'aktif')
                    ON DUPLICATE KEY UPDATE status = 'aktif', id_atasan = VALUES(id_atasan)";
            if (mysqli_query($conn, $sql)) {
                $saved_count++;
            }
        }

        if (!empty($id_penilai_list)) {
            mysqli_query($conn, "UPDATE tb_penilaian_karakter_assignment
                SET status = 'nonaktif', id_atasan = $id_user_login
                WHERE id_user_dinilai = $id_user_dinilai AND id_penilai NOT IN ($id_penilai_sql)");
        } else {
            mysqli_query($conn, "UPDATE tb_penilaian_karakter_assignment
                SET status = 'nonaktif', id_atasan = $id_user_login
                WHERE id_user_dinilai = $id_user_dinilai");
        }

        karakterFlash($saved_count > 0 || empty($id_penilai_list) ? 'success' : 'danger', empty($id_penilai_list) ? 'Semua penilai anggota ini dinonaktifkan.' : "$saved_count penilai berhasil disimpan.");
        karakterRedirect();
    }

    if (isset($_POST['hapus_penilai'])) {
        $id_assignment = intval($_POST['id_assignment'] ?? 0);
        if (!$can_manage) {
            karakterFlash('danger', 'Anda tidak memiliki akses mengatur penilai.');
            karakterRedirect();
        }

        $sql = "UPDATE tb_penilaian_karakter_assignment a
                INNER JOIN tb_users u ON u.id = a.id_user_dinilai
                SET a.status = 'nonaktif'
                WHERE a.id_assignment = $id_assignment AND u.atasan = '$nama_lngkp'";
        $deleted = mysqli_query($conn, $sql);
        karakterFlash($deleted ? 'success' : 'danger', $deleted ? 'Penilai dinonaktifkan.' : 'Gagal menonaktifkan penilai.');
        karakterRedirect();
    }

    if (isset($_POST['simpan_penilaian'])) {
        $id_assignment = intval($_POST['id_assignment'] ?? 0);
        $assignment_check = mysqli_query($conn, "SELECT id_assignment FROM tb_penilaian_karakter_assignment WHERE id_assignment = $id_assignment AND id_penilai = $id_user_login AND status = 'aktif' LIMIT 1");
        if (!$assignment_check || mysqli_num_rows($assignment_check) === 0) {
            karakterFlash('danger', 'Permintaan penilaian tidak ditemukan.');
            karakterRedirect();
        }

        $columns = [];
        $values = [];
        $updates = [];
        for ($i = 1; $i <= count($questions); $i++) {
            $jawaban = ($_POST['q' . $i . '_jawaban'] ?? '') === 'Ya' ? 'Ya' : 'Tidak';
            $fakta = mysqli_real_escape_string($conn, trim($_POST['q' . $i . '_fakta'] ?? ''));
            if ($fakta === '') {
                karakterFlash('warning', 'Semua fakta penilaian wajib diisi.');
                karakterRedirect();
            }

            $columns[] = "q{$i}_jawaban";
            $columns[] = "q{$i}_fakta";
            $values[] = "'$jawaban'";
            $values[] = "'$fakta'";
            $updates[] = "q{$i}_jawaban = VALUES(q{$i}_jawaban)";
            $updates[] = "q{$i}_fakta = VALUES(q{$i}_fakta)";
        }

        $column_sql = implode(', ', $columns);
        $value_sql = implode(', ', $values);
        $update_sql = implode(', ', $updates);
        $sql = "INSERT INTO tb_penilaian_karakter_response (id_assignment, bulan, $column_sql, submitted_at)
                VALUES ($id_assignment, '$bulan_penilaian', $value_sql, NOW())
                ON DUPLICATE KEY UPDATE $update_sql, submitted_at = NOW()";
        $submitted = mysqli_query($conn, $sql);
        karakterFlash($submitted ? 'success' : 'danger', $submitted ? 'Penilaian karakter berhasil disimpan.' : 'Gagal menyimpan penilaian karakter.');
        karakterRedirect();
    }
}

$anggota_rows = [];
$anggota_result = mysqli_query($conn, "SELECT id, nama_lngkp, bagian, departement, jabatan FROM tb_users WHERE atasan = '$nama_lngkp' ORDER BY nama_lngkp");
if ($anggota_result) {
    while ($anggota = mysqli_fetch_assoc($anggota_result)) {
        $anggota_rows[] = $anggota;
    }
}

$users_rows = [];
$departemen_penilai_rows = [];
$users_result = mysqli_query($conn, "SELECT id, nama_lngkp, bagian, departement, jabatan FROM tb_users WHERE jabatan != 'Admin HRD' ORDER BY nama_lngkp");
if ($users_result) {
    while ($user_row = mysqli_fetch_assoc($users_result)) {
        $users_rows[] = $user_row;
        if (!empty($user_row['departement'])) {
            $departemen_penilai_rows[$user_row['departement']] = $user_row['departement'];
        }
    }
}
sort($departemen_penilai_rows);

$assignment_data = karakterFetchAssignmentRows($conn, $nama_lngkp, $bulan_penilaian);
$assignment_rows = $assignment_data['rows'];
$assignments_by_user = $assignment_data['by_user'];

$assignment_data_lalu = karakterFetchAssignmentRows($conn, $nama_lngkp, $bulan_lalu);
$assignment_rows_lalu = $assignment_data_lalu['rows'];
$assignments_by_user_lalu = $assignment_data_lalu['by_user'];

$karakter_summary_by_user = [];
foreach ($anggota_rows as $anggota) {
    $id_anggota = $anggota['id'];
    $karakter_summary_by_user[$id_anggota] = [
        'current' => karakterAverageScores($assignments_by_user[$id_anggota] ?? [], $questions),
        'previous' => karakterAverageScores($assignments_by_user_lalu[$id_anggota] ?? [], $questions)
    ];
}

$requests_result = mysqli_query($conn, "SELECT a.id_assignment, dinilai.nama_lngkp AS nama_dinilai, dinilai.bagian, dinilai.departement, dinilai.jabatan,
        r.submitted_at,
        r.q1_jawaban, r.q1_fakta, r.q2_jawaban, r.q2_fakta, r.q3_jawaban, r.q3_fakta,
        r.q4_jawaban, r.q4_fakta, r.q5_jawaban, r.q5_fakta, r.q6_jawaban, r.q6_fakta,
        r.q7_jawaban, r.q7_fakta, r.q8_jawaban, r.q8_fakta, r.q9_jawaban, r.q9_fakta,
        r.q10_jawaban, r.q10_fakta, r.q11_jawaban, r.q11_fakta
    FROM tb_penilaian_karakter_assignment a
    INNER JOIN tb_users dinilai ON dinilai.id = a.id_user_dinilai
    LEFT JOIN tb_penilaian_karakter_response r ON r.id_assignment = a.id_assignment AND r.bulan = '$bulan_penilaian'
    WHERE a.id_penilai = $id_user_login AND a.status = 'aktif'
    ORDER BY r.submitted_at IS NULL DESC, dinilai.nama_lngkp");
?>
<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"><a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a></li>
                    <li class="nav-item d-none d-md-block"><a href="skillstandard" class="nav-link">Kembali</a></li>
                    <li class="nav-item d-none d-md-block"><a href="ssanggota" class="nav-link">SS Anggota</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img style="margin-top: -2px;" src="assets/img/profile.png" class="user-image rounded-circle shadow" alt="User Image">
                            <span class="d-none d-md-inline"><?= h($username); ?></span>
                        </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer"><center><a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a></center></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <?php include("pages/part/p_aside.php"); ?>

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid py-4" style="font-size:13px;">
                    <?php if (isset($_SESSION['karakter_message'])) { ?>
                        <div class="alert alert-<?= h($_SESSION['karakter_message']['type']); ?> alert-dismissible fade show" role="alert">
                            <?= h($_SESSION['karakter_message']['text']); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <?php unset($_SESSION['karakter_message']); ?>
                    <?php } ?>

                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                        <div>
                            <h4 class="fw-bold mb-1">Penilaian Karakter</h4>
                            <div class="text-muted">Periode <?= h(date('m/Y')); ?></div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white"
                                     style="cursor:pointer; position:relative; padding-right:3rem;"
                                     data-bs-toggle="collapse"
                                     data-bs-target="#collapsePermintaanPenilaian"
                                     aria-expanded="<?= $can_manage ? 'false' : 'true'; ?>"
                                     aria-controls="collapsePermintaanPenilaian">
                                    <h5 class="card-title mb-0">
                                        <i class="bi bi-inbox"></i> Permintaan Penilaian Saya
                                        <?php
                                        $pending_count = 0;
                                        $total_requests = 0;
                                        if ($requests_result && mysqli_num_rows($requests_result) > 0) {
                                            $requests_result->data_seek(0);
                                            while ($tmp = mysqli_fetch_assoc($requests_result)) {
                                                $total_requests++;
                                                if (empty($tmp['submitted_at'])) $pending_count++;
                                            }
                                            $requests_result->data_seek(0);
                                        }
                                        if ($total_requests > 0) {
                                            ?>
                                                <span class="badge bg-white text-dark ms-2"><?= $total_requests; ?> permintaan</span>
                                            <?php
                                        }
                                        if ($pending_count > 0) {
                                            ?>
                                                <span class="badge bg-warning text-dark ms-1"><?= $pending_count; ?> menunggu</span>
                                            <?php
                                        } ?>
                                    </h5>
                                    <i class="bi bi-chevron-down collapse-chevron" style="position:absolute; right:1rem; top:50%; transform:translateY(-50%); transition:transform .2s; <?= $can_manage ? '' : 'transform:translateY(-50%) rotate(180deg);' ?>"></i>
                                </div>
                                <div class="collapse <?= $can_manage ? '' : 'show'; ?>" id="collapsePermintaanPenilaian">
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Nama yang Dinilai</th>
                                                        <th>Bagian</th>
                                                        <th>Departemen</th>
                                                        <th>Status</th>
                                                        <th width="12%" style="text-align:right; padding-right:16px;">Aksi</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if ($requests_result && mysqli_num_rows($requests_result) > 0) { ?>
                                                        <?php while ($request = mysqli_fetch_assoc($requests_result)) { ?>
                                                            <tr>
                                                                <td><?= h($request['nama_dinilai']); ?></td>
                                                                <td><?= h($request['bagian']); ?></td>
                                                                <td><?= h($request['departement']); ?></td>
                                                                <td>
                                                                    <?php if (!empty($request['submitted_at'])) { ?>
                                                                        <span class="badge bg-success">Sudah dinilai</span>
                                                                        <small class="text-muted d-block"><?= h(date('d/m/Y H:i', strtotime($request['submitted_at']))); ?></small>
                                                                    <?php } else { ?>
                                                                        <span class="badge bg-warning text-dark">Menunggu</span>
                                                                    <?php } ?>
                                                                </td>
                                                                <td style="vertical-align:middle; padding:6px 12px;">
                                                                    <div style="display:flex; justify-content:flex-end;">
                                                                        <button class="btn btn-success btn-sm"
                                                                                data-bs-toggle="modal"
                                                                                data-bs-target="#nilaiKarakter<?= intval($request['id_assignment']); ?>">
                                                                            <i class="bi bi-pencil-square me-1"></i> Nilai
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>

                                                            <div class="modal fade" id="nilaiKarakter<?= intval($request['id_assignment']); ?>" tabindex="-1" aria-hidden="true">
                                                                <div class="modal-dialog modal-xl modal-dialog-scrollable">
                                                                    <div class="modal-content">
                                                                        <div class="modal-header bg-success text-white">
                                                                            <h5 class="modal-title fw-bold">Penilaian Karakter - <?= h($request['nama_dinilai']); ?></h5>
                                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                        </div>
                                                                        <form method="POST" action="">
                                                                            <input type="hidden" name="id_assignment" value="<?= intval($request['id_assignment']); ?>">
                                                                            <div class="modal-body">
                                                                                <div class="alert alert-info mb-3">Jawaban anda menentukan kesuksesan kami, mohon disampaikan apa adanya sesuai dengan yang Anda ketahui, Terimakasih.</div>
                                                                                <?php foreach ($questions as $index => $question) {
                                                                                    $number = $index + 1;
                                                                                    $jawaban_key = 'q' . $number . '_jawaban';
                                                                                    $fakta_key = 'q' . $number . '_fakta';
                                                                                ?>
                                                                                    <div class="border rounded p-3 mb-3">
                                                                                        <div class="mb-2">
                                                                                            <span class="badge bg-primary"><?= h($question['kategori']); ?></span>
                                                                                            <span class="badge bg-light text-dark"><?= h($question['poin']); ?></span>
                                                                                        </div>
                                                                                        <label class="form-label fw-bold"><?= $number; ?>. <?= h($question['tanya']); ?></label>
                                                                                        <div class="d-flex gap-3 mb-2">
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input" type="radio" name="q<?= $number; ?>_jawaban" id="q<?= $number; ?>ya<?= intval($request['id_assignment']); ?>" value="Ya" <?= ($request[$jawaban_key] ?? '') === 'Ya' ? 'checked' : ''; ?> required>
                                                                                                <label class="form-check-label" for="q<?= $number; ?>ya<?= intval($request['id_assignment']); ?>">Ya</label>
                                                                                            </div>
                                                                                            <div class="form-check">
                                                                                                <input class="form-check-input" type="radio" name="q<?= $number; ?>_jawaban" id="q<?= $number; ?>tidak<?= intval($request['id_assignment']); ?>" value="Tidak" <?= ($request[$jawaban_key] ?? '') === 'Tidak' ? 'checked' : ''; ?> required>
                                                                                                <label class="form-check-label" for="q<?= $number; ?>tidak<?= intval($request['id_assignment']); ?>">Tidak</label>
                                                                                            </div>
                                                                                        </div>
                                                                                        <label class="form-label text-muted"><?= h($question['fakta']); ?></label>
                                                                                        <textarea class="form-control" name="q<?= $number; ?>_fakta" rows="2" required><?= h($request[$fakta_key] ?? ''); ?></textarea>
                                                                                    </div>
                                                                                <?php } ?>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                                <button type="submit" name="simpan_penilaian" class="btn btn-primary"><i class="bi bi-save"></i> Simpan Penilaian</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada permintaan penilaian karakter.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <?php if ($can_manage) { ?>
                            <div class="col-12">
                                <div class="card shadow-sm">
                                    <div class="card-header bg-primary text-white">
                                        <h5 class="card-title mb-0"><i class="bi bi-people"></i> Daftar Anggota & Hasil Penilaian Karakter</h5>
                                    </div>
                                    <div class="card-body p-0">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Anggota</th>
                                                        <th><center>Penilai</center></th>
                                                        <th><center>Status</center></th>
                                                        <th><center>Bulan Ini (<?= h($bulan_penilaian); ?>)</center></th>
                                                        <th><center>Bulan Lalu (<?= h($bulan_lalu); ?>)</center></th>
                                                        <th><center>Selisih</center></th>
                                                        <th width="14%"><center>Aksi</center></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php if (!empty($anggota_rows)) { ?>
                                                        <?php foreach ($anggota_rows as $anggota) {
                                                            $summary_current = $karakter_summary_by_user[$anggota['id']]['current'];
                                                            $summary_previous = $karakter_summary_by_user[$anggota['id']]['previous'];
                                                            $member_assignments = $assignments_by_user[$anggota['id']] ?? [];
                                                            $total_penilai = count($member_assignments);
                                                            $total_selesai = 0;
                                                            foreach ($member_assignments as $assignment_status) {
                                                                if (!empty($assignment_status['submitted_at'])) {
                                                                    $total_selesai++;
                                                                }
                                                            }
                                                        ?>
                                                            <tr>
                                                                <td>
                                                                    <strong><?= h($anggota['nama_lngkp']); ?></strong>
                                                                    <small class="text-muted d-block"><?= h($anggota['bagian'] . ' / ' . $anggota['departement']); ?></small>
                                                                </td>
                                                                <td><center><span class="badge bg-primary"><?= $total_penilai; ?> orang</span></center></td>
                                                                <td>
                                                                    <center>
                                                                        <?php if ($total_penilai > 0) { ?>
                                                                            <span class="badge bg-success"><?= $total_selesai; ?>/<?= $total_penilai; ?> selesai</span>
                                                                        <?php } else { ?>
                                                                            <span class="badge bg-secondary">Belum ada</span>
                                                                        <?php } ?>
                                                                    </center>
                                                                </td>
                                                                <td><center><span class="badge bg-success"><?= karakterFormatScore($summary_current['total']); ?></span></center></td>
                                                                <td><center><span class="badge bg-secondary"><?= karakterFormatScore($summary_previous['total']); ?></span></center></td>
                                                                <td><center><?= karakterTrendBadge($summary_current['total'], $summary_previous['total']); ?></center></td>
                                                                <td>
                                                                    <div class="d-flex gap-1 justify-content-center">
                                                                        <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#hasilKarakter<?= intval($anggota['id']); ?>">
                                                                            <i class="bi bi-eye"></i>
                                                                        </button>
                                                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#aturPenilai<?= intval($anggota['id']); ?>">
                                                                            <i class="bi bi-person-plus"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    <?php } else { ?>
                                                        <tr><td colspan="7" class="text-center text-muted py-4">Belum ada anggota di bawah atasan ini.</td></tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>

        <?php if ($can_manage) { ?>
            <?php foreach ($anggota_rows as $anggota) {
                $summary_current = $karakter_summary_by_user[$anggota['id']]['current'];
                $summary_previous = $karakter_summary_by_user[$anggota['id']]['previous'];
                $member_assignments = $assignments_by_user[$anggota['id']] ?? [];
            ?>
                <div class="modal fade" id="hasilKarakter<?= intval($anggota['id']); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title fw-bold">Hasil Penilaian Karakter - <?= h($anggota['nama_lngkp']); ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="row g-3 mb-3">
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="text-muted">Bulan Ini</div>
                                            <h3 class="mb-0"><?= karakterFormatScore($summary_current['total']); ?></h3>
                                            <small>Skor maksimal 4.00</small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="text-muted">Bulan Lalu</div>
                                            <h3 class="mb-0"><?= karakterFormatScore($summary_previous['total']); ?></h3>
                                            <small>Periode <?= h($bulan_lalu); ?></small>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="border rounded p-3 h-100">
                                            <div class="text-muted">Selisih</div>
                                            <div class="fs-4"><?= karakterTrendBadge($summary_current['total'], $summary_previous['total']); ?></div>
                                            <small><?= intval($summary_current['count']); ?> penilai sudah mengisi</small>
                                        </div>
                                    </div>
                                </div>

                                <h6 class="fw-bold">Perbandingan Kategori</h6>
                                <div class="table-responsive mb-3">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Kategori</th>
                                                <th><center>Bulan Ini</center></th>
                                                <th><center>Bulan Lalu</center></th>
                                                <th><center>Selisih</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($categories as $category) { ?>
                                                <tr>
                                                    <td><?= h($category); ?></td>
                                                    <td><center><?= karakterFormatScore($summary_current['categories'][$category]); ?></center></td>
                                                    <td><center><?= karakterFormatScore($summary_previous['categories'][$category]); ?></center></td>
                                                    <td><center><?= karakterTrendBadge($summary_current['categories'][$category], $summary_previous['categories'][$category]); ?></center></td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <h6 class="fw-bold">Detail Penilai Bulan Ini</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Penilai</th>
                                                <th><center>Skor</center></th>
                                                <?php foreach ($categories as $category) { ?>
                                                    <th><center><?= h($category); ?></center></th>
                                                <?php } ?>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($member_assignments)) { ?>
                                                <?php foreach ($member_assignments as $member_assignment) {
                                                    $score = karakterScoreResponse($member_assignment, $questions);
                                                ?>
                                                    <tr>
                                                        <td><?= h($member_assignment['nama_penilai']); ?></td>
                                                        <td><center><?= $score['submitted'] ? karakterFormatScore($score['total']) : '-'; ?></center></td>
                                                        <?php foreach ($categories as $category) { ?>
                                                            <td><center><?= $score['submitted'] ? karakterFormatScore($score['categories'][$category]['score']) : '-'; ?></center></td>
                                                        <?php } ?>
                                                        <td>
                                                            <?php if ($score['submitted']) { ?>
                                                                <span class="badge bg-success">Sudah</span>
                                                                <small class="text-muted d-block"><?= h(date('d/m/Y H:i', strtotime($member_assignment['submitted_at']))); ?></small>
                                                            <?php } else { ?>
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr><td colspan="<?= 3 + count($categories); ?>" class="text-center text-muted py-3">Belum ada penilai aktif.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php
                                $submitted_assignments = array_filter($member_assignments, fn($a) => !empty($a['submitted_at']));
                                if (!empty($submitted_assignments)) {
                                ?>
                                <hr>
                                <h6 class="fw-bold mt-3">Detail Jawaban Per Penilai</h6>
                                <div class="mb-3">
                                    <select class="form-select form-select-sm detail-penilai-select" data-member-id="<?= intval($anggota['id']); ?>" style="max-width:350px;">
                                        <option value="">-- Pilih nama penilai --</option>
                                        <?php foreach ($submitted_assignments as $ma) { ?>
                                            <option value="detail-jawaban-<?= intval($anggota['id']); ?>-<?= intval($ma['id_assignment']); ?>">
                                                <?= h($ma['nama_penilai']); ?> &mdash; <?= h($ma['bagian_penilai'] . ' / ' . $ma['departement_penilai']); ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php foreach ($submitted_assignments as $ma) { ?>
                                    <div class="card mb-3 border-0 shadow-sm detail-jawaban-card" id="detail-jawaban-<?= intval($anggota['id']); ?>-<?= intval($ma['id_assignment']); ?>" style="display:none;">
                                        <div class="card-header bg-light d-flex align-items-center justify-content-between py-2">
                                            <span class="fw-bold"><i class="bi bi-person-fill me-1"></i><?= h($ma['nama_penilai']); ?></span>
                                            <small class="text-muted"><?= h($ma['bagian_penilai'] . ' / ' . $ma['departement_penilai']); ?> &mdash; Dikirim: <?= h(date('d/m/Y H:i', strtotime($ma['submitted_at']))); ?></small>
                                        </div>
                                        <div class="card-body p-0">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th width="3%">#</th>
                                                        <th width="18%">Kategori / Poin</th>
                                                        <th>Pertanyaan</th>
                                                        <th width="8%"><center>Jawaban</center></th>
                                                        <th>Fakta / Penjelasan</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($questions as $qi => $question) {
                                                        $qn = $qi + 1;
                                                        $jawaban_val = $ma['q' . $qn . '_jawaban'] ?? '-';
                                                        $fakta_val   = $ma['q' . $qn . '_fakta'] ?? '';
                                                        $is_ideal    = $jawaban_val === $question['ideal'];
                                                    ?>
                                                        <tr>
                                                            <td class="text-center text-muted"><?= $qn; ?></td>
                                                            <td>
                                                                <span class="badge bg-primary" style="font-size:10px;"><?= h($question['kategori']); ?></span>
                                                                <div class="text-muted" style="font-size:11px;"><?= h($question['poin']); ?></div>
                                                            </td>
                                                            <td><?= h($question['tanya']); ?></td>
                                                            <td class="text-center">
                                                                <span class="badge <?= $is_ideal ? 'bg-success' : 'bg-danger'; ?>"><?= h($jawaban_val); ?></span>
                                                            </td>
                                                            <td class="text-muted" style="white-space:pre-wrap;"><?= nl2br(h($fakta_val)); ?></td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php foreach ($anggota_rows as $anggota) {
                $member_assignments = $assignments_by_user[$anggota['id']] ?? [];
                $selected_penilai_ids = [];
                foreach ($member_assignments as $member_assignment) {
                    $selected_penilai_ids[] = intval($member_assignment['id_penilai']);
                }
            ?>
                <div class="modal fade" id="aturPenilai<?= intval($anggota['id']); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title fw-bold">Atur Penilai - <?= h($anggota['nama_lngkp']); ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <div class="mb-3">
                                    <strong>Anggota:</strong> <?= h($anggota['nama_lngkp']); ?><br>
                                    <span class="text-muted"><?= h($anggota['bagian'] . ' / ' . $anggota['departement'] . ' / ' . $anggota['jabatan']); ?></span>
                                </div>

                                <form method="POST" action="">
                                    <input type="hidden" name="id_user_dinilai" value="<?= intval($anggota['id']); ?>">
                                    <label class="form-label fw-bold">Pilih Penilai</label>
                                    <div class="row g-2 mb-3">
                                        <div class="col-md-7">
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                                <input type="text" class="form-control penilai-search" data-member-id="<?= intval($anggota['id']); ?>" placeholder="Cari nama, bagian, departemen, atau jabatan">
                                            </div>
                                        </div>
                                        <div class="col-md-5">
                                            <select class="form-select penilai-filter" data-member-id="<?= intval($anggota['id']); ?>">
                                                <option value="">Semua departemen</option>
                                                <?php foreach ($departemen_penilai_rows as $departemen_penilai) { ?>
                                                    <option value="<?= h(strtolower($departemen_penilai)); ?>"><?= h($departemen_penilai); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <?php foreach ($users_rows as $user_row) {
                                            if (intval($user_row['id']) === intval($anggota['id'])) {
                                                continue;
                                            }
                                            $checked = in_array(intval($user_row['id']), $selected_penilai_ids, true);
                                            $search_text = strtolower($user_row['nama_lngkp'] . ' ' . $user_row['bagian'] . ' ' . $user_row['departement'] . ' ' . $user_row['jabatan']);
                                        ?>
                                            <div class="col-md-6 penilai-option" data-member-id="<?= intval($anggota['id']); ?>" data-search="<?= h($search_text); ?>" data-departement="<?= h(strtolower($user_row['departement'])); ?>">
                                                <label for="penilai<?= intval($anggota['id']); ?>_<?= intval($user_row['id']); ?>" class="d-flex align-items-start gap-2 border rounded p-2 h-100 cursor-pointer w-100" style="cursor:pointer;">
                                                    <input class="form-check-input flex-shrink-0 mt-1" type="checkbox" name="id_penilai[]" value="<?= intval($user_row['id']); ?>" id="penilai<?= intval($anggota['id']); ?>_<?= intval($user_row['id']); ?>" <?= $checked ? 'checked' : ''; ?>>
                                                    <span>
                                                        <strong><?= h($user_row['nama_lngkp']); ?></strong>
                                                        <small class="text-muted d-block"><?= h($user_row['bagian'] . ' / ' . $user_row['departement']); ?></small>
                                                    </span>
                                                </label>
                                            </div>
                                        <?php } ?>
                                    </div>
                                    <button type="submit" name="tambah_penilai" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Penilai
                                    </button>
                                </form>

                                <hr>
                                <h6 class="fw-bold">Penilai Aktif</h6>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Penilai</th>
                                                <th>Departemen</th>
                                                <th>Status</th>
                                                <th width="12%"><center>#</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if (!empty($member_assignments)) { ?>
                                                <?php foreach ($member_assignments as $member_assignment) { ?>
                                                    <tr>
                                                        <td><?= h($member_assignment['nama_penilai']); ?></td>
                                                        <td><?= h($member_assignment['departement_penilai']); ?></td>
                                                        <td>
                                                            <?php if (!empty($member_assignment['submitted_at'])) { ?>
                                                                <span class="badge bg-success">Sudah</span>
                                                                <small class="text-muted d-block"><?= h(date('d/m/Y H:i', strtotime($member_assignment['submitted_at']))); ?></small>
                                                            <?php } else { ?>
                                                                <span class="badge bg-warning text-dark">Pending</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-1 justify-content-center">
                                                                <?php if (!empty($member_assignment['submitted_at'])) { ?>
                                                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#detailKarakter<?= intval($member_assignment['id_assignment']); ?>"><i class="bi bi-eye"></i></button>
                                                                <?php } ?>
                                                                <form method="POST" action="" onsubmit="return confirm('Nonaktifkan penilai ini?')">
                                                                    <input type="hidden" name="id_assignment" value="<?= intval($member_assignment['id_assignment']); ?>">
                                                                    <button type="submit" name="hapus_penilai" class="btn btn-danger btn-sm"><i class="bi bi-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada penilai aktif.</td></tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <?php foreach ($assignment_rows as $assignment) {
                if (empty($assignment['submitted_at'])) {
                    continue;
                }
            ?>
                <div class="modal fade" id="detailKarakter<?= intval($assignment['id_assignment']); ?>" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable">
                        <div class="modal-content">
                            <div class="modal-header bg-info text-white">
                                <h5 class="modal-title fw-bold">Detail Penilaian - <?= h($assignment['nama_dinilai']); ?></h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <?php $assignment_score = karakterScoreResponse($assignment, $questions); ?>
                                <div class="mb-3">
                                    <strong>Penilai:</strong> <?= h($assignment['nama_penilai']); ?><br>
                                    <strong>Dikirim:</strong> <?= h(date('d/m/Y H:i', strtotime($assignment['submitted_at']))); ?><br>
                                    <strong>Skor:</strong> <?= karakterFormatScore($assignment_score['total']); ?> / 4.00
                                </div>
                                <div class="row g-2 mb-3">
                                    <?php foreach ($categories as $category) { ?>
                                        <div class="col-md-3">
                                            <div class="border rounded p-2 h-100">
                                                <div class="text-muted"><?= h($category); ?></div>
                                                <strong><?= karakterFormatScore($assignment_score['categories'][$category]['score']); ?></strong>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php foreach ($questions as $index => $question) {
                                    $number = $index + 1;
                                    $jawaban_key = 'q' . $number . '_jawaban';
                                    $fakta_key = 'q' . $number . '_fakta';
                                ?>
                                    <div class="border rounded p-3 mb-2">
                                        <div class="mb-2">
                                            <span class="badge bg-primary"><?= h($question['kategori']); ?></span>
                                            <span class="badge bg-light text-dark"><?= h($question['poin']); ?></span>
                                        </div>
                                        <div class="d-flex align-items-center justify-content-between gap-2">
                                            <strong><?= $number; ?>. <?= h($question['tanya']); ?></strong>
                                            <span class="badge <?= ($assignment[$jawaban_key] ?? '') === $question['ideal'] ? 'bg-success' : 'bg-danger'; ?>"><?= h($assignment[$jawaban_key] ?? '-'); ?></span>
                                        </div>
                                        <div class="text-muted mt-2"><?= nl2br(h($assignment[$fakta_key] ?? '')); ?></div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } ?>

        <?php include("pages/part/p_footer.php"); ?>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function filterPenilai(memberId) {
                var searchInput = document.querySelector('.penilai-search[data-member-id="' + memberId + '"]');
                var filterInput = document.querySelector('.penilai-filter[data-member-id="' + memberId + '"]');
                var searchValue = searchInput ? searchInput.value.toLowerCase().trim() : '';
                var filterValue = filterInput ? filterInput.value.toLowerCase().trim() : '';

                document.querySelectorAll('.penilai-option[data-member-id="' + memberId + '"]').forEach(function (item) {
                    var text = item.getAttribute('data-search') || '';
                    var departement = item.getAttribute('data-departement') || '';
                    var matchSearch = searchValue === '' || text.indexOf(searchValue) !== -1;
                    var matchFilter = filterValue === '' || departement === filterValue;
                    item.style.display = matchSearch && matchFilter ? '' : 'none';
                });
            }

            document.querySelectorAll('.penilai-search, .penilai-filter').forEach(function (input) {
                input.addEventListener('input', function () {
                    filterPenilai(this.getAttribute('data-member-id'));
                });
                input.addEventListener('change', function () {
                    filterPenilai(this.getAttribute('data-member-id'));
                });
            });

            document.querySelectorAll('.detail-penilai-select').forEach(function (select) {
                select.addEventListener('change', function () {
                    var memberId = this.getAttribute('data-member-id');
                    var selectedId = this.value;
                    document.querySelectorAll('.detail-jawaban-card[id^="detail-jawaban-' + memberId + '-"]').forEach(function (card) {
                        card.style.display = 'none';
                    });
                    if (selectedId) {
                        var target = document.getElementById(selectedId);
                        if (target) {
                            target.style.display = '';
                            target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                        }
                    }
                });
            });

            document.querySelectorAll('[id^="hasilKarakter"]').forEach(function (modal) {
                modal.addEventListener('hidden.bs.modal', function () {
                    modal.querySelectorAll('.detail-penilai-select').forEach(function (sel) {
                        sel.value = '';
                    });
                    modal.querySelectorAll('.detail-jawaban-card').forEach(function (card) {
                        card.style.display = 'none';
                    });
                });
            });

            // Rotasi icon chevron dropdown "Permintaan Penilaian Saya"
            var permintaanCollapse = document.getElementById('collapsePermintaanPenilaian');
            if (permintaanCollapse) {
                var header = permintaanCollapse.previousElementSibling;
                var chevron = header ? header.querySelector('.collapse-chevron') : null;
                permintaanCollapse.addEventListener('show.bs.collapse', function () {
                    if (chevron) chevron.style.transform = 'translateY(-50%) rotate(180deg)';
                });
                permintaanCollapse.addEventListener('hide.bs.collapse', function () {
                    if (chevron) chevron.style.transform = 'translateY(-50%) rotate(0deg)';
                });
            }
        });
    </script>
</body>

</html>