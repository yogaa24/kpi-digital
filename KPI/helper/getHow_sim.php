<?php
require 'config.php';
$id_user = $_SESSION['id_user'];

$sql10 = "SELECT * FROM tbsim_hows WHERE id_user='$id_user'";
$result = mysqli_query($conn, $sql);

$idhow;
$idsh;
$idkpih;
$tujuanh;
$boboth;
$nilaih;
$indikatorh;


?>