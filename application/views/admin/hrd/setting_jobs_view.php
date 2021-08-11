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
        <?php /*<div class="panel-heading">
            <a href="<?php echo base_url(SITE_ADMIN . '/hrd/add_jobs'); ?>" class="btn btn-primary pull-right ">
            		 <div id="title-form-emp-affair" class="btn-add">Tambah Jabatan</div>
            </a>  
            <div class="clearfix"></div>
        </div>*/ ?>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-jobs-list">
                <thead>
                <tr> 
                    <th width="20%">Nama</th>
                    <th width="30%">Keterangan</th>
                    <th width="20%">Komponen Gaji</th>
                    <?php /*<th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>*/ ?>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo base_url(SITE_ADMIN . '/hrd/get_data_jobs'); ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel --> 
</div> 
<input type="hidden" id="admin_url" value="<?php echo base_url(SITE_ADMIN) ?>"/>
<input type="hidden" id="root_base_url" value="<?php echo base_url(); ?>"/>
<!-- /.col-lg-12 -->