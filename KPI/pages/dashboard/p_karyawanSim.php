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
                <input disabled type="text" value="<?= $jabatan . ' - ' . $bagian ?>" class="form-control"
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
                <span style="color : #343A40;" class="input-group-text fw-bold" id="penilai-addon">Penilai Tambahan :</span>
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
            <table class="table table-bordered table-sm">
                <thead>
                    <tr>
                        <th colspan="2" class="table-secondary">
                            <center>WHAT + HOW</center>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th>
                            <center>NILAI KPI</center>
                        </th>
                        <th>
                            <center><?= round($zboth + $zbotw,2) ?></center>
                        </th>
                    </tr>
                    <tr>
                        <?php
                        $nilair = $zboth + $zbotw;
                        $wrabs;
                        if ($nilair < 90) {
                            $wrabs = "red";
                        } elseif ($nilair <= 100) {
                            $wrabs = "orange";
                        } elseif ($nilair <= 110) {
                            $wrabs = "blue";
                        } else { // $nilai > 110
                            $wrabs = "green";
                        } ?>
                        <td colspan="2" style="font-size: 25pt; color:<?= $wrabs ?>" class="fw-bolder">
                            <?php
                            function getRating($nilair)
                            {
                                if ($nilair < 90) {
                                    return "POOR";
                                } elseif ($nilair <= 100) {
                                    return "GOOD";
                                } elseif ($nilair <= 110) {
                                    return "VERY GOOD";
                                } else { // $nilai > 110
                                    return "EXCELLENT";
                                }
                            }
                            ?>
                            <center><?php echo getRating($nilair); ?></center>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold" id="archiveModalLabel">Simpan KPI Bulan <?= tmapil($busd[0]-1,$busd[1]); ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="" class="input">
                        <center><h3 class="fw-bold text-danger">Apakah Kamu Yakin Menyimpan KPI?</h3></center>
                       <center><p>Pastikan tidak ada yang salah karena data yang sudah tersimpan tidak bisa dirubah</p></center>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="input" name="archiveNow" class="btn btn-primary">Simpan</button>
                </div>
                </form>
            </div>
        </div>
    </div>

</div>