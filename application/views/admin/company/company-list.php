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
         
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-member">
                <thead>
                <tr>
                    <th wi>Resto</th>
                    <th>Nama Perusahaan</th>
                    <th>PIC</th>
                    <th>Alamat</th>
                    <th>Email</th>
                    <th>Tlp</th>
                    <th>Hp</th>
                    <th>Uang Muka</th>
                    <th>No Rekening</th>
                    <th>Atas Nama</th>
                    <th>Banquet</th> 
                    <th style="text-align: center; width:20%"><?php echo $this->lang->line('column_action'); ?></th>
                </tr>
                </thead>
            </table>
            <input type="hidden" id="dataProcessUrl"
                   value="<?php echo $data_url ?>"/>

            <!-- /.table-responsive -->
        </div>
        <!-- /.panel-body -->
    </div>
    <!-- /.panel -->
</div>
<!-- /.col-lg-12 -->

<script data-main="<?php echo base_url('assets/js/main-company'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>