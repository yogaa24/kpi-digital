<?php
$serverarc = "localhost";
$userarc = "root";
$passarc = "";
$databasearc = "db_kpiarchive";
$connarc = mysqli_connect($serverarc, $userarc, $passarc, $databasearc);
if (!$connarc) {
    header("Location: error");
    die("Koneksi ke database gagal: " . mysqli_connect_error());
    
}
?>