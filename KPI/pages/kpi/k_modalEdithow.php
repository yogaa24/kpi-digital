<?php
// Query untuk mengambil indikator-indikator yang terkait dengan how ini
$id_how = $res['id_how'];
$tipe_how = $res['tipe_how'];
$sql_indikator = "SELECT * FROM tb_indikator_hows WHERE id_how = $id_how ORDER BY urutan ASC";
$result_indikator = mysqli_query($conn, $sql_indikator);
?>

<div class="modal fade" id="EditHowModal<?=$res['id_how']?>" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">

      <!-- HEADER -->
      <div class="modal-header">
        <h5 class="modal-title fw-bold">
            Edit Detail How <?=$tipe_how?>
            <?php if ($tipe_how == 'A') { ?>
                <span class="badge bg-primary">How A</span>
            <?php } else { ?>
                <span class="badge bg-success">How B</span>
            <?php } ?>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <form method="POST" action="" class="how_edit" id="editHowForm<?=$res['id_how']?>">
        <!-- BODY -->
        <div class="modal-body">
          <input type="hidden" name="idkh" value="<?=$res['id_how']?>" form="editHowForm<?=$res['id_how']?>">

          <!-- Tujuan -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Tujuan</span>
            <textarea class="form-control" name="tujuanh" form="editHowForm<?=$res['id_how']?>" required placeholder="Tujuan KPI"><?=$res['p_how']?></textarea>
          </div>

          <!-- Bobot -->
          <div class="input-group mb-3">
            <span class="input-group-text fw-bold">Bobot</span>
            <input type="number" step="0.01" class="form-control" name="boboth" form="editHowForm<?=$res['id_how']?>" required value="<?=$res['bobot']?>" placeholder="Tanpa %">
          </div>

          <?php if ($tipe_how == 'A') { ?>
          <!-- HOW A: Indikator -->
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
                            form="editHowForm<?=$res['id_how']?>"
                            rows="1"
                            required
                            placeholder="Keterangan indikator"><?=$indikator['keterangan']?></textarea>
                  <input type="hidden" name="indikator_id[]" value="<?=$indikator['id_indikator']?>" form="editHowForm<?=$res['id_how']?>">
                </div>

                <div class="col-md-3">
                  <input type="number"
                        step="0.01"
                        class="form-control form-control-sm"
                        name="indikator_nilai[]"
                        form="editHowForm<?=$res['id_how']?>"
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
                            form="editHowForm<?=$res['id_how']?>"
                            rows="1"
                            required
                            placeholder="Keterangan indikator"></textarea>
                  <input type="hidden" name="indikator_id[]" value="0" form="editHowForm<?=$res['id_how']?>">
                </div>

                <div class="col-md-3">
                  <input type="number"
                        step="0.01"
                        class="form-control form-control-sm"
                        name="indikator_nilai[]"
                        form="editHowForm<?=$res['id_how']?>"
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
          <?php } ?>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="submit" name="how_edit" form="editHowForm<?=$res['id_how']?>" class="btn btn-success">Simpan</button>
        </div>

      </form>
    </div>
  </div>
</div>

<?php if ($tipe_how == 'A') { ?>
<!-- JAVASCRIPT (hanya untuk How A) -->
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
                    form="editHowForm<?=$res['id_how']?>"
                    rows="1"
                    required
                    placeholder="Keterangan indikator"></textarea>
          <input type="hidden" name="indikator_id[]" value="0" form="editHowForm<?=$res['id_how']?>">
        </div>

        <div class="col-md-3">
          <input type="number"
                step="0.01"
                class="form-control form-control-sm"
                name="indikator_nilai[]"
                form="editHowForm<?=$res['id_how']?>"
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
        deleteInput.setAttribute('form', 'editHowForm<?=$res['id_how']?>');
        document.querySelector('#EditHowModal<?=$res['id_how']?> form').appendChild(deleteInput);
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

function syncIndikatorFormEditHow<?=$res['id_how']?>() {
    const form = document.getElementById('editHowForm<?=$res['id_how']?>');
    if (!form) {
        return;
    }

    form.querySelectorAll('[data-indikator-mirror="how-<?=$res['id_how']?>"]').forEach(input => input.remove());

    document
        .querySelectorAll('#EditHowModal<?=$res['id_how']?> [name="indikator_id[]"], #EditHowModal<?=$res['id_how']?> [name="indikator_keterangan[]"], #EditHowModal<?=$res['id_how']?> [name="indikator_nilai[]"]')
        .forEach(input => input.setAttribute('form', 'editHowForm<?=$res['id_how']?>'));

    document.querySelectorAll('#indikatorContainerEditHow<?=$res['id_how']?> .indikator-item').forEach(item => {
        const idInput = item.querySelector('[name="indikator_id[]"]');
        const ketInput = item.querySelector('[name="indikator_keterangan[]"]');
        const nilaiInput = item.querySelector('[name="indikator_nilai[]"]');
        const values = [
            ['indikator_id_submit[]', idInput ? idInput.value : '0'],
            ['indikator_keterangan_submit[]', ketInput ? ketInput.value : ''],
            ['indikator_nilai_submit[]', nilaiInput ? nilaiInput.value : '']
        ];

        values.forEach(([name, value]) => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = name;
            hidden.value = value;
            hidden.setAttribute('form', 'editHowForm<?=$res['id_how']?>');
            hidden.setAttribute('data-indikator-mirror', 'how-<?=$res['id_how']?>');
            form.appendChild(hidden);
        });
    });
}

// Jalankan saat modal dibuka
document.addEventListener('DOMContentLoaded', function() {
    updateHapusButtonsEditHow<?=$res['id_how']?>();
    syncIndikatorFormEditHow<?=$res['id_how']?>();

    const form = document.getElementById('editHowForm<?=$res['id_how']?>');
    if (form) {
        form.addEventListener('submit', syncIndikatorFormEditHow<?=$res['id_how']?>);
    }
});
</script>
<?php } ?>
