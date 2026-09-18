<!DOCTYPE html>
<?php
// app/archive/archivesspoin.php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: index");
    exit();
} else {
    require 'helper/config.php';
    require 'helper/getUser.php';
    require_once 'helper/ss_archive_functions.php';

    function convertBulanSS($blan) {
        $busd = explode('/', $blan);
        $bl = $busd[0] ?? '01';
        $th = $busd[1] ?? date('Y');

        $nama_bulan = [
            '00' => 'Desember ' . ($th - 1),
            '01' => 'Januari ' . $th,
            '02' => 'Februari ' . $th,
            '03' => 'Maret ' . $th,
            '04' => 'April ' . $th,
            '05' => 'Mei ' . $th,
            '06' => 'Juni ' . $th,
            '07' => 'Juli ' . $th,
            '08' => 'Agustus ' . $th,
            '09' => 'September ' . $th,
            '10' => 'Oktober ' . $th,
            '11' => 'November ' . $th,
            '12' => 'Desember ' . $th
        ];
        return $nama_bulan[$bl] ?? ($bl . '/' . $th);
    }

    $id_target_user = isset($_GET['id']) ? intval($_GET['id']) : intval($_SESSION['id_user']);
    $bulan_archive = isset($_GET['idarc']) ? trim($_GET['idarc']) : '';
    $ref = isset($_GET['ref']) ? trim($_GET['ref']) : '';

    if (empty($bulan_archive)) {
        echo "<script>alert('Bulan archive tidak ditemukan!'); window.location.href='archive?tab=ss';</script>";
        exit();
    }

    // Ambil info karyawan pemilik arsip
    $query_target_user = mysqli_query($conn, "SELECT * FROM tb_users WHERE id = $id_target_user");
    $target_user = mysqli_fetch_assoc($query_target_user);

    if (!$target_user) {
        echo "<script>alert('User tidak ditemukan!'); window.location.href='archive?tab=ss';</script>";
        exit();
    }

    $bulan_archive_safe = mysqli_real_escape_string($conn, $bulan_archive);

    // Ambil header archive SS
    $query_archive = mysqli_query($conn, "SELECT a.*, u.nama_lngkp as verifikator_nama 
                                          FROM tbar_ss_archive a 
                                          LEFT JOIN tb_users u ON u.id = a.verified_by 
                                          WHERE a.id_user = $id_target_user AND a.bulan = '$bulan_archive_safe'");
    $archive_header = mysqli_fetch_assoc($query_archive);

    if (!$archive_header) {
        echo "<script>alert('Data archive Skill Standard tidak ditemukan untuk periode ini!'); window.history.back();</script>";
        exit();
    }

    $id_ss_archive = intval($archive_header['id_ss_archive']);

    // Tentukan URL kembali berdasarkan referer
    if ($ref === 'hrd') {
        $back_url = "archive-adminhrd-detail?id=" . $id_target_user . "&tab=ss";
    } elseif ($ref === 'kabag') {
        $back_url = "archiveanggota?id=" . $id_target_user . "&tab=ss";
    } else {
        $back_url = "archive?tab=ss";
    }

    $active_tab_ss = isset($_GET['tab']) && in_array($_GET['tab'], ['umum', 'teknis']) ? $_GET['tab'] : 'umum';
}
?>
<html lang="en">
<?php include("pages/part/p_header.php"); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
    integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<body class="layout-fixed sidebar-expand-lg sidebar-mini sidebar-collapse bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav nav-underline">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button"><i class="bi bi-list"></i></a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="<?= $back_url ?>" class="nav-link"><i class="bi bi-arrow-left me-1"></i> Kembali</a>
                    </li>
                    <li class="nav-item d-none d-md-block">
                        <a href="dashboard-utama" class="nav-link">Dashboard</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="assets/img/profile.png" class="user-image rounded-circle shadow" alt="User Image">
                            <span class="d-none d-md-inline"><?= htmlspecialchars($username) ?></span>
                        </a>
                        <ul style="width: 80px;" class="dropdown-menu dropdown-menu-end">
                            <li class="user-footer">
                                <center><a href="logout.php" class="btn btn-default btn-flat">Sign out</a></center>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <?php include("pages/part/p_aside.php"); ?>

        <div class="m-3">
            <div class="container-fluid mt-2" style="font-size:13px;">

                <!-- Header Info Banner Archive -->
                <div class="card shadow-sm border-0 mb-3 bg-white">
                    <div class="card-body py-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-dark fs-6 px-3 py-1">
                                        Periode: <?= convertBulanSS($archive_header['bulan']) ?>
                                    </span>
                                    <?php if ($archive_header['status'] == 1) { ?>
                                        <span class="badge bg-success fs-6 px-3 py-1">
                                            <i class="bi bi-check-circle me-1"></i> Terverifikasi
                                        </span>
                                    <?php } else { ?>
                                        <span class="badge bg-warning text-dark fs-6 px-3 py-1">
                                            <i class="bi bi-clock-history me-1"></i> Belum Terverifikasi
                                        </span>
                                    <?php } ?>
                                </div>
                                <h5 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($target_user['nama_lngkp']) ?></h5>
                                <small class="text-muted">
                                    <?= htmlspecialchars($target_user['jabatan']) ?> - <?= htmlspecialchars($target_user['departement']) ?> | <?= htmlspecialchars($target_user['bagian']) ?>
                                    <?php if (!empty($archive_header['verifikator_nama'])) { ?>
                                        &bull; Diverifikasi oleh: <strong><?= htmlspecialchars($archive_header['verifikator_nama']) ?></strong>
                                    <?php } ?>
                                </small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <div class="text-center px-3 py-1 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:11px;">Rata-rata Umum</small>
                                    <strong class="fs-6 text-primary"><?= number_format($archive_header['rata_rata_umum'], 2) ?></strong>
                                </div>
                                <div class="text-center px-3 py-1 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:11px;">Rata-rata Teknis</small>
                                    <strong class="fs-6 text-success"><?= number_format($archive_header['rata_rata_teknis'], 2) ?></strong>
                                </div>
                                <div class="text-center px-3 py-1 border rounded bg-light">
                                    <small class="text-muted d-block" style="font-size:11px;">Nilai Akhir</small>
                                    <span class="badge bg-primary text-white fs-6">
                                        <?= number_format($archive_header['rata_rata_total'], 2) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($archive_header['keterangan'])) { ?>
                            <div class="alert alert-light border mb-0 mt-2 py-2 px-3 small">
                                <strong><i class="bi bi-chat-left-quote me-1"></i> Catatan Atasan:</strong> 
                                <?= htmlspecialchars($archive_header['keterangan']) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Periode & Tabs Header (Sama seperti halaman skillstandard.php) -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <ul class="nav nav-tabs mb-0">
                        <li class="nav-item">
                            <a class="nav-link fw-bold <?= $active_tab_ss === 'umum' ? 'active' : ''; ?>" 
                               href="archivesspoin?idarc=<?= urlencode($bulan_archive) ?>&id=<?= $id_target_user ?>&ref=<?= urlencode($ref) ?>&tab=umum">
                                <i class="bi bi-person-check me-1"></i>Skill Standard Umum
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold <?= $active_tab_ss === 'teknis' ? 'active' : ''; ?>" 
                               href="archivesspoin?idarc=<?= urlencode($bulan_archive) ?>&id=<?= $id_target_user ?>&ref=<?= urlencode($ref) ?>&tab=teknis">
                                <i class="bi bi-tools me-1"></i>Skill Standard Teknis
                            </a>
                        </li>
                    </ul>
                    <div>
                        <span class="badge bg-secondary p-2">
                            <i class="bi bi-lock-fill me-1"></i> Mode Arsip (Read-Only)
                        </span>
                    </div>
                </div>

                <?php
                $no = 1;
                $tipe_filter = mysqli_real_escape_string($conn, $active_tab_ss);
                $sqler = "SELECT * FROM tbar_ss 
                          WHERE id_ss_archive = $id_ss_archive AND tipe_ss = '$tipe_filter' 
                          ORDER BY id ASC";
                $tewg = mysqli_query($conn, $sqler);

                if ($tewg && mysqli_num_rows($tewg) > 0) {
                    while ($hasil = mysqli_fetch_assoc($tewg)) {
                        $id_tbar_ss = intval($hasil['id']);

                        // Ambil semua butir poin kategori arsip ini
                        $sql_poin = "SELECT * FROM tbar_sspoin WHERE id_tbar_ss = $id_tbar_ss ORDER BY id ASC";
                        $ql = mysqli_query($conn, $sql_poin);

                        // Hitung rata-rata kategori ini
                        $total_kat = 0; $count_kat = 0;
                        $poin_rows = [];
                        while ($r_p = mysqli_fetch_assoc($ql)) {
                            $total_kat += (float)$r_p['nilaiss'];
                            $count_kat++;
                            $poin_rows[] = $r_p;
                        }
                        $avg_kat = ($count_kat > 0) ? round($total_kat / $count_kat, 2) : 0.00;
                ?>
                    <div class="row">
                        <div class="col-lg connectedSortable">
                            <div class="d-flex">
                                <div class="card mb-4 w-100" style="margin-right:7px;">
                                    <div class="card-header bg-primary d-flex align-items-center justify-content-between gap-2 flex-wrap" style="min-height: 52px;">
                                        <div class="d-flex align-items-center gap-2 flex-wrap">
                                            <h5 style="color:white;" class="card-title mb-0">
                                                <?= $no . '. ' . htmlspecialchars($hasil['poin_ss']); ?>
                                            </h5>
                                            <span class="badge text-bg-warning fw-bolder">
                                                Rata-rata: <?= number_format($avg_kat, 2); ?>
                                            </span>
                                        </div>
                                        <div class="card-tools d-flex align-items-center gap-1 ms-auto">
                                            <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                                                <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                                                <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="card-body p-0">
                                        <table class="table table-striped table-bordered mb-0 align-middle">
                                            <thead class="table-primary">
                                                <tr>
                                                    <th style="width: 5%"><center>No</center></th>
                                                    <th style="padding-left: 20px">Poin</th>
                                                    <th style="width: 15%"><center>Nilai</center></th>
                                                    <th style="width: 40%"><center>Deskripsi Penilaian</center></th>
                                                    <th style="width: 10%"><center>Action</center></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $nodd = 1;
                                                foreach ($poin_rows as $res) {
                                                    $display_nilai = (float)$res['nilaiss'];
                                                    $display_desc = $res['deskripsi'];
                                                ?>
                                                    <tr class="align-middle">
                                                        <td><center><?= $no . '.' . $nodd ?></center></td>
                                                        <td style="padding-left: 20px;">
                                                            <strong><?= htmlspecialchars($res['poinss']); ?></strong>
                                                        </td>
                                                        <td>
                                                            <center>
                                                                <?php if ($display_nilai != 0) { ?>
                                                                    <span class="badge bg-success fs-7 px-3 py-2">
                                                                        <?= number_format($display_nilai, 2); ?>
                                                                    </span>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-warning fs-8">Belum Dinilai</span>
                                                                <?php } ?>
                                                            </center>
                                                        </td>
                                                        <td>
                                                            <?php if (!empty($display_desc)) { ?>
                                                                <small class="text-dark"><?= nl2br(htmlspecialchars($display_desc)); ?></small>
                                                            <?php } else { ?>
                                                                <small class="text-muted fst-italic">Tidak ada deskripsi.</small>
                                                            <?php } ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal"
                                                                data-bs-target="#LihatSSS<?= $res['id'] ?>" title="Lihat Detail">
                                                                <i class="bi bi-eye fs-8"></i> Detail
                                                            </button>
                                                        </td>
                                                    </tr>

                                                    <!-- Modal Lihat Detail (Sama persis dengan skillstandard.php) -->
                                                    <div class="modal fade" id="LihatSSS<?= $res['id'] ?>" tabindex="-1" aria-labelledby="LihatModalLabel<?= $res['id'] ?>" aria-hidden="true">
                                                        <div class="modal-dialog modal-lg">
                                                            <div class="modal-content">
                                                                <div class="modal-header bg-primary text-white">
                                                                    <h5 class="modal-title fw-bold" id="LihatModalLabel<?= $res['id'] ?>">
                                                                        <i class="bi bi-info-circle-fill me-1"></i> Detail Skill Standard (Arsip)
                                                                    </h5>
                                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                </div>
                                                                <div class="modal-body">
                                                                    <div class="input-group mb-3">
                                                                        <span style="color : #343A40;" class="input-group-text fw-bold">Poin :</span>
                                                                        <input type="text" value="<?= htmlspecialchars($res['poinss']); ?>" class="form-control" disabled>
                                                                    </div>
                                                                    
                                                                    <div class="input-group mb-3">
                                                                        <span style="color : #343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                                        <input type="text" value="<?= number_format($res['nilaiss'], 2); ?>" class="form-control" disabled>
                                                                    </div>
                                                                    
                                                                    <div class="mb-3">
                                                                        <label class="form-label fw-bold">Deskripsi Penilaian :</label>
                                                                        <textarea class="form-control" rows="4" disabled><?= htmlspecialchars($res['deskripsi']); ?></textarea>
                                                                    </div>
                                                                    
                                                                    <!-- Tampilkan Indikator Penilaian Level 1 - Level 4 -->
                                                                    <?php if (!empty($res['nilai1']) || !empty($res['nilai2']) || !empty($res['nilai3']) || !empty($res['nilai4'])) { ?>
                                                                    <div class="alert alert-light border mt-3">
                                                                        <h6 class="fw-bold mb-3 text-dark">
                                                                            <i class="bi bi-bar-chart-fill me-1 text-primary"></i> Rubrik Indikator Penilaian
                                                                        </h6>

                                                                        <?php if (!empty($res['nilai1'])) { ?>
                                                                        <div class="mb-2 p-2 rounded <?= ($display_nilai >= 1.0 && $display_nilai < 2.0) ? 'bg-danger-subtle border border-danger' : 'bg-light' ?>">
                                                                            <span class="badge bg-danger me-2">Nilai 1</span>
                                                                            <span class="fw-semibold text-dark">
                                                                                <?= nl2br(htmlspecialchars($res['nilai1'])); ?>
                                                                            </span>
                                                                        </div>
                                                                        <?php } ?>

                                                                        <?php if (!empty($res['nilai2'])) { ?>
                                                                        <div class="mb-2 p-2 rounded <?= ($display_nilai >= 2.0 && $display_nilai < 3.0) ? 'bg-warning-subtle border border-warning' : 'bg-light' ?>">
                                                                            <span class="badge me-2" style="background-color:#fd7e14; color:white;">Nilai 2</span>
                                                                            <span class="fw-semibold text-dark">
                                                                                <?= nl2br(htmlspecialchars($res['nilai2'])); ?>
                                                                            </span>
                                                                        </div>
                                                                        <?php } ?>

                                                                        <?php if (!empty($res['nilai3'])) { ?>
                                                                        <div class="mb-2 p-2 rounded <?= ($display_nilai >= 3.0 && $display_nilai < 4.0) ? 'bg-info-subtle border border-info' : 'bg-light' ?>">
                                                                            <span class="badge bg-warning me-2">Nilai 3</span>
                                                                            <span class="fw-semibold text-dark">
                                                                                <?= nl2br(htmlspecialchars($res['nilai3'])); ?>
                                                                            </span>
                                                                        </div>
                                                                        <?php } ?>

                                                                        <?php if (!empty($res['nilai4'])) { ?>
                                                                        <div class="mb-2 p-2 rounded <?= ($display_nilai >= 4.0) ? 'bg-success-subtle border border-success' : 'bg-light' ?>">
                                                                            <span class="badge bg-success me-2">Nilai 4</span>
                                                                            <span class="fw-semibold text-dark">
                                                                                <?= nl2br(htmlspecialchars($res['nilai4'])); ?>
                                                                            </span>
                                                                        </div>
                                                                        <?php } ?>
                                                                    </div>
                                                                    <?php } ?>

                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                <?php 
                                                    $nodd++;
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php 
                        $no++;
                    }
                } else { ?>
                    <div class="alert alert-info text-center py-4 border">
                        <i class="bi bi-info-circle fs-3 text-muted d-block mb-2"></i>
                        Tidak ada data Skill Standard untuk kategori <strong><?= ucfirst($active_tab_ss) ?></strong> pada arsip periode ini.
                    </div>
                <?php } ?>

            </div>
        </div>

        <?php include("pages/part/p_footer.php"); ?>
    </div>
</body>
</html>
