<?php if (! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @author      Diky Pratansyah <pratansyah@gmail.com>
 * @copyright   2015 Digital Oasis
 * @since       1.0.0
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
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <form method="POST" class="form-horizontal">
                <div class="panel-heading">
                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                               Permintaan Dari
                            </div>
                            <div class="panel-body">
                                <table class="form-table-cms">
                                    <tbody>
                                        <tr>
                                            <td class="col-md-2"><label>Permintaan Dari</label></td>
                                            <td class="col-md-3">
                                                    <?php  echo ($data_store->store_name);?>
                                            </td>
                                        </tr>
                                         <tr>
                                            <td class="col-md-2"><label>Departement</label></td>
                                            <td class="col-md-3">
                                                <select  class="def-select form-control" name="requester_outlet_id">
                                                    <option value="" selected="selected">Select Outlet</option>
                                                    <?php foreach($outlets as $outlet): ?>
                                                        <option value="<?php echo $outlet->id; ?>" <?php echo set_select('store-id', $outlet->id); ?>><?php echo $outlet->outlet_name; ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                Request Ke
                            </div>
                            <div class="panel-body">
                                <table class="form-table-cms">  
                                    <tbody>
                                        <tr>
                                            <td class="col-md-2"><label>Request ke</label></td>
                                            <td class="col-md-3">
                                                <select id="request-transfer-select-store" class="def-select form-control" name="store-id">
                                                    <option value="" selected="selected">Select Store</option>
                                                    <?php foreach($stores as $store): ?>
                                                        <option value="<?php echo $store->id; ?>" <?php echo set_select('store-id', $store->id); ?>><?php echo $store->store_name; ?></option>
                                                    <?php endforeach ?>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="col-md-2"><label>Department</label></td>
                                            <td class="col-md-3">
                                                <select id="request-transfer-select-outlet" class="def-select form-control" name="outlet-id">
                                                    <option value="" selected="selected">Select Outlet</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <fieldset>
                        <legend><h3>Data Permintaan</h3></legend>
                        <table class="table table-striped table-bordered table-hover dt-responsive" id="po_create_table">
                            <thead>
                                <tr>
                                    <th>Nama Inventori</th>
                                    <th width="200px" class="text-center">Jumlah Order</th>
                                    <th width="150px" class="text-center">Satuan</th>
                                    <th width="50px"><a href="javascript:void(0);" class="btn btn-sm btn-primary" id="add_po_create"><i class="fa fa-plus"></i></a></th>
                                </tr>
                            </thead>
                            <tbody><?php echo $add_inventory; ?></tbody>
                        </table>
                    </fieldset>
                    <div class="text-center">
                        <button id="save-request" type="submit" value="save" class="btn btn-primary">Simpan</button>
                        <a href="<?php echo base_url(SITE_ADMIN . '/stock_transfer/request'); ?>" class="btn btn-default">Batal</a>
                    </div>

                    <!-- /.table-responsive -->
                </div>
                <!-- /.panel-body -->
                </form>
            </div>
            <!-- /.panel -->
        </div>
        <input id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>" type="hidden">
        <!-- /.col-lg-12 -->

    </div>
    <!-- /.row -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>