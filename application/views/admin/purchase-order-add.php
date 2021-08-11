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
                                <label class="col-sm-2 control-label">Tanggal</label>
                                <div class="col-sm-10">
                                    <div class='input-group date' id='purchase_order_date'>
                                        <?php echo form_input(array('name' => 'order_date',
                                            'id' => 'purchase_date_val',
                                            'type' => 'text',
                                            'class' => 'form-control',
                                            'field-name' => 'Tanggal',
                                            'placeholder' => 'Tanggal',
                                            'value' => set_value('order_date')

                                            )); ?>
                                        <span class="input-group-addon" style="cursor:pointer">
                                            <span class="glyphicon glyphicon-calendar">
                                            </span>
                                        </span>
                                    </div> 
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="supplier_name" class="col-sm-2 control-label">Supplier</label>
                                <div class="col-sm-10">
                                    <select class="def-select form-control" name="supplier">
                                        <?php foreach($suppliers as $supplier): ?>
                                            <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                                        <?php endforeach ?>
                                    </select>
                                </div>
                            </div>
                           <!--  <div class="form-group">
                                <label class="col-sm-2 control-label">Nomor Purchase Order</label>

                                <div class="col-sm-10">
                                    <input placeholder="Nomor Purchase Order" value="<?php echo set_value('po_number') ?>" name="po_number" class="form-control" type="text">
                                </div>
                            </div> -->

                            <div class="form-group">
                                <label class="col-sm-2 control-label">Keterangan</label>

                                <div class="col-sm-10">
                                    <textarea placeholder="Keterangan" name="description" cols="40" rows="5" type="text" class="form-control" field-name="Alamat resto"><?php echo set_value('description') ?></textarea>
                                </div>
                            </div>
                          </fieldset>
                          <div class="clearfix"></div>
                          <fieldset>
                            <legend><h3>Request & Order</h3></legend>
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                <thead>
                                <tr>
                                    <th>Pesan</th>
                                    <th>Nama</th>
                                    <th>Banyak Request</th>
                                    <th>Tanggal Request</th>
                                    <th>Restoran</th>
                                    <th>Banyak Order</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($items as $item): ?>
                                    <tr data-id="<?php echo $item->id; ?>" >
                                        <td><input class="check-order" type="checkbox" name="check[<?php echo $item->id; ?>]" checked></td>
                                        <td><?php echo $item->name ?></td>
                                        <td><?php echo $item->request_quantity ?></td>
                                        <td><?php echo $item->created_at ?></td>
                                        <td><?php echo $item->requester_name ?></td>
                                        <td><input class="po-spinner spinner-<?php echo $item->id; ?>" data-id="<?php echo $item->id; ?>" name="transfer[<?php echo $item->id; ?>][quantity]" data-value="<?php echo $item->request_quantity; ?>"> <input type="hidden" name="uom[<?php echo $item->id; ?>]" value="<?php echo $item->uom_id ?>"><?php echo $item->code; ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                          </fieldset>
                          <div class="clearfix"></div>
                          <fieldset>
                            <legend><h3>Data Pesanan</h3></legend>
                            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-purchase-order-list">
                                <thead>
                                <tr>
                                    <th>Nama Inventori</th>
                                    <th>Jumlah Order</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($recaps as $id => $recap): ?>
                                    <tr>
                                        <td><?php echo $recap['name'] ?></td>
                                        <td id="sum-<?php echo $id; ?>"> <?php echo $recap['sum'] ?></td>
                                        <input type="hidden" id="hidden-sum-<?php echo $item->id; ?>" data-id="<?php echo $id; ?>" name="sum[<?php echo $id; ?>]" value="<?php echo $recap['sum'] ?>" >
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                          </fieldset>
                            <div class="text-center">
                                  <button type="submit" name="btnAction" value="save"
                                          class="btn btn-primary">Order
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