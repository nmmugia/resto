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
  .vertical_middle td,.vertical_middle th{
    vertical-align: middle !important;
  }
</style>
<div class="panel-body <?php echo ($is_print==true ? "is_print" : ""); ?>">
  <div class="text-center" style="margin-bottom:15px;">
    <h3><?php echo $data_store->store_name;?></h3>
    <h4><label>Laporan Summary Pengembalian Barang</label></h4>
    <h5><?php echo (sizeof($supplier)>0 ? "Supplier : ".$supplier->name : "");?></h5>
    <h5><?php echo date("d F Y",strtotime($start_date))." s/d ".date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr class="vertical_middle">
        <th width="7%">Tanggal Retur</th>
        <th width="15%">Supplier</th>
        <th width="9%">Nomor PO</th>
        <th width="9%">Nomor Pengembalian</th>
        <th width="7%">Total</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total=0;
      foreach($results as $d){
        echo '<tr>';
        echo '<td>'.date("d F Y",strtotime($d->retur_date)).'</td>';
        echo '<td>'.$d->name.'</td>';
        echo '<td>'.$d->po_number.'</td>';
        echo '<td>'.$d->number.'</td>';
        echo '<td align="right">'.number_format($d->total,2).'</td>';
        $total+=$d->total;
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr style="background-color:lightgray;font-weight:bold;">
        <td align="center" colspan="4">TOTAL</td>
        <td align="right"><?php echo number_format($total,2) ?></td>
      </tr>
    </tfoot>
  </table>
</div>