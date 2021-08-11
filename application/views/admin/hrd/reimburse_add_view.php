<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
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
          <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_reimburse'); ?>">Kelola Reimburse</a></li> 
          <li class="active"><?php echo $subtitle;?></li>
      </ol>
      <div class="row">
          <div class="col-lg-12">
              <div class="panel panel-default">
                  <div class="panel-body">
                       <div class="row">
                        <?php 
                              echo form_open_multipart(
                                  base_url(SITE_ADMIN . '/hrd_reimburse/add_reimburse'), 
                                  array('class' => 'form-horizontal form-ajax'));
                              ?>
                          <div class="col-lg-12">
                           
                              <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-2 control-label">Nama Pegawai</label>
                                  <div class="col-sm-6">
                                   <?php echo form_dropdown('user_id', $data_users, 
                                            $this->input->post("user_id"), 
                                            'id="table_id" field-name = "Nama Pegawai" 
                                            class="form-control requiredDropdown select2" autocomplete="on"');
                                  ?>
                                  </div>
                              </div>
                              <!-- <div class="form-group">
                                  <label for="inputPassword3" class="col-sm-2 control-label">Status Kepegawaian</label>
                                  <div class="col-sm-6" id="status_employee">
                                    
                                  </div>
                              </div>    
                              <div class="form-group">
                                  <label for="inputPassword3" class="col-sm-2 control-label">Masa Jabatan</label>
                                  <div class="col-sm-6" id="jobs_date">
                                    
                                  </div>
                              </div>   
                              <div class="form-group">
                                  <label for="inputPassword3" class="col-sm-2 control-label">Sisa Reimburse</label>
                                  <div class="col-sm-6" id="jobs_date">
                                    
                                  </div>
                              </div>    -->
                            
                          </div>
                           <div class="col-lg-12">
                              
                              <div class="panel panel-default">
                                <div class="panel-body">
                                  <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-2 control-label">Jumlah</label>

                                      <div class="col-sm-6">
                                        <div class="input-group">
                                         <div class="input-group-addon">Rp. </div>
                                         <input type="text" name="total_reimburse" class="form-control requiredTextField qty-input" field-name="Jumlah" value="<?php echo $this->input->post("total_reimburse") ?>">
                                       
                                      </div>
                                        
                                      </div>
                                  </div>     
                                  <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-2 control-label">Keterangan</label>
                                      <div class="col-sm-6" >
                                        <textarea name="note" class="form-control "><?php echo $this->input->post("note") ?></textarea>
                                      </div>
                                  </div>     
                                 <!--  <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-2 control-label">Lampiran</label>
                                      <div class="col-sm-6">
                                        <input type="file" name="file" class="form-control ">
                                      </div>
                                  </div>      -->
                                  <div class="form-group">
                                    <div class="col-sm-8 col-sm-offset-4" >
                                      <button type="submit" name="btnAction" value="save_exit"
                                              class="btn btn-primary">
                                          <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                      </button>  
                                      <a href="<?php echo base_url(SITE_ADMIN . '/hrd_reimburse/'); ?>"
                                         class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                    </div>
                                  </div>
                                </div>
                              </div>    
                            <?php echo form_close(); ?> 
                          </div>
                      </div>
                  </div>
                  <!-- /.panel -->
              </div>
          </div>
      </div>
  </div>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>