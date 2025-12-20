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
}
?>
<html lang="en">

<?php include("pages/part/p_header.php"); 
echo '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">';?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary"> <!--begin::App Wrapper-->
    <div class="app-wrapper"> <!--begin::Header-->
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i
                                class="bi bi-list"></i> </a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="archive" class="nav-link">Kembali</a> </li>
                    
                    <!-- <li class="nav-item d-none d-md-block"> <a href="dashboard" class="nav-link">Archive SS</a> </li> -->
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
        </nav> <!--end::Header--> <!--begin::Sidebar-->
        <?php include("pages/part/p_aside.php"); ?>
        <main class="app-main">
            <div class="app-content">
                <div class="app-content"> <!--begin::Container-->
                    <div class="container-fluid"> <!--begin::Row-->
                        <div class="row"> <!-- Start col -->
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
                                                <th width="25%">
                                                    <center>Jabatan</center>
                                                </th>
                                                <th width="25%">
                                                    <center>Bagian</center>
                                                </th>
                                                <th width="10%">
                                                    <center>#</center>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php $no = 1; while ($row = mysqli_fetch_assoc($resultevidenangg)) { ?>
                                            <tr>
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
                                                <td>
                                                    <center>
                                                        <?php if($row['jabatan']!='Kabag' && $row['jabatan'] !='Kadep'){ ?>
                                                        <a type="button" class="btn btn-sm btn-success" href="archiveanggota?id=<?= $row['id']; ?>"><i class="bi bi-eye"></i></a>
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