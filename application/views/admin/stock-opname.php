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
        echo form_open(base_url(SITE_ADMIN."/stock/opname_add"), array('class' => 'form-horizontal form-ajax'));
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
    <div class="panel panel-default" id="form-opname-single">
        <div class="panel-body">
            <div class="form-group">
                <label for="sotre" class="col-sm-2 control-label">Nama Barang</label>
                <div class="col-sm-10">
                 <?php
                    echo $detail_stock[0]->name;
                    echo '<input type="hidden" name="outlet_id" id="outlet_id" value="' . $outlet_id . '"/>';
                     echo '<input type="hidden" name="store_id" id="store_id" value="' . $detail_stock[0]->store_id. '"/>';
                     echo '<input type="hidden" name="inventory_id" id="inventory_id" value="' . $inventory_id . '"/>';
                     echo '<input type="hidden" name="uom_id" id="uom_id" value="' . $uom_id . '"/>';
                ?>
                </div>
            </div>
           
            <div class="form-group">
                <label for="store_id" class="col-sm-2 control-label">Waktu</label>
                  <div class="col-sm-3">
                   <?php echo date('Y-m-d H:i');?>
                  </div> 
               
                
            </div>
            <div class="form-group">
                <label for="banyak" class="col-sm-2 control-label">Banyak Di sistem</label>
                <div class="col-sm-10">
                  <label id="jumlah_stok"><?php echo $detail_stock[0]->jumlah_stok;?></label>

                    <?php
                      echo $detail_stock[0]->code;
                    ?>
                </div>
            </div>
            
            <div class="form-group">
                <label for="banyak" class="col-sm-2 control-label">Banyak di Opname</label>
                <div class="col-sm-10">
                    <?php
                     echo form_input(array('name' => 'quantity_opname',
                                           'id' => 'quantity_opname',
                                           'type' => 'text',
                                           'class' => 'form-control requiredTextField qty-input',
                                           'field-name' => 'Total Opname',
                                           'placeholder'=>""
                                           ));
                    ?>
                </div>
            </div>
             <div class="form-group price" style="display:none;">
                <label for="banyak" class="col-sm-2 control-label">Harga</label>
                <div class="col-sm-10">
                    <?php
                      echo form_input(array('name' => 'price',
                                           'id' => 'price',
                                           'type' => 'text',
                                           'class' => 'form-control',
                                           'field-name' => 'Harga',
                                           'placeholder'=>""
                                           ));
                    ?>
                </div>
            </div>
             <div class="form-group">
                <label for="store_id" class="col-sm-2 control-label">Perbedaan</label>
                <div class="col-sm-10">
                    <label id="diff_opname"></label>
                    <?php echo '<input type="hidden" name="difference" id="difference"  class="requiredTextField" field-name="Perbedaaan"/>';?>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">  
                    <button type="submit" name="btnAction" value="save" class="btn btn-primary">       Ubah Stok                                      
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