<div id="cover"></div>
<body id="login-theme">
	<div class="btn-float-container">
		<a href="<?php echo SITE_ADMIN ?>" class="btn-float-admin"><i class="fa fa-user fa-2x"></i><p>Go to Admin</p></a>
	</div>
 
<script type="text/javascript" async="async" defer="defer" data-cfasync="false" src="https://mylivechat.com/chatinline.aspx?hccid=97374916"></script> 

<div id="page-wrapper" style="padding-top:10px;">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <!-- Panel Login -->
				<div class="col-lg-12">
                        <img class="brand-logo" src="<?php echo base_url() ?>assets/img/reskin/logo.png" width=200px
                             alt="logo"/>
                </div>
				
                <div class="login-container">
					<div class="alert-pop">
					<?php echo form_open("auth/login"); ?>
								<?php if ($this->session->flashdata('message')) { ?>
									<div class="alert alert-danger">
										<?php echo $this->session->flashdata('message'); ?>
									</div>
								<?php } ?>
				</div>
                    <div class="col-lg-12">
						<div class="row">
								<div style="clear:both;"></div>
								<input class="form-control input-pin" name="identity" type="password"
									   placeholder="ENTER PIN"/>

								<div style="clear:both;"></div>
							 <div class="login-panel">
								<div class="login-button-left">
									<a class="btn btn-pin">1</a>
									<a class="btn btn-pin">2</a>
									<a class="btn btn-pin">3</a>
									
									<div class="clearfix"></div>
									<a class="btn btn-pin">4</a>
									<a class="btn btn-pin">5</a>
									<a class="btn btn-pin">6</a>
									
									<a class="btn btn-pin">7</a>
									<a class="btn btn-pin">8</a>
									<a class="btn btn-pin">9</a>
									
									<div class="clearfix"></div>
									<a class="btn btn-clear">C</a>
									<a class="btn btn-pin">0</a>
									<a class="btn btn-backspace"><i class="fa fa-long-arrow-left"></i></a>

									<div class="clearfix"></div>
									<button class="btn btn-enter" type="submit">OK</button>
								</div>

								<?php echo form_close(); ?>
							</div>
						</div>
                    </div>

                </div>
				<div class="login-copyright">
                    Copyright © Digital Oasis <?php echo date('Y'); ?> <?php echo $this->config->item("version");?>
                </div>

                <!-- End Panel Login -->
            </div>
        </div>
        <!-- End row -->
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->
</body>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/libs/require.js"
        data-main="<?php echo base_url() ?>assets/js/main-login"></script>
</html>