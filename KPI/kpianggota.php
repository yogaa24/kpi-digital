<!-- kpianggota -->
<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/sp_functions.php';
    require 'helper/verified_functions.php';

    updateExpiredSP($conn);

    $id_sf = $_GET['id'];
    $user_id = isset($id_sf) ? $id_sf : $id_user;

    // Cek status verified
    $bulan_sekarang = date('m/Y');
    $verified_status = checkKPIVerified($conn, $id_sf, $bulan_sekarang);

    $sqlang = "SELECT * FROM tb_users WHERE id='$id_sf'";
    $resulasft = mysqli_query($conn, $sqlang);
    while ($hasilsfa = mysqli_fetch_assoc($resulasft)) {
        $nama_lngkpan = $hasilsfa['nama_lngkp'];
        $nikan = $hasilsfa['nik'];
        $bagianan = $hasilsfa['bagian'];
        $departementan = $hasilsfa['departement'];
        $jabatanan = $hasilsfa['jabatan'];
        $atasanan = $hasilsfa['atasan'];
        $penilaian = $hasilsfa['penilai'];
    }

    // Hitung nilai dengan SP
    function getnilaiWithSPDisplay($conn, $id) {
        // Hitung nilai asli (copy dari fungsi getnilai yang sudah ada)
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
        
        $nilai_asli = $zboth + $zbotw;
        
        // Kurangi dengan SP jika ada
        return calculateKPIWithSP($conn, $id, $nilai_asli);
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

        return $zboth + $zbotw;
    }
    // Proses verify/unverify
    if (isset($_POST['verifyKPI'])) {
        $keterangan = $_POST['keterangan'] ?? '';
        if (verifyKPI($conn, $id_sf, $id_user, $keterangan, $bulan_sekarang)) {
            echo "<script>
                alert('KPI berhasil diverifikasi!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "?id=" . $id_sf . "';
            </script>";
        } else {
            echo "<script>alert('Gagal memverifikasi KPI!');</script>";
        }
    }

    if (isset($_POST['unverifyKPI'])) {
        if (unverifyKPI($conn, $id_sf, $bulan_sekarang)) {
            echo "<script>
                alert('Verifikasi KPI berhasil dibatalkan!');
                window.location.href = '" . $_SERVER['PHP_SELF'] . "?id=" . $id_sf . "';
            </script>";
        } else {
            echo "<script>alert('Gagal membatalkan verifikasi!');</script>";
        }
    }
    if (isset($_POST['updateWhatB'])) {
        $bwasfg = $_POST['bobot'];
        $idfj = $_POST['idU'];
        $sql = "UPDATE `tb_bobotkpi` SET bobotwhat =" . $bwasfg . " where id_user =" . $idfj;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Woops! Gagal update.')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }
    if (isset($_POST['updateHowB'])) {
        $bwasfg = $_POST['bobot'];
        $idfj = $_POST['idU'];
        $sql = "UPDATE `tb_bobotkpi` SET bobothow =" . $bwasfg . " where id_user =" . $idfj;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Woops! Gagal update.')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }
    if (isset($_POST['saveFeedback'])) {
        $feedback_text = mysqli_real_escape_string($conn, $_POST['feedback_text']);
        $bulan_feedback = date('m/Y');
        $pemberi_feedback = $id_user; // Yang login saat ini
        $penerima_feedback = $_POST['user_target']; // Target user
        
        // Cek apakah sudah ada feedback untuk bulan ini
        $cek_feedback = mysqli_query($conn, "SELECT id_feedback FROM tb_feedback WHERE 
            id_user_pemberi = $pemberi_feedback 
            AND id_user_penerima = $penerima_feedback 
            AND bulan = '$bulan_feedback'");
        
        if (mysqli_num_rows($cek_feedback) > 0) {
            // Update feedback yang sudah ada
            $update_feedback = mysqli_query($conn, "UPDATE tb_feedback SET 
                feedback = '$feedback_text',
                tanggal_update = NOW()
                WHERE id_user_pemberi = $pemberi_feedback 
                AND id_user_penerima = $penerima_feedback 
                AND bulan = '$bulan_feedback'");
            
            if ($update_feedback) {
                echo "<script>
                    alert('✅ Feedback berhasil diperbarui!');
                    window.location.href = 'kpianggota?id=" . $penerima_feedback . "'; // PERBAIKI: redirect ke halaman yang sama
                </script>";
            }
        } else {
            // Insert feedback baru
            $insert_feedback = mysqli_query($conn, "INSERT INTO tb_feedback 
                (id_user_pemberi, id_user_penerima, feedback, bulan, tanggal_buat) 
                VALUES ($pemberi_feedback, $penerima_feedback, '$feedback_text', '$bulan_feedback', NOW())");
            
            if ($insert_feedback) {
                echo "<script>
                    alert('✅ Feedback berhasil disimpan!');
                    window.location.href = 'kpianggota?id=" . $penerima_feedback . "'; // PERBAIKI: redirect ke halaman yang sama
                </script>";
            }   
        }
    }

    // Fungsi untuk mendapatkan feedback
    function getFeedback($conn, $id_user_target, $bulan) {
        $feedback_data = [
            'self' => null,
            'atasan' => null
        ];
        
        // Feedback dari diri sendiri
        $self_feedback = mysqli_query($conn, "SELECT * FROM tb_feedback WHERE 
            id_user_pemberi = $id_user_target 
            AND id_user_penerima = $id_user_target 
            AND bulan = '$bulan'");
        
        if ($self_row = mysqli_fetch_assoc($self_feedback)) {
            $feedback_data['self'] = $self_row;
        }
        
        // Feedback dari atasan
        $atasan_feedback = mysqli_query($conn, "SELECT f.*, u.nama_lngkp as nama_pemberi 
            FROM tb_feedback f
            JOIN tb_users u ON f.id_user_pemberi = u.id
            WHERE f.id_user_penerima = $id_user_target 
            AND f.id_user_pemberi != $id_user_target 
            AND f.bulan = '$bulan'
            ORDER BY f.tanggal_buat DESC");
        
        if ($atasan_row = mysqli_fetch_assoc($atasan_feedback)) {
            $feedback_data['atasan'] = $atasan_row;
        }
        
        return $feedback_data;
    }
    $blan = date('m/Y');
$busd = explode('/', $blan);

function tmapil($bl, $th){
    $bulannnn = '';
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
} ?>
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

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                 class="bi bi-list"></i> </a> </li>
                               <?php if ($leveel == 5) { ?>
                                <li class="nav-item d-none d-md-block">
                                    <a href="kpikadep" class="nav-link">Kembali</a>
                                </li>
                            <?php } elseif ($leveel == 6) { ?>
                                <li class="nav-item d-none d-md-block">
                                    <a href="dashboard-adminhrd" class="nav-link">Kembali</a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item d-none d-md-block">
                                    <a href="kpikabag" class="nav-link">Kembali</a>
                                </li>
                            <?php } ?>
                    <li class="nav-item d-none d-md-block"> <a href="kpidetailanggota?id=<?= $_GET['id']; ?>" class="nav-link">Detail KPI</a> </li>
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
        </nav>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="mt-3"> <!--begin::Container-->
                <!-- isi -->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row"> <!-- Start col -->
                        <div class="col-lg-4 connectedSortable">
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-danger">
                                    <h5 style="color:white;" class="card-title fw-bolder">Profil Karyawan</h5>
                                    <div class="card-tools">
                                        <!-- <button style="color: white;" type="button" class="btn btn-tool">
                    <i class="bi bi-pencil"></i>
                </button> -->
                                        <button style="color: white;" type="button" class="btn btn-tool"
                                            data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="nama-addon">Nama : </span>
                                        <input disabled type="text" value="<?php echo $nama_lngkpan; ?>"
                                            class="form-control" placeholder="Nama" aria-label="Nama"
                                            aria-describedby="nama-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="jabatan-addon">Jabatan :</span>
                                        <input disabled type="text"
                                            value="<?php echo $jabatanan . " - " . $bagianan; ?>" class="form-control"
                                            placeholder="Jabatan" aria-label="Jabatan" aria-describedby="jabatan-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="depart-addon">Departement :</span>
                                        <input disabled type="text" value="<?php echo $departementan; ?>"
                                            class="form-control" placeholder="Departement" aria-label="Departement"
                                            aria-describedby="depart-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="atasan-addon">Atasan :</span>
                                        <input disabled type="text" value="<?php echo $atasanan; ?>"
                                            class="form-control" placeholder="Koordinator" aria-label="Koordinator"
                                            aria-describedby="atasan-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="penilai-addon">Penilai Tambahan:</span>
                                        <input disabled type="text" value="<?php echo $penilaian; ?>"
                                            class="form-control" placeholder="Penilai" aria-label="Penilai"
                                            aria-describedby="penilai-addon">
                                    </div>

                                </div>
                            </div>
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-warning bg-gradient">
                                    <h5 style="color:black;" class="card-title fw-bolder">
                                        TOTAL NILAI KPI
                                        <?php if ($verified_status) { ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="bi bi-check-circle-fill"></i> Verified
                                            </span>
                                        <?php } ?>
                                    </h5>
                                    <?php if ($leveel == 2 || $leveel == 3 ||$leveel == 4 || $leveel == 5) { ?>
                                    <div class="card-tools">
                                        <button style="color: black; margin-top: -20px; margin-right: 5px;" type="button"
                                            data-bs-toggle="dropdown" class="btn btn-tool dropdown-toggle">
                                            <i class="bi bi-shield-check fs-6"></i>
                                        </button>
                                        <button type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#feedbackModal"
                                            class="btn btn-tool"
                                            title="Feedback KPI"
                                            style="color: black; margin-top: -20px; margin-right: 5px;"> <!-- PERBAIKI: ubah color jadi black -->
                                            <i class="bi bi-chat-left-text-fill fs-5"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                                            <?php if ($verified_status) { ?>
                                                <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#unverifyModal">
                                                    <i class="bi bi-x-circle"></i> Batalkan Verifikasi
                                                </a>
                                            <?php } else { ?>
                                                <a href="#" class="dropdown-item text-success" data-bs-toggle="modal"
                                                    data-bs-target="#verifyModal">
                                                    <i class="bi bi-check-circle"></i> Verifikasi KPI
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <?php } ?>
                                </div>
                                <div class="card-body">
                                    <?php 
                                    $kpi_result = getnilaiWithSPDisplay($conn, $user_id);
                                    $nilai_asli = $kpi_result['nilai_asli'];
                                    $nilai_akhir = $kpi_result['nilai_akhir'];
                                    $sp_data = $kpi_result['sp_data'];
                                    $pengurangan = $kpi_result['pengurangan'];
                                    
                                    // Tentukan warna dan rating
                                    if ($nilai_akhir < 90) {
                                        $wrabs = "red";
                                        $rating = "POOR";
                                    } elseif ($nilai_akhir <= 100) {
                                        $wrabs = "orange";
                                        $rating = "GOOD";
                                    } elseif ($nilai_akhir <= 110) {
                                        $wrabs = "blue";
                                        $rating = "Very Good";
                                    } else {
                                        $wrabs = "green";
                                        $rating = "Excellent";
                                    }
                                    ?>
                                    
                                    <!-- Alert SP jika ada -->
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="table-secondary">
                                                    <center>WHAT + HOW</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php if ($sp_data) { ?>
                                            <tr>
                                                <th>
                                                    <center>NILAI ASLI (Sebelum SP)</center>
                                                </th>
                                                <td>
                                                    <center><del><?= number_format($nilai_asli, 2); ?></del></center>
                                                </td>
                                            </tr>
                                            <tr class="table-warning">
                                                <th>
                                                    <center>PENGURANGAN SP (<?=$sp_data['jenis_sp']?>)</center>
                                                </th>
                                                <td class="text-danger">
                                                    <center><strong>- <?= $pengurangan ?></strong></center>
                                                </td>
                                            </tr>
                                            <?php } ?>
                                            <tr class="table-primary">
                                                <th>
                                                    <center>NILAI KPI AKHIR</center>
                                                </th>
                                                <th>
                                                    <center><?= number_format($nilai_akhir, 2); ?></center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <td colspan="2" style="font-size: 25pt; color:<?= $wrabs ?>" class="fw-bolder">
                                                    <center><?= $rating ?></center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    
                                    <?php if ($sp_data) { ?>
                                    <div class="alert alert-info mb-0 mt-3">
                                        <small>
                                            <i class="bi bi-calendar-check"></i> 
                                            SP ini akan berakhir pada <strong><?=formatTanggalIndo($sp_data['masa_berlaku_selesai'])?></strong>
                                        </small>
                                    </div>
                                    <?php } ?>
                                </div>      
                            </div>
                            <?php if ($verified_status) { 
    $verifier_name = getVerifierName($conn, $verified_status['verified_by']);
?>
    <div class="alert alert-success mb-0 mt-3">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-2"></i>
            <div>
                <strong>KPI Sudah Diverifikasi</strong><br>
                <small>
                    Oleh: <strong><?= $verifier_name ?></strong><br>
                    <i class="bi bi-calendar-check"></i> 
                    Pada: <?= date('d/m/Y H:i', strtotime($verified_status['verified_at'])) ?>
                    <?php if (!empty($verified_status['keterangan'])) { ?>
                        <br><i class="bi bi-chat-text"></i> Catatan: <?= htmlspecialchars($verified_status['keterangan']) ?>
                    <?php } ?>
                </small>
            </div>
        </div>
    </div>
<?php } ?>


                        </div>
<!-- ================================================================================================================================================ -->
                        <div class="col-lg-8 connectedSortable">
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                                    <h5 style="color:white;" class="card-title fw-bolder">What</h5>
                                    <div class="card-tools">
                                        <button style="color: white;" data-bs-toggle="modal" data-bs-target="#bobotWhatss" type="button"
                                            class="btn btn-tool">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button style="color: white;" type="button" class="btn btn-tool"
                                            data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr class="table-secondary">
                                                <th style="color: white;" scope="col" class="col-7 bg-primary">
                                                    <center>Poin</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1  bg-primary">
                                                    <center>Bobot</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-primary">
                                                    <center>Penilaian</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-primary">
                                                    <center>NILAI</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalw = 0;
                                            $totalbobot = 0;
                                            $totalnilai4 = 0;
                                            $sqlad = "SELECT * FROM tb_kpi WHERE id_user='$id_sf'";
                                            $resultsfafa = mysqli_query($conn, $sqlad);
                                            while ($hasilddd = mysqli_fetch_assoc($resultsfafa)) {
                                                $poin = $hasilddd['poin'];
                                                $bobot = $hasilddd['bobot'];
                                                $dsf = $hasilddd['id'];

                                                $sql3 = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id_sf AND id_kpi=$dsf";
                                                $result3 = mysqli_query($conn, $sql3);
                                                $row3 = mysqli_fetch_assoc($result3);
                                                $totalnilai = $row3['total'];
                                                $nilaiw = number_format(($totalnilai * $bobot) / 100, 2);
                                                $totalw += number_format($nilaiw, 2);
                                                $totalbobot += $bobot;

                                                echo "
                                <tr>
                                    <td>$poin</td>
                                    <td><center>$bobot%</center></td>
                                    <td><center>" . round($totalnilai) . "</center></td>
                                    <td><center>$nilaiw</center></td>
                                </tr>";
                                            }

                                            $sql4 = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id_sf";
                                            $result4 = mysqli_query($conn, $sql4);
                                            $row4 = mysqli_fetch_assoc($result4);
                                            $totalnilai4 = $row4['total'];
                                            ?>

                                            <tr class="table-secondary">
                                                <th>
                                                    <center>TOTAL NILAI</center>
                                                </th>
                                                <th>
                                                    <center><?= $totalbobot ?> %</center>
                                                </th>
                                                <th>
                                                    <center><?= round($totalnilai4) ?></center>
                                                </th>
                                                <th>
                                                    <center><?= $totalw ?></center>
                                                </th>
                                            </tr>

                                        </tbody>

                                        <tr>
                                            <th rowspan="2"></th>
                                            <th style="color: white;" rowspan="2"
                                                class="align-middle table-secondary bg-primary">
                                                <center>WHAT</center>
                                            </th>
                                            <th class="table-secondary">
                                                <center>BOBOT</center>
                                            </th>
                                            <th class="table-secondary">
                                                <center>NILAI</center>
                                            </th>
                                        </tr>
                                        <tr>
                                            <?php
                                            $bobotkpiw = 0;
                                            $sql5 = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id_sf";
                                            $result5 = mysqli_query($conn, $sql5);
                                            while ($row5 = mysqli_fetch_assoc($result5)) {
                                                $bobotkpiw = $row5['bw'];
                                            }
                                            $zbotw = ($totalw * $bobotkpiw) / 100;
                                            ?>

                                            <td>
                                                <center><?= $bobotkpiw ?> % </center>
                                            </td>
                                            <td>
                                                <center><?= $zbotw ?></center>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!-- --------------------------------------------------------------------->
                                </div>

                            </div>
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
                                    <h5 style="color:white;" class="card-title fw-bolder">How</h5>
                                    <div class="card-tools">
                                        <button style="color: white;" data-bs-toggle="modal" data-bs-target="#bobotHow" type="button"
                                            class="btn btn-tool">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button style="color: white;" type="button" class="btn btn-tool"
                                            data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr class="table-secondary">
                                                <th style="color: white;" scope="col" class="col-7  bg-success">
                                                    <center>Poin</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-success">
                                                    <center>Bobot</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-success">
                                                    <center>Penilaian</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-success">
                                                    <center>NILAI</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalh = 0;
                                            $totalboboth = 0;
                                            $totalnilai7 = 0;

                                             $sqlads = "SELECT * FROM tb_kpi WHERE id_user='$id_sf'";
                                            $resultsfafas = mysqli_query($conn, $sqlads);

                                            while ($hasil = mysqli_fetch_assoc($resultsfafas)) {
                                                $poin2 = $hasil['poin2'];
                                                $bobot2 = $hasil['bobot2'];
                                                $dsf = $hasil['id'];

                                                $sql7 = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id_sf AND id_kpi=$dsf";
                                                $result7 = mysqli_query($conn, $sql7);
                                                $row7 = mysqli_fetch_assoc($result7);
                                                $totalnilaih = $row7['totalh'];

                                                $nilaih = ($totalnilaih * $bobot2) / 100;
                                                $totalh += $nilaih;
                                                $totalboboth += $bobot2;

                                                echo "
                                <tr>
                                    <td>$poin2</td>
                                    <td><center>$bobot2%</center></td>
                                    <td><center>" . round($totalnilaih) . "</center></td>
                                    <td><center>$nilaih</center></td>
                                </tr>";
                                            }

                                            $sql4 = "SELECT SUM(total) as total FROM tb_hows WHERE id_user=$id_sf ";
                                            $result4 = mysqli_query($conn, $sql4);
                                            $row4 = mysqli_fetch_assoc($result4);
                                            $totalnilai5 = $row4['total'];
                                            ?>

                                            <tr class="table-secondary">
                                                <th>
                                                    <center>TOTAL NILAI</center>
                                                </th>
                                                <th>
                                                    <center>
                                                        <?= $totalboboth ?> %
                                                    </center>
                                                </th>
                                                <th>
                                                    <center>
                                                        <?= round($totalnilai5) ?>
                                                    </center>
                                                </th>
                                                <th>
                                                    <center>
                                                        <?= $totalh ?>
                                                    </center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2"></th>
                                                <th style="color: white;" rowspan="2"
                                                    class=" bg-success align-middle table-secondary">
                                                    <center>HOW</center>
                                                </th>
                                                <th class="table-secondary">
                                                    <center>BOBOT</center>
                                                </th>
                                                <th class="table-secondary">
                                                    <center>NILAI</center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <?php
                                                $bobotkpih = 0;
                                                $sql8 = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id_sf";
                                                $result8 = mysqli_query($conn, $sql8);
                                                while ($row8 = mysqli_fetch_assoc($result8)) {
                                                    $bobotkpih = $row8['bh'];
                                                }
                                                $zboth = ($totalh * $bobotkpih) / 100;
                                                ?>

                                                <td>
                                                    <center>
                                                        <?= $bobotkpih ?>%
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?= $zboth ?>
                                                    </center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- --------------------------------------------------------------------->
                                </div>

                            </div>
                            <div class="modal fade" id="bobotWhatss" tabindex="-1" aria-labelledby="bobotWhatssLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="bobotWhatssLabel">Bobot What</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="" class="input">
                                                <input type="input" value="<?= $id_sf; ?>" class="form-control"
                                                    name="idU" hidden>
                                                <div class="input-group mb-3">
                                                    <span style="color : #343A40;" class="input-group-text fw-bold"
                                                        id="bobot">Bobot What
                                                        :</span>
                                                    <input type="input" value="<?= $bobotkpiw; ?>" class="form-control"
                                                        name="bobot" placeholder="0-90" aria-label="Bobot KPI"
                                                        aria-describedby="bobot">
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="input" name="updateWhatB"
                                                class="btn btn-primary">Simpan</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="bobotHow" tabindex="-1" aria-labelledby="bobotHowLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="bobotHowLabel">Bobot How</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="" class="input">
                                                <input type="input" value="<?= $dsf; ?>" class="form-control"
                                                    name="idkss" hidden>
                                                <input type="input" value="<?= $id_sf; ?>" class="form-control"
                                                    name="idU" hidden>
                                                <div class="input-group mb-3">
                                                    <span style="color : #343A40;" class="input-group-text fw-bold"
                                                        id="bobot">Bobot How
                                                        :</span>
                                                    <input type="input" value="<?= $bobotkpih; ?>" class="form-control"
                                                        name="bobot" placeholder="0-90" aria-label="Bobot KPI"
                                                        aria-describedby="bobot">
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="input" name="updateHowB"
                                                class="btn btn-primary">Simpan</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php 
                            $bulan_sekarang = date('m/Y');
                            $feedback_data = getFeedback($conn, $id_sf, $bulan_sekarang); // PERBAIKI: gunakan $id_sf bukan $id_user

                            // Cek apakah user yang login adalah atasan dari user yang dilihat
                            $is_atasan = false;
                            $user_info = mysqli_query($conn, "SELECT atasan FROM tb_users WHERE id = $id_sf"); // PERBAIKI: gunakan $id_sf
                            $user_row = mysqli_fetch_assoc($user_info);
                            if ($user_row['atasan'] == $nama_lngkp) { // Jika atasan user yang dilihat = user yang login
                                $is_atasan = true;
                            }
                            ?>

                            <!-- Modal Feedback -->
                            <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title fw-bold" id="feedbackModalLabel">
                                                <i class="bi bi-chat-left-text-fill"></i> Feedback KPI - <?= tmapil($busd[0], $busd[1]); ?>
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Tab Navigation -->
                                            <ul class="nav nav-tabs" id="feedbackTab" role="tablist">
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link active" id="view-tab" data-bs-toggle="tab" data-bs-target="#view-feedback" type="button" role="tab">
                                                        <i class="bi bi-eye"></i> Lihat Feedback
                                                    </button>
                                                </li>
                                                <li class="nav-item" role="presentation">
                                                    <button class="nav-link" id="write-tab" data-bs-toggle="tab" data-bs-target="#write-feedback" type="button" role="tab">
                                                        <i class="bi bi-pencil"></i> Tulis Feedback
                                                    </button>
                                                </li>
                                            </ul>

                                            <!-- Tab Content -->
                                            <div class="tab-content mt-3" id="feedbackTabContent">
                                                <!-- Tab Lihat Feedback -->
                                                <div class="tab-pane fade show active" id="view-feedback" role="tabpanel">
                                                    <!-- Feedback dari Diri Sendiri -->
                                                    <div class="card mb-3">
                                                        <div class="card-header bg-primary text-white">
                                                            <h6 class="mb-0">
                                                                <i class="bi bi-person-fill"></i> Feedback dari Diri Sendiri
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php if ($feedback_data['self']) { ?>
                                                                <div class="feedback-content">
                                                                    <p><?= nl2br(htmlspecialchars($feedback_data['self']['feedback'])) ?></p>
                                                                    <small class="text-muted">
                                                                        <i class="bi bi-clock"></i> 
                                                                        Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($feedback_data['self']['tanggal_update'] ?? $feedback_data['self']['tanggal_buat'])) ?>
                                                                    </small>
                                                                </div>
                                                            <?php } else { ?>
                                                                <p class="text-muted mb-0">
                                                                    <i class="bi bi-info-circle"></i> Belum ada feedback dari diri sendiri
                                                                </p>
                                                            <?php } ?>
                                                        </div>
                                                    </div>

                                                    <!-- Feedback dari Atasan -->
                                                    <div class="card">
                                                        <div class="card-header bg-success text-white">
                                                            <h6 class="mb-0">
                                                                <i class="bi bi-person-badge"></i> Feedback dari Atasan
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <?php if ($feedback_data['atasan']) { ?>
                                                                <div class="feedback-content">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <strong><?= $feedback_data['atasan']['nama_pemberi'] ?></strong>
                                                                    </div>
                                                                    <p><?= nl2br(htmlspecialchars($feedback_data['atasan']['feedback'])) ?></p>
                                                                    <small class="text-muted">
                                                                        <i class="bi bi-clock"></i> 
                                                                        Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($feedback_data['atasan']['tanggal_update'] ?? $feedback_data['atasan']['tanggal_buat'])) ?>
                                                                    </small>
                                                                </div>
                                                            <?php } else { ?>
                                                                <p class="text-muted mb-0">
                                                                    <i class="bi bi-info-circle"></i> Belum ada feedback dari atasan
                                                                </p>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Tab Tulis Feedback -->
                                                <div class="tab-pane fade" id="write-feedback" role="tabpanel">
                                                    <form method="POST" action="">
                                                        <input type="hidden" name="user_target" value="<?= $id_sf ?>"> <!-- PERBAIKI: gunakan $id_sf -->
                                                        
                                                        <div class="alert alert-info">
                                                            <i class="bi bi-info-circle"></i> 
                                                            <strong>Panduan:</strong>
                                                            <ul class="mb-0 mt-2">
                                                                <li>Tuliskan refleksi diri atau masukan untuk peningkatan performa</li>
                                                                <li>Feedback dapat diperbarui sebelum bulan berganti</li>
                                                                <li>Bersikaplah objektif dan konstruktif</li>
                                                            </ul>
                                                        </div>

                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">
                                                                <?php if ($is_atasan) { ?>
                                                                    <i class="bi bi-person-badge"></i> Feedback untuk <?= $nama_lngkpan ?> <!-- PERBAIKI: gunakan $nama_lngkpan -->
                                                                <?php } else { ?>
                                                                    <i class="bi bi-person-fill"></i> Feedback untuk Diri Sendiri
                                                                <?php } ?>
                                                            </label>
                                                            <textarea class="form-control" name="feedback_text" rows="8" 
                                                                placeholder="Tuliskan feedback Anda di sini..." required><?php 
                                                                if ($is_atasan && $feedback_data['atasan']) {
                                                                    echo htmlspecialchars($feedback_data['atasan']['feedback']);
                                                                } elseif (!$is_atasan && $feedback_data['self']) {
                                                                    echo htmlspecialchars($feedback_data['self']['feedback']);
                                                                }
                                                            ?></textarea>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted">
                                                                <i class="bi bi-calendar"></i> Periode: <?= tmapil($busd[0], $busd[1]); ?>
                                                            </small>
                                                            <button type="submit" name="saveFeedback" class="btn btn-primary">
                                                                <i class="bi bi-save"></i> Simpan Feedback
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal Verify KPI -->
                            <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title fw-bold" id="verifyModalLabel">
                                                <i class="bi bi-check-circle"></i> Verifikasi KPI
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="">
                                            <div class="modal-body">
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle"></i> 
                                                    Anda akan memverifikasi KPI <strong><?= $nama_lngkpan ?></strong> untuk periode <strong><?= date('F Y') ?></strong>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="keterangan" class="form-label">Catatan (Opsional)</label>
                                                    <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                                        placeholder="Tambahkan catatan verifikasi..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" name="verifyKPI" class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> Verifikasi
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Unverify KPI -->
                            <div class="modal fade" id="unverifyModal" tabindex="-1" aria-labelledby="unverifyModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title fw-bold" id="unverifyModalLabel">
                                                <i class="bi bi-x-circle"></i> Batalkan Verifikasi
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="">
                                            <div class="modal-body">
                                                <div class="alert alert-warning">
                                                    <i class="bi bi-exclamation-triangle"></i> 
                                                    Apakah Anda yakin ingin membatalkan verifikasi KPI ini?
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                                                <button type="submit" name="unverifyKPI" class="btn btn-danger">
                                                    <i class="bi bi-x-circle"></i> Ya, Batalkan
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div> <!--end::Container-->
            </div> <!--end::App Content-->
        </main> <!--end::App Main--> <!--begin::Footer-->

        <?php include("pages/part/p_footer.php"); ?>
</body>

</html>


</html>