<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="kitchen_duration">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Periode</label></td>
              <td class="col-sm-8">
                <div class='input-group date'>
                  <?php 
                    echo form_input(array(
                      'name' => 'month_year',
                      'id' => 'month_year',
                      'type' => 'text',
                      'class' => 'form-control date', 
                      'onkeydown'=>'return false',
                      'value'=>date("m-Y")
                    )); 
                  ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
              <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <button id="export_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div id="report_content">
      
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
    <h4><label>Laporan Waktu Proses Pesanan Kitchen</label></h4>
    <h5>Periode <?php echo date("F Y",strtotime($year."-".$month."-01"));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Tanggal</th>
        <th>Waktu Order</th>
        <th>Nama Menu</th>
        <th>Waktu Ideal Proses</th>
        <th>Jumlah Order</th>
        <th>Waktu Selesai</th>
        <th>Lama Waktu Proses</th>
        <th>Reward</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      $total=0;
      $reward_setting=(sizeof($reward_kitchen)>0 ? $reward_kitchen->reward : 0);
      foreach($results as $d){
        $datetime1 = strtotime($d->created_at);
        $datetime2 = strtotime($d->finished_at);
        $interval  = abs($datetime2 - $datetime1);
        $minutes   = round($interval / 60);
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-center">'.date("d/m/Y",strtotime($d->created_at)).'</td>';
        echo '<td class="text-center">'.date("H:i",strtotime($d->created_at)).'</td>';
        echo '<td class="text-center">'.$d->menu_name.'</td>';
        echo '<td class="text-center">'.$d->duration.' menit</td>';
        echo '<td class="text-center">'.$d->quantity.'</td>';
        echo '<td class="text-center">'.date("H:i",strtotime($d->finished_at)).'</td>';
        echo '<td class="text-center">'.$minutes.' menit</td>';
        $reward=($minutes<$d->duration ? $reward_setting*$d->quantity : 0);
        $total+=$reward;
        echo '<td class="text-right">'.number_format($reward,0,",",".").'</td>';
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td class="text-center" colspan="8"><b>Total Reward</b></td>
        <td class="text-right"><b><?php echo number_format($total,0,",",".") ?></b></td>
      </tr>
    </tfoot>
  </table>
</div>
    </div>
    <input type="hidden" id="report_type" value="kitchen_duration"/>
  </div>
</div>