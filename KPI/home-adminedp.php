<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/checkAdmin.php';
    require 'helper/sp_functions.php';

    // Hanya Admin EDP yang bisa akses
    
    updateExpiredSP($conn);

    // ========== LOGIKA PEMILIHAN DRIVER ==========
    // Jika ada driver yang dipilih dari dropdown
    $selected_driver_id = isset($_GET['driver_id']) ? intval($_GET['driver_id']) : 0;
    
    // Jika belum ada driver yang dipilih, ambil driver pertama sebagai default
    if ($selected_driver_id == 0) {
        $sql_first_driver = "SELECT u.id FROM tb_users u
                             INNER JOIN tb_auth a ON u.id = a.id_user
                             WHERE u.bagian = 'Driver Distribusi'
                             ORDER BY u.nama_lngkp ASC
                             LIMIT 1";
        $result_first = mysqli_query($conn, $sql_first_driver);
        if ($row_first = mysqli_fetch_assoc($result_first)) {
            $selected_driver_id = $row_first['id'];
        }
    }
    
    // Ambil data driver yang dipilih
    $driver_data = null;
    if ($selected_driver_id > 0) {
        $sql_driver = "SELECT * FROM tb_users WHERE id = $selected_driver_id";
        $result_driver = mysqli_query($conn, $sql_driver);
        $driver_data = mysqli_fetch_assoc($result_driver);
    }
    
    // PENTING: Set variabel untuk query KPI
    // Simpan ID admin yang login
    $admin_id = $_SESSION['id_user'];
    // Set id_user sebagai driver yang dipilih untuk semua query KPI
    $id_user = $selected_driver_id;
    
    // ========== QUERY KPI UNTUK DRIVER TERPILIH ==========
    // GUNAKAN $id_user (yang sudah di-set ke $selected_driver_id)
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id_user'";
    require 'helper/getHow.php';
    
    $zboth = 0;
    $zbotw = 0;
    $blan = '';

    $totalws = 0;
    
    // Query KPI untuk driver yang dipilih
    $result = mysqli_query($conn, $sql);
    $resultsaf = mysqli_query($conn, $sql);
    
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $poin = $hasils['poin'];

        $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id_user AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id_user";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;
    
    // Hitung nilai dengan SP
    function getnilaiWithSPDisplay($conn, $id) {
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
    
    // Hitung How
    $totalhfg = 0;
    $totalbobothfg = 0;
    $resultfg = mysqli_query($conn, $sql);

    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $poin2fg = $hasilfg['poin2'];

        $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id_user AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];

        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id_user";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;

    // Handler Update Bobot
    if (isset($_POST['updateWhatB'])) {
        $bwasfg = floatval($_POST['bobot']);
        $idfj = intval($_POST['idU']);
        $sql = "UPDATE `tb_bobotkpi` SET bobotwhat = $bwasfg WHERE id_user = $idfj";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: home-adminedp?driver_id=' . $idfj);
            exit();
        } else {
            echo "<script>alert('Gagal update bobot What!')</script>";
        }
    }
    
    if (isset($_POST['updateHowB'])) {
        $bwasfg = floatval($_POST['bobot']);
        $idfj = intval($_POST['idU']);
        $sql = "UPDATE `tb_bobotkpi` SET bobothow = $bwasfg WHERE id_user = $idfj";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: home-adminedp?driver_id=' . $idfj);
            exit();
        } else {
            echo "<script>alert('Gagal update bobot How!')</script>";
        }
    }
    
    // ========== HANDLER UNTUK KPI DRIVER (Admin EDP mengelola KPI driver) ==========
    
    // Tambah KPI Poin untuk Driver
    if (isset($_POST['submit'])) {
        $driver_id = intval($_POST['driver_id']);
        $poin = mysqli_real_escape_string($conn, $_POST['poin']);
        $bobot = floatval($_POST['bobot']);
        $poin2 = mysqli_real_escape_string($conn, $_POST['poin2']);
        $bobot2 = floatval($_POST['bobot2']);

        $sql = "INSERT INTO tb_kpi (id_user, poin, bobot, poin2, bobot2)
                VALUES ($driver_id, '$poin', $bobot, '$poin2', $bobot2)";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: home-adminedp?driver_id=' . $driver_id);
            exit();
        } else {
            echo "<script>alert('Gagal tambah poin KPI!')</script>";
        }
    }
    
    // Update KPI Poin What
    if (isset($_POST['update'])) {
        $driver_id = intval($_POST['driver_id']);
        $poin = mysqli_real_escape_string($conn, $_POST['poin']);
        $bobot = floatval($_POST['bobot']);
        $idk = intval($_POST['idk']);

        $sql = "UPDATE tb_kpi SET poin='$poin', bobot=$bobot WHERE id=$idk AND id_user=$driver_id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: home-adminedp?driver_id=' . $driver_id);
            exit();
        } else {
            echo "<script>alert('Gagal update poin What!')</script>";
        }
    }
    
    // Update KPI Poin How
    if (isset($_POST['update2'])) {
        $driver_id = intval($_POST['driver_id']);
        $poin2 = mysqli_real_escape_string($conn, $_POST['poin2']);
        $bobot2 = floatval($_POST['bobot2']);
        $idk = intval($_POST['idk']);

        $sql = "UPDATE tb_kpi SET poin2='$poin2', bobot2=$bobot2 WHERE id=$idk AND id_user=$driver_id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: home-adminedp?driver_id=' . $driver_id);
            exit();
        } else {
            echo "<script>alert('Gagal update poin How!')</script>";
        }
    }
}
?>
<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/adminedp/p_nav.php"); ?>
        <?php include("pages/part/p_aside_adminedp.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <div class="app-content">
                    <div class="container-fluid">
                        <!-- Alert Informasi Admin EDP -->
                        <div class="alert alert-info alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="bi bi-info-circle-fill me-2"></i>
                                <strong>Dashboard Admin EDP</strong>
                            </h5>
                            <hr>
                            <p class="mb-0">
                                <i class="bi bi-person-badge"></i> Anda sedang mengelola KPI untuk: 
                                <strong><?= $driver_data ? $driver_data['nama_lngkp'] : 'Pilih Driver' ?></strong>
                                <?php if ($driver_data) { ?>
                                    <br>
                                    <small class="text-muted">
                                        NIK: <?= $driver_data['nik'] ?> | 
                                        Jabatan: <?= $driver_data['jabatan'] ?> | 
                                        Bagian: <?= $driver_data['bagian'] ?>
                                    </small>
                                <?php } ?>
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        
                        <div class="row">
                            <?php include("pages/adminedp/p_mainProfile.php"); ?>
                            <?php include("pages/dashboard/p_mainSummary.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
</body>
</html>