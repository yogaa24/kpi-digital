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
                echo "<script>alert('Surat Peringatan berhasil ditambahkan!'); window.location.href='datakpi-adminhrd';</script>";
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
            echo "<script>alert('Surat Peringatan berhasil dihapus!'); window.location.href='datakpi-adminhrd';</script>";
        } else {
            echo "<script>alert('Gagal menghapus Surat Peringatan!');</script>";
        }
    }
}

// ========== FUNGSI PERHITUNGAN KPI DENGAN SP ==========

function getnilaiWithSP($conn, $id)
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
    
    $nilai_asli = $zboth + $zbotw;
    
    // Kurangi dengan SP jika ada
    return calculateKPIWithSP($conn, $id, $nilai_asli);
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />
    
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
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    
                    <!-- Header Page -->
                    <div class="header-page mt-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="mb-2">
                                    <i class="bi bi-graph-up-arrow me-2"></i>Data KPI Karyawan
                                </h3>
                                <p class="mb-0 opacity-75">Monitoring dan evaluasi KPI seluruh karyawan</p>
                            </div>
                            <div>
                                <a href="dashboard-adminhrd" class="btn btn-light">
                                    <i class="bi bi-arrow-left me-2"></i>Kembali ke Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table KPI -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-dark text-white">
                            <h5 class="card-title mb-0">
                                <i class="bi bi-table me-2"></i>Tabel Data KPI Karyawan
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table id="datatablenya" class="table align-middle table-hover table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th width="3%"><center>No</center></th>
                                            <th><center>Nama Lengkap</center></th>
                                            <th width="12%"><center>Jabatan</center></th>
                                            <th width="12%"><center>Departemen</center></th>
                                            <th width="12%"><center>Bagian</center></th>
                                            <th width="8%"><center>What</center></th>
                                            <th width="8%"><center>How</center></th>
                                            <th width="8%"><center>Nilai</center></th>
                                            <th width="10%"><center>KPI</center></th>
                                            <th width="10%"><center>Aksi</center></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $no = 1;
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
                                            
                                            // Cek SP aktif untuk user ini
                                            $sp_aktif_user = getActiveSP($conn, $hasilsfa['id']);
                                        ?>
                                        <tr>
                                            <td><center><?= $no; ?></center></td>
                                            <td style="padding-left: 20px; position: relative;">
                                                <?php if ($sp_aktif_user) { ?>
                                                    <span class="sp-indicator" title="SP Aktif"></span>
                                                <?php } ?>
                                                <strong><?= $hasilsfa['nama_lngkp']; ?></strong>
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
                                            <td>
                                                <center>
                                                    <span class="badge bg-<?= $badge_color ?>">
                                                        <i class="bi bi-<?= $badge_icon ?> me-1"></i>
                                                        <?= $hasilsfa['jabatan']; ?>
                                                    </span>
                                                </center>
                                            </td>
                                            <td><center><?= $hasilsfa['departement']; ?></center></td>
                                            <td><center><?= $hasilsfa['bagian']; ?></center></td>
                                            <td><center><strong><?= getWhatt($conn, $hasilsfa['id']); ?></strong></center></td>
                                            <td><center><strong><?= getHoww($conn, $hasilsfa['id']); ?></strong></center></td>
                                            <?php
                                            $kpi_result = getnilaiWithSP($conn, $hasilsfa['id']);
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
                                            ?>
                                            <td style="color:<?= $wrabs ?>">
                                                <center>
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
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    <span class="badge bg-<?= $badge_kpi ?>">
                                                        <?= getkpi($nilai_akhir); ?>
                                                    </span>
                                                </center>
                                            </td>
                                            <td>
                                                <center>
                                                    <div class="btn-group-compact">
                                                        <!-- Tombol Lihat KPI -->
                                                        <a href="kpianggota?id=<?= $hasilsfa['id']; ?>" 
                                                        class="btn btn-primary btn-sm" 
                                                        title="Lihat KPI">
                                                            <i class="bi bi-eye"></i>
                                                        </a>
                                                        
                                                        <!-- Tombol Tambah SP -->
                                                        <button type="button" 
                                                                class="btn btn-warning btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalTambahSP<?=$hasilsfa['id']?>"
                                                                title="Tambah Surat Peringatan">
                                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                                        </button>
                                                        
                                                        <!-- Tombol Kelola SP -->
                                                        <button type="button" 
                                                                class="btn btn-info btn-sm" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#modalKelolaSP<?=$hasilsfa['id']?>"
                                                                title="Kelola Surat Peringatan">
                                                            <i class="bi bi-file-earmark-text"></i>
                                                            <?php if ($sp_aktif_user) { ?>
                                                                <span class="badge bg-danger rounded-circle" style="padding: 2px 5px; font-size: 0.6rem;">!</span>
                                                            <?php } ?>
                                                        </button>
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

                    <?php
                    // Reset pointer query
                    mysqli_data_seek($sgdah, 0);

                    // Loop lagi untuk generate modal di luar tabel
                    while ($hasilsfa = mysqli_fetch_assoc($sgdah)) {
                    ?>

                    <?php include ("pages/adminhrd/modal_tambah_sp.php"); ?>
                    <?php include ("pages/adminhrd/modal_kelola_sp.php"); ?>

                    <?php } // End while untuk modal ?>

                </div>
            </div>
        </main>
        
        <?php include("pages/part/p_footeradminhrd.php"); ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/datatables/datatables.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#datatablenya').DataTable({
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
                "order": [[0, 'asc']]
            });
        });
    </script>
</body>
</html>