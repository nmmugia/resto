<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<link href="<?php echo base_url(); ?>assets/css/bootstrap-datetimepicker.css" rel="stylesheet">
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-heading">
      <form id="formFilter" method="POST">
        <div class="clear-export"></div>
        <table class="col-lg-8 form-table-cms">
          <tbody>
            <tr>
              <td><label>Waktu Mulai</label></td>
              <td class="col-sm-8">
                <div class='input-group date ' id='reprint_billing_start_date'>
                  <?php 
                    echo form_input(array(
                      'name' => 'start_date',
                      'id' => 'input_start_date',
                      'type' => 'text',
                      'class' => 'form-control date',
                      'onkeydown'=>'return false',
                      'value' => ($this->input->post("start_date")!="" ? $this->input->post("start_date") : date("Y-m-d")." 00:00")
                    )); 
                  ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td><label>Waktu Akhir</label></td>
              <td class="col-sm-8">
                <div class='input-group date ' id='reprint_billing_end_date'>
                  <?php 
                    echo form_input(array(
                      'name' => 'end_date',
                      'id' => 'input_end_date',
                      'type' => 'text',
                      'class' => 'form-control date',
                      'onkeydown'=>'return false',
                      'value' => ($this->input->post("end_date")!="" ? $this->input->post("end_date") : date("Y-m-d")." 23:59")
                    )); 
                  ?>
                  <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div> 
              </td>
            </tr>
            <tr>
              <td colspan="2" align="right">
                <button id="reprint_billing_filter_submit" class="btn btn-default"><i class="fa fa-filter" aria-hidden="true"></i> Filter</button>
                <button id="reprint_billing_export_pdf" class="btn btn-success "><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Export PDF</button>
                
              </td>
            </tr>
            </tbody>
        </table>
      </form>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
      <table class="table table-striped table-bordered table-hover dt-responsive" id="table-reprint-billing">
        <thead>
          <tr>
            <th width="140px">Tanggal</th>
            <th width="90px">Meja</th>
            <th width="150px">Customer</th>
            <th width="140px">Receipt</th>
            <th width="130px">Pembelian</th>
            <th width="150px">Grand Total</th>
            <th width="170px">Jumlah Pelanggan</th>
            <th width="100px">Order ID</th>
            <th width="80px">Aksi</th>
          </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN . '/reprint_billings/get_data'); ?>"/>
    </div>
  </div>
</div>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>