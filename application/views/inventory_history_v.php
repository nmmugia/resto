<body id="floor-theme">
	<style type="text/css">
		.panel-body{
		min-height: 0;
		}
	</style>
	<input id="use_role_checker" type="hidden" value="<?php echo $setting['use_role_checker']; ?>"/>
	<div id="cover"></div>
	<div id="server-error-message" title="Server Error" style="display: none">
		<p>
			Internal server error. Please contact administrator if the problem persists
		</p>
	</div>
	<?php $menu = "inventory_history";?>
	<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
	<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
	<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
	<link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.bootstrap.css'); ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.responsive.css'); ?>" rel="stylesheet">
	<?php $this->load->view('partials/navigation_v', array("menu" => $menu)); ?>
	<div id="page-wrapper">
		<div class="col-lg-12">
			<div class="col-lg-12" style="margin-bottom:15px;">
				<div class="row">
					<div class="col-sm-4">
						<div class="row">
							<div class="resto-info-mini">
								<div class="resto-info-pic">
								</div>
								<div class="resto-info-name">
									<?php echo $data_store[0]->store_name; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="col-sm-4"></div>
					<div class="col-sm-4">
						<div class="row">
							<div class="margin-wrap">
								<div class="panel-info">
									<div class="col-xs-6">
										<p class="role-info text-left">History Inventory</p>
									</div>
									<div class="col-xs-6">
										<p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
										<p class="role-name text-right"><?php echo $user_name; ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="col-lg-12" style="background:#fff">
		</div>
		<div class="col-lg-12" style="background:#fff">
			<br>
			<div class="clearfix"></div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3>Data Inventory</h3>
				</div>
				<!-- /.panel-heading -->
				<div class="panel-body">
					<div class="col-lg-12"> 
						<a id="add-siblings" class="btn btn-primary" href="#" class="btn btn-primary pull-right " 
							data-toggle="modal" data-target="#spoiled-modal" >Tambah Spoiled</a><br><br>
					</div>
					<div class="col-lg-12">
						<table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-inventory-history">
							<thead>
								<tr>
									<th width="50%">Nama Inventory</th>
									<th>Stok Terpakai</th>
									<th>Stok Spoiled</th>
									<th>Stok Sisa</th>
								</tr>
							</thead>
						</table>
						<input type="hidden" id="dataProcessUrl" value="<?php echo $data_url;?>">
					</div>
					<!-- /.table-responsive -->
				</div>
				<!-- /.panel-body -->
			</div>
		</div>
	</div>
	<div class="modal fade" id="spoiled-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="exampleModalLabel">Tambah Stok Spoiled</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-lg-12 form-group">
							<div class="col-md-4">
								<label for="floor_name" class=" control-label">Bahan</label> 
							</div>
							<div class="col-md-6">
								<select name="from" class="form-control " data-width="100%" id="inventory_id">
									<option value="">Pilih Inventory</option>
									<?php foreach ($inventories as $inventory) { ?>
									<option value='<?php echo $inventory->id?>' uom_id="<?php echo $inventory->uom_id ?>"><?php echo $inventory->name;?></option>
									<?php }?>           
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<div class="col-md-4">
								<label for="floor_name" class=" control-label">Satuan</label> 
							</div>
							<div class="col-md-6">
								<select name="uom_id" class="form-control "  data-width="100%" id="uom_id">
									<option value="">Pilih Satuan</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<div class="col-md-4">
								<label for="floor_name" class=" control-label">Quantity</label> 
							</div>
							<div class="col-md-6">
								<input type="text" id="quantity" class="form-control" placeholder="Masukan Quantity">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-12 form-group">
							<div class="col-md-4">
								<label for="floor_name" class=" control-label">Keterangan</label> 
							</div>
							<div class="col-md-6">
								<textarea id="description" rows="5" class="form-control"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
					<button type="button" class="btn btn-primary"  id="save-spoiled" data-action='save'>Simpan</button>
				</div>
			</div>
		</div>
	</div>
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js" data-main="<?php echo base_url() ?>assets/js/main-kitchen"></script>
</html>