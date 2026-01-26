<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/config.php';
    require_once 'helper/sp_functions.php';

    // Ambil level user yang sedang login
    $id_user_login = $_SESSION['id_user'];
    $sql_login = "SELECT * FROM tb_auth WHERE id_user='$id_user_login'";
    $result_login = mysqli_query($conn, $sql_login);
    $leveel_login = '';
    while ($hasil_login = mysqli_fetch_assoc($result_login)) {
        $leveel_login = $hasil_login['level'];
    }

    $id_user = $_GET['id'];

    $sql = "SELECT * FROM tb_users WHERE id='$id_user'";
    $result = mysqli_query($conn, $sql);

    $sqls = "SELECT * FROM tb_auth WHERE id_user='$id_user'";
    $resultso = mysqli_query($conn, $sqls);

    $username;
    $nama_lngkp;
    $nik;
    $departement;
    $jabatan;
    $atasan;
    $penilai;
    $leveel;
    while ($hasilsf = mysqli_fetch_assoc($resultso)) {
        $leveel = $hasilsf['level'];
    }
    while ($hasil = mysqli_fetch_assoc($result)) {
        $username = $hasil['username'];
        $nama_lngkp = $hasil['nama_lngkp'];
        $nik = $hasil['nik'];
        $bagian = $hasil['bagian'];
        $departement = $hasil['departement'];
        $jabatan = $hasil['jabatan'];
        $atasan = $hasil['atasan'];
        $penilai = $hasil['penilai'];
    }

    $idar = $_GET['idar'];

    $sql= "SELECT tbar_kpi.* FROM tbar_kpi INNER JOIN tbar_archive ON tbar_archive.id_archive = tbar_kpi.id_arcv WHERE tbar_archive.bulan = '$idar' AND tbar_archive.id_user = $id_user";
    $result = mysqli_query($conn, $sql);
    $idKPI;
    $idUSER;
    $poin;
    $bobot;
    $poin2;
    $bobot2;

    $sql2 = "SELECT sum(bobot) FROM tbar_kpi WHERE id_user=$id_user AND bulan = '$idar'";
    $result2 = mysqli_query($conn, $sql2);

    $zboth = 0;
    $zbotw = 0;
    
    $blan = '';

    $totalws = 0;
    $result = mysqli_query($conn, $sql);
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $poin = $hasils['poin'];

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
    $totalbobothfg = 0;
    $resultfg = mysqli_query($conn, $sql);

    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $poin2fg = $hasilfg['poin2'];

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
    // ==================================================================================

    $archivec = "SELECT * FROM tbar_kpi where id_user = $id_user group by bulan";
    $getArch = mysqli_query($conn, $archivec);
    
    $blan = $_GET['idar'];

    $bulannnn = '';
    if ($blan != '') {
        $busd = explode('/', $blan);
        if ($busd[0] == '01') {
            $bulannnn = 'Januari ' . $busd[1];
        }
        if ($busd[0] == '02') {
            $bulannnn = 'Februari ' . $busd[1];
        }
        if ($busd[0] == '03') {
            $bulannnn = 'Maret ' . $busd[1];
        }
        if ($busd[0] == '04') {
            $bulannnn = 'April ' . $busd[1];
        }
        if ($busd[0] == '05') {
            $bulannnn = 'Mei ' . $busd[1];
        }
        if ($busd[0] == '06') {
            $bulannnn = 'Juni ' . $busd[1];
        }
        if ($busd[0] == '07') {
            $bulannnn = 'Juli ' . $busd[1];
        }
        if ($busd[0] == '08') {
            $bulannnn = 'Agustus ' . $busd[1];
        }
        if ($busd[0] == '09') {
            $bulannnn = 'September ' . $busd[1];
        }
        if ($busd[0] == '10') {
            $bulannnn = 'Oktober ' . $busd[1];
        }
        if ($busd[0] == '11') {
            $bulannnn = 'November ' . $busd[1];
        }
        if ($busd[0] == '12') {
            $bulannnn = 'Desember ' . $busd[1];
        }
    }
    $nilai_asli = $zbotw + $zboth;

    // Hitung dengan pengurangan SP untuk user yang dilihat
    $kpi_result = calculateKPIWithSP($conn, $id_user, $nilai_asli);

    // Extract variabel untuk digunakan di archiveProfile.php
    $nilai_akhir = $kpi_result['nilai_akhir'];
    $sp_archive_data = $kpi_result['sp_data'];
    $pengurangan = $kpi_result['pengurangan'];
}

?>
<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <?php if ($leveel_login == 5) { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="archive-adminhrd-detail?id=<?= $id_user ?>" class="nav-link">Kembali</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="archiveanggota?id=<?= $id_user ?>" class="nav-link">Kembali</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item d-none d-md-block"> <a href="archiveangdet?id=<?= $id_user ?>&idar=<?= $idar ?>" class="nav-link">Detail KPI</a> </li>
                </ul>

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
                    </li>
                </ul>

            </div>
        </nav>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php include("pages/archive/archiveProfile.php"); ?>
                            <?php include("pages/archive/archiveSummary.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    <?php include("pages/part/p_footer.php"); ?>
</body>

</html>