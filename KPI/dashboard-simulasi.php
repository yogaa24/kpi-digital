<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getKPI_sim.php';
    require 'helper/getHow_sim.php';

    $zboth = 0;
    $zbotw = 0;

    $blan = '';

    $totalws = 0;
    $result = mysqli_query($conn, $sql);
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $poin = $hasils['poin'];

        $sql3s = "SELECT SUM(total) as total FROM tbsim_whats WHERE id_user=$id_user AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tbsim_bobotkpi WHERE id_user=$id_user";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;
    // ===============================================================================
    $totalhfg = 0;
    $totalbobothfg = 0;
    $resultfg = mysqli_query($conn, $sql);

    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $poin2fg = $hasilfg['poin2'];

        $sql7fg = "SELECT SUM(total) as totalh FROM tbsim_hows WHERE id_user=$id_user AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];

        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tbsim_bobotkpi WHERE id_user=$id_user";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;

    if (isset($_POST['updateWhatB'])) {
        $bwasfg = $_POST['bobot'];
        $idfj = $_POST['idU'];
        $sql = "UPDATE `tbsim_bobotkpi` SET bobotwhat =" . $bwasfg . " where id_user =" . $idfj;
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
        $sql = "UPDATE `tbsim_bobotkpi` SET bobothow =" . $bwasfg . " where id_user =" . $idfj;
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Woops! Gagal update.')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }
    $blan = date('m/Y');
    $busd = explode('/', $blan);
    function tmapil($bl, $th)
    {

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
}

?>
<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/s_nav_sim.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php include("pages/dashboard/p_karyawanSim.php"); ?>
                            <?php include("pages/dashboard/p_mainSummarysim.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>

</body>

</html>