<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/17/2014
 * Time: 8:29 PM
 */

?>
<style>
.control-label{
    text-align: left !important;
}
</style>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
 
 <?php 
        echo form_open(base_url(SITE_ADMIN."/stock/add"), array('class' => 'form-horizontal form-ajax'));
    ?>
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
                <label for="sotre" class="col-sm-2 control-label">Restoran</label>
                <div class="col-sm-10">
                 <?php
                    echo form_dropdown('store_id', $store_id, $this->input->post('store_id'), 'field-name = "Store" id="store_id_d"  class="form-control requiredDropdown" autocomplete="off"');
                    echo '<input type="hidden" name="origin_outlet_id" id="origin_outlet_id" value="' . $origin_outlet_id . '"/>';
                ?>
                </div>
            </div>
            <div class="form-group">
                <label for="outlet" class="col-sm-2 control-label">Outlet</label>
                <div class="col-sm-10">
                 <?php 
                  echo form_dropdown('outlet_id', $outlet_id, "", 'field-name = "Outlet" id="outlet_id_d"  class="form-control requiredDropdown2" autocomplete="off"');
                 ?>
                </div>
            </div>
            <div class="form-group">
                <label for="bahan" class="col-sm-2 control-label">Bahan</label>
                <div class="col-sm-10">
                 <?php
                    echo form_dropdown('inventory_id', $inventory, "", 'field-name = "Inventory" id="stock_add_inventory_id"  class="form-control requiredDropdown3" autocomplete="off"');
                ?>
                </div>
            </div>
            <div class="form-group">
                <label for="bahan" class="col-sm-2 control-label">Satuan</label>
                <div class="col-sm-10">
                  <select name="uom_id" id="stock_add_uom_id" class="form-control requiredDropdown" field-name = "Satuan">
                    <option value="">Pilih Satuan</option>
                  </select>
                </div>
            </div>
            <div class="form-group">
                <label for="banyak" class="col-sm-2 control-label">Banyak</label>
                <div class="col-sm-10">
                    <?php
                     echo form_input(array('name' => 'quantity',
                                           'id' => 'quantity',
                                           'type' => 'text',
                                           'class' => 'form-control requiredTextField qty-input',
                                           'field-name' => 'Banyak'
                                           ));
                    ?>
                </div>
            </div>
             <div class="form-group">
                <label for="store_id" class="col-sm-2 control-label">Tanggal</label>
                  <div class="col-sm-3">
                    
                       
                        
                   <div class='input-group date' id='purchase_date'>
                     <?php echo form_input(array('name' => 'purchase_date',
                       'id' => 'purchase_date_val',
                       'type' => 'text',
                       'class' => 'form-control requiredTextField',
                       'field-name' => 'Tanggal'

                       )); ?>
                    <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar">
                        </span>
                    </span>
                  </div> 

                </div>
                 
            </div>
             <div class="form-group">
                <label for="store_id" class="col-sm-2 control-label">Harga</label>
                <div class="col-sm-10">
                    <?php
                     echo form_input(array('name' => 'price',
                                           'id' => 'price',
                                           'type' => 'text',
                                           'class' => 'form-control requiredTextField qty-input',
                                           'field-name' => 'Harga'
                                           ));
                    ?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">  
                    <button type="submit" name="btnAction" value="save" class="btn btn-primary">       Tambah Stok                                      
                    </button>
                    <a href="<?php echo base_url(SITE_ADMIN . '/stock/stocklet'); ?>" class="btn btn-default">Batal</a>
                </div>
            </div>
        </div>
      </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
<?php echo form_close(); ?>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>