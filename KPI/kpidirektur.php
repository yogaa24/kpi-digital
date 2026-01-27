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
    // ===============================================================================
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

function getPreviousMonth() {
    $currentMonth = date('n');
    $currentYear = date('Y');
    
    if ($currentMonth == 1) {
        return ['month' => 12, 'year' => $currentYear - 1];
    } else {
        return ['month' => $currentMonth - 1, 'year' => $currentYear];
    }
}

function getNamaBulan($bulan) {
    $namaBulan = [
        1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
        5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
        9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
    ];
    return $namaBulan[$bulan];
}

function getKPIFromHistory($conn, $id_user, $bulan, $tahun) {
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
            'total_kpi' => number_format($row['total_kpi_real'], 2),
            'nilai_what' => number_format($row['nilai_what'], 2),
            'nilai_how' => number_format($row['nilai_how'], 2),
            'exists' => true
        ];
    }
    
    return [
        'total_kpi' => '0.00',
        'nilai_what' => '0.00',
        'nilai_how' => '0.00',
        'exists' => false
    ];
}
function getkpi($nilair)
{
    if ($nilair < 90) {
        return "POOR";
    } elseif ($nilair <= 100) {
        return "GOOD";
    } elseif ($nilair <= 110) {
        return "Very Good";
    } else { // $nilai > 110
        return "Excellent";
    }
}
?>

<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KPI Digital</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="KPI Digital">
    <meta name="author" content="Rvld">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />

    <script type="text/javascript" src="datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <?php include("pages/kpikabag/k_nav.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table id="datatablenya" class="table align-midle table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%" rowspan="2"><center>No</center></th>
                                        <th rowspan="2"><center>Nama Anggota</center></th>
                                        <th width="15%" rowspan="2"><center>Jabatan</center></th>
                                        <th width="15%" rowspan="2"><center>Bagian</center></th>
                                        <th colspan="3"><center>Bulan Ini</center></th>
                                        <th colspan="3"><center>Bulan Lalu</center></th>
                                        <th width="8%" rowspan="2"><center>Trend</center></th>
                                        <th width="5%" rowspan="2"><center>#</center></th>
                                    </tr>
                                    <tr>
                                        <!-- Bulan Ini -->
                                        <th width="8%"><center>What</center></th>
                                        <th width="8%"><center>How</center></th>
                                        <th width="8%"><center>Total</center></th>
                                        <!-- Bulan Lalu -->
                                        <th width="8%"><center>What</center></th>
                                        <th width="8%"><center>How</center></th>
                                        <th width="8%"><center>Total</center></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $no = 1;
                                    $prevMonth = getPreviousMonth();
                                    $bulanSebelumnya = $prevMonth['month'];
                                    $tahunSebelumnya = $prevMonth['year'];
                                    $namaBulanSebelumnya = getNamaBulan($bulanSebelumnya);
                                    $bulanIni = date('n');
                                    $tahunIni = date('Y');
                                    $namaBulanIni = getNamaBulan($bulanIni);
                                    
                                   $sqlhd = "SELECT * 
                                            FROM tb_users
                                            WHERE atasan = '$nama_lngkp' OR nama_lngkp = '$nama_lngkp'
                                            ORDER BY 
                                                CASE 
                                                    WHEN nama_lngkp = '$nama_lngkp' THEN 0 
                                                    ELSE 1 
                                                END,
                                                nama_lngkp";
                                    $sgdah = mysqli_query($conn, $sqlhd);
                                    while ($hasilsfa = mysqli_fetch_assoc($sgdah)) { 
                                        // Nilai bulan ini
                                        $nilair = getnilai($conn, $hasilsfa['id']);
                                        $whatIni = getWhatt($conn, $hasilsfa['id']);
                                        $howIni = getHoww($conn, $hasilsfa['id']);
                                        
                                        // Nilai bulan lalu
                                        $dataHistoryBulanLalu = getKPIFromHistory($conn, $hasilsfa['id'], $bulanSebelumnya, $tahunSebelumnya);
                                        $nilaiBulanLalu = $dataHistoryBulanLalu['total_kpi'];
                                        $whatBulanLalu = $dataHistoryBulanLalu['nilai_what'];
                                        $howBulanLalu = $dataHistoryBulanLalu['nilai_how'];
                                        $adaDataBulanLalu = $dataHistoryBulanLalu['exists'];
                                        
                                        // Hitung selisih dan trend
                                        $selisih = floatval($nilair) - floatval($nilaiBulanLalu);
                                        
                                        if (!$adaDataBulanLalu) {
                                            $trendIcon = '<i class="bi bi-dash-circle"></i>';
                                            $trendColor = 'gray';
                                            $trendBg = '#f8f9fa';
                                            $trendText = 'N/A';
                                        } elseif ($selisih > 0) {
                                            $trendIcon = '<i class="bi bi-arrow-up-circle-fill"></i>';
                                            $trendColor = 'green';
                                            $trendBg = '#d4edda';
                                            $trendText = '+' . number_format($selisih, 2);
                                        } elseif ($selisih < 0) {
                                            $trendIcon = '<i class="bi bi-arrow-down-circle-fill"></i>';
                                            $trendColor = 'red';
                                            $trendBg = '#f8d7da';
                                            $trendText = number_format($selisih, 2);
                                        } else {
                                            $trendIcon = '<i class="bi bi-dash-circle-fill"></i>';
                                            $trendColor = '#6c757d';
                                            $trendBg = '#e9ecef';
                                            $trendText = '0.00';
                                        }
                                        
                                        // Warna untuk nilai KPI bulan ini
                                        if ($nilair < 90) {
                                            $wrabs = "red";
                                        } elseif ($nilair <= 100) {
                                            $wrabs = "orange";
                                        } elseif ($nilair <= 110) {
                                            $wrabs = "green";
                                        } else {
                                            $wrabs = "blue";
                                        }
                                        
                                        // Warna untuk nilai KPI bulan lalu
                                        if ($nilaiBulanLalu < 90) {
                                            $wrabsLalu = "red";
                                        } elseif ($nilaiBulanLalu <= 100) {
                                            $wrabsLalu = "orange";
                                        } elseif ($nilaiBulanLalu <= 110) {
                                            $wrabsLalu = "green";
                                        } else {
                                            $wrabsLalu = "blue";
                                        }
                                    ?>
                                        <tr>
                                            <td><center><?= $no; ?></center></td>
                                            <td style="padding-left: 20px;"><?= $hasilsfa['nama_lngkp']; ?></td>
                                            <td><center><?= $hasilsfa['jabatan']; ?></center></td>
                                            <td><center><?= $hasilsfa['bagian']; ?></center></td>
                                            
                                            <!-- Nilai Bulan Ini -->
                                            <td><center><?= $whatIni ?></center></td>
                                            <td><center><?= $howIni ?></center></td>
                                            <td style="color:<?= $wrabs ?>">
                                                <center>
                                                    <strong><?= $nilair ?></strong><br>
                                                    <small class="text-muted"><?= getkpi($nilair) ?></small>
                                                </center>
                                            </td>
                                            
                                            <!-- Nilai Bulan Lalu -->
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
                                            
                                            <!-- Kolom Trend -->
                                            <td style="background-color:<?= $trendBg ?>; color:<?= $trendColor ?>">
                                                <center>
                                                    <?= $trendIcon ?><br>
                                                    <small><strong><?= $trendText ?></strong></small>
                                                </center>
                                            </td>
                                            
                                            <td>
                                                <?php if ($hasilsfa['nama_lngkp'] != $nama_lngkp) { ?>
                                                    <center>
                                                        <a type="button" href="kpianggota?id=<?= $hasilsfa['id']; ?>"
                                                            class="btn btn-success btn-sm">
                                                            <i class="bi bi-eye fs-8"></i>
                                                        </a>
                                                    </center>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php 
                                        $no++;
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <?php
                    // Hitung statistik KPI untuk diagram pie
                    $count_poor = 0;
                    $count_good = 0;
                    $count_very_good = 0;
                    $count_excellent = 0;
                    $total_team = 0;

                    $sqlhd_stats = "SELECT * 
        FROM tb_users
        WHERE atasan = '$nama_lngkp' OR nama_lngkp = '$nama_lngkp'
        ORDER BY 
            CASE 
                WHEN nama_lngkp = '$nama_lngkp' THEN 0 
                ELSE 1 
            END,
            nama_lngkp";

                    $sgdah_stats = mysqli_query($conn, $sqlhd_stats);
                    while ($hasil_stats = mysqli_fetch_assoc($sgdah_stats)) {
                        $nilai = getnilai($conn, $hasil_stats['id']);
                        $kpi_category = getkpi($nilai);
                        
                        if ($kpi_category == "POOR") {
                            $count_poor++;
                        } elseif ($kpi_category == "GOOD") {
                            $count_good++;
                        } elseif ($kpi_category == "Very Good") {
                            $count_very_good++;
                        } elseif ($kpi_category == "Excellent") {
                            $count_excellent++;
                        }
                        $total_team++;
                    }

                    // Hitung persentase
                    $percent_poor = $total_team > 0 ? round(($count_poor / $total_team) * 100, 1) : 0;
                    $percent_good = $total_team > 0 ? round(($count_good / $total_team) * 100, 1) : 0;
                    $percent_very_good = $total_team > 0 ? round(($count_very_good / $total_team) * 100, 1) : 0;
                    $percent_excellent = $total_team > 0 ? round(($count_excellent / $total_team) * 100, 1) : 0;
                    ?>

                    <!-- Card untuk Diagram Pie -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Analisa KPI Team</h5>
                                </div>
                                <div class="card-body">
                                    <div id="kpiPieChart"></div>
                                    <div class="mt-3">
                                        <small class="text-muted">Total Anggota: <?= $total_team ?> orang</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-secondary text-white">
                                    <h5 class="mb-0">Detail Persentase</h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><span class="badge" style="background-color: #dc3545;">POOR</span></td>
                                            <td><?= $count_poor ?> orang</td>
                                            <td><strong><?= $percent_poor ?>%</strong></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge" style="background-color: #fd7e14;">GOOD</span></td>
                                            <td><?= $count_good ?> orang</td>
                                            <td><strong><?= $percent_good ?>%</strong></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge" style="background-color: #28a745;">Very Good</span></td>
                                            <td><?= $count_very_good ?> orang</td>
                                            <td><strong><?= $percent_very_good ?>%</strong></td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge" style="background-color: #007bff;">Excellent</span></td>
                                            <td><?= $count_excellent ?> orang</td>
                                            <td><strong><?= $percent_excellent ?>%</strong></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <?php include("pages/part/p_footer.php"); ?>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
        var options = {
            series: [<?= $count_poor ?>, <?= $count_good ?>, <?= $count_very_good ?>, <?= $count_excellent ?>],
            chart: {
                type: 'pie',
                height: 350
            },
            labels: ['POOR', 'GOOD', 'Very Good', 'Excellent'],
            colors: ['#dc3545', '#fd7e14', '#28a745', '#007bff'],
            legend: {
                position: 'bottom'
            },
            dataLabels: {
                enabled: true,
                formatter: function(val, opts) {
                    return opts.w.config.series[opts.seriesIndex] + " (" + val.toFixed(1) + "%)";
                }
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " orang"
                    }
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#kpiPieChart"), options);
        chart.render();
    });
    </script>
</body>

</html>