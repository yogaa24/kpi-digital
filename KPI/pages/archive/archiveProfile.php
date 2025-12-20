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
                        <th>
                            <center>KPI BULAN : </center>
                        </th>
                        <th>
                            <center><?= $bulannnn; ?></center>
                        </th>
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
                            $wrabs = "green";
                        } else { // $nilai > 110
                            $wrabs = "blue";
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
</div>