<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
?>
<div class="col-lg-12">
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
    <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
    ?>
    <ol class="breadcrumb">
        <li><a href="#">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/staff_list'); ?>">Staff</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/detail_staff/'.$form_data->user_id); ?>"><?php echo $form_data->name;?></a></li>
        <li class="active">Tambah History Gaji</li>
    </ol>
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
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">NIP</label> 
                            </div> 
                            <div class="col-md-3">
                                <?php echo $form_data->nip;?> 
                                <input type="hidden" name="employee_id" value="<?php echo $form_data->user_id; ?>"/>
                                <input type="hidden" name="jobs_id" value="<?php echo $form_data->jobs_id; ?>"/>
                                <input type="hidden" name="payroll_id" value="<?php echo $form_data->id; ?>"/>
                            </div>  
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Nama</label> 
                            </div> 
                            <div class="col-md-3">
                                <?php echo $form_data->name;?> 
                            </div>  
                        </div> 
                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Jabatan</label> 
                            </div> 
                            <div class="col-md-3">
                                <?php echo $form_data->jobs_name;?> 
                            </div>  
                        </div> 
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Period</label> 
                            </div> 
                            <div class="col-md-3">
                                 <?php echo $form_data->period;?> 
                            </div>  
                        </div>
                     </div>
                </div>
            <!-- /.panel -->
            </div>


            <div class="panel panel-default">
                <div class="panel-heading">Penerimaan</div>
                <div class="panel-body">
                    <div class="row">
                       
                        <div class="col-lg-12">
                            <table class="table table-striped" id="enhancer_container"
                                   style="margin: 20px 0!important;">
                                <?php
                                $counterEnhancer = 0;
                                if (! empty($data_enhancer_jobs_component)) {
                                 
                                    foreach ($data_enhancer_jobs_component as $po) { 
                                        add_enhancer_func($po, $counterEnhancer,$form_data, $enhancer_sal_component_dropdwn);
                                        $counterEnhancer++;
                                    }
                                } ?>

                                <input type="hidden" id="count_enhancer" value='<?php echo $counterEnhancer-1; ?>'/>
                            </table> 
                        </div>
                        <div class="col-lg-4 col-md-offset-5">
                             <a id="add_enhancer" href="#" class="btn btn-default">  Tambah Komponen</a>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>


            <div class="panel panel-default">
                    <div class="panel-heading">Pengurang</div>
                    <div class="panel-body">
                        <div class="row">
                           
                            <div class="col-lg-12">
                                <table class="table table-striped" id="subtrahend_container"
                                       style="margin: 20px 0!important;">
                                    <?php
                                    $counterSubtrahend = 0;
                                    if (! empty($data_subtrahend_jobs_component)) { 
                                        foreach ($data_subtrahend_jobs_component as $po) { 
                                            add_substrahend_func($po, $counterSubtrahend,$form_data, $substrahend_sal_component_dropdwn);
                                            $counterSubtrahend++;
                                        }
                                    } ?>
                                    <input type="hidden" id="count_subtrahend" value='<?php echo $counterSubtrahend-1; ?>'/>
                                </table> 
                            </div>


                            <div class="col-lg-4 col-md-offset-5">
                                <a class="btn btn-default" id="add_subtrahend">Tambah Komponen</a>
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>

             <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                       <!--  <div class="col-lg-12">
                             <div class="col-md-5">
                                <label for="floor_name" class=" control-label">Total Home Pay</label> 
                              </div> 
                              <div class="col-md-3">
                                <label for="floor_name" class="col-sm-3 control-label"> <?php echo $form_data->jobs_name;?> </label> 
                            </div> 
                            
                        </div> -->
                        <!-- /.row (nested) -->
                    </div>
                    <!-- /.panel-body -->
                </div>
            <!-- /.panel -->
            </div>

             <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                       <div class="form-group">
                            <div class="col-sm-offset-4 col-sm-8"> 
                                <button type="submit" name="btnAction" value="save_exit"
                                        class="btn btn-primary">
                                    <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                </button>  
                                <a href="<?php echo base_url(SITE_ADMIN . '/hrd_staff/detail_staff/'.$form_data->user_id); ?>"
                                   class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                            </div>
                        </div>
                    </div>
                    <!-- /.panel-body -->
                </div>
            <!-- /.panel -->
            </div>
         </div>
     </div>
    <!-- /.panel --> 
 <?php echo form_close(); ?>
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
 
<input type="hidden" id="data_enhancer_salary_component" value='<?php echo json_encode($data_enhancer_salary_component) ?>'/>
<input type="hidden" id="data_substrahend_salary_component" value='<?php echo json_encode($data_substrahend_salary_component) ?>'/>
<?php

function add_enhancer_func($data, $count,$form_data ,$outlet_ddl)
{ 
    $stuff = '
    <tr id="enhancer-' . $count . '" class="countIngredient">
        <td>
            <div class="row"> 
                <div class="col-md-5">'.
                form_dropdown('
                    enhancer['.$count.'][component_id]', 
                    $outlet_ddl, 
                    $data->component_id, 
                    'id="ingredient_id_chained_'.$count.'"  
                    class="form-control 
                    requiredDropdown ingredient_id_chained" 
                    autocomplete="off" 
                    url-data="'.base_url(SITE_ADMIN).'/menus/get_inventory_unit" ').
                 '
                                             </div>
                <div class="col-md-3">
                    <input type="text" class="form-control NumericDecimal"
                     id="ingredient_amount_chained_'.$count.'"
                     field-name="jumlah" placeholder="Jumlah"
                     name="enhancer[' . $count . '][quantity]" value="' . $data->value . '"/> 
                </div>
               
                <div class="col-md-1">
                    <button id="remove_subtrahend_' . $count . '" type="button"
                            class="btn btn-mini btn-danger pull-right remove_enhancer">
                        <i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </td>
    </tr> ';
    echo replace_newline($stuff);
}

function add_substrahend_func($data, $count,$form_data ,$outlet_ddl)
{ 
    $stuff = '
    <tr id="subtrahend-' . $count . '" class="countIngredient">
        <td>
            <div class="row"> 
                <div class="col-md-5">'.
                form_dropdown('
                    subtrahend['.$count.'][component_id]', 
                    $outlet_ddl, 
                    $data->component_id, 
                    'id="ingredient_id_chained_'.$count.'"  
                    class="form-control 
                    requiredDropdown ingredient_id_chained" 
                    autocomplete="off" 
                    url-data="'.base_url(SITE_ADMIN).'/menus/get_inventory_unit" ').
                 '
                                             </div>
                <div class="col-md-3">
                    <input type="text" class="form-control NumericDecimal"
                      field-name="jumlah" 
                       placeholder="Jumlah"
                     name="subtrahend[' . $count . '][quantity]" value="' . $data->value . '"/>
 
                </div>

                                            
               
                <div class="col-md-1">
                    <button id="remove_subtrahend_' . $count . '" type="button"
                            class="btn btn-mini btn-danger pull-right remove_subtrahend">
                        <i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </td>
    </tr> ';
    echo replace_newline($stuff);
}

function replace_newline($string)
{
    return trim((string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string));
}

?>