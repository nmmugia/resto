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
          <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_loan'); ?>">Kelola Pinjaman</a></li> 
          <li class="active"><?php echo $subtitle;?></li>
      </ol>
      <div class="row">
          <div class="col-lg-6 col-sm-offset-3">
              <div class="panel panel-default">
                  <div class="panel-body">
                       <div class="row">
                        <?php 
                              echo form_open_multipart(
                                  base_url(SITE_ADMIN . '/hrd_loan/edit_loan'), 
                                  array('class' => 'form-horizontal form-ajax'));
                              ?>
                          <div class="col-lg-12">
                            <input type="hidden" name="loan_id" value="<?php echo $data_loan->id;?>">
                              <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-4 control-label">Nama Pegawai</label>
                                  <div class="col-sm-6">
                                   <?php echo form_dropdown('user_id', $data_users, 
                                            $data_loan->user_id, 
                                            'id="loan_user_id" field-name = "Nama Pegawai" 
                                            class="form-control requiredDropdown select2" autocomplete="on"');?>
                                  </div>
                              </div>
                               <div class="form-group" id="take_home_pay">
                                  <label for="inputEmail3" class="col-sm-4 control-label">Total Take Home</label>
                                  <div class="col-sm-6" id="take_home_pay_value"> 
                                  Rp. <?php echo number_format($data_last_job->total_take_home_pay, 0, "", ".");?>
                                  </div>
                                  <input type="hidden" name="total_take_home" id="total_take_home" value="<?php echo $data_last_job->total_take_home_pay;?>">
                              </div>
                                <div class="form-group">
                                  <label for="inputEmail3" class="col-sm-4 control-label">Jenis Pembayaran</label>
                                  <div class="col-sm-6">
                                   <?php echo form_dropdown('payment_option', $data_enum_loan_payments, 
                                            $data_loan->payment_option, 
                                            'id="payment_option" field-name = "Jenis Pembayaran" 
                                            class="form-control requiredDropdown" autocomplete="on"');?>
                                  </div>
                              </div> 
                            
                          </div>
                          <div class="col-lg-12" id="cash_bon"> 
                              <div class="panel panel-default">
                                <div class="panel-body">
                                  <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-4 control-label">Jumlah Pinjaman</label>

                                      <div class="col-sm-6">
                                        <div class="input-group">
                                         <div class="input-group-addon">Rp. </div>
                                         <input type="text" name="loan_total" 
                                         class="form-control requiredTextField qty-input" field-name="Jumlah Pinjaman" value="<?php echo  $data_loan->loan_total;?>">
                                       
                                      </div>
                                        
                                      </div>
                                  </div>     
                                  <div class="form-group">
                                      <label for="inputPassword3" class="col-sm-4 control-label">Banyak Cicilan</label>

                                      <div class="col-sm-6">
                                       
                                         <input type="text" name="instalment" id="instalment" class="form-control requiredTextField qty-input" field-name="Cicilan"
                                         value="<?php echo  $data_loan->instalment;?>">
                                      
                                        
                                      </div>
                                  </div>    
                                   
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
                                      <a href="<?php echo base_url(SITE_ADMIN . '/hrd_loan/'); ?>"
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