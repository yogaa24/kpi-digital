<!-- Modal Register User Baru -->
<div class="modal fade" id="modalRegister" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-person-plus-fill me-2"></i>Tambah User Baru
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="namalengkap" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" name="cpassword" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">NIK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="nik" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Departemen <span class="text-danger">*</span></label>
                            <select class="form-select" name="departemen" id="departemen_register" required>
                                <option value="">Pilih Departemen</option>
                                <option value="Keuangan & Sales">Keuangan & Sales</option>
                                <option value="Purchasing">Purchasing</option>
                                <option value="IT">IT</option>
                                <option value="HRD">HRD</option>
                                <option value="Logistik">Logistik</option>
                                <option value="GA">GA</option>
                                <option value="Unit Bisnis Seed">Unit Bisnis Seed</option>
                                <option value="Unit Bisnis CP">Unit Bisnis CP</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Bagian <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" name="bagian" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Jabatan <span class="text-danger">*</span></label>
                            <select class="form-select" name="jabatan" id="jabatan_register" required>
                                <option value="">Pilih Jabatan</option>
                                <option value="Direktur">Direktur</option>
                                <option value="Kadep">Kadep</option>
                                <option value="Manager">Manager</option>
                                <option value="Karyawan">Karyawan</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Atasan <span class="text-danger">*</span></label>
                            <select class="form-select" name="atasan" id="atasan_register" required>
                                <option value="">Pilih Atasan</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Penilai <span class="text-danger">*</span></label>
                            <select class="form-select" name="penilai[]" id="penilai_register" multiple size="3" required>
                                <option value="">Pilih Penilai</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Batal
                    </button>
                    <button type="submit" name="register_user" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Tambah User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// ====== CHANGE DEPARTEMEN ======
document.getElementById('departemen_register').addEventListener('change', function() {
    const penilaiSelect = document.getElementById('penilai_register');
    const atasanSelect = document.getElementById('atasan_register');
    let items = [];
    let penilaiItems = [];

    if (this.value === 'Keuangan & Sales') {
        penilaiItems = ['Diana Wulandari', 'Vita Ari Puspitasari'];
        items = ["Pilih Atasan", "Ibnu Sutoro", "Evi Yulia Purnama Sari", "Ahmad Syaiti", "Iva Isti Farini"];
    }
    else if (this.value === 'IT') {
        penilaiItems = ['Diana Wulandari'];
        items = ["Pilih Atasan", "Wahyu Arif Prasetyo"];
    }  
    else if (this.value === 'Purchasing') {
        penilaiItems = ['Diana Wulandari'];
        items = ["Pilih Atasan", "Evi Yulia", "Heru Sucahyo"];
    } 
    else if (this.value === 'HRD') {
        penilaiItems = ['Diana Wulandari'];
        items = ["Pilih Atasan", "Riza Dwi Fitrianingtyas"];
    }
    else if (this.value === 'Logistik') {
        penilaiItems = ['Diana Wulandari'];
        items = ["Pilih Atasan", "Fauzan","Wildan Ma'ruf N. W."];
    } 
    else if (this.value === 'GA') {
        penilaiItems = ['Nandang Ernoko'];
        items = ["Pilih Atasan", "Nandang", "Wawan"];
    }
    else if (this.value === 'Unit Bisnis Seed') {
        penilaiItems = ['Heru Sucahyo'];
        items = ["Pilih Atasan", "Acep Andriyanto", "Yama Muhammad", "Ahmad Muhlisin"];
    }
    else if (this.value === 'Unit Bisnis CP') {
        penilaiItems = ['Arfin Indra Cahyadi'];
        items = ["Pilih Atasan", "Arfin Indra Cahyadi"];
    }

    renderPenilai(penilaiItems, penilaiSelect);
    renderAtasan(items, atasanSelect);
});


document.getElementById('jabatan_register').addEventListener('change', function() {
    const atasanSelect = document.getElementById('atasan_register');
    let items = [];

    if (this.value === 'Manager') {
        items = ["Pilih Atasan", "Diana Wulandari", "Vita Ari Puspita", "Riza Dwi Fitrianingtyas","Kurniawan Pratama Arifin", "Heru Sucahyo", "Arfin Indra Cahyadi"];
    } 
    else if (this.value === 'Kadep') {
        items = ["Pilih Atasan", "Diana Wulandari"];
    } 
    else if (this.value === 'Direktur') {
        items = ["Pilih Atasan", "Direksi"];
    }
    else if (this.value === 'Karyawan') {
        // ⚠️ PENTING: JANGAN overwrite dari departemen
        return;
    }

    renderAtasan(items, atasanSelect);
});

// ====== FUNCTION RENDER OPTION ======
function renderPenilai(items, selectElement) {
    let str = "";
    for (let item of items) {
        str += `<option value="${item}" selected>${item}</option>`;
    }
    selectElement.innerHTML = str;
}

function renderAtasan(items, selectElement) {
    let str = "";
    for (let item of items) {
        str += `<option>${item}</option>`;
    }
    selectElement.innerHTML = str;
}
</script>