<body id="floor-theme">
	<input id="use_role_checker" type="hidden" value="<?php echo $setting['use_role_checker']; ?>"/>
	<div id="cover"></div>
	<div id="server-error-message" title="Server Error" style="display: none">
		<p>
			Internal server error. Please contact administrator if the problem persists
		</p>
	</div>
	<?php $menu = "menu_history";?>
	<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
	<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
	<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
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
										<p class="role-info text-left">History Pengeluaran</p>
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
			<h3>Data Histori Pengeluaran</h3>
			<table class="table table-bordered kitchen-table">
				<thead>
					<tr>
						<th width="150px">Waktu</th>
						<th width="200px">Nama Meja / Customer</th>
						<th width="70px">Jumlah</th>
						<th>Nama Menu</th>
						<th>Catatan</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($lists as $l): ?>
					<tr>
						<td><?php echo date("d/m/Y H:i:s",strtotime($l->created_at)); ?></td>
						<td><?php echo ($l->table_name!="" ? $l->table_name.($l->customer_name!="" ? " / ".$l->customer_name : $l->customer_name) : ($l->customer_name!="" ? $l->customer_name : "")); ?></td>
						<td class="text-center"><?php echo $l->quantity; ?></td>
						<td><?php echo $l->menu_name; ?></td>
						<td><?php 
							$notes="Tipe : ".ucfirst($l->type_origin)."<br>";
							if (!empty($l->note)) {
							  $notes.='Catatan : ' . $l->note . '<br> ';
							}
							foreach ($l->option_list as $option) {
							  $notes.=$option->option_name . ' : ' . $option->option_value_name . '<br>';
							}
							foreach ($l->side_dish_list as $side_dish) {
							  $notes.=$side_dish->name. '<br>';
							}
							echo $notes;
							?></td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js" data-main="<?php echo base_url() ?>assets/js/main-kitchen"></script>
</html>