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
echo form_open_multipart(base_url(SITE_ADMIN . '/hrd_schedule/edit_holidays'), array('class' => 'form-horizontal form-ajax'));
    ?>
     <ol class="breadcrumb">
        <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule'); ?>">Kelola Jadwal</a></li> 
        <li class="active"><?php echo $subtitle;?></li>
    </ol>
    <div class="row">
        <div class="col-lg-12">
            <form class="form-horizontal">
                <div class="form-group">
                    <label for="inputEmail3" class="col-sm-2 control-label">NIP</label>
                    <div class="col-sm-6">
                     <?php echo $users->nip;?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Nama</label>
                    <div class="col-sm-6">
                     <?php echo $users->name;?>
                    </div>
                </div>

                     <input type="hidden" name="user_id" value="<?php echo $users->id?>">
                      <input type="hidden" name="holiday_id" value="<?php echo $data_holidays->id?>">
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Tanggal Mulai</label>
                    <div class="col-sm-6">
                      <div class='input-group date ' id='start-date'>
                      <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Mulai" 
                      name="start_date" onkeydown="return false"
                      value="<?php echo $data_holidays->start_date;?>"
                      > 
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
                      <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Akhir" 
                      name="end_date" onkeydown="return false"
                      value="<?php echo $data_holidays->end_date;?>"
                      > 
                      <span class="input-group-addon">
                        <span class="glyphicon glyphicon-calendar"></span>
                      </span>
                    </div> 
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Jatah Hari</label>
                    <div class="col-sm-6" id="quota">
                     <div class="col-sm-1"><?php echo ($history_jobs->vacation - $taken_total->day_total);?></div>
                      <div class="col-sm-3">Hari </div>  
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Potong Jatah Cuti</label>
                    <div class="col-sm-6" id="quota">
                    <input type="checkbox" name="enum_holiday_status" value="1" <?php if($data_holidays->enum_holiday_status == "1") echo "checked"?>> Ya
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
                    <button type="submit" name="btnAction" value="save_exit"
                            class="btn btn-primary">
                        <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                    </button>  
                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/holiday/'.$users->id); ?>"
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