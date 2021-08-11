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