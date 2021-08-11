<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="pending_bill">
        <input type="hidden" id="pending_type" name="pending_type" value="<?php echo $pending_type; ?>">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
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
              <td colspan="2" align="right">
                        <!-- <button id="export_xls" class="btn btn-default hide_btn" style="display: none">Export XLS</button> -->
                        <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                        <button id="export_pdf" class="btn btn-success hide_btn" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                        
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
    <h4><label><?php echo $subtitle; ?></label></h4>
    <h5><?php echo date("d F Y",strtotime($start_date))." s/d ".date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr class="vertical_middle">
        <th width="10%">Tanggal Transaksi</th>
        <th width="10%">Waktu Transaksi</th>
        <th width="10%">Tipe Order</th>
        <th width="10%"><?php echo ($pending_type == 6) ? "Company" : (($pending_type == 7) ? "Employee" : ""); ?></th>
        <th width="10%">Telepon</th>
        <th width="10%">Nomor Bill</th>
        <th width="10%">Nomor Order</th>
        <th width="10%">Total Transaksi</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total=0;
      foreach($results as $d){
        $total+=$d->total_transaction;
        echo '<tr>';
        echo '<td>'.date("d F Y",strtotime($d->payment_date)).'</td>';
        echo '<td>'.date("H:i:s",strtotime($d->start_order))." s/d ".date("H:i:s",strtotime($d->end_order)).'</td>';
        echo '<td>'.($d->is_delivery==1 ? "Delivery" : ($d->is_take_away==1 ? "Takeaway" : "Dine In")).'</td>';
        echo '<td>'.$d->customer_name.'</td>';
        echo '<td>'.$d->customer_phone.'</td>';
        echo '<td>'.$d->receipt_number.' <a target="_blank" href="'.base_url(SITE_ADMIN."/reports/detail_transaction/".$d->receipt_number).'" class="btn btn-default btn-xs pull-right" style="'.($is_print==true ? "display:none;" : "").'"><i class="fa fa-search"></i></a></td>';
        echo '<td>'.$d->order_id.'</td>';
        echo '<td align="right">'.number_format($d->total_transaction,2).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr style="background-color:lightgray;font-weight:bold;">
        <td align="center" colspan="7">TOTAL</td>
        <td align="right"><?php echo number_format($total,2) ?></td>
      </tr>
    </tfoot>
  </table>
</div>


    </div>
    <input type="hidden" id="report_type" value="pending_bill"/>
  </div>
</div>