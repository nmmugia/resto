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
                             <div class="panel panel-default">
                                <div class="panel-heading">
                                <h4>Informasi</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Nomor Purchase Order</label>

                                        <div class="col-sm-9">
                                            <?php echo $purchase_order->number; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Tanggal Order</label>
                                        <div class="col-sm-9">
                                            <?php echo $purchase_order->order_at; ?>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="supplier_name" class="col-sm-3 control-label">Supplier</label>
                                        <div class="col-sm-9">
                                            <?php echo $purchase_order->name; ?>
                                        </div>
                                    </div>
                                   

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">Keterangan</label>

                                        <div class="col-sm-9">
                                            <?php echo $purchase_order->description; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php
                                $count = 1;
                                foreach ($detail_pembayaran as $pay):
                            ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4>Detail Penerimaan Ke-<?php echo $count; ?></h4>
                                    </div>

                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">No. Pembayaran</label>
                                            <div class="col-sm-3">
                                                <?php if(!empty($pay)) echo $pay->payment_no; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Tanggal Kedatangan</label>
                                            <div class="col-sm-3">
                                                <?php if(!empty($pay)) echo $pay->incoming_date; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Metode Pembayaran</label>
                                            <div class="col-sm-3">
                                                <?php if(!empty($pay)) echo ($pay->payment_method == 1) ? 'Bayar Langsung' : 'Kontra Bon'; ?>
                                            </div>
                                        </div>

                                        <div class="form-group col-lg-12">
                                            <div style="float:right">
                                                <a class="btn btn-success" href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/prints/'.$purchase_order->id.'/'.$pay->id); ?>"><i class="fa fa-print" aria-hidden="true"></i> Cetak</a>
                                            </div>
                                        </div>

                                        <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                            <thead>
                                                <tr>
                                                    <th>No.</th>
                                                    <th>Kode Barang</th>
                                                    <th>Nama Barang</th>
                                                    <th>Jumlah Barang</th>
                                                    <th>Satuan</th>
                                                    <th>Harga</th>
                                                    <th>Sub Total</th>
                                                </tr>                                                
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $i = 1;
                                                    $detail_pesanan = $this->purchase_order_receive_detail_model->get(array('purchase_order_receive_id' => $pay->id));
                                                    foreach($detail_pesanan as $item):
                                                ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $item->inventory_id; ?></td>
                                                    <td><?php echo $item->name; ?></td>
                                                    <td><?php echo $item->received_quantity; ?></td>
                                                    <td><?php echo $item->code; ?></td>
                                                    <td align="right">Rp. <?php echo number_format($item->price, 2); ?></td>
                                                    <td align="right">Rp. <?php echo number_format($item->received_quantity * $item->price, 2); ?></td>
                                                </tr>
                                                <?php
                                                    $i++;
                                                    endforeach;
                                                ?>
                                                <tr>
                                                    <td align="right" colspan="6">Total</td>
                                                    <td align="right">Rp. <?php echo number_format($pay->total, 2); ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="right" colspan="6">Diskon</td>
                                                    <td align="right">Rp. <?php echo number_format($pay->discount, 2); ?></td>
                                                </tr>
                                                <tr>
                                                    <td align="right" colspan="6">Grand Total</td>
                                                    <td align="right">Rp. <?php echo number_format(($pay->total - $pay->discount), 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php
                                $count++;
                                endforeach;
                            ?>

                            <!--<div class="panel panel-default">
                                <div class="panel-heading">
                                <h4>Detail Pembayaran</h4>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover dt-responsive">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Kedatangan</th>
                                                <th>No Pembayaran</th>
                                                <th>Metode Pembayaran</th>
                                                <th>Total </th>
                                                <th>Diskon Total</th>
                                                <th width="200px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($detail_pembayaran as $item): ?>
                                            <tr>
                                                <td><?php echo $item->incoming_date; ?></td>
                                                <td><?php echo $item->payment_no; ?></td>
                                                <td><?php echo ($item->payment_method == 1)? "Bayar Langsung" : "Kas Bon"; ?></td>
                                                <td>Rp. <?php echo number_format($item->total,2); ?></td>
                                                <td>Rp. <?php echo number_format($item->discount,2); ?></td>
                                                <td class="text-center">-->
                                                  <!-- <a class="btn btn-default" href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/detail/'.$purchase_order->id.'/'.$item->id); ?>">Detail</a> -->
                                                  <!--<a class="btn btn-default" href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/prints/'.$purchase_order->id.'/'.$item->id); ?>">Cetak</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

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
                                                <th>No Pembayaran</th>
                                                <th>Nama</th>
                                                <th>Banyak Datang</th>
                                                <th>Harga Satuan</th>
                                                <th>Sub Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($detail_pesanan as $item): ?>
                                            <tr>
                                                <td><?php echo $item->payment_no; ?></td>
                                                <td><?php echo $item->name; ?></td>
                                                <td><?php echo $item->received_quantity.' '.$item->code; ?></td>
                                                <td>Rp. <?php echo number_format($item->price,2); ?></td>
                                                <td>Rp. <?php echo number_format($item->received_quantity*$item->price,2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    
                                </div>
                            </div>-->
                            
                            <div class="form-group">
                                <div class="col-sm-offset-5 col-sm-2">
                                    <a href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/listing/'); ?>"
                                       class="btn btn-primary">Kembali</a>
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