<?php
// File: pages/adminedp/p_mainProfile.php
// Ambil daftar semua driver distribusi
$sql_drivers = "SELECT u.id, u.nama_lngkp, u.nik, u.jabatan, u.bagian, u.departement, u.atasan, u.penilai
                FROM tb_users u
                INNER JOIN tb_auth a ON u.id = a.id_user
                WHERE u.bagian = 'Driver Distribusi'
                ORDER BY u.nama_lngkp ASC";
$result_drivers = mysqli_query($conn, $sql_drivers);
?>

<div class="col-lg-4 connectedSortable">
    <!-- Card Dropdown Pilih Driver -->
    <div class="card mb-4 shadow-sm border-primary" style="border-width: 2px;">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
            <h5 style="color:white;" class="card-title fw-bolder">
                <i class="bi bi-person-circle me-2"></i>Pilih Driver
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" action="home-adminedp" id="formPilihDriver">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-primary text-white fw-bold">
                        <i class="bi bi-search"></i>
                    </span>
                    <select class="form-select form-select-lg" name="driver_id" id="selectDriver" onchange="this.form.submit()" required>
                        <option value="">-- Pilih Driver Distribusi --</option>
                        <?php while ($driver = mysqli_fetch_assoc($result_drivers)) { ?>
                            <option value="<?= $driver['id'] ?>" <?= ($driver['id'] == $selected_driver_id) ? 'selected' : '' ?>>
                                <?= $driver['nama_lngkp'] ?> (NIK: <?= $driver['nik'] ?>)
                            </option>
                        <?php } ?>
                    </select>
                </div>
            </form>
            
            <?php if ($selected_driver_id > 0) { ?>
                <div class="alert alert-success">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    <strong>Driver Terpilih!</strong> Data KPI di bawah adalah milik driver yang dipilih.
                </div>
            <?php } else { ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Silakan pilih driver untuk mengelola KPI.
                </div>
            <?php } ?>
        </div>
    </div>
    
    <?php if ($driver_data) { ?>
    <!-- Card Profil Karyawan (Driver) -->
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-danger">
            <h5 style="color:white;" class="card-title fw-bolder">
                <i class="bi bi-person-badge me-2"></i>Profil Driver
            </h5>
            <div class="card-tools">
                <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="nama-addon">
                    <i class="bi bi-person-fill me-2"></i>Nama :
                </span>
                <input disabled type="text" value="<?php echo $driver_data['nama_lngkp']; ?>"
                    class="form-control" placeholder="Nama" aria-label="Nama" aria-describedby="nama-addon">
            </div>
            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="nik-addon">
                    <i class="bi bi-card-text me-2"></i>NIK :
                </span>
                <input disabled type="text" value="<?php echo $driver_data['nik']; ?>"
                    class="form-control" placeholder="NIK" aria-label="NIK" aria-describedby="nik-addon">
            </div>
            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="jabatan-addon">
                    <i class="bi bi-briefcase-fill me-2"></i>Jabatan :
                </span>
                <input disabled type="text" value="<?php echo $driver_data['jabatan'] . " - " . $driver_data['bagian']; ?>" 
                    class="form-control" placeholder="Jabatan" aria-label="Jabatan" aria-describedby="jabatan-addon">
            </div>
            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="depart-addon">
                    <i class="bi bi-building me-2"></i>Departement :
                </span>
                <input disabled type="text" value="<?php echo $driver_data['departement']; ?>"
                    class="form-control" placeholder="Departement" aria-label="Departement" aria-describedby="depart-addon">
            </div>
            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="atasan-addon">
                    <i class="bi bi-person-check-fill me-2"></i>Atasan :
                </span>
                <input disabled type="text" value="<?php echo $driver_data['atasan']; ?>"
                    class="form-control" placeholder="Koordinator" aria-label="Koordinator" aria-describedby="atasan-addon">
            </div>
            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="penilai-addon">
                    <i class="bi bi-clipboard-check-fill me-2"></i>Penilai :
                </span>
                <input disabled type="text" value="<?php echo $driver_data['penilai']; ?>"
                    class="form-control" placeholder="Penilai" aria-label="Penilai" aria-describedby="penilai-addon">
            </div>
        </div>
    </div>
    
    <!-- Card Total Nilai KPI dengan Info SP -->
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-warning bg-gradient">
            <h5 style="color:black;" class="card-title fw-bolder">
                <i class="bi bi-graph-up me-2"></i>TOTAL NILAI KPI
            </h5>
        </div>
        <div class="card-body">
            <?php 
            $kpi_result = getnilaiWithSPDisplay($conn, $id_user);
            $nilai_asli = $kpi_result['nilai_asli'];
            $nilai_akhir = $kpi_result['nilai_akhir'];
            $sp_data = $kpi_result['sp_data'];
            $pengurangan = $kpi_result['pengurangan'];
            
            // Tentukan warna dan rating
            if ($nilai_akhir < 90) {
                $wrabs = "red";
                $rating = "POOR";
            } elseif ($nilai_akhir <= 100) {
                $wrabs = "orange";
                $rating = "GOOD";
            } elseif ($nilai_akhir <= 110) {
                $wrabs = "green";
                $rating = "Very Good";
            } else {
                $wrabs = "blue";
                $rating = "Excellent";
            }
            ?>
            
            <!-- Alert SP jika ada -->
            <?php if ($sp_data) { ?>
            <div class="alert alert-<?=getSPBadgeClass($sp_data['jenis_sp'])?> alert-dismissible fade show" role="alert">
                <h6 class="alert-heading">
                    <i class="bi bi-exclamation-triangle-fill"></i> 
                    <strong>Surat Peringatan Aktif: <?=$sp_data['jenis_sp']?></strong>
                </h6>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <small>
                            <strong>Nomor:</strong> <?=$sp_data['nomor_sp']?><br>
                            <strong>Tanggal:</strong> <?=formatTanggalIndo($sp_data['tanggal_sp'])?>
                        </small>
                    </div>
                    <div class="col-md-6">
                        <small>
                            <strong>Masa Berlaku:</strong><br>
                            <?=formatTanggalIndo($sp_data['masa_berlaku_mulai'])?> s/d 
                            <?=formatTanggalIndo($sp_data['masa_berlaku_selesai'])?>
                        </small>
                    </div>
                </div>
                <hr>
                <small>
                    <strong>Alasan:</strong> <?=$sp_data['alasan']?>
                </small>
                <hr class="my-2">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="mb-0">
                        <i class="bi bi-info-circle"></i> 
                        Nilai KPI dikurangi <strong><?=$pengurangan?> poin</strong> selama masa berlaku SP
                    </small>
                    <span class="badge bg-danger">-<?=$pengurangan?> poin</span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php } ?>
            
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="2" class="table-secondary">
                            <center>WHAT + HOW</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sp_data) { ?>
                    <tr>
                        <th>
                            <center>NILAI ASLI (Sebelum SP)</center>
                        </th>
                        <td>
                            <center><del><?= number_format($nilai_asli, 2); ?></del></center>
                        </td>
                    </tr>
                    <tr class="table-warning">
                        <th>
                            <center>PENGURANGAN SP (<?=$sp_data['jenis_sp']?>)</center>
                        </th>
                        <td class="text-danger">
                            <center><strong>- <?= $pengurangan ?></strong></center>
                        </td>
                    </tr>
                    <?php } ?>
                    <tr class="table-primary">
                        <th>
                            <center>NILAI KPI AKHIR</center>
                        </th>
                        <th>
                            <center><?= number_format($nilai_akhir, 2); ?></center>
                        </th>
                    </tr>
                    <tr>
                        <td colspan="2" style="font-size: 25pt; color:<?= $wrabs ?>" class="fw-bolder">
                            <center><?= $rating ?></center>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php if ($sp_data) { ?>
            <div class="alert alert-info mb-0 mt-3">
                <small>
                    <i class="bi bi-calendar-check"></i> 
                    SP ini akan berakhir pada <strong><?=formatTanggalIndo($sp_data['masa_berlaku_selesai'])?></strong>
                </small>
            </div>
            <?php } ?>
        </div>
    </div>
    <?php } else { ?>
    <!-- Placeholder jika belum ada driver dipilih -->
    <div class="card mb-4">
        <div class="card-body text-center py-5">
            <i class="bi bi-person-x text-muted" style="font-size: 5rem;"></i>
            <h5 class="mt-3 text-muted">Belum Ada Driver Dipilih</h5>
            <p class="text-muted">Silakan pilih driver dari dropdown di atas untuk mengelola KPI.</p>
        </div>
    </div>
    <?php } ?>
</div>

<style>
#selectDriver {
    font-weight: bold;
    cursor: pointer;
}

#selectDriver:hover {
    background-color: #f8f9fa;
}

.form-select-lg {
    font-size: 1rem;
    padding: 0.75rem 1rem;
}
</style>

<script>
// Auto-submit saat dropdown berubah dengan smooth transition
document.getElementById('selectDriver').addEventListener('change', function() {
    // Tampilkan loading indicator
    const form = document.getElementById('formPilihDriver');
    const submitBtn = document.createElement('div');
    submitBtn.className = 'text-center mt-2';
    submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm text-primary" role="status"><span class="visually-hidden">Loading...</span></div> <small>Memuat data driver...</small>';
    form.appendChild(submitBtn);
});
</script>