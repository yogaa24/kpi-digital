<?php
if (isset($_GET['debug'])) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

@ini_set('memory_limit', '512M');
@ini_set('max_execution_time', '300');
if (function_exists('set_time_limit')) {
    @set_time_limit(300);
}

if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

if (file_exists(__DIR__ . '/../../helper/config.php')) {
    require_once __DIR__ . '/../../helper/config.php';
} elseif (file_exists('helper/config.php')) {
    require_once 'helper/config.php';
}

if (function_exists('mysqli_report')) {
    mysqli_report(MYSQLI_REPORT_OFF);
}

if (file_exists(__DIR__ . '/../../helper/getUser.php')) {
    require_once __DIR__ . '/../../helper/getUser.php';
} elseif (file_exists('helper/getUser.php')) {
    require_once 'helper/getUser.php';
}

if (file_exists(__DIR__ . '/../../helper/checkAdmin.php')) {
    require_once __DIR__ . '/../../helper/checkAdmin.php';
} elseif (file_exists('helper/checkAdmin.php')) {
    require_once 'helper/checkAdmin.php';
}

if (file_exists(__DIR__ . '/../../helper/sp_functions.php')) {
    require_once __DIR__ . '/../../helper/sp_functions.php';
} elseif (file_exists('helper/sp_functions.php')) {
    require_once 'helper/sp_functions.php';
}

// Fallback functions jika helper belum lengkap
if (!function_exists('canViewAllEmployees')) {
    function canViewAllEmployees() {
        return (isset($_SESSION['level']) && in_array((int)$_SESSION['level'], [5, 6, 7]));
    }
}
if (!function_exists('requireAdminHRDOrDirektur')) {
    function requireAdminHRDOrDirektur() {
        if (!canViewAllEmployees()) {
            header("Location: home-kpi-real");
            exit();
        }
    }
}
if (!function_exists('updateExpiredSP')) {
    function updateExpiredSP($conn) {}
}
if (!function_exists('calculateKPIWithSP')) {
    function calculateKPIWithSP($conn, $id_user, $nilai_asli) {
        return [
            'nilai_akhir' => $nilai_asli,
            'sp_data' => null,
            'pengurangan' => 0,
            'nilai_asli' => $nilai_asli
        ];
    }
}

$autoload_path = __DIR__ . '/../../vendor/autoload.php';
if (!file_exists($autoload_path) && file_exists('vendor/autoload.php')) {
    $autoload_path = 'vendor/autoload.php';
}
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

requireAdminHRDOrDirektur();
if (isset($conn) && $conn) {
    updateExpiredSP($conn);
}
date_default_timezone_set('Asia/Jakarta');

// Cek dependency PhpSpreadsheet & ZipArchive
$use_phpspreadsheet = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet') && class_exists('ZipArchive');

// Filter parameter
$mode                   = isset($_GET['mode']) && $_GET['mode'] === 'simulasi' ? 'simulasi' : 'real';
$filter_departemen      = isset($_GET['departemen']) ? trim($_GET['departemen']) : '';
$filter_jabatan         = isset($_GET['jabatan']) ? trim($_GET['jabatan']) : '';
$filter_kpi             = isset($_GET['status_kpi']) ? trim($_GET['status_kpi']) : '';
$filter_sp              = isset($_GET['status_sp']) ? trim($_GET['status_sp']) : '';
$filter_status_karyawan = isset($_GET['status_karyawan']) ? trim($_GET['status_karyawan']) : '';

function getkpi_export($nilair)
{
    if ($nilair < 90) {
        return "POOR";
    } elseif ($nilair <= 100) {
        return "GOOD";
    } elseif ($nilair <= 110) {
        return "Very Good";
    } else {
        return "Excellent";
    }
}

function getKPISimUserSummary($conn, $id_user)
{
    $id = intval($id_user);
    $sql = "SELECT id, bobot, bobot2 FROM tbsim_kpi WHERE id_user='$id'";
    $result = mysqli_query($conn, $sql);
    $exists = $result && mysqli_num_rows($result) > 0;

    $what_map = [];
    $res_w = mysqli_query($conn, "SELECT id_kpi, SUM(total) as tot FROM tbsim_whats WHERE id_user = $id GROUP BY id_kpi");
    if ($res_w) {
        while ($rw = mysqli_fetch_assoc($res_w)) {
            $what_map[intval($rw['id_kpi'])] = floatval($rw['tot']);
        }
    }

    $how_map = [];
    $res_h = mysqli_query($conn, "SELECT id_kpi, SUM(total) as tot FROM tbsim_hows WHERE id_user = $id GROUP BY id_kpi");
    if ($res_h) {
        while ($rh = mysqli_fetch_assoc($res_h)) {
            $how_map[intval($rh['id_kpi'])] = floatval($rh['tot']);
        }
    }

    $totalws = 0;
    $totalhfg = 0;
    if ($result) {
        while ($hasils = mysqli_fetch_assoc($result)) {
            $id_kpi = intval($hasils['id']);
            $bobot1 = floatval($hasils['bobot']);
            $bobot2 = floatval($hasils['bobot2']);

            $totalws += ((($what_map[$id_kpi] ?? 0) * $bobot1) / 100);
            $totalhfg += ((($how_map[$id_kpi] ?? 0) * $bobot2) / 100);
        }
    }

    $bobotwhat = 0;
    $bobothow = 0;
    $sql5a = "SELECT bobotwhat, bobothow FROM tbsim_bobotkpi WHERE id_user=$id LIMIT 1";
    $result5a = mysqli_query($conn, $sql5a);
    if ($result5a && ($row5a = mysqli_fetch_assoc($result5a))) {
        $bobotwhat = floatval($row5a['bobotwhat']);
        $bobothow = floatval($row5a['bobothow']);
    }

    $nilaiwhat = ($totalws * $bobotwhat) / 100;
    $nilaihow = ($totalhfg * $bobothow) / 100;
    $total_kpi = $nilaiwhat + $nilaihow;

    return [
        'what' => $nilaiwhat,
        'how' => $nilaihow,
        'nilai_asli' => $total_kpi,
        'nilai_akhir' => $total_kpi,
        'rating' => $exists ? getkpi_export($total_kpi) : '-',
        'exists' => $exists,
        'sp_data' => null,
        'pengurangan' => 0
    ];
}

function getKPIUserSummary($conn, $id_user)
{
    $id_user = intval($id_user);
    $bobotwhat = 0;
    $bobothow = 0;
    $sql_bobot = "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user = $id_user LIMIT 1";
    $res_bobot = mysqli_query($conn, $sql_bobot);
    if ($res_bobot && ($b = mysqli_fetch_assoc($res_bobot))) {
        $bobotwhat = floatval($b['bobotwhat']);
        $bobothow = floatval($b['bobothow']);
    }

    $what_map = [];
    $res_w = mysqli_query($conn, "SELECT id_kpi, SUM(total) as tot FROM tb_whats WHERE id_user = $id_user GROUP BY id_kpi");
    if ($res_w) {
        while ($rw = mysqli_fetch_assoc($res_w)) {
            $what_map[intval($rw['id_kpi'])] = floatval($rw['tot']);
        }
    }

    $how_map = [];
    $res_h = mysqli_query($conn, "SELECT id_kpi, SUM(total) as tot FROM tb_hows WHERE id_user = $id_user GROUP BY id_kpi");
    if ($res_h) {
        while ($rh = mysqli_fetch_assoc($res_h)) {
            $how_map[intval($rh['id_kpi'])] = floatval($rh['tot']);
        }
    }

    $sql_kpi = "SELECT id, bobot, bobot2 FROM tb_kpi WHERE id_user = $id_user";
    $res_kpi = mysqli_query($conn, $sql_kpi);
    $totalws = 0;
    $totalhfg = 0;
    if ($res_kpi) {
        while ($k = mysqli_fetch_assoc($res_kpi)) {
            $id_kpi = intval($k['id']);
            $bobot1 = floatval($k['bobot']);
            $bobot2 = floatval($k['bobot2']);

            $totalws += ((($what_map[$id_kpi] ?? 0) * $bobot1) / 100);
            $totalhfg += ((($how_map[$id_kpi] ?? 0) * $bobot2) / 100);
        }
    }

    $what_val = ($totalws * $bobotwhat) / 100;
    $how_val = ($totalhfg * $bobothow) / 100;
    $nilai_asli = $what_val + $how_val;

    $sp_res = function_exists('calculateKPIWithSP')
        ? calculateKPIWithSP($conn, $id_user, $nilai_asli)
        : ['nilai_akhir' => $nilai_asli, 'sp_data' => null, 'pengurangan' => 0];

    $nilai_akhir = $sp_res['nilai_akhir'] ?? $nilai_asli;

    return [
        'what' => $what_val,
        'how' => $how_val,
        'nilai_asli' => $nilai_asli,
        'nilai_akhir' => $nilai_akhir,
        'rating' => getkpi_export($nilai_akhir),
        'sp_data' => $sp_res['sp_data'] ?? null,
        'pengurangan' => $sp_res['pengurangan'] ?? 0,
        'exists' => true
    ];
}

// Cek apakah kolom status_karyawan ada di tb_users agar tidak SQL error jika database belum termigrasi
$has_col_status_karyawan = false;
$chk_col = mysqli_query($conn, "SHOW COLUMNS FROM tb_users LIKE 'status_karyawan'");
if ($chk_col && mysqli_num_rows($chk_col) > 0) {
    $has_col_status_karyawan = true;
}

// Bangun query list user
$where = "WHERE u.jabatan != 'Admin HRD' AND u.username NOT IN ('itboy', 'adminhrd', 'backdoor_admin')";
if (!empty($filter_departemen)) {
    $dept_safe = mysqli_real_escape_string($conn, $filter_departemen);
    $where .= " AND u.departement = '$dept_safe'";
}
if (!empty($filter_jabatan)) {
    $jab_safe = mysqli_real_escape_string($conn, $filter_jabatan);
    $where .= " AND u.jabatan = '$jab_safe'";
}
if (!empty($filter_status_karyawan)) {
    if ($has_col_status_karyawan) {
        $stat_safe = mysqli_real_escape_string($conn, $filter_status_karyawan);
        $where .= " AND u.status_karyawan = '$stat_safe'";
    }
} else {
    // Default hanya karyawan aktif yang diexport jika kolom tersedia
    if ($has_col_status_karyawan) {
        $where .= " AND (u.status_karyawan = 'AKTIF' OR (u.status_karyawan IS NULL AND (u.status = 1 OR u.status IS NULL)))";
    }
}

$sql_users = "SELECT u.*
              FROM tb_users u
              $where
              ORDER BY
                CASE
                    WHEN u.jabatan = 'Kadep' THEN 1
                    WHEN u.jabatan = 'Koordinator' THEN 2
                    WHEN u.jabatan = 'Manager' THEN 3
                    WHEN u.jabatan = 'Karyawan' THEN 4
                    ELSE 5
                END,
                u.nama_lngkp ASC";

$result_users = mysqli_query($conn, $sql_users);
if (!$result_users) {
    // Fallback jika query filter gagal
    $sql_users = "SELECT u.* FROM tb_users u WHERE u.jabatan != 'Admin HRD' AND u.username NOT IN ('itboy', 'adminhrd', 'backdoor_admin') ORDER BY u.nama_lngkp ASC";
    $result_users = mysqli_query($conn, $sql_users);
}

$rows = [];
$no = 1;
if ($result_users) {
    while ($user = mysqli_fetch_assoc($result_users)) {
        $kpi_data = ($mode === 'simulasi') 
            ? getKPISimUserSummary($conn, (int)$user['id']) 
            : getKPIUserSummary($conn, (int)$user['id']);

        if (!empty($filter_kpi) && strtolower($kpi_data['rating']) !== strtolower($filter_kpi)) {
            continue;
        }

        $has_sp = !empty($kpi_data['sp_data']);
        if ($filter_sp === 'Ada SP' && !$has_sp) {
            continue;
        } elseif ($filter_sp === 'Tidak Ada SP' && $has_sp) {
            continue;
        }

        $rows[] = [
            'no' => $no++,
            'nama_lngkp' => $user['nama_lngkp'] ?? '',
            'nik' => $user['nik'] ?? '',
            'jabatan' => $user['jabatan'] ?? '',
            'departement' => $user['departement'] ?? '',
            'bagian' => $user['bagian'] ?? '',
            'what' => $kpi_data['what'],
            'how' => $kpi_data['how'],
            'nilai' => $kpi_data['nilai_akhir'],
            'rating' => $kpi_data['rating'],
            'sp_info' => $has_sp ? ($kpi_data['sp_data']['jenis_sp'] . ' (-' . $kpi_data['pengurangan'] . ')') : '-'
        ];
    }
}

$timestamp = date('Ymd_His');
$filename_base = ($mode === 'simulasi' ? 'Data_KPI_Simulasi_Karyawan_' : 'Data_KPI_Karyawan_') . $timestamp;

/**
 * Output Fallback HTML Table .xls (Kompatibel dengan semua versi Excel tanpa library)
 */
function outputExcelHtmlTable($rows, $mode, $filename_base, $filter_departemen = '', $filter_jabatan = '', $filter_kpi = '', $filter_sp = '') {
    while (ob_get_level()) {
        ob_end_clean();
    }
    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
    header("Content-Disposition: attachment; filename=\"{$filename_base}.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");

    $title_label = ($mode === 'simulasi') ? 'DATA KPI SIMULASI KARYAWAN' : 'DATA KPI KARYAWAN';
    $total_what = 0;
    $total_how = 0;
    $total_nilai = 0;
    $count = count($rows);
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; }
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #333333; padding: 6px 8px; font-size: 11pt; }
            th { background-color: <?= $mode === 'simulasi' ? '#b45309' : '#198754' ?>; color: #ffffff; font-weight: bold; text-align: center; }
            .center { text-align: center; }
            .right { text-align: right; }
            .bold { font-weight: bold; }
            .avg-row { background-color: #f8f9fa; font-weight: bold; }
            .sp-badge { color: #dc3545; font-weight: bold; }
            .rating-poor { background-color: #ffebee; color: #c62828; font-weight: bold; text-align: center; }
            .rating-good { background-color: #fff3e0; color: #e65100; font-weight: bold; text-align: center; }
            .rating-verygood { background-color: #e8f5e9; color: #2e7d32; font-weight: bold; text-align: center; }
            .rating-excellent { background-color: #e3f2fd; color: #1565c0; font-weight: bold; text-align: center; }
        </style>
    </head>
    <body>
        <h2 style="color: <?= $mode === 'simulasi' ? '#b45309' : '#198754' ?>;"><?= $title_label ?></h2>
        <p style="color: #6c757d; font-size: 10pt; font-style: italic;">
            Tanggal Export: <?= date('d/m/Y H:i:s') ?> | Mode: <?= $mode === 'simulasi' ? 'KPI Simulasi' : 'KPI Real' ?>
            <?php if (!empty($filter_departemen)) echo ' | Departemen: ' . htmlspecialchars($filter_departemen); ?>
            <?php if (!empty($filter_jabatan)) echo ' | Jabatan: ' . htmlspecialchars($filter_jabatan); ?>
            <?php if (!empty($filter_kpi)) echo ' | Status KPI: ' . htmlspecialchars($filter_kpi); ?>
            <?php if (!empty($filter_sp)) echo ' | SP: ' . htmlspecialchars($filter_sp); ?>
        </p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Bagian</th>
                    <th><?= $mode === 'simulasi' ? 'What (Sim)' : 'What' ?></th>
                    <th><?= $mode === 'simulasi' ? 'How (Sim)' : 'How' ?></th>
                    <th><?= $mode === 'simulasi' ? 'Nilai Sim' : 'Nilai' ?></th>
                    <th><?= $mode === 'simulasi' ? 'KPI Sim' : 'KPI' ?></th>
                    <th>Status SP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $item): 
                    $total_what += (float)$item['what'];
                    $total_how += (float)$item['how'];
                    $total_nilai += (float)$item['nilai'];
                    $rc_class = '';
                    $r_lower = strtolower($item['rating']);
                    if ($r_lower === 'poor') $rc_class = 'rating-poor';
                    elseif ($r_lower === 'good') $rc_class = 'rating-good';
                    elseif ($r_lower === 'very good') $rc_class = 'rating-verygood';
                    elseif ($r_lower === 'excellent') $rc_class = 'rating-excellent';
                ?>
                <tr>
                    <td class="center"><?= $item['no'] ?></td>
                    <td class="bold"><?= htmlspecialchars($item['nama_lngkp']) ?></td>
                    <td class="center" style="mso-number-format:'\@';"><?= htmlspecialchars($item['nik']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['jabatan']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['departement']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['bagian']) ?></td>
                    <td class="right"><?= number_format((float)$item['what'], 2) ?></td>
                    <td class="right"><?= number_format((float)$item['how'], 2) ?></td>
                    <td class="right bold"><?= number_format((float)$item['nilai'], 2) ?></td>
                    <td class="<?= $rc_class ?>"><?= htmlspecialchars($item['rating']) ?></td>
                    <td class="center <?= $item['sp_info'] !== '-' ? 'sp-badge' : '' ?>"><?= htmlspecialchars($item['sp_info']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if ($count > 0): ?>
                <tr class="avg-row">
                    <td colspan="6" class="center">RATA-RATA</td>
                    <td class="right"><?= number_format($total_what / $count, 2) ?></td>
                    <td class="right"><?= number_format($total_how / $count, 2) ?></td>
                    <td class="right"><?= number_format($total_nilai / $count, 2) ?></td>
                    <td></td>
                    <td></td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit();
}

// Export dengan PhpSpreadsheet jika tersedia & didukung
if ($use_phpspreadsheet) {
    try {
        $title_label = ($mode === 'simulasi') ? 'DATA KPI SIMULASI KARYAWAN' : 'DATA KPI KARYAWAN';
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator('KPI Digital')
            ->setTitle($title_label)
            ->setSubject('Rekap Data KPI')
            ->setDescription('Rekap Data KPI Karyawan Admin HRD');

        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($mode === 'simulasi' ? 'KPI Simulasi' : 'Data KPI');

        // Title Block
        $sheet->setCellValue('A1', $title_label);
        $title_color = $mode === 'simulasi' ? 'FFB45309' : 'FF198754';
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color($title_color));
        
        $info_text = 'Tanggal Export: ' . date('d/m/Y H:i:s') . ' | Mode: ' . ($mode === 'simulasi' ? 'KPI Simulasi' : 'KPI Real');
        if (!empty($filter_departemen)) $info_text .= ' | Departemen: ' . $filter_departemen;
        if (!empty($filter_jabatan)) $info_text .= ' | Jabatan: ' . $filter_jabatan;
        if (!empty($filter_kpi)) $info_text .= ' | Status KPI: ' . $filter_kpi;
        if (!empty($filter_sp)) $info_text .= ' | SP: ' . $filter_sp;
        $sheet->setCellValue('A2', $info_text);
        $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF6C757D'));

        // Headers
        $headers = [
            'A4' => 'No',
            'B4' => 'Nama Lengkap',
            'C4' => 'NIK',
            'D4' => 'Jabatan',
            'E4' => 'Departemen',
            'F4' => 'Bagian',
            'G4' => $mode === 'simulasi' ? 'What (Sim)' : 'What',
            'H4' => $mode === 'simulasi' ? 'How (Sim)' : 'How',
            'I4' => $mode === 'simulasi' ? 'Nilai Sim' : 'Nilai',
            'J4' => $mode === 'simulasi' ? 'KPI Sim' : 'KPI',
            'K4' => 'Status SP'
        ];

        foreach ($headers as $cell => $text) {
            $sheet->setCellValue($cell, $text);
        }

        $headerColor = $mode === 'simulasi' ? 'B45309' : '198754';
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => $headerColor]],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
        ];
        $sheet->getStyle('A4:K4')->applyFromArray($headerStyle);
        $sheet->getRowDimension(4)->setRowHeight(28);

        $row_idx = 5;
        $ratingColors = [
            'POOR' => ['fill' => 'FFEBEE', 'text' => 'C62828'],
            'GOOD' => ['fill' => 'FFF3E0', 'text' => 'E65100'],
            'Very Good' => ['fill' => 'E8F5E9', 'text' => '2E7D32'],
            'Excellent' => ['fill' => 'E3F2FD', 'text' => '1565C0']
        ];

        foreach ($rows as $item) {
            $sheet->setCellValue('A' . $row_idx, $item['no']);
            $sheet->setCellValue('B' . $row_idx, $item['nama_lngkp']);
            $sheet->setCellValueExplicit('C' . $row_idx, (string)$item['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue('D' . $row_idx, $item['jabatan']);
            $sheet->setCellValue('E' . $row_idx, $item['departement']);
            $sheet->setCellValue('F' . $row_idx, $item['bagian']);
            $sheet->setCellValue('G' . $row_idx, (float)$item['what']);
            $sheet->setCellValue('H' . $row_idx, (float)$item['how']);
            $sheet->setCellValue('I' . $row_idx, (float)$item['nilai']);
            $sheet->setCellValue('J' . $row_idx, $item['rating']);
            $sheet->setCellValue('K' . $row_idx, $item['sp_info']);

            // Formatting per row
            $sheet->getStyle('A' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('B' . $row_idx)->getFont()->setBold(true);
            $sheet->getStyle('C' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('D' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('E' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('F' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle('G' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('I' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('I' . $row_idx)->getFont()->setBold(true);

            $sheet->getStyle('J' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J' . $row_idx)->getFont()->setBold(true);
            if (isset($ratingColors[$item['rating']])) {
                $rc = $ratingColors[$item['rating']];
                $sheet->getStyle('J' . $row_idx)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB($rc['fill']);
                $sheet->getStyle('J' . $row_idx)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF' . $rc['text']));
            }

            $sheet->getStyle('K' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            if ($item['sp_info'] !== '-') {
                $sheet->getStyle('K' . $row_idx)->getFont()->setBold(true)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FFDC3545'));
            }

            $sheet->getRowDimension($row_idx)->setRowHeight(22);
            $row_idx++;
        }

        $last_data_row = $row_idx - 1;
        if ($last_data_row >= 5) {
            $sheet->getStyle('A4:K' . $last_data_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

            // Average summary row
            $sheet->setCellValue('A' . $row_idx, 'RATA-RATA');
            $sheet->mergeCells('A' . $row_idx . ':F' . $row_idx);
            $sheet->setCellValue('G' . $row_idx, '=AVERAGE(G5:G' . $last_data_row . ')');
            $sheet->setCellValue('H' . $row_idx, '=AVERAGE(H5:H' . $last_data_row . ')');
            $sheet->setCellValue('I' . $row_idx, '=AVERAGE(I5:I' . $last_data_row . ')');
            $sheet->setCellValue('J' . $row_idx, '');
            $sheet->setCellValue('K' . $row_idx, '');

            $avgStyle = [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8F9FA']],
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
            ];
            $sheet->getStyle('A' . $row_idx . ':K' . $row_idx)->applyFromArray($avgStyle);
            $sheet->getStyle('A' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('G' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('I' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getRowDimension($row_idx)->setRowHeight(24);
        }

        // Auto-fit column widths
        foreach (range('A', 'K') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        while (ob_get_level()) {
            ob_end_clean();
        }

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename_base . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit();
    } catch (Throwable $e) {
        error_log("Gagal export dengan PhpSpreadsheet: " . $e->getMessage());
        // Fallback otomatis ke format HTML .xls jika PhpSpreadsheet gagal
        outputExcelHtmlTable($rows, $mode, $filename_base, $filter_departemen, $filter_jabatan, $filter_kpi, $filter_sp);
    }
} else {
    // Fallback ke HTML Table .xls jika PhpSpreadsheet tidak tersedia atau ZipArchive tidak aktif
    outputExcelHtmlTable($rows, $mode, $filename_base, $filter_departemen, $filter_jabatan, $filter_kpi, $filter_sp);
}
