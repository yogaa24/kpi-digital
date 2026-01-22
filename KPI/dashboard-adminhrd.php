<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/checkAdmin.php';
    require 'helper/sp_functions.php';

    // Hanya Admin HRD yang bisa akses
    requireAdminHRD();
    
    // Update SP yang sudah expired
    updateExpiredSP($conn);
    
    // ========== HANDLER UNTUK SURAT PERINGATAN ==========
    
    // Handler untuk tambah SP
    if (isset($_POST['tambah_sp'])) {
        $id_user_sp = intval($_POST['id_user']);
        $jenis_sp = mysqli_real_escape_string($conn, $_POST['jenis_sp']);
        $nomor_sp = mysqli_real_escape_string($conn, $_POST['nomor_sp']);
        $tanggal_sp = mysqli_real_escape_string($conn, $_POST['tanggal_sp']);
        $masa_berlaku_mulai = mysqli_real_escape_string($conn, $_POST['masa_berlaku_mulai']);
        $masa_berlaku_selesai = mysqli_real_escape_string($conn, $_POST['masa_berlaku_selesai']);
        $alasan = mysqli_real_escape_string($conn, $_POST['alasan']);
        $keterangan = mysqli_real_escape_string($conn, $_POST['keterangan']);
        $created_by = $_SESSION['id_user'];
        
        // Validasi tanggal
        if ($masa_berlaku_selesai < $masa_berlaku_mulai) {
            echo "<script>alert('Tanggal selesai tidak boleh lebih kecil dari tanggal mulai!');</script>";
        } else {
            $sql = "INSERT INTO tb_surat_peringatan 
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
    
    // Handler untuk hapus SP
    if (isset($_POST['hapus_sp'])) {
        $id_sp = intval($_POST['id_sp']);
        
        $sql = "UPDATE tb_surat_peringatan SET status = 'dihapus' WHERE id_sp = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "i", $id_sp);
        
        if (mysqli_stmt_execute($stmt)) {
            echo "<script>alert('Surat Peringatan berhasil dihapus!'); window.location.href='dashboard-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal menghapus Surat Peringatan!');</script>";
        }
    }
}

?>

<html lang="en">

<head>
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
    
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
        
        .menu-card:hover .icon-wrapper {
            transform: scale(1.1) rotate(5deg);
        }
        
        .icon-bg-info {
            background: linear-gradient(135deg, #e0f2fe 0%, #bae6fd 100%);
        }
        
        .icon-bg-success {
            background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);
        }
        
        .icon-bg-warning {
            background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        }
        
        .icon-bg-danger {
            background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        }
        
        .icon-bg-purple {
            background: linear-gradient(135deg, #f3e8ff 0%, #e9d5ff 100%);
        }
        
        .icon-bg-primary {
            background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        }
        
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
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper icon-bg-info">
                                        <i class="bi bi-people-fill text-info" style="font-size:2.2rem;"></i>
                                    </div>
                                    <h5 class="card-title">Data User</h5>
                                    <p class="card-text">Kelola data pengguna sistem</p>
                                </div>
                            </div>
                        </div>

                        <!-- Data KPI Karyawan -->
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card shadow-sm menu-card" onclick="window.location.href='datakpi-adminhrd'">
                                <div class="card-body text-center p-4">
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
                                <div class="card-body text-center p-4">
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
                                <div class="card-body text-center p-4">
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
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper icon-bg-purple">
                                        <i class="bi bi-lock-fill text-purple" style="font-size:2.2rem; color: #9333ea;"></i>
                                    </div>
                                    <h5 class="card-title">Lock KPI Settings</h5>
                                    <p class="card-text">Atur periode & akses pengisian KPI</p>
                                </div>
                            </div>
                        </div>

                        <!-- Skill Standard - NEW -->
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-4">
                            <div class="card shadow-sm menu-card" onclick="window.location.href='skill-standard-adminhrd'">
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper icon-bg-primary">
                                        <i class="bi bi-award-fill text-primary" style="font-size:2.2rem;"></i>
                                    </div>
                                    <h5 class="card-title">Skill Standard</h5>
                                    <p class="card-text">Kelola standar kompetensi karyawan</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Summary -->
                    <div class="row mb-4">
                        <?php
                        // Hitung statistik
                        $sql_stats = "SELECT 
                                        COUNT(*) as total_karyawan,
                                        SUM(CASE WHEN u.jabatan = 'Kadep' THEN 1 ELSE 0 END) as total_kadep,
                                        SUM(CASE WHEN u.jabatan = 'Koordinator' THEN 1 ELSE 0 END) as total_Koordinator,
                                        SUM(CASE WHEN u.jabatan = 'Manager' THEN 1 ELSE 0 END) as total_manager,
                                        SUM(CASE WHEN u.jabatan = 'Karyawan' THEN 1 ELSE 0 END) as total_karyawan_biasa
                                      FROM tb_users u
                                      INNER JOIN tb_auth a ON u.id = a.id_user
                                      WHERE u.id != $id_user AND u.jabatan != 'Admin HRD'";
                        $result_stats = mysqli_query($conn, $sql_stats);
                        $stats = mysqli_fetch_assoc($result_stats);
                        
                        // Hitung SP aktif
                        $today = date('Y-m-d');
                        $sql_sp_aktif = "SELECT COUNT(*) as total_sp_aktif 
                                         FROM tb_surat_peringatan 
                                         WHERE status = 'aktif' 
                                         AND masa_berlaku_mulai <= '$today' 
                                         AND masa_berlaku_selesai >= '$today'";
                        $result_sp_aktif = mysqli_query($conn, $sql_sp_aktif);
                        $sp_aktif_count = mysqli_fetch_assoc($result_sp_aktif)['total_sp_aktif'];
                        
                        // Hitung periode lock yang aktif hari ini
                        $sql_lock_aktif = "SELECT COUNT(*) as total_lock 
                                        FROM tb_kpi_lock_settings 
                                        WHERE status = 'aktif' 
                                        AND tanggal_mulai <= '$today' 
                                        AND tanggal_selesai >= '$today'";
                        $result_lock = mysqli_query($conn, $sql_lock_aktif);
                        $lock_aktif = mysqli_fetch_assoc($result_lock)['total_lock'];

                        // Ambil info periode aktif
                        $sql_periode_aktif = "SELECT nama_periode, level_akses 
                                            FROM tb_kpi_lock_settings 
                                            WHERE status = 'aktif' 
                                            AND tanggal_mulai <= '$today' 
                                            AND tanggal_selesai >= '$today'
                                            ORDER BY created_at DESC
                                            LIMIT 1";
                        $result_periode = mysqli_query($conn, $sql_periode_aktif);
                        $periode_aktif = mysqli_fetch_assoc($result_periode);
                        ?>

                        <!-- Status Lock KPI -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card shadow-sm <?= $lock_aktif > 0 ? 'border-warning' : 'border-success' ?>" style="border-width: 2px !important;">
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
                                            <i class="bi bi-<?= $lock_aktif > 0 ? 'lock-fill' : 'unlock-fill' ?>" style="font-size: 2.5rem; opacity: 0.8;"></i>
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
                                        <div class="text-primary" style="opacity: 0.8;">
                                            <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
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
                                        <div class="text-danger" style="opacity: 0.8;">
                                            <i class="bi bi-award-fill" style="font-size: 2.5rem;"></i>
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
                                        <div class="text-warning" style="opacity: 0.8;">
                                            <i class="bi bi-star-fill" style="font-size: 2.5rem;"></i>
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
                                            <h3 class="fw-bold mb-0" style="color: #6366f1;"><?=$stats['total_manager']?></h3>
                                            <small class="text-muted">Level manager</small>
                                        </div>
                                        <div style="opacity: 0.8; color: #6366f1;">
                                            <i class="bi bi-person-badge-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- SP Aktif -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card stat-card shadow-sm border-danger" style="border-width: 2px !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1 small">Surat Peringatan</h6>
                                            <h3 class="fw-bold mb-0 text-danger"><?=$sp_aktif_count?></h3>
                                            <small class="text-muted">SP Aktif</small>
                                        </div>
                                        <div class="text-danger" style="opacity: 0.8;">
                                            <i class="bi bi-exclamation-triangle-fill" style="font-size: 2.5rem;"></i>
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
                                    <i class="bi bi-info-circle-fill text-primary mb-3" style="font-size: 3.5rem;"></i>
                                    <h4 class="fw-bold mb-3" style="color: #1e40af;">Selamat Datang di Dashboard Admin HRD</h4>
                                    <p class="text-muted mb-4 mx-auto" style="max-width: 700px;">
                                        Kelola data karyawan, monitoring KPI, standar kompetensi, dan administrasi HRD dengan mudah melalui menu yang tersedia di atas.
                                    </p>
                                    <div class="d-flex justify-content-center align-items-center gap-2">
                                        <i class="bi bi-lightbulb-fill text-warning" style="font-size: 1.2rem;"></i>
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
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/datatables/datatables.min.js"></script>
</body>
</html>