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
                                    <label for="name" class="col-sm-3 control-label">Jenis Pengeluaran</label>
                                    <div class="col-sm-6">
                                        <?php echo form_dropdown('ge_id', $ge_id, $this->input->post('ge_id'), 'field-name = "Jenis Pengeluaran" class="form-control requiredDropdown" autocomplete="off"'); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-sm-3 control-label">Deskripsi</label>
                                    <div class="col-sm-6">
                                        <?php echo form_textarea($description); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-sm-3 control-label">Jumlah Pengeluaran</label>
                                    <div class="col-sm-6">
                                        <?php echo form_input($amount); ?>
                                    </div>
                                </div>
                                <?php   if($is_accounting_module_active){ ?>
                                <div class="form-group">
                                    <label for="accounts" class="col-sm-3 control-label">Akun</label>

                                    <div class="col-sm-6">
                                        <?php 
                                            echo form_dropdown('account_id', $accounts,"", 'id="account_id" field-name= "Akun" class="form-control requiredDropdown select2" autocomplete="off"'); 
                                         ?>
                                    </div>
                                </div>
                               <?php }?>
                                <div class="form-group">
                                    <div class="col-sm-offset-2 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        <button type="submit" name="btnAction" value="save_exit"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                        </button>
                                        <a href="<?php echo base_url(SITE_ADMIN . '/general_expenses'); ?>"
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

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>