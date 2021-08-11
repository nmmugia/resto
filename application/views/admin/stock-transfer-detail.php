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
       
        <div class="panel-heading">
            <table class="col-lg-12 form-table-cms">
                <tbody>
                    <tr>
                        <td class="col-md-2"><label>Request ke</label></td>
                        <td class="col-md-3">: <?php echo $request->supplier_name; ?></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-md-2"><label>Status</label></td>
                        <td class="col-md-3">: <?php echo $status_requester[$request->requester_status]; ?></td>
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
                        <td class="col-md-2"><label>Tanggal Kirim</label></td>
                        <td class="col-md-3">: <?php echo isset($request->sent_at) ? date('d M Y H:i', strtotime($request->sent_at)) : 'Belum Dikirim'; ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td class="col-md-2"><label>Tanggal Selesai</label></td>
                        <td class="col-md-3">: <?php echo isset($request->finished_at) ? date('d M Y H:i', strtotime($request->finished_at)) : 'Belum Diterima'; ?></td>
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
                    <th>Banyak datang</th>
                    <th>Satuan</th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($detail as $item): ?>
                    <tr>
                        <td><?php echo $item->name ?></td>
                        <td><?php echo $item->request_quantity; ?></td>
                        <td><?php echo $item->provided_quantity; ?></td>
                        <td><?php echo $item->received_quantity; ?></td>
                        <td><?php echo $item->code ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <div class="pull-right">
                <a href="<?php echo $back_url; ?>" class="btn btn-default">Kembali</a>
            </div>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>