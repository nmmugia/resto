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
                                    <label for="discount_name" class="col-sm-2 control-label">Resto</label>
                                    <div class="col-sm-10">
                                        <?php

                                        echo form_dropdown('store_id', $store, 
                                           $form_data->store_id, 
                                            'id="store_id" field-name = "resto" 
                                            class="form-control requiredDropdown" autocomplete="on"');
                                            ?>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Nama Perusahaan</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($company_name); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Penanggung Jawab</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($pic_name); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Email</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($email); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Alamat</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($address); ?>
                                        </div>
                                    </div>

 

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Telepon</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($land_phone); ?>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Handphone</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($mobile_phone); ?>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Uang Muka</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($down_payment); ?>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label for="is_banquet" class="col-sm-2 control-label">Banquet</label>

                                        <div class="col-sm-10">
                                            <div class="checkbox">
                                            <label>
                                              <input type="radio" name="is_use_banquet" value="1" <?php if($is_use_banquet == 1) echo "checked";?>> Ya
                                            </label>
                                            <label>
                                              <input type="radio" name="is_use_banquet" value="0" <?php if($is_use_banquet == 0) echo "checked";?>> Tidak
                                            </label>
                                          </div>
                                        </div>
                                    </div>
                                     <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">No Rekening</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($no_rec); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">A.n Bank</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($beneficary); ?>
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
                                        <a href="<?php echo base_url(SITE_ADMIN . '/order_company'); ?>"
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
<?php echo form_hidden('id', $form_data->id); ?>
<?php echo form_hidden($csrf); ?>
<?php echo form_close(); ?>
<script data-main="<?php echo base_url('assets/js/main-company'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>