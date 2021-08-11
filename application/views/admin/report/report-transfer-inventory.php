<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="transfer_inventory">
        <div class="clear-export"></div>
        <table class="col-lg-12 form-table-cms">
          <tbody>
            <tr>
              <td class="col-sm-2"><label>Store Asal</label></td>
              <td class="col-sm-3">
                    <?php echo form_dropdown('store_id_start', $store_id_start, '', 'id="store_id_start" field-name = "" class="form-control" autocomplete="on"');?></td>
              <td class="col-sm-2"><label>Outlet Asal</label></td>
              <td class="col-sm-3"><?php echo form_dropdown('outlet_id_start', $all_outlet_start, '', 'id="outlet_id_start" field-name = "" class="form-control" autocomplete="on"');?></td>
            </tr>
            <tr>
              <td class="col-sm-2"><label>Store Tujuan</label></td>
              <td class="col-sm-3"><?php echo form_dropdown('store_id_end', $store_id_end, '', 'id="store_id_end" field-name = "" class="form-control" autocomplete="on"');?></td>
              <td class="col-sm-2"><label>Outlet Tujuan</label></td>
              <td class="col-sm-3"><?php echo form_dropdown('outlet_id_end', $all_outlet_end, '', 'id="outlet_id_end" field-name = "" class="form-control" autocomplete="on"');?></td>
            </tr>
            <tr>
              <td class="col-sm-2"><label>Tanggal Awal</label></td>
              <td class="col-sm-3">
                <div class='input-group date' id="start_date">
                    <?php echo form_input(array(
                      'name' => 'start_date',
                      'type' => 'text',
                      'class' => 'form-control date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")." 00:00"
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
              <td class="col-sm-2"><label>Tanggal Akhir</label></td>
              <td class="col-sm-3">
                <div class='input-group date' id="end_date">
                    <?php echo form_input(array(
                      'name' => 'end_date',
                      'type' => 'text',
                      'class' => 'form-control end_date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")." 23:59"
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td class="col-sm-2"><label>Nama Barang</label></td>
              <td colspan="3" class="col-sm-8">
                <select class="form-control" data-width="100%" name="inventory_id" id="inventory_id">
                  <option value="">Semua Inventory</option>
                  <?php foreach($inventory_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="4" align="right">
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
    <h4><label>Laporan Transfer Inventory</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($start_date))?>
    -<?php echo date("d/m/Y",strtotime($end_date));?>
    </h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Store Awal</th>
        <th>Outlet Awal</th>
        <th>Store Tujuan</th>
        <th>Outlet Tujuan</th>
        <th>Nama</th>
        <th>Qty</th>
        <th>Unit</th>
        <th>Tanggal Pembelian</th>
        <th>Harga Pembelian</th>
        <th>Tanggal Pengiriman</th>
      </tr>
    </thead>
    <tbody>
    <?php
    foreach($results as $d){
        echo '<tr>';
        echo '<td>'.$d->storestart.'</td>';
        echo '<td>'.$d->origin.'</td>';
        echo '<td>'.$d->storeend.'</td>';
        echo '<td>'.$d->destination.'</td>';
        echo '<td>'.$d->inventoryname.'</td>';
        echo '<td>'.$d->quantity.'</td>';
        echo '<td>'.$d->unit.'</td>';
        echo '<td>'.date("d/m/Y H:i:s",strtotime($d->podate)).'</td>';
        echo '<td>'.number_format($d->price).'</td>';
        echo '<td>'.date("d/m/Y H:i:s",strtotime($d->date)).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
  </table>
</div>


    </div>
    <input type="hidden" id="report_type" value="transfer_inventory"/>
  </div>
</div>