<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

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
                                    <label for="floor_name" class="col-sm-3 control-label">Nama Komponen Gaji</label> 
                                    <div class="col-sm-7">
                                        <?php echo form_input($name); ?>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Status</label> 
                                    <div class="col-sm-7">
                                        <label class="radio-inline">
                                        <?php echo form_radio($is_enhancer_on); ?>
                                         <small>Penambah</small>
                                      </label>

                                      <label class="radio-inline">
                                        <?php echo form_radio($is_enhancer_off); ?>
                                         <small>Pengurang</small>
                                      </label>
                                    </div>
                                </div> 
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">kata Kunci</label> 
                                    <div class="col-sm-7">
                                        <?php echo form_textarea($key); ?>
                                    </div>
                                </div> 
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Rumus Default</label> 
                                    <div class="col-sm-7">
                                        <?php echo form_textarea($formula_default); ?>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        <button type="submit" name="btnAction" value="save_exit"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                        </button>  
                                        <a href="<?php echo base_url(SITE_ADMIN . '/hrd/setting_salary_component_list'); ?>"
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