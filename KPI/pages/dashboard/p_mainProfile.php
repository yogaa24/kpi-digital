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
            <div class="card-tools">
                <button type="button"
                    data-bs-toggle="dropdown"
                    class="btn btn-tool dropdown-toggle"
                    style="color: white; margin-top: -10px; margin-right: 5px;">
                    <i class="bi bi-archive-fill fs-5"></i>
                </button>

                <div class="dropdown-menu dropdown-menu-end" role="menu">
                    <?php if ($sudah_archive) { ?>
                        <!-- Jika sudah di-archive -->
                        <a href="#" class="dropdown-item disabled text-muted">
                            <i class="bi bi-check-circle"></i> Sudah Di-Archive - <?= tmapil($busd[0]-1,$busd[1]); ?>
                        </a>
                    <?php } elseif (!$verified_status) { ?>
                        <!-- ===== TAMBAHAN BARU: Jika belum diverifikasi ===== -->
                        <a href="#" class="dropdown-item disabled text-muted" 
                        title="KPI harus diverifikasi atasan terlebih dahulu">
                            <i class="bi bi-lock-fill"></i> Archive Terkunci - <?= tmapil($busd[0]-1,$busd[1]); ?>
                            <br><small class="text-danger">* Menunggu verifikasi atasan</small>
                        </a>
                    <?php } else { ?>
                        <!-- Jika sudah diverifikasi dan belum di-archive -->
                        <a href="#" class="dropdown-item" data-bs-toggle="modal"
                            data-bs-target="#archiveModal">
                            <i class="bi bi-archive"></i> Archive - <?= tmapil($busd[0]-1,$busd[1]); ?>
                        </a>
                    <?php } ?>
                </div>
                <!-- ===== TOMBOL SIMULASI BARU ===== -->
                <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#simulateModal"
                    class="btn btn-tool"
                    title="Salin ke Simulasi"
                    style="color: white; margin-top: -10px; margin-right: 5px;">
                    <i class="bi bi-arrow-right-circle-fill fs-5"></i>
                </button>
                <button type="button"
                    data-bs-toggle="modal"
                    data-bs-target="#feedbackModal"
                    class="btn btn-tool"
                    title="Feedback KPI"
                    style="color: white; margin-top: -10px; margin-right: 5px;">
                    <i class="bi bi-chat-left-text-fill fs-5"></i>
                </button>

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
                $wrabs  = 'blue';
                $rating = 'VERY GOOD';
            } else {
                $wrabs  = 'green';
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
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <small>
                                <i class="bi bi-calendar-check"></i> 
                                SP ini akan berakhir pada <strong><?=formatTanggalIndo($sp_data['masa_berlaku_selesai'])?></strong>
                            </small>
                        </div>
                        <?php if ($sp_data['file_sp']) { ?>
                        <button type="button" 
                                class="btn btn-sm btn-danger" 
                                data-bs-toggle="modal" 
                                data-bs-target="#modalLihatSP">
                            <i class="bi bi-file-earmark-text"></i> Lihat
                        </button>
                        <?php } ?>
                    </div>
                </div>

                <!-- Modal Lihat Surat SP untuk Karyawan -->
                <div class="modal fade" id="modalLihatSP" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title fw-bold">
                                    <i class="bi bi-file-earmark-text-fill"></i> 
                                    Surat Peringatan - <?=$sp_data['jenis_sp']?>
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <!-- Info SP -->
                                <div class="alert alert-<?=getSPBadgeClass($sp_data['jenis_sp'])?> mb-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong><i class="bi bi-file-text"></i> Nomor SP:</strong> <?=$sp_data['nomor_sp']?><br>
                                            <strong><i class="bi bi-calendar-event"></i> Tanggal:</strong> <?=formatTanggalIndo($sp_data['tanggal_sp'])?><br>
                                        </div>
                                        <div class="col-md-6">
                                            <strong><i class="bi bi-calendar-range"></i> Masa Berlaku:</strong><br>
                                            <?=formatTanggalIndo($sp_data['masa_berlaku_mulai'])?> s/d <?=formatTanggalIndo($sp_data['masa_berlaku_selesai'])?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Alasan -->
                                <div class="mb-3">
                                    <strong><i class="bi bi-chat-left-text"></i> Alasan/Pelanggaran:</strong>
                                    <div class="alert alert-light mt-2">
                                        <?=nl2br(htmlspecialchars($sp_data['alasan']))?>
                                    </div>
                                </div>

                                <?php if ($sp_data['keterangan']) { ?>
                                <div class="mb-3">
                                    <strong><i class="bi bi-chat-dots"></i> Keterangan:</strong>
                                    <div class="alert alert-light mt-2">
                                        <?=nl2br(htmlspecialchars($sp_data['keterangan']))?>
                                    </div>
                                </div>
                                <?php } ?>

                                <!-- Preview File -->
                                <div class="text-center">
                                    <?php 
                                    $file_path = 'uploads/surat_peringatan/' . $sp_data['file_sp'];
                                    $file_ext = strtolower(pathinfo($sp_data['file_sp'], PATHINFO_EXTENSION));
                                    
                                    if ($file_ext == 'pdf') { ?>
                                        <iframe src="<?=$file_path?>" 
                                                width="100%" 
                                                height="600px" 
                                                style="border: 1px solid #ddd; border-radius: 5px;">
                                        </iframe>
                                    <?php } else { ?>
                                        <img src="<?=$file_path?>" 
                                            class="img-fluid" 
                                            style="max-height: 600px; border: 1px solid #ddd; border-radius: 5px;"
                                            alt="Surat Peringatan">
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <a href="<?=$file_path?>" 
                                class="btn btn-primary" 
                                download="<?=$sp_data['nomor_sp']?>.<?=$file_ext?>">
                                    <i class="bi bi-download"></i> Download Surat SP
                                </a>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                    <i class="bi bi-x-circle"></i> Tutup
                                </button>
                            </div>
                        </div>
                    </div>
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

    <!-- ===== MODAL SIMULASI ===== -->
    <div class="modal fade" id="simulateModal" tabindex="-1" aria-labelledby="simulateModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="simulateModalLabel">
                        <i class="bi bi-arrow-right-circle-fill"></i> Simulasikan KPI
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <div class="text-center">
                            <div class="mb-4">
                                <i class="bi bi-arrow-right-circle-fill text-primary" style="font-size: 4rem;"></i>
                            </div>
                            <h4 class="fw-bold text-primary mb-3">
                                Salin Data KPI Real ke Simulasi?
                            </h4>
                            <p class="text-muted">
                                Semua data KPI Real Anda akan disalin ke halaman KPI Simulasi untuk keperluan perencanaan dan proyeksi
                            </p>
                        </div>
                        
                        <div class="alert alert-info mt-3">
                            <strong><i class="bi bi-info-circle"></i> Yang Akan Disalin:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Semua Poin KPI (WHAT & HOW)</li>
                                <li>Semua Indikator (A & B)</li>
                                <li>Bobot KPI</li>
                                <li>Nilai dan Target saat ini</li>
                            </ul>
                        </div>

                        <div class="alert alert-warning mt-3">
                            <strong><i class="bi bi-exclamation-triangle"></i> Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li><strong>Data simulasi lama akan dihapus</strong> dan diganti dengan data real yang baru</li>
                                <li>Anda dapat mengubah data di halaman simulasi setelah proses ini</li>
                                <li>Data KPI Real tidak akan berubah</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" name="simulateKPI" class="btn btn-primary">
                            <i class="bi bi-arrow-right-circle-fill"></i> Ya, Simulasikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="archiveModal" tabindex="-1" aria-labelledby="archiveModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold" id="archiveModalLabel">
                        <i class="bi bi-archive-fill"></i> Simpan KPI Bulan <?= tmapil($busd[0]-1,$busd[1]); ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="">
                    <div class="modal-body">
                        <!-- ===== TAMBAHAN: Tampilkan info verifikasi ===== -->
                        <?php if ($verified_status) { 
                            $verifier_name = getVerifierName($conn, $verified_status['verified_by']);
                        ?>
                            <div class="alert alert-success mb-3">
                                <i class="bi bi-check-circle-fill"></i> 
                                <strong>KPI Sudah Diverifikasi</strong><br>
                                <small>
                                    Oleh: <strong><?= $verifier_name ?></strong><br>
                                    Pada: <?= date('d/m/Y H:i', strtotime($verified_status['verified_at'])) ?>
                                </small>
                            </div>
                        <?php } ?>
                        <!-- ===== AKHIR TAMBAHAN ===== -->
                        
                        <div class="text-center">
                            <h4 class="fw-bold text-danger mb-3">
                                <i class="bi bi-exclamation-triangle-fill"></i> 
                                Apakah Kamu Yakin Menyimpan KPI?
                            </h4>
                            <p class="text-muted">
                                Pastikan tidak ada yang salah karena data yang sudah tersimpan tidak bisa diubah
                            </p>
                        </div>
                        
                        <div class="alert alert-warning mt-3">
                            <strong><i class="bi bi-info-circle"></i> Perhatian:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Data KPI akan disimpan secara permanen</li>
                                <li>Tidak dapat diubah setelah di-archive</li>
                                <li>Proses ini hanya bisa dilakukan sekali</li>
                            </ul>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <button type="submit" name="archiveNow" class="btn btn-primary">
                            <i class="bi bi-archive-fill"></i> Ya, Simpan KPI
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php 
    $bulan_sekarang = date('m/Y');
    $feedback_data = getFeedback($conn, $id_user, $bulan_sekarang);

    // Cek apakah user yang login adalah atasan dari user ini
    $is_atasan = false;
    $user_info = mysqli_query($conn, "SELECT atasan FROM tb_users WHERE id = $id_user");
    $user_row = mysqli_fetch_assoc($user_info);
    if ($user_row['atasan'] == $nama_lngkp) {
        $is_atasan = true;
    }
    ?>

    <!-- Modal Feedback -->
    <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title fw-bold" id="feedbackModalLabel">
                        <i class="bi bi-chat-left-text-fill"></i> Feedback KPI - <?= tmapil($busd[0], $busd[1]); ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Tab Navigation -->
                    <ul class="nav nav-tabs" id="feedbackTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="view-tab" data-bs-toggle="tab" data-bs-target="#view-feedback" type="button" role="tab">
                                <i class="bi bi-eye"></i> Lihat Feedback
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="write-tab" data-bs-toggle="tab" data-bs-target="#write-feedback" type="button" role="tab">
                                <i class="bi bi-pencil"></i> Tulis Feedback
                            </button>
                        </li>
                    </ul>

                    <!-- Tab Content -->
                    <div class="tab-content mt-3" id="feedbackTabContent">
                        <!-- Tab Lihat Feedback -->
                        <div class="tab-pane fade show active" id="view-feedback" role="tabpanel">
                            <!-- Feedback dari Diri Sendiri -->
                            <div class="card mb-3">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-person-fill"></i> Feedback dari Diri Sendiri
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($feedback_data['self']) { ?>
                                        <div class="feedback-content">
                                            <p><?= nl2br(htmlspecialchars($feedback_data['self']['feedback'])) ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> 
                                                Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($feedback_data['self']['tanggal_update'] ?? $feedback_data['self']['tanggal_buat'])) ?>
                                            </small>
                                        </div>
                                    <?php } else { ?>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-info-circle"></i> Belum ada feedback dari diri sendiri
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>

                            <!-- Feedback dari Atasan -->
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0">
                                        <i class="bi bi-person-badge"></i> Feedback dari Atasan
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <?php if ($feedback_data['atasan']) { ?>
                                        <div class="feedback-content">
                                            <div class="d-flex align-items-center mb-2">
                                                <strong><?= $feedback_data['atasan']['nama_pemberi'] ?></strong>
                                            </div>
                                            <p><?= nl2br(htmlspecialchars($feedback_data['atasan']['feedback'])) ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-clock"></i> 
                                                Terakhir diupdate: <?= date('d/m/Y H:i', strtotime($feedback_data['atasan']['tanggal_update'] ?? $feedback_data['atasan']['tanggal_buat'])) ?>
                                            </small>
                                        </div>
                                    <?php } else { ?>
                                        <p class="text-muted mb-0">
                                            <i class="bi bi-info-circle"></i> Belum ada feedback dari atasan
                                        </p>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>

                        <!-- Tab Tulis Feedback -->
                        <div class="tab-pane fade" id="write-feedback" role="tabpanel">
                            <form method="POST" action="">
                                <input type="hidden" name="user_target" value="<?= $id_user ?>">
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> 
                                    <strong>Panduan:</strong>
                                    <ul class="mb-0 mt-2">
                                        <li>Tuliskan refleksi diri atau masukan untuk peningkatan performa</li>
                                        <li>Feedback dapat diperbarui sebelum bulan berganti</li>
                                        <li>Bersikaplah objektif dan konstruktif</li>
                                    </ul>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label fw-bold">
                                        <?php if ($is_atasan) { ?>
                                            <i class="bi bi-person-badge"></i> Feedback untuk <?= $nama_lngkp ?>
                                        <?php } else { ?>
                                            <i class="bi bi-person-fill"></i> Feedback untuk Diri Sendiri
                                        <?php } ?>
                                    </label>
                                    <textarea class="form-control" name="feedback_text" rows="8" 
                                        placeholder="Tuliskan feedback Anda di sini..." required><?php 
                                        if ($is_atasan && $feedback_data['atasan']) {
                                            echo htmlspecialchars($feedback_data['atasan']['feedback']);
                                        } elseif (!$is_atasan && $feedback_data['self']) {
                                            echo htmlspecialchars($feedback_data['self']['feedback']);
                                        }
                                    ?></textarea>
                                </div>

                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar"></i> Periode: <?= tmapil($busd[0], $busd[1]); ?>
                                    </small>
                                    <button type="submit" name="saveFeedback" class="btn btn-primary">
                                        <i class="bi bi-save"></i> Simpan Feedback
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>