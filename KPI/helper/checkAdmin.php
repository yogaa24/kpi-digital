<?php
// File: helper/checkAdmin.php

/**
 * Cek apakah user adalah Admin HRD (level 5)
 */
function isAdminHRD() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 7;
}

/**
 * Cek apakah user adalah Kadep (level 4)
 */
function isKadepOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 5;
}

/**
 * Cek apakah user adalah Kadep MT atau lebih tinggi (level >= 3)
 */
function isKadepMTOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] >= 4;
}

/**
 * Cek apakah user adalah Kabag atau lebih tinggi (level >= 2)
 */
function isKabagOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] >= 3;
}

function isKoorOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] >= 2;
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
    $level = intval($_SESSION['level'] ?? 0);
    $id = intval($_SESSION['id_user'] ?? 0);
    return ($level >= 5 || $id == 1);
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
