<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

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
                                    <label for="store_name" class="col-sm-2 control-label">Store Name</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($store_name); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="store_address" class="col-sm-2 control-label">Store Address</label>

                                    <div class="col-sm-10">
                                        <?php echo form_textarea($store_address); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="store_phone" class="col-sm-2 control-label">Store Phone</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($store_phone); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="store_logo" class="col-sm-2 control-label">Logo</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($store_logo); ?>
                                        <small>*only JPG, JPEG dan PNG, max size 1 MB</small>
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
                                        <a href="<?php echo base_url(SITE_ADMIN . '/store'); ?>"
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