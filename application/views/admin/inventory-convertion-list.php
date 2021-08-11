<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
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
      <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-inventory-convertion">
        <thead>
        <tr>
          <th>Inventori</th>
          <th style="text-align: center" width="400px"><?php echo $this->lang->line('column_action'); ?></th>
        </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url ?>"/>
    </div>
  </div>
</div>
<div class="modal fade" id="process-inventory-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
				<?php echo form_open(base_url(SITE_ADMIN."/inventory_process/add"), array('class' => 'form-horizontal')); ?>
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title" id="exampleModalLabel">Proses Inventory </h4>
					</div>
					<div class="modal-body">
	        
						<div class="result"></div>
	       	 	<div class="form-group error-message"></div>
						<div class="form-group">
							<label for="store_id" class="col-sm-3 control-label"><?php echo $this->lang->line('column_store'); ?></label>
							<div class="col-sm-9">
									<?php
										echo form_dropdown('store_id', $store_lists, $setting['store_id'], 'id="store_id_chained" field-name = "Resto" class="form-control requiredDropdown" autocomplete="off"');
									?>
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="form-group">
								<label for="outlet_id" class="col-sm-3 control-label">Outlet</label>

								<div class="col-sm-9">
										<?php
										echo form_dropdown('outlet_id', $outlets, "", 'id="outlet_id_chained" data-width="100%" field-name = "Outlet" class="form-control requiredDropdown select2" autocomplete="off"');
										?>
								</div>
								<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
						<div class="form-group">
								<label for="inventory_id" class="col-sm-3 control-label">Inventori</label>
								<div class="col-sm-9">
										<?php 
											echo form_dropdown('inventory_id', $inventories, "", 
												'data-width="100%" field-name = "Dari Inventory" id="ip_inventory_id" class="form-control requiredDropdown select2" autocomplete="off"');
										?>
								</div>
								<div class="clearfix"></div>
						</div>
						<div class="form-group">
								<label for="quantity" class="col-sm-3 control-label">Jumlah Proses</label>
								<div class="col-sm-9">
										<?php echo form_input(array(
											'name' => 'quantity',
											'id' => 'ip_quantity',
											'type' => 'text',
											'class' => 'form-control requiredTextField only_number',
											'field-name' => 'Jumlah Proses',
											'placeholder' => 'Masukan Jumlah Proses'
										)); ?>
								</div>
								<div class="clearfix"></div>
						</div>
						<div class="form-group">
							<label class="col-sm-3 control-label"></label>
							<div class="col-sm-9" id="show_tree_convertion"></div>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
	        
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
						<button type="submit" class="btn btn-primary"  id="save-process-inventory" data-action='save'>Simpan</button>
					</div>
				</form>
	    </div>
	  </div>
	</div>