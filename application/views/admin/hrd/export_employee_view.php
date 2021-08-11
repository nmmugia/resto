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
<table class="table">
  <tbody>
    <tr>
      <td style="width:20%;">
        <img src="<?php echo $data_store->store_logo ?>" style="height:100px;">
      </td>
      <td style="width:70%;text-align:center">
        <h3 style="margin:0px !important;"><?php echo $data_store->store_name ?></h3>
        <p style="margin:0px !important;"><?php echo $data_store->store_address ?></p>        
      </td>
    </tr>
  </tbody>
</table>
<hr style="margin:0px !important;">
<div class="panel-body">
  <h5><b>Informasi Pegawai</b></h5>
  <table class="table table-bordered is_print border" style="width:300px;">
    <tbody>
      <tr>
        <td>NIP</td>
        <td><?php echo $employee->nip ?></td>
      </tr>
      <tr>
        <td>Nama Pegawai</td>
        <td><?php echo $employee->name ?></td>
      </tr>
      <tr>
        <td>Jenis Kelamin</td>
        <td><?php echo ($employee->gender==1 ? "Laki - Laki" : "Perempuan") ?></td>
      </tr>
      <tr>
        <td>Alamat</td>
        <td><?php echo $employee->address ?></td>
      </tr>
      <tr>
        <td>Telepon</td>
        <td><?php echo $employee->phone ?></td>
      </tr>
      <tr>
        <td>Email</td>
        <td><?php echo $employee->email ?></td>
      </tr>
    </tbody>
  </table>
  <h5><b>Data Status Kepegawaian</b></h5>
  <table class="table table-bordered is_print border" >
    <thead>
      <tr>
        <th>Status</th>
        <th>Tgl Mulai</th>
        <th>Tgl Akhir</th>
        <th>Kantor</th>
        <th>Jabatan</th>
        <th>Reimburse</th>
        <th>Cuti</th>
      </tr>
    </thead>
    <tbody>
    <?php
      foreach($job_histories as $l){
        echo '<tr>';
        echo '<td>'.$l->status_name.'</td>';
        echo '<td>'.date("d F Y",strtotime($l->start_date)).'</td>';
        echo '<td>'.($l->end_date!="" ? date("d F Y",strtotime($l->end_date)) : "").'</td>';
        echo '<td>'.$l->store_name.'</td>';
        echo '<td>'.$l->jobs_name.'</td>';
        echo '<td>'.$l->reimburse.'</td>';
        echo '<td>'.$l->vacation.'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
  </table>
  <h5><b>Data Gaji</b></h5>
  <table class="table table-bordered is_print border" >
    <thead>
      <tr>
        <th>Kantor</th>
        <th>Jabatan</th>
        <th>Periode</th>
        <th>Jml Penerimaan</th>
        <th>Pinjaman</th>
        <th>Jumlah Reimburse</th>
      </tr>
    </thead>
    <tbody>
    <?php
      foreach($payroll_histories as $l){
        $temp=explode("-",$l->period);
        echo '<tr>';
        echo '<td>'.$l->store_name.'</td>';
        echo '<td>'.$l->jobs_name.'</td>';
        echo '<td>'.date("F Y",strtotime($temp[1]."-".$temp[0]."-01")).'</td>';
        echo '<td align="right">'.number_format($l->payroll_total,0).'</td>';
        echo '<td align="right">'.number_format($l->total_loan,0).'</td>';
        echo '<td align="right">'.number_format($l->total_reimburse,0).'</td>';
        echo '</tr>';
      }
    ?>
    </tbody>
  </table>
</div>