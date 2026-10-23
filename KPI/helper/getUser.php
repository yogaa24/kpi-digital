<?php
require_once __DIR__ . '/config.php';
$id_user = $_SESSION['id_user'];

$sql = "SELECT * FROM tb_users WHERE id='$id_user'";
$result = mysqli_query($conn, $sql);

$username = '';
$nama_lngkp = '';
$nik = '';
$bagian = '';
$departement = '';
$jabatan = '';
$atasan = '';
$penilai = '';
$leveel = null;
while ($hasil = mysqli_fetch_assoc($result)) {
    $username = $hasil['username'];
    $nama_lngkp = $hasil['nama_lngkp'];
    $nik = $hasil['nik'];
    $bagian = $hasil['bagian'];
    $departement = $hasil['departement'];
    $jabatan = $hasil['jabatan'];
    $atasan = $hasil['atasan'];
    $penilai = $hasil['penilai'];
    $leveel = $hasil['level'];
}
if (isset($leveel)) {
    $_SESSION['level'] = $leveel;
}
$nama_lngkp = mysqli_real_escape_string($conn, (string) ($nama_lngkp ?? ''));
?>
