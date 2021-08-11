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
      <div class="col-lg-6" style="border:1px solid black;height:555px;overflow:auto;">  
        <table class="table" style="border:0px;">
          <tbody>
            <tr>
              <td colspan="3" align="center"><b>Bill Sebelum Refund</b></td>
            </tr>
            <tr>
              <td colspan="3" align="center">
                <img src="<?php echo base_url($setting['printer_logo']) ?>"><br>
                <?php echo $data_store[0]->store_name."<br>".$data_store[0]->store_address ?>
              </td>
            </tr>
            <tr>
              <td>BILL</td>
              <td colspan="2"><?php echo $old_bill->receipt_number ?></td>
            </tr>
            <tr>
              <td>WAITER</td>
              <td colspan="2"><?php echo strtoupper($old_bill->waiter_name) ?></td>
            </tr>
            <tr>
              <td>KASIR</td>
              <td colspan="2"><?php echo strtoupper($old_bill->cashier_name) ?></td>
            </tr>
            <tr>
              <td>TANGGAL</td>
              <td colspan="2"><?php echo date("d F Y",strtotime($old_bill->end_order)) ?></td>
            </tr>
            <tr>
              <td>JAM</td>
              <td colspan="2"><?php echo date("H:i:s",strtotime($old_bill->end_order)) ?></td>
            </tr>
            <?php if($old_bill->table_id!=0): ?>
            <tr>
              <td>MEJA</td>
              <td colspan="2"><?php echo strtoupper($old_bill->table_name) ?></td>
            </tr>
            <?php endif; ?>
            <?php if($old_bill->customer_name!=""): ?>
            <tr>
              <td>Nama</td>
              <td colspan="2"><?php echo strtoupper($old_bill->customer_name) ?></td>
            </tr>
            <tr>
              <td>Telp</td>
              <td colspan="2"><?php echo strtoupper($old_bill->customer_phone) ?></td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td colspan="2"><?php echo strtoupper($old_bill->customer_address) ?></td>
            </tr>
            <?php endif; ?>
            <?php 
              $total=0;
              foreach($old_bill_menu as $bm): 
              $subtotal=($bm->quantity*$bm->price);
              $total+=$subtotal;
            ?>
            <tr>
              <td width="50%"><?php echo strtoupper($bm->menu_name) ?></td>
              <td width="25%" align="right"><?php echo $bm->quantity." x ".number_format($bm->price,0); ?></td>
              <td width="25%" align="right"><?php echo number_format($subtotal,0); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="2" align="right"><b>SUBTOTAL</b></td>
              <td align="right"><b><?php echo number_format($total,0); ?></b></td>
            </tr>
            <?php 
              foreach($old_bill_information as $bi): 
              if($bi->type==1){
                $total+=$bi->amount;
              }else{
                $total-=$bi->amount;
              }
            ?>
            <tr>
              <td colspan="2" align="right"><b><?php echo strtoupper($bi->info) ?></b></td>
              <td align="right"><b><?php echo number_format($bi->amount,0); ?></b></td>
            </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="2" align="right"><b>GRANDTOTAL</b></td>
              <td align="right"><b><?php echo number_format($total,0); ?></b></td>
            </tr>
            <?php 
              foreach($old_bill_payment as $bp): 
            ?>
            <tr>
              <td colspan="2" align="right"><b><?php echo strtoupper($bp->payment_option_name) ?></b></td>
              <td align="right"><b><?php echo number_format($bp->amount,0); ?></b></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <div class="col-lg-6" style="border:1px solid black;height:555px;overflow:auto;">  
        <table class="table">
          <tbody>
            <tr>
              <td colspan="3" align="center"><b>Bill Setelah Refund</b></td>
            </tr>
            <tr>
              <td colspan="3" align="center">
                <img src="<?php echo base_url($setting['printer_logo']) ?>"><br>
                <?php echo $data_store[0]->store_name."<br>".$data_store[0]->store_address ?>
              </td>
            </tr>
            <tr>
              <td>BILL</td>
              <td colspan="2"><?php echo $new_bill->receipt_number ?></td>
            </tr>
            <tr>
              <td>WAITER</td>
              <td colspan="2"><?php echo strtoupper($new_bill->waiter_name) ?></td>
            </tr>
            <tr>
              <td>KASIR</td>
              <td colspan="2"><?php echo strtoupper($new_bill->cashier_name) ?></td>
            </tr>
            <tr>
              <td>TANGGAL</td>
              <td colspan="2"><?php echo date("d F Y",strtotime($new_bill->end_order)) ?></td>
            </tr>
            <tr>
              <td>JAM</td>
              <td colspan="2"><?php echo date("H:i:s",strtotime($new_bill->end_order)) ?></td>
            </tr>
            <?php if($new_bill->table_id!=0): ?>
            <tr>
              <td>MEJA</td>
              <td colspan="2"><?php echo strtoupper($new_bill->table_name) ?></td>
            </tr>
            <?php endif; ?>
            <?php if($new_bill->customer_name!=""): ?>
            <tr>
              <td>Nama</td>
              <td colspan="2"><?php echo strtoupper($new_bill->customer_name) ?></td>
            </tr>
            <tr>
              <td>Telp</td>
              <td colspan="2"><?php echo strtoupper($new_bill->customer_phone) ?></td>
            </tr>
            <tr>
              <td>Alamat</td>
              <td colspan="2"><?php echo strtoupper($new_bill->customer_address) ?></td>
            </tr>
            <?php endif; ?>
            <?php 
              $total=0;
              foreach($new_bill_menu as $bm): 
              $subtotal=($bm->quantity*$bm->price);
              $total+=$subtotal;
            ?>
            <tr>
              <td width="50%"><?php echo strtoupper($bm->menu_name) ?></td>
              <td width="25%" align="right"><?php echo $bm->quantity." x ".number_format($bm->price,0); ?></td>
              <td width="25%" align="right"><?php echo number_format($subtotal,0); ?></td>
            </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="2" align="right"><b>SUBTOTAL</b></td>
              <td align="right"><b><?php echo number_format($total,0); ?></b></td>
            </tr>
            <?php 
              foreach($new_bill_information as $bi): 
              if($bi->type==1){
                $total+=$bi->amount;
              }else{
                $total-=$bi->amount;
              }
            ?>
            <tr>
              <td colspan="2" align="right"><b><?php echo strtoupper($bi->info) ?></b></td>
              <td align="right"><b><?php echo number_format($bi->amount,0); ?></b></td>
            </tr>
            <?php endforeach; ?>
            <tr>
              <td colspan="2" align="right"><b>GRANDTOTAL</b></td>
              <td align="right"><b><?php echo number_format($total,0); ?></b></td>
            </tr>
            <?php 
              foreach($new_bill_payment as $bp): 
            ?>
            <tr>
              <td colspan="2" align="right"><b><?php echo strtoupper($bp->payment_option_name) ?></b></td>
              <td align="right"><b><?php echo number_format($bp->amount,0); ?></b></td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>