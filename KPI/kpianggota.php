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

    $sqlang = "SELECT * FROM tb_users WHERE id='$id_sf'";
    $resulasft = mysqli_query($conn, $sqlang);
    while ($hasilsfa = mysqli_fetch_assoc($resulasft)) {
        $nama_lngkpan = $hasilsfa['nama_lngkp'];
        $nikan = $hasilsfa['nik'];
        $bagianan = $hasilsfa['bagian'];
        $departementan = $hasilsfa['departement'];
        $jabatanan = $hasilsfa['jabatan'];
        $atasanan = $hasilsfa['atasan'];
        $penilaian = $hasilsfa['penilai'];
    }
    function getnilai($conn, $id)
    {
        $sql = "SELECT * FROM tb_kpi WHERE id_user='$id'";
        $zboth = 0;
        $zbotw = 0;

        $totalws = 0;
        $resultsaf = mysqli_query($conn, $sql);
        while ($hasils = mysqli_fetch_assoc($resultsaf)) {
            $sql3s = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id AND id_kpi=" . $hasils['id'];
            $result3s = mysqli_query($conn, $sql3s);
            $row3sd = mysqli_fetch_assoc($result3s);
            $totalnilaisd = $row3sd['total'];
            $nilaiws = ($totalnilaisd * $hasils['bobot']) / 100;
            $totalws += $nilaiws;
        }
        $bobotkpid = 0;
        $sql5a = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id";
        $result5a = mysqli_query($conn, $sql5a);
        while ($row5a = mysqli_fetch_assoc($result5a)) {
            $bobotkpid = $row5a['bw'];
        }
        $zbotw = ($totalws * $bobotkpid) / 100;
        // ===============================================================================
        $totalhfg = 0;
        $resultfg = mysqli_query($conn, $sql);

        while ($hasilfg = mysqli_fetch_assoc($resultfg)) {

            $sql7fg = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id AND id_kpi=" . $hasilfg['id'];
            $result7fg = mysqli_query($conn, $sql7fg);
            $row7fg = mysqli_fetch_assoc($result7fg);
            $totalnilaihfg = $row7fg['totalh'];

            $nilaihfg = ($totalnilaihfg * $hasilfg['bobot2']) / 100;
            $totalhfg += $nilaihfg;
        }
        $bobotkpias = 0;
        $sql8a = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id";
        $result8a = mysqli_query($conn, $sql8a);
        while ($row8a = mysqli_fetch_assoc($result8a)) {
            $bobotkpias = $row8a['bh'];
        }
        $zboth = ($totalhfg * $bobotkpias) / 100;

        return $zboth + $zbotw;
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

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                                <?php if ($leveel == 5) { ?>
                                    <li class="nav-item d-none d-md-block">
                                        <a href="dashboard-adminhrd" class="nav-link">Kembali</a>
                                    </li>
                                <?php } elseif ($leveel == 4) { ?>
                                    <li class="nav-item d-none d-md-block">
                                        <a href="kpikadep" class="nav-link">Kembali</a>
                                    </li>
                                <?php } else { ?>
                                    <li class="nav-item d-none d-md-block">
                                        <a href="kpikabag" class="nav-link">Kembali</a>
                                    </li>
                                <?php } ?>

                    <li class="nav-item d-none d-md-block"> <a href="kpidetailanggota?id=<?= $_GET['id']; ?>" class="nav-link">Detail KPI</a> </li>
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
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main"> <!--begin::App Content Header-->
            <div class="mt-3"> <!--begin::Container-->
                <!-- isi -->
            </div> <!--end::App Content Header--> <!--begin::App Content-->
            <div class="app-content"> <!--begin::Container-->
                <div class="container-fluid"> <!--begin::Row-->
                    <div class="row"> <!-- Start col -->
                        <div class="col-lg-4 connectedSortable">
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-danger">
                                    <h5 style="color:white;" class="card-title fw-bolder">Profil Karyawan</h5>
                                    <div class="card-tools">
                                        <!-- <button style="color: white;" type="button" class="btn btn-tool">
                    <i class="bi bi-pencil"></i>
                </button> -->
                                        <button style="color: white;" type="button" class="btn btn-tool"
                                            data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="nama-addon">Nama : </span>
                                        <input disabled type="text" value="<?php echo $nama_lngkpan; ?>"
                                            class="form-control" placeholder="Nama" aria-label="Nama"
                                            aria-describedby="nama-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="jabatan-addon">Jabatan :</span>
                                        <input disabled type="text"
                                            value="<?php echo $jabatanan . " - " . $bagianan; ?>" class="form-control"
                                            placeholder="Jabatan" aria-label="Jabatan" aria-describedby="jabatan-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="depart-addon">Departement :</span>
                                        <input disabled type="text" value="<?php echo $departementan; ?>"
                                            class="form-control" placeholder="Departement" aria-label="Departement"
                                            aria-describedby="depart-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="atasan-addon">Atasan :</span>
                                        <input disabled type="text" value="<?php echo $atasanan; ?>"
                                            class="form-control" placeholder="Koordinator" aria-label="Koordinator"
                                            aria-describedby="atasan-addon">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span style="color : #343A40;" class="input-group-text fw-bold"
                                            id="penilai-addon">Penilai :</span>
                                        <input disabled type="text" value="<?php echo $penilaian; ?>"
                                            class="form-control" placeholder="Penilai" aria-label="Penilai"
                                            aria-describedby="penilai-addon">
                                    </div>

                                </div>
                            </div>
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-warning bg-gradient">
                                    <h5 style="color:black;" class="card-title fw-bolder">TOTAL NILAI KPI</h5>
                                </div>
                                <!-- ---------------------------------------------------------------------->
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr>
                                                <th colspan="2" class="table-secondary">
                                                    <center>WHAT + HOW</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>
                                                    <center>NILAI KPI</center>
                                                </th>
                                                <th>
                                                    <center><?= getnilai($conn, $id_sf); ?></center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <?php
                                                $nilair = getnilai($conn, $id_sf);
                                                $wrabs;
                                                if ($nilair < 90) {
                                                    $wrabs = "red";
                                                } elseif ($nilair <= 100) {
                                                    $wrabs = "orange";
                                                } elseif ($nilair <= 110) {
                                                    $wrabs = "green";
                                                } else { // $nilai > 110
                                                    $wrabs = "blue";
                                                } ?>
                                                <td colspan="2" style="font-size: 25pt; color:<?= $wrabs ?>"
                                                    class="fw-bolder">
                                                    <?php
                                                    function getRating($nilair)
                                                    {
                                                        if ($nilair < 90) {
                                                            return "POOR";
                                                        } elseif ($nilair <= 100) {
                                                            return "GOOD";
                                                        } elseif ($nilair <= 110) {
                                                            return "Very Good";
                                                        } else { // $nilai > 110
                                                            return "Excellent";
                                                        }
                                                    }
                                                    ?>
                                                    <center><?php echo getRating($nilair); ?></center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
<!-- ================================================================================================================================================ -->
                        <div class="col-lg-8 connectedSortable">
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                                    <h5 style="color:white;" class="card-title fw-bolder">What</h5>
                                    <div class="card-tools">
                                         <?php if($jabatan=="Karyawan"){ $hfgiub = "hidden"; }else{ $hfgiub = ""; }?>
                                        <button <?= $hfgiub ?> style="color: white;" data-bs-toggle="modal" data-bs-target="#bobotWhatss" type="button"
                                             class="btn btn-tool">
                                            <i class="bi bi-pencil"></i>
                                        </button> 

                                        <button style="color: white;" type="button" class="btn btn-tool"
                                            data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr class="table-secondary">
                                                <th style="color: white;" scope="col" class="col-7 bg-primary">
                                                    <center>Poin</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1  bg-primary">
                                                    <center>Bobot</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-primary">
                                                    <center>Penilaian</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-primary">
                                                    <center>NILAI</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalw = 0;
                                            $totalbobot = 0;
                                            $totalnilai4 = 0;
                                            $sqlad = "SELECT * FROM tb_kpi WHERE id_user='$id_sf'";
                                            $resultsfafa = mysqli_query($conn, $sqlad);
                                            while ($hasilddd = mysqli_fetch_assoc($resultsfafa)) {
                                                $poin = $hasilddd['poin'];
                                                $bobot = $hasilddd['bobot'];
                                                $dsf = $hasilddd['id'];

                                                $sql3 = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id_sf AND id_kpi=$dsf";
                                                $result3 = mysqli_query($conn, $sql3);
                                                $row3 = mysqli_fetch_assoc($result3);
                                                $totalnilai = $row3['total'];
                                                $nilaiw = number_format(($totalnilai * $bobot) / 100, 2);
                                                $totalw += number_format($nilaiw, 2);
                                                $totalbobot += $bobot;

                                                echo "
                                <tr>
                                    <td>$poin</td>
                                    <td><center>$bobot%</center></td>
                                    <td><center>" . round($totalnilai) . "</center></td>
                                    <td><center>$nilaiw</center></td>
                                </tr>";
                                            }

                                            $sql4 = "SELECT SUM(total) as total FROM tb_whats WHERE id_user=$id_sf";
                                            $result4 = mysqli_query($conn, $sql4);
                                            $row4 = mysqli_fetch_assoc($result4);
                                            $totalnilai4 = $row4['total'];
                                            ?>

                                            <tr class="table-secondary">
                                                <th>
                                                    <center>TOTAL NILAI</center>
                                                </th>
                                                <th>
                                                    <center><?= $totalbobot ?> %</center>
                                                </th>
                                                <th>
                                                    <center><?= round($totalnilai4) ?></center>
                                                </th>
                                                <th>
                                                    <center><?= $totalw ?></center>
                                                </th>
                                            </tr>

                                        </tbody>

                                        <tr>
                                            <th rowspan="2"></th>
                                            <th style="color: white;" rowspan="2"
                                                class="align-middle table-secondary bg-primary">
                                                <center>WHAT</center>
                                            </th>
                                            <th class="table-secondary">
                                                <center>BOBOT</center>
                                            </th>
                                            <th class="table-secondary">
                                                <center>NILAI</center>
                                            </th>
                                        </tr>
                                        <tr>
                                            <?php
                                            $bobotkpiw = 0;
                                            $sql5 = "SELECT bobotwhat as bw FROM tb_bobotkpi WHERE id_user=$id_sf";
                                            $result5 = mysqli_query($conn, $sql5);
                                            while ($row5 = mysqli_fetch_assoc($result5)) {
                                                $bobotkpiw = $row5['bw'];
                                            }
                                            $zbotw = ($totalw * $bobotkpiw) / 100;
                                            ?>

                                            <td>
                                                <center><?= $bobotkpiw ?> % </center>
                                            </td>
                                            <td>
                                                <center><?= $zbotw ?></center>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <!-- --------------------------------------------------------------------->
                                </div>

                            </div>
                            <div class="card mb-4">
                                <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
                                    <h5 style="color:white;" class="card-title fw-bolder">How</h5>
                                    <div class="card-tools">
                                        <?php if($jabatan=="Karyawan"){ $hfgiub = "hidden";}?>
                                        <button <?= $hfgiub ?> style="color: white;" data-bs-toggle="modal" data-bs-target="#bobotHow" type="button"
                                            class="btn btn-tool">
                                            <i class="bi bi-pencil"></i>
                                        </button> 

                                        <button style="color: white;" type="button" class="btn btn-tool"
                                            data-lte-toggle="card-collapse">
                                            <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                                            <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <table class="table table-bordered table-sm">
                                        <thead>
                                            <tr class="table-secondary">
                                                <th style="color: white;" scope="col" class="col-7  bg-success">
                                                    <center>Poin</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-success">
                                                    <center>Bobot</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-success">
                                                    <center>Penilaian</center>
                                                </th>
                                                <th style="color: white;" scope="col" class="col-1 bg-success">
                                                    <center>NILAI</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $totalh = 0;
                                            $totalboboth = 0;
                                            $totalnilai7 = 0;

                                             $sqlads = "SELECT * FROM tb_kpi WHERE id_user='$id_sf'";
                                            $resultsfafas = mysqli_query($conn, $sqlads);

                                            while ($hasil = mysqli_fetch_assoc($resultsfafas)) {
                                                $poin2 = $hasil['poin2'];
                                                $bobot2 = $hasil['bobot2'];
                                                $dsf = $hasil['id'];

                                                $sql7 = "SELECT SUM(total) as totalh FROM tb_hows WHERE id_user=$id_sf AND id_kpi=$dsf";
                                                $result7 = mysqli_query($conn, $sql7);
                                                $row7 = mysqli_fetch_assoc($result7);
                                                $totalnilaih = $row7['totalh'];

                                                $nilaih = ($totalnilaih * $bobot2) / 100;
                                                $totalh += $nilaih;
                                                $totalboboth += $bobot2;

                                                echo "
                                <tr>
                                    <td>$poin2</td>
                                    <td><center>$bobot2%</center></td>
                                    <td><center>" . round($totalnilaih) . "</center></td>
                                    <td><center>$nilaih</center></td>
                                </tr>";
                                            }

                                            $sql4 = "SELECT SUM(total) as total FROM tb_hows WHERE id_user=$id_sf ";
                                            $result4 = mysqli_query($conn, $sql4);
                                            $row4 = mysqli_fetch_assoc($result4);
                                            $totalnilai5 = $row4['total'];
                                            ?>

                                            <tr class="table-secondary">
                                                <th>
                                                    <center>TOTAL NILAI</center>
                                                </th>
                                                <th>
                                                    <center>
                                                        <?= $totalboboth ?> %
                                                    </center>
                                                </th>
                                                <th>
                                                    <center>
                                                        <?= round($totalnilai5) ?>
                                                    </center>
                                                </th>
                                                <th>
                                                    <center>
                                                        <?= $totalh ?>
                                                    </center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <th rowspan="2"></th>
                                                <th style="color: white;" rowspan="2"
                                                    class=" bg-success align-middle table-secondary">
                                                    <center>HOW</center>
                                                </th>
                                                <th class="table-secondary">
                                                    <center>BOBOT</center>
                                                </th>
                                                <th class="table-secondary">
                                                    <center>NILAI</center>
                                                </th>
                                            </tr>
                                            <tr>
                                                <?php
                                                $bobotkpih = 0;
                                                $sql8 = "SELECT bobothow as bh FROM tb_bobotkpi WHERE id_user=$id_sf";
                                                $result8 = mysqli_query($conn, $sql8);
                                                while ($row8 = mysqli_fetch_assoc($result8)) {
                                                    $bobotkpih = $row8['bh'];
                                                }
                                                $zboth = ($totalh * $bobotkpih) / 100;
                                                ?>

                                                <td>
                                                    <center>
                                                        <?= $bobotkpih ?>%
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?= $zboth ?>
                                                    </center>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!-- --------------------------------------------------------------------->
                                </div>

                            </div>
                            <div class="modal fade" id="bobotWhatss" tabindex="-1" aria-labelledby="bobotWhatssLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="bobotWhatssLabel">Bobot What</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="" class="input">
                                                <input type="input" value="<?= $id_sf; ?>" class="form-control"
                                                    name="idU" hidden>
                                                <div class="input-group mb-3">
                                                    <span style="color : #343A40;" class="input-group-text fw-bold"
                                                        id="bobot">Bobot What
                                                        :</span>
                                                    <input type="input" value="<?= $bobotkpiw; ?>" class="form-control"
                                                        name="bobot" placeholder="0-90" aria-label="Bobot KPI"
                                                        aria-describedby="bobot">
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="input" name="updateWhatB"
                                                class="btn btn-primary">Simpan</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade" id="bobotHow" tabindex="-1" aria-labelledby="bobotHowLabel"
                                aria-hidden="true">
                                <div class="modal-dialog modal-sm">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold" id="bobotHowLabel">Bobot How</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="" class="input">
                                                <input type="input" value="<?= $dsf; ?>" class="form-control"
                                                    name="idkss" hidden>
                                                <input type="input" value="<?= $id_sf; ?>" class="form-control"
                                                    name="idU" hidden>
                                                <div class="input-group mb-3">
                                                    <span style="color : #343A40;" class="input-group-text fw-bold"
                                                        id="bobot">Bobot How
                                                        :</span>
                                                    <input type="input" value="<?= $bobotkpih; ?>" class="form-control"
                                                        name="bobot" placeholder="0-90" aria-label="Bobot KPI"
                                                        aria-describedby="bobot">
                                                </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary"
                                                data-bs-dismiss="modal">Close</button>
                                            <button type="input" name="updateHowB"
                                                class="btn btn-primary">Simpan</button>
                                        </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> <!--end::Container-->
            </div> <!--end::App Content-->
        </main> <!--end::App Main--> <!--begin::Footer-->

        <?php include("pages/part/p_footer.php"); ?>
</body>
</html>


</html>