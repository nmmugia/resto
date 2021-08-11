<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="total_quantity_order_table_waiter">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Waiter</label></td>
              <td>
                <select class="form-control" name="user_id" id="user_id">
                  <option value="">Semua Waiter</option>
                  <?php foreach($waiter_lists as $w): ?>
                  <option value="<?php echo $w->id ?>"><?php echo $w->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
            <tr>
              <td><label>Tanggal</label></td>
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
              <td colspan="2" align="right">
                <button id="filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <button id="export_pdf" type="submit" class="btn btn-success" ><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                
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
    <h4><label>Laporan Total Kuantitas Penjualan Waiter</label></h4>
    <h5>Tanggal <?php echo date("d/m/Y",strtotime($date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" >
    <thead>
      <tr>
        <th width="30">No</th>
        <th>Nama Waiter</th>
        <th>Total Menu</th>
        <th>Total Bill</th>
        <?php if($is_print!=true): ?>
        <th width="80" class="text-center">Detail</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody>
    <?php
      $oounter=1;
      $total_quantity=0;
      $total_bill=0;
      foreach($results as $d){
        $total_quantity+=$d->total_quantity;
        $total_bill+=$d->total_bill;
        echo '<tr>';
        echo '<td class="text-center">'.$oounter.'</td>';
        echo '<td class="text-center">'.$d->name.'</td>';
        echo '<td class="text-center">'.(int)$d->total_quantity.'</td>';
        echo '<td class="text-center">'.(int)$d->total_bill.'</td>';
         if($is_print!=true){
          echo '<td class="text-center">
            <a href="'.base_url(SITE_ADMIN).'/reports/total_sales_waiter_detail?user_id='.$d->id.'&date='.$date.'" class="btn btn-default" target="_blank" title="Detail"><i class="fa fa-search"></i></a>
          </td>';
        }
        echo '</tr>';
        $oounter++;
      }
    ?>
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2" class="text-center"><b>Total</b></td>
        <td class="text-center"><?php echo (int)$total_quantity ?></td>
        <td class="text-center"><?php echo (int)$total_bill ?></td>
        <?php if($is_print!=true): ?>
        <td></td>
        <?php endif; ?>
      </tr>
    </tfoot>
  </table>
</div>

      
    </div>
    <input type="hidden" id="report_type" value="total_quantity_order_table_waiter"/>
  </div>
</div>