<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require_once 'helper/sp_functions.php';

    // Ambil level user yang sedang login
    $id_user_login = $_SESSION['id_user'];
    $sql_login = "SELECT level FROM tb_users WHERE id='$id_user_login'";
    $result_login = mysqli_query($conn, $sql_login);
    $leveel_login = '';
    while ($hasil_login = mysqli_fetch_assoc($result_login)) {
        $leveel_login = $hasil_login['level'];
    }

    $id_user = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $idar = isset($_GET['idar']) ? $_GET['idar'] : '';
    $idar_safe = mysqli_real_escape_string($conn, $idar);

    if ($id_user <= 0 || $idar === '') {
        header("Location: archivekabag");
        exit();
    }

    $sql = "SELECT * FROM tb_users WHERE id=$id_user";
    $result = mysqli_query($conn, $sql);

    $username;
    $nama_lngkp;
    $nik;
    $departement;
    $jabatan;
    $atasan;
    $penilai;
    $leveel;
    while ($hasil = mysqli_fetch_assoc($result)) {
        $username = $hasil['username'];
        $nama_lngkp = $hasil['nama_lngkp'];
        $nik = $hasil['nik'];
        $bagian = $hasil['bagian'];
        $departement = $hasil['departement'];
        $jabatan = $hasil['jabatan'];
        $atasan = $hasil['atasan'];
        $penilai = $hasil['penilai'];
        $leveel = $hasil['level'];
    }

    $query_archive = mysqli_query($conn, "SELECT id_archive FROM tbar_archive WHERE bulan = '$idar_safe' AND id_user = $id_user LIMIT 1");
    if (!$query_archive || mysqli_num_rows($query_archive) == 0) {
        echo "<script>alert('Data archive tidak ditemukan!'); window.location.href='archiveanggota?id=$id_user';</script>";
        exit();
    }

    $row_archive = mysqli_fetch_assoc($query_archive);
    $id_archive = intval($row_archive['id_archive']);
    $current_archive_id = $id_archive;

    $sql = "SELECT * FROM tbar_kpi WHERE id_user = $id_user AND id_arcv = $id_archive";
    $result = mysqli_query($conn, $sql);
    $idKPI;
    $idUSER;
    $poin;
    $bobot;
    $poin2;
    $bobot2;

    $sql2 = "SELECT sum(bobot) FROM tbar_kpi WHERE id_user=$id_user AND id_arcv = $id_archive";
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
    $sql5a = "SELECT bobotwhat as bw FROM tbar_bobotkpi WHERE id_user=$id_user AND id_arcv=$id_archive";
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
    $sql8a = "SELECT bobothow as bh FROM tbar_bobotkpi WHERE id_user=$id_user AND id_arcv=$id_archive";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;
    // ==================================================================================

    $archivec = "SELECT * FROM tbar_archive WHERE id_user = $id_user ORDER BY bulan DESC";
    $getArch = mysqli_query($conn, $archivec);
    
    $blan = $idar;

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

    $nilai_akhir = $nilai_asli;
    $sp_archive_data = null;
    $pengurangan = 0;

    $cek_tabel_sp_archive = mysqli_query($conn, "SHOW TABLES LIKE 'tbar_sp_archive'");
    if ($cek_tabel_sp_archive && mysqli_num_rows($cek_tabel_sp_archive) > 0) {
        $cek_sp_archive = mysqli_query($conn, "SELECT * FROM tbar_sp_archive WHERE id_archive = $id_archive AND id_user = $id_user");
        if ($cek_sp_archive && $sp_row = mysqli_fetch_assoc($cek_sp_archive)) {
            $sp_archive_data = $sp_row;
            $nilai_akhir = $sp_row['nilai_akhir'];
            $pengurangan = $sp_row['pengurangan'];
        }
    }
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
                    <?php if ($leveel_login == 7) { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="archive-adminhrd-detail?id=<?= $id_user ?>" class="nav-link">Kembali</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="archiveanggota?id=<?= $id_user ?>" class="nav-link">Kembali</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item d-none d-md-block"> <a href="archiveangdet?id=<?= $id_user ?>&idar=<?= urlencode($idar) ?>" class="nav-link">Detail KPI</a> </li>
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
