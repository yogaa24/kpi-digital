<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/config.php';
    require 'helper/getUser.php';

    function getss($conn, $id)
    {
        $row3sd = 0;
        $totil = 1;
        $sqler = "select * from tb_ss where id_user=$id";
        $tewg = mysqli_query($conn, $sqler);
        while ($hasil = mysqli_fetch_assoc($tewg)) {
            $fiub = "SELECT SUM(nilaiss) as total, COUNT(nilaiss) as totil FROM tb_sspoin WHERE id_user=$id AND id_ss=" . $hasil['id_poinss'];
            $sggh = mysqli_query($conn, $fiub);
            while ($hasilsd = mysqli_fetch_assoc($sggh)) {
                if ($hasilsd['total'] != 0 &&  $hasilsd['totil'] != 0) {
                    $row3cf = $hasilsd['total'] / $hasilsd['totil'];
                    $row3sd += $row3cf;
                }
            }
            $totil++;
        }
        return number_format($row3sd / $totil, 2);
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
    <!--begin::App Wrapper-->
    <div class="app-wrapper">
        <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="skillstandard"
                            class="nav-link">Kembali</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="dashboard" class="nav-link">Dashboard</a> </li>
                    <!-- <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#detailModal" class="nav-link">Tambah Detail Poin KPI</a> </li> -->
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
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
            <!--begin::App Content Header-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid">
                    <div class="mt-4">
                        <div class="table-responsive">
                            <table id="datatablenya" class="table align-midle table-hover table-bordered">
                                <thead class="table-dark">
                                    <tr>
                                        <th width="3%">
                                            <center>No</center>
                                        </th>
                                        <th>
                                            <center>Nama Anggota</center>
                                        </th>
                                        <th width="15%">
                                            <center>Bagian</center>
                                        </th>
                                        <th width="20%">
                                            <center>Nilai</center>
                                        </th>
                                        <th width="10%">
                                            <center>#</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $sqlhd = "SELECT * 
FROM tb_users
WHERE atasan = '$nama_lngkp' OR nama_lngkp = '$nama_lngkp'
ORDER BY 
    CASE 
        WHEN nama_lngkp = '$nama_lngkp' THEN 0 
        ELSE 1 
    END,
    nama_lngkp";
                                    $sgdah = mysqli_query($conn, $sqlhd);
                                    while ($hasilsfa = mysqli_fetch_assoc($sgdah)) { ?>
                                        <tr>
                                            <td>
                                                <center><?= $no; ?></center>
                                            </td>
                                            <td style="padding-left: 20px;">
                                                <?= $hasilsfa['nama_lngkp']; ?>
                                            </td>
                                            <td>
                                                <center><?= $hasilsfa['bagian']; ?></center>
                                            </td>
                                            <td>
                                                <center><?php echo getss($conn, $hasilsfa['id']); ?></center>
                                            </td>
                                            <td>
                                                <?php if ($hasilsfa['nama_lngkp'] != $nama_lngkp) { ?>
                                                    <center><a type="button" href="ssanggotadetail?id=<?= $hasilsfa['id']; ?>"
                                                            class="btn btn-success btn-sm">
                                                            <i class="bi bi-eye fs-8"></i>
                                                        </a></center>
                                                <?php } ?>
                                            </td>
                                        </tr>
                                    <?php $no++;
                                    } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <?php include("pages/part/p_footer.php"); ?>
</body>

</html>