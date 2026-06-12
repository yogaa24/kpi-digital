<?php
require __DIR__ . '/../vendor/autoload.php';

$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Import Skill Standard');

$headers = ['Kategori SS', 'Poin SS', 'Nilai 1', 'Nilai 2', 'Nilai 3', 'Nilai 4', 'Nilai Bulan Ini', 'Deskripsi Penilaian'];
$sheet->fromArray($headers, null, 'A1');
$sheet->fromArray([
    [
        'Leadership',
        'Mampu membuat dan menerapkan KPI untuk dirinya sendiri',
        'Mengisi KPI Sendiri dari bukti pendukung',
        'Membuat Simulasi',
        'Membuat Simulasi & Next Step',
        'Membuat Simulasi, Membuat Next Step, bisa menganalisa terkait How atau ada poin yang sudah tidak sesuai',
        3.5,
        'Sudah mampu membuat simulasi KPI dan menyiapkan next step.'
    ],
    [
        'Leadership',
        'Mampu memimpin dengan data',
        'Belum menggunakan data',
        'Kadang menggunakan data',
        'Sering menggunakan data',
        'Konsisten memimpin berbasis data',
        3,
        'Mulai rutin menggunakan data saat evaluasi pekerjaan.'
    ],
    [
        'Komunikasi',
        'Mampu menyampaikan ide dengan jelas',
        'Belum jelas',
        'Cukup jelas',
        'Jelas',
        'Sangat jelas dan terstruktur',
        '',
        ''
    ],
], null, 'A2');

$sheet->getStyle('A1:H1')->getFont()->setBold(true);
$sheet->getStyle('A1:H1')->getFill()
    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
    ->getStartColor()->setARGB('FFD9EAD3');
$sheet->freezePane('A2');
$sheet->getStyle('A:H')->getAlignment()->setWrapText(true);
$sheet->getStyle('G:G')->getNumberFormat()->setFormatCode('0.00');

foreach (range('A', 'H') as $column) {
    $sheet->getColumnDimension($column)->setAutoSize(true);
}

$targetDir = __DIR__ . '/../assets/template';
if (!is_dir($targetDir)) {
    mkdir($targetDir, 0777, true);
}

$writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
$writer->save($targetDir . '/template_import_skill_standard.xlsx');

echo "Template created: assets/template/template_import_skill_standard.xlsx\n";
