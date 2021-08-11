<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<form method="post" class="form-horizontal form-ajax" enctype="multipart/form-data">
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
                            <legend><h3>Informasi</h3></legend>
                             <div class="form-group">
                                <label class="col-sm-3 control-label">Nomor Purchase Order</label>

                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $purchase_order->number; ?>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Tanggal Diterima</label>
                                <div class="col-sm-9" style="padding-top:7px;">
                                    <?php echo $purchase_order_receive->incoming_date; ?>
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
                                <label class="col-sm-3 control-label">Tanggal Retur</label>
                                <div class="col-sm-9">
                                    <div class='input-group date date-input'>
                                        <?php echo form_input(array('name' => 'retured_date',
                                            'id' => 'retur_date_val',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Tanggal',
                                            'placeholder' => 'Tanggal',
                                            'value'         => set_value('retured_date')

                                            )); ?>
                                        <span class="input-group-addon" style="cursor:pointer">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div> 
                                </div>
                            </div>                            
                          </fieldset>
                          <div class="clearfix"></div>
                          <fieldset>
                            <legend><h3>Data Pesanan</h3></legend>
                            <input type="hidden" id="detail_order" name="detail_order" value="<?php echo count($detail); ?>">
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-retur-order-list">
                                <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th width="10%;">Jumlah Datang</th>
                                    <th width="10%;">Retur Sebelumnya</th>
                                    <th width="10%;">Banyak Retur</th>
                                    <th width="20%;">Catatan</th>
                                    <th width="15%;">Harga Satuan</th>
                                    <th width="15%;">Sub Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach($detail as $item):
                                    ?>
                                    <tr>
                                        <td><input type="hidden" name="inventory_id[]" value="<?php echo $item->inventory_id; ?>">
                                        <?php echo $item->name; ?></td>
                                        <td><input type="hidden" id="received_quantity<?php echo $i; ?><?php echo $i; ?>" name="received_quantity[<?php echo $item->id; ?>]" value="<?php echo $item->quantity; ?>"><?php echo $item->quantity.' '.$item->code; ?></td>
                                        
                                        <td><input type="hidden" id="retured_quantity<?php echo $i; ?>" value="<?php echo ($item->retur_quantity != null) ? $item->retur_quantity : 0; ?>"><?php echo ($item->retur_quantity != null) ? $item->retur_quantity : 0; echo ' '.$item->code; ?></td>
                                        
                                        <td>
                                            <?php
                                                $max_value = 0;
                                                if ($item->retur_quantity != null) {
                                                    $max_value = $item->quantity - $item->retur_quantity;
                                                } else {
                                                    $max_value = $item->quantity;
                                                }
                                            ?>
                                            <input type="hidden" id="max-retur-<?php echo $i; ?>" value="<?php echo $max_value; ?>">
                                            <input id="retur-qty<?php echo $i; ?>" class="retur-qty spinner-<?php echo $item->id; ?>"
                                                    type="number"
                                                    min="0"
                                                    max="<?php echo $max_value; ?>"
                                                    data-id="<?php echo $item->id; ?>"
                                                    name="retured[<?php echo $item->id; ?>]"
                                                    value="0"
                                                    data-value="0" data-min="0" data-max="<?php echo $item->quantity - ($item->retur_quantity != null) ? $item->retur_quantity : 0; ?>" style="width: 60%;" />
                                            <?php echo $item->code; ?>
                                        </td>
                                        <td>
                                            <textarea placeholder="Catatan" name="notes[<?php echo $item->id; ?>]" cols="10" rows="2" type="text" class="form-control" field-name="Catatan retur"><?php echo set_value('info'); ?></textarea>
                                        </td>
                                        <td>Rp <input type="hidden" class="pcs-price" id="pcs-price<?php echo $i; ?>" value="<?php echo set_value(0, $item->price); ?>" name="price[<?php echo $item->id; ?>]"/><?php echo number_format($item->price); ?> </span></td>
                                        <td>
                                            Rp <span class="sub-total" sub_total="0"><?php echo set_value(0, $sub_total) ?></span>
                                            <input type="hidden" name="sub_total[]" id="debit-hidden<?php echo $i; ?>" value="<?php echo set_value(0, $sub_total); ?>" />
                                        </td>
                                    </tr>
                                    <?php $i++; endforeach; ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
                                        <td>
                                            Rp <span class="grand-total"><?php echo set_value(0, $total) ?></span>
                                            <input type="hidden" name="total" id="total-hidden" value="<?php echo set_value(0, $total); ?>"/>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                          </fieldset>
                          <div class="clearfix"></div>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" name="btnAction" value="save"
                                            class="btn btn-primary">Simpan
                                    </button>
                                    <a href="<?php echo base_url(SITE_ADMIN . '/retur_order/listing'); ?>"
                                       class="btn btn-danger">Batal</a>
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