<style>
.dropdown-menu {   
}
</style> 
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12">
                    <div class="col-lg-6"> 
                        <div class="panel panel-default">
                            <div class="panel-body">  
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Nama Istri/Suami</label>

                                    <div class="col-sm-8">
                                       <input type="text" name="mate_name" class="form-control no-special-char"
                                         value="<?php if(!empty($mates)) echo $mates->mate_name;?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Tempat</label>
                                    <div class="col-sm-8">
                                       <input type="text" name="mate_birth_place" class="form-control no-special-char"
                                         value="<?php if(!empty($mates)) echo $mates->mate_birth_place;?>">
                                    </div>
                                    
                                </div> 
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Tgl Lahir</label> 
                                    <div class="col-sm-8">
                                        <div class='input-group date ' id='mate_birth_date'>
                                          <input type="text" class="form-control no-special-char" onkeydown="return false" 
                                          name="mate_birth_date" 
                                            value="<?php if(!empty($mates)) echo $mates->mate_birth_date;?>"> 
                                          <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                          </span>
                                        </div>   
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="identity_num" class="col-sm-4 control-label">Pekerjaan</label>

                                    <div class="col-sm-8"> 
                                            <?php
                                      $jobs = array(
                                            'swasta'  => 'Pegawai Swasta',
                                            'negeri'    => 'Pegawai Negeri',
                                            'wiraswasta'   => 'Wiraswasta',
                                            'irt' => 'Ibu Rumah Tangga',
                                            'lainnya' => 'Lainnya'
                                          );
                                            echo form_dropdown('mate_job', $jobs, 
                                           (!empty($mates))?$mates->mate_job:"", 
                                            'id="mate_job" field-name = "Pekerjaan" 
                                            class="form-control" autocomplete="on"');
                                          ?>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="identity_num" class="col-sm-4 control-label">Jumlah Anak</label>

                                  <div class="col-sm-8">
                                       <input type="text" name="child_total" class="form-control qty-input"
                                         value="<?php if(!empty($mates)) echo $mates->child_total;?>">
                                    </div>
                                </div> 
                                
                                 
                            </div>
                        </div>
                    </div>  
                    <div class="col-lg-6"> 
                        <div class="panel panel-default">
                            <div class="panel-body">   
                               <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Nama Ayah/Ibu/Wali</label>

                                    <div class="col-sm-8">
                                       <input type="text" name="parent_name" class="form-control no-special-char"
                                         value="<?php if(!empty($parents)) echo $parents->parent_name;?>">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="name" class="col-sm-4 control-label">Pekerjaan</label>

                                    <div class="col-sm-8">
                                      
                                          <?php
                                        $jobs = array(
                                            'swasta'  => 'Pegawai Swasta',
                                            'negeri'    => 'Pegawai Negeri',
                                            'wiraswasta'   => 'Wiraswasta',
                                            'irt' => 'Ibu Rumah Tangga',
                                            'lainnya' => 'Lainnya'
                                          );
                                            echo form_dropdown('parent_job', $jobs, 
                                           (!empty($parents))?$parents->parent_job:"", 
                                            'id="mate_job" field-name = "Pekerjaan" 
                                            class="form-control" autocomplete="on"');
                                          ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="address" class="col-sm-4 control-label">Alamat</label> 
                                    <div class="col-sm-8">
                                        <textarea name="parent_address" class="form-control" rows="5" cols="100"><?php if(!empty($parents)) echo $parents->parent_address;?>
                                        </textarea>
                                    </div>
                                </div>
                                 
                            </div>
                        </div>
                    </div>  
                </div>
                
                <div class="col-lg-12">
                    <div class="col-lg-6"> 
                        <div class="panel panel-default">
                            <div class="panel-heading"> 
                                Keluarga  
                            </div>
                            <div class="panel-body">   
                                 <div class="col-lg-12"> 
                                 <a id="add-siblings" class="btn btn-primary">Tambah</a><br><br>
                                </div>
                                 <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-siblings-list">
                                    <thead>
                                    <tr> 
                                        <th  >Nama</th>
                                        <th  >Kakak/Adik</th> 
                                        <th >Umur</th> 
                                        <th >Pendidikan</th> 
                                         <th >X</th> 
                                    </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(!empty($siblings)){
                                            $index = 0;
                                            foreach ($siblings as $sibling) { ?>
                                                <tr id="remove-siblings-<?php echo $index;?>"> 
                                                    <td >
                                                        <input type="text" name="siblings[<?php echo $index;?>][name]" class="form-control no-special-char"
                                                        value="<?php  echo $sibling->sibling_name;?>" ></td>
                                                    <td >
                                                        <input type="text" name="siblings[<?php echo $index;?>][status]" 
                                                    class="form-control no-special-char"
                                                    value="<?php  echo $sibling->sibling_status;?>"
                                                    ></td>  
                                                    <td >
                                                        <input type="text" name="siblings[<?php echo $index;?>][age]" 
                                                        class="form-control qty-input"
                                                        value="<?php  echo $sibling->sibling_age;?>">

                                                    </td>

                                                   <td >
                                                     


                                          <?php
                                            $edu_list = array(
                                            'SD'  => 'SD',
                                            'SMP'    => 'SMP',
                                            'SMA'   => 'SMA',
                                            'D3'   => 'D3',
                                            'D4'   => 'D4',
                                            'S1' => 'S1 ',
                                            'S2' => 'S2',
                                            'S3' => 'S3'
                                          );
                                            echo form_dropdown("siblings[$index][education]", $edu_list, 
                                            (!empty($sibling))?$sibling->sibling_edu_level:"", 
                                            '   class="form-control" autocomplete="on"');
                                          ?>
                                                    </td>
                                                    <td ><a  class="btn remove-siblings" id='<?php echo $index;?>'>X</a></td> 
                                                </tr>
                                           <?php  $index++; }
                                        }?>
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                    </div> 

                    <div class="col-lg-6"> 
                        <div class="panel panel-default">
                            <div class="panel-heading"> 
                                Anak
                            </div>
                            <div class="panel-body">   
                             <div class="col-lg-12"> 
                                 <a id="add-family" class="btn btn-primary">Tambah</a><br><br>
                                </div>
                                 <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-family-list">
                                    <thead>
                                    <tr> 
                                        <th  >Nama Anak</th>
                                        <th  >Umur</th> 
                                        <th >Pendidikan</th> 
                                         <th >X</th> 
                                    </tr>
                                    </thead>
                                    <tbody>
                                         <?php if(!empty($childrens)){
                                            $index = 0;
                                            foreach ($childrens as $children) { ?>
                                                <tr id="remove-family-<?php echo $index;?>">
                                                    <td >
                                                        <input type="text" name="family[<?php echo $index;?>][name]" class="form-control no-special-char"
                                                        value="<?php  echo $children->child_name;?>" ></td>
                                                    <td >
                                                        <input type="text" name="family[<?php echo $index;?>][age]" 
                                                        class="form-control qty-input"
                                                         value="<?php  echo $children->child_age;?>"> 
                                                    </td>
                                                   <td >
                                                    

                                                      <?php
                                            $edu_list = array(
                                            'SD'  => 'SD',
                                            'SMP'    => 'SMP',
                                            'SMA'   => 'SMA',
                                            'D3'   => 'D3',
                                            'D4'   => 'D4',
                                            'S1' => 'S1 ',
                                            'S2' => 'S2',
                                            'S3' => 'S3'
                                          );
                                            echo form_dropdown("family[$index][education]", $edu_list, 
                                            (!empty($children))?$children->child_edu_level:"", 
                                            '   class="form-control" autocomplete="on"');
                                          ?>
                                                    </td>
                                                    <td ><a  class="btn remove-family" id='<?php echo $index;?>'>X</a></td> 
                                                </tr>
                                           <?php  $index++; }
                                        }?>
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                    </div>  
                </div>
                
            </div>
            <!-- /.panel-body -->
        </div>
        <!-- /.panel -->
    </div>
  