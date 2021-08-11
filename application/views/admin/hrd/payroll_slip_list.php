<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<div class="col-lg-12">
  <div class="panel panel-default"> 
    <div class="panel-body">
      <div class="col-lg-12">
        <?php echo form_open("",array("id"=>"form_payroll")); ?>
        <div class="row">
          <div class="col-lg-4">
            <div class="form-group">
              <div class="col-md-3">
                <label class="control-label">Period</label> 
              </div> 
              <div class="col-md-9">
                <div class='input-group date ' id='period-date'>
                  <input type="text" class="form-control no-special-char requiredTextField" field-name="Period" name="periode" onkeydown="return false" id="generate-period-value" value="<?php echo date("m-Y") ?>"> 
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </div> 
              <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group">
              <div class="col-md-3">
                <label class="control-label">Jabatan</label> 
              </div> 
              <div class="col-md-9">
                <select name="from" class="form-control select2"  id="generate-jobs">
                  <option value=''>Semua Jabatan</option>
                  <?php foreach ($all_jobs as $job): ?>
                    <option value='<?php echo $job->id?>'><?php echo $job->jobs_name;?></option>
                  <?php endforeach; ?>
                 </select>
              </div>  
              <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            <div class="form-group">
              <div class="col-md-3">
                <label class="control-label">Status</label> 
              </div> 
              <div class="col-md-9">
                <select name="from" class="form-control select2" id="generate-status"> 
                  <option value=''>Semua Status</option>
                  <?php foreach ($all_employee_affairs as $e_affairs): ?>
                    <option value='<?php echo $e_affairs->id?>'><?php echo $e_affairs->name;?></option>
                  <?php endforeach; ?>            
                </select>
              </div>  
              <div class="clearfix"></div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="form-group" id="panel_store" >
              <div class="row">
                <div class="col-xs-5">
                  <select class="multiselect form-control" size="10" 
                    multiple="multiple" 
                    data-right="#multiselect_to_1" 
                    data-right-all="#right_All_1" 
                    data-right-selected="#right_Selected_1" 
                    data-left-all="#left_All_1" 
                    data-left-selected="#left_Selected_1" id="employee_list">
                    <?php foreach ($all_employees as $l): ?>
                      <option value='<?php echo $l->id?>'><?php echo $l->name;?></option>
                    <?php endforeach; ?>        
                  </select>
                </div>
                <div class="col-xs-2">
                  <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                  <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                  <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                  <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
                </div>
                <div class="col-xs-5">
                  <select name="employees[]" id="multiselect_to_1" class="form-control" size="10" multiple="multiple"></select>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="text-center">
          <a href="javascript:void(0);" class="btn btn-primary" id="save-preview-slip">Preview Slip</a>
          <a href="javascript:void(0);" class="btn btn-primary" id="save-generate-slip">Generate Slip</a>
          <a href="javascript:void(0);" class="btn btn-primary" id="save-download-slip">Download Csv</a>
          <a href="javascript:void(0);" class="btn btn-primary" id="save-print-slip">Cetak Slip</a>
        </div> 
        <?php echo form_close(); ?>
      </div> 
    </div>
  </div>
</div>
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
  
    <div class="panel panel-default">
        
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-payroll-slip">
                <thead>
                <tr> 
                    <th width="20%">Nama</th>
                     <th width="20%"> Kantor Cabang</th>
                    <th width="20%">Jabatan</th>  
                    <th width="20%">Periode</th>
                    <th width="20%">Jumlah</th>
                    <th width="20%">View</th>
                   <!--  <th style="text-align: center" ><?php echo $this->lang->line('column_action'); ?></th> -->
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlSlip" value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
 </div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
 <?php /*
<div class="modal fade" id="generate-slip-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Generate Slip Gaji</h4>
      </div>
      <div class="modal-body"> 
            <div class="form-group error-message">
                
            </div>
            <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Period</label> 
                    </div> 
                    <div class="col-md-6">
                        <div class='input-group date ' id='period-generate-date'>
                          <input type="text" class="form-control no-special-char requiredTextField" field-name="Period" name="period" onkeydown="return false" id="generate-period-value"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>  
                </div>
             </div>
            <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Jabatan</label> 
                    </div> 
                    <div class="col-md-6">
                         <select name="from" class="multiselect form-control" size="8" 
                            multiple="multiple"  id="generate-jobs">
                            <?php foreach ($all_jobs as $job) { ?>
                                <option value='<?php echo $job->id?>'><?php echo $job->jobs_name;?></option>
                            <?php }?>
                           
                                     
                     </select>
                    </div>   
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Status Kepegawaian</label> 
                    </div> 
                    <div class="col-md-6">
                         <select name="from" class="multiselect form-control" size="8" 
                            multiple="multiple" id="generate-status" 
                            > 
                             <?php foreach ($all_employee_affairs as $e_affairs) { ?>
                                <option value='<?php echo $e_affairs->id?>'><?php echo $e_affairs->name;?></option>
                            <?php }?>
                                  
                     </select>
                    </div>   
                </div>
            </div>
         
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary"  id="save-generate-slip" data-action='save'>Simpan</button>
      </div>
    </div>
  </div>
</div>


 
<div class="modal fade" id="download-slip-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Download Slip Gaji</h4>
      </div>
      <div class="modal-body">
        <form>
            <div class="form-group error-message">
                
            </div>
           <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Period</label> 
                    </div> 
                    <div class="col-md-6">
                        <div class='input-group date ' id='period-download-date'>
                          <input type="text" class="form-control no-special-char requiredTextField" field-name="Period" name="period" onkeydown="return false" id="download-period-value"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>  
                </div>
             </div>
            <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Jabatan</label> 
                    </div> 
                    <div class="col-md-6">
                         <select name="from" class="multiselect form-control" size="8" 
                            multiple="multiple"  id="download-jobs">
                            <?php foreach ($all_jobs as $job) { ?>
                                <option value='<?php echo $job->id?>'><?php echo $job->jobs_name;?></option>
                            <?php }?>
                           
                     </select>
                    </div>   
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12 form-group">
                    <div class="col-md-4">
                        <label for="floor_name" class=" control-label">Status Kepegawaian</label> 
                    </div> 
                    <div class="col-md-6">
                         <select name="from" class="multiselect form-control" size="8" 
                            multiple="multiple" id="download-status" 
                            >
                            <?php foreach ($all_employee_affairs as $e_affairs) { ?>
                                  <option value='<?php echo $e_affairs->id?>'><?php echo $e_affairs->name;?></option>
                            <?php }?>      
                     </select>
                    </div>   
                </div>
            </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary"  id="save-download-slip" data-action='save'>Download</button>
      </div>
    </div>
  </div>
</div>
<div class="modal fade" id="print-slip-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Cetak Slip Gaji</h4>
      </div>
      <div class="modal-body">
        <form>
          <div class="form-group error-message"></div>
          <div class="row">
            <div class="col-lg-12 form-group">
              <div class="col-md-4">
                  <label for="floor_name" class=" control-label">Period</label> 
              </div> 
              <div class="col-md-6">
                <div class='input-group date ' id='period-print-date'>
                  <input type="text" class="form-control no-special-char requiredTextField" field-name="Period" name="period" onkeydown="return false" id="print-period-value" value="<?php echo date("m-Y") ?>"> 
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </div>  
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 form-group">
              <div class="col-md-4">
                <label for="floor_name" class=" control-label">Jabatan</label> 
              </div> 
              <div class="col-md-6">
                 <select name="from" class="multiselect form-control" size="8" multiple="multiple"  id="print-jobs">
                    <?php foreach ($all_jobs as $job) { ?>
                        <option value='<?php echo $job->id?>'><?php echo $job->jobs_name;?></option>
                    <?php }?>
               </select>
              </div>   
            </div>
          </div>
          <div class="row">
            <div class="col-lg-12 form-group">
              <div class="col-md-4">
                  <label for="floor_name" class=" control-label">Status Kepegawaian</label> 
              </div> 
              <div class="col-md-6">
                 <select name="from" class="multiselect form-control" size="8" 
                    multiple="multiple" id="print-status" 
                    >
                    <?php foreach ($all_employee_affairs as $e_affairs) { ?>
                          <option value='<?php echo $e_affairs->id?>'><?php echo $e_affairs->name;?></option>
                    <?php }?>      
                </select>
              </div>   
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary"  id="save-print-slip" data-action='save'>Cetak</button>
      </div>
    </div>
  </div>
</div>*/ ?>