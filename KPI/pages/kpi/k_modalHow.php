<div class="modal fade" id="HowModal<?=$idKPI?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content"> 

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Tambah Detail How</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form method="POST" action="" class="how_add">
          <input type="hidden" name="idkpi" value="<?=$idKPI?>">

          <!-- Pilihan Tipe How -->
          <div class="mb-3">
            <label class="form-label fw-bold">Pilih Tipe How:</label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe_how" id="tipeHowA<?=$idKPI?>" value="A" checked onchange="toggleHowType<?=$idKPI?>(this.value)">
                <label class="form-check-label" for="tipeHowA<?=$idKPI?>">
                  How A (Dengan Indikator Penilaian)
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="tipe_how" id="tipeHowB<?=$idKPI?>" value="B" onchange="toggleHowType<?=$idKPI?>(this.value)">
                <label class="form-check-label" for="tipeHowB<?=$idKPI?>">
                  How B (Target Omset)
                </label>
              </div>
            </div>
          </div>

          <hr>

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">How</span>
            <input type="text" class="form-control" name="tujuan" required placeholder="How KPI">
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control" name="bobot" required placeholder="Tanpa %">
          </div>

          <!-- Target Omset (Hanya untuk How B) -->
          <div id="targetOmsetSectionHow<?=$idKPI?>" style="display:none;">
            <div class="input-group mb-3">
              <span class="input-group-text fw-bold">Target Omset</span>
              <input type="number" step="0.01" class="form-control" name="target_omset" placeholder="Contoh: 1000000">
            </div>
          </div>

          <hr id="separatorIndikatorHow<?=$idKPI?>">

          <!-- SECTION INDIKATOR (Hanya untuk How A) -->
          <div id="indikatorSectionHow<?=$idKPI?>">
            <!-- HEADER INDIKATOR -->
            <div class="d-flex justify-content-between align-items-center mb-2">
              <h6 class="fw-bold mb-0">Indikator Penilaian</h6>
              <button type="button" class="btn btn-sm btn-success" onclick="tambahIndikatorHow<?=$idKPI?>()">
                <i class="bi bi-plus-circle"></i> Tambah
              </button>
            </div>

            <!-- INDIKATOR CONTAINER -->
            <div id="indikatorContainerHow<?=$idKPI?>">

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
                            onclick="hapusIndikatorHow<?=$idKPI?>(this)"
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
        <button type="submit" name="how_add" class="btn btn-success">Simpan</button>
      </div>

        </form>
    </div>
  </div>
</div>

<!-- JAVASCRIPT -->
<script>
let indikatorCountHow<?=$idKPI?> = 1;

function toggleHowType<?=$idKPI?>(tipe) {
    const indikatorSection = document.getElementById('indikatorSectionHow<?=$idKPI?>');
    const targetOmsetSection = document.getElementById('targetOmsetSectionHow<?=$idKPI?>');
    const separatorIndikator = document.getElementById('separatorIndikatorHow<?=$idKPI?>');
    const indikatorInputs = document.querySelectorAll('#indikatorContainerHow<?=$idKPI?> textarea, #indikatorContainerHow<?=$idKPI?> input[type="number"]');
    const targetOmsetInput = document.querySelector('#HowModal<?=$idKPI?> input[name="target_omset"]');
    
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

function tambahIndikatorHow<?=$idKPI?>() {
    indikatorCountHow<?=$idKPI?>++;
    const container = document.getElementById('indikatorContainerHow<?=$idKPI?>');

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
                onclick="hapusIndikatorHow<?=$idKPI?>(this)">
          <i class="bi bi-trash"></i>
        </button>
      </div>
    </div>
    `;

    container.appendChild(div);
    updateHapusButtonsHow<?=$idKPI?>();
}

function hapusIndikatorHow<?=$idKPI?>(btn) {
    btn.closest('.indikator-item').remove();
    updateHapusButtonsHow<?=$idKPI?>();
}

function updateHapusButtonsHow<?=$idKPI?>() {
    const items = document.querySelectorAll('#indikatorContainerHow<?=$idKPI?> .indikator-item');
    const buttons = document.querySelectorAll('#indikatorContainerHow<?=$idKPI?> .btn-hapus-indikator');

    buttons.forEach(button => {
        button.style.display = items.length > 1 ? 'inline-block' : 'none';
    });
}

// Set default saat modal dibuka
document.getElementById('HowModal<?=$idKPI?>').addEventListener('shown.bs.modal', function() {
    toggleHowType<?=$idKPI?>('A');
});
</script>