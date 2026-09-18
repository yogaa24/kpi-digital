<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/status_functions.php';

$id_userdd = isset($_GET['id']) ? intval($_GET['id']) : 0;

// FUNGSI-FUNGSI HELPER (LETAKKAN DI SINI DULU)
function getnilaiaa($conn,$idar,$id_userdd){
    $sql= "SELECT tbar_kpi.* FROM tbar_kpi INNER JOIN tbar_archive ON tbar_archive.id_archive = tbar_kpi.id_arcv WHERE tbar_archive.bulan = '$idar' AND tbar_archive.id_user = $id_userdd";

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

function getstatad($nilair){
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

function getsfo($nilair){
    if ($nilair < 90) {
        return "red";
    } elseif ($nilair <= 100) {
        return "orange";
    } elseif ($nilair <= 110) {
        return "blue";
    } else {
        return "green";
    }
}

function tmaadfl($blan){
    $busd = explode('/', $blan);
    $bl = $busd[0];
    $th = $busd[1];

    $months = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    
    return isset($months[$bl]) ? $months[$bl] . ' ' . $th : $blan;
}

// HANDLER UNTUK MARK AS REVIEWED (PINDAHKAN KE SINI - SEBELUM HTML)
if (isset($_POST['mark_reviewed'])) {
    $id_archive = intval($_POST['id_archive']);
    $bulan = mysqli_real_escape_string($conn, $_POST['bulan']);
    $reviewer_id = $_SESSION['id_user'];
    
    $sql_update = "UPDATE tbar_archive 
                   SET status = 2, 
                       reviewed_by = $reviewer_id, 
                       reviewed_at = NOW() 
                   WHERE id_archive = $id_archive";
    
    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['success_message'] = "Archive bulan " . tmaadfl($bulan) . " berhasil ditandai sebagai REVIEWED";
        header("Location: archiveanggota.php?id=$id_userdd");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal update status: " . mysqli_error($conn);
    }
}

// HANDLER UNTUK APPROVE (PINDAHKAN KE SINI - SEBELUM HTML)
if (isset($_POST['approve_archive'])) {
    $id_archive = intval($_POST['id_archive']);
    $bulan = mysqli_real_escape_string($conn, $_POST['bulan']);
    $approver_id = $_SESSION['id_user'];
    
    $sql_update = "UPDATE tbar_archive 
                   SET status = 3, 
                       approved_by = $approver_id, 
                       approved_at = NOW() 
                   WHERE id_archive = $id_archive";
    
    if (mysqli_query($conn, $sql_update)) {
        $_SESSION['success_message'] = "Archive bulan " . tmaadfl($bulan) . " berhasil DISETUJUI";
        header("Location: archiveanggota.php?id=$id_userdd");
        exit();
    } else {
        $_SESSION['error_message'] = "Gagal approve archive: " . mysqli_error($conn);
    }
}

// QUERY DATA ARCHIVE
$archivec = "SELECT DISTINCT ta.id_archive, ta.bulan, ta.status, ta.reviewed_by, ta.reviewed_at, ta.approved_by, ta.approved_at
            FROM tbar_archive ta
            WHERE ta.id_user = $id_userdd  
            ORDER BY ta.bulan DESC";
$getArch = mysqli_query($conn, $archivec);

require_once 'helper/ss_archive_functions.php';
ensureSSArchiveTables($conn);

$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['kpi', 'ss']) ? $_GET['tab'] : 'kpi';

$query_ss_arch = "SELECT a.*, u.nama_lngkp as verifikator_name 
                  FROM tbar_ss_archive a 
                  LEFT JOIN tb_users u ON u.id = a.verified_by 
                  WHERE a.id_user = $id_userdd 
                  ORDER BY a.bulan DESC";
$getSSArch = mysqli_query($conn, $query_ss_arch);
?>
<!DOCTYPE html>
<html lang="en">

<?php include("pages/part/p_header.php"); 
echo '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">';?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <?php if($jabatan=="Manager" || $jabatan=="Koordinator" || $jabatan=="Kadep" || $jabatan=="Direktur" || $jabatan=="Wadir Utama"){?>
                        <li class="nav-item d-none d-md-block"> <a href="archivekabag" class="nav-link">Kembali</a> </li>
                    <?php }?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li>
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png"
                                class="user-image rounded-circle shadow" alt="User Image"> <span
                                class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                                </center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <div class="app-content">
                    <div class="container-fluid">
                        
                        <!-- TAMPILKAN PESAN SUCCESS/ERROR -->
                        <?php if (isset($_SESSION['success_message'])) { ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success_message'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['success_message']); ?>
                        <?php } ?>
                        
                        <?php if (isset($_SESSION['error_message'])) { ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error_message'] ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                            <?php unset($_SESSION['error_message']); ?>
                        <?php } ?>
                        <div class="row"> <!-- Start col -->
                            <div class="mt-3">
                                <!-- Navigation Tabs Archive KPI & Archive SS -->
                                <ul class="nav nav-pills mb-3">
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($active_tab == 'kpi') ? 'active fw-bold' : '' ?>" href="archiveanggota?id=<?= $id_userdd ?>&tab=kpi">
                                            <i class="bi bi-bar-chart-fill me-1"></i> Archive KPI
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($active_tab == 'ss') ? 'active fw-bold' : '' ?>" href="archiveanggota?id=<?= $id_userdd ?>&tab=ss">
                                            <i class="bi bi-award-fill me-1"></i> Archive Skill Standard
                                        </a>
                                    </li>
                                </ul>

                                <?php if ($active_tab == 'kpi') { ?>
                                <div class="table-responsive">
                                    <table id="datatablenya" class="table align-midle table-hover table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="3%"><center>No</center></th>
                                                <th><center>Bulan</center></th>
                                                <th width="12%"><center>Nilai</center></th>
                                                <th width="12%"><center>KPI</center></th>
                                                <!-- <th width="15%"><center>Status</center></th>  -->
                                                <th width="15%"><center>Action</center></th> <!-- UBAH DARI # -->
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; if($getArch){ while ($row = mysqli_fetch_assoc($getArch)) { 
                                                $nilai = round(getnilaiaa($conn,$row['bulan'],$id_userdd),2);
                                                $status = $row['status'];
                                                $id_archive = $row['id_archive'];
                                            ?>
                                            <tr style="background-color: <?= $status == 1 ? '#fff3cd' : 'transparent' ?>"> <!-- Highlight yang perlu review -->
                                                <td><center><?= $no; ?></center></td>
                                                <td><center><?= tmaadfl($row['bulan']) ?></center></td>
                                                <td><center><?= $nilai ?></center></td>
                                                <td style="color:<?= getsfo($nilai) ?>">
                                                    <center><?= getstatad($nilai) ?></center>
                                                </td>
                                                <!-- KOLOM STATUS BARU -->
                                                <!-- <td> 
                                                    <center>
                                                        <?= getStatusBadge($status) ?>
                                                        
                                                        <?php if ($status == 2 && !empty($row['reviewed_at'])) { ?>
                                                            <br><small class="text-muted" style="font-size: 10px;">
                                                                <i class="bi bi-calendar-check"></i> 
                                                                <?= date('d/m/Y H:i', strtotime($row['reviewed_at'])) ?>
                                                            </small>
                                                        <?php } elseif ($status == 3 && !empty($row['approved_at'])) { ?>
                                                            <br><small class="text-success" style="font-size: 10px;">
                                                                <i class="bi bi-check-all"></i> 
                                                                <?= date('d/m/Y H:i', strtotime($row['approved_at'])) ?>
                                                            </small>
                                                        <?php } ?>
                                                    </center>
                                                </td> -->
                                                <td> <!-- KOLOM ACTION DENGAN TOMBOL STATUS -->
                                                    <center>
                                                        <!-- Tombol Lihat Detail -->
                                                        <a type="button" href="archiveangpoin?id=<?= $id_userdd ?>&idar=<?= $row['bulan'] ?>"
                                                        class="btn btn-success btn-sm mb-1" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i> Detail
                                                        </a>
                                                        
                                                        <!-- <?php if ($status == 1) { ?>
                                                            <button type="button" class="btn btn-info btn-sm mb-1" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#markReviewedModal<?= $id_archive ?>"
                                                                    title="Tandai Sudah Direview">
                                                                <i class="bi bi-check"></i> Review
                                                            </button>
                                                        <?php } elseif ($status == 2) { ?>
                                                            <button type="button" class="btn btn-success btn-sm mb-1" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#approveModal<?= $id_archive ?>"
                                                                    title="Setujui Archive">
                                                                <i class="bi bi-check-all"></i> Approve
                                                            </button>
                                                        <?php } else { ?>
                                                            <span class="badge bg-success">
                                                                <i class="bi bi-shield-check"></i> Completed
                                                            </span>
                                                        <?php } ?> -->
                                                    </center>
                                                </td>
                                            </tr>
                                            
                                            <!-- Modal Mark as Reviewed -->
                                            <div class="modal fade" id="markReviewedModal<?= $id_archive ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-info text-white">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-eye-fill"></i> Tandai Sudah Direview
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="">
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin menandai archive bulan <strong><?= tmaadfl($row['bulan']) ?></strong> sebagai <strong>REVIEWED</strong>?</p>
                                                                <input type="hidden" name="id_archive" value="<?= $id_archive ?>">
                                                                <input type="hidden" name="bulan" value="<?= $row['bulan'] ?>">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" name="mark_reviewed" class="btn btn-info">
                                                                    <i class="bi bi-check"></i> Ya, Tandai Reviewed
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Modal Approve -->
                                            <div class="modal fade" id="approveModal<?= $id_archive ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-success text-white">
                                                            <h5 class="modal-title">
                                                                <i class="bi bi-check-circle-fill"></i> Setujui Archive
                                                            </h5>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <form method="POST" action="">
                                                            <div class="modal-body">
                                                                <p>Apakah Anda yakin ingin <strong>MENYETUJUI</strong> archive bulan <strong><?= tmaadfl($row['bulan']) ?></strong>?</p>
                                                                <p class="text-muted small">Setelah disetujui, status archive akan menjadi selesai (Completed).</p>
                                                                <input type="hidden" name="id_archive" value="<?= $id_archive ?>">
                                                                <input type="hidden" name="bulan" value="<?= $row['bulan'] ?>">
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" name="approve_archive" class="btn btn-success">
                                                                    <i class="bi bi-check-all"></i> Ya, Setujui
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <?php $no++; } }?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php } else { ?>
                                <!-- TAB KONTEN 2: ARCHIVE SKILL STANDARD ANGGOTA -->
                                <div class="table-responsive">
                                    <table id="datatablenya" class="table align-middle table-hover table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="3%"><center>No</center></th>
                                                <th><center>Bulan</center></th>
                                                <th width="15%"><center>Rata-rata Umum</center></th>
                                                <th width="15%"><center>Rata-rata Teknis</center></th>
                                                <th width="15%"><center>Nilai Total</center></th>
                                                <th width="18%"><center>Status Verifikasi</center></th>
                                                <th width="10%"><center>Action</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php 
                                            $no_ss = 1; 
                                            if ($getSSArch && mysqli_num_rows($getSSArch) > 0) { 
                                                while ($row_ss = mysqli_fetch_assoc($getSSArch)) { 
                                            ?>
                                            <tr>
                                                <td><center><?= $no_ss; ?></center></td>
                                                <td><center><strong><?= tmaadfl($row_ss['bulan']) ?></strong></center></td>
                                                <td><center><?= number_format($row_ss['rata_rata_umum'], 2) ?></center></td>
                                                <td><center><?= number_format($row_ss['rata_rata_teknis'], 2) ?></center></td>
                                                <td><center><strong class="fs-6"><?= number_format($row_ss['rata_rata_total'], 2) ?></strong></center></td>
                                                <td>
                                                    <center>
                                                        <?php if ($row_ss['status'] == 1) { ?>
                                                            <span class="badge bg-success text-white">
                                                                <i class="bi bi-check-circle me-1"></i> Terverifikasi
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-clock-history me-1"></i> Belum Terverifikasi
                                                            </span>
                                                        <?php } ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <a href="archivesspoin?id=<?= $id_userdd ?>&idarc=<?= urlencode($row_ss['bulan']); ?>&ref=kabag" class="btn btn-success btn-sm" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i> Detail
                                                        </a>
                                                    </center>
                                                </td>
                                            </tr>
                                            <?php 
                                                    $no_ss++; 
                                                } 
                                            } else { ?>
                                                <tr>
                                                    <td colspan="8" class="text-center py-4 text-muted">
                                                        <i class="bi bi-info-circle fs-4 d-block mb-2"></i>
                                                        Belum ada arsip Skill Standard untuk anggota ini.
                                                    </td>
                                                </tr>
                                            <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div> <!--end::App Content-->
                </div>
        </main> <!--end::App Main--> <!--begin::Footer-->

        <?php include("pages/part/p_footer.php"); ?>
        </div>
</body>

</html>