<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
}

require 'helper/config.php';
require 'helper/getUser.php';
require 'helper/checkAdmin.php';
require_once 'helper/ss_functions.php';

// Hanya Admin HRD dan Direktur (termasuk Diana Wulandari) yang bisa akses
if (!canViewAllEmployees()) {
    header("Location: home-kpi-real");
    exit();
}

function getScoreBadgeColor($score)
{
    if ($score === null || $score <= 0) return 'secondary';
    if ($score >= 4.0) return 'success';
    if ($score >= 3.0) return 'info text-dark';
    if ($score >= 2.0) return 'warning text-dark';
    return 'danger';
}

// Ambil semua data user aktif
$sql_users = "SELECT u.id, u.username, u.nama_lngkp, u.nik, u.bagian, u.departement, u.jabatan,
              (SELECT COUNT(*) FROM tb_ss WHERE id_user = u.id) as total_ss
              FROM tb_users u
              WHERE u.jabatan != 'Admin HRD' AND u.username NOT IN ('itboy', 'adminhrd', 'backdoor_admin')
              AND (u.status_karyawan = 'AKTIF' OR (u.status_karyawan IS NULL AND u.status = 1))
              ORDER BY
                CASE
                    WHEN u.jabatan = 'Kadep' THEN 1
                    WHEN u.jabatan = 'Koordinator' THEN 2
                    WHEN u.jabatan = 'Manager' THEN 3
                    WHEN u.jabatan = 'Karyawan' THEN 4
                    ELSE 5
                END,
                u.nama_lngkp ASC";
$result_users = mysqli_query($conn, $sql_users);

// Batch ambil summary SS seluruh user (sangat cepat & efisien)
$all_ss_data = getAllUserSSSummary($conn);

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
        font-size: 0.9rem;
        font-weight: 600;
        padding: 0.35rem 0.65rem;
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
                                                Monitoring dan evaluasi nilai Skill Standard (Umum & Teknis) seluruh karyawan
                                            </p>
                                        </div>

                                        <div class="d-flex align-items-center gap-2">
                                            <a href="export_ss_summary" id="btnExportSS" class="btn btn-success btn-sm shadow-sm">
                                                <i class="bi bi-file-earmark-spreadsheet me-1"></i>
                                                Export Summary SS
                                            </a>
                                            <a href="export_ss_detail" id="btnExportSSDetail" class="btn btn-info btn-sm shadow-sm text-white">
                                                <i class="bi bi-file-earmark-ruled me-1"></i>
                                                Export Detail SS
                                            </a>
                                            <?php $back_url_ss = (isset($_SESSION['level']) && $_SESSION['level'] == 7) ? 'dashboard-adminhrd' : 'data-karyawan'; ?>
                                            <a href="<?= $back_url_ss ?>"
                                            class="btn btn-light btn-sm shadow-sm">
                                                <i class="bi bi-arrow-left me-1"></i>
                                                Kembali
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
                                    <div class="row g-2 align-items-end">
                                        <!-- Filter Jabatan -->
                                        <div class="col-md-2">
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

                                        <!-- Filter Status SS -->
                                        <div class="col-md-2">
                                            <label class="form-label small fw-bold">
                                                <i class="bi bi-check2-circle me-1"></i>Status SS
                                            </label>
                                            <select id="filterStatusSS" class="form-select form-select-sm">
                                                <option value="">-- Semua Status --</option>
                                                <option value="Ada SS">Memiliki SS</option>
                                                <option value="Belum Ada">Belum Memiliki SS</option>
                                            </select>
                                        </div>

                                        <!-- Tombol Reset -->
                                        <div class="col-md-2">
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
                                        <table id="datatablenya" class="table table-hover table-bordered align-middle">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="3%"><center>No</center></th>
                                                    <th><center>Nama Lengkap</center></th>
                                                    <th width="10%"><center>NIK</center></th>
                                                    <th width="11%"><center>Jabatan</center></th>
                                                    <th width="12%"><center>Departemen</center></th>
                                                    <th width="12%"><center>Bagian</center></th>
                                                    <th width="8%"><center>SS Umum</center></th>
                                                    <th width="8%"><center>SS Teknis</center></th>
                                                    <th width="8%"><center>Rata-rata</center></th>
                                                    <th width="8%"><center>Total SS</center></th>
                                                    <th width="8%"><center>Status</center></th>
                                                    <th width="7%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            <?php 
                                                $no = 1;
                                                while ($user = mysqli_fetch_assoc($result_users)) { 
                                                    $uid = intval($user['id']);
                                                    $user_ss = $all_ss_data[$uid] ?? [];

                                                    $val_umum = $user_ss['avg_umum'] ?? null;
                                                    $val_teknis = $user_ss['avg_teknis'] ?? null;
                                                    $val_total = $user_ss['avg_total'] ?? null;
                                                    $total_kat = $user_ss['total_kat'] ?? intval($user['total_ss']);
                                                    $total_poin = $user_ss['total_poin'] ?? 0;

                                                    // Tentukan badge color berdasarkan jabatan
                                                    $badge_color = 'secondary';
                                                    $badge_icon = 'person-fill';
                                                    
                                                    if ($user['jabatan'] == 'Kadep') {
                                                        $badge_color = 'danger';
                                                        $badge_icon = 'award-fill';
                                                    } elseif ($user['jabatan'] == 'Manager') {
                                                        $badge_color = 'warning text-dark';
                                                        $badge_icon = 'star-fill';
                                                    } elseif ($user['jabatan'] == 'Koordinator') {
                                                        $badge_color = 'info text-dark';
                                                        $badge_icon  = 'people-fill';
                                                    } elseif ($user['jabatan'] == 'Karyawan') {
                                                        $badge_color = 'success';
                                                        $badge_icon = 'person-check-fill';
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
                                                            <?php if ($val_umum !== null) { ?>
                                                                <span class="badge bg-<?= getScoreBadgeColor($val_umum) ?> nilai-badge">
                                                                    <?= number_format($val_umum, 2) ?>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary nilai-badge">-</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($val_teknis !== null) { ?>
                                                                <span class="badge bg-<?= getScoreBadgeColor($val_teknis) ?> nilai-badge">
                                                                    <?= number_format($val_teknis, 2) ?>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary nilai-badge">-</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($val_total !== null) { ?>
                                                                <span class="badge bg-<?= getScoreBadgeColor($val_total) ?> nilai-badge">
                                                                    <i class="bi bi-graph-up me-1"></i><?= number_format($val_total, 2) ?>
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary nilai-badge">-</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($total_kat > 0) { ?>
                                                                <span class="badge bg-primary">
                                                                    <i class="bi bi-list-check me-1"></i><?= $total_kat ?> SS
                                                                </span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-light text-muted border">0 SS</span>
                                                            <?php } ?>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <?php if ($total_kat > 0) { ?>
                                                                <span class="badge bg-success">Ada SS</span>
                                                            <?php } else { ?>
                                                                <span class="badge bg-secondary">Belum Ada</span>
                                                            <?php } ?>
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
                { "orderable": false, "targets": [0, 11] } // No dan Aksi tidak bisa diurutkan
            ]
        });

        // Function update URL tombol export sesuai filter aktif
        function updateExportUrl() {
            var params = new URLSearchParams();
            var jab = $('#filterJabatan').val();
            var dept = $('#filterDepartemen').val();
            var bag = $('#filterBagian').val();
            var stat = $('#filterStatusSS').val();
            if (jab) params.set('jabatan', jab);
            if (dept) params.set('departemen', dept);
            if (bag) params.set('bagian', bag);
            if (stat === 'Ada SS') {
                params.set('status_ss', 'ada');
            } else if (stat === 'Belum Ada') {
                params.set('status_ss', 'belum');
            }
            var qs = params.toString();
            $('#btnExportSS').attr('href', 'export_ss_summary' + (qs ? '?' + qs : ''));
            $('#btnExportSSDetail').attr('href', 'export_ss_detail' + (qs ? '?' + qs : ''));
        }
        
        // Filter Jabatan - otomatis
        $('#filterJabatan').on('change', function() {
            var jabatan = $(this).val();
            table.column(3).search(jabatan).draw(); // Kolom 3 = Jabatan
            updateExportUrl();
        });
        
        // Filter Departemen - otomatis
        $('#filterDepartemen').on('change', function() {
            var dept = $(this).val();
            table.column(4).search(dept).draw(); // Kolom 4 = Departemen
            updateExportUrl();
        });
        
        // Filter Bagian - otomatis
        $('#filterBagian').on('change', function() {
            var bagian = $(this).val();
            table.column(5).search(bagian).draw(); // Kolom 5 = Bagian
            updateExportUrl();
        });

        // Filter Status SS - otomatis
        $('#filterStatusSS').on('change', function() {
            var status = $(this).val();
            table.column(10).search(status).draw(); // Kolom 10 = Status SS
            updateExportUrl();
        });
        
        // Reset Filter
        $('#resetFilter').on('click', function() {
            $('#filterJabatan').val('');
            $('#filterDepartemen').val('');
            $('#filterBagian').val('');
            $('#filterStatusSS').val('');
            table.search('').columns().search('').draw();
            updateExportUrl();
        });
    });
</script>
</body>
</html>
