<!-- archivekabag.php -->
<!DOCTYPE html>
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {

    require 'helper/configarchive.php';
    require 'helper/getUser.php';
    require 'helper/getEviAnggota.php';
    require 'helper/status_functions.php'; // TAMBAHKAN INI
}
?>
<html lang="en">

<?php include("pages/part/p_header.php"); 
echo '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">';?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="archive" class="nav-link">Kembali</a> </li>
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
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="app-content">
                <div class="app-content">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="mt-3">
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
                                                <!-- TAMBAH KOLOM STATUS -->
                                                <th width="8%">
                                                    <center>Perlu Review</center>
                                                </th>
                                                <th width="8%">
                                                    <center>Reviewed</center>
                                                </th>
                                                <th width="8%">
                                                    <center>Approved</center>
                                                </th>
                                                <th width="8%">
                                                    <center>Total</center>
                                                </th>
                                                <th width="10%">
                                                    <center>#</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php 
                                        $no = 1; 
                                        while ($row = mysqli_fetch_assoc($resultevidenangg)) { 
                                            $user_id = $row['id'];
                                            
                                            // Hitung status per user
                                            $sql_status = "SELECT 
                                                            SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as need_review,
                                                            SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) as reviewed,
                                                            SUM(CASE WHEN status = 3 THEN 1 ELSE 0 END) as approved,
                                                            COUNT(*) as total
                                                           FROM tbar_archive 
                                                           WHERE id_user = $user_id";
                                            $result_status = mysqli_query($connarc, $sql_status);
                                            $status_data = mysqli_fetch_assoc($result_status);
                                            
                                            // Highlight baris jika ada yang perlu review
                                            $highlight = ($status_data['need_review'] > 0) ? 'background-color: #fff3cd;' : '';
                                        ?>
                                            <tr style="<?= $highlight ?>">
                                                <td>
                                                    <center><?= $no; ?></center>
                                                </td>
                                                <td>
                                                    <center><?= $row['nama_lngkp']; ?></center>
                                                </td>
                                                <td>
                                                    <center><?= $row['jabatan']; ?></center>
                                                </td>
                                                <td>
                                                    <center><?= $row['bagian']; ?></center>
                                                </td>
                                                <!-- KOLOM STATUS -->
                                                <td>
                                                    <center>
                                                        <?php if ($status_data['need_review'] > 0) { ?>
                                                            <span class="badge bg-warning text-dark fs-6">
                                                                <?= $status_data['need_review'] ?>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php } ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?php if ($status_data['reviewed'] > 0) { ?>
                                                            <span class="badge bg-info fs-6">
                                                                <?= $status_data['reviewed'] ?>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php } ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?php if ($status_data['approved'] > 0) { ?>
                                                            <span class="badge bg-success fs-6">
                                                                <?= $status_data['approved'] ?>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php } ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                        <?php if ($status_data['total'] > 0) { ?>
                                                            <span class="badge bg-primary fs-6">
                                                                <?= $status_data['total'] ?>
                                                            </span>
                                                        <?php } else { ?>
                                                            <span class="text-muted">N/A</span>
                                                        <?php } ?>
                                                    </center>
                                                </td>
                                                <td>
                                                    <center>
                                                    <?php if ($row['id'] != $_SESSION['id_user']) { ?>
                                                        <a type="button" class="btn btn-sm btn-success" 
                                                        href="archiveanggota?id=<?= $row['id']; ?>"
                                                        title="Lihat Archive">
                                                            <i class="bi bi-eye"></i> Lihat
                                                        </a>
                                                    <?php } else { ?>
                                                        <span class="text-muted">
                                                            <i class="bi bi-person"></i>
                                                        </span>
                                                    <?php } ?>
                                                    </center>
                                                </td>
                                            </tr>
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
    </div>
</body>

</html>