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
    <h5><?php echo $data_store->store_name;?></h5>
    <h4><label>Laporan Detail Inventory : <?php echo $data_inventory->name.(!empty($data_uom) ? " (".$data_uom->code.")" : ""); ?></label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($from_date))." s/d ".date("d/m/Y",strtotime($to_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Nama Menu</th>
        <th>Jumlah Pemakaian</th>
      </tr>
    </thead>
    <tbody>
    <?php
	  $total=0;
      foreach($results as $d){
        echo '<tr>';
        echo '<td>'.$d->menu_name.'</td>';
        echo '<td class="text-right">'.number_format($d->used,2,',','.').' '.$d->code.'</td>';
        echo '</tr>';
		$total+=$d->used;
      }
	  echo '<tr>';
	  echo '<td><b>Total</b></td>';
	  echo '<td class="text-right"><b>'.number_format($total,2,',','.').' '.$data_uom->code.'</b></td>';
	  echo '</tr>'; 
    ?>
    </tbody>
  </table>
</div>