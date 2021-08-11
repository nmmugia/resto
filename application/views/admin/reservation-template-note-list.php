<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php
    if (!empty($message_success)) {
        echo '<div class="alert alert-success" role="alert">';
        echo $message_success;
        echo '</div>';
    }
    if (!empty($message)) {
        echo '<div class="alert alert-danger" role="alert">';
        echo $message;
        echo '</div>';
    }
  ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <a href="<?php echo base_url(SITE_ADMIN . '/reservation_template_notes/add'); ?>" class="btn btn-primary pull-right"><i class='fa fa-plus'></i> Tambah Baru</a>
      <div class="clearfix"></div>
    </div>
    <div class="panel-body">
      <table class="table table-striped table-bordered" id="dataTables-reservation-template-note">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nama Template</th>
            <th>Note</th>
            <th style="text-align: center" width="150"><?php echo $this->lang->line('column_action'); ?></th>
          </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN.'/reservation_template_notes/get_data');?>"/>
    </div>
  </div>
</div>

<script data-main="<?php echo base_url('assets/js/main-reservation'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>