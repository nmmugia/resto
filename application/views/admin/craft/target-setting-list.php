<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php
    if (! empty($message_success)) {
        echo '<div class="alert alert-success" role="alert">';
        echo $message_success;
        echo '</div>';
    }
    if (! empty($message)) {
        echo '<div class="alert alert-danger" role="alert">';
        echo $message;
        echo '</div>';
    }
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <a href="<?php echo $add_url; ?>" class="btn btn-primary pull-right"><i class='fa fa-plus'></i> Tambah Target</a> 
      <a href="#" class="btn btn-success pull-right" style="margin-right:2px;" data-toggle="modal" data-target="#kitchen-reward-modal" id="add_reward_kitcen"><i class='fa fa-plus'></i> Setting Reward Kitchen</a> 
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
      <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-target-setting">
        <thead>
        <tr>
          <th>Nama Waiter</th>
          <th>Tipe Target</th>
          <th>Reward Penjualan</th>
          <th style="text-align: center" width="200px"><?php echo $this->lang->line('column_action'); ?></th>
        </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url ?>"/>
    </div>
  </div>
</div>
<div class="modal fade" id="kitchen-reward-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Pengaturan Reward Kitchen per Menu</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group error-message"></div>
          <div class="form-group">
            <label class="control-label">Oulet Kitchen</label>
            <select class="form-control" name="outlet_kitchen" id="outlet_kitchen">
              <?php foreach($outlets as $o): ?>
              <option value="<?php echo $o->id ?>"><?php echo $o->outlet_name ?></option>
              <?php endforeach; ?>
            </select>
          </div>  
          <div class="form-group">
            <label class="control-label">Jumlah Reward :</label>
            <input type="text" class="form-control no-special-char" id="kitchen_reward" name="kitchen_reward">
          </div>   
          <div class="container-group">
            <div class="form-group">
              <label for="recipient-name" class="control-label qty-input">Masuk ke perhitungan Gaji ?</label>
              <div class="input-group">
                Ya
                <input type="hidden" name="calculate_to_payroll" id="calculate_to_payroll" value="1">
              </div>
            </div>  
          </div> 
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary"  id="save-kitchen-reward" data-action='save'>Simpan</button>
      </div>
    </div>
  </div>
</div>