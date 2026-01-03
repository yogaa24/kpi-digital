<?php
$id_user_sp = $hasilsfa['id'];
$sql_sp = "SELECT * FROM tb_surat_peringatan 
           WHERE id_user = $id_user_sp 
           ORDER BY created_at DESC";
$result_sp = mysqli_query($conn, $sql_sp);
?>

<div class="modal fade" id="modalKelolaSP<?=$hasilsfa['id']?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-file-earmark-text-fill me-2"></i>
                    Kelola Surat Peringatan - <?=$hasilsfa['nama_lngkp']?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <?php if (mysqli_num_rows($result_sp) > 0) { ?>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-sm">
                            <thead class="table-dark">
                                <tr>
                                    <th width="8%">Jenis SP</th>
                                    <th width="12%">Nomor SP</th>
                                    <th width="10%">Tanggal</th>
                                    <th width="15%">Masa Berlaku</th>
                                    <th width="8%">Pengurangan</th>
                                    <th>Alasan</th>
                                    <th width="10%">Status</th>
                                    <th width="8%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($sp = mysqli_fetch_assoc($result_sp)) { 
                                    $today = date('Y-m-d');
                                    $is_active = ($sp['status'] == 'aktif' && 
                                                 $sp['masa_berlaku_mulai'] <= $today && 
                                                 $sp['masa_berlaku_selesai'] >= $today);
                                    
                                    $status_badge = 'secondary';
                                    $status_text = 'Selesai';
                                    
                                    if ($sp['status'] == 'aktif') {
                                        if ($is_active) {
                                            $status_badge = 'success';
                                            $status_text = 'Aktif';
                                        } else if ($sp['masa_berlaku_selesai'] < $today) {
                                            $status_badge = 'secondary';
                                            $status_text = 'Expired';
                                        } else {
                                            $status_badge = 'warning';
                                            $status_text = 'Akan Datang';
                                        }
                                    } else if ($sp['status'] == 'dihapus') {
                                        $status_badge = 'danger';
                                        $status_text = 'Dihapus';
                                    }
                                    
                                    $sp_badge = getSPBadgeClass($sp['jenis_sp']);
                                    $penalty = getSPPenalty($sp['jenis_sp']);
                                ?>
                                <tr class="<?=$is_active ? 'table-warning' : ''?>">
                                    <td>
                                        <span class="badge bg-<?=$sp_badge?> fw-bold">
                                            <?=$sp['jenis_sp']?>
                                        </span>
                                    </td>
                                    <td><small><?=$sp['nomor_sp']?></small></td>
                                    <td><small><?=date('d/m/Y', strtotime($sp['tanggal_sp']))?></small></td>
                                    <td>
                                        <small>
                                            <?=date('d/m/Y', strtotime($sp['masa_berlaku_mulai']))?><br>
                                            s/d <?=date('d/m/Y', strtotime($sp['masa_berlaku_selesai']))?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-danger">-<?=$penalty?> poin</span>
                                    </td>
                                    <td>
                                        <small><?=substr($sp['alasan'], 0, 50)?><?=strlen($sp['alasan']) > 50 ? '...' : ''?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?=$status_badge?>">
                                            <?=$status_text?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($sp['status'] == 'aktif') { ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="if(confirm('Yakin ingin menghapus SP ini?\n\nNilai KPI karyawan akan kembali normal setelah SP dihapus.')) { document.getElementById('formHapusSP<?=$sp['id_sp']?>').submit(); }">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            <form id="formHapusSP<?=$sp['id_sp']?>" method="POST" style="display:none;">
                                                <input type="hidden" name="id_sp" value="<?=$sp['id_sp']?>">
                                                <input type="hidden" name="hapus_sp" value="1">
                                            </form>
                                        <?php } else { ?>
                                            <span class="text-muted">-</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                <?php } else { ?>
                    <div class="alert alert-info text-center">
                        <i class="bi bi-info-circle fs-3"></i>
                        <p class="mb-0 mt-2">Tidak ada data surat peringatan untuk karyawan ini.</p>
                    </div>
                <?php } ?>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>