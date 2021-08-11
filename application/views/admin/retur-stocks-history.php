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
                                foreach ($detail_retur as $pay):
                            ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4>Detail Pengembalian Ke-<?php echo $count; ?></h4>
                                    </div>

                                    <div class="panel-body">
                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">No. Retur</label>
                                            <div class="col-sm-3">
                                                <?php if(!empty($pay)) echo $pay->number; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-3 control-label">Tanggal Retur</label>
                                            <div class="col-sm-3">
                                                <?php if(!empty($pay)) echo $pay->retur_date; ?>
                                            </div>
                                        </div>                                        

                                        <div class="form-group col-lg-12">
                                            <div style="float:right">
                                                <a class="btn btn-success" href="<?php echo base_url(SITE_ADMIN . '/retur_order/prints/'.$purchase_order->id.'/'.$pay->id); ?>"><i class="fa fa-print" aria-hidden="true"></i> Cetak</a>
                                            </div>
                                        </div>

                                        <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                            <thead>
                                                <tr>
                                                    <th width="1%">No.</th>
                                                    <th width="8%">Kode Barang</th>
                                                    <th width="26%">Nama Barang</th>
                                                    <th width="8%">Jumlah Retur</th>
                                                    <th width="8%">Satuan</th>
                                                    <th width="22%">Catatan</th>
                                                    <th width="12%">Harga</th>
                                                    <th width="15%">Sub Total</th>
                                                </tr>                                                
                                            </thead>
                                            <tbody>
                                                <?php
                                                    $i = 1;
                                                    $detail_pesanan = $this->purchase_order_retur_detail_model->get(array('purchase_order_retur_id' => $pay->id));
                                                    foreach($detail_pesanan as $item):
                                                ?>
                                                <tr>
                                                    <td><?php echo $i; ?></td>
                                                    <td><?php echo $item->inventory_id; ?></td>
                                                    <td><?php echo $item->name; ?></td>
                                                    <td><?php echo $item->retur_quantity; ?></td>
                                                    <td><?php echo $item->code; ?></td>
                                                    <td><?php echo $item->notes; ?></td>
                                                    <td align="right">Rp. <?php echo number_format($item->price, 2); ?></td>
                                                    <td align="right">Rp. <?php echo number_format($item->retur_quantity * $item->price, 2); ?></td>
                                                </tr>
                                                <?php
                                                    $i++;
                                                    endforeach;
                                                ?>
                                                <tr>
                                                    <td align="right" colspan="7">Grand Total</td>
                                                    <td align="right">Rp. <?php echo number_format($pay->total, 2); ?></td>
                                                </tr>
                                            </tbody>
                                        </table>

                                        <!--<div style="float:right">
                                            <a class="btn btn-success" href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/prints/'.$purchase_order->id.'/'.$pay->id); ?>"><i class="fa fa-print" aria-hidden="true"></i> Cetak</a>
                                        </div>-->
                                    </div>
                                </div>
                            <?php
                                $count++;
                                endforeach;
                            ?>

                            <!--<div class="panel panel-default">
                                <div class="panel-heading">
                                <h4>Detail Retur</h4>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover dt-responsive">
                                        <thead>
                                            <tr>
                                                <th>Tanggal Retur</th>
                                                <th>No Retur</th>
                                                <th>Total </th>
                                                <th width="200px">Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($detail_retur as $item): ?>
                                            <tr>
                                                <td><?php echo $item->retur_date; ?></td>
                                                <td><?php echo $item->number; ?></td>
                                                <td>Rp. <?php echo number_format($item->total,2); ?></td>
                                                <td class="text-center">
                                                  <a class="btn btn-default" href="<?php echo base_url(SITE_ADMIN . '/retur_order/prints/'.$purchase_order->id.'/'.$item->id); ?>">Cetak</a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                <h4>Detail Retur Pesanan</h4>
                                </div>
                                <div class="panel-body">
                                    <table class="table table-striped table-bordered table-hover dt-responsive">
                                        <thead>
                                            <tr>
                                                <th>No Retur</th>
                                                <th>Nama</th>
                                                <th>Banyak Retur</th>
                                                <th>Harga Satuan</th>
                                                <th>Sub Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($detail_pesanan as $item): ?>
                                            <tr>
                                                <td><?php echo $item->number; ?></td>
                                                <td><?php echo $item->name; ?></td>
                                                <td><?php echo $item->retur_quantity.' '.$item->code; ?></td>
                                                <td>Rp. <?php echo number_format($item->price,2); ?></td>
                                                <td>Rp. <?php echo number_format($item->retur_quantity*$item->price,2); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                    
                                </div>
                            </div>-->
                            
                            <div class="form-group">
                                <div class="col-sm-offset-5 col-sm-2">
                                    <a href="<?php echo base_url(SITE_ADMIN . '/retur_order/listing/'); ?>"
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