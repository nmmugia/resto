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
                                <div class="panel panel-default">
                                    <div class="panel-heading"> 
                                        Pemberi Appraisal
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label for="floor_name" class="col-sm-2 control-label">Jabatan</label> 
                                            <div class="col-sm-7">
                                               <select name="grantor_appraisal" class="form-control">
                                                <?php 
                                                print_r($jobs_no_grantors);
                                                foreach ($jobs_no_grantors as $job) { ?> 
                                                    <option value="<?php echo $job->id;?>"><?php echo $job->jobs_name;?></option>
                                                <?php }?>
                                               </select>
                                            </div>
                                        </div>  
                                    </div>  
                                </div>  
                            </div>
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"> 
                                        Penerima Appraisal
                                    </div>
                                    <div class="panel-body" id="container-receiver">
                                        <div class="panel panel-default" id="receiver-0">
                                            <div class="panel-heading"> 
                                                
                                            </div>
                                            <div class="panel-body">
                                                <div class="form-group">
                                                    <label for="floor_name" class="col-sm-2 control-label">Jabatan</label> 
                                                    <div class="col-sm-7">
                                                       <select name="receiver[0][job]" class="form-control">
                                                        <?php foreach ($jobs as $job) { ?> 
                                                            <option value="<?php echo $job->id;?>"  ><?php echo $job->jobs_name;?></option>
                                                        <?php }?>
                                                       </select>
                                                    </div>
                                                </div>  
                                                <div class="form-group">
                                                    <label for="floor_name" class="col-sm-2 control-label">Template Appraisal</label> 
                                                    <div class="col-sm-7">
                                                       <select  class="form-control" name="receiver[0][template]">
                                                        <?php foreach ($template_appraisals as $temp) { ?> 
                                                            <option value="<?php echo $temp->id;?>" ><?php echo $temp->name;?></option>
                                                        <?php }?>
                                                       </select>
                                                    </div>
                                                </div>  
                                            </div>
                                        </div>
                                        
                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-2 col-sm-offset-5">
                                             <a  id="add_receiver_appraisal"   class="btn btn-default">Tambah</a>
                                        </div>
                                     </div>  
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading"> 
                                        Approval
                                    </div>
                                    <div class="panel-body">
                                         
                                         <div class="form-group">
                                            <label for="floor_name" class="col-sm-2 control-label">Appoval Oleh</label> 
                                            <div class="col-sm-7">
                                              <select name="approval[]" class="multiselect form-control" size="8" 
                                                multiple="multiple"  id="generate-jobs">
                                                <?php foreach ($jobs as $job) { ?>
                                                    <option value='<?php echo $job->id?>'><?php echo $job->jobs_name;?></option>
                                                <?php }?>
                                               
                                                         
                                         </select>
                                            </div>
                                        </div>  
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
                                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_appraisal/setting_template_appraisal'); ?>"
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
<input type="hidden" id="jobs_data" value='<?php echo json_encode($jobs);?>'>
<input type="hidden" id="appraisal_template_data" value='<?php echo json_encode($template_appraisals);?>'>
<?php echo form_close(); ?>