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
    <h4><label>Laporan Summary Inventory</label></h4>
    <h5><?php echo date("d F Y",strtotime($start_date)).' - '.date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr>
        <th width="6%">Outlet</th>
        <th width="7%">Inventory</th>
        <th width="7%">Stok Awal</th>
        <th width="7%">Penjualan</th>
        <th width="7%">Transfer</th>
        <th width="8%">Pembelian</th>
        <th width="7%">Opname</th>
        <th width="8%">Penerimaan</th>
        <th width="7%">Proses Inventory</th>
        <th width="6%">Spoiled</th>
        <th width="6%">Void</th> 
        <th width="6%">Retur</th>
        <th width="6%">Refund</th>
        <th width="7%">Stok Akhir</th>
      </tr>
    </thead>
    <tbody>
    <?php
      foreach($results as $r){
        echo '<tr>';
        echo '<td>'.$r->outlet_name.'</td>';
        echo '<td>'.$r->name.' ( '.$r->code.' ) '.'</td>';
        echo '<td align="right">'.number_format($r->beginning_stock,2).'</td>';
		$counter=1;
		foreach($history_status as $h){
			echo '<td align="right">'.number_format($r->{"total_".$counter},2).'</td>';
			$counter++;
		}
		echo '<td align="right">'.number_format($r->last_stock,2).'</td>';
		echo '</tr>';
      }
    ?>
    </tbody>
  </table>
</div>