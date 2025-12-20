<?php

$id_user = $_SESSION['id_user'];
$idar = $_GET['idarc'];

$sql= "SELECT tbar_kpi.* FROM tbar_kpi INNER JOIN tbar_archive ON tbar_archive.id_archive = tbar_kpi.id_arcv WHERE tbar_archive.bulan = '$idar' AND tbar_archive.id_user = $id_user";
$result = mysqli_query($connarc, $sql);
$idKPI;
$idUSER;
$poin;
$bobot;
$poin2;
$bobot2;

$sql2 = "SELECT sum(bobot) FROM tbar_kpi WHERE id_user=$id_user AND bulan = '$idar'";
$result2 = mysqli_query($connarc, $sql2);


?>