<!-- kpidepartemen.php -->
<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getKPI.php';
}

// Hanya boleh diakses oleh level 4 (Kadep) dan level 5 (Direktur)
if (!isset($leveel) || ($leveel != 4 && $leveel != 5)) {
    header("Location: dashboard");
    exit();
}

function getnilai($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zboth = 0;
    $zbotw = 0;
    $totalws = 0;
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;

    $totalhfg = 0;
    $resultfg = mysqli_query($conn, $sql);
    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];
        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;

    return number_format($zboth + $zbotw, 2);
}

function getkpi($nilair)
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

function getPreviousMonth()
{
    $currentMonth = date('n');
    $currentYear = date('Y');
    if ($currentMonth == 1) {
        return ['month' => 12, 'year' => $currentYear - 1];
    } else {
        return ['month' => $currentMonth - 1, 'year' => $currentYear];
    }
}

function getNamaBulan($bulan)
{
    $namaBulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    return $namaBulan[$bulan];
}

function getKPIFromHistory($conn, $id_user, $bulan, $tahun)
{
    $bulanFormat = sprintf("%04d-%02d", $tahun, $bulan);
    $sql = "SELECT total_kpi_real, nilai_what, nilai_how 
            FROM tb_kpi_history 
            WHERE id_user = $id_user 
            AND bulan = '$bulanFormat' 
            AND is_summary = 1
            LIMIT 1";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return [
            'total_kpi'  => number_format($row['total_kpi_real'], 2),
            'nilai_what' => number_format($row['nilai_what'], 2),
            'nilai_how'  => number_format($row['nilai_how'], 2),
            'exists'     => true
        ];
    }
    return [
        'total_kpi'  => '0.00',
        'nilai_what' => '0.00',
        'nilai_how'  => '0.00',
        'exists'     => false
    ];
}

function getWhatt($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zbotw = 0;
    $totalws = 0;
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;
    return number_format($zbotw, 2);
}

function getHoww($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zboth = 0;
    $totalhfg = 0;
    $resultfg = mysqli_query($conn, $sql);
    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];
        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;
    return number_format($zboth, 2);
}

// ===== TENTUKAN FILTER DEPARTEMEN =====
$prevMonth           = getPreviousMonth();
$bulanSebelumnya     = $prevMonth['month'];
$tahunSebelumnya     = $prevMonth['year'];
$bulanIni            = date('n');
$tahunIni            = date('Y');
$namaBulanIni        = getNamaBulan($bulanIni);
$namaBulanSebelumnya = getNamaBulan($bulanSebelumnya);

// Ambil daftar departemen/bagian unik untuk filter
if ($leveel == 5) {
    // Ambil daftar departemen unik
    $sqlDept = "SELECT DISTINCT departement FROM tb_users WHERE departement IS NOT NULL AND departement != '' ORDER BY departement ASC";
    $resultDept = mysqli_query($conn, $sqlDept);
    $daftarDept = [];
    while ($rowDept = mysqli_fetch_assoc($resultDept)) {
        $daftarDept[] = $rowDept['departement'];
    }

    $filterBagian = isset($_GET['bagian']) ? mysqli_real_escape_string($conn, $_GET['bagian']) : '';

    if ($filterBagian != '') {
        $sqlUsers = "SELECT * FROM tb_users WHERE departement = '$filterBagian' 
                     ORDER BY CASE WHEN id = '$id_user' THEN 0 ELSE 1 END, nama_lngkp ASC";
    } else {
        $sqlUsers = "SELECT * FROM tb_users 
                     ORDER BY CASE WHEN id = '$id_user' THEN 0 ELSE 1 END, departement ASC, nama_lngkp ASC";
    }
    $pageTitle = "KPI Seluruh Departemen";
    $pageDesc  = $filterBagian != '' ? "Departemen: <strong>$filterBagian</strong>" : "Semua Departemen";

} else {
    // Kadep (level 4): ambil semua departemen milik kadep ini
    $nama_kadep  = mysqli_real_escape_string($conn, $nama_lngkp);

    // Ambil semua departemen yang dimiliki user kadep ini
    $sqlDeptKadep = "SELECT DISTINCT departement FROM tb_users WHERE nama_lngkp = '$nama_kadep' AND departement IS NOT NULL AND departement != ''";
    $resultDeptKadep = mysqli_query($conn, $sqlDeptKadep);
    $deptList = [];
    while ($rowDK = mysqli_fetch_assoc($resultDeptKadep)) {
        $deptList[] = "'" . mysqli_real_escape_string($conn, $rowDK['departement']) . "'";
    }

    if (!empty($deptList)) {
        $deptIn   = implode(',', $deptList);
        $sqlUsers = "SELECT * FROM tb_users WHERE departement IN ($deptIn) 
                     ORDER BY CASE WHEN id = '$id_user' THEN 0 ELSE 1 END, departement ASC, nama_lngkp ASC";
        $deptLabel = implode(', ', array_map(function($d) { return trim($d, "'"); }, $deptList));
    } else {
        // fallback jika tidak ketemu, tampilkan bagian yang sama
        $bagianKadep = mysqli_real_escape_string($conn, $bagian);
        $sqlUsers    = "SELECT * FROM tb_users WHERE bagian = '$bagianKadep' 
                        ORDER BY CASE WHEN id = '$id_user' THEN 0 ELSE 1 END, nama_lngkp ASC";
        $deptLabel   = $bagian;
    }

    $pageTitle  = "KPI Departemen";
    $pageDesc   = "Departemen: <strong>" . htmlspecialchars($deptLabel) . "</strong>";
    $daftarDept = [];
    $filterBagian = '';
}
?>

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KPI Departemen - KPI Digital</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="KPI Digital">
    <meta name="author" content="Rvld">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />
    <script type="text/javascript" src="datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/kpikabag/k_nav.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">

                    <!-- Page Header -->
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3">
                        <div>
                            <h4 class="mb-0"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i> <?= $pageTitle ?></h4>
                            <small class="text-muted"><?= $pageDesc ?> &nbsp;|&nbsp; 
                                Bulan Ini: <strong><?= $namaBulanIni . ' ' . $tahunIni ?></strong> &nbsp;|&nbsp;
                                Bulan Lalu: <strong><?= $namaBulanSebelumnya . ' ' . $tahunSebelumnya ?></strong>
                            </small>
                        </div>
                        <?php if ($leveel == 5): ?>
                        <!-- Filter Departemen untuk Direktur -->
                        <form method="GET" action="kpidepartemen" class="form-inline">
                            <div class="input-group input-group-sm">
                                <select name="bagian" class="form-control" onchange="this.form.submit()">
                                    <option value="">-- Semua Departemen --</option>
                                    <?php foreach ($daftarDept as $dept): ?>
                                        <option value="<?= htmlspecialchars($dept) ?>" <?= ($filterBagian == $dept) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($dept) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($filterBagian != ''): ?>
                                    <div class="input-group-append">
                                        <a href="kpidepartemen" class="btn btn-outline-secondary btn-sm" title="Reset Filter">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </form>
                        <?php endif; ?>
                    </div>

                    <!-- Tabel KPI -->
                    <div class="table-responsive">
                        <table id="datatablenya" class="table align-middle table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%" rowspan="2"><center>No</center></th>
                                    <th rowspan="2"><center>Nama Karyawan</center></th>
                                    <th width="15%" rowspan="2"><center>Jabatan</center></th>
                                    <th width="15%" rowspan="2"><center>Departemen</center></th>
                                    <?php if ($leveel == 5): ?>
                                    <th width="12%" rowspan="2"><center>Atasan Langsung</center></th>
                                    <?php endif; ?>
                                    <th colspan="3"><center>Bulan Ini (<?= $namaBulanIni ?>)</center></th>
                                    <th colspan="3"><center>Bulan Lalu (<?= $namaBulanSebelumnya ?>)</center></th>
                                    <th width="8%" rowspan="2"><center>Trend</center></th>
                                    <th width="5%" rowspan="2"><center>#</center></th>
                                </tr>
                                <tr>
                                    <th width="7%"><center>What</center></th>
                                    <th width="7%"><center>How</center></th>
                                    <th width="7%"><center>Total</center></th>
                                    <th width="7%"><center>What</center></th>
                                    <th width="7%"><center>How</center></th>
                                    <th width="7%"><center>Total</center></th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $no = 1;
                            $count_poor       = 0;
                            $count_good       = 0;
                            $count_very_good  = 0;
                            $count_excellent  = 0;
                            $total_team       = 0;

                            // Array untuk chart per departemen (khusus direktur)
                            $deptStats = [];

                            $sgdah = mysqli_query($conn, $sqlUsers);
                            while ($hasilsfa = mysqli_fetch_assoc($sgdah)) {

                                $nilair  = getnilai($conn, $hasilsfa['id']);
                                $whatIni = getWhatt($conn, $hasilsfa['id']);
                                $howIni  = getHoww($conn, $hasilsfa['id']);

                                $dataHistoryBulanLalu = getKPIFromHistory($conn, $hasilsfa['id'], $bulanSebelumnya, $tahunSebelumnya);
                                $nilaiBulanLalu  = $dataHistoryBulanLalu['total_kpi'];
                                $whatBulanLalu   = $dataHistoryBulanLalu['nilai_what'];
                                $howBulanLalu    = $dataHistoryBulanLalu['nilai_how'];
                                $adaDataBulanLalu = $dataHistoryBulanLalu['exists'];

                                $selisih = floatval($nilair) - floatval($nilaiBulanLalu);

                                if (!$adaDataBulanLalu) {
                                    $trendIcon  = '<i class="bi bi-dash-circle"></i>';
                                    $trendColor = 'gray';
                                    $trendBg    = '#f8f9fa';
                                    $trendText  = 'N/A';
                                } elseif ($selisih > 0) {
                                    $trendIcon  = '<i class="bi bi-arrow-up-circle-fill"></i>';
                                    $trendColor = 'green';
                                    $trendBg    = '#d4edda';
                                    $trendText  = '+' . number_format($selisih, 2);
                                } elseif ($selisih < 0) {
                                    $trendIcon  = '<i class="bi bi-arrow-down-circle-fill"></i>';
                                    $trendColor = 'red';
                                    $trendBg    = '#f8d7da';
                                    $trendText  = number_format($selisih, 2);
                                } else {
                                    $trendIcon  = '<i class="bi bi-dash-circle-fill"></i>';
                                    $trendColor = '#6c757d';
                                    $trendBg    = '#e9ecef';
                                    $trendText  = '0.00';
                                }

                                // Warna nilai KPI bulan ini
                                if ($nilair < 90)        { $wrabs = "red"; }
                                elseif ($nilair <= 100)  { $wrabs = "orange"; }
                                elseif ($nilair <= 110)  { $wrabs = "green"; }
                                else                     { $wrabs = "blue"; }

                                // Warna nilai KPI bulan lalu
                                if ($nilaiBulanLalu < 90)        { $wrabsLalu = "red"; }
                                elseif ($nilaiBulanLalu <= 100)  { $wrabsLalu = "orange"; }
                                elseif ($nilaiBulanLalu <= 110)  { $wrabsLalu = "green"; }
                                else                             { $wrabsLalu = "blue"; }

                                // Statistik pie chart
                                $kpi_category = getkpi($nilair);
                                if ($kpi_category == "POOR")           { $count_poor++; }
                                elseif ($kpi_category == "GOOD")       { $count_good++; }
                                elseif ($kpi_category == "Very Good")  { $count_very_good++; }
                                elseif ($kpi_category == "Excellent")  { $count_excellent++; }
                                $total_team++;

                                // Stats per departemen untuk chart direktur
                                if ($leveel == 5) {
                                    $bg = $hasilsfa['departement']; // <-- ganti dari bagian ke departement
                                    if (!isset($deptStats[$bg])) {
                                        $deptStats[$bg] = ['poor'=>0,'good'=>0,'very_good'=>0,'excellent'=>0,'total'=>0];
                                    }
                                    if ($kpi_category == "POOR")           { $deptStats[$bg]['poor']++; }
                                    elseif ($kpi_category == "GOOD")       { $deptStats[$bg]['good']++; }
                                    elseif ($kpi_category == "Very Good")  { $deptStats[$bg]['very_good']++; }
                                    elseif ($kpi_category == "Excellent")  { $deptStats[$bg]['excellent']++; }
                                    $deptStats[$bg]['total']++;
                                }
                            ?>
                                <tr>
                                    <td><center><?= $no ?></center></td>
                                    <td style="padding-left:15px;">
                                        <?= htmlspecialchars($hasilsfa['nama_lngkp']) ?>
                                        <?php if ($hasilsfa['id'] == $_SESSION['id_user']): ?>
                                            <span class="badge bg-primary">Saya</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><center><?= htmlspecialchars($hasilsfa['jabatan']) ?></center></td>
                                    <td><center><?= htmlspecialchars($hasilsfa['departement']) ?></center></td>
                                    <?php if ($leveel == 5): ?>
                                    <td><center><?= htmlspecialchars($hasilsfa['atasan'] ?? '-') ?></center></td>
                                    <?php endif; ?>

                                    <!-- Bulan Ini -->
                                    <td><center><?= $whatIni ?></center></td>
                                    <td><center><?= $howIni ?></center></td>
                                    <td style="color:<?= $wrabs ?>">
                                        <center>
                                            <strong><?= $nilair ?></strong><br>
                                            <small class="text-muted"><?= getkpi($nilair) ?></small>
                                        </center>
                                    </td>

                                    <!-- Bulan Lalu -->
                                    <td><center><?= $adaDataBulanLalu ? $whatBulanLalu : '-' ?></center></td>
                                    <td><center><?= $adaDataBulanLalu ? $howBulanLalu : '-' ?></center></td>
                                    <td style="color:<?= $adaDataBulanLalu ? $wrabsLalu : '#6c757d' ?>">
                                        <center>
                                            <?php if ($adaDataBulanLalu): ?>
                                                <strong><?= $nilaiBulanLalu ?></strong><br>
                                                <small class="text-muted"><?= getkpi($nilaiBulanLalu) ?></small>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </center>
                                    </td>

                                    <!-- Trend -->
                                    <td style="background-color:<?= $trendBg ?>; color:<?= $trendColor ?>">
                                        <center>
                                            <?= $trendIcon ?><br>
                                            <small><strong><?= $trendText ?></strong></small>
                                        </center>
                                    </td>

                                    <td>
                                        <?php if (
                                            $hasilsfa['id'] != $_SESSION['id_user'] &&
                                            $hasilsfa['jabatan'] != 'Direktur'
                                        ): ?>
                                            <center>
                                                <a href="kpianggota?id=<?= $hasilsfa['id'] ?>"
                                                   class="btn btn-success btn-sm" title="Lihat Detail">
                                                    <i class="bi bi-eye fs-8"></i>
                                                </a>
                                            </center>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php
                                $no++;
                            }

                            $percent_poor      = $total_team > 0 ? round(($count_poor / $total_team) * 100, 1) : 0;
                            $percent_good      = $total_team > 0 ? round(($count_good / $total_team) * 100, 1) : 0;
                            $percent_very_good = $total_team > 0 ? round(($count_very_good / $total_team) * 100, 1) : 0;
                            $percent_excellent = $total_team > 0 ? round(($count_excellent / $total_team) * 100, 1) : 0;
                            ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- ===== ANALISA & CHART ===== -->
                    <div class="row mb-4 mt-3">

                        <!-- Pie Chart Keseluruhan -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0"><i class="bi bi-pie-chart-fill me-1"></i> Distribusi KPI <?= ($leveel == 5 && $filterBagian == '') ? 'Seluruh Perusahaan' : htmlspecialchars($filterBagian ?: $bagian) ?></h5>
                                </div>
                                <div class="card-body">
                                    <div id="kpiPieChart"></div>
                                    <div class="mt-2">
                                        <small class="text-muted">Total: <strong><?= $total_team ?> karyawan</strong></small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Detail Persentase -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0"><i class="bi bi-table me-1"></i> Detail Persentase</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm table-bordered">
                                        <thead class="thead-light">
                                            <tr>
                                                <th>Kategori</th>
                                                <th>Jumlah</th>
                                                <th>Persentase</th>
                                                <th>Rentang Nilai</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><span class="badge" style="background-color:#dc3545;">POOR</span></td>
                                                <td><?= $count_poor ?> orang</td>
                                                <td><strong><?= $percent_poor ?>%</strong></td>
                                                <td><small class="text-muted">< 90</small></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge" style="background-color:#ffc107; color:#000;">GOOD</span></td>
                                                <td><?= $count_good ?> orang</td>
                                                <td><strong><?= $percent_good ?>%</strong></td>
                                                <td><small class="text-muted">90 – 100</small></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge" style="background-color:#007bff;">Very Good</span></td>
                                                <td><?= $count_very_good ?> orang</td>
                                                <td><strong><?= $percent_very_good ?>%</strong></td>
                                                <td><small class="text-muted">100 – 110</small></td>
                                            </tr>
                                            <tr>
                                                <td><span class="badge" style="background-color:#28a745;">Excellent</span></td>
                                                <td><?= $count_excellent ?> orang</td>
                                                <td><strong><?= $percent_excellent ?>%</strong></td>
                                                <td><small class="text-muted">> 110</small></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if ($leveel == 5 && count($deptStats) > 1 && $filterBagian == ''): ?>
                    <!-- Chart Perbandingan Antar Departemen (hanya Direktur, semua dept) -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-dark text-white">
                                    <h5 class="mb-0"><i class="bi bi-bar-chart-fill me-1"></i> Perbandingan KPI Antar Departemen</h5>
                                </div>
                                <div class="card-body">
                                    <div id="kpiBarChart"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>

        <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {

            // ===== PIE CHART =====
            var pieOptions = {
                series: [<?= $count_poor ?>, <?= $count_good ?>, <?= $count_very_good ?>, <?= $count_excellent ?>],
                chart: { type: 'pie', height: 320 },
                labels: ['POOR', 'GOOD', 'Very Good', 'Excellent'],
                colors: ['#dc3545', '#ffc107', '#007bff', '#28a745'],
                legend: { position: 'bottom' },
                dataLabels: {
                    enabled: true,
                    formatter: function (val, opts) {
                        return opts.w.config.series[opts.seriesIndex] + " (" + val.toFixed(1) + "%)";
                    }
                },
                tooltip: {
                    y: { formatter: function (val) { return val + " orang"; } }
                }
            };
            new ApexCharts(document.querySelector("#kpiPieChart"), pieOptions).render();

            <?php if ($leveel == 5 && count($deptStats) > 1 && $filterBagian == ''): ?>
            // ===== BAR CHART PERBANDINGAN DEPARTEMEN (Direktur) =====
            var deptLabels  = <?= json_encode(array_keys($deptStats)) ?>;
            var deptPoor    = <?= json_encode(array_column($deptStats, 'poor')) ?>;
            var deptGood    = <?= json_encode(array_column($deptStats, 'good')) ?>;
            var deptVGood   = <?= json_encode(array_column($deptStats, 'very_good')) ?>;
            var deptExcell  = <?= json_encode(array_column($deptStats, 'excellent')) ?>;

            var barOptions = {
                series: [
                    { name: 'POOR',      data: deptPoor   },
                    { name: 'GOOD',      data: deptGood   },
                    { name: 'Very Good', data: deptVGood  },
                    { name: 'Excellent', data: deptExcell }
                ],
                chart: { type: 'bar', height: 350, stacked: true },
                colors: ['#dc3545', '#ffc107', '#007bff', '#28a745'],
                xaxis: { categories: deptLabels },
                yaxis: { title: { text: 'Jumlah Karyawan' } },
                legend: { position: 'top' },
                plotOptions: { bar: { horizontal: false, dataLabels: { total: { enabled: true, style: { fontSize: '12px', fontWeight: 900 } } } } },
                tooltip: {
                    y: { formatter: function (val) { return val + " orang"; } }
                }
            };
            new ApexCharts(document.querySelector("#kpiBarChart"), barOptions).render();
            <?php endif; ?>
        });
        </script>
    </div>
</body>
</html>