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
                          <select class="def-select form-control select2" name="supplier">
                              <?php foreach($suppliers as $supplier): ?>
                                  <option value="<?php echo $supplier->id; ?>"><?php echo $supplier->name; ?></option>
                              <?php endforeach ?>
                          </select>
                      </div>
                  </div>
                  <div class="form-group">
                      <label class="col-sm-2 control-label">Keterangan</label>
                      <div class="col-sm-10">
                          <textarea placeholder="Keterangan" name="description" cols="40" rows="5" type="text" class="form-control" field-name="Alamat resto"><?php echo set_value('description') ?></textarea>
                      </div>
                  </div>
                </fieldset>
                <div class="clearfix"></div>
                <fieldset>
                  <legend><h3>Data Pesanan</h3></legend>
                  <table class="table table-striped table-bordered table-hover dt-responsive" id="po_create_table">
                    <thead>
                      <tr>
                        <th>Nama Inventori</th>
                        <th width="200px" class="text-center">Jumlah Order</th>
                        <th width="150px" class="text-center">Satuan</th>
                        <th width="50px"><a href="javascript:void(0);" class="btn btn-sm btn-primary" id="add_po_create"><i class="fa fa-plus"></i></a></th>
                      </tr>
                    </thead>
                    <tbody><?php echo $add_po_create; ?></tbody>
                  </table>
                </fieldset>
                <div class="text-center">
                  <button type="submit" name="btnAction" value="save" class="btn btn-primary">Simpan</button>
                  <a href="<?php echo base_url(SITE_ADMIN . '/purchase_order/po_list'); ?>" class="btn btn-default">Batal</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</form>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>