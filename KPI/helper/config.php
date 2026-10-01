<?php
// $server = "https://hrd.karismaonline.id/kpi/";
// $user = "u471548307_adminkarisma";
// $pass = "?BQlb>[>6";
$server = "localhost";
$user = "root";
$pass = "";
$database = "kiucoid_karismaerp_local";
// $database = "db_kpi";
$conn = mysqli_connect($server, $user, $pass, $database);
if (!$conn) {
    header("Location: error");
    die("Koneksi ke database gagal: " . mysqli_connect_error());
    
}

require_once __DIR__ . '/period_helper.php';
require_once __DIR__ . '/karakter_helper.php';

?>