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
            <a href="#" class="btn btn-primary pull-right" id="add-memorandum"
            		data-toggle="modal" data-target="#memorandum-modal"  
            		>  <div id="title-form-memorandum" class="btn-add">Tambah Surat Peringatan </div>
            </a>  
            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-memorandum">
                <thead>
                <tr> 
                    <th width="40%">Nama</th>
                    <th width="20%">Period /hari</th>
                    <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo base_url(SITE_ADMIN . '/hrd/get_data_memorandum'); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->


    <div class="modal fade" id="memorandum-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="exampleModalLabel">Tambah Surat Peringatan </h4>
	      </div>
	      <div class="modal-body">
	        <form>
	       	 	<div class="form-group error-message">
				  	
				</div>
	          	<div class="form-group">
	            	<label for="recipient-name" class="control-label">Nama:</label>
	            	<input type="text" class="form-control no-special-char" id="memorandum-name">
                    <input type="hidden" class="form-control" id="memorandum-id">
	          	</div> 
                
                <div class="form-group"> 
                    <label for="recipient-name" class="control-label">Masa Berlaku:</label>
                    <div class="input-group">
                        <input type="text" class="form-control col-sm-2 qty-input" id="memorandum-period"> 
                      <div class="input-group-addon">Hari</div>
                    </div>
                </div> 
	        </form>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        <button type="button" class="btn btn-primary"  id="save-memorandum" data-action='save'>Simpan</button>
	      </div>
	    </div>
	  </div>
	</div>
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
<!-- /.col-lg-12 -->