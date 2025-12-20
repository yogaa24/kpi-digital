<div class="modal fade" id="HapusHowModal<?=$res['id_how']?>" tabindex="-1" aria-labelledby="EditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="EditModalLabel"><?=$res['p_how'];?></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form method="POST" action="" class="input">      
        <input hidden type="input" value="<?= $res['id_how'];?>" class="form-control" name="idkhd" >
        <div class="container">
      <p>Are you sure you want to delete your how wkwkwk?</p>
        </div>         
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" name="how_hapus" class="btn btn-danger">Hapus</button>
        </div>
    </form>
    </div>
  </div>
</div>