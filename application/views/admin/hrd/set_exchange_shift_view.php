<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
  ?>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
    <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule'); ?>">Kelola Jadwal</a></li> 
    <li class="active">Atur Jadwal Standar</li> 
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
          <label for="inputPassword3" class="col-sm-2 control-label">Tanggal Mulai</label>
          <div class="col-sm-3">
            <div class='input-group date ' id='start-date'>
              <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Mulai" name="start_date" onkeydown="return false" value="<?php if(!empty($schedules)) echo $schedules->start_date;?>"> 
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div> 
          </div>  
        </div>
        <input type="hidden" id="repeat-status" value="0">
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Pengulangan</label>
          <div class="col-sm-6">
            <input type="radio" name="repeat" class="repeat-status" value="0" checked> 
              Ada Tanggal Akhir
            <input type="radio" name="repeat" class="repeat-status" value="1" > 
            Berlaku Seterusnya
          </div>  
        </div>
        <div class="form-group" id="container-end-date">
          <label class="col-sm-2 control-label">Tanggal Akhir</label>
          <div class="col-sm-3">
            <div class='input-group date ' id='end-date'>
              <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Akhir" value="" name="end_date" onkeydown="return false"> 
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
                <label class="control-label">Dari Jam Kerja</label>
                <select class="form-control select2 requiredDropDown" data-width="100%" name="from_office_hour" id="from_office_hour">
                  <option value="">Pilih Template Jam Kerja</option>
                  <?php foreach($office_hours as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
                  <?php endforeach; ?>
                </select>
                <select class="multiselect form-control" size="15" name="employees_from[]" id="multiselect_from_1"
                  multiple="multiple" 
                  data-right="#multiselect_to_1" 
                  data-right-all="#right_All_1" 
                  data-right-selected="#right_Selected_1" 
                  data-left-all="#left_All_1" 
                  data-left-selected="#left_Selected_1">
                 
                </select>
              </div>
              <div class="col-xs-2">
                <br><br><br>
                <button type="button" id="right_All_1" class="btn btn-block"><i class="glyphicon glyphicon-forward"></i></button>
                <button type="button" id="right_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-right"></i></button>
                <button type="button" id="left_Selected_1" class="btn btn-block"><i class="glyphicon glyphicon-chevron-left"></i></button>
                <button type="button" id="left_All_1" class="btn btn-block"><i class="glyphicon glyphicon-backward"></i></button>
              </div>
              <div class="col-xs-5">
                <label class="control-label">Ke Jam Kerja</label>
                <select class="form-control select2" data-width="100%" name="to_office_hour" id="to_office_hour">
                  <option value="">Pilih Template Jam Kerja</option>
                  <?php foreach($office_hours as $o): ?>
                  <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
                  <?php endforeach; ?>
                </select>
                <select name="employees_to[]" id="multiselect_to_1" class="form-control" size="15" multiple="multiple"></select>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-lg-12">
        <div class="form-group">
          <div class="col-sm-8 col-sm-offset-4" >
            <button type="submit" name="btnAction" value="save_exit" class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save_exit'); ?></button>  
            <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
          </div>
        </div> 
      </div>
    </div>
  </div>
  <?php echo form_close(); ?>  
</div>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>