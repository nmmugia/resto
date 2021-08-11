<?php if (! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
  <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
  ?>
  <ol class="breadcrumb">
    <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
    <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule'); ?>">Kelola Jadwal</a></li> 
    <li class="active">Pergantian Shift</li> 
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
        <input type="hidden" id="repeat-status" value="<?php if(!empty($schedules))  echo $schedules->enum_repeat; else echo 0;?>">
        <div class="form-group">
          <label for="inputPassword3" class="col-sm-2 control-label">Pengulangan</label>
          <div class="col-sm-6">
            <input type="radio" name="repeat" class="repeat-status" value="0" <?php if(!empty($schedules) && $schedules->enum_repeat == 0) echo "checked"; else echo "checked"; ?>> 
              Ada Tanggal Akhir
            <input type="radio" name="repeat" class="repeat-status" value="1" <?php if(!empty($schedules) && $schedules->enum_repeat == 1) echo "checked"; ?>> 
            Berlaku Seterusnya
          </div>  
        </div>
        <div class="form-group" id="container-end-date">
          <label class="col-sm-2 control-label">Tanggal Akhir</label>
          <div class="col-sm-3">
            <div class='input-group date ' id='end-date'>
              <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Akhir" value="<?php if(!empty($schedules)) echo $schedules->end_date;?>" name="end_date" onkeydown="return false"> 
              <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
              </span>
            </div> 
          </div>
        </div> 
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Shift</th>
              <th>Jam Kerja</th>
              <th>Ganti Jam Kerja</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach($office_hours as $l): ?>
              <tr office_hour_id="<?php echo $l->id ?>">
                <td><a href="<?php echo base_url(SITE_ADMIN."/hrd_schedule/view_detail_office_hours/".$l->id) ?>" class="detail_office_hour"><?php echo $l->name ?></a></td>
                <td><?php echo date("H:i:s",strtotime($l->checkin_time))." s/d ".date("H:i:s",strtotime($l->checkout_time)) ?></td>
                <td>
                  <select class="form-control select2 office_hour_target_id" value_before="<?php echo $l->id ?>" data-width="100%" name="detail[<?php echo $l->id ?>]">
										<option value="<?php echo $l->id ?>"><?php echo $l->name." ( ".date("H:i:s",strtotime($l->checkin_time))." s/d ".date("H:i:s",strtotime($l->checkout_time))." )" ?></option>
                    <?php foreach($office_hour_targets[$l->id] as $o): ?>
                      <option value="<?php echo $o->id ?>" <?php echo ($l->id==$o->id ? "selected" : "") ?>><?php echo $o->name." ( ".date("H:i:s",strtotime($o->checkin_time))." s/d ".date("H:i:s",strtotime($o->checkout_time))." )" ?></option>
                    <?php endforeach; ?>
                  </select>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
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
<div class="modal fade" id="detail_office_hour_employee" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="exampleModalLabel">Jadwal Shift Pegawai </h4>
      </div>
      <div class="modal-body"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>
<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>" src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>