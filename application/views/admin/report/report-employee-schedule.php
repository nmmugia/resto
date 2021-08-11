<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="employee_schedule">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
        <tr>
        <td><label>Pegawai</label></td>
        <td class="col-sm-8">
          <select class="form-control" name="office_hour"  id="user_id">
          <option value="">Semua Pegawai</option>
          <?php foreach($users as $o): ?>
          <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
          <?php endforeach; ?>
          </select>
        </td>
        </tr>
        <tr>
              <td><label>Tanggal</label></td>
              <td class="col-sm-8">
                <div class='input-group date ' id='report_attendance_start_date'>
                  <?php echo form_input(array('name' => 'start_date',
                     'id' => 'input_start_date',
                     'type' => 'text',
                     'class' => 'form-control date',
                     'onkeydown'=>'return false',
                     'value'=>date("Y-m-d")
                  )); ?>
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div> 
              </td>
            </tr>
            <!-- <tr>
              <td><label>Sampai Tanggal</label></td>
              <td class="col-sm-8">
                <div class='input-group date ' id='report_attendance_end_date'>
                  <?php echo form_input(array('name' => 'end_date',
                     'id' => 'input_end_date',
                     'type' => 'text',
                     'class' => 'form-control date',
                     'onkeydown'=>'return false',
                     'value'=>date("Y-m-d")
                  )); ?>
                  <span class="input-group-addon"><span class="glyphicon glyphicon-calendar"></span></span>
                </div> 
              </td>
            </tr> -->
        <tr>
        <td colspan="2" align="right">

          <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
      <!--     <button id="export_pdf" type="submit" class="btn btn-success"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button> -->
          
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
    <h3><?php 

    if ($store == 0){
      echo "Semua Store";
    } else {
      (!empty($data_store))?$data_store->store_name:"";
    }

    ?></h3>
    <h4><label>Laporan Jadwal Jam Kerja</label></h4>
    <?php /*<h5><?php echo date("d/m/Y",strtotime($start_date))." s/d ".date("d/m/Y",strtotime($start_date)) ?></h5>*/ ?>
    <!-- <h5><?php echo (!empty($office_hour) ? $office_hour->name : "") ?></h5>
    <h5><?php echo (!empty($user) ? $user->name : "") ?></h5> -->
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th>Nama Pegawai</th>
        <th>Tanggal Mulai</th>
        <th>Tanggal Akhir</th>
        <th>Jam Masuk</th>
        <th>Jam Pulang</th>
      </tr>
    </thead>
    <tbody>
    <?php
      
        foreach($results as $d){
          echo '<tr>';
          echo '<td>'.$d->uname.'</td>';
          echo '<td class="text-center">'.$d->start_date.'</td>';

          if ($d->end_date == "0000-00-00"){
            echo '<td class="text-center"> Selama Bekerja </td>';
          } else {
            echo '<td class="text-center">'.$d->end_date.'</td>';
          }
          echo '<td class="text-center">'.date("H:i:s",strtotime($d->start_time)).'</td>';
          echo '<td class="text-center">'.date("H:i:s",strtotime($d->end_time)).'</td>';
          echo '</tr>';       
      }
    ?>
    </tbody>
  </table>
</div>
      
    </div>
    <input type="hidden" id="report_type" value="employee_schedule"/>
  </div>
</div>