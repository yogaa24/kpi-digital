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
                                <option value="Kabag">Kabag</option>
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
                            <input type="text" class="form-control" name="penilai" id="penilai_register" required readonly>
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

<script>
// ====== CHANGE DEPARTEMEN ======
document.getElementById('departemen_register').addEventListener('change', function() {
    const penilaiInput = document.getElementById('penilai_register');
    const atasanSelect = document.getElementById('atasan_register');
    let items = [];

    if (this.value === 'Keuangan & Sales') {
        penilaiInput.value = 'Diana Wulandari';
        items = ["Pilih Atasan", "Ibnu", "Evi Yulia Purnama Sari", "Ahmad Syaiti", "Fairin"];
    }
    if (this.value === 'IT') {
        penilaiInput.value = 'Diana Wulandari';
        items = ["Pilih Atasan", "Wahyu Arif Prasetyo"];
    }  
    else if (this.value === 'Purchasing') {
        penilaiInput.value = 'Diana Wulandari';
        items = ["Pilih Atasan", "Evi Yulia", "Heru Sucahyo"];
    } 
    else if (this.value === 'HRD') {
        penilaiInput.value = 'Diana Wulandari';
        items = ["Pilih Atasan", "Riza Dwi Fitrianingtyas"];
    }
    else if (this.value === 'Logistik') {
        penilaiInput.value = 'Diana Wulandari';
        items = ["Pilih Atasan", "Fauzan","Wildan Ma'ruf N. W."];
    } 
    else if (this.value === 'GA') {
        penilaiInput.value = 'Nandang Ernoko';
        items = ["Pilih Atasan", "Nandang", "Wawan"];
    }

    renderAtasan(items, atasanSelect);
});


document.getElementById('jabatan_register').addEventListener('change', function() {
    const atasanSelect = document.getElementById('atasan_register');
    let items = [];

    if (this.value === 'Kabag') {
        items = ["Pilih Atasan", "Diana Wulandari", "Vita Ari Puspita", "Riza Dwi Fitrianingtyas","Kurniawan Pratama Arifin"];
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
function renderAtasan(items, selectElement) {
    let str = "";
    for (let item of items) {
        str += `<option>${item}</option>`;
    }
    selectElement.innerHTML = str;
}

</script>