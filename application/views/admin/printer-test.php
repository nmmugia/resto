<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-body">
    <form method="post" action="<?php echo base_url('admincms/printer_test'); ?>">
      <div class="form-group">
          <label class="col-sm-2 control-label">Nama Printer</label>
              <div class="col-sm-4">
                  <?php echo form_input($name); ?>
              </div>
              <div class="col-sm-2">
              <button type="submit" class="btn btn-primary">Test
              </button>
              </div>
      </div>
      </form>
    </div>
  </div>
</div>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>