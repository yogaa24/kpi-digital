<?php
// helper/verified_functions.php

function checkKPIVerified($conn, $id_user, $bulan = null) {
    if ($bulan === null) {
        $bulan = date('m/Y');
    }
    
    $sql = "SELECT * FROM tb_kpi_verified WHERE id_user = $id_user AND bulan = '$bulan'";
    $result = mysqli_query($conn, $sql);
    
    if (mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

function verifyKPI($conn, $id_user, $verified_by, $keterangan = '', $bulan = null) {
    if ($bulan === null) {
        $bulan = date('m/Y');
    }
    
    // Cek apakah sudah pernah diverified
    $cek = checkKPIVerified($conn, $id_user, $bulan);
    
    if ($cek) {
        // Update jika sudah ada
        $sql = "UPDATE tb_kpi_verified SET 
                verified_by = $verified_by, 
                verified_at = NOW(), 
                keterangan = '".mysqli_real_escape_string($conn, $keterangan)."' 
                WHERE id_user = $id_user AND bulan = '$bulan'";
    } else {
        // Insert baru
        $sql = "INSERT INTO tb_kpi_verified (id_user, bulan, verified_by, keterangan) 
                VALUES ($id_user, '$bulan', $verified_by, '".mysqli_real_escape_string($conn, $keterangan)."')";
    }
    
    return mysqli_query($conn, $sql);
}

function unverifyKPI($conn, $id_user, $bulan = null) {
    if ($bulan === null) {
        $bulan = date('m/Y');
    }
    
    $sql = "DELETE FROM tb_kpi_verified WHERE id_user = $id_user AND bulan = '$bulan'";
    return mysqli_query($conn, $sql);
}

function getVerifierName($conn, $verified_by) {
    $sql = "SELECT nama_lngkp FROM tb_users WHERE id = $verified_by";
    $result = mysqli_query($conn, $sql);
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['nama_lngkp'];
    }
    return 'Unknown';
}
?>