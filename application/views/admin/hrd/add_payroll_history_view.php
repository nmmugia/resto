<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<div class="col-lg-12">
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
    <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
    ?>
      <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/staff_list'); ?>">Staff</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/detail_staff/'.$employee_id); ?>"><?php echo $form_data->name;?></a></li>
        <li class="active">Tambah History Gaji</li>
    </ol>
    <style>
    .label{
        float:left;
    }
    </style>
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
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">NIP</label> 
                            </div> 
                            <div class="col-md-3">
                                <?php echo $form_data->nip; 
                                ?> 
                                <input type="hidden" name="employee_id" id="employee_id" value="<?php echo $employee_id; ?>"/>
                                <input type="hidden" name="jobs_id" id="jobs_id" value="<?php echo $form_data->jobs_id; ?>"/>
                                 <input type="hidden" name="job_history_id" id="job_history_id" value="<?php echo $form_data->job_history_id; ?>"/>
                            </div>  
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Nama</label> 
                            </div> 
                            <div class="col-md-3">
                                <?php echo $form_data->name;?> 
                            </div>  
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Jabatan</label> 
                            </div> 
                            <div class="col-md-3">
                                <?php echo $form_data->jobs_name;?> 
                            </div>  
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Period</label> 
                            </div> 
                            <div class="col-md-3">
                                <div class='input-group date ' id='period-date'>
                                  <input type="text" class="form-control no-special-char requiredTextField" field-name="Period" name="period" onkeydown="return false" id="period-value" value="<?php echo date("m-Y") ?>"> 
                                  <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div> 
                            </div>  
                        </div>
                     </div>
                </div>
            <!-- /.panel -->
            </div>
            <div id="payroll_history_content"></div>
<?php /*
            <div class="panel panel-default">
                <div class="panel-heading">Penerimaan</div>
                <div class="panel-body">
                    <div class="row">
                       
                        <div class="col-lg-12">
                            <table class="table table-striped" id="enhancer_container"
                                   style="margin: 20px 0!important;">
                                <?php
                                $counterEnhancer = 0;
                                if (! empty($data_enhancer_jobs_component)) {
                                 
                                    foreach ($data_enhancer_jobs_component as $po) { 
                                        add_enhancer_func($po, $counterEnhancer,$form_data, $enhancer_sal_component_dropdwn);
                                        $counterEnhancer++;
                                    }
                                } ?>
                                 <input type="hidden" id="count_enhancer" value='<?php echo $counterEnhancer-1; ?>'/>
                            </table> 
                        </div>
                        <div class="col-lg-4 col-md-offset-5">
                             <a id="add_enhancer" href="#" class="btn btn-default">  Tambah Komponen</a>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>


            <div class="panel panel-default">
                    <div class="panel-heading">Pengurang</div>
                    <div class="panel-body">
                        <div class="row">
                           
                            <div class="col-lg-12">
                                <table class="table table-striped" id="subtrahend_container"
                                       style="margin: 20px 0!important;">
                                    <?php
                                    $counterSubtrahend = 0;
                                    if (! empty($data_subtrahend_jobs_component)) { 
                                        foreach ($data_subtrahend_jobs_component as $po) { 
                                            add_substrahend_func($po, $counterSubtrahend,$form_data, $substrahend_sal_component_dropdwn,$total_pinjaman);
                                            $counterSubtrahend++;
                                        }
                                    } ?>
                                     <input type="hidden" id="count_subtrahend" value='<?php echo $counterSubtrahend-1; ?>'/>
                                </table> 
                            </div>


                            <div class="col-lg-4 col-md-offset-5">
                                <a class="btn btn-default" id="add_subtrahend">Tambah Komponen</a>
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
*/ ?>
             <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                       <!--  <div class="col-lg-12">
                             <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Total Home Pay</label> 
                              </div> 
                              <div class="col-md-3">
                                <label for="floor_name" class="col-sm-3 control-label"> <?php echo $form_data->jobs_name;?> </label> 
                            </div> 
                            
                        </div> -->
                        <!-- /.row (nested) -->
                    </div>
                    <!-- /.panel-body -->
                </div>
            <!-- /.panel -->
            </div>

             <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                       <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8"> 
                                <button type="submit" name="btnAction" value="save_exit"
                                        class="btn btn-primary">
                                    <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                </button>  
                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/detail_staff/'.$employee_id); ?>"
                                   class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                            </div>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
            <!-- /.panel -->
            </div>
         </div>
     </div>
    <!-- /.panel --> 
 <?php echo form_close(); ?>
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
 
<input type="hidden" id="data_enhancer_salary_component" value='<?php echo json_encode($data_enhancer_salary_component) ?>'/>
<input type="hidden" id="data_substrahend_salary_component" value='<?php echo json_encode($data_substrahend_salary_component) ?>'/>
