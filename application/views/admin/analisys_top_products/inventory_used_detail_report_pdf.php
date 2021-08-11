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
    <h4>Inventory Used Detail Report : <?php echo (sizeof($inventory)>0 ? $inventory->name : "") ?></h4>
    <h5><?php echo date("d/m/Y",strtotime($search['from']))." s/d ".date("d/m/Y",strtotime($search['to'])); ?></h5>
  </div>
  <br>
  <div class="col-sm-6">
    <table class="table table-bordered border">
      <thead>
        <tr>
          <th>Menu</th>
          <th>Total Used</th>
          <th>Persentase</th>
        </tr>
      </thead>
      <tbody>
        <?php 
          $total_used=0;
          foreach($lists as $l): 
          $total_used+=$l->total_used;
        ?>
          <tr>
            <td><?php echo $l->menu_name ?></td>
            <td class="text-right"><?php echo number_format($l->total_used,2) ?></td>
            <td class="text-right"><?php echo number_format($l->percentage,2) ?> %</td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <div class="col-sm-6">
    <div id="inventory_used_detail_chart_pie"></div>
  </div>
</div>
<script type="text/javascript">
  var inventory_used_detail_series_data_pie=
  [
    <?php 
      $counter=1;
      foreach($lists as $l){
        echo "{";
        echo "name:"."'".$l->menu_name."',";
        echo "y:".($l->total_used/$total_used*100);
        echo "}";
        if($counter!=sizeof($lists))echo ",";
        $counter++;
      }
    ?>
  ]
</script>