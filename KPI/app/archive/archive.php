<?php
// archive.php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/status_functions.php';

    function getnilaiaa($conn,$idar,$id_user){
        $sql= "SELECT tbar_kpi.* FROM tbar_kpi INNER JOIN tbar_archive ON tbar_archive.id_archive = tbar_kpi.id_arcv WHERE tbar_archive.bulan = '$idar' AND tbar_archive.id_user = $id_user";

        $totalws = 0;
        $resultsaf = mysqli_query($conn, $sql);
        while ($hasils = mysqli_fetch_assoc($resultsaf)) {

            $sql3s = "SELECT SUM(total) as total FROM tbar_whats WHERE id_user=$id_user AND id_kpi=" . $hasils['id'];
            $result3s = mysqli_query($conn, $sql3s);
            $row3sd = mysqli_fetch_assoc($result3s);
            $totalnilaisd = $row3sd['total'];
            $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
            $totalws += $nilaiws;
        }
        $bobotkpid = 0;
        $sql5a = "SELECT bobotwhat as bw FROM tbar_bobotkpi WHERE id_user=$id_user";
        $result5a = mysqli_query($conn, $sql5a);
        while ($row5a = mysqli_fetch_assoc($result5a)) {
            $bobotkpid = $row5a['bw'];
        }
        $zbotw = ($totalws * $bobotkpid) / 100;
        // ====================================================================================
        $totalhfg = 0;
        $resultfg = mysqli_query($conn, $sql);

        while ($hasilfg = mysqli_fetch_assoc($resultfg)) {

            $sql7fg = "SELECT SUM(total) as totalh FROM tbar_hows WHERE id_user=$id_user AND id_kpi=" . $hasilfg['id'];
            $result7fg = mysqli_query($conn, $sql7fg);
            $row7fg = mysqli_fetch_assoc($result7fg);
            $totalnilaihfg = $row7fg['totalh'];

            $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
            $totalhfg += $nilaihfg;
        }
        $bobotkpias = 0;
        $sql8a = "SELECT bobothow as bh FROM tbar_bobotkpi WHERE id_user=$id_user";
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
        } else { // $nilai > 110
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
        } else { // $nilai > 110
            return "green";
        }
    }

    function tmaadfl($blan){
        $busd = explode('/', $blan);
        $bl = $busd[0];
        $th = $busd[1];

        if ($bl == '00') {
            $bulannnn = 'Desember ' . ($th - 1);
        }
        if ($bl == '01') {
            $bulannnn = 'Januari ' . $th;
        }
        if ($bl == '02') {
            $bulannnn = 'Februari ' . $th;
        }
        if ($bl == '03') {
            $bulannnn = 'Maret ' . $th;
        }
        if ($bl == '04') {
            $bulannnn = 'April ' . $th;
        }
        if ($bl == '05') {
            $bulannnn = 'Mei ' . $th;
        }
        if ($bl == '06') {
            $bulannnn = 'Juni ' . $th;
        }
        if ($bl == '07') {
            $bulannnn = 'Juli ' . $th;
        }
        if ($bl == '08') {
            $bulannnn = 'Agustus ' . $th;
        }
        if ($bl == '09') {
            $bulannnn = 'September ' . $th;
        }
        if ($bl == '10') {
            $bulannnn = 'Oktober ' . $th;
        }
        if ($bl == '11') {
            $bulannnn = 'November ' . $th;
        }
        if ($bl == '12') {
            $bulannnn = 'Desember ' . $th;
        }
        return $bulannnn;
    }

   // Jika setiap bulan hanya ada 1 archive per user
$archivec = "SELECT DISTINCT ta.id_archive, ta.bulan, ta.status, ta.reviewed_by, ta.reviewed_at, ta.approved_by, ta.approved_at 
            FROM tbar_archive ta
            WHERE ta.id_user = $id_user
            ORDER BY ta.bulan DESC";
$getArch = mysqli_query($conn, $archivec);

require_once 'helper/ss_archive_functions.php';
ensureSSArchiveTables($conn);

$active_tab = isset($_GET['tab']) && in_array($_GET['tab'], ['kpi', 'ss']) ? $_GET['tab'] : 'kpi';

$query_ss_arch = "SELECT a.*, u.nama_lngkp as verifikator_name 
                  FROM tbar_ss_archive a 
                  LEFT JOIN tb_users u ON u.id = a.verified_by 
                  WHERE a.id_user = $id_user 
                  ORDER BY a.bulan DESC";
$getSSArch = mysqli_query($conn, $query_ss_arch);
}
?>
<html lang="en">

<?php include("pages/part/p_header.php"); 
echo '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">';?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="dashboard-utama" class="nav-link">Dashboard</a> </li>
                    <?php if (
                        $jabatan == "Koordinator" ||
                        $jabatan == "Manager" ||
                        $jabatan == "Kadep" ||
                        $jabatan == "Direktur" ||
                        $jabatan == "Wadir Utama"
                    ) { ?>
                        <li class="nav-item d-none d-md-block"> <a href="archivekabag" class="nav-link">Archive Anggota</a> </li>
                    <?php }?>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->

                <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png"
                                class="user-image rounded-circle shadow" alt="User Image"> <span
                                class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end"> <!--begin::User Image-->
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                                </center>
                            </li> <!--end::Menu Footer-->
                        </ul>
                    </li> <!--end::User Menu Dropdown-->
                </ul> <!--end::End Navbar Links-->

            </div> <!--end::Container-->
        </nav> <!--end::Header--> <!--begin::Sidebar-->
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="mt-3"> <!--begin::Container-->
                <div class="app-content"> <!--begin::Container-->
                    <div class="container-fluid"> <!--begin::Row-->
                        <div class="row"> <!-- Start col -->
                            <div class="mt-3">
                                <!-- Navigation Tabs Archive KPI & Archive SS -->
                                <ul class="nav nav-pills mb-3">
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($active_tab == 'kpi') ? 'active fw-bold' : '' ?>" href="archive?tab=kpi">
                                            <i class="bi bi-bar-chart-fill me-1"></i> Archive KPI
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link <?= ($active_tab == 'ss') ? 'active fw-bold' : '' ?>" href="archive?tab=ss">
                                            <i class="bi bi-award-fill me-1"></i> Archive Skill Standard
                                        </a>
                                    </li>
                                </ul>

                                <?php if ($active_tab == 'kpi') { ?>
                                <!-- TAB KONTEN 1: ARCHIVE KPI -->
                                <div class="table-responsive">
                                    <table id="datatablenya" class="table align-midle table-hover table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="3%"><center>No</center></th>
                                                <th><center>Bulan</center></th>
                                                <th width="15%"><center>Nilai</center></th>
                                                <th width="15%"><center>KPI</center></th>
                                                <th width="10%"><center>#</center></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $no = 1; if($getArch){ while ($row = mysqli_fetch_assoc($getArch)) { 
                                                $nilai = round(getnilaiaa($conn,$row['bulan'],$id_user),2);
                                                $status = $row['status'];
                                            ?>
                                            <tr>
                                                <td><center><?= $no; ?></center></td>
                                                <td><center><?= tmaadfl($row['bulan']) ?></center></td>
                                                <td><center><?= $nilai ?></center></td>
                                                <td style="color:<?= getsfo($nilai) ?>">
                                                    <center><?= getstatad($nilai) ?></center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <a type="button" href="archivepoin?idarc=<?= $row['bulan'];?>"
                                                        class="btn btn-success btn-sm"><i class="bi bi-eye"></i></a>
                                                    </center>
                                                </td>
                                            </tr>
                                            <?php $no++; } }?>
                                        </tbody>
                                    </table>
                                </div>

                                <?php } else { ?>
                                <!-- TAB KONTEN 2: ARCHIVE SKILL STANDARD -->
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
                                                <th width="10%"><center>#</center></th>
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
                                                            <?php if (!empty($row_ss['verifikator_name'])) { ?>
                                                                <br><small class="text-muted" style="font-size: 11px;">Oleh: <?= htmlspecialchars($row_ss['verifikator_name']) ?></small>
                                                            <?php } ?>
                                                        <?php } else { ?>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-clock-history me-1"></i> Belum Terverifikasi
                                                            </span>
                                                        <?php } ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <a href="archivesspoin?idarc=<?= urlencode($row_ss['bulan']); ?>" class="btn btn-success btn-sm" title="Lihat Detail">
                                                            <i class="bi bi-eye"></i>
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
                                                        Belum ada arsip Skill Standard. Arsip akan otomatis tercatat saat atasan melakukan verifikasi bulanan.
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