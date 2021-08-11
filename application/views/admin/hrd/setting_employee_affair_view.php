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
        <?php /*<div class="panel-heading">
            <a href="#" class="btn btn-primary pull-right " id="add-employee-affair"
            		data-toggle="modal" data-target="#employee-affair-modal"  
            		> <div id="title-form-emp-affair" class="btn-add">Tambah Status Kepegawaian </div>
            </a>  
            <div class="clearfix"></div>
        </div>*/ ?>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-employee-affair">
                <thead>
                <tr>
                    <th>Nama</th>
                     <th >Bulan</th>
                    <?php /*<th  width="40%"><?php echo $this->lang->line('column_action'); ?></th>*/ ?>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo base_url(SITE_ADMIN . '/hrd/get_data_empl_affair'); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->


  <div class="modal fade" id="employee-affair-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
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
	            	<label for="recipient-name" class="control-label">Nama:</label>
	            	<input type="text" class="form-control no-special-char" id="employee-affair-name">
                    <input type="hidden" class="form-control" id="employee-affair-id" value="">
	          	</div>   
                <div class="container-group">
                    <div class="form-group">
                        <label for="recipient-name" class="control-label qty-input">Lama :</label>
                       
                        <div class="input-group">
                         
                            <input type="text" class="form-control qty-input" id="during"> 
                             <div class="input-group-addon">Bulan </div>
                        </div>
                    </div>  

                    <div class="form-group">
                        <label for="recipient-name" class="control-label">Status Selanjutnya:</label> 
                       
                        <select id="next_job" class="form-control"> 
                        </select>
                    </div> 
                </div>
               
	        </form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-primary"  id="save-employee-affair" data-action='save'>Simpan</button>
	      </div>
	    </div>
	  </div>
	</div>
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
<!-- /.col-lg-12 -->