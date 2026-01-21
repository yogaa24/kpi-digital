<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getKPI.php';

    if (isset($_POST['submitSS'])) {
        $poin = $_POST['poin'];
        $sql = "INSERT INTO tb_ss 
        VALUES (null, $id_user, '$poin')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Tambah Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal, Tambah Skill Standard')</script>";
        }
    }
    if (isset($_POST['addsspoin'])) {
        $poin = $_POST['tujuan'];
        $id = $_POST['idss'];
        $indikator_1 = $_POST['indikator_1'];
        $indikator_2 = $_POST['indikator_2'];
        $indikator_3 = $_POST['indikator_3'];
        $indikator_4 = $_POST['indikator_4'];

        $sql = "INSERT INTO tb_sspoin (`id_user`, `id_ss`, `poinss`, `nilai1`, `nilai2`, `nilai3`, `nilai4`, `nilaiss`, `deskripsi`)
        VALUES ($id_user, $id, '$poin', '$indikator_1', '$indikator_2', '$indikator_3', '$indikator_4', 0, '')";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Gagal, Tambah Skill Standard')</script>";
        }
    }
    if (isset($_POST['ss_edit'])) {
        $poin = $_POST['poinsss'];
        $id = $_POST['idsss'];
        $indikator_1 = $_POST['indikator_1'];
        $indikator_2 = $_POST['indikator_2'];
        $indikator_3 = $_POST['indikator_3'];
        $indikator_4 = $_POST['indikator_4'];

        $sql = "UPDATE tb_sspoin 
        SET poinss='$poin', nilai1='$indikator_1', nilai2='$indikator_2', nilai3='$indikator_3', nilai4='$indikator_4' 
        WHERE id_sspoin=$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Gagal Edit Skill')</script>";
        }
    }
    if (isset($_POST['ss_nilai'])) {
        $id = $_POST['idnilai'];
        $nilai = $_POST['nilai'];
        $keterangan = $_POST['keterangan'];
    
        $sql = "UPDATE tb_sspoin 
        SET nilaiss='$nilai', deskripsi='$keterangan' 
        WHERE id_sspoin=$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        } else {
            echo "<script>alert('Gagal Memberikan Nilai')</script>";
        }
    }
    if (isset($_POST['ss_hapus'])) {
        $id = $_POST['idpoin'];

        $sql = "DELETE FROM tb_sspoin WHERE id_sspoin=$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Hapus Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal Hapus Skill')</script>";
        }
    }
    if (isset($_POST['hapus_kategori_ss'])) {
        $id = $_POST['id_kategori'];

        // Hapus semua poin di kategori ini terlebih dahulu
        $sql1 = "DELETE FROM tb_sspoin WHERE id_ss=$id";
        mysqli_query($conn, $sql1);
        
        // Kemudian hapus kategori
        $sql2 = "DELETE FROM tb_ss WHERE id_poinss=$id";
        $result = mysqli_query($conn, $sql2);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Hapus Kategori Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal Hapus Kategori')</script>";
        }
    }
    if (isset($_POST['edit_kategori_ss'])) {
        $id = $_POST['idk'];
        $poinn = $_POST['poin'];

        $sql = "UPDATE tb_ss SET poin_ss='$poinn' WHERE id_poinss=$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Edit Poin')</script>";
        } else {
            echo "<script>alert('Gagal, Edit Poin')</script>";
        }
    }
}
?>

<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="dashboard-utama" class="nav-link">Kembali</a> </li>
                    <?php if ($jabatan == "Manager" || $jabatan == "Kadep" || $jabatan == "Koordinator" || $jabatan == "Direktur") { ?>
                        <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#SSmodal"
                                class="nav-link">Tambah Poin SS</a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="ssanggota" class="nav-link">SS Anggota</a> </li>
                    <?php } else { ?>
                        <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#SSmodal"
                                class="nav-link">Tambah Kategori SS</a> </li>
                    <?php } ?>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li>
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png"
                                class="user-image rounded-circle shadow" alt="User Image"> <span
                                class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                                </center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        
        <!-- Modal Tambah Kategori SS -->
        <div class="modal fade" id="SSmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Kategori Skill Standard </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" class="input">
                            <div class="input-group mb-3">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Kategori :</span>
                                <input type="input" class="form-control" name="poin" placeholder="Contoh: Komunikasi, Leadership, dll"
                                    aria-label="Poin Skill Standard" aria-describedby="poin" required>
                            </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="input" name="submitSS" class="btn btn-primary">Tambah</button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
        
        <?php include("pages/part/p_aside.php"); ?>
        
        <div class="m-3">
            <div class="container-fluid mt-2" style="font-size:13px;">
                <?php
                $no = 1;
                $sqler = "SELECT * FROM tb_ss WHERE id_user=$id_user";
                $tewg = mysqli_query($conn, $sqler);
                while ($hasil = mysqli_fetch_assoc($tewg)) {
                    $fiub = "SELECT SUM(nilaiss) as total, COUNT(nilaiss) as totil FROM tb_sspoin WHERE id_user=$id_user AND id_ss=" . $hasil['id_poinss'];
                    $sggh = mysqli_query($conn, $fiub);
                ?>
                    <div class="row">
                        <div class="col-lg connectedSortable">
                            <div class="d-flex">
                                <div class="card mb-4 w-100" style="margin-right:7px;">
                                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                                        <h5 style="color:white;" class="card-title">
                                            <?= $no . '. ' . $hasil['poin_ss']; ?>
                                        </h5>
                                        <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">
                                            Nilai : <?php while ($hasfg = mysqli_fetch_assoc($sggh)) {
                                                        if ($hasfg['total'] && $hasfg['totil']) {
                                                            echo number_format($hasfg['total'] / $hasfg['totil'], 2);
                                                        } else {
                                                            echo 'Belum dinilai';
                                                        }
                                                    } ?>
                                        </h5>
                                        <div class="card-tools">
                                            <button style="color: white; margin-top: -20px; margin-right: 5px;"
                                                type="button" data-bs-toggle="modal"
                                                data-bs-target="#EditKategoriSS<?= $hasil['id_poinss']; ?>" class="btn btn-tool">
                                                <i class="bi bi-pencil fs-6"></i>
                                            </button>

                                            <button style="color: white; margin-top: -20px; margin-right: 5px; "
                                                type="button" data-bs-toggle="dropdown"
                                                class="btn btn-tool dropdown-toggle">
                                                <i class="bi bi-plus-circle fs-6"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#TambahSS<?= $hasil['id_poinss']; ?>">Tambah Poin</a>
                                                <div class="dropdown-divider"></div>
                                                <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal"
                                                    data-bs-target="#HapusKategoriSS<?= $hasil['id_poinss']; ?>">Hapus Kategori</a>
                                            </div>
                                            
                                            <button style="color: white;" type="button" class="btn btn-tool"
                                                data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 5%">No</th>
                                                    <th style="padding-left: 30px">Poin</th>
                                                    <th style="width: 10%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 30%">
                                                        <center>Deskripsi Penilaian</center>
                                                    </th>
                                                    <th style="width: 10%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql1 = "SELECT * FROM tb_sspoin WHERE id_user='$id_user' AND id_ss='" . $hasil['id_poinss'] . "'";
                                                $ql = mysqli_query($conn, $sql1);
                                                $nodd = 1;
                                                while ($res = mysqli_fetch_assoc($ql)) {
                                                ?>
                                                    <tr class="align-middle">
                                                        <td><?= $no . '.' . $nodd ?></td>
                                                        <td><?= $res['poinss']; ?></td>
                                                        <td>
                                                            <center>
                                                                <?php if ($res['nilaiss'] != 0) { ?>
                                                                    <span class="badge bg-success fs-8">
                                                                        <?= number_format($res['nilaiss'], 2); ?>
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-warning fs-8">Belum Dinilai</span>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($res['deskripsi'])) { ?>
                                                                <small><?= $res['deskripsi']; ?></small>
                                                            <?php } else { ?>
                                                                <small class="text-muted fst-italic">Belum ada deskripsi. Klik "Nilai" untuk menambahkan.</small>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown"
                                                                class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#LihatSSS<?= $res['id_sspoin'] ?>">
                                                                    <i class="bi bi-eye"></i> Lihat Detail
                                                                </a>
                                                                <div class="dropdown-divider"></div>
                                                                <a class="dropdown-item text-primary" data-bs-toggle="modal"
                                                                    data-bs-target="#NilaiSSS<?= $res['id_sspoin'] ?>">
                                                                    <i class="bi bi-star-fill"></i> Nilai
                                                                </a>
                                                                <a name="how_edit" class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#EditSSS<?= $res['id_sspoin'] ?>">
                                                                    <i class="bi bi-pencil"></i> Edit Poin
                                                                </a>
                                                                <a class="dropdown-item text-danger" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusSSS<?= $res['id_sspoin'] ?>">
                                                                    <i class="bi bi-trash"></i> Hapus
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    
                                                    <!-- Modal Edit Poin SS -->
                                                    <div class="modal fade" id="EditSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="EditModalLabel">Edit Poin Skill Standard</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input type="hidden" value="<?= $res['id_sspoin']; ?>" name="idsss">
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Poin :</span>
                                                                            <input type="text" value="<?= $res['poinss']; ?>" class="form-control" 
                                                                                name="poinsss" placeholder="Poin SS" required>
                                                                        </div>
                                                                        
                                                                        <small class="fs-6 fw-bold">Indikator Penilaian</small>
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 1</span>
                                                                            <input type="text" class="form-control" name="indikator_1" 
                                                                                value="<?= $res['nilai1']; ?>"
                                                                                placeholder="Contoh: Indikator 1 .." required>
                                                                        </div>
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 2</span>
                                                                            <input type="text" class="form-control" name="indikator_2" 
                                                                                value="<?= $res['nilai2']; ?>"
                                                                                placeholder="Indikator 2 ..." required>
                                                                        </div>

                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 3</span>
                                                                            <input type="text" class="form-control" name="indikator_3" 
                                                                                value="<?= $res['nilai3']; ?>"
                                                                                placeholder="Indikator 3 ..." required>
                                                                        </div>

                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 4</span>
                                                                            <input type="text" class="form-control" name="indikator_4" 
                                                                                value="<?= $res['nilai4']; ?>"
                                                                                placeholder="Indikator 4 ..." required>
                                                                        </div>
                                                                        
                                                                        <div class="alert alert-info">
                                                                            <i class="bi bi-info-circle"></i> Untuk mengubah nilai, gunakan menu "Nilai"
                                                                        </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" name="ss_edit" class="btn btn-primary">Simpan</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Modal Nilai Skill Standard -->
                                                    <div class="modal fade" id="NilaiSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="NilaiModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title fw-bold" id="NilaiModalLabel">
                                                                        <i class="bi bi-star-fill"></i> Beri Nilai Skill Standard
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input type="hidden" value="<?= $res['id_sspoin']; ?>" name="idnilai">
                                                                        
                                                                        <div class="alert alert-info">
                                                                            <strong>Poin:</strong> <?= $res['poinss']; ?>
                                                                        </div>
                                                                        
                                                                        <div class="input-group mb-3">
                                                                            <span style="color:#343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                                            <input type="number" 
                                                                                step="0.01" 
                                                                                min="0"
                                                                                max="4"
                                                                                value="<?= $res['nilaiss'] != 0 ? $res['nilaiss'] : ''; ?>" 
                                                                                class="form-control" 
                                                                                name="nilai" 
                                                                                placeholder="Masukkan nilai 1-4"
                                                                                required>
                                                                        </div>
                                                                        
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-bold">Keterangan / Next Step :</label>
                                                                            <textarea class="form-control" name="keterangan" rows="5" 
                                                                                    placeholder="Jelaskan pencapaian, bukti, atau alasan pemberian nilai ini..." 
                                                                                    required><?= !empty($res['deskripsi']) ? $res['deskripsi'] : ''; ?></textarea>
                                                                        </div>
                                                                        
                                                                        <!-- Tampilkan Indikator Penilaian -->
                                                                        <?php if (!empty($res['nilai1']) || !empty($res['nilai2']) || !empty($res['nilai3']) || !empty($res['nilai4'])) { ?>
                                                                        <div class="alert alert-light border">

                                                                            <h5 class="fw-bold mb-3">
                                                                                <i class="bi bi-bar-chart-fill me-1"></i> Indikator Penilaian
                                                                            </h5>

                                                                            <?php if (!empty($res['nilai1'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge bg-danger me-2">Nilai 1</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai1']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                            <?php if (!empty($res['nilai2'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge me-2" style="background-color:#fd7e14; color:white;">Nilai 2</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai2']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                            <?php if (!empty($res['nilai3'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge bg-warning me-2">Nilai 3</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai3']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                            <?php if (!empty($res['nilai4'])) { ?>
                                                                            <div class="mb-2">
                                                                                <span class="badge bg-success me-2">Nilai 4</span>
                                                                                <span class="fw-semibold fs-7 text-dark">
                                                                                    <?= $res['nilai4']; ?>
                                                                                </span>
                                                                            </div>
                                                                            <?php } ?>

                                                                        </div>
                                                                        <?php } ?>

                                                                        
                                                                        <div class="alert alert-warning">
                                                                            <i class="bi bi-info-circle"></i> Nilai dan keterangan yang Anda berikan dapat diubah kapan saja
                                                                        </div>
                                                                        
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                    <button type="submit" name="ss_nilai" class="btn btn-primary">
                                                                        <i class="bi bi-save"></i> Simpan Nilai
                                                                    </button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Modal Lihat Detail -->
                                                    <div class="modal fade" id="LihatSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="LihatModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="LihatModalLabel">Detail Skill Standard</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="input-group mb-3">
                                                                        <span style="color : #343A40;" class="input-group-text fw-bold">Poin :</span>
                                                                        <input type="text" value="<?= $res['poinss']; ?>" class="form-control" disabled>
                                                                    </div>
                                                                    
                                                                    <div class="input-group mb-3">
                                                                        <span style="color : #343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                                        <input type="text" value="<?= number_format($res['nilaiss'], 2); ?>" class="form-control" disabled>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Deskripsi :</label>
                                                                        <textarea class="form-control" rows="4" disabled><?= $res['deskripsi']; ?></textarea>
                                                                    </div>
                                                                    
                                                                    <div class="divider-line" style="height: 0; border-bottom: 1px solid #ccc; margin: 20px 0;"></div>
                                                                    
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Modal Hapus Poin -->
                                                    <div class="modal fade" id="HapusSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="HapusModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-danger text-white">
                                                                    <h5 class="modal-title fw-bold" id="HapusModalLabel">Konfirmasi Hapus</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input hidden type="input" value="<?= $res['id_sspoin']; ?>" class="form-control" name="idpoin">
                                                                        <div class="container">
                                                                            <p class="fw-bold"><?= $res['poinss']; ?></p>
                                                                            <p>Apakah Anda yakin ingin menghapus poin ini?</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                                            <button type="submit" name="ss_hapus" class="btn btn-danger">Ya, Hapus</button>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php $nodd++;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Edit Kategori SS -->
                    <div class="modal fade" id="EditKategoriSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="EditKategoriLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="EditKategoriLabel">Edit Kategori Skill Standard</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="" class="input">
                                        <input type="input" value="<?= $hasil['id_poinss']; ?>" class="form-control" name="idk" hidden>
                                        <div class="input-group mb-3">
                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Kategori :</span>
                                            <input type="input" value="<?= $hasil['poin_ss']; ?>" class="form-control" name="poin" placeholder="Nama Kategori" required>
                                        </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="submit" name="edit_kategori_ss" class="btn btn-primary">Simpan</button>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Modal Hapus Kategori -->
                    <div class="modal fade" id="HapusKategoriSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="HapusKategoriLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title fw-bold" id="HapusKategoriLabel">Konfirmasi Hapus Kategori</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="">
                                        <input hidden type="input" value="<?= $hasil['id_poinss']; ?>" name="id_kategori">
                                        <div class="container">
                                            <p class="fw-bold"><?= $hasil['poin_ss']; ?></p>
                                            <div class="alert alert-warning">
                                                <i class="bi bi-exclamation-triangle"></i> Menghapus kategori akan menghapus <strong>semua poin</strong> di dalamnya!
                                            </div>
                                            <p>Apakah Anda yakin ingin menghapus kategori ini beserta seluruh poinnya?</p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                            <button type="submit" name="hapus_kategori_ss" class="btn btn-danger">Ya, Hapus Kategori</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php include('pages/kpi/k_modalTambahSS.php'); ?>
                <?php
                    $no++;
                } ?>
            </div>
        </div>
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>

</html>