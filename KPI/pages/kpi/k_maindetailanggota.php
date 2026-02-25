<!-- k_maindetailanggota.php -->
<div class="row">
    <div class="col-lg connectedSortable">
        <div class="d-flex">
            <!-- CARD WHAT -->
            <div class="card mb-4 w-50" style="margin-right:7px;">
                <div style="height: 50px; margin-top: -3px;" class="card-header bg-primary">
                    <h5 style="color:white;" class="card-title"><?= $poin; ?></h5>
                    <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">Bobot :
                        <?= $bobot ?>%
                    </h5>
                    <div class="card-tools">
                        <button style="color: white; margin-top: -20px; margin-right: 5px;" type="button"
                            data-bs-toggle="modal" data-bs-target="#EditModal<?= $idKPI ?>" class="btn btn-tool">
                            <i class="bi bi-pencil fs-6"></i>
                        </button>

                        <button style="color: white; margin-top: -20px; margin-right: 5px; " type="button"
                            data-bs-toggle="dropdown" class="btn btn-tool dropdown-toggle">
                            <i class="bi bi-plus-circle fs-6"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#WhatModal<?= $idKPI ?>">Tambah What </a>
                        </div>
                        <button style="color: white;" type="button" class="btn btn-tool"
                            data-lte-toggle="card-collapse">
                            <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                            <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Whats</th>
                                <th style="width: 30%">Hasil</th>
                                <th style="width: 5%">
                                    <center>Nilai</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Bobot</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Total</center>
                                </th>
                                <th style="width: 9%">
                                    <center>Action</center>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $sql1 = "SELECT * FROM tb_whats WHERE id_user='$id_sf' AND id_kpi='" . $hasil['id'] . "'";
                        $ql = mysqli_query($conn, $sql1);
                        while ($res = mysqli_fetch_assoc($ql)) {
                            $is_edited = $res['is_edited'];
                            $row_class = $is_edited ? 'edited-row' : '';
                        ?>
                            <tr class="align-middle <?= $row_class ?>">
                                <!-- KOLOM WHATS -->
                                <td>
                                    <?= $res['p_what']; ?>
                                    <?php if ($is_edited && !empty($res['original_p_what']) && $res['original_p_what'] != $res['p_what']) { ?>
                                        <span class="edited-badge"><i class="bi bi-pencil-fill"></i> DIUBAH</span>
                                        <div class="change-info">
                                            <strong style="font-size: 9px;">Sebelum:</strong> 
                                            <span class="old-val"><?= htmlspecialchars(substr($res['original_p_what'], 0, 50)) ?><?= strlen($res['original_p_what']) > 50 ? '...' : '' ?></span>
                                            <br>
                                            <strong style="font-size: 9px;">Sesudah:</strong> 
                                            <span class="new-val"><?= htmlspecialchars(substr($res['p_what'], 0, 50)) ?><?= strlen($res['p_what']) > 50 ? '...' : '' ?></span>
                                        </div>
                                    <?php } ?>
                                    
                                    <?php if ($res['tipe_what'] == 'B' && $res['target_omset'] > 0) { ?>
                                        <br><small class="text-muted fw-semibold fs-6" style="font-size: 10px;">
                                             Target: <?=number_format($res['target_omset'], 2)?>
                                        </small>
                                        
                                        <!-- ===== TAMBAHAN BARU: TAMPILKAN PERUBAHAN TARGET OMSET ===== -->
                                        <?php if ($is_edited && isset($res['original_target_omset']) && $res['original_target_omset'] > 0 && $res['original_target_omset'] != $res['target_omset']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info">
                                                <strong style="font-size: 9px;">Target Sebelum:</strong> 
                                                <span class="old-val"><?= number_format($res['original_target_omset'], 2) ?></span>
                                                <br>
                                                <strong style="font-size: 9px;">Target Sesudah:</strong> 
                                                <span class="new-val"><?= number_format($res['target_omset'], 2) ?></span>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                </td>
                                
                                <!-- KOLOM HASIL -->
                                <td>
                                    <?= $res['hasil']; ?>
                                    <?php if ($is_edited && !empty($res['original_hasil']) && $res['original_hasil'] != $res['hasil']) { ?>
                                        <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                        <div class="change-info">
                                            <strong style="font-size: 9px;">Sebelum:</strong> 
                                            <span class="old-val"><?= htmlspecialchars(substr($res['original_hasil'], 0, 30)) ?><?= strlen($res['original_hasil']) > 30 ? '...' : '' ?></span>
                                            <br>
                                            <strong style="font-size: 9px;">Sesudah:</strong> 
                                            <span class="new-val"><?= htmlspecialchars(substr($res['hasil'], 0, 30)) ?><?= strlen($res['hasil']) > 30 ? '...' : '' ?></span>
                                        </div>
                                    <?php } ?>
                                </td>
                                
                                <!-- KOLOM NILAI -->
                                <td>
                                    <center>
                                        <?= $res['nilai']; ?>
                                        <?php if ($is_edited && $res['original_nilai'] != null && $res['original_nilai'] != $res['nilai']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info" style="text-align: left;">
                                                <span class="old-val"><?= $res['original_nilai'] ?></span> 
                                                → 
                                                <span class="new-val"><?= $res['nilai'] ?></span>
                                            </div>
                                        <?php } ?>
                                    </center>
                                </td>
                                
                                <!-- KOLOM BOBOT -->
                                <td>
                                    <center>
                                        <?= $res['bobot']; ?>%
                                        <?php if ($is_edited && $res['original_bobot'] != null && $res['original_bobot'] != $res['bobot']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info" style="text-align: left;">
                                                <span class="old-val"><?= $res['original_bobot'] ?>%</span> 
                                                → 
                                                <span class="new-val"><?= $res['bobot'] ?>%</span>
                                            </div>
                                        <?php } ?>
                                    </center>
                                </td>
                                
                                <!-- KOLOM TOTAL -->
                                <td>
                                    <center>
                                        <?= $res['total']; ?>
                                        <?php if ($is_edited && $res['original_total'] != null && $res['original_total'] != $res['total']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info" style="text-align: left;">
                                                <span class="old-val"><?= $res['original_total'] ?></span> 
                                                → 
                                                <span class="new-val"><?= $res['total'] ?></span>
                                            </div>
                                        <?php } ?>
                                    </center>
                                </td>
                                
                                <!-- KOLOM ACTION -->
                                <td class="text-center">
                                    <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                        <i class="bi bi-eye fs-8"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" role="menu">
                                        <a value="<?php echo $res['id_what']; ?>" name="what_edit" class="dropdown-item"
                                            data-bs-toggle="modal"
                                            data-bs-target="#EditWhatModal<?= $res['id_what'] ?>">Edit</a>
                                        <a class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#HapusWhatModal<?= $res['id_what'] ?>">Hapus</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item fw-bolder" data-bs-toggle="modal"
                                            data-bs-target="#NilaiWhatModal<?= $res['id_what'] ?>">Nilai</a>
                                    </div>
                                    
                                    <?php if ($is_edited && !empty($res['edited_at'])) { ?>
                                        <div class="change-timestamp mt-1">
                                            <i class="bi bi-clock"></i> <?= date('d/m H:i', strtotime($res['edited_at'])) ?>
                                        </div>
                                    <?php } ?>
                                </td>

                                <?php include('pages/kpi/k_modalHapuswhat.php'); ?>
                            </tr>
                            
                            <?php
                            // Ambil data what
                            $tipe_what = $res['tipe_what'];
                            $target_omset = $res['target_omset'];
                            ?>

                            <!-- Modal Nilai What -->
                            <div class="modal fade" id="NilaiWhatModal<?=$res['id_what']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"> 
                                    
                                        <!-- Tambahkan style ini -->
                                        <style>
                                            #NilaiWhatModal<?=$res['id_what']?> select option {
                                                white-space: normal;
                                                word-wrap: break-word;
                                                overflow-wrap: break-word;
                                                max-width: 100%;
                                            }
                                            
                                            #NilaiWhatModal<?=$res['id_what']?> select {
                                                max-width: 100%;
                                            }
                                        </style>
                                        
                                        <div class="modal-header"> 
                                            <h5 class="modal-title fw-bold" id="exampleModalLabel">
                                                Penilaian What <?=$tipe_what?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <input type="hidden" value="<?php echo $res['id_what']; ?>" name="idkpi"> 
                                                
                                                <div class="input-group mb-3">
                                                    <span style="color: #343A40;" class="input-group-text fw-bold" id="tujuan">Tujuan :</span>
                                                    <textarea type="input" class="form-control" name="indikatorwhat" disabled placeholder="" aria-label="Tujuan KPI" aria-describedby="tujuan"><?=$res['p_what']?></textarea>
                                                </div>
                                                
                                                <?php if ($tipe_what == 'A') { ?>
                                                    <!-- WHAT A: Pilih dari indikator -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold">Pilih Nilai Penilaian:</label>
                                                        <select required class="form-select" name="nilaisi" id="nilaisi<?=$res['id_what']?>">
                                                            <option selected disabled>-- Pilih Nilai --</option>
                                                            <?php 
                                                            $id_what = $res['id_what'];
                                                            $sql_indikator = "SELECT * FROM tb_indikator_whats 
                                                                            WHERE id_what = '$id_what' 
                                                                            ORDER BY urutan ASC";
                                                            $result_indikator = mysqli_query($conn, $sql_indikator);
                                                            
                                                            while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                                                // Potong keterangan jika terlalu panjang untuk ditampilkan
                                                                $ket_display = strlen($indikator['keterangan']) > 80 
                                                                            ? substr($indikator['keterangan'], 0, 80) . '...' 
                                                                            : $indikator['keterangan'];
                                                                
                                                                echo '<option value="'.$indikator['id_indikator'].'" title="'.$indikator['keterangan'].'">';
                                                                echo htmlspecialchars($ket_display) . ' = ' . $indikator['nilai'];
                                                                echo '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                        <small class="text-muted">Hover pada pilihan untuk melihat keterangan lengkap</small>
                                                    </div>
                                                <?php } else { ?>
                                                    <!-- WHAT B: Input target omset dan hasil -->
                                                    <div class="input-group mb-3">
                                                        <span style="color: #343A40;" class="input-group-text fw-bold">Target :</span>
                                                        <input type="number" step="0.01" class="form-control" name="target_omset" 
                                                            value="<?=$target_omset?>" required placeholder="Contoh: 1000000">
                                                    </div>
                                                    
                                                    <div class="input-group mb-3">
                                                        <span style="color: #343A40;" class="input-group-text fw-bold">Hasil :</span>
                                                        <input type="number" step="0.01" class="form-control" name="hasil_omset" 
                                                            required placeholder="Hasil yang dicapai">
                                                    </div>
                                                <?php } ?>
                                                
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="nilai_what" class="btn btn-primary">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            
                            <?php
                            // Tampilkan perubahan pada indikator (untuk What A)
                            if ($res['tipe_what'] == 'A') {
                                $sql_indikator = "SELECT * FROM tb_indikator_whats 
                                                WHERE id_what = " . $res['id_what'] . " 
                                                ORDER BY urutan ASC";
                                $result_indikator = mysqli_query($conn, $sql_indikator);
                                
                                $has_edited_indikator = false;
                                while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                    if ($indikator['is_edited']) {
                                        $has_edited_indikator = true;
                                        break;
                                    }
                                }
                                
                                if ($has_edited_indikator) {
                                    mysqli_data_seek($result_indikator, 0); // Reset pointer
                            ?>
                                <!-- <tr class="edited-row">
                                    <td colspan="6">
                                        <div class="alert alert-warning mb-0">
                                            <small>
                                                <strong>
                                                    <i class="bi bi-exclamation-triangle-fill"></i> 
                                                    Indikator yang Diubah
                                                </strong>

                                                <ul class="mb-0 list-unstyled">
                                                    <?php while ($indikator = mysqli_fetch_assoc($result_indikator)) { 
                                                        if ($indikator['is_edited'] && !empty($indikator['original_keterangan'])) {
                                                    ?>
                                                        <li class="d-flex align-items-center gap-2">
                                                            
                                                            <span class="fw-semibold">Indikator</span>

                                                            <span>|</span>

                                                            <span>
                                                                <strong>Sebelum:</strong> 
                                                                <span class="old-value">
                                                                    <?= htmlspecialchars($indikator['original_keterangan']) ?> = <?= $indikator['original_nilai'] ?>
                                                                </span>
                                                            </span>

                                                            <span class="change-arrow">→</span>

                                                            <span>
                                                                <strong>Sesudah:</strong> 
                                                                <span class="new-value">
                                                                    <?= htmlspecialchars($indikator['keterangan']) ?> = <?= $indikator['nilai'] ?>
                                                                </span>
                                                            </span>

                                                            <?php if (!empty($indikator['edited_at'])) { ?>
                                                                <span class="text-muted">
                                                                    <i class="bi bi-clock"></i> 
                                                                    <?= date('d/m/Y H:i', strtotime($indikator['edited_at'])) ?>
                                                                </span>
                                                            <?php } ?>

                                                        </li>

                                                    <?php 
                                                        } elseif ($indikator['is_edited'] && empty($indikator['original_keterangan'])) {
                                                    ?>
                                                        <li class="d-flex align-items-center gap-2">
                                                            <span class="badge bg-success">BARU</span>

                                                            <span>
                                                                <?= htmlspecialchars($indikator['keterangan']) ?> = <?= $indikator['nilai'] ?>
                                                            </span>

                                                            <small class="text-muted">(Ditambahkan oleh atasan)</small>
                                                        </li>
                                                    <?php 
                                                        }
                                                    } ?>
                                                </ul>

                                            </small>
                                        </div>
                                    </td>
                                </tr> -->

                            <?php 
                                }
                            }
                            
                            include('pages/kpi/k_modalEditwhat.php');
                            
                        } ?>
                        </tbody>
                    </table>
                </div> <!-- /.card-body -->
            </div> <!-- /.card -->
            
            <!-- CARD HOW -->
            <div class="card mb-4 w-50" style="margin-left:7px;">
                <div style="height: 50px; margin-top: -3px;" class="card-header bg-success">
                    <h5 style="color:white;" class="card-title"><?= $poin2; ?></h5>
                    <h5 style="color:white; margin-left: 15px;" class="badge text-bg-warning fs-7 fw-bolder">
                        Bobot :
                        <?= $bobot2 ?>%
                    </h5>
                    <div class="card-tools">
                        <button style="color: white; margin-top: -20px; margin-right: 5px;" type="button"
                            data-bs-toggle="modal" data-bs-target="#EditModal2<?= $idKPI ?>" class="btn btn-tool">
                            <i class="bi bi-pencil fs-6"></i>
                        </button>
                        <button style="color: white; margin-top: -20px; margin-right: 5px; " type="button"
                            data-bs-toggle="dropdown" class="btn btn-tool dropdown-toggle">
                            <i class="bi bi-plus-circle fs-6"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-end" role="menu">
                            <a href="#" class="dropdown-item" data-bs-toggle="modal"
                                data-bs-target="#HowModal<?= $idKPI ?>">Tambah
                                How </a>
                        </div>
                        <button style="color: white;" type="button" class="btn btn-tool"
                            data-lte-toggle="card-collapse">
                            <i data-lte-icon="expand" class="bi bi-caret-down-fill"></i>
                            <i data-lte-icon="collapse" class="bi bi-caret-up-fill"></i>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Hows</th>
                                <th style="width: 30%">Hasil</th>
                                <th style="width: 5%">
                                    <center>Nilai</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Bobot</center>
                                </th>
                                <th style="width: 4%">
                                    <center>Total</center>
                                </th>
                                <th style="width: 9%">
                                    <center>Action</center>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        $sql1 = "SELECT * FROM tb_hows WHERE id_user='$id_sf' AND id_kpi='" . $hasil['id'] . "'";
                        $ql = mysqli_query($conn, $sql1);
                        while ($res = mysqli_fetch_assoc($ql)) {
                            $is_edited = $res['is_edited'];
                            $row_class = $is_edited ? 'edited-row' : '';
                        ?>
                            <tr class="align-middle <?= $row_class ?>">
                                <td>
                                    <?= $res['p_how']; ?>
                                    
                                    <?php if ($is_edited && !empty($res['original_p_how']) && $res['original_p_how'] != $res['p_how']) { ?>
                                        <span class="edited-badge">
                                            <i class="bi bi-pencil-fill"></i> DIUBAH ATASAN
                                        </span>
                                        
                                        <!-- Detail Perubahan -->
                                        <div class="comparison-box">
                                            <strong><i class="bi bi-file-diff"></i> Perubahan:</strong>
                                            <div class="mt-2">
                                                <small>
                                                    <strong>Sebelum:</strong><br>
                                                    <span class="old-value"><?= htmlspecialchars($res['original_p_how']) ?></span>
                                                </small>
                                            </div>
                                            <div class="mt-2">
                                                <small>
                                                    <strong>Sesudah:</strong><br>
                                                    <span class="new-value"><?= htmlspecialchars($res['p_how']) ?></span>
                                                </small>
                                            </div>
                                            
                                            <?php if ($res['original_bobot'] != $res['bobot']) { ?>
                                                <div class="mt-2">
                                                    <small>
                                                        <strong>Bobot:</strong> 
                                                        <span class="old-value"><?= $res['original_bobot'] ?>%</span>
                                                        <span class="change-arrow">→</span>
                                                        <span class="new-value"><?= $res['bobot'] ?>%</span>
                                                    </small>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        
                                        <?php if (!empty($res['edited_at'])) { ?>
                                            <div class="editor-info">
                                                <i class="bi bi-clock-history"></i> 
                                                Diubah: <?= date('d/m/Y H:i', strtotime($res['edited_at'])) ?>
                                                <?php 
                                                if (!empty($res['edited_by'])) {
                                                    $editor_id = $res['edited_by'];
                                                    $sql_editor = "SELECT nama_lengkap FROM user WHERE id_user = $editor_id";
                                                    $result_editor = mysqli_query($conn, $sql_editor);
                                                    if ($result_editor && mysqli_num_rows($result_editor) > 0) {
                                                        $editor_data = mysqli_fetch_assoc($result_editor);
                                                        echo '<br><i class="bi bi-person-fill"></i> Oleh: ' . $editor_data['nama_lengkap'];
                                                    }
                                                }
                                                ?>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    
                                    <!-- ===== TAMBAHAN BARU: TAMPILKAN TARGET OMSET DAN PERUBAHANNYA ===== -->
                                    <?php if ($res['tipe_how'] == 'B' && $res['target_omset'] > 0) { ?>
                                        <br><small class="text-muted fw-semibold fs-6" style="font-size: 10px;">
                                            Target: <?=number_format($res['target_omset'], 2)?>
                                        </small>
                                        
                                        <?php if ($is_edited && isset($res['original_target_omset']) && $res['original_target_omset'] > 0 && $res['original_target_omset'] != $res['target_omset']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info mt-1">
                                                <strong style="font-size: 9px;">Target Sebelum:</strong> 
                                                <span class="old-val"><?= number_format($res['original_target_omset'], 2) ?></span>
                                                <br>
                                                <strong style="font-size: 9px;">Target Sesudah:</strong> 
                                                <span class="new-val"><?= number_format($res['target_omset'], 2) ?></span>
                                            </div>
                                        <?php } ?>
                                    <?php } ?>
                                    <!-- ===== AKHIR TAMBAHAN ===== -->
                                </td>
                                <!-- KOLOM HASIL -->
                                <td>
                                    <?= $res['hasil']; ?>
                                    
                                    <?php if ($is_edited && !empty($res['original_hasil']) && $res['original_hasil'] != $res['hasil']) { ?>
                                        <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                        <div class="change-info">
                                            <strong style="font-size: 9px;">Sebelum:</strong> 
                                            <span class="old-val"><?= htmlspecialchars(substr($res['original_hasil'], 0, 30)) ?><?= strlen($res['original_hasil']) > 30 ? '...' : '' ?></span>
                                            <br>
                                            <strong style="font-size: 9px;">Sesudah:</strong> 
                                            <span class="new-val"><?= htmlspecialchars(substr($res['hasil'], 0, 30)) ?><?= strlen($res['hasil']) > 30 ? '...' : '' ?></span>
                                        </div>
                                    <?php } ?>
                                </td>
                                <td>
                                    <center>
                                        <?= $res['nilai']; ?>
                                        <?php if ($is_edited && $res['original_nilai'] != null && $res['original_nilai'] != $res['nilai']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info" style="text-align: left;">
                                                <span class="old-val"><?= $res['original_nilai'] ?></span> 
                                                → 
                                                <span class="new-val"><?= $res['nilai'] ?></span>
                                            </div>
                                        <?php } ?>
                                    </center>
                                </td>
                                <td>
                                    <center>
                                        <?= $res['bobot']; ?>%
                                        <?php if ($is_edited && $res['original_bobot'] != null && $res['original_bobot'] != $res['bobot']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info" style="text-align: left;">
                                                <span class="old-val"><?= $res['original_bobot'] ?>%</span> 
                                                → 
                                                <span class="new-val"><?= $res['bobot'] ?>%</span>
                                            </div>
                                        <?php } ?>
                                    </center>
                                </td>
                                <td>
                                    <center>
                                        <?= $res['total']; ?>
                                        
                                        <?php if ($is_edited && $res['original_total'] != null && $res['original_total'] != $res['total']) { ?>
                                            <span class="edited-badge"><i class="bi bi-pencil-fill"></i></span>
                                            <div class="change-info" style="text-align: left;">
                                                <span class="old-val"><?= $res['original_total'] ?></span> 
                                                → 
                                                <span class="new-val"><?= $res['total'] ?></span>
                                            </div>
                                        <?php } ?>
                                    </center>
                                </td>
                                <td class="text-center">
                                    <button type="button" data-bs-toggle="dropdown" class="btn btn-success btn-sm">
                                        <i class="bi bi-eye fs-8"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end" role="menu">
                                        <a value="<?php echo $res['id_how']; ?>" name="how_edit" class="dropdown-item"
                                            data-bs-toggle="modal"
                                            data-bs-target="#EditHowModal<?= $res['id_how'] ?>">Edit</a>
                                        <a class="dropdown-item" data-bs-toggle="modal"
                                            data-bs-target="#HapusHowModal<?= $res['id_how'] ?>">Hapus</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item fw-bolder" data-bs-toggle="modal"
                                            data-bs-target="#NilaiHowModal<?= $res['id_how'] ?>">Nilai</a>
                                    </div>
                                </td>
                            </tr>
                            
                            <?php
                            $tipe_how = $res['tipe_how'];
                            $target_omset = $res['target_omset'];
                            ?>

                            <!-- Modal Nilai How -->
                            <div class="modal fade" id="NilaiHowModal<?=$res['id_how']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content"> 
                                        <div class="modal-header"> 
                                            <h5 class="modal-title fw-bold" id="exampleModalLabel">
                                                Penilaian How <?=$tipe_how?>
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <form method="POST" action="">
                                                <input type="hidden" value="<?php echo $res['id_how']; ?>" name="idkpi"> 
                                                
                                                <div class="input-group mb-3">
                                                    <span style="color: #343A40;" class="input-group-text fw-bold" id="tujuan">Tujuan :</span>
                                                    <textarea type="input" class="form-control" name="indikatorhow" disabled placeholder="" aria-label="Tujuan KPI" aria-describedby="tujuan"><?=$res['p_how']?></textarea>
                                                </div>
                                                
                                                <?php if ($tipe_how == 'A') { ?>
                                                    <!-- HOW A: Pilih dari indikator -->
                                                    <div class="input-group mb-3">
                                                        <span style="color: #343A40;" class="input-group-text fw-bold">Nilai :</span>
                                                        <select required class="form-control" name="nilaisi" id="nilaisi">
                                                            <option selected disabled>Pilih Nilai</option>
                                                            <?php 
                                                            $id_how = $res['id_how'];
                                                            $sql_indikator = "SELECT * FROM tb_indikator_hows 
                                                                            WHERE id_how = '$id_how' 
                                                                            ORDER BY urutan ASC";
                                                            $result_indikator = mysqli_query($conn, $sql_indikator);
                                                            
                                                            while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                                                echo '<option value="'.$indikator['id_indikator'].'">';
                                                                echo ''.$indikator['keterangan'].' = '.$indikator['nilai'];
                                                                echo '</option>';
                                                            }
                                                            ?>
                                                        </select>
                                                    </div>
                                                <?php } else { ?>
                                                    <!-- HOW B: Input target omset dan hasil -->
                                                    <div class="input-group mb-3">
                                                        <span style="color: #343A40;" class="input-group-text fw-bold">Target :</span>
                                                        <input type="number" step="0.01" class="form-control" name="target_omset" 
                                                            value="<?=$target_omset?>" required placeholder="Contoh: 1000000">
                                                    </div>
                                                    
                                                    <div class="input-group mb-3">
                                                        <span style="color: #343A40;" class="input-group-text fw-bold">Hasil :</span>
                                                        <input type="number" step="0.01" class="form-control" name="hasil_omset" 
                                                            required placeholder="Hasil yang dicapai">
                                                    </div>
                                                <?php } ?>
                                                
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                    <button type="submit" name="nilai_how" class="btn btn-success">Simpan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                            // Tampilkan perubahan pada indikator (untuk How A)
                            if ($res['tipe_how'] == 'A') {
                                $sql_indikator = "SELECT * FROM tb_indikator_hows 
                                                WHERE id_how = " . $res['id_how'] . " 
                                                ORDER BY urutan ASC";
                                $result_indikator = mysqli_query($conn, $sql_indikator);
                                
                                $has_edited_indikator = false;
                                while ($indikator = mysqli_fetch_assoc($result_indikator)) {
                                    if ($indikator['is_edited']) {
                                        $has_edited_indikator = true;
                                        break;
                                    }
                                }
                                
                                if ($has_edited_indikator) {
                                    mysqli_data_seek($result_indikator, 0);
                            ?>
                                <!-- <tr class="edited-row">
                                    <td colspan="6">
                                        <div class="alert alert-warning mb-0">
                                            <strong><i class="bi bi-exclamation-triangle-fill"></i> Indikator yang Diubah:</strong>
                                            <ul class="mb-0 mt-2">
                                                <?php while ($indikator = mysqli_fetch_assoc($result_indikator)) { 
                                                    if ($indikator['is_edited'] && !empty($indikator['original_keterangan'])) {
                                                ?>
                                                    <li>
                                                        <small>
                                                            <strong>Sebelum:</strong> 
                                                            <span class="old-value">
                                                                <?= htmlspecialchars($indikator['original_keterangan']) ?> = <?= $indikator['original_nilai'] ?>
                                                            </span>
                                                            <span class="change-arrow">→</span>
                                                            <strong>Sesudah:</strong> 
                                                            <span class="new-value">
                                                                <?= htmlspecialchars($indikator['keterangan']) ?> = <?= $indikator['nilai'] ?>
                                                            </span>
                                                            <?php if (!empty($indikator['edited_at'])) { ?>
                                                                <br><small class="text-muted">
                                                                    <i class="bi bi-clock"></i> 
                                                                    <?= date('d/m/Y H:i', strtotime($indikator['edited_at'])) ?>
                                                                </small>
                                                            <?php } ?>
                                                        </small>
                                                    </li>
                                                <?php 
                                                    } elseif ($indikator['is_edited'] && empty($indikator['original_keterangan'])) {
                                                ?>
                                                    <li>
                                                        <small>
                                                            <span class="badge bg-success">BARU</span>
                                                            <span class="new-value">
                                                                <?= htmlspecialchars($indikator['keterangan']) ?> = <?= $indikator['nilai'] ?>
                                                            </span>
                                                            <small class="text-muted">(Ditambahkan oleh atasan)</small>
                                                        </small>
                                                    </li>
                                                <?php 
                                                    }
                                                } ?>
                                            </ul>
                                        </div>
                                    </td>
                                </tr> -->
                            <?php 
                                }
                            }
                            
                            include('pages/kpi/k_modalEdithow.php');
                            include('pages/kpi/k_modalHapushow.php');
                        } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('pages/kpi/k_modalWhat.php'); ?>
<?php include('pages/kpi/k_modalHow.php'); ?>
<?php include('pages/kpi/k_modalEditPoin.php'); ?>
<?php include('pages/kpi/k_modalEditPoin2.php'); ?>