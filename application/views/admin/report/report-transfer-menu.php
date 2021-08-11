<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="transfer_menu">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Tanggal Awal</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="input_date">
                    <?php echo form_input(array(
                      'name' => 'date',
                      'type' => 'text',
                      'class' => 'form-control date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")
                    )); ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td><label>Tanggal Akhir</label></td>
              <td class="col-sm-8">
                <div class='input-group date' id="report_end_date">
                    <?php echo form_input(array(
                      'name' => 'report_end_date',
                      'type' => 'text',
                      'class' => 'form-control end_date', 
                      'onkeydown'=>'return false',
                      'value'=>date("Y-m-d")
                    )); ?>
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


    </div>
    <input type="hidden" id="report_type" value="transfer_menu"/>
  </div>
</div>