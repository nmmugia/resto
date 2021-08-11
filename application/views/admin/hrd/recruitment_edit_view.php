<?php if (! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 10:01 AM
 */
?>
 <?php 
    echo form_open(base_url(uri_string()), array('class' => 'form-horizontal form-ajax'));
    ?>
    
<style>
.form-horizontal .control-label{ 
text-align:left; 
}
</style> 
<div class="col-lg-12">
    
    <div class="panel panel-default"> 
        <div class="panel-body"> 

            <div class="col-lg-12">
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
            </div>
            <div class="col-lg-12"> 
                <div class="panel panel-default">
                    <div class="panel-body">   
                       <div class="form-group">
                            <label for="name" class="col-sm-3 control-label">Jabatan Yang Diinginkan</label>

                            <div class="col-sm-8">
                               <input type="text" name="job_apply" class="form-control requiredTextField no-special-char" 
                                    field-name="Jabatan"
                                    value="<?php if(!empty($detail_recruits)) echo $detail_recruits->job_apply;?>">
                            </div>
                        </div> 
                    </div>
                </div>
            </div> 
            <div class="col-lg-12">    
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#identity" aria-controls="identity" role="tab" data-toggle="tab">Identitas</a></li>
                    <li role="presentation"><a href="#family" aria-controls="family" role="tab" data-toggle="tab">Keluarga</a></li>
                    <li role="presentation"><a href="#education" aria-controls="education" role="tab" data-toggle="tab">Riwayat Pendidikan</a></li>
                    <!-- <li role="presentation"><a href="#reference" aria-controls="reference" role="tab" data-toggle="tab">Referensi</a></li> -->
                </ul>

                  <!-- Tab panes -->
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active col-lg-12" id="identity"><br>
                        <?php $this->load->view('admin/hrd/form_recruitment_identity.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane col-lg-12" id="family"><br>
                        <?php $this->load->view('admin/hrd/form_recruitment_family.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane col-lg-12" id="education"><br>
                        <?php $this->load->view('admin/hrd/form_recruitment_education.php'); ?>
                    </div>
                    <div role="tabpanel" class="tab-pane col-lg-12" id="reference"> <br>
                      <?php //$this->load->view('admin/hrd/form_recruitment_reference.php'); ?>
                    </div>  
                </div>
            </div>
            <div class="col-lg-12">    
                <div class="form-group">
                  <div class="col-sm-offset-4 col-sm-8">
                  <button type="submit" name="btnAction" value="save"
                                class="btn btn-primary"><?php echo $this->lang->line('ds_submit_save'); ?>
                        </button>
                        <button type="submit" name="btnAction" value="save_exit"
                                class="btn btn-primary">
                            <?php echo $this->lang->line('ds_submit_save_exit'); ?>
                        </button>
                         <a href="<?php echo base_url(SITE_ADMIN . '/hrd_recruitment/recruitment_list/'); ?>"
                            class="btn btn-default"><?php echo $this->lang->line('ds_submit_cancel'); ?>
                        </a>
                        
                    </div>
                </div>
            </div> 
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->
</form> 