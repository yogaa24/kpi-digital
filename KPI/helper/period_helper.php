<?php
// helper/period_helper.php
// Centralized Global Date & Period Setting Helper

date_default_timezone_set('Asia/Jakarta');

if (!defined('GLOBAL_MONTH_OFFSET')) {
    // Setting offset bulan global (Default: -1 bulan dari bulan berjalan)
    define('GLOBAL_MONTH_OFFSET', -1);
}

/**
 * Mendapatkan DateTime objek setelah disesuaikan dengan GLOBAL_MONTH_OFFSET.
 * @param string $time_str (opsional, default 'now')
 * @return DateTime
 */
if (!function_exists('getGlobalPeriodDate')) {
    function getGlobalPeriodDate($time_str = 'now')
    {
        $dt = new DateTime($time_str);
        $dt->modify('first day of this month');
        if (GLOBAL_MONTH_OFFSET !== 0) {
            $dt->modify(GLOBAL_MONTH_OFFSET . ' month');
        }
        return $dt;
    }
}

/**
 * Mendapatkan info bulan dan tahun operasional (Default: Bulan Ini versi sistem/mundur -1).
 * Return format array: ['month' => int, 'year' => int, 'formatted_my' => 'm/Y', 'formatted_ym' => 'Y-m']
 */
if (!function_exists('getAppCurrentPeriod')) {
    function getAppCurrentPeriod($offset_months = 0)
    {
        $dt = getGlobalPeriodDate();
        if ($offset_months !== 0) {
            $dt->modify(($offset_months > 0 ? "+$offset_months" : "$offset_months") . ' month');
        }
        return [
            'month' => intval($dt->format('n')),
            'year'  => intval($dt->format('Y')),
            'formatted_my' => $dt->format('m/Y'),
            'formatted_ym' => $dt->format('Y-m')
        ];
    }
}

/**
 * Pengganti fungsi getPreviousMonth() global
 * Mendapatkan bulan dan tahun operasional (Bulan Ini versi sistem)
 */
if (!function_exists('getPreviousMonth')) {
    function getPreviousMonth()
    {
        $period = getAppCurrentPeriod(0);
        return [
            'month' => $period['month'],
            'year'  => $period['year']
        ];
    }
}

/**
 * Mendapatkan bulan lalu dari bulan operasional (Mundur 1 bulan lagi dari bulan operasional)
 */
if (!function_exists('getAppPreviousPeriod')) {
    function getAppPreviousPeriod()
    {
        return getAppCurrentPeriod(-1);
    }
}

/**
 * Helper nama bulan singkat
 */
if (!function_exists('getNamaBulan')) {
    function getNamaBulan($bulan)
    {
        $namaBulan = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];
        return $namaBulan[intval($bulan)] ?? '';
    }
}
?>
