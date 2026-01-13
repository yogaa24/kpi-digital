<?php
require 'config.php';

$iduser = $_SESSION['id_user'];

$sqlevis= "SELECT *
FROM tb_users
WHERE atasan = '$nama_lngkp' OR nama_lngkp = '$nama_lngkp'
ORDER BY 
    CASE 
    	WHEN jabatan = 'Kadep' THEN 1
        WHEN jabatan = 'Manager' THEN 2
        WHEN jabatan = 'Karyawan' THEN 3
        ELSE 4
    END,
    nama_lngkp";
$resultevidenangg = mysqli_query($conn, $sqlevis);


?>  