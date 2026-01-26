<?php

$id_user = $_SESSION['id_user'];

// Cek apakah $idar sudah didefinisikan (dari archivepoin.php)
if (!isset($idar)) {
    $idarc_param = isset($_GET['idarc']) ? $_GET['idarc'] : '';
    
    // Cek apakah parameter berisi bulan (format: XX/YYYY) atau id_archive (angka)
    if (strpos($idarc_param, '/') !== false) {
        // Format bulan (dari archivepoin.php)
        $query_archive = mysqli_query($conn, "SELECT id_archive FROM tbar_archive WHERE bulan = '$idarc_param' AND id_user = $id_user");
        
        if (mysqli_num_rows($query_archive) > 0) {
            $row_archive = mysqli_fetch_assoc($query_archive);
            $idar = $row_archive['id_archive'];
        } else {
            $idar = 0;
        }
    } else {
        // Format id_archive langsung (dari archivedetail.php)
        $idar = intval($idarc_param);
        
        // Verifikasi apakah id_archive valid dan milik user ini
        $query_verify = mysqli_query($conn, "SELECT id_archive FROM tbar_archive WHERE id_archive = $idar AND id_user = $id_user");
        if (mysqli_num_rows($query_verify) == 0) {
            $idar = 0;
        }
    }
}

// Gunakan id_arcv untuk query KPI
$sql = "SELECT * FROM tbar_kpi WHERE id_user = $id_user AND id_arcv = $idar";
$result = mysqli_query($conn, $sql);

$idKPI;
$idUSER;
$poin;
$bobot;
$poin2;
$bobot2;

$sql2 = "SELECT sum(bobot) FROM tbar_kpi WHERE id_user=$id_user AND id_arcv = $idar";
$result2 = mysqli_query($conn, $sql2);

?>