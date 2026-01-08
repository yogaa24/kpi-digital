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
                               placeholder="Contoh: Kemampuan Komunikasi Verbal" required>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle"></i> Setelah menambahkan poin, Anda dapat memberikan nilai melalui menu "Nilai"
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