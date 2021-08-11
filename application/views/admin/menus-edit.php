<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 2:20 PM
 */

echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
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
                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#menuTab" data-toggle="tab">Menu</a>
                                    </li>
                                    <li><a href="#sideTab" data-toggle="tab">Side Dish</a>
                                    </li>
                                    <li><a href="#optionsTab" data-toggle="tab">Pilihan</a>
                                    </li>
                                    <li><a href="#ingredientTab" data-toggle="tab">Komposisi</a>
                                    </li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="menuTab" style="padding-top: 20px">
                                        <div class="col-lg-12">
                                            <div class="form-group">
                                                <label for="menu_name" class="col-sm-2 control-label">Menu</label>

                                                <div class="col-sm-10">
                                                    <?php echo form_input($menu_name); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="menu_hpp" class="col-sm-2 control-label">Harga Awal</label>

                                                <div class="col-sm-10">
                                                    <?php echo form_input($menu_hpp); ?>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="menu_price" class="col-sm-2 control-label">Harga</label>

                                                <div class="col-sm-10">
                                                    <?php echo form_input($menu_price); ?>
                                                </div>
                                            </div>
                                     
                                            <div class="form-group">
                                                <label for="outlet_id" class="col-sm-2 control-label"><?php echo $this->lang->line('outlet_title');?></label>

                                                <div class="col-sm-10">
                                                    <?php echo form_input($menu_outlet); ?>
                                                    
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="category_id" class="col-sm-2 control-label">Kategori</label>

                                                <div class="col-sm-10">
                                                    <?php echo form_input($menu_category); ?>
                                                    
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="color" class="col-sm-2 control-label">Warna(berdasarkan lama proses pembuatan)</label>
                                                <div class="col-sm-10">
                                                  <select name="color" class="form-control" disabled="">
                                                  <?php
                                                    echo "<option value=''>Tanpa Warna</option>";
                                                    foreach($color_lists as $key=>$c){
                                                      echo "<option value='".$key."' ".($form_data->color==$key ? "selected" : "")." style='background:".$key.";'>".$c."</option>";
                                                    }
                                                  ?>
                                                  </select>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="color" class="col-sm-2 control-label">Menu Proses ?</label>
                                                <div class="col-sm-10">
                                                  <label class="radio-inline">
                                                    <?php echo form_radio($is_instant_yes); ?>
                                                     <small>Ya</small>
                                                  </label>

                                                  <label class="radio-inline">
                                                    <?php echo form_radio($is_instant_no); ?>
                                                     <small>Tidak</small>
                                                  </label>
                                                  <br>

                                                  <small>Apakah menu ini diproses / dimasak terlebih dahulu ?</small>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="color" class="col-sm-2 control-label">Menu masuk checker ?</label>
                                                <div class="col-sm-10">
                                                  <label class="radio-inline">
                                                    <?php echo form_radio($process_checker_yes); ?>
                                                     <small>Ya</small>
                                                  </label>

                                                  <label class="radio-inline">
                                                    <?php echo form_radio($process_checker_no); ?>
                                                     <small>Tidak</small>
                                                  </label>
                                                  <br>

                                                  <small>Apakah menu ini setelah diproses masuk ke bagian checker ?</small>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="color" class="col-sm-2 control-label">Menu kena pajak / service ?</label>
                                                <div class="col-sm-10">
                                                  <label class="radio-inline">
                                                    <?php echo form_radio($use_taxes_yes); ?>
                                                     <small>Ya</small>
                                                  </label>

                                                  <label class="radio-inline">
                                                    <?php echo form_radio($use_taxes_no); ?>
                                                     <small>Tidak</small>
                                                  </label>
                                                  <br>

                                                  <small>Apakah menu ini kena pajak / service ?</small>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="icon_url" class="col-sm-2 control-label"><?php echo $this->lang->line('column_icon'); ?></label>

                                            </div>
                                            <?php
                                            if (! empty($form_data->icon_url)) {
                                                ?>
                                                <div class="form-group" id="primaryimage">
                                                    <label for="pages_slug" class="col-sm-2 control-label sr-only"><?php echo $this->lang->line('column_icon'); ?>
                                                        URL</label>

                                                    <div class="col-sm-10">
                                                        <img class="gc_thumbnail"
                                                             src="<?php echo base_url($form_data->icon_url); ?>"
                                                             style="padding:5px; border:1px solid #ddd"/>

                                                        
                                                    </div>
                                                </div>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade in" id="sideTab">
                                        <div class="col-lg-12">
                                        
                                            <div class="clearfix"></div>
                                            <table class="table table-striped" id="sidedish_container"
                                                   style="margin: 20px 0!important;">
                                                <?php
                                                $counter1 = 0;
                                                if (! empty($side_dish_value)) {
                                                    foreach ($side_dish_value as $po) {
                                                        add_side_dish_func($sidedish,$po, $counter1);
                                                        $counter1++;
                                                    }
                                                }else{
                                                    echo "<h4>tidak ada sidedish</h4>";
                                                } ?>
                                            </table>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade in" id="optionsTab">
                                        <div class="col-lg-12">

                                            <div class="clearfix"></div>
                                            <table class="table table-striped" id="options_container"
                                                   style="margin: 20px 0!important;">
                                                <?php
                                                $counter2 = 0;
                                                if (! empty($options_value)) {
                                                    foreach ($options_value as $po) {
                                                        add_option_func($po, $counter2);
                                                        $counter2++;
                                                    }
                                                }else{
                                                    echo "<h4>tidak ada pilihan</h4>";
                                                }?>
                                            </table>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <div class="tab-pane fade in" id="ingredientTab" style="">
                                        <div class="col-lg-12">

                                            <div class="clearfix"></div>
                                            <table class="table table-striped" id="ingredient_container"
                                                   style="margin: 20px 0!important;">
                                                <?php
                                                $counterIngredient = 0;
                                                if (! empty($menu_ingredient)) {
                                                    foreach ($menu_ingredient as $po) {
                                                        add_ingredient_func($po, $counterIngredient,$form_data, $data_inventory);
                                                        $counterIngredient++;
                                                    }
                                                }else{
                                                    echo "<h4>tidak ada komposisi</h4>";
                                                } ?>
                                            </table>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-sm-offset-1 col-sm-10">
                                            
                                            <a href="<?php echo base_url(SITE_ADMIN . '/menus'); ?>"
                                               class="btn btn-primary"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
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
    </div>
<input type="hidden" id="menu_ingredient" value='<?php echo json_encode($data_inventory) ?>'/>
<div class="side-dish-list" style="display:none;">
<?php echo form_dropdown('sidedish', $sidedish, "", 'id="side_dish"   class="form-control" autocomplete="off"');?>
</div>
<?php echo form_input($sidedishCount); ?>
<?php echo form_input($ingredientCount); ?>
<?php echo form_input($optionsCount); ?>
<?php echo form_input($optionsValueCount); ?>
<?php echo form_close(); ?>
<?php

function add_ingredient_func($ingredient, $count,$form_data ,$outlet_ddl)
{
    $stuff = '
    <tr id="ingredient-' . $count . '" class="countIngredient">
        <td>
            <div class="row">
                <div class="col-md-1"><a class="handle btn btn-mini"><i class="fa fa-bars"></i></a></div>
                <div class="col-md-4">'.
                 '<input type="text" class="form-control" value="'.$ingredient->name.'" readonly/>
                                             </div>
                <div class="col-md-3"><input type="text" class="form-control"
                                             id="ingredient_amount_chained_'.$count.'"
                                             readonly
                                             field-name="jumlah komposisi" placeholder="Jumlah"
                                             name="ingredient[' . $count . '][quantity]" value="' . $ingredient->quantity . '"/></div>
                <div class="col-md-3"><input type="text"  class="form-control"
                                             id="ingredient_unit_chained_'.$count.'"                                             
                                             field-name="bahan komposisi" placeholder="Satuan"
                                             value="' . $ingredient->unit . '" readonly /></div>
                
            </div>
        </td>
    </tr>
    ';
    echo replace_newline($stuff);
}


function add_side_dish_func($sidedish, $po, $count)
{
    // $stuff = '
    // <tr id="side-dish-' . $count . '" class="countside">
    //     <td>
    //         <div class="row">
    //             <div class="col-md-1"><a class="handle btn btn-mini"><i class="fa fa-bars"></i></a></div>
    //             <div class="col-md-4"><input type="text" class="form-control "
    //                                          field-name="Nama side dish" placeholder="Masukan nama side dish"
    //                                          readonly
    //                                          name="sidedishval[' . $count . '][]" value="' . $po->side_dish_name . '"/></div>
    //             <div class="col-md-3"><input type="text" class="form-control  "
    //                                         readonly
    //                                          field-name="Harga awal side dish" placeholder="Masukan harga awal"
    //                                          name="sidedishval[' . $count . '][side_dish_hpp]" value="' . $po->side_dish_hpp . '"/></div>
    //             <div class="col-md-3"><input type="text" class="form-control  " readonly
    //                                          field-name="Harga side dish" placeholder="Masukan harga" readonly
    //                                          name="sidedishval[' . $count . '][side_dish_price]" value="' . $po->side_dish_price . '"/></div>
                
    //         </div>
    //     </td>
    // </tr>
    // ';
    // echo replace_newline($stuff);

        $stuff = '
    <tr id="side-dish-' . $count . '" class="countside">
        <td>
            <div class="row">
                <div class="col-md-1"><a class="handle btn btn-mini"><i class="fa fa-bars"></i></a></div>
                <div class="col-md-4">'.
                form_dropdown('sidedishval['.$count.'][side_dish_id]', $sidedish, (int)$po->side_dish_id, 'field-name = "side dish" 
                    class="form-control requiredDropdown " autocomplete="off"  disabled').
                 '
                                             </div>
              
               
            </div>
        </td>
    </tr>
    ';
     // <div class="col-md-1">
     //                <button id="remove_sidedish_' . $count . '" type="button"
     //                        class="btn btn-mini btn-danger pull-right">
     //                    <i class="fa fa-trash-o"></i></button>
     //            </div>
    echo replace_newline($stuff);
    
}

function add_option_func($po, $count)
{
    $stuff = '
    <tr id="m-option-' . $count . '" class="countopt">
        <td>
            <div class="row">
                <div class="col-md-1"><a class="handle btn btn-mini"><i class="fa fa-bars"></i></a></div>
                <div class="col-md-10"><input type="text" readonly class="form-control "
                                              placeholder="Masukan nama pilihan" field-name="Nama Pilihan"
                                              name="options[' . $count . '][option_name]" value="' . $po->option_name . '"/></div>
                
            </div>
            <div class="clearfix"><p>&nbsp;</p></div>
            <div class="row">
                
            </div>
            <div class="row">
                <div class="col-md-10 col-md-offset-1">
                    <div class="option-items" id="option-items-' . $count . '">';
    if ($po->values) {
        $counter3 = 0;
        foreach ($po->values as $value) {
            $value = (object)$value;
            $stuff .= '
                    <div class="option-values-form countoptvalue-' . $count . '">
                        <div class="row">
                            <div class="col-md-1"><a class="handle-item btn btn-mini"><i class="fa fa-bars"></i></a></div>
                            <div class="col-md-10"><input type="text" readonly class="form-control "
                                                          placeholder="Masukan value pilihan" field-name="Value Pilihan"
                                                          name="options[' . $count . '][values][' . $counter3 . '][option_value_name]" value="' . $value->option_value_name . '"/>
                            </div>
                        </div>
                    </div>
                ';
            $counter3++;
        }
    }

    $stuff .= '
                    </div>
                </div>
            </div>
        </td>
    </tr>
    ';
    echo replace_newline($stuff);
}


function replace_newline($string)
{
    return trim((string)str_replace(array("\r", "\r\n", "\n", "\t"), ' ', $string));
}

?>