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
<div class="panel-body">
  <div class="text-center">
    <h4><?php echo $store->store_name;?></h4>
    <h4>Hourly Sales Report</h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($search['date']));?></h5>
  </div>
  <table class="table table-bordered border">
    <thead>
      <tr>
        <th>Hour</th>
        <th>No of Transaction</th>
        <th>Revenue</th>
        <th>Percentage</th>
      </tr>
    </thead>
    <tbody>
      <?php
        $total=0;
        foreach($lists as $l): 
        $total+=$l->revenue;
      ?>
        <tr>
          <td><?php echo $l->hour ?></td>
          <td><?php echo $l->no_of_transaction ?></td>
          <td class="text-right"><?php echo number_format($l->revenue,2) ?></td>
          <td class="text-center"><?php echo $l->percentage ?> %</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="text-center" colspan="2"><b>Total</b></td>
        <td class="text-right"><b><?php echo number_format($total,2) ?></b></td>
        <td></td>
      </tr>
    </tfoot>
  </table>
</div>