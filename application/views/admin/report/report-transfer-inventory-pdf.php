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