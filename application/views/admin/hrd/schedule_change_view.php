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
echo form_open_multipart(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
    ?>
    <style >
        .form-horizontal .control-label{
 
text-align:left; 
}
    </style>
     <ol class="breadcrumb">
        <li><a href="<?php echo base_url(SITE_ADMIN); ?>">Home</a></li>
        <li><a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule'); ?>">Kelola Jadwal</a></li> 
        <li class="active"><?php echo $subtitle;?></li>
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
                    <label for="inputEmail3" class="col-sm-2 control-label">NIP</label>
                    <div class="col-sm-6">
                     <?php echo $users->nip;?>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Nama</label>
                    <div class="col-sm-6">
                     <?php echo $users->name;?>
                      <input type="hidden" name="user_id" value="<?php echo $users->id?>">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Tanggal Diliburkan</label>
                    <div class="col-sm-6">
                        <div class='input-group date ' id='start-date'>
                          <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Mulai" name="holiday_date" onkeydown="return false"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>
                </div> 
                
                <div class="form-group"  id="container-end-date">
                    <label for="inputPassword3" class="col-sm-2 control-label">Tanggal Masuk</label>
                    <div class="col-sm-6">
                        <div class='input-group date ' id='end-date'>
                          <input type="text" class="form-control no-special-char requiredTextField" field-name="Tanggal Akhir" name="work_date" onkeydown="return false"> 
                          <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div> 
                    </div>
                </div> 
        </div>
        
       
        <div class="col-lg-12">
            <div class="form-group">
                <div class="col-sm-8 col-sm-offset-4" >
                    <button type="submit" name="btnAction" value="save_exit"
                            class="btn btn-primary">
                        <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                    </button>  
                    <a href="<?php echo base_url(SITE_ADMIN . '/hrd_schedule/'); ?>"
                   class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                   </a>
                </div>
            </div> 
        </div>
    </div>
    </div>
    </form>   
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-hrd'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>