<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
    <style>
    .control-label{ 
        text-align: left !important;
    }

    .control-point{
           margin-top: 6px;
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
                                    <label for="floor_name" class="col-sm-3 control-label">Pegawai</label> 
                                    <div class="col-sm-7">
                                        <?php    echo form_dropdown('user_id', $users, '', 
                                              'id="user_id" field-name = "Pegawai" 
                                              class="def-select form-control requiredDropdown" autocomplete="on"'); ?>
                                    </div>
                                </div>  
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Template</label> 
                                    <div class="col-sm-7">
                                          <?php    echo form_dropdown('template_id', $template_appraisals, '', 
                                              'id="template_appraisal" field-name = "Template" 
                                              class="def-select form-control requiredDropdown" autocomplete="on"'); ?>
                                    </div>
                                </div>   

                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Period</label> 
                                    <div class="col-sm-7">
                                          <div class='input-group date ' id='period-generate-date'>
                                              <input type="text" class="form-control no-special-char requiredTextField" field-name="Period" name="period" onkeydown="return false"> 
                                              <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-calendar"></span>
                                              </span>
                                            </div> 
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Total Point</label> 
                                    <div class="col-sm-7">
                                        <div class="col-sm-1">
                                            <label for="floor_name" class="col-sm-3 control-label" id="total_grade_appraisal">0</label> 
                                        </div>

                                         <div class="col-sm-1">
                                          <label for="floor_name" class="col-sm-3 control-label">Of</label> 
                                        </div>
                                         <div class="col-sm-1">
                                        <label for="floor_name" class="col-sm-3 control-label " id="max_grade_appraisal">0</label> 

                                        </div>
                                        
                                    </div>
                                </div>   
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Nilai</label> 
                                    <div class="col-sm-7">
                                          <label for="floor_name" class="col-sm-3 control-label" id="total_precentage_appraisal">0%</label> 
                                    </div>
                                </div>   
                            </div>
                           
                             <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="category_list">
                                         
                                        </div> 
                                    </div>  
                                </div>  
                            </div>
                             <div class="col-lg-12">
                              <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Keterangan</label> 
                                    <div class="col-sm-7">
                                       <textarea class="form-control" name="description"></textarea>
                                    </div>
                                </div>  
                            </div>
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                         <div class="form-group">
                                            <div class="col-sm-offset-4 col-sm-6">
                                                <button type="submit" name="btnAction" value="save"
                                                        class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                                </button>
                                                <button type="submit" name="btnAction" value="save_exit"
                                                        class="btn btn-primary">
                                                    <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                                </button>  
                                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_appraisal/process_appraisal'); ?>"
                                                   class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                            </div>
                                        </div>
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



<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>