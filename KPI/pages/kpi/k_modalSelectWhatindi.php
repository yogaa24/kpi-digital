<div class="modal fade" id="selectwhatindi<?= $res['id_what'] ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">List Indikator What :  <?= $res['p_what'] ; ?> </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="input">
        <input type="input" value="<?= $res['id_what'];?>" class="form-control" name="idkw" >
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="tujuan">List Indikator :</span>
                <textarea type="input" value="<?=$res['p_what'];?>" class="form-control" name="tujuanw" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="tujuan" ><?php echo $res['p_what']; ?></textarea>
            </div>
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="hasil">hasil :</span>
                <textarea type="input" value="<?=$res['hasil'];?>" class="form-control" name="hasilw" placeholder="Hasil KPI" aria-label="Hasil KPI" aria-describedby="hasil" ><?php echo $res['hasil']; ?></textarea>
            </div>            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">nilai :</span>
                <input type="input" value="<?=$res['nilai'];?>" class="form-control" name="nilaiw" placeholder="Nilai KPI" aria-label="Nilai KPI" aria-describedby="nilai" >
            </div>            
            <div class="input-group mb-3">
                <span style="color : #343A40;" class="input-group-text fw-bold" id="bobot">bobot :</span>
                <input type="input" value="<?=$res['bobot'];?>" class="form-control" name="bobotw" placeholder="Bobot KPI" aria-label="Bobot KPI" aria-describedby="bobot" >
            </div>            
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="what_edit" class="btn btn-primary">Simpan</button>
        </div>
        </form>
      </div>
    </div>
  </div> 