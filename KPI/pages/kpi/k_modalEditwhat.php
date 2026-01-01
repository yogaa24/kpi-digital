<?php
// Query untuk mengambil indikator-indikator yang terkait dengan what ini
$id_what = $res['id_what'];
$tipe_what = $res['tipe_what'];
$sql_indikator = "SELECT * FROM tb_indikator_whats WHERE id_what = $id_what ORDER BY urutan ASC";
$result_indikator = mysqli_query($conn, $sql_indikator);
?>

<div class="modal fade" id="EditWhatModal<?=$res['id_what']?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold">
            Edit Detail What <?=$tipe_what?>
            <?php if ($tipe_what == 'A') { ?>
                <span class="badge bg-primary">What A</span>
            <?php } else { ?>
                <span class="badge bg-success">What B</span>
            <?php } ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <!-- BODY -->
      <div class="modal-body">
        <form method="POST" action="" class="what_edit">
          <input type="hidden" name="idkw" value="<?=$res['id_what']?>">

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Tujuan</span>
            <textarea class="form-control" name="tujuanw" required placeholder="Tujuan KPI"><?=$res['p_what']?></textarea>
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control" name="bobotw" required value="<?=$res['bobot']?>" placeholder="Tanpa %">
          </div>

          <?php if ($tipe_what == 'A') { ?>
          <!-- WHAT A: Indikator -->
          <hr>

          <!-- HEADER INDIKATOR -->
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="fw-bold mb-0">Indikator Penilaian</h6>
            <button type="button" class="btn btn-sm btn-primary" onclick="tambahIndikatorEdit<?=$res['id_what']?>()">
              <i class="bi bi-plus-circle"></i> Tambah
            </button>
          </div>

          <!-- INDIKATOR CONTAINER -->
          <div id="indikatorContainerEdit<?=$res['id_what']?>">

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
                          onclick="hapusIndikatorEdit<?=$res['id_what']?>(this)">
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
                          onclick="hapusIndikatorEdit<?=$res['id_what']?>(this)"
                          style="display:none">
                    <i class="bi bi-trash"></i>
                  </button>
                </div>
              </div>
            </div>
            <?php } ?>

          </div>
          <?php } ?> 
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

<?php if ($tipe_what == 'A') { ?>
<!-- JAVASCRIPT (hanya untuk What A) -->
<script>
let indikatorCountEdit<?=$res['id_what']?> = <?=mysqli_num_rows($result_indikator) > 0 ? mysqli_num_rows($result_indikator) : 1?>;

function tambahIndikatorEdit<?=$res['id_what']?>() {
    indikatorCountEdit<?=$res['id_what']?>++;
    const container = document.getElementById('indikatorContainerEdit<?=$res['id_what']?>');

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
                  onclick="hapusIndikatorEdit<?=$res['id_what']?>(this)">
            <i class="bi bi-trash"></i>
          </button>
        </div>
      </div>
    `;

    container.appendChild(div);
    updateHapusButtonsEdit<?=$res['id_what']?>();
}

function hapusIndikatorEdit<?=$res['id_what']?>(btn) {
    const indikatorItem = btn.closest('.indikator-item');
    const idIndikator = indikatorItem.querySelector('input[name="indikator_id[]"]').value;
    
    // Jika indikator sudah ada di database (id > 0), tandai untuk dihapus
    if (idIndikator > 0) {
        // Buat input hidden untuk menandai indikator yang akan dihapus
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'indikator_hapus[]';
        deleteInput.value = idIndikator;
        document.querySelector('#EditWhatModal<?=$res['id_what']?> form').appendChild(deleteInput);
    }
    
    indikatorItem.remove();
    updateHapusButtonsEdit<?=$res['id_what']?>();
}

function updateHapusButtonsEdit<?=$res['id_what']?>() {
    const items = document.querySelectorAll('#indikatorContainerEdit<?=$res['id_what']?> .indikator-item');
    const buttons = document.querySelectorAll('#indikatorContainerEdit<?=$res['id_what']?> .btn-hapus-indikator');

    buttons.forEach(button => {
        button.style.display = items.length > 1 ? 'inline-block' : 'none';
    });
}

// Jalankan saat modal dibuka
document.addEventListener('DOMContentLoaded', function() {
    updateHapusButtonsEdit<?=$res['id_what']?>();
});
</script>
<?php } ?>