<!-- Modal Tambah SP untuk <?=$hasilsfa['nama_lngkp']?> -->
<div class="modal fade" id="modalTambahSP<?=$hasilsfa['id']?>" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Tambah Surat Peringatan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST" action="">
                <div class="modal-body">
                    <input type="hidden" name="id_user" value="<?=$hasilsfa['id']?>">
                    
                    <!-- Info Karyawan -->
                    <div class="alert alert-info">
                        <strong><i class="bi bi-person-fill"></i> Karyawan:</strong> <?=$hasilsfa['nama_lngkp']?><br>
                        <strong><i class="bi bi-card-text"></i> NIK:</strong> <?=$hasilsfa['nik']?>
                    </div>
                    
                    <!-- Jenis SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-clipboard-check"></i> Jenis Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="jenis_sp" id="jenisSP<?=$hasilsfa['id']?>" required onchange="updatePenaltyInfo<?=$hasilsfa['id']?>()">
                            <option value="">-- Pilih Jenis SP --</option>
                            <option value="SP1">SP 1 (Pengurangan 2 poin)</option>
                            <option value="SP2">SP 2 (Pengurangan 3.5 poin)</option>
                            <option value="SP3">SP 3 (Pengurangan 5 poin)</option>
                        </select>
                        <div id="penaltyInfo<?=$hasilsfa['id']?>" class="mt-2"></div>
                    </div>
                    
                    <!-- Nomor SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text"></i> Nomor Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="nomor_sp" required 
                               placeholder="Contoh: SP/HRD/001/2024">
                    </div>
                    
                   <!-- Tanggal SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-event"></i> Tanggal Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="tanggal_sp" id="tanggalSP<?=$hasilsfa['id']?>" required value="<?=date('Y-m-d')?>" onchange="updateMasaBerlaku<?=$hasilsfa['id']?>()">
                    </div>

                    <!-- Masa Berlaku (Auto-calculated, read-only) -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Masa Berlaku SP (Otomatis 6 Bulan)
                        </label>
                        <div class="alert alert-info mb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mulai:</strong> <span id="displayMulai<?=$hasilsfa['id']?>"><?=date('d/m/Y')?></span>
                                    <input type="hidden" name="masa_berlaku_mulai" id="masaMulai<?=$hasilsfa['id']?>" value="<?=date('Y-m-d')?>">
                                </div>
                                <div class="col-md-6">
                                    <strong>Selesai:</strong> <span id="displaySelesai<?=$hasilsfa['id']?>"><?=date('d/m/Y', strtotime('+6 months'))?></span>
                                    <input type="hidden" name="masa_berlaku_selesai" id="masaSelesai<?=$hasilsfa['id']?>" value="<?=date('Y-m-d', strtotime('+6 months'))?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Alasan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-chat-left-text"></i> Alasan/Pelanggaran <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="alasan" rows="3" required 
                                  placeholder="Jelaskan alasan pemberian surat peringatan..."></textarea>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-chat-dots"></i> Keterangan Tambahan
                        </label>
                        <textarea class="form-control" name="keterangan" rows="2" 
                                  placeholder="Keterangan tambahan (opsional)"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" name="tambah_sp" class="btn btn-danger">
                        <i class="bi bi-save"></i> Simpan Surat Peringatan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function updatePenaltyInfo<?=$hasilsfa['id']?>() {
    const jenisSP = document.getElementById('jenisSP<?=$hasilsfa['id']?>').value;
    const infoDiv = document.getElementById('penaltyInfo<?=$hasilsfa['id']?>');
    
    const penalties = {
        'SP1': { poin: 2, class: 'warning', icon: 'exclamation-circle' },
        'SP2': { poin: 3.5, class: 'danger', icon: 'exclamation-triangle' },
        'SP3': { poin: 5, class: 'dark', icon: 'x-octagon' }
    };
    
    if (jenisSP && penalties[jenisSP]) {
        const p = penalties[jenisSP];
        infoDiv.innerHTML = `
            <div class="alert alert-${p.class} mb-0">
                <i class="bi bi-${p.icon}-fill"></i> 
                <strong>Dampak:</strong> Nilai KPI akan dikurangi <strong>${p.poin} poin</strong> selama 6 bulan masa berlaku SP
            </div>
        `;
    } else {
        infoDiv.innerHTML = '';
    }
}

function updateMasaBerlaku<?=$hasilsfa['id']?>() {
    const tanggalSP = document.getElementById('tanggalSP<?=$hasilsfa['id']?>').value;
    
    if (tanggalSP) {
        const startDate = new Date(tanggalSP);
        const endDate = new Date(tanggalSP);
        endDate.setMonth(endDate.getMonth() + 6);
        
        // Format tanggal untuk input hidden (Y-m-d)
        const startDateStr = tanggalSP;
        const endDateStr = endDate.toISOString().split('T')[0];
        
        // Format tanggal untuk display (d/m/Y)
        const startDisplay = startDate.toLocaleDateString('id-ID');
        const endDisplay = endDate.toLocaleDateString('id-ID');
        
        // Update hidden inputs
        document.getElementById('masaMulai<?=$hasilsfa['id']?>').value = startDateStr;
        document.getElementById('masaSelesai<?=$hasilsfa['id']?>').value = endDateStr;
        
        // Update display
        document.getElementById('displayMulai<?=$hasilsfa['id']?>').textContent = startDisplay;
        document.getElementById('displaySelesai<?=$hasilsfa['id']?>').textContent = endDisplay;
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateMasaBerlaku<?=$hasilsfa['id']?>();
});
</script>