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
    <h4><label>Kontra Bon</label></h4>
    <h5><?php echo date("d F Y",strtotime($start_date))." s/d ".date("d F Y",strtotime($end_date));?></h5>
  </div>
  <table class="table table-bordered <?php echo ($is_print==true ? "border" : ""); ?>" style="font-size: 11px;">
    <thead>
      <tr class="vertical_middle">
        <th width="10%">Store</th>
        <th width="10%">No Purchase Order</th>
        <th width="10%">No Pembayaran</th>
        <th width="10%">Total</th>
        <th width="10%">Diskon</th>
        <th width="10%">Arrival</th>
        <th width="10%">Status</th>
        <th width="10%">Supplier</th>
        <th width="10%">Aksi</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($results as $payment): ?>
                    <tr>
                        <td><?php echo $payment->stname ?></td>
                        <td><?php echo $payment->number ?></td>
                        <td><?php echo $payment->payment_no ?></td>
                        <td><?php echo number_format($payment->total,0) ?></td>
                        <td><?php echo number_format($payment->discount,0) ?></td>
                        <td><?php echo date("d F Y",strtotime($payment->incoming_date)) ?></td>
                        <td><?php echo (0 == $payment->payment_status) ? 'Open' : 'Finished'; ?></td>
                        <td><?php echo $payment->supname ?></td>
                        <td>
                            <?php if(0 == $payment->payment_status): ?>
                                <a  href="<?php echo base_url().SITE_ADMIN.'/payment/detail/bon/'.$payment->po_id.'/'.$payment->id ?>"
                                    class="btn">
                                    Bayar
                                </a>
                            <?php else: ?>
                                <a  href="<?php echo base_url().SITE_ADMIN.'/payment/detail/view/'.$payment->po_id.'/'.$payment->id; ?>"
                                    class="btn">
                                    Detail
                                </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
    </tbody>
  </table>
</div>