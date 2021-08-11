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