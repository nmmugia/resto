<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>

<div class="col-lg-6">
    <div class="panel panel-default"> 
    <div class="panel-body"> 
        <div class="row">
            <div class="col-lg-12 form-group">
                <div class="col-md-4">
                    <label for="floor_name" class=" control-label">NIP</label> 
                </div> 
                <div class="col-md-8">
                   <?php echo $data_users->nip;?>
                </div>  
            </div> 
        </div>
        <div class="row">
            <div class="col-lg-12 form-group">
                <div class="col-md-4">
                    <label for="floor_name" class=" control-label">Nama</label> 
                </div> 
                 <div class="col-md-8">
                <?php echo $data_users->name;?>
                </div>  
            </div> 
        </div>
        <div class="row">
            <div class="col-lg-12 form-group">
                <div class="col-md-4">
                    <label for="floor_name" class=" control-label">Jabatan</label> 
                </div> 
                 <div class="col-md-8">
                  Waiter
                </div>  
            </div> 
        </div> 
    </div>
    </div>
</div>
<div class="col-lg-6">
    <div class="panel panel-default"> 
        <div class="panel-body"> 
            <div class="result">
                <?php
                if (!empty($message_success)) {
                    echo '<div class="alert alert-success" role="alert">';
                    echo $message_success;
                    echo '</div>';
                }
                if (!empty($message)) {
                    echo '<div class="alert alert-danger" role="alert">';
                    echo $message;
                    echo '</div>';
                }
                ?>
            </div>
            <?php 
                echo form_open_multipart(
                  base_url(SITE_ADMIN . '/hrd_attendance/update_status_attendance'), 
                  array('class' => 'form-horizontal form-ajax'));
              ?>
            <div class="form-group">
                <div class="col-xs-12">
                <div class="row">
                    <div class="checkbox col-xs-3">
                      <label for="floor_name" class=" control-label">Tindakan</label> 
                    </div>
                    <div class="checkbox col-xs-7">
                        <?php
                        $status_attendance = 0; 
                        if(!empty($data_attendance_today)){

                            $status_attendance = $data_attendance_today->enum_status_attendance;
                        }
                        echo form_dropdown('status_attendances', $status_attendances, $status_attendance,'id="status_attendances" field-name = "Status Absensi" class="form-control requiredDropdown" autocomplete="off"');
                   ?>
                    </div>
                </div>
                <div class="row">
                    <div class="checkbox col-xs-3">
                      <label for="" class=" control-label">Lampiran</label> 
                    </div>
                    <div class="checkbox col-xs-7">
                        <?php echo form_input($file_url); ?>
                    </div>
                </div> 
                <div class="row">
                    <div class="checkbox col-xs-3">
                      <label for="" class=" control-label">Note</label> 
                    </div>
                    <div class="checkbox col-xs-7">
                        <?php echo form_textarea($note); ?>
                    </div>
                </div>
                </div>
            </div>
            <input type="hidden" name="user_id" value="<?php echo $employee_id;?>">
            <input type="hidden" name="date" value="<?php echo $date;?>">
            
           <!--   <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Lampiran</label> 
                    </div> 
                     <div class="col-md-6">
                     <input type="file" class="form-control">
                    </div>  
                </div> 
            </div> -->

            <div class="form-group">
                <div class="col-xs-12">
                <div class="row"> 
                    <div class="checkbox col-xs-6 col-sm-offset-4">
                      <button type="submit" name="btnAction" value="save_exit"
                            class="btn btn-primary">
                        <?php echo $this->lang->line('ds_submit_save'); ?>
                    </button> 
                    </div>
                </div>
                </div>
            </div> 
             <?php echo form_close(); ?>
        </div> 

    </div>
</div>
<div class="col-lg-12">  
    <div class="panel panel-default"> 
        <div class="panel-body"> 
         <div class="col-lg-12">
            <!-- Nav tabs -->
              <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#graphic" aria-controls="home" role="tab" data-toggle="tab">Grafik Absensi</a></li>
                <li role="presentation"><a href="#statistic" aria-controls="statistic" role="tab" data-toggle="tab">Statistik Absensi</a></li>
                <li role="presentation"><a href="#log" aria-controls="log" role="tab" data-toggle="tab">Log Absensi</a></li>
                <!-- <li role="presentation"><a href="#performance" aria-controls="performance" role="tab" data-toggle="tab">Status Performance</a></li> -->
                <li role="presentation"><a href="#quota" aria-controls="quota" role="tab" data-toggle="tab">Jatah</a></li>
              </ul>

              <!-- Tab panes -->
              <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="graphic"> <br>
                  <?php $this->load->view('admin/hrd/performance_graphic_view.php'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="statistic"> <br>
                     <?php $this->load->view('admin/hrd/performance_statistic.php'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="log"> <br>
                        <?php $this->load->view('admin/hrd/history_employee_attendance.php'); ?>
                </div>
                <div role="tabpanel" class="tab-pane" id="performance"> <br>
                    <?php $this->load->view('admin/hrd/performance_status_view.php'); ?>
                    
                </div>
                 <div role="tabpanel" class="tab-pane" id="quota">  <br>
                     <?php $this->load->view('admin/hrd/performance_quota.php'); ?>
                </div>
              </div>
            </div>
        </div> 
    </div> 
</div> 

<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/> 

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>
<!-- /.col-lg-12 -->