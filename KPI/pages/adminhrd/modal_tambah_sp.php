<!-- Modal Tambah SP (Reusable Single Modal) -->
<div class="modal fade" id="modalTambahSP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    Tambah Surat Peringatan
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <form method="POST" action="datakpi-adminhrd" enctype="multipart/form-data" id="formTambahSP">
                <div class="modal-body">
                    <input type="hidden" name="id_user" id="tambahSP_id_user" value="">
                    
                    <!-- Info Karyawan -->
                    <div class="alert alert-info">
                        <strong><i class="bi bi-person-fill"></i> Karyawan:</strong> <span id="tambahSP_nama">-</span><br>
                        <strong><i class="bi bi-card-text"></i> NIK:</strong> <span id="tambahSP_nik">-</span>
                    </div>
                    
                    <!-- Jenis SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-clipboard-check"></i> Jenis Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <select class="form-control" name="jenis_sp" id="tambahSP_jenis" required onchange="handleSPPenaltyChange()">
                            <option value="">-- Pilih Jenis SP --</option>
                            <option value="SP1">SP 1 (Pengurangan 2 poin)</option>
                            <option value="SP2">SP 2 (Pengurangan 3.5 poin)</option>
                            <option value="SP3">SP 3 (Pengurangan 5 poin)</option>
                        </select>
                        <div id="tambahSP_penaltyInfo" class="mt-2"></div>
                    </div>
                    
                    <!-- Nomor SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-text"></i> Nomor Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control" name="nomor_sp" id="tambahSP_nomor" required 
                            placeholder="Contoh: SP/HRD/001/2024">
                    </div>
                    
                    <!-- Tanggal SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-event"></i> Tanggal Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" name="tanggal_sp" id="tambahSP_tanggal" required value="<?=date('Y-m-d')?>" onchange="handleSPMasaBerlakuChange()">
                    </div>

                    <!-- Masa Berlaku -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-calendar-range"></i> Masa Berlaku SP (Otomatis 6 Bulan)
                        </label>
                        <div class="alert alert-info mb-0">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Mulai:</strong> <span id="tambahSP_displayMulai"><?=date('d/m/Y')?></span>
                                </div>
                                <div class="col-md-6">
                                    <strong>Selesai:</strong> <span id="tambahSP_displaySelesai"><?=date('d/m/Y', strtotime('+6 months'))?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upload File SP -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-file-earmark-arrow-up"></i> Upload Surat Peringatan <span class="text-danger">*</span>
                        </label>
                        <input type="file" class="form-control" name="file_sp" id="tambahSP_file" 
                            accept=".pdf,.jpg,.jpeg,.png" required onchange="handleSPFilePreview()">
                        <small class="form-text text-muted">
                            <i class="bi bi-info-circle"></i> Format: PDF, JPG, JPEG, PNG | Maksimal: 5MB
                        </small>
                        <div id="tambahSP_filePreview" class="mt-2"></div>
                    </div>
                    
                    <!-- Alasan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-chat-left-text"></i> Alasan/Pelanggaran <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" name="alasan" id="tambahSP_alasan" rows="3" required 
                                placeholder="Jelaskan alasan pemberian surat peringatan..."></textarea>
                    </div>
                    
                    <!-- Keterangan -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-chat-dots"></i> Keterangan Tambahan
                        </label>
                        <textarea class="form-control" name="keterangan" id="tambahSP_keterangan" rows="2" 
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