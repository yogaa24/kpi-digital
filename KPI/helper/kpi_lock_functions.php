<?php
// File: helper/kpi_lock_functions.php

/**
 * Mendapatkan level user berdasarkan jabatan
 */
function getUserLevel($conn, $jabatan) {
    $sql = "SELECT level FROM tb_user_level_mapping WHERE jabatan = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $jabatan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row['level'];
    }
    
    // Default: jika tidak ada mapping, anggap level 1
    return 1;
}

/**
 * Cek apakah user boleh mengakses KPI pada tanggal tertentu
 */
function checkKPIAccess($conn, $user_level, $tanggal = null, $action = 'view') {
    // Admin HRD (level 5) selalu punya akses penuh
    if ($user_level >= 5) {
        return true;
    }
    
    if ($tanggal === null) {
        $tanggal = date('Y-m-d');
    }
    
    // Cari pengaturan yang aktif untuk tanggal tersebut
    $sql = "SELECT level_akses, izin_akses 
            FROM tb_kpi_lock_settings 
            WHERE status = 'aktif' 
            AND tanggal_mulai <= ? 
            AND tanggal_selesai >= ?
            ORDER BY created_at DESC
            LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $tanggal, $tanggal);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $level_akses = explode(',', $row['level_akses']);
        $izin_akses = json_decode($row['izin_akses'], true);
        
        // Cek apakah level user termasuk dalam level yang diizinkan
        if (empty($row['level_akses'])) {
            // Jika level_akses kosong, berarti semua level tidak boleh kecuali view
            return ($action === 'view' && isset($izin_akses['view']) && $izin_akses['view']);
        }
        
        if (in_array($user_level, $level_akses)) {
            // Cek izin untuk action tertentu
            return isset($izin_akses[$action]) && $izin_akses[$action];
        }
        
        // Level tidak termasuk yang diizinkan, hanya boleh view jika diperbolehkan
        return ($action === 'view' && isset($izin_akses['view']) && $izin_akses['view']);
    }
    
    // Jika tidak ada pengaturan, default: semua boleh akses penuh
    return true;
}

/**
 * Mendapatkan pesan lock untuk ditampilkan ke user
 */
function getKPILockMessage($conn, $user_level, $tanggal = null) {
    if ($tanggal === null) {
        $tanggal = date('Y-m-d');
    }
    
    $sql = "SELECT nama_periode, tanggal_mulai, tanggal_selesai, level_akses, keterangan 
            FROM tb_kpi_lock_settings 
            WHERE status = 'aktif' 
            AND tanggal_mulai <= ? 
            AND tanggal_selesai >= ?
            ORDER BY created_at DESC
            LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $tanggal, $tanggal);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        $level_akses = explode(',', $row['level_akses']);
        
        if (!in_array($user_level, $level_akses) && !empty($row['level_akses'])) {
            return [
                'locked' => true,
                'message' => "Akses KPI dibatasi pada periode ini ({$row['nama_periode']}). " . 
                            "Hanya level " . implode(', ', $level_akses) . " yang dapat mengakses. " .
                            "Periode: " . date('d/m/Y', strtotime($row['tanggal_mulai'])) . 
                            " - " . date('d/m/Y', strtotime($row['tanggal_selesai'])),
                'keterangan' => $row['keterangan']
            ];
        } elseif (empty($row['level_akses'])) {
            return [
                'locked' => true,
                'message' => "Periode input KPI telah ditutup ({$row['nama_periode']}). " .
                            "Periode: " . date('d/m/Y', strtotime($row['tanggal_mulai'])) . 
                            " - " . date('d/m/Y', strtotime($row['tanggal_selesai'])),
                'keterangan' => $row['keterangan']
            ];
        }
    }
    
    return ['locked' => false, 'message' => '', 'keterangan' => ''];
}

/**
 * Mendapatkan semua periode lock yang aktif
 */
function getAllActiveLockPeriods($conn) {
    $sql = "SELECT * FROM tb_kpi_lock_settings WHERE status = 'aktif' ORDER BY tanggal_mulai ASC";
    $result = mysqli_query($conn, $sql);
    return $result;
}

/**
 * Cek apakah ada overlap periode
 */
function checkPeriodOverlap($conn, $tanggal_mulai, $tanggal_selesai, $exclude_id = null) {
    $sql = "SELECT COUNT(*) as count FROM tb_kpi_lock_settings 
            WHERE status = 'aktif' 
            AND (
                (tanggal_mulai <= ? AND tanggal_selesai >= ?) OR
                (tanggal_mulai <= ? AND tanggal_selesai >= ?) OR
                (tanggal_mulai >= ? AND tanggal_selesai <= ?)
            )";
    
    if ($exclude_id) {
        $sql .= " AND id_lock != ?";
    }
    
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($exclude_id) {
        mysqli_stmt_bind_param($stmt, "sssssssi", 
            $tanggal_mulai, $tanggal_mulai,
            $tanggal_selesai, $tanggal_selesai,
            $tanggal_mulai, $tanggal_selesai,
            $exclude_id
        );
    } else {
        mysqli_stmt_bind_param($stmt, "ssssss", 
            $tanggal_mulai, $tanggal_mulai,
            $tanggal_selesai, $tanggal_selesai,
            $tanggal_mulai, $tanggal_selesai
        );
    }
    
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);
    
    return $row['count'] > 0;
}
?>