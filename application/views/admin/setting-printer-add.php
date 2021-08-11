<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
<style type="text/css">
.ui-datepicker-trigger {
    margin: 4px;
}
</style>
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
                                    <label for="discount_name" class="col-sm-2 control-label">Printer Type</label>

                                    <div class="col-sm-10">                                                        
                                        <?php 
                                        echo form_dropdown('type_id', $ddl_printer_type, $printer_type_id,
                                            'id="ddl_printer_type" field-name = "tipe printer" 
                                            class="form-control " autocomplete="on" ');                     
                                        ?>
                                    </div>
                                </div>

                              
                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Nama Printer</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($name_printer); ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Nama Alias Printer</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($alias_name); ?>
                                    </div>
                                </div>
                            <div id="printer-detail">
                                <div class="form-group" id="outlet">
                                    <label for="discount_name" class="col-sm-2 control-label"><?php echo $this->lang->line('outlet_title');?></label>
                                    <div class="col-sm-10">                                                        
                                        <?php 
                                        echo form_dropdown('outlet_id', $ddl_outlet, $outlet_id,
                                            'id="group_chained" field-name = "parent" 
                                            class="form-control " autocomplete="on" ');                     
                                        ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Lebar</label>

                                    <div class="col-sm-10">
                                        <label class="radio-inline">
                                            <?php echo form_radio($printer_width_48); ?>
                                            <small>Lebar 48</small>
                                        </label>

                                        <label class="radio-inline">
                                            <?php echo form_radio($printer_width_72); ?>
                                            <small>Lebar 72</small>
                                        </label>
                                        <label class="radio-inline">
                                            <?php echo form_radio($printer_width_72_plus); ?>
                                            <small>Lebar 72+</small>
                                        </label>
                                        <label class="radio-inline">
                                            <?php echo form_radio($printer_generic); ?>
                                            <small>Generic</small>
                                        </label>
                                        <br>
                                        <small>Kertas Printer Lebar 48mm / 72mm / Generic</small>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tax_percentage" class="col-sm-2 control-label">Font Size</label>

                                    <div class="col-sm-10">
                                        <label class="radio-inline">
                                            <?php echo form_radio($font_size_bill_1); ?>
                                            <small>1</small>
                                        </label>

                                        <label class="radio-inline">
                                            <?php echo form_radio($font_size_bill_2); ?>
                                            <small>2</small>
                                        </label>
                                        <br>

                                        <small>ukuran font pada bill,semakin besar angka semakin besar font sizenya.</small>

                                    </div>
                                </div>

                                <div class="form-group" id="format_order">
                                    <label for="format_order" class="col-sm-2 control-label">Format Order</label>

                                    <div class="col-sm-10">
                                        <label class="radio-inline">
                                            <?php echo form_radio($format_order_1); ?>
                                            <small>1</small>
                                        </label>

                                        <label class="radio-inline">
                                            <?php echo form_radio($format_order_2); ?>
                                            <small>2</small>
                                        </label>
                                        <br>

                                        <small>ukuran font pada order,semakin besar angka semakin besar font sizenya.</small>

                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="discount_name" class="col-sm-2 control-label">Apakah Menggunakan Logo</label>

                                    <div class="col-sm-10">
                                        <label class="radio-inline">
                                            <?php echo form_radio($use_logo_yes); ?>
                                            <small>Ya</small>
                                        </label>

                                        <label class="radio-inline">
                                            <?php echo form_radio($use_logo_no); ?>
                                            <small>Tidak</small>
                                        </label> <br>
                                    </div>
                                </div>
                 
                                <div class="form-group" id="panel_store" >
                                    <label for="discount_name" class="col-sm-2 control-label">Meja</label>

                                    <div class="col-sm-10">
                                        <div class="row">
                                         <div class="col-xs-5">
                                            <select name="from" class="multiselect form-control" size="8" 
                                            multiple="multiple" 
                                            data-right="#multiselect_to_1" 
                                            data-right-all="#right_All_1" 
                                            data-right-selected="#right_Selected_1" 
                                            data-left-all="#left_All_1" 
                                            data-left-selected="#left_Selected_1">

                                            <?php 
                                                foreach ($tables as $key => $row) {
                                                    echo "<option value='".$row->id."'>".$row->table_name."</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>

                                        <div class="col-xs-2">
                                            <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                                            <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                                            <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                                            <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                                        </div>

                                        <input type="hidden" name="table_list" id="table_list" value="0" >
                                        <div class="col-xs-5">
                                            <select name="to" id="multiselect_to_1" class="form-control" size="8" multiple="multiple">
                                                <?php 
                                                foreach ($selected_table as $key => $row) {
                                                    echo "<option value='".$row->id."'>".$row->table_name."</option>";
                                                }
                                            ?>
                                            </select>
                                        </div>
                                    </div>
                                    </div>
                                </div>
                            </div>
                               
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        <button type="submit" name="btnAction" value="save_exit"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                        </button>
                                        <a href="<?php echo $cancel_url; ?>"
                                           class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
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
<?php echo form_close(); ?>
<script data-main="<?php echo base_url('assets/js/main-setting-printer'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>