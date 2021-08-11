<?php if (! defined('BASEPATH')) exit('No direct script access allowed'); 
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="<?php echo base_url(SITE_ADMIN . '/hrd_appraisal/add_process_appraisal'); ?>" class="btn btn-primary pull-right ">
            		 <div id="title-form-emp-affair" class="btn-add">Tambah Proses Appraisal</div>
            </a>  
            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-app-process-list">
                <thead>
                <tr> 
                    <th width="200px">Nama Pegawai</th>
                    <th width="100px">Period</th>
                    <th width="130px">Tanggal Appraisal</th>
                     <th width="200px">Template Name</th>
                     <th width="200px">Deskripsi</th>
                    <th style="text-align: center" width="140px"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrlProcessAppraisal"
                   value="<?php echo $data_url; ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
<!-- /.col-lg-12 -->