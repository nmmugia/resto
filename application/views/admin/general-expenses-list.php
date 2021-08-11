<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');?>
<div class="col-lg-12">
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
    <div class="panel panel-default">
        <div class="panel-heading">
            <a href="<?php echo base_url(SITE_ADMIN . '/general_expenses/add'); ?>" class="btn btn-primary pull-right"><i class='fa fa-plus'></i> <?php echo $this->lang->line('btn_add'); ?></a>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

                <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-general-expenses">
                    <thead>
                    <tr>
                        <th>Jenis Pengeluaran</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Pengeluaran</th>
                        <th style="text-align: center"><?php echo $this->lang->line('column_action'); ?></th>
                    </tr>
                    </thead>
                </table>
                <input type="hidden" id="dataProcessUrl" value="<?php echo base_url(SITE_ADMIN.'/general_expenses/getdatatables');?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-admin'); ?>"src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>