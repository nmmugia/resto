<?php 
  if (!defined('BASEPATH')) exit('No direct script access allowed');
  echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
?>
<style type="text/css">
.panel-heading{
  background-color: #fff;
}
</style>
<?php
  $this->load->view('partials/navigation_v');
?>
<div id="page-wrapper">
  <div class="container-fluid">
    <div class="col-lg-12" style="margin-top:90px;margin-bottom:15px;">
      <div class="row">
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
        <div class="col-sm-4">
        </div>
         <div class="col-sm-4">
          <div class="row">
            <div class="margin-wrap">
            <div class="panel-info">
              <div class="col-xs-6">
                <p class="role-info text-left">Saldo</p>
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
    </div>
    <div class="clearfix"></div>
    <div class="panel panel-default">
      <div class="col-lg-12">
        <div class="row">
          <div class="title-bg-custom">
            <h4 class="title-name left"><?php if (isset($subtitle)) echo $subtitle; ?></h4>
          </div>
        </div>
      </div>
      <div class="panel-body">
        <div class="col-lg-12">
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
            <div class="form-group" style="margin-top:10px;">
              <label for="description" class="col-sm-2 control-label">Deskripsi Penambah</label>
              <div class="col-sm-10">
                <?php echo form_textarea($description); ?>
              </div>
            </div>
            <div class="form-group">
              <label for="amount" class="col-sm-2 control-label">Jumlah Penambah</label>
              <div class="col-sm-10">
                <div class="input-group">
                   <div class="input-group-addon">Rp. </div>
                  <?php echo form_input($amount); ?>
                </div>
              </div>
            </div>
            <div class="form-group">
              <div class="text-center">
                <button type="submit" name="btnAction" value="save" class="btn btn-std-yellow save_petty_cash" feature_confirmation="<?php echo ($this->data['feature_confirmation']['petty_cash']) ?>"><?php echo $this->lang->line('ds_submit_save'); ?></button>
                <button type="submit" name="btnAction" value="save_exit" class="btn btn-std-yellow save_petty_cash" feature_confirmation="<?php echo ($this->data['feature_confirmation']['petty_cash']) ?>"><?php echo $this->lang->line('ds_submit_save_exit'); ?></button>
                <a href="<?php echo base_url('petty_cash'); ?>" class="btn btn-std"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php echo form_close(); ?>
<script data-main=" <?php echo base_url('assets/js/main-cashier'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>