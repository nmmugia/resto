<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <div class="panel panel-default">
    <div class="panel-body">
      <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-delivery-cost">
        <thead>
          <tr>
            <th>Nama</th>
            <th>Ongkos Kirim</th>
          </tr>
        </thead>
      </table>
      <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url ?>"/>
    </div>
  </div>
</div>
<script data-main="<?php echo base_url('assets/js/main-admin'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>