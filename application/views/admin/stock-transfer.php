<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

echo form_open(base_url(SITE_ADMIN."/stock/transfer_add"), array('class' => 'form-horizontal form-ajax'));
?>

    <div class="col-lg-12" style="padding: 0 !important">
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
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="store_id" class="col-sm-2 control-label">Tujuan</label>
                                    <div class="col-sm-8">
                                        <?php
                                            echo form_dropdown('store_id', array($data_store->id => $data_store->store_name ),"", 'field-name = "Store Id" id="store_id"  class="form-control requiredDropdown" autocomplete="off"  ');
                                             echo '<input type="hidden" name="origin_outlet_id" id="origin_outlet_id" value="' . $origin_outlet_id . '"/>';
                                        ?>
                                    </div>
                                </div>
                                 <div class="form-group">
                                    
                                    <div class="col-sm-8 col-md-offset-2">
                                        <?php

                                        echo form_dropdown('outlet_id', $outlets,"", 'field-name = "Store" id="outlet_id"  class="form-control requiredDropdown" autocomplete="off"');
                                        ?>
                                    </div>

                                    <?php
                                       echo form_input($transfers);
                                    ?>
                                </div>
                                <div class="form-group">
                                <label for="store_id" class="col-sm-2 control-label">Transfer</label>
                                    <div class="col-sm-8">
                                        <div class="col-sm-5 ">
                                            <div class="row" id="inventories">
                                                 <table class="bill-table" >
                                                    <thead>
                                                        <tr>
                                                            <th  >Nama</th>
                                                            <th class="border-side"  >Banyak</th>
                                                        </tr>
                                                        <tr>
                                                          <td colspan="2">
                                                            <input type="text" class="form-control search" >
                                                          </td>
                                                        </tr>
                                                    </thead>
                                                </table>
                                                <div id="" style="height: 364px;overflow-y: auto">
                                                    <table class="bill-table" id="bill-table-left">
                                                       
                                                        <tbody class="list">
                                                        <?php 
                                                      foreach ($data_stocks as $stock) {
                                                          # code...
                                                      ?>
                                                            <tr id="<?php echo $stock->id."_".$stock->uom_id ;?>">
                                                            <td class="name"><?php echo $stock->name." ".$stock->code?></td>
                                                            <td  ><?php echo $stock->quantity ?></td>
                                                           <td  style="display:none;"><?php echo $stock->price?></td>
                                                       </tr>
                                                         <?php }
                                                        ?>
                                                     
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                         
                                        <div class="col-sm-2 ">
                                         <div class="row">
                                            <div class="splitter-container">
                                                <a class="btn btn-splitter  " id="split-all-right"><i class="fa fa-angle-double-right rotate-up"></i></a>
                                                <a class="btn btn-splitter  " id="split-choose-right" rel="popup-right"><i class="fa fa-angle-right rotate-up"></i> *</a>
                                                <a class="btn btn-splitter  " id="split-single-right"><i class="fa fa-angle-right rotate-up"></i> 1</a>
                                                <a class="btn btn-splitter  " id="split-single-left"><i class="fa fa-angle-left rotate-down"></i> 1</a>
                                                <a class="btn btn-splitter  " id="split-choose-left" rel="popup-left"><i class="fa fa-angle-left rotate-down"></i> *</a>
                                                <a class="btn btn-splitter  " id="split-all-left"><i class="fa fa-angle-double-left rotate-down"></i></a>
                                            </div>
                                         </div>
                                         </div>
                                        
                                        <div class="col-sm-5 ">
                                            <div class="row">
                                                 <table class="bill-table" >
                                                    <thead>
                                                        <tr>
                                                            <th  >Nama</th>
                                                            <th class="border-side"  >Banyak</th>
                                                           
                                                        </tr>
                                                    </thead>
                                                </table>
                                                <div id="" style="height: 364px;overflow-y: auto">
                                                    <table class="bill-table" id="bill-table-right">
                                                        <tbody>
                                                         
                                                     
                                                     
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>  
                                </div>
                                <div class="form-group">
                                    <div class="text-center">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"> Transfer                                      
                                                 </button>
                                        
                                        <a href="<?php echo base_url(SITE_ADMIN . '/stock/stocklet'); ?>"
                                           class="btn btn-default">Batal</a>
                                    </div>
                                </div>

                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>



            
        </div>
    </div>
    <div class="popup-block popup-choose-right" style="display:none;">
        <div class="col-lg-12   input-right">
            <div class="col-lg-12">
                <div class="row">
                    <div class="title-bg">
                        <h4 class="title-name">Quantity</h4>
                    </div>
                    
                    <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                        <input class="form-control qty-input" type="text" name="value" id="right_value"/>
                       
                        <div class="col-lg-12">
                            <div class="row">
                            <a  class="btn btn-std pull-right btn-cancel">Batal</a>
                            <a   class="btn btn-std pull-right" id="btn-ok-right"><i class="fa fa-check"></i> OK</a>
                            </div>
                        </div>
                    </div>
                   

                </div>
            </div>
        </div>
    </div>

    <div class="popup-block popup-choose-left" style="display:none;">
        <div class="col-lg-12   input-left">
            <div class="col-lg-12">
                <div class="row">
                    <div class="title-bg">
                        <h4 class="title-name">Quantity</h4>
                    </div>
                    
                    <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                        <input class="form-control qty-input" type="text" name="value" id="left_value"/>
                       
                        <div class="col-lg-12">
                            <div class="row">
                            <a   class="btn btn-std pull-right btn-cancel">Batal</a>
                            <a  class="btn btn-std pull-right" id="btn-ok-left"><i class="fa fa-check"></i> OK</a>
                            </div>
                        </div>
                    </div>
                   

                </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>