<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<style type="text/css">
  .table tr td{
    padding:3px !important;
  }
</style>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
      <div class="col-lg-6 col-lg-offset-3">  
        <table class="table" style="border:0px;">
          <tbody>
            <tr>
              <td colspan="2" align="center">
                <img src="<?php echo base_url($setting['printer_logo']) ?>"><br>
                <?php echo $data_store[0]->store_name."<br>".$data_store[0]->store_address ?>
              </td>
            </tr>
            <tr>
              <td width="30%">NO ORDER</td>
              <td width="70%"><?php echo $data->order->id ?></td>
            </tr>
            <tr>
              <td>WAITER</td>
              <td><?php echo strtoupper($data->order->waiter_name) ?></td>
            </tr>
            <tr>
              <td>TANGGAL</td>
              <td><?php echo date("d F Y",strtotime($data->order->created_at)) ?></td>
            </tr>
            <tr>
              <td>JAM</td>
              <td><?php echo date("H:i:s",strtotime($data->order->created_at)) ?></td>
            </tr>
            <tr>
              <td>TIPE</td>
              <td><?php echo ($data->order->is_take_away==1 ? "Take Away" : ($data->order->is_delivery==1 ? "Delivery" : "Dine In")) ?></td>
            </tr>
            <?php if($data->order->table_id!=0): ?>
            <tr>
              <td>MEJA</td>
              <td><?php echo strtoupper($data->order->table_name) ?></td>
            </tr>
            <?php endif; ?>
            <?php if($data->order->customer_name!=""): ?>
            <tr>
              <td>Nama</td>
              <td><?php echo strtoupper($data->order->customer_name) ?></td>
            </tr>
            <tr>
              <td>Telp</td>
              <td><?php echo strtoupper($data->order->customer_phone) ?></td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td><?php echo strtoupper($data->order->customer_address) ?></td>
            </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th width="85%">Nama Menu</th>
              <th width="15%">Jumlah</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              foreach($data->order_menu as $bm): 
            ?>
            <tr>
              <td width="75%"><?php echo strtoupper($bm->menu_name) ?></td>
              <td width="25%" align="right"><?php echo $bm->quantity; ?></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>