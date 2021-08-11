<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
  ?>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
    <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule'); ?>">Kelola Jadwal</a></li> 
    <li class="active">Atur Hari Libur</li> 
  </ol>
  <div class="result">
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
  </div>
  <div class="panel panel-default">
    <div class="panel-body">
      <div class="col-lg-12">
        <div class="form-group">
          <label class="col-sm-2 control-label">Tanggal Libur</label>
          <div class="col-sm-6">
            <div class='input-group date ' id="holiday-date" >
                <input type="text" class="form-control no-special-char" field-name="Tanggal Libur" name="holiday-date" onkeydown="return false"> 
                <span class="input-group-addon">
                  <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
          </div>
        </div> 
        <div class="form-group" id="panel_store" >
          <label for="discount_name" class="col-sm-2 control-label">&nbsp;</label>
          <div class="col-sm-10">
            <div class="row">
              <div class="col-xs-5">
                <select class="multiselect form-control" size="15" 
                  multiple="multiple" 
                  data-right="#multiselect_to_1" 
                  data-right-all="#right_All_1" 
                  data-right-selected="#right_Selected_1" 
                  data-left-all="#left_All_1" 
                  data-left-selected="#left_Selected_1">
                  <?php 
                    foreach ($employees as $key => $row) {
                      echo "<option value='".$row->id."'>".$row->name."</option>";
                    }
                  ?>                
                </select>
              </div>
              <div class="col-xs-2">
                <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
              </div>
              <div class="col-xs-5">
                <select name="employees[]" id="multiselect_to_1" class="form-control" size="15" multiple="multiple"></select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="form-group">
          <div class="col-sm-8 col-sm-offset-4" >
            <button type="submit" name="btnAction" value="save" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?></button>  
            <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
          </div>
        </div> 
      </div>
      <br>
      <div class="col-lg-12">
        <table class="table table-striped table-bordered table-hover dt-responsive width100" cellspacing="0" width="100%" id="table-holidays">
          <thead>
          <tr>
              <th>Nama Pegawai</th>
              <th>Tanggal Libur</th>
              <th>Tanggal Dibuat</th>  
          </tr>
          </thead>
        </table>
         <input type="hidden" id="dataProcessUrl" value="<?php echo $data_url; ?>"/>
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>  
</div>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>