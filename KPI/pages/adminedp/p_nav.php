<?php
// File: pages/adminedp/p_nav.php
// Cek SP aktif untuk driver yang sedang dikelola
$sp_aktif = $selected_driver_id > 0 ? getActiveSP($conn, $selected_driver_id) : null;
?>

<nav class="app-header navbar navbar-expand bg-body">
    <div class="container-fluid">
        <ul class="navbar-nav nav-underline">
            <li class="nav-item">
                <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                    <i class="bi bi-list"></i>
                </a>
            </li>
            <li class="nav-item d-none d-md-block">
                <a href="home-adminedp" class="nav-link">
                    <i class="bi bi-house-fill me-1"></i>Dashboard
                </a>
            </li>

            <?php if ($selected_driver_id > 0) { ?>
                <li class="nav-item d-none d-md-block">
                    <a href="kpidetailanggota?id=<?= $selected_driver_id ?>&ref=home-adminedp" class="nav-link">
                        <i class="bi bi-clipboard-data me-1"></i>Detail KPI Driver
                    </a>
                </li>
            <?php } ?>

            <!-- Badge Info Driver -->
            <?php if ($driver_data) { ?>
                <li class="nav-item d-none d-md-block">
                    <span class="nav-link">
                        <span class="badge bg-info">
                            <i class="bi bi-person-fill me-1"></i>
                            Mengelola: <?= $driver_data['nama_lngkp'] ?>
                        </span>
                    </span>
                </li>
            <?php } ?>

            <!-- Notifikasi SP Aktif -->
            <?php if ($sp_aktif) { ?>
                <li class="nav-item d-none d-md-block">
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#modalInfoSP">
                        <span class="badge bg-<?= getSPBadgeClass($sp_aktif['jenis_sp']) ?> pulse">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                            <?= $sp_aktif['jenis_sp'] ?> Aktif untuk Driver ini
                        </span>
                    </a>
                </li>
            <?php } ?>
        </ul>

        <ul class="navbar-nav ms-auto">
            <li class="nav-item">
                <a class="nav-link" href="#" data-lte-toggle="fullscreen">
                    <i data-lte-icon="maximize" class="bi bi-arrows-fullscreen"></i>
                    <i data-lte-icon="minimize" class="bi bi-fullscreen-exit" style="display: none;"></i>
                </a>
            </li>

            <!-- User Menu dengan Badge Admin EDP -->
            <li class="nav-item dropdown user-menu">
                <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                    <img style="margin-top: -2px;" src="assets/img/profile.png" class="user-image rounded-circle shadow"
                        alt="User Image">
                    <span class="d-none d-md-inline">
                        <?php echo $username ?>
                        <span class="badge bg-primary ms-1">Admin EDP</span>
                    </span>
                </a>
                <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                    <li class="user-footer">
                        <center>
                            <a href="logout.php" class="btn btn-default btn-flat float-center">Sign out</a>
                        </center>
                    </li>
                </ul>
            </li>
        </ul>
    </div>
</nav>

<!-- Modal Info SP untuk Driver -->
<?php if ($sp_aktif) { ?>
    <div class="modal fade" id="modalInfoSP" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-<?= getSPBadgeClass($sp_aktif['jenis_sp']) ?> text-white">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        Informasi Surat Peringatan Driver
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong><i class="bi bi-person-fill"></i> Driver:</strong> <?= $driver_data['nama_lngkp'] ?><br>
                        <strong><i class="bi bi-card-text"></i> NIK:</strong> <?= $driver_data['nik'] ?>
                    </div>

                    <div class="alert alert-<?= getSPBadgeClass($sp_aktif['jenis_sp']) ?> mb-3">
                        <h6 class="alert-heading">
                            <strong><?= $sp_aktif['jenis_sp'] ?></strong> - <?= $sp_aktif['nomor_sp'] ?>
                        </h6>
                        <hr>
                        <p class="mb-0">
                            <strong>Tanggal:</strong> <?= formatTanggalIndo($sp_aktif['tanggal_sp']) ?><br>
                            <strong>Masa Berlaku:</strong><br>
                            <?= formatTanggalIndo($sp_aktif['masa_berlaku_mulai']) ?> s/d
                            <?= formatTanggalIndo($sp_aktif['masa_berlaku_selesai']) ?>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold">Alasan:</label>
                        <p><?= $sp_aktif['alasan'] ?></p>
                    </div>

                    <?php if (!empty($sp_aktif['keterangan'])) { ?>
                        <div class="mb-3">
                            <label class="fw-bold">Keterangan:</label>
                            <p><?= $sp_aktif['keterangan'] ?></p>
                        </div>
                    <?php } ?>

                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-circle-fill"></i>
                        <strong>Dampak pada KPI:</strong><br>
                        Nilai KPI driver ini dikurangi <strong><?= getSPPenalty($sp_aktif['jenis_sp']) ?> poin</strong>
                        selama masa berlaku surat peringatan.
                    </div>

                    <div class="alert alert-info mb-0">
                        <i class="bi bi-calendar-check"></i>
                        SP ini akan berakhir pada <strong><?= formatTanggalIndo($sp_aktif['masa_berlaku_selesai']) ?></strong>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

<style>
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7);
        }

        70% {
            box-shadow: 0 0 0 10px rgba(220, 53, 69, 0);
        }

        100% {
            box-shadow: 0 0 0 0 rgba(220, 53, 69, 0);
        }
    }

    .badge.pulse {
        animation: pulse 2s infinite;
    }
</style>