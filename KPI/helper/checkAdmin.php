<?php
// File: helper/checkAdmin.php

/**
 * Cek apakah user adalah Admin HRD (level 7)
 */
function isAdminHRD() {
    global $leveel, $username;
    $level = intval($_SESSION['level'] ?? ($leveel ?? 0));
    $user = strtolower($username ?? ($_SESSION['username'] ?? ''));
    return $level == 7 || $user === 'adminhrd';
}

/**
 * Cek apakah user adalah Kadep (level 4)
 */
function isKadepOrHigher() {
    global $leveel;
    $level = intval($_SESSION['level'] ?? ($leveel ?? 0));
    return $level >= 4;
}

/**
 * Cek apakah user adalah Kadep MT atau lebih tinggi (level >= 3)
 */
function isKadepMTOrHigher() {
    global $leveel;
    $level = intval($_SESSION['level'] ?? ($leveel ?? 0));
    return $level >= 4;
}

/**
 * Cek apakah user adalah Kabag atau lebih tinggi (level >= 2)
 */
function isKabagOrHigher() {
    global $leveel;
    $level = intval($_SESSION['level'] ?? ($leveel ?? 0));
    return $level >= 3;
}

function isKoorOrHigher() {
    global $leveel;
    $level = intval($_SESSION['level'] ?? ($leveel ?? 0));
    return $level >= 2;
}

/**
 * Redirect jika bukan Admin HRD
 */
function requireAdminHRD() {
    if (!isAdminHRD()) {
        header("Location: home-kpi-real");
        exit();
    }
}

/**
 * Cek apakah user adalah Direktur / Wadir Utama / ID 1 (Diana Wulandari)
 */
function isDirekturOrHigher() {
    global $leveel, $jabatan, $nama_lngkp;
    $level = intval($_SESSION['level'] ?? ($leveel ?? 0));
    $id = intval($_SESSION['id_user'] ?? 0);
    $user_jabatan = strtolower($jabatan ?? '');
    $user_nama = strtolower($nama_lngkp ?? '');

    // Level >= 5 (Direktur/Wadir), atau ID 1, atau jabatan Direktur/Wadir, atau nama Diana Wulandari
    if ($level >= 5 || $id == 1 || strpos($user_jabatan, 'direktur') !== false || strpos($user_jabatan, 'wadir') !== false || strpos($user_nama, 'diana wulandari') !== false) {
        return true;
    }

    return false;
}

/**
 * Cek apakah user berhak melihat data seluruh karyawan (Admin HRD atau Direktur)
 */
function canViewAllEmployees() {
    return isAdminHRD() || isDirekturOrHigher();
}

/**
 * Redirect jika bukan Admin HRD atau Direktur
 */
function requireAdminHRDOrDirektur() {
    if (!canViewAllEmployees()) {
        header("Location: home-kpi-real");
        exit();
    }
}

/**
 * Get user level name
 */
function getUserLevelName($level) {
    switch($level) {
        case 7:
            return "Admin HRD";
        case 6:
            return "Wadir Utama";
        case 5:
            return "Direktur";
        case 4:
            return "Kadep";
        case 3:
            return "Manager";
        case 2:
            return "Koordinator";
        case 1:
            return "Karyawan";
        default:
            return "Unknown";
    }
}
?>
