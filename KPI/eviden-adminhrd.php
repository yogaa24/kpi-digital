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

if ($user_data['level'] != 5) {
    header("Location: dashboard");
    exit();
}

// Ambil semua data user yang memiliki eviden
$sql_users = "SELECT DISTINCT u.id, u.username, u.nama_lngkp, u.nik, u.bagian, u.departement, u.jabatan,
              (SELECT COUNT(*) FROM tb_eviden WHERE id_user = u.id) as total_eviden
              FROM tb_users u
              INNER JOIN tb_eviden e ON u.id = e.id_user
              WHERE u.id != '$id_user'
              ORDER BY u.nama_lngkp ASC";
$result_users = mysqli_query($conn, $sql_users);
?>

<!DOCTYPE html>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>

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
                                    <h4 class="fw-bold mb-0">
                                        <i class="bi bi-folder-fill text-danger me-2"></i>Data Eviden - Pilih Karyawan
                                    </h4>
                                    <p class="text-muted mb-0 small mt-2">Pilih karyawan untuk melihat eviden mereka</p>
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
                                                    <th width="10%"><center>Total Eviden</center></th>
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
                                                            <span class="badge bg-info">
                                                                <i class="bi bi-file-earmark-text me-1"></i>
                                                                <?= $user['total_eviden'] ?> File
                                                            </span>
                                                        </center>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <a href="eviden-adminhrd-detail?id=<?= $user['id'] ?>" 
                                                               class="btn btn-sm btn-success"
                                                               title="Lihat Eviden">
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
</body>
</html>