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
        'SP2' => 3,
        'SP3' => 4
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
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $split = explode('-', $date);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}
?>