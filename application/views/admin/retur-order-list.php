<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>

<div class="col-lg-12">
	<div class="panel panel-default">
		<div class="panel-heading">
      <form id="formFilter" method="POST" target="_blank">
        <input type="hidden" id="type" name="type" value="summary_receive_order">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
          	<tr>
              <td><label>Supplier</label></td>
              <td class="col-sm-8">
                <select class="form-control select2" name="supplier_id" id="filter_supplier_id" data-target-column="1">
                  <option value="">Semua Supplier</option>
                  <?php foreach($supplier_lists as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name; ?></option>
                  <?php endforeach; ?>
                </select>
              </td>
            </tr>
          
            <tr>
              <td><label>Dari Tanggal</label></td>
              <td class="col-sm-8">
                <div class="input-group date filter_date">
                <input id="filter_start_date" type="text" name="start_date" value="<?php echo date("Y-m-01"); ?>" data-target-column="2" class="form-control date">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
              </td>
            </tr>
            
            <tr>
              <td><label>Sampai Tanggal</label></td>
              <td class="col-sm-8">
                <div class="input-group date filter_date">
                <input id="filter_end_date" type="text" name="end_date" value="<?php echo date("Y-m-d"); ?>" data-target-column="3" class="form-control date">
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
              </div>
              </td>
            </tr>
            
            <tr>
              <td colspan="4" align="right">
                <button type="button" class="btn btn-default" id="trigger_filter_received_po"><i class="fa fa-search" aria-hidden="true"></i> Cari</button>
              </td>
            </tr>
          </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>

    <div class="panel-body">
      <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-received-list">
        <thead>
          <tr>
            <th>Nomor</th>
            <th>Tanggal Barang Diterima</th>
            <th>Supplier</th>
            <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN."/retur_order/get_received_po_data") ?>"/>
      <!-- /.table-responsive -->
    </div>

    <div></div>
	</div>
</div>

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"
		src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>