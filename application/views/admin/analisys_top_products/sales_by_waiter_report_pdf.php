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
    <h4>Sales By Waiter Report</h4>
    <h5><?php echo date("d/m/Y",strtotime($search['from']))." s/d ".date("d/m/Y",strtotime($search['to'])); ?></h5>
  </div>
  <br>
  <table class="table table-bordered border">
    <thead>
      <tr>
        <th>Waiter</th>
        <th>No Of Transaction</th>
        <th>Revenue</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($lists as $l): ?>
        <tr>
          <td><?php echo $l->name ?></td>
          <td><?php echo $l->no_of_transaction ?></td>
          <td class="text-right"><?php echo number_format($l->revenue,2) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <div class="col-sm-12" id="container_chart">
    <?php if($button=="filter"): ?>
    <div class="col-sm-6">
      <div id="sales_by_waiter_chart"></div>
    </div>
    <div class="col-sm-6">
      <div id="sales_by_waiter_chart_pie"></div>
    </div>
    <?php else: ?>
    <?php if($image_data!=""): ?>
    <center><img src="<?php echo $image_data; ?>" width="750px;"></center>
    <?php endif; ?>
    <?php endif; ?>
  </div>
</div>
<script type="text/javascript">
  var sales_by_waiter_categories_data=[
    <?php 
      $counter=1;
      foreach($lists as $l){
        echo "'".$l->name."'";
        if($counter!=sizeof($lists))echo ",";
        $counter++;
      }
    ?>
  ];
  var sales_by_waiter_series_data=[
    <?php 
      $counter=1;
      $total_revenue=0;
      foreach($lists as $l){
        $total_revenue+=$l->revenue;
        echo $l->revenue;
        if($counter!=sizeof($lists))echo ",";
        $counter++;
      }
    ?>
  ]
  var sales_by_waiter_series_data_pie=
  [
    <?php 
      $counter=1;
      foreach($lists as $l){
        echo "{";
        echo "name:"."'".$l->name."',";
        echo "y:".($l->revenue/$total_revenue*100);
        echo "}";
        if($counter!=sizeof($lists))echo ",";
        $counter++;
      }
    ?>
  ]
</script>