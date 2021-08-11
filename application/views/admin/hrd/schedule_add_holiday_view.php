<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:01 AM
 */
?>
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
    <style >
        .form-horizontal .control-label{
 
text-align:left; 
}
    </style>
      <?php 
echo form_open_multipart(base_url(SITE_ADMIN . '/hrd_schedule/add_holiday'), array('class' => 'form-horizontal form-ajax'));
    ?>
     <ol class="breadcrumb">
        <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/holiday'); ?>">Kelola Cuti Pegawai</a></li> 
        <li class="active"><?php echo $subtitle;?></li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal">
                <div class="form-group">
                    <label class="col-sm-2 control-label">Nama Karyawan</label>
                    <div class="col-sm-6">
                        <?php echo form_dropdown('user_id', $users, '', 
                              'id="user_id" field-name = "Pegawai" 
                              class="def-select form-control"'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Tanggal Mulai</label>
                    <div class="col-sm-6">
                      <div class='input-group date ' id='start-date'>
                        <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Mulai" name="start_date" onkeydown="return false"> 
                        <span class="input-group-addon">
                          <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                      </div> 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Tanggal Akhir</label>
                    <div class="col-sm-6">
                      <div class='input-group date ' id='end-date'>
                      <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Akhir" name="end_date" onkeydown="return false"> 
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div> 
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Potong Jatah Cuti</label>
                    <div class="col-sm-6" id="quota">
                    <input type="checkbox" name="enum_holiday_status" value="1"> Ya
                    </div>
                </div>

               <!--  <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Lampiran</label>
                    <div class="col-sm-6" id="quota">
                    <input type="file">
                    </div>
                </div> -->

                <div class="form-group">
                 <div class="col-sm-8 col-sm-offset-4" >
                    <button type="submit" name="btnAction" value="save"
                            class="btn btn-primary">
                        <?php echo $this->lang->line('ds_submit_save'); ?>
                    </button>
                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/holiday/'); ?>"
                       class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?></a>
                         </div>
                </div>
            </form>   
        </div>
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>