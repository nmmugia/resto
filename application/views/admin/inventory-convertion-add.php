<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
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
                                    <label for="store_id" class="col-sm-2 control-label">Pilih Resto</label>
                                    <div class="col-sm-10">
                                        <?php 
                                          echo form_dropdown('store_id', $store_lists, $this->input->post('store_id'), 
                                            'field-name = "Resto" class="form-control requiredDropdown" autocomplete="off"');
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="inventory_id" class="col-sm-2 control-label">Inventori</label>
                                    <div class="col-sm-10">
                                        <?php 
                                          echo form_dropdown('inventory_id', $inventories, $this->input->post('inventory_id'), 
                                            'id="convertion_inventory_id" field-name = "Inventory" class="select2 form-control requiredDropdown" autocomplete="off"');
                                        ?>
                                    </div>
                                </div>
                                <table class="table table-bordered" id="inventory_convertion_uom">
                                  <thead>
                                    <tr>
                                      <th>Dari Satuan</th>
                                      <th>Konversi</th>
                                      <th>Ke Satuan</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                    
                                  </tbody>
                                </table>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                      <button type="submit" name="btnAction" value="save" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                                      <button type="submit" name="btnAction" value="save_exit" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save_exit'); ?></button>
                                      <a href="<?php echo base_url(SITE_ADMIN . '/inventory_convertions'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php echo form_close(); ?>