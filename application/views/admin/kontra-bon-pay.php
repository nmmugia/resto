<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<form method="post" class="form-horizontal form-ajax">
<div class="col-lg-12" style="padding: 0 !important">
<style>
   .form-group label{
            text-align: left !important;
    }
</style>
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
                                <div class="panel-heading"><h4>Informasi Umum</h4></div>
                                <div class="panel-body">
                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="col-sm-6 control-label">Nomor Purchase Order</label>

                                            <div class="col-sm-6">
                                                <?php  if(!empty($purchase_order)) echo $purchase_order->number; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-6 control-label">Tanggal Order</label>
                                            <div class="col-sm-6">
                                                <?php if(!empty($purchase_order)) echo $purchase_order->order_at; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="supplier_name" class="col-sm-6 control-label">Supplier</label>
                                            <div class="col-sm-6">
                                                <?php if(!empty($purchase_order)) echo $purchase_order->name; ?>
                                            </div>
                                        </div>
                                       
                                        <div class="form-group">
                                            <label class="col-sm-6 control-label">Keterangan</label>

                                            <div class="col-sm-6">
                                                <?php if(!empty($purchase_order)) echo $purchase_order->description; ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-lg-6">
                                        <div class="form-group">
                                            <label class="col-sm-6 control-label">No Pembayaran</label>
                                            <div class="col-sm-6">
                                                <?php  if(!empty($receive)) echo $receive->payment_no; ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-sm-6 control-label">Metode Pembayaran</label>
                                            <div class="col-sm-6">
                                             <?php  if(!empty($receive)) echo ($receive->payment_method==1 ? "Cash" : "Kontra Bon"); ?>
                                               
                                            </div>
                                        </div>

                                        <div class="form-group" id="bon-date">
                                            <label class="col-sm-6 control-label">Tanggal Kontra Bon</label>
                                            <div class="col-sm-6">
                                                <?php  if(!empty($receive)) echo $receive->payment_date; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4>Detail Pesanan</h4>
                                </div>

                                <div class="panel-body">
                                    <?php if($mode != "bon"): ?>
                                    <a style="float:right; margin-bottom:5px;" href="<?php echo base_url(SITE_ADMIN . '/payment/prints/'.$purchase_order->id.'/'.$receive->id.'/'.date("Y-m-d").'/0/true'); ?>"
                                       class="btn btn-success"><i class="fa fa-print"> Cetak</i></a>
                                    <?php endif; ?>


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
                                                <td align="right"><?php echo convert_rupiah($item->price); ?></td>
                                                <td align="right"><?php echo convert_rupiah($item->received_quantity*$item->price); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <tr>
                                                <td align="right" colspan="3">Total</td>
                                                <td align="right"><?php if(!empty($receive)) echo convert_rupiah($receive->total); ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right" colspan="3">Diskon/Potongan</td>
                                                <td align="right"><?php if(!empty($receive)) echo convert_rupiah($receive->discount); ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right" colspan="3">Sudah Dibayar</td>
                                                <td align="right"><?php echo (!empty($payment_history)) ? convert_rupiah($payment_history->total) : 0; ?></td>
                                            </tr>
                                            <tr>
                                                <td align="right" colspan="3">Sisa Hutang</td>
                                                <td align="right">
                                                    <?php
                                                        $payment_history = (!empty($payment_history)) ? $payment_history->total : 0;
                                                        if(!empty($receive)) echo convert_rupiah($receive->total - $receive->discount - $payment_history);
                                                    ?>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>

                                    <?php if($module['ACCOUNTING']==1): ?>
                                    <div class="row">
                                        <div class="col-sm-8 form-group">
                                            <label class="col-sm-4 control-label">Akun Pelunasan</label>
                                            <div class="col-sm-4">
                                                <select class="form-control" name="account_id" id="account_id">
                                                    <?php
                                                        echo '<option value="">Pilih Akun</option>';
                                                        foreach($account_lists as $a) {
                                                            echo '<option value="'.$a->id.'">'.$a->name.'</option>';
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>

                                    <div class="row">
                                        <div class="col-sm-10 form-group">
                                            <label class="col-sm-4 control-label">Tanggal Bayar</label>
                                            <div class="col-sm-6">
                                                <?php if($receive->payment_status == 0){ ?>
                                                   <div class='input-group date ' id='payment_bon_date'>
                                                    <?php
                                                    
                                                    echo form_input(array('name' => 'payment_bon_date',
                                                        'id' => 'payment_bon_date_val',
                                                        'type' => 'text',
                                                        'class' => 'form-control date',
                                                        'onkeydown' => 'return false'
                                                    ));
                                                
                                                    ?>
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar">
                                                            </span>
                                                        </span>
                                                    </div> 
                                                <?php }else{ ?>
                                                    <div class="col-sm-4">
                                                        <?php if(!empty($receive)) echo $receive->payment_date; ?>
                                                    </div>
                                                <?php }?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-sm-8 form-group">
                                            <label class="col-sm-4 control-label">Jumlah Bayar</label>
                                            <div class="col-sm-4">
                                                <?php
                                                    if (!empty($receive)) {
                                                        if ($receive->payment_status == 1) {
                                                            echo form_input(array('name' => 'payment_amount',
                                                                'id' => 'payment_amount',
                                                                'type' => 'text',
                                                                'class' => 'form-control requiredTextField only_alpha_numeric',
                                                                'field-name' => 'Jumlah Bayar',
                                                                'placeholder' => 'Masukan jumlah bayar',
                                                                'disabled' => ''
                                                            ));
                                                        } else {
                                                            echo form_input(array('name' => 'payment_amount',
                                                                'id' => 'payment_amount',
                                                                'type' => 'text',
                                                                'class' => 'form-control requiredTextField only_alpha_numeric',
                                                                'field-name' => 'Jumlah Bayar',
                                                                'placeholder' => 'Masukan jumlah bayar'
                                                            ));
                                                        }
                                                    }
                                                ?>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-lg-8 text-right"> 
                                            <div class="form-group">
                                                <div class="col-sm-4">
                                                    <?php if($mode == "bon"): ?>
                                                    <a href="<?php echo base_url(SITE_ADMIN . '/payment/bon/'.$receive->id); ?>"
                                                       class="btn btn-primary btn-default pay-bon">Bayar</a>
                                                    <?php endif; ?>
                                                    <a href="<?php echo base_url(SITE_ADMIN . '/reports/kontra_bon/'); ?>"
                                                       class="btn btn-default">Kembali</a>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- /.row (nested) -->
                                    </div>
                                </div>
                            </div>
                        </div>
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