<?php

?>
<div class="modal fade" id="EditModal2<?=$idKPI?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="EditModalLabel">Edit Poin KPI</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="input">
        <input type="input" value="<?= $idKPI;?>" class="form-control" name="idk" hidden>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="poin2">How :</span>
                <input type="input" value="<?= $poin2;?>" class="form-control" name="poin2" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="poin2">
            </div>
            <div class="input-group mb-3" hidden>
                <span style="color : #343A40;" class="input-group-text fw-bold" id="bobot2">Bobot How :</span>
                <input type="input" value="<?= $bobot2;?>" class="form-control" name="bobot2" placeholder="0-90" aria-label="Bobot KPI" aria-describedby="bobot2">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="input" name="update2" class="btn btn-primary">Simpan</button>
        </div>
    </form>
    </div>
  </div>
</div>