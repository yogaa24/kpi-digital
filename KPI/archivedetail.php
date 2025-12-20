<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/configarchive.php';
    require 'helper/getUser.php';
    require 'helper/getKPIArch.php';


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
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="archive" class="nav-link">Kembali</a> </li>
                </ul> <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"> <a class="nav-link" href="#" data-lte-toggle="fullscreen"> <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i> <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i> </a> </li> <!--end::Fullscreen Toggle--> <!--begin::User Menu Dropdown-->
                    <li class="nav-item dropdown user-menu"> <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown"> <img style="margin-top: -2px;" src="assets/img/profile.png" class="user-image rounded-circle shadow" alt="User Image"> <span class="d-none d-md-inline"><?php echo $username?></span> </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <center> <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a></center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
            <div class="sidebar-brand">
                <a href="dashboard" class="brand-link">
                    <img src="assets/img/logokpi.png" alt="Logo" class="brand-image opacity-100">
                    <span class="brand-text fw-light">KPI Digital</span>
                </a>
            </div>
            <div class="sidebar-wrapper">
                <nav class="mt-2">
                    <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                        <li class="nav-item"> <a href="dashboard" class="nav-link"> <i class="nav-icon bi bi-plus-circle"></i>
                                <p>KPI & SS</p>
                            </a>
                        </li>

                        <li class="nav-item"> <a href="archive" class="nav-link"> <i class="nav-icon bi bi-archive"></i>
                                <p>Archive</p>
                            </a>
                        </li>
                        <!-- <li class="nav-item"> <a href="sop" class="nav-link"> <i class="nav-icon bi bi-journal-bookmark"></i>
                                <p>SOP</p>
                            </a>
                        </li> -->
                        <li class="nav-item"> <a href="eviden" class="nav-link"> <i class="nav-icon bi bi-box2"></i>
                                <p>Eviden</p>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>
        <main class="app-main">
            <!--begin::App Content Header-->
            <div class="mt-3">
                <!--begin::Container-->
                <!-- isi -->
            </div>
            <!--end::App Content Header-->
            <!--begin::App Content-->
            <div class="app-content">
                <!--begin::Container-->
                <div class="container-fluid" style="font-size:13px;">
                    <!--begin::Row-->
                    <?php while ($hasil = mysqli_fetch_assoc($result)) {
                        $idKPI = $hasil['id'];
                        $poin = $hasil['poin'];
                        $bobot = $hasil['bobot'];
                        $poin2 = $hasil['poin2'];
                        $bobot2 = $hasil['bobot2'];
                    ?>
                    <div class="row">
                        <div class="col-lg connarcectedSortable">
                            <div class="d-flex">
                                <div class="card mb-4 w-50" style="margin-right:7px;">
                                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                                        <h5 style="color:white;" class="card-title"><?= $poin; ?></h5>
                                        <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">Bobot :
                                            <?= $bobot ?>%
                                        </h5>
                                        <div class="card-tools">
                                            <button style="color: white;" type="button" class="btn btn-tool"
                                                data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Whats</th>
                                                    <th style="width: 30%">Hasil</th>
                                                    <th style="width: 5%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Bobot</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Total</center>
                                                    </th>
                                                    </th>
                                                    <th style="width: 9%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <?php $sql1 = "SELECT * FROM tbar_whats WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                                            $ql = mysqli_query($connarc, $sql1);
                                            while ($res = mysqli_fetch_assoc($ql)) {
                                                ?>
                                                <tbody>
                                                    <tr class="align-middle">
                                                        <td><?= $res['p_what']; ?></td>
                                                        <td><?= $res['hasil']; ?></td>
                                                        <td>
                                                            <center><?= $res['nilai']; ?>
                                                        </td>
                                                        <td>
                                                            <center><?= $res['bobot']; ?>%
                                                        </td>
                                                        <td>
                                                            <center><?= $res['total']; ?>
                                                        </td>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a value="<?php echo $res['id_what']; ?>" name="what_edit" class="dropdown-item"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#EditWhatModal<?= $res['id_what'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusWhatModal<?= $res['id_what'] ?>">Hapus</a>
                                                            </div>
                                                        </td>

                                                        <?php include('pages/kpi/k_modalHapuswhat.php'); ?>

                                                    </tr>
                                                </tbody>

                                                <?php include('pages/kpi/k_modalEditwhat.php'); ?>

                                            <?php } ?>
                                        </table>
                                    </div> <!-- /.card-body -->
                                </div> <!-- /.card -->
                                <!-- ======================================= -->
                                <div class="card mb-4 w-50" style="margin-left:7px;">
                                    <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
                                        <h5 style="color:white;" class="card-title"><?= $poin2; ?></h5>
                                        <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">
                                            Bobot :
                                            <?= $bobot2 ?>%
                                        </h5>
                                        <div class="card-tools">
                                            <button style="color: white;" type="button" class="btn btn-tool"
                                                data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                    <th>Hows</th>
                                                    <th style="width: 30%">Hasil</th>
                                                    <th style="width: 5%">
                                                        <center>Nilai</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Bobot</center>
                                                    </th>
                                                    <th style="width: 4%">
                                                        <center>Total</center>
                                                    </th>
                                                    <th style="width: 9%">
                                                        <center>Action</center>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <?php
                                            $sql1 = "SELECT * FROM tbar_hows WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                                            $ql = mysqli_query($connarc, $sql1);
                                            while ($res = mysqli_fetch_assoc($ql)) {
                                                ?>
                                                <tbody>
                                                    <tr class="align-middle">
                                                        <td><?= $res['p_how']; ?></td>
                                                        <td><?= $res['hasil']; ?></td>
                                                        <td>
                                                            <center><?= $res['nilai']; ?>
                                                        </td>
                                                        <td>
                                                            <center><?= $res['bobot']; ?>%
                                                        </td>
                                                        <td>
                                                            <center><?= $res['total']; ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a value="<?php echo $res['id_how']; ?>" name="how_edit" class="dropdown-item"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#EditHowModal<?= $res['id_how'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusHowModal<?= $res['id_how'] ?>">Hapus</a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <?php include('pages/kpi/k_modalEdithow.php'); ?>
                                                <?php include('pages/kpi/k_modalHapushow.php'); ?>
                                            <?php } ?>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <!--end::Container-->
            </div>
            <!--end::App Content-->
        </main>
        <!--end::App Main-->
        <!--begin::Footer-->
        <?php include("pages/part/p_footer.php"); ?>
</body>

</html>