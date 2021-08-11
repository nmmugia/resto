<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Fazhal Darul
 * Date: 12/16/2014
 * Time: 11:05 AM
 */
?>
<style >
  .form-horizontal .control-label{
    text-align:left; 
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
      <ol class="breadcrumb">
          <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
          <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_overtime'); ?>">Daftar Lembur</a></li> 
          <li class="active"><?php echo $subtitle;?></li>
      </ol>
      <div class="row">
          <div class="col-lg-12">
              <div class="panel panel-default">
                  <div class="panel-body">
                       <div class="row">
                        <?php 
                              echo form_open_multipart(
                                  base_url(SITE_ADMIN . '/hrd_overtime/add_overtime'), 
                                  array('class' => 'form-horizontal form-ajax'));
                              ?>
                          <div class="col-lg-12">
                           
                              <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Nama Pegawai</label>
                                  <div class="col-sm-6">
                                   <?php echo form_dropdown('user_id', $data_users, 
                                            "", 
                                            'id="loan_user_id" field-name = "Nama Pegawai" 
                                            class="form-control requiredDropdown select2" autocomplete="on"');?>
                                  </div>
                              </div>
                               <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Jam Mulai</label>
                                  <div class="col-sm-6">
                                   <div class='input-group date' id='start_time'>
                                    <input type="text" name="start_time" class="form-control" id="over_start_time" onkeydown="return false" value="<?php echo date("H:i:s") ?>"> 
                                    <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                  </div> 
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Jam Selesai</label>
                                  <div class="col-sm-6">
                                   <div class='input-group date' id='end_time'>
                                    <input type="text" name="end_time" class="form-control" id="over_end_time" onkeydown="return false" value="<?php echo date("H:i:s") ?>"> 
                                    <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-time"></span>
                                    </span>
                                  </div> 
                                  </div>
                              </div>
                              <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Note</label>
                                  <div class="col-sm-6">
                                   <?php echo form_textarea($note); ?>
                                  </div>
                              </div> 
                            
                          </div>
                   
                          <div class="col-lg-12"> 
                              <div class="panel panel-default">
                                <div class="panel-body">  
                                  <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-4" >
                                      <button type="submit" name="btnAction" value="save_exit"
                                              class="btn btn-primary">
                                          <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                      </button>  
                                      <a href="<?php echo base_url(SITE_ADMIN . '/hrd_overtime/'); ?>"
                                         class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                    </div>
                                  </div>
                                </div>
                              </div>  
                          </div>
                          <?php echo form_close(); ?> 
                      </div>
                  </div>
                  <!-- /.panel -->
              </div>
          </div>
      </div>
  </div>