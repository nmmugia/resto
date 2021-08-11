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
    <h5></h5>
    <h4><label>Laporan Cost Opname</label></h4>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Inventory</th>
        <th>Tanggal</th>
        <th>Qty</th>
        <th>Harga</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $counter=1;
      $total_price = 0;
      $quantity = 0;
      foreach($inventories as $inventory){
          $quantity += doubleval($inventory->quantity);
          $cost = $inventory->quantity * $inventory->price;
          $total_price += $cost;
          echo '<tr>';
          echo '<td>'.$counter.'</td>';
          echo '<td style="padding-left:15px;">'.$inventory->name.'</td>';
          echo '<td>'.$inventory->created_at.'</td>';
          echo '<td>'.$inventory->quantity.'</td>';
          echo '<td>'.convert_rupiah($cost).'</td>';
          echo '</tr>';
          $counter++;       
      }
    ?> 
      <tr>
        <td colspan="2"></td>
        <td colspan="2">Total Opname : </td>
        <td><?php echo $quantity ?></td>
      </tr>
      <tr>
        <td colspan="2"></td>
        <td colspan="2">Total Cost Opname : </td>
        <td><?php echo convert_rupiah($total_price) ?></td>
      </tr>
    </tbody>
  </table>
</div>