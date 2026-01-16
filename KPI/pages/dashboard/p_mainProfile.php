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
                <span style="color : #343A40;" class="input-group-text fw-bold" id="penilai-addon">Penilai Tambahan:</span>
                <input disabled type="text" value="<?php echo $penilai; ?>" class="form-control" placeholder="Penilai"
                    aria-label="Penilai" aria-describedby="penilai-addon">
            </div>

        </div>
    </div>
    <div class="card mb-4">
        <div style="height: 50px; margin-top: -3px;" class="card-header bg-warning bg-gradient">
            <h5 style="color:black;" class="card-title fw-bolder">
                TOTAL NILAI KPI
                <?php if ($verified_status) { ?>
                    <span class="badge bg-success ms-2">
                        <i class="bi bi-check-circle-fill"></i> Verified
                    </span>
                <?php } ?>
            </h5>
            <?php if ($leveel == 2 || $leveel == 4 || $leveel == 5) { ?>
            <div class="card-tools">
                <button style="color: black; margin-top: -20px; margin-right: 5px;" type="button"
                    data-bs-toggle="dropdown" class="btn btn-tool dropdown-toggle">
                    <i class="bi bi-shield-check fs-6"></i>
                </button>
                <div class="dropdown-menu dropdown-menu-end" role="menu">
                    <?php if ($verified_status) { ?>
                        <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal"
                            data-bs-target="#unverifyModal">
                            <i class="bi bi-x-circle"></i> Batalkan Verifikasi
                        </a>
                    <?php } else { ?>
                        <a href="#" class="dropdown-item text-success" data-bs-toggle="modal"
                            data-bs-target="#verifyModal">
                            <i class="bi bi-check-circle"></i> Verifikasi KPI
                        </a>
                    <?php } ?>
                </div>
            </div>
            <?php } ?>
            <div class="card-tools">
            <button type="button"
                data-bs-toggle="dropdown"
                class="btn btn-tool dropdown-toggle"
                style="color: white; margin-top: -10px; margin-right: 5px;">
                <i class="bi bi-archive-fill fs-5"></i>
            </button>

            <div class="dropdown-menu dropdown-menu-end" role="menu">
                <?php if ($sudah_archive) { ?>
                    <a href="#" class="dropdown-item disabled text-muted">
                        <i class="bi bi-check-circle"></i> Sudah Di-Archive - <?= tmapil($busd[0]-1,$busd[1]); ?>
                    </a>
                <?php } else { ?>
                    <a href="#" class="dropdown-item" data-bs-toggle="modal"
                        data-bs-target="#archiveModal">
                        <i class="bi bi-archive"></i> Archive - <?= tmapil($busd[0]-1,$busd[1]); ?>
                    </a>
                <?php } ?>
            </div>
            </div>
        </div>
        <!-- ---------------------------------------------------------------------->
        <div class="card-body">
            <?php
            $kpi_result  = getnilaiWithSPDisplay($conn, $user_id);

            $nilai_asli  = $kpi_result['nilai_asli'];
            $nilai_akhir = $kpi_result['nilai_akhir'];
            $sp_data     = $kpi_result['sp_data'];
            $pengurangan = $kpi_result['pengurangan'];

            if ($nilai_akhir < 90) {
                $wrabs  = 'red';
                $rating = 'POOR';
            } elseif ($nilai_akhir <= 100) {
                $wrabs  = 'orange';
                $rating = 'GOOD';
            } elseif ($nilai_akhir <= 110) {
                $wrabs  = 'green';
                $rating = 'VERY GOOD';
            } else {
                $wrabs  = 'blue';
                $rating = 'EXCELLENT';
            }
            ?>
            <table class="table table-bordered table-sm mb-2">
                <thead>
                    <tr>
                        <th>
                            <center>KPI BULAN : </center>
                        </th>
                        <th>
                            <center><?= tmapil($busd[0],$busd[1]); ?></center>
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
                    <?php if ($sp_data) { ?>
                        <tr>
                            <th>
                                <center>NILAI ASLI (Sebelum SP)</center>
                            </th>
                            <td>
                                <center>
                                    <del><?= number_format($nilai_asli, 2); ?></del>
                                </center>
                            </td>
                        </tr>

                        <tr class="table-warning">
                            <th>
                                <center>PENGURANGAN SP (<?= $sp_data['jenis_sp']; ?>)</center>
                            </th>
                            <td class="text-danger">
                                <center>
                                    <strong>- <?= $pengurangan; ?></strong>
                                </center>
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
                        <td colspan="2"
                            class="fw-bolder"
                            style="font-size: 25pt; color: <?= $wrabs; ?>;">
                            <center><?= $rating; ?></center>
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

    <?php if ($verified_status) { 
        $verifier_name = getVerifierName($conn, $verified_status['verified_by']);
    ?>
        <div class="alert alert-success mb-0 mt-3">
            <div class="d-flex align-items-center">
                <i class="bi bi-check-circle-fill fs-4 me-2"></i>
                <div>
                    <strong>KPI Sudah Diverifikasi</strong><br>
                    <small>
                        Oleh: <strong><?= $verifier_name ?></strong><br>
                        <i class="bi bi-calendar-check"></i> 
                        Pada: <?= date('d/m/Y H:i', strtotime($verified_status['verified_at'])) ?>
                        <?php if (!empty($verified_status['keterangan'])) { ?>
                            <br><i class="bi bi-chat-text"></i> Catatan: <?= htmlspecialchars($verified_status['keterangan']) ?>
                        <?php } ?>
                    </small>
                </div>
            </div>
        </div>
    <?php } ?>

    <!-- Modal Verify KPI -->
    <div class="modal fade" id="verifyModal" tabindex="-1" aria-labelledby="verifyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="verifyModalLabel">
                        <i class="bi bi-check-circle"></i> Verifikasi KPI
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> 
                            Anda akan memverifikasi KPI <strong><?= $nama_lngkpan ?></strong> untuk periode <strong><?= date('F Y') ?></strong>
                        </div>
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3" 
                                placeholder="Tambahkan catatan verifikasi..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="verifyKPI" class="btn btn-success">
                            <i class="bi bi-check-circle"></i> Verifikasi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Unverify KPI -->
    <div class="modal fade" id="unverifyModal" tabindex="-1" aria-labelledby="unverifyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title fw-bold" id="unverifyModalLabel">
                        <i class="bi bi-x-circle"></i> Batalkan Verifikasi
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> 
                            Apakah Anda yakin ingin membatalkan verifikasi KPI ini?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tidak</button>
                        <button type="submit" name="unverifyKPI" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Ya, Batalkan
                        </button>
                    </div>
                </form>
            </div>
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