<?php
require 'config.php';
$id_user = $_SESSION['id_user'];

$sql= "SELECT * FROM tb_kpi WHERE id_user='$id_user'";
$result = mysqli_query($conn, $sql);
$idKPI;
$idUSER;
$poin;
$bobot;
$poin2; 
$bobot2;

$sql2 = "SELECT sum(bobot) FROM tb_kpi WHERE id_user=$id_user";
$result2 = mysqli_query($conn, $sql2);

$totalbobot;
while($row = mysqli_fetch_array($result2)){
    $totalbobot=$row['sum(bobot)'];  
}
?>  