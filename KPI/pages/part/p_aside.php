<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="home-kpi-real" class="brand-link">
            <img src="assets/img/logokpi.png" alt="Logo" class="brand-image opacity-100">
            <span class="brand-text fw-light">KPI Digital</span>
        </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">

                <li class="nav-item"> <a href="home-kpi-real" class="nav-link"> <i class="nav-icon bi bi-plus-circle"></i>
                        <p>KPI & SS</p>
                    </a>
                </li>

                <li class="nav-item"> <a href="archive" class="nav-link"> <i class="nav-icon bi bi-archive"></i>
                        <p>Archive</p>
                    </a>
                </li>
                <li class="nav-item"> <a href="sop" class="nav-link"> <i class="nav-icon bi bi-journal-bookmark"></i>
                        <p>SOP</p>
                    </a>
                </li>
                <li class="nav-item"> <a href="eviden" class="nav-link"> <i class="nav-icon bi bi-box2"></i>
                        <p>Eviden</p>
                    </a>
                </li>
                <?php if (isset($_SESSION['level']) && $_SESSION['level'] >= 5 && ($_SESSION['id_user'] ?? 0) != 1) { ?>
                <li class="nav-item"> <a href="data-karyawan" class="nav-link"> <i class="nav-icon bi bi-people-fill"></i>
                        <p>Data Karyawan</p>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </nav>
    </div>
</aside>