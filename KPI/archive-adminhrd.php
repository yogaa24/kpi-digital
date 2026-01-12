<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/configarchive.php';
require 'helper/getUser.php';

// Pastikan $id_user sudah terdefinisi dari getUser.php
// Jika belum, ambil dari session
if (!isset($id_user)) {
    $id_user = $_SESSION['id_user'];
}

// Cek level Admin HRD
$sql_check = "SELECT level FROM tb_auth WHERE id_user = '$id_user'";
$result_check = mysqli_query($conn, $sql_check);

// Tambahkan error checking
if (!$result_check) {
    die("Error pada query check level: " . mysqli_error($conn));
}

$user_data = mysqli_fetch_assoc($result_check);

if ($user_data['level'] != 5) {
    header("Location: dashboard");
    exit();
}

// Ambil dulu semua user dari database utama
$sql_users = "SELECT id, username, nama_lngkp, nik, bagian, departement, jabatan 
              FROM tb_users 
              WHERE id != '$id_user'
              ORDER BY nama_lngkp ASC";

$result_users = mysqli_query($conn, $sql_users); // Pakai $conn, bukan $connarc

// Tambahkan error checking
if (!$result_users) {
    echo "<div class='alert alert-danger m-3'>";
    echo "<strong>Error Query:</strong><br>";
    echo mysqli_error($conn);
    echo "</div>";
    exit();
}

// Buat array untuk menyimpan data user dengan archive count
$users_with_archive = array();

while ($user = mysqli_fetch_assoc($result_users)) {
    // Cek berapa banyak archive yang dimiliki user ini
    $sql_count = "SELECT COUNT(DISTINCT bulan) as total_archive 
                  FROM tbar_archive 
                  WHERE id_user = " . $user['id'];
    
    $result_count = mysqli_query($connarc, $sql_count);
    $count_data = mysqli_fetch_assoc($result_count);
    
    // Hanya tambahkan user yang memiliki archive
    if ($count_data['total_archive'] > 0) {
        $user['total_archive'] = $count_data['total_archive'];
        $users_with_archive[] = $user;
    }
}

// Ambil data untuk filter dropdown (dari users yang punya archive)
$jabatan_list = array();
$departemen_list = array();
$bagian_list = array();

foreach ($users_with_archive as $user) {
    if (!empty($user['jabatan']) && !in_array($user['jabatan'], $jabatan_list)) {
        $jabatan_list[] = $user['jabatan'];
    }
    if (!empty($user['departement']) && !in_array($user['departement'], $departemen_list)) {
        $departemen_list[] = $user['departement'];
    }
    if (!empty($user['bagian']) && !in_array($user['bagian'], $bagian_list)) {
        $bagian_list[] = $user['bagian'];
    }
}

sort($jabatan_list);
sort($departemen_list);
sort($bagian_list);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

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
                                                <i class="bi bi-archive-fill text-warning me-2"></i>
                                                Data Archive - Pilih Karyawan
                                            </h4>
                                            <p class="text-muted mb-0 small mt-2">
                                                Pilih karyawan untuk melihat archive KPI mereka
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
                                                <?php foreach ($jabatan_list as $jab) { ?>
                                                    <option value="<?= htmlspecialchars($jab) ?>">
                                                        <?= htmlspecialchars($jab) ?>
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
                                                <?php foreach ($departemen_list as $dept) { ?>
                                                    <option value="<?= htmlspecialchars($dept) ?>">
                                                        <?= htmlspecialchars($dept) ?>
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
                                                <?php foreach ($bagian_list as $bag) { ?>
                                                    <option value="<?= htmlspecialchars($bag) ?>">
                                                        <?= htmlspecialchars($bag) ?>
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
                                    
                                    <div class="row mt-2">
                                        <div class="col-12">
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
                                    
                                    <?php if (count($users_with_archive) > 0) { ?>

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
                                                    <th width="10%"><center>Total Archive</center></th>
                                                    <th width="8%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                foreach ($users_with_archive as $user) { 
                                                    // Badge logic tetap sama...
                                                    $badge_color = 'secondary';
                                                    $badge_icon = 'person-fill';
                                                    
                                                    if ($user['jabatan'] == 'Kadep') {
                                                        $badge_color = 'danger';
                                                        $badge_icon = 'award-fill';
                                                    } elseif ($user['jabatan'] == 'Kabag') {
                                                        $badge_color = 'warning';
                                                        $badge_icon = 'star-fill';
                                                    } elseif ($user['jabatan'] == 'Karyawan') {
                                                        $badge_color = 'success';
                                                        $badge_icon = 'person-check-fill';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><center><?= $no++ ?></center></td>
                                                    <td style="padding-left: 15px;">
                                                        <strong><?= htmlspecialchars($user['nama_lngkp']) ?></strong>
                                                        <br>
                                                        <small class="text-muted">@<?= htmlspecialchars($user['username']) ?></small>
                                                    </td>
                                                    <td><center><?= htmlspecialchars($user['nik']) ?></center></td>
                                                    <td>
                                                        <center>
                                                            <span class="badge bg-<?= $badge_color ?>">
                                                                <i class="bi bi-<?= $badge_icon ?> me-1"></i>
                                                                <?= htmlspecialchars($user['jabatan']) ?>
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td><center><?= htmlspecialchars($user['departement']) ?></center></td>
                                                    <td><center><?= htmlspecialchars($user['bagian']) ?></center></td>
                                                    <td>
                                                        <center>
                                                            <span class="badge bg-warning text-dark">
                                                                <i class="bi bi-calendar3 me-1"></i>
                                                                <?= $user['total_archive'] ?> Bulan
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a href="archive-adminhrd-detail?id=<?= $user['id'] ?>" 
                                                            class="btn btn-sm btn-success"
                                                            title="Lihat Archive">
                                                                <i class="bi bi-eye"></i> Lihat
                                                            </a>
                                                        </center>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <?php } else { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Belum ada data archive untuk ditampilkan
                                    </div>
                                    <?php } ?>
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

    <?php if (count($users_with_archive) > 0) { ?>
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
                    { "orderable": false, "targets": [0, 7] } // No dan Aksi tidak bisa diurutkan
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
    <?php } ?>
</body>
</html>