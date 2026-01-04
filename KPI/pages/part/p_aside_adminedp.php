<?php
// File: pages/part/p_aside_adminedp.php
?>

<aside class="app-sidebar bg-body-secondary shadow" data-bs-theme="dark">
    <div class="sidebar-brand">
        <a href="home-adminedp" class="brand-link">
            <img src="assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">KPI Digital</span>
        </a>
    </div>
    
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                
                <!-- Header Admin EDP -->
                <li class="nav-header">ADMIN EDP MENU</li>
                
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="home-adminedp" class="nav-link">
                        <i class="nav-icon bi bi-speedometer2"></i>
                        <p>Dashboard EDP</p>
                    </a>
                </li>
                
                <!-- Kelola KPI Driver -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="nav-icon bi bi-clipboard-data"></i>
                        <p>
                            Kelola KPI Driver
                            <i class="nav-arrow bi bi-chevron-right"></i>
                        </p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="home-adminedp" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Pilih & Kelola Driver</p>
                            </a>
                        </li>
                        <?php if ($selected_driver_id > 0) { ?>
                        <li class="nav-item">
                            <a href="kpianggota-adminedp?id=<?=$selected_driver_id?>" class="nav-link">
                                <i class="nav-icon bi bi-circle"></i>
                                <p>Detail KPI Driver</p>
                            </a>
                        </li>
                        <?php } ?>
                    </ul>
                </li>
                
                <!-- Daftar Driver -->
                <li class="nav-item">
                    <a href="daftardriver-adminedp" class="nav-link">
                        <i class="nav-icon bi bi-people-fill"></i>
                        <p>Daftar Driver Distribusi</p>
                    </a>
                </li>
                
                <!-- Logout -->
                <li class="nav-item">
                    <a href="logout.php" class="nav-link text-danger">
                        <i class="nav-icon bi bi-box-arrow-right"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<style>
.sidebar-brand {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.nav-header {
    color: #adb5bd;
    font-weight: bold;
    font-size: 0.75rem;
    text-transform: uppercase;
    padding: 0.5rem 1rem;
    margin-top: 1rem;
}

.nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
</style>