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
    <h4>Inventory Used Report</h4>
    <h5><?php echo date("d/m/Y",strtotime($search['from']))." s/d ".date("d/m/Y",strtotime($search['to'])); ?></h5>
  </div>
  <br>
  <table class="table table-bordered border">
    <thead>
      <tr>
        <th>Inventori</th>
        <th>Total Used</th>
        <th width="150px">Unit</th>
        <?php if($search['is_print']==false): ?>
        <th width="80px">Action</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach($lists as $l): ?>
        <tr>
          <td><?php echo $l->name ?></td>
          <td class="text-right"><?php echo number_format($l->total_used,2) ?></td>
          <td class="text-center"><?php echo $l->unit ?></td>
          <?php if($search['is_print']==false): ?>
          <td class="text-center">
            <a href="javascript:void(0);" class="btn btn-primary btn-xs get_detail_inventory_used" inventory_id="<?php echo $l->inventory_id ?>">Detail</a>
          </td>
          <?php endif; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>