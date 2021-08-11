<body id="floor-theme">
	<audio controls="controls" id="bgsound_notification" style="display:none;">
		<source src="<?= base_url("assets/sounds/new.mp3"); ?>" type="audio/mp3" />
	</audio>
	<input id="use_role_checker" type="hidden" value="<?php echo $setting['use_role_checker']; ?>"/>
	<div id="cover"></div>
	<div id="server-error-message" title="Server Error" style="display: none">
		<p>
			Internal server error. Please contact administrator if the problem persists
		</p>
	</div>
	<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
	<input id="outlet_id" type="hidden" value="<?php echo $user->outlet_id; ?>"/>
	<input id="theme" type="hidden" value="<?php echo $setting['theme']; ?>"/>
	<input id="dining_type" type="hidden" value="<?php echo $setting['dining_type']; ?>"/>
	<input id="count_kitchen_process" type="hidden" value="<?php echo $setting['count_kitchen_process']; ?>"/>
	<input id="use_checking_order" type="hidden" value="<?php echo (sizeof($outlet_data)>0 ? $outlet_data->checking_order : 0); ?>"/>
	<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
	<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
	<?php $menu = "order";?>
	<?php $this->load->view('partials/navigation_v', array("menu" => $menu)); ?>
	<button class="btn btn-option-list <?php echo $slide_setting; ?>"><i class="fa <?php echo ($slide_setting=="slide-up" ? "fa-arrow-up" : "fa-arrow-down") ?>"></i></button>
	<div id="page-wrapper">
	<div class="col-lg-12">
		<div class="col-lg-12" id="header_store" style="margin-bottom:15px;">
			<div class="row">
				<div class="col-sm-4">
					<div class="row">
						<div class="resto-info-mini">
							<div class="resto-info-pic"></div>
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
									<p class="role-info text-left">Take Order</p>
									<?php if($setting['theme']==2): ?>
									<div class="left">
										<label class="left" style="margin-right: 7px;">
											<input type="checkbox" class="left choice_type" name="choice_type_dinein" value="dinein" style="cursor:pointer;" <?php echo ($kitchen_setting['dinein']==1 ? "checked" : ""); ?>> 
											<div class="left">DI</div>
										</label>
										<label class="left" style="margin-right: 7px;">
											<input type="checkbox" class="left choice_type" name="choice_type_takeaway" value="takeaway_delivery" style="cursor:pointer;" <?php echo ($kitchen_setting['takeaway_delivery']==1 ? "checked" : ""); ?>> 
											<div class="left">TA & DO</div>
										</label>
									</div>
									<?php endif; ?>
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
		<!-- Kitchen panel -->
		<?php echo $list_detail_kitchen; ?>
	</div>
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js"
	data-main="<?php echo base_url() ?>assets/js/main-kitchen"></script>
</html>