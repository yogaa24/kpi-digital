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

                <?php $aside_dash_url = (isset($_SESSION['level']) && $_SESSION['level'] == 7) ? 'dashboard-adminhrd' : 'dashboard-utama'; ?>
                <li class="nav-item"> <a href="<?= $aside_dash_url ?>" class="nav-link"> <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard</p>
                    </a>
                </li>


                <li class="nav-item"> <a href="archive-adminhrd" class="nav-link"> <i class="nav-icon bi bi-archive"></i>
                        <p>Archive</p>
                    </a>
                </li>
                <li class="nav-item"> <a href="sop" class="nav-link"> <i class="nav-icon bi bi-journal-bookmark"></i>
                        <p>SOP</p>
                    </a>
                </li>
                <li class="nav-item"> <a href="eviden-adminhrd" class="nav-link"> <i class="nav-icon bi bi-box2"></i>
                        <p>Eviden</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>