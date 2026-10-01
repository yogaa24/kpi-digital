<!-- Modal Tambah SP (Dibuat Langsung dari Aplikasi Tanpa Wajib Upload File) -->
<div class="modal fade" id="modalTambahSP" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <form method="POST" action="datakpi-adminhrd<?= !empty($_GET['mode']) ? '?mode=' . htmlspecialchars($_GET['mode']) : '' ?>" enctype="multipart/form-data" id="formTambahSP" class="modal-content">
            <input type="hidden" name="id_user" id="tambahSP_id_user" value="">

            <div class="modal-header bg-danger text-white">
                <div>
                    <h5 class="modal-title fw-bold mb-0">
                        <i class="bi bi-file-earmark-plus-fill me-2"></i>
                        Buat Surat Peringatan (SP)
                    </h5>
                    <small class="opacity-75">Dibuat langsung dari sistem &bull; Tanpa wajib upload file fisik</small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <!-- Tab Navigasi: Form vs Pratinjau Dokumen -->
            <div class="bg-light border-bottom px-3 pt-2">
                <ul class="nav nav-tabs border-0" id="spModalTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold" id="form-tab" data-bs-toggle="tab" data-bs-target="#tab-form-sp" type="button" role="tab">
                            <i class="bi bi-pencil-square me-1"></i> 1. Formulir Surat Peringatan
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold text-dark" id="preview-tab" data-bs-toggle="tab" data-bs-target="#tab-preview-sp" type="button" role="tab" onclick="updateLiveSPPreview()">
                            <i class="bi bi-eye-fill me-1 text-primary"></i> 2. Pratinjau Surat Resmi
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4" style="overflow-y: auto; max-height: calc(85vh - 130px);">
                    <div class="tab-content" id="spModalTabContent">
                        
                        <!-- TAB 1: FORMULIR INPUT -->
                        <div class="tab-pane fade show active" id="tab-form-sp" role="tabpanel">
                            
                            <!-- Info Karyawan Terpilih -->
                            <div class="alert alert-info border-0 shadow-sm mb-4">
                                <div class="row align-items-center">
                                    <div class="col-md-6 mb-2 mb-md-0">
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                                                <i class="bi bi-person-fill fs-5"></i>
                                            </div>
                                            <div>
                                                <div class="text-muted small">Penerima Surat Peringatan:</div>
                                                <div class="fw-bold fs-6" id="tambahSP_nama">-</div>
                                                <small class="text-secondary">NIK: <span id="tambahSP_nik">-</span></small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 border-start-md">
                                        <div class="row small">
                                            <div class="col-6">
                                                <span class="text-muted">Departemen:</span><br>
                                                <strong id="tambahSP_departemen">-</strong>
                                            </div>
                                            <div class="col-6">
                                                <span class="text-muted">Jabatan:</span><br>
                                                <strong id="tambahSP_jabatan">-</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- Jenis SP -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-clipboard-check text-danger"></i> Jenis Surat Peringatan <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select" name="jenis_sp" id="tambahSP_jenis" required onchange="handleSPPenaltyChange(); updateLiveSPPreview();">
                                        <option value="">-- Pilih Jenis SP --</option>
                                        <option value="SP1">SP 1 - Surat Peringatan Pertama (Pengurangan 2 poin)</option>
                                        <option value="SP2">SP 2 - Surat Peringatan Kedua (Pengurangan 3.5 poin)</option>
                                        <option value="SP3">SP 3 - Surat Peringatan Ketiga (Pengurangan 5 poin)</option>
                                    </select>
                                    <div id="tambahSP_penaltyInfo" class="mt-2"></div>
                                </div>

                                <!-- Nomor SP -->
                                <div class="col-md-6 mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <label class="form-label fw-bold mb-0">
                                            <i class="bi bi-hash text-danger"></i> Nomor Surat Peringatan <span class="text-danger">*</span>
                                        </label>
                                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0" onclick="autoGenerateSPNomor()">
                                            <i class="bi bi-magic"></i> Generate Otomatis
                                        </button>
                                    </div>
                                    <input type="text" class="form-control mt-1" name="nomor_sp" id="tambahSP_nomor" required 
                                        placeholder="Contoh: 294/KIU-HRD/IX/2026" oninput="updateLiveSPPreview()">
                                </div>
                            </div>

                            <div class="row">
                                <!-- Tanggal SP -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calendar-event text-danger"></i> Tanggal Surat SP <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" name="tanggal_sp" id="tambahSP_tanggal" required 
                                        value="<?=date('Y-m-d')?>" onchange="handleSPMasaBerlakuChange(); updateLiveSPPreview();">
                                </div>

                                <!-- Tanggal Kejadian Pelanggaran -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-calendar-x text-danger"></i> Tanggal Kejadian Pelanggaran <span class="text-danger">*</span>
                                    </label>
                                    <input type="date" class="form-control" name="tanggal_kejadian" id="tambahSP_tgl_kejadian" required 
                                        value="<?=date('Y-m-d')?>" onchange="updateLiveSPPreview()">
                                </div>

                                <!-- Masa Berlaku Otomatis -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-clock-history text-danger"></i> Masa Berlaku (6 Bulan)
                                    </label>
                                    <div class="border rounded p-2 bg-light text-center small">
                                        <span id="tambahSP_displayMulai" class="fw-bold"><?=date('d/m/Y')?></span>
                                        <span class="text-muted mx-1">s/d</span>
                                        <span id="tambahSP_displaySelesai" class="fw-bold text-danger"><?=date('d/m/Y', strtotime('+6 months'))?></span>
                                    </div>
                                </div>
                            </div>

                            <div class="card mb-3 border-danger-subtle bg-light">
                                <div class="card-header bg-white fw-bold text-dark py-2 d-flex justify-content-between align-items-center">
                                    <span><i class="bi bi-shield-exclamation text-danger me-1"></i> Rincian Pelanggaran & Tata Tertib</span>
                                    <button type="button" class="btn btn-outline-danger btn-sm py-0 px-2" style="font-size: 11px;" onclick="setDefaultPasal()">
                                        <i class="bi bi-arrow-repeat"></i> Reset Format Standar
                                    </button>
                                </div>
                                <div class="card-body">
                                    <!-- Aturan / Pasal yang Dilanggar -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">
                                            1. Peraturan Perusahaan / Pasal yang Dilanggar <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" name="aturan_dilanggar" id="tambahSP_aturan" rows="3" required 
                                            placeholder="Contoh: Peraturan Perusahaan Pasal 22 ayat (2) point 23 :&#10;Tidak berhati-hati dan / atau lalai dalam melaksanakan tugas sehingga dapat mengakibatkan kerugiaan bagi perusahaan"
                                            oninput="updateLiveSPPreview()">Peraturan Perusahaan Pasal 22 ayat (2) point 23 :
Tidak berhati-hati dan / atau lalai dalam melaksanakan tugas sehingga dapat mengakibatkan kerugiaan bagi perusahaan</textarea>
                                    </div>

                                    <!-- Uraian Kejadian Pelanggaran 1 -->
                                    <div class="mb-3">
                                        <label class="form-label fw-bold small">
                                            2. Uraian Kejadian Pelanggaran 1 <span class="text-danger">*</span>
                                        </label>
                                        <textarea class="form-control" name="alasan" id="tambahSP_alasan" rows="3" required 
                                            placeholder="jelaskan sop yang dilanggar"
                                            oninput="updateLiveSPPreview()"></textarea>
                                    </div>

                                    <!-- Uraian Kejadian Pelanggaran 2 -->
                                    <div class="mb-0">
                                        <label class="form-label fw-bold small">
                                            3. Uraian Kejadian Pelanggaran 2 (Opsional)
                                        </label>
                                        <textarea class="form-control" name="alasan_2" id="tambahSP_alasan_2" rows="3" 
                                            placeholder="Jelaskan uraian pelanggaran kedua jika ada..."
                                            oninput="updateLiveSPPreview()"></textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Pihak Penandatangan & Tembusan -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">
                                        <i class="bi bi-pen text-secondary"></i> Pejabat Pembuat / Penandatangan
                                    </label>
                                    <input type="text" class="form-control form-control-sm" name="penandatangan" id="tambahSP_penandatangan" 
                                        value="Riza Dwi Fitrianingtyas" oninput="updateLiveSPPreview()">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold small">
                                        <i class="bi bi-briefcase text-secondary"></i> Jabatan Penandatangan
                                    </label>
                                    <input type="text" class="form-control form-control-sm" name="jabatan_penandatangan" id="tambahSP_jabatan_penandatangan" 
                                        value="Kepala Departemen HRD" oninput="updateLiveSPPreview()">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold small">
                                        <i class="bi bi-send text-secondary"></i> Tembusan Surat
                                    </label>
                                    <input type="text" class="form-control form-control-sm" name="tembusan" id="tambahSP_tembusan" 
                                        value="1. Direktur sebagai laporan; 2. Kepala Departemen HRD; 3. Arsip;" oninput="updateLiveSPPreview()">
                                </div>
                            </div>


                            <!-- Keterangan Tambahan -->
                            <div class="mt-3">
                                <label class="form-label fw-bold small text-muted">
                                    <i class="bi bi-chat-dots"></i> Catatan Internal (Opsional)
                                </label>
                                <textarea class="form-control form-control-sm" name="keterangan" id="tambahSP_keterangan" rows="1" 
                                    placeholder="Catatan tambahan untuk arsip HRD..."></textarea>
                            </div>

                        </div>

                        <!-- TAB 2: PRATINJAU LANGSUNG (LIVE PREVIEW) -->
                        <div class="tab-pane fade" id="tab-preview-sp" role="tabpanel">
                            <div class="alert alert-warning py-2 px-3 small d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <i class="bi bi-info-circle-fill me-1"></i> Ini adalah tampilan pratinjau surat resmi yang akan dibuat oleh sistem.
                                </div>
                                <span class="badge bg-danger">Format Resmi PT Karisma Indoagro Universal</span>
                            </div>

                            <!-- Kertas Preview A4 Miniatur -->
                            <div class="p-4 mx-auto bg-white border shadow-sm" style="max-width: 800px; font-family: Arial, sans-serif; font-size: 11pt; line-height: 1.45; color: #111;">
                                <!-- Header / Kop -->
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div>
                                        <img src="assets/img/logo_karisma.png" alt="PT KARISMA INDOAGRO UNIVERSAL" style="max-height: 50px;">
                                    </div>
                                    <div style="color: #002060; font-weight: 900; font-size: 13pt; letter-spacing: 0.5px;">
                                        TRADISI, REPUTASI, INOVASI
                                    </div>
                                </div>

                                <!-- Judul Dokumen -->
                                <div class="text-center mb-3">
                                    <h5 class="fw-bold mb-0 text-uppercase" style="letter-spacing: 1px; font-size: 14pt;">SURAT PERINGATAN</h5>
                                    <div>Nomor : <span id="prev_nomor" class="fw-bold text-danger">[Nomor SP]</span></div>
                                </div>

                                <!-- Pembuka -->
                                <p class="text-justify mb-2">
                                    Sehubungan dengan pelanggaran yang dilakukan, dengan ini perusahaan memberikan Surat Peringatan kepada :
                                </p>

                                <table class="table table-borderless table-sm mb-3" style="width: auto; margin-left: 15px;">
                                    <tr>
                                        <td style="width: 130px;">Nama</td>
                                        <td style="width: 10px;">:</td>
                                        <td class="fw-bold" id="prev_nama">-</td>
                                    </tr>
                                    <tr>
                                        <td>Departemen</td>
                                        <td>:</td>
                                        <td id="prev_departemen">-</td>
                                    </tr>
                                    <tr>
                                        <td>Jabatan</td>
                                        <td>:</td>
                                        <td id="prev_jabatan">-</td>
                                    </tr>
                                </table>

                                <p class="text-justify mb-2">
                                    Atas sikap indisipliner dan pelanggaran terhadap tata tertib perusahaan yang saudara lakukan yaitu melanggar :
                                </p>

                                <ol style="padding-left: 20px;" class="mb-3">
                                    <li class="mb-2">
                                        <strong id="prev_aturan">Peraturan Perusahaan Pasal 22 ayat (2) point 23 :</strong><br>
                                        <span id="prev_aturan_detail">Tidak berhati-hati dan / atau lalai dalam melaksanakan tugas sehingga dapat mengakibatkan kerugiaan bagi perusahaan</span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Uraian Pelanggaran 1 :</strong><br>
                                        <span id="prev_alasan" class="text-danger fst-italic">[Isi uraian pelanggaran pada form]</span>
                                    </li>
                                    <li class="mb-2" id="prev_alasan_2_container" style="display:none;">
                                        <strong>Uraian Pelanggaran 2 :</strong><br>
                                        <span id="prev_alasan_2" class="text-danger fst-italic"></span>
                                    </li>
                                    <li class="mb-2">
                                        <strong>Tanggal kejadian :</strong><br>
                                        <span id="prev_tgl_kejadian"><?=date('d/m/Y')?></span>
                                    </li>
                                </ol>

                                <p class="text-justify mb-2">
                                    Maka dengan ini perusahaan memberikan <strong id="prev_sp_title">Surat Peringatan Pertama (SP-1)</strong> dengan ketentuan sebagai berikut :
                                </p>

                                <blockquote class="fst-italic px-3 mb-3 text-justify" style="border-left: 3px solid #ccc;">
                                    “<span id="prev_ketentuan">Surat Peringatan Pertama (SP-1) berlaku untuk 6 (enam) bulan kedepan sejak diterbitkan...</span>”
                                </blockquote>

                                <p class="text-justify mb-2">
                                    Surat Peringatan ini bertujuan untuk memberikan pengarahan sekaligus peringatan kepada saudara agar bersedia melaksanakan Peraturan Perusahaan, SOP, tata tertib perusahaan, dan tidak melakukan kesalahan yang dapat merugikan pihak perusahaan dan rekan sekerja.
                                </p>

                                <p class="text-justify mb-4">
                                    Demikian Surat Peringatan ini diberikan untuk dapat dijadikan sebagai bahan perhatian dan instropeksi diri.
                                </p>

                                <!-- Tanda Tangan -->
                                <div class="mb-3">
                                    <div>Jember, <span id="prev_tgl_sp"><?=date('d/m/Y')?></span></div>
                                    <div class="fw-bold mb-2">PT Karisma Indoagro Universal</div>
                                    
                                    <div class="row mt-3">
                                        <div class="col-6">
                                            Dibuat,
                                            <div style="height: 60px;"></div>
                                            <strong class="text-decoration-underline" id="prev_penandatangan">Riza Dwi Fitrianingtyas</strong><br>
                                            <span id="prev_jabatan_penandatangan">Kepala Departemen HRD</span>
                                        </div>
                                        <div class="col-6">
                                            Yang bersangkutan,
                                            <div style="height: 60px;"></div>
                                            <strong class="text-decoration-underline" id="prev_karyawan_ttd">-</strong><br>
                                            <span>Karyawan</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Tembusan -->
                                <div class="border-top border-secondary-subtle pt-2 mt-3 small">
                                    <strong>Tembusan :</strong> <span id="prev_tembusan">1. Direktur sebagai laporan; 2. Kepala Departemen HRD; 3. Arsip;</span>
                                </div>

                                <!-- Footer Perusahaan -->
                                <div class="border-top border-primary mt-4 pt-2 small text-muted">
                                    <div class="fw-bold text-primary">Jl. Semeru No.89 Ajung 68175, Jember - Jawa Timur</div>
                                    <div>Phone. : +62 (331) 4833 33, 4832 11-4877 88 &nbsp;|&nbsp; e-mail : karisma.kiu@gmail.com</div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Batal
                    </button>
                    <button type="submit" name="tambah_sp" class="btn btn-danger px-4">
                        <i class="bi bi-check-circle-fill me-1"></i> Simpan &amp; Buat Surat Peringatan
                    </button>
                </div>
            </form>
    </div>
</div>

<style>
/* Pastikan body modal dapat di-scroll lancar */
#modalTambahSP .modal-body {
    max-height: calc(85vh - 130px) !important;
    overflow-y: auto !important;
    scrollbar-width: thin;
    scrollbar-color: #adb5bd #f8f9fa;
}
#modalTambahSP .modal-body::-webkit-scrollbar {
    width: 8px;
}
#modalTambahSP .modal-body::-webkit-scrollbar-track {
    background: #f8f9fa;
    border-radius: 4px;
}
#modalTambahSP .modal-body::-webkit-scrollbar-thumb {
    background: #adb5bd;
    border-radius: 4px;
}
#modalTambahSP .modal-body::-webkit-scrollbar-thumb:hover {
    background: #6c757d;
}
</style>