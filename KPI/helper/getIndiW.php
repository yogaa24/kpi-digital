<?php
require 'config.php';
$id_user = $_SESSION['id_user'];

$sql = "SELECT * FROM tb_indikatorwhats WHERE id_user='$id_user' AND id_kpi='$idKPI' AND id_what='$idwhatind'";
$result = mysqli_query($conn, $sql);

$username;
$nama_lngkp;
$nik;
$departement;
$jabatan;
$atasan;
$penilai;

while ($hasil = mysqli_fetch_assoc($result)) {
    $username = $hasil['username'];
    $nama_lngkp = $hasil['nama_lngkp'];
    $nik = $hasil['nik'];
    $bagian = $hasil['bagian'];
    $departement = $hasil['departement'];
    $jabatan = $hasil['jabatan'];
    $atasan = $hasil['atasan'];
    $penilai = $hasil['penilai'];
}
?>
