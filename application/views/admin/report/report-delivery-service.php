<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="delivery_service">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
                <td><label>Waktu Mulai</label></td>
                <td class="col-sm-8">
                    <div class='input-group date ' id='start_period'>
                      <?php echo form_input(array('name' => 'start_period',
                       'id' => 'input_start_date',
                       'type' => 'text',
                       'class' => 'form-control date',
                       'onkeydown'=>'return false',
                       'value'=>date("Y-m-d"),
                       )); ?>
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div> 
                </td>
            </tr>
            <tr>
                <td><label>Waktu Akhir</label></td>
                <td class="col-sm-8">
                  <div class='input-group date ' id='end_period'>
                     <?php echo form_input(array('name' => 'end_period',
                       'id' => 'input_end_date',
                       'type' => 'text',
                       'class' => 'form-control date',
                       'onkeydown'=>'return false',
                       'value'=>date("Y-m-d"),
                       )); ?>
                       <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar">
                          </span>
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
    <h4><label>Laporan Jasa Kurir</label></h4>
    <h5><?php echo "Periode ".$start_date." s.d. ".$end_date;?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Vendor</th>
        <th>ID Kurir</th>
        <th>Nama Kurir</th>
        <th>Total Komisi</th>
      </tr>
    </thead>
    <tbody>
    <?php
      $total_commission=0;
      foreach($results as $d){
        $total_commission+=$d->amount;
        echo '<tr>';
        echo '<td>'.$d->company_name.'</td>';
        echo '<td>'.$d->courier_code.'</td>';
        echo '<td>'.$d->courier_name.'</td>';
        echo '<td class="text-right">Rp '.number_format($d->amount,0).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="3" class="text-center"><b>Total</b></td>
        <td class="text-right"><b>Rp <?php echo number_format($total_commission,0) ?></b></td>
      </tr>
    </tfoot>
  </table>
</div>


    </div>
  <input type="hidden" id="report_type" value="delivery_service"/>
  </div>
</div>