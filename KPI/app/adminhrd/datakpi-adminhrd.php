<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/checkAdmin.php';
require 'helper/sp_functions.php';
require 'helper/verified_functions.php';

// Admin HRD dan Direktur (termasuk Diana Wulandari) bisa akses
requireAdminHRDOrDirektur();

// Update SP yang sudah expired
updateExpiredSP($conn);

// Ambil nomor SP terakhir untuk auto-generate
$q_last_sp = mysqli_query($conn, "SELECT nomor_sp FROM tb_surat_peringatan ORDER BY id_sp DESC LIMIT 1");
$last_sp_row = mysqli_fetch_assoc($q_last_sp);
$last_sp_nomor = $last_sp_row['nomor_sp'] ?? '0/KIU-HRD/' . date('Y');
$last_sp_prefix = intval(explode('/', $last_sp_nomor)[0]);
$next_sp_prefix = $last_sp_prefix + 1;

// AJAX Handler untuk memuat data Surat Peringatan karyawan (Modal Kelola SP)
if (isset($_GET['ajax_sp_list'])) {
    $id_user_sp = intval($_GET['id_user']);
    $sql_sp = "SELECT * FROM tb_surat_peringatan 
               WHERE id_user = $id_user_sp 
               ORDER BY created_at DESC";
    $result_sp = mysqli_query($conn, $sql_sp);
    
    if ($result_sp && mysqli_num_rows($result_sp) > 0) {
        ?>
        <div class="table-responsive">
            <table class="table table-hover table-bordered table-sm align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="8%">Jenis SP</th>
                        <th width="15%">Nomor SP</th>
                        <th width="10%">Tanggal</th>
                        <th width="15%">Masa Berlaku</th>
                        <th width="10%">Pengurangan</th>
                        <th>Alasan</th>
                        <th width="10%">Status</th>
                        <th width="12%">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($sp = mysqli_fetch_assoc($result_sp)) { 
                        $today = date('Y-m-d');
                        $is_active = ($sp['status'] == 'aktif' && 
                                     $sp['masa_berlaku_mulai'] <= $today && 
                                     $sp['masa_berlaku_selesai'] >= $today);
                        
                        $status_badge = 'secondary';
                        $status_text = 'Selesai';
                        
                        if ($sp['status'] == 'aktif') {
                            if ($is_active) {
                                $status_badge = 'success';
                                $status_text = 'Aktif';
                            } else if ($sp['masa_berlaku_selesai'] < $today) {
                                $status_badge = 'secondary';
                                $status_text = 'Expired';
                            } else {
                                $status_badge = 'warning';
                                $status_text = 'Akan Datang';
                            }
                        } else if ($sp['status'] == 'dihapus') {
                            $status_badge = 'danger';
                            $status_text = 'Dihapus';
                        }
                        
                        $sp_badge = getSPBadgeClass($sp['jenis_sp']);
                        $penalty = getSPPenalty($sp['jenis_sp']);
                    ?>
                    <tr class="<?=$is_active ? 'table-warning' : ''?>">
                        <td>
                            <span class="badge bg-<?=$sp_badge?> fw-bold">
                                <?=$sp['jenis_sp']?>
                            </span>
                        </td>
                        <td><small class="fw-bold"><?=$sp['nomor_sp']?></small></td>
                        <td><small><?=date('d/m/Y', strtotime($sp['tanggal_sp']))?></small></td>
                        <td>
                            <small>
                                <?=date('d/m/Y', strtotime($sp['masa_berlaku_mulai']))?><br>
                                s/d <?=date('d/m/Y', strtotime($sp['masa_berlaku_selesai']))?>
                            </small>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-danger">-<?=$penalty?> poin</span>
                        </td>
                        <td>
                            <small>
                                <strong>1.</strong> <?=htmlspecialchars($sp['alasan'])?><br>
                                <?php if (!empty($sp['alasan_2'])) { ?>
                                    <strong>2.</strong> <?=htmlspecialchars($sp['alasan_2'])?>
                                <?php } ?>
                            </small>
                        </td>
                        <td>
                            <span class="badge bg-<?=$status_badge?>">
                                <?=$status_text?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <!-- Cetak / Lihat Surat Resmi SP -->
                                <a href="cetak-sp?id_sp=<?=$sp['id_sp']?>" target="_blank" class="btn btn-sm btn-primary" title="Cetak / Lihat Dokumen Resmi SP">
                                    <i class="bi bi-printer-fill me-1"></i> Cetak
                                </a>
                                <?php if (!empty($sp['file_sp'])) { ?>
                                    <a href="uploads/surat_peringatan/<?=$sp['file_sp']?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Lihat Lampiran Berkas Upload">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                <?php } ?>
                                <?php if ($sp['status'] == 'aktif') { ?>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="if(confirm('Yakin ingin menghapus SP ini?\n\nNilai KPI karyawan akan kembali normal setelah SP dihapus.')) { document.getElementById('formHapusSP<?=$sp['id_sp']?>').submit(); }"
                                            title="Hapus SP">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="formHapusSP<?=$sp['id_sp']?>" method="POST" action="datakpi-adminhrd" style="display:none;">
                                        <input type="hidden" name="id_sp" value="<?=$sp['id_sp']?>">
                                        <input type="hidden" name="hapus_sp" value="1">
                                    </form>
                                <?php } else { ?>
                                    <span class="text-muted">-</span>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
        <?php
    } else {
        ?>
        <div class="alert alert-info text-center my-3">
            <i class="bi bi-info-circle fs-3"></i>
            <p class="mb-0 mt-2">Tidak ada data surat peringatan untuk karyawan ini.</p>
        </div>
        <?php
    }
    exit();
}

// Handler untuk tambah SP (Dibuat langsung dari aplikasi atau dengan upload)
if (isset($_POST['tambah_sp'])) {
    $id_user_sp = intval($_POST['id_user']);
    $jenis_sp = trim($_POST['jenis_sp']);
    $nomor_sp = trim($_POST['nomor_sp']);
    $tanggal_sp = trim($_POST['tanggal_sp']);
    $alasan = trim($_POST['alasan']);
    $alasan_2 = trim($_POST['alasan_2'] ?? '');
    $aturan_dilanggar = trim($_POST['aturan_dilanggar'] ?? '');
    $tanggal_kejadian = !empty($_POST['tanggal_kejadian']) ? trim($_POST['tanggal_kejadian']) : $tanggal_sp;
    $penandatangan = trim($_POST['penandatangan'] ?? 'Riza Dwi Fitrianingtyas');
    $jabatan_penandatangan = trim($_POST['jabatan_penandatangan'] ?? 'Kepala Departemen HRD');
    $tembusan = trim($_POST['tembusan'] ?? '1. Direktur sebagai laporan; 2. Kepala Departemen HRD; 3. Arsip;');
    $keterangan = trim($_POST['keterangan'] ?? '');
    $created_by = $_SESSION['id_user'];
    
    // Hitung otomatis masa berlaku 6 bulan dari tanggal SP
    $masa_berlaku_mulai = $tanggal_sp;
    $masa_berlaku_selesai = date('Y-m-d', strtotime($tanggal_sp . ' +6 months'));
    
    // Handle upload file (opsional)
    $file_sp = null;
    if (isset($_FILES['file_sp']) && $_FILES['file_sp']['error'] == 0 && !empty($_FILES['file_sp']['name'])) {
        $allowed_ext = ['pdf', 'jpg', 'jpeg', 'png'];
        $file_name = $_FILES['file_sp']['name'];
        $file_size = $_FILES['file_sp']['size'];
        $file_tmp = $_FILES['file_sp']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        // Validasi ekstensi
        if (!in_array($file_ext, $allowed_ext)) {
            echo "<script>alert('Format file tidak valid! Hanya PDF, JPG, JPEG, PNG yang diperbolehkan.'); window.history.back();</script>";
            exit();
        }
        
        // Validasi ukuran (max 5MB)
        if ($file_size > 5242880) {
            echo "<script>alert('Ukuran file terlalu besar! Maksimal 5MB.'); window.history.back();</script>";
            exit();
        }
        
        // Generate nama file unik
        $new_file_name = 'SP_' . $id_user_sp . '_' . time() . '.' . $file_ext;
        $upload_path = 'uploads/surat_peringatan/';
        
        // Buat folder jika belum ada
        if (!file_exists($upload_path)) {
            mkdir($upload_path, 0777, true);
        }
        
        // Upload file
        if (move_uploaded_file($file_tmp, $upload_path . $new_file_name)) {
            $file_sp = $new_file_name;
        } else {
            echo "<script>alert('Gagal upload file lampiran!'); window.history.back();</script>";
            exit();
        }
    }
    
    $sql = "INSERT INTO tb_surat_peringatan 
            (id_user, jenis_sp, nomor_sp, tanggal_sp, masa_berlaku_mulai, masa_berlaku_selesai, alasan, alasan_2, aturan_dilanggar, tanggal_kejadian, keterangan, penandatangan, jabatan_penandatangan, tembusan, file_sp, status, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'aktif', ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    if (!$stmt) {
        echo "<script>alert('❌ Gagal prepare statement: " . mysqli_error($conn) . "'); window.history.back();</script>";
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "issssssssssssssi", 
        $id_user_sp, $jenis_sp, $nomor_sp, $tanggal_sp, 
        $masa_berlaku_mulai, $masa_berlaku_selesai, 
        $alasan, $alasan_2, $aturan_dilanggar, $tanggal_kejadian, 
        $keterangan, $penandatangan, $jabatan_penandatangan, $tembusan, 
        $file_sp, $created_by
    );
    
    if (mysqli_stmt_execute($stmt)) {
        $new_sp_id = mysqli_insert_id($conn);
        echo "<script>
            alert('✅ Surat Peringatan berhasil dibuat langsung dari aplikasi!'); 
            if (confirm('Apakah Anda ingin langsung melihat / mencetak Surat Peringatan ini?')) {
                window.open('cetak-sp?id_sp=" . $new_sp_id . "', '_blank');
            }
            window.location.href='datakpi-adminhrd';
        </script>";
    } else {
        // Hapus file jika insert gagal
        if ($file_sp && file_exists($upload_path . $file_sp)) {
            unlink($upload_path . $file_sp);
        }
        echo "<script>alert('❌ Gagal menambahkan Surat Peringatan: " . mysqli_error($conn) . "');</script>";
    }
}

// Handler untuk hapus SP
if (isset($_POST['hapus_sp'])) {
    $id_sp = intval($_POST['id_sp']);
    
    $sql = "UPDATE tb_surat_peringatan SET status = 'dihapus' WHERE id_sp = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id_sp);
    
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Surat Peringatan berhasil dihapus!'); window.location.href='datakpi-adminhrd';</script>";
    } else {
        echo "<script>alert('Gagal menghapus Surat Peringatan!');</script>";
    }
}

// ========== FUNGSI DAN PRE-FETCHING OPTIMASI LOAD TIME ==========

function getkpi($nilair)
{
    if ($nilair < 90) {
        return "POOR";
    } elseif ($nilair <= 100) {
        return "GOOD";
    } elseif ($nilair <= 110) {
        return "Very Good";
    } else {
        return "Excellent";
    }
}

function calculateUserKPIFast($id_user, $kpi_by_user, $whats_sum, $hows_sum, $bobot_map, $active_sps) {
    $kpi_list = $kpi_by_user[$id_user] ?? [];
    $totalws = 0;
    $totalhfg = 0;
    foreach ($kpi_list as $kpi) {
        $kid = $kpi['id'];
        $w_tot = $whats_sum[$id_user][$kid] ?? 0;
        $totalws += ($w_tot * (float)$kpi['bobot']) / 100;

        $h_tot = $hows_sum[$id_user][$kid] ?? 0;
        $totalhfg += ($h_tot * (float)$kpi['bobot2']) / 100;
    }

    $bw = (float)($bobot_map[$id_user]['bobotwhat'] ?? 0);
    $bh = (float)($bobot_map[$id_user]['bobothow'] ?? 0);

    $zbotw = ($totalws * $bw) / 100;
    $zboth = ($totalhfg * $bh) / 100;
    $nilai_asli = $zboth + $zbotw;

    $sp_data = $active_sps[$id_user] ?? null;
    $pengurangan = 0;
    if ($sp_data) {
        $pengurangan = getSPPenalty($sp_data['jenis_sp']);
        $nilai_akhir = $nilai_asli - $pengurangan;
    } else {
        $nilai_akhir = $nilai_asli;
    }

    return [
        'what' => number_format($zbotw, 2),
        'how' => number_format($zboth, 2),
        'nilai_asli' => $nilai_asli,
        'nilai_akhir' => $nilai_akhir,
        'sp_data' => $sp_data,
        'pengurangan' => $pengurangan
    ];
}

function calculateUserKPISimFast($id_user, $sim_kpi_by_user, $sim_whats_sum, $sim_hows_sum, $sim_bobot_map) {
    $exists = isset($sim_kpi_by_user[$id_user]) && count($sim_kpi_by_user[$id_user]) > 0;
    $kpi_list = $sim_kpi_by_user[$id_user] ?? [];
    $totalws = 0;
    $totalhfg = 0;
    foreach ($kpi_list as $kpi) {
        $kid = $kpi['id'];
        $w_tot = $sim_whats_sum[$id_user][$kid] ?? 0;
        $totalws += ($w_tot * (float)$kpi['bobot']) / 100;

        $h_tot = $sim_hows_sum[$id_user][$kid] ?? 0;
        $totalhfg += ($h_tot * (float)$kpi['bobot2']) / 100;
    }

    $bw = (float)($sim_bobot_map[$id_user]['bobotwhat'] ?? 0);
    $bh = (float)($sim_bobot_map[$id_user]['bobothow'] ?? 0);

    $nilaiwhat = ($totalws * $bw) / 100;
    $nilaihow = ($totalhfg * $bh) / 100;

    return [
        'nilai_what' => number_format($nilaiwhat, 2),
        'nilai_how' => number_format($nilaihow, 2),
        'total_kpi' => number_format($nilaiwhat + $nilaihow, 2),
        'exists' => $exists
    ];
}

// 1. Bulk fetch active SPs
$today = date('Y-m-d');
$active_sps = [];
$res_sp_bulk = mysqli_query($conn, "SELECT * FROM tb_surat_peringatan 
    WHERE status = 'aktif' 
    AND masa_berlaku_mulai <= '$today' 
    AND masa_berlaku_selesai >= '$today'
    ORDER BY CASE jenis_sp WHEN 'SP3' THEN 1 WHEN 'SP2' THEN 2 WHEN 'SP1' THEN 3 END");
if ($res_sp_bulk) {
    while ($row = mysqli_fetch_assoc($res_sp_bulk)) {
        if (!isset($active_sps[$row['id_user']])) {
            $active_sps[$row['id_user']] = $row;
        }
    }
}

// 2. Bulk fetch verified info bulan ini
$bulan_ini = date('m/Y');
$verified_map = [];
$verifier_ids = [];
$res_ver_bulk = mysqli_query($conn, "SELECT * FROM tb_kpi_verified WHERE bulan = '$bulan_ini'");
if ($res_ver_bulk) {
    while ($row = mysqli_fetch_assoc($res_ver_bulk)) {
        $verified_map[$row['id_user']] = $row;
        $verifier_ids[$row['verified_by']] = true;
    }
}

$verifier_names = [];
if (!empty($verifier_ids)) {
    $ids_str = implode(',', array_map('intval', array_keys($verifier_ids)));
    $res_vf_names = mysqli_query($conn, "SELECT id, nama_lngkp FROM tb_users WHERE id IN ($ids_str)");
    if ($res_vf_names) {
        while ($row = mysqli_fetch_assoc($res_vf_names)) {
            $verifier_names[$row['id']] = $row['nama_lngkp'];
        }
    }
}

// 3. Bulk fetch bobot KPI real
$bobot_map = [];
$res_b_bulk = mysqli_query($conn, "SELECT id_user, bobotwhat, bobothow FROM tb_bobotkpi");
if ($res_b_bulk) {
    while ($row = mysqli_fetch_assoc($res_b_bulk)) {
        $bobot_map[$row['id_user']] = $row;
    }
}

// 4. Bulk fetch tb_whats aggregated
$whats_sum = [];
$res_w_bulk = mysqli_query($conn, "SELECT id_user, id_kpi, SUM(total) as total FROM tb_whats GROUP BY id_user, id_kpi");
if ($res_w_bulk) {
    while ($row = mysqli_fetch_assoc($res_w_bulk)) {
        $whats_sum[$row['id_user']][$row['id_kpi']] = (float)$row['total'];
    }
}

// 5. Bulk fetch tb_hows aggregated
$hows_sum = [];
$res_h_bulk = mysqli_query($conn, "SELECT id_user, id_kpi, SUM(total) as total FROM tb_hows GROUP BY id_user, id_kpi");
if ($res_h_bulk) {
    while ($row = mysqli_fetch_assoc($res_h_bulk)) {
        $hows_sum[$row['id_user']][$row['id_kpi']] = (float)$row['total'];
    }
}

// 6. Bulk fetch tb_kpi
$kpi_by_user = [];
$res_k_bulk = mysqli_query($conn, "SELECT id, id_user, bobot, bobot2 FROM tb_kpi");
if ($res_k_bulk) {
    while ($row = mysqli_fetch_assoc($res_k_bulk)) {
        $kpi_by_user[$row['id_user']][] = $row;
    }
}

// 7. Bulk fetch SIMULASI data
$sim_bobot_map = [];
$res_sb_bulk = mysqli_query($conn, "SELECT id_user, bobotwhat, bobothow FROM tbsim_bobotkpi");
if ($res_sb_bulk) {
    while ($row = mysqli_fetch_assoc($res_sb_bulk)) {
        $sim_bobot_map[$row['id_user']] = $row;
    }
}

$sim_whats_sum = [];
$res_sw_bulk = mysqli_query($conn, "SELECT id_user, id_kpi, SUM(total) as total FROM tbsim_whats GROUP BY id_user, id_kpi");
if ($res_sw_bulk) {
    while ($row = mysqli_fetch_assoc($res_sw_bulk)) {
        $sim_whats_sum[$row['id_user']][$row['id_kpi']] = (float)$row['total'];
    }
}

$sim_hows_sum = [];
$res_sh_bulk = mysqli_query($conn, "SELECT id_user, id_kpi, SUM(total) as total FROM tbsim_hows GROUP BY id_user, id_kpi");
if ($res_sh_bulk) {
    while ($row = mysqli_fetch_assoc($res_sh_bulk)) {
        $sim_hows_sum[$row['id_user']][$row['id_kpi']] = (float)$row['total'];
    }
}

$sim_kpi_by_user = [];
$res_sk_bulk = mysqli_query($conn, "SELECT id, id_user, bobot, bobot2 FROM tbsim_kpi");
if ($res_sk_bulk) {
    while ($row = mysqli_fetch_assoc($res_sk_bulk)) {
        $sim_kpi_by_user[$row['id_user']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="icon" type="image/svg+xml" href="assets/img/favicon.svg">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>Data KPI Karyawan - Admin HRD</title>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">

    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <style>
        .badge-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .header-page {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .sp-indicator {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            background: #dc3545;
            border-radius: 50%;
            border: 2px solid white;
            animation: pulse-sp 2s infinite;
        }
        
        @keyframes pulse-sp {
            0% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
            }
        }
        
        .btn-group-compact {
            display: flex;
            gap: 2px;
        }
        
        .nilai-sp-info {
            font-size: 0.75rem;
            margin-top: 2px;
        }

        .card-header.d-flex::after {
            display: none !important;
        }

        /* User Menu Dropdown Navbar */
        .navbar-nav .user-menu {
            position: relative;
        }
        .navbar-nav .user-menu .dropdown-menu {
            position: absolute !important;
            top: 100% !important;
            right: 0 !important;
            left: auto !important;
            transform: none !important;
        }
        .navbar-nav .user-menu .dropdown-menu.show {
            display: block !important;
            visibility: visible !important;
            opacity: 1 !important;
            z-index: 1060 !important;
            min-width: 90px !important;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    <?php $mode = isset($_GET['mode']) && $_GET['mode'] === 'simulasi' ? 'simulasi' : 'real'; ?>
                    
                    <!-- Header Page -->
                    <div class="header-page mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-2">
                                    <i class="bi bi-graph-up-arrow me-2"></i>Data KPI Karyawan
                                </h3>
                                <p class="mb-0 opacity-75">Monitoring dan evaluasi KPI seluruh karyawan</p>
                            </div>
                            <div class="text-end">
                                <a href="export_kpi_summary?mode=<?= $mode ?>" id="btnExportSummary" class="btn btn-success me-2 shadow-sm">
                                    <i class="bi bi-file-earmark-spreadsheet me-2"></i>Export Summary KPI
                                </a>
                                <a href="export_kpi_all_adminhrd.php" class="btn btn-outline-light me-2 shadow-sm">
                                    <i class="bi bi-file-earmark-excel me-2"></i>Export Semua KPI
                                </a>
                                <?php $back_url_kpi = (isset($_SESSION['level']) && $_SESSION['level'] == 7) ? 'dashboard-adminhrd' : 'data-karyawan'; ?>
                                <a href="<?= $back_url_kpi ?>" class="btn btn-light shadow-sm">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <div class="row align-items-center g-2">
                                <div class="col-md-3">
                                    <label class="form-label mb-1 fw-bold">
                                        <i class="bi bi-building"></i> Departemen
                                    </label>
                                    <select id="filterDepartemen" class="form-select form-select-sm">
                                        <option value="">Semua Departemen</option>
                                        <?php
                                        // Ambil list departemen
                                        $sql_dept = "SELECT DISTINCT u.departement 
                                                    FROM tb_users u
                                                    WHERE u.username != 'backdoor_admin' 
                                                    AND u.jabatan != 'Admin HRD'
                                                    ORDER BY u.departement ASC";
                                        $result_dept = mysqli_query($conn, $sql_dept);
                                        while($dept = mysqli_fetch_assoc($result_dept)) {
                                            echo "<option value='".$dept['departement']."'>".$dept['departement']."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label mb-1 fw-bold">
                                        <i class="bi bi-briefcase"></i> Jabatan
                                    </label>
                                    <select id="filterJabatan" class="form-select form-select-sm">
                                        <option value="">Semua Jabatan</option>
                                        <option value="Kadep">Kadep</option>
                                        <option value="Manager">Manager</option>
                                        <option value="Koordinator">Koordinator</option>
                                        <option value="Karyawan">Karyawan</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label mb-1 fw-bold">
                                        <i class="bi bi-star"></i> Status KPI
                                    </label>
                                    <select id="filterKPI" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="Excellent">Excellent</option>
                                        <option value="Very Good">Very Good</option>
                                        <option value="GOOD">GOOD</option>
                                        <option value="POOR">POOR</option>
                                    </select>
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="form-label mb-1 fw-bold">
                                        <i class="bi bi-exclamation-triangle"></i> Status SP
                                    </label>
                                    <select id="filterSP" class="form-select form-select-sm">
                                        <option value="">Semua</option>
                                        <option value="Ada SP">Memiliki SP Aktif</option>
                                        <option value="Tidak Ada SP">Tanpa SP</option>
                                    </select>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label mb-1 fw-bold">
                                        <i class="bi bi-person-check"></i> Status Karyawan
                                    </label>
                                    <select id="filterStatusKaryawan" class="form-select form-select-sm">
                                        <option value="">Semua Status</option>
                                        <option value="AKTIF">Hanya Aktif</option>
                                        <option value="NONAKTIF">Hanya Non Aktif</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="row mt-3">
                                <div class="col-12 text-end">
                                    <button id="resetFilter" class="btn btn-secondary btn-sm">
                                        <i class="bi bi-arrow-clockwise"></i> Reset Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table KPI -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-table me-2"></i>Tabel Data KPI Karyawan
                                <?php if ($mode === 'simulasi') { ?>
                                    <span class="badge bg-warning text-dark ms-2"><i class="bi bi-rocket-takeoff me-1"></i>Mode KPI Simulasi</span>
                                <?php } else { ?>
                                    <span class="badge bg-primary ms-2"><i class="bi bi-calendar-check me-1"></i>Mode KPI Real</span>
                                <?php } ?>
                            </h5>
                            <div class="card-tools ms-auto">
                                <div class="btn-group" role="group" aria-label="Mode KPI">
                                    <a href="datakpi-adminhrd?mode=real" class="btn btn-sm fw-bold <?= $mode !== 'simulasi' ? 'btn-primary active' : 'btn-outline-light' ?>">
                                        <i class="bi bi-calendar-check me-1"></i>KPI Real
                                    </a>
                                    <a href="datakpi-adminhrd?mode=simulasi" class="btn btn-sm fw-bold <?= $mode === 'simulasi' ? 'btn-warning text-dark active' : 'btn-outline-light' ?>">
                                        <i class="bi bi-rocket-takeoff me-1"></i>KPI Simulasi
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if ($mode === 'simulasi') { ?>
                                <div class="alert alert-warning border-0 shadow-sm d-flex justify-content-between align-items-center mb-3">
                                    <div>
                                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                                        <strong>Mode KPI Simulasi Aktif:</strong> Menampilkan data perencanaan dan proyeksi simulasi KPI karyawan.
                                    </div>
                                    <a href="datakpi-adminhrd?mode=real" class="btn btn-outline-dark btn-sm fw-bold">
                                        <i class="bi bi-arrow-left me-1"></i>Kembali ke KPI Real
                                    </a>
                                </div>
                            <?php } ?>
                            <div class="table-responsive">
                                <table id="datatablenya" class="table align-middle table-hover table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="3%"><center>No</center></th>
                                            <th><center>Nama Lengkap</center></th>
                                            <th width="12%"><center>Jabatan</center></th>
                                            <th width="12%"><center>Departemen</center></th>
                                            <th width="12%"><center>Bagian</center></th>
                                            <th width="8%"><center><?= $mode === 'simulasi' ? 'What (Sim)' : 'What' ?></center></th>
                                            <th width="8%"><center><?= $mode === 'simulasi' ? 'How (Sim)' : 'How' ?></center></th>
                                            <th width="8%"><center><?= $mode === 'simulasi' ? 'Nilai Sim' : 'Nilai' ?></center></th>
                                            <th width="10%"><center><?= $mode === 'simulasi' ? 'KPI Sim' : 'KPI' ?></center></th>
                                            <th width="12%"><center>Aksi</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
                                        $sqlhd = "SELECT u.*
                                        FROM tb_users u
                                        WHERE u.id != $id_user 
                                        AND u.jabatan != 'Admin HRD'
                                        AND u.username != 'itboy'
                                        ORDER BY 
                                            CASE 
                                                WHEN u.jabatan = 'Kadep' THEN 1
                                                WHEN u.jabatan = 'Koordinator' THEN 2
                                                WHEN u.jabatan = 'Manager' THEN 3
                                                WHEN u.jabatan = 'Karyawan' THEN 4
                                                ELSE 5
                                            END,
                                            u.nama_lngkp ASC";
                                        $sgdah = mysqli_query($conn, $sqlhd);
                                        
                                        while ($hasilsfa = mysqli_fetch_assoc($sgdah)) { 
                                            // Tentukan badge color berdasarkan jabatan
                                            $badge_color = 'secondary';
                                            $badge_icon = 'person-fill';
                                            
                                            if ($hasilsfa['jabatan'] == 'Kadep') {
                                                $badge_color = 'danger';
                                                $badge_icon = 'award-fill';
                                            } elseif ($hasilsfa['jabatan'] == 'Manager') {
                                                $badge_color = 'warning';
                                                $badge_icon = 'star-fill';
                                            } elseif ($hasilsfa['jabatan'] == 'Koordinator') {
                                                $badge_color = 'info';
                                                $badge_icon  = 'people-fill';
                                            } elseif ($hasilsfa['jabatan'] == 'Karyawan') {
                                                $badge_color = 'success';
                                                $badge_icon = 'person-check-fill';
                                            }
                                            
                                            // Cek SP aktif untuk user ini (menggunakan data pre-fetched)
                                            $sp_aktif_user = $active_sps[$hasilsfa['id']] ?? null;
                                            $kpi_verified_info = $verified_map[$hasilsfa['id']] ?? null;
                                        ?>
                                        <tr>
                                            <td class="text-center"><?= $no; ?></td>
                                            <td style="padding-left: 20px; position: relative;">
                                                <?php if ($sp_aktif_user) { ?>
                                                    <span class="sp-indicator" title="SP Aktif"></span>
                                                <?php } ?>
                                                <strong><?= $hasilsfa['nama_lngkp']; ?></strong>
                                                <?php if (($hasilsfa['status_karyawan'] ?? 'AKTIF') === 'NONAKTIF') { ?>
                                                    <span class="badge bg-danger ms-1" title="Karyawan Non Aktif">
                                                        <i class="bi bi-person-x-fill me-1"></i>Non Aktif
                                                    </span>
                                                <?php } ?>
                                                <?php if ($kpi_verified_info) { 
                                                    $verifier_name = $verifier_names[$kpi_verified_info['verified_by']] ?? 'Unknown';
                                                ?>
                                                    <span class="badge bg-success ms-1" title="Diverifikasi oleh <?= $verifier_name ?> pada <?= date('d/m/Y H:i', strtotime($kpi_verified_info['verified_at'])) ?>">
                                                        <i class="bi bi-check-circle-fill me-1"></i>Verified
                                                    </span>
                                                <?php } ?>
                                                <br>
                                                <small class="text-muted">NIK: <?= $hasilsfa['nik']; ?></small>
                                                <?php if ($sp_aktif_user) { ?>
                                                    <br>
                                                    <span class="badge bg-<?=getSPBadgeClass($sp_aktif_user['jenis_sp'])?> mt-1">
                                                        <i class="bi bi-exclamation-triangle-fill"></i> 
                                                        <?=$sp_aktif_user['jenis_sp']?> Aktif
                                                    </span>
                                                <?php } ?>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-<?= $badge_color ?>">
                                                    <i class="bi bi-<?= $badge_icon ?> me-1"></i>
                                                    <?= $hasilsfa['jabatan']; ?>
                                                </span>
                                            </td>
                                            <td data-search="<?= htmlspecialchars($hasilsfa['departement']) ?>" class="text-center"><?= $hasilsfa['departement']; ?></td>
                                            <td class="text-center"><?= $hasilsfa['bagian']; ?></td>
                                            <?php
                                            $dataSimulasi = calculateUserKPISimFast($hasilsfa['id'], $sim_kpi_by_user, $sim_whats_sum, $sim_hows_sum, $sim_bobot_map);
                                            $nilaiSimulasi = $dataSimulasi['total_kpi'];
                                            $whatSimulasi = $dataSimulasi['nilai_what'];
                                            $howSimulasi = $dataSimulasi['nilai_how'];
                                            $adaDataSimulasi = $dataSimulasi['exists'];

                                            $kpi_result = calculateUserKPIFast($hasilsfa['id'], $kpi_by_user, $whats_sum, $hows_sum, $bobot_map, $active_sps);
                                            $whatReal = $kpi_result['what'];
                                            $howReal = $kpi_result['how'];
                                            $nilai_asli = $kpi_result['nilai_asli'];
                                            $nilai_akhir = $kpi_result['nilai_akhir'];
                                            $sp_data = $kpi_result['sp_data'];
                                            $pengurangan = $kpi_result['pengurangan'];
                                            
                                            if ($nilai_akhir < 90) {
                                                $wrabs = "red";
                                                $badge_kpi = "danger";
                                            } elseif ($nilai_akhir <= 100) {
                                                $wrabs = "orange";
                                                $badge_kpi = "warning";
                                            } elseif ($nilai_akhir <= 110) {
                                                $wrabs = "green";
                                                $badge_kpi = "success";
                                            } else {
                                                $wrabs = "blue";
                                                $badge_kpi = "primary";
                                            }

                                            if ($adaDataSimulasi) {
                                                $valSim = (float)$nilaiSimulasi;
                                                if ($valSim < 90) {
                                                    $wrabs_sim = "red";
                                                    $badge_kpi_sim = "danger";
                                                } elseif ($valSim <= 100) {
                                                    $wrabs_sim = "orange";
                                                    $badge_kpi_sim = "warning";
                                                } elseif ($valSim <= 110) {
                                                    $wrabs_sim = "green";
                                                    $badge_kpi_sim = "success";
                                                } else {
                                                    $wrabs_sim = "blue";
                                                    $badge_kpi_sim = "primary";
                                                }
                                            } else {
                                                $wrabs_sim = "#6c757d";
                                                $badge_kpi_sim = "secondary";
                                            }
                                            ?>

                                            <?php if ($mode === 'simulasi') { ?>
                                                <td class="text-center"><strong><?= $adaDataSimulasi ? $whatSimulasi : '-' ?></strong></td>
                                                <td class="text-center"><strong><?= $adaDataSimulasi ? $howSimulasi : '-' ?></strong></td>
                                                <td class="text-center" style="color:<?= $wrabs_sim ?>">
                                                    <?php if ($adaDataSimulasi) { ?>
                                                        <strong><?= number_format((float)$nilaiSimulasi, 2); ?></strong>
                                                        <small class="badge bg-warning text-dark d-block mt-1">Simulasi</small>
                                                    <?php } else { ?>
                                                        <span class="text-muted fst-italic">Belum ada</span>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($adaDataSimulasi) { ?>
                                                        <span class="badge bg-<?= $badge_kpi_sim ?>">
                                                            <?= getkpi((float)$nilaiSimulasi); ?>
                                                        </span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-secondary">-</span>
                                                    <?php } ?>
                                                </td>
                                            <?php } else { ?>
                                                <td class="text-center"><strong><?= $whatReal; ?></strong></td>
                                                <td class="text-center"><strong><?= $howReal; ?></strong></td>
                                                <td class="text-center" style="color:<?= $wrabs ?>">
                                                    <strong><?= number_format($nilai_akhir, 2); ?></strong>
                                                    <?php if ($sp_data) { ?>
                                                        <div class="nilai-sp-info">
                                                            <small class="text-muted">
                                                                <del><?=number_format($nilai_asli, 2)?></del>
                                                            </small>
                                                            <small class="badge bg-<?=getSPBadgeClass($sp_data['jenis_sp'])?> d-block mt-1">
                                                                <?=$sp_data['jenis_sp']?> (-<?=$pengurangan?>)
                                                            </small>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($adaDataSimulasi) { ?>
                                                        <div class="mt-1">
                                                            <small class="badge bg-warning text-dark" title="Nilai KPI Simulasi">
                                                                <i class="bi bi-rocket-takeoff me-1"></i>Sim: <?= $nilaiSimulasi ?>
                                                            </small>
                                                        </div>
                                                    <?php } ?>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-<?= $badge_kpi ?>">
                                                        <?= getkpi($nilai_akhir); ?>
                                                    </span>
                                                </td>
                                            <?php } ?>

                                            <td class="text-center">
                                                <div class="btn-group-compact justify-content-center">
                                                    <!-- Tombol Lihat KPI Real -->
                                                    <a href="kpianggota?id=<?= $hasilsfa['id']; ?>&from=datakpi-adminhrd" 
                                                       class="btn btn-primary btn-sm" 
                                                       title="Lihat KPI Real">
                                                        <i class="bi bi-eye"></i>
                                                    </a>

                                                    <!-- Tombol Lihat KPI Simulasi -->
                                                    <a href="home-kpi-simulasi?id=<?= $hasilsfa['id']; ?>&from=datakpi-adminhrd" 
                                                       class="btn btn-warning btn-sm" 
                                                       title="Lihat KPI Simulasi">
                                                        <i class="bi bi-clipboard-data"></i>
                                                    </a>

                                                    <a href="<?= $mode === 'simulasi' ? 'export_kpisim_detail.php' : 'export_kpi_detail.php' ?>?id=<?= $hasilsfa['id']; ?>"
                                                       class="btn btn-success btn-sm" title="Export Excel Detail <?= $mode === 'simulasi' ? 'Simulasi' : '' ?>">
                                                        <i class="bi bi-file-earmark-excel fs-8"></i>
                                                    </a>
                                                    
                                                    <!-- Tombol Tambah SP -->
                                                    <button type="button" 
                                                            class="btn btn-warning btn-sm btn-modal-tambah-sp" 
                                                            data-id="<?=$hasilsfa['id']?>"
                                                            data-nama="<?=htmlspecialchars($hasilsfa['nama_lngkp'])?>"
                                                            data-nik="<?=htmlspecialchars($hasilsfa['nik'])?>"
                                                            data-departemen="<?=htmlspecialchars($hasilsfa['departement'] ?? '')?>"
                                                            data-jabatan="<?=htmlspecialchars($hasilsfa['jabatan'] ?? '')?>"
                                                            title="Tambah Surat Peringatan">
                                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                                    </button>
                                                    
                                                    <!-- Tombol Kelola SP -->
                                                    <button type="button" 
                                                            class="btn btn-info btn-sm btn-modal-kelola-sp" 
                                                            data-id="<?=$hasilsfa['id']?>"
                                                            data-nama="<?=htmlspecialchars($hasilsfa['nama_lngkp'])?>"
                                                            title="Kelola Surat Peringatan">
                                                        <i class="bi bi-file-earmark-text"></i>
                                                        <?php if ($sp_aktif_user) { ?>
                                                            <span class="badge bg-danger rounded-circle" style="padding: 2px 5px; font-size: 0.6rem;">!</span>
                                                        <?php } ?>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php 
                                            $no++;
                                        } 
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <?php
                    // Include modal reusable tunggal di luar tabel (ringan & instan)
                    include ("pages/adminhrd/modal_tambah_sp.php");
                    include ("pages/adminhrd/modal_kelola_sp.php");
                    ?>

                </div>
            </div>
        </main>
        
        <?php include("pages/part/p_footeradminhrd.php"); ?>

    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        // Toggle User Menu Dropdown Navbar
        $(document).on('click', '.user-menu .dropdown-toggle', function(e) {
            e.preventDefault();
            e.stopPropagation();
            var $menu = $(this).closest('.user-menu').find('.dropdown-menu');
            $menu.toggleClass('show');
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.user-menu').length) {
                $('.user-menu .dropdown-menu').removeClass('show');
            }
        });

        // ==========================================
        // Modal Handlers (Reusable Dynamic Modals)
        // ==========================================
        $(document).on('click', '.btn-modal-tambah-sp', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            var nik = $(this).data('nik');
            var departemen = $(this).data('departemen') || '-';
            var jabatan = $(this).data('jabatan') || '-';
            
            $('#tambahSP_id_user').val(id);
            $('#tambahSP_nama').text(nama);
            $('#tambahSP_nik').text(nik);
            $('#tambahSP_departemen').text(departemen);
            $('#tambahSP_jabatan').text(jabatan);
            
            // Set ke Preview juga
            $('#prev_nama').text(nama);
            $('#prev_departemen').text(departemen);
            $('#prev_jabatan').text(jabatan);
            $('#prev_karyawan_ttd').text(nama);
            
            // Reset form fields
            $('#formTambahSP')[0].reset();
            $('#tambahSP_id_user').val(id);
            $('#tambahSP_tanggal').val('<?=date("Y-m-d")?>');
            $('#tambahSP_tgl_kejadian').val('<?=date("Y-m-d")?>');
            $('#tambahSP_penandatangan').val('Riza Dwi Fitrianingtyas');
            $('#tambahSP_jabatan_penandatangan').val('Kepala Departemen HRD');
            $('#tambahSP_tembusan').val('1. Direktur sebagai laporan; 2. Kepala Departemen HRD; 3. Arsip;');
            setDefaultPasal();
            $('#tambahSP_penaltyInfo').html('');
            $('#tambahSP_filePreview').html('');
            handleSPMasaBerlakuChange();
            
            // Generate saran nomor otomatis
            autoGenerateSPNomor();
            
            // Aktifkan tab pertama
            var firstTab = new bootstrap.Tab(document.querySelector('#form-tab'));
            firstTab.show();
            $('#modalTambahSP .modal-body').scrollTop(0);
            
            var modal = new bootstrap.Modal(document.getElementById('modalTambahSP'));
            modal.show();
        });

        $(document).on('click', '.btn-modal-kelola-sp', function() {
            var id = $(this).data('id');
            var nama = $(this).data('nama');
            
            $('#kelolaSP_nama').text(nama);
            $('#kelolaSP_content').html(`
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2 text-muted">Memuat data surat peringatan...</p>
                </div>
            `);
            
            var modal = new bootstrap.Modal(document.getElementById('modalKelolaSP'));
            modal.show();
            
            $.ajax({
                url: 'datakpi-adminhrd?ajax_sp_list=1&id_user=' + id,
                type: 'GET',
                success: function(response) {
                    $('#kelolaSP_content').html(response);
                },
                error: function() {
                    $('#kelolaSP_content').html(`
                        <div class="alert alert-danger text-center my-3">
                            <i class="bi bi-exclamation-triangle fs-3"></i>
                            <p class="mb-0 mt-2">Gagal memuat data surat peringatan. Silakan coba lagi.</p>
                        </div>
                    `);
                }
            });
        });

        function handleSPPenaltyChange() {
            const jenisSP = document.getElementById('tambahSP_jenis').value;
            const infoDiv = document.getElementById('tambahSP_penaltyInfo');
            
            const penalties = {
                'SP1': { poin: 2, class: 'warning', icon: 'exclamation-circle' },
                'SP2': { poin: 3.5, class: 'danger', icon: 'exclamation-triangle' },
                'SP3': { poin: 5, class: 'dark', icon: 'x-octagon' }
            };
            
            if (jenisSP && penalties[jenisSP]) {
                const p = penalties[jenisSP];
                infoDiv.innerHTML = `
                    <div class="alert alert-${p.class} py-2 mb-0">
                        <i class="bi bi-${p.icon}-fill me-1"></i> 
                        <strong>Dampak:</strong> Nilai KPI akan dikurangi <strong>${p.poin} poin</strong> selama 6 bulan masa berlaku SP
                    </div>
                `;
            } else {
                infoDiv.innerHTML = '';
            }
        }

        function handleSPMasaBerlakuChange() {
            const tanggalSP = document.getElementById('tambahSP_tanggal').value;
            if (tanggalSP) {
                const startDate = new Date(tanggalSP);
                const endDate = new Date(tanggalSP);
                endDate.setMonth(endDate.getMonth() + 6);
                
                const startDisplay = startDate.toLocaleDateString('id-ID');
                const endDisplay = endDate.toLocaleDateString('id-ID');
                
                document.getElementById('tambahSP_displayMulai').textContent = startDisplay;
                document.getElementById('tambahSP_displaySelesai').textContent = endDisplay;
            }
        }

        function handleSPFilePreview() {
            const fileInput = document.getElementById('tambahSP_file');
            const previewDiv = document.getElementById('tambahSP_filePreview');
            if (!fileInput || !previewDiv) return;
            
            if (fileInput.files && fileInput.files.length > 0) {
                const file = fileInput.files[0];
                const fileSize = (file.size / 1024 / 1024).toFixed(2);
                const fileExt = file.name.split('.').pop().toLowerCase();
                
                let icon = 'file-earmark';
                if (fileExt === 'pdf') icon = 'file-earmark-pdf';
                else if (['jpg', 'jpeg', 'png'].includes(fileExt)) icon = 'file-earmark-image';
                
                previewDiv.innerHTML = `
                    <div class="alert alert-success py-2 mb-0 small">
                        <i class="bi bi-${icon}-fill me-1"></i> 
                        <strong>${file.name}</strong> (${fileSize} MB) siap diunggah sebagai arsip lampiran.
                    </div>
                `;
            } else {
                previewDiv.innerHTML = '';
            }
        }

        function getRomanMonthJS(monthIdx) {
            const romans = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
            return romans[monthIdx] || 'I';
        }

        // Nomor SP selanjutnya dari DB
        const nextSpPrefix = <?= $next_sp_prefix ?>;

        function autoGenerateSPNomor() {
            const tgl = document.getElementById('tambahSP_tanggal').value || '<?=date("Y-m-d")?>';
            const d = new Date(tgl);
            const romanM = getRomanMonthJS(d.getMonth());
            const year = d.getFullYear();
            
            $('#tambahSP_nomor').val(`${nextSpPrefix}/KIU-HRD/${romanM}/${year}`);
            updateLiveSPPreview();
        }

        function setDefaultPasal() {
            $('#tambahSP_aturan').val('Peraturan Perusahaan Pasal 22 ayat (2) point 23 :\nTidak berhati-hati dan / atau lalai dalam melaksanakan tugas sehingga dapat mengakibatkan kerugiaan bagi perusahaan');
            updateLiveSPPreview();
        }

        function updateLiveSPPreview() {
            const nomor = $('#tambahSP_nomor').val() || '[Nomor SP Belum Diisi]';
            const jenis = $('#tambahSP_jenis').val() || 'SP1';
            const tglSP = $('#tambahSP_tanggal').val();
            const tglKejadian = $('#tambahSP_tgl_kejadian').val();
            const aturan = $('#tambahSP_aturan').val() || '-';
            const alasan = $('#tambahSP_alasan').val() || '';
            const alasan2 = $('#tambahSP_alasan_2').val() || '';
            const penandatangan = $('#tambahSP_penandatangan').val() || 'Riza Dwi Fitrianingtyas';
            const jabatanPenandatangan = $('#tambahSP_jabatan_penandatangan').val() || 'Kepala Departemen HRD';
            const tembusan = $('#tambahSP_tembusan').val() || '1. Direktur sebagai laporan; 2. Kepala Departemen HRD; 3. Arsip;';

            $('#prev_nomor').text(nomor);
            $('#prev_penandatangan').text(penandatangan);
            $('#prev_jabatan_penandatangan').text(jabatanPenandatangan);
            $('#prev_tembusan').text(tembusan);

            if (tglSP) {
                const d = new Date(tglSP);
                $('#prev_tgl_sp').text(d.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));
            }
            if (tglKejadian) {
                const dk = new Date(tglKejadian);
                $('#prev_tgl_kejadian').text(dk.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }));
            }

            // Bersihkan escape literal \r\n jika ada
            const cleanAturan = aturan.replace(/\\r\\n/g, '\n').replace(/\\n/g, '\n').replace(/\\r/g, '\n');
            const lines = cleanAturan.split('\n');
            if (lines.length > 1) {
                $('#prev_aturan').text(lines[0].trim());
                $('#prev_aturan_detail').text(lines.slice(1).join(' ').trim());
            } else {
                $('#prev_aturan').text(cleanAturan.trim());
                $('#prev_aturan_detail').text('');
            }

            const cleanAlasan = alasan.replace(/\\r\\n/g, '\n').replace(/\\n/g, '\n').replace(/\\r/g, '\n');
            if (cleanAlasan.trim().length > 0) {
                $('#prev_alasan').html(cleanAlasan.trim().replace(/\n/g, '<br>')).removeClass('text-danger fst-italic');
            } else {
                $('#prev_alasan').text('[Uraian pelanggaran 1 belum diisi]').addClass('text-danger fst-italic');
            }

            const cleanAlasan2 = alasan2.replace(/\\r\\n/g, '\n').replace(/\\n/g, '\n').replace(/\\r/g, '\n');
            if (cleanAlasan2.trim().length > 0) {
                $('#prev_alasan_2').html(cleanAlasan2.trim().replace(/\n/g, '<br>')).removeClass('text-danger fst-italic');
                $('#prev_alasan_2_container').show();
            } else {
                $('#prev_alasan_2_container').hide();
            }

            let title = 'Surat Peringatan Pertama (SP-1)';
            let ketentuan = 'Surat Peringatan Pertama (SP-1) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan. Apabila saudara kembali melakukan tindakan pelanggaran dalam kurun waktu 6 (enam) bulan kedepan sejak Surat Peringatan Pertama (SP-1) ini diterbitkan, maka perusahaan akan memberikan sanksi yang lebih tegas kepada saudara.';
            
            if (jenis === 'SP2') {
                title = 'Surat Peringatan Kedua (SP-2)';
                ketentuan = 'Surat Peringatan Kedua (SP-2) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan. Apabila saudara kembali melakukan tindakan pelanggaran dalam kurun waktu 6 (enam) bulan kedepan sejak Surat Peringatan Kedua (SP-2) ini diterbitkan, maka perusahaan akan memberikan sanksi Surat Peringatan Ketiga (SP-3) atau sanksi yang lebih tegas kepada saudara.';
            } else if (jenis === 'SP3') {
                title = 'Surat Peringatan Ketiga (SP-3)';
                ketentuan = 'Surat Peringatan Ketiga (SP-3) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan. Apabila saudara kembali melakukan tindakan pelanggaran dalam kurun waktu 6 (enam) bulan kedepan sejak Surat Peringatan Ketiga (SP-3) ini diterbitkan, maka perusahaan akan memberikan sanksi pemutusan hubungan kerja (PHK) sesuai ketentuan peraturan perundang-undangan dan peraturan perusahaan yang berlaku.';
            }

            $('#prev_sp_title').text(title);
            $('#prev_ketentuan').text(ketentuan);
        }

        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#datatablenya').DataTable({
                "responsive": true,
                "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                    '<"row"<"col-sm-12"tr>>' +
                    '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                "language": {
                    "search": "Cari:",
                    "lengthMenu": "Tampilkan _MENU_ data per halaman",
                    "info": "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    "infoEmpty": "Menampilkan 0 sampai 0 dari 0 data",
                    "infoFiltered": "(difilter dari _MAX_ total data)",
                    "paginate": {
                        "first": "Pertama",
                        "last": "Terakhir",
                        "next": "Selanjutnya",
                        "previous": "Sebelumnya"
                    },
                    "zeroRecords": "Data tidak ditemukan"
                },
                "pageLength": 10,
                "order": [[0, 'asc']],
                "columnDefs": [
                    { "orderable": false, "targets": 9 } // Kolom aksi tidak bisa diurutkan
                ]
            });
            
            $('#filterDepartemen').on('change', function() {
                var dept = $(this).val();
                if (dept) {
                    var escapedDept = $.fn.dataTable.util.escapeRegex(dept);
                    table.column(3).search('^' + escapedDept + '$', true, false).draw();
                } else {
                    table.column(3).search('', false, false).draw();
                }
            });

            // Filter Jabatan
            $('#filterJabatan').on('change', function() {
                var jabatan = $(this).val();
                if (jabatan) {
                    var escapedJabatan = $.fn.dataTable.util.escapeRegex(jabatan);
                    table.column(2).search('^' + escapedJabatan + '$', true, false).draw();
                } else {
                    table.column(2).search('', false, false).draw();
                }
            });
            
            // Filter Status KPI
            $('#filterKPI').on('change', function() {
                var kpi = $(this).val();
                table.column(8).search(kpi).draw(); // Kolom 8 = KPI
            });
            
            // Filter Status SP
            $('#filterSP').on('change', function() {
                var sp = $(this).val();
                if (sp === 'Ada SP') {
                    table.column(1).search('SP.*Aktif', true, false).draw();
                } else if (sp === 'Tidak Ada SP') {
                    table.column(1).search('^((?!SP.*Aktif).)*$', true, false).draw();
                } else {
                    table.column(1).search('').draw();
                }
            });

            // Filter Status Karyawan
            $('#filterStatusKaryawan').on('change', function() {
                var stat = $(this).val();
                if (stat === 'NONAKTIF') {
                    table.column(1).search('Non Aktif', true, false).draw();
                } else if (stat === 'AKTIF') {
                    table.column(1).search('^((?!Non Aktif).)*$', true, false).draw();
                } else {
                    table.column(1).search('').draw();
                }
            });
            
            // Dynamic Export URL sesuai filter aktif & mode
            function updateExportUrl() {
                var params = new URLSearchParams();
                var currentMode = '<?= $mode ?>';
                if (currentMode) params.set('mode', currentMode);
                var dept = $('#filterDepartemen').val();
                var jab = $('#filterJabatan').val();
                var kpi = $('#filterKPI').val();
                var sp = $('#filterSP').val();
                var statKaryawan = $('#filterStatusKaryawan').val();
                if (dept) params.set('departemen', dept);
                if (jab) params.set('jabatan', jab);
                if (kpi) params.set('status_kpi', kpi);
                if (sp) params.set('status_sp', sp);
                if (statKaryawan) params.set('status_karyawan', statKaryawan);
                var qs = params.toString();
                $('#btnExportSummary').attr('href', 'export_kpi_summary' + (qs ? '?' + qs : ''));
            }

            $('#filterDepartemen, #filterJabatan, #filterKPI, #filterSP, #filterStatusKaryawan').on('change', updateExportUrl);

            // Reset Filter
            $('#resetFilter').on('click', function() {
                $('#filterDepartemen').val('');
                $('#filterJabatan').val('');
                $('#filterKPI').val('');
                $('#filterSP').val('');
                $('#filterStatusKaryawan').val('');
                table.search('').columns().search('').draw();
                updateExportUrl();
            });
        });
    </script>
</body>
</html>
