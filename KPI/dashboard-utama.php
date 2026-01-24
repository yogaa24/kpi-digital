<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

$id_user = $_SESSION['id_user'];

$user_level = $_SESSION['level'] ?? 1; // Assume level dari session

// ==================== FILTER PARAMETERS ====================
$filter_user = isset($_GET['filter_user']) ? $_GET['filter_user'] : $id_user;
// $filter_periode = isset($_GET['filter_periode']) ? $_GET['filter_periode'] : date('Y-m');
$filter_departemen = isset($_GET['filter_departemen']) ? $_GET['filter_departemen'] : '';
// $filter_comparison = isset($_GET['filter_comparison']) ? $_GET['filter_comparison'] : 'current'; // current, last_month, last_year

/// ==================== FETCH FILTER OPTIONS ====================
// Get all users based on level
if ($user_level >= 2) {
    $sql_users = "SELECT id, nama_lngkp, departement, jabatan FROM tb_users WHERE 1=1";
    $sql_users .= " AND username NOT IN ('itboy', 'adminhrd')";
    
    if ($user_level == 2) {         // KOORDINATOR - team sendiri
        $sql_users .= " AND atasan = (SELECT nama_lngkp FROM tb_users WHERE id='$id_user')";
    
    } elseif ($user_level == 3) {   // MANAGER - team yang di-supervisi
        $sql_users .= " AND atasan = (SELECT nama_lngkp FROM tb_users WHERE id='$id_user')";
        
    } elseif ($user_level == 4) {   // KADEP - seluruh departemen
        $sql_users .= " AND departement = (SELECT departement FROM tb_users WHERE id='$id_user')";
        
    } elseif ($user_level >= 5) {   // DIREKTUR - semua atau filter departemen
        if (!empty($filter_departemen)) {
            $sql_users .= " AND departement = '" . mysqli_real_escape_string($conn, $filter_departemen) . "'";
        }
    }
    
    $sql_users .= " ORDER BY departement, nama_lngkp";
    $result_users = mysqli_query($conn, $sql_users);
}

$sql_departments = "SELECT DISTINCT departement FROM tb_users WHERE departement IS NOT NULL AND departement != '' ORDER BY departement";
$result_departments = mysqli_query($conn, $sql_departments);

// ==================== KPI CALCULATION FUNCTION ====================
function calculateKPI($conn, $user_id, $is_simulation = false) {
    // Tentukan prefix tabel berdasarkan mode
    $prefix = $is_simulation ? 'tbsim_' : 'tb_';
    
    // Ambil semua KPI
    $sql_kpi = "SELECT * FROM {$prefix}kpi WHERE id_user='$user_id'";
    $result_kpi = mysqli_query($conn, $sql_kpi);
    
    $total_what = 0;
    $total_how = 0;
    $kpi_details = [];
    
    while ($kpi = mysqli_fetch_assoc($result_kpi)) {
        // Hitung WHAT
        $sql_what = "SELECT SUM(total) as total FROM {$prefix}whats WHERE id_user='$user_id' AND id_kpi='{$kpi['id']}'";
        $result_what = mysqli_query($conn, $sql_what);
        $row_what = mysqli_fetch_assoc($result_what);
        $total_nilai_what = $row_what['total'] ?? 0;
        $nilai_what = ($total_nilai_what * $kpi['bobot']) / 100;
        $total_what += $nilai_what;
        
        // Hitung HOW
        $sql_how = "SELECT SUM(total) as total FROM {$prefix}hows WHERE id_user='$user_id' AND id_kpi='{$kpi['id']}'";
        $result_how = mysqli_query($conn, $sql_how);
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
    $sql_bobot = "SELECT bobotwhat, bobothow FROM {$prefix}bobotkpi WHERE id_user='$user_id' LIMIT 1";
    $result_bobot = mysqli_query($conn, $sql_bobot);
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

// ==================== SAVE KPI HISTORY FUNCTION ====================
function saveKPIHistory($conn, $user_id, $kpi_real, $kpi_sim) {
    $bulan = date('Y-m');
    
    // ✅ VALIDASI: Pastikan user_id sesuai dengan data yang akan disimpan
    if (!empty($kpi_real['kpi_details'])) {
        $first_kpi_id = $kpi_real['kpi_details'][0]['id'];
        $check_owner = mysqli_query($conn, "SELECT id_user FROM tb_kpi WHERE id='$first_kpi_id' LIMIT 1");
        
        if ($check_owner && mysqli_num_rows($check_owner) > 0) {
            $owner = mysqli_fetch_assoc($check_owner);
            if ($owner['id_user'] != $user_id) {
                return false;
            }
        }
    }
    
    // Check if table exists
    $check_table = mysqli_query($conn, "SHOW TABLES LIKE 'tb_kpi_history'");
    if (mysqli_num_rows($check_table) == 0) {
        return false;
    }
    
    // ========== 1. SIMPAN/UPDATE SUMMARY ROW (DENGAN FINAL_WHAT DAN FINAL_HOW) ==========
    $check_summary = mysqli_query($conn, "SELECT id FROM tb_kpi_history 
                                          WHERE id_user='$user_id' 
                                          AND bulan='$bulan' 
                                          AND is_summary=1");
    
    // TAMBAHKAN NILAI FINAL WHAT DAN FINAL HOW
    $final_what = mysqli_real_escape_string($conn, $kpi_real['final_what']);
    $final_how = mysqli_real_escape_string($conn, $kpi_real['final_how']);
    $total_what = mysqli_real_escape_string($conn, $kpi_real['total_what']);
    $total_how = mysqli_real_escape_string($conn, $kpi_real['total_how']);
    $total_kpi_real = mysqli_real_escape_string($conn, $kpi_real['total_kpi']);
    $total_kpi_target = mysqli_real_escape_string($conn, $kpi_sim['total_kpi']);
    $bobot_what = mysqli_real_escape_string($conn, $kpi_real['bobot_what']);
    $bobot_how = mysqli_real_escape_string($conn, $kpi_real['bobot_how']);
    
    if (mysqli_num_rows($check_summary) > 0) {
        // Update summary - TAMBAHKAN bobot_what, bobot_how, nilai_what, nilai_how
        $sql_summary = "UPDATE tb_kpi_history SET 
                       total_kpi_real = '$total_kpi_real',
                       total_kpi_target = '$total_kpi_target',
                       total_what = '$total_what',
                       total_how = '$total_how',
                       bobot_what = '$bobot_what',
                       bobot_how = '$bobot_how',
                       nilai_what = '$final_what',
                       nilai_how = '$final_how'
                       WHERE id_user='$user_id' 
                       AND bulan='$bulan' 
                       AND is_summary=1";
    } else {
        // Insert summary - TAMBAHKAN bobot_what, bobot_how, nilai_what, nilai_how
        $sql_summary = "INSERT INTO tb_kpi_history 
                       (id_user, id_kpi, bulan, is_summary, 
                        total_kpi_real, total_kpi_target, 
                        total_what, total_how,
                        bobot_what, bobot_how,
                        nilai_what, nilai_how) 
                       VALUES 
                       ('$user_id', NULL, '$bulan', 1, 
                        '$total_kpi_real', '$total_kpi_target', 
                        '$total_what', '$total_how',
                        '$bobot_what', '$bobot_how',
                        '$final_what', '$final_how')";
    }
    
    if (!mysqli_query($conn, $sql_summary)) {
        return false;
    }
    
    // ========== 2. SIMPAN/UPDATE DETAIL ROWS ==========
    foreach ($kpi_real['kpi_details'] as $detail) {
        $id_kpi = mysqli_real_escape_string($conn, $detail['id']);
        $poin_what = mysqli_real_escape_string($conn, $detail['poin_what']);
        $poin_how = mysqli_real_escape_string($conn, $detail['poin_how']);
        $bobot_what = mysqli_real_escape_string($conn, $detail['bobot_what']);
        $bobot_how = mysqli_real_escape_string($conn, $detail['bobot_how']);
        $total_what_raw = mysqli_real_escape_string($conn, $detail['total_what_raw']);
        $total_how_raw = mysqli_real_escape_string($conn, $detail['total_how_raw']);
        $nilai_what = mysqli_real_escape_string($conn, $detail['nilai_what']);
        $nilai_how = mysqli_real_escape_string($conn, $detail['nilai_how']);
        
        // Check if detail exists
        $check_detail = mysqli_query($conn, "SELECT id FROM tb_kpi_history 
                                             WHERE id_user='$user_id' 
                                             AND id_kpi='$id_kpi' 
                                             AND bulan='$bulan'
                                             AND is_summary=0");
        
        if (mysqli_num_rows($check_detail) > 0) {
            // Update detail
            $sql_detail = "UPDATE tb_kpi_history SET 
                          poin_what = '$poin_what',
                          poin_how = '$poin_how',
                          bobot_what = '$bobot_what',
                          bobot_how = '$bobot_how',
                          total_what_raw = '$total_what_raw',
                          total_how_raw = '$total_how_raw',
                          nilai_what = '$nilai_what',
                          nilai_how = '$nilai_how'
                          WHERE id_user='$user_id' 
                          AND id_kpi='$id_kpi' 
                          AND bulan='$bulan'
                          AND is_summary=0";
        } else {
            // Insert detail
            $sql_detail = "INSERT INTO tb_kpi_history 
                          (id_user, id_kpi, bulan, is_summary, poin_what, poin_how, 
                           bobot_what, bobot_how, total_what_raw, total_how_raw, 
                           nilai_what, nilai_how) 
                          VALUES 
                          ('$user_id', '$id_kpi', '$bulan', 0, '$poin_what', '$poin_how', 
                           '$bobot_what', '$bobot_how', '$total_what_raw', '$total_how_raw', 
                           '$nilai_what', '$nilai_how')";
        }
        
        mysqli_query($conn, $sql_detail);
    }
    
    return true;
}

// ==================== GET DETAILED KPI FROM HISTORY ====================
function getDetailedKPIFromHistory($conn, $user_id, $bulan) {
    $details = [];
    
    // Ambil detail rows (is_summary = 0)
    $sql = "SELECT * FROM tb_kpi_history 
            WHERE id_user='$user_id' 
            AND bulan='$bulan'
            AND is_summary=0
            ORDER BY id ASC";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result || mysqli_num_rows($result) == 0) {
        return null;
    }
    
    while ($row = mysqli_fetch_assoc($result)) {
        $details[] = [
            'id' => $row['id_kpi'],
            'poin_what' => $row['poin_what'],
            'poin_how' => $row['poin_how'],
            'bobot_what' => $row['bobot_what'],
            'bobot_how' => $row['bobot_how'],
            'total_what_raw' => $row['total_what_raw'],
            'total_how_raw' => $row['total_how_raw'],
            'nilai_what' => $row['nilai_what'],
            'nilai_how' => $row['nilai_how']
        ];
    }
    
    return $details;
}

// Calculate KPI for filtered user
if ($user_level == 1) {
    $filter_user = $id_user; // Paksa filter_user = diri sendiri
}

$kpi_real = calculateKPI($conn, $filter_user, false);
$kpi_sim = calculateKPI($conn, $filter_user, true);

// ✅ HANYA SIMPAN HISTORY JIKA USER MELIHAT DATA DIRINYA SENDIRI
if ($filter_user == $id_user) {
    saveKPIHistory($conn, $id_user, $kpi_real, $kpi_sim);
}

// Get user info
$sql_user_info = "SELECT * FROM tb_users WHERE id='$filter_user'";
$result_user_info = mysqli_query($conn, $sql_user_info);
$user_info = mysqli_fetch_assoc($result_user_info);

// ==================== GET DETAILED KPI COMPARISON ====================
$bulan_ini = date('Y-m');
$bulan_kemarin = date('Y-m', strtotime('-1 month'));

// Ambil data summary bulan ini (is_summary = 1)
$sql_current = "SELECT * FROM tb_kpi_history 
                WHERE id_user='$filter_user' 
                AND bulan='$bulan_ini'
                AND is_summary=1";
$result_current = mysqli_query($conn, $sql_current);
$data_current = mysqli_fetch_assoc($result_current);

// Ambil data summary bulan kemarin (is_summary = 1)
$sql_previous = "SELECT * FROM tb_kpi_history 
                 WHERE id_user='$filter_user' 
                 AND bulan='$bulan_kemarin'
                 AND is_summary=1";
$result_previous = mysqli_query($conn, $sql_previous);
$data_previous = mysqli_fetch_assoc($result_previous);

// Jika tidak ada data bulan kemarin, set default 0
if (!$data_previous) {
    $data_previous = [
        'total_kpi_real' => 0,
        'total_what' => 0,
        'total_how' => 0
    ];
}

// Jika tidak ada data bulan ini, gunakan perhitungan real-time
if (!$data_current) {
    $data_current = [
        'total_kpi_real' => $kpi_real['total_kpi'],
        'total_what' => $kpi_real['total_what'],
        'total_how' => $kpi_real['total_how']
    ];
}

// Ambil detail KPI bulan ini (gunakan data real-time)
$kpi_current_detail = $kpi_real['kpi_details'];

// Ambil detail KPI bulan kemarin dari history
$kpi_previous_detail = getDetailedKPIFromHistory($conn, $filter_user, $bulan_kemarin);

// Jika tidak ada detail bulan kemarin, set null
if (!$kpi_previous_detail) {
    $kpi_previous_detail = [];
}

// ==================== TREND DATA (Last 6 months) - REAL DATA ====================
$trend_data = [];

$sql_trend = "SELECT 
    bulan,
    total_kpi_real AS `real`,
    total_kpi_target AS target
FROM tb_kpi_history
WHERE id_user = $filter_user
AND is_summary = 1
ORDER BY bulan DESC
LIMIT 6";

$result_trend = mysqli_query($conn, $sql_trend);

if (mysqli_num_rows($result_trend) > 0) {
    $temp_data = [];
    while ($row = mysqli_fetch_assoc($result_trend)) {
        $temp_data[] = $row;
    }

    $trend_data = array_reverse($temp_data);

    foreach ($trend_data as &$item) {
        $item['month'] = date('M Y', strtotime($item['bulan'] . '-01'));
    }

} else {
    $trend_data[] = [
        'month'  => date('M Y'),
        'real'   => $kpi_real['total_kpi'],
        'target' => $kpi_sim['total_kpi']
    ];
}

// ==================== DEPARTMENT/TEAM COMPARISON ====================
$dept_comparison = [];
$comparison_title = "Team Comparison";

if ($user_level >= 2) {

    if ($user_level == 2) {
        $kabag_name = $user_info['nama_lngkp'];
        $sql_dept_users = "SELECT id, nama_lngkp FROM tb_users 
                          WHERE atasan='$kabag_name' 
                          AND username NOT IN ('itboy', 'adminhrd')
                          ORDER BY nama_lngkp";
        $comparison_title = "My Team Members Performance";
    
    } elseif ($user_level == 3) {
        $kabag_name = $user_info['nama_lngkp'];
        $sql_dept_users = "SELECT id, nama_lngkp FROM tb_users 
                          WHERE atasan='$kabag_name' 
                          AND username NOT IN ('itboy', 'adminhrd')
                          ORDER BY nama_lngkp";
        $comparison_title = "My Team Members Performance";

    } elseif ($user_level == 4) {
        $target_dept = $user_info['departement'];
        $sql_dept_users = "SELECT id, nama_lngkp FROM tb_users 
                          WHERE departement='$target_dept' 
                          AND username NOT IN ('itboy', 'adminhrd')
                          ORDER BY nama_lngkp";
        $comparison_title = "Department Team - " . $target_dept;

    } elseif ($user_level >= 5) {
        // DIREKTUR - Cek apakah filter departemen kosong (All Departments)
        if (!empty($filter_departemen)) {
            // Filter berdasarkan departemen tertentu
            $target_dept = $filter_departemen;
            $sql_dept_users = "SELECT id, nama_lngkp FROM tb_users 
                              WHERE departement='$target_dept' 
                              AND username NOT IN ('itboy', 'adminhrd')
                              ORDER BY nama_lngkp";
            $comparison_title = "Department Team - " . $target_dept;
        } else {
            // TAMPILKAN SEMUA EMPLOYEE dari semua departemen
            $sql_dept_users = "SELECT id, nama_lngkp, departement FROM tb_users 
                              WHERE username NOT IN ('itboy', 'adminhrd')
                              AND departement IS NOT NULL 
                              AND departement != ''
                              ORDER BY departement, nama_lngkp";
            $comparison_title = "All Employees - All Departments";
        }
    }

    $result_dept_users = mysqli_query($conn, $sql_dept_users);

    if ($result_dept_users && mysqli_num_rows($result_dept_users) > 0) {
        while ($dept_user = mysqli_fetch_assoc($result_dept_users)) {
            $dept_kpi = calculateKPI($conn, $dept_user['id'], false);

            $name_display = $dept_user['nama_lngkp'];
            
            // Tambahkan nama departemen jika All Departments (untuk direktur)
            if ($user_level >= 5 && empty($filter_departemen)) {
                $name_display .= ' (' . $dept_user['departement'] . ')';
            }
            
            if ($dept_user['id'] == $id_user) {
                $name_display .= ' (You)';
            } elseif ($dept_user['id'] == $filter_user) {
                $name_display .= ' (Selected)';
            }

            $dept_comparison[] = [
                'id'    => $dept_user['id'],
                'name'  => $name_display,
                'score' => $dept_kpi['total_kpi']
            ];
        }

        // Sort berdasarkan skor tertinggi
        usort($dept_comparison, function ($a, $b) {
            return $b['score'] <=> $a['score'];
        });
    }
}
$dept_performance_analysis = [];

if ($user_level >= 5) {
    // Ambil semua departemen
    $sql_all_depts = "SELECT DISTINCT departement FROM tb_users 
                      WHERE departement IS NOT NULL AND departement != '' 
                      AND username NOT IN ('itboy', 'adminhrd')
                      ORDER BY departement";
    $result_all_depts = mysqli_query($conn, $sql_all_depts);
    
    while ($dept_row = mysqli_fetch_assoc($result_all_depts)) {
        $dept_name = $dept_row['departement'];
        
        // Hitung performance untuk setiap kategori di departemen ini
        $excellent_count = 0;
        $very_good_count = 0;
        $good_count = 0;
        $poor_count = 0;
        $total_members = 0;
        
        // Ambil semua user di departemen ini
        $sql_dept_members = "SELECT id FROM tb_users 
                            WHERE departement='$dept_name' 
                            AND username NOT IN ('itboy', 'adminhrd')";
        $result_dept_members = mysqli_query($conn, $sql_dept_members);
        
        while ($member = mysqli_fetch_assoc($result_dept_members)) {
            $member_kpi = calculateKPI($conn, $member['id'], false);
            $score = $member_kpi['total_kpi'];
            
            // Kategorikan berdasarkan score
            if ($score > 110) {
                $excellent_count++;
            } elseif ($score > 100) {
                $very_good_count++;
            } elseif ($score >= 90) {
                $good_count++;
            } else {
                $poor_count++;
            }
            
            $total_members++;
        }
        
        if ($total_members > 0) {
            $dept_performance_analysis[] = [
                'department' => $dept_name,
                'excellent' => $excellent_count,
                'very_good' => $very_good_count,
                'good' => $good_count,
                'poor' => $poor_count,
                'total' => $total_members,
                'excellent_percentage' => ($excellent_count / $total_members) * 100,
                'very_good_percentage' => ($very_good_count / $total_members) * 100,
                'good_percentage' => ($good_count / $total_members) * 100,
                'poor_percentage' => ($poor_count / $total_members) * 100
            ];
        }
    }
    
    // Sort by excellent count descending
    usort($dept_performance_analysis, function($a, $b) {
        return $b['excellent'] <=> $a['excellent'];
    });
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
                                        <!-- <div class="col-md-6 text-md-end">
                                            <button class="btn btn-light btn-sm no-print" onclick="window.print()">
                                                <i class="bi bi-printer me-1"></i>Print
                                            </button>
                                        </div> -->
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
                                        <?php if ($user_level == 1) { ?>
                                        <div class="col-md-3">
                                            <a href="dashboard" class="btn btn-outline-primary w-100">
                                                <i class="bi bi-speedometer2 me-2"></i>Real KPI Dashboard
                                            </a>
                                        </div>
                                        <?php } ?>
                                        
                                        <?php 
                                        if ($user_level >= 2) {

                                            $kpi_anggota_url = 'kpikabag'; // default level 2 (Koordinator)
                                            $kpi_label = 'KPI Anggota';

                                            if ($user_level == 3) {
                                                $kpi_anggota_url = 'kpikabag';   // Kabag
                                            } elseif ($user_level == 4) {
                                                $kpi_anggota_url = 'kpikadep';   // Kadep
                                            } elseif ($user_level >= 5) {
                                                $kpi_anggota_url = 'kpidirektur'; // Direktur
                                            }
                                        ?>
                                        <div class="col-md-3">
                                            <a href="<?= $kpi_anggota_url ?>" class="btn btn-outline-secondary w-100">
                                                <i class="bi bi-people-fill me-2"></i><?= $kpi_label ?>
                                            </a>
                                        </div>
                                        <?php } ?>
                                        
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

                                        <!-- 🔥 TAMBAHAN ARCHIVE (TANPA MENGUBAH KODE LAIN) -->
                                        <div class="col-md-3">
                                            <a href="archive" class="btn btn-outline-dark w-100">
                                                <i class="bi bi-archive me-2"></i>Archive KPI
                                            </a>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== FILTER SECTION ==================== -->
                    <?php if ($user_level >= 2) { ?>  <!-- UBAH INI -->
                    <div class="row mb-4 no-print">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filters & Options</h5>
                                </div>
                                <div class="card-body">
                                    <form method="GET" action="" id="filterForm">
                                        <div class="row g-3">
                                            
                                            <?php if ($user_level >= 5) { ?>  <!-- DIREKTUR: Dropdown Department -->
                                            <!-- Department Filter - Tampil duluan untuk level 5 -->
                                            <div class="col-md-3">
                                                <label class="form-label fw-bold">
                                                    Department 
                                                    <?php if (!empty($filter_departemen)) { ?>
                                                        <span class="badge bg-primary"><?= $filter_departemen ?></span>
                                                    <?php } ?>
                                                </label>
                                                <select name="filter_departemen" class="form-select" id="departmentSelect" onchange="this.form.submit()">
                                                    <option value="">All Departments</option>
                                                    <?php 
                                                    $sql_all_depts = "SELECT DISTINCT departement FROM tb_users 
                                                                    WHERE departement IS NOT NULL AND departement != '' 
                                                                    ORDER BY departement";
                                                    $result_all_depts = mysqli_query($conn, $sql_all_depts);
                                                    
                                                    while ($dept = mysqli_fetch_assoc($result_all_depts)) { 
                                                    ?>
                                                        <option value="<?= $dept['departement'] ?>" <?= $filter_departemen == $dept['departement'] ? 'selected' : '' ?>>
                                                            <?= $dept['departement'] ?>
                                                        </option>
                                                    <?php } ?>
                                                </select>
                                                <?php if (!empty($filter_departemen)) { ?>
                                                    <small class="text-muted">Employee list filtered by this department</small>
                                                <?php } ?>
                                            </div>
                                            <?php } ?>
                                            
                                            <!-- User Filter - Tampil untuk level 2 ke atas -->
                                            <div class="col-md-<?= $user_level >= 5 ? '3' : ($user_level == 4 ? '4' : '6') ?>">
                                                <label class="form-label fw-bold">
                                                    Select Employee
                                                    <?php if (isset($result_users) && mysqli_num_rows($result_users) > 0) { ?>
                                                        <span class="badge bg-secondary"><?= mysqli_num_rows($result_users) ?> users</span>
                                                    <?php } ?>
                                                </label>
                                                <select name="filter_user" class="form-select" id="userSelect" onchange="this.form.submit()">
                                                    <option value="<?= $id_user ?>" <?= $filter_user == $id_user ? 'selected' : '' ?>>My KPI</option>
                                                    <?php 
                                                    if (isset($result_users) && mysqli_num_rows($result_users) > 0) {
                                                        // Reset pointer result_users
                                                        mysqli_data_seek($result_users, 0);
                                                        
                                                        $current_dept = '';
                                                        while ($user = mysqli_fetch_assoc($result_users)) {
                                                            // Untuk level 5, tampilkan grouping by department
                                                            if ($user_level >= 5 && empty($filter_departemen)) {
                                                                if ($current_dept != $user['departement']) {
                                                                    if ($current_dept != '') echo '</optgroup>';
                                                                    echo '<optgroup label="' . htmlspecialchars($user['departement']) . '">';
                                                                    $current_dept = $user['departement'];
                                                                }
                                                            }
                                                    ?>
                                                            <option value="<?= $user['id'] ?>" <?= $filter_user == $user['id'] ? 'selected' : '' ?>>
                                                                <?= $user['nama_lngkp'] ?> - <?= $user['jabatan'] ?>
                                                            </option>
                                                    <?php 
                                                        }
                                                        // Close last optgroup if exists
                                                        if ($user_level >= 5 && empty($filter_departemen) && $current_dept != '') {
                                                            echo '</optgroup>';
                                                        }
                                                    } else {
                                                        echo '<option value="" disabled>No employees found</option>';
                                                    }
                                                    ?>
                                                </select>
                                                <?php if ($user_level >= 5 && empty($filter_departemen)) { ?>
                                                    <small class="text-muted">Select department first to filter employees</small>
                                                <?php } ?>
                                            </div>

                                            <?php if ($user_level == 4) { ?>
                                            <!-- Department (Read-only for Kadep) -->
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Department</label>
                                                <input type="text" class="form-control" value="<?= $user_info['departement'] ?>" readonly>
                                                <input type="hidden" name="filter_departemen" value="<?= $user_info['departement'] ?>">
                                                <small class="text-muted">Your department only</small>
                                            </div>
                                            <?php } ?>

                                            <!-- Reset Button -->
                                            <div class="col-md-<?= $user_level >= 5 ? '3' : ($user_level == 4 ? '4' : '6') ?>">
                                                <label class="form-label fw-bold">&nbsp;</label>
                                                <a href="dashboard-utama" class="btn btn-outline-secondary w-100">
                                                    <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                                                </a>
                                            </div>

                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- JavaScript untuk Clear Department -->
                    <script>
                    function clearDepartment() {
                        const form = document.getElementById('filterForm');
                        const deptSelect = document.getElementById('departmentSelect');
                        if (deptSelect) {
                            deptSelect.value = '';
                            form.submit();
                        }
                    }
                    </script>

                    <?php } ?>
                    <!-- AKHIR FILTER SECTION -->

                    <!-- ==================== DEPARTMENT/TEAM COMPARISON ==================== -->
                    <?php if (!empty($dept_comparison) && $user_level >= 2) { ?>
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0">
                                            <i class="bi bi-people me-2"></i>
                                            <?php 
                                            // Tentukan judul berdasarkan level
                                            if ($user_level == 2) {
                                                echo "My Team Performance (Koordinator)";
                                            } elseif ($user_level == 3) {
                                                echo "My Team Performance (Manager)";
                                            } elseif ($user_level == 4) {
                                                echo $user_info['departement'] . " Department (Kadep)";
                                            } elseif ($user_level >= 5) {
                                                echo !empty($filter_departemen) ? $filter_departemen . " Department (Direktur)" : "All Departments (Direktur)";
                                            } else {
                                                echo $comparison_title;
                                            }
                                            ?>
                                        </h5>
                                        <div>
                                            <span class="badge bg-primary"><?= count($dept_comparison) ?> Members</span>
                                            <?php if ($user_level == 4) { ?>
                                                <span class="badge bg-info ms-1"><?= $user_info['departement'] ?></span>
                                            <?php } elseif ($user_level >= 5 && !empty($filter_departemen)) { ?>
                                                <span class="badge bg-info ms-1"><?= $filter_departemen ?></span>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <!-- Container dengan scroll -->
                                    <div style="max-height: 500px; overflow-y: auto; overflow-x: hidden;">
                                        <div class="chart-container" style="height: <?= max(300, count($dept_comparison) * 30) ?>px;">
                                            <canvas id="deptComparisonChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

                    <!-- ==================== DEPARTMENT PERFORMANCE ANALYSIS (DIREKTUR ONLY) ==================== -->
                    <?php if ($user_level >= 5 && !empty($dept_performance_analysis)) { ?>
                    <div class="row mb-4">
                        <!-- Pie Chart - Excellent Performance by Department -->
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                    <h5 class="mb-0">
                                        <i class="bi bi-trophy-fill me-2"></i>Excellent Performance Distribution by Department
                                    </h5>
                                    <small class="opacity-90">Departments with Score > 110</small>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="excellentByDeptChart"></canvas>
                                    </div>
                                    <div class="mt-3">
                                        <div class="alert alert-info mb-0">
                                            <i class="bi bi-info-circle me-2"></i>
                                            <strong>Total Excellent Performers:</strong> 
                                            <?php 
                                            $total_excellent = array_sum(array_column($dept_performance_analysis, 'excellent'));
                                            echo $total_excellent; 
                                            ?> employees across all departments
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Department Performance Summary Table -->
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="bi bi-table me-2"></i>Department Performance Summary
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                        <table class="table table-sm table-hover">
                                            <thead class="table-light sticky-top">
                                                <tr>
                                                    <th style="width: 30%;">Department</th>
                                                    <th class="text-center" style="width: 15%;">
                                                        <span class="badge bg-success">Excellent</span>
                                                    </th>
                                                    <th class="text-center" style="width: 15%;">
                                                        <span class="badge bg-primary">Very Good</span>
                                                    </th>
                                                    <th class="text-center" style="width: 15%;">
                                                        <span class="badge bg-warning">Good</span>
                                                    </th>
                                                    <th class="text-center" style="width: 15%;">
                                                        <span class="badge bg-danger">Poor</span>
                                                    </th>
                                                    <th class="text-center" style="width: 10%;">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($dept_performance_analysis as $dept) { ?>
                                                <tr>
                                                    <td class="fw-bold"><?= htmlspecialchars($dept['department']) ?></td>
                                                    <td class="text-center">
                                                        <span class="badge bg-success">
                                                            <?= $dept['excellent'] ?> (<?= number_format($dept['excellent_percentage'], 1) ?>%)
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary">
                                                            <?= $dept['very_good'] ?> (<?= number_format($dept['very_good_percentage'], 1) ?>%)
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-warning text-dark">
                                                            <?= $dept['good'] ?> (<?= number_format($dept['good_percentage'], 1) ?>%)
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-danger">
                                                            <?= $dept['poor'] ?> (<?= number_format($dept['poor_percentage'], 1) ?>%)
                                                        </span>
                                                    </td>
                                                    <td class="text-center fw-bold"><?= $dept['total'] ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                            <tfoot class="table-light">
                                                <tr class="fw-bold">
                                                    <td>TOTAL</td>
                                                    <td class="text-center">
                                                        <?= array_sum(array_column($dept_performance_analysis, 'excellent')) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= array_sum(array_column($dept_performance_analysis, 'very_good')) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= array_sum(array_column($dept_performance_analysis, 'good')) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= array_sum(array_column($dept_performance_analysis, 'poor')) ?>
                                                    </td>
                                                    <td class="text-center">
                                                        <?= array_sum(array_column($dept_performance_analysis, 'total')) ?>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- All Departments Performance Overview -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">
                                        <i class="bi bi-bar-chart-fill me-2"></i>Performance Distribution Across All Departments
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container" style="height: 400px;">
                                        <canvas id="allDeptPerformanceChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>

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
                                    <h5 class="mb-0"><i class="bi bi-pie-chart me-2"></i>Real vs Simulasi</h5>
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
                        <!-- WHAT Comparison: Real vs Simulasi -->
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-bullseye me-2"></i>WHAT Breakdown - Real vs Simulasi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="kpiBreakdownWhat"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- HOW Comparison: Real vs Simulasi -->
                        <div class="col-lg-6 mb-3">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0"><i class="bi bi-gear-fill me-2"></i>HOW Breakdown - Real vs Simulasi</h5>
                                </div>
                                <div class="card-body">
                                    <div class="chart-container">
                                        <canvas id="kpiBreakdownHow"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== DETAILED TABLES ==================== -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-header bg-light">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0"><i class="bi bi-table me-2"></i>Detailed KPI Analysis - Month over Month Comparison</h5>
                                        <div>
                                            <span class="badge bg-primary me-2"><?= date('M Y') ?> (Current)</span>
                                            <span class="badge bg-secondary"><?= date('M Y', strtotime('-1 month')) ?> (Previous)</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    
                                    <!-- Summary Comparison -->
                                    <!-- <div class="row mb-4">
                                        <div class="col-md-4">
                                            <div class="card border-primary">
                                                <div class="card-body text-center">
                                                    <h6 class="text-muted mb-2">Total KPI</h6>
                                                    <div class="d-flex justify-content-around align-items-center">
                                                        <div>
                                                            <small class="text-muted">Current</small>
                                                            <h4 class="text-primary mb-0"><?= number_format($data_current['total_kpi_real'], 2) ?></h4>
                                                        </div>
                                                        <div>
                                                            <i class="bi bi-arrow-right text-muted fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Previous</small>
                                                            <h4 class="text-secondary mb-0"><?= number_format($data_previous['total_kpi_real'], 2) ?></h4>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                    $delta_total = $data_current['total_kpi_real'] - $data_previous['total_kpi_real'];
                                                    $growth_total = $data_previous['total_kpi_real'] > 0 
                                                        ? (($data_current['total_kpi_real'] - $data_previous['total_kpi_real']) / $data_previous['total_kpi_real']) * 100 
                                                        : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <span class="badge bg-<?= $delta_total >= 0 ? 'success' : 'danger' ?> fs-6">
                                                            <?= $delta_total >= 0 ? '▲' : '▼' ?> <?= number_format(abs($delta_total), 2) ?> 
                                                            (<?= number_format(abs($growth_total), 1) ?>%)
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card border-info">
                                                <div class="card-body text-center">
                                                    <h6 class="text-muted mb-2">WHAT Score</h6>
                                                    <div class="d-flex justify-content-around align-items-center">
                                                        <div>
                                                            <small class="text-muted">Current</small>
                                                            <h4 class="text-primary mb-0"><?= number_format($data_current['total_what'], 2) ?></h4>
                                                        </div>
                                                        <div>
                                                            <i class="bi bi-arrow-right text-muted fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Previous</small>
                                                            <h4 class="text-secondary mb-0"><?= number_format($data_previous['total_what'], 2) ?></h4>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                    $delta_what = $data_current['total_what'] - $data_previous['total_what'];
                                                    $growth_what = $data_previous['total_what'] > 0 
                                                        ? (($data_current['total_what'] - $data_previous['total_what']) / $data_previous['total_what']) * 100 
                                                        : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <span class="badge bg-<?= $delta_what >= 0 ? 'success' : 'danger' ?> fs-6">
                                                            <?= $delta_what >= 0 ? '▲' : '▼' ?> <?= number_format(abs($delta_what), 2) ?> 
                                                            (<?= number_format(abs($growth_what), 1) ?>%)
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="card border-success">
                                                <div class="card-body text-center">
                                                    <h6 class="text-muted mb-2">HOW Score</h6>
                                                    <div class="d-flex justify-content-around align-items-center">
                                                        <div>
                                                            <small class="text-muted">Current</small>
                                                            <h4 class="text-primary mb-0"><?= number_format($data_current['total_how'], 2) ?></h4>
                                                        </div>
                                                        <div>
                                                            <i class="bi bi-arrow-right text-muted fs-4"></i>
                                                        </div>
                                                        <div>
                                                            <small class="text-muted">Previous</small>
                                                            <h4 class="text-secondary mb-0"><?= number_format($data_previous['total_how'], 2) ?></h4>
                                                        </div>
                                                    </div>
                                                    <?php 
                                                    $delta_how = $data_current['total_how'] - $data_previous['total_how'];
                                                    $growth_how = $data_previous['total_how'] > 0 
                                                        ? (($data_current['total_how'] - $data_previous['total_how']) / $data_previous['total_how']) * 100 
                                                        : 0;
                                                    ?>
                                                    <div class="mt-2">
                                                        <span class="badge bg-<?= $delta_how >= 0 ? 'success' : 'danger' ?> fs-6">
                                                            <?= $delta_how >= 0 ? '▲' : '▼' ?> <?= number_format(abs($delta_how), 2) ?> 
                                                            (<?= number_format(abs($growth_how), 1) ?>%)
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div> -->

                                    <!-- Detailed WHAT Comparison -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-primary mb-3">
                                            <i class="bi bi-bullseye me-2"></i>WHAT (Objective) - Detailed Comparison
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm">
                                                <thead class="table-primary">
                                                    <tr>
                                                        <th style="width: 5%;" class="text-center">#</th>
                                                        <th style="width: 35%;">KPI Item</th>
                                                        <th class="text-center" style="width: 10%;">Weight</th>
                                                        <th class="text-center" style="width: 12%;">Current Month</th>
                                                        <th class="text-center" style="width: 12%;">Previous Month</th>
                                                        <th class="text-center" style="width: 12%;">Δ Change</th>
                                                        <th class="text-center" style="width: 10%;">Growth %</th>
                                                        <th class="text-center" style="width: 4%;">Trend</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach ($kpi_current_detail as $idx => $detail) { 
                                                        // Cari data bulan lalu berdasarkan id_kpi yang sama
                                                        $previous_value = 0;
                                                        $previous_raw = 0;
                                                        
                                                        if (!empty($kpi_previous_detail)) {
                                                            foreach ($kpi_previous_detail as $prev) {
                                                                if ($prev['id'] == $detail['id']) {
                                                                    $previous_value = $prev['nilai_what'];
                                                                    $previous_raw = $prev['total_what_raw'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        
                                                        $delta = $detail['nilai_what'] - $previous_value;
                                                        $growth = $previous_value > 0 
                                                            ? (($detail['nilai_what'] - $previous_value) / $previous_value) * 100 
                                                            : 0;
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td>
                                                            <div class="fw-semibold text-dark fs-6">
                                                                <?= htmlspecialchars($detail['poin_what']) ?>
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning text-dark"><?= $detail['bobot_what'] ?>%</span>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <strong class="text-primary fw-semibold fs-6">
                                                                <?= number_format($detail['nilai_what'], 2) ?>
                                                            </strong>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <div class="fw-semibold text-dark fs-6">
                                                                    <?= number_format($previous_value, 2) ?>
                                                                </div>
                                                                <div class="text-body-secondary fs-7">
                                                                    (Total: <?= number_format($previous_raw, 2) ?>)
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="fw-semibold text-secondary fs-6">-</div>
                                                                <div class="text-body-secondary fs-7">No data</div>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <span class="badge bg-<?= $delta >= 0 ? 'success' : 'danger' ?>">
                                                                    <?= $delta >= 0 ? '+' : '' ?><?= number_format($delta, 2) ?>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="text-muted">-</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <span class="badge bg-<?= $growth >= 0 ? 'success' : 'danger' ?>">
                                                                    <?= number_format(abs($growth), 1) ?>%
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">New</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <?php if ($growth > 0) { ?>
                                                                    <i class="bi bi-arrow-up-circle-fill text-success fs-5"></i>
                                                                <?php } elseif ($growth < -0    ) { ?>
                                                                    <i class="bi bi-arrow-down-circle-fill text-danger fs-5"></i>
                                                                <?php } else { ?>
                                                                    <i class="bi bi-dash-circle-fill text-warning fs-5"></i>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <i class="bi bi-plus-circle-fill text-info fs-5"></i>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr class="table-light fw-bold">
                                                        <td colspan="3" class="text-end">TOTAL WHAT:</td>
                                                        <td class="text-center text-primary"><?= number_format($data_current['total_what'], 2) ?></td>
                                                        <td class="text-center text-secondary"><?= number_format($data_previous['total_what'], 2) ?></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-<?= $delta_what >= 0 ? 'success' : 'danger' ?> fs-6">
                                                                <?= $delta_what >= 0 ? '+' : '' ?><?= number_format($delta_what, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-<?= $growth_what >= 0 ? 'success' : 'danger' ?> fs-6">
                                                                <?= number_format(abs($growth_what), 1) ?>%
                                                            </span>
                                                        </td>
                                                        <td></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <hr class="my-4">

                                    <!-- Detailed HOW Comparison -->
                                    <div class="mb-4">
                                        <h6 class="fw-bold text-success mb-3">
                                            <i class="bi bi-gear-fill me-2"></i>HOW (Method) - Detailed Comparison
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-sm">
                                                <thead class="table-success">
                                                    <tr>
                                                        <th style="width: 5%;" class="text-center">#</th>
                                                        <th style="width: 35%;">KPI Item</th>
                                                        <th class="text-center" style="width: 10%;">Weight</th>
                                                        <th class="text-center" style="width: 12%;">Current Month</th>
                                                        <th class="text-center" style="width: 12%;">Previous Month</th>
                                                        <th class="text-center" style="width: 12%;">Δ Change</th>
                                                        <th class="text-center" style="width: 10%;">Growth %</th>
                                                        <th class="text-center" style="width: 4%;">Trend</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php 
                                                    $no = 1;
                                                    foreach ($kpi_current_detail as $idx => $detail) { 
                                                        // Cari data bulan lalu berdasarkan id_kpi yang sama
                                                        $previous_value = 0;
                                                        $previous_raw = 0;
                                                        
                                                        if (!empty($kpi_previous_detail)) {
                                                            foreach ($kpi_previous_detail as $prev) {
                                                                if ($prev['id'] == $detail['id']) {
                                                                    $previous_value = $prev['nilai_how'];
                                                                    $previous_raw = $prev['total_how_raw'];
                                                                    break;
                                                                }
                                                            }
                                                        }
                                                        
                                                        $delta = $detail['nilai_how'] - $previous_value;
                                                        $growth = $previous_value > 0 
                                                            ? (($detail['nilai_how'] - $previous_value) / $previous_value) * 100 
                                                            : 0;
                                                    ?>
                                                    <tr>
                                                        <td class="text-center"><?= $no++ ?></td>
                                                        <td>
                                                           <div class="fw-semibold text-dark fs-6">
                                                                <?= htmlspecialchars($detail['poin_how']) ?>
                                                           </div>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-warning text-dark"><?= $detail['bobot_how'] ?>%</span>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <strong class="text-success fw-semibold fs-6">
                                                                <?= number_format($detail['nilai_how'], 2) ?>
                                                            </strong>
                                                        </td>
                                                        <td class="text-center align-middle">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <div class="fw-semibold text-dark fs-6">
                                                                    <?= number_format($previous_value, 2) ?>
                                                                </div>
                                                                <div class="text-body-secondary fs-7">
                                                                    (Total: <?= number_format($previous_raw, 2) ?>)
                                                                </div>
                                                            <?php } else { ?>
                                                                <div class="fw-semibold text-secondary fs-6">-</div>
                                                                <div class="text-body-secondary fs-7">No data</div>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <span class="badge bg-<?= $delta >= 0 ? 'success' : 'danger' ?>">
                                                                    <?= $delta >= 0 ? '+' : '' ?><?= number_format($delta, 2) ?>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="text-muted">-</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <span class="badge bg-<?= $growth >= 0 ? 'success' : 'danger' ?>">
                                                                    <?= number_format(abs($growth), 1) ?>%
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">New</span>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php if ($previous_value > 0) { ?>
                                                                <?php if ($growth > 0) { ?>
                                                                    <i class="bi bi-arrow-up-circle-fill text-success fs-5"></i>
                                                                <?php } elseif ($growth < -0) { ?>
                                                                    <i class="bi bi-arrow-down-circle-fill text-danger fs-5"></i>
                                                                <?php } else { ?>
                                                                    <i class="bi bi-dash-circle-fill text-warning fs-5"></i>
                                                                <?php } ?>
                                                            <?php } else { ?>
                                                                <i class="bi bi-plus-circle-fill text-info fs-5"></i>
                                                            <?php } ?>
                                                        </td>
                                                    </tr>
                                                    <?php } ?>
                                                    <tr class="table-light fw-bold">
                                                        <td colspan="3" class="text-end">TOTAL HOW:</td>
                                                        <td class="text-center text-success"><?= number_format($data_current['total_how'], 2) ?></td>
                                                        <td class="text-center text-secondary"><?= number_format($data_previous['total_how'], 2) ?></td>
                                                        <td class="text-center">
                                                            <span class="badge bg-<?= $delta_how >= 0 ? 'success' : 'danger' ?> fs-6">
                                                                <?= $delta_how >= 0 ? '+' : '' ?><?= number_format($delta_how, 2) ?>
                                                            </span>
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="badge bg-<?= $growth_how >= 0 ? 'success' : 'danger' ?> fs-6">
                                                                <?= number_format(abs($growth_how), 1) ?>%
                                                            </span>
                                                        </td>
                                                        <td></td>
                                                    </tr>
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

        // Data dari PHP
        const trendLabels = <?= json_encode(array_column($trend_data, 'month')) ?>;
        const trendRealData = <?= json_encode(array_column($trend_data, 'real')) ?>;
        const trendTargetData = <?= json_encode(array_column($trend_data, 'target')) ?>;

        // Validasi data tidak kosong
        if (trendLabels.length > 0) {
            const trendChart = new Chart(trendCtx, {
                type: 'line',
                data: {
                    labels: trendLabels,
                    datasets: [{
                        label: 'Real Performance',
                        data: trendRealData,
                        borderColor: '#0d6efd',
                        backgroundColor: 'rgba(13, 110, 253, 0.1)',
                        tension: 0.4,
                        fill: true
                    }, {
                        label: 'Simulasi',
                        data: trendTargetData,
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
                        y: { 
                            beginAtZero: false,
                            ticks: {
                                callback: function(value) {
                                    return value.toFixed(2);
                                }
                            }
                        }
                    }
                }
            });
        } else {
            // Jika tidak ada data, tampilkan pesan
            document.getElementById('trendChart').parentElement.innerHTML = 
                '<p class="text-center text-muted">Belum ada data history. Data akan tersimpan setiap bulan.</p>';
        }

        // Real vs Target Chart
        const realVsTargetCtx = document.getElementById('realVsTargetChart').getContext('2d');

        // Hitung persentase pencapaian
        const realValue = <?= $kpi_real['total_kpi'] ?>;
        const targetValue = <?= $kpi_sim['total_kpi'] ?>;
        const achievementPercentage = (realValue / targetValue) * 100;

        // Tentukan data dan warna berdasarkan pencapaian
        let chartData, chartColors, chartLabels;

        if (realValue >= targetValue) {
            // Jika real sudah mencapai atau melebihi target, tampilkan biru penuh
            chartData = [100];
            chartColors = ['#0d6efd'];
            chartLabels = ['Target Tercapai ✓'];
        } else {
            // Jika belum mencapai target, tampilkan perbandingan
            chartData = [realValue, targetValue - realValue];
            chartColors = ['#0d6efd', '#198754'];
            chartLabels = ['Real Performance', 'Gap to Target'];
        }

        const realVsTargetChart = new Chart(realVsTargetCtx, {
            type: 'doughnut',
            data: {
                labels: chartLabels,
                datasets: [{
                    data: chartData,
                    backgroundColor: chartColors,
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
                                if (realValue >= targetValue) {
                                    return 'Real: ' + realValue.toFixed(2) + ' / Target: ' + targetValue.toFixed(2) + ' (100%)';
                                } else {
                                    let label = context.label || '';
                                    if (label === 'Real Performance') {
                                        return 'Real: ' + realValue.toFixed(2) + ' (' + achievementPercentage.toFixed(1) + '%)';
                                    } else {
                                        let gap = targetValue - realValue;
                                        return 'Gap: ' + gap.toFixed(2) + ' (' + (100 - achievementPercentage).toFixed(1) + '%)';
                                    }
                                }
                            }
                        }
                    }
                }
            }
        });

        // KPI Breakdown WHAT - Real vs Simulasi
        const breakdownWhatCtx = document.getElementById('kpiBreakdownWhat').getContext('2d');
        const breakdownWhatChart = new Chart(breakdownWhatCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($detail) {
                    return substr($detail['poin_what'], 0, 20) . (strlen($detail['poin_what']) > 20 ? '...' : '');
                }, $kpi_real['kpi_details'])) ?>,
                datasets: [{
                    label: 'Real WHAT',
                    data: <?= json_encode(array_column($kpi_real['kpi_details'], 'nilai_what')) ?>,
                    backgroundColor: '#0d6efd',
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                }, {
                    label: 'Simulasi WHAT',
                    data: <?= json_encode(array_column($kpi_sim['kpi_details'], 'nilai_what')) ?>,
                    backgroundColor: '#198754',
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: { 
                    x: { 
                        display: true,
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    }
                }
            }
        });

        // KPI Breakdown HOW - Real vs Simulasi
        const breakdownHowCtx = document.getElementById('kpiBreakdownHow').getContext('2d');
        const breakdownHowChart = new Chart(breakdownHowCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_map(function($detail) {
                    return substr($detail['poin_how'], 0, 20) . (strlen($detail['poin_how']) > 20 ? '...' : '');
                }, $kpi_real['kpi_details'])) ?>,
                datasets: [{
                    label: 'Real HOW',
                    data: <?= json_encode(array_column($kpi_real['kpi_details'], 'nilai_how')) ?>,
                    backgroundColor: '#0d6efd',
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                }, {
                    label: 'Simulasi HOW',
                    data: <?= json_encode(array_column($kpi_sim['kpi_details'], 'nilai_how')) ?>,
                    backgroundColor: '#198754',
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + context.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: { 
                    x: { 
                        display: true,
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Score'
                        }
                    }
                }
            }
        });

        <?php if (!empty($dept_comparison) && count($dept_comparison) > 0) { ?>
        // Department/Team Comparison Chart
        const deptCompCtx = document.getElementById('deptComparisonChart').getContext('2d');

        // Generate colors dynamically based on score status
        const deptColors = <?= json_encode(array_map(function($member) {
            $score = $member['score'];
            
            // Tentukan warna berdasarkan nilai
            if ($score < 90) {
                return '#dc3545'; // Merah untuk POOR
            } elseif ($score <= 100) {
                return '#ffc107'; // Kuning untuk GOOD
            } elseif ($score <= 110) {
                return '#0d6efd'; // Biru untuk VERY GOOD
            } else {
                return '#198754'; // Hijau untuk EXCELLENT
            }
        }, $dept_comparison)) ?>;

        const deptCompChart = new Chart(deptCompCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($dept_comparison, 'name')) ?>,
                datasets: [{
                    label: 'KPI Score',
                    data: <?= json_encode(array_column($dept_comparison, 'score')) ?>,
                    backgroundColor: deptColors,
                    borderRadius: 5,
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const score = context.parsed.x;
                                let status = '';
                                
                                // Tampilkan status di tooltip
                                if (score < 90) {
                                    status = 'POOR';
                                } else if (score <= 100) {
                                    status = 'GOOD';
                                } else if (score <= 110) {
                                    status = 'VERY GOOD';
                                } else {
                                    status = 'EXCELLENT';
                                }
                                
                                return 'Score: ' + score.toFixed(2) + ' (' + status + ')';
                            }
                        }
                    }
                },
                scales: { 
                    x: { 
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'KPI Score'
                        }
                    },
                    y: {
                        ticks: {
                            autoSkip: false
                        }
                    }
                }
            }
        });
        <?php } ?>

        <?php if ($user_level >= 5 && !empty($dept_performance_analysis)) { ?>
        // Excellent Performance by Department - Pie Chart
        const excellentByDeptCtx = document.getElementById('excellentByDeptChart').getContext('2d');

        const deptExcellentData = {
            labels: <?= json_encode(array_column($dept_performance_analysis, 'department')) ?>,
            datasets: [{
                data: <?= json_encode(array_column($dept_performance_analysis, 'excellent')) ?>,
                backgroundColor: [
                    '#198754', // Green
                    '#0d6efd', // Blue
                    '#ffc107', // Yellow
                    '#dc3545', // Red
                    '#6f42c1', // Purple
                    '#fd7e14', // Orange
                    '#20c997', // Teal
                    '#e83e8c', // Pink
                    '#6c757d', // Gray
                    '#17a2b8'  // Cyan
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        const excellentByDeptChart = new Chart(excellentByDeptCtx, {
            type: 'pie',
            data: deptExcellentData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 15,
                            font: { size: 11 },
                            generateLabels: function(chart) {
                                const data = chart.data;
                                if (data.labels.length && data.datasets.length) {
                                    const total = data.datasets[0].data.reduce((a, b) => a + b, 0);
                                    return data.labels.map((label, i) => {
                                        const value = data.datasets[0].data[i];
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        const meta = chart.getDatasetMeta(0);
                                        const hidden = meta.data[i].hidden;
                                        
                                        return {
                                            text: `${label}: ${value} (${percentage}%)`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: hidden,
                                            index: i,
                                            // Tambahkan style untuk strikethrough
                                            fontColor: hidden ? '#999' : '#666',
                                            lineWidth: hidden ? 0 : 1
                                        };
                                    });
                                }
                                return [];
                            }
                        },
                        // Tambahkan onClick handler
                        onClick: function(e, legendItem, legend) {
                            const index = legendItem.index;
                            const chart = legend.chart;
                            const meta = chart.getDatasetMeta(0);
                            
                            // Toggle visibility
                            meta.data[index].hidden = !meta.data[index].hidden;
                            
                            chart.update();
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                return label + ': ' + value + ' employees (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });

        // All Department Performance - Stacked Bar Chart
        const allDeptPerfCtx = document.getElementById('allDeptPerformanceChart').getContext('2d');

        const allDeptPerfChart = new Chart(allDeptPerfCtx, {
            type: 'bar',
            data: {
                labels: <?= json_encode(array_column($dept_performance_analysis, 'department')) ?>,
                datasets: [
                    {
                        label: 'Excellent (>110)',
                        data: <?= json_encode(array_column($dept_performance_analysis, 'excellent')) ?>,
                        backgroundColor: '#198754',
                        borderRadius: 5,
                        borderWidth: 1,
                        borderColor: '#fff'
                    },
                    {
                        label: 'Very Good (100-110)',
                        data: <?= json_encode(array_column($dept_performance_analysis, 'very_good')) ?>,
                        backgroundColor: '#0d6efd',
                        borderRadius: 5,
                        borderWidth: 1,
                        borderColor: '#fff'
                    },
                    {
                        label: 'Good (90-100)',
                        data: <?= json_encode(array_column($dept_performance_analysis, 'good')) ?>,
                        backgroundColor: '#ffc107',
                        borderRadius: 5,
                        borderWidth: 1,
                        borderColor: '#fff'
                    },
                    {
                        label: 'Poor (<90)',
                        data: <?= json_encode(array_column($dept_performance_analysis, 'poor')) ?>,
                        backgroundColor: '#dc3545',
                        borderRadius: 5,
                        borderWidth: 1,
                        borderColor: '#fff'
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            padding: 15,
                            font: { size: 12 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.dataset.label || '';
                                const value = context.parsed.y || 0;
                                return label + ': ' + value + ' employees';
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                        ticks: {
                            maxRotation: 45,
                            minRotation: 45,
                            font: { size: 10 }
                        }
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Number of Employees'
                        },
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
        // grafikanggota
        <?php } ?>
    </script>

</body>
</html>