 
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-8 col-sm-offset-2"> 
                    <div class="panel panel-default">
                        <div class="panel-body">  
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Nama Lengkap</label>

                                <div class="col-sm-8">
                                   <input type="text" name="full_name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Tempat Tgl Lahir</label>

                                <div class="col-sm-8">
                                   <input type="text" name="full_name" class="form-control">
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="identity_num" class="col-sm-3 control-label">Jenis Kelamin</label>

                                <div class="col-sm-8"> 
                                   
                                    <label class="radio-inline">
                                      <input type="radio" name="jk[]" id="inlineRadio1" value="option1"> Pria 
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="jk[]" id="inlineRadio2" value="option2"> Wanita
                                    </label>
                               
                                 
                                </div>
                            </div> 
                            <div class="form-group">
                                <label for="identity_num" class="col-sm-3 control-label">Warga Negara</label>

                                <div class="col-sm-8"> 
                                   
                                    <label class="radio-inline">
                                      <input type="radio" name="nationality[]" id="inlineRadio1" value="option1"> WNI 
                                    </label>
                                    <label class="radio-inline">
                                      <input type="radio" name="nationality[]" id="inlineRadio2" value="option2"> WNA
                                    </label>
                               
                                 
                                </div>
                            </div> 
                           <div class="form-group">
                                <label for="name" class="col-sm-3 control-label">Agama</label>

                                <div class="col-sm-8">
                                   <input type="text" name="full_name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="address" class="col-sm-3 control-label"><?php echo $this->lang->line('column_address'); ?></label>

                                <div class="col-sm-8">
                                    <textarea name="address" class="form-control">
                                        
                                    </textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label"><?php echo $this->lang->line('column_phone'); ?></label>

                                <div class="col-sm-8">
                                     <input type="text" name="full_name" class="form-control">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">No KTP</label>

                                <div class="col-sm-8">
                                     <input type="text" name="full_name" class="form-control">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">SIM</label>
                                <div class="col-sm-3">
                                     <select class="form-control">
                                         <option>Jenis SIM</option>
                                     </select>
                                </div>
                                <div class="col-sm-5">
                                     <input type="text" name="full_name" class="form-control qty-input">
                                </div>
                            </div>

                             <div class="form-group">
                                <label for="phone" class="col-sm-3 control-label">Status</label>
                                <div class="col-sm-8">
                                     <select class="form-control">
                                         <option>Status Pernikahan</option>
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
 