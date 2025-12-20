<div class="modal fade" id="TambahSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Poin Skill Standard</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="what_add">
          <input hidden value="<?php echo $hasil['id_poinss']; ?>" name="idss">
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text  fw-bold" id="tujuan">Poin :</span>
            <input type="input" required class="form-control" name="tujuan" placeholder="Poin SS" aria-label="Poin SS" aria-describedby="tujuan">
          </div>
          <div class="divider-line"></div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Kriteria Nilai 1 :</span>
            <input type="input" required class="form-control" name="nilaiss1" placeholder="Alasan Nilai SS = 1">
          </div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Kriteria Nilai 2 :</span>
            <input type="input" required class="form-control" name="nilaiss2" placeholder="Alasan Nilai SS = 2">
          </div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Kriteria Nilai 3 :</span>
            <input type="input" required class="form-control" name="nilaiss3" placeholder="Alasan Nilai SS = 3">
          </div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="nilai">Kriteria Nilai 4 :</span>
            <input type="input" required class="form-control" name="nilaiss4" placeholder="Alasan Nilai SS = 4">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="input" name="addsspoin" class="btn btn-primary">Tambah</button>
      </div>
      </form>
    </div>
  </div>
</div>