<body id="floor-theme">
  <input id="use_role_checker" type="hidden" value="<?php echo $setting['use_role_checker']; ?>"/>
  <div id="cover"></div>
  <div id="server-error-message" title="Server Error" style="display: none">
      <p>
          Internal server error. Please contact administrator if the problem persists
      </p>
  </div>
  <input id="base_url" type="hidden" value="<?php echo base_url(); ?>"/>
  <input type="hidden" id="node_url" value="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>"/>
<script src="<?php echo $this->config->item('node_server_ip'); ?>:<?php echo $this->config->item('node_server_port'); ?>/socket.io/socket.io.js"></script>
  <link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
  <?php
    $this->load->view('partials/navigation_v');
  ?>
  <div id="page-wrapper">
    <div class="col-lg-12" >
			<div class="col-lg-12" style="margin-bottom:15px;">
        <div class="row">
					<div class="col-sm-2">
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
					<div class="col-sm-6">
            <div class="form-group col-sm-offset-3 " style="margin-top: 16px;">
              <div class="col-sm-8" style="margin-top: -6px;">
                <a href="javascript:void(0);" class="btn btn-danger btn-lg" id="today_reservation" date="<?php echo date("Y-m-d") ?>">Today</a>
                <a href="javascript:void(0);" class="btn btn-warning btn-lg" id="tommorow_reservation" date="<?php echo date("Y-m-d",strtotime("+ 1day")) ?>">Tommorow</a>
                <a href="javascript:void(0);" class="btn btn-success btn-lg" id="all_reservation" date="">All</a>
              </div>
              <div class="col-sm-4" style="margin-top:-18px;display:none;">
                <label class="control-label">Dari</label>
                <div class='input-group date' id='start_date'>
                  <input type="text" class="form-control" field-name="Dari" name="start_date" onkeydown="return false" value="<?php echo $monitoring_setting['start_date'] ?>"> 
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
              <div class="col-sm-4" style="margin-top:-18px;display:none;">
                <label class="control-label">Ke</label>
                <div class='input-group date' id='to_date'>
                  <input type="text" class="form-control" field-name="Dari" name="to_date" onkeydown="return false" value="<?php echo $monitoring_setting['end_date'] ?>"> 
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div>
              </div>
              <div class="col-sm-1" style="padding-left:0px;margin-top: 6px;display:none;">
                <a href="javascript:void(0);" class="btn btn-primary btn-mini" id="search_reservation">Cari</a>
              </div>
            </div>
					</div>
					 <div class="col-sm-4">
						<div class="row">
							<div class="margin-wrap">
								<div class="panel-info">
									<div class="col-xs-4">
										<p class="role-info text-left">Monitoring</p>
									</div>
									<div class="col-xs-8">
										<p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
										<p class="role-name text-right"><?php echo $user_name; ?></p>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
      </div>
      <div id="content_monitoring"><?php echo $list_view; ?></div>
    </div>
    <div class="popup-block" style="display:none;" id="confirm_post">
    <div class="col-lg-12   input-right">
        <div class="col-lg-12">
            <div class="row">
                <div class="title-bg">
                    <h4 class="title-popup"><b>Post</b></h4>
                </div>
                
                <div class="dark-theme-con" style="display:table;width:100%;padding:10px;">
                    <p id="message_confirm_post">Anda Yakin Akan Melakukan Post ?
                   
                    <div class="col-lg-12">
                        <div class="row">
                        <a  class="btn btn-std pull-right btn-cancel" id="btn-cancel-post">Batal</a>
                        <a   class="btn btn-std pull-right" id="btn-ok-post"><i class="fa fa-check"></i> OK</a>
                        </div>
                    </div>
                </div>
               

            </div>
        </div>
    </div>
</div>
  </div>
  <script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js" data-main="<?php echo base_url() ?>assets/js/main-monitoring"></script>
</body>
</html>