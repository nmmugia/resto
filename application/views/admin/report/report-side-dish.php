<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
<form id="formFilter" method="get" target="_blank">
  <input type="hidden" id="report_type" value="side_dish" name="type"/>
    <div class="clear-export"></div>
            <table class="col-lg-8 form-table-cms">
                <tbody>
                    <tr>
                    <td><label>Menu</label></td>
                    <td>   <?php 
                    echo form_dropdown('menu_id', $all_menus, '', 
                        'id="menu_id" field-name = "" 
                        class="form-control select2" autocomplete="on"');
                        ?></td>

                     </tr>
                    

                    <tr>
                    <td><label>Waktu Mulai</label></td>
                    <td class="col-sm-8">
                            <div class='input-group date ' id='start_date'>
                             <?php echo form_input(array('name' => 'start_date',
                               'id' => 'input_start_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false',
                               'value'=>date("Y-m-d")." 00:00",

                               )); ?>
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
                   
                </tr>
                <tr>
                    <td><label>Waktu Akhir</label></td>
                    <td class="col-sm-8">
                            <div class='input-group date ' id='end_date'>
                             <?php echo form_input(array('name' => 'end_date',
                               'id' => 'input_end_date',
                               'type' => 'text',
                               'class' => 'form-control date',
                               'onkeydown'=>'return false',
                 'value'=>date("Y-m-d")." 23:59",

                               )); ?>
                               <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                        </div> 
                    </td>
                    
                </tr>
               
               
                <tr>
                    <td colspan="4" align="right">
                        <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                        <!-- <button id="export_pdf" class="btn btn-success hide_btn"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button> -->
                        
                    </td>
                </tr>
                </tbody>
            </table>
</form>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">


        
            <table class="table table-striped table-bordered table-hover dt-responsive width100" cellspacing="0" width="100%" id="table-side-dish">
                <thead>
                <tr>
                    <th>Nama Side Dish</th>
                    <th>Harga</th>
                    <th>Nama Menu</th>
                    <th>No Bill</th>
                </tr>
                </thead>
            </table>

            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>