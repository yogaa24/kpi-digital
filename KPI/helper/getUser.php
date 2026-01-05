<?php
require 'config.php';
$id_user = $_SESSION['id_user'];

$sql = "SELECT * FROM tb_users WHERE id='$id_user'";
$result = mysqli_query($conn, $sql);

$sqls = "SELECT * FROM tb_auth WHERE id_user='$id_user'";
$resultso = mysqli_query($conn, $sqls);

$username;
$nama_lngkp;
$nik;
$departement;
$jabatan;
$atasan;
$penilai;
$leveel;
while ($hasilsf = mysqli_fetch_assoc($resultso)) {
    $leveel = $hasilsf['level'];
}
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
$nama_lngkp = mysqli_real_escape_string($conn, $nama_lngkp);
?>