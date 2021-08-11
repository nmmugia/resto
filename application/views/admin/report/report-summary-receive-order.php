<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="summary_receive_order">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
          <tr>
              <td><label>Supplier</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" name="supplier_id" id="supplier_id">
                  <option value="">Semua Supplier</option>
                  <?php foreach($supplier_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
          <tr>
              <td><label>Tipe Pembayaran</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" name="payment_method" id="payment_method">
                  <option value="">Semua Tipe Pembayaran</option>
                  <option value="1">Cash</option>
                  <option value="2">Kas Bon</option>
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
  .vertical_middle td,.vertical_middle th{
    vertical-align: middle !important;
  }
</style>
<div class="panel-body <?php echo ($is_print==true ? "is_print" : ""); ?>">
  <div class="text-center" style="margin-bottom:15px;">
    <h3><?php echo $data_store->store_name;?></h3>
    <h4><label>Laporan Summary Penerimaan Barang</label></h4>
    <h5><?php echo (sizeof($supplier)>0 ? "Supplier : ".$supplier->name : "")." | ".($payment_method!="" ? "Tipe Pembayaran : ".($payment_method==1 ? "Cash" : "Kas Bon") : "");?></h5>
    <h5><?php echo date("d F Y",strtotime($start_date))." s/d ".date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr class="vertical_middle">
        <th width="7%" rowspan="2">Tanggal Datang</th>
        <th width="15%" rowspan="2">Supplier</th>
        <th width="9%" rowspan="2">Nomor PO</th>
        <th width="9%" rowspan="2">Nomor Penerimaan</th>
        <th width="5%" rowspan="2">Tipe</th>
        <th width="7%" rowspan="2">Total</th>
        <th width="7%" rowspan="2">Diskon</th>
        <th width="13%" colspan="2">Subtotal</th>
      </tr>
      <tr>
        <th>Cash</th>
        <th>Kas Bon</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total_cash=0;
      $total_cash_bon=0;
      foreach($results as $d){
        echo '<tr>';
        echo '<td>'.date("d F Y",strtotime($d->incoming_date)).'</td>';
        echo '<td>'.$d->name.'</td>';
        echo '<td>'.$d->number.'</td>';
        echo '<td>'.$d->payment_no.'</td>';
        echo '<td>'.($d->payment_method==1 ? "Cash" : "Kas Bon").'</td>';
        echo '<td align="right">'.number_format($d->total,2).'</td>';
        echo '<td align="right">'.number_format($d->discount,2).'</td>';
        if($d->payment_method==1){
          $total_cash+=$d->total-$d->discount;
          echo '<td align="right">'.number_format($d->total-$d->discount,2).'</td>';
          echo '<td></td>';
        }else{
          $total_cash_bon+=$d->total-$d->discount;
          echo '<td></td>';
          echo '<td align="right">'.number_format($d->total-$d->discount,2).'</td>';
        }
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr style="background-color:lightgray;font-weight:bold;">
        <td align="center" colspan="7">TOTAL</td>
        <td align="right"><?php echo number_format($total_cash,2) ?></td>
        <td align="right"><?php echo number_format($total_cash_bon,2) ?></td>
      </tr>
    </tfoot>
  </table>
</div>

    </div>
    <input type="hidden" id="report_type" value="summary_receive_order"/>
  </div>
</div>