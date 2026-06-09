<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/checkAdmin.php';
require 'helper/sp_functions.php';

requireAdminHRD();
updateExpiredSP($conn);
date_default_timezone_set('Asia/Jakarta');

function h($value) {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
}

function getRatingAllKPI($nilai) {
    if ($nilai < 90) {
        return "POOR";
    } elseif ($nilai <= 100) {
        return "GOOD";
    } elseif ($nilai <= 110) {
        return "Very Good";
    }
    return "Excellent";
}

function getKPIBreakdownAll($conn, $id_user) {
    $stmt = $conn->prepare("SELECT * FROM tb_kpi WHERE id_user = ? ORDER BY id ASC");
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();

    $items = [];
    $total_what_weighted = 0;
    $total_how_weighted = 0;

    while ($kpi = $result->fetch_assoc()) {
        $id_kpi = (int)$kpi['id'];

        $stmt_what = $conn->prepare("SELECT * FROM tb_whats WHERE id_user = ? AND id_kpi = ? ORDER BY id_what ASC");
        $stmt_what->bind_param("ii", $id_user, $id_kpi);
        $stmt_what->execute();
        $result_what = $stmt_what->get_result();
        $whats = [];
        $subtotal_what = 0;
        while ($what = $result_what->fetch_assoc()) {
            $subtotal_what += (float)$what['total'];
            $whats[] = $what;
        }

        $stmt_how = $conn->prepare("SELECT * FROM tb_hows WHERE id_user = ? AND id_kpi = ? ORDER BY id_how ASC");
        $stmt_how->bind_param("ii", $id_user, $id_kpi);
        $stmt_how->execute();
        $result_how = $stmt_how->get_result();
        $hows = [];
        $subtotal_how = 0;
        while ($how = $result_how->fetch_assoc()) {
            $subtotal_how += (float)$how['total'];
            $hows[] = $how;
        }

        $nilai_what = ($subtotal_what * (float)$kpi['bobot']) / 100;
        $nilai_how = ($subtotal_how * (float)$kpi['bobot2']) / 100;
        $total_what_weighted += $nilai_what;
        $total_how_weighted += $nilai_how;

        $items[] = [
            'kpi' => $kpi,
            'whats' => $whats,
            'hows' => $hows,
            'subtotal_what' => $subtotal_what,
            'subtotal_how' => $subtotal_how,
            'nilai_what' => $nilai_what,
            'nilai_how' => $nilai_how,
        ];
    }

    $stmt_bobot = $conn->prepare("SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user = ? LIMIT 1");
    $stmt_bobot->bind_param("i", $id_user);
    $stmt_bobot->execute();
    $bobot = $stmt_bobot->get_result()->fetch_assoc();
    $bobot_what = (float)($bobot['bobotwhat'] ?? 0);
    $bobot_how = (float)($bobot['bobothow'] ?? 0);

    $final_what = ($total_what_weighted * $bobot_what) / 100;
    $final_how = ($total_how_weighted * $bobot_how) / 100;
    $nilai_asli = $final_what + $final_how;
    $nilai_sp = calculateKPIWithSP($conn, $id_user, $nilai_asli);

    return [
        'items' => $items,
        'total_what_raw' => $total_what_weighted,
        'total_how_raw' => $total_how_weighted,
        'bobot_what' => $bobot_what,
        'bobot_how' => $bobot_how,
        'final_what' => $final_what,
        'final_how' => $final_how,
        'nilai_asli' => $nilai_asli,
        'nilai_akhir' => $nilai_sp['nilai_akhir'],
        'sp_data' => $nilai_sp['sp_data'],
        'pengurangan' => $nilai_sp['pengurangan'],
    ];
}

function safeExportFilename($value) {
    $value = preg_replace('/[^A-Za-z0-9_\-]+/', '_', (string)$value);
    $value = trim($value, '_');
    return $value !== '' ? $value : 'Karyawan';
}

function renderIndividualKPIXls($user, $breakdown) {
    ob_start();
    ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        table { border-collapse: collapse; width: 100%; margin-bottom: 18px; }
        th, td { border: 1px solid #111; padding: 6px; vertical-align: top; }
        th { background: #2c3e50; color: #fff; font-weight: bold; }
        .title { font-size: 16px; font-weight: bold; text-align: center; border: none; }
        .header-info { font-weight: bold; border: none; }
        .section { background: #34495e; color: #fff; font-weight: bold; text-align: center; }
        .what { background: #3498db; color: #fff; font-weight: bold; text-align: center; }
        .how { background: #27ae60; color: #fff; font-weight: bold; text-align: center; }
        .center { text-align: center; }
        .right { text-align: right; }
        .muted { background: #ecf0f1; }
        .sp { color: #c0392b; font-weight: bold; }
    </style>
</head>
<body>
    <table border="0">
        <tr><td colspan="12" class="title">DETAIL KPI KARYAWAN</td></tr>
        <tr><td colspan="12" style="border:none;">&nbsp;</td></tr>
        <tr><td class="header-info">Nama</td><td colspan="11" class="header-info">: <?= h($user['nama_lngkp']) ?></td></tr>
        <tr><td class="header-info">NIK</td><td colspan="11" class="header-info">: <?= h($user['nik']) ?></td></tr>
        <tr><td class="header-info">Jabatan</td><td colspan="11" class="header-info">: <?= h($user['jabatan']) ?></td></tr>
        <tr><td class="header-info">Bagian</td><td colspan="11" class="header-info">: <?= h($user['bagian']) ?></td></tr>
        <tr><td class="header-info">Departemen</td><td colspan="11" class="header-info">: <?= h($user['departement']) ?></td></tr>
        <tr><td class="header-info">Atasan</td><td colspan="11" class="header-info">: <?= h($user['atasan']) ?></td></tr>
        <tr><td class="header-info">Tanggal Export</td><td colspan="11" class="header-info">: <?= date('d/m/Y H:i:s') ?></td></tr>
    </table>

    <table>
        <tr><th colspan="12" class="center">RINGKASAN NILAI KPI</th></tr>
        <tr>
            <td colspan="2" class="center"><strong>Nilai What</strong></td>
            <td colspan="2" class="center"><?= number_format($breakdown['final_what'], 2) ?></td>
            <td colspan="2" class="center"><strong>Nilai How</strong></td>
            <td colspan="2" class="center"><?= number_format($breakdown['final_how'], 2) ?></td>
            <td colspan="2" class="center"><strong>Total KPI</strong></td>
            <td colspan="2" class="center"><strong><?= number_format($breakdown['nilai_akhir'], 2) ?></strong></td>
        </tr>
        <tr>
            <td colspan="3" class="center"><strong>Rating</strong></td>
            <td colspan="3" class="center"><?= getRatingAllKPI($breakdown['nilai_akhir']) ?></td>
            <td colspan="3" class="center"><strong>SP Aktif</strong></td>
            <td colspan="3" class="center sp">
                <?= $breakdown['sp_data'] ? h($breakdown['sp_data']['jenis_sp']) . ' (-' . number_format($breakdown['pengurangan'], 2) . ')' : '-' ?>
            </td>
        </tr>
    </table>

    <?php $kpi_no = 1; foreach ($breakdown['items'] as $item): $kpi = $item['kpi']; ?>
        <table>
            <tr><th colspan="12" class="section">KPI #<?= $kpi_no++ ?></th></tr>
            <tr>
                <th colspan="6" class="what">WHAT (Bobot: <?= h($kpi['bobot']) ?>%)</th>
                <th colspan="6" class="how">HOW (Bobot: <?= h($kpi['bobot2']) ?>%)</th>
            </tr>
            <tr>
                <td colspan="6" class="muted"><strong><?= h($kpi['poin']) ?></strong></td>
                <td colspan="6" class="muted"><strong><?= h($kpi['poin2']) ?></strong></td>
            </tr>
            <tr>
                <th>No</th><th>Poin What</th><th>Bobot</th><th>Hasil</th><th>Nilai</th><th>Total</th>
                <th>No</th><th>Poin How</th><th>Bobot</th><th>Hasil</th><th>Nilai</th><th>Total</th>
            </tr>
            <?php
            $max_rows = max(count($item['whats']), count($item['hows']));
            if ($max_rows === 0) {
                echo "<tr><td colspan='12' class='center'>Belum ada detail KPI</td></tr>";
            }
            for ($i = 0; $i < $max_rows; $i++):
                $what = $item['whats'][$i] ?? null;
                $how = $item['hows'][$i] ?? null;
            ?>
            <tr>
                <?php if ($what): ?>
                    <td class="center"><?= $i + 1 ?></td>
                    <td><?= h($what['p_what']) ?></td>
                    <td class="right"><?= h($what['bobot']) ?>%</td>
                    <td><?= h($what['hasil']) ?></td>
                    <td class="right"><?= h($what['nilai']) ?></td>
                    <td class="right"><?= number_format((float)$what['total'], 2) ?></td>
                <?php else: ?>
                    <td colspan="6"></td>
                <?php endif; ?>

                <?php if ($how): ?>
                    <td class="center"><?= $i + 1 ?></td>
                    <td><?= h($how['p_how']) ?></td>
                    <td class="right"><?= h($how['bobot']) ?>%</td>
                    <td><?= h($how['hasil']) ?></td>
                    <td class="right"><?= h($how['nilai']) ?></td>
                    <td class="right"><?= number_format((float)$how['total'], 2) ?></td>
                <?php else: ?>
                    <td colspan="6"></td>
                <?php endif; ?>
            </tr>
            <?php endfor; ?>
            <tr>
                <td colspan="5" class="right"><strong>Subtotal What</strong></td>
                <td class="right"><strong><?= number_format($item['subtotal_what'], 2) ?></strong></td>
                <td colspan="5" class="right"><strong>Subtotal How</strong></td>
                <td class="right"><strong><?= number_format($item['subtotal_how'], 2) ?></strong></td>
            </tr>
        </table>
    <?php endforeach; ?>
</body>
</html>
    <?php
    return ob_get_clean();
}

$users = [];
$sql_users = "SELECT u.*
              FROM tb_users u
              INNER JOIN tb_auth a ON u.id = a.id_user
              WHERE u.jabatan != 'Admin HRD'
              AND u.username NOT IN ('itboy', 'adminhrd', 'backdoor_admin')
              ORDER BY
                CASE
                    WHEN u.jabatan = 'Kadep' THEN 1
                    WHEN u.jabatan = 'Manager' THEN 2
                    WHEN u.jabatan = 'Koordinator' THEN 3
                    WHEN u.jabatan = 'Karyawan' THEN 4
                    ELSE 5
                END,
                u.nama_lngkp ASC";
$result_users = mysqli_query($conn, $sql_users);
while ($user = mysqli_fetch_assoc($result_users)) {
    $breakdown = getKPIBreakdownAll($conn, (int)$user['id']);
    $users[] = ['user' => $user, 'breakdown' => $breakdown];
}

if (!class_exists('ZipArchive')) {
    die("Ekstensi ZipArchive belum aktif di PHP.");
}

$zipFilename = 'Export_Semua_KPI_Karyawan_' . date('Ymd_His') . '.zip';
$tmpZip = tempnam(sys_get_temp_dir(), 'kpi_all_');
$zip = new ZipArchive();

if ($zip->open($tmpZip, ZipArchive::OVERWRITE) !== true) {
    die("Gagal membuat file ZIP export KPI.");
}

$usedNames = [];
foreach ($users as $entry) {
    $user = $entry['user'];
    $baseName = safeExportFilename(($user['nama_lngkp'] ?: 'Karyawan') . '_' . ($user['nik'] ?: $user['id']));
    $fileName = 'Detail_KPI_' . $baseName . '.xls';
    $counter = 2;

    while (isset($usedNames[$fileName])) {
        $fileName = 'Detail_KPI_' . $baseName . '_' . $counter . '.xls';
        $counter++;
    }

    $usedNames[$fileName] = true;
    $zip->addFromString($fileName, renderIndividualKPIXls($user, $entry['breakdown']));
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
