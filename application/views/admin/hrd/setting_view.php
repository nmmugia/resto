   <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
    ?>
    <div class="col-lg-12"  >
        <div class="panel panel-default"  >
            <div class="panel-heading">
              <h5><b>Mesin Absensi</b></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <div class="col-md-3">
                          <label for="floor_name" class=" control-label">IP Fingerprint</label> 
                        </div> 
                        <div class="col-md-2"> 
                         <input type="text" class="form-control " name="fingerprint_ip" 
                                                value="<?php   if(!empty($fingerprint_ip)) echo $fingerprint_ip;?>"> 
                        </div>  
                    </div>  
                    
                </div>

            </div> 
        </div>  
        <div class="panel panel-default"  >
            <div class="panel-heading">
              <h5><b>Pengaturan Absensi</b></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <div class="col-md-3">
                            <label for="floor_name" class=" control-label">Max Keterlambatan</label> 
                        </div> 
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control qty-input" name="max_late" 
                                value="<?php   if(!empty($max_late)) echo $max_late;?>"> 
                              <div class="input-group-addon">Menit</div>
                            </div>
                           
                        </div>  
                    </div>   
                    <div class="col-lg-12 form-group">
                        <div class="col-md-3">
                            <label for="floor_name" class=" control-label">Range Waktu</label> 
                        </div> 
                        <div class="col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control qty-input" name="range_attendance" 
                                value="<?php   if(!empty($range_attendance)) echo $range_attendance;?>"> 
                              <div class="input-group-addon">Menit</div>
                            </div>
                           
                        </div>  
                    </div>   
                    <div class="col-lg-12 form-group">
                      <div class="col-md-3">
                          <label for="floor_name" class=" control-label">Periode Perhitungan Gaji</label> 
                      </div> 
                      <div class="col-md-2" style="padding-right:0px;">
                        <div class="input-group">
                          <div class="input-group-addon">Dari Tgl</div>
                          <input type="text" class="form-control qty-input" name="from" value="<?php   if(sizeof($payroll_periode)>0) echo $payroll_periode->from;?>"> 
                        </div>
                      </div>  
                      <div class="col-md-2" style="padding-left:0px;">
                        <div class="input-group">
                          <div class="input-group-addon">Sampai</div>
                          <input type="text" class="form-control qty-input" name="to" value="<?php   if(sizeof($payroll_periode)>0) echo $payroll_periode->to;?>"> 
                        </div>
                      </div> 
                    </div> 
                  </div>
                </div>
				</div>
				<div class="panel panel-default"  >
							<div class="panel-heading">
								<h5><b>Perhitungan Insentif</b></h5>
							</div>
							<div class="panel-body">
								<div class="row">
									<div class="col-lg-12 form-group">
										<div class="col-md-3">
											<label class="control-label">Terlambat < 10 menit</label> 
										</div> 
										<div class="col-md-2">
											<div class="input-group">
												<input type="text" class="form-control qty-input" name="late_1" value="<?php echo (isset($components['late_1']) ? $components['late_1'] : 0) ?>"> 
												<div class="input-group-addon">%</div>
											</div>
										</div>  
									</div> 
									<div class="col-lg-12 form-group">
										<div class="col-md-3">
											<label class="control-label">Terlambat > 10 menit</label> 
										</div> 
										<div class="col-md-2">
											<div class="input-group">
												<input type="text" class="form-control qty-input" name="late_2" value="<?php echo (isset($components['late_2']) ? $components['late_2'] : 0) ?>"> 
												<div class="input-group-addon">%</div>
											</div>
										</div>  
									</div> 
									<div class="col-lg-12 form-group">
										<div class="col-md-3">
											<label class="control-label">Ijin Pulang</label> 
										</div> 
										<div class="col-md-2">
											<div class="input-group">
												<input type="text" class="form-control qty-input" name="permission_go_home" value="<?php echo (isset($components['permission_go_home']) ? $components['permission_go_home'] : 0) ?>"> 
												<div class="input-group-addon">%</div>
											</div>
										</div>  
									</div> 
									<div class="col-lg-12 form-group">
										<div class="col-md-3">
											<label class="control-label">Ijin Tidak Hadir</label> 
										</div> 
										<div class="col-md-2">
											<div class="input-group">
												<input type="text" class="form-control qty-input" name="permission_alpha" value="<?php echo (isset($components['permission_alpha']) ? $components['permission_alpha'] : 0) ?>"> 
												<div class="input-group-addon">%</div>
											</div>
										</div>  
									</div> 
									<div class="col-lg-12 form-group">
										<div class="col-md-3">
											<label class="control-label">Tidak Hadir</label> 
										</div> 
										<div class="col-md-2">
											<div class="input-group">
												<input type="text" class="form-control qty-input" name="alpha" value="<?php echo (isset($components['alpha']) ? $components['alpha'] : 0) ?>"> 
												<div class="input-group-addon">%</div>
											</div>
										</div>  
									</div> 
									<?php if(sizeof($payroll_advice)>0): ?>
									<div class="col-lg-12 form-group">
											<div class="col-md-3">
													<label for="floor_name" class=" control-label">Saran Payroll</label> 
											</div> 
											<div class="col-md-8">
													<div class="panel panel-default">
															<div class="panel-body">

															<?php 
																	foreach ($payroll_advice as $advice) { ?> 
																	 <div class="col-lg-12 form-group">
																			<div class="col-md-3">
																					<label for="floor_name" class=" control-label"><?php echo $advice->description; ?></label> 
																			</div> 
																			<div class="col-md-3">
																					<div class="input-group">
																							<input type="hidden"  name="advices[]" value="<?php echo $advice->id;?>"> 
																							<input type="text" class="form-control qty-input" name="advice_day[]" value="<?php echo $advice->total_days;?>"> 
																						<div class="input-group-addon">Hari</div>
																					</div>
																			</div>  

																			<div class="col-md-2">
																					<label for="floor_name" class=" control-label">Bonus</label> 
																			</div> 
																			<div class="col-md-4">
																					<div class="input-group">
																					<div class="input-group-addon">Rp</div>
																							<input type="text" class="form-control qty-input col-md-3" name="advice_percentage[]" 

																							value="<?php echo $advice->bonus;?>"> 
																						
																					</div>
																			</div>  
																	</div> 
																<?php   }
															?>
																 
															</div>  
													</div> 
											</div>  
									</div> 
									<?php endif; ?>
							</div>

					</div> 
        </div>  
				<div class="panel panel-default"  >
            <div class="panel-heading">
              <h5><b>Pengaturan Poin Bonus</b></h5>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-12 form-group">
                        <div class="col-md-3">
                          <label for="floor_name" class=" control-label">Poin Bonus</label> 
                        </div> 
                        <div class="col-md-2"> 
                         <input type="text" class="form-control " name="bonus_point"  value="<?php   if(!empty($bonus_point)) echo $bonus_point;?>"> 
                        </div>  
                    </div>  
                    
                </div>

            </div> 
        </div>  
    </div> 
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="row">
                   <div class="form-group">
                        <div class="col-sm-offset-4 col-sm-8"> 
                            <button type="submit" name="btnAction" value="save_exit"
                                    class="btn btn-primary">
                                <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                            </button>  
                            <a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/detail_staff'); ?>"
                               class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
        </div>
    </div>
  <?php echo form_close(); ?>