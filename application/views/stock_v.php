<body id="floor-theme">
	<div id="cover"></div>
	<div id="server-error-message" title="Server Error" style="display: none">
		<p>
			Internal server error. Please contact administrator if the problem persists
		</p>
	</div>
	<?php $menu = "stock";?>
	<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
	<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
	<input id="stock_menu_by_inventory" name="stock_menu_by_inventory" type="hidden" value="<?php echo $setting["stock_menu_by_inventory"]?>" />
	<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
	<?php if($setting['stock_menu_by_inventory'] == 0): ?>
	<div class="popup-block popup-available-menu" style="display:none;">
	    <div class="col-lg-4 col-lg-offset-4 input-menu-quantity" style="margin-top: 10%;">
	        <div class="col-lg-12">
	            <div class="row">
	            <div class="title-bg-popup">
	                    <a  class="btn btn-std btn-cancel btn-distance" style="float:right"> X </a></div>
	                <div class="title-bg title-bg-member">
	                    <h4 class="title-popup"></h4>
	                </div>
	                <form action="" method="post" id="form-payment">
		                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
		                    <span id="subtitle"><b>Jumlah Menu :</b></span>
		                     <div id="select-member">
		                        <input class="form-control" type="text" name="menu_quantity" id="menu_quantity_val"/>
		                        <input class="form-control" type="hidden" name="menu_id" id="menu_id_val"/>
		                    </div> 
		                    <input class="form-control" type="hidden" name="confirm_type" id="confirm_type"/>
		                    <div class="col-lg-12">
		                        <div class="row">
		                            <div class="button-wrapper">
		                                <a  class="btn btn-std btn-cancel btn-distance">Batal</a>
		                                <a class="btn btn-std" id="btn-ok">OK</a>
		                            </div>
		                        </div>
		                    </div>
		                </div>
	                </form>
	            </div>
	        </div>
	    </div>
	</div>
	<?php endif; ?>

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
					<div class="col-sm-4">
					</div>
					<div class="col-sm-4">
						<div class="row">
							<div class="margin-wrap">
								<div class="panel-info">
									<div class="col-xs-6">
										<p class="role-info text-left">Stock</p>
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
			<div class="row">
				<div class="col-md-6">
					<div class="title-bg title-bg-stock">
						<div class="col-md-12">
							<h4 class="title-popup left"><?php echo $this->lang->line('ds_nav_category') ?></h4>
						</div>
					</div>
					<div class="dark-theme-con"
						style="height:440px;overflow:auto;padding:10px;">
						<div style="padding:10px;">
							<div class="row">
								<?php if (!empty($categories)) {
									foreach ($categories as $category) {
									    $image = base_url('assets/img/default.jpg');
									    if ($category->icon_url != '') {
									        $image = base_url($category->icon_url);
									    }
									    ?>
								<div class="stock-order menu-order">
									<img src="<?php echo $image; ?>" alt="menu"/>
									<p><?php echo $category->category_name; ?></p>
									<input id="category_id" type="hidden"
										value="<?php echo $category->id; ?>"/>
								</div>
								<?php }
									}
									else {
									    echo '<p>Tidak ada list kategori</p>';
									} ?>
							</div>
						</div>
					</div>
				</div>
				<div class="col-md-6">
					<div class="title-bg title-bg-pack">
						<div class="col-md-12">
							<h4 class="title-popup left category-name"><?php if (!empty($categories)) {
								echo $categories[0]->category_name;
								}
								else {
								echo "TIDAK ADA LIST MENU";
								} ?></h4>
						</div>
					</div>
					<div class="dark-theme-con"
						style="height:440px;overflow:scroll;overflow-x:hidden;padding:10px;">
						<div class="col-lg-12">
							<div class="row container-menus">
								<?php if (!empty($menus)) {
									foreach ($menus as $menu) {
									    $image = base_url('assets/img/reskin/default-menus.jpg');
									    if ($menu->icon_url != '') {
									        $image = base_url($menu->icon_url);
										}
								?>
								<div class="menu-stock">
									<img src="<?php echo $image ?>" alt="menu"/>
									<p><?php echo $menu->menu_name; ?></p>
									<button class="btn btn-trans stock-btn"
										id="<?php echo $menu->available; ?>" data-menu-quantity="<?php echo $menu->menu_quantity; ?>">
										<?php if ($menu->available == 0) {
											echo ($setting['stock_menu_by_inventory'] != 1) ? 'Set Habis' : 'Set Available';
										} else {
											echo ($setting['stock_menu_by_inventory'] != 1) ? 'Set Available' : 'Set Habis';
										} ?>
									</button>
									<input id="menu_id" type="hidden" value="<?php echo $menu->id; ?>"/>
								</div>
								<?php
									}
								} else {
								    echo 'Tidak ada list menu';
								} ?>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- End row -->
		</div>
	</div>
	<!-- End page wrapper -->
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js"
	data-main="<?php echo base_url() ?>assets/js/main-stock"></script>
</html>