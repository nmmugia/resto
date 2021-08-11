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
    <h5><?php echo $data_store[0]->store_name;?></h5>
    <h4><label>Laporan Transfer Barang</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($date))?>
    - <?php echo date("d/m/Y",strtotime($report_end_date));?>
    </h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="150px">Tanggal</th>
        <th>Dari Outlet</th>
        <th>Ke Outlet</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php
      foreach($results as $d){
        echo '<tr>';
        echo '<td>'.date("d/m/Y H:i:s",strtotime($d->created_at)).'</td>';
        echo '<td>'.$d->origin_outlet.'</td>';
        echo '<td>'.$d->destination_outlet.'</td>';
        echo '<td>
              <a class="btn btn-default" href="'.base_url(SITE_ADMIN . '/reports/transfer_menu_detail/'.$d->id).'"><i class="fa fa-search" aria-hidden="true"></i> Detail</a>
              <a class="btn btn-success" href="'.base_url(SITE_ADMIN . '/stock/prints/'.$d->id).'"><i class="fa fa-print" aria-hidden="true"></i> Print</a>
            </td>';
        echo '</tr>';
      }
    ?>
    </tbody>
  </table>
</div>