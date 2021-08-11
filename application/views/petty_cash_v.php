<?php if (!defined('BASEPATH')) exit('No direct script access allowed');?>
<style type="text/css">
.panel-heading{
  background-color: #fff;
}
</style>
<link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.bootstrap.css'); ?>" rel="stylesheet">
<link href="<?php echo base_url('assets/js/plugins/dataTables/css/dataTables.responsive.css'); ?>" rel="stylesheet">
<?php
  $this->load->view('partials/navigation_v');
?>
<div id="page-wrapper">
    <div class="col-lg-12 order-header">
        <div class="col-sm-4">
          <div class="row">
            <div class="resto-info-mini">
              <div class="resto-info-pic">
              
              </div>
              <div class="resto-info-name">
                <?php echo $data_store->store_name; ?>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-4"></div>
        <div class="col-sm-4">
          <div class="row">
            <div class="margin-wrap">
              <div class="panel-info panel-info-fixed">
                <div class="col-xs-6" style="padding-top:2px;">
                  
                </div>
                <div class="col-xs-6">
                  <p class="role-info text-right"><?php echo ucfirst($this->data['group_name']); ?></p>
                  <p class="role-name text-right"><?php echo $user_name; ?></p>
                </div>
              </div>
            </div>
          </div>
        </div>
    </div>
    <?php if ($this->session->flashdata('message')): ?>
      <div class="alert alert-danger"><?php echo $this->session->flashdata('message'); ?></div>
    <?php endif; ?>
    <div class="clearfix"></div>
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
    <div class="col-lg-6">
      <div class="col-lg-12">
        <div class="row">
          <div class="title-bg-custom">
            <div class="col-lg-10">
              <h4 class="title-name left">Kas Kecil</h4>
            </div>
            <div class="col-lg-2 pull-right">
               <a href="<?php echo $add_petty_cash; ?>" class="btn btn-std-yellow-pc">Tambah</a>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="col-lg-12">
          <div style="padding-top:20px;">
            <table class="table table-striped table-bordered table-hover dt-responsive  " cellspacing="0" width="100%" id="dataTables-petty-cash">
              <thead>
                <tr>
                  <th>Diproses Oleh</th>
                  <th>Deskripsi</th>
                  <th>Jumlah</th>
                  <th style="text-align: center; width:15%">Aksi</th>
                </tr>
              </thead>
            </table>
            <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url ?>"/>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-6">
      <div class="col-lg-12">
        <div class="row">
          <div class="title-bg-custom">
            <div class="col-lg-10">
              <h4 class="title-name left">Saldo</h4>
            </div>
            <div class="col-lg-2 pull-right">
               <a href="<?php echo $add_balance_cash; ?>" class="btn btn-std-yellow-pc">Tambah</a>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="col-lg-12">
          <div style="padding-top:20px;">
            <table class="table table-striped table-bordered table-hover dt-responsive  " cellspacing="0" width="100%" id="dataTables-balance">
              <thead>
                <tr>
                  <th>Diproses Oleh</th>
                  <th>Deskripsi</th>
                  <th>Jumlah</th>
                  <th style="text-align: center; width:15%">Aksi</th>
                </tr>
              </thead>
            </table>
            <input type="hidden" id="dataProcessBalanceUrl" value="<?php echo $data_balance_url ?>"/>
          </div>
        </div>
      </div>
    </div>
</div>
<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>