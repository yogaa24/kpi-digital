<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';
    require 'helper/getSOP.php';
}

if (isset($_POST['submitsop']) && !empty($_FILES['file']['name'])) {
    $targetfolder = "assets/pdf/sop/";

    $nama = $_POST['namasop'];
    $kode = $_POST['kodesop'];
    $tipe = $_POST['depsop'];
    $iska = 0;
    if (isset($_POST['inlineCheckbox1'])) {
        $iska = 1;
    }
    $ispri = 0;
    if (isset($_POST['inlineCheckbox2'])) {
        $ispri = 1;
    }
    $namafile = basename($_FILES['file']['name']);

    $degif = "SELECT * FROM tb_sop where kode_sop = $kode ";
    $resss = mysqli_query($conn, $degif);
    $dosigh = mysqli_fetch_assoc($resss);
    if ($dosigh['kode'] != $kode) {
        $sqlrr = "INSERT INTO tb_sop (nama_sop,kode_sop,tipe_sop,namafile_sop,is_karisma,is_prioritas)
                    VALUES ('$nama', '$kode','$tipe','$namafile',$iska,$ispri)";
        $resultok = mysqli_query($conn, $sqlrr);

        $targetfolder = $targetfolder . $kode;
        if (move_uploaded_file($_FILES['file']['tmp_name'], $targetfolder) && $resultok == true) {
            echo "<script>alert('Berhasil Upload')</script>";
            header('Location: ' . $_SERVER['REQUEST_URI']);
        } else {
            echo "<script>alert('Woops! Terjadi kesalahan.')</script>";
        }
    } else {
        echo "<script>alert('Kode SOP Sudah Ada')</script>";
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
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">


                </ul>
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
                                <?php if ($leveel == 2) { ?>
                                    <center> <a href="updateSOP" class="btn btn-default btn-flat float-center">SOP</a>
                                    </center>
                                <?php }
                                ?>
                                <center> <a href="logout" class="btn btn-default btn-flat float-center">Sign Out</a>
                                </center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <?php include("pages/part/p_aside.php"); ?>
        <div class="m-3">
            <div class="">
                <button type="button" style="font-weight: 600; width: 200px;"
                    class="float-right btn btn-primary btn-sm mr-4" data-toggle="modal" data-target="#staticBackdrop">
                    Upload SOP
                </button>
            </div>
            <table id="datatablenya" class="table align-midle table-hover table-bordered">
                <thead>
                    <th width="5%">
                        <center>No</center>
                    </th>
                    <th width="12%">
                        <center>Kode SOP</center>
                    </th>
                    <th>
                        <center>Nama Sop</center>
                    </th>
                    <th width="15%">
                        <center>Departemen</center>
                    </th>
                    <th width="18%">
                        <center>Tipe SOP</center>
                    </th>
                    <th width="8%">
                        <center>#</center>
                    </th>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($resultsop)) { ?>
                        <tr>
                            <td>
                                <center><?= $no = 1; ?></center>
                            </td>
                            <td>
                                <center><?= $row['kode_sop']; ?></center>
                            </td>
                            <td>
                                <?= $row['nama_sop']; ?>
                            </td>
                            <td>
                                <center><?= $row['tipe_sop']; ?></center>
                            </td>
                            <td>
                                <center><?php if ($row['is_karisma'] == 1) {
                                            echo 'Karisma';
                                        }
                                        if ($row['is_karisma'] == 1 && $row['is_prioritas'] == 1) {
                                            echo ' , ';
                                        }
                                        if ($row['is_prioritas'] == 1) {
                                            echo 'Prioritas';
                                        } ?></center>
                            </td>
                            <td>
                                <center>
                                    <a class="btn btn-sm btn-primary" data-toggle="modal" data-target="#onSOP<?= $row['id_sop'] ?>" role="button">
                                        <i style="font-size: 12px;" class="bi bi-eye"></i>
                                    </a>
                                </center>
                            </td>
                        </tr>
                        <div class="modal fade" id="onSOP<?= $row['id_sop'] ?>" tabindex="-1" aria-labelledby="openSOP" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold" id="openSOP"><?= $row['kode_sop'] . " - " . $row['nama_sop'] ?></h5>
                                        <button type="button" class="btn-close" data-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <iframe src="assets\pdf\sop\<?= $row['namafile_sop'] ?>" width="100%" height="650px"></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php $no++;
                    } ?>
                    </tr>
                </tbody>
            </table>
            <div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1"
                aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" style="font-weight: bold;" id="staticBackdropLabel">Tambah Target
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="modal-body">
                                <div class="form-group" style="margin-bottom: 3px; margin-top:-5px;">
                                    <label for="kodesop">Kode SOP</label>
                                    <input type="text" class="form-control" name="kodesop" id="kodesop"
                                        aria-describedby="kodesop" placeholder="Input Kode SOP" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 3px;">
                                    <label for="namasop">Nama SOP</label>
                                    <input type="text" class="form-control" name="namasop" id="namasop"
                                        aria-describedby="namasop" placeholder="Input Nama SOP" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 3px;">
                                    <label for="depsop">Departemen</label>
                                    <input type="text" class="form-control" name="depsop" id="depsop"
                                        aria-describedby="depsop" placeholder="Input Departemen SOP" required>
                                </div>
                                <div class="form-group" style="margin-bottom: 3px;margin-top:5px;">
                                    <label for="file" class="form-label">File SOP (.pdf)</label>
                                    <input class="form-control" type="file" name="file" accept=".pdf" id="file"
                                        required>
                                </div>
                                <label class="form-label">Tipe SOP</label>
                                <div class="form-group" style="margin-bottom: 3px;margin-top:-5px;" required>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="inlineCheckbox1"
                                            id="inlineCheckbox1" value="1">
                                        <label class="form-check-label" for="inlineCheckbox1">SOP Karisma</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" name="inlineCheckbox2"
                                            id="inlineCheckbox2" value="1">
                                        <label class="form-check-label" for="inlineCheckbox2">SOP Prioritas</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Kembali</button>
                                <button type="submit" name="submitsop" class="btn btn-primary">Upload</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php include("pages/part/p_footer.php"); ?>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('#datatablenya').DataTable({
                "lengthMenu": [
                    [-1, 10, 25, 50],
                    ["All", 10, 25, 50]
                ]
            });

        });
    </script>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct"
        crossorigin="anonymous"></script>
</body>

</html>