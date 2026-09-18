<?php
// helper/ss_archive_functions.php

/**
 * Memastikan tabel arsip Skill Standard tersedia di database
 */
function ensureSSArchiveTables($conn) {
    // 1. Header Arsip SS
    $sql1 = "CREATE TABLE IF NOT EXISTS `tbar_ss_archive` (
        `id_ss_archive` INT NOT NULL AUTO_INCREMENT,
        `bulan` VARCHAR(10) NOT NULL,
        `id_user` INT NOT NULL,
        `rata_rata_umum` DECIMAL(5,2) DEFAULT 0.00,
        `rata_rata_teknis` DECIMAL(5,2) DEFAULT 0.00,
        `rata_rata_total` DECIMAL(5,2) DEFAULT 0.00,
        `status` TINYINT(1) DEFAULT 1,
        `verified_by` INT DEFAULT NULL,
        `verified_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `keterangan` TEXT DEFAULT NULL,
        PRIMARY KEY (`id_ss_archive`),
        UNIQUE KEY `unique_user_bulan_ss` (`id_user`, `bulan`),
        KEY `idx_ss_archive_user` (`id_user`),
        KEY `idx_ss_archive_bulan` (`bulan`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $sql1);

    // 2. Kategori Arsip SS
    $sql2 = "CREATE TABLE IF NOT EXISTS `tbar_ss` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `id_ss_archive` INT NOT NULL,
        `id_user` INT NOT NULL,
        `id_poinss_ref` INT DEFAULT NULL,
        `poin_ss` VARCHAR(255) NOT NULL,
        `tipe_ss` ENUM('umum', 'teknis') NOT NULL DEFAULT 'umum',
        PRIMARY KEY (`id`),
        KEY `idx_tbar_ss_archive` (`id_ss_archive`),
        KEY `idx_tbar_ss_user` (`id_user`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $sql2);

    // 3. Poin / Indikator Arsip SS
    $sql3 = "CREATE TABLE IF NOT EXISTS `tbar_sspoin` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `id_tbar_ss` INT NOT NULL,
        `id_user` INT NOT NULL,
        `id_sspoin_ref` INT DEFAULT NULL,
        `poinss` TEXT NOT NULL,
        `nilai1` TEXT DEFAULT NULL,
        `nilai2` TEXT DEFAULT NULL,
        `nilai3` TEXT DEFAULT NULL,
        `nilai4` TEXT DEFAULT NULL,
        `nilaiss` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
        `deskripsi` TEXT DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `idx_tbar_sspoin_ss` (`id_tbar_ss`),
        KEY `idx_tbar_sspoin_user` (`id_user`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    mysqli_query($conn, $sql3);

    return true;
}

/**
 * Menghitung periode bulan arsip Skill Standard (bulan sebelumnya: m/Y, sama seperti KPI Real)
 */
function getSSArchiveTargetMonth() {
    $blan_now = date('m/Y');
    $busd_now = explode('/', $blan_now);
    $odkgh = (int)$busd_now[0] - 1;
    $tahunArchive = (int)$busd_now[1];

    if ($odkgh == 0) {
        $odkgh = 12;
        $tahunArchive = $tahunArchive - 1;
    }

    return str_pad($odkgh, 2, '0', STR_PAD_LEFT) . '/' . $tahunArchive;
}

/**
 * Melakukan auto-archive snapshot Skill Standard saat atasan melakukan verifikasi
 */
function autoArchiveSS($conn, $id_user, $verified_by, $keterangan = '', $target_bulan = null) {
    ensureSSArchiveTables($conn);

    $id_user = intval($id_user);
    $verified_by = intval($verified_by);
    if ($target_bulan === null) {
        $target_bulan = getSSArchiveTargetMonth();
    }
    $target_bulan_safe = mysqli_real_escape_string($conn, $target_bulan);
    $keterangan_safe = mysqli_real_escape_string($conn, $keterangan);

    // Hitung rata-rata kategori Umum
    $sql_umum = "SELECT sp.nilaiss 
                 FROM tb_sspoin sp 
                 INNER JOIN tb_ss s ON s.id_poinss = sp.id_ss 
                 WHERE sp.id_user = $id_user AND s.tipe_ss = 'umum'";
    $res_umum = mysqli_query($conn, $sql_umum);
    $tot_umum = 0; $cnt_umum = 0;
    if ($res_umum) {
        while ($r = mysqli_fetch_assoc($res_umum)) {
            $tot_umum += (float)$r['nilaiss'];
            $cnt_umum++;
        }
    }
    $avg_umum = ($cnt_umum > 0) ? round($tot_umum / $cnt_umum, 2) : 0.00;

    // Hitung rata-rata kategori Teknis
    $sql_teknis = "SELECT sp.nilaiss 
                   FROM tb_sspoin sp 
                   INNER JOIN tb_ss s ON s.id_poinss = sp.id_ss 
                   WHERE sp.id_user = $id_user AND s.tipe_ss = 'teknis'";
    $res_teknis = mysqli_query($conn, $sql_teknis);
    $tot_teknis = 0; $cnt_teknis = 0;
    if ($res_teknis) {
        while ($r = mysqli_fetch_assoc($res_teknis)) {
            $tot_teknis += (float)$r['nilaiss'];
            $cnt_teknis++;
        }
    }
    $avg_teknis = ($cnt_teknis > 0) ? round($tot_teknis / $cnt_teknis, 2) : 0.00;

    // Rata-rata keseluruhan
    $tot_all = $tot_umum + $tot_teknis;
    $cnt_all = $cnt_umum + $cnt_teknis;
    $avg_total = ($cnt_all > 0) ? round($tot_all / $cnt_all, 2) : 0.00;

    // Cek apakah arsip untuk user & bulan ini sudah pernah dibuat
    $cek_archive = mysqli_query($conn, "SELECT id_ss_archive FROM tbar_ss_archive WHERE id_user = $id_user AND bulan = '$target_bulan_safe'");
    if ($cek_archive && mysqli_num_rows($cek_archive) > 0) {
        $row_arch = mysqli_fetch_assoc($cek_archive);
        $id_ss_archive = intval($row_arch['id_ss_archive']);

        // Update header archive (set status = 1 terverifikasi)
        $update_header = "UPDATE tbar_ss_archive SET 
                            rata_rata_umum = $avg_umum,
                            rata_rata_teknis = $avg_teknis,
                            rata_rata_total = $avg_total,
                            status = 1,
                            verified_by = $verified_by,
                            verified_at = NOW(),
                            keterangan = '$keterangan_safe'
                          WHERE id_ss_archive = $id_ss_archive";
        mysqli_query($conn, $update_header);

        // Hapus detail lama agar disinkronkan dengan data terkini saat diverifikasi
        mysqli_query($conn, "DELETE FROM tbar_sspoin WHERE id_tbar_ss IN (SELECT id FROM tbar_ss WHERE id_ss_archive = $id_ss_archive)");
        mysqli_query($conn, "DELETE FROM tbar_ss WHERE id_ss_archive = $id_ss_archive");
    } else {
        // Insert header baru
        $insert_header = "INSERT INTO tbar_ss_archive 
                            (bulan, id_user, rata_rata_umum, rata_rata_teknis, rata_rata_total, status, verified_by, verified_at, keterangan) 
                          VALUES 
                            ('$target_bulan_safe', $id_user, $avg_umum, $avg_teknis, $avg_total, 1, $verified_by, NOW(), '$keterangan_safe')";
        mysqli_query($conn, $insert_header);
        $id_ss_archive = mysqli_insert_id($conn);
    }

    if (!$id_ss_archive) {
        return false;
    }

    // Salin data kategori tb_ss dan poin tb_sspoin
    $query_kategori = mysqli_query($conn, "SELECT * FROM tb_ss WHERE id_user = $id_user");
    if ($query_kategori) {
        while ($kat = mysqli_fetch_assoc($query_kategori)) {
            $id_poinss_ref = intval($kat['id_poinss']);
            $poin_ss = mysqli_real_escape_string($conn, $kat['poin_ss']);
            $tipe_ss = in_array($kat['tipe_ss'], ['umum', 'teknis']) ? $kat['tipe_ss'] : 'umum';

            $ins_kat = "INSERT INTO tbar_ss (id_ss_archive, id_user, id_poinss_ref, poin_ss, tipe_ss) 
                        VALUES ($id_ss_archive, $id_user, $id_poinss_ref, '$poin_ss', '$tipe_ss')";
            mysqli_query($conn, $ins_kat);
            $last_tbar_ss_id = mysqli_insert_id($conn);

            // Salin butir poin kategori ini
            $query_poin = mysqli_query($conn, "SELECT * FROM tb_sspoin WHERE id_ss = $id_poinss_ref AND id_user = $id_user");
            if ($query_poin) {
                while ($p = mysqli_fetch_assoc($query_poin)) {
                    $id_sspoin_ref = intval($p['id_sspoin']);
                    $poinss = mysqli_real_escape_string($conn, $p['poinss']);
                    $nilai1 = mysqli_real_escape_string($conn, $p['nilai1'] ?? '');
                    $nilai2 = mysqli_real_escape_string($conn, $p['nilai2'] ?? '');
                    $nilai3 = mysqli_real_escape_string($conn, $p['nilai3'] ?? '');
                    $nilai4 = mysqli_real_escape_string($conn, $p['nilai4'] ?? '');
                    $nilaiss = floatval($p['nilaiss']);
                    $deskripsi = mysqli_real_escape_string($conn, $p['deskripsi'] ?? '');

                    $ins_poin = "INSERT INTO tbar_sspoin 
                                    (id_tbar_ss, id_user, id_sspoin_ref, poinss, nilai1, nilai2, nilai3, nilai4, nilaiss, deskripsi) 
                                 VALUES 
                                    ($last_tbar_ss_id, $id_user, $id_sspoin_ref, '$poinss', '$nilai1', '$nilai2', '$nilai3', '$nilai4', $nilaiss, '$deskripsi')";
                    mysqli_query($conn, $ins_poin);
                }
            }
        }
    }

    return true;
}

/**
 * Membatalkan verifikasi pada arsip SS (mengubah status menjadi 0)
 */
function unverifyArchiveSS($conn, $id_user, $target_bulan = null) {
    ensureSSArchiveTables($conn);
    $id_user = intval($id_user);
    if ($target_bulan === null) {
        $target_bulan = getSSArchiveTargetMonth();
    }
    $target_bulan_safe = mysqli_real_escape_string($conn, $target_bulan);

    $sql = "UPDATE tbar_ss_archive SET status = 0 WHERE id_user = $id_user AND bulan = '$target_bulan_safe'";
    return mysqli_query($conn, $sql);
}

/**
 * Mendapatkan predikat / grade untuk nilai Skill Standard (skala 1-4)
 */
function getSSGrade($nilai) {
    $nilai = (float)$nilai;
    if ($nilai >= 3.5) {
        return ['label' => 'EXCELLENT', 'color' => 'success', 'textColor' => '#198754'];
    } elseif ($nilai >= 3.0) {
        return ['label' => 'VERY GOOD', 'color' => 'primary', 'textColor' => '#0d6efd'];
    } elseif ($nilai >= 2.0) {
        return ['label' => 'GOOD', 'color' => 'warning', 'textColor' => '#ffc107'];
    } else {
        return ['label' => 'POOR', 'color' => 'danger', 'textColor' => '#dc3545'];
    }
}
