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
                           <div class="clearfix"></div>

                            <div class="panel panel-default">
                                <div class="panel-heading">
                                <h4>Metode Pembayaran</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">No Pembayaran</label>
                                        <div class="col-sm-9">
                                            <?php echo $receive->payment_no; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Metode Pembayaran</label>
                                        <div class="col-sm-9">
                                            <?php echo ($receive->payment_method == 1)?"Bayar Langsung":"Kontra Bon"; ?>
                                        </div>
                                    </div>
                                    <?php if( $receive->payment_method == 2){?>
                                    <div class="form-group" id="bon-date">
                                        <label class="col-sm-3 control-label">Tanggal Kontra Bon</label>
                                        <div class="col-sm-9">
                                            <?php echo $receive->payment_date; ?>
                                        </div>
                                    </div>
                                    <?php }?>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Total</label>
                                        <div class="col-sm-9">
                                            Rp. <span class="grand-total"><?php echo $receive->total; ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Diskon/Potongan/Pembulatan</label>
                                        <div class="col-sm-9">
                                            Rp. <?php echo $receive->discount; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Grand Total</label>
                                        <div class="col-sm-9">
                                            Rp. <span class="grand-total-final"><?php echo $receive->total-$receive->discount; ?></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Keterangan</label> 
                                        <div class="col-sm-9">
                                            <?php echo $receive->discount_description; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                <h4>Detail Pesanan</h4>
                                </div>
                                <div class="panel-body">
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
                                                <td><?php echo $item->received_quantity.' '.$item->unit; ?></td>
                                                <td>Rp <?php echo $item->price; ?></td>
                                                <td>Rp <?php echo $item->received_quantity*$item->price; ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    
                                </div>
                            </div>
                             
                            
                           
                           
                            <div class="form-group">
                                <div class="col-sm-offset-5 col-sm-2">
                                    <a href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/history/'.$purchase_order->id); ?>"
                                       class="btn btn-default">Kembali</a>
                                </div>
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