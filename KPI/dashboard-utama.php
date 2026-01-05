<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/simulasi-db/config.php';
require 'helper/getUser.php';
$conn_sim = mysqli_connect("localhost", "root", "", "db_simulasi");

$id_user = $_SESSION['id_user'];
$user_level = $_SESSION['level'] ?? 1; // Assume level dari session

// ==================== KPI CALCULATION FUNCTION ====================
function calculateKPI($conn, $conn_sim, $user_id, $is_simulation = false) {
    $db_conn = $is_simulation ? $conn_sim : $conn;
    
    // Ambil semua KPI
    $sql_kpi = "SELECT * FROM tb_kpi WHERE id_user='$user_id'";
    $result_kpi = mysqli_query($db_conn, $sql_kpi);
    
    $total_what = 0;
    $total_how = 0;
    $kpi_details = [];
    
    while ($kpi = mysqli_fetch_assoc($result_kpi)) {
        // Hitung WHAT
        $sql_what = "SELECT SUM(total) as total FROM tb_whats WHERE id_user='$user_id' AND id_kpi='{$kpi['id']}'";
        $result_what = mysqli_query($db_conn, $sql_what);
        $row_what = mysqli_fetch_assoc($result_what);
        $total_nilai_what = $row_what['total'] ?? 0;
        $nilai_what = ($total_nilai_what * $kpi['bobot']) / 100;
        $total_what += $nilai_what;
        
        // Hitung HOW
        $sql_how = "SELECT SUM(total) as total FROM tb_hows WHERE id_user='$user_id' AND id_kpi='{$kpi['id']}'";
        $result_how = mysqli_query($db_conn, $sql_how);
        $row_how = mysqli_fetch_assoc($result_how);
        $total_nilai_how = $row_how['total'] ?? 0;
        $nilai_how = ($total_nilai_how * $kpi['bobot2']) / 100;
        $total_how += $nilai_how;
        
        $kpi_details[] = [
            'id' => $kpi['id'],
            'poin_what' => $kpi['poin'],
            'poin_how' => $kpi['poin2'],
            'bobot_what' => $kpi['bobot'],
            'bobot_how' => $kpi['bobot2'],
            'nilai_what' => $nilai_what,
            'nilai_how' => $nilai_how,
            'total_what_raw' => $total_nilai_what,
            'total_how_raw' => $total_nilai_how
        ];
    }
    
    // Ambil bobot WHAT dan HOW
    $sql_bobot = "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user='$user_id' LIMIT 1";
    $result_bobot = mysqli_query($db_conn, $sql_bobot);
    $bobot = mysqli_fetch_assoc($result_bobot);
    $bobot_what = $bobot['bobotwhat'] ?? 0;
    $bobot_how = $bobot['bobothow'] ?? 0;
    
    // Hitung final score
    $final_what = ($total_what * $bobot_what) / 100;
    $final_how = ($total_how * $bobot_how) / 100;
    $total_kpi = $final_what + $final_how;
    
    return [
        'total_what' => $total_what,
        'total_how' => $total_how,
        'bobot_what' => $bobot_what,
        'bobot_how' => $bobot_how,
        'final_what' => $final_what,
        'final_how' => $final_how,
        'total_kpi' => $total_kpi,
        'kpi_details' => $kpi_details
    ];
}

$kpi_real = calculateKPI($conn, $conn_sim, $id_user, false);
$kpi_sim = calculateKPI($conn, $conn_sim, $id_user, true);

// Get user info
$sql_user_info = "SELECT * FROM tb_users WHERE id='$id_user'";
$result_user_info = mysqli_query($conn, $sql_user_info);
$user_info = mysqli_fetch_assoc($result_user_info);

// ==================== TREND DATA (Last 6 months) ====================
$trend_data = [];
for ($i = 5; $i >= 0; $i--) {
    $month = date('Y-m', strtotime("-$i months"));
    // Simplified - In real implementation, you'd fetch historical data
    $trend_data[] = [
        'month' => date('M Y', strtotime("-$i months")),
        'real' => $kpi_real['total_kpi'] * (0.85 + ($i * 0.03)), // Simulated trend
        'target' => $kpi_sim['total_kpi']
    ];
}

// ==================== DEPARTMENT COMPARISON ====================
$dept_comparison = [];
if ($user_level >= 3) { // Kadep or higher
    $sql_dept_users = "SELECT id, nama_lngkp FROM tb_users WHERE departement='{$user_info['departement']}'";
    $result_dept_users = mysqli_query($conn, $sql_dept_users);
    
    while ($dept_user = mysqli_fetch_assoc($result_dept_users)) {
        $dept_kpi = calculateKPI($conn, $conn_sim, $dept_user['id'], false);
        $dept_comparison[] = [
            'name' => $dept_user['nama_lngkp'],
            'score' => $dept_kpi['total_kpi']
        ];
    }
}
?>

<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<style>
    .stat-card {
        border-left: 4px solid;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .chart-container {
        position: relative;
        height: 300px;
        margin-bottom: 20px;
    }
    .filter-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
    }
    .comparison-badge {
        font-size: 0.85rem;
        padding: 0.35rem 0.75rem;
    }
    .kpi-gauge {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto;
    }
    
    /* ==================== TAMBAHKAN CSS INI ==================== */
    .hover-card-analytics {
        transition: all 0.3s ease;
    }
    .hover-card-analytics:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 28px rgba(0,0,0,0.2) !important;
    }
    /* ==================== AKHIR CSS BARU ==================== */
    
    @media print {
        .no-print { display: none; }
    }
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_utama.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid" style="font-size:13px;">
                    
                    <!-- ==================== HEADER ==================== -->
                    <div class="row mb-4 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0 filter-card">
                                <div class="card-body">
                                    <div class="row align-items-center">
                                        <div class="col-md-6">
                                            <h3 class="fw-bold mb-2">
                                                <i class="bi bi-speedometer2 me-2"></i>KPI Analytics Dashboard
                                            </h3>
                                            <p class="mb-0 opacity-90">Advanced Performance Monitoring & Analysis</p>
                                        </div>
                                        <div class="col-md-6 text-md-end">
                                            <button class="btn btn-light btn-sm no-print" onclick="window.print()">
                                                <i class="bi bi-printer me-1"></i>Print
                                            </button>
                                            <button class="btn btn-light btn-sm no-print" onclick="exportData()">
                                                <i class="bi bi-download me-1"></i>Export
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== QUICK ACTIONS ==================== -->
                    <div class="row mb-4 no-print">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-2">
                                        <div class="col-md-3">
                                            <a href="dashboard" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-speedometer2 me-2"></i>Real KPI Dashboard
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="dashboard-simulasi" class="btn btn-outline-success w-100">
                                                <i class="bi bi-graph-up me-2"></i>Simulation Dashboard
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="skillstandard" class="btn btn-outline-info w-100">
                                                <i class="bi bi-award me-2"></i>Skill Standard
                                            </a>
                                        </div>
                                        <div class="col-md-3">
                                            <a href="eviden" class="btn btn-outline-warning w-100">
                                                <i class="bi bi-folder me-2"></i>Evidence
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== KEY METRICS ==================== -->
                    <div class="row mb-4">
                        <!-- Total Score Real -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 stat-card" style="border-left-color: #0d6efd !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 small">Real Performance</p>
                                            <h2 class="fw-bold mb-0 text-primary"><?= number_format($kpi_real['total_kpi'], 2) ?></h2>
                                        </div>
                                        <div class="kpi-icon">
                                            <i class="bi bi-check-circle-fill text-primary" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-primary comparison-badge">
                                            <?= $kpi_real['total_kpi'] >= $kpi_sim['total_kpi'] ? '▲' : '▼' ?>
                                            <?= number_format(abs($kpi_real['total_kpi'] - $kpi_sim['total_kpi']), 2) ?> vs Target
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Score Simulation -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 stat-card" style="border-left-color: #198754 !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 small">Target (Simulation)</p>
                                            <h2 class="fw-bold mb-0 text-success"><?= number_format($kpi_sim['total_kpi'], 2) ?></h2>
                                        </div>
                                        <div class="kpi-icon">
                                            <i class="bi bi-graph-up-arrow text-success" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-success comparison-badge">Benchmark</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Achievement Percentage -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 stat-card" style="border-left-color: #ffc107 !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 small">Achievement Rate</p>
                                            <h2 class="fw-bold mb-0 text-warning">
                                                <?= $kpi_sim['total_kpi'] > 0 ? number_format(($kpi_real['total_kpi'] / $kpi_sim['total_kpi']) * 100, 1) : 0 ?>%
                                            </h2>
                                        </div>
                                        <div class="kpi-icon">
                                            <i class="bi bi-trophy-fill text-warning" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <div class="progress" style="height: 8px;">
                                            <div class="progress-bar bg-warning" role="progressbar" 
                                                 style="width: <?= min(100, ($kpi_real['total_kpi'] / max($kpi_sim['total_kpi'], 1)) * 100) ?>%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Gap Analysis -->
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 stat-card" style="border-left-color: <?= $kpi_real['total_kpi'] >= $kpi_sim['total_kpi'] ? '#198754' : '#dc3545' ?> !important;">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <p class="text-muted mb-1 small">Gap to Target</p>
                                            <h2 class="fw-bold mb-0 <?= $kpi_real['total_kpi'] >= $kpi_sim['total_kpi'] ? 'text-success' : 'text-danger' ?>">
                                                <?= number_format($kpi_real['total_kpi'] - $kpi_sim['total_kpi'], 2) ?>
                                            </h2>
                                        </div>
                                        <div class="kpi-icon">
                                            <i class="bi bi-<?= $kpi_real['total_kpi'] >= $kpi_sim['total_kpi'] ? 'arrow-up-circle-fill text-success' : 'arrow-down-circle-fill text-danger' ?>" style="font-size: 3rem;"></i>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <span class="badge bg-<?= $kpi_real['total_kpi'] >= $kpi_sim['total_kpi'] ? 'success' : 'danger' ?> comparison-badge">
                                            <?= $kpi_real['total_kpi'] >= $kpi_sim['total_kpi'] ? 'Above Target' : 'Below Target' ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== COMPARISON CARDS (Real vs Simulation) ==================== -->
                    <div class="row mb-4">
                        
                        <!-- KPI REAL Card -->
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card-analytics" style="cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='dashboard'">
                                
                                <!-- Header Card Real -->
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 fw-bold"><i class="bi bi-check-circle-fill me-2"></i>KPI REAL</h5>
                                            <small class="opacity-75">Actual Performance Data</small>
                                        </div>
                                        <div class="text-end">
                                            <h3 class="mb-0 fw-bold"><?= number_format($kpi_real['total_kpi'], 2) ?></h3>
                                            <small>Total Score</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    
                                    <!-- WHAT Section -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-primary mb-0">
                                                <i class="bi bi-bullseye me-1"></i>WHAT (Target/Objective)
                                            </h6>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Weight: <?= $kpi_real['bobot_what'] ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 40%;">KPI Point</th>
                                                        <th class="text-center" style="width: 20%;">Weight</th>
                                                        <th class="text-center" style="width: 20%;">Total</th>
                                                        <th class="text-center" style="width: 20%;">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach ($kpi_real['kpi_details'] as $detail) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1"><?= substr($detail['poin_what'], 0, 30) ?><?= strlen($detail['poin_what']) > 30 ? '...' : '' ?></span>
                                                        </td>
                                                        <td class="text-center"><small><?= $detail['bobot_what'] ?>%</small></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info" style="font-size: 0.7rem;"><?= number_format($detail['total_what_raw'], 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-primary"><small><?= number_format($detail['nilai_what'], 2) ?></small></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold"><small>Final WHAT (<?= $kpi_real['bobot_what'] ?>%):</small></td>
                                                        <td class="text-center fw-bold text-primary"><?= number_format($kpi_real['final_what'], 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <!-- HOW Section -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-success mb-0">
                                                <i class="bi bi-gear-fill me-1"></i>HOW (Method/Process)
                                            </h6>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Weight: <?= $kpi_real['bobot_how'] ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 40%;">KPI Point</th>
                                                        <th class="text-center" style="width: 20%;">Weight</th>
                                                        <th class="text-center" style="width: 20%;">Total</th>
                                                        <th class="text-center" style="width: 20%;">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach ($kpi_real['kpi_details'] as $detail) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1"><?= substr($detail['poin_how'], 0, 30) ?><?= strlen($detail['poin_how']) > 30 ? '...' : '' ?></span>
                                                        </td>
                                                        <td class="text-center"><small><?= $detail['bobot_how'] ?>%</small></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info" style="font-size: 0.7rem;"><?= number_format($detail['total_how_raw'], 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-success"><small><?= number_format($detail['nilai_how'], 2) ?></small></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold"><small>Final HOW (<?= $kpi_real['bobot_how'] ?>%):</small></td>
                                                        <td class="text-center fw-bold text-success"><?= number_format($kpi_real['final_how'], 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                                <!-- Footer Total -->
                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-uppercase">Total KPI Real</span>
                                        <h4 class="mb-0 fw-bold text-primary"><?= number_format($kpi_real['total_kpi'], 2) ?></h4>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- KPI SIMULATION Card -->
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card-analytics" style="cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='dashboard-simulasi'">
                                
                                <!-- Header Card Simulation -->
                                <div class="card-header bg-success text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h5 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>KPI SIMULATION</h5>
                                            <small class="opacity-75">Target/Projection Data</small>
                                        </div>
                                        <div class="text-end">
                                            <h3 class="mb-0 fw-bold"><?= number_format($kpi_sim['total_kpi'], 2) ?></h3>
                                            <small>Total Score</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    
                                    <!-- WHAT Section -->
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-primary mb-0">
                                                <i class="bi bi-bullseye me-1"></i>WHAT (Target/Objective)
                                            </h6>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Weight: <?= $kpi_sim['bobot_what'] ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 40%;">KPI Point</th>
                                                        <th class="text-center" style="width: 20%;">Weight</th>
                                                        <th class="text-center" style="width: 20%;">Total</th>
                                                        <th class="text-center" style="width: 20%;">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach ($kpi_sim['kpi_details'] as $detail) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1"><?= substr($detail['poin_what'], 0, 30) ?><?= strlen($detail['poin_what']) > 30 ? '...' : '' ?></span>
                                                        </td>
                                                        <td class="text-center"><small><?= $detail['bobot_what'] ?>%</small></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info" style="font-size: 0.7rem;"><?= number_format($detail['total_what_raw'], 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-primary"><small><?= number_format($detail['nilai_what'], 2) ?></small></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold"><small>Final WHAT (<?= $kpi_sim['bobot_what'] ?>%):</small></td>
                                                        <td class="text-center fw-bold text-primary"><?= number_format($kpi_sim['final_what'], 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <hr class="my-3">

                                    <!-- HOW Section -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold text-success mb-0">
                                                <i class="bi bi-gear-fill me-1"></i>HOW (Method/Process)
                                            </h6>
                                            <span class="badge bg-warning text-dark" style="font-size: 0.75rem;">Weight: <?= $kpi_sim['bobot_how'] ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0" style="font-size: 0.85rem;">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th style="width: 40%;">KPI Point</th>
                                                        <th class="text-center" style="width: 20%;">Weight</th>
                                                        <th class="text-center" style="width: 20%;">Total</th>
                                                        <th class="text-center" style="width: 20%;">Score</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach ($kpi_sim['kpi_details'] as $detail) {
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1"><?= substr($detail['poin_how'], 0, 30) ?><?= strlen($detail['poin_how']) > 30 ? '...' : '' ?></span>
                                                        </td>
                                                        <td class="text-center"><small><?= $detail['bobot_how'] ?>%</small></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info" style="font-size: 0.7rem;"><?= number_format($detail['total_how_raw'], 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-success"><small><?= number_format($detail['nilai_how'], 2) ?></small></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold"><small>Final HOW (<?= $kpi_sim['bobot_how'] ?>%):</small></td>
                                                        <td class="text-center fw-bold text-success"><?= number_format($kpi_sim['final_how'], 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                                <!-- Footer Total -->
                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-uppercase">Total KPI Simulation</span>
                                        <h4 class="mb-0 fw-bold text-success"><?= number_format($kpi_sim['total_kpi'], 2) ?></h4>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- ==================== AKHIR COMPARISON CARDS ==================== -->


                    <!-- ==================== CHARTS ROW ==================== -->
                    <div class="row mb-4">
                        
                        <!-- Trend Analysis Chart -->
                        <div class="col-lg-8 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Performance Trend (Last 6 Months)</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="trendChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KPI Real vs Target Comparison -->
                        <div class="col-lg-4 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Real vs Target</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="realVsTargetChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- ==================== KPI BREAKDOWN ==================== -->
                    <div class="row mb-4">
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>KPI Breakdown - Real</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="kpiBreakdownReal"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-bar-chart-line me-2"></i>KPI Breakdown - Target</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="kpiBreakdownSim"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== DEPARTMENT COMPARISON (if applicable) ==================== -->
                    <?php if (!empty($dept_comparison) && $user_level >= 3) { ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Department Team Comparison</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="deptComparisonChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- ==================== DETAILED TABLES ==================== -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-table me-2"></i>Detailed KPI Analysis</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>KPI Item</th>
                                                    <th class="text-center">Weight</th>
                                                    <th class="text-center">Real Score</th>
                                                    <th class="text-center">Target Score</th>
                                                    <th class="text-center">Gap</th>
                                                    <th class="text-center">Achievement</th>
                                                    <th class="text-center">Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (!empty($kpi_real['kpi_details'])) { ?>
                                                    <?php foreach ($kpi_real['kpi_details'] as $idx => $detail) { 
                                                        
                                                        // Ambil data simulasi jika ada, jika tidak pakai default
                                                        $sim_detail = $kpi_sim['kpi_details'][$idx] ?? [
                                                            'nilai_what' => 0
                                                        ];

                                                        $real_nilai   = $detail['nilai_what'] ?? 0;
                                                        $target_nilai = $sim_detail['nilai_what'] ?? 0;

                                                        $achievement = $target_nilai > 0 
                                                            ? ($real_nilai / $target_nilai) * 100 
                                                            : 0;
                                                    ?>
                                                    <tr>
                                                        <td><strong><?= htmlspecialchars($detail['poin_what']) ?></strong></td>
                                                        <td class="text-center"><?= $detail['bobot_what'] ?>%</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-primary">
                                                                <?= number_format($real_nilai, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-success">
                                                                <?= number_format($target_nilai, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-<?= $real_nilai >= $target_nilai ? 'success' : 'warning' ?>">
                                                                <?= number_format($real_nilai - $target_nilai, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center"><?= number_format($achievement, 1) ?>%</td>
                                                        <td class="text-center">
                                                            <?php if ($achievement >= 100) { ?>
                                                                <i class="bi bi-check-circle-fill text-success"></i>
                                                            <?php } elseif ($achievement >= 80) { ?>
                                                                <i class="bi bi-exclamation-circle-fill text-warning"></i>
                                                            <?php } else { ?>
                                                                <i class="bi bi-x-circle-fill text-danger"></i>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                <?php } else { ?>
                                                    <tr>
                                                        <td colspan="7" class="text-center text-muted">
                                                            Data KPI belum tersedia
                                                        </td>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
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

    <!-- ==================== JAVASCRIPT FOR CHARTS ==================== -->
    <script>
        // Trend Chart
        const trendCtx = document.getElementById('trendChart').getContext('2d');
        const trendChart = new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: <?= json_encode(array_column($trend_data, 'month')) ?>,
                datasets: [{
                    label: 'Real Performance',
                    data: <?= json_encode(array_column($trend_data, 'real')) ?>,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Target',
                    data: <?= json_encode(array_column($trend_data, 'target')) ?>,
                    borderColor: '#198754',
                    backgroundColor: 'rgba(25, 135, 84, 0.1)',
                    tension: 0.4,
                    fill: true,
                    borderDash: [5, 5]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top' },
                    tooltip: { mode: 'index', intersect: false }
                },
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });

        // Real vs Target Chart
        const realVsTargetCtx = document.getElementById('realVsTargetChart').getContext('2d');
        const realVsTargetChart = new Chart(realVsTargetCtx, {
            type: 'doughnut',
            data: {
                labels: ['Real Performance', 'Target'],
                datasets: [{
                    data: [<?= $kpi_real['total_kpi'] ?>, <?= $kpi_sim['total_kpi'] ?>],
                    backgroundColor: ['#0d6efd', '#198754'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { 
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                let value = context.parsed || 0;
                                let total = context.dataset.data.reduce((a, b) => a + b, 0);
                                let percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value.toFixed(2) + ' (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // KPI Breakdown Real
        const breakdownRealCtx = document.getElementById('kpiBreakdownReal').getContext('2d');
        const breakdownRealChart = new Chart(breakdownRealCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($kpi_real['kpi_details'], 'poin_what')) ?>,
                datasets: [{
                    label: 'Score',
                    data: <?= json_encode(array_column($kpi_real['kpi_details'], 'nilai_what')) ?>,
                    backgroundColor: '#0d6efd',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        // KPI Breakdown Simulation
        const breakdownSimCtx = document.getElementById('kpiBreakdownSim').getContext('2d');
        const breakdownSimChart = new Chart(breakdownSimCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($kpi_sim['kpi_details'], 'poin_what')) ?>,
                datasets: [{
                    label: 'Target Score',
                    data: <?= json_encode(array_column($kpi_sim['kpi_details'], 'nilai_what')) ?>,
                    backgroundColor: '#198754',
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });

        <?php if (!empty($dept_comparison)) { ?>
        // Department Comparison Chart
        const deptCompCtx = document.getElementById('deptComparisonChart').getContext('2d');
        const deptCompChart = new Chart(deptCompCtx, {
            type: 'horizontalBar',
            data: {
                labels: <?= json_encode(array_column($dept_comparison, 'name')) ?>,
                datasets: [{
                    label: 'KPI Score',
                    data: <?= json_encode(array_column($dept_comparison, 'score')) ?>,
                    backgroundColor: '#ffc107',
                    borderRadius: 5
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true } }
            }
        });
        <?php } ?>

        // Export Function
        function exportData() {
            alert('Export feature will generate Excel/PDF report');
            // Implement export to Excel/PDF here
        }
    </script>

</body>
</html>