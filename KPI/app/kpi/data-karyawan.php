<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/checkAdmin.php';

// Hanya Admin HRD atau Direktur / ID 1 (Diana Wulandari) yang bisa mengakses
requireAdminHRDOrDirektur();

// Hitung statistik ringkas untuk overview Direktur
$total_karyawan_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM tb_users WHERE username NOT IN ('itboy', 'adminhrd', 'backdoor_admin')");
$total_karyawan = ($total_karyawan_res) ? (mysqli_fetch_assoc($total_karyawan_res)['total'] ?? 0) : 0;

$total_dept_res = mysqli_query($conn, "SELECT COUNT(DISTINCT departement) as total FROM tb_users WHERE departement IS NOT NULL AND departement != '' AND username NOT IN ('itboy', 'adminhrd')");
$total_dept = ($total_dept_res) ? (mysqli_fetch_assoc($total_dept_res)['total'] ?? 0) : 0;

$periode_aktif = date('F Y');
?>

<html lang="id">
<?php include("pages/part/p_header.php"); ?>

<style>
    .header-hero {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
        color: white;
        border-radius: 16px;
        padding: 28px;
        margin-bottom: 25px;
        box-shadow: 0 10px 25px rgba(30, 60, 114, 0.2);
    }
    .badge-role {
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(8px);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 6px 14px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    .menu-hub-card {
        border-radius: 16px;
        border: none;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        cursor: pointer;
        height: 100%;
        background: #ffffff;
        box-shadow: 0 4px 15px rgba(0,0,0,0.06);
        position: relative;
        overflow: hidden;
    }
    .menu-hub-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 16px 32px rgba(0,0,0,0.12) !important;
    }
    .menu-hub-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
    }
    .card-kpi::before { background: linear-gradient(90deg, #10b981, #059669); }
    .card-archive::before { background: linear-gradient(90deg, #f59e0b, #d97706); }
    .card-skill::before { background: linear-gradient(90deg, #3b82f6, #1d4ed8); }
    .card-karakter::before { background: linear-gradient(90deg, #8b5cf6, #ec4899); }

    .icon-box {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.2rem;
        margin-bottom: 20px;
        transition: transform 0.3s ease;
    }
    .menu-hub-card:hover .icon-box {
        transform: scale(1.1) rotate(4deg);
    }
    .icon-kpi { background: #d1fae5; color: #059669; }
    .icon-archive { background: #fef3c7; color: #d97706; }
    .icon-skill { background: #dbeafe; color: #1d4ed8; }
    .icon-karakter { background: #f3e8ff; color: #7c3aed; }

    .card-title-hub {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
    }
    .card-desc-hub {
        color: #64748b;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-bottom: 22px;
    }
    .btn-action-hub {
        border-radius: 10px;
        font-weight: 600;
        padding: 9px 20px;
        font-size: 0.9rem;
        transition: all 0.2s;
    }
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 255, 255, 0.15);
        padding: 6px 16px;
        border-radius: 30px;
        font-size: 0.85rem;
    }
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_utama.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>

        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid py-4">

                    <!-- ==================== HEADER SECTION ==================== -->
                    <div class="header-hero">
                        <div class="row align-items-center">
                            <div class="col-lg-8">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <span class="badge-role">
                                        <i class="bi bi-shield-lock-fill me-1"></i><?= htmlspecialchars($jabatan ?? 'Direktur Operasional') ?>
                                    </span>
                                    <span class="badge-role">
                                        <i class="bi bi-person-circle me-1"></i><?= htmlspecialchars($nama_lngkp ?? 'Diana Wulandari') ?>
                                    </span>
                                </div>
                                <h2 class="fw-bold mb-1">
                                    <i class="bi bi-people-fill me-2"></i>Pusat Data Karyawan
                                </h2>
                                <p class="mb-3 opacity-90">
                                    Akses dan monitoring terpusat seluruh data kinerja, arsip, kompetensi, dan evaluasi karakter karyawan PT. Karisma Indoagro Universal.
                                </p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="stat-pill">
                                        <i class="bi bi-people"></i> Total Karyawan: <strong><?= $total_karyawan ?></strong>
                                    </span>
                                    <span class="stat-pill">
                                        <i class="bi bi-building"></i> Departemen: <strong><?= $total_dept ?></strong>
                                    </span>
                                    <span class="stat-pill">
                                        <i class="bi bi-calendar-check"></i> Periode: <strong><?= $periode_aktif ?></strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                                <a href="dashboard-utama" class="btn btn-light shadow-sm fw-bold px-4 py-2" style="border-radius: 10px;">
                                    <i class="bi bi-arrow-left me-1"></i> Dashboard Utama
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== MENU CARDS GRID ==================== -->
                    <div class="row g-4">
                        
                        <!-- 1. DATA KPI SELURUH KARYAWAN -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card menu-hub-card card-kpi" onclick="window.location.href='datakpi-adminhrd'">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="icon-box icon-kpi">
                                            <i class="bi bi-graph-up-arrow"></i>
                                        </div>
                                        <h4 class="card-title-hub">Data KPI Seluruh Karyawan</h4>
                                        <p class="card-desc-hub">
                                            Monitoring performa KPI seluruh karyawan, perbandingan skor WHAT & HOW, evaluasi bobot, dan status Surat Peringatan (SP).
                                        </p>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <a href="datakpi-adminhrd" class="btn btn-outline-success w-100 btn-action-hub">
                                            <i class="bi bi-eye me-1"></i> Buka Data KPI
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 2. ARCHIVE KPI SELURUH KARYAWAN -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card menu-hub-card card-archive" onclick="window.location.href='archive-adminhrd'">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="icon-box icon-archive">
                                            <i class="bi bi-archive-fill"></i>
                                        </div>
                                        <h4 class="card-title-hub">Archive KPI Seluruh Karyawan</h4>
                                        <p class="card-desc-hub">
                                            Pusat arsip dan riwayat rekam jejak KPI karyawan dari periode-periode sebelumnya secara komprehensif.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <a href="archive-adminhrd" class="btn btn-outline-warning w-100 btn-action-hub text-dark">
                                            <i class="bi bi-folder2-open me-1"></i> Buka Archive KPI
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 3. SKILL STANDARD SELURUH KARYAWAN -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card menu-hub-card card-skill" onclick="window.location.href='skill-standard-adminhrd'">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="icon-box icon-skill">
                                            <i class="bi bi-award-fill"></i>
                                        </div>
                                        <h4 class="card-title-hub">Skill Standard Seluruh Karyawan</h4>
                                        <p class="card-desc-hub">
                                            Evaluasi dan monitoring standar keahlian (Skill Standard), verifikasi poin kompetensi, dan sertifikasi teknis seluruh anggota tim.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <a href="skill-standard-adminhrd" class="btn btn-outline-primary w-100 btn-action-hub">
                                            <i class="bi bi-check2-circle me-1"></i> Buka Skill Standard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 4. PENILAIAN KARAKTER SELURUH KARYAWAN -->
                        <div class="col-xl-3 col-md-6">
                            <div class="card menu-hub-card card-karakter" onclick="window.location.href='penilaian-karakter-adminhrd'">
                                <div class="card-body p-4 d-flex flex-column justify-content-between">
                                    <div>
                                        <div class="icon-box icon-karakter">
                                            <i class="bi bi-heart-pulse-fill"></i>
                                        </div>
                                        <h4 class="card-title-hub">Penilaian Karakter Seluruh Karyawan</h4>
                                        <p class="card-desc-hub">
                                            Monitoring hasil evaluasi karakter 360° (Tanggung jawab, Persisten, Komunikasi, dan Realistis) seluruh karyawan dan filter departemen.
                                        </p>
                                    </div>
                                    <div class="pt-2 border-top">
                                        <a href="penilaian-karakter-adminhrd" class="btn btn-action-hub text-white w-100" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                                            <i class="bi bi-bar-chart-fill me-1"></i> Buka Penilaian Karakter
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Quick Information Banner -->
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0 bg-white" style="border-radius: 14px;">
                                <div class="card-body p-3 d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-primary-subtle text-primary p-2 rounded-circle">
                                            <i class="bi bi-info-circle-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">Hak Akses Direktur Operasional</h6>
                                            <small class="text-muted">Anda memiliki akses penuh untuk meninjau data KPI, arsip, skill standard, serta penilaian karakter dari seluruh departemen perusahaan.</small>
                                        </div>
                                    </div>
                                    <div>
                                        <a href="dashboard-utama" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-speedometer2 me-1"></i> Kembali ke Dashboard
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </main>

        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>
</html>
