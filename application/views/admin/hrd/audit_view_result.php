<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 11:05 AM
 */

echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>

    <div class="col-lg-12" style="padding: 0 !important">
        <div class="result">
            <?php
            if (! empty($message_success)) {
                echo '<div class="alert alert-success" role="alert">';
                echo $message_success;
                echo '</div>';
            }
            if (! empty($message)) {
                echo '<div class="alert alert-danger" role="alert">';
                echo $message;
                echo '</div>';
            }
            ?>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="panel panel-default">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Nama Resto</label> 
                                    <div class="col-sm-7">
                                        <?php echo $data_process->store_name; ?>
                                    </div>
                                </div>  
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Period</label> 
                                    <div class="col-sm-7">
                                        <?php echo $data_process->period; ?>
                                    </div>
                                </div>  
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label"> Template</label> 
                                    <div class="col-sm-7">
                                        <?php echo $data_templates->name; ?>
                                    </div>
                                </div> 
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label"> Total Point</label> 
                                    <div class="col-sm-7">
                                        <?php echo $data_grade->grade; ?> of  <?php echo $data_grade->max; ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label"> Nilai</label> 
                                    <div class="col-sm-7">
                                        <?php  
                                        if($data_grade->grade == 0){
                                            $percentage = 0;
                                        }else{
                                            $percentage = ($data_grade->grade/$data_grade->max) * 100;
                                        }
                                            
                                          echo  number_format((float)$percentage, 2, '.', '')
                                         ?> %
                                    </div>
                                </div>      
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Deskripsi</label> 
                                    <div class="col-sm-7">
                                      <?php echo $data_process->description; ?>
                                    </div>
                                </div>   
                            </div>
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="category_list">
                                        <div class="col-lg-6 col-sm-offset-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                <?php
                                                    foreach ($data_categories as $data) { ?> 
                                                       <div class="col-lg-12" id="category-0">
                                                        <div class="form-group">
                                                            <label for="floor_name" class="col-sm-4 control-label">Nama Kategori</label> 
                                                            <div class="col-sm-8">
                                                                <?php  
                                                                echo $data->name_category;?>
                                                            </div>
                                                        </div>   
                                                        <div class="col-lg-12" id="detail-container-0">
                                                        <?php  
                                                            foreach ($data->detail as $detail) { ?> 
                                                            <div class="panel panel-default" id="category-detail-0">
                                                                <div class="panel-body">
                                                                    <div class="col-lg-12">
                                                                         <div class="form-group">
                                                                            <label for="floor_name" class="col-sm-2 control-label">Nama</label> 
                                                                            <div class="col-sm-10">
                                                                             <?php echo $detail->name;?>
                                                                            </div>
                                                                        </div>    
                                                                    </div>
                                                                     <div class="col-lg-12">
                                                                         <div class="form-group">
                                                                            <label for="floor_name" class="col-sm-2 control-label">Point</label> 
                                                                            <div class="col-sm-10">
                                                                                <?php echo $detail->value;?>
                                                                            </div>
                                                                        </div>    
                                                                    </div> 
                                                                </div>   
                                                            </div>   
                                                            <?php }?>
                                                        </div>   
                                                    </div> 



                                                <?php }
                                                ?>
                                                   
                                                      
                                                </div>  
                                            </div>  
                                        </div> 
                                    </div>  
                                </div>  
                            </div>  

                             <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                         <div class="form-group">
                                            <div class="col-sm-offset-5 col-sm-6">
                                                 
                                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_audit/process_audit'); ?>"
                                                   class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                                            </div>
                                        </div>
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
    </div>
<?php echo form_close(); ?>