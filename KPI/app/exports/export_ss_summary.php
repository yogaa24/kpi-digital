<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require_once __DIR__ . '/../../helper/config.php';
require_once __DIR__ . '/../../helper/getUser.php';
require_once __DIR__ . '/../../helper/checkAdmin.php';
require_once __DIR__ . '/../../helper/ss_functions.php';

$autoload_path = __DIR__ . '/../../vendor/autoload.php';
if (file_exists($autoload_path)) {
    require_once $autoload_path;
}

requireAdminHRDOrDirektur();
date_default_timezone_set('Asia/Jakarta');

// Cek dependency PhpSpreadsheet
$use_phpspreadsheet = class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet');

// Filter parameter
$filter_departemen = isset($_GET['departemen']) ? trim($_GET['departemen']) : '';
$filter_jabatan    = isset($_GET['jabatan']) ? trim($_GET['jabatan']) : '';
$filter_bagian     = isset($_GET['bagian']) ? trim($_GET['bagian']) : '';
$filter_status_ss  = isset($_GET['status_ss']) ? trim($_GET['status_ss']) : 'all'; // all, ada, belum, lengkap
$filter_status_karyawan = isset($_GET['status_karyawan']) ? trim($_GET['status_karyawan']) : '';

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
if (!empty($filter_bagian)) {
    $bag_safe = mysqli_real_escape_string($conn, $filter_bagian);
    $where .= " AND u.bagian = '$bag_safe'";
}
if (!empty($filter_status_karyawan)) {
    $stat_safe = mysqli_real_escape_string($conn, $filter_status_karyawan);
    $where .= " AND u.status_karyawan = '$stat_safe'";
} else {
    // Default hanya karyawan aktif yang diexport
    $where .= " AND (u.status_karyawan = 'AKTIF' OR (u.status_karyawan IS NULL AND u.status = 1))";
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

// Ambil summary skill standard seluruh user secara batch
$all_ss_data = getAllUserSSSummary($conn);

$rows = [];
$no = 1;
while ($user = mysqli_fetch_assoc($result_users)) {
    $uid = intval($user['id']);
    $ss = $all_ss_data[$uid] ?? [];

    $avg_umum   = $ss['avg_umum'] ?? null;
    $avg_teknis = $ss['avg_teknis'] ?? null;
    $avg_total  = $ss['avg_total'] ?? null;
    $total_kat  = $ss['total_kat'] ?? 0;
    $total_poin = $ss['total_poin'] ?? 0;
    $kat_umum   = $ss['total_kat_umum'] ?? 0;
    $kat_teknis = $ss['total_kat_teknis'] ?? 0;

    // Tentukan status keterangan SS
    if ($total_kat == 0) {
        $status_label = 'Belum Ada SS';
        $status_type  = 'empty';
    } elseif ($total_poin == 0) {
        $status_label = 'Belum Ada Poin SS';
        $status_type  = 'partial';
    } elseif ($avg_umum !== null && $avg_teknis !== null) {
        $status_label = 'Lengkap (Umum & Teknis)';
        $status_type  = 'complete';
    } elseif ($avg_umum !== null && $avg_teknis === null) {
        $status_label = ($kat_teknis > 0) ? 'Umum Dinilai, Teknis Belum' : 'Hanya SS Umum';
        $status_type  = ($kat_teknis > 0) ? 'partial' : 'complete_part';
    } elseif ($avg_teknis !== null && $avg_umum === null) {
        $status_label = ($kat_umum > 0) ? 'Teknis Dinilai, Umum Belum' : 'Hanya SS Teknis';
        $status_type  = ($kat_umum > 0) ? 'partial' : 'complete_part';
    } else {
        $status_label = 'Belum Dinilai';
        $status_type  = 'pending';
    }

    // Filter berdasarkan status SS
    if ($filter_status_ss === 'ada' && $total_kat == 0) {
        continue;
    } elseif ($filter_status_ss === 'belum' && $total_kat > 0) {
        continue;
    } elseif ($filter_status_ss === 'lengkap' && ($avg_umum === null || $avg_teknis === null)) {
        continue;
    }

    $rows[] = [
        'no'          => $no++,
        'nama_lngkp'  => $user['nama_lngkp'],
        'nik'         => $user['nik'],
        'jabatan'     => $user['jabatan'],
        'departement' => $user['departement'],
        'bagian'      => $user['bagian'],
        'ss_umum'     => $avg_umum,
        'ss_teknis'   => $avg_teknis,
        'ss_total'    => $avg_total,
        'total_kat'   => $total_kat,
        'total_poin'  => $total_poin,
        'status'      => $status_label,
        'status_type' => $status_type,
    ];
}

// Generate nama file
$filename_parts = ['Summary_Skill_Standard'];
if (!empty($filter_departemen)) $filename_parts[] = preg_replace('/[^A-Za-z0-9]/', '_', $filter_departemen);
if (!empty($filter_jabatan))    $filename_parts[] = preg_replace('/[^A-Za-z0-9]/', '_', $filter_jabatan);
if (!empty($filter_bagian))     $filename_parts[] = preg_replace('/[^A-Za-z0-9]/', '_', $filter_bagian);
$filename_parts[] = date('Ymd_His');
$filename_base = implode('_', $filename_parts);

if ($use_phpspreadsheet) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Summary SS'); 

    // Title Block
    $sheet->setCellValue('A1', 'SUMMARY SKILL STANDARD KARYAWAN (UMUM & TEKNIS)');
    $sheet->mergeCells('A1:L1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(16)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF1E3A8A'));
    $sheet->getStyle('A1')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    
    $info_text = 'Tanggal Export: ' . date('d/m/Y H:i:s');
    if (!empty($filter_departemen)) $info_text .= ' | Departemen: ' . $filter_departemen;
    if (!empty($filter_jabatan))    $info_text .= ' | Jabatan: ' . $filter_jabatan;
    if (!empty($filter_bagian))     $info_text .= ' | Bagian: ' . $filter_bagian;
    if ($filter_status_ss !== 'all') {
        $info_text .= ' | Status SS: ' . ($filter_status_ss === 'ada' ? 'Memiliki SS' : ($filter_status_ss === 'belum' ? 'Belum Memiliki SS' : 'Lengkap'));
    }
    $info_text .= ' | Total Karyawan: ' . count($rows);
    $sheet->setCellValue('A2', $info_text);
    $sheet->mergeCells('A2:L2');
    $sheet->getStyle('A2')->getFont()->setItalic(true)->setSize(10)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF6C757D'));
    $sheet->getStyle('A2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

    // Headers
    $headers = [
        'A4' => 'No',
        'B4' => 'Nama Lengkap',
        'C4' => 'NIK',
        'D4' => 'Jabatan',
        'E4' => 'Departemen',
        'F4' => 'Bagian',
        'G4' => 'Nilai SS Umum',
        'H4' => 'Nilai SS Teknis',
        'I4' => 'Rata-rata Total',
        'J4' => 'Total Kategori SS',
        'K4' => 'Total Poin SS',
        'L4' => 'Status SS'
    ];

    foreach ($headers as $cell => $text) {
        $sheet->setCellValue($cell, $text);
    }

    $headerStyle = [
        'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
        'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A8A']], // Dark Navy
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            'vertical'   => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
        ],
        'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN]]
    ];
    $sheet->getStyle('A4:L4')->applyFromArray($headerStyle);
    $sheet->getRowDimension(4)->setRowHeight(28);

    $row_idx = 5;
    $statusColors = [
        'complete'      => ['fill' => 'E8F5E9', 'text' => '2E7D32'], // Hijau
        'complete_part' => ['fill' => 'E3F2FD', 'text' => '1565C0'], // Biru
        'partial'       => ['fill' => 'FFF3E0', 'text' => 'E65100'], // Oranye
        'pending'       => ['fill' => 'FFFDE7', 'text' => 'F57F17'], // Kuning
        'empty'         => ['fill' => 'F5F5F5', 'text' => '757575']  // Abu-abu
    ];

    foreach ($rows as $item) {
        $sheet->setCellValue('A' . $row_idx, $item['no']);
        $sheet->setCellValue('B' . $row_idx, $item['nama_lngkp']);
        $sheet->setCellValueExplicit('C' . $row_idx, (string)$item['nik'], \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
        $sheet->setCellValue('D' . $row_idx, $item['jabatan']);
        $sheet->setCellValue('E' . $row_idx, $item['departement']);
        $sheet->setCellValue('F' . $row_idx, $item['bagian']);
        
        // Nilai SS Umum
        if ($item['ss_umum'] !== null) {
            $sheet->setCellValue('G' . $row_idx, (float)$item['ss_umum']);
            $sheet->getStyle('G' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('G' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        } else {
            $sheet->setCellValue('G' . $row_idx, '-');
            $sheet->getStyle('G' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Nilai SS Teknis
        if ($item['ss_teknis'] !== null) {
            $sheet->setCellValue('H' . $row_idx, (float)$item['ss_teknis']);
            $sheet->getStyle('H' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('H' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        } else {
            $sheet->setCellValue('H' . $row_idx, '-');
            $sheet->getStyle('H' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Rata-rata Total
        if ($item['ss_total'] !== null) {
            $sheet->setCellValue('I' . $row_idx, (float)$item['ss_total']);
            $sheet->getStyle('I' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('I' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            $sheet->getStyle('I' . $row_idx)->getFont()->setBold(true);
        } else {
            $sheet->setCellValue('I' . $row_idx, '-');
            $sheet->getStyle('I' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        $sheet->setCellValue('J' . $row_idx, $item['total_kat']);
        $sheet->setCellValue('K' . $row_idx, $item['total_poin']);
        $sheet->setCellValue('L' . $row_idx, $item['status']);

        // Formatting per row
        $sheet->getStyle('A' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B' . $row_idx)->getFont()->setBold(true);
        $sheet->getStyle('C' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Status color coding
        if (isset($statusColors[$item['status_type']])) {
            $sc = $statusColors[$item['status_type']];
            $sheet->getStyle('L' . $row_idx)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB($sc['fill']);
            $sheet->getStyle('L' . $row_idx)->getFont()->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('FF' . $sc['text']))->setBold(true);
        }

        $sheet->getRowDimension($row_idx)->setRowHeight(22);
        $row_idx++;
    }

    $last_data_row = $row_idx - 1;
    if ($last_data_row >= 5) {
        $sheet->getStyle('A4:L' . $last_data_row)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Baris Rata-rata / Total Keseluruhan
        $sheet->setCellValue('A' . $row_idx, 'RATA-RATA KESELURUHAN');
        $sheet->mergeCells('A' . $row_idx . ':F' . $row_idx);
        $sheet->setCellValue('G' . $row_idx, '=IFERROR(AVERAGE(G5:G' . $last_data_row . '), 0)');
        $sheet->setCellValue('H' . $row_idx, '=IFERROR(AVERAGE(H5:H' . $last_data_row . '), 0)');
        $sheet->setCellValue('I' . $row_idx, '=IFERROR(AVERAGE(I5:I' . $last_data_row . '), 0)');
        $sheet->setCellValue('J' . $row_idx, '=SUM(J5:J' . $last_data_row . ')');
        $sheet->setCellValue('K' . $row_idx, '=SUM(K5:K' . $last_data_row . ')');
        $sheet->setCellValue('L' . $row_idx, '-');

        $avgStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => '1E3A8A'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E9ECEF']],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_MEDIUM]]
        ];
        $sheet->getStyle('A' . $row_idx . ':L' . $row_idx)->applyFromArray($avgStyle);
        $sheet->getStyle('A' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('G' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('H' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('I' . $row_idx)->getNumberFormat()->setFormatCode('#,##0.00');
        $sheet->getStyle('I' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('J' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L' . $row_idx)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $sheet->getRowDimension($row_idx)->setRowHeight(24);
    }

    // Column widths: Kolom A khusus No dibuat ringkas (lebar 6), kolom B-L auto-fit
    $sheet->getColumnDimension('A')->setAutoSize(false)->setWidth(6);
    foreach (range('B', 'L') as $col) {
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
            th { background: #1e3a8a; color: #fff; font-weight: bold; text-align: center; }
            .col-no { width: 50px; text-align: center; }
            .center { text-align: center; }
            .right { text-align: right; }
            .bold { font-weight: bold; }
        </style>
    </head>
    <body>
        <h2>SUMMARY SKILL STANDARD KARYAWAN (UMUM & TEKNIS)</h2>
        <p>Tanggal Export: <?= date('d/m/Y H:i:s') ?></p>
        <table>
            <thead>
                <tr>
                    <th class="col-no" style="width: 50px;">No</th>
                    <th>Nama Lengkap</th>
                    <th>NIK</th>
                    <th>Jabatan</th>
                    <th>Departemen</th>
                    <th>Bagian</th>
                    <th>Nilai SS Umum</th>
                    <th>Nilai SS Teknis</th>
                    <th>Rata-rata Total</th>
                    <th>Total Kategori</th>
                    <th>Total Poin</th>
                    <th>Status SS</th>
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
                    <td class="right"><?= $item['ss_umum'] !== null ? number_format($item['ss_umum'], 2) : '-' ?></td>
                    <td class="right"><?= $item['ss_teknis'] !== null ? number_format($item['ss_teknis'], 2) : '-' ?></td>
                    <td class="right bold"><?= $item['ss_total'] !== null ? number_format($item['ss_total'], 2) : '-' ?></td>
                    <td class="center"><?= $item['total_kat'] ?></td>
                    <td class="center"><?= $item['total_poin'] ?></td>
                    <td class="center"><?= htmlspecialchars($item['status']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </body>
    </html>
    <?php
    exit();
}
