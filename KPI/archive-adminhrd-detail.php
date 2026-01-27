<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/config.php';
require 'helper/getUser.php';

// Cek level Admin HRD
$sql_check = "SELECT level FROM tb_auth WHERE id_user = '$id_user'";
$result_check = mysqli_query($conn, $sql_check);
$user_data = mysqli_fetch_assoc($result_check);

if ($user_data['level'] != 7) {
    header("Location: dashboard");
    exit();
}

// Ambil ID user dari parameter
if (!isset($_GET['id'])) {
    header("Location: archive-adminhrd");
    exit();
}

$id_user_archive = $_GET['id'];

// Ambil data user yang dipilih
$sql_user = "SELECT * FROM tb_users WHERE id = '$id_user_archive'";
$result_user = mysqli_query($conn, $sql_user);
$user_info = mysqli_fetch_assoc($result_user);

if (!$user_info) {
    header("Location: archive-adminhrd");
    exit();
}

// Function untuk convert bulan
function convertBulan($bulan) {
    $busd = explode('/', $bulan);
    $bl = $busd[0];
    $th = $busd[1];
    
    $nama_bulan = [
        '00' => 'Desember', '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April', '05' => 'Mei', '06' => 'Juni',
        '07' => 'Juli', '08' => 'Agustus', '09' => 'September',
        '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
    ];
    
    return $nama_bulan[$bl] . ' ' . $th;
}

// Function untuk hitung nilai
function getnilaiaa($conn, $idar, $id_userdd) {
    $sql = "SELECT tbar_kpi.* FROM tbar_kpi 
            INNER JOIN tbar_archive ON tbar_archive.id_archive = tbar_kpi.id_arcv 
            WHERE tbar_archive.bulan = '$idar' AND tbar_archive.id_user = $id_userdd";

    $totalws = 0;
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $sql3s = "SELECT SUM(total) as total FROM tbar_whats WHERE id_user=$id_userdd AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tbar_bobotkpi WHERE id_user=$id_userdd";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;

    $totalhfg = 0;
    $resultfg = mysqli_query($conn, $sql);
    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $sql7fg = "SELECT SUM(total) as totalh FROM tbar_hows WHERE id_user=$id_userdd AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];
        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tbar_bobotkpi WHERE id_user=$id_userdd";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;

    return $zbotw + $zboth;
}

function getstatad($nilair) {
    if ($nilair < 90) {
        return "POOR";
    } elseif ($nilair <= 100) {
        return "GOOD";
    } elseif ($nilair <= 110) {
        return "VERY GOOD";
    } else {
        return "EXCELLENT";
    }
}

function getsfo($nilair) {
    if ($nilair < 90) {
        return "danger";
    } elseif ($nilair <= 100) {
        return "warning";
    } elseif ($nilair <= 110) {
        return "primary";
    } else {
        return "success";
    }
}

// Ambil archive user tersebut
$archivec = "SELECT bulan FROM tbar_archive WHERE id_user = $id_user_archive GROUP BY bulan ORDER BY bulan DESC";
$getArch = mysqli_query($conn, $archivec);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    
                    <!-- Header dengan Info User -->
                    <div class="row mb-3 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="fw-bold mb-2">
                                                <i class="bi bi-archive-fill text-warning me-2"></i>Archive KPI - <?= $user_info['nama_lngkp'] ?>
                                            </h4>
                                            <div class="text-muted small">
                                                <span class="me-3">
                                                    <i class="bi bi-person-badge me-1"></i>NIK: <?= $user_info['nik'] ?>
                                                </span>
                                                <span class="me-3">
                                                    <i class="bi bi-building me-1"></i><?= $user_info['departement'] ?>
                                                </span>
                                                <span class="me-3">
                                                    <i class="bi bi-briefcase me-1"></i><?= $user_info['bagian'] ?>
                                                </span>
                                                <span class="badge bg-primary">
                                                    <?= $user_info['jabatan'] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="archive-adminhrd" class="btn btn-secondary">
                                                <i class="bi bi-arrow-left me-1"></i>Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table Archive -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <?php if (mysqli_num_rows($getArch) > 0) { ?>
                                    <div class="table-responsive">
                                        <table id="datatablenya" class="table table-hover table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%"><center>No</center></th>
                                                    <th><center>Periode</center></th>
                                                    <th width="12%"><center>Nilai</center></th>
                                                    <th width="15%"><center>KPI Status</center></th>
                                                    <th width="10%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                while ($row = mysqli_fetch_assoc($getArch)) { 
                                                    $nilai = getnilaiaa($conn, $row['bulan'], $id_user_archive);
                                                    $badge_color = getsfo($nilai);
                                                    $status = getstatad($nilai);
                                                ?>
                                                <tr>
                                                    <td><center><?= $no++ ?></center></td>
                                                    <td style="padding-left: 15px;">
                                                        <strong>
                                                            <i class="bi bi-calendar-event me-2 text-warning"></i>
                                                            <?= convertBulan($row['bulan']) ?>
                                                        </strong>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <h5 class="mb-0">
                                                                <span class="badge bg-<?= $badge_color ?>">
                                                                    <?= number_format($nilai, 2) ?>
                                                                </span>
                                                            </h5>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <span class="badge bg-<?= $badge_color ?> px-3 py-2">
                                                                <i class="bi bi-trophy-fill me-1"></i>
                                                                <?= $status ?>
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a href="archiveangpoin?id=<?= $id_user_archive ?>&idar=<?= $row['bulan'] ?>" 
                                                               class="btn btn-sm btn-success"
                                                               title="Lihat Detail">
                                                                <i class="bi bi-eye"></i> Detail
                                                            </a>
                                                        </center>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Belum ada archive untuk karyawan ini
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Statistics (Optional) -->
                    <?php if (mysqli_num_rows($getArch) > 0) { 
                        mysqli_data_seek($getArch, 0); // Reset pointer
                        $total_nilai = 0;
                        $count = 0;
                        while ($row = mysqli_fetch_assoc($getArch)) {
                            $total_nilai += getnilaiaa($conn, $row['bulan'], $id_user_archive);
                            $count++;
                        }
                        $avg_nilai = $total_nilai / $count;
                        $avg_badge = getsfo($avg_nilai);
                        $avg_status = getstatad($avg_nilai);
                    ?>
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0 bg-light">
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <h6 class="text-muted">Total Periode</h6>
                                            <h3 class="fw-bold text-primary">
                                                <i class="bi bi-calendar-range me-2"></i><?= $count ?>
                                            </h3>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-muted">Rata-rata Nilai</h6>
                                            <h3 class="fw-bold text-<?= $avg_badge ?>">
                                                <?= number_format($avg_nilai, 2) ?>
                                            </h3>
                                        </div>
                                        <div class="col-md-4">
                                            <h6 class="text-muted">Performa Rata-rata</h6>
                                            <h4>
                                                <span class="badge bg-<?= $avg_badge ?> px-3 py-2">
                                                    <?= $avg_status ?>
                                                </span>
                                            </h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>
</html>