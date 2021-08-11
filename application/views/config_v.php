<div id="cover"></div>
<body  >
    <link href="<?php echo base_url('assets/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet">
<div id="page-wrapper">
<?php echo form_open("config");?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-sm-offset-4" style="margin-top:120px;">
               <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12"> 
								<?php foreach($modules as $module){?>
								 <div class="form-group">
								 <label for="server_sync_url" class="col-sm-6 control-label"><?php echo $module->name;?></label>
									<input type="checkbox" name="<?php echo $module->name;?>" 
											value="<?php echo $module->is_installed;?>" 
											<?php echo ($module->is_installed == "1")?"checked":"";  ?>> 
								 </div>
								<?php }?>
                                <div class="form-group">
                                    <div class="col-sm-offset-4 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        
                                    </div>
                                </div>
								   <div class="form-group">
                                    <div class=" col-sm-12">
                                        <a href="<?php echo base_url(); ?>" style="margin-top:15px;" class="btn btn-primary btn-block">
									Login</a>
                                    </div>
                                </div>
								 
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>
            </div>
        </div>
        <!-- End row -->
		<?php form_close();?>
    </div>
    <!-- End container fluid -->
</div>
<!-- End page wrapper -->
</body> 
</html>