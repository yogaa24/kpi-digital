<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

// Check level Admin HRD
$sql_check = "SELECT level FROM tb_users WHERE id = '$id_user'";
$result_check = mysqli_query($conn, $sql_check);
$user_data = mysqli_fetch_assoc($result_check);

if ($user_data['level'] != 7) {
    header("Location: home-kpi-real");
    exit();
}

$karakter_autoload_path = __DIR__ . '/../../vendor/autoload.php';
if (is_file($karakter_autoload_path)) {
    require_once $karakter_autoload_path;
}
    
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
        ['kategori' => 'Realistis', 'poin' => 'Memilih dan bertindak menghadapi hambatan', 'ideal' => 'Ya', 'tanya' => 'Apakah ybs memilih dan bertindak untuk menghadapi hambatan or kesulitan, bukan menghindar or menunggu ?', 'fakta' => 'Jelaskan fakta dari jawaban anda terkait poin Realistis'],
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

    $num_categories = 0;
    $total_score = 0;
    foreach ($score['categories'] as $category => $category_score) {
        if ($category_score['total'] > 0) {
            $score['categories'][$category]['score'] = ($category_score['correct'] / $category_score['total']) * 4;
            $total_score += $score['categories'][$category]['score'];
            $num_categories++;
        }
    }

    $score['total'] = $num_categories > 0 ? $total_score / $num_categories : 0;
    return $score;
}

function karakterAverageScores($rows, $questions)
{
    $summary = [
        'count'      => 0,
        'total'      => null,
        'categories' => [],
        'questions'  => []
    ];

    foreach (karakterCategories($questions) as $category) {
        $summary['categories'][$category] = null;
    }

    $q_correct = [];
    $q_count   = [];
    foreach ($questions as $index => $question) {
        $q_correct[$index] = 0;
        $q_count[$index]   = 0;
    }

    foreach ($rows as $row) {
        if (empty($row['submitted_at'])) {
            continue;
        }
        $summary['count']++;

        foreach ($questions as $index => $question) {
            $number = $index + 1;
            $answer = $row['q' . $number . '_jawaban'] ?? null;
            if ($answer !== null) {
                $q_count[$index]++;
                if ($answer === $question['ideal']) {
                    $q_correct[$index]++;
                }
            }
        }
    }

    if ($summary['count'] > 0) {
        $category_scores = [];
        $all_q_scores    = [];
        foreach ($questions as $index => $question) {
            $category = $question['kategori'];
            $q_score  = $q_count[$index] > 0
                ? ($q_correct[$index] / $q_count[$index]) * 4
                : 0;

            $summary['questions'][$index] = [
                'correct' => $q_correct[$index],
                'count'   => $q_count[$index],
                'score'   => $q_score
            ];

            $all_q_scores[]              = $q_score;
            $category_scores[$category][] = $q_score;
        }

        foreach ($category_scores as $category => $scores) {
            $summary['categories'][$category] = count($scores) > 0
                ? array_sum($scores) / count($scores)
                : null;
        }

        $summary['total'] = count($all_q_scores) > 0
            ? array_sum($all_q_scores) / count($all_q_scores)
            : null;
    }

    return $summary;
}

function karakterFormatScore($value)
{
    return $value === null ? '-' : number_format((float) $value, 2);
}

function karakterPointLabel($point)
{
    $labels = [
        'Tidak mengeluh' => 'Mengeluh',
        'Tidak menyalahkan' => 'Menyalahkan',
        'Selalu berpegang pada hasil' => 'Berpegang pada hasil',
        'Terus melakukan perbaikan' => "Terus melakukan\nperbaikan",
        'Daya juang tinggi' => 'Daya Juang Tinggi',
        'Tidak mudah menyerah' => 'Tidak Mudah Menyerah',
        'Tidak mudah dijatuhkan' => 'Tidak Mudah Dijatuhkan',
        'Berani mengungkapkan kejujuran apa adanya' => 'Jujur apa adanya',
        'Keterbukaan' => 'Keterbukaan',
        'Tidak defensif' => 'Tidak Defensif',
        'Memilih dan bertindak menghadapi hambatan' => 'Realistis'
    ];

    return $labels[$point] ?? $point;
}

function karakterCategoryLabel($category)
{
    $labels = [
        'Tanggung jawab' => 'Tanggung Jawab',
        'Persisten' => 'Persisten',
        'Komunikasi' => 'Komunikasi',
        'Realistis' => 'Realistis'
    ];

    return $labels[$category] ?? $category;
}

function karakterScoreQuestionForAssignments($assignments, $question, $number)
{
    $submitted_count = 0;
    $correct_count = 0;

    foreach ($assignments as $assignment) {
        if (empty($assignment['submitted_at'])) {
            continue;
        }

        $answer = $assignment['q' . $number . '_jawaban'] ?? null;
        if ($answer === null || $answer === '') {
            continue;
        }

        $submitted_count++;
        if ($answer === $question['ideal']) {
            $correct_count++;
        }
    }

    return $submitted_count > 0 ? ($correct_count / $submitted_count) * 4 : null;
}

function karakterMonthLabel($month)
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

function h($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function karakterFetchAllAssignmentRows($conn, $bulan)
{
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
        WHERE a.status = 'aktif'
        ORDER BY dinilai.nama_lngkp, penilai.nama_lngkp");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $rows[] = $row;
            $by_user[$row['id_user_dinilai']][] = $row;
        }
    }

    return ['rows' => $rows, 'by_user' => $by_user];
}

function karakterExportAllResults($anggota_rows, $assignment_rows, $assignments_by_user, $questions, $bulan)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $spreadsheet->getProperties()
        ->setCreator('KPI')
        ->setTitle('Export Penilaian Karakter ' . karakterMonthLabel($bulan))
        ->setSubject('Penilaian Karakter')
        ->setDescription('Hasil penilaian karakter seluruh anggota periode ' . karakterMonthLabel($bulan));

    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle(substr('Rekap ' . karakterMonthLabel($bulan), 0, 31));

    $blue = '002060';
    $green = '00B050';
    $yellow = 'FFC000';
    $red = 'FF0000';

    $dark_header_style = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $blue]],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            'wrapText' => true
        ],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $border_style = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                'color' => ['rgb' => '000000']
            ]
        ]
    ];

    $last_col_index = max(1, count($anggota_rows) + 1);
    $last_col = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($last_col_index);
    $row_number = 1;
    $current_category = null;
    $member_totals = [];
    $member_counts = [];

    foreach ($anggota_rows as $member_index => $anggota) {
        $member_totals[$member_index] = 0;
        $member_counts[$member_index] = 0;
    }

    foreach ($questions as $index => $question) {
        if ($current_category !== $question['kategori']) {
            $current_category = $question['kategori'];
            $category_label = $current_category === 'Tanggung jawab' ? 'Karakter Bertanggung Jawab' : karakterCategoryLabel($current_category);
            $sheet->setCellValue('A' . $row_number, $category_label);
            if ($row_number === 1) {
                foreach ($anggota_rows as $member_index => $anggota) {
                    $sheet->setCellValueByColumnAndRow($member_index + 2, $row_number, $anggota['nama_lngkp']);
                }
            }
            $sheet->getStyle('A' . $row_number . ':' . $last_col . $row_number)->applyFromArray($dark_header_style);
            $row_number++;
        }

        $number = $index + 1;
        $sheet->setCellValue('A' . $row_number, karakterPointLabel($question['poin']));

        foreach ($anggota_rows as $member_index => $anggota) {
            $score = karakterScoreQuestionForAssignments($assignments_by_user[$anggota['id']] ?? [], $question, $number);
            $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($member_index + 2) . $row_number;
            if ($score !== null) {
                $sheet->setCellValue($cell, $score);
                $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('0.0');
                $member_totals[$member_index] += $score;
                $member_counts[$member_index]++;
                if ($score < 4) {
                    $sheet->getStyle($cell)->getFont()->getColor()->setRGB($red);
                    $sheet->getStyle($cell)->getFont()->setBold(true);
                }
            } else {
                $sheet->setCellValue($cell, '-');
            }
        }

        $row_number++;
    }

    $average_row = $row_number;
    $sheet->setCellValue('A' . $average_row, 'Rata2');
    $sheet->getStyle('A' . $average_row)->applyFromArray([
        'font' => ['bold' => true],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $yellow]],
        'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
    ]);

    foreach ($anggota_rows as $member_index => $anggota) {
        $cell = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($member_index + 2) . $average_row;
        $average = $member_counts[$member_index] > 0 ? $member_totals[$member_index] / $member_counts[$member_index] : null;
        if ($average !== null) {
            $sheet->setCellValue($cell, $average);
            $sheet->getStyle($cell)->getNumberFormat()->setFormatCode('0.00');
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => $average < 4 ? $red : $green]
                ],
                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
            ]);
        } else {
            $sheet->setCellValue($cell, '-');
        }
    }

    $sheet->getStyle('A1:' . $last_col . $average_row)->applyFromArray($border_style);
    $sheet->getStyle('A1:' . $last_col . $average_row)->getAlignment()
        ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
        ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
        ->setWrapText(true);
    $sheet->getColumnDimension('A')->setWidth(24);
    for ($i = 2; $i <= $last_col_index; $i++) {
        $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($i))->setWidth(17);
    }
    for ($i = 1; $i <= $average_row; $i++) {
        $sheet->getRowDimension($i)->setRowHeight(21);
    }
    $sheet->freezePane('B2');

    $filename = 'penilaian-karakter-semua-' . $bulan . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit();
}

$questions = karakterQuestions();
$categories = karakterCategories($questions);
$bulan_penilaian = date('Y-m', strtotime(date('Y-m-01') . ' -1 month'));
$bulan_lalu = date('Y-m', strtotime(date('Y-m-01') . ' -2 month'));

$anggota_rows = [];
$anggota_result = mysqli_query($conn, "SELECT id, username, nik, nama_lngkp, bagian, departement, jabatan FROM tb_users WHERE jabatan != 'Admin HRD' ORDER BY nama_lngkp");
if ($anggota_result) {
    while ($anggota = mysqli_fetch_assoc($anggota_result)) {
        $anggota_rows[] = $anggota;
    }
}

// Ambil data untuk filter dropdown
$sql_jabatan = "SELECT DISTINCT jabatan FROM tb_users WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan";
$result_jabatan = mysqli_query($conn, $sql_jabatan);

$sql_departemen = "SELECT DISTINCT departement FROM tb_users WHERE departement IS NOT NULL AND departement != '' ORDER BY departement";
$result_departemen = mysqli_query($conn, $sql_departemen);

$sql_bagian = "SELECT DISTINCT bagian FROM tb_users WHERE bagian IS NOT NULL AND bagian != '' ORDER BY bagian";
$result_bagian = mysqli_query($conn, $sql_bagian);

$assignment_data = karakterFetchAllAssignmentRows($conn, $bulan_penilaian);
$assignment_rows = $assignment_data['rows'];
$assignments_by_user = $assignment_data['by_user'];

$assignment_data_lalu = karakterFetchAllAssignmentRows($conn, $bulan_lalu);
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

if (isset($_GET['export_karakter'])) {
    if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        $_SESSION['karakter_message'] = ['type' => 'danger', 'text' => 'Export Excel belum bisa digunakan karena dependency Composer belum terpasang.'];
        header('Location: penilaian-karakter-adminhrd');
        exit();
    }
    karakterExportAllResults($anggota_rows, $assignment_rows, $assignments_by_user, $questions, $bulan_penilaian);
}
?>
<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>
<style>
    .nilai-badge {
        font-size: 0.95rem;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
    }
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
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

                    <!-- Header -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                        <div>
                                            <h4 class="fw-bold mb-0">
                                                <i class="bi bi-heart-pulse-fill text-purple me-2" style="color: #8b5cf6;"></i>
                                                Monitoring Penilaian Karakter - Semua Karyawan
                                            </h4>
                                            <p class="text-muted mb-0 small mt-2">
                                                Periode Penilaian: <?= h(karakterMonthLabel($bulan_penilaian)); ?>
                                            </p>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <a href="penilaian-karakter-adminhrd?export_karakter=1" class="btn btn-success btn-sm shadow-sm">
                                                <i class="bi bi-file-earmark-excel me-1"></i> Export Excel
                                            </a>
                                            <a href="dashboard-adminhrd" class="btn btn-light btn-sm shadow-sm">
                                                <i class="bi bi-arrow-left me-1"></i> Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold"><i class="bi bi-award me-1"></i>Jabatan</label>
                                            <select id="filterJabatan" class="form-select form-select-sm">
                                                <option value="">-- Semua Jabatan --</option>
                                                <?php while ($jab = mysqli_fetch_assoc($result_jabatan)) { ?>
                                                    <option value="<?= h($jab['jabatan']) ?>"><?= h($jab['jabatan']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold"><i class="bi bi-building me-1"></i>Departemen</label>
                                            <select id="filterDepartemen" class="form-select form-select-sm">
                                                <option value="">-- Semua Departemen --</option>
                                                <?php while ($dept = mysqli_fetch_assoc($result_departemen)) { ?>
                                                    <option value="<?= h($dept['departement']) ?>"><?= h($dept['departement']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold"><i class="bi bi-diagram-3 me-1"></i>Bagian</label>
                                            <select id="filterBagian" class="form-select form-select-sm">
                                                <option value="">-- Semua Bagian --</option>
                                                <?php while ($bag = mysqli_fetch_assoc($result_bagian)) { ?>
                                                    <option value="<?= h($bag['bagian']) ?>"><?= h($bag['bagian']) ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-3">
                                            <button id="resetFilter" class="btn btn-secondary btn-sm w-100">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table id="datatablenya" class="table table-hover table-bordered mb-0">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>No</th>
                                                    <th>Nama Karyawan</th>
                                                    <th>Jabatan</th>
                                                    <th>Departemen</th>
                                                    <th>Bagian</th>
                                                    <th>Penilai</th>
                                                    <th>Status</th>
                                                    <th>Bulan Ini</th>
                                                    <th>Bulan Lalu</th>
                                                    <th>Selisih</th>
                                                    <th width="8%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                foreach ($anggota_rows as $anggota) {
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
                                                        <td><?= $no++; ?></td>
                                                        <td>
                                                            <strong><?= h($anggota['nama_lngkp']); ?></strong><br>
                                                            <small class="text-muted">NIK: <?= h($anggota['nik']); ?></small>
                                                        </td>
                                                        <td><?= h($anggota['jabatan']); ?></td>
                                                        <td><?= h($anggota['departement']); ?></td>
                                                        <td><?= h($anggota['bagian']); ?></td>
                                                        <td><?= $total_penilai; ?> orang</td>
                                                        <td>
                                                            <?php if ($total_penilai > 0) { ?>
                                                                <span class="badge bg-success"><?= $total_selesai; ?>/<?= $total_penilai; ?> selesai</span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">Belum ada</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td><span class="badge bg-success"><?= karakterFormatScore($summary_current['total']); ?></span></td>
                                                        <td><span class="badge bg-secondary"><?= karakterFormatScore($summary_previous['total']); ?></span></td>
                                                        <td><?= karakterTrendBadge($summary_current['total'], $summary_previous['total']); ?></td>
                                                        <td>
                                                            <center>
                                                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#hasilKarakter<?= intval($anggota['id']); ?>">
                                                                    <i class="bi bi-eye"></i> Detail
                                                                </button>
                                                            </center>
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

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
                        <div class="modal-body" style="font-size: 13px;">
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
                            <div class="table-responsive mb-3">
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
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <?php include("pages/part/p_footer.php"); ?>
    </div>

    <!-- jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#datatablenya').DataTable({
                "responsive": true,
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "zeroRecords": "Data tidak ditemukan"
                },
                "pageLength": 10,
                "order": [[1, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": [0, 10] }
                ]
            });

            // Filter Jabatan
            $('#filterJabatan').on('change', function() {
                var val = $(this).val();
                table.column(2).search(val).draw();
            });

            // Filter Departemen
            $('#filterDepartemen').on('change', function() {
                var val = $(this).val();
                table.column(3).search(val).draw();
            });

            // Filter Bagian
            $('#filterBagian').on('change', function() {
                var val = $(this).val();
                table.column(4).search(val).draw();
            });

            // Reset Filter
            $('#resetFilter').on('click', function() {
                $('#filterJabatan').val('');
                $('#filterDepartemen').val('');
                $('#filterBagian').val('');
                table.search('').columns().search('').draw();
            });

            // Handle rater dropdown details change
            $(document).on('change', '.detail-penilai-select', function() {
                var memberId = $(this).data('member-id');
                var targetId = $(this).val();
                
                $('#hasilKarakter' + memberId + ' .detail-jawaban-card').hide();
                if (targetId) {
                    $('#' + targetId).show();
                }
            });
        });
    </script>
</body>
</html>
