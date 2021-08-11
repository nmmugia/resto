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
        <div class="panel-headingy">
            <a href="<?php echo $add_url ?>" class="btn btn-primary pull-right"><i
                    class='fa fa-plus'></i> Tambah</a>

            <div class="clearfix"></div>
        </div>
        <!-- /.panel-heading -->
        <div class="panel-body">

            <table class="table table-striped table-bordered table-hover dt-responsive" id="dataTables-member">
                <thead>
                <tr>
                    <th>Nama</th>
                    <th>ID Member</th>
                   <!--  <th>Point</th> -->
                    <th>Kategori</th>
                    <th>Benefit</th>
                    <th>Bergabung</th>
                    <th>Transaksi Terakhir</th>
                    <th>Pembelajaan</th>
                    <th style="text-align: center; width:18%"><?php echo $this->lang->line('column_action'); ?></th>
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

<script data-main="<?php echo base_url('assets/js/main-member'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>