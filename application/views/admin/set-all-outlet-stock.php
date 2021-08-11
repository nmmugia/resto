<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<style>
.control-label{
  text-align: left !important;
}
</style>
<?php echo form_open(base_url(SITE_ADMIN."/stock/set_outlet_stock"), array('class' => 'form-horizontal form-ajax'));?>
  <div class="col-lg-12">
    <div class="result">
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
    </div>
    <div class="panel panel-default">
      <div class="panel-body">
        <div class="form-group">
            <label for="store_id" class="col-sm-2 control-label">Waktu</label>
            <div class="col-sm-3" style="padding-top:7px;">
             <?php echo date('Y-m-d H:i');?>
            </div> 
        </div>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th width="20px">ID</th>
              <th width="100px">Nama Inventori</th> 
              <th width="100px">Outlet</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              $j= 1;
            foreach($detail_stock as $l): ?>
              <tr>
                <td><?php echo $j; ?></td>
                <td><?php echo  $l['name']; ?></td> 
                <td> 
                  <input type="hidden" name="detail[inventory_id][<?php echo $l['id'] ?>]" value="<?php echo $l['price'] ?>">
                  <input type="hidden" name="detail[uom_id][<?php echo $l['id'] ?>]" value="<?php echo $l['uom_id'] ?>">
                  <?php 
                  $i=0;
                      foreach ($outlets as $key => $value) {   
                          ?>
                          <input type="checkbox" name="detail[outlet_id][<?php echo $l['id'] ?>][]" value="<?php echo $key;?>" 
                                  <?php if(in_array($key,$l['outlets'])) echo  "checked disabled already-checked"; ?> class="checkbox_outlet"> <?php echo $value;?> 
                          <?php 
                          $i++;
                      }
                  ?>
                </td>
              </tr>
            <?php $j++; endforeach; ?>
          </tbody>
        </table>
        <div class="form-group">
          <div class="text-center">  
            <button type="submit" name="btnAction" value="save" class="btn btn-primary">Simpan</button>
            <a href="<?php echo base_url(SITE_ADMIN . '/stock/stocklet'); ?>" class="btn btn-default">Batal</a>
          </div>
        </div>
      </div>
    </div>
  </div>
<?php echo form_close(); ?>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>