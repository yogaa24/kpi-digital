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
    require 'helper/sp_functions.php';

    updateExpiredSP($conn);

    $user_id = isset($id_sf) ? $id_sf : $id_user;

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
        $bulannnn = '';

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
    

    if (isset($_POST['archiveNow'])) {
        $forIdArc=0;
        $odkgh = $busd[0]-1;
        
        // Perbaikan: jika bulan jadi 0, ubah ke 12 dan tahun mundur 1
        if ($odkgh == 0) {
            $odkgh = 12;
            $tahunArchive = $busd[1] - 1;
        } else {
            $tahunArchive = $busd[1];
        }
        
        $tgslk = $odkgh.'/'.$tahunArchive;
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

            $panggilHow = mysqli_query($conn,"SELECT * FROM tb_hows WHERE id_user = $id_user AND id_kpi = ".$ppPoin['id']);
            while($howPoin = mysqli_fetch_assoc($panggilHow)){
                $addHow = mysqli_query($connarc,"INSERT INTO tbar_hows (id_user,id_kpi,tipe_how,p_how,bobot,target_omset,hasil,nilai,total) VALUES ($id_user,$last_poin,'".$howPoin['tipe_how']."','".$howPoin['p_how']."','".$howPoin['bobot']."','".$howPoin['target_omset']."','".$howPoin['hasil']."','".$howPoin['nilai']."','".$howPoin['total']."')");
            }

            $panggilWhat = mysqli_query($conn,"SELECT * FROM tb_whats WHERE id_user = $id_user AND id_kpi = ".$ppPoin['id']);
            while($whatPoin = mysqli_fetch_assoc($panggilWhat)){
                $addWhat = mysqli_query($connarc,"INSERT INTO tbar_whats (id_user,id_kpi,tipe_what,p_what,bobot,target_omset,hasil,nilai,total) VALUES ($id_user,$last_poin,'".$whatPoin['tipe_what']."','".$whatPoin['p_what']."','".$whatPoin['bobot']."','".$whatPoin['target_omset']."','".$whatPoin['hasil']."','".$whatPoin['nilai']."','".$whatPoin['total']."')");
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
        <?php include("pages/dashboard/p_nav.php"); ?>
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