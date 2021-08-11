<body id="floor-theme">
<input id="use_role_checker" type="hidden" value="<?php echo $setting['use_role_checker']; ?>"/>
<div id="cover"></div>
<div id="server-error-message" title="Server Error" style="display: none">
    <p>
        Internal server error. Please contact administrator if the problem persists
    </p>
</div>
<input id="ip_address" type="hidden" value="<?php echo $ip_address; ?>"/>
<input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
<input id="theme" type="hidden" value="<?php echo $setting['theme']; ?>"/>
<input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
<div class="popup-block popup-input" id="popup-ajax" style="display:none;"></div>
<?php
  $this->load->view('partials/navigation_v');
?>
<?php if(isset($slide_setting)): ?>
<button class="btn btn-option-list <?php echo $slide_setting; ?>"><i class="fa <?php echo ($slide_setting=="slide-up" ? "fa-arrow-up" : "fa-arrow-down") ?>"></i></button>
<?php endif; ?>
<div id="page-wrapper">
    <div class="col-lg-12" >
			<div class="col-lg-12" id="header_store" style="margin-bottom:15px;">
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
						<?php if($setting['print_number']==1): ?>
						<div class="row col-sm-8 right">
							<div class="margin-wrap">
								<div class="panel-info">
									<div class="col-xs-12">
										<div class="left">
											<p class="role-info text-left">Print Nomor : </p>
										</div>
										<div class="col-xs-4" style="padding-right:0px;">
											<input type="text" class="form-control only_numeric" id="string_number" maxlength="5">
										</div>
										<div class="left">
											<button id="print_number" class="btn btn-danger"><i class="fa fa-print"></i></button>
										</div>
									</div>
								</div>
							</div>
						</div>
						<?php endif; ?>
					</div>
					 <div class="col-sm-4">
						<div class="row">
							<div class="margin-wrap">
								<div class="panel-info">
									<div class="col-xs-6">
										<p class="role-info text-left">Checking Order</p>
                     <?php if($setting['theme']==2): ?>
                    <div class="left">
                      <label class="left" style="margin-right: 7px;">
                        <input type="checkbox" class="left choice_type" name="choice_type_dinein" value="dinein" style="cursor:pointer;" <?php echo ($checker_setting['dinein']==1 ? "checked" : ""); ?>> <div class="left">DI</div>
                      </label>
                      <label class="left" style="margin-right: 7px;">
                        <input type="checkbox" class="left choice_type" name="choice_type_takeaway" value="takeaway_delivery" style="cursor:pointer;" <?php echo ($checker_setting['takeaway_delivery']==1 ? "checked" : ""); ?>> <div class="left">TA & DO</div>
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
      <div id="content_checker"><?php echo $list_detail_view; ?></div>
    </div>
           
    <!-- End page wrapper -->
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js"
        data-main="<?php echo base_url() ?>assets/js/main-checker"></script>
</html>