  
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-10 col-sm-offset-1"> 
                    <div class="panel panel-default">
                        <div class="panel-body">  
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Nama Lengkap</label>

                                <div class="col-sm-8">
                                   <input type="text" name="full_name" class="form-control no-special-char requiredTextField" 
                                   field-name="Nama"
                                    value="<?php if(!empty($detail_recruits)) echo $detail_recruits->name;?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Tempat Lahir</label>
                                <div class="col-sm-8">
                                   <input type="text" name="placedate" class="form-control requiredTextField" 
                                   field-name="Tempat Lahir"
                                   value="<?php if(!empty($detail_recruits)) echo $detail_recruits->birth_place;?>">
                                </div>
                                 
                            </div> 

                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Tgl Lahir</label>
                                
                                <div class="col-sm-4">
                                    <div class='input-group date ' id='start-date'>
                                      <input type="text" class="form-control no-special-char requiredTextField" onkeydown="return false" 
                                      field-name="Tanggal Lahir" name="birthdate"
                                       value="<?php if(!empty($detail_recruits)) echo $detail_recruits->birth_date;?>"> 
                                      <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                    </div>  
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="identity_num" class="col-sm-3 control-label">Jenis Kelamin</label>

                                <div class="col-sm-8"> 
                                   
                                    <label class="radio-inline">
                                      <input type="radio" name="jk" id="inlineRadio1" value="1" checked="true"> Pria 
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="jk" id="inlineRadio2" value="0"> Wanita
                                    </label>
                               
                                 
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="identity_num" class="col-sm-3 control-label">Warga Negara</label>

                                <div class="col-sm-8"> 
                                   
                                    <label class="radio-inline">
                                      <input type="radio" name="nationality" id="inlineRadio1" value="wni" checked="true"> WNI 
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="nationality" id="inlineRadio2" value="wna"> WNA
                                    </label>
                               
                                 
                                </div>
                            </div> 
                           <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Agama</label>

                                <div class="col-sm-8"> 

                                 <?php
                                      $religions = array(
                                            'islam'  => 'Islam',
                                            'katolik'    => 'Kristen Katolik',
                                            'protestan'   => 'Kristen Protestan',
                                            'hindu' => 'Hindu',
                                            'budha' => 'Budha',
                                            'konghucu' => 'Konghucu'
                                          );
                                            echo form_dropdown('religion', $religions, 
                                           (!empty($detail_recruits))?$detail_recruits->religion:"", 
                                            'id="religion" field-name = "Agama" 
                                            class="form-control" autocomplete="on"');
                                          ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-sm-3 control-label"><?php echo $this->lang->line('column_address'); ?></label>

                                <div class="col-sm-8">
                                    <textarea name="address" class="form-control">  <?php if(!empty($detail_recruits)) echo $detail_recruits->address;?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label"><?php echo $this->lang->line('column_phone'); ?></label>

                                <div class="col-sm-8">
                                     <input type="text" name="phone" class="form-control qty-input requiredTextField" field-name="No HP"
                                      value="<?php if(!empty($detail_recruits)) echo $detail_recruits->phone_no;?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">No KTP</label>

                                <div class="col-sm-8">
                                     <input type="text" name="no_ktp" class="form-control qty-input requiredTextField" field-name="No KTP"
                                      value="<?php if(!empty($detail_recruits)) echo $detail_recruits->identity_no;?>"
                                     >
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">SIM</label>
                                <div class="col-sm-3">
                                     <select class="form-control" name="no_sim">
                                        <option value="1">SIM A    </option>
                                        <option value="2">SIM B    </option>
                                        <option value="3">SIM C    </option> 
                                     </select>
                                </div>
                                <div class="col-sm-5">
                                     <input type="text" name="sim_number" class="form-control qty-input"
                                      value="<?php if(!empty($detail_recruits)) echo $detail_recruits->driving_license_no;?>"
                                     >
                                </div>
                            </div>

                             <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">Status</label>
                                <div class="col-sm-8">
                                     <select class="form-control" name="status" >
                                         <option value="1">Belum Menikah</option>
                                         <option value="2">Menikah</option> 
                                     </select>
                                </div> 
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
 