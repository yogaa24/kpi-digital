<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';

    $id_sf = $_GET['id'];

    $rsd = mysqli_fetch_assoc(mysqli_query($conn, 'Select * from tb_users where id = ' . $id_sf));

    if (isset($_POST['submitSS'])) {
        $poin = $_POST['poin'];
        $sql = "INSERT INTO tb_ss 
        VALUES (null, $id_sf, '$poin')";
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
        VALUES ($id_sf, $id, '$poin','$nilai1','$nilai2','$nilai3','$nilai4',0)";
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
        $nilai1 = $_POST['nilaiss1'];
        $nilai2 = $_POST['nilaiss2'];
        $nilai3 = $_POST['nilaiss3'];
        $nilai4 = $_POST['nilaiss4'];

        $sql = "UPDATE tb_sspoin 
        SET poinss='$poin' , nilai1='$nilai1', nilai2='$nilai2', nilai3='$nilai3', nilai4='$nilai4' where id_sspoin =$id";
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
    if (isset($_POST['ss_nilai'])) {
        $id = $_POST['idsss'];
        $nialiii = $_POST['nilaisi'];

        $sql = "UPDATE tb_sspoin 
        SET nilaiss=$nialiii where id_sspoin =$id";
        $result = mysqli_query($conn, $sql);
        if ($result) {
            header('Location: ' . $_SERVER['REQUEST_URI']);
            echo "<script>alert('Berhasil, Hapus Skill Standard')</script>";
        } else {
            echo "<script>alert('Gagal Edit Skill')</script>";
        }
    }
    if (isset($_POST['update'])) {
    $idk = $_POST['idk'];
    $poinn = $_POST['poin'];

    $sql = "UPDATE `tb_ss` set poin_ss = '$poinn' where id_poinss=$idk";
    $result = mysqli_query($conn, $sql);
    if ($result) {
        header('Location: ' . $_SERVER['REQUEST_URI']);
        echo "<script>alert('Berhasil, Edit Poin')</script>";
    } else {
        echo "<script>alert('Gagal, Edit Poin')</script>";
    }
}
} ?>
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

    <link rel="stylesheet" type="text/css" href="assets/css/datatables/datatables.min.css" />

    <script type="text/javascript" src="datatables/datatables.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css"
        integrity="sha256-4MX+61mt9NVvvuPjUWdUdyfZfxSB1/Rf9WtqRHgG5S0=" crossorigin="anonymous"><!-- jsvectormap -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css"
        integrity="sha256-+uGLJmmTKOqBr+2E6KDYs/NRsHxSkONXFHUL0fy2O/4=" crossorigin="anonymous">
    <style>
        .divider-line {
            height: 0;
            /* Menghilangkan tinggi default elemen */
            border-bottom: 1px solid #ccc;
            /* Membuat garis horizontal dengan ketebalan dan warna */
            margin: 20px 0;
            /* Menambahkan jarak di atas dan bawah */
            width: 100%;
            /* Mengatur lebar garis agar tidak penuh */
            margin-left: auto;
            /* Memusatkan garis secara horizontal */
            margin-right: auto;
        }
    </style>
</head>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="ssanggota"
                            class="nav-link">Kembali</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#SSmodal"
                            class="nav-link">Tambah Poin SS</a> </li>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->

                <ul class="navbar-nav ms-auto"> <!--begin::Navbar Search-->
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i
                                data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i
                                data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a>
                    </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png"
                                class="user-image rounded-circle shadow" alt="User Image"> <span
                                class="d-none d-md-inline"><?php echo $username ?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end"> <!--begin::User Image-->
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                                </center>
                            </li> <!--end::Menu Footer-->
                        </ul>
                    </li> <!--end::User Menu Dropdown-->
                </ul> <!--end::End Navbar Links-->

            </div> <!--end::Container-->
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
            <div class="container-fluid" style="font-size:13px;">
                <div class="card mb-3">
                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-danger">
                        <h5 style="color:white;" class="card-title fw-bolder">Profil Karyawan</h5>
                        <div class="card-tools">
                            <!-- <button style="color: white;" type="button" class="btn btn-tool">
                    <i class="bi bi-pencil"></i>
                </button> -->
                            <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="d-flex">
                            <div class="input-group mb-1 w-75" style="margin-right: 20px;">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="nama-addon">Nama : </span>
                                <input disabled type="text" value="<?php echo $rsd['nama_lngkp']; ?>" class="form-control" placeholder="Nama"
                                    aria-label="Nama" aria-describedby="nama-addon">
                            </div>
                            <div class="input-group mb-1">
                                <span style="color : #343A40;" class="input-group-text fw-bold" id="depart-addon">Departement :</span>
                                <input disabled type="text" value="<?php echo $rsd['departement']; ?>" class="form-control"
                                    placeholder="Departement" aria-label="Departement" aria-describedby="depart-addon">
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                $no = 1;
                $sqler = "select * from tb_ss where id_user=$id_sf";
                $tewg = mysqli_query($conn, $sqler);
                while ($hasil = mysqli_fetch_assoc($tewg)) {
                    $fiub = "SELECT SUM(nilaiss) as total, COUNT(nilaiss) as totil FROM tb_sspoin WHERE id_user=$id_sf AND id_ss=" . $hasil['id_poinss'];
                    $sggh = mysqli_query($conn, $fiub);
                ?>
                    <div class="row">
                        <div class="col-lg connectedSortable">
                            <div class="d-flex">
                                <div class="card mb-4 w-100">
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
                                            <button style="color: white; margin-top: -20px; margin-right: 5px;" type="button"
                                                data-bs-toggle="modal" data-bs-target="#EditASSS<?= $hasil['id_poinss']; ?>" class="btn btn-tool">
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
                                                    <th style="width: 15%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 25%">
                                                        <center>Deskripsi</center>
                                                    </th>
                                                    <th style="width: 5%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql1 = "SELECT * FROM tb_sspoin WHERE id_user='$id_sf' AND id_ss='" . $hasil['id_poinss'] . "'";
                                                $ql = mysqli_query($conn, $sql1);
                                                $nodd = 1;
                                                while ($res = mysqli_fetch_assoc($ql)) {
                                                ?>
                                                    <tr class="align-middle">
                                                        <td><?= $no . '.' . $nodd ?></td>
                                                        <td><?= $res['poinss']; ?></td>
                                                        <td>
                                                            <center><?php if ($res['nilaiss'] != 0) {
                                                                        echo $res['nilaiss'];
                                                                    }
                                                                    if ($res['nilaiss'] == 0) {
                                                                        echo 'Perlu Di Nilai';
                                                                    } ?>
                                                        </td>
                                                        <td>
                                                            <center><?php if ($res['nilaiss'] != 0) {
                                                                        $resdd = 'nilai'.$res['nilaiss'];
                                                                        echo $res[$resdd];
                                                                    }
                                                                    if ($res['nilaiss'] == 0) {
                                                                        echo 'Perlu Di Nilai';
                                                                    } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown"
                                                                class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#NilaiSSS<?= $res['id_sspoin'] ?>">Nilai</a>
                                                                <div class="dropdown-divider"></div>
                                                                <a name="how_edit"
                                                                    class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#EditSSS<?= $res['id_sspoin'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusSSS<?= $res['id_sspoin'] ?>">Hapus</a>
                                                            </div>
                                                        </td>
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
                                                                        <div class="divider-line"></div>
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Poin Nilai 1 :</span>
                                                                            <input type="input" required value="<?= $res['nilai1']; ?>" class="form-control" name="nilaiss1" placeholder="Alasan Nilai SS = 1">
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Poin Nilai 2 :</span>
                                                                            <input type="input" required value="<?= $res['nilai2']; ?>" class="form-control" name="nilaiss2" placeholder="Alasan Nilai SS = 2">
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Poin Nilai 3 :</span>
                                                                            <input type="input" required value="<?= $res['nilai3']; ?>" class="form-control" name="nilaiss3" placeholder="Alasan Nilai SS = 3">
                                                                        </div>
                                                                        <div class="input-group mb-3">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Poin Nilai 4 :</span>
                                                                            <input type="input" required value="<?= $res['nilai4']; ?>" class="form-control" name="nilaiss4" placeholder="Alasan Nilai SS = 4">
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
                                                                            <input type="input" value="<?= $res['poinss']; ?>" class="form-control" name="poinsss" disabled placeholder="Poin SS">
                                                                        </div>
                                                                        <select required class="form-control mb-3 input-group" name="nilaisi" id="nilaisi">
                                                                            <span style="color : #343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                                            <option selected>Pilih Nilai</option>
                                                                            <option value="1">1 : <?= $res['nilai1']; ?></option>
                                                                            <option value="2">2 : <?= $res['nilai2']; ?></option>
                                                                            <option value="3">3 : <?= $res['nilai3']; ?></option>
                                                                            <option value="4">4 : <?= $res['nilai4']; ?></option>
                                                                        </select>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                    <button type="submit" name="ss_nilai" class="btn btn-primary">Simpan</button>
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
                    <div class="modal fade" id="EditASSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title fw-bold" id="EditModalLabel">Edit Poin Skill Standard</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form method="POST" action="" class="input">
                                <input type="input" value="<?= $hasil['id_poinss'];?>" class="form-control" name="idk" hidden>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">Poin :</span>
                                        <input type="input" value="<?= $hasil['poin_ss'];?>" class="form-control" name="poin" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="poin">
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    <button type="input" name="update" class="btn btn-primary">Simpan</button>
                                </div>
                            </form>
                            </div>
                        </div>
                        </div>
                <?php
                    $no++;
                } ?>
            </div>
        </div>
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>

</html>