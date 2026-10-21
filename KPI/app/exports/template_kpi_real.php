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

$spreadsheet = new Spreadsheet();

// =========================================================
// Sheet 0: DATA KPI REAL
// =========================================================
$dataSheet = $spreadsheet->getActiveSheet();
$dataSheet->setTitle('DATA KPI REAL');

$headers = ['JENIS', 'POIN KPI', 'INDIKATOR KPI', 'TIPE', 'BOBOT (%)', 'TARGET', 'NILAI (TIPE A)'];
$dataSheet->fromArray($headers, null, 'A1');

$widths = ['A' => 12, 'B' => 34, 'C' => 58, 'D' => 10, 'E' => 13, 'F' => 18, 'G' => 20];
foreach ($widths as $col => $w) {
    $dataSheet->getColumnDimension($col)->setWidth($w);
}

$headerStyle = $dataSheet->getStyle('A1:G1');
$headerStyle->getFont()->setName('Calibri')->setSize(11)->setBold(true)->getColor()->setARGB('FFFFFFFF');
$headerStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF198754');
$headerStyle->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
$dataSheet->freezePane('A2');

$rowIndex = 2;

// Fetch WHAT items
$qWhats = mysqli_query($conn, "SELECT w.id_what, w.p_what, w.tipe_what, w.bobot, w.target_omset, k.poin 
    FROM tb_whats w 
    JOIN tb_kpi k ON k.id=w.id_kpi 
    WHERE w.id_user=$targetUserId 
    ORDER BY w.id_what ASC");
if ($qWhats) {
    while ($w = mysqli_fetch_assoc($qWhats)) {
        $tipe = strtoupper(trim((string) $w['tipe_what']));
        $bobot = (float) $w['bobot'];
        $target = ($tipe === 'B' && (float) $w['target_omset'] > 0) ? (float) $w['target_omset'] : '';

        $dataSheet->setCellValue('A' . $rowIndex, 'WHAT');
        $dataSheet->setCellValue('B' . $rowIndex, $w['poin']);
        $dataSheet->setCellValue('C' . $rowIndex, $w['p_what']);
        $dataSheet->setCellValue('D' . $rowIndex, $tipe);
        $dataSheet->setCellValue('E' . $rowIndex, $bobot);
        $dataSheet->setCellValue('F' . $rowIndex, $target);
        $dataSheet->setCellValue('G' . $rowIndex, '');

        $rowIndex++;
    }
}

// Fetch HOW items
$qHows = mysqli_query($conn, "SELECT h.id_how, h.p_how, h.tipe_how, h.bobot, h.target_omset, k.poin2 
    FROM tb_hows h 
    JOIN tb_kpi k ON k.id=h.id_kpi 
    WHERE h.id_user=$targetUserId 
    ORDER BY h.id_how ASC");
if ($qHows) {
    while ($h = mysqli_fetch_assoc($qHows)) {
        $tipe = strtoupper(trim((string) $h['tipe_how']));
        $bobot = (float) $h['bobot'];
        $target = ($tipe === 'B' && (float) $h['target_omset'] > 0) ? (float) $h['target_omset'] : '';

        $dataSheet->setCellValue('A' . $rowIndex, 'HOW');
        $dataSheet->setCellValue('B' . $rowIndex, $h['poin2']);
        $dataSheet->setCellValue('C' . $rowIndex, $h['p_how']);
        $dataSheet->setCellValue('D' . $rowIndex, $tipe);
        $dataSheet->setCellValue('E' . $rowIndex, $bobot);
        $dataSheet->setCellValue('F' . $rowIndex, $target);
        $dataSheet->setCellValue('G' . $rowIndex, '');

        $rowIndex++;
    }
}

$lastRow = $rowIndex - 1;
if ($lastRow >= 2) {
    $dataSheet->getStyle("A2:G{$lastRow}")->getFont()->setName('Calibri')->setSize(11);
    $dataSheet->getStyle("A2:G{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
    $dataSheet->getStyle("B2:C{$lastRow}")->getAlignment()->setWrapText(true);
    $dataSheet->getStyle("E2:G{$lastRow}")->getNumberFormat()->setFormatCode('#,##0.00');

    for ($r = 2; $r <= $lastRow; $r++) {
        $tipe = $dataSheet->getCell('D' . $r)->getValue();
        $dataSheet->getStyle("A{$r}:E{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');

        if ($tipe === 'B') {
            $dataSheet->getStyle("F{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
            $dataSheet->getStyle("G{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
        } else {
            $dataSheet->getStyle("F{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');
            $dataSheet->getStyle("G{$r}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF2CC');
        }
    }
}

// =========================================================
// Sheet 1: PANDUAN
// =========================================================
$guideSheet = $spreadsheet->createSheet();
$guideSheet->setTitle('PANDUAN');

$guideSheet->getColumnDimension('A')->setWidth(110);
$guideSheet->getColumnDimension('B')->setWidth(35);

$guideSheet->setCellValue('A1', 'TEMPLATE IMPORT KPI REAL');
$guideSheet->mergeCells('A1:F1');
$guideHeaderStyle = $guideSheet->getStyle('A1:F1');
$guideHeaderStyle->getFont()->setName('Calibri')->setSize(16)->setBold(true)->getColor()->setARGB('FFFFFFFF');
$guideHeaderStyle->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FF198754');

$guideSheet->setCellValue('A3', 'Nama Anggota');
$guideSheet->setCellValue('B3', $targetUser['nama_lngkp']);
$guideSheet->setCellValue('A4', 'NIK');
$guideSheet->setCellValueExplicit('B4', (string) $targetUser['nik'], DataType::TYPE_STRING);

$guideSheet->setCellValue('A6', 'Cara mengisi');
$instructions = [
    '1. Jangan mengubah kolom JENIS, POIN KPI, INDIKATOR KPI, TIPE, dan BOBOT.',
    '2. Isi hanya sel berwarna kuning.',
    '3. Untuk tipe A: isi kolom NILAI (TIPE A) sesuai angka pada sheet PILIHAN NILAI.',
    '4. Untuk tipe B: pastikan TARGET benar lalu isi HASIL REALISASI dengan angka.',
    '5. Baris tanpa hasil/nilai akan dilewati, sehingga Anda boleh hanya mengisi item yang ingin diperbarui.',
    '6. Simpan file sebagai .xlsx lalu unggah melalui tombol Import KPI Real pada halaman anggota.',
];
foreach ($instructions as $idx => $instr) {
    $guideSheet->setCellValue('A' . (7 + $idx), $instr);
}
$guideSheet->getStyle('A3:B12')->getFont()->setName('Calibri')->setSize(11);

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
