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

?>

<html lang="en">
<?php include("pages/part/p_header.php"); ?>

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
            <div class="container-fluid"> <!--begin::Start Navbar Links-->
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item d-none d-md-block"> <a href="sop" class="nav-link">SOP Karisma</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="sop-prioritas" class="nav-link">SOP Prioritas</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="sop-departemen" class="nav-link">SOP Departemen</a></li>
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
                                <?php if($leveel==2){ ?>
                                    <center> <a href="updateSOP" class="btn btn-default btn-flat float-center">SOP</a></center>
                                <?php }; ?>
                                <center> <a href="logout" class="btn btn-default btn-flat float-center">Sign Out</a></center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>
        <?php include("pages/part/p_aside.php"); ?>

        <div class="" style="margin-right:12px;">
            <div class="row p-5">
                <?php while($row = mysqli_fetch_assoc($resultsop)){ ?>
                    <div class="card" style="width: 19rem; margin-right : 10px; margin-bottom : 10px;">
                        <a class="card-block stretched-link text-decoration-none text-undeline-none text-black" href data-bs-toggle="modal"
                                data-bs-target="#openSOP<?= $row['id_sop'] ?>" >
                            <div class="card-header fw-bold" style="height: 48px;">
                                <?= $row['kode_sop'];?>
                            </div>
                            <div class="card-body">
                                <p class="card-text" style="margin-top:-5px"><?= $row['nama_sop'];?></p>
                            </div>
                        </a>
                    </div>
                    <div class="modal fade" id="openSOP<?= $row['id_sop'] ?>" tabindex="-1" aria-labelledby="openSOP" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold" id="openSOP"><?= $row['kode_sop']." - ".$row['nama_sop'] ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <iframe src="assets\pdf\sop\<?= $row['namafile_sop'] ?>" width="100%" height="650px"></iframe>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>


</html>