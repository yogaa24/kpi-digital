<?php
// File: helper/checkAdmin.php

/**
 * Cek apakah user adalah Admin HRD (level 5)
 */
function isAdminHRD() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 5;
}

/**
 * Cek apakah user adalah Admin EDP (level 6)
 */
function isAdminEDP() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 6;
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
 * Redirect jika bukan Admin EDP
 */
function requireAdminEDP() {
    if (!isAdminEDP()) {
        header("Location: dashboard");
        exit();
    }
}

/**
 * Get user level name
 */
function getUserLevelName($level) {
    switch($level) {
        case 6:
            return "Admin EDP";
        case 5:
            return "Admin HRD";
        case 4:
            return "Kadep";
        case 3:
            return "Kabag";
        case 2:
            return "Karyawan";
        case 1:
            return "Karyawan";
        default:
            return "Unknown";
    }
}
?>