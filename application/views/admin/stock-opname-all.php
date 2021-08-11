<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<style>
.control-label{
  text-align: left !important;
}
</style>
<?php echo form_open(base_url(SITE_ADMIN."/stock/adjustment"), array('class' => 'form-horizontal form-ajax'));?>
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
        <div class="form-group">
          <label for="store_id" class="col-sm-2 control-label">Outlet</label>
          <div class="col-sm-3" style="padding-top:7px;">
            <select id="stock_opname_outlet_id" class="form-control select2">
              <option value="">Semua Outlet</option>
              <?php foreach($outlets as $o): ?>
              <option value="<?php echo $o->id ?>"><?php echo $o->outlet_name ?></option>
              <?php endforeach; ?>
            </select>
          </div> 
        </div>
        <div class="form-group">
          <label for="store_id" class="col-sm-2 control-label">Inventory</label>
          <div class="col-sm-6" style="padding-top:7px;">
            <select id="stock_opname_inventory_id" class="form-control select2">
              <option value="">Semua Inventory</option>
              <?php foreach($inventory_lists as $o): ?>
              <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
              <?php endforeach; ?>
            </select>
          </div> 
        </div>
        <table class="table table-bordered" id="stock_opname_all_table">
          <thead>
            <tr>
              <th width="150px">Outlet</th>
              <th>Nama Inventori</th>
              <th width="150px">Stok Sistem</th>
              <th width="150px">Opname</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($detail_stock as $l): ?>
              <tr>
                <td><?php echo $l->outlet_name; ?></td>
                <td><?php echo $l->name; ?></td>
                <td><?php echo $l->stock_system." ".$l->code; ?></td>
                <td>
                  <input type="hidden" name="detail[outlet_id][]" value="<?php echo $l->outlet_id ?>">
                  <input type="hidden" name="detail[stock_system][]" value="<?php echo $l->stock_system ?>" class="stock_system">
                  <input type="hidden" name="detail[inventory_id][]" value="<?php echo $l->id ?>">
                  <input type="hidden" name="detail[uom_id][]" value="<?php echo $l->uom_id ?>">
                  <input type="text" name="detail[qty][]" class="form-control col-sm-5 only_number qty">
                  <input type="text" name="detail[price][]" class="form-control col-sm-5 only_number price"  placeholder="Harga HPP"  style="display:none;">
                </td>
              </tr>
            <?php endforeach; ?>
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