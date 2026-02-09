<?php
$server = "https://hrd.karismaonline.id/kpi/";
$user = "u471548307_adminkarisma";
$pass = "?BQlb>[>6";
$database = "u471548307_karismaerp";
$conn = mysqli_connect($server, $user, $pass, $database);
if (!$conn) {
    header("Location: error");
    die("Koneksi ke database gagal: " . mysqli_connect_error());
    
}


?>