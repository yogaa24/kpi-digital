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

    return number_format($zboth + $zbotw, 2);
}
function getkpi($nilair)
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
        <?php include("pages/kpikabag/k_nav.php"); ?>
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
                                            <center>Jabatan</center>
                                        </th>
                                        <th width="15%">
                                            <center>Bagian</center>
                                        </th>
                                        <th width="15%">
                                            <center>Nilai</center>
                                        </th>
                                        <th width="15%">
                                            <center>KPI</center>
                                        </th>
                                        <th width="5%">
                                            <center>#</center>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1;
                                    $sqlhd = "SELECT * 
FROM tb_users
WHERE atasan = 'Diana Wulandari' OR nama_lngkp = 'Diana Wulandari'
ORDER BY 
    CASE 
        WHEN nama_lngkp = 'Diana Wulandari' THEN 0 
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
                                                <center><?= $hasilsfa['jabatan']; ?></center>
                                            </td>
                                            <td>
                                                <center><?= $hasilsfa['bagian']; ?></center>
                                            </td>
                                            <?php
                                            $nilair = getnilai($conn, $hasilsfa['id']);
                                            if ($nilair < 90) {
                                                $wrabs = "red";
                                            } elseif ($nilair <= 100) {
                                                $wrabs = "orange";
                                            } elseif ($nilair <= 110) {
                                                $wrabs = "green";
                                            } else { // $nilai > 110
                                                $wrabs = "blue";
                                            } ?>
                                            <td style="color:<?= $wrabs ?>">
                                                <center><?php echo getnilai($conn, $hasilsfa['id']); ?></center>
                                            </td>
                                            <td style="color:<?= $wrabs ?>">
                                                <center><?= getkpi(getnilai($conn, $hasilsfa['id'])); ?></center>
                                            </td>
                                            <td>
                                                <?php if ($hasilsfa['jabatan'] != 'Kadep') { ?>
                                                    <center><a type="button" href="kpianggota?id=<?= $hasilsfa['id']; ?>"
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