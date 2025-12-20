<?php
$server = "localhost";
$user = "root";
$pass = "";
$database = "db_kpi";
$conn = mysqli_connect($server, $user, $pass, $database);
if (!$conn) {
    header("Location: error");
    die("Koneksi ke database gagal: " . mysqli_connect_error());
    
}


?>