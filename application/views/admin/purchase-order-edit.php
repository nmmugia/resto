<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<form method="post" class="form-horizontal form-ajax">
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
                                <div class="col-sm-9">
                                    <input value="<?php echo $purchase_order->number ?>" name="po_number" class="form-control" type="text" readonly="">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Tanggal</label>
                                <div class="col-sm-9">
                                    <div class='input-group date' id='purchase_date'>
                                        <?php echo form_input(array('name' => 'order_date',
                                            'id' => 'purchase_date_val',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Tanggal',
                                            'value' => $purchase_order->order_at

                                            )); ?>
                                        <span class="input-group-addon" style="cursor:pointer">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-3 control-label">Supplier</label>
                                <div class="col-sm-9">
                                    <select class="def-select form-control select2" name="supplier">
                                        <?php foreach($suppliers as $supplier): ?>
                                            <option value="<?php echo $supplier->id; ?>" <?php echo ($supplier->id == $purchase_order->supplier_id) ? 'selected' : '' ?>><?php echo $supplier->name; ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">Keterangan</label>
                                <div class="col-sm-9">
                                    <textarea name="description" cols="40" rows="5" type="text" class="form-control" field-name="Alamat resto"><?php echo $purchase_order->description; ?></textarea>
                                </div>
                            </div>
                          </fieldset>
                            <div class="clearfix"></div>
                          <fieldset>
                            <legend><h3>Data Pesanan</h3></legend>
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                <thead>
                                <tr>
                                    <th>Nama Inventori</th>
                                    <th  width="300px">Jumlah Order</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($details as $detail): ?>
                                    <tr>
                                        <td><?php echo $detail->item->name ?></td>
                                        <td id="sum-<?php echo $detail->id; ?>">
                                            <input class="spinner-<?php echo $detail->id; ?>" name="quantity[<?php echo $detail->id; ?>]" data-value="<?php echo $detail->quantity; ?>" value="<?php echo $detail->quantity; ?>" />
                                            <?php echo $detail->uom->code; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                          </fieldset>
                            <div class="text-center">
                                    <button type="submit" name="btnAction" value="save"
                                            class="btn btn-primary">Simpan
                                    </button>
                                    <a href="<?php echo base_url(SITE_ADMIN . '/purchase_order/po_list'); ?>"
                                       class="btn btn-default">Batal</a>
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