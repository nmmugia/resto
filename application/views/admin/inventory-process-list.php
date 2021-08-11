<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
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
		<?php /*
    <div class="panel-heading">
      <a href="<?php echo $add_url; ?>" class="btn btn-primary pull-right"><i class='fa fa-plus'></i> Tambah Proses Inventori</a>
      <div class="clearfix"></div>
    </div>*/ ?>
    <!-- /.panel-heading -->
    <div class="panel-body">
      <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-inventory-process">
        <thead>
        <tr>
          <th>Tanggal</th>
          <th>Inventori</th>
          <th>Jumlah Proses</th>
          <th>Unit</th>
          <!--<th style="text-align: center" width="180px"><?php echo $this->lang->line('column_action'); ?></th>-->
        </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url ?>"/>
    </div>
  </div>
</div>
