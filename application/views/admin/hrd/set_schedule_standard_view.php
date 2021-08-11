<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
  ?>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
    <li class="active">Kelola Jadwal</li> 
  </ol>
  <div class="panel panel-default">
    <div class="panel-heading">
        <div class="form-group">
          <div class="col-sm-3">
          <label for="inputPassword3" class=" control-label">Tanggal Mulai</label>
          </div>
          <div class="col-sm-6">
            <div class='input-group date ' id='start-date'>
              <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Mulai" name="start_date" onkeydown="return false" value="<?php echo date('Y-m-d'); ?>"> 
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div> 
          </div>  
        </div>
        <input type="hidden" id="repeat-status" value="">
        <div class="form-group">
          <div class="col-sm-3">
          <label for="inputPassword3" class=" control-label">Pengulangan</label>
          </div>
          <div class="col-sm-2">
            <input type="radio" name="repeat" class="repeat-status" value="0" checked=""> 
              Ada Tanggal Akhir
          </div>
          <div class="col-sm-2">
            <input type="radio" name="repeat" class="repeat-status" value="1"> 
            Berlaku Seterusnya
          </div>  
        </div>
        <div class="form-group" id="container-end-date">
          <div class="col-sm-3">
          <label for="inputPassword3" class=" control-label">Tanggal Akhir</label>
          </div>
          <div class="col-sm-6">
            <div class='input-group date ' id='end-date'>
              <input id="end_date" type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Akhir" value="<?php echo date('Y-m-d'); ?>" name="end_date" onkeydown="return false"> 
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div> 
          </div>
        </div> 
        <div class="form-group">
          <div class="col-sm-3">
          <label for="inputPassword3" class=" control-label">Template Jam Kerja</label>
          </div>
          <div class="col-sm-6">
            <select name="office_hour" class="form-control" field-name="Jam Kerja" id="office_hour">
              <option value="">Pilih Template Jam Kerja</option>
              <?php foreach($office_hours as $o): ?>
              <option value="<?php echo $o->id ?>"><?php echo $o->name ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <div class="col-sm-3">
          <label for="inputPassword3" class=" control-label">Jam Masuk</label>
          </div>
          <div class="col-sm-6">
            <div class='input-group date' id='start-time'>
              <input id="checkin_time" type="text" class="form-control no-special-char" field-name="Jam Mulai" name="start_time" onkeydown="return false" value="<?php echo date('H:i:s'); ?>" disabled> 
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-time"></span>
              </span>
            </div> 
          </div>
        </div> 
        <div class="form-group">
          <div class="col-sm-3">
          <label for="inputPassword3" class=" control-label">Jam Pulang</label>
          </div>
          <div class="col-sm-6">
            <div class='input-group date' id='end-time'>
              <input id="checkout_time" type="text" class="form-control no-special-char" field-name="Jam Akhir" name="end_time" onkeydown="return false" value="<?php echo date('H:i:s'); ?>" disabled> 
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-time"></span>
              </span>
            </div> 
          </div>
        </div> 
        <div class="form-group" id="panel_store" >
          <label for="discount_name" class="col-sm-3 control-label">&nbsp;</label>
          <div class="col-sm-6">
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
                <select name="employees[]" id="multiselect_to_1" class="form-control requiredDropDown" field-name="Pegawai" size="15" multiple="multiple"></select>
              </div>
            </div>
          </div>
        </div>
  

        <div class="form-group">
          <label for="xxx" class="col-sm-3 control-label">&nbsp;</label>
          <div class="col-sm-6" align="right">
            <button type="submit" name="btnAction" value="save" class="btn btn-primary" id="validate_standard_schedule"><?php echo $this->lang->line('ds_submit_save'); ?></button>  
            <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/'); ?>" class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
          </div>
        </div> 

    </div>
    <div class="clearfix"></div>
    <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive width100" cellspacing="0" width="100%" id="table-kelola-jadwal">
                <thead>
                <tr>
                    <th>Nama Pegawai</th>
                    <th>Tanggal Awal</th>
                    <th>Tanggal Akhir</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>

                </tr>
                </thead>
            </table>

            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url; ?>"/>

    </div>
  </div>
  <?php echo form_close(); ?>  
</div>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>