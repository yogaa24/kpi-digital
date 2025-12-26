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

$id_user = $_SESSION['id_user'];

// ==================== KPI REAL ====================
// Ambil semua data KPI Real
$sql_kpi_real = "SELECT * FROM tb_kpi WHERE id_user='$id_user'";
$result_kpi_real = mysqli_query($conn, $sql_kpi_real);

$total_what_real = 0;
$total_how_real = 0;

// Hitung total WHAT dan HOW untuk semua KPI Real
while ($kpi = mysqli_fetch_assoc($result_kpi_real)) {
    // Hitung WHAT
    $sql_what = "SELECT SUM(total) as total FROM tb_whats WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
    $result_what = mysqli_query($conn, $sql_what);
    $row_what = mysqli_fetch_assoc($result_what);
    $total_nilai_what = $row_what['total'] ?? 0;
    $nilai_what = ($total_nilai_what * $kpi['bobot']) / 100;
    $total_what_real += $nilai_what;
    
    // Hitung HOW
    $sql_how = "SELECT SUM(total) as total FROM tb_hows WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
    $result_how = mysqli_query($conn, $sql_how);
    $row_how = mysqli_fetch_assoc($result_how);
    $total_nilai_how = $row_how['total'] ?? 0;
    $nilai_how = ($total_nilai_how * $kpi['bobot2']) / 100;
    $total_how_real += $nilai_how;
}

// Ambil bobot WHAT dan HOW dari tb_bobotkpi Real
$sql_bobot_real = "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user='$id_user' LIMIT 1";
$result_bobot_real = mysqli_query($conn, $sql_bobot_real);
$bobot_real = mysqli_fetch_assoc($result_bobot_real);
$bobot_what_real = $bobot_real['bobotwhat'] ?? 0;
$bobot_how_real = $bobot_real['bobothow'] ?? 0;

// Hitung final score dengan bobot KPI
$final_what_real = ($total_what_real * $bobot_what_real) / 100;
$final_how_real = ($total_how_real * $bobot_how_real) / 100;
$total_kpi_real = $final_what_real + $final_how_real;

// ==================== KPI SIMULASI ====================
// Koneksi ke database simulasi
$conn_sim = mysqli_connect("localhost", "root", "", "db_simulasi");

// Ambil semua data KPI Simulasi
$sql_kpi_sim = "SELECT * FROM tb_kpi WHERE id_user='$id_user'";
$result_kpi_sim = mysqli_query($conn_sim, $sql_kpi_sim);

$total_what_sim = 0;
$total_how_sim = 0;

// Hitung total WHAT dan HOW untuk semua KPI Simulasi
while ($kpi = mysqli_fetch_assoc($result_kpi_sim)) {
    // Hitung WHAT
    $sql_what = "SELECT SUM(total) as total FROM tb_whats WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
    $result_what = mysqli_query($conn_sim, $sql_what);
    $row_what = mysqli_fetch_assoc($result_what);
    $total_nilai_what = $row_what['total'] ?? 0;
    $nilai_what = ($total_nilai_what * $kpi['bobot']) / 100;
    $total_what_sim += $nilai_what;
    
    // Hitung HOW
    $sql_how = "SELECT SUM(total) as total FROM tb_hows WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
    $result_how = mysqli_query($conn_sim, $sql_how);
    $row_how = mysqli_fetch_assoc($result_how);
    $total_nilai_how = $row_how['total'] ?? 0;
    $nilai_how = ($total_nilai_how * $kpi['bobot2']) / 100;
    $total_how_sim += $nilai_how;
}

// Ambil bobot WHAT dan HOW dari tb_bobotkpi Simulasi
$sql_bobot_sim = "SELECT bobotwhat, bobothow FROM tb_bobotkpi WHERE id_user='$id_user' LIMIT 1";
$result_bobot_sim = mysqli_query($conn_sim, $sql_bobot_sim);
$bobot_sim = mysqli_fetch_assoc($result_bobot_sim);
$bobot_what_sim = $bobot_sim['bobotwhat'] ?? 0;
$bobot_how_sim = $bobot_sim['bobothow'] ?? 0;

// Hitung final score dengan bobot KPI
$final_what_sim = ($total_what_sim * $bobot_what_sim) / 100;
$final_how_sim = ($total_how_sim * $bobot_how_sim) / 100;
$total_kpi_sim = $final_what_sim + $final_how_sim;

// Reset pointer untuk tampilan list
mysqli_data_seek($result_kpi_real, 0);
mysqli_data_seek($result_kpi_sim, 0);
?>

<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_utama.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid" style="font-size:13px;">
                    
                    <!-- Header Dashboard -->
                    <div class="row mb-4 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <h3 class="fw-bold mb-2"><i class="bi bi-bar-chart-line-fill text-primary me-2"></i>Dashboard KPI</h3>
                                    <p class="text-muted mb-0">Komparasi antara KPI Real dan KPI Simulasi</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Menu Cards - BARU -->
                    <div class="row mb-4">
                        <!-- Skill Standard -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card" onclick="window.location.href='skillstandard'" style="cursor: pointer; transition: transform 0.2s;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-award-fill text-info" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Skill Standard</h5>
                                    <p class="text-muted mb-0 small">Kelola standar keterampilan dan kompetensi</p>
                                </div>
                            </div>
                        </div>

                        <!-- Archive -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card" onclick="window.location.href='archive'" style="cursor: pointer; transition: transform 0.2s;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-archive-fill text-warning" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Archive</h5>
                                    <p class="text-muted mb-0 small">Arsip dokumen dan data historis</p>
                                </div>
                            </div>
                        </div>

                        <!-- Eviden -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card" onclick="window.location.href='eviden'" style="cursor: pointer; transition: transform 0.2s;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-folder-fill text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Eviden</h5>
                                    <p class="text-muted mb-0 small">Dokumentasi bukti dan evidensi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Comparison Cards -->
                    <div class="row">
                        
                        <!-- KPI REAL -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0 h-100 hover-card" style="cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='dashboard'">
                                
                                <!-- Header Card Real -->
                                <div class="card-header bg-primary text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0 fw-bold"><i class="bi bi-check-circle-fill me-2"></i>KPI REAL</h4>
                                            <small class="opacity-75">Data Aktual Kinerja</small>
                                        </div>
                                        <div class="text-end">
                                            <h2 class="mb-0 fw-bold"><?= number_format($total_kpi_real, 2) ?></h2>
                                            <small>Total Score</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    
                                    <!-- WHAT Section -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold text-primary mb-0">
                                                <i class="bi bi-bullseye me-2"></i>WHAT (Target/Tujuan)
                                            </h5>
                                            <span class="badge bg-warning text-dark">Bobot KPI: <?= $bobot_what_real ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Poin KPI</th>
                                                        <th class="text-center" width="20%">Bobot Poin</th>
                                                        <th class="text-center" width="20%">Total WHAT</th>
                                                        <th class="text-center" width="20%">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    mysqli_data_seek($result_kpi_real, 0);
                                                    while ($kpi = mysqli_fetch_assoc($result_kpi_real)) {
                                                        // Hitung total WHAT untuk KPI ini
                                                        $sql_total = "SELECT SUM(total) as total FROM tb_whats WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
                                                        $res_total = mysqli_query($conn, $sql_total);
                                                        $row_total = mysqli_fetch_assoc($res_total);
                                                        $total_what_kpi = $row_total['total'] ?? 0;
                                                        $nilai_akhir = ($total_what_kpi * $kpi['bobot']) / 100;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1 fw-bold"><?= $kpi['poin'] ?></span>
                                                        </td>
                                                        <td class="text-center"><?= $kpi['bobot'] ?>%</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info"><?= number_format($total_what_kpi, 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-primary"><?= number_format($nilai_akhir, 2) ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold">Total WHAT (<?= $bobot_what_real ?>%):</td>
                                                        <td class="text-center fw-bold text-primary"><?= number_format($final_what_real, 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- HOW Section -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold text-success mb-0">
                                                <i class="bi bi-gear-fill me-2"></i>HOW (Cara/Metode)
                                            </h5>
                                            <span class="badge bg-warning text-dark">Bobot KPI: <?= $bobot_how_real ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Poin KPI</th>
                                                        <th class="text-center" width="20%">Bobot Poin</th>
                                                        <th class="text-center" width="20%">Total HOW</th>
                                                        <th class="text-center" width="20%">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    mysqli_data_seek($result_kpi_real, 0);
                                                    while ($kpi = mysqli_fetch_assoc($result_kpi_real)) {
                                                        // Hitung total HOW untuk KPI ini
                                                        $sql_total = "SELECT SUM(total) as total FROM tb_hows WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
                                                        $res_total = mysqli_query($conn, $sql_total);
                                                        $row_total = mysqli_fetch_assoc($res_total);
                                                        $total_how_kpi = $row_total['total'] ?? 0;
                                                        $nilai_akhir = ($total_how_kpi * $kpi['bobot2']) / 100;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1 fw-bold"><?= $kpi['poin2'] ?></span>
                                                        </td>
                                                        <td class="text-center"><?= $kpi['bobot2'] ?>%</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info"><?= number_format($total_how_kpi, 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-success"><?= number_format($nilai_akhir, 2) ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold">Total HOW (<?= $bobot_how_real ?>%):</td>
                                                        <td class="text-center fw-bold text-success"><?= number_format($final_how_real, 2) ?></td>
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
                                        <h3 class="mb-0 fw-bold text-primary"><?= number_format($total_kpi_real, 2) ?></h3>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <!-- KPI SIMULASI -->
                        <div class="col-lg-6 mb-4">
                            <div class="card shadow-sm border-0 h-100 hover-card" style="cursor: pointer; transition: transform 0.2s;" onclick="window.location.href='dashboard-simulasi'">
                                
                                <!-- Header Card Simulasi -->
                                <div class="card-header bg-success text-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2"></i>KPI SIMULASI</h4>
                                            <small class="opacity-75">Data Proyeksi/Target</small>
                                        </div>
                                        <div class="text-end">
                                            <h2 class="mb-0 fw-bold"><?= number_format($total_kpi_sim, 2) ?></h2>
                                            <small>Total Score</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    
                                    <!-- WHAT Section -->
                                    <div class="mb-4">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold text-primary mb-0">
                                                <i class="bi bi-bullseye me-2"></i>WHAT (Target/Tujuan)
                                            </h5>
                                            <span class="badge bg-warning text-dark">Bobot KPI: <?= $bobot_what_sim ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Poin KPI</th>
                                                        <th class="text-center" width="20%">Bobot Poin</th>
                                                        <th class="text-center" width="20%">Total WHAT</th>
                                                        <th class="text-center" width="20%">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    mysqli_data_seek($result_kpi_sim, 0);
                                                    while ($kpi = mysqli_fetch_assoc($result_kpi_sim)) {
                                                        // Hitung total WHAT untuk KPI ini
                                                        $sql_total = "SELECT SUM(total) as total FROM tb_whats WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
                                                        $res_total = mysqli_query($conn_sim, $sql_total);
                                                        $row_total = mysqli_fetch_assoc($res_total);
                                                        $total_what_kpi = $row_total['total'] ?? 0;
                                                        $nilai_akhir = ($total_what_kpi * $kpi['bobot']) / 100;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1 fw-bold"><?= $kpi['poin'] ?></span>
                                                        </td>
                                                        <td class="text-center"><?= $kpi['bobot'] ?>%</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info"><?= number_format($total_what_kpi, 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-primary"><?= number_format($nilai_akhir, 2) ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold">Total WHAT (<?= $bobot_what_sim ?>%):</td>
                                                        <td class="text-center fw-bold text-primary"><?= number_format($final_what_sim, 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                    <hr>

                                    <!-- HOW Section -->
                                    <div>
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <h5 class="fw-bold text-success mb-0">
                                                <i class="bi bi-gear-fill me-2"></i>HOW (Cara/Metode)
                                            </h5>
                                            <span class="badge bg-warning text-dark">Bobot KPI: <?= $bobot_how_sim ?>%</span>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-sm table-hover mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Poin KPI</th>
                                                        <th class="text-center" width="20%">Bobot Poin</th>
                                                        <th class="text-center" width="20%">Total HOW</th>
                                                        <th class="text-center" width="20%">Nilai</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    mysqli_data_seek($result_kpi_sim, 0);
                                                    while ($kpi = mysqli_fetch_assoc($result_kpi_sim)) {
                                                        // Hitung total HOW untuk KPI ini
                                                        $sql_total = "SELECT SUM(total) as total FROM tb_hows WHERE id_user='$id_user' AND id_kpi='{$kpi['id']}'";
                                                        $res_total = mysqli_query($conn_sim, $sql_total);
                                                        $row_total = mysqli_fetch_assoc($res_total);
                                                        $total_how_kpi = $row_total['total'] ?? 0;
                                                        $nilai_akhir = ($total_how_kpi * $kpi['bobot2']) / 100;
                                                    ?>
                                                    <tr>
                                                        <td>
                                                            <small class="text-muted"><?= $no++ ?>.</small>
                                                            <span class="ms-1 fw-bold"><?= $kpi['poin2'] ?></span>
                                                        </td>
                                                        <td class="text-center"><?= $kpi['bobot2'] ?>%</td>
                                                        <td class="text-center">
                                                            <span class="badge bg-info"><?= number_format($total_how_kpi, 2) ?></span>
                                                        </td>
                                                        <td class="text-center fw-bold text-success"><?= number_format($nilai_akhir, 2) ?></td>
                                                    </tr>
                                                    <?php } ?>
                                                </tbody>
                                                <tfoot class="table-light">
                                                    <tr>
                                                        <td colspan="3" class="text-end fw-bold">Total HOW (<?= $bobot_how_sim ?>%):</td>
                                                        <td class="text-center fw-bold text-success"><?= number_format($final_how_sim, 2) ?></td>
                                                    </tr>
                                                </tfoot>
                                            </table>
                                        </div>
                                    </div>

                                </div>

                                <!-- Footer Total -->
                                <div class="card-footer bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold text-uppercase">Total KPI Simulasi</span>
                                        <h3 class="mb-0 fw-bold text-success"><?= number_format($total_kpi_sim, 2) ?></h3>
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- Comparison Summary -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-warning">
                                    <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard-data me-2"></i>Ringkasan Perbandingan</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row text-center">
                                        <div class="col-md-4">
                                            <div class="p-3">
                                                <h6 class="text-muted mb-2">Selisih Total</h6>
                                                <h3 class="fw-bold <?= ($total_kpi_real - $total_kpi_sim) >= 0 ? 'text-success' : 'text-danger' ?>">
                                                    <?= number_format(abs($total_kpi_real - $total_kpi_sim), 2) ?>
                                                </h3>
                                                <small class="text-muted">
                                                    <?= ($total_kpi_real - $total_kpi_sim) >= 0 ? 'Real lebih tinggi' : 'Simulasi lebih tinggi' ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 border-start border-end">
                                                <h6 class="text-muted mb-2">Persentase Pencapaian</h6>
                                                <h3 class="fw-bold text-primary">
                                                    <?= $total_kpi_sim > 0 ? number_format(($total_kpi_real / $total_kpi_sim) * 100, 2) : 0 ?>%
                                                </h3>
                                                <small class="text-muted">Real vs Target Simulasi</small>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3">
                                                <h6 class="text-muted mb-2">Status</h6>
                                                <h3>
                                                    <?php if ($total_kpi_real >= $total_kpi_sim) { ?>
                                                        <span class="badge bg-success fs-6">Target Tercapai</span>
                                                    <?php } else { ?>
                                                        <span class="badge bg-danger fs-6">Belum Tercapai</span>
                                                    <?php } ?>
                                                </h3>
                                                <small class="text-muted">Evaluasi Kinerja</small>
                                            </div>
                                        </div>
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

    <style>
        .hover-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.15) !important;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(0,0,0,0.02);
        }
        
        .badge {
            font-weight: 600;
            padding: 0.4em 0.8em;
        }
    </style>

</body>
</html>