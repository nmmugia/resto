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
                                            (int) $this->data['setting']['store_id'], 
                                            'id="store_id" field-name = "resto" 
                                            class="form-control requiredDropdown" disabled autocomplete="on"');
                                            ?>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Nama</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($name); ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">ID Member</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($member_id); ?>
                                        </div>
                                    </div>

                               

                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Negara</label>

                                        <div class="col-sm-10">
                                            <?php  

                                            echo form_dropdown('country_id', $country, 
                                                 (int) $form_data->country_id, 
                                                'id="country_id_chained" field-name = "negara" 
                                                class="form-control requiredDropdown" autocomplete="on"');
                                                  echo '<input type="hidden" name="country_id" value="' . (int)$form_data->country_id . '"/>';

                                                ?>
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Provinsi</label>

                                            <div class="col-sm-10">
                                              <?php  

                                              echo form_dropdown('province_id', $province, 
                                                (int) $form_data->province_id, 
                                                'id="province_id_chained" field-name = "provinsi" 
                                                class="form-control requiredDropdown" autocomplete="on"');

                                                ?>
                                            </div>
                                        </div>



                                        <div class="form-group">
                                            <label for="discount_value" class="col-sm-2 control-label">Kota</label>

                                            <div class="col-sm-10">
                                               <?php  

                                               echo form_dropdown('city_id', $city, 
                                                (int) $form_data->city_id, 
                                                'id="city_id_chained" field-name = "kota" 
                                                class="form-control requiredDropdown" autocomplete="on"');

                                                ?>
                                            </div>
                                        </div>


                                     <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Alamat</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($address); ?>
                                        </div>
                                    </div>


                                    <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Kode POS</label>

                                        <div class="col-sm-10">
                                            <?php echo form_input($postal_code); ?>
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
                                        <label for="discount_value" class="col-sm-2 control-label">Kategori Member</label>

                                        <div class="col-sm-10">
                                            <?php

                                            echo form_dropdown('member_category_id', $member_category, 
                                            $form_data->member_category_id, 
                                            'id="member_category_id" field-name = "kategori member" 
                                            class="form-control requiredDropdown" autocomplete="on"');

                                            ?>
                                        </div>
                                    </div>


                                     <div class="form-group">
                                        <label for="discount_value" class="col-sm-2 control-label">Tanggal Bergabung</label>

                                        <div class="col-sm-3">
                                            <?php echo form_input($join_date); ?>
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
                                        <a href="<?php echo base_url(SITE_ADMIN . '/member'); ?>"
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
<script data-main="<?php echo base_url('assets/js/main-member'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>