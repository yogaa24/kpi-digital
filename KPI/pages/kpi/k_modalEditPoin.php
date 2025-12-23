<?php

?>
<div class="modal fade" id="EditModal<?=$idKPI?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
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
                <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">What :</span>
                <input type="input" value="<?= $poin;?>" class="form-control" name="poin" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="poin">
            </div>
            <?php if($jabatan=="Karyawan"){ $hfgiub = "hidden";}else{$hfgiub ='';}?>
            <div <?= $hfgiub ?> class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="bobot">Bobot What :</span>
                <input type="input" value="<?= $bobot;?>" class="form-control" name="bobot" placeholder="0-90" aria-label="Bobot KPI" aria-describedby="bobot">
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="input" name="update" class="btn btn-primary">Simpan</button>
        </div>
    </form>
    </div>
  </div>
</div>