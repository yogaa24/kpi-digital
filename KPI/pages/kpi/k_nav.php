<nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Start Navbar Links-->
        <ul class="navbar-nav nav-underline">
            <li class="nav-item"> <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"> <i class="bi bi-list"></i> </a> </li>
            <li class="nav-item d-none d-md-block"> <a href="dashboard" class="nav-link">Dashboard</a> </li>
            <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal" class="nav-link">Tambah Poin KPI</a> </li>
            <!-- <li class="nav-item d-none d-md-block"> <a href="#" data-bs-toggle="modal" data-bs-target="#detailModal" class="nav-link">Tambah Detail Poin KPI</a> </li> -->
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
<?php include('pages/kpi/k_modalAdd.php');?>
