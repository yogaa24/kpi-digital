<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require_once __DIR__ . '/../../helper/config.php';
require_once __DIR__ . '/../../helper/getUser.php';
require_once __DIR__ . '/../../helper/checkAdmin.php';
require_once __DIR__ . '/../../helper/sp_functions.php';

$autoload_path = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

requireAdminHRDOrDirektur();
updateExpiredSP($conn);
date_default_timezone_set('Asia/Jakarta');

// Cek dependency PhpSpreadsheet
$use_phpspreadsheet = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');

// Filter parameter
$mode              = isset($_GET['mode']) && $_GET['mode'] === 'simulasi' ? 'simulasi' : 'real';
$filter_departemen = isset($_GET['departemen']) ? trim($_GET['departemen']) : '';
$filter_jabatan    = isset($_GET['jabatan']) ? trim($_GET['jabatan']) : '';
$filter_kpi        = isset($_GET['status_kpi']) ? trim($_GET['status_kpi']) : '';
$filter_sp         = isset($_GET['status_sp']) ? trim($_GET['status_sp']) : '';

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
    $sql = "SELECT * FROM tbsim_kpi WHERE id_user='$id'";
    $result = mysqli_query($conn, $sql);
    $exists = $result && mysqli_num_rows($result) > 0;

    $totalws = 0;
    if ($result) {
        while ($hasils = mysqli_fetch_assoc($result)) {
            $sql3s = "SELECT SUM(total) as total FROM tbsim_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
            $result3s = mysqli_query($conn, $sql3s);
            $row3sd = mysqli_fetch_assoc($result3s);
            $totalnilaisd = $row3sd['total'] ?? 0;
            $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
            $totalws += $nilaiws;
        }
    }

    $bobotwhat = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tbsim_bobotkpi WHERE id_user=$id";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotwhat = $row5a['bw'];
    }
    $nilaiwhat = ($totalws * $bobotwhat) / 100;

    $totalhfg = 0;
    $resultfg = mysqli_query($conn, $sql);
    if ($resultfg) {
        while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
            $sql7fg = "SELECT SUM(total) as totalh FROM tbsim_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
            $result7fg = mysqli_query($conn, $sql7fg);
            $row7fg = mysqli_fetch_assoc($result7fg);
            $totalnilaihfg = $row7fg['totalh'] ?? 0;
            $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
            $totalhfg += $nilaihfg;
        }
    }

    $bobothow = 0;
    $sql8a = "SELECT bobothow as bh FROM tbsim_bobotkpi WHERE id_user=$id";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobothow = $row8a['bh'];
    }
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
    $bobotwhat = 0;
    $bobothow = 0;
    $sql_bobot = "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user = $id_user LIMIT 1";
    $res_bobot = mysqli_query($conn, $sql_bobot);
    if ($b = mysqli_fetch_assoc($res_bobot)) {
        $bobotwhat = floatval($b['bobotwhat']);
        $bobothow = floatval($b['bobothow']);
    }

    $sql_kpi = "SELECT id, bobot, bobot2 FROM tb_kpi WHERE id_user = $id_user";
    $res_kpi = mysqli_query($conn, $sql_kpi);
    $totalws = 0;
    $totalhfg = 0;
    while ($k = mysqli_fetch_assoc($res_kpi)) {
        $id_kpi = intval($k['id']);
        $bobot1 = floatval($k['bobot']);
        $bobot2 = floatval($k['bobot2']);

        $res_w = mysqli_query($conn, "SELECT SUM(total) as tot FROM tb_whats WHERE id_user = $id_user AND id_kpi = $id_kpi");
        $rw = mysqli_fetch_assoc($res_w);
        $totalws += ((floatval($rw['tot'] ?? 0) * $bobot1) / 100);

        $res_h = mysqli_query($conn, "SELECT SUM(total) as tot FROM tb_hows WHERE id_user = $id_user AND id_kpi = $id_kpi");
        $rh = mysqli_fetch_assoc($res_h);
        $totalhfg += ((floatval($rh['tot'] ?? 0) * $bobot2) / 100);
    }

    $what_val = ($totalws * $bobotwhat) / 100;
    $how_val = ($totalhfg * $bobothow) / 100;
    $nilai_asli = $what_val + $how_val;

    $sp_res = calculateKPIWithSP($conn, $id_user, $nilai_asli);
    $nilai_akhir = $sp_res['nilai_akhir'];

    return [
        'what' => $what_val,
        'how' => $how_val,
        'nilai_asli' => $nilai_asli,
        'nilai_akhir' => $nilai_akhir,
        'rating' => getkpi_export($nilai_akhir),
        'sp_data' => $sp_res['sp_data'],
        'pengurangan' => $sp_res['pengurangan'],
        'exists' => true
    ];
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

$rows = [];
$no = 1;
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
        'nama_lngkp' => $user['nama_lngkp'],
        'nik' => $user['nik'],
        'jabatan' => $user['jabatan'],
        'departement' => $user['departement'],
        'bagian' => $user['bagian'],
        'what' => $kpi_data['what'],
        'how' => $kpi_data['how'],
        'nilai' => $kpi_data['nilai_akhir'],
        'rating' => $kpi_data['rating'],
        'sp_info' => $has_sp ? $kpi_data['sp_data']['jenis_sp'] . ' (-' . $kpi_data['pengurangan'] . ')' : '-'
    ];
}

$timestamp = date('Ymd_His');
$filename_base = ($mode === 'simulasi' ? 'Data_KPI_Simulasi_Karyawan_' : 'Data_KPI_Karyawan_') . $timestamp;

// Export dengan PhpSpreadsheet jika tersedia
if ($use_phpspreadsheet) {
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

    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '198754']],
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
        $sheet->setCellValueExplicit('C' . $row_idx, $item['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('D' . $row_idx, $item['jabatan']);
        $sheet->setCellValue('E' . $row_idx, $item['departement']);
        $sheet->setCellValue('F' . $row_idx, $item['bagian']);
        $sheet->setCellValue('G' . $row_idx, $item['what']);
        $sheet->setCellValue('H' . $row_idx, $item['how']);
        $sheet->setCellValue('I' . $row_idx, $item['nilai']);
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
} else {
    // Fallback ke HTML Table .xls jika PhpSpreadsheet tidak tersedia
    while (ob_get_level()) {
        ob_end_clean();
    }
    header("Content-Type: application/vnd.ms-excel");
    header("Content-Disposition: attachment; filename=\"{$filename_base}.xls\"");
    header("Pragma: no-cache");
    header("Expires: 0");
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            table { border-collapse: collapse; width: 100%; }
            th, td { border: 1px solid #333; padding: 6px; }
            th { background: #198754; color: #fff; font-weight: bold; text-align: center; }
            .center { text-align: center; }
            .right { text-align: right; }
            .bold { font-weight: bold; }
        </style>
    </head>
    <body>
        <h2>DATA KPI KARYAWAN</h2>
        <p>Tanggal Export: <?= date('d/m/Y H:i:s') ?></p>
        <table>
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Bagian</th>
                    <th>What</th>
                    <th>How</th>
                    <th>Nilai</th>
                    <th>KPI</th>
                    <th>Status SP</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $item): ?>
                <tr>
                    <td class="center"><?= $item['no'] ?></td>
                    <td class="bold"><?= htmlspecialchars($item['nama_lngkp']) ?></td>
                    <td class="center">'<?= htmlspecialchars($item['nik']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['jabatan']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['departement']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['bagian']) ?></td>
                    <td class="right"><?= number_format($item['what'], 2) ?></td>
                    <td class="right"><?= number_format($item['how'], 2) ?></td>
                    <td class="right bold"><?= number_format($item['nilai'], 2) ?></td>
                    <td class="center bold"><?= htmlspecialchars($item['rating']) ?></td>
                    <td class="center"><?= htmlspecialchars($item['sp_info']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit();
}
