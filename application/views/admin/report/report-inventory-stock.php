<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="inventory_stock">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Tanggal Awal</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="input_date">
                    <?php echo form_input(array(
                      'name' => 'date',
                      'type' => 'text',
                      'class' => 'form-control date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td><label>Tanggal Akhir</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="report_end_date">
                    <?php echo form_input(array(
                      'name' => 'report_end_date',
                      'type' => 'text',
                      'class' => 'form-control end_date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
                <!--<button id="export_xls" type="submit" class="btn btn-default" style="float:right;display: none">Export Excel</button>-->
                <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <button id="export_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                
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
    <h5><?php echo @$data_store->store_name;?></h5>
    <h4><label>Laporan Stok Inventory</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($date))?>
    -<?php echo date("d/m/Y",strtotime($report_end_date));?>
    </h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Nama Inventory</th>
        <th>Masuk</th>
        <th>Terpakai</th>
        <th>Spoiled</th>
        <th>Stok Terakhir</th>
        <?php if($is_print!=true): ?>
        <th width="75">Aksi</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
    <?php
      foreach($results as $d){
        echo '<tr>';
        echo '<td>'.$d->name." ( ".$d->code." ) ".'</td>';
        echo '<td class="text-right">'.number_format($d->incoming_stock,2,',','.').'</td>';
        echo '<td class="text-right">'.number_format($d->used,2,',','.').'</td>';
        echo '<td class="text-right">'.number_format($d->spoiled,2,',','.').'</td>';
        echo '<td class="text-right">'.number_format($d->last_stock,2,',','.').' '.$d->code.'</td>';
        if($is_print!=true){
          echo '<td class="text-right">
            <a href="'.base_url(SITE_ADMIN).'/reports/inventory_stock_detail?inventory_id='.$d->id.'&uom_id='.$d->uom_id.'&from_date='.$date.'&to_date='.$report_end_date.'" class="btn btn-xs btn-default" target="_blank" title="Detail"><i class="fa fa-search"></i> Detail</a>
          </td>';
        }
        echo '</tr>';
      }
    ?>
    </tbody>
  </table>
</div>
    </div>
    <input type="hidden" id="report_type" value="inventory_stock"/>
  </div>
</div>