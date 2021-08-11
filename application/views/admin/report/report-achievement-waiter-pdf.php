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
    <h4><label>Laporan Pencapaian Target Waiter</label></h4>
    <h5>Tanggal <?php echo date("F Y",strtotime($year."-".$month."-01"));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Nama Waiter</th>
        <th>Target</th>
        <th>Reward Setting</th>
        <th>Status</th>
        <th>Reward</th>
        <?php if($is_print!=true): ?>
        <th width="80" class="text-center">Detail</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      foreach($results as $d){
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-center">'.$d->name.'</td>';
        echo '<td class="text-center">'.($d->target_type==1 ? "Target Total Penjualan" : "Target Penjualan Item ").'</td>';
        echo '<td class="text-center">'.($d->is_percentage==1 ? $d->reward." %" : number_format($d->reward,2,',','.')).'</td>';
        echo '<td class="text-center">'.($d->target_type==1 ? ($d->achievement_by_total>=$d->target_by_total ? "Tercapai" : "Tidak Tercapai") : ($d->achievement_by_item>0 ? "Tidak Tercapai" : "Tercapai")).'</td>';
        echo '<td class="text-right">';
        if($d->target_type==1 && $d->achievement_by_total>=$d->target_by_total){
          $reward=($d->is_percentage==1 ? $d->reward*$d->achievement_by_total/100 : $d->reward );
          echo number_format($reward,2,',','.');
        }
        if($d->target_type==2 && $d->achievement_by_item==0){
          $reward=($d->is_percentage==1 ? $d->reward*$d->achievement_by_item_total/100 : $d->reward );
          echo number_format($reward,2,',','.');
        }
        echo '</td>';
        if($is_print!=true){
          echo '<td class="text-center">
            <a href="'.base_url(SITE_ADMIN).'/reports/achievement_waiter_detail?target_id='.$d->target_id.'&user_id='.$d->id.'&month='.$month.'&year='.$year.'" class="btn btn-default" target="_blank" title="Detail"><i class="fa fa-search"></i></a>
          </td>';
        }
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
  </table>
</div>