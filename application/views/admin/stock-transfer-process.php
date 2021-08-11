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
                        <td class="col-md-2"><label>Permintaan dari</label></td>
                        <td class="col-md-3">: <?php echo $request->requester_name; ?></td>
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
                        <td class="col-md-2"><label>Ke Outlet</label></td>
                        <td class="col-md-3">: <?php echo $request->supplier_outlet_name; ?></td>
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
                    <th>Banyak Stok Tersedia</th>
                    <th>Banyak Dikirim</th>
                    <th>Satuan</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($detail as $item): ?>
                    <tr>
                        <td><?php echo $item->name ?></td>
                        <td><?php echo $item->request_quantity; ?></td>
                        <td><?php echo $item->sum_quantity; ?></td>
                        <td>
                            <input class="stock-request-spinner" 
                                name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][quantity]" data-value="<?php echo $item->request_quantity; ?>"
                                 data-max="<?php echo (null == $item->spinner) ? 0 : $item->spinner; ?>">
                            <input type="hidden" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][inventory_id]" value="<?php echo $item->inventory_id; ?>"/>
                            <input type="hidden" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][name]" value="<?php echo $item->name; ?>"/>                            
                            <input type="hidden" name="transfer[<?php echo $item->stock_transfer_detail_id; ?>][uom_id]" value="<?php echo $item->uom_id; ?>"/>                            
                        </td>
                        <td><?php echo $item->code ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="col-sm-offset-11 col-sm-2">
                <button type="submit" value="save" class="btn btn-primary">Kirim</button>
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