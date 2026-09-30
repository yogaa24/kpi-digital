<?php
function getActiveSP($conn, $id_user) {
    $today = date('Y-m-d');
    $sql = "SELECT * FROM tb_surat_peringatan 
            WHERE id_user = ? 
            AND status = 'aktif' 
            AND masa_berlaku_mulai <= ? 
            AND masa_berlaku_selesai >= ?
            ORDER BY 
                CASE jenis_sp 
                    WHEN 'SP3' THEN 1 
                    WHEN 'SP2' THEN 2 
                    WHEN 'SP1' THEN 3 
                END
            LIMIT 1";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "iss", $id_user, $today, $today);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        return $row;
    }
    return null;
}

/**
 * Mendapatkan pengurangan nilai berdasarkan jenis SP
 * @param string $jenis_sp
 * @return int Nilai pengurangan
 */
function getSPPenalty($jenis_sp) {
    $penalties = [
        'SP1' => 2,
        'SP2' => 3.5,
        'SP3' => 5
    ];
    return isset($penalties[$jenis_sp]) ? $penalties[$jenis_sp] : 0;
}

/**
 * Menghitung nilai KPI dengan pengurangan SP
 * @param mysqli $conn
 * @param int $id_user
 * @param float $nilai_asli Nilai KPI asli sebelum pengurangan
 * @return array ['nilai_akhir' => float, 'sp_data' => array|null, 'pengurangan' => int]
 */
function calculateKPIWithSP($conn, $id_user, $nilai_asli) {
    $sp_data = getActiveSP($conn, $id_user);
    
    if ($sp_data) {
        $pengurangan = getSPPenalty($sp_data['jenis_sp']);
        $nilai_akhir = $nilai_asli - $pengurangan;
        
        return [
            'nilai_akhir' => $nilai_akhir,
            'sp_data' => $sp_data,
            'pengurangan' => $pengurangan,
            'nilai_asli' => $nilai_asli
        ];
    }
    
    return [
        'nilai_akhir' => $nilai_asli,
        'sp_data' => null,
        'pengurangan' => 0,
        'nilai_asli' => $nilai_asli
    ];
}

/**
 * Update status SP yang sudah melewati masa berlaku
 * @param mysqli $conn
 */
function updateExpiredSP($conn) {
    $today = date('Y-m-d');
    $sql = "UPDATE tb_surat_peringatan 
            SET status = 'selesai' 
            WHERE status = 'aktif' 
            AND masa_berlaku_selesai < ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $today);
    mysqli_stmt_execute($stmt);
}

/**
 * Mendapatkan badge class berdasarkan jenis SP
 * @param string $jenis_sp
 * @return string Bootstrap badge class
 */
function getSPBadgeClass($jenis_sp) {
    $badges = [
        'SP1' => 'warning',
        'SP2' => 'danger',
        'SP3' => 'dark'
    ];
    return isset($badges[$jenis_sp]) ? $badges[$jenis_sp] : 'secondary';
}

/**
 * Format tanggal Indonesia
 * @param string $date
 * @return string
 */
function formatTanggalIndo($date) {
    if (empty($date) || $date === '0000-00-00') return '-';
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $date);
    if (count($split) !== 3) return $date;
    return (int)$split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

/**
 * Mendapatkan judul resmi SP
 */
function getSPFullTitle($jenis_sp) {
    switch ($jenis_sp) {
        case 'SP1':
            return 'Surat Peringatan Pertama (SP-1)';
        case 'SP2':
            return 'Surat Peringatan Kedua (SP-2)';
        case 'SP3':
            return 'Surat Peringatan Ketiga (SP-3)';
        default:
            return 'Surat Peringatan (' . $jenis_sp . ')';
    }
}

/**
 * Mendapatkan klausul ketentuan sanksi SP
 */
function getSPKetentuanText($jenis_sp) {
    switch ($jenis_sp) {
        case 'SP1':
            return 'Surat Peringatan Pertama (SP-1) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan. Apabila saudara kembali melakukan tindakan pelanggaran dalam kurun waktu 6 (enam) bulan kedepan sejak Surat Peringatan Pertama (SP-1) ini diterbitkan, maka perusahaan akan memberikan sanksi yang lebih tegas kepada saudara.';
        case 'SP2':
            return 'Surat Peringatan Kedua (SP-2) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan. Apabila saudara kembali melakukan tindakan pelanggaran dalam kurun waktu 6 (enam) bulan kedepan sejak Surat Peringatan Kedua (SP-2) ini diterbitkan, maka perusahaan akan memberikan sanksi Surat Peringatan Ketiga (SP-3) atau sanksi yang lebih tegas kepada saudara.';
        case 'SP3':
            return 'Surat Peringatan Ketiga (SP-3) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan. Apabila saudara kembali melakukan tindakan pelanggaran dalam kurun waktu 6 (enam) bulan kedepan sejak Surat Peringatan Ketiga (SP-3) ini diterbitkan, maka perusahaan akan memberikan sanksi pemutusan hubungan kerja (PHK) sesuai ketentuan peraturan perundang-undangan dan peraturan perusahaan yang berlaku.';
        default:
            return 'Surat Peringatan ini berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan.';
    }
}

/**
 * Konversi angka bulan ke angka Romawi
 */
function getRomanMonth($month) {
    $romans = [
        1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI',
        7 => 'VII', 8 => 'VIII', 9 => 'IX', 10 => 'X', 11 => 'XI', 12 => 'XII'
    ];
    $m = (int)$month;
    return $romans[$m] ?? 'I';
}

/**
 * Generate saran nomor SP otomatis berdasarkan urutan & tanggal
 */
function generateNomorSPSuggestion($conn, $tanggal = null) {
    if (!$tanggal) {
        $tanggal = date('Y-m-d');
    }
    $year = date('Y', strtotime($tanggal));
    $month = date('n', strtotime($tanggal));
    $romanMonth = getRomanMonth($month);
    
    // Cari counter tertinggi di tahun ini atau id_sp
    $sql = "SELECT COUNT(*) as total FROM tb_surat_peringatan WHERE YEAR(tanggal_sp) = '$year'";
    $res = mysqli_query($conn, $sql);
    $count = 1;
    if ($res && $row = mysqli_fetch_assoc($res)) {
        $count = (int)$row['total'] + 1;
    }
    
    // Ambil max id juga jika nomor SP lama lebih besar
    $sql_max = "SELECT MAX(id_sp) as max_id FROM tb_surat_peringatan";
    $res_max = mysqli_query($conn, $sql_max);
    if ($res_max && $row_max = mysqli_fetch_assoc($res_max)) {
        $max_id = (int)$row_max['max_id'] + 1;
        if ($max_id > $count) {
            $count = $max_id;
        }
    }
    
    return sprintf("%03d/KIU-HRD/%s/%s", $count, $romanMonth, $year);
}
?>