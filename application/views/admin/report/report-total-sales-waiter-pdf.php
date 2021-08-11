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
    <h4><label>Laporan Total Penjualan Waiter</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Nama Waiter</th>
        <th>Total Sales</th>
        <?php if($is_print!=true): ?>
        <th width="80" class="text-center">Detail</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      $total=0;
      foreach($results as $d){
        $total+=$d->total_sales;
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-left">'.$d->name.'</td>';
        echo '<td class="text-right">'.number_format($d->total_sales,2,',','.').'</td>';
        if($is_print!=true){
          echo '<td class="text-center">
            <a href="'.base_url(SITE_ADMIN).'/reports/total_sales_waiter_detail?user_id='.$d->id.'&date='.$date.'" class="btn btn-default" target="_blank" title="Detail"><i class="fa fa-search"></i></a>
          </td>';
        }
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2" class="text-center"><b>Total</b></td>
        <td class="text-right"><?php echo number_format($total,2,",",".") ?></td>
        <?php if($is_print!=true): ?>
        <td></td>
        <?php endif; ?>
      </tr>
    </tfoot>
  </table>
</div>