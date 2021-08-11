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
                                    <label for="floor_name" class="col-sm-3 control-label">Jabatan</label> 
                                    <label for="floor_name" class="col-sm-3 control-label"><?php echo $jabatan;?></label> 
                                </div> 
                                
                            </div>
                            <!-- /.row (nested) -->
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <!-- /.panel -->
                </div>


                <div class="panel panel-default">
                    <div class="panel-heading">Penambah</div>
                    <div class="panel-body">
                        <div class="row">
                           
                            <div class="col-lg-12">
                                <table class="table table-striped" id="enhancer_container"
                                       style="margin: 20px 0!important;">
                                    <?php
                                    $counterEnhancer = 0;
                                    foreach($enhancer_sal_component_static as $e){
                                      $e->salary_component_id=$e->id;
                                      $e->salary_component_name=$e->name;
                                      $e->value=0;
                                      $check=false;
                                      foreach ($data_enhancer_jobs_component as $po) {
                                        if($e->salary_component_id==$po->salary_component_id)$check=true;
                                      }
                                      if($check==false){
                                        add_enhancer_func($e, $counterEnhancer,$form_data, $enhancer_sal_component_dropdwn);
                                        $counterEnhancer++;
                                      }
                                    }
                                    if (! empty($data_enhancer_jobs_component)) {
                                     
                                        foreach ($data_enhancer_jobs_component as $po) { 
                                            add_enhancer_func($po, $counterEnhancer,$form_data, $enhancer_sal_component_dropdwn);
                                            $counterEnhancer++;
                                        }
                                    } ?>
                                    <input type="hidden" id="count_enhancer" value='<?php echo $counterEnhancer-1; ?>'/>
                                </table> 
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
                                    foreach($substrahend_sal_component_static as $e){
                                      $e->salary_component_id=$e->id;
                                      $e->salary_component_name=$e->name;
                                      $e->value=0;
                                      $check=false;
                                      foreach ($data_subtrahend_jobs_component as $po) {
                                        if($e->salary_component_id==$po->salary_component_id)$check=true;
                                      }
                                      if($check==false){
                                        add_substrahend_func($e, $counterSubtrahend,$form_data, $substrahend_sal_component_dropdwn);
                                        $counterSubtrahend++;
                                      }
                                    }
                                    if (! empty($data_subtrahend_jobs_component)) { 
                                        foreach ($data_subtrahend_jobs_component as $po) { 
                                            add_substrahend_func($po, $counterSubtrahend,$form_data, $substrahend_sal_component_dropdwn);
                                            $counterSubtrahend++;
                                        }
                                    } ?>
                                    <input type="hidden" id="count_subtrahend" value='<?php echo $counterSubtrahend-1; ?>'/>
                                </table> 
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
                             
                            <div class="form-group">
                                    <div class="col-sm-offset-3 col-sm-10">
                                        <button type="submit" name="btnAction" value="save"
                                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                                        </button>
                                        <button type="submit" name="btnAction" value="save_exit"
                                                class="btn btn-primary">
                                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                                        </button>  
                                        <a href="<?php echo base_url(SITE_ADMIN . '/hrd/setting_jobs_list'); ?>"
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
    </div>
<?php echo form_close(); ?>

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
                form_dropdown('enhancer['.$count.'][salary_component_id]', 
                    $outlet_ddl, 
                    $data->salary_component_id, 
                    'class="form-control 
                    requiredDropdown" 
                    autocomplete="off"
                    '.($data->is_static==1 ? "style='display:none;'" : "").'
                ').($data->is_static==1 ? $data->salary_component_name : "").'
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control NumericDecimal"
                     id="ingredient_amount_chained_'.$count.'"
                     field-name="jumlah" placeholder="Jumlah"
                     name="enhancer[' . $count . '][quantity]" value="' . $data->value . '"/> 
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
                form_dropdown('subtrahend['.$count.'][salary_component_id]', 
                    $outlet_ddl, 
                    $data->salary_component_id, 
                    ' class="form-control 
                    requiredDropdown" 
                    autocomplete="off"
                    '.($data->is_static==1 ? "style='display:none;'" : "").'
                ').($data->is_static==1 ? $data->salary_component_name : "").'
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control NumericDecimal"
                      field-name="jumlah" 
                       placeholder="Jumlah"
                     name="subtrahend[' . $count . '][quantity]" value="' . $data->value . '"/>
                </div>
                <div class="col-md-1">
                    <button id="remove_subtrahend_' . $count . '" type="button" class="btn btn-mini btn-danger pull-right remove_subtrahend" '.($data->is_static==1 ? "style='display:none;'" : "").'><i class="fa fa-trash-o"></i></button>
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