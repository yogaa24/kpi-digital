<?php
// ============================================================
// HANDLER AJAX — HARUS PALING ATAS, SEBELUM OUTPUT HTML APAPUN
// ============================================================
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/checkAdmin.php';
require 'helper/sp_functions.php';

requireAdminHRD();

function resetKPIData_adm($conn, $user_id) {
    $user_id = intval($user_id);
    $sql_reset_whats = "UPDATE tb_whats 
                        SET hasil = '', nilai = 0, total = 0,
                            is_edited = 0,
                            edited_by = NULL,
                            edited_at = NULL,
                            original_hasil = NULL,
                            original_nilai = NULL,
                            original_total = NULL
                        WHERE id_user = '$user_id'";
    $reset_whats = mysqli_query($conn, $sql_reset_whats);

    $sql_reset_hows = "UPDATE tb_hows 
                       SET hasil = '', nilai = 0, total = 0,
                           is_edited = 0,
                           edited_by = NULL,
                           edited_at = NULL,
                           original_hasil = NULL,
                           original_nilai = NULL,
                           original_total = NULL
                       WHERE id_user = '$user_id'";
    $reset_hows = mysqli_query($conn, $sql_reset_hows);
    return $reset_whats && $reset_hows;
}

function kpiNumber_adm($value) {
    return is_numeric($value) ? (float) $value : 0.0;
}

function kpiHasSavedValue_adm($data) {
    $keys = ['total_kpi', 'final_what', 'final_how', 'total_what', 'total_how'];
    foreach ($keys as $key) {
        if (abs(kpiNumber_adm($data[$key] ?? 0)) > 0.00001) return true;
    }

    foreach (($data['kpi_details'] ?? []) as $detail) {
        $detail_keys = ['nilai_what', 'nilai_how', 'total_what_raw', 'total_how_raw'];
        foreach ($detail_keys as $key) {
            if (abs(kpiNumber_adm($detail[$key] ?? 0)) > 0.00001) return true;
        }
    }

    return false;
}

function historySummaryHasValue_adm($row) {
    $keys = ['total_kpi_real', 'nilai_what', 'nilai_how', 'total_what', 'total_how'];
    foreach ($keys as $key) {
        if (abs(kpiNumber_adm($row[$key] ?? 0)) > 0.00001) return true;
    }

    return false;
}

function sumKPITotal_adm($conn, $table, $user_id, $id_kpi) {
    $user_id = intval($user_id);
    $id_kpi = intval($id_kpi);
    $result = mysqli_query($conn, "SELECT COALESCE(SUM(total), 0) as total FROM {$table} WHERE id_user='$user_id' AND id_kpi='$id_kpi'");
    if (!$result) return 0.0;

    $row = mysqli_fetch_assoc($result);
    return kpiNumber_adm($row['total'] ?? 0);
}

function calculateKPI_adm($conn, $user_id, $is_simulation = false) {
    $user_id = intval($user_id);
    $prefix = $is_simulation ? 'tbsim_' : 'tb_';
    $sql_kpi = "SELECT * FROM {$prefix}kpi WHERE id_user='$user_id'";
    $result_kpi = mysqli_query($conn, $sql_kpi);
    $total_what = 0; $total_how = 0; $kpi_details = [];
    if (!$result_kpi) {
        return compact('total_what','total_how') + [
            'bobot_what' => 0,
            'bobot_how' => 0,
            'final_what' => 0,
            'final_how' => 0,
            'total_kpi' => 0,
            'kpi_details' => []
        ];
    }
    while ($kpi = mysqli_fetch_assoc($result_kpi)) {
        $id_kpi = intval($kpi['id']);
        $total_nilai_what = sumKPITotal_adm($conn, "{$prefix}whats", $user_id, $id_kpi);
        $nilai_what = ($total_nilai_what * kpiNumber_adm($kpi['bobot'])) / 100;
        $total_what += $nilai_what;
        $total_nilai_how = sumKPITotal_adm($conn, "{$prefix}hows", $user_id, $id_kpi);
        $nilai_how = ($total_nilai_how * kpiNumber_adm($kpi['bobot2'])) / 100;
        $total_how += $nilai_how;
        $kpi_details[] = ['id'=>$id_kpi,'poin_what'=>$kpi['poin'],'poin_how'=>$kpi['poin2'],
                          'bobot_what'=>kpiNumber_adm($kpi['bobot']),'bobot_how'=>kpiNumber_adm($kpi['bobot2']),
                          'nilai_what'=>$nilai_what,'nilai_how'=>$nilai_how,
                          'total_what_raw'=>$total_nilai_what,'total_how_raw'=>$total_nilai_how];
    }
    $result_bobot = mysqli_query($conn, "SELECT bobotwhat, bobothow FROM {$prefix}bobotkpi WHERE id_user='$user_id' LIMIT 1");
    $bobot = $result_bobot ? mysqli_fetch_assoc($result_bobot) : [];
    $bobot_what = kpiNumber_adm($bobot['bobotwhat'] ?? 0); $bobot_how = kpiNumber_adm($bobot['bobothow'] ?? 0);
    $final_what = ($total_what * $bobot_what) / 100;
    $final_how  = ($total_how  * $bobot_how)  / 100;
    $total_kpi  = $final_what + $final_how;
    return compact('total_what','total_how','bobot_what','bobot_how','final_what','final_how','total_kpi','kpi_details');
}

function saveKPIHistory_adm($conn, $user_id, $kpi_real, $kpi_sim) {
    $user_id = intval($user_id);
    $bulan_simpan   = date('Y-m', strtotime('-1 month'));
    $bulan_sekarang = date('Y-m');
    if (!empty($kpi_real['kpi_details'])) {
        $first_kpi_id = $kpi_real['kpi_details'][0]['id'];
        $check_owner  = mysqli_query($conn, "SELECT id_user FROM tb_kpi WHERE id='$first_kpi_id' LIMIT 1");
        if ($check_owner && mysqli_num_rows($check_owner) > 0) {
            $owner = mysqli_fetch_assoc($check_owner);
            if ($owner['id_user'] != $user_id) return false;
        }
    }
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_kpi_history'");
    if (!$check_table || mysqli_num_rows($check_table) == 0) return false;
    $check_next = mysqli_query($conn, "SELECT id FROM tb_kpi_history WHERE id_user='$user_id' AND bulan='$bulan_sekarang' AND is_summary=1");
    if ($check_next && mysqli_num_rows($check_next) > 0) return true;

    $current_has_value = kpiHasSavedValue_adm($kpi_real);
    $check_sum = mysqli_query($conn, "SELECT * FROM tb_kpi_history WHERE id_user='$user_id' AND bulan='$bulan_simpan' AND is_summary=1 LIMIT 1");
    $existing_summary = ($check_sum && mysqli_num_rows($check_sum) > 0) ? mysqli_fetch_assoc($check_sum) : null;

    // Reset bisa diklik ulang setelah nilai live sudah 0. Jangan timpa history valid dengan angka 0.
    if ($existing_summary && !$current_has_value && historySummaryHasValue_adm($existing_summary)) {
        return true;
    }

    if (!$current_has_value) {
        return true;
    }

    $fw  = mysqli_real_escape_string($conn, $kpi_real['final_what']);
    $fh  = mysqli_real_escape_string($conn, $kpi_real['final_how']);
    $tw  = mysqli_real_escape_string($conn, $kpi_real['total_what']);
    $th  = mysqli_real_escape_string($conn, $kpi_real['total_how']);
    $tkr = mysqli_real_escape_string($conn, $kpi_real['total_kpi']);
    $tkt = mysqli_real_escape_string($conn, $kpi_sim['total_kpi']);
    $bw  = mysqli_real_escape_string($conn, $kpi_real['bobot_what']);
    $bh  = mysqli_real_escape_string($conn, $kpi_real['bobot_how']);
    if ($existing_summary) {
        if (!mysqli_query($conn, "UPDATE tb_kpi_history SET total_kpi_real='$tkr',total_kpi_target='$tkt',total_what='$tw',total_how='$th',bobot_what='$bw',bobot_how='$bh',nilai_what='$fw',nilai_how='$fh' WHERE id_user='$user_id' AND bulan='$bulan_simpan' AND is_summary=1")) return false;
    } else {
        if (!mysqli_query($conn, "INSERT INTO tb_kpi_history (id_user,id_kpi,bulan,is_summary,total_kpi_real,total_kpi_target,total_what,total_how,bobot_what,bobot_how,nilai_what,nilai_how) VALUES ('$user_id',NULL,'$bulan_simpan',1,'$tkr','$tkt','$tw','$th','$bw','$bh','$fw','$fh')")) return false;
    }
    foreach ($kpi_real['kpi_details'] as $d) {
        $id_kpi=intval($d['id']); $pw=mysqli_real_escape_string($conn,$d['poin_what']); $ph=mysqli_real_escape_string($conn,$d['poin_how']);
        $dbw=mysqli_real_escape_string($conn,$d['bobot_what']); $dbh=mysqli_real_escape_string($conn,$d['bobot_how']);
        $twr=mysqli_real_escape_string($conn,$d['total_what_raw']); $thr=mysqli_real_escape_string($conn,$d['total_how_raw']);
        $nw=mysqli_real_escape_string($conn,$d['nilai_what']); $nh=mysqli_real_escape_string($conn,$d['nilai_how']);
        $ck=mysqli_query($conn,"SELECT id FROM tb_kpi_history WHERE id_user='$user_id' AND id_kpi='$id_kpi' AND bulan='$bulan_simpan' AND is_summary=0");
        if ($ck && mysqli_num_rows($ck)>0) {
            if (!mysqli_query($conn,"UPDATE tb_kpi_history SET poin_what='$pw',poin_how='$ph',bobot_what='$dbw',bobot_how='$dbh',total_what_raw='$twr',total_how_raw='$thr',nilai_what='$nw',nilai_how='$nh' WHERE id_user='$user_id' AND id_kpi='$id_kpi' AND bulan='$bulan_simpan' AND is_summary=0")) return false;
        } else {
            if (!mysqli_query($conn,"INSERT INTO tb_kpi_history (id_user,id_kpi,bulan,is_summary,poin_what,poin_how,bobot_what,bobot_how,total_what_raw,total_how_raw,nilai_what,nilai_how) VALUES ('$user_id','$id_kpi','$bulan_simpan',0,'$pw','$ph','$dbw','$dbh','$twr','$thr','$nw','$nh')")) return false;
        }
    }
    return true;
}

function saveKPIResetLog_adm($conn, $reset_by, $bulan_simpan, $total_user, $berhasil, $gagal, $catatan) {
    mysqli_query($conn, "CREATE TABLE IF NOT EXISTS `tb_kpi_reset_log` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `reset_by` INT NOT NULL,
        `bulan_direset` VARCHAR(7) NOT NULL,
        `jumlah_user` INT DEFAULT 0,
        `jumlah_berhasil` INT DEFAULT 0,
        `jumlah_gagal` INT DEFAULT 0,
        `catatan` TEXT NULL,
        `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $columns = [];
    $desc = mysqli_query($conn, "SHOW COLUMNS FROM tb_kpi_reset_log");
    if ($desc) {
        while ($row = mysqli_fetch_assoc($desc)) {
            $columns[$row['Field']] = true;
        }
    }

    $reset_by = intval($reset_by);
    $total_user = intval($total_user);
    $berhasil = intval($berhasil);
    $gagal = intval($gagal);
    $catatan = mysqli_real_escape_string($conn, $catatan);
    $bulan_simpan = mysqli_real_escape_string($conn, $bulan_simpan);

    if (isset($columns['bulan_direset'])) {
        return mysqli_query($conn, "INSERT INTO tb_kpi_reset_log (reset_by,bulan_direset,jumlah_user,jumlah_berhasil,jumlah_gagal,catatan) VALUES ('$reset_by','$bulan_simpan','$total_user','$berhasil','$gagal','$catatan')");
    }

    if (isset($columns['bulan']) && isset($columns['tahun'])) {
        $bulan = intval(date('m', strtotime($bulan_simpan . '-01')));
        $tahun = intval(date('Y', strtotime($bulan_simpan . '-01')));
        return mysqli_query($conn, "INSERT INTO tb_kpi_reset_log (bulan,tahun,reset_by,catatan,jumlah_tereset) VALUES ('$bulan','$tahun','$reset_by','$catatan','$berhasil')");
    }

    return false;
}

// ============================================================
// TANGKAP REQUEST AJAX SEBELUM ADA OUTPUT APAPUN
// ============================================================
if (isset($_POST['action']) && $_POST['action'] === 'reset_kpi_all_users') {
    // Bersihkan semua output buffer yang mungkin sudah ada
    while (ob_get_level()) ob_end_clean();

    header('Content-Type: application/json');

    $sql_all_users = "SELECT u.id FROM tb_users u
                      WHERE u.jabatan != 'Admin HRD'
                      AND u.username NOT IN ('itboy', 'adminhrd')";
    $result_all_users = mysqli_query($conn, $sql_all_users);

    $berhasil = 0; $gagal = 0;
    while ($u = mysqli_fetch_assoc($result_all_users)) {
        $uid      = $u['id'];
        $kpi_real = calculateKPI_adm($conn, $uid, false);
        $kpi_sim  = calculateKPI_adm($conn, $uid, true);
        if (!saveKPIHistory_adm($conn, $uid, $kpi_real, $kpi_sim)) {
            $gagal++;
            continue;
        }
        resetKPIData_adm($conn, $uid) ? $berhasil++ : $gagal++;
    }

    $catatan    = $_POST['catatan'] ?? '';
    $total_user = $berhasil + $gagal;
    $reset_by   = $_SESSION['id_user'];
    $bln        = date('Y-m', strtotime('-1 month'));
    saveKPIResetLog_adm($conn, $reset_by, $bln, $total_user, $berhasil, $gagal, $catatan);

    echo json_encode([
        'success'  => true,
        'berhasil' => $berhasil,
        'gagal'    => $gagal,
        'bulan'    => $bln,
        'message'  => "Reset selesai! $berhasil user berhasil direset."
    ]);
    exit();
}
// ============================================================

updateExpiredSP($conn);

    // ========== HANDLER UNTUK SURAT PERINGATAN ==========
    
    if (isset($_POST['tambah_sp'])) {
        $id_user_sp          = intval($_POST['id_user']);
        $jenis_sp            = mysqli_real_escape_string($conn, $_POST['jenis_sp']);
        $nomor_sp            = mysqli_real_escape_string($conn, $_POST['nomor_sp']);
        $tanggal_sp          = mysqli_real_escape_string($conn, $_POST['tanggal_sp']);
        $masa_berlaku_mulai  = mysqli_real_escape_string($conn, $_POST['masa_berlaku_mulai']);
        $masa_berlaku_selesai= mysqli_real_escape_string($conn, $_POST['masa_berlaku_selesai']);
        $alasan              = mysqli_real_escape_string($conn, $_POST['alasan']);
        $keterangan          = mysqli_real_escape_string($conn, $_POST['keterangan']);
        $created_by          = $_SESSION['id_user'];
        
        if ($masa_berlaku_selesai < $masa_berlaku_mulai) {
            echo "<script>alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai!');</script>";
        } else {
            $sql  = "INSERT INTO tb_surat_peringatan 
                    (id_user, jenis_sp, nomor_sp, tanggal_sp, masa_berlaku_mulai, masa_berlaku_selesai, alasan, keterangan, status, created_by) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'aktif', ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "isssssssi", $id_user_sp, $jenis_sp, $nomor_sp, $tanggal_sp, 
                                   $masa_berlaku_mulai, $masa_berlaku_selesai, $alasan, $keterangan, $created_by);
            if (mysqli_stmt_execute($stmt)) {
                echo "<script>alert('Surat Peringatan berhasil ditambahkan!'); window.location.href='dashboard-adminhrd';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan Surat Peringatan: " . mysqli_error($conn) . "');</script>";
            }
        }
    }
    
    if (isset($_POST['hapus_sp'])) {
        $id_sp = intval($_POST['id_sp']);
        $sql   = "UPDATE tb_surat_peringatan SET status = 'dihapus' WHERE id_sp = ?";
        $stmt  = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_sp);
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Surat Peringatan berhasil dihapus!'); window.location.href='dashboard-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal menghapus Surat Peringatan!');</script>";
        }
    }
?>

<html lang="en">
<head>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KPI Digital - Admin HRD</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="KPI Digital">
    <meta name="author" content="Rvld">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />
    <!-- SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 0.95rem;
        }
        .header-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 8px 16px rgba(102, 126, 234, 0.3);
        }
        .menu-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            border: none;
            overflow: hidden;
            border-radius: 12px;
            height: 100%;
            background: white;
        }
        .menu-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 24px rgba(0,0,0,0.15) !important;
        }
        .menu-card .icon-wrapper {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 16px;
            transition: all 0.3s ease;
        }
        .menu-card:hover .icon-wrapper { transform: scale(1.1) rotate(5deg); }
        .icon-bg-info    { background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%); }
        .icon-bg-success { background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%); }
        .icon-bg-warning { background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%); }
        .icon-bg-danger  { background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%); }
        .icon-bg-purple  { background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%); }
        .icon-bg-primary { background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); }
        .stat-card {
            border-radius: 12px;
            transition: all 0.3s ease;
            border: none;
            background: white;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
        }
        .info-card {
            border-radius: 12px;
            border: 2px solid #3b82f6;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
        }
        .menu-card .card-title {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #1e293b;
        }
        .menu-card .card-text {
            font-size: 0.875rem;
            color: #64748b;
            line-height: 1.4;
        }

        /* ===== RESET KPI CARD ===== */
        .reset-kpi-card {
            border: 2px solid #ef4444;
            border-radius: 12px;
            background: linear-gradient(135deg, #fff5f5 0%, #fee2e2 100%);
            transition: all 0.3s ease;
        }
        .reset-kpi-card:hover {
            box-shadow: 0 8px 20px rgba(239,68,68,0.2);
        }
        .reset-icon-wrap {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .btn-reset-kpi {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border: none;
            color: white;
            border-radius: 10px;
            padding: 10px 24px;
            font-weight: bold;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(239,68,68,0.4);
            transition: all 0.3s ease;
        }
        .btn-reset-kpi:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(239,68,68,0.5);
            color: white;
        }
        .btn-reset-kpi:active { transform: translateY(0); }

        /* ===== MODAL RESET ===== */
        #overlayResetKPI {
            display: none;
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: rgba(0,0,0,0.65);
            z-index: 99999;
            align-items: center;
            justify-content: center;
        }
        .modal-reset-box {
            background: white;
            border-radius: 16px;
            width: 100%;
            max-width: 500px;
            margin: 16px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.35);
            overflow: hidden;
            animation: slideUp 0.3s ease;
        }
        @keyframes slideUp {
            from { transform: translateY(40px); opacity: 0; }
            to   { transform: translateY(0);    opacity: 1; }
        }
        .modal-reset-header {
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            padding: 18px 22px;
            color: white;
        }
        .modal-reset-body { padding: 22px; }
        .warning-box {
            background: #fef2f2;
            border: 1.5px solid #fca5a5;
            border-radius: 10px;
            padding: 13px 15px;
            margin-bottom: 18px;
        }
        .konfirmasi-input {
            border: 2px solid #fca5a5 !important;
            border-radius: 8px !important;
            font-weight: 600;
            letter-spacing: 1px;
        }
        .konfirmasi-input:focus {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 3px rgba(239,68,68,0.15) !important;
        }
        .btn-modal-batal {
            border-radius: 8px;
            padding: 9px 0;
            font-weight: 600;
            flex: 1;
        }
        .btn-modal-eksekusi {
            border-radius: 8px;
            padding: 9px 0;
            font-weight: 700;
            flex: 1;
            background: linear-gradient(135deg, #ef4444, #b91c1c);
            border: none;
            color: white;
            transition: opacity 0.3s;
        }
        .btn-modal-eksekusi:disabled { opacity: 0.45; cursor: not-allowed; }
        .btn-modal-eksekusi:not(:disabled):hover { filter: brightness(1.08); color: white; }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
<div class="app-wrapper">
    <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
    <?php include("pages/part/p_aside_adminhrd.php"); ?>
    
    <main class="app-main">
        <div class="app-content">
            <div class="container-fluid">

                <!-- Header Admin HRD -->
                <div class="header-admin mt-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h3 class="mb-2 fw-bold">
                                <i class="bi bi-shield-fill-check me-2"></i>Dashboard Admin HRD
                            </h3>
                            <p class="mb-0 opacity-90">Monitoring dan Pengelolaan KPI Seluruh Karyawan</p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <span class="badge-admin">
                                <i class="bi bi-person-badge-fill me-2"></i><?= $nama_lngkp ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Menu Cards Section -->
                <div class="row mb-4">
                    <!-- Data User -->
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm menu-card" onclick="window.location.href='datauser-adminhrd'">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div class="icon-wrapper icon-bg-info mb-3">
                                    <i class="bi bi-people-fill text-info" style="font-size:2.2rem;"></i>
                                </div>
                                <h5 class="card-title mb-1">Data User</h5>
                                <p class="card-text text-muted mb-0">Kelola Data User</p>
                            </div>
                        </div>
                    </div>
                    <!-- Data KPI Karyawan -->
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm menu-card" onclick="window.location.href='datakpi-adminhrd'">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div class="icon-wrapper icon-bg-success">
                                    <i class="bi bi-graph-up-arrow text-success" style="font-size:2.2rem;"></i>
                                </div>
                                <h5 class="card-title">Data KPI Karyawan</h5>
                                <p class="card-text">Monitoring KPI seluruh karyawan</p>
                            </div>
                        </div>
                    </div>
                    <!-- Archive -->
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm menu-card" onclick="window.location.href='archive-adminhrd'">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div class="icon-wrapper icon-bg-warning">
                                    <i class="bi bi-archive-fill text-warning" style="font-size:2.2rem;"></i>
                                </div>
                                <h5 class="card-title">Archive</h5>
                                <p class="card-text">Arsip dokumen & data historis</p>
                            </div>
                        </div>
                    </div>
                    <!-- Eviden -->
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm menu-card" onclick="window.location.href='eviden-adminhrd'">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div class="icon-wrapper icon-bg-danger">
                                    <i class="bi bi-folder-fill text-danger" style="font-size:2.2rem;"></i>
                                </div>
                                <h5 class="card-title">Eviden</h5>
                                <p class="card-text">Dokumentasi bukti & evidensi</p>
                            </div>
                        </div>
                    </div>
                    <!-- Lock KPI Settings -->
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm menu-card" onclick="window.location.href='kpi-lock-settings-adminhrd'">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div class="icon-wrapper icon-bg-purple">
                                    <i class="bi bi-lock-fill" style="font-size:2.2rem; color: #9333ea;"></i>
                                </div>
                                <h5 class="card-title">Lock KPI Settings</h5>
                                <p class="card-text">Atur periode & akses pengisian KPI</p>
                            </div>
                        </div>
                    </div>
                    <!-- Skill Standard -->
                    <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                        <div class="card shadow-sm menu-card" onclick="window.location.href='skill-standard-adminhrd'">
                            <div class="card-body d-flex flex-column align-items-center justify-content-center text-center p-4">
                                <div class="icon-wrapper icon-bg-primary">
                                    <i class="bi bi-award-fill text-primary" style="font-size:2.2rem;"></i>
                                </div>
                                <h5 class="card-title">Skill Standard</h5>
                                <p class="card-text">Kelola standar kompetensi karyawan</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========== TOMBOL RESET KPI ========== -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card shadow-sm reset-kpi-card">
                            <div class="card-body py-4 px-4">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <div class="d-flex align-items-center" style="gap:16px;">
                                            <div class="reset-icon-wrap">
                                                <i class="bi bi-arrow-counterclockwise" style="font-size:1.8rem; color:white;"></i>
                                            </div>
                                            <div>
                                                <h5 class="fw-bold mb-1" style="color:#991b1b;">
                                                    Reset KPI Semua Karyawan
                                                </h5>
                                                <p class="mb-0 text-muted" style="font-size:0.88rem; line-height:1.5;">
                                                    Reset nilai KPI (<strong>tb_whats</strong> & <strong>tb_hows</strong>) seluruh karyawan menjadi 0.
                                                    History bulan berjalan akan <strong>disimpan otomatis</strong> sebelum direset.
                                                    <span style="color:#dc2626; font-weight:600;">Tindakan ini tidak dapat dibatalkan.</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4 text-md-right mt-3 mt-md-0">
                                        <button class="btn btn-reset-kpi" onclick="bukaModalReset()">
                                            <i class="bi bi-arrow-counterclockwise mr-2"></i>Reset KPI Sekarang
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ========== END RESET KPI ========== -->

                <!-- Statistics Summary -->
                <div class="row mb-4">
                    <?php
                    $sql_stats = "SELECT 
                                    COUNT(*) as total_karyawan,
                                    SUM(CASE WHEN u.jabatan = 'Kadep' THEN 1 ELSE 0 END) as total_kadep,
                                    SUM(CASE WHEN u.jabatan = 'Koordinator' THEN 1 ELSE 0 END) as total_Koordinator,
                                    SUM(CASE WHEN u.jabatan = 'Manager' THEN 1 ELSE 0 END) as total_manager,
                                    SUM(CASE WHEN u.jabatan = 'Karyawan' THEN 1 ELSE 0 END) as total_karyawan_biasa
                                  FROM tb_users u
                                  WHERE u.id != $id_user AND u.jabatan != 'Admin HRD'";
                    $result_stats = mysqli_query($conn, $sql_stats);
                    $stats = mysqli_fetch_assoc($result_stats);

                    $today = date('Y-m-d');
                    $sql_sp_aktif = "SELECT COUNT(*) as total_sp_aktif 
                                     FROM tb_surat_peringatan 
                                     WHERE status = 'aktif' 
                                     AND masa_berlaku_mulai <= '$today' 
                                     AND masa_berlaku_selesai >= '$today'";
                    $result_sp_aktif = mysqli_query($conn, $sql_sp_aktif);
                    $sp_aktif_count  = mysqli_fetch_assoc($result_sp_aktif)['total_sp_aktif'];

                    $sql_lock_aktif = "SELECT COUNT(*) as total_lock 
                                    FROM tb_kpi_lock_settings 
                                    WHERE status = 'aktif' 
                                    AND tanggal_mulai <= '$today' 
                                    AND tanggal_selesai >= '$today'";
                    $result_lock  = mysqli_query($conn, $sql_lock_aktif);
                    $lock_aktif   = mysqli_fetch_assoc($result_lock)['total_lock'];

                    $sql_periode_aktif = "SELECT nama_periode, level_akses 
                                        FROM tb_kpi_lock_settings 
                                        WHERE status = 'aktif' 
                                        AND tanggal_mulai <= '$today' 
                                        AND tanggal_selesai >= '$today'
                                        ORDER BY created_at DESC LIMIT 1";
                    $result_periode = mysqli_query($conn, $sql_periode_aktif);
                    $periode_aktif  = mysqli_fetch_assoc($result_periode);
                    ?>

                    <!-- Status Lock KPI -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow-sm <?= $lock_aktif > 0 ? 'border-warning' : 'border-success' ?>" style="border-width:2px !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1 small">Status Lock KPI</h6>
                                        <h3 class="fw-bold mb-0 <?= $lock_aktif > 0 ? 'text-warning' : 'text-success' ?>">
                                            <?= $lock_aktif > 0 ? 'TERBATAS' : 'TERBUKA' ?>
                                        </h3>
                                        <?php if($periode_aktif): ?>
                                        <small class="text-muted d-block mt-1"><?= $periode_aktif['nama_periode'] ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div class="<?= $lock_aktif > 0 ? 'text-warning' : 'text-success' ?>">
                                        <i class="bi bi-<?= $lock_aktif > 0 ? 'lock-fill' : 'unlock-fill' ?>" style="font-size:2.5rem; opacity:0.8;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Total Karyawan -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1 small">Total Karyawan</h6>
                                        <h3 class="fw-bold mb-0 text-primary"><?=$stats['total_karyawan']?></h3>
                                        <small class="text-muted">Karyawan aktif</small>
                                    </div>
                                    <div class="text-primary" style="opacity:0.8;">
                                        <i class="bi bi-people-fill" style="font-size:2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Kadep -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1 small">Kepala Departemen</h6>
                                        <h3 class="fw-bold mb-0 text-danger"><?=$stats['total_kadep']?></h3>
                                        <small class="text-muted">Kadep</small>
                                    </div>
                                    <div class="text-danger" style="opacity:0.8;">
                                        <i class="bi bi-award-fill" style="font-size:2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Koordinator -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1 small">Koordinator</h6>
                                        <h3 class="fw-bold mb-0 text-warning"><?=$stats['total_Koordinator']?></h3>
                                        <small class="text-muted">Tim koordinator</small>
                                    </div>
                                    <div class="text-warning" style="opacity:0.8;">
                                        <i class="bi bi-star-fill" style="font-size:2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Manager -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow-sm border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1 small">Manager</h6>
                                        <h3 class="fw-bold mb-0" style="color:#6366f1;"><?=$stats['total_manager']?></h3>
                                        <small class="text-muted">Level manager</small>
                                    </div>
                                    <div style="opacity:0.8; color:#6366f1;">
                                        <i class="bi bi-person-badge-fill" style="font-size:2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SP Aktif -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card stat-card shadow-sm border-danger" style="border-width:2px !important;">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="text-muted mb-1 small">Surat Peringatan</h6>
                                        <h3 class="fw-bold mb-0 text-danger"><?=$sp_aktif_count?></h3>
                                        <small class="text-muted">SP Aktif</small>
                                    </div>
                                    <div class="text-danger" style="opacity:0.8;">
                                        <i class="bi bi-exclamation-triangle-fill" style="font-size:2.5rem;"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm info-card">
                            <div class="card-body text-center py-5">
                                <i class="bi bi-info-circle-fill text-primary mb-3" style="font-size:3.5rem;"></i>
                                <h4 class="fw-bold mb-3" style="color:#1e40af;">Selamat Datang di Dashboard Admin HRD</h4>
                                <p class="text-muted mb-4 mx-auto" style="max-width:700px;">
                                    Kelola data karyawan, monitoring KPI, standar kompetensi, dan administrasi HRD dengan mudah melalui menu yang tersedia di atas.
                                </p>
                                <div class="d-flex justify-content-center align-items-center" style="gap:8px;">
                                    <i class="bi bi-lightbulb-fill text-warning" style="font-size:1.2rem;"></i>
                                    <p class="text-muted mb-0">
                                        <strong>Tip:</strong> Klik menu <strong>"Data KPI Karyawan"</strong> untuk melihat detail KPI seluruh karyawan
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </main>

    <?php include("pages/part/p_footeradminhrd.php"); ?>
</div>

<!-- ========== MODAL KONFIRMASI RESET KPI ========== -->
<div id="overlayResetKPI" onclick="klikOverlay(event)">
    <div class="modal-reset-box">
        <!-- Header -->
        <div class="modal-reset-header">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center" style="gap:10px;">
                    <i class="bi bi-exclamation-triangle-fill" style="font-size:1.3rem;"></i>
                    <h5 class="mb-0 font-weight-bold">Konfirmasi Reset KPI</h5>
                </div>
                <button onclick="tutupModalReset()" style="background:none;border:none;color:white;font-size:1.6rem;line-height:1;cursor:pointer;">&times;</button>
            </div>
        </div>
        <!-- Body -->
        <div class="modal-reset-body">

            <!-- Warning box -->
            <div class="warning-box">
                <div class="d-flex" style="gap:10px;">
                    <i class="bi bi-shield-exclamation text-danger" style="font-size:1.2rem; margin-top:1px; flex-shrink:0;"></i>
                    <div>
                        <strong style="color:#dc2626; font-size:0.9rem;">Perhatian!</strong>
                        <p class="mb-0 text-muted" style="font-size:0.85rem; margin-top:3px; line-height:1.5;">
                            Aksi ini akan me-reset <strong>nilai KPI (tb_whats & tb_hows) semua karyawan menjadi 0</strong>.
                            History bulan berjalan akan disimpan otomatis sebelum reset dilakukan.
                            Data yang sudah direset <strong>tidak dapat dikembalikan</strong>.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Info bulan yang akan disimpan -->
            <div class="d-flex align-items-center mb-3 p-2" style="background:#f0fdf4; border:1px solid #86efac; border-radius:8px;">
                <i class="bi bi-calendar-check-fill text-success mr-2" style="font-size:1rem;"></i>
                <small class="text-success font-weight-bold">
                    History bulan <strong><?= date('F Y', strtotime('-1 month')) ?></strong> akan disimpan otomatis sebelum reset.
                </small>
            </div>

            <!-- Catatan opsional -->
            <div class="mb-3">
                <label class="font-weight-bold" style="font-size:0.9rem;">Catatan Reset <span class="text-muted font-weight-normal">(opsional)</span></label>
                <textarea id="resetCatatan" class="form-control" rows="2"
                    placeholder="Contoh: Reset awal bulan <?= date('F Y') ?>"
                    style="border-radius:8px; resize:none; font-size:0.9rem;"></textarea>
            </div>

            <!-- Input konfirmasi -->
            <div class="mb-4">
                <label class="font-weight-bold" style="font-size:0.9rem; color:#dc2626;">
                    Ketik <code style="background:#fee2e2; padding:2px 7px; border-radius:4px; color:#b91c1c;">RESET KPI</code> untuk lanjut:
                </label>
                <input type="text" id="inputKonfirmasiReset" class="form-control konfirmasi-input mt-1"
                       placeholder="Ketik RESET KPI"
                       oninput="periksaKonfirmasi()"
                       autocomplete="off">
            </div>

            <!-- Tombol aksi -->
            <div class="d-flex" style="gap:10px;">
                <button class="btn btn-secondary btn-modal-batal" onclick="tutupModalReset()">
                    <i class="bi bi-x-lg mr-1"></i>Batal
                </button>
                <button id="btnEksekusiReset" class="btn-modal-eksekusi" disabled onclick="eksekusiReset()">
                    <i class="bi bi-arrow-counterclockwise mr-1"></i>Ya, Reset Semua
                </button>
            </div>
        </div>
    </div>
</div>
<!-- ========== END MODAL RESET KPI ========== -->

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/datatables/datatables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>

<script>
function bukaModalReset() {
    document.getElementById('overlayResetKPI').style.display = 'flex';
    document.getElementById('inputKonfirmasiReset').value = '';
    document.getElementById('resetCatatan').value = '';
    periksaKonfirmasi();
    setTimeout(function(){ document.getElementById('inputKonfirmasiReset').focus(); }, 300);
}

function tutupModalReset() {
    document.getElementById('overlayResetKPI').style.display = 'none';
}

function klikOverlay(e) {
    if (e.target === document.getElementById('overlayResetKPI')) tutupModalReset();
}

function periksaKonfirmasi() {
    var val = document.getElementById('inputKonfirmasiReset').value;
    var btn = document.getElementById('btnEksekusiReset');
    btn.disabled = (val !== 'RESET KPI');
}

function eksekusiReset() {
    var catatan = document.getElementById('resetCatatan').value;
    var btn     = document.getElementById('btnEksekusiReset');

    // Disable tombol & tampilkan loading
    btn.disabled  = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm mr-2" role="status"></span>Memproses...';

    $.ajax({
        url: 'dashboard-adminhrd',   // POST ke halaman ini sendiri
        method: 'POST',
        data: {
            action  : 'reset_kpi_all_users',
            catatan : catatan
        },
        dataType: 'json',
        success: function(res) {
            tutupModalReset();
            if (res.success) {
                Swal.fire({
                    icon : 'success',
                    title: 'Reset Berhasil!',
                    html : '<b>' + res.berhasil + ' karyawan</b> berhasil direset.<br>'
                         + (res.gagal > 0 ? '<span class="text-danger">' + res.gagal + ' gagal.</span><br>' : '')
                         + '<small class="text-muted">History bulan ' + res.bulan + ' tersimpan otomatis.</small>',
                    confirmButtonColor: '#3b82f6',
                    confirmButtonText : 'OK'
                }).then(function() { location.reload(); });
            } else {
                Swal.fire({
                    icon : 'error',
                    title: 'Reset Gagal',
                    text : res.message || 'Terjadi kesalahan, silakan coba lagi.',
                    confirmButtonColor: '#ef4444'
                });
            }
        },
        error: function() {
            tutupModalReset();
            Swal.fire({
                icon : 'error',
                title: 'Koneksi Error',
                text : 'Tidak dapat terhubung ke server. Silakan coba lagi.',
                confirmButtonColor: '#ef4444'
            });
        }
    });
}

// Keyboard shortcut: Escape menutup modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') tutupModalReset();
});
</script>
</body>
</html>
