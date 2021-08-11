<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
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
                                    <label for="input_outlet_id" class="col-sm-2 control-label">Outlet</label>

                                    <div class="col-sm-10">
                                        <?php 
                                        echo form_dropdown('input_outlet_id', $ddl_outlet, $this->input->post('input_outlet_id'), 
                                            'field-name = "Outlet" class="form-control requiredDropdown" autocomplete="off"');

                                         ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="input_inventory_id" class="col-sm-2 control-label">Bahan</label>

                                    <div class="col-sm-10">
                                        <?php 
                                        echo form_dropdown('input_inventory_id', $ddl_inventory, $this->input->post('input_inventory_id'), 
                                            'field-name = "Bahan" class="form-control requiredDropdown" autocomplete="off"');

                                         ?>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="input_stock" class="col-sm-2 control-label">Jumlah</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($input_stock); ?>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="input_created_date" class="col-sm-2 control-label">Tanggal</label>

                                    <div class="col-sm-3">
                                        <?php echo form_input($input_created_date); ?>

                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label for="input_note" class="col-sm-2 control-label">Catatan</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($input_note); ?>
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
                                        <a href="<?php echo base_url(SITE_ADMIN . '/inventory/stock'); ?>"
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