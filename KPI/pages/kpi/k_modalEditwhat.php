<div class="modal fade" id="EditWhatModal<?=$res['id_what']?>" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit What</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form method="POST">
          <input type="hidden" name="id_what" value="<?=$res['id_what']?>">

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Tujuan</span>
            <input type="text" class="form-control"
                   name="tujuan"
                   value="<?=$res['p_what']?>"
                   required>
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control"
                   name="bobot"
                   value="<?=$res['bobot']?>"
                   required>
          </div>

          <hr>

          <!-- HEADER INDIKATOR -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Indikator Penilaian</h6>
            <button type="button"
                    class="btn btn-sm btn-primary"
                    onclick="tambahIndikatorEdit<?=$res['id_what']?>()">
              <i class="bi bi-plus-circle"></i> Tambah
            </button>
          </div>

          <!-- INDIKATOR CONTAINER -->
          <div id="indikatorEditContainer<?=$res['id_what']?>">

            <?php while($i = mysqli_fetch_assoc($indikator)) { ?>
              <div class="indikator-item mb-2">
                <div class="row g-2 align-items-center">

                  <div class="col-md-8">
                    <textarea class="form-control form-control-sm"
                              name="indikator_keterangan[]"
                              rows="1"
                              required><?=$i['keterangan']?></textarea>
                  </div>

                  <div class="col-md-3">
                    <input type="number" step="0.01"
                           class="form-control form-control-sm"
                           name="indikator_nilai[]"
                           value="<?=$i['nilai']?>"
                           required>
                  </div>

                  <div class="col-md-1 text-end">
                    <button type="button"
                            class="btn btn-sm btn-outline-danger btn-hapus-indikator"
                            onclick="hapusIndikatorEdit<?=$res['id_what']?> (this)">
                      <i class="bi bi-trash"></i>
                    </button>
                  </div>

                </div>
              </div>
            <?php } ?>

          </div>
      </div>

      <!-- FOOTER -->
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="submit" name="what_edit" class="btn btn-primary">Simpan</button>
      </div>

        </form>
    </div>
  </div>
</div>

<script>
function tambahIndikatorEdit<?=$res['id_what']?>() {
    const container = document.getElementById('indikatorEditContainer<?=$res['id_what']?>');

    const div = document.createElement('div');
    div.className = 'indikator-item mb-2';
    div.innerHTML = `
      <div class="row g-2 align-items-center">
        <div class="col-md-8">
          <textarea class="form-control form-control-sm"
                    name="indikator_keterangan[]"
                    rows="1"
                    required></textarea>
        </div>

        <div class="col-md-3">
          <input type="number" step="0.01"
                 class="form-control form-control-sm"
                 name="indikator_nilai[]"
                 required>
        </div>

        <div class="col-md-1 text-end">
          <button type="button"
                  class="btn btn-sm btn-outline-danger"
                  onclick="hapusIndikatorEdit<?=$res['id_what']?> (this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;
    container.appendChild(div);
}

function hapusIndikatorEdit<?=$res['id_what']?> (btn) {
    btn.closest('.indikator-item').remove();
}
</script>
