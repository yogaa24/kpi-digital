<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/configarchive.php';
    require 'helper/getUser.php';
    require 'helper/getKPI.php';
    require 'helper/getHow.php';

    $zboth = 0;
    $zbotw = 0;
    
    $blan = '';

    $totalws = 0;
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
    // ===============================================================================
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
    $blan = date('m/Y');
    $busd = explode('/', $blan);
    function tmapil($bl,$th){

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
    

    if (isset($_POST['archiveNow'])) {
        $forIdArc=0;
        $odkgh = $busd[0]-1;
        $tgslk = $odkgh.'/'.$busd[1];
        $sqlksf = "INSERT INTO tbar_archive (bulan, id_user) VALUES ('$tgslk',$id_user)";
        $resuarc = mysqli_query($connarc, $sqlksf);
        $last_id = mysqli_insert_id($connarc);

        $resultrtyhj = mysqli_query($connarc, "SELECT * FROM tbar_archive WHERE id_archive = $last_id and id_user = $id_user");
        $rosagw = mysqli_fetch_assoc($resultrtyhj);
        $idarcv = $rosagw['id_archive'];

        $panggilPoin = mysqli_query($conn,"Select * from tb_kpi where id_user = $id_user");
        while($ppPoin = mysqli_fetch_assoc($panggilPoin)){
            $addPoin = mysqli_query($connarc,"INSERT INTO tbar_kpi (id_user,id_arcv,poin,bobot,poin2,bobot2) values ($id_user, $idarcv ,'".$ppPoin['poin']."', '".$ppPoin['bobot']."','".$ppPoin['poin2']."','".$ppPoin['bobot2']."')");
            $last_poin = mysqli_insert_id($connarc);

            $panggilHow = mysqli_query($conn,"Select * from tb_hows where id_user = $id_user and id_kpi = ".$ppPoin['id']);
            while($howPoin = mysqli_fetch_assoc($panggilHow)){
                $addHow = mysqli_query($connarc,"INSERT INTO tbar_hows (id_user, id_kpi, p_how, bobot, hasil, nilai, total, indikatorhow) values ($id_user, $last_poin, '".$howPoin['p_how']."','".$howPoin['bobot']."','".$howPoin['hasil']."','".$howPoin['nilai']."','".$howPoin['total']."','".$howPoin['indikatorhow']."')");
            }

            $panggilWhat = mysqli_query($conn,"Select * from tb_whats where id_user = $id_user and id_kpi = ".$ppPoin['id']);
            while($whatPoin = mysqli_fetch_assoc($panggilWhat)){
                $addWhat = mysqli_query($connarc,"INSERT INTO tbar_whats (id_user, id_kpi, p_what, bobot, hasil, nilai, total, indikatorwhat) values ($id_user, $last_poin, '".$whatPoin['p_what']."','".$whatPoin['bobot']."','".$whatPoin['hasil']."','".$whatPoin['nilai']."','".$whatPoin['total']."','".$whatPoin['indikatorwhat']."')");
            }
        }
        $panggilbobot = mysqli_query($conn,"Select * from tb_bobotkpi where id_user = $id_user");
        while($bobotPoin = mysqli_fetch_assoc($panggilbobot)){
            $addbobot = mysqli_query($connarc,"INSERT INTO tbar_bobotkpi (id_user, id_arcv, bobotwhat, bobothow) values ($id_user,$idarcv, ".$bobotPoin['bobotwhat'].",".$bobotPoin['bobothow'].")");
        }
    }
}

?>
<html lang="en">

<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/s_nav.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="mt-3">
                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <?php include("pages/dashboard/p_mainProfile.php"); ?>
                            <?php include("pages/dashboard/p_mainSummary.php"); ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
        
</body>

</html>