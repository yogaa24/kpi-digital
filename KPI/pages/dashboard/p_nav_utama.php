<nav class="app-header navbar navbar-expand bg-body"> <!--begin::Container-->
    <div class="container-fluid"> <!--begin::Start Navbar Links-->
        <ul class="navbar-nav nav-underline">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>

            <li class="nav-item d-none d-md-block">
                <a href="dashboard-utama" class="nav-link">Dashboard</a>
            </li>
        </ul>
        <!--end::Start Navbar Links--> <!--begin::End Navbar Links-->
                
        <ul class="navbar-nav ms-auto">

    <!-- FULLSCREEN ICON -->
    <li class="nav-item">
        <a class="nav-link" href="#" data-lte-toggle="fullscreen">
            <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
            <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display:none"></i>
        </a>
    </li>

    <!-- USER MENU -->
    <li class="nav-item dropdown user-menu">
        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
            <img src="assets/img/profile.png"
                 class="user-image rounded-circle shadow"
                 alt="User Image">
            <span class="d-none d-md-inline">
                <?php echo $username ?>
            </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end">
            

            <li class="user-body text-center">
                <a href="profile-settings" class="d-block">
                    <i class="bi bi-gear-fill me-1"></i> Account Settings
                </a>
            </li>

            <li class="user-footer px-3">
                <a href="logout.php" class="btn btn-default btn-flat w-100">
                    <i class="bi bi-box-arrow-right me-1"></i> Sign out
                </a>
            </li>
        </ul>
    </li>
</ul>
       
        
    </div> <!--end::Container-->
</nav> <!--end::Header--> <!--begin::Sidebar-->
