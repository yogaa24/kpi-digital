<!-- Modal Tambah Poin SS -->
<div class="modal fade" id="TambahSS<?= $hasil['id_poinss']; ?>" tabindex="-1" aria-labelledby="TambahModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="TambahModalLabel">Tambah Poin Skill Standard</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="" class="input">
                    <input type="hidden" value="<?= $hasil['id_poinss']; ?>" name="idss">
                    
                    <div class="input-group mb-3">
                        <span style="color : #343A40;" class="input-group-text fw-bold">Poin :</span>
                        <input type="text" class="form-control" name="tujuan" 
                               placeholder="Poin skill standard" required>
                    </div>

                    <small class="fs-6 fw-bold">Indikator Penilaian</small>
                    
                    <div class="input-group mb-3">
                        <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 1</span>
                        <input type="text" class="form-control" name="indikator_1" 
                               placeholder="Indikator 1 ..." required>
                    </div>
                    
                    <div class="input-group mb-3">
                        <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 2</span>
                        <input type="text" class="form-control" name="indikator_2" 
                               placeholder="Indikator 2 ..." required>
                    </div>

                    <div class="input-group mb-3">
                        <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 3</span>
                        <input type="text" class="form-control" name="indikator_3" 
                               placeholder="Indikator 3 ..." required>
                    </div>

                    <div class="input-group mb-3">
                        <span style="color : #343A40;" class="input-group-text fw-bold">Nilai 4</span>
                        <input type="text" class="form-control" name="indikator_4" 
                               placeholder="Indikator 4 ..." required>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" name="addsspoin" class="btn btn-primary">Tambah</button>
            </div>
            </form>
        </div>
    </div>
</div>