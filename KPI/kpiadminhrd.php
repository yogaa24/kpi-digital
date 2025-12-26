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

    // Hanya Admin HRD yang bisa akses
    requireAdminHRD();
}

function getnilai($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zboth = 0;
    $zbotw = 0;

    $totalws = 0;
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;
    // ===============================================================================
    $totalhfg = 0;
    $resultfg = mysqli_query($conn, $sql);

    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];

        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;

    return number_format($zboth + $zbotw, 2);
}

function getWhatt($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zbotw = 0;

    $totalws = 0;
    $resultsaf = mysqli_query($conn, $sql);
    while ($hasils = mysqli_fetch_assoc($resultsaf)) {
        $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
        $result3s = mysqli_query($conn, $sql3s);
        $row3sd = mysqli_fetch_assoc($result3s);
        $totalnilaisd = $row3sd['total'];
        $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
        $totalws += $nilaiws;
    }
    $bobotkpid = 0;
    $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id";
    $result5a = mysqli_query($conn, $sql5a);
    while ($row5a = mysqli_fetch_assoc($result5a)) {
        $bobotkpid = $row5a['bw'];
    }
    $zbotw = ($totalws * $bobotkpid) / 100;
    return number_format($zbotw, 2);
}

function getHoww($conn, $id)
{
    $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
    $zboth = 0;
    $totalhfg = 0;
    $resultfg = mysqli_query($conn, $sql);

    while ($hasilfg = mysqli_fetch_assoc($resultfg)) {
        $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
        $result7fg = mysqli_query($conn, $sql7fg);
        $row7fg = mysqli_fetch_assoc($result7fg);
        $totalnilaihfg = $row7fg['totalh'];

        $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
        $totalhfg += $nilaihfg;
    }
    $bobotkpias = 0;
    $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id";
    $result8a = mysqli_query($conn, $sql8a);
    while ($row8a = mysqli_fetch_assoc($result8a)) {
        $bobotkpias = $row8a['bh'];
    }
    $zboth = ($totalhfg * $bobotkpias) / 100;
    return number_format($zboth, 2);
}

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
    <script type="text/javascript" src="datatables/datatables.min.js"></script>
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
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/kpikabag/k_nav.php"); ?>
        <?php include("pages/part/p_aside.php"); ?>
        
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
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card"
                                onclick="window.location.href='skillstandard'"
                                style="cursor:pointer; transition:transform 0.2s;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-people-fill text-info" style="font-size:3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Data User</h5>
                                    <p class="text-muted mb-0 small">Kelola data pengguna sistem</p>
                                </div>
                            </div>
                        </div>

                        <!-- Archive -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card"
                                onclick="window.location.href='archive'"
                                style="cursor:pointer; transition:transform 0.2s;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-archive-fill text-warning" style="font-size:3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Archive</h5>
                                    <p class="text-muted mb-0 small">Arsip dokumen & data historis</p>
                                </div>
                            </div>
                        </div>

                        <!-- Eviden -->
                        <div class="col-lg-4 col-md-6 mb-3">
                            <div class="card shadow-sm border-0 h-100 hover-card"
                                onclick="window.location.href='eviden'"
                                style="cursor:pointer; transition:transform 0.2s;">
                                <div class="card-body text-center p-4">
                                    <div class="mb-3">
                                        <i class="bi bi-folder-fill text-danger" style="font-size:3rem;"></i>
                                    </div>
                                    <h5 class="fw-bold mb-2">Eviden</h5>
                                    <p class="text-muted mb-0 small">Dokumentasi bukti & evidensi</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Statistics Summary -->
                    
                    <!-- Table KPI -->
                    <div class="table-responsive">
                        <table id="datatablenya" class="table align-middle table-hover table-bordered">
                            <thead class="table-dark">
                                <tr>
                                    <th width="3%">
                                        <center>No</center>
                                    </th>
                                    <th>
                                        <center>Nama Lengkap</center>
                                    </th>
                                    <th width="12%">
                                        <center>Jabatan</center>
                                    </th>
                                    <th width="12%">
                                        <center>Departemen</center>
                                    </th>
                                    <th width="12%">
                                        <center>Bagian</center>
                                    </th>
                                    <th width="8%">
                                        <center>What</center>
                                    </th>
                                    <th width="8%">
                                        <center>How</center>
                                    </th>
                                    <th width="8%">
                                        <center>Nilai</center>
                                    </th>
                                    <th width="10%">
                                        <center>KPI</center>
                                    </th>
                                    <th width="5%">
                                        <center>#</center>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                // Query untuk menampilkan semua user kecuali Admin HRD
                                // Urutkan: Kadep -> Kabag -> Karyawan
                                $sqlhd = "SELECT u.*, a.level
                                          FROM tb_users u
                                          INNER JOIN tb_auth a ON u.id = a.id_user
                                          WHERE u.id != $id_user AND u.jabatan != 'Admin HRD'
                                          ORDER BY 
                                              CASE 
                                                  WHEN u.jabatan = 'Kadep' THEN 1
                                                  WHEN u.jabatan = 'Kabag' THEN 2
                                                  WHEN u.jabatan = 'Karyawan' THEN 3
                                                  ELSE 4
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
                                    } elseif ($hasilsfa['jabatan'] == 'Kabag') {
                                        $badge_color = 'warning';
                                        $badge_icon = 'star-fill';
                                    } elseif ($hasilsfa['jabatan'] == 'Karyawan') {
                                        $badge_color = 'success';
                                        $badge_icon = 'person-check-fill';
                                    }
                                ?>
                                <tr>
                                    <td>
                                        <center><?= $no; ?></center>
                                    </td>
                                    <td style="padding-left: 20px;">
                                        <strong><?= $hasilsfa['nama_lngkp']; ?></strong>
                                        <br>
                                        <small class="text-muted">NIK: <?= $hasilsfa['nik']; ?></small>
                                    </td>
                                    <td>
                                        <center>
                                            <span class="badge bg-<?= $badge_color ?>">
                                                <i class="bi bi-<?= $badge_icon ?> me-1"></i>
                                                <?= $hasilsfa['jabatan']; ?>
                                            </span>
                                        </center>
                                    </td>
                                    <td>
                                        <center><?= $hasilsfa['departement']; ?></center>
                                    </td>
                                    <td>
                                        <center><?= $hasilsfa['bagian']; ?></center>
                                    </td>
                                    <td>
                                        <center><strong><?= getWhatt($conn, $hasilsfa['id']); ?></strong></center>
                                    </td>
                                    <td>
                                        <center><strong><?= getHoww($conn, $hasilsfa['id']); ?></strong></center>
                                    </td>
                                    <?php
                                    $nilair = getnilai($conn, $hasilsfa['id']);
                                    if ($nilair < 90) {
                                        $wrabs = "red";
                                        $badge_kpi = "danger";
                                    } elseif ($nilair <= 100) {
                                        $wrabs = "orange";
                                        $badge_kpi = "warning";
                                    } elseif ($nilair <= 110) {
                                        $wrabs = "green";
                                        $badge_kpi = "success";
                                    } else {
                                        $wrabs = "blue";
                                        $badge_kpi = "primary";
                                    }
                                    ?>
                                    <td style="color:<?= $wrabs ?>">
                                        <center><strong><?= getnilai($conn, $hasilsfa['id']); ?></strong></center>
                                    </td>
                                    <td>
                                        <center>
                                            <span class="badge bg-<?= $badge_kpi ?>">
                                                <?= getkpi(getnilai($conn, $hasilsfa['id'])); ?>
                                            </span>
                                        </center>
                                    </td>
                                    <td>
                                        <center>
                                            <div class="btn-group" role="group">
                                                <a href="kpianggota?id=<?= $hasilsfa['id']; ?>" 
                                                   class="btn btn-primary btn-sm" 
                                                   title="Lihat KPI">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="home-kpi-admin?id=<?= $hasilsfa['id']; ?>" 
                                                   class="btn btn-success btn-sm" 
                                                   title="Edit User">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                            </div>
                                        </center>
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
        </main>
        
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>

</html>