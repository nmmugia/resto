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
    <h4><label>Laporan Pajak & Service Per Hari</label></h4>
    <h5><?php echo "Periode ".$start_date." s.d. ".$end_date;?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Tanggal</th>
        <th>Total Pajak</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total_taxes=0;
      $percentage=100;
      foreach($results as $d){
        $total_taxes+=($d->total_taxes*$percentage/100);
        echo '<tr>';
        echo '<td>'.$d->date.'</td>';
        echo '<td class="text-right">'.number_format(($d->total_taxes*$percentage/100),0).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="text-center"><b>Total</b></td>
        <td class="text-right"><b><?php echo number_format($total_taxes,0) ?></b></td>
      </tr>
    </tfoot>
  </table>
</div>