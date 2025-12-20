<div class="modal fade" id="addwhatindi<?=$res['id_what']?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content"> 
      <div class="modal-header"> 
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Indikator : <?=$res['p_what'];?> </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="add_indi">          
        <input hidden value="<?php echo $res['id_user'];?>" name="iduser"> 
        <input hidden value="<?=$idKPI;?>" name="idkpiindikator"> 
        <input hidden value="<?=$res['id_what'];?>" name="idwhatindikator"> 
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text  fw-bold" id="whatindikator">What Indikator :</span>
                <input type="input" class="form-control" name="whatindikator" placeholder="Keterangan" aria-label="What Indikator" aria-describedby="whatindikator">
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="nilaiindi">Nilai Indikator :</span>
                <input type="input" class="form-control" name="nilaiindi" placeholder="Nilai" aria-label="Hasil KPI" aria-describedby="hasil">
            </div>            
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="input" name="whatindi_add" class="btn btn-primary">Tambah</button>
        </div>
    </form>
    </div>
  </div>
</div> 

