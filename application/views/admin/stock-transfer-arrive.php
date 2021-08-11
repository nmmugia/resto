<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       2.0.0
 */

?>
<div class="col-lg-12">
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
    <div class="panel panel-default">
        <form method="POST">
        <div class="panel-heading">
            <table class="col-lg-12 form-table-cms">
                <tbody>
                    <tr>
                        <td class="col-md-2"><label>Request ke</label></td>
                        <td class="col-md-3">: <?php echo $request->supplier_name; ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-md-2"><label>Tanggal Request</label></td>
                        <td class="col-md-3">: <?php echo date('d M Y H:i', strtotime($request->request_at)); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-md-2"><label>Tanggal Dikirim</label></td>
                        <td class="col-md-3">: <?php echo date('d M Y H:i', strtotime($request->sent_at)); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-md-2"><label>Masukkan ke</label></td>
                        <td class="col-md-3">
                            <select id="request-transfer-select-store" class="def-select form-control" name="outlet_id">
                                <?php foreach($outlets as $outlet): ?>
                                    <option value="<?php echo $outlet->id; ?>"><?php echo $outlet->outlet_name; ?></option>
                                <?php endforeach ?>
                            </select>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-transfer-request">
                <thead>
                <tr>
                    <th>Name</th>
                    <th>Banyak Request</th>
                    <th>Banyak Dikirim</th>
                    <th>Banyak Datang</th>
                    <th>Satuan</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($detail as $item): ?>
                    <tr>
                        <td><?php echo $item->name; ?></td>
                        <td><?php echo $item->request_quantity; ?></td>
                        <td><?php echo $item->provided_quantity; ?></td>
                        <td>
                            <input class="stock-request-spinner" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][quantity]" data-value="<?php echo (int)$item->provided_quantity; ?>" data-max="<?php echo (int)$item->provided_quantity; ?>">
                            <input type="hidden" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][inventory_id]" value="<?php echo $item->inventory_id; ?>"/>
                            <input type="hidden" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][name]" value="<?php echo $item->name; ?>"/>                            
                            <input type="hidden" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][uom_id]" value="<?php echo $item->uom_id; ?>"/>                            
                        </td>
                        <td><?php echo $item->code ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="col-sm-offset-10 col-sm-10">
                <a href="<?php echo base_url(SITE_ADMIN) ?>/stock_transfer/request" class="btn btn-default">Kembali</a>
                <button type="submit" value="save" class="btn btn-primary">Terima</button>
            </div>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
        </form>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>