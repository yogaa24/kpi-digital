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
            padding: 5px 10px;
            border-radius: 5px;
            font-weight: bold;
        }
        
        .header-admin {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.15) !important;
        }
        
        .menu-card {
            transition: all 0.3s ease;
            cursor: pointer;
            border: none;
            overflow: hidden;
        }
        
        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.2) !important;
        }
        
        .menu-card .icon-wrapper {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            transition: all 0.3s ease;
        }
        
        .menu-card:hover .icon-wrapper {
            transform: scale(1.1);
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
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-2">
                                    <i class="bi bi-shield-fill-check me-2"></i>Dashboard Admin HRD
                                </h3>
                                <p class="mb-0 opacity-75">Monitoring KPI Seluruh Karyawan</p>
                            </div>
                            <div class="text-end">
                                <span class="badge-admin">
                                    <i class="bi bi-person-badge-fill me-2"></i><?= $nama_lngkp ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Menu Cards -->
                    <div class="row mb-4">
                        <!-- Data User -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm menu-card h-100"
                                onclick="window.location.href='datauser-adminhrd'">
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-people-fill text-info" style="font-size:2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Data User</h5>
                                    <p class="text-muted mb-0 small">Kelola data pengguna sistem</p>
                                </div>
                            </div>
                        </div>

                        <!-- Data KPI Karyawan -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm menu-card h-100"
                                onclick="window.location.href='datakpi-adminhrd'">
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-graph-up-arrow text-success" style="font-size:2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Data KPI Karyawan</h5>
                                    <p class="text-muted mb-0 small">Monitoring KPI seluruh karyawan</p>
                                </div>
                            </div>
                        </div>

                        <!-- Archive -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm menu-card h-100"
                                onclick="window.location.href='archive-adminhrd'">
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-archive-fill text-warning" style="font-size:2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Archive</h5>
                                    <p class="text-muted mb-0 small">Arsip dokumen & data historis</p>
                                </div>
                            </div>
                        </div>

                        <!-- Eviden -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm menu-card h-100"
                                onclick="window.location.href='eviden-adminhrd'">
                                <div class="card-body text-center p-4">
                                    <div class="icon-wrapper mb-3">
                                        <i class="bi bi-folder-fill text-danger" style="font-size:2.5rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Eviden</h5>
                                    <p class="text-muted mb-0 small">Dokumentasi bukti & evidensi</p>
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
                                        SUM(CASE WHEN u.jabatan = 'Kabag' THEN 1 ELSE 0 END) as total_kabag,
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
                        ?>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Total Karyawan</h6>
                                            <h3 class="fw-bold mb-0"><?=$stats['total_karyawan']?></h3>
                                        </div>
                                        <div class="text-primary">
                                            <i class="bi bi-people-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Kadep</h6>
                                            <h3 class="fw-bold mb-0"><?=$stats['total_kadep']?></h3>
                                        </div>
                                        <div class="text-danger">
                                            <i class="bi bi-award-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">Kabag</h6>
                                            <h3 class="fw-bold mb-0"><?=$stats['total_kabag']?></h3>
                                        </div>
                                        <div class="text-warning">
                                            <i class="bi bi-star-fill" style="font-size: 2.5rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 border-danger" style="border-width: 2px !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="text-muted mb-1">SP Aktif</h6>
                                            <h3 class="fw-bold mb-0 text-danger"><?=$sp_aktif_count?></h3>
                                        </div>
                                        <div class="text-danger">
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
                            <div class="card shadow-sm border-primary" style="border-width: 2px;">
                                <div class="card-body text-center py-4">
                                    <i class="bi bi-info-circle-fill text-primary mb-2" style="font-size: 3rem;"></i>
                                    <h5 class="fw-bold mb-2">Selamat Datang di Dashboard Admin HRD</h5>
                                    <p class="text-muted mb-3">
                                        Kelola data karyawan, monitoring KPI, dan administrasi HRD dengan mudah melalui menu di atas.
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-lightbulb-fill text-warning me-1"></i>
                                        Tip: Klik <strong>"Data KPI Karyawan"</strong> untuk melihat detail KPI seluruh karyawan
                                    </p>
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