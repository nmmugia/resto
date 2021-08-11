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
                                <label class="col-sm-3 control-label">Tanggal Datang</label>
                                <div class="col-sm-9">
                                    <div class='input-group date date-input'>
                                        <?php echo form_input(array('name' => 'received_date',
                                            'id' => 'purchase_date_val',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Tanggal',
                                            'placeholder' => 'Tanggal',
                                            'value'         => set_value('received_date')

                                            )); ?>
                                        <span class="input-group-addon" style="cursor:pointer">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Dikirim ke</label>
                              <div class="col-sm-9" style="padding-top:7px;">
                                <select name="outlet_destination_id" class="form-control">
                                  <?php foreach($outlet_lists as $o): ?>
                                  <option value="<?php echo $o->id ?>"><?php echo $o->outlet_name ?></option>
                                  <?php endforeach; ?>
                                </select>
                              </div>
                            </div>
                            <div class="form-group">
                              <label class="col-sm-3 control-label">Otomatis Konversi</label>
                              <div class="col-sm-9" style="padding-top:7px;">
                                <input type="radio" name="auto_convert" value="1" checked> Ya
                                <input type="radio" name="auto_convert" value="0"> Tidak
                              </div>
                            </div>
                          </fieldset>
                          <div class="clearfix"></div>
                          <fieldset>
                            <legend><h3>Data Pesanan</h3></legend>
                            <input type="hidden" id="detail_order" name="detail_order" value="<?php echo count($detail); ?>">
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                <thead>
                                <tr>
                                    <th>Nama</th>
                                    <th width="120px;">Jumlah Order</th>
                                    <?php if($already_received == true): ?>
                                      <th>Kedatangan Sebelumnya</th>
                                    <?php endif; ?>
                                    <th width="220px;">Banyak Datang</th>
                                    <th width="220px;">Harga Satuan</th>
                                    <th>Sub Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $i = 0;
                                    foreach($detail as $item):                                        
                                    if(!isset($price[$item->id])) $price[$item->id] = 0;

                                    ?>
                                    <tr>
                                        <td><input type="hidden" name="inventory_id[]" value="<?php echo $item->inventory_id; ?>">
                                        <?php echo $item->name; ?></td>
                                        <td><?php echo $item->quantity; ?></td>
                                        <?php if($already_received == true): ?>
                                            <td><?php echo $previous[$item->id]->previous; $item->quantity -= $previous[$item->id]->previous; ?></td>
                                        <?php endif; ?>
                                        <td>
                                            <input id="receive-qty<?php echo $i; ?>" class="receive-qty spinner-<?php echo $item->id; ?>"
                                                    data-id="<?php echo $item->id; ?>"
                                                    name="received[<?php echo $item->id; ?>]"
                                                    value="<?php echo isset($received[$item->id]) ? $received[$item->id] : $item->quantity; ?>"
                                                    data-value="<?php echo isset($received[$item->id]) ? $received[$item->id] : $item->quantity; ?>" data-min="0" data-max="<?php echo $item->quantity; ?>" />
                                            <?php echo $item->code; ?>
                                        </td>
                                        <td>Rp <input type="text" class="pcs-price" id="pcs-price<?php echo $i; ?>" value="<?php echo set_value(0, $price[$item->id]); ?>" name="price[<?php echo $item->id; ?>]"/></td>
                                        <td>
                                            Rp <span class="sub-total" sub_total="0"><?php echo set_value(0, $sub_total) ?></span>
                                            <input type="hidden" name="sub_total[]" id="debit-hidden<?php echo $i; ?>" value="<?php echo set_value(0, $sub_total); ?>" />
                                        </td>
                                    </tr>
                                    <?php $i++; endforeach; ?>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <?php if($already_received == true): ?>
                                          <td></td>
                                        <?php endif; ?>
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
                          <fieldset>
                            <legend><h3>Metode Pembayaran</h3></legend>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Total</label>
                                <div class="col-sm-9" style="padding-top:7px;">
                                    Rp. <span class="grand-total"><?php echo set_value(0, $total) ?></span>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Diskon/Potongan/Pembulatan</label>
                                <div class="col-sm-9">
                                    <input type="text" name="discount" class="form-control" id="discount" value="<?php echo set_value(0, $discount); ?>"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Keterangan</label>
                                <div class="col-sm-9">
                                    <textarea placeholder="Keterangan" name="discount_description" cols="40" rows="5" type="text" class="form-control" field-name="Alamat resto"><?php echo set_value('discount_description') ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Grand Total</label>
                                <div class="col-sm-9" style="padding-top:7px;">
                                    Rp. <span class="grand-total-final"><?php echo set_value(0, $total-$discount); ?></span>
                                    <input type="hidden" id="grand-total-hidden" name="grand_total" value="<?php echo set_value(0, $total-$discount); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Metode Pembayaran</label>
                                <div class="col-sm-9">
                                    <div class="col-sm-2" style="padding-left:0px">
                                        <label><input type="radio" name="method" value="bon" checked/> Kontra Bon</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label><input type="radio" name="method" value="cash" /> Bayar Langsung</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" id="bon-date">
                                <label class="col-sm-3 control-label">Tanggal Kontra Bon</label>
                                <div class="col-sm-9">
                                    <div class='input-group date bon_date'>
                                        <?php echo form_input(array('name' => 'bon_date',
                                            'id' => 'bon_date',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Tanggal',
                                            'placeholder' => 'Tanggal',
                                            'value'         => set_value('bon_date')

                                            )); ?>
                                        <span class="input-group-addon" style="cursor:pointer">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                           <!--  <div class="form-group">
                                <label class="col-sm-3 control-label">No Pembayaran</label>
                                <div class="col-sm-9">
                                    <input type="text" name="payment_number" class="form-control" value="<?php echo set_value('payment_number'); ?>"/>
                                </div>
                            </div> -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Status Purchase Order</label>
                                <div class="col-sm-9">
                                    <div class="col-sm-2" style="padding-left:0px">
                                        <label><input type="radio" name="status" value="closed" checked/> Closed</label>
                                    </div>
                                    <div class="col-sm-3">
                                        <label><input type="radio" name="status" value="open" /> Open</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="invoice_logo" class="col-sm-2 control-label">Logo Invoice</label>

                                <div class="col-sm-10">
                                    <?php echo form_input($invoice_logo); ?>
                                    <small>*hanya BMP, max size 1 MB (150px x 81px)</small>
                                </div>
                            </div>
                            <!-- <?php
                            if (!empty($form_data['invoice_logo'])) {
                                ?>
                                <div class="form-group" id="primaryimage">
                                    <label for="pages_slug" class="col-sm-2 control-label">Logo Invoice</label>

                                    <div class="col-sm-10">
                                        <img class="gc_thumbnail" src="<?php echo base_url($form_data['invoice_logo']); ?>"
                                             style="padding:5px; border:1px solid #ddd"/>
                                        <a href="javascript:void(0);"
                                           url-data="<?php echo base_url(SITE_ADMIN . '/system/remove_printer_logo'); ?>"
                                           class="btn btn-danger removeImageMenu"><i class="fa fa-trash-o"></i></a>
                                    </div>
                                </div>
                            <?php
                            }
                            ?> -->
                          </fieldset>
                            <div class="form-group">
                                <div class="col-sm-offset-3 col-sm-9">
                                    <button type="submit" name="btnAction" value="save"
                                            class="btn btn-primary">Terima
                                    </button>
                                    <a href="<?php echo base_url(SITE_ADMIN . '/receive_stocks/listing'); ?>"
                                       class="btn btn-default">Batal</a>
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