<?php
// File: helper/checkAdmin.php

/**
 * Cek apakah user adalah Admin HRD (level 5)
 */
function isAdminHRD() {
    return isset($_SESSION['level']) && $_SESSION['level'] == 6;
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
            return "Admin HRD";
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
