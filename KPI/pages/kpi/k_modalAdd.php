<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="exampleModalLabel">Tambah Poin KPI </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="input">
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="poin">What :</span>
            <input type="input" class="form-control" name="poin" placeholder="Tujuan KPI" aria-label="Tujuan KPI" aria-describedby="tujuan">
          </div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="bobot">Bobot What:</span>
            <input type="input" class="form-control" name="bobot" placeholder="10-90" aria-label="Bobot KPI" aria-describedby="bobot">
          </div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="poin2">How :</span>
            <input type="input" class="form-control" name="poin2" placeholder="Bagaimana Caranya" aria-label="Bobot KPI" aria-describedby="bobot">
          </div>
          <div class="input-group mb-3">
            <span style="color : #343A40;" class="input-group-text fw-bold" id="bobot2">Bobot How:</span>
            <input type="input" class="form-control" name="bobot2" placeholder="10-90" aria-label="Bobot KPI" aria-describedby="bobot">
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="input" name="submit" class="btn btn-primary">Tambah</button>
      </div>
      </form>
    </div>
  </div>
</div>