<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="detail_inventory">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
          <tr>
              <td><label>Outlet</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" name="outlet_id" id="outlet_id">
                  <option value="">Semua Outlet</option>
                  <?php foreach($outlet_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->outlet_name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><label>Inventory</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" name="inventory_id" id="inventory_id">
                  <option value="">Semua Inventory</option>
                  <?php foreach($inventory_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><label>Dari Tanggal</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="input_date">
                    <?php echo form_input(array(
                      'name' => 'start_date',
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
              <td><label>Sampai Tanggal</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="report_end_date">
                    <?php echo form_input(array(
                      'name' => 'end_date',
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
              <td colspan="4" align="right">
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
  <div class="text-center" style="margin-bottom:15px;">
    <h3><?php echo $data_store->store_name;?></h3>
    <h4><label>Laporan Detail Inventory</label></h4>
    <h5><?php echo date("d F Y",strtotime($start_date))." s/d ".date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr>
        <th width="6%">Outlet</th>
        <th width="7%">Inventory</th>
        <th width="7%">Stok Awal</th>
        <th width="6%">Tanggal</th>
        <th width="7%">Penjualan</th>
        <th width="7%">Transfer</th>
        <th width="8%">Pembelian</th>
        <th width="7%">Opname</th>
        <th width="8%">Penerimaan</th>
        <th width="7%">Proses Inventory</th>
        <th width="6%">Spoiled</th>
        <th width="6%">Void</th> 
        <th width="6%">Retur</th>
        <th width="6%">Refund</th>
        <th width="7%">Stok Akhir</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $inventories=array();
      foreach($results as $r){
        if(!isset($inventories[$r->outlet_id."_".$r->inventory_id."_".$r->uom_id])){
          $inventories[$r->outlet_id."_".$r->inventory_id."_".$r->uom_id]=array(
            "data" => $r,
            "detail"=>array()
          );
        }
        array_push($inventories[$r->outlet_id."_".$r->inventory_id."_".$r->uom_id]['detail'],$r);
      }
      foreach($inventories as $i){
        $last_stock=$i['data']->beginning_stock;
        echo '<tr style="background:lightgray;">';
        echo '<td>'.$i['data']->outlet_name.'</td>';
        echo '<td>'.$i['data']->name.'</td>';
        echo '<td align="right">'.number_format($i['data']->beginning_stock,2).' '.$i['data']->code.'</td>';
        echo '<td colspan="'.(sizeof($history_status)+2).'"></td>';
        echo '</tr>';
        foreach($i['detail'] as $d){
          if($d->range_status==1){
            echo '<tr>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td></td>';
            echo '<td>'.date("d/m/Y",strtotime($d->date)).'</td>';
            foreach($history_status as $h){
              if($h->id==$d->status){
                $last_stock+=$d->total_quantity;
                echo '<td align="right">'.number_format($d->total_quantity,2).' '.$d->code.'</td>';
              }else{
                echo '<td></td>';
              }
            }
            echo '<td align="right">'.number_format($last_stock,2).' '.$d->code.'</td>';
            echo '</tr>';
            
          }          
        }
      }
    ?>
    </tbody>
  </table>
</div>
      
    </div>
    <input type="hidden" id="report_type" value="detail_inventory"/>
  </div>
</div>