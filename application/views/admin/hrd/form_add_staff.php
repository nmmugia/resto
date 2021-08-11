<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-6"> 
                    <div class="panel panel-default">
                        <div class="panel-body"> 
                            <div class="form-group">
                                    <label for="nip" class="col-sm-3 control-label">NIP</label>

                                    <div class="col-sm-8">
                                        <?php echo form_input($nip); ?>
                                    </div>
                                </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label"><?php echo $this->lang->line('column_name'); ?></label>

                                <div class="col-sm-8">
                                    <?php echo form_input($name); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="floor_name" class="col-sm-4 control-label"><?php echo $this->lang->line('column_gender'); ?></label> 
                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                    <?php echo form_radio($men); ?>
                                     <small>Pria</small>
                                  </label>

                                  <label class="radio-inline">
                                    <?php echo form_radio($women); ?>
                                     <small>Wanita</small>
                                  </label>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="identity_type" class="col-sm-3 control-label"><?php echo $this->lang->line('column_identity'); ?></label>

                                <div class="col-sm-8">
                                    <?php
                                    echo form_dropdown('identity_type', $identity_type, $this->input->post('identity_type'), 'field-name = "'.$this->lang->line('column_identity').'" class="form-control" autocomplete="off"');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="identity_num" class="col-sm-3 control-label"><?php echo $this->lang->line('column_identity_id'); ?></label>

                                <div class="col-sm-8">
                                    <?php echo form_input($identity_num); ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="email" class="col-sm-3 control-label">Email</label>

                                <div class="col-sm-8">
                                    <?php echo form_input($email); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-sm-3 control-label"><?php echo $this->lang->line('column_address'); ?></label>

                                <div class="col-sm-8">
                                    <?php echo form_textarea($address); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label"><?php echo $this->lang->line('column_phone'); ?></label>

                                <div class="col-sm-8">
                                    <?php echo form_input($phone); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6"> 
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="store_id" class="col-sm-3 control-label"><?php echo $this->lang->line('column_store'); ?></label>

                                <div class="col-sm-8">
                                    <?php
                                         echo form_dropdown('store_id', $store_id, $this->input->post('store_id'), 'id="store_id_chained" field-name = "Store" class="form-control requiredDropdown" autocomplete="off"'); 
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="outlet_id" class="col-sm-3 control-label">Outlet</label>

                                <div class="col-sm-8">
                                    <?php 
                                        echo form_dropdown('outlet_id', $outlet_id, $this->input->post('outlet_id'), 'id="outlet_id_chained" field-name = "Outlet" class="form-control requiredDropdown" autocomplete="off"');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="user_level" class="col-sm-3 control-label">User Level</label> 
                                <div class="col-sm-8">
                                    <?php 
                                        echo form_dropdown('user_level', $user_levels, $this->input->post('user_level'), 'id="user_level" field-name = "User Level" class="form-control requiredDropdown" autocomplete="off"');
                                    ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="username" class="col-sm-3 control-label">Username</label> 
                                <div class="col-sm-8">
                                    <?php echo form_input($username); ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="password" class="col-sm-3 control-label">Password</label>

                                <div class="col-sm-8">
                                    <?php echo form_input($password); ?>
                                </div>
                            </div>
                             <div class="form-group">
                                <label for="floor_name" class="col-sm-4 control-label">Compliment?</label> 
                                <div class="col-sm-8">
                                    <label class="radio-inline">
                                    <?php echo form_radio($y_compliment); ?>
                                     <small>Ya</small>
                                  </label>

                                  <label class="radio-inline">
                                    <?php echo form_radio($n_compliment); ?>
                                     <small>Tidak</small>
                                  </label>
                                </div>
                            </div> 
                             <div class="form-group" id="container-pin">
                                <label for="password" class="col-sm-3 control-label">PIN</label>

                                <div class="col-sm-8">
                                    <?php echo form_input($pin); ?>
                                </div>
                            </div> 
                        </div> 
                    </div>
                </div>
                <div class="col-lg-6"> 
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="account_no" class="col-sm-3 control-label">No Rekening</label>

                                <div class="col-sm-8">
                                    <?php echo form_input($account_no); ?>  
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="account_name" class="col-sm-3 control-label">A.n Rekening</label>

                                <div class="col-sm-8">
                                    <?php echo form_input($account_name); ?>  
                                </div>
                            </div>
                            
                             <div class="form-group">
                                <label for="banks" class="col-sm-3 control-label">Bank</label>

                                <div class="col-sm-8">
                                    <?php 
                                        echo form_dropdown('banks', $banks, $this->input->post('banks'), 'id="banks" field-name = "Banks" class="form-control requiredDropdown" autocomplete="off"');
                                    ?>
                                </div>
                            </div> 
                             <div class="form-group">
                                <label for="account_branch" class="col-sm-3 control-label">Kantor Cabang</label>

                                <div class="col-sm-8">
                                <?php echo form_input($account_branch); ?>  
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">    
                    <div class="form-group">
                        <div class="col-sm-offset-5 col-sm-4">
                            <button type="submit" name="btnAction" value="save"
                                    class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                            </button> 
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
