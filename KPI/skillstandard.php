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
        $nilai = $_POST['nilai'];
        $id = $_POST['idss'];
        $nilai1 = $_POST['nilaiss1'];
        $nilai2 = $_POST['nilaiss2'];
        $nilai3 = $_POST['nilaiss3'];
        $nilai4 = $_POST['nilaiss4'];

        $sql = "INSERT INTO tb_sspoin (`id_user`, `id_ss`, `poinss`, nilai1, nilai2, nilai3, nilai4, `nilaiss`)
        VALUES ($id_user, $id, '$poin','$nilai1','$nilai2','$nilai3','$nilai4',0)";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Tambah Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal, Tambah Skill Standard')</script>";
        }
    }
    if (isset($_POST['ss_edit'])) {
        $poin = $_POST['poinsss'];
        $nilai = $_POST['nilaisss'];
        $id = $_POST['idsss'];

        $sql = "UPDATE tb_sspoin 
        SET poinss='$poin' , nilaiss=$nilai where id_sspoin =$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Edit Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal Edit Skill')</script>";
        }
    }
    if (isset($_POST['ss_hapus'])) {
        $id = $_POST['idpoin'];

        $sql = "delete from tb_sspoin where id_sspoin=$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Hapus Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal Edit Skill')</script>";
        }
    }
    
}
?>

<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="dashboard-utama" class="nav-link">Kembali</a> </li>
                    <?php if ($jabatan == "Kabag" or $jabatan=="Kadep") { ?>
                        <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#SSmodal"
                                class="nav-link">Tambah Poin SS</a> </li>
                        <li class="nav-item d-none d-md-block"> <a href="ssanggota" class="nav-link">SS Anggota</a> </li>
                    <?php }; ?>
                    <!-- <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#detailModal" class="nav-link">Tambah Detail Poin KPI</a> </li> -->
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
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
        <div class="modal fade" id="SSmodal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Poin Skill Standard </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form method="POST" action="" class="input">
                            <div class="input-group mb-3">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Poin :</span>
                                <input type="input" class="form-control" name="poin" placeholder="Poin Skill Standard"
                                    aria-label="Poin Skill Standard" aria-describedby="poin">
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
                $sqler = "select * from tb_ss where id_user=$id_user";
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
                                                            echo '0';
                                                        }
                                                    } ?>
                                        </h5>
                                        <div class="card-tools">
                                            <?php if ($jabatan == "Kabag") { ?>
                                            <button style="color: white; margin-top: -20px; margin-right: 5px;"
                                                type="button" data-bs-toggle="modal"
                                                data-bs-target="#EditSS<?= $hasil['id_poinss']; ?>" class="btn btn-tool">
                                                <i class="bi bi-pencil fs-6"></i>
                                            </button>

                                            <button style="color: white; margin-top: -20px; margin-right: 5px; "
                                                type="button" data-bs-toggle="dropdown"
                                                class="btn btn-tool dropdown-toggle">
                                                <i class="bi bi-plus-circle fs-6"></i>
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                                    data-bs-target="#TambahSS<?= $hasil['id_poinss']; ?>">Tambah Poin </a>
                                            </div>
                                            <?php } ?>
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
                                                    <th style="padding-left : 30px">Poin</th>
                                                    <th style="width: 30%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 15%">
                                                        <center>Penilaian</center>
                                                    </th>
                                                    <?php if ($jabatan == "Kabag") { ?>
                                                        <th style="width: 15%">
                                                            <center>Action</center>
                                                        </th>
                                                    <?php } ?>
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
                                                            <center><?= $res['nilaiss']; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown"
                                                                class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#NilaiSSS<?= $res['id_sspoin'] ?>">Nilai</a>
                                                            </div>
                                                        </td>
                                                        <?php if ($jabatan == "Kabag") { ?>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown"
                                                                class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a name="how_edit"
                                                                    class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#EditSSS<?= $res['id_sspoin'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusSSS<?= $res['id_sspoin'] ?>">Hapus</a>
                                                                </div>
                                                            </td>
                                                            <?php } ?>
                                                    </tr>
                                                    <div class="modal fade" id="EditSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="EditModalLabel">Edit Skill Standard</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input type="input" hidden value="<?= $res['id_sspoin']; ?>" class="form-control" name="idsss">
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Poin :</span>
                                                                            <input type="input" value="<?= $res['poinss']; ?>" class="form-control" name="poinsss" placeholder="Poin SS">
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Nilai :</span>
                                                                            <input type="input" value="<?= $res['nilaiss']; ?>" class="form-control" name="nilaisss" placeholder="Nilai SS">
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
                                                    <div class="modal fade" id="NilaiSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="EditModalLabel">Nilai Skill Standard</h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input type="input" hidden value="<?= $res['id_sspoin']; ?>" class="form-control" name="idsss">
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Poin :</span>
                                                                            <input type="input" value="<?= $res['poinss']; ?>" class="form-control" name="poinsss" placeholder="Poin SS">
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Nilai :</span>
                                                                            <input type="input" value="<?= $res['nilaiss']; ?>" class="form-control" name="nilaisss" placeholder="Nilai SS">
                                                                        </div>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal fade" id="HapusSSS<?= $res['id_sspoin'] ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title fw-bold" id="EditModalLabel"><?= $res['poinss']; ?></h5>
                                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <form method="POST" action="" class="input">
                                                                        <input hidden type="input" value="<?= $res['id_sspoin']; ?>" class="form-control" name="idpoin">
                                                                        <div class="container">
                                                                            <p>Apa Kamu Yakin Hapus Poin Ini?</p>
                                                                        </div>
                                                                        <div class="modal-footer">
                                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                            <button type="submit" name="ss_hapus" class="btn btn-danger">Hapus</button>
                                                                        </div>
                                                                    </form>
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