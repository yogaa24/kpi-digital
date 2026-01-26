<div class="modal fade" id="WhatModal<?=$idKPI?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content"> 

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Detail What</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form method="POST" action="" class="what_add">
          <input type="hidden" name="idkpi" value="<?=$idKPI?>">

          <!-- Pilihan Tipe What -->
          <div class="mb-3">
            <label class="form-label fw-bold">Pilih Tipe What:</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe_what" id="tipeWhatA<?=$idKPI?>" value="A" checked onchange="toggleWhatType<?=$idKPI?>(this.value)">
                <label class="form-check-label" for="tipeWhatA<?=$idKPI?>">
                  What A (Dengan Indikator Penilaian)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe_what" id="tipeWhatB<?=$idKPI?>" value="B" onchange="toggleWhatType<?=$idKPI?>(this.value)">
                <label class="form-check-label" for="tipeWhatB<?=$idKPI?>">
                  What B (Dengan Angka Penilaian)
                </label>
              </div>
            </div>
          </div>

          <hr>

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">What</span>
            <input type="text" class="form-control" name="tujuan" required placeholder="What KPI">
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control" name="bobot" required placeholder="Tanpa %">
          </div>

          <!-- Target Omset (Hanya untuk What B) -->
          <div id="targetOmsetSection<?=$idKPI?>" style="display:none;">
            <div class="input-group mb-3">
              <span class="input-group-text fw-bold">Target Omset</span>
              <input type="number" step="0.01" class="form-control" name="target_omset" placeholder="Contoh: 1000000">
            </div>
          </div>

          <hr id="separatorIndikator<?=$idKPI?>">

          <!-- SECTION INDIKATOR (Hanya untuk What A) -->
          <div id="indikatorSection<?=$idKPI?>">
            <!-- HEADER INDIKATOR -->
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold mb-0">Indikator Penilaian</h6>
              <button type="button" class="btn btn-sm btn-primary" onclick="tambahIndikator<?=$idKPI?>()">
                <i class="bi bi-plus-circle"></i> Tambah
              </button>
            </div>

            <!-- INDIKATOR CONTAINER -->
            <div id="indikatorContainer<?=$idKPI?>">

              <!-- INDIKATOR DEFAULT -->
              <div class="indikator-item mb-2">
                <div class="row g-2 align-items-center">
                  <div class="col-md-8">
                    <textarea class="form-control form-control-sm"
                              name="indikator_keterangan[]"
                              rows="1"
                              placeholder="Keterangan indikator"></textarea>
                  </div>

                  <div class="col-md-3">
                    <input type="number"
                          step="0.01"
                          class="form-control form-control-sm"
                          name="indikator_nilai[]"
                          placeholder="Nilai">
                  </div>

                  <div class="col-md-1 text-end">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-hapus-indikator"
                            onclick="hapusIndikator<?=$idKPI?>(this)"
                            style="display:none">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>
                </div>
              </div>

            </div>
          </div>

      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="what_add" class="btn btn-primary">Simpan</button>
      </div>

        </form>
    </div>
  </div>
</div>

<!-- JAVASCRIPT -->
<script>
let indikatorCount<?=$idKPI?> = 1;

function toggleWhatType<?=$idKPI?>(tipe) {
    const indikatorSection = document.getElementById('indikatorSection<?=$idKPI?>');
    const targetOmsetSection = document.getElementById('targetOmsetSection<?=$idKPI?>');
    const separatorIndikator = document.getElementById('separatorIndikator<?=$idKPI?>');
    const indikatorInputs = document.querySelectorAll('#indikatorContainer<?=$idKPI?> textarea, #indikatorContainer<?=$idKPI?> input[type="number"]');
    const targetOmsetInput = document.querySelector('input[name="target_omset"]');
    
    if (tipe === 'A') {
        // Tampilkan indikator, sembunyikan target omset
        indikatorSection.style.display = 'block';
        separatorIndikator.style.display = 'block';
        targetOmsetSection.style.display = 'none';
        
        // Set required untuk indikator
        indikatorInputs.forEach(input => input.required = true);
        if (targetOmsetInput) targetOmsetInput.required = false;
    } else {
        // Sembunyikan indikator, tampilkan target omset
        indikatorSection.style.display = 'none';
        separatorIndikator.style.display = 'none';
        targetOmsetSection.style.display = 'block';
        
        // Set required untuk target omset
        indikatorInputs.forEach(input => input.required = false);
        if (targetOmsetInput) targetOmsetInput.required = true;
    }
}

function tambahIndikator<?=$idKPI?>() {
    indikatorCount<?=$idKPI?>++;
    const container = document.getElementById('indikatorContainer<?=$idKPI?>');

    const div = document.createElement('div');
    div.className = 'indikator-item mb-2';
    div.innerHTML = `
    <div class="row g-2 align-items-center">
      <div class="col-md-8">
        <textarea class="form-control form-control-sm"
                  name="indikator_keterangan[]"
                  rows="1"
                  required
                  placeholder="Keterangan indikator"></textarea>
      </div>

      <div class="col-md-3">
        <input type="number"
              step="0.01"
              class="form-control form-control-sm"
              name="indikator_nilai[]"
              required
              placeholder="Nilai">
      </div>

      <div class="col-md-1 text-end">
        <button type="button"
                class="btn btn-sm btn-outline-danger btn-hapus-indikator"
                onclick="hapusIndikator<?=$idKPI?>(this)">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </div>
    `;

    container.appendChild(div);
    updateHapusButtons<?=$idKPI?>();
}

function hapusIndikator<?=$idKPI?>(btn) {
    btn.closest('.indikator-item').remove();
    updateHapusButtons<?=$idKPI?>();
}

function updateHapusButtons<?=$idKPI?>() {
    const items = document.querySelectorAll('#indikatorContainer<?=$idKPI?> .indikator-item');
    const buttons = document.querySelectorAll('#indikatorContainer<?=$idKPI?> .btn-hapus-indikator');

    buttons.forEach(button => {
        button.style.display = items.length > 1 ? 'inline-block' : 'none';
    });
}

// Set default saat modal dibuka
document.getElementById('WhatModal<?=$idKPI?>').addEventListener('shown.bs.modal', function() {
    toggleWhatType<?=$idKPI?>('A');
});
</script>