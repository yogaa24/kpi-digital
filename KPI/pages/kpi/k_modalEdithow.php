<?php
// Query untuk mengambil indikator-indikator yang terkait dengan how ini
$id_how = $res['id_how'];
$sql_indikator = "SELECT * FROM tb_indikator_hows WHERE id_how = $id_how ORDER BY urutan ASC";
$result_indikator = mysqli_query($conn, $sql_indikator);
?>

<div class="modal fade" id="EditHowModal<?=$res['id_how']?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold">Edit Detail How</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form method="POST" action="" class="how_edit">
          <input type="hidden" name="idkh" value="<?=$res['id_how']?>">

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Tujuan</span>
            <textarea class="form-control" name="tujuanh" required placeholder="Tujuan KPI"><?=$res['p_how']?></textarea>
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control" name="boboth" required value="<?=$res['bobot']?>" placeholder="Tanpa %">
          </div>

          <hr>

          <!-- HEADER INDIKATOR -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Indikator Penilaian</h6>
            <button type="button" class="btn btn-sm btn-success" onclick="tambahIndikatorEditHow<?=$res['id_how']?>()">
              <i class="bi bi-plus-circle"></i> Tambah
            </button>
          </div>

          <!-- INDIKATOR CONTAINER -->
          <div id="indikatorContainerEditHow<?=$res['id_how']?>">

            <?php 
            // Jika ada indikator, tampilkan
            if (mysqli_num_rows($result_indikator) > 0) {
                while ($indikator = mysqli_fetch_assoc($result_indikator)) {
            ?>
            <!-- INDIKATOR ITEM -->
            <div class="indikator-item mb-2">
              <div class="row g-2 align-items-center">
                <div class="col-md-8">
                  <textarea class="form-control form-control-sm"
                            name="indikator_keterangan[]"
                            rows="1"
                            required
                            placeholder="Keterangan indikator"><?=$indikator['keterangan']?></textarea>
                  <input type="hidden" name="indikator_id[]" value="<?=$indikator['id_indikator']?>">
                </div>

                <div class="col-md-3">
                  <input type="number"
                        step="0.01"
                        class="form-control form-control-sm"
                        name="indikator_nilai[]"
                        required
                        value="<?=$indikator['nilai']?>"
                        placeholder="Nilai">
                </div>

                <div class="col-md-1 text-end">
                  <button type="button"
                          class="btn btn-sm btn-outline-danger btn-hapus-indikator"
                          onclick="hapusIndikatorEditHow<?=$res['id_how']?>(this)">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            <?php 
                }
            } else {
                // Jika tidak ada indikator, tampilkan satu field kosong
            ?>
            <div class="indikator-item mb-2">
              <div class="row g-2 align-items-center">
                <div class="col-md-8">
                  <textarea class="form-control form-control-sm"
                            name="indikator_keterangan[]"
                            rows="1"
                            required
                            placeholder="Keterangan indikator"></textarea>
                  <input type="hidden" name="indikator_id[]" value="0">
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
                          onclick="hapusIndikatorEditHow<?=$res['id_how']?>(this)"
                          style="display:none">
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
        <button type="submit" name="how_edit" class="btn btn-success">Simpan</button>
      </div>

        </form>
    </div>
  </div>
</div>

<!-- JAVASCRIPT -->
<script>
let indikatorCountEditHow<?=$res['id_how']?> = <?=mysqli_num_rows($result_indikator) > 0 ? mysqli_num_rows($result_indikator) : 1?>;

function tambahIndikatorEditHow<?=$res['id_how']?>() {
    indikatorCountEditHow<?=$res['id_how']?>++;
    const container = document.getElementById('indikatorContainerEditHow<?=$res['id_how']?>');

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
          <input type="hidden" name="indikator_id[]" value="0">
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
                  onclick="hapusIndikatorEditHow<?=$res['id_how']?>(this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;

    container.appendChild(div);
    updateHapusButtonsEditHow<?=$res['id_how']?>();
}

function hapusIndikatorEditHow<?=$res['id_how']?>(btn) {
    const indikatorItem = btn.closest('.indikator-item');
    const idIndikator = indikatorItem.querySelector('input[name="indikator_id[]"]').value;
    
    // Jika indikator sudah ada di database (id > 0), tandai untuk dihapus
    if (idIndikator > 0) {
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'indikator_hapus[]';
        deleteInput.value = idIndikator;
        indikatorItem.closest('form').appendChild(deleteInput);
    }
    
    indikatorItem.remove();
    updateHapusButtonsEditHow<?=$res['id_how']?>();
}

function updateHapusButtonsEditHow<?=$res['id_how']?>() {
    const items = document.querySelectorAll('#indikatorContainerEditHow<?=$res['id_how']?> .indikator-item');
    const buttons = document.querySelectorAll('#indikatorContainerEditHow<?=$res['id_how']?> .btn-hapus-indikator');

    buttons.forEach(button => {
        button.style.display = items.length > 1 ? 'inline-block' : 'none';
    });
}

// Jalankan saat modal dibuka
document.addEventListener('DOMContentLoaded', function() {
    updateHapusButtonsEditHow<?=$res['id_how']?>();
});
</script>