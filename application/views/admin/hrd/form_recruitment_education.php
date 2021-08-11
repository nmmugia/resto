 
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-lg-12"> 
                    <div class="panel panel-default">
                      <div class="panel-heading">   
                            Pendidikan Formal 
                      </div>
                        <div class="panel-body">  
                            <div class="col-lg-12"> 
                             <a id="add-edu" class="btn btn-primary">Tambah</a> <br><br>
                            </div>

                             <div class="col-lg-12"> 
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-edu">
                                <thead>
                                <tr> 
                                    <th  >Tahun Lulus</th>
                                    <th  >Nama Sekolah</th> 
                                    <th >Kota</th> 
                                    <th >Ijasah</th> 
                                    <th ></th> 
                                </tr>
                                </thead>
                                <tbody>
                                     <?php if(!empty($educations)){
                                        $index = 0;
                                        foreach ($educations as $education) { ?>
                                           <tr id="remove-edu-<?php echo $index;?>"> 
                                                <td >
                                                    <input type="text" name="edu[<?php echo $index;?>][period]" 
                                                    class="form-control  "
                                                    value="<?php  echo $education->school_period;?>">
                                                </td>
                                                <td >
                                                    <input type="text" name="edu[<?php echo $index;?>][school_name]" 
                                                    class="form-control no-special-char"
                                                    value="<?php  echo $education->school_name;?>">
                                                </td> 
                                               <td >
                                                    <input type="text" name="edu[<?php echo $index;?>][city]" 
                                                    class="form-control no-special-char"
                                                    value="<?php  echo $education->school_city;?>"
                                                    >
                                                </td>  
                                                <td >
                                                    <input type="text" name="edu[<?php echo $index;?>][legacy]"
                                                     class="form-control no-special-char"
                                                     value="<?php  echo $education->school_ijazah;?>">
                                                </td>
                                                <td >
                                                    <a  class="btn remove-edu" id='<?php echo $index;?>'>X</a>
                                                </td>
                                            </tr>
                                       <?php  $index++; }
                                    }?>
                                </tbody>
                            </table>
                            </div>
                              
                        </div>
                    </div>
                </div> 
                <div class="col-lg-12"> 
                    <div class="panel panel-default">
                      <div class="panel-heading">   
                            Kursus
                             
                      </div>
                        <div class="panel-body">  
                            <div class="col-lg-12"> 
                             <a id="add-courses"  class="btn btn-primary">Tambah</a><br><br>
                            </div>

                             <div class="col-lg-12"> 
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-courses">
                                <thead>
                                <tr> 
                                    <th >Bidang Kursus</th>
                                    <th >Lama</th> 
                                    <th >Tempat</th> 
                                    <th >keterangan</th> 
                                     <th ></th> 
                                </tr>
                                </thead>
                               <tbody>
                                    <?php if(!empty($courses)){
                                        $index = 0;
                                        foreach ($courses as $course) { ?>
                                            <tr id="remove-courses-<?php echo $index;?>"> 
                                                <td >
                                                    <input type="text" name="courses[<?php echo $index;?>][course_name]" 
                                                    class="form-control no-special-char" 
                                                     value="<?php  echo $course->course_name;?>">
                                                </td>
                                                <td >
                                                    <input type="text" name="courses[<?php echo $index;?>][course_time]"
                                                     class="form-control" placeholder="2013-2014"
                                                      value="<?php  echo $course->course_period;?>">
                                                     </td>
                                                <td >
                                                    <input type="text" name="courses[<?php echo $index;?>][course_place]" 
                                                    class="form-control no-special-char" placeholder="Bandung"
                                                     value="<?php  echo $course->course_place;?>">
                                                </td>
                                                <td >
                                                    <input type="text" name="courses[<?php echo $index;?>][course_description]" 
                                                    class="form-control no-special-char"
                                                     value="<?php  echo $course->course_description;?>">
                                                </td>
                                                <td >
                                                    <a  class="btn remove-courses" id='<?php echo $index;?>'>X</a>
                                                </td>
                                            </tr>
                                       <?php  $index++; }
                                    }?>
                                </tbody>
                            </table>
                            </div>
                              
                        </div>
                    </div>
                </div> 

                <div class="col-lg-12"> 
                    <div class="panel panel-default">
                      <div class="panel-heading">   
                            Pengalaman Bekerja                             
                      </div>
                        <div class="panel-body">  
                            <div class="col-lg-12"> 
                             <a id="add-experience"  class="btn btn-primary">Tambah</a><br><br>
                            </div>

                             <div class="col-lg-12"> 
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-experience">
                                <thead>
                                <tr> 
                                    <th  >Perusahaan</th>
                                    <th  >Periode</th> 
                                    <th >Jabatan</th> 
                                    <th >Alasan Pindah</th> 
                                    <th ></th> 
                                </tr>
                                </thead>
                                <tbody>
                                 <?php if(!empty($experiences)){
                                        $index = 0;
                                        foreach ($experiences as $experience) { ?>
                                        <tr id="remove-experience-<?php echo $index;?>"> 
                                            <td >
                                                <input type="text" name="experience[<?php echo $index;?>][experience_company]" 
                                                class="form-control no-special-char"
                                                 value="<?php  echo $experience->company_name;?>">
                                            </td>
                                            <td >
                                                <input type="text" name="experience[<?php echo $index;?>][experience_period]"
                                                 class="form-control "
                                                  value="<?php  echo $experience->company_period;?>">
                                             </td> 
                                            <td >
                                                <input type="text" name="experience[<?php echo $index;?>][experience_job]"
                                                 class="form-control no-special-char"
                                                  value="<?php  echo $experience->company_job;?>">
                                             </td> 
                                            <td >
                                                <input type="text" name="experience[<?php echo $index;?>][experience_reason]" 
                                                class="form-control no-special-char"
                                                 value="<?php  echo $experience->company_reason;?>">
                                            </td> 
                                            <td >
                                                <a  class="btn remove-experience" id='<?php echo $index;?>'>X</a>
                                            </td>
                                        </tr>
                                    <?php  $index++; }
                                    }?>
                                </tbody>
                                   
                            </table>
                            </div>
                              
                        </div>
                    </div>
                </div> 

                  <div class="col-lg-12"> 
                    <div class="panel panel-default">
                      <div class="panel-heading">   
                            Pengalaman Organisasi                        
                      </div>
                        <div class="panel-body">  
                            <div class="col-lg-12"> 
                                <a id="add-org" class="btn btn-primary">Tambah</a><br><br>
                            </div>

                             <div class="col-lg-12"> 
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-org">
                                <thead>
                                <tr> 
                                    <th  >Nama Organisasi</th>
                                    <th  >Periode</th> 
                                    <th >Jabatan</th> 
                                    <th >Keterangan</th> 
                                    <th ></th> 
                                </tr>
                                </thead>
                                 <tbody>
                                       <?php if(!empty($orgs)){
                                        $index = 0;
                                        foreach ($orgs as $org) { ?>
                                         <tr id="remove-org-<?php echo $index;?>">
                                            <td >
                                                <input type="text" name="org[<?php echo $index;?>][experience_company]"
                                                class="form-control no-special-char"
                                                value="<?php  echo $org->org_name;?>">
                                            </td>
                                            <td >
                                                <input type="text" name="org[<?php echo $index;?>][experience_period]" 
                                                class="form-control "
                                                value="<?php  echo $org->org_period;?>">
                                            </td>
                                            <td >
                                                <input type="text" name="org[<?php echo $index;?>][experience_job]" 
                                                class="form-control no-special-char"
                                                value="<?php  echo $org->org_job;?>">
                                            </td>
                                            <td >
                                                <input type="text" name="org[<?php echo $index;?>][experience_reason]" 
                                                class="form-control no-special-char"
                                                value="<?php  echo $org->org_description;?>">
                                            </td>
                                            <td >
                                                <a  class="btn remove-org" id='<?php echo $index;?>'>X</a>
                                            </td> 
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
           
<?php echo form_close(); ?>

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>