<div class="panel panel-default">
  <div class="panel-heading">Penerimaan</div>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-12">
        <table class="table table-striped" id="enhancer_container" style="margin: 20px 0!important;">
          <?php
          $counterEnhancer = 0;
          if (! empty($data_enhancer_jobs_component)) {
            foreach ($data_enhancer_jobs_component as $po) { 
              add_enhancer_func($po, $counterEnhancer,"", $enhancer_sal_component_dropdwn,$employee_component);
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
  </div>
</div>
<div class="panel panel-default">
  <div class="panel-heading">Pengurang</div>
  <div class="panel-body">
    <div class="row">
      <div class="col-lg-12">
        <table class="table table-striped" id="subtrahend_container" style="margin: 20px 0!important;">
          <?php
          $counterSubtrahend = 0;
          if (! empty($data_subtrahend_jobs_component)) { 
              foreach ($data_subtrahend_jobs_component as $po) { 
                  add_substrahend_func($po, $counterSubtrahend,"", $substrahend_sal_component_dropdwn,$total_pinjaman,$employee_component);
                  $counterSubtrahend++;
              }
          } ?>
          <input type="hidden" id="count_subtrahend" value='<?php echo $counterSubtrahend-1; ?>'/>
        </table> 
      </div>
      <div class="col-lg-4 col-md-offset-5">
        <a class="btn btn-default" id="add_subtrahend">Tambah Komponen</a>
      </div>
    </div>
  </div>
</div>
<?php

function add_enhancer_func($data, $count,$form_data ,$outlet_ddl,$employee_component)
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
                     name="enhancer[' . $count . '][quantity]" value="' . $employee_component[$data->component_id] . '"/> 
                </div>
                <div class="col-md-1">
                  <button id="remove_subtrahend_' . $count . '" type="button" class="btn btn-mini btn-danger pull-right remove_enhancer" '.($data->is_static==1 ? "style='display:none;'" : "").'><i class="fa fa-trash-o"></i></button>
                </div>
            </div>
        </td>
    </tr> ';
    echo replace_newline($stuff);
} 
function add_substrahend_func($data, $count,$form_data ,$outlet_ddl,$total_pinjaman,$employee_component)
{   
    if($data->component_id == 1){
        $data->value = $total_pinjaman;
    }
    $stuff = '
    <tr id="subtrahend-' . $count . '" class="countIngredient">
        <td>
            <div class="row"> 
                <div class="col-md-5">'.
                form_dropdown('
                    subtrahend['.$count.'][component_id]', 
                    $outlet_ddl, 
                    $data->component_id, 
                    'class="form-control 
                    requiredDropdown" 
                    autocomplete="off"
                    '.($data->is_static==1 ? "style='display:none;'" : "").'
                ').($data->is_static==1 ? $data->salary_component_name : "").'
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control NumericDecimal"
                      field-name="jumlah" 
                       placeholder="Jumlah"
                     name="subtrahend[' . $count . '][quantity]" value="' . $employee_component[$data->component_id] . '"/>
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