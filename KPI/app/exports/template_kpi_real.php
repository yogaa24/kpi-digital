<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_user'])) {
    header('Location: index');
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/kpi_real_import_functions.php';
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$targetUserId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$userResult = mysqli_query($conn, "SELECT nama_lngkp, nik, atasan, departement FROM tb_users WHERE id=$targetUserId LIMIT 1");
$targetUser = $userResult ? mysqli_fetch_assoc($userResult) : null;
if (!$targetUser) {
    http_response_code(404);
    exit('Anggota tidak ditemukan.');
}
if (!canImportKPIRealForMember(
    $id_user,
    $targetUserId,
    $leveel,
    $nama_lngkp,
    $departement,
    $targetUser['atasan'],
    $targetUser['departement']
)) {
    http_response_code(403);
    exit('Anda tidak memiliki akses untuk mengunduh template KPI Real anggota ini.');
}

// Get Bobot What & How from tb_bobotkpi
$bobotWhat = 60;
$bobotHow = 40;
$qBobot = mysqli_query($conn, "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user=$targetUserId LIMIT 1");
if ($qBobot && ($rb = mysqli_fetch_assoc($qBobot))) {
    if ($rb['bobotwhat'] !== null && $rb['bobotwhat'] !== '') {
        $bobotWhat = (float) $rb['bobotwhat'];
    }
    if ($rb['bobothow'] !== null && $rb['bobothow'] !== '') {
        $bobotHow = (float) $rb['bobothow'];
    }
}

$spreadsheet = new Spreadsheet();

// =========================================================
// Sheet 0: DATA KPI REAL
// =========================================================
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('DATA KPI REAL');

$headers = ['JENIS', 'BOBOT WHAT / HOW (%)', 'POIN KPI', 'BOBOT POIN KPI (%)', 'INDIKATOR KPI', 'TIPE', 'BOBOT (%)', 'TARGET'];
$dataSheet->fromArray($headers, null, 'A1');

$widths = [
    'A' => 12,
    'B' => 25,
    'C' => 38,
    'D' => 24,
    'E' => 58,
    'F' => 10,
    'G' => 14,
    'H' => 18,
];
foreach ($widths as $col => $w) {
    $dataSheet->getColumnDimension($col)->setWidth($w);
}

$headerStyle = $dataSheet->getStyle('A1:H1');
$headerStyle->getFont()->setName('Calibri')->setSize(11)->setBold(true)->getColor()->setARGB('FFFFFFFF');
$headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF198754');
$headerStyle->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
$headerStyle->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
$dataSheet->freezePane('A2');

$rowIndex = 2;

// Fetch WHAT items
$qWhats = mysqli_query($conn, "SELECT w.id_what, w.p_what, w.tipe_what, w.bobot, w.target_omset, k.poin, k.bobot AS bobot_kpi 
    FROM tb_whats w 
    JOIN tb_kpi k ON k.id=w.id_kpi 
    WHERE w.id_user=$targetUserId 
    ORDER BY w.id_what ASC");
if ($qWhats) {
    while ($w = mysqli_fetch_assoc($qWhats)) {
        $tipe = strtoupper(trim((string) $w['tipe_what']));
        $bobot = (float) $w['bobot'];
        $bobotKpi = (float) $w['bobot_kpi'];
        $target = ($tipe === 'B' && (float) $w['target_omset'] > 0) ? (float) $w['target_omset'] : '';

        $dataSheet->setCellValue('A' . $rowIndex, 'WHAT');
        $dataSheet->setCellValue('B' . $rowIndex, $bobotWhat);
        $dataSheet->setCellValue('C' . $rowIndex, $w['poin']);
        $dataSheet->setCellValue('D' . $rowIndex, $bobotKpi);
        $dataSheet->setCellValue('E' . $rowIndex, $w['p_what']);
        $dataSheet->setCellValue('F' . $rowIndex, $tipe);
        $dataSheet->setCellValue('G' . $rowIndex, $bobot);
        $dataSheet->setCellValue('H' . $rowIndex, $target);

        $rowIndex++;
    }
}

// Fetch HOW items
$qHows = mysqli_query($conn, "SELECT h.id_how, h.p_how, h.tipe_how, h.bobot, h.target_omset, k.poin2, k.bobot2 AS bobot_kpi 
    FROM tb_hows h 
    JOIN tb_kpi k ON k.id=h.id_kpi 
    WHERE h.id_user=$targetUserId 
    ORDER BY h.id_how ASC");
if ($qHows) {
    while ($h = mysqli_fetch_assoc($qHows)) {
        $tipe = strtoupper(trim((string) $h['tipe_how']));
        $bobot = (float) $h['bobot'];
        $bobotKpi = (float) $h['bobot_kpi'];
        $target = ($tipe === 'B' && (float) $h['target_omset'] > 0) ? (float) $h['target_omset'] : '';

        $dataSheet->setCellValue('A' . $rowIndex, 'HOW');
        $dataSheet->setCellValue('B' . $rowIndex, $bobotHow);
        $dataSheet->setCellValue('C' . $rowIndex, $h['poin2']);
        $dataSheet->setCellValue('D' . $rowIndex, $bobotKpi);
        $dataSheet->setCellValue('E' . $rowIndex, $h['p_how']);
        $dataSheet->setCellValue('F' . $rowIndex, $tipe);
        $dataSheet->setCellValue('G' . $rowIndex, $bobot);
        $dataSheet->setCellValue('H' . $rowIndex, $target);

        $rowIndex++;
    }
}

$lastRow = $rowIndex - 1;
if ($lastRow >= 2) {
    $dataSheet->getStyle("A2:H{$lastRow}")->getFont()->setName('Calibri')->setSize(11);
    $dataSheet->getStyle("A2:H{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    $dataSheet->getStyle("C2:C{$lastRow}")->getAlignment()->setWrapText(true);
    $dataSheet->getStyle("E2:E{$lastRow}")->getAlignment()->setWrapText(true);
    
    $dataSheet->getStyle("A2:B{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $dataSheet->getStyle("D2:D{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    $dataSheet->getStyle("F2:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
    
    $dataSheet->getStyle("B2:B{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    $dataSheet->getStyle("D2:D{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');
    $dataSheet->getStyle("G2:H{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

    for ($r = 2; $r <= $lastRow; $r++) {
        $tipe = $dataSheet->getCell('F' . $r)->getValue();
        $dataSheet->getStyle("A{$r}:G{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');

        if ($tipe === 'B') {
            $dataSheet->getStyle("H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        } else {
            $dataSheet->getStyle("H{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        }
    }
}

// =========================================================
// Sheet 1: PANDUAN
// =========================================================
$guideSheet = $spreadsheet->createSheet();
$guideSheet->setTitle('PANDUAN');

$guideSheet->getColumnDimension('A')->setWidth(30);
$guideSheet->getColumnDimension('B')->setWidth(50);

$guideSheet->setCellValue('A1', 'TEMPLATE KPI REAL ANGGOTA');
$guideSheet->mergeCells('A1:B1');
$guideHeaderStyle = $guideSheet->getStyle('A1:B1');
$guideHeaderStyle->getFont()->setName('Calibri')->setSize(14)->setBold(true)->getColor()->setARGB('FFFFFFFF');
$guideHeaderStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF198754');

$guideSheet->setCellValue('A3', 'Nama Anggota');
$guideSheet->setCellValue('B3', $targetUser['nama_lngkp']);
$guideSheet->setCellValue('A4', 'NIK');
$guideSheet->setCellValueExplicit('B4', (string) $targetUser['nik'], DataType::TYPE_STRING);
$guideSheet->setCellValue('A5', 'Departemen');
$guideSheet->setCellValue('B5', $targetUser['departement']);
$guideSheet->setCellValue('A6', 'Bobot WHAT');
$guideSheet->setCellValue('B6', $bobotWhat . ' %');
$guideSheet->setCellValue('A7', 'Bobot HOW');
$guideSheet->setCellValue('B7', $bobotHow . ' %');

$guideSheet->setCellValue('A9', 'Keterangan Template');
$instructions = [
    '1. Sheet DATA KPI REAL memuat struktur KPI anggota yang terdiri dari aspek WHAT dan HOW.',
    '2. Kolom BOBOT WHAT / HOW (%) menunjukkan proporsi total aspek WHAT dan HOW.',
    '3. Kolom BOBOT POIN KPI (%) menunjukkan bobot masing-masing Poin KPI.',
    '4. Kolom BOBOT (%) menunjukkan bobot masing-masing Indikator KPI.',
    '5. Untuk indikator bertipe B, kolom TARGET dapat diisi/disesuaikan dengan angka target.',
    '6. Simpan file dalam format .xlsx jika ingin mengunggah kembali melalui fitur Import KPI Real.',
];
foreach ($instructions as $idx => $instr) {
    $guideSheet->setCellValue('A' . (10 + $idx), $instr);
    $guideSheet->mergeCells('A' . (10 + $idx) . ':B' . (10 + $idx));
}
$guideSheet->getStyle('A3:B7')->getFont()->setName('Calibri')->setSize(11);
$guideSheet->getStyle('A9:B16')->getFont()->setName('Calibri')->setSize(11);

// =========================================================
// Sheet 2: PILIHAN NILAI
// =========================================================
$choiceSheet = $spreadsheet->createSheet();
$choiceSheet->setTitle('PILIHAN NILAI');

$choiceHeaders = ['JENIS', 'POIN KPI', 'INDIKATOR KPI', 'HASIL / PILIHAN', 'NILAI'];
$choiceSheet->fromArray($choiceHeaders, null, 'A1');

$choiceWidths = ['A' => 12, 'B' => 34, 'C' => 58, 'D' => 58, 'E' => 14];
foreach ($choiceWidths as $col => $w) {
    $choiceSheet->getColumnDimension($col)->setWidth($w);
}

$choiceHeaderStyle = $choiceSheet->getStyle('A1:E1');
$choiceHeaderStyle->getFont()->setName('Calibri')->setSize(11)->setBold(true)->getColor()->setARGB('FFFFFFFF');
$choiceHeaderStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF0D6EFD');
$choiceSheet->freezePane('A2');

$cRowIndex = 2;

// WHAT indicators
$qIndWhats = mysqli_query($conn, "SELECT iw.keterangan, iw.nilai, w.p_what, k.poin 
    FROM tb_indikator_whats iw 
    JOIN tb_whats w ON w.id_what=iw.id_what 
    JOIN tb_kpi k ON k.id=w.id_kpi 
    WHERE w.id_user=$targetUserId 
    ORDER BY w.id_what ASC, iw.urutan ASC, iw.id_indikator ASC");
if ($qIndWhats) {
    while ($iw = mysqli_fetch_assoc($qIndWhats)) {
        $choiceSheet->setCellValue('A' . $cRowIndex, 'WHAT');
        $choiceSheet->setCellValue('B' . $cRowIndex, $iw['poin']);
        $choiceSheet->setCellValue('C' . $cRowIndex, $iw['p_what']);
        $choiceSheet->setCellValue('D' . $cRowIndex, $iw['keterangan']);
        $scoreVal = (float) $iw['nilai'];
        $choiceSheet->setCellValue('E' . $cRowIndex, $scoreVal > 0 ? $scoreVal : '');
        $cRowIndex++;
    }
}

// HOW indicators
$qIndHows = mysqli_query($conn, "SELECT ih.keterangan, ih.nilai, h.p_how, k.poin2 
    FROM tb_indikator_hows ih 
    JOIN tb_hows h ON h.id_how=ih.id_how 
    JOIN tb_kpi k ON k.id=h.id_kpi 
    WHERE h.id_user=$targetUserId 
    ORDER BY h.id_how ASC, ih.urutan ASC, ih.id_indikator ASC");
if ($qIndHows) {
    while ($ih = mysqli_fetch_assoc($qIndHows)) {
        $choiceSheet->setCellValue('A' . $cRowIndex, 'HOW');
        $choiceSheet->setCellValue('B' . $cRowIndex, $ih['poin2']);
        $choiceSheet->setCellValue('C' . $cRowIndex, $ih['p_how']);
        $choiceSheet->setCellValue('D' . $cRowIndex, $ih['keterangan']);
        $scoreVal = (float) $ih['nilai'];
        $choiceSheet->setCellValue('E' . $cRowIndex, $scoreVal > 0 ? $scoreVal : '');
        $cRowIndex++;
    }
}

$cLastRow = $cRowIndex - 1;
if ($cLastRow >= 2) {
    $choiceSheet->getStyle("A2:E{$cLastRow}")->getFont()->setName('Calibri')->setSize(11);
    $choiceSheet->getStyle("C2:D{$cLastRow}")->getAlignment()->setWrapText(true);
}

$spreadsheet->setActiveSheetIndex(0);

$safeNik = preg_replace('/[^A-Za-z0-9_-]/', '_', (string) $targetUser['nik']);
$filename = 'template_kpi_real_' . $safeNik . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit();
