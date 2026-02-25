<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getEviden.php';
}

if (isset($_POST['submitevid']) && !empty($_FILES['file']['name'])) {
    $targetfolder = "assets/kpi/eviden/".$id_user."/";

    $nama      = $_POST['namaevi'];
    $ket       = $_POST['keteranganevi'];
    $namafile  = basename($_FILES['file']['name']);

    if (!is_dir($targetfolder)) {
        mkdir($targetfolder, 0777, true);   // true = buat folder bertingkat jika perlu
    }

    // Cek apakah user sudah punya eviden dengan nama file yang sama (contoh)
    $cek = mysqli_query($conn, "SELECT * FROM tb_eviden WHERE id_user = $id_user AND namafoto = '$namafile' AND nama_eviden = '$nama'");
    if (mysqli_num_rows($cek) > 0) {
        echo "<script>alert('Kode / File sudah ada')</script>";
        exit;
    }
    // Simpan database
    $sql = "INSERT INTO tb_eviden (id_user, nama_eviden, namafoto, keterangan)
            VALUES ($id_user, '$nama', '$namafile', '$ket')";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "<script>alert('Gagal insert data')</script>";
        exit;
    }else{
        header('Location: ' . $_SERVER['REQUEST_URI']);
    }

    // Upload file
    $targetPath = $targetfolder . $namafile;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
        echo "<script>alert('Berhasil Upload')</script>";
        header('Location: ' . $_SERVER['REQUEST_URI']);
    } else {
        echo "<script>alert('Upload file gagal')</script>";
    }
}
if (isset($_POST['editevi'])) {
    if(empty($_FILES['file']['name'])){
        $nama      = $_POST['namaevi'];
        $ket       = $_POST['keteranganevi'];
        $ids       = $_POST['idnya'];
        // Simpan database
        $sql = "UPDATE tb_eviden SET nama_eviden = '$nama' , keterangan = '$ket' WHERE id_user = $id_user AND id = $ids";
        $result = mysqli_query($conn, $sql);

        if (!$result) {
            echo "<script>alert('Gagal edit data')</script>";
            exit;
        }else{
        header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }else if (!empty($_FILES['file']['name'])){
        
        $targetfolder = "assets/kpi/eviden/".$id_user."/";
        $namafile  = basename($_FILES['file']['name']);

        $nama      = $_POST['namaevi'];
        $ket       = $_POST['keteranganevi'];
        $ids       = $_POST['idnya'];
        // Simpan database
        $sql = "UPDATE tb_eviden SET nama_eviden = '$nama' , keterangan = '$ket', namafoto='$namafile' WHERE id_user = $id_user AND id = $ids";
        $result = mysqli_query($conn, $sql);

        $targetPath = $targetfolder . $namafile;

        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
            echo "<script>alert('Berhasil Upload')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Upload file gagal')</script>";
        }

        if (!$result) {
            echo "<script>alert('Gagal edit data')</script>";
            exit;
        }else{
        header('Location: ' . $_SERVER['REQUEST_URI']);
        }
    }
}
if (isset($_POST['deleteevi'])) {
    $ids = $_POST['idnya'];

    // Ambil nama file dulu sebelum dihapus
    $cekfile = mysqli_query($conn, "SELECT namafoto FROM tb_eviden WHERE id = $ids AND id_user = $id_user");
    $datafile = mysqli_fetch_assoc($cekfile);

    if ($datafile) {
        $filepath = "assets/kpi/eviden/" . $id_user . "/" . $datafile['namafoto'];
        if (file_exists($filepath)) {
            unlink($filepath); // Hapus file fisik
        }
    }

    $sql = "DELETE FROM tb_eviden WHERE id = $ids AND id_user = $id_user";
    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "<script>alert('Gagal hapus data')</script>";
        exit;
    } else {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        exit;
    }
}

?>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <title>KPI Digital</title><!--begin::Primary Meta Tags-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="title" content="KPI Digital">
    <meta name="author" content="Rvld">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"
        integrity="sha256-tXJfXfp6Ewt1ilPzLDtQnJV4hclT9XuaZUKyUvmyr+Q=" crossorigin="anonymous">
    <!--end::Fonts--><!--begin::Third Party Plugin(OverlayScrollbars)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.3.0/styles/overlayscrollbars.min.css"
        integrity="sha256-dSokZseQNT08wYEWiz5iLI8QPlKxG+TswNRD8k35cpg=" crossorigin="anonymous">
    <!--end::Third Party Plugin(OverlayScrollbars)--><!--begin::Third Party Plugin(Bootstrap Icons)-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css"
        integrity="sha256-Qsx5lrStHZyR9REqhUF8iQt73X06c8LGIUPzpOhwRrI=" crossorigin="anonymous">
    <!--end::Third Party Plugin(Bootstrap Icons)--><!--begin::Required Plugin(AdminLTE)-->
    <link rel="stylesheet" href="assets/css/adminlte.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />

    <script type="text/javascript" src="datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="dashboard-utama" class="nav-link">Dashboard</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#tambahEviModal" class="nav-link">Tambah Eviden</a> </li>
                    <?php if (
                        $jabatan == "Koordinator" ||
                        $jabatan == "Manager" ||
                        $jabatan == "Kadep" ||
                        $jabatan == "Direktur" ||
                        $jabatan == "Wadir Utama"
                    ) { ?>
                        <li class="nav-item d-none d-md-block">
                            <a href="evidenkabag" class="nav-link">Eviden Anggota</a>
                        </li>
                    <?php } ?>

                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a> </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a></center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="app-content">
                <div class="app-content"> <!--begin::Container-->
                    <div class="container-fluid"> <!--begin::Row-->
                        <div class="row"> <!-- Start col -->
                            <div class="mt-3">
                                <div class="table-responsive">
                                    <table id="datatablenya" class="table align-midle table-hover table-bordered">
                                        <thead class="table-dark">
                                            <tr>
                                                <th width="3%">
                                                    <center>No</center>
                                                </th>
                                                <th>
                                                    <center>Task</center>
                                                </th>
                                                <th width="55%">
                                                    <center>Keterangan</center>
                                                </th>
                                                <th width="15%">
                                                    <center>#</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($resulteviden)) { ?>
                                            <tr>
                                                <td>
                                                    <center><?= $no; ?></center>
                                                </td>
                                                <td>
                                                    <center><?= $row['nama_eviden']; ?></center>
                                                </td>
                                                <td>
                                                    <center><?= $row['keterangan']; ?></center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#viewImage<?= $row['id']; ?>">
                                                            <i class="bi bi-eye"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#tambahEviEditModal<?= $row['id']; ?>">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#hapusEviModal<?= $row['id']; ?>">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </center>
                                                </td>
                                            </tr>
                                            <div class="modal fade" id="tambahEviEditModal<?= $row['id']; ?>" tabindex="-1" aria-labelledby="tambahEviEditModal" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold" id="tambahEviEditModalLabel">Edit Eviden - <?= $row['nama_eviden']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <form action="" method="POST" enctype="multipart/form-data">
                                                            <div class="modal-body">
                                                                <div class="input-group mb-3">
                                                                    <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Judul</span>
                                                                    <input type="input" class="form-control" name="idnya" value="<?= $row['id']; ?>" hidden>
                                                                    <input type="input" class="form-control" name="namaevi" placeholder="Masukkan Judul Eviden" value="<?= $row['nama_eviden']; ?>" aria-label="Judul Eviden" aria-describedby="judul">
                                                                </div>
                                                                <div class="input-group">
                                                                    <div class="input-group-prepend">
                                                                        <span style="color : #343A40;" class="input-group-text fw-bold">Keterangan</span>
                                                                    </div>
                                                                    <textarea class="form-control" name="keteranganevi" placeholder="Masukkan Keterangan Eviden" aria-label="With textarea"><?= $row['keterangan']; ?></textarea>
                                                                </div>
                                                                <div class="form-group" style="margin-bottom: 3px;margin-top:20px;">
                                                                    <input class="form-control" type="file" name="file" id="file"
                                                                            >
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                <button type="submit" name="editevi" class="btn btn-primary">Simpan</button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="viewImage<?= $row['id']; ?>" tabindex="-1" aria-labelledby="viewImage" aria-hidden="true">
                                                <div class="modal-dialog modal-lg">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title fw-bold" id="viewImageLabel">Edit Eviden - <?= $row['nama_eviden']; ?></h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                                <?php $fileExtension = pathinfo($row['namafoto'], PATHINFO_EXTENSION);
                                                                $fileExtension = strtolower($fileExtension);
                                                                if ($fileExtension === 'xlsx' || $fileExtension === 'csv' || $fileExtension === 'xls' || $fileExtension === 'xlsm' || $fileExtension === 'xltx' || $fileExtension === 'xltm') { ?>
                                                                    <p>This browser does not support Excel. Please download the Excel to view it: <a href="assets\kpi\eviden\<?= $row['id_user'] ?>/<?= $row['namafoto']; ?>">Download File</a>.</p>
                                                                <?php } else { ?>
                                                                    <embed src="assets\kpi\eviden\<?= $row['id_user'] ?>/<?= $row['namafoto']; ?>" width="100%" height="650px">

                                                                    </embed>
                                                                    <p><a href="assets\kpi\eviden\<?= $row['id_user'] ?>/<?= $row['namafoto']; ?>">Download File</a>.</p>
                                                                <?php } ?>
                                                        </div> 
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal fade" id="hapusEviModal<?= $row['id']; ?>" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
                                                        <div class="modal-header bg-danger text-white">
                                                            <h5 class="modal-title fw-bold">Konfirmasi Hapus</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body text-center">
                                                            <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 2rem;"></i>
                                                            <p class="mt-2">Yakin ingin menghapus eviden <strong><?= $row['nama_eviden']; ?></strong>?</p>
                                                            <small class="text-muted">File terkait juga akan ikut terhapus.</small>
                                                        </div>
                                                        <div class="modal-footer justify-content-center">
                                                            <form action="" method="POST">
                                                                <input type="hidden" name="idnya" value="<?= $row['id']; ?>">
                                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                                <button type="submit" name="deleteevi" class="btn btn-danger btn-sm">Ya, Hapus</button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php $no++;}  ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <?php include("pages/part/p_footer.php"); ?>
        <div class="modal fade" id="tambahEviModal" tabindex="-1" aria-labelledby="tambahEviModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="tambahEviModalLabel">Tambah Eviden</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="" method="POST" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="input-group mb-3">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Judul</span>
                                <input type="input" class="form-control" name="namaevi" placeholder="Masukkan Judul Eviden" aria-label="Judul Eviden" aria-describedby="judul">
                            </div>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span style="color : #343A40;" class="input-group-text fw-bold">Keterangan</span>
                                </div>
                                <textarea class="form-control" name="keteranganevi" placeholder="Masukkan Keterangan Eviden" aria-label="With textarea"></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom: 3px;margin-top:20px;">
                                <input class="form-control" type="file" name="file" id="file"
                                        required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" name="submitevid" class="btn btn-primary">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>

</html>