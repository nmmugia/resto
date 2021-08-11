<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="promo_cc">
        <div class="clear-export"></div>
        <table class="col-lg-4 form-table-cms">
          <tbody>
            <tr>
                <td><label>Nama Promo</label></td>
                <td class="col-sm-8">
                  <?php 
                    echo form_dropdown('promo_cc_name', $all_promo_cc, '', 
                        'id="promo_cc_name" field-name = "" 
                        class="form-control" autocomplete="on"');
                        ?>
                </td>
            </tr>
            <tr>
                <td><label>Waktu Mulai</label></td>
                <td class="col-sm-8">
                  <div class='input-group date ' id='start_date'>
                       <?php echo form_input(array('name' => 'start_date',
                         'id' => 'input_start_date',
                         'type' => 'text',
                         'class' => 'form-control date',
                         'onkeydown'=>'return false',
                         'value'=>date("Y-m-d")." 00:00",
                         )); ?>
                         <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar">
                          </span>
                      </span>
                  </div> 
                </td>
            </tr>
            <tr>
                <td><label>Waktu Akhir</label></td>
                <td class="col-sm-8">
                    <div class='input-group date ' id='end_date'>
                       <?php echo form_input(array('name' => 'end_date',
                         'id' => 'input_end_date',
                         'type' => 'text',
                         'class' => 'form-control date',
                         'onkeydown'=>'return false',
                         'value'=>date("Y-m-d")." 23:59",

                         )); ?>
                         <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar">
                            </span>
                        </span>
                    </div> 
                </td>
            </tr>
            <tr>
              <td colspan="4" align="center">
                <!--<button id="export_xls" type="submit" class="btn btn-default" style="float:right;display: none">Export Excel</button>-->
                <button id="filter_submit" class="btn btn-default">Filter</button>
                <button id="export_pdf" type="submit" class="btn btn-default" >Export PDF</button>
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
  .vertical_middle td,.vertical_middle th{
    vertical-align: middle !important;
  }
</style>
<div class="panel-body <?php echo ($is_print==true ? "is_print" : ""); ?>">
  <div class="text-center" style="margin-bottom:15px;">
    <h3><?php echo $data_store->store_name;?></h3>
    <h4><label>Laporan Penggunaan Promo Kartu Kredit</label></h4>
    <h5><?php echo date("d F Y",strtotime($start_date))." s/d ".date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr class="vertical_middle">
        <th width="15%">Jadwal</th>
        <th width="10%">Nama Diskon</th>
        <th width="10%">Besar Diskon</th>
        <th width="10%">Tanggal Transaksi</th>
        <th width="10%">Waktu Transaksi</th>
        <th width="10%">Nomor Bill</th>
        <th width="10%">Total Transaksi</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total=0;
      foreach($results as $d){
        $total+=$d->total_price;
        echo '<tr>';
        echo '<td>'.$d->schedule_name.'</td>';
        echo '<td>'.$d->name.'</td>';
        echo '<td>'.$d->discount.' %</td>';
        echo '<td>'.date("d F Y",strtotime($d->start_order)).'</td>';
        echo '<td>'.date("H:i:s",strtotime($d->start_order))." s/d ".date("H:i:s",strtotime($d->end_order)).'</td>';
        echo '<td>'.$d->receipt_number.' <a target="_blank" href="'.base_url(SITE_ADMIN."/reports/detail_transaction/".$d->receipt_number).'" class="btn btn-default btn-xs pull-right" style="'.($is_print==true ? "display:none;" : "").'"><i class="fa fa-search"></i></a></td>';
        echo '<td align="right">'.number_format($d->total_price).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr style="background-color:lightgray;font-weight:bold;">
        <td align="center" colspan="6">TOTAL</td>
        <td align="right"><?php echo number_format($total,2) ?></td>
      </tr>
    </tfoot>
  </table>
</div>


    </div>
    <input type="hidden" id="report_type" value="promo_cc"/>

    
  </div>
</div>