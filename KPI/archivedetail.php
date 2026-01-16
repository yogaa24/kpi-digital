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

<style>
.edited-badge {
    background-color: #ffc107;
    color: #000;
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 3px;
    margin-left: 5px;
    font-weight: bold;
}

.edited-row {
    background-color: #fff3cd !important;
}

.edited-info {
    font-size: 11px;
    color: #856404;
    font-style: italic;
    margin-top: 3px;
}

.edited-by-info {
    font-size: 10px;
    color: #856404;
    background-color: #fff3cd;
    padding: 2px 5px;
    border-radius: 3px;
    display: inline-block;
    margin-top: 2px;
}
</style>

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
                    <?php 
                    // Cek apakah ada data yang diubah atasan
                    $sql_check_edited = "SELECT COUNT(*) as total_edited 
                                        FROM (
                                            SELECT id_what FROM tbar_whats WHERE id_user='$id_user' AND is_edited=1
                                            UNION ALL
                                            SELECT id_how FROM tbar_hows WHERE id_user='$id_user' AND is_edited=1
                                        ) as edited_items";
                    $result_check = mysqli_query($connarc, $sql_check_edited);
                    $data_check = mysqli_fetch_assoc($result_check);
                    
                    if ($data_check['total_edited'] > 0) {
                    ?>
                        <div class="alert alert-warning alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="bi bi-exclamation-triangle-fill"></i> Ada Perubahan dari Atasan!
                            </h5>
                            <p class="mb-0">
                                Terdapat <strong><?= $data_check['total_edited'] ?> item</strong> yang telah diubah atau dinilai oleh atasan Anda.
                                Baris yang diubah ditandai dengan <span class="edited-badge">DIUBAH ATASAN</span> dan background kuning.
                            </p>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php } ?>
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
                                            <tbody>
                                                <?php 
                                                $sql1 = "SELECT * FROM tbar_whats WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                                                $ql = mysqli_query($connarc, $sql1);
                                                while ($res = mysqli_fetch_assoc($ql)) {
                                                ?>
                                                    <tr class="align-middle <?= ($res['is_edited'] == 1) ? 'edited-row' : '' ?>">
                                                        <td>
                                                            <?= $res['p_what']; ?>
                                                            
                                                            <?php if ($res['is_edited'] == 1) { ?>
                                                                <span class="edited-badge">
                                                                    <i class="bi bi-pencil-fill"></i> DIUBAH ATASAN
                                                                </span>
                                                                <br>
                                                                <small class="edited-info">
                                                                    <i class="bi bi-clock-history"></i> 
                                                                    Diubah pada: <?= date('d/m/Y H:i', strtotime($res['edited_at'])) ?>
                                                                </small>
                                                                <?php 
                                                                // Ambil nama editor (atasan)
                                                                if (!empty($res['edited_by'])) {
                                                                    $editor_id = $res['edited_by'];
                                                                    $sql_editor = "SELECT nama_lengkap FROM user WHERE id_user = $editor_id";
                                                                    $result_editor = mysqli_query($connarc, $sql_editor);
                                                                    if ($result_editor && mysqli_num_rows($result_editor) > 0) {
                                                                        $editor_data = mysqli_fetch_assoc($result_editor);
                                                                        echo '<br><small class="edited-by-info">';
                                                                        echo '<i class="bi bi-person-fill"></i> Oleh: ' . $editor_data['nama_lengkap'];
                                                                        echo '</small>';
                                                                    }
                                                                }
                                                                ?>
                                                            <?php } ?>
                                                            
                                                            <?php if ($res['tipe_what'] == 'B' && $res['target_omset'] > 0) { ?>
                                                                <br><small class="text-muted fw-semibold fs-6">
                                                                    Target: <?=number_format($res['target_omset'], 2)?>
                                                                </small>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?= $res['hasil']; ?>
                                                            <?php if ($res['is_edited'] == 1) { ?>
                                                                <span class="badge bg-warning text-dark" style="font-size: 9px;">
                                                                    <i class="bi bi-check-circle-fill"></i> Dinilai
                                                                </span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?= $res['nilai']; ?>
                                                                <?php if ($res['is_edited'] == 1) { ?>
                                                                    <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <center><?= $res['bobot']; ?>%</center>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?= $res['total']; ?>
                                                                <?php if ($res['is_edited'] == 1) { ?>
                                                                    <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <!-- <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a value="<?php echo $res['id_what']; ?>" name="what_edit" class="dropdown-item"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#EditWhatModal<?= $res['id_what'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusWhatModal<?= $res['id_what'] ?>">Hapus</a>
                                                            </div> -->
                                                        </td>

                                                        <?php include('pages/kpi/k_modalHapuswhat.php'); ?>
                                                    </tr>

                                                    <?php include('pages/kpi/k_modalEditwhat.php'); ?>

                                                <?php } ?>
                                            </tbody>
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
                                            <tbody>
                                                <?php
                                                $sql1 = "SELECT * FROM tbar_hows WHERE id_user='$id_user' AND id_kpi='" . $hasil['id'] . "'";
                                                $ql = mysqli_query($connarc, $sql1);
                                                while ($res = mysqli_fetch_assoc($ql)) {
                                                ?>
                                                    <tr class="align-middle <?= ($res['is_edited'] == 1) ? 'edited-row' : '' ?>">
                                                        <td>
                                                            <?= $res['p_how']; ?>
                                                            
                                                            <?php if ($res['is_edited'] == 1) { ?>
                                                                <span class="edited-badge">
                                                                    <i class="bi bi-pencil-fill"></i> DIUBAH ATASAN
                                                                </span>
                                                                <br>
                                                                <small class="edited-info">
                                                                    <i class="bi bi-clock-history"></i> 
                                                                    Diubah pada: <?= date('d/m/Y H:i', strtotime($res['edited_at'])) ?>
                                                                </small>
                                                                <?php 
                                                                // Ambil nama editor (atasan)
                                                                if (!empty($res['edited_by'])) {
                                                                    $editor_id = $res['edited_by'];
                                                                    $sql_editor = "SELECT nama_lengkap FROM user WHERE id_user = $editor_id";
                                                                    $result_editor = mysqli_query($connarc, $sql_editor);
                                                                    if ($result_editor && mysqli_num_rows($result_editor) > 0) {
                                                                        $editor_data = mysqli_fetch_assoc($result_editor);
                                                                        echo '<br><small class="edited-by-info">';
                                                                        echo '<i class="bi bi-person-fill"></i> Oleh: ' . $editor_data['nama_lengkap'];
                                                                        echo '</small>';
                                                                    }
                                                                }
                                                                ?>
                                                            <?php } ?>
                                                            
                                                            <?php if ($res['tipe_how'] == 'B' && $res['target_omset'] > 0) { ?>
                                                                <br><small class="text-muted fw-semibold fs-6">
                                                                    Target: <?=number_format($res['target_omset'], 2)?>
                                                                </small>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <?= $res['hasil']; ?>
                                                            <?php if ($res['is_edited'] == 1) { ?>
                                                                <span class="badge bg-warning text-dark" style="font-size: 9px;">
                                                                    <i class="bi bi-check-circle-fill"></i> Dinilai
                                                                </span>
                                                            <?php } ?>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?= $res['nilai']; ?>
                                                                <?php if ($res['is_edited'] == 1) { ?>
                                                                    <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <center><?= $res['bobot']; ?>%</center>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?= $res['total']; ?>
                                                                <?php if ($res['is_edited'] == 1) { ?>
                                                                    <i class="bi bi-star-fill text-warning" style="font-size: 10px;"></i>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                                                <i class="bi bi-eye fs-8"></i>
                                                            </button>
                                                            <!-- <div class="dropdown-menu dropdown-menu-end" role="menu">
                                                                <a value="<?php echo $res['id_how']; ?>" name="how_edit" class="dropdown-item"
                                                                    data-bs-toggle="modal"
                                                                    data-bs-target="#EditHowModal<?= $res['id_how'] ?>">Edit</a>
                                                                <a class="dropdown-item" data-bs-toggle="modal"
                                                                    data-bs-target="#HapusHowModal<?= $res['id_how'] ?>">Hapus</a>
                                                            </div> -->
                                                        </td>
                                                    </tr>
                                                    
                                                    <?php include('pages/kpi/k_modalEdithow.php'); ?>
                                                    <?php include('pages/kpi/k_modalHapushow.php'); ?>
                                                    
                                                <?php } ?>
                                            </tbody>
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