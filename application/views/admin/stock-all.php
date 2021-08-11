<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<style>
.control-label{
  text-align: left !important;
}
</style>
  <div class="col-lg-12">
    <div class="panel panel-default">
      <div class="panel-body">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th width="150px">Outlet</th>
              <th>Nama Inventori</th>
              <th width="150px">Stok Sistem</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($stocks as $l): ?>
              <tr>
                <td><?php echo $l->outlet_name; ?></td>
                <td><?php echo $l->name; ?></td>
                <td><?php echo (int)$l->stock_system." ".$l->code; ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>