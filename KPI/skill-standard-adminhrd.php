<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';

// Cek level Admin HRD
$sql_check = "SELECT level FROM tb_auth WHERE id_user = '$id_user'";
$result_check = mysqli_query($conn, $sql_check);
$user_data = mysqli_fetch_assoc($result_check);

if ($user_data['level'] != 7) {
    header("Location: dashboard");
    exit();
}

// Fungsi untuk menghitung nilai rata-rata skill standard
function getss($conn, $id)
{
    $row3sd = 0;
    $totil = 0;
    $sqler = "SELECT * FROM tb_ss WHERE id_user=$id";
    $tewg = mysqli_query($conn, $sqler);
    
    while ($hasil = mysqli_fetch_assoc($tewg)) {
        $fiub = "SELECT SUM(nilaiss) as total, COUNT(nilaiss) as totil FROM tb_sspoin WHERE id_user=$id AND id_ss=" . $hasil['id_poinss'];
        $sggh = mysqli_query($conn, $fiub);
        while ($hasilsd = mysqli_fetch_assoc($sggh)) {
            if ($hasilsd['total'] != 0 && $hasilsd['totil'] != 0) {
                $row3cf = $hasilsd['total'] / $hasilsd['totil'];
                $row3sd += $row3cf;
                $totil++;
            }
        }
    }
    
    if ($totil == 0) {
        return "0.00";
    }
    
    return number_format($row3sd / $totil, 2);
}

// Ambil semua data user yang memiliki skill standard
$sql_users = "SELECT DISTINCT u.id, u.username, u.nama_lngkp, u.nik, u.bagian, u.departement, u.jabatan,
              (SELECT COUNT(*) FROM tb_ss WHERE id_user = u.id) as total_ss
              FROM tb_users u
              INNER JOIN tb_ss s ON u.id = s.id_user
              WHERE u.id != '$id_user'
              ORDER BY u.nama_lngkp ASC";
$result_users = mysqli_query($conn, $sql_users);

// Ambil data untuk filter dropdown
$sql_jabatan = "SELECT DISTINCT jabatan FROM tb_users WHERE jabatan IS NOT NULL AND jabatan != '' ORDER BY jabatan";
$result_jabatan = mysqli_query($conn, $sql_jabatan);

$sql_departemen = "SELECT DISTINCT departement FROM tb_users WHERE departement IS NOT NULL AND departement != '' ORDER BY departement";
$result_departemen = mysqli_query($conn, $sql_departemen);

$sql_bagian = "SELECT DISTINCT bagian FROM tb_users WHERE bagian IS NOT NULL AND bagian != '' ORDER BY bagian";
$result_bagian = mysqli_query($conn, $sql_bagian);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>
<style>
    .sp-indicator {
        position: absolute;
        top: 5px;
        right: 5px;
        width: 8px;
        height: 8px;
        background: #dc3545;
        border-radius: 50%;
    }
    
    .nilai-badge {
        font-size: 0.95rem;
        font-weight: 600;
        padding: 0.4rem 0.8rem;
    }
</style>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <?php include("pages/dashboard/p_nav_adminhrd.php"); ?>
        <?php include("pages/part/p_aside_adminhrd.php"); ?>
        
        <main class="app-main">
            <div class="app-content">
                <div class="container-fluid">
                    
                    <!-- Header -->
                    <div class="row mb-3 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                        <div>
                                            <h4 class="fw-bold mb-0">
                                                <i class="bi bi-award-fill text-primary me-2"></i>
                                                Data Skill Standard - Semua Karyawan
                                            </h4>
                                            <p class="text-muted mb-0 small mt-2">
                                                Pilih karyawan untuk melihat detail skill standard mereka
                                            </p>
                                        </div>

                                        <div>
                                            <a href="dashboard-adminhrd"
                                            class="btn btn-light btn-sm shadow-sm">
                                                <i class="bi bi-arrow-left me-1"></i>
                                                Kembali ke Dashboard
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Filter Section -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="row g-3 align-items-end">
                                        <!-- Filter Jabatan -->
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">
                                                <i class="bi bi-award me-1"></i>Jabatan
                                            </label>
                                            <select id="filterJabatan" class="form-select form-select-sm">
                                                <option value="">-- Semua Jabatan --</option>
                                                <?php 
                                                mysqli_data_seek($result_jabatan, 0);
                                                while ($jab = mysqli_fetch_assoc($result_jabatan)) { ?>
                                                    <option value="<?= $jab['jabatan'] ?>">
                                                        <?= $jab['jabatan'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- Filter Departemen -->
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">
                                                <i class="bi bi-building me-1"></i>Departemen
                                            </label>
                                            <select id="filterDepartemen" class="form-select form-select-sm">
                                                <option value="">-- Semua Departemen --</option>
                                                <?php 
                                                mysqli_data_seek($result_departemen, 0);
                                                while ($dept = mysqli_fetch_assoc($result_departemen)) { ?>
                                                    <option value="<?= $dept['departement'] ?>">
                                                        <?= $dept['departement'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- Filter Bagian -->
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">
                                                <i class="bi bi-diagram-3 me-1"></i>Bagian
                                            </label>
                                            <select id="filterBagian" class="form-select form-select-sm">
                                                <option value="">-- Semua Bagian --</option>
                                                <?php 
                                                mysqli_data_seek($result_bagian, 0);
                                                while ($bag = mysqli_fetch_assoc($result_bagian)) { ?>
                                                    <option value="<?= $bag['bagian'] ?>">
                                                        <?= $bag['bagian'] ?>
                                                    </option>
                                                <?php } ?>
                                            </select>
                                        </div>

                                        <!-- Tombol Reset -->
                                        <div class="col-md-3">
                                            <button id="resetFilter" class="btn btn-secondary btn-sm w-100">
                                                <i class="bi bi-arrow-clockwise me-1"></i>Reset Filter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="datatablenya" class="table table-hover table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="3%"><center>No</center></th>
                                                    <th><center>Nama Lengkap</center></th>
                                                    <th width="12%"><center>NIK</center></th>
                                                    <th width="15%"><center>Jabatan</center></th>
                                                    <th width="15%"><center>Departemen</center></th>
                                                    <th width="15%"><center>Bagian</center></th>
                                                    <th width="10%"><center>Nilai Rata-rata</center></th>
                                                    <th width="10%"><center>Total SS</center></th>
                                                    <th width="8%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                $no = 1;
                                                while ($user = mysqli_fetch_assoc($result_users)) { 
                                                    // Tentukan badge color berdasarkan jabatan
                                                    $badge_color = 'secondary';
                                                    $badge_icon = 'person-fill';
                                                    
                                                    if ($user['jabatan'] == 'Kadep') {
                                                        $badge_color = 'danger';
                                                        $badge_icon = 'award-fill';
                                                    } elseif ($user['jabatan'] == 'Manager') {
                                                        $badge_color = 'warning';
                                                        $badge_icon = 'star-fill';
                                                    } elseif ($user['jabatan'] == 'Koordinator') {
                                                        $badge_color = 'info';
                                                        $badge_icon  = 'people-fill';
                                                    } elseif ($user['jabatan'] == 'Karyawan') {
                                                        $badge_color = 'success';
                                                        $badge_icon = 'person-check-fill';
                                                    }
                                                    
                                                    // Hitung nilai rata-rata
                                                    $nilai_avg = getss($conn, $user['id']);
                                                    
                                                    // Tentukan warna badge nilai berdasarkan nilai
                                                    $nilai_color = 'secondary';
                                                    if ($nilai_avg >= 4.0) {
                                                        $nilai_color = 'success';
                                                    } elseif ($nilai_avg >= 3.0) {
                                                        $nilai_color = 'info';
                                                    } elseif ($nilai_avg >= 2.0) {
                                                        $nilai_color = 'warning';
                                                    } elseif ($nilai_avg > 0) {
                                                        $nilai_color = 'danger';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><center><?= $no++ ?></center></td>
                                                    <td style="padding-left: 15px;">
                                                        <strong><?= $user['nama_lngkp'] ?></strong>
                                                        <br>
                                                        <small class="text-muted">@<?= $user['username'] ?></small>
                                                    </td>
                                                    <td><center><?= $user['nik'] ?></center></td>
                                                    <td>
                                                        <center>
                                                            <span class="badge bg-<?= $badge_color ?>">
                                                                <i class="bi bi-<?= $badge_icon ?> me-1"></i>
                                                                <?= $user['jabatan'] ?>
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td><center><?= $user['departement'] ?></center></td>
                                                    <td><center><?= $user['bagian'] ?></center></td>
                                                    <td>
                                                        <center>
                                                            <span class="badge bg-<?= $nilai_color ?> nilai-badge">
                                                                <i class="bi bi-graph-up me-1"></i>
                                                                <?= $nilai_avg ?>
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <span class="badge bg-primary">
                                                                <i class="bi bi-list-check me-1"></i>
                                                                <?= $user['total_ss'] ?> SS
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a href="ssanggotadetail?id=<?= $user['id'] ?>" 
                                                               class="btn btn-sm btn-success"
                                                               title="Lihat Detail SS">
                                                                <i class="bi bi-eye"></i> Lihat
                                                            </a>
                                                        </center>
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

<!-- jQuery harus dimuat pertama -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTable
        var table = $('#datatablenya').DataTable({
            "responsive": true,
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
            "order": [[1, 'asc']], // Urutkan berdasarkan nama
            "columnDefs": [
                { "orderable": false, "targets": [0, 8] } // No dan Aksi tidak bisa diurutkan
            ]
        });
        
        // Filter Jabatan - otomatis
        $('#filterJabatan').on('change', function() {
            var jabatan = $(this).val();
            table.column(3).search(jabatan).draw(); // Kolom 3 = Jabatan
        });
        
        // Filter Departemen - otomatis
        $('#filterDepartemen').on('change', function() {
            var dept = $(this).val();
            table.column(4).search(dept).draw(); // Kolom 4 = Departemen
        });
        
        // Filter Bagian - otomatis
        $('#filterBagian').on('change', function() {
            var bagian = $(this).val();
            table.column(5).search(bagian).draw(); // Kolom 5 = Bagian
        });
        
        // Reset Filter
        $('#resetFilter').on('click', function() {
            $('#filterJabatan').val('');
            $('#filterDepartemen').val('');
            $('#filterBagian').val('');
            table.search('').columns().search('').draw();
        });
    });
</script>
</body>
</html>