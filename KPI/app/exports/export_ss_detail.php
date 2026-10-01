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

requireAdminHRDOrDirektur();
date_default_timezone_set('Asia/Jakarta');

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function safeExportFilename($value) {
    $value = preg_replace('/[^A-Za-z0-9_\-\s]+/', '_', (string)$value);
    $value = trim($value, '_ ');
    return $value !== '' ? $value : 'Karyawan';
}

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

// Filter khusus status SS dengan summary
$all_ss_data = getAllUserSSSummary($conn);
$allowed_users = [];
$sql_users = "SELECT u.id FROM tb_users u $where";
$res_users = mysqli_query($conn, $sql_users);
while ($u = mysqli_fetch_assoc($res_users)) {
    $uid = intval($u['id']);
    $ss = $all_ss_data[$uid] ?? [];
    $total_kat  = $ss['total_kat'] ?? 0;
    $avg_umum   = $ss['avg_umum'] ?? null;
    $avg_teknis = $ss['avg_teknis'] ?? null;

    if ($filter_status_ss === 'ada' && $total_kat == 0) continue;
    if ($filter_status_ss === 'belum' && $total_kat > 0) continue;
    if ($filter_status_ss === 'lengkap' && ($avg_umum === null || $avg_teknis === null)) continue;
    
    $allowed_users[] = $uid;
}

if (empty($allowed_users)) {
    echo "Tidak ada data yang sesuai filter.";
    exit;
}

$ids_str = implode(',', $allowed_users);
$where_detail = " AND u.id IN ($ids_str) ";

$sql_detail = "SELECT u.id AS id_user, u.nama_lngkp, u.nik, u.jabatan, u.departement, u.bagian,
               s.tipe_ss, s.poin_ss AS kategori,
               sp.poinss AS pertanyaan, sp.nilaiss, sp.deskripsi
               FROM tb_users u
               JOIN tb_ss s ON s.id_user = u.id
               JOIN tb_sspoin sp ON sp.id_ss = s.id_poinss AND sp.id_user = u.id
               WHERE 1=1 $where_detail
               ORDER BY 
                 CASE
                     WHEN u.jabatan = 'Kadep' THEN 1
                     WHEN u.jabatan = 'Koordinator' THEN 2
                     WHEN u.jabatan = 'Manager' THEN 3
                     WHEN u.jabatan = 'Karyawan' THEN 4
                     ELSE 5
                 END,
                 u.nama_lngkp ASC, s.tipe_ss, s.poin_ss, sp.poinss";

$result_detail = mysqli_query($conn, $sql_detail);

$users_data = [];
if ($result_detail) {
    while ($row = mysqli_fetch_assoc($result_detail)) {
        $uid = $row['id_user'];
        if (!isset($users_data[$uid])) {
            $users_data[$uid] = [
                'info' => [
                    'nama_lngkp'  => $row['nama_lngkp'],
                    'nik'         => $row['nik'],
                    'jabatan'     => $row['jabatan'],
                    'departement' => $row['departement'],
                    'bagian'      => $row['bagian'],
                ],
                'details' => []
            ];
        }
        $users_data[$uid]['details'][] = $row;
    }
}

function renderIndividualSSXls($user_info, $details) {
    ob_start();
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 18px; }
        th, td { border: 1px solid #111; padding: 6px; vertical-align: top; }
        th { background: #1e3a8a; color: #fff; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; text-align: center; border: none; }
        .header-info { font-weight: bold; border: none; }
        .center { text-align: center; }
        .right { text-align: right; }
        .bold { font-weight: bold; }
    </style>
</head>
<body>
    <table border="0">
        <tr><td colspan="6" class="title">DETAIL SKILL STANDARD KARYAWAN</td></tr>
        <tr><td colspan="6" style="border:none;">&nbsp;</td></tr>
        <tr><td class="header-info">Nama</td><td colspan="5" class="header-info">: <?= h($user_info['nama_lngkp']) ?></td></tr>
        <tr><td class="header-info">NIK</td><td colspan="5" class="header-info">: <?= h($user_info['nik']) ?></td></tr>
        <tr><td class="header-info">Jabatan</td><td colspan="5" class="header-info">: <?= h($user_info['jabatan']) ?></td></tr>
        <tr><td class="header-info">Bagian</td><td colspan="5" class="header-info">: <?= h($user_info['bagian']) ?></td></tr>
        <tr><td class="header-info">Departemen</td><td colspan="5" class="header-info">: <?= h($user_info['departement']) ?></td></tr>
        <tr><td class="header-info">Tanggal Export</td><td colspan="5" class="header-info">: <?= date('d/m/Y H:i:s') ?></td></tr>
    </table>

    <table>
        <thead>
            <tr>
                <th style="width: 50px;">No</th>
                <th>Tipe SS</th>
                <th>Kategori</th>
                <th>Pertanyaan / Parameter</th>
                <th>Nilai SS</th>
                <th>Deskripsi</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $no = 1;
            foreach ($details as $item): ?>
            <tr>
                <td class="center"><?= $no++ ?></td>
                <td class="center"><?= h(ucfirst($item['tipe_ss'])) ?></td>
                <td><?= h($item['kategori']) ?></td>
                <td><?= h($item['pertanyaan']) ?></td>
                <td class="right"><?= $item['nilaiss'] > 0 ? number_format($item['nilaiss'], 2) : '0' ?></td>
                <td><?= h($item['deskripsi']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
    <?php
    return ob_get_clean();
}

if (!class_exists('ZipArchive')) {
    die("Ekstensi ZipArchive belum aktif di PHP.");
}

$zipFilename = 'Export_Detail_SS_' . date('Ymd_His') . '.zip';
$tmpZip = tempnam(sys_get_temp_dir(), 'ss_detail_');
$zip = new ZipArchive();

if ($zip->open($tmpZip, ZipArchive::OVERWRITE) !== true) {
    die("Gagal membuat file ZIP export SS Detail.");
}

$usedNames = [];
foreach ($users_data as $uid => $data) {
    $u = $data['info'];
    $baseName = safeExportFilename(($u['departement'] ?: 'Dept') . ' - ' . ($u['nama_lngkp'] ?: 'Karyawan'));
    $fileName = $baseName . '.xls';
    $counter = 2;

    while (isset($usedNames[$fileName])) {
        $fileName = $baseName . '_' . $counter . '.xls';
        $counter++;
    }

    $usedNames[$fileName] = true;
    $zip->addFromString($fileName, renderIndividualSSXls($u, $data['details']));
}

$zip->close();

while (ob_get_level()) {
    ob_end_clean();
}

header("Content-Type: application/zip");
header("Content-Disposition: attachment; filename=\"$zipFilename\"");
header("Content-Length: " . filesize($tmpZip));
header("Pragma: no-cache");
header("Expires: 0");

readfile($tmpZip);
unlink($tmpZip);
exit();
