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
    <h4>Sales Summary Report</h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($search['date']));?></h5>
  </div>
  <table class="table table-bordered border">
    <thead>
      <tr>
        <th>Product</th>
        <th>Sales</th>
        <th>COGS</th>
        <th>Gross Profit</th>
        <th>Profit Margin</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($lists as $l): ?>
        <tr>
          <td><?php echo $l->menu_name ?></td>
          <td class="text-right"><?php echo number_format($l->sales,2) ?></td>
          <td class="text-right"><?php echo number_format($l->cogs,2) ?></td>
          <td class="text-right"><?php echo number_format($l->gross_profit,2) ?></td>
          <td class="text-center"><?php echo $l->profit_margin ?> %</td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>