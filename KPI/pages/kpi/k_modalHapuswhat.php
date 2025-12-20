<div class="modal fade" id="HapusWhatModal<?=$res['id_what']?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="EditModalLabel"><?=$res['p_what'];?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="input">      
        <input hidden type="input" value="<?= $res['id_what'];?>" class="form-control" name="idkwd" >
        <div class="container">
      <p>Are you sure you want to delete your what wkwkwk?</p>
        </div>         
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="what_hapus" class="btn btn-danger">Hapus</button>
        </div>
    </form>
    </div>
  </div>
</div>