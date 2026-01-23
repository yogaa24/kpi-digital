<?php

$id_user = $_SESSION['id_user'];

// Cek apakah $idar sudah didefinisikan (dari archivepoin.php yang baru)
// Jika belum, ambil dari parameter URL
if (!isset($idar)) {
    $blan_param = $_GET['idarc'];
    $query_archive = mysqli_query($conn, "SELECT id_archive FROM tbar_archive WHERE bulan = '$blan_param' AND id_user = $id_user");
    
    if (mysqli_num_rows($query_archive) > 0) {
        $row_archive = mysqli_fetch_assoc($query_archive);
        $idar = $row_archive['id_archive'];
    } else {
        $idar = 0; // Default jika tidak ditemukan
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