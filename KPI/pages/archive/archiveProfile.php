<!-- archiveprofile.php -->
<div class="col-lg-4 connectedSortable">
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-danger">
            <h5 style="color:white;" class="card-title fw-bolder">Profil Karyawan</h5>
            <div class="card-tools">
                <!-- <button style="color: white;" type="button" class="btn btn-tool">
                    <i class="bi bi-pencil"></i>
                </button> -->
                <button style="color: white;" type="button" class="btn btn-tool" data-lte-toggle="card-collapse">
                    <i data-lte-icon="expand" class="bi bi-plus-lg"></i>
                    <i data-lte-icon="collapse" class="bi bi-dash-lg"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="nama-addon">Nama : </span>
                <input disabled type="text" value="<?php echo $nama_lngkp; ?>" class="form-control" placeholder="Nama"
                    aria-label="Nama" aria-describedby="nama-addon">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="jabatan-addon">Jabatan :</span>
                <input disabled type="text" value="<?php if($leveel==4){echo "Kepala Departemen";}else{ echo $jabatan . " - " . $bagian;} ?>" class="form-control"
                    placeholder="Jabatan" aria-label="Jabatan" aria-describedby="jabatan-addon">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="depart-addon">Departement :</span>
                <input disabled type="text" value="<?php echo $departement; ?>" class="form-control"
                    placeholder="Departement" aria-label="Departement" aria-describedby="depart-addon">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="atasan-addon">Atasan :</span>
                <input disabled type="text" value="<?php echo $atasan; ?>" class="form-control"
                    placeholder="Koordinator" aria-label="Koordinator" aria-describedby="atasan-addon">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="penilai-addon">Penilai :</span>
                <input disabled type="text" value="<?php echo $penilai; ?>" class="form-control" placeholder="Penilai"
                    aria-label="Penilai" aria-describedby="penilai-addon">
            </div>

        </div>
    </div>
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-warning bg-gradient">
            <h5 style="color:black;" class="card-title fw-bolder">TOTAL NILAI KPI</h5>
        </div>
        <!-- ---------------------------------------------------------------------->
        <div class="card-body">
            <table class="table table-bordered table-sm mb-2">
                <thead>
                    <tr>
                        <th><center>KPI BULAN : </center></th>
                        <th><center><?= $bulannnn; ?></center></th>
                    </tr>
                </thead>
            </table>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="2" class="table-secondary">
                            <center>WHAT + HOW</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($sp_archive_data) { ?>
                        <tr>
                            <th><center>NILAI ASLI (Sebelum SP)</center></th>
                            <td><center><del><?= number_format($nilai_asli, 2); ?></del></center></td>
                        </tr>
                        <tr class="table-warning">
                            <th><center>PENGURANGAN SP (<?= $sp_archive_data['jenis_sp']; ?>)</center></th>
                            <td class="text-danger">
                                <center><strong>- <?= $pengurangan; ?></strong></center>
                            </td>
                        </tr>
                    <?php } ?>
                    <tr class="table-primary">
                        <th><center>NILAI KPI AKHIR</center></th>
                        <th><center><?= number_format($nilai_akhir, 2) ?></center></th>
                    </tr>
                    <tr>
                        <?php
                        $wrabs = 'red';
                        $rating = 'POOR';
                        if ($nilai_akhir < 90) {
                            $wrabs = "red";
                            $rating = "POOR";
                        } elseif ($nilai_akhir <= 100) {
                            $wrabs = "orange";
                            $rating = "GOOD";
                        } elseif ($nilai_akhir <= 110) {
                            $wrabs = "green";
                            $rating = "VERY GOOD";
                        } else {
                            $wrabs = "blue";
                            $rating = "EXCELLENT";
                        }
                        ?>
                        <td colspan="2" style="font-size: 25pt; color:<?= $wrabs ?>" class="fw-bolder">
                            <center><?= $rating; ?></center>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <?php if ($sp_archive_data) { ?>
                <div class="alert alert-info mb-0 mt-3">
                    <small>
                        <i class="bi bi-info-circle"></i> 
                        <strong>Catatan SP Archive:</strong><br>
                        Nomor SP: <?= $sp_archive_data['nomor_sp'] ?><br>
                        Tanggal: <?= formatTanggalIndo($sp_archive_data['tanggal_sp']) ?><br>
                        Masa Berlaku: <?= formatTanggalIndo($sp_archive_data['masa_berlaku_mulai']) ?> s/d 
                        <?= formatTanggalIndo($sp_archive_data['masa_berlaku_selesai']) ?>
                    </small>
                </div>
            <?php } ?>
        </div>
    </div>
</div>