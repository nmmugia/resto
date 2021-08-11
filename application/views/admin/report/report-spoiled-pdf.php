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
    <h4><label>Laporan Spoiled</label></h4>
		<?php if(sizeof($outlet)>0): ?>
    <h5><?php echo $outlet->outlet_name ?></h5>
		<?php endif; ?>
		<?php if(sizeof($inventory)>0): ?>
    <h5><?php echo $inventory->name ?></h5>
		<?php endif; ?>
    <h5></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr>
        <th width="10%">Outlet</th>
        <th>Inventory</th>
        <th width="9%">Tanggal</th>
        <th width="9%">Keterangan</th>
        <th width="9%">Quantity</th>
        <th width="9%">Cost Spoiled</th>
      </tr>
    </thead>
    <tbody>
    <?php
    $total1 = 0;
    $total2 = 0;
      foreach($lists as $l){
        echo '<tr>';
        echo '<td>'.$l->outlet_name.'</td>';
        echo '<td>'.$l->name.'</td>';
        echo '<td>'.date("d/m/Y",strtotime($l->created_at)).'</td>';
        echo '<td >'.$l->description.'</td>';
        echo '<td align="right">'.round($l->quantity,3)." ".$l->code.'</td>';
        echo '<td align="right"> Rp. '.number_format($l->cost).'</td>';
        echo '</tr>';

    $total1 = $total1 + round($l->quantity,3);
    $total2 = $total2 + $l->cost;

      }
    ?>
      <tr>
        <td colspan="5" align="right"><strong>TOTAL COST :</strong></td>
        <td width="9%" align="right">Rp. <?php echo number_format($total2); ?></td>
      </tr>
    </tbody>
  </table>
</div>