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

// Ambil ID user dari parameter
if (!isset($_GET['id'])) {
    header("Location: eviden-adminhrd");
    exit();
}

$id_user_eviden = $_GET['id'];

// Ambil data user yang dipilih
$sql_user = "SELECT * FROM tb_users WHERE id = '$id_user_eviden'";
$result_user = mysqli_query($conn, $sql_user);
$user_info = mysqli_fetch_assoc($result_user);

if (!$user_info) {
    header("Location: eviden-adminhrd");
    exit();
}

// Ambil semua eviden user tersebut
$sql_eviden = "SELECT * FROM tb_eviden 
               WHERE id_user = '$id_user_eviden' 
               ORDER BY nama_eviden ASC";
$result_eviden = mysqli_query($conn, $sql_eviden);
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
                    
                    <!-- Header dengan Info User -->
                    <div class="row mb-3 mt-3">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="fw-bold mb-2">
                                                <i class="bi bi-folder-fill text-danger me-2"></i>Eviden - <?= $user_info['nama_lngkp'] ?>
                                            </h4>
                                            <div class="text-muted small">
                                                <span class="me-3">
                                                    <i class="bi bi-person-badge me-1"></i>NIK: <?= $user_info['nik'] ?>
                                                </span>
                                                <span class="me-3">
                                                    <i class="bi bi-building me-1"></i><?= $user_info['departement'] ?>
                                                </span>
                                                <span class="me-3">
                                                    <i class="bi bi-briefcase me-1"></i><?= $user_info['bagian'] ?>
                                                </span>
                                                <span class="badge bg-primary">
                                                    <?= $user_info['jabatan'] ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div>
                                            <a href="eviden-adminhrd" class="btn btn-secondary">
                                                <i class="bi bi-arrow-left me-1"></i>Kembali
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Table Eviden -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card shadow-sm border-0">
                                <div class="card-body">
                                    <?php if (mysqli_num_rows($result_eviden) > 0) { ?>
                                    <div class="table-responsive">
                                        <table id="datatablenya" class="table table-hover table-bordered">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th width="5%"><center>No</center></th>
                                                    <th width="25%"><center>Nama Eviden</center></th>
                                                    <th width="20%"><center>Nama File</center></th>
                                                    <th><center>Keterangan</center></th>
                                                    <th width="10%"><center>Aksi</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php 
                                                $no = 1;
                                                while ($eviden = mysqli_fetch_assoc($result_eviden)) { 
                                                    // Deteksi tipe file
                                                    $file_ext = strtolower(pathinfo($eviden['namafoto'], PATHINFO_EXTENSION));
                                                    $icon = 'file-earmark';
                                                    $icon_color = 'secondary';
                                                    
                                                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                                        $icon = 'file-earmark-image';
                                                        $icon_color = 'success';
                                                    } elseif ($file_ext == 'pdf') {
                                                        $icon = 'file-earmark-pdf';
                                                        $icon_color = 'danger';
                                                    } elseif (in_array($file_ext, ['doc', 'docx'])) {
                                                        $icon = 'file-earmark-word';
                                                        $icon_color = 'primary';
                                                    } elseif (in_array($file_ext, ['xls', 'xlsx', 'csv'])) {
                                                        $icon = 'file-earmark-excel';
                                                        $icon_color = 'success';
                                                    }
                                                ?>
                                                <tr>
                                                    <td><center><?= $no++ ?></center></td>
                                                    <td style="padding-left: 15px;">
                                                        <strong><?= $eviden['nama_eviden'] ?></strong>
                                                    </td>
                                                    <td>
                                                        <center>
                                                            <i class="bi bi-<?= $icon ?> text-<?= $icon_color ?> me-2"></i>
                                                            <small><?= $eviden['namafoto'] ?></small>
                                                        </center>
                                                    </td>
                                                    <td style="padding-left: 15px;"><?= $eviden['keterangan'] ?></td>
                                                    <td>
                                                        <center>
                                                            <button class="btn btn-sm btn-success" 
                                                                    data-bs-toggle="modal" 
                                                                    data-bs-target="#viewModal<?= $eviden['id'] ?>"
                                                                    title="Lihat File">
                                                                <i class="bi bi-eye"></i>
                                                            </button>
                                                            <a href="assets/kpi/eviden/<?= $eviden['id_user'] ?>/<?= $eviden['namafoto'] ?>" 
                                                               class="btn btn-sm btn-primary" 
                                                               download
                                                               title="Download">
                                                                <i class="bi bi-download"></i>
                                                            </a>
                                                        </center>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal View File -->
                                                <div class="modal fade" id="viewModal<?= $eviden['id'] ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-xl">
                                                        <div class="modal-content">
                                                            <div class="modal-header bg-success text-white">
                                                                <h5 class="modal-title fw-bold">
                                                                    <i class="bi bi-eye-fill me-2"></i><?= $eviden['nama_eviden'] ?>
                                                                </h5>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-0">
                                                                <div class="p-3 bg-light border-bottom">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <small class="text-muted">
                                                                                <strong>Karyawan:</strong> <?= $user_info['nama_lngkp'] ?><br>
                                                                                <strong>Bagian:</strong> <?= $user_info['bagian'] ?>
                                                                            </small>
                                                                        </div>
                                                                        <div class="col-md-6 text-end">
                                                                            <small class="text-muted">
                                                                                <strong>File:</strong> <?= $eviden['namafoto'] ?><br>
                                                                                <strong>Keterangan:</strong> <?= $eviden['keterangan'] ?>
                                                                            </small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div style="height: 650px; overflow: auto;">
                                                                    <?php
                                                                    $file_path = "assets/kpi/eviden/" . $eviden['id_user'] . "/" . $eviden['namafoto'];
                                                                    
                                                                    if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif', 'bmp'])) {
                                                                        // Tampilkan gambar
                                                                        echo '<div class="text-center p-3">';
                                                                        echo '<img src="' . $file_path . '" class="img-fluid" style="max-height: 600px;" alt="' . $eviden['nama_eviden'] . '">';
                                                                        echo '</div>';
                                                                    } else {
                                                                        // Tampilkan iframe untuk PDF dan file lain
                                                                        echo '<iframe src="' . $file_path . '" width="100%" height="650px" style="border: none;"></iframe>';
                                                                    }
                                                                    ?>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a href="<?= $file_path ?>" 
                                                                   class="btn btn-primary" 
                                                                   download>
                                                                    <i class="bi bi-download me-1"></i>Download File
                                                                </a>
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                    <i class="bi bi-x-circle me-1"></i>Tutup
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <?php } else { ?>
                                    <div class="alert alert-info text-center">
                                        <i class="bi bi-info-circle me-2"></i>
                                        Belum ada eviden untuk karyawan ini
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
</body>
</html>