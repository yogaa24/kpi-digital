<?php
// File: helper/checkAdmin.php

/**
 * Cek apakah user adalah Admin HRD (level 5)
 */
function isAdminHRD() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 5;
}

/**
 * Cek apakah user adalah Kadep (level 4)
 */
function isKadepOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 4;
}

/**
 * Cek apakah user adalah Kadep MT atau lebih tinggi (level >= 3)
 */
function isKadepMTOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] >= 3;
}

/**
 * Cek apakah user adalah Kabag atau lebih tinggi (level >= 2)
 */
function isKabagOrHigher() {
    return isset($_SESSION['level']) && $_SESSION['level'] >= 2;
}

/**
 * Redirect jika bukan Admin HRD
 */
function requireAdminHRD() {
    if (!isAdminHRD()) {
        header("Location: dashboard");
        exit();
    }
}

/**
 * Get user level name
 */
function getUserLevelName($level) {
    switch($level) {
        case 4:
            return "Admin HRD";
        case 3:
            return "Kadep";
        case 2:
            return "Kabag";
        case 1:
            return "Karyawan";
        default:
            return "Unknown";
    }
}
?>
