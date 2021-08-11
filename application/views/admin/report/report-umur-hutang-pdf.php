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
    <h5><?php echo $data_store->store_name;?></h5>
    <h4><label>Laporan Transfer Barang</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($report_end_date));?>
    </h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
       <tr>
          <th>Inventory</th>
          <th>Total Hutang</th>
          <th>0 - 30</th>
          <th>31 - 60</th>
          <th>61 - 90</th>
          <th>90+</th>
        </tr>
    </thead>
    <tbody>
    <?php
      foreach($results as $d){ ?>
        <tr>
          <td><?php echo $d->name;?></td>
          <td><?php echo convert_rupiah($d->total_payment);?></td>
          <td><?php echo convert_rupiah($d->due_1);?></td>
          <td><?php echo convert_rupiah($d->due_2);?></td>
          <td><?php echo convert_rupiah($d->due_3);?></td>
          <td><?php echo convert_rupiah($d->due_4);?></td>
        </tr>
      <?php }
    ?>
    </tbody>
  </table>
</div>