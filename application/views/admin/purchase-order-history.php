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
                            <legend><h3>Informasi Purchase  Order</h3></legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Nomor Purchase Order</label>

                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $purchase_order->number; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Tanggal Order</label>
                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $purchase_order->order_at; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-3 control-label">Supplier</label>
                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $purchase_order->name; ?>
                                </div>
                            </div> 
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Keterangan</label>

                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $purchase_order->description; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Metode Pembayaran</label>

                                <?php if (!empty($receive)): ?>
                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $receive->payment_method == 1 ? "Cash" : "Kontra Bon"; ?>
                                </div>
                            <?php endif; ?>
                            </div>
                            <?php if (!empty($receive)): ?>
                            <div class="form-group" id="bon-date">
                                <label class="col-sm-3 control-label">Tanggal Kontra Bon</label>
                                <?php if($receive->payment_method == 2): ?>
                                <div class="col-sm-9"  style="padding-top:7px;">
                                    <?php echo $receive->payment_date; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endif; ?>
                          </fieldset>
                          <div class="clearfix"></div>
                          <fieldset>
                          <legend><h3>Detail Pesanan</h3></legend>
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Jumlah Pesanan</th>
                                        <th>Satuan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($detail_po as $item): ?>
                                    <tr>
                                        <td><?php echo $item->name; ?></td>
                                        <td><?php echo $item->quantity; ?></td>
                                        <td><?php echo $item->code; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                          </fieldset>
                            <div class="text-center">
                                <a href="<?php echo base_url(SITE_ADMIN . '/purchase_order/po_list/'); ?>" class="btn btn-default">Kembali</a>
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