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
    <h4><label>Laporan Detail Total Penjualan Waiter</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Nama Menu</th>
        <th>Harga</th>
        <th>Jumlah</th>
        <th>Total</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      $total=0;
      $total_qty=0;
      foreach($results as $d){
        $total_qty+=$d->quantity;
        $total+=$d->quantity*$d->price;
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-center">'.$d->menu_name.'</td>';
        echo '<td class="text-right">'.number_format($d->price,2,',','.').'</td>';
        echo '<td class="text-center">'.$d->quantity.'</td>';
        echo '<td class="text-right">'.number_format($d->quantity*$d->price,2,',','.').'</td>';
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-center"><b>Total</b></td>
        <td class="text-center"><?php echo number_format($total_qty,2,",",".") ?></td>
        <td class="text-right"><?php echo number_format($total,2,",",".") ?></td>
      </tr>
    </tfoot>
  </table>
  
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Nama Meja</th>
        <th>Jumlah Dilayani</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      $total=0;
      foreach($results2 as $d){
        $total+=$d->table_count;
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-center">'.$d->table_name.'</td>';
        echo '<td class="text-center">'.$d->table_count.'</td>';
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2" class="text-center"><b>Total</b></td>
        <td class="text-center"><?php echo number_format($total,2,",",".") ?></td>
      </tr>
    </tfoot>
  </table>
</div>