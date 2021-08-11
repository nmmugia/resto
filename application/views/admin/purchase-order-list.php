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
          <div class="col-lg-7 pull-left" style="padding-left:0px;">
            <div class="col-lg-6">
              <b>Pencarian Supplier</b>
              <select class="form-control select2" name="filter_supplier_id" id="filter_supplier_id" data-target-column="2">
                <option value="">Semua Supplier</option>
                <?php foreach($supplier_lists as $l): ?>
                <option value="<?php echo $l->id ?>"><?php echo $l->name ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-lg-4">
              <b>Pencarian Tanggal</b>              
              <div class="input-group date filter_date">
                <input type="text" name="filter_date" value="" data-target-column="1" class="form-control date">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
            </div>
            <div class="col-lg-2">
              <br>
              <button type="button" class="btn btn-primary" id="trigger_filter_po">Cari</button>
            </div>
          </div>
          <div class="col-lg-5 pull-right">
            <a href="<?php echo base_url(SITE_ADMIN . '/purchase_order/add'); ?>" class="btn btn-primary pull-right" style="margin-left:10px;"><i
                    class='fa fa-plus'></i>Tambah PO Request</a>
            <a href="<?php echo base_url(SITE_ADMIN . '/purchase_order/po_create'); ?>" class="btn btn-primary pull-right"><i
                    class='fa fa-plus'></i>Tambah PO Manual</a>
          </div>
          <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-purchase-order-list">
                <thead>
                <tr>
                    <th width="15%">Nomor</th>
                    <th width="16%">Tanggal</th>
                    <th width="20%">Supplier</th>
                    <th>Keterangan</th>
                    <th width="15%" style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
            <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN."/purchase_order/get_po_data") ?>"/>
            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>