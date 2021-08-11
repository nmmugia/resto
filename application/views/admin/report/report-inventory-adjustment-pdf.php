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
    <h4><label>Laporan Stok Opname</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Outlet</th>
        <th>Nama Inventory</th>
        <th>Stok Opname</th>
        <th>Stok Terakhir</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      $total=0;
      foreach($results as $d){
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-left">'.$d->outlet_name.'</td>';
        echo '<td class="text-left">'.$d->name.'</td>';
        echo '<td class="text-right">'.number_format($d->quantity,2,',','.').'</td>';
        echo '<td class="text-right">'.number_format($d->last_stock,2,',','.').'</td>';
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
  </table>
</div>