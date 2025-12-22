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

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Tujuan</span>
            <input type="text" class="form-control" name="tujuan" required placeholder="Tujuan KPI">
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control" name="bobot" required placeholder="Tanpa %">
          </div>

          <hr>

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

                <!-- HANYA 1 KOLOM -->
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

function tambahIndikatorHow<?=$idKPI?>() {
    indikatorCountHow<?=$idKPI?>++;
    const container = document.getElementById('indikatorContainerHow<?=$idKPI?>');

    const div = document.createElement('div');
    div.className = 'indikator-item mb-2';
    div.innerHTML = `
    <div class="indikator-item mb-2">
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

        <!-- HANYA 1 KOLOM -->
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
</script>