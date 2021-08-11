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
                                    <label for="floor_name" class="col-sm-3 control-label">Nama Template Audit</label> 
                                    <div class="col-sm-7">
                                        <input type="text" name="name" class="form-control" value="<?php echo $data_templates->name; ?>">
                                        <input type="hidden" name="template_id" class="form-control" value=" <?php echo $template_id; ?>">
                                    </div>
                                </div>  
                                 <div class="form-group">
                                    <label for="floor_name" class="col-sm-3 control-label">Deskripsi</label> 
                                    <div class="col-sm-7">
                                    <textarea name="description" class="form-control"><?php echo $data_templates->description; ?> </textarea>
                                    </div>
                                </div>   
                            </div>
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body" id="category_list">
                                        <div class="col-lg-6 col-sm-offset-3">
                                            <div class="panel panel-default">
                                                <div class="panel-body" id="child-list">
                                                <?php
                                                    $indexCategory = 0;
                                                    foreach ($data_categories as $data) { ?> 
                                                       <div class="col-lg-12" id="category-<?php echo $indexCategory;?>">
                                                        <div class="form-group">
                                                            <label for="floor_name" class="col-sm-4 control-label">Nama Kategori</label> 
                                                            <div class="col-sm-8">
                                                               
                                                                <input type="text" name="category[]" class="form-control" value=" <?php    echo $data->name_category;?>">
                                                            </div>
                                                        </div>   
                                                        <div class="col-lg-12" id="detail-container-<?php echo $indexCategory;?>">
                                                        <?php  
                                                         $indexDetail = 0;
                                                            foreach ($data->detail as $detail) { ?> 
                                                            <div class="panel panel-default" id="category-detail-<?php echo $indexDetail; ?>">
                                                                <div class="panel-body">
                                                                    <div class="col-lg-12">
                                                                         <div class="form-group">
                                                                            <label for="floor_name" class="col-sm-2 control-label">Nama</label> 
                                                                            <div class="col-sm-10">
                                                                             
                                                                             <input type="text" 
                                                                                    name="detail_category[<?php echo $indexCategory;?>][<?php echo $indexDetail;?>][name]" 
                                                                                    class="form-control no-special-char"
                                                                                    value="<?php echo $detail->name;?>"
                                                                                    >
                                                                            </div>
                                                                        </div>    
                                                                    </div>
                                                                     <div class="col-lg-12">
                                                                         <div class="form-group">
                                                                            <label for="floor_name" class="col-sm-2 control-label">Point</label> 
                                                                            <div class="col-sm-10">
                                                                                
                                                                                 <input type="text" 
                                                                                    name="detail_category[<?php echo $indexCategory;?>][<?php echo $indexDetail;?>][point]" 
                                                                                    class="form-control qty-input"
                                                                                      value="<?php echo $detail->point;?>"
                                                                                    >
                                                                            </div>
                                                                        </div>    
                                                                    </div> 
                                                                </div>   
                                                            </div>   
                                                            <?php $indexDetail++; }?>
                                                        </div> 
                                                        <div class="col-lg-12">
                                                            <div class="form-group"> 
                                                                <div class="col-sm-4 col-sm-offset-5">
                                                                     <a  category-id="<?php echo $indexCategory;?>"    class="btn btn-default add_audit_detail_category">Tambah</a>
                                                                </div>
                                                            </div>    
                                                        </div>  
                                                    </div>  

                                                <?php   $indexCategory++ ;
                                                }
                                                ?>
                                                   
                                                      
                                                </div>  
                                            </div>  
                                        </div> 
                                    </div>  
                                </div>  
                            </div>   
                             <div class="col-lg-12">
                                <div class="form-group">
                                    <div class="col-lg-2 col-sm-offset-5">
                                         <a  id="add_audit_category"   class="btn btn-default">Tambah Kategori</a>
                                    </div>
                                 </div>  
                             </div>
                            <div class="col-lg-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                         <div class="form-group">
                                            <div class="col-sm-offset-4 col-sm-6">
                                                <button type="submit" name="btnAction" value="save"
                                                        class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                                </button>
                                                <button type="submit" name="btnAction" value="save_exit"
                                                        class="btn btn-primary">
                                                    <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                                </button>  
                                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_audit/setting_audit'); ?>"
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