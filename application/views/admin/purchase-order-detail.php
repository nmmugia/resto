<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<form method="post" class="form-horizontal form-ajax">
<style>
   .form-group label{
            text-align: left !important;
    }
</style>
<div class="col-lg-12" style="padding: 0 !important">
    <div class="result">
        <?php
        if (! empty($message_success)) {
            echo '<div class="alert alert-success" role="alert">';
            echo $message_success;
            echo '</div>';
        }
        if (! empty($message)) {
            echo '<div class="alert alert-danger" role="alert">';
            echo $message;
            echo '</div>';
        }
        ?>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-body">
                    <div class="row">
                        <div class="col-lg-12">
                           <fieldset>
                          <legend><h3>Metode Pembayaran</h3></legend>
                          <div class="form-group">
                                <label class="col-sm-3 control-label">No Pembayaran</label>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                    <?php echo $receive->payment_no; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Metode Pembayaran</label>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                    <?php echo ($receive->payment_method==1 ? "Cash" : "Kontra Bon"); ?>
                                </div>
                            </div>
                            <?php if($receive->payment_method==2): ?>
                            <div class="form-group" id="bon-date">
                                <label class="col-sm-3 control-label">Tanggal Kontra Bon</label>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                    <?php echo $receive->payment_date; ?>
                                </div>
                            </div>
                              <?php endif; ?>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Total</label>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                    Rp. <span class="grand-total"><?php echo number_format($receive->total,2); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Diskon/Potongan/Pembulatan</label>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                   Rp. <?php echo number_format($receive->discount,2); ?>
                                </div>
                            </div>
                             <div class="form-group">
                                <label class="col-sm-3 control-label">Grand Total</label>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                    Rp. <span class="grand-total-final"><?php echo number_format($receive->total-$receive->discount,2); ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Keterangan</label>

                                <div class="col-sm-9"  style="padding-top:7px;">
                                    <?php echo $receive->discount_description; ?>
                                </div>
                            </div>
                           
                            
                          
                          </fieldset>
                          <div class="clearfix"></div>
                          <fieldset>
                            <legend><h3>Data Pesanan</h3></legend>
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Banyak Datang</th>
                                        <th>Harga Satuan</th>
                                        <th>Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($detail as $item): ?>
                                    <tr>
                                        <td><?php echo $item->name; ?></td>
                                        <td><?php echo $item->received_quantity.' '.$item->code; ?></td>
                                        <td>Rp <?php echo number_format($item->price,2); ?></td>
                                        <td>Rp <?php echo number_format($item->received_quantity*$item->price,2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                          </fieldset>
                          <div class="clearfix"></div>
                          
                            <div class="text-center">
                              <a href="<?php echo base_url(SITE_ADMIN . '/purchase_order/history/'.$purchase_order->id); ?>" class="btn btn-default">Kembali</a>
                            </div>
                        </div>
                        <!-- /.row (nested) -->
                    </div>

                    <!-- /.panel-body -->
                </div>
                <!-- /.panel -->
            </div>
        </div>
    </div>
</div>
</form>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>