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

// =========================================================================
// Skill Standard Verified Functions
// =========================================================================
function ensureSSVerifiedTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS `tb_ss_verified` (
        `id_ss_verified` INT NOT NULL AUTO_INCREMENT,
        `id_user` INT NOT NULL,
        `bulan` VARCHAR(10) NOT NULL,
        `verified_by` INT NOT NULL,
        `verified_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `keterangan` TEXT DEFAULT NULL,
        PRIMARY KEY (`id_ss_verified`),
        UNIQUE KEY `unique_ss_user_bulan` (`id_user`, `bulan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    return mysqli_query($conn, $sql);
}

function checkSSVerified($conn, $id_user, $bulan = null) {
    if ($bulan === null) {
        $bulan = date('m/Y');
    }
    ensureSSVerifiedTable($conn);
    $id_user = intval($id_user);
    $bulan_safe = mysqli_real_escape_string($conn, $bulan);
    $sql = "SELECT * FROM tb_ss_verified WHERE id_user = $id_user AND bulan = '$bulan_safe'";
    $result = mysqli_query($conn, $sql);
    if ($result && mysqli_num_rows($result) > 0) {
        return mysqli_fetch_assoc($result);
    }
    return false;
}

function verifySS($conn, $id_user, $verified_by, $keterangan = '', $bulan = null) {
    if ($bulan === null) {
        $bulan = date('m/Y');
    }
    ensureSSVerifiedTable($conn);
    $id_user = intval($id_user);
    $verified_by = intval($verified_by);
    $keterangan_safe = mysqli_real_escape_string($conn, $keterangan);
    $bulan_safe = mysqli_real_escape_string($conn, $bulan);

    $cek = checkSSVerified($conn, $id_user, $bulan);
    if ($cek) {
        $sql = "UPDATE tb_ss_verified SET 
                verified_by = $verified_by, 
                verified_at = NOW(), 
                keterangan = '$keterangan_safe' 
                WHERE id_user = $id_user AND bulan = '$bulan_safe'";
    } else {
        $sql = "INSERT INTO tb_ss_verified (id_user, bulan, verified_by, keterangan) 
                VALUES ($id_user, '$bulan_safe', $verified_by, '$keterangan_safe')";
    }
    return mysqli_query($conn, $sql);
}

function unverifySS($conn, $id_user, $bulan = null) {
    if ($bulan === null) {
        $bulan = date('m/Y');
    }
    ensureSSVerifiedTable($conn);
    $id_user = intval($id_user);
    $bulan_safe = mysqli_real_escape_string($conn, $bulan);
    $sql = "DELETE FROM tb_ss_verified WHERE id_user = $id_user AND bulan = '$bulan_safe'";
    return mysqli_query($conn, $sql);
}
?>