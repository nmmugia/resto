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
            <a href="<?php echo base_url(SITE_ADMIN . '/stock_transfer/receive'); ?>" class="btn btn-primary pull-right">
                <i class='fa fa-refresh'></i>Refresh status
            </a>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-stock-transfer-request">
                <thead>
                <tr>
                    <th>Tanggal Request</th>
                    <th>Permintaan Dari</th>
                     <th>Outlet</th>
                    <th>Status Request</th>
                    <th>Status Pengirim</th>
                    <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
                <tbody>
                    <?php foreach($table as $item): ?>
                    <tr>
                        <td><?php echo date('d F Y H:i', strtotime($item->created_at)); ?></td>
                        <td><?php echo $item->requester_name; ?></td>
                          <td><?php echo $item->requester_outlet_name; ?></td>
                        <td><?php echo $status_requester[$item->requester_status]; ?></td>
                        <td><?php echo $status_supplier[$item->supplier_status]; ?></td>
                        <td>
                            <?php foreach($item->action as $button): ?>
                            <a href="<?php echo $button['href'] ?>" class="btn btn-default <?php echo isset($button['class']) ? $button['class'] : ''; ?>">  <?php echo $button['action']; ?></a>
                            <?php endforeach; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>