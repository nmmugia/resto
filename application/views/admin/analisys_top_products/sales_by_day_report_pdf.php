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
    <h4>Sales By Day Report</h4>
    <h5>Bulan <?php echo date("F Y",strtotime($search['date']));?></h5>
  </div>
  <table class="table table-bordered border">
    <thead>
      <tr>
        <th>Date</th>
        <th>No of Transaction</th>
        <th>Revenue</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($lists as $l): ?>
        <tr>
          <td><?php echo date("d F Y",strtotime($l->date)) ?></td>
          <td><?php echo $l->no_of_transaction ?></td>
          <td class="text-right"><?php echo number_format($l->revenue,2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <?php if($button=="filter"): ?>
  <div id="sales_by_day_chart"></div>
  <?php else: ?>
  <?php if($image_data!=""): ?>
  <center><img src="<?php echo $image_data; ?>" width="550px;"></center>
  <?php endif; ?>
  <?php endif; ?>
</div>
<script type="text/javascript">
  var sales_by_day_categories_data=[
    <?php 
      $counter=1;
      foreach($lists as $l){
        $label=date("d M Y",strtotime($l->date));
        echo "'".$label."'";
        if($counter!=sizeof($lists))echo ",";
        $counter++;
      }
    ?>
  ];
  var sales_by_day_series_data=[
    <?php 
      $counter=1;
      foreach($lists as $l){
        echo $l->revenue;
        if($counter!=sizeof($lists))echo ",";
        $counter++;
      }
    ?>
  ]
</script>