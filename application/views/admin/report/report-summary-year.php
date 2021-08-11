<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="summary_year">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Tahun</label></td>
              <td class="col-sm-8">
                <select class="form-control" id="filter_year" name="year">
                  <?php for($x=date("Y");$x>=date("Y")-5;$x--): ?>
                  <option value="<?php echo $x ?>"><?php echo $x ?></option>
                  <?php endfor; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
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
    <h4><label>Laporan Summary Transaksi Per Tahun</label></h4>
    <h5><?php echo "Tahun ".$year;?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Bulan</th>
        <th>Total DineIn</th>
        <th>Total Delivery</th>
        <th>Total Take Away</th>
        <th>Total Kas Kecil</th>
        <th>Total Customer</th>
        <th>Grand Total<br><span style="font-weight:normal;font-size:11px;">(Dinein+Delivery<br>+Takeaway-Kas Kecil)</span></th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total=array(
        "dinein"=>0,
        "delivery"=>0,
        "takeaway"=>0,
        "petty_cash"=>0,
        "customer"=>0, 
        "total"=>0, 
      );
      foreach($results as $d){
        $subtotal=$d->total_dinein+$d->total_delivery+$d->total_takeaway-$d->total_petty_cash;
        $total['dinein']+=$d->total_dinein;
        $total['delivery']+=$d->total_delivery;
        $total['takeaway']+=$d->total_takeaway;
        $total['customer']+=$d->total_customer;
        $total['petty_cash']+=$d->total_petty_cash;
        $total['total']+=$subtotal;
        echo '<tr>';
        echo '<td>'.$d->name.'</td>';
        echo '<td class="text-right">'.number_format($d->total_dinein,0).'</td>';
        echo '<td class="text-right">'.number_format($d->total_delivery,0).'</td>';
        echo '<td class="text-right">'.number_format($d->total_takeaway,0).'</td>';
        echo '<td class="text-right">'.number_format($d->total_petty_cash,0).'</td>';
        echo '<td class="text-right">'.number_format($d->total_customer,0).'</td>';
        echo '<td class="text-right">'.number_format($subtotal,0).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr style="font-weight:bold;">
        <td class="text-center"><b>Total</b></td>
        <td class="text-right"><?php echo number_format($total['dinein'],0) ?></td>
        <td class="text-right"><?php echo number_format($total['delivery'],0) ?></td>
        <td class="text-right"><?php echo number_format($total['takeaway'],0) ?></td>
        <td class="text-right"><?php echo number_format($total['petty_cash'],0) ?></td>
        <td class="text-right"><?php echo number_format($total['customer'],0) ?></td>
        <td class="text-right"><?php echo number_format($total['total'],0) ?></td>
      </tr>
    </tfoot>
  </table>
</div>

</div>
    <input type="hidden" id="report_type" value="summary_year"/>
  </div>
</div>