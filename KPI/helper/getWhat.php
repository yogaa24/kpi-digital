<?php
require 'config.php';
$id_user = $_SESSION['id_user'];

$sql = "SELECT * FROM tb_whats WHERE id_user='$id_user'and id_kpi='$idKPI'";
$result = mysqli_query($conn, $sql);

$idwhat;
$ids;
$idkpi;
$tujuan;
$bobot;
$nilai;
$indikatorwhat;


?>