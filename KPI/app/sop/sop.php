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
                    <li class="nav-item d-none d-md-block"> <a href="sop" class="nav-link active">SOP Karisma</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="sop-prioritas" class="nav-link">SOP Prioritas</a> </li>
                    <li class="nav-item d-none d-md-block"> <a href="sop-departemen" class="nav-link">SOP Departemen</a></li>
                    <li class="nav-item d-none d-md-block"> <a href="updatesop" class="nav-link text-primary fw-semibold"><i class="bi bi-plus-circle me-1"></i>Kelola / Upload SOP</a></li>
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

        <main class="app-main">
            <div class="app-content-header py-3">
                <div class="container-fluid">
                    <div class="row align-items-center">
                        <div class="col-sm-6">
                            <h3 class="mb-0 fw-bold text-dark">SOP Karisma</h3>
                            <p class="text-muted small mb-0">Daftar Standard Operating Procedure Karisma</p>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-2 mt-sm-0">
                            <a href="updatesop" class="btn btn-outline-primary btn-sm px-3">
                                <i class="bi bi-gear-fill me-1"></i> Kelola / Upload SOP
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="app-content pb-5">
                <div class="container-fluid">
                    <div class="row g-3">
                        <?php 
                        $has_data = false;
                        while($row = mysqli_fetch_assoc($resultsop)){ 
                            $has_data = true;
                            $file_path = "assets/pdf/sop/" . $row['namafile_sop'];
                        ?>
                            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                                <div class="card h-100 border-0 shadow-sm rounded-3 hover-shadow transition">
                                    <div class="card-header bg-light border-bottom py-2 d-flex justify-content-between align-items-center">
                                        <span class="badge bg-primary-subtle text-primary font-monospace"><?= htmlspecialchars($row['kode_sop']);?></span>
                                        <span class="badge bg-secondary-subtle text-secondary small"><?= htmlspecialchars($row['tipe_sop'] ?: 'Umum');?></span>
                                    </div>
                                    <div class="card-body d-flex flex-column justify-content-between p-3">
                                        <h6 class="card-title fw-bold text-dark mb-3"><?= htmlspecialchars($row['nama_sop']);?></h6>
                                        <button type="button" class="btn btn-outline-primary btn-sm w-100 mt-auto" data-bs-toggle="modal" data-bs-target="#openSOP<?= $row['id_sop'] ?>">
                                            <i class="bi bi-file-earmark-pdf me-1"></i> Buka SOP
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Modal Preview -->
                            <div class="modal fade" id="openSOP<?= $row['id_sop'] ?>" tabindex="-1" aria-labelledby="openSOPLabel<?= $row['id_sop'] ?>" aria-hidden="true">
                                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content shadow border-0">
                                        <div class="modal-header bg-light py-2">
                                            <h5 class="modal-title fw-bold text-dark text-truncate" id="openSOPLabel<?= $row['id_sop'] ?>">
                                                <?= htmlspecialchars($row['kode_sop']." - ".$row['nama_sop']) ?>
                                            </h5>
                                            <div class="d-flex align-items-center gap-2">
                                                <a href="<?= htmlspecialchars($file_path) ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> Tab Baru
                                                </a>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                        </div>
                                        <div class="modal-body p-0" style="background-color: #525659;">
                                            <iframe src="<?= htmlspecialchars($file_path) ?>" width="100%" height="700px" style="border: none;"></iframe>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if(!$has_data){ ?>
                            <div class="col-12">
                                <div class="card border-0 shadow-sm p-5 text-center text-muted">
                                    <i class="bi bi-journal-x fs-1 mb-2"></i>
                                    <h5>Belum ada dokumen SOP yang tersedia.</h5>
                                    <p class="mb-3">Silakan tambahkan dokumen SOP baru melalui menu kelola SOP.</p>
                                    <div>
                                        <a href="updatesop" class="btn btn-primary btn-sm px-3">
                                            <i class="bi bi-upload me-1"></i> Upload SOP Sekarang
                                        </a>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </main>
        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>


</html>