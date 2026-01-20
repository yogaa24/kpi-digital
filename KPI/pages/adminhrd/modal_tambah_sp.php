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
            
            <form method="POST" action="" enctype="multipart/form-data"> <!-- TAMBAHKAN enctype -->
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

                    <!-- Masa Berlaku -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Masa Berlaku SP (Otomatis 6 Bulan)
                        </label>
                        <div class="alert alert-info mb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mulai:</strong> <span id="displayMulai<?=$hasilsfa['id']?>"><?=date('d/m/Y')?></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Selesai:</strong> <span id="displaySelesai<?=$hasilsfa['id']?>"><?=date('d/m/Y', strtotime('+6 months'))?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- TAMBAHAN BARU: Upload File SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-earmark-arrow-up"></i> Upload Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" name="file_sp" id="fileSP<?=$hasilsfa['id']?>" 
                            accept=".pdf,.jpg,.jpeg,.png" required onchange="previewFileName<?=$hasilsfa['id']?>()">
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> Format: PDF, JPG, JPEG, PNG | Maksimal: 5MB
                        </small>
                        <div id="filePreview<?=$hasilsfa['id']?>" class="mt-2"></div>
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
function previewFileName<?=$hasilsfa['id']?>() {
    const fileInput = document.getElementById('fileSP<?=$hasilsfa['id']?>');
    const previewDiv = document.getElementById('filePreview<?=$hasilsfa['id']?>');
    
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // Convert to MB
        const fileExt = file.name.split('.').pop().toLowerCase();
        
        let icon = 'file-earmark';
        if (fileExt === 'pdf') icon = 'file-earmark-pdf';
        else if (['jpg', 'jpeg', 'png'].includes(fileExt)) icon = 'file-earmark-image';
        
        previewDiv.innerHTML = `
            <div class="alert alert-success mb-0">
                <i class="bi bi-${icon}-fill"></i> 
                <strong>${file.name}</strong> (${fileSize} MB)
            </div>
        `;
    } else {
        previewDiv.innerHTML = '';
    }
}

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
        
        const startDisplay = startDate.toLocaleDateString('id-ID');
        const endDisplay = endDate.toLocaleDateString('id-ID');
        
        document.getElementById('displayMulai<?=$hasilsfa['id']?>').textContent = startDisplay;
        document.getElementById('displaySelesai<?=$hasilsfa['id']?>').textContent = endDisplay;
    }
}
</script>