<div class="modal-dialog" role="document">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Pengaturan Fitur Unlock </h4>
    </div>
    <form method="post" action="<?php echo base_url(SITE_ADMIN."/feature_settings/set") ?>" class="form-horizontal">
      <input type="hidden" name="feature_id" value="<?php echo $feature->id; ?>">
      <div class="modal-body">
        <div class="form-group">
          <label class="col-sm-2 control-label">User</label>
            <div class="col-sm-10"> 
              <select name="users[]" id="users_unlock" class="multiselect form-control" size="8" multiple="multiple">
                  <?php 
                    $users=explode(",",$feature->users_unlock);
                    foreach ($lists as $l) {
                      echo "<option value='".$l->id."' ".(in_array($l->id,$users) ? "selected" : "").">".$l->name."</option>";
                    }
                  ?>
              </select>
            </div>
        </div> 
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default clear_feature_setting">Clear</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
      </div>
    </form>
  </div>
</div>