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
                                    <label for="server_sync_url" class="col-sm-2 control-label">URL</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($server_sync_url); ?> 
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_start_time" class="col-sm-2 control-label">Waktu</label>

                                    <div class="col-sm-10">
                                        <div class="input-daterange input-group" id="datepickertime">
                                            <?php echo form_input($server_start_time); ?>
                                            <span class="input-group-addon">to</span>
                                            <?php echo form_input($server_end_time); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="server_interval" class="col-sm-2 control-label">Interval(menit)</label>

                                    <div class="col-sm-10">
                                        <?php echo form_input($server_interval); ?>
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
                                        <a href="<?php echo base_url(SITE_ADMIN . '/hrd_attendance/server_sync'); ?>"
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