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
      echo (!empty($data_store))?$data_store->store_name:"";
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