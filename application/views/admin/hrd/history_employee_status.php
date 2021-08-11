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
    <div class="panel panel-default">
        <div class="panel-heading">
          <?php if($users->active==1): ?>
            <?php /*if(sizeof($check_job_histories)>0): ?>
            <a href="#" class="btn btn-danger pull-right" id="resign" data-toggle="modal" data-target="#resign-modal"><div id="title-form-jobs-history" class="btn-add">Resign</div></a>
            <?php endif;*/ ?>
            <a href="#" style="margin-right:5px;" class="btn btn-primary pull-right" id="add-jobs-history"
            		data-toggle="modal" data-target="#jobs-history-modal"  
            		>  <div id="title-form-jobs-history" class="btn-add">Tambah Status Kepegawaian </div>
            </a>
          <?php endif; ?>
            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-jobs-history">
                <thead>
                    <tr> 
                        <th>Status  </th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Berakhir</th>
                        <th>  Kantor Cabang</th>
                        <th>Jabatan</th>
                        <th>Reimburse</th>
                        <th >Cuti</th>
                        <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                    </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlJobs" value="<?php echo base_url(SITE_ADMIN . '/hrd_staff/get_jobs_history/'.$employee_id); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->


  <div class="modal fade" id="jobs-history-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="exampleModalLabel">Tambah Status Kepegawaian </h4>
	      </div>
	      <div class="modal-body">
	        <form>
	       	 	<div class="form-group error-message">
				  	
				</div>
	          	<div class="form-group">
	            	<label for="recipient-name" class="col-sm-4 control-label">Status Kepegawaian</label>
	               <div class="col-sm-7"> 
                   <?php 
                    echo form_dropdown('employee_affairs', $employee_affairs, "", 'id="emp_affair_id" field-name = "Status Kepegawaian" class="form-control requiredDropdown" autocomplete="off"');
                ?>
                </div>
                    <input type="hidden" class="form-control" id="jobs-history-id">
	          	</div> 
                <div class="form-group">
                    <label for="floor_name" class="col-sm-4 control-label">Tanggal Mulai</label> 
                    <div class="col-sm-7"> 

                        <div class='input-group date ' id='start-date'>
                          <input type="text" class="form-control no-special-char" onkeydown="return false" id="start-date-value"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>
                </div>  
                <?php /*
                 <div class="form-group">
                    <label for="floor_name" class="col-sm-4 control-label">Tanggal Berakhir</label> 
                    <div class="col-sm-7"> 

                        <div class='input-group date ' id='end-date'>
                          <input type="text" class="form-control no-special-char" onkeydown="return false" id="end-date-value"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>
                </div>  */ ?>
                
                
                 <div class="form-group">
                    <label for="recipient-name" class=" col-sm-4 control-label">Kantor cabang </label>
                    <div class="col-sm-7"> 
                     <?php 
                    echo form_dropdown('stores', $stores, "", 'id="store_id" field-name = "Kantor Cabang" class="form-control requiredDropdown" autocomplete="off"');
                     ?>
                     </div>
                </div> 

                <div class="form-group">
                    <label for="recipient-name" class="col-sm-4  control-label">Jabatan  </label> 
                    <div class="col-sm-7"> <?php 
                    echo form_dropdown('jobs', $jobs, "", 'id="jobs_id" field-name = "Jabatan" class="form-control requiredDropdown" autocomplete="off"');
                     ?> </div> 
                </div> 

                 <div class="form-group">
                    <label for="recipient-name" class="col-sm-4 control-label">Jatah Reimburse   </label>
                   <div class="col-sm-7"> 

                    <div class="input-group">
                      <div class="input-group-addon">Rp. </div>
                        <input type="text" class="form-control qty-input" id="reimburse"> 
                    
                    </div>
                   </div> 
                </div> 

                 <div class="form-group">
                   <label for="recipient-name" class="col-sm-4 control-label">Jatah Cuti  </label>
                    <div class="col-sm-7">
                        <div class="input-group">
                        <input type="text" class="form-control qty-input" id="vacation"> 
                      <div class="input-group-addon">Hari</div>
                    </div>

                    </div> 

                </div> 
	        </form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-primary"  id="save-jobs-history" data-action='save'>Simpan</button>
	      </div>
	    </div>
	  </div>
	</div>
  <div class="modal fade" id="resign-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
        <form method="post" action="<?php echo base_url(SITE_ADMIN."/hrd_staff/resign") ?>">
          <input type="hidden" name="user_id" value="<?php echo $users->id ?>">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="exampleModalLabel">Form Resign </h4>
          </div>
          <div class="modal-body">
            <div class="form-group error-message"></div>
            <div class="form-group">
              <label for="recipient-name" class="col-sm-4 control-label">Nomor Surat</label>
              <div class="col-sm-7" style="padding-top:7px;">
                <?php echo $resign_number; ?>
              </div> 
            </div> 
            <div class="form-group">
              <label for="recipient-name" class="col-sm-4 control-label">Keterangan</label>
              <div class="col-sm-7">
                <textarea rows="5" class="form-control requiredTextField" name="description" field-name = "Keterangan" id="resign_description"></textarea>
              </div> 
            </div> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">Simpan</button>
          </div>
        </form>
	    </div>
	  </div>
	</div>
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>

<input type="hidden" id="employee_id" value="<?php echo $employee_id ?>"/>
<!-- /.col-lg-12 -->