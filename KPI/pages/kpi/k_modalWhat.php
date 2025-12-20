<div class="modal fade" id="WhatModal<?=$idKPI?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content"> 
      <div class="modal-header"> 
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Detail What</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="what_add">
        <input  hidden value="<?php echo $idKPI; ?>" name="idkpi"> 
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text  fw-bold" id="tujuan">Tujuan :</span>
                <input type="input" class="form-control" name="tujuan" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="tujuan">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="hasil">Hasil :</span>
                <input type="input" class="form-control" name="hasil" placeholder="Hasil" aria-label="Hasil KPI" aria-describedby="hasil">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Nilai :</span>
                <input type="input" class="form-control" name="nilai" placeholder="Angka" aria-label="Nilai KPI" aria-describedby="nilai">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="bobot">Bobot :</span>
                <input type="input" class="form-control" name="bobot" placeholder="angka tidak usah %" aria-label="Bobot KPI" aria-describedby="bobot">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text  fw-bold" id="indikatorwhat">indikator :</span>
                <textarea type="input" value="<?echo $indikatorwhat;?>" class="form-control" name="indikatorwhat" placeholder="jika selesai 100% = 115 , 90% = 100 " aria-label="Tujuan KPI" aria-describedby="tujuan"></textarea>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="input" name="what_add" class="btn btn-primary">Tambah</button>
        </div>
    </form>
    </div>
  </div>
</div>
  