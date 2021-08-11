<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

?>
<div class="col-lg-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <form id="formFilter" method="get" target="_blank">
                <input type="hidden" id="type" name="type" value="cost_opname">
                <div class="clear-export"></div>
                <table class="col-lg-8 form-table-cms">
                    <tbody>
                        <tr>
                            <td><label>Inventory</label></td>
                            <td class="col-sm-8">
                               <?php 
                               echo form_dropdown('inventory_id', $all_inventories, '', 
                                'id="inventory_id" field-name = "Inventory" 
                                class="form-control select2" autocomplete="on"');
                                ?>
                                
                            </td>
                            
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
                        <td class="col-sm-8"><!-- div class="col-sm-6">
                            <input id="end_date" type="text" name="end_date"  class="date-input form-control"/>
                        </div> -->


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
                            <!-- <button id="export_xls" class="btn btn-default hide_btn" style="float:right;display: none">Export XLS</button> -->
                            <button id="filter_submit" class="btn btn-default" ><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                            <button id="export_pdf" class="btn btn-success hide_btn"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                            
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
            <div class="clearfix"></div>
        </div>
        <div id="report_content">
            <style>
  table th, table td {
    word-wrap: break-word;
    max-width: 50px;
  }
  .table th
  {
    text-align:center;
  }
  table {
    width: 100%;   
  }
  .bold{
    font-weight:bold;
  }
  th {
    height: 50px;
  }
  table {
    border-collapse: collapse;
  }
  .border{
    margin-bottom:15px;
  }
  .border td, .border th{
    border: solid 1px #000;
    padding-left: 5px;
    padding-right: 5px;
  }
  .text-right{
    text-align:right;
  }
  .text-center{
    text-align:center;
  }
  h4,h5{
    margin-top:3px;
    margin-bottom:3px;
  }
  .is_print{
    font-size:11px;
  }
</style>
<div class="panel-body <?php echo ($is_print==true ? "is_print" : ""); ?>">
  <div class="text-center">
    <h5></h5>
    <h4><label>Laporan Cost Opname</label></h4>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Inventory</th>
        <th>Tanggal</th>
        <th>Qty</th>
        <th>Harga</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $counter=1;
      $total_price = 0;
      $quantity = 0;
      foreach($inventories as $inventory){          
          $quantity += doubleval($inventory->quantity);
          $cost = $inventory->quantity * $inventory->price;
          $total_price += $cost;
          echo '<tr>';
          echo '<td>'.$counter.'</td>';
          echo '<td style="padding-left:15px;">'.$inventory->name.'</td>';
          echo '<td>'.$inventory->created_at.'</td>';
          echo '<td>'.$inventory->quantity.'</td>';
          echo '<td>'.convert_rupiah($cost).'</td>';
          echo '</tr>';
          $counter++;       
      }
    ?> 
      <tr>
        <td colspan="2"></td>
        <td colspan="2">Total Opname : </td>
        <td><?php echo $quantity ?></td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td colspan="2">Total Cost Opname : </td>
        <td><?php echo convert_rupiah($total_price) ?></td>
      </tr>
    </tbody>
  </table>
</div>


        </div>
        <input type="hidden" id="report_type" value="cost_opname"/>
        <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url; ?>"/>
    </div>
</div>