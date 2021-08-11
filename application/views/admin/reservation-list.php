<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Created by DIOS.
 * User: Alta Falconeri
 * Date: 12/16/2014
 * Time: 2:20 PM
 */
?>
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
    
    <!-- /.panel-heading -->
    <div class="panel-body">
		
        <table class="table table-striped table-bordered table-hover dt-responsive" cellspacing="0" width="100%" id="dataTables-reservation">
            <thead>
            <tr>
                    <th>Atas Nama</th>
                    <th>Nomor Kontak</th>
                    <th>Waktu</th>
                    <th>Jumlah Tamu</th>
                    <th>Catatan</th>
                    <th>Meja</th>
                    <th>DP</th>
                    <th>Status</th>
                    <th>Catatan Gagal</th>
                    <th style="text-align: center; width:15%">Aksi</th>
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

<script data-main="<?php echo base_url('assets/js/main-reservation'); ?>"
        src="<?php echo base_url('assets/js/libs/require.js'); ?>"></script>